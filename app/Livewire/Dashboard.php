<?php

namespace App\Livewire;

use App\Models\{Agenda, Prazo, Processo, Recebimento};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.partials.skeleton', ['cards' => 4, 'blocks' => 2, 'blockHeight' => 240]);
    }

    public function render()
    {
        $user     = auth('usuarios')->user();
        $tenantId = $user->tenant_id;

        // ── KPI Stats ──────────────────────────────────────────────
        $totalProcessos = Processo::where('tenant_id', $tenantId)
            ->where('status', 'Ativo')->count();

        $prazosHoje = Prazo::where('tenant_id', $tenantId)
            ->where('status', 'aberto')
            ->whereDate('data_prazo', today())->count();

        $totalReceber = (float) Recebimento::whereHas('processo', fn($q) => 
    		$q->where('tenant_id', $tenantId)
		)->where('recebido', false)->sum('valor');



        $andamentosHoje = DB::table('andamentos')
            ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
            ->where('processos.tenant_id', $tenantId)
            ->whereDate('andamentos.created_at', today())->count();

        $processosParados = Processo::where('tenant_id', $tenantId)
            ->where('status', 'Ativo')
            ->whereNotExists(fn($q) => $q->from('andamentos')
                ->whereColumn('andamentos.processo_id', 'processos.id')
                ->where('andamentos.created_at', '>=', now()->subDays(30)))
            ->count();

        // ── Ações urgentes (prazos próximos 7 dias) ─────────────────
        $acoesUrgentes = Prazo::with(['processo.cliente'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'aberto')
            ->where('data_prazo', '<=', now()->addDays(7))
            ->orderBy('data_prazo')
            ->take(5)
            ->get();

        // ── Últimos andamentos ──────────────────────────────────────
        $ultimosAndamentos = DB::table('andamentos')
            ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
            ->leftJoin('pessoas as clientes', 'clientes.id', '=', 'processos.cliente_id')
            ->where('processos.tenant_id', $tenantId)
            ->select(
                'andamentos.id',
                'andamentos.descricao',
                'andamentos.created_at',
                'processos.id as processo_id',
                'processos.numero',
                'clientes.nome as cliente_nome'
            )
            ->orderByDesc('andamentos.created_at')
            ->limit(6)
            ->get();

        // ── Agenda hoje ─────────────────────────────────────────────
        $agendaHoje = Agenda::whereHas('processo', fn($q) => 
        $q->where('tenant_id', $tenantId)
    	)
    		->whereDate('data_hora', today())
    		->where('concluido', false)
    		->orderBy('data_hora')
    		->take(4)
    ->get();

        // ── Visão geral processos ───────────────────────────────────
        $criticos    = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->where('score', 'critico')->count();
        $atencao     = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->where('score', 'atencao')->count();
        $normais     = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->where('score', 'normal')->count();
        $monitorados = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->whereNotNull('numero')->count();

        // ── Atividade da semana (PostgreSQL) ─────────────────────
        $atividadeSemana = DB::table('andamentos')
            ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
            ->where('processos.tenant_id', $tenantId)
            ->where('andamentos.created_at', '>=', now()->startOfWeek())
            ->where('andamentos.created_at', '<=', now()->endOfWeek())
            ->selectRaw("TO_CHAR(andamentos.created_at, 'Dy') as dia, DATE(andamentos.created_at) as data, COUNT(*) as total")
            ->groupByRaw("TO_CHAR(andamentos.created_at, 'Dy'), DATE(andamentos.created_at)")
            ->orderByRaw("DATE(andamentos.created_at)")
            ->get()
            ->map(fn($r) => ['dia' => strtoupper($r->dia), 'total' => (int)$r->total])
            ->toArray();

        // ── Audiências da semana ──────────────────────────────────
        $audienciasSemanais = \App\Models\Agenda::where('tenant_id', $tenantId)
            ->where('data_hora', '>=', now()->startOfWeek())
            ->where('data_hora', '<=', now()->endOfWeek())
            ->count();

        return view('livewire.dashboard', compact(
            'totalProcessos', 'prazosHoje', 'totalReceber', 'andamentosHoje',
            'acoesUrgentes', 'ultimosAndamentos',
            'agendaHoje', 'processosParados',
            'criticos', 'atencao', 'normais', 'monitorados',
            'atividadeSemana', 'audienciasSemanais'
        ));
    }
}
