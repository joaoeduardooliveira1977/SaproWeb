<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Agenda as AgendaModel, Processo};
use Illuminate\Support\Facades\Auth;

class Agenda extends Component
{
    use WithPagination;

    public string $data_ini    = '';
    public string $data_fim    = '';
    public string $tipo        = '';
    public bool   $so_pendentes = true;

    // Modal
    public bool   $modalAberto  = false;
    public ?int   $eventoId     = null;
    public string $titulo       = '';
    public string $data_hora    = '';
    public string $local        = '';
    public string $tipo_evento  = 'Outros';
    public bool   $urgente      = false;
    public string $processo_id  = '';
    public string $observacoes  = '';

    protected $rules = [
        'titulo'    => 'required|string|max:200',
        'data_hora' => 'required|date',
        'tipo_evento' => 'required',
    ];

    public function mount(): void
    {
        $this->data_ini = today()->format('Y-m-d');
        $this->data_fim = today()->addDays(30)->format('Y-m-d');
    }

    public function abrirModal(?int $id = null): void
    {
        $this->eventoId    = $id;
        $this->modalAberto = true;

        if ($id) {
            $ev = AgendaModel::findOrFail($id);
            $this->titulo       = $ev->titulo;
            $this->data_hora    = $ev->data_hora->format('Y-m-d\TH:i');
            $this->local        = $ev->local ?? '';
            $this->tipo_evento  = $ev->tipo;
            $this->urgente      = $ev->urgente;
            $this->processo_id  = (string) ($ev->processo_id ?? '');
            $this->observacoes  = $ev->observacoes ?? '';
        } else {
            $this->titulo = $this->local = $this->processo_id = $this->observacoes = '';
            $this->tipo_evento = 'Outros';
            $this->urgente = false;
            $this->data_hora = now()->format('Y-m-d\TH:i');
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->eventoId    = null;
        $this->resetErrorBag();
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'titulo'        => $this->titulo,
            'data_hora'     => $this->data_hora,
            'local'         => $this->local ?: null,
            'tipo'          => $this->tipo_evento,
            'urgente'       => $this->urgente,
            'processo_id'   => $this->processo_id ?: null,
            'responsavel_id'=> Auth::id(),
            'observacoes'   => $this->observacoes ?: null,
        ];

        if ($this->eventoId) {
            AgendaModel::findOrFail($this->eventoId)->update($dados);
        } else {
            AgendaModel::create($dados);
        }

        $this->fecharModal();
        session()->flash('sucesso', 'Evento salvo!');
    }

    public function concluir(int $id): void
    {
        AgendaModel::findOrFail($id)->update(['concluido' => true]);
    }

    public function excluir(int $id): void
    {
        AgendaModel::findOrFail($id)->delete();
        session()->flash('sucesso', 'Evento removido.');
    }

    public function render()
    {
        $eventos = AgendaModel::with('processo')
            ->when($this->data_ini, fn($q) => $q->where('data_hora', '>=', $this->data_ini))
            ->when($this->data_fim, fn($q) => $q->where('data_hora', '<=', $this->data_fim . ' 23:59:59'))
            ->when($this->tipo,     fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->so_pendentes, fn($q) => $q->where('concluido', false))
            ->orderBy('data_hora')
            ->paginate(20);

        return view('livewire.agenda', [
            'eventos'   => $eventos,
            'processos' => Processo::where('status', 'Ativo')->orderBy('numero')->get(),
        ]);
    }
}
