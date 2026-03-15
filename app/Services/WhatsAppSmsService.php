<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envio de WhatsApp e SMS via Twilio.
 *
 * Variáveis de ambiente:
 *   TWILIO_ACCOUNT_SID=ACxxxxx
 *   TWILIO_AUTH_TOKEN=xxxxxxxx
 *   TWILIO_WHATSAPP_FROM=whatsapp:+14155238886   (sandbox) ou seu número aprovado
 *   TWILIO_SMS_FROM=+5511999999999               (número Twilio com SMS habilitado)
 *   TWILIO_CANAL_PADRAO=whatsapp                 (whatsapp|sms)
 *
 * Para usar o sandbox do WhatsApp: o destinatário deve ter enviado
 *   "join <palavra>" para o número sandbox antes de receber mensagens.
 */
class WhatsAppSmsService
{
    private string $sid;
    private string $token;
    private string $fromWhatsapp;
    private string $fromSms;
    private string $canalPadrao;

    public function __construct()
    {
        $this->sid          = config('services.twilio.sid', '');
        $this->token        = config('services.twilio.token', '');
        $this->fromWhatsapp = config('services.twilio.from_whatsapp', '');
        $this->fromSms      = config('services.twilio.from_sms', '');
        $this->canalPadrao  = config('services.twilio.canal_padrao', 'whatsapp');
    }

    public function configurado(): bool
    {
        return !empty($this->sid) && !empty($this->token);
    }

    // ── Envio ─────────────────────────────────────────────────

    /**
     * Envia mensagem pelo canal padrão configurado.
     * Registra o envio na tabela notificacoes_whatsapp.
     * Retorna true em caso de sucesso, false em falha.
     */
    public function enviar(
        string  $telefone,
        string  $mensagem,
        string  $destinatarioNome,
        string  $tipo            = 'teste',
        string  $canal           = '',
        ?string $referenciaTipo  = null,
        ?int    $referenciaId    = null
    ): bool {
        $canal = $canal ?: $this->canalPadrao;

        // Normaliza telefone para formato E.164
        $fone = $this->normalizarTelefone($telefone);

        if (!$fone) {
            $this->registrar($canal, $tipo, $destinatarioNome, $telefone, $mensagem,
                'falha', null, 'Telefone inválido: ' . $telefone,
                $referenciaTipo, $referenciaId);
            return false;
        }

        if (!$this->configurado()) {
            Log::warning('WhatsAppSmsService: Twilio não configurado — mensagem não enviada', [
                'destinatario' => $destinatarioNome,
                'telefone'     => $fone,
                'tipo'         => $tipo,
            ]);
            $this->registrar($canal, $tipo, $destinatarioNome, $fone, $mensagem,
                'falha', null, 'Twilio não configurado',
                $referenciaTipo, $referenciaId);
            return false;
        }

        try {
            if ($canal === 'sms') {
                $sid = $this->enviarSms($fone, $mensagem);
            } else {
                $sid = $this->enviarWhatsapp($fone, $mensagem);
            }

            $this->registrar($canal, $tipo, $destinatarioNome, $fone, $mensagem,
                'enviado', $sid, null, $referenciaTipo, $referenciaId);

            Log::info("WhatsAppSmsService: {$canal} enviado", [
                'destinatario' => $destinatarioNome,
                'telefone'     => $fone,
                'tipo'         => $tipo,
                'sid'          => $sid,
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error("WhatsAppSmsService: falha ao enviar {$canal}", [
                'destinatario' => $destinatarioNome,
                'telefone'     => $fone,
                'erro'         => $e->getMessage(),
            ]);

            $this->registrar($canal, $tipo, $destinatarioNome, $fone, $mensagem,
                'falha', null, $e->getMessage(), $referenciaTipo, $referenciaId);

            return false;
        }
    }

    // ── Verificação de duplicatas ─────────────────────────────

    /**
     * Retorna true se já foi enviada notificação do mesmo tipo
     * para a mesma referência hoje.
     */
    public function jaEnviado(string $tipo, string $refTipo, int $refId): bool
    {
        return DB::table('notificacoes_whatsapp')
            ->where('tipo', $tipo)
            ->where('referencia_tipo', $refTipo)
            ->where('referencia_id', $refId)
            ->where('status', 'enviado')
            ->whereDate('created_at', today())
            ->exists();
    }

    // ── Internos ──────────────────────────────────────────────

    private function enviarWhatsapp(string $fone, string $mensagem): string
    {
        if (empty($this->fromWhatsapp)) {
            throw new \RuntimeException('TWILIO_WHATSAPP_FROM não configurado.');
        }

        $response = Http::withBasicAuth($this->sid, $this->token)
            ->timeout(15)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                'From' => $this->fromWhatsapp,
                'To'   => 'whatsapp:' . $fone,
                'Body' => $mensagem,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Twilio WhatsApp: HTTP ' . $response->status() . ' — ' . $response->body());
        }

        return $response->json('sid', '');
    }

    private function enviarSms(string $fone, string $mensagem): string
    {
        if (empty($this->fromSms)) {
            throw new \RuntimeException('TWILIO_SMS_FROM não configurado.');
        }

        $response = Http::withBasicAuth($this->sid, $this->token)
            ->timeout(15)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                'From' => $this->fromSms,
                'To'   => $fone,
                'Body' => $mensagem,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Twilio SMS: HTTP ' . $response->status() . ' — ' . $response->body());
        }

        return $response->json('sid', '');
    }

    /**
     * Normaliza para E.164. Assume Brasil (+55) se não tiver código de país.
     */
    public function normalizarTelefone(string $fone): ?string
    {
        $limpo = preg_replace('/[^0-9+]/', '', $fone);

        if (empty($limpo)) return null;

        // Já com código de país
        if (str_starts_with($limpo, '+')) {
            return strlen($limpo) >= 12 ? $limpo : null;
        }

        // Remove leading zeros
        $limpo = ltrim($limpo, '0');

        // Brasil: 10 ou 11 dígitos (sem DDI)
        if (strlen($limpo) >= 10 && strlen($limpo) <= 11) {
            return '+55' . $limpo;
        }

        // Número já com DDI sem +
        if (strlen($limpo) >= 12) {
            return '+' . $limpo;
        }

        return null;
    }

    private function registrar(
        string  $canal,
        string  $tipo,
        string  $nome,
        string  $telefone,
        string  $mensagem,
        string  $status,
        ?string $sid,
        ?string $erro,
        ?string $refTipo,
        ?int    $refId
    ): void {
        DB::table('notificacoes_whatsapp')->insert([
            'canal'               => $canal,
            'tipo'                => $tipo,
            'destinatario_nome'   => $nome,
            'destinatario_telefone' => $telefone,
            'mensagem'            => $mensagem,
            'status'              => $status,
            'twilio_sid'          => $sid,
            'erro'                => $erro,
            'referencia_tipo'     => $refTipo,
            'referencia_id'       => $refId,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }
}
