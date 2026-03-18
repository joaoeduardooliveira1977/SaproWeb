<?php

namespace App\Livewire;

use App\Models\Prazo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Analytics extends Component
{
    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.partials.skeleton', ['cards' => 4, 'blocks' => 3, 'blockHeight' => 280]);
    }

    public function render(): \Illuminate\View\View
    {
        // ── KPIs ─────────────────────────────────────────────────
        $mesAtual = now()->format('Y-m');

        $kpis = [
            'processos_ativos' => DB::table('processos')->where('status', 'Ativo')->count(),
            'receita_mes'      => (float) DB::table('recebimentos')
                ->where('recebido', true)
                ->whereRaw("TO_CHAR(data_recebimento, 'YYYY-MM') = ?", [$mesAtual])
                ->sum('valor'),
            'horas_mes'        => (float) DB::table('apontamentos')
                ->whereRaw("TO_CHAR(data, 'YYYY-MM') = ?", [$mesAtual])
                ->sum('horas'),
            'prazos_fatais'    => Prazo::where('status', 'aberto')
                ->where('prazo_fatal', true)
                ->whereDate('data_prazo', '>=', today())
                ->count(),
        ];

        // ── Processos por Status ──────────────────────────────────
        $porStatus = DB::table('processos')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        // ── Processos por Fase (ativos, top 8) ───────────────────
        $porFase = DB::table('processos as p')
            ->leftJoin('fases as f', 'f.id', '=', 'p.fase_id')
            ->where('p.status', 'Ativo')
            ->selectRaw("COALESCE(f.descricao, 'Sem Fase') as fase, COUNT(*) as total")
            ->groupBy('f.descricao')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // ── Processos por Risco (ativos) ─────────────────────────
        $porRisco = DB::table('processos as p')
            ->leftJoin('graus_risco as r', 'r.id', '=', 'p.risco_id')
            ->where('p.status', 'Ativo')
            ->selectRaw("COALESCE(r.descricao, 'Sem Risco') as risco, COALESCE(r.cor_hex, '#94a3b8') as cor, COUNT(*) as total")
            ->groupBy('r.descricao', 'r.cor_hex')
            ->orderByDesc('total')
            ->get();

        // ── Financeiro: Recebimentos x Pagamentos (12 meses) ─────
        $meses12 = collect(range(11, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $recebimentos = DB::table('recebimentos')
            ->where('recebido', true)
            ->where('data_recebimento', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw("TO_CHAR(data_recebimento, 'YYYY-MM') as mes, SUM(valor) as total")
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $pagamentos = DB::table('pagamentos')
            ->where('pago', true)
            ->where('data_pagamento', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw("TO_CHAR(data_pagamento, 'YYYY-MM') as mes, SUM(valor) as total")
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $labelsFinanceiro  = $meses12->map(fn($m) => Carbon::parse($m.'-01')->translatedFormat('M/y'))->values()->toArray();
        $dadosRecebimentos = $meses12->map(fn($m) => round((float)($recebimentos[$m] ?? 0), 2))->values()->toArray();
        $dadosPagamentos   = $meses12->map(fn($m) => round((float)($pagamentos[$m] ?? 0), 2))->values()->toArray();

        // ── Andamentos por mês (6 meses) ─────────────────────────
        $meses6 = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $andamentos = DB::table('andamentos')
            ->where('data', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("TO_CHAR(data, 'YYYY-MM') as mes, COUNT(*) as total")
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $labelsAndamentos = $meses6->map(fn($m) => Carbon::parse($m.'-01')->translatedFormat('M/y'))->values()->toArray();
        $dadosAndamentos  = $meses6->map(fn($m) => (int)($andamentos[$m] ?? 0))->values()->toArray();

        // ── Horas apontadas por mês (6 meses) ────────────────────
        $horas = DB::table('apontamentos')
            ->where('data', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("TO_CHAR(data, 'YYYY-MM') as mes, SUM(horas) as total")
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $dadosHoras = $meses6->map(fn($m) => round((float)($horas[$m] ?? 0), 1))->values()->toArray();

        // ── Prazos por urgência ───────────────────────────────────
        $prazosAbertos   = Prazo::where('status', 'aberto')->get();
        $prazosUrgencia  = [
            'Normal'  => $prazosAbertos->filter(fn($p) => $p->urgencia() === 'normal')->count(),
            'Alerta'  => $prazosAbertos->filter(fn($p) => $p->urgencia() === 'alerta')->count(),
            'Atenção' => $prazosAbertos->filter(fn($p) => $p->urgencia() === 'atencao')->count(),
            'Urgente' => $prazosAbertos->filter(fn($p) => $p->urgencia() === 'urgente')->count(),
            'Vencido' => $prazosAbertos->filter(fn($p) => $p->urgencia() === 'vencido')->count(),
        ];

        // ── Desempenho ────────────────────────────────────────────
        // Processos encerrados: taxa de conclusão e tempo médio
        $encerrados = DB::table('processos')
            ->whereIn('status', ['Encerrado', 'Arquivado'])
            ->selectRaw("COUNT(*) as total, AVG(EXTRACT(DAY FROM (updated_at - data_distribuicao))) as media_dias")
            ->first();

        $totalProcessos = DB::table('processos')->count();
        $taxaConclusao  = $totalProcessos > 0
            ? round(($encerrados->total / $totalProcessos) * 100, 1)
            : 0;
        $tempoMedioMeses = $encerrados->media_dias
            ? round($encerrados->media_dias / 30, 1)
            : null;

        // Receita por advogado (últimos 12 meses)
        $receitaPorAdvogado = DB::table('recebimentos as r')
            ->join('processos as p', 'p.id', '=', 'r.processo_id')
            ->join('processo_advogado as pa', 'pa.processo_id', '=', 'p.id')
            ->join('pessoas as pe', 'pe.id', '=', 'pa.advogado_id')
            ->where('r.recebido', true)
            ->where('r.data_recebimento', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw('pe.nome, SUM(r.valor) as total')
            ->groupBy('pe.nome')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // Top clientes por processos ativos
        $topClientes = DB::table('processos as p')
            ->join('pessoas as pe', 'pe.id', '=', 'p.cliente_id')
            ->where('p.status', 'Ativo')
            ->selectRaw('pe.nome, COUNT(*) as total')
            ->groupBy('pe.nome')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return view('livewire.analytics', compact(
            'kpis',
            'porStatus', 'porFase', 'porRisco',
            'labelsFinanceiro', 'dadosRecebimentos', 'dadosPagamentos',
            'labelsAndamentos', 'dadosAndamentos', 'dadosHoras',
            'prazosUrgencia',
            'taxaConclusao', 'tempoMedioMeses', 'receitaPorAdvogado', 'topClientes'
        ));
    }
}
