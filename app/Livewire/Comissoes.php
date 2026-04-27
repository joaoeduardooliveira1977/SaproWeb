<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Comissao, Indicador};
use App\Services\Financeiro\ComissaoService;
use Illuminate\Support\Facades\Auth;

class Comissoes extends Component
{
    use WithPagination;

    public string $filtroIndicador  = '';
    public string $filtroCompetencia = '';
    public string $filtroStatus     = '';

    // Pagamento em lote
    public bool   $modalPagamento   = false;
    public string $dataPagamento    = '';
    public array  $selecionados     = [];

    protected $queryString = [
        'filtroIndicador'   => ['except' => ''],
        'filtroCompetencia' => ['except' => ''],
        'filtroStatus'      => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->dataPagamento   = now()->format('Y-m-d');
        $this->filtroCompetencia = now()->format('Y-m');
    }

    public function updatingFiltroIndicador():   void { $this->resetPage(); $this->selecionados = []; }
    public function updatingFiltroCompetencia(): void { $this->resetPage(); $this->selecionados = []; }
    public function updatingFiltroStatus():      void { $this->resetPage(); $this->selecionados = []; }

    public function toggleSelecionado(int $id): void
    {
        if (in_array($id, $this->selecionados)) {
            $this->selecionados = array_values(array_filter($this->selecionados, fn($v) => $v !== $id));
        } else {
            $this->selecionados[] = $id;
        }
    }

    public function selecionarTodos(): void
    {
        $this->selecionados = $this->queryBase()->pluck('id')->toArray();
    }

    public function desmarcarTodos(): void
    {
        $this->selecionados = [];
    }

    public function abrirPagamento(): void
    {
        if (empty($this->selecionados)) {
            $this->dispatch('toast', message: 'Selecione ao menos uma comissão.', type: 'error');
            return;
        }
        $this->modalPagamento = true;
    }

    public function confirmarPagamento(): void
    {
        $usuario = Auth::guard('usuarios')->user();
        abort_unless($usuario?->temAcao('financeiro.editar'), 403);

        if (empty($this->selecionados) || empty($this->dataPagamento)) {
            return;
        }

        app(ComissaoService::class)->marcarPagoEmLote($this->selecionados, $this->dataPagamento);

        $qtd = count($this->selecionados);
        $this->selecionados   = [];
        $this->modalPagamento = false;
        $this->dispatch('toast', message: "{$qtd} comissão(ões) marcada(s) como paga.", type: 'success');
    }

    private function queryBase()
    {
        return Comissao::with(['indicador', 'pessoa'])
            ->when($this->filtroIndicador, fn($q) => $q->where('indicador_id', $this->filtroIndicador))
            ->when($this->filtroCompetencia, function ($q) {
                $q->whereYear('competencia', substr($this->filtroCompetencia, 0, 4))
                  ->whereMonth('competencia', substr($this->filtroCompetencia, 5, 2));
            })
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus));
    }

    public function render()
    {
        $comissoes = $this->queryBase()
            ->orderByDesc('competencia')
            ->orderBy('status')
            ->paginate(20);

        $totais = $this->queryBase()->selectRaw('
            SUM(valor_comissao) as total,
            SUM(CASE WHEN status = \'pendente\' THEN valor_comissao ELSE 0 END) as pendente,
            SUM(CASE WHEN status = \'pago\'     THEN valor_comissao ELSE 0 END) as pago
        ')->first();

        $indicadores = Indicador::ativos()->orderBy('nome')->get(['id', 'nome']);

        return view('livewire.comissoes', compact('comissoes', 'totais', 'indicadores'));
    }
}
