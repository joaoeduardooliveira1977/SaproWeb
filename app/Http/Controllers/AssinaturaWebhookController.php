<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\AssinaturaSignatario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Recebe webhooks do ClickSign para atualizar status de assinaturas.
 *
 * Configurar no ClickSign:
 *   URL: https://seudominio.com/webhooks/clicksign
 *   Eventos: document_signed, document_refused, envelope_closed
 *
 * A rota deve estar fora do middleware 'auth' e 'csrf'.
 * Defina CLICKSIGN_WEBHOOK_SECRET no .env com o segredo configurado no painel ClickSign.
 */
class AssinaturaWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        // ── Validação de assinatura HMAC ─────────────────────────────────────
        if (!$this->assinaturaValida($request)) {
            Log::warning('ClickSign webhook: assinatura HMAC inválida.', [
                'ip'     => $request->ip(),
                'header' => $request->header('X-Clicksign-Hmac-Sha256') ? '[presente]' : '[ausente]',
            ]);
            return response('Forbidden', 403);
        }

        $payload = $request->json()->all();

        // Log sanitizado — apenas identificadores, sem dados de conteúdo
        Log::info('ClickSign webhook recebido.', [
            'evento'   => $payload['event']['name'] ?? $payload['event'] ?? 'desconhecido',
            'list_key' => $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null,
        ]);

        $evento = $payload['event']['name'] ?? $payload['event'] ?? null;

        match($evento) {
            'sign'              => $this->processarAssinatura($payload),
            'refuse'            => $this->processarRecusa($payload),
            'close'             => $this->processarFechamento($payload),
            'auto_close'        => $this->processarFechamento($payload),
            'deadline_exceeded' => $this->processarPrazoExpirado($payload),
            default             => Log::debug("ClickSign webhook: evento '{$evento}' ignorado."),
        };

        return response('OK', 200);
    }

    // ── Valida a assinatura HMAC-SHA256 enviada pelo ClickSign ──────────────
    private function assinaturaValida(Request $request): bool
    {
        $secret = env('CLICKSIGN_WEBHOOK_SECRET');

        // Fora de local/testing, exigir sempre o secret configurado (fail-closed)
        if (empty($secret)) {
            if (app()->environment(['local', 'testing'])) {
                Log::warning('ClickSign webhook: CLICKSIGN_WEBHOOK_SECRET não configurado — validação pulada (ambiente local).');
                return true;
            }

            Log::error('ClickSign webhook: CLICKSIGN_WEBHOOK_SECRET não configurado em ambiente não-local. Requisição rejeitada.');
            return false;
        }

        $headerRecebido = $request->header('X-Clicksign-Hmac-Sha256');
        if (!$headerRecebido) {
            return false;
        }

        // ClickSign envia HMAC-SHA256 do corpo bruto em base64
        $assinaturaEsperada = base64_encode(
            hash_hmac('sha256', $request->getContent(), $secret, true)
        );

        // Comparação timing-safe para evitar timing attacks
        return hash_equals($assinaturaEsperada, $headerRecebido);
    }

    // ── Processamento de eventos ─────────────────────────────────────────────

    private function processarAssinatura(array $payload): void
    {
        $signerKey = $payload['signer']['key']    ?? $payload['event']['data']['signer_key'] ?? null;
        $listKey   = $payload['document']['key']  ?? $payload['event']['data']['document']['key'] ?? null;

        if ($signerKey) {
            $sig = AssinaturaSignatario::where('clicksign_signer_key', $signerKey)->first();
            if ($sig) {
                $sig->update([
                    'status'      => 'assinado',
                    'assinado_em' => now(),
                ]);
                Log::info('ClickSign: signatário assinou.', ['signer_key' => $signerKey]);
            }
        }

        if ($listKey) {
            $assinatura = Assinatura::where('clicksign_list_key', $listKey)->first();
            if ($assinatura) {
                $total    = $assinatura->signatarios()->count();
                $assinado = $assinatura->signatarios()->where('status', 'assinado')->count();

                if ($total > 0 && $total === $assinado) {
                    $assinatura->update(['status' => 'concluido', 'concluido_em' => now()]);
                } elseif ($assinatura->status === 'enviado') {
                    $assinatura->update(['status' => 'assinando']);
                }
            }
        }
    }

    private function processarRecusa(array $payload): void
    {
        $signerKey = $payload['signer']['key']   ?? $payload['event']['data']['signer_key'] ?? null;
        $listKey   = $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null;

        if ($signerKey) {
            AssinaturaSignatario::where('clicksign_signer_key', $signerKey)
                ->update(['status' => 'recusado']);
        }

        if ($listKey) {
            Assinatura::where('clicksign_list_key', $listKey)
                ->update(['status' => 'recusado']);
        }

        Log::info('ClickSign: assinatura recusada.', ['list_key' => $listKey]);
    }

    private function processarFechamento(array $payload): void
    {
        $listKey = $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null;

        if ($listKey) {
            Assinatura::where('clicksign_list_key', $listKey)
                ->whereNotIn('status', ['recusado', 'cancelado'])
                ->update(['status' => 'concluido', 'concluido_em' => now()]);

            Log::info('ClickSign: envelope concluído.', ['list_key' => $listKey]);
        }
    }

    private function processarPrazoExpirado(array $payload): void
    {
        $listKey = $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null;

        if ($listKey) {
            Assinatura::where('clicksign_list_key', $listKey)
                ->whereIn('status', ['enviado', 'assinando'])
                ->update(['status' => 'cancelado']);

            Log::warning('ClickSign: prazo expirado.', ['list_key' => $listKey]);
        }
    }
}
