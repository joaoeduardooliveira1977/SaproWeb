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
        abort_unless(Auth::user()->temAcao('processos.arquivar'), 403, 'Sem permissão.');

        $processo = Processo::findOrFail($this->processoParaExcluir);

        $processo->update(['status' => 'Arquivado']);
        Auth::user()->registrarAuditoria('Arquivou processo', 'processos', $processo->id);

        $this->confirmandoExclusao  = false;
        $this->processoParaExcluir  = null;
        $this->dispatch('toast', message: "Processo {$processo->numero} arquivado.", type: 'success');
    }

    public function cancelarExclusao(): void
    {
        $this->confirmandoExclusao  = false;
        $this->processoParaExcluir  = null;
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = Processo::with(['cliente', 'advogado', 'fase', 'risco'])
            ->when($this->busca,    fn($q) => $q->busca($this->busca))
            ->when($this->status,   fn($q) => $q->where('status', $this->status))
            ->when($this->fase_id,  fn($q) => $q->where('fase_id', $this->fase_id))
            ->when($this->risco_id, fn($q) => $q->where('risco_id', $this->risco_id))
            ->orderByDesc('created_at')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, ['Número','Cliente','Parte Contrária','Advogado','Fase','Risco','Status','Valor Causa','Data Distribuição'], ';');
            foreach ($rows as $p) {
                fputcsv($out, [
                    $p->numero,
                    $p->cliente?->nome ?? '',
                    $p->parteContraria?->nome ?? ($p->parte_contraria ?? ''),
                    $p->advogado?->nome ?? '',
                    $p->fase?->descricao ?? '',
                    $p->risco?->descricao ?? '',
                    $p->status,
                    $p->valor_causa ? number_format($p->valor_causa, 2, ',', '.') : '',
                    $p->data_distribuicao?->format('d/m/Y') ?? '',
                ], ';');
            }
            fclose($out);
        }, 'processos-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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
