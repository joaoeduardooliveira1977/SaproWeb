<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceiroConsolidado extends Component
{
    use WithPagination;

    public string $abaAtiva     = 'visao';
    public string $filtroMes    = '';
    public string $filtroStatus = 'pendente';
    public int    $periodoFluxo = 6;           // meses: 3 | 6 | 12

    protected $queryString = [
        'abaAtiva'     => ['except' => 'visao'],
        'filtroMes'    => ['except' => ''],
        'filtroStatus' => ['except' => 'pendente'],
    ];

    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.partials.skeleton', ['cards' => 3, 'blocks' => 3, 'blockHeight' => 300]);
    }

    public function mount(): void
    {
        $this->filtroMes = now()->format('Y-m');
    }

    public function updatedAbaAtiva(): void
    {
        $this->resetPage();
    }

    // ── Métricas ────────────────────────────────────────────────

    private function metricas(): array
    {
        $inicioMes = now()->startOfMonth()->format('Y-m-d');
        $fimMes    = now()->endOfMonth()->format('Y-m-d');

        $a_receber = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM recebimentos
            WHERE recebido = false
        ")->total;

        $recebido_mes = DB::selectOne("
            SELECT COALESCE(SUM(valor_recebido),0) AS total
            FROM recebimentos
            WHERE recebido = true
              AND data_recebimento BETWEEN ? AND ?
        ", [$inicioMes, $fimMes])->total;

        $a_pagar = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM pagamentos
            WHERE pago = false
        ")->total;

        $pago_mes = DB::selectOne("
            SELECT COALESCE(SUM(valor_pago),0) AS total
            FROM pagamentos
            WHERE pago = true
              AND data_pagamento BETWEEN ? AND ?
        ", [$inicioMes, $fimMes])->total;

        $honorarios_atrasados = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM honorario_parcelas
            WHERE status IN ('atrasado','pendente')
              AND vencimento < CURRENT_DATE
        ")->total;

        $honorarios_vencer = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM honorario_parcelas
            WHERE status = 'pendente'
              AND vencimento >= CURRENT_DATE
        ")->total;

        return compact(
            'a_receber', 'recebido_mes',
            'a_pagar',   'pago_mes',
            'honorarios_atrasados', 'honorarios_vencer'
        );
    }

    // ── Prioridades de hoje ──────────────────────────────────────

    private function prioridades(): array
    {
        $rows = DB::table('recebimentos as r')
            ->join('processos as p', 'p.id', '=', 'r.processo_id')
            ->leftJoin('pessoas as cl', 'cl.id', '=', 'p.cliente_id')
            ->select('r.id', 'r.valor', 'r.data', 'cl.nome as cliente_nome')
            ->where('r.recebido', false)
            ->whereRaw("r.data <= ?", [today()->addDay()->format('Y-m-d')])
            ->orderBy('r.data')
            ->take(5)
            ->get();

        return $rows->map(function ($r) {
            $data     = Carbon::parse($r->data);
            $atrasado = $data->isPast() && !$data->isToday();
            return [
                'cliente'   => $r->cliente_nome ?? '—',
                'valor'     => (float) $r->valor,
                'descricao' => $atrasado
                    ? 'Cobrança imediata recomendada.'
                    : 'Enviar lembrete preventivo.',
                'urgencia'  => $atrasado ? 'vencido' : 'amanha',
                'tag'       => $atrasado
                    ? 'VENCIDO HÁ ' . today()->diffInDays($data) . ' DIA(S)'
                    : 'VENCE AMANHÃ',
            ];
        })->toArray();
    }

    // ── Agenda financeira próximos 7 dias ────────────────────────

    private function agendaFinanceira(): array
    {
        $rows = DB::table('recebimentos as r')
            ->join('processos as p', 'p.id', '=', 'r.processo_id')
            ->leftJoin('pessoas as cl', 'cl.id', '=', 'p.cliente_id')
            ->select('r.id', 'r.valor', 'r.data', 'cl.nome as cliente_nome')
            ->where('r.recebido', false)
            ->whereRaw("r.data BETWEEN ? AND ?", [
                today()->format('Y-m-d'),
                today()->addDays(7)->format('Y-m-d'),
            ])
            ->orderBy('r.data')
            ->take(5)
            ->get();

        return $rows->map(fn($r) => [
            'data'      => Carbon::parse($r->data)->format('d/m'),
            'titulo'    => 'Receber R$ ' . number_format($r->valor, 2, ',', '.'),
            'descricao' => $r->cliente_nome ?? '—',
            'tipo'      => 'entrada',
        ])->toArray();
    }

    // ── Ações rápidas ───────────────────────────────────────────

    public function marcarRecebido(int $id): void
    {
        DB::table('recebimentos')->where('id', $id)->update([
            'recebido'         => true,
            'data_recebimento' => today(),
            'valor_recebido'   => DB::table('recebimentos')->where('id', $id)->value('valor'),
            'updated_at'       => now(),
        ]);
        $this->dispatch('toast', message: 'Recebimento marcado como recebido.', type: 'success');
    }

    public function marcarPago(int $id): void
    {
        DB::table('pagamentos')->where('id', $id)->update([
            'pago'           => true,
            'data_pagamento' => today(),
            'valor_pago'     => DB::table('pagamentos')->where('id', $id)->value('valor'),
            'updated_at'     => now(),
        ]);
        $this->dispatch('toast', message: 'Pagamento marcado como pago.', type: 'success');
    }

    // ── Exportar CSV ────────────────────────────────────────────

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if ($this->abaAtiva === 'receber') {
            $rows = $this->queryReceber()->get();
            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Data','Processo','Cliente','Descrição','Valor','Status'], ';');
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->data ? Carbon::parse($r->data)->format('d/m/Y') : '',
                        $r->processo_numero ?? '',
                        $r->cliente_nome ?? '',
                        $r->descricao ?? '',
                        number_format($r->valor, 2, ',', '.'),
                        $r->recebido ? 'Recebido' : 'Pendente',
                    ], ';');
                }
                fclose($out);
            }, 'receber-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        if ($this->abaAtiva === 'pagar') {
            $rows = $this->queryPagar()->get();
            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Vencimento','Processo','Cliente','Fornecedor','Descrição','Valor','Status'], ';');
                foreach ($rows as $r) {
                    $vencido = !$r->pago && $r->data_vencimento && Carbon::parse($r->data_vencimento)->isPast();
                    fputcsv($out, [
                        $r->data_vencimento ? Carbon::parse($r->data_vencimento)->format('d/m/Y') : '',
                        $r->processo_numero ?? '',
                        $r->cliente_nome ?? '',
                        $r->fornecedor_nome ?? '',
                        $r->descricao ?? '',
                        number_format($r->valor, 2, ',', '.'),
                        $r->pago ? 'Pago' : ($vencido ? 'Vencido' : 'Pendente'),
                    ], ';');
                }
                fclose($out);
            }, 'pagar-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        // honorarios
        $rows = $this->queryHonorariosAtrasados()->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Vencimento','Dias Atraso','Cliente','Processo','Contrato','Parcela','Valor'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    Carbon::parse($r->vencimento)->format('d/m/Y'),
                    $r->dias_atraso,
                    $r->cliente_nome ?? '',
                    $r->processo_numero ?? '',
                    $r->honorario_descricao ?? '',
                    $r->numero_parcela.'ª',
                    number_format($r->valor, 2, ',', '.'),
                ], ';');
            }
            fclose($out);
        }, 'honorarios-atraso-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Fluxo de Caixa (mensal) ─────────────────────────────────

    private function fluxoCaixa(): array
    {
        $meses = [];
        for ($i = $this->periodoFluxo - 1; $i >= 0; $i--) {
            $meses[] = now()->subMonths($i)->format('Y-m');
        }

        $rows = [];
        foreach ($meses as $mes) {
            $recebido = DB::selectOne("
                SELECT COALESCE(SUM(valor_recebido),0) AS total
                FROM recebimentos
                WHERE recebido = true
                  AND TO_CHAR(data_recebimento,'YYYY-MM') = ?
            ", [$mes])->total;

            $pago = DB::selectOne("
                SELECT COALESCE(SUM(valor_pago),0) AS total
                FROM pagamentos
                WHERE pago = true
                  AND TO_CHAR(data_pagamento,'YYYY-MM') = ?
            ", [$mes])->total;

            $honorarios = DB::selectOne("
                SELECT COALESCE(SUM(valor),0) AS total
                FROM honorario_parcelas
                WHERE status = 'pago'
                  AND TO_CHAR(data_pagamento,'YYYY-MM') = ?
            ", [$mes])->total;

            $rows[] = [
                'mes'        => $mes,
                'label'      => Carbon::createFromFormat('Y-m', $mes)->translatedFormat('M/Y'),
                'recebido'   => (float) $recebido,
                'pago'       => (float) $pago,
                'honorarios' => (float) $honorarios,
                'saldo'      => (float) $recebido + (float) $honorarios - (float) $pago,
            ];
        }

        return $rows;
    }

    // ── Queries das abas ────────────────────────────────────────

    private function queryReceber()
    {
        $q = DB::table('recebimentos as r')
            ->join('processos as p', 'p.id', '=', 'r.processo_id')
            ->leftJoin('pessoas as cl', 'cl.id', '=', 'p.cliente_id')
            ->select(
                'r.id', 'r.descricao', 'r.valor', 'r.data',
                'r.data_recebimento', 'r.recebido',
                'p.numero as processo_numero',
                'cl.nome as cliente_nome'
            )
            ->orderBy('r.data');

        if ($this->filtroStatus === 'pendente') {
            $q->where('r.recebido', false);
        }

        if ($this->filtroMes) {
            $q->whereRaw("TO_CHAR(r.data,'YYYY-MM') = ?", [$this->filtroMes]);
        }

        return $q;
    }

    private function queryPagar()
    {
        $q = DB::table('pagamentos as p')
            ->join('processos as pr', 'pr.id', '=', 'p.processo_id')
            ->leftJoin('pessoas as cl', 'cl.id', '=', 'pr.cliente_id')
            ->leftJoin('fornecedores as f', 'f.id', '=', 'p.fornecedor_id')
            ->select(
                'p.id', 'p.descricao', 'p.valor', 'p.data',
                'p.data_vencimento', 'p.data_pagamento', 'p.pago',
                'pr.numero as processo_numero',
                'cl.nome as cliente_nome',
                'f.nome as fornecedor_nome'
            )
            ->orderBy('p.data_vencimento');

        if ($this->filtroStatus === 'pendente') {
            $q->where('p.pago', false);
        }

        if ($this->filtroMes) {
            $q->whereRaw("TO_CHAR(p.data,'YYYY-MM') = ?", [$this->filtroMes]);
        }

        return $q;
    }

    private function queryHonorariosAtrasados()
    {
        return DB::table('honorario_parcelas as hp')
            ->join('honorarios as h', 'h.id', '=', 'hp.honorario_id')
            ->leftJoin('pessoas as cl', 'cl.id', '=', 'h.cliente_id')
            ->leftJoin('processos as pr', 'pr.id', '=', 'h.processo_id')
            ->select(
                'hp.id', 'hp.numero_parcela', 'hp.valor',
                'hp.vencimento', 'hp.status',
                'h.descricao as honorario_descricao',
                'cl.nome as cliente_nome',
                'pr.numero as processo_numero',
                DB::raw("CURRENT_DATE - hp.vencimento AS dias_atraso")
            )
            ->whereIn('hp.status', ['atrasado', 'pendente'])
            ->whereRaw('hp.vencimento < CURRENT_DATE')
            ->orderBy('hp.vencimento');
    }

    // ── Render ──────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $metricas         = $this->metricas();
        $fluxo            = $this->abaAtiva === 'fluxo' ? $this->fluxoCaixa() : [];
        $prioridades      = $this->abaAtiva === 'visao'  ? $this->prioridades() : [];
        $agendaFinanceira = $this->abaAtiva === 'visao'  ? $this->agendaFinanceira() : [];

        $receber      = $this->abaAtiva === 'receber'
            ? $this->queryReceber()->paginate(20, ['*'], 'rec_page')
            : null;

        $pagar        = $this->abaAtiva === 'pagar'
            ? $this->queryPagar()->paginate(20, ['*'], 'pag_page')
            : null;

        $honAtrasados = $this->abaAtiva === 'honorarios'
            ? $this->queryHonorariosAtrasados()->paginate(20, ['*'], 'hon_page')
            : null;

        $inadimplentesCount = DB::table('honorario_parcelas')
            ->whereIn('status', ['atrasado', 'pendente'])
            ->whereRaw('vencimento < CURRENT_DATE')
            ->count();
        $aReceberCount = DB::table('recebimentos')->where('recebido', false)->count();
        $aPagarCount   = DB::table('pagamentos')->where('pago', false)->count();

        return view('livewire.financeiro-consolidado', compact(
            'metricas', 'fluxo', 'receber', 'pagar', 'honAtrasados',
            'prioridades', 'agendaFinanceira',
            'inadimplentesCount', 'aReceberCount', 'aPagarCount'
        ));
    }
}
