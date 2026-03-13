<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceiroConsolidado extends Component
{
    use WithPagination;

    public string $aba        = 'visao-geral';
    public string $filtroMes  = '';
    public string $filtroStatus = 'pendente'; // pendente | todos

    public function mount(): void
    {
        $this->filtroMes = now()->format('Y-m');
    }

    public function updatedAba(): void
    {
        $this->resetPage();
    }

    // ── KPIs ────────────────────────────────────────────────────

    private function kpis(): array
    {
        $mesAtual    = now()->format('Y-m');
        $inicioMes   = now()->startOfMonth()->format('Y-m-d');
        $fimMes      = now()->endOfMonth()->format('Y-m-d');

        $aReceber = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM recebimentos
            WHERE recebido = false
        ")->total;

        $recebidoMes = DB::selectOne("
            SELECT COALESCE(SUM(valor_recebido),0) AS total
            FROM recebimentos
            WHERE recebido = true
              AND data_recebimento BETWEEN ? AND ?
        ", [$inicioMes, $fimMes])->total;

        $aPagar = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM pagamentos
            WHERE pago = false
        ")->total;

        $pagoMes = DB::selectOne("
            SELECT COALESCE(SUM(valor_pago),0) AS total
            FROM pagamentos
            WHERE pago = true
              AND data_pagamento BETWEEN ? AND ?
        ", [$inicioMes, $fimMes])->total;

        $honAtrasado = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM honorario_parcelas
            WHERE status IN ('atrasado','pendente')
              AND vencimento < CURRENT_DATE
        ")->total;

        $honPendente = DB::selectOne("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM honorario_parcelas
            WHERE status = 'pendente'
              AND vencimento >= CURRENT_DATE
        ")->total;

        return compact(
            'aReceber', 'recebidoMes',
            'aPagar',   'pagoMes',
            'honAtrasado', 'honPendente'
        );
    }

    // ── Fluxo de Caixa (últimos 6 meses) ────────────────────────

    private function fluxoCaixa(): array
    {
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $meses[] = now()->subMonths($i)->format('Y-m');
        }

        $rows = [];
        foreach ($meses as $mes) {
            [$ano, $m] = explode('-', $mes);

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
                'label'      => \Carbon\Carbon::createFromFormat('Y-m', $mes)->translatedFormat('M/Y'),
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
        $kpis       = $this->kpis();
        $fluxo      = $this->aba === 'fluxo' ? $this->fluxoCaixa() : [];

        $receber    = $this->aba === 'receber'
            ? $this->queryReceber()->paginate(20, ['*'], 'rec_page')
            : null;

        $pagar      = $this->aba === 'pagar'
            ? $this->queryPagar()->paginate(20, ['*'], 'pag_page')
            : null;

        $honAtrasados = $this->aba === 'honorarios'
            ? $this->queryHonorariosAtrasados()->paginate(20, ['*'], 'hon_page')
            : null;

        return view('livewire.financeiro-consolidado', compact(
            'kpis', 'fluxo', 'receber', 'pagar', 'honAtrasados'
        ));
    }
}
