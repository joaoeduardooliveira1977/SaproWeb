<?php

namespace App\Livewire;

use App\Models\{Agenda, Prazo, Processo, Recebimento, Audiencia};
use Illuminate\Support\Facades\{Cache, DB};
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
        $hoje     = now()->startOfDay();
        $em7dias  = now()->addDays(7)->endOfDay();

        // ── KPI Stats ───────────────────────────────────────────────
        $totalProcessos = Processo::where('tenant_id', $tenantId)
            ->where('status', 'Ativo')->count();

        $prazosHoje = Prazo::where('tenant_id', $tenantId)
            ->where('status', 'aberto')
            ->whereDate('data_prazo', today())->count();

        $prazosVencidos = Prazo::where('tenant_id', $tenantId)
            ->where('status', 'aberto')
            ->where('data_prazo', '<', today())->count();

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

        // ── Andamentos últimos 7 dias (sparkline) ───────────────────
        $sparkAndamentos = DB::table('andamentos')
            ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
            ->where('processos.tenant_id', $tenantId)
            ->where('andamentos.created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw("DATE(andamentos.created_at) as data, COUNT(*) as total")
            ->groupByRaw("DATE(andamentos.created_at)")
            ->orderByRaw("DATE(andamentos.created_at)")
            ->pluck('total', 'data')
            ->toArray();

        $spark7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $spark7[] = (int) ($sparkAndamentos[$d] ?? 0);
        }

        // ── Próximos 7 dias — timeline unificada ────────────────────
        $prazos7 = Prazo::with(['processo:id,numero,cliente_id', 'processo.cliente:id,nome'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'aberto')
            ->where('data_prazo', '<=', $em7dias)
            ->orderBy('data_prazo')
            ->take(8)
            ->get();

     	$audiencias7 = Audiencia::with(['processo:id,numero,cliente_id', 'processo.cliente:id,nome'])
    		->whereHas('processo', fn($q) => $q->where('tenant_id', $tenantId))
    		->where('status', 'agendada')
    		->where('data_hora', '>=', $hoje)
    		->where('data_hora', '<=', $em7dias)
    		->orderBy('data_hora')
    		->take(5)
    		->get();

	$agenda7 = Agenda::with(['processo:id,numero,cliente_id', 'processo.cliente:id,nome'])
    		->whereHas('processo', fn($q) => $q->where('tenant_id', $tenantId))
    		->where('concluido', false)
    		->where('data_hora', '>=', $hoje)
    		->where('data_hora', '<=', $em7dias)
    		->orderBy('data_hora')
    		->take(5)
    		->get();

        // Merge e ordenar por data
        $timeline = collect();
        foreach ($prazos7 as $p) {
            $dias = (int) $hoje->diffInDays($p->data_prazo, false);
            $timeline->push([
                'tipo'    => 'prazo',
                'titulo'  => $p->titulo,
                'data'    => $p->data_prazo,
                'hora'    => null,
                'dias'    => $dias,
                'fatal'   => $p->prazo_fatal,
                'processo_id' => $p->processo_id,
                'numero'  => $p->processo?->numero,
                'cliente' => $p->processo?->cliente?->nome,
            ]);
        }
        foreach ($audiencias7 as $a) {
            $dias = (int) $hoje->diffInDays($a->data_hora->startOfDay(), false);
            $timeline->push([
                'tipo'    => 'audiencia',
                'titulo'  => $a->tipoLabel(),
                'data'    => $a->data_hora,
                'hora'    => $a->data_hora->format('H:i'),
                'dias'    => $dias,
                'fatal'   => false,
                'processo_id' => $a->processo_id,
                'numero'  => $a->processo?->numero,
                'cliente' => $a->processo?->cliente?->nome,
            ]);
        }
        foreach ($agenda7 as $ag) {
            $dias = (int) $hoje->diffInDays($ag->data_hora->startOfDay(), false);
            $timeline->push([
                'tipo'    => 'agenda',
                'titulo'  => $ag->titulo,
                'data'    => $ag->data_hora,
                'hora'    => $ag->data_hora->format('H:i'),
                'dias'    => $dias,
                'fatal'   => $ag->urgente,
                'processo_id' => $ag->processo_id,
                'numero'  => $ag->processo?->numero,
                'cliente' => $ag->processo?->cliente?->nome,
            ]);
        }
        $timeline = $timeline->sortBy('data')->values()->take(10);

        // ── Aniversariantes da semana ───────────────────────────────
        $aniversariantes = DB::table('pessoas as p')
            ->join('pessoa_tipos as pt', 'pt.pessoa_id', '=', 'p.id')
            ->where('pt.tipo', 'Cliente')
            ->where('p.ativo', true)
            ->where('p.tenant_id', $tenantId)
            ->whereNotNull('p.data_nascimento')
            ->whereRaw(
                "TO_CHAR(p.data_nascimento, 'MM-DD') BETWEEN TO_CHAR(NOW(), 'MM-DD') AND TO_CHAR(NOW() + INTERVAL '7 days', 'MM-DD')"
            )
            ->select('p.id', 'p.nome', 'p.data_nascimento')
            ->orderByRaw("TO_CHAR(p.data_nascimento, 'MM-DD')")
            ->limit(4)
            ->get()
            ->map(function ($p) {
                $nasc = \Carbon\Carbon::parse($p->data_nascimento);
                $aniversario = $nasc->copy()->setYear(now()->year);
                $diasAte = (int) now()->startOfDay()->diffInDays($aniversario, false);
                return [
                    'nome'    => $p->nome,
                    'dia'     => $nasc->format('d/m'),
                    'idade'   => $nasc->age,
                    'dias_ate' => $diasAte,
                    'hoje'    => $diasAte === 0,
                ];
            });

        // ── Últimas movimentações ───────────────────────────────────
        $ultimosAndamentos = DB::table('andamentos')
            ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
            ->leftJoin('pessoas as clientes', 'clientes.id', '=', 'processos.cliente_id')
            ->where('processos.tenant_id', $tenantId)
            ->select(
                'andamentos.id', 'andamentos.descricao', 'andamentos.created_at',
                'processos.id as processo_id', 'processos.numero',
                'clientes.nome as cliente_nome'
            )
            ->orderByDesc('andamentos.created_at')
            ->limit(5)
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

        // ── Score carteira ──────────────────────────────────────────
        $criticos    = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->where('score', 'critico')->count();
        $atencao     = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->where('score', 'atencao')->count();
        $normais     = Processo::where('tenant_id', $tenantId)->where('status', 'Ativo')->where('score', 'normal')->count();

        // ── Atividade da semana ─────────────────────────────────────
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

        $audienciasSemanais = Audiencia::where('tenant_id', $tenantId)
            ->where('data_hora', '>=', now()->startOfWeek())
            ->where('data_hora', '<=', now()->endOfWeek())
            ->count();

        // ── Honorários recebidos este mês ───────────────────────────
        $honorariosMes = (float) DB::table('recebimentos')
            ->join('processos', 'processos.id', '=', 'recebimentos.processo_id')
            ->where('processos.tenant_id', $tenantId)
            ->where('recebimentos.recebido', true)
            ->whereMonth('recebimentos.data_recebimento', now()->month)
            ->whereYear('recebimentos.data_recebimento', now()->year)
            ->sum('recebimentos.valor_recebido');

        // ── Novos processos este mês vs anterior ────────────────────
        $novosEstesMes = Processo::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $novosMesAnterior = Processo::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // Tendência últimos 6 meses (cache 10 min — muda raramente)
        $tendenciaMensal = Cache::remember("dashboard.tendencia.{$tenantId}", 600, function () use ($tenantId) {
            $result = collect();
            for ($i = 5; $i >= 0; $i--) {
                $mes = now()->subMonths($i);
                $result->push([
                    'mes'   => $mes->locale('pt_BR')->isoFormat('MMM'),
                    'total' => Processo::where('tenant_id', $tenantId)
                        ->whereMonth('created_at', $mes->month)
                        ->whereYear('created_at', $mes->year)
                        ->count(),
                ]);
            }
            return $result;
        });

        // ── Top 5 clientes por processos ativos (cache 10 min) ──────
        $topClientes = Cache::remember("dashboard.topclientes.{$tenantId}", 600, function () use ($tenantId) {
            return DB::table('processos')
                ->join('pessoas', 'pessoas.id', '=', 'processos.cliente_id')
                ->where('processos.tenant_id', $tenantId)
                ->where('processos.status', 'Ativo')
                ->whereNotNull('processos.cliente_id')
                ->selectRaw('pessoas.id, pessoas.nome, COUNT(*) as total')
                ->groupBy('pessoas.id', 'pessoas.nome')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        });

        return view('livewire.dashboard', compact(
            'totalProcessos', 'prazosHoje', 'prazosVencidos', 'totalReceber',
            'andamentosHoje', 'processosParados',
            'timeline', 'ultimosAndamentos',
            'agendaHoje', 'aniversariantes',
            'criticos', 'atencao', 'normais',
            'atividadeSemana', 'audienciasSemanais',
            'spark7',
            'honorariosMes', 'novosEstesMes', 'novosMesAnterior', 'tendenciaMensal',
            'topClientes'
        ));
    }
}
