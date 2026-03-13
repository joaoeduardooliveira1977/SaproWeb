<?php

namespace App\Console\Commands;

use App\Models\Notificacao;
use App\Models\Prazo;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GerarNotificacoes extends Command
{
    protected $signature   = 'notificacoes:gerar';
    protected $description = 'Gera notificações internas para prazos e honorários';

    public function handle(): void
    {
        $this->processarPrazos();
        $this->processarHonorarios();
        $this->enviarEmailsResumo();

        $this->info('Notificações geradas com sucesso.');
    }

    // ── Prazos ──────────────────────────────────────────────────

    private function processarPrazos(): void
    {
        // Prazos que vencem em 1, 5 ou 15 dias
        $marcos = [
            1  => 'prazo_vencendo',
            5  => 'prazo_vencendo',
            15 => 'prazo_vencendo',
        ];

        foreach ($marcos as $dias => $tipo) {
            $data = today()->addDays($dias);

            $prazos = Prazo::with(['processo.cliente', 'responsavel'])
                ->where('status', 'aberto')
                ->whereDate('data_prazo', $data)
                ->get();

            foreach ($prazos as $prazo) {
                $tipoReal = $prazo->prazo_fatal ? 'prazo_fatal' : $tipo;

                if (Notificacao::jaExiste($tipoReal, 'prazo', $prazo->id)) {
                    continue;
                }

                $cliente = $prazo->processo?->cliente?->nome ?? '';
                $proc    = $prazo->processo?->numero ?? '';

                $titulo = $prazo->prazo_fatal
                    ? "🚨 PRAZO FATAL em {$dias} dia(s): {$prazo->titulo}"
                    : "⏳ Prazo em {$dias} dia(s): {$prazo->titulo}";

                $mensagem = "Vence em " . $prazo->data_prazo->format('d/m/Y');
                if ($proc)    $mensagem .= " | Processo: {$proc}";
                if ($cliente) $mensagem .= " | Cliente: {$cliente}";

                Notificacao::create([
                    'usuario_id'      => $prazo->responsavel_id,
                    'tipo'            => $tipoReal,
                    'titulo'          => $titulo,
                    'mensagem'        => $mensagem,
                    'referencia_tipo' => 'prazo',
                    'referencia_id'   => $prazo->id,
                    'link'            => '/prazos',
                ]);
            }
        }

        // Prazos vencidos (qualquer data passada, abertos) — notifica uma vez por dia
        $vencidos = Prazo::with(['processo.cliente', 'responsavel'])
            ->where('status', 'aberto')
            ->whereDate('data_prazo', '<', today())
            ->get();

        foreach ($vencidos as $prazo) {
            if (Notificacao::jaExiste('prazo_vencido', 'prazo', $prazo->id)) {
                continue;
            }

            $cliente    = $prazo->processo?->cliente?->nome ?? '';
            $diasAtraso = $prazo->data_prazo->diffInDays(today());

            Notificacao::create([
                'usuario_id'      => $prazo->responsavel_id,
                'tipo'            => 'prazo_vencido',
                'titulo'          => "❌ Prazo vencido há {$diasAtraso} dia(s): {$prazo->titulo}",
                'mensagem'        => "Venceu em " . $prazo->data_prazo->format('d/m/Y')
                                     . ($cliente ? " | Cliente: {$cliente}" : ''),
                'referencia_tipo' => 'prazo',
                'referencia_id'   => $prazo->id,
                'link'            => '/prazos',
            ]);
        }
    }

    // ── Honorários ──────────────────────────────────────────────

    private function processarHonorarios(): void
    {
        $parcelas = DB::table('honorario_parcelas as hp')
            ->join('honorarios as h', 'h.id', '=', 'hp.honorario_id')
            ->leftJoin('pessoas as cl', 'cl.id', '=', 'h.cliente_id')
            ->select('hp.id', 'hp.numero_parcela', 'hp.valor', 'hp.vencimento', 'cl.nome as cliente_nome')
            ->whereIn('hp.status', ['pendente', 'atrasado'])
            ->whereRaw('hp.vencimento < CURRENT_DATE')
            ->get();

        foreach ($parcelas as $parcela) {
            if (Notificacao::jaExiste('honorario_atrasado', 'honorario_parcela', $parcela->id)) {
                continue;
            }

            $diasAtraso = Carbon::parse($parcela->vencimento)->diffInDays(today());

            Notificacao::create([
                'usuario_id'      => null, // visível para todos os financeiros/admin
                'tipo'            => 'honorario_atrasado',
                'titulo'          => "💸 Honorário em atraso: {$parcela->cliente_nome}",
                'mensagem'        => "Parcela {$parcela->numero_parcela} — R$ "
                                     . number_format($parcela->valor, 2, ',', '.')
                                     . " — {$diasAtraso} dia(s) em atraso",
                'referencia_tipo' => 'honorario_parcela',
                'referencia_id'   => $parcela->id,
                'link'            => '/honorarios',
            ]);
        }
    }

    // ── E-mail resumo diário ─────────────────────────────────────

    private function enviarEmailsResumo(): void
    {
        $usuarios = Usuario::where('ativo', true)
            ->whereNotNull('email')
            ->get();

        $tiposLabel = [
            'prazo_fatal'        => 'Prazo Fatal',
            'prazo_vencendo'     => 'Prazo a Vencer',
            'prazo_vencido'      => 'Prazo Vencido',
            'honorario_atrasado' => 'Honorário em Atraso',
        ];

        // Ordem de prioridade para exibição
        $ordemTipo = ['prazo_fatal' => 0, 'prazo_vencido' => 1, 'prazo_vencendo' => 2, 'honorario_atrasado' => 3];

        foreach ($usuarios as $usuario) {
            $notifs = Notificacao::paraUsuario($usuario->id)
                ->naoLidas()
                ->whereDate('created_at', today())
                ->get()
                ->sortBy(fn($n) => $ordemTipo[$n->tipo] ?? 99)
                ->values();

            if ($notifs->isEmpty()) {
                continue;
            }

            $dataFmt   = now()->format('d/m/Y');
            $geradoEm  = now()->format('d/m/Y H:i');
            $total     = $notifs->count();
            $fatais    = $notifs->where('tipo', 'prazo_fatal')->count();
            $vencidos  = $notifs->where('tipo', 'prazo_vencido')->count();
            $vencendo  = $notifs->where('tipo', 'prazo_vencendo')->count();
            $honAtras  = $notifs->where('tipo', 'honorario_atrasado')->count();

            $corpo = "
            <div style='font-family:Arial,Helvetica,sans-serif;max-width:680px;margin:0 auto;background:#f1f5f9;'>

            <div style='background:#1a3a5c;padding:28px 36px 24px;border-radius:8px 8px 0 0;'>
                <div style='color:#93c5fd;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;'>Sistema Jurídico SAPRO</div>
                <div style='color:#fff;font-size:24px;font-weight:700;line-height:1.2;'>Notificações do dia &mdash; {$dataFmt}</div>
                <div style='color:#93c5fd;font-size:13px;margin-top:6px;'>Olá, {$usuario->nome}</div>
            </div>

            <div style='background:#fff;border:1px solid #e2e8f0;border-top:none;padding:16px 36px;'>
                <table width='100%' cellpadding='0' cellspacing='0'>
                    <tr>";

            $resumo = [];
            if ($fatais)   $resumo[] = ['🚨', $fatais,   'Fatal(is)',  '#fce7f3', '#9d174d'];
            if ($vencidos) $resumo[] = ['❌', $vencidos,  'Vencido(s)', '#fee2e2', '#991b1b'];
            if ($vencendo) $resumo[] = ['⏳', $vencendo,  'A Vencer',   '#fef9c3', '#854d0e'];
            if ($honAtras) $resumo[] = ['💸', $honAtras,  'Hon. Atraso','#ede9fe', '#5b21b6'];

            $primeiro = true;
            foreach ($resumo as $r) {
                if (!$primeiro) {
                    $corpo .= "<td style='width:1px;background:#e2e8f0;'></td>";
                }
                $corpo .= "
                    <td style='text-align:center;padding:12px 8px;'>
                        <div style='font-size:22px;line-height:1;margin-bottom:4px;'>{$r[0]}</div>
                        <div style='font-size:26px;font-weight:700;color:{$r[4]};line-height:1;'>{$r[1]}</div>
                        <div style='font-size:10px;color:#64748b;margin-top:4px;'>{$r[2]}</div>
                    </td>";
                $primeiro = false;
            }

            $corpo .= "
                    </tr>
                </table>
            </div>

            <div style='padding:20px 36px;'>";

            // Agrupar por tipo
            $grupos = $notifs->groupBy('tipo');
            $grupoOrdem = ['prazo_fatal', 'prazo_vencido', 'prazo_vencendo', 'honorario_atrasado'];

            foreach ($grupoOrdem as $tipo) {
                if (!$grupos->has($tipo)) continue;

                $itens     = $grupos[$tipo];
                $label     = $tiposLabel[$tipo] ?? $tipo;
                $primeira  = $itens->first();
                $corFundo  = $primeira->cor();

                $corpo .= "
                <div style='background:{$corFundo};padding:10px 16px;border-radius:4px;margin-top:20px;margin-bottom:4px;'>
                    <div style='font-size:14px;font-weight:700;color:#1e293b;'>{$primeira->icone()} {$label}</div>
                    <div style='font-size:11px;color:#64748b;margin-top:2px;'>{$itens->count()} item(ns)</div>
                </div>";

                $seq   = 1;
                $qtd   = $itens->count();
                foreach ($itens as $n) {
                    $corpo .= "
                    <div style='background:#fff;border:1px solid #e2e8f0;border-left:4px solid #2563eb;border-radius:0 4px 4px 0;overflow:hidden;'>
                        <div style='padding:10px 14px;'>
                            <div style='font-size:13px;font-weight:700;color:#1e293b;margin-bottom:4px;'>{$n->titulo}</div>
                            <div style='font-size:12px;color:#64748b;'>{$n->mensagem}</div>
                        </div>
                    </div>";
                    if ($seq < $qtd) {
                        $corpo .= "<div style='border-top:1px solid #e2e8f0;'></div>";
                    }
                    $seq++;
                }
            }

            $corpo .= "
            </div>
            <div style='background:#e2e8f0;border:1px solid #cbd5e1;border-top:none;padding:14px 36px;border-radius:0 0 8px 8px;text-align:center;'>
                <span style='font-size:11px;color:#64748b;'>Gerado pelo Sistema Jurídico SAPRO &nbsp;·&nbsp; {$geradoEm}</span>
            </div>

            </div>";

            try {
                Mail::html($corpo, function ($msg) use ($usuario, $dataFmt, $total) {
                    $msg->to($usuario->email)
                        ->subject("🔔 {$total} notificação(ões) — {$dataFmt}");
                });
            } catch (\Exception) {
                // silencia falha de e-mail individual
            }
        }
    }
}
