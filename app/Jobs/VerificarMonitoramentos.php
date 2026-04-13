<?php

namespace App\Jobs;

use App\Models\Andamento;
use App\Models\Monitoramento;
use App\Models\Notificacao;
use App\Models\Processo;
use App\Services\TribunalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificarMonitoramentos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function handle(): void
    {
        // Sem tenant context no job: BelongsToTenant não aplica scope (tenant_id() = null → if($tenantId) não entra)
        $monitoramentos = Monitoramento::with(['processo.advogado'])
            ->where('ativo', true)
            ->get();

        if ($monitoramentos->isEmpty()) return;

        $service = new TribunalService();

        foreach ($monitoramentos as $mon) {
            try {
                $resultado = $service->consultarProcesso($mon->numero_processo);

                if (!$resultado['sucesso'] || empty($resultado['andamentos'])) {
                    usleep(300000);
                    continue;
                }

                $andamentos = $resultado['andamentos']; // sorted desc by date
                $latest     = $andamentos[0];
                $novoHash   = md5($latest['data'] . '|' . $latest['descricao']);

                // Atualiza tribunal se necessário
                if (!$mon->tribunal && !empty($resultado['tribunal'])) {
                    $mon->tribunal = $resultado['tribunal'];
                }

                // Sincronização inicial: apenas armazena hash, sem notificar
                if (!$mon->ultimo_andamento_hash) {
                    $mon->update([
                        'ultimo_andamento_hash' => $novoHash,
                        'ultimo_andamento_data' => $latest['data'],
                        'tribunal'              => $resultado['tribunal'] ?? $mon->tribunal,
                    ]);
                    usleep(500000);
                    continue;
                }

                // Sem novidade
                if ($novoHash === $mon->ultimo_andamento_hash) {
                    usleep(300000);
                    continue;
                }

                // Há andamentos novos — filtra os mais recentes que o último registrado
                $ultimaData = $mon->ultimo_andamento_data?->format('Y-m-d') ?? '1900-01-01';
                $novos = collect($andamentos)
                    ->filter(fn($a) => $a['data'] > $ultimaData)
                    ->values()
                    ->all();

                // Se não há andamentos com data posterior (ex: descrição mudou), usa todos novos de mesma data
                if (empty($novos)) {
                    $novos = collect($andamentos)
                        ->filter(fn($a) => $a['data'] >= $ultimaData)
                        ->values()
                        ->all();
                }

                // Salva andamentos no processo cadastrado
                if ($mon->processo_id) {
                    foreach ($novos as $a) {
                        $existe = Andamento::withoutGlobalScopes()
                            ->where('processo_id', $mon->processo_id)
                            ->whereDate('data', $a['data'])
                            ->where('descricao', $a['descricao'])
                            ->exists();

                        if (!$existe) {
                            $andamento = new Andamento([
                                'processo_id' => $mon->processo_id,
                                'data'        => $a['data'],
                                'descricao'   => $a['descricao'],
                            ]);
                            $andamento->tenant_id = $mon->tenant_id;
                            $andamento->save();
                        }
                    }
                }

                // Cria notificação interna
                if (!Notificacao::jaExiste('monitoramento_andamento', 'monitoramento', $mon->id, $novoHash)) {
                    Notificacao::create([
                        'usuario_id'      => null,
                        'tipo'            => 'monitoramento_andamento',
                        'titulo'          => count($novos) . ' andamento(s) novo(s): ' . $mon->numero_processo,
                        'mensagem'        => 'Monitoramento automático detectou novos movimentos no processo ' . $mon->numero_processo . ' (' . ($resultado['tribunal'] ?? $mon->tribunal) . ').',
                        'referencia_tipo' => 'monitoramento',
                        'referencia_id'   => $mon->id,
                        'link'            => '/tjsp#' . $novoHash,
                        'lida'            => false,
                    ]);
                }

                // Envia e-mail se habilitado
                if ($mon->notificar_email && $mon->processo?->advogado?->email) {
                    $this->enviarEmail($mon, $novos, $resultado['tribunal'] ?? '');
                }

                // Atualiza monitoramento
                $mon->update([
                    'ultimo_andamento_hash' => $novoHash,
                    'ultimo_andamento_data' => $latest['data'],
                    'tribunal'              => $resultado['tribunal'] ?? $mon->tribunal,
                ]);

                Log::info('VerificarMonitoramentos: novos andamentos', [
                    'numero'  => $mon->numero_processo,
                    'novos'   => count($novos),
                    'tribunal'=> $resultado['tribunal'] ?? '',
                ]);

                usleep(500000);

            } catch (\Throwable $e) {
                Log::error('VerificarMonitoramentos: erro', [
                    'id'    => $mon->id,
                    'numero'=> $mon->numero_processo,
                    'erro'  => $e->getMessage(),
                ]);
                usleep(300000);
            }
        }
    }

    private function enviarEmail(Monitoramento $mon, array $novos, string $tribunal): void
    {
        try {
            $advogado = $mon->processo->advogado;
            $email    = $advogado->email;
            $nome     = $advogado->nome ?? 'Advogado';

            $linhasHtml = '';
            foreach (array_slice($novos, 0, 5) as $a) {
                $data  = \Carbon\Carbon::parse($a['data'])->format('d/m/Y');
                $desc  = htmlspecialchars($a['descricao']);
                $linhasHtml .= "<tr>
                    <td style='padding:8px 12px;border-bottom:1px solid #f0f0f0;color:#555;font-size:13px;white-space:nowrap;'>{$data}</td>
                    <td style='padding:8px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;'>{$desc}</td>
                </tr>";
            }

            if (count($novos) > 5) {
                $restante = count($novos) - 5;
                $linhasHtml .= "<tr><td colspan='2' style='padding:8px 12px;font-size:12px;color:#94a3b8;font-style:italic;'>...e mais {$restante} andamento(s)</td></tr>";
            }

            $tribunalLabel = $tribunal ?: ($mon->tribunal ?? 'tribunal');
            $url           = rtrim(config('app.url'), '/') . '/tjsp';
            $totalNovos    = count($novos);

            $corpo = "<!DOCTYPE html><html lang='pt-BR'><body style='margin:0;padding:0;background:#f1f5f9;font-family:sans-serif;'>
<table width='100%' cellpadding='0' cellspacing='0' style='padding:32px 16px;'>
<tr><td align='center'>
<table width='600' cellpadding='0' cellspacing='0' style='max-width:600px;width:100%;'>
  <tr><td style='background:#1a3a5c;padding:24px;border-radius:10px 10px 0 0;'>
    <p style='margin:0;color:#93c5fd;font-size:12px;text-transform:uppercase;letter-spacing:1px;'>Software Jur�dico — Monitoramento Automático</p>
    <h2 style='margin:8px 0 0;color:#fff;font-size:20px;'>📋 Novos andamentos detectados</h2>
  </td></tr>
  <tr><td style='background:#fff;padding:24px;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 10px 10px;'>
    <p style='color:#374151;margin:0 0 12px;'>Olá, <strong>{$nome}</strong>.</p>
    <p style='color:#374151;margin:0 0 16px;'>O monitoramento automático detectou <strong>{$totalNovos} novo(s) andamento(s)</strong> no processo:</p>
    <div style='background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;margin:0 0 20px;'>
      <p style='margin:0;font-size:15px;font-weight:700;color:#1e40af;'>{$mon->numero_processo}</p>
      <p style='margin:4px 0 0;font-size:12px;color:#64748b;'>{$tribunalLabel}</p>
    </div>
    <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin:0 0 20px;'>
      <thead><tr>
        <th style='text-align:left;padding:8px 12px;background:#f8fafc;font-size:11px;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;'>Data</th>
        <th style='text-align:left;padding:8px 12px;background:#f8fafc;font-size:11px;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;'>Andamento</th>
      </tr></thead>
      <tbody>{$linhasHtml}</tbody>
    </table>
    <a href='{$url}' style='display:inline-block;background:#1a3a5c;color:#fff;padding:11px 22px;border-radius:7px;text-decoration:none;font-weight:600;font-size:13px;'>Ver no Software Jur�dico →</a>
    <p style='margin:20px 0 0;font-size:11px;color:#94a3b8;'>Para desativar este monitoramento, acesse a aba Monitoramentos na página de Consulta Judicial.</p>
  </td></tr>
</table>
</td></tr>
</table>
</body></html>";

            Mail::html($corpo, function ($msg) use ($email, $mon) {
                $msg->to($email)->subject('Software Jur�dico — Novo andamento: ' . $mon->numero_processo);
            });

        } catch (\Throwable $e) {
            Log::warning('VerificarMonitoramentos: falha ao enviar e-mail: ' . $e->getMessage(), [
                'monitoramento_id' => $mon->id,
            ]);
        }
    }
}
