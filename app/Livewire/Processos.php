<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Processo, Fase, GrauRisco};
use Illuminate\Support\Facades\Auth;

class Processos extends Component
{
    use WithPagination;

    public string  $busca      = '';
    public string  $status     = '';
    public string  $fase_id    = '';
    public string  $risco_id   = '';
    public bool    $confirmandoExclusao = false;
    public ?int    $processoParaExcluir = null;

    protected $queryString = ['busca', 'status', 'fase_id', 'risco_id'];

    public function updatingBusca():   void { $this->resetPage(); }
    public function updatingStatus():  void { $this->resetPage(); }
    public function updatingFaseId():  void { $this->resetPage(); }
    public function updatingRiscoId(): void { $this->resetPage(); }

    public function confirmarArquivar(int $id): void
    {
        $this->processoParaExcluir  = $id;
        $this->confirmandoExclusao  = true;
    }

    public function arquivar(): void
    {
        $processo = Processo::findOrFail($this->processoParaExcluir);

        if (!Auth::user()->isAdvogado()) {
            session()->flash('erro', 'Sem permissão.');
            return;
        }

        $processo->update(['status' => 'Arquivado']);
        Auth::user()->registrarAuditoria('Arquivou processo', 'processos', $processo->id);

        $this->confirmandoExclusao  = false;
        $this->processoParaExcluir  = null;
        session()->flash('sucesso', "Processo {$processo->numero} arquivado.");
    }

    public function cancelarExclusao(): void
    {
        $this->confirmandoExclusao  = false;
        $this->processoParaExcluir  = null;
    }

    public function render()
    {
        $processos = Processo::with(['cliente', 'advogado', 'fase', 'risco'])
            ->when($this->busca,    fn($q) => $q->busca($this->busca))
            ->when($this->status,   fn($q) => $q->where('status', $this->status))
            ->when($this->fase_id,  fn($q) => $q->where('fase_id', $this->fase_id))
            ->when($this->risco_id, fn($q) => $q->where('risco_id', $this->risco_id))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.processos', [
            'processos' => $processos,
            'fases'     => Fase::orderBy('ordem')->get(),
            'riscos'    => GrauRisco::all(),
        ]);
    }
}
