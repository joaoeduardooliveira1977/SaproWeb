<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\AssinaturaSignatario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Recebe webhooks do ClickSign para atualizar status de assinaturas.
 *
 * Configurar no ClickSign:
 *   URL: https://seudominio.com/webhooks/clicksign
 *   Eventos: document_signed, document_refused, envelope_closed
 *
 * A rota deve estar fora do middleware 'auth' e 'csrf'.
 */
class AssinaturaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->json()->all();

        Log::info('ClickSign webhook recebido', ['payload' => $payload]);

        $evento = $payload['event']['name'] ?? $payload['event'] ?? null;

        match($evento) {
            'sign'              => $this->processarAssinatura($payload),
            'refuse'            => $this->processarRecusa($payload),
            'close'             => $this->processarFechamento($payload),
            'auto_close'        => $this->processarFechamento($payload),
            'deadline_exceeded' => $this->processarPrazoExpirado($payload),
            default             => Log::debug("ClickSign webhook: evento '{$evento}' ignorado"),
        };

        return response()->json(['ok' => true]);
    }

    private function processarAssinatura(array $payload): void
    {
        $signerKey  = $payload['signer']['key']    ?? $payload['event']['data']['signer_key'] ?? null;
        $listKey    = $payload['document']['key']   ?? $payload['event']['data']['document']['key'] ?? null;

        if ($signerKey) {
            $sig = AssinaturaSignatario::where('clicksign_signer_key', $signerKey)->first();
            if ($sig) {
                $sig->update([
                    'status'      => 'assinado',
                    'assinado_em' => now(),
                ]);
                Log::info('ClickSign: signatário assinou', ['signer_key' => $signerKey]);
            }
        }

        // Verifica se todos assinaram
        if ($listKey) {
            $assinatura = Assinatura::where('clicksign_list_key', $listKey)->first();
            if ($assinatura) {
                $total    = $assinatura->signatarios()->count();
                $assinado = $assinatura->signatarios()->where('status', 'assinado')->count();
                if ($total > 0 && $total === $assinado) {
                    $assinatura->update([
                        'status'       => 'concluido',
                        'concluido_em' => now(),
                    ]);
                } elseif ($assinatura->status === 'enviado') {
                    $assinatura->update(['status' => 'assinando']);
                }
            }
        }
    }

    private function processarRecusa(array $payload): void
    {
        $signerKey = $payload['signer']['key'] ?? $payload['event']['data']['signer_key'] ?? null;
        $listKey   = $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null;

        if ($signerKey) {
            AssinaturaSignatario::where('clicksign_signer_key', $signerKey)
                ->update(['status' => 'recusado']);
        }

        if ($listKey) {
            Assinatura::where('clicksign_list_key', $listKey)
                ->update(['status' => 'recusado']);
        }

        Log::info('ClickSign: assinatura recusada', ['list_key' => $listKey]);
    }

    private function processarFechamento(array $payload): void
    {
        $listKey = $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null;

        if ($listKey) {
            Assinatura::where('clicksign_list_key', $listKey)
                ->whereNotIn('status', ['recusado', 'cancelado'])
                ->update([
                    'status'       => 'concluido',
                    'concluido_em' => now(),
                ]);

            Log::info('ClickSign: envelope concluído', ['list_key' => $listKey]);
        }
    }

    private function processarPrazoExpirado(array $payload): void
    {
        $listKey = $payload['document']['key'] ?? $payload['event']['data']['document']['key'] ?? null;

        if ($listKey) {
            Assinatura::where('clicksign_list_key', $listKey)
                ->whereIn('status', ['enviado', 'assinando'])
                ->update(['status' => 'cancelado']);

            Log::warning('ClickSign: prazo expirado', ['list_key' => $listKey]);
        }
    }
}
