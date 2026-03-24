<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{Processo, Pessoa, Agenda, Prazo, Recebimento, Procuracao};

class Dashboard extends Component
{
    public array $stats              = [];
    public array $agendaHoje         = [];
    public array $processos          = [];
    public array $prazosProximos     = [];
    public array $processosParados   = [];
    public array $processosPorFase   = [];
    public array $ultimasAtividades  = [];
    public array $receitaMensal      = [];
    public array $riscosPrioritarios = [];
    public int   $procuracoesVencendo = 0;
    public int   $procuracoesVencidas = 0;


    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.partials.skeleton', ['cards' => 4, 'blocks' => 2, 'blockHeight' => 240]);
    }

    public function mount(): void
    {
        $this->carregarDados();
    }

    public function carregarDados(): void
    {
        $this->stats = [
            'processos_ativos'      => Processo::where('status', 'Ativo')->count(),
            'audiencias_hoje'       => Agenda::whereDate('data_hora', today())->where('tipo', 'Audiência')->count(),
            'prazos_7dias'          => Prazo::where('status', 'aberto')
                                        ->whereBetween('data_prazo', [today(), today()->addDays(7)])
                                        ->count(),
            'prazos_vencidos'       => Prazo::where('status', 'aberto')
                                        ->where('data_prazo', '<', today())
                                        ->count(),
            'recebimentos_pendentes'=> Recebimento::where('recebido', false)->sum('valor'),
            'clientes'              => Pessoa::ativos()->doTipo('Cliente')->count(),
            'processos_parados'     => Processo::where('status', 'Ativo')
                                        ->whereNotExists(fn($q) => $q->from('andamentos')
                                            ->whereColumn('andamentos.processo_id', 'processos.id')
                                            ->where('andamentos.created_at', '>=', now()->subDays(30)))
                                        ->count(),
        ];

        $this->agendaHoje = Agenda::with('processo')
            ->whereDate('data_hora', today())
            ->where('concluido', false)
            ->orderBy('data_hora')
            ->get()
            ->map(fn($a) => [
                'hora'    => $a->data_hora->format('H:i'),
                'titulo'  => $a->titulo,
                'tipo'    => $a->tipo,
                'urgente' => $a->urgente,
                'processo'=> $a->processo?->numero,
            ])
            ->toArray();

        $this->processos = Processo::with(['cliente', 'fase', 'risco'])
            ->where('status', 'Ativo')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'numero'    => $p->numero,
                'cliente'   => $p->cliente?->nome,
                'fase'      => $p->fase?->descricao,
                'risco'     => $p->risco?->descricao,
                'risco_cor' => $p->risco?->cor_hex ?? '#64748b',
            ])
            ->toArray();

        $this->processosParados = Processo::with(['cliente', 'fase'])
            ->where('status', 'Ativo')
            ->whereNotExists(fn($q) => $q->from('andamentos')
                ->whereColumn('andamentos.processo_id', 'processos.id')
                ->where('andamentos.created_at', '>=', now()->subDays(30)))
            ->orderBy('updated_at')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'      => $p->id,
                'numero'  => $p->numero,
                'cliente' => $p->cliente?->nome ?? '—',
                'fase'    => $p->fase?->descricao,
                'dias'    => (int) now()->diffInDays($p->updated_at),
            ])
            ->toArray();

        // Procurações
        $this->procuracoesVencidas  = Procuracao::where('ativa', true)
            ->whereNotNull('data_validade')->where('data_validade', '<', today())->count();
        $this->procuracoesVencendo  = Procuracao::where('ativa', true)
            ->whereNotNull('data_validade')
            ->whereBetween('data_validade', [today(), today()->addDays(30)])->count();

        // Processos por Fase (barras horizontais)
        $this->processosPorFase = DB::table('processos')
            ->join('fases', 'fases.id', '=', 'processos.fase_id')
            ->where('processos.status', 'Ativo')
            ->whereNotNull('processos.fase_id')
            ->selectRaw('fases.descricao as fase, COUNT(*) as total')
            ->groupBy('fases.id', 'fases.descricao')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn($f) => ['fase' => $f->fase, 'total' => (int) $f->total])
            ->toArray();

        // Últimas atividades (andamentos recentes)
        $this->ultimasAtividades = DB::table('andamentos')
            ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
            ->join('pessoas as clientes', 'clientes.id', '=', 'processos.cliente_id')
            ->leftJoin('usuarios', 'usuarios.id', '=', 'andamentos.usuario_id')
            ->leftJoin('pessoas as upes', 'upes.id', '=', 'usuarios.pessoa_id')
            ->select(
                'andamentos.descricao',
                'andamentos.created_at',
                'processos.numero',
                'processos.id as processo_id',
                'clientes.nome as cliente_nome',
                'upes.nome as usuario_nome'
            )
            ->orderByDesc('andamentos.created_at')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'descricao'   => Str::limit($a->descricao, 80),
                'numero'      => $a->numero,
                'processo_id' => $a->processo_id,
                'cliente'     => $a->cliente_nome,
                'usuario'     => $a->usuario_nome ?? 'Sistema',
                'quando'      => \Carbon\Carbon::parse($a->created_at)->diffForHumans(),
            ])
            ->toArray();

        // Receita mensal (últimos 6 meses)
        $this->receitaMensal = collect(range(5, 0))->map(function ($i) {
            $mes = now()->subMonths($i);
            return [
                'mes'   => $mes->locale('pt_BR')->isoFormat('MMM'),
                'valor' => (float) Recebimento::where('recebido', true)
                    ->whereYear('data_recebimento', $mes->year)
                    ->whereMonth('data_recebimento', $mes->month)
                    ->sum('valor'),
            ];
        })->toArray();

        $this->prazosProximos = Prazo::with('processo')
            ->where('status', 'aberto')
            ->where('data_prazo', '<=', today()->addDays(15))
            ->orderBy('data_prazo')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'titulo'   => $p->titulo,
                'processo' => $p->processo?->numero,
                'data'     => $p->data_prazo->format('d/m'),
                'fatal'    => $p->prazo_fatal,
                'urgencia' => $p->urgencia(),
                'dias'     => $p->diasRestantes(),
            ])
            ->toArray();


	$this->riscosPrioritarios = Processo::with(['cliente', 'risco'])
    		->where('status', 'Ativo')
    		->whereNotNull('risco_id')
    		->orderByDesc('risco_id')
    		->take(5)
    		->get()
    		->map(fn($p) => [
        		'numero'  => $p->numero,
        		'cliente' => $p->cliente?->nome ?? 'Sem cliente',
        		'risco'   => $p->risco?->descricao ?? 'Médio',
    	])
   ->toArray();








    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
