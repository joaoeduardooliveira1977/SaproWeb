<?php

namespace App\Console\Commands;

use App\Services\WhatsAppSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnviarWhatsappSms extends Command
{
    protected $signature = 'notificacoes:whatsapp
                            {--dry-run : Simula o envio sem chamar a API}
                            {--tipo=todos : prazo|cobranca|audiencia|todos}';

    protected $description = 'Envia notificações de prazos, cobranças e audiências via WhatsApp/SMS (Twilio)';

    private WhatsAppSmsService $svc;
    private bool $dryRun;

    public function handle(): void
    {
        $this->svc    = new WhatsAppSmsService();
        $this->dryRun = (bool) $this->option('dry-run');
        $tipo         = $this->option('tipo');

        if ($this->dryRun) {
            $this->warn('⚠️  Modo dry-run — nenhuma mensagem será enviada.');
        }

        if (!$this->svc->configurado() && !$this->dryRun) {
            $this->warn('Twilio não configurado. Configure TWILIO_ACCOUNT_SID e TWILIO_AUTH_TOKEN no .env.');
            $this->warn('Use --dry-run para testar sem credenciais.');
        }

        $enviados = 0;

        if (in_array($tipo, ['prazo', 'todos'])) {
            $enviados += $this->notificarPrazos();
        }

        if (in_array($tipo, ['cobranca', 'todos'])) {
            $enviados += $this->notificarCobrancas();
        }

        if (in_array($tipo, ['audiencia', 'todos'])) {
            $enviados += $this->notificarAudiencias();
        }

        $this->info("✅ {$enviados} mensagem(ns) processada(s).");
    }

    // ── Prazos ────────────────────────────────────────────────

    private function notificarPrazos(): int
    {
        $enviados = 0;

        // Dias de antecedência para notificar
        foreach ([1, 3, 7] as $dias) {
            $data   = today()->addDays($dias);
            $prazos = DB::select("
                SELECT pz.id, pz.titulo, pz.data_prazo, pz.prazo_fatal,
                       pe.nome as responsavel_nome, pe.celular as responsavel_celular,
                       pr.numero as processo_numero,
                       cl.nome as cliente_nome
                FROM prazos pz
                JOIN usuarios u ON u.id = pz.responsavel_id
                JOIN pessoas pe ON pe.id = u.pessoa_id
                LEFT JOIN processos pr ON pr.id = pz.processo_id
                LEFT JOIN pessoas cl ON cl.id = pr.cliente_id
                WHERE pz.status = 'aberto'
                  AND DATE(pz.data_prazo) = ?
                  AND pe.celular IS NOT NULL
                  AND pe.celular <> ''
            ", [$data->format('Y-m-d')]);

            foreach ($prazos as $prazo) {
                $tipo = $prazo->prazo_fatal ? 'prazo_fatal' : 'prazo_vencendo';

                if ($this->svc->jaEnviado($tipo, 'prazo', $prazo->id)) {
                    continue;
                }

                $diasLabel = $dias === 1 ? 'AMANHÃ' : "em {$dias} dias";
                $emoji     = $prazo->prazo_fatal ? '🚨' : '⏳';
                $fatal     = $prazo->prazo_fatal ? ' *(FATAL)*' : '';

                $msg  = "{$emoji} *SAPRO Jurídico — Prazo{$fatal}*\n";
                $msg .= "Olá {$prazo->responsavel_nome}!\n\n";
                $msg .= "Prazo vence {$diasLabel}: *{$prazo->titulo}*\n";
                $msg .= "Data: " . Carbon::parse($prazo->data_prazo)->format('d/m/Y') . "\n";
                if ($prazo->processo_numero) {
                    $msg .= "Processo: {$prazo->processo_numero}";
                    if ($prazo->cliente_nome) $msg .= " | {$prazo->cliente_nome}";
                    $msg .= "\n";
                }

                $enviados += $this->enviar(
                    telefone:       $prazo->responsavel_celular,
                    mensagem:       $msg,
                    nome:           $prazo->responsavel_nome,
                    tipo:           $tipo,
                    refTipo:        'prazo',
                    refId:          $prazo->id
                );
            }
        }

        return $enviados;
    }

    // ── Cobranças ─────────────────────────────────────────────

    private function notificarCobrancas(): int
    {
        $enviados = 0;

        // Parcelas atrasadas há 3, 7 ou 15 dias (primeiro aviso de cada)
        $parcelas = DB::select("
            SELECT hp.id, hp.numero_parcela, hp.valor, hp.vencimento,
                   cl.nome as cliente_nome, cl.celular as cliente_celular,
                   (CURRENT_DATE - hp.vencimento) as dias_atraso
            FROM honorario_parcelas hp
            JOIN honorarios h ON h.id = hp.honorario_id
            JOIN pessoas cl ON cl.id = h.cliente_id
            WHERE hp.status = 'atrasado'
              AND cl.celular IS NOT NULL
              AND cl.celular <> ''
              AND (CURRENT_DATE - hp.vencimento) IN (3, 7, 15)
        ");

        foreach ($parcelas as $parcela) {
            if ($this->svc->jaEnviado('cobranca', 'honorario_parcela', $parcela->id)) {
                continue;
            }

            $msg  = "💳 *SAPRO Jurídico — Aviso de Pagamento*\n";
            $msg .= "Olá *{$parcela->cliente_nome}*!\n\n";
            $msg .= "Identificamos a parcela {$parcela->numero_parcela}ª ";
            $msg .= "de R$ " . number_format($parcela->valor, 2, ',', '.') . " em aberto.\n";
            $msg .= "Vencimento: " . Carbon::parse($parcela->vencimento)->format('d/m/Y');
            $msg .= " ({$parcela->dias_atraso} dia(s) em atraso)\n\n";
            $msg .= "Entre em contato para regularizar.";

            $enviados += $this->enviar(
                telefone: $parcela->cliente_celular,
                mensagem: $msg,
                nome:     $parcela->cliente_nome,
                tipo:     'cobranca',
                refTipo:  'honorario_parcela',
                refId:    $parcela->id
            );
        }

        return $enviados;
    }

    // ── Audiências ────────────────────────────────────────────

    private function notificarAudiencias(): int
    {
        $enviados = 0;
        $amanha   = today()->addDay();

        $audiencias = DB::select("
            SELECT au.id, au.tipo, au.data_hora, au.local, au.sala,
                   pe.nome as advogado_nome, pe.celular as advogado_celular,
                   pr.numero as processo_numero,
                   cl.nome as cliente_nome
            FROM audiencias au
            JOIN pessoas pe ON pe.id = au.advogado_id
            LEFT JOIN processos pr ON pr.id = au.processo_id
            LEFT JOIN pessoas cl ON cl.id = pr.cliente_id
            WHERE au.status = 'agendada'
              AND DATE(au.data_hora) = ?
              AND pe.celular IS NOT NULL
              AND pe.celular <> ''
        ", [$amanha->format('Y-m-d')]);

        foreach ($audiencias as $aud) {
            if ($this->svc->jaEnviado('audiencia', 'audiencia', $aud->id)) {
                continue;
            }

            $hora  = Carbon::parse($aud->data_hora)->format('H:i');
            $tipos = [
                'conciliacao'           => 'Conciliação',
                'instrucao'             => 'Instrução',
                'instrucao_julgamento'  => 'Instrução e Julgamento',
                'julgamento'            => 'Julgamento',
                'una'                   => 'UNA',
                'outra'                 => 'Audiência',
            ];
            $tipoLabel = $tipos[$aud->tipo] ?? 'Audiência';

            $msg  = "📅 *SAPRO Jurídico — Lembrete de Audiência*\n";
            $msg .= "Olá *{$aud->advogado_nome}*!\n\n";
            $msg .= "Você tem *{$tipoLabel}* amanhã às *{$hora}*.\n";
            if ($aud->processo_numero) {
                $msg .= "Processo: {$aud->processo_numero}";
                if ($aud->cliente_nome) $msg .= " | {$aud->cliente_nome}";
                $msg .= "\n";
            }
            if ($aud->local)  $msg .= "Local: {$aud->local}\n";
            if ($aud->sala)   $msg .= "Sala: {$aud->sala}\n";

            $enviados += $this->enviar(
                telefone: $aud->advogado_celular,
                mensagem: $msg,
                nome:     $aud->advogado_nome,
                tipo:     'audiencia',
                refTipo:  'audiencia',
                refId:    $aud->id
            );
        }

        return $enviados;
    }

    // ── Helper ────────────────────────────────────────────────

    private function enviar(
        string $telefone,
        string $mensagem,
        string $nome,
        string $tipo,
        string $refTipo,
        int    $refId
    ): int {
        if ($this->dryRun) {
            $this->line("  [DRY-RUN] {$tipo} → {$nome} ({$telefone})");
            return 1;
        }

        $ok = $this->svc->enviar(
            telefone:       $telefone,
            mensagem:       $mensagem,
            destinatarioNome: $nome,
            tipo:           $tipo,
            referenciaTipo: $refTipo,
            referenciaId:   $refId
        );

        if ($ok) {
            $this->line("  ✅ {$tipo} → {$nome}");
        } else {
            $this->warn("  ❌ Falha: {$tipo} → {$nome}");
        }

        return 1;
    }
}
