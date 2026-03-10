<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use App\Models\{Pessoa, Processo, Andamento, Agenda};
use Illuminate\Support\Facades\Session;

class PortalDashboard extends Component
{
    public ?Pessoa $pessoa  = null;
    public array $processos = [];
    public array $agenda    = [];
    public ?int $processoSelecionado = null;
    public array $andamentos = [];

    public function mount(): void
    {
        $id = Session::get('portal_pessoa_id');
        if (! $id) {
            $this->redirect(route('portal.login'));
            return;
        }

        $this->pessoa = Pessoa::find($id);
        $this->carregarProcessos();
        $this->carregarAgenda();
    }

    public function carregarProcessos(): void
    {
        $this->processos = Processo::with(['fase', 'risco', 'advogado'])
            ->where('cliente_id', $this->pessoa->id)
            ->orderByDesc('data_distribuicao')
            ->get()
            ->map(fn($p) => [
                'id'                => $p->id,
                'numero'            => $p->numero,
                'fase'              => $p->fase?->descricao ?? '—',
                'risco'             => $p->risco?->descricao ?? '—',
                'risco_cor'         => $p->risco?->cor_hex ?? '#64748b',
                'advogado'          => $p->advogado?->nome ?? '—',
                'status'            => $p->status,
                'data_distribuicao' => $p->data_distribuicao?->format('d/m/Y') ?? '—',
                'valor_causa'       => $p->valor_causa ? 'R$ ' . number_format($p->valor_causa, 2, ',', '.') : '—',
                'parte_contraria'   => $p->parte_contraria ?? '—',
            ])
            ->toArray();
    }

    public function carregarAgenda(): void
    {
        $processosIds = Processo::where('cliente_id', $this->pessoa->id)->pluck('id');

        $this->agenda = Agenda::with('processo')
            ->whereIn('processo_id', $processosIds)
            ->where('concluido', false)
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora')
            ->take(10)
            ->get()
            ->map(fn($a) => [
                'titulo'    => $a->titulo,
                'data_hora' => $a->data_hora->format('d/m/Y H:i'),
                'tipo'      => $a->tipo,
                'urgente'   => $a->urgente,
                'processo'  => $a->processo?->numero,
                'local'     => $a->local ?? '',
            ])
            ->toArray();
    }

    public function verAndamentos(int $processoId): void
    {
        $this->processoSelecionado = $processoId;

        $processo = Processo::find($processoId);
        if (! $processo || $processo->cliente_id !== $this->pessoa->id) return;

        $this->andamentos = Andamento::where('processo_id', $processoId)
            ->orderByDesc('data')
            ->get()
            ->map(fn($a) => [
                'data'      => $a->data->format('d/m/Y'),
                'descricao' => $a->descricao,
            ])
            ->toArray();
    }

    public function fecharAndamentos(): void
    {
        $this->processoSelecionado = null;
        $this->andamentos = [];
    }

    public function sair(): void
    {
        Session::forget(['portal_pessoa_id', 'portal_pessoa_nome']);
        $this->redirect(route('portal.login'));
    }

    public function render()
    {
        return view('livewire.portal.dashboard')
            ->layout('portal.layout');
    }
}
