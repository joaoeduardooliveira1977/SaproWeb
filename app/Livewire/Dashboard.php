<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Processo, Pessoa, Agenda, Prazo, Recebimento};

class Dashboard extends Component
{
    public array $stats          = [];
    public array $agendaHoje     = [];
    public array $processos      = [];
    public array $prazosProximos = [];

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

        $this->prazosProximos = Prazo::with('processo')
            ->where('status', 'aberto')
            ->where('data_prazo', '<=', today()->addDays(15))
            ->orderBy('data_prazo')
            ->take(8)
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

    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
