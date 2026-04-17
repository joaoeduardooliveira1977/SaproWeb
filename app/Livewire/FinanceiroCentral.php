<?php

namespace App\Livewire;

use App\Models\{FinanceiroLancamento, Pessoa, Contrato};
use Illuminate\Support\Facades\{Auth, DB};
use Livewire\Component;
use Livewire\WithPagination;

class FinanceiroCentral extends Component
{
    use WithPagination;

    // ── Filtros ───────────────────────────────────────────────
    public string $busca         = '';
    public string $filtroStatus  = '';
    public string $filtroTipo    = '';
    public string $filtroMes     = '';
    public string $filtroCliente = '';

    protected $queryString = [
        'busca'         => ['except' => ''],
        'filtroStatus'  => ['except' => ''],
        'filtroTipo'    => ['except' => ''],
        'filtroMes'     => ['except' => ''],
        'filtroCliente' => ['except' => ''],
    ];

    // ── Modal lançamento ──────────────────────────────────────
    public bool   $modal        = false;
    public ?int   $lancamentoId = null;
    public int    $clienteId    = 0;
    public ?int   $contratoId   = null;
    public string $tipo         = 'receita';
    public string $descricao    = '';
    public string $valor        = '';
    public string $vencimento   = '';
    public string $observacoes  = '';

    // ── Modal: registrar pagamento ────────────────────────────
    public bool   $modalPagamento  = false;
    public ?int   $pagamentoLancId = null;
    public string $dataPagamento   = '';
    public string $valorPago       = '';
    public string $formaPagamento  = 'pix';
    public string $pagamentoTipo   = 'receita';

    // ── Dados auxiliares ──────────────────────────────────────
    public array $clientes     = [];
    public array $fornecedores = [];
    public array $contratos    = [];

    public function mount(): void
    {
        $this->filtroMes = now()->format('Y-m');
        $this->carregarAuxiliares();
    }

    private function carregarAuxiliares(): void
    {
        $this->clientes = DB::select("
            SELECT p.id, p.nome FROM pessoas p
            JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
            WHERE pt.tipo = 'Cliente' AND p.ativo = true
            ORDER BY p.nome
        ");
        $this->fornecedores = DB::select("
            SELECT p.id, p.nome FROM pessoas p
            JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
            WHERE pt.tipo = 'Fornecedor' AND p.ativo = true
            ORDER BY p.nome
        ");
    }

    public function updatedClienteId(): void
    {
        $this->contratos = $this->clienteId
            ? Contrato::where('cliente_id', $this->clienteId)->where('status', 'ativo')->get(['id','descricao'])->toArray()
            : [];
        $this->contratoId = null;
    }

    // ── Filtros reset page ────────────────────────────────────
    public function updatingBusca():         void { $this->resetPage(); }
    public function updatingFiltroStatus():  void { $this->resetPage(); }
    public function updatingFiltroTipo():    void { $this->resetPage(); }
    public function updatingFiltroMes():     void { $this->resetPage(); }
    public function updatingFiltroCliente(): void { $this->resetPage(); }

    // ── Modal lançamento manual ───────────────────────────────
    public function abrirModal(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->lancamentoId = $id;
        $this->contratoId   = null;
        $this->contratos    = [];

        if ($id) {
            $l = FinanceiroLancamento::findOrFail($id);
            $this->clienteId  = $l->cliente_id;
            $this->contratoId = $l->contrato_id;
            $this->tipo       = $l->tipo;
            $this->descricao  = $l->descricao;
            $this->valor      = number_format($l->valor, 2, ',', '.');
            $this->vencimento = $l->vencimento->format('Y-m-d');
            $this->observacoes = $l->observacoes ?? '';
            $this->updatedClienteId();
        } else {
            $this->clienteId  = 0;
            $this->tipo       = 'receita';
            $this->descricao  = '';
            $this->valor      = '';
            $this->vencimento = now()->format('Y-m-d');
            $this->observacoes = '';
        }

        $this->modal = true;
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetErrorBag();
    }

    public function salvar(): void
    {
        $this->validate([
            'clienteId'  => 'required|integer|min:1',
            'tipo'       => 'required|string',
            'descricao'  => 'required|string|max:300',
            'valor'      => 'required',
            'vencimento' => 'required|date',
        ], [
            'clienteId.min'      => 'Selecione o cliente.',
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required'     => 'Informe o valor.',
        ]);

        $dados = [
            'tenant_id'   => Auth::guard('usuarios')->user()?->tenant_id,
            'cliente_id'  => $this->clienteId,
            'contrato_id' => $this->contratoId ?: null,
            'tipo'        => $this->tipo,
            'descricao'   => $this->descricao,
            'valor'       => (float) str_replace(['.', ','], ['', '.'], $this->valor),
            'vencimento'  => $this->vencimento,
            'status'      => 'previsto',
            'observacoes' => $this->observacoes ?: null,
        ];

        if ($this->lancamentoId) {
            DB::table('financeiro_lancamentos')->where('id', $this->lancamentoId)
                ->update(array_merge($dados, ['updated_at' => now()]));
            $msg = 'Lançamento atualizado.';
        } else {
            DB::table('financeiro_lancamentos')
                ->insert(array_merge($dados, ['created_at' => now(), 'updated_at' => now()]));
            $msg = 'Lançamento criado!';
        }

        $this->fecharModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function cancelar(int $id): void
    {
        DB::table('financeiro_lancamentos')->where('id', $id)
            ->update(['status' => 'cancelado', 'updated_at' => now()]);
        $this->dispatch('toast', message: 'Lançamento cancelado.', type: 'success');
    }

    // ── Modal: registrar recebimento ──────────────────────────
    public function abrirPagamento(int $id): void
    {
        $l = FinanceiroLancamento::findOrFail($id);
        $this->pagamentoLancId = $id;
        $this->dataPagamento   = now()->format('Y-m-d');
        $this->valorPago       = number_format($l->valor, 2, ',', '.');
        $this->formaPagamento  = 'pix';
        $this->pagamentoTipo   = $l->tipo;
        $this->modalPagamento  = true;
    }

    public function fecharPagamento(): void
    {
        $this->modalPagamento  = false;
        $this->pagamentoLancId = null;
        $this->resetErrorBag();
    }

    public function registrarPagamento(): void
    {
        $this->validate([
            'dataPagamento' => 'required|date',
            'valorPago'     => 'required',
        ]);

        DB::table('financeiro_lancamentos')->where('id', $this->pagamentoLancId)->update([
            'status'         => 'recebido',
            'data_pagamento' => $this->dataPagamento,
            'valor_pago'     => (float) str_replace(['.', ','], ['', '.'], $this->valorPago),
            'forma_pagamento'=> $this->formaPagamento,
            'updated_at'     => now(),
        ]);

        $this->fecharPagamento();
        $this->dispatch('toast', message: 'Pagamento registrado!', type: 'success');
    }

    // ── Render ────────────────────────────────────────────────
    public function render(): \Illuminate\View\View
    {
        FinanceiroLancamento::atualizarAtrasados();

        $lancamentos = FinanceiroLancamento::with(['cliente', 'contrato', 'processo'])
            ->when($this->busca, fn($q) => $q->where(fn($s) => $s
                ->where('descricao', 'ilike', "%{$this->busca}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->busca}%"))
            ))
            ->when($this->filtroStatus,  fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,    fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroCliente, fn($q) => $q->where('cliente_id', $this->filtroCliente))
            ->when($this->filtroMes,     fn($q) => $q->whereRaw("TO_CHAR(vencimento, 'YYYY-MM') = ?", [$this->filtroMes]))
            ->orderBy('vencimento')
            ->paginate(20);

        // KPIs do mês filtrado
        $base = FinanceiroLancamento::when($this->filtroMes, fn($q) =>
            $q->whereRaw("TO_CHAR(vencimento, 'YYYY-MM') = ?", [$this->filtroMes])
        );

        $totalPrevisto  = (clone $base)->where('tipo', 'receita')->whereIn('status', ['previsto','atrasado'])->sum('valor');
        $totalRecebido  = (clone $base)->where('tipo', 'receita')->where('status', 'recebido')->sum('valor_pago');
        $totalAtrasado  = (clone $base)->where('tipo', 'receita')->where('status', 'atrasado')->sum('valor');
        $totalDespesa   = (clone $base)->where('tipo', 'despesa')->whereIn('status', ['previsto','atrasado','recebido'])->sum('valor');
        $totalRepasse   = (clone $base)->where('tipo', 'repasse')->whereIn('status', ['previsto','atrasado'])->sum('valor');

        $clientes     = $this->clientes;
        $fornecedores = $this->fornecedores;

        return view('livewire.financeiro-central', compact(
            'lancamentos', 'totalPrevisto', 'totalRecebido', 'totalAtrasado', 'totalDespesa', 'totalRepasse', 'clientes', 'fornecedores'
        ));
    }
}
