<?php

namespace App\Livewire\Financeiro;

use App\Models\{Fornecedor, Pagamento};
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DespesasEscritorio extends Component
{
    use WithPagination;

    public string $filtroCompetencia = '';
    public string $filtroCategoria   = '';
    public string $filtroStatus      = '';

    public bool   $modalAberto  = false;
    public ?int   $pagamentoId  = null;

    public string  $data           = '';
    public string  $descricao      = '';
    public string  $categoria      = '';
    public ?int    $fornecedorId   = null;
    public string  $valor          = '';
    public string  $dataVencimento = '';
    public bool    $pago           = false;
    public string  $dataPagamento  = '';
    public string  $valorPago      = '';

    public function mount(): void
    {
        $this->filtroCompetencia = now()->format('Y-m');
        if (request('novo')) {
            $this->abrirModal();
        }
    }

    public static function categorias(): array
    {
        return [
            'aluguel'   => 'Aluguel',
            'software'  => 'Software / Assinaturas',
            'salario'   => 'Salários / Pró-labore',
            'energia'   => 'Energia Elétrica',
            'telefone'  => 'Telefone',
            'internet'  => 'Internet',
            'material'  => 'Material de Escritório',
            'contador'  => 'Contabilidade',
            'marketing' => 'Marketing / Publicidade',
            'imposto'   => 'Impostos / Taxas',
            'manutencao'=> 'Manutenção',
            'outros'    => 'Outros',
        ];
    }

    public function abrirModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->pagamentoId = $id;

        if ($id) {
            $p = Pagamento::findOrFail($id);
            $this->data           = $p->data?->format('Y-m-d') ?? '';
            $this->descricao      = $p->descricao ?? '';
            $this->categoria      = $p->categoria ?? '';
            $this->fornecedorId   = $p->fornecedor_id;
            $this->valor          = (string) $p->valor;
            $this->dataVencimento = $p->data_vencimento?->format('Y-m-d') ?? '';
            $this->pago           = (bool) $p->pago;
            $this->dataPagamento  = $p->data_pagamento?->format('Y-m-d') ?? '';
            $this->valorPago      = $p->pago ? (string) $p->valor_pago : '';
        } else {
            $this->data           = now()->format('Y-m-d');
            $this->descricao      = '';
            $this->categoria      = '';
            $this->fornecedorId   = null;
            $this->valor          = '';
            $this->dataVencimento = '';
            $this->pago           = false;
            $this->dataPagamento  = '';
            $this->valorPago      = '';
        }

        $this->modalAberto = true;
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
    }

    protected function rules(): array
    {
        return [
            'data'           => 'required|date',
            'descricao'      => 'required|string|max:200',
            'categoria'      => 'required|string',
            'fornecedorId'   => 'nullable|integer',
            'valor'          => 'required|numeric|min:0.01',
            'dataVencimento' => 'nullable|date',
            'pago'           => 'boolean',
            'dataPagamento'  => 'nullable|date|required_if:pago,true',
            'valorPago'      => 'nullable|numeric|required_if:pago,true',
        ];
    }

    protected function messages(): array
    {
        return [
            'data.required'             => 'A data é obrigatória.',
            'descricao.required'        => 'A descrição é obrigatória.',
            'categoria.required'        => 'Selecione uma categoria.',
            'valor.required'            => 'O valor é obrigatório.',
            'valor.min'                 => 'O valor deve ser maior que zero.',
            'dataPagamento.required_if' => 'Informe a data de pagamento.',
            'valorPago.required_if'     => 'Informe o valor pago.',
        ];
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'processo_id'     => null,
            'data'            => $this->data,
            'descricao'       => $this->descricao,
            'categoria'       => $this->categoria,
            'fornecedor_id'   => $this->fornecedorId ?: null,
            'valor'           => $this->valor,
            'data_vencimento' => $this->dataVencimento ?: null,
            'pago'            => $this->pago,
            'data_pagamento'  => $this->pago ? $this->dataPagamento : null,
            'valor_pago'      => $this->pago ? $this->valorPago : null,
            'usuario_id'      => Auth::guard('usuarios')->id(),
            'reembolsavel'    => false,
        ];

        if ($this->pagamentoId) {
            Pagamento::findOrFail($this->pagamentoId)->update($dados);
        } else {
            Pagamento::create($dados);
        }

        $this->fecharModal();
        $this->resetPage();
    }

    public function togglePago(int $id): void
    {
        $p = Pagamento::findOrFail($id);
        if ($p->pago) {
            $p->update(['pago' => false, 'data_pagamento' => null, 'valor_pago' => null]);
        } else {
            $p->update(['pago' => true, 'data_pagamento' => now()->format('Y-m-d'), 'valor_pago' => $p->valor]);
        }
    }

    public function excluir(int $id): void
    {
        Pagamento::whereNull('processo_id')->findOrFail($id)->delete();
    }

    public function updatedFiltroCompetencia(): void { $this->resetPage(); }
    public function updatedFiltroCategoria(): void   { $this->resetPage(); }
    public function updatedFiltroStatus(): void      { $this->resetPage(); }

    public function render()
    {
        $query = Pagamento::query()
            ->whereNull('processo_id')
            ->with('fornecedor')
            ->orderByDesc('data')
            ->orderByDesc('id');

        if ($this->filtroCompetencia) {
            $query->whereRaw("TO_CHAR(data, 'YYYY-MM') = ?", [$this->filtroCompetencia]);
        }
        if ($this->filtroCategoria) {
            $query->where('categoria', $this->filtroCategoria);
        }
        if ($this->filtroStatus === 'pago') {
            $query->where('pago', true);
        } elseif ($this->filtroStatus === 'pendente') {
            $query->where('pago', false);
        }

        $despesas = $query->paginate(20);

        $totais = Pagamento::query()
            ->whereNull('processo_id')
            ->when($this->filtroCompetencia, fn($q) => $q->whereRaw("TO_CHAR(data, 'YYYY-MM') = ?", [$this->filtroCompetencia]))
            ->selectRaw("SUM(valor) as total, SUM(CASE WHEN pago THEN valor_pago ELSE 0 END) as pago, SUM(CASE WHEN NOT pago THEN valor ELSE 0 END) as pendente")
            ->first();

        $fornecedores = Fornecedor::ativos()->orderBy('nome')->get(['id', 'nome']);

        return view('livewire.financeiro.despesas-escritorio', [
            'despesas'    => $despesas,
            'totais'      => $totais,
            'fornecedores'=> $fornecedores,
            'categorias'  => self::categorias(),
        ])->extends('layouts.app')->section('content');
    }
}
