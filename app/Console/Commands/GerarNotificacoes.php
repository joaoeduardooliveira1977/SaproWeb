<?php

namespace App\Console\Commands;

use App\Mail\DiarioNotificacoes;
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
        $this->processarLancamentosAtrasados();
        $this->processarProcessosSemAndamento();
        $this->enviarEmailsResumo();

        $this->info('Notificações geradas com sucesso.');
    }

    // ── Prazos ──────────────────────────────────────────────────

    private function processarPrazos(): void
    {
        // Prazos que vencem hoje, em 1, 5 ou 15 dias
        $marcos = [
            0  => 'prazo_hoje',
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
                $tipoReal = match(true) {
                    $prazo->prazo_fatal && $dias === 0 => 'prazo_fatal',
                    $prazo->prazo_fatal               => 'prazo_fatal',
                    $dias === 0                        => 'prazo_hoje',
                    default                            => $tipo,
                };

                if (Notificacao::jaExiste($tipoReal, 'prazo', $prazo->id)) {
                    continue;
                }

                $cliente = $prazo->processo?->cliente?->nome ?? '';
                $proc    = $prazo->processo?->numero ?? '';

                $titulo = match(true) {
                    $prazo->prazo_fatal && $dias === 0 => "🚨 PRAZO FATAL HOJE: {$prazo->titulo}",
                    $prazo->prazo_fatal               => "🚨 PRAZO FATAL em {$dias} dia(s): {$prazo->titulo}",
                    $dias === 0                        => "🔔 Prazo vence HOJE: {$prazo->titulo}",
                    default                            => "⏳ Prazo em {$dias} dia(s): {$prazo->titulo}",
                };

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

    // ── Lançamentos financeiros atrasados (novo módulo) ──────────

    private function processarLancamentosAtrasados(): void
    {
        $lancamentos = DB::table('financeiro_lancamentos as fl')
            ->join('pessoas as p', 'p.id', '=', 'fl.cliente_id')
            ->where('fl.status', 'atrasado')
            ->where('fl.tipo', 'receita')
            ->select('fl.id', 'fl.descricao', 'fl.valor', 'fl.vencimento', 'p.nome as cliente_nome')
            ->get();

        foreach ($lancamentos as $lanc) {
            if (Notificacao::jaExiste('lancamento_atrasado', 'financeiro_lancamento', $lanc->id)) {
                continue;
            }

            $diasAtraso = Carbon::parse($lanc->vencimento)->diffInDays(today());

            Notificacao::create([
                'usuario_id'      => null,
                'tipo'            => 'lancamento_atrasado',
                'titulo'          => "💰 Recebimento atrasado: {$lanc->cliente_nome}",
                'mensagem'        => "{$lanc->descricao} — R$ "
                                     . number_format($lanc->valor, 2, ',', '.')
                                     . " — {$diasAtraso} dia(s) em atraso",
                'referencia_tipo' => 'financeiro_lancamento',
                'referencia_id'   => $lanc->id,
                'link'            => '/financeiro',
            ]);
        }
    }

    // ── Processos sem andamento há 30+ dias ─────────────────────

    private function processarProcessosSemAndamento(): void
    {
        $processos = DB::select("
            SELECT p.id, p.numero, pe.nome as cliente_nome,
                   MAX(a.data) as ultimo_andamento
            FROM processos p
            LEFT JOIN pessoas pe ON pe.id = p.cliente_id
            LEFT JOIN andamentos a ON a.processo_id = p.id
            WHERE p.status = 'Ativo'
            GROUP BY p.id, p.numero, pe.nome
            HAVING MAX(a.data) < CURRENT_DATE - INTERVAL '30 days'
               OR MAX(a.data) IS NULL
        ");

        foreach ($processos as $proc) {
            if (Notificacao::jaExiste('processo_sem_andamento', 'processo', $proc->id)) {
                continue;
            }

            $ultimo = $proc->ultimo_andamento
                ? Carbon::parse($proc->ultimo_andamento)->format('d/m/Y')
                : 'nenhum';

            Notificacao::create([
                'usuario_id'      => null,
                'tipo'            => 'processo_sem_andamento',
                'titulo'          => "📋 Processo sem andamento: {$proc->numero}",
                'mensagem'        => "Cliente: {$proc->cliente_nome} — Último andamento: {$ultimo}",
                'referencia_tipo' => 'processo',
                'referencia_id'   => $proc->id,
                'link'            => "/processos/{$proc->id}/andamentos",
            ]);
        }
    }

    // ── E-mail resumo diário ─────────────────────────────────────

    private function enviarEmailsResumo(): void
    {
        $ordemTipo = [
            'prazo_fatal' => 0, 'prazo_vencido' => 1, 'prazo_vencendo' => 2,
            'honorario_atrasado' => 3, 'processo_sem_andamento' => 4,
        ];

        Usuario::where('ativo', true)
            ->whereNotNull('email')
            ->get()
            ->each(function (Usuario $usuario) use ($ordemTipo) {
                $notifs = Notificacao::paraUsuario($usuario->id)
                    ->naoLidas()
                    ->whereDate('created_at', today())
                    ->get()
                    ->sortBy(fn($n) => $ordemTipo[$n->tipo] ?? 99)
                    ->values();

                if ($notifs->isEmpty()) {
                    return;
                }

                try {
                    Mail::to($usuario->email)->send(new DiarioNotificacoes($usuario, $notifs));
                } catch (\Exception) {
                    // silencia falha individual
                }
            });
    }
}
