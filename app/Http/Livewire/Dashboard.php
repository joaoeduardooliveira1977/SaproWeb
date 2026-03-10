<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\{Processo, Pessoa, Agenda, Custa};
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public array $stats       = [];
    public array $agendaHoje  = [];
    public array $processos   = [];

    public function mount(): void
    {
        $this->carregarDados();
    }

    public function carregarDados(): void
    {
        $this->stats = [
            'processos_ativos'    => Processo::where('status', 'Ativo')->count(),
            'audiencias_hoje'     => Agenda::whereDate('data_hora', today())->where('tipo', 'Audiência')->count(),
            'prazos_vencendo'     => Agenda::whereBetween('data_hora', [now(), now()->addDays(7)])
                                        ->where('tipo', 'Prazo')->where('concluido', false)->count(),
            'clientes'            => Pessoa::ativo()->doTipo('Cliente')->count(),
            'custas_pendentes'    => Custa::where('pago', false)->sum('valor'),
            'processos_encerrados'=> Processo::where('status', 'Encerrado')->count(),
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
            ->take(8)
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'numero'      => $p->numero,
                'cliente'     => $p->cliente?->nome,
                'fase'        => $p->fase?->descricao,
                'risco'       => $p->risco?->descricao,
                'risco_cor'   => $p->risco?->cor_hex ?? '#64748b',
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
