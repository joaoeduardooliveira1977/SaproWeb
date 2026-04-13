<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Apontamento, Pagamento, Recebimento, Fornecedor, OrigemRecebimento, Pessoa, Processo};
use Illuminate\Support\Facades\Auth;

/**
 * Software Jur�dico — Módulo Financeiro por Processo
 * Gerencia Apontamentos, Pagamentos e Recebimentos.
 */
class Financeiro extends Component
{
    use WithPagination;

    public int    $processoId;
    public string $aba = 'pagamentos'; // pagamentos | recebimentos | apontamentos

    // ── Estado dos modais ──────────────────────
    public bool  $modalAberto = false;
    public ?int  $registroId  = null;

    // ── Campos compartilhados ──────────────────
    public string $data       = '';
    public string $descricao  = '';
    public string $valor      = '0,00';

    // ── Campos de Pagamento ────────────────────
    public string $numero_doc      = '';
    public string $documento       = '';
    public string $valor_pago      = '0,00';
    public string $data_vencimento = '';
    public string $data_pagamento  = '';
    public bool   $pago            = false;
    public string $fornecedor_id   = '';

    // ── Campos de Recebimento ──────────────────
    public string $valor_recebido   = '0,00';
    public string $data_recebimento = '';
    public bool   $recebido         = false;
    public string $origem_id        = '';

    // ── Campos de Apontamento ──────────────────
    public string $horas       = '0,00';
    public string $advogado_id = '';

    // ── Totais ─────────────────────────────────
    public array $totais = [];

    protected function rules(): array
    {
        return match ($this->aba) {
            'apontamentos' => [
                'data'      => 'required|date',
                'descricao' => 'required|string|max:500',
                'horas'     => 'nullable',
            ],
            default => [
                'data'      => 'required|date',
                'descricao' => 'required|string|max:200',
                'valor'     => 'nullable',
            ],
        };
    }

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
        $this->data       = today()->format('Y-m-d');
        $this->atualizarTotais();
    }

    public function updatedAba(): void
    {
        $this->resetPage();
        $this->atualizarTotais();
    }

    // ── Totais ─────────────────────────────────
    public function atualizarTotais(): void
    {
        $this->totais = [
            'pagamentos'   => Pagamento::totaisPorProcesso($this->processoId),
            'recebimentos' => Recebimento::totaisPorProcesso($this->processoId),
            'apontamentos' => Apontamento::totaisPorProcesso($this->processoId),
        ];
    }

    // ── Modal ──────────────────────────────────
    public function abrirModal(?int $id = null): void
    {
        $this->limpar();
        $this->registroId = $id;
        $this->modalAberto = true;

        if (!$id) return;

        match ($this->aba) {
            'pagamentos'   => $this->carregarPagamento($id),
            'recebimentos' => $this->carregarRecebimento($id),
            'apontamentos' => $this->carregarApontamento($id),
        };
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->limpar();
    }

    private function carregarPagamento(int $id): void
    {
        $p = Pagamento::findOrFail($id);
        $this->data           = $p->data->format('Y-m-d');
        $this->descricao      = $p->descricao;
        $this->valor          = number_format($p->valor, 2, ',', '.');
        $this->valor_pago     = number_format($p->valor_pago, 2, ',', '.');
        $this->numero_doc     = $p->numero_doc ?? '';
        $this->documento      = $p->documento ?? '';
        $this->data_vencimento= $p->data_vencimento?->format('Y-m-d') ?? '';
        $this->data_pagamento = $p->data_pagamento?->format('Y-m-d') ?? '';
        $this->pago           = $p->pago;
        $this->fornecedor_id  = (string) ($p->fornecedor_id ?? '');
    }

    private function carregarRecebimento(int $id): void
    {
        $r = Recebimento::findOrFail($id);
        $this->data             = $r->data->format('Y-m-d');
        $this->descricao        = $r->descricao ?? '';
        $this->valor            = number_format($r->valor, 2, ',', '.');
        $this->valor_recebido   = number_format($r->valor_recebido, 2, ',', '.');
        $this->numero_doc       = $r->numero_doc ?? '';
        $this->data_recebimento = $r->data_recebimento?->format('Y-m-d') ?? '';
        $this->recebido         = $r->recebido;
        $this->origem_id        = (string) ($r->origem_id ?? '');
    }

    private function carregarApontamento(int $id): void
    {
        $a = Apontamento::findOrFail($id);
        $this->data        = $a->data->format('Y-m-d');
        $this->descricao   = $a->descricao;
        $this->horas       = number_format($a->horas, 2, ',', '.');
        $this->valor       = number_format($a->valor, 2, ',', '.');
        $this->advogado_id = (string) ($a->advogado_id ?? '');
    }

    // ── Salvar ─────────────────────────────────
    public function salvar(): void
    {
        $this->validate();

        match ($this->aba) {
            'pagamentos'   => $this->salvarPagamento(),
            'recebimentos' => $this->salvarRecebimento(),
            'apontamentos' => $this->salvarApontamento(),
        };

        $this->fecharModal();
        $this->atualizarTotais();
        session()->flash('sucesso', 'Registro salvo com sucesso!');
    }

    private function salvarPagamento(): void
    {
        $dados = [
            'processo_id'     => $this->processoId,
            'fornecedor_id'   => $this->fornecedor_id   ?: null,
            'data'            => $this->data,
            'descricao'       => $this->descricao,
            'valor'           => $this->toDecimal($this->valor),
            'valor_pago'      => $this->toDecimal($this->valor_pago),
            'numero_doc'      => $this->numero_doc       ?: null,
            'documento'       => $this->documento        ?: null,
            'data_vencimento' => $this->data_vencimento  ?: null,
            'data_pagamento'  => $this->data_pagamento   ?: null,
            'pago'            => $this->pago,
            'usuario_id'      => Auth::id(),
        ];

        $this->registroId
            ? Pagamento::findOrFail($this->registroId)->update($dados)
            : Pagamento::create($dados);
    }

    private function salvarRecebimento(): void
    {
        $dados = [
            'processo_id'      => $this->processoId,
            'origem_id'        => $this->origem_id         ?: null,
            'data'             => $this->data,
            'descricao'        => $this->descricao         ?: null,
            'valor'            => $this->toDecimal($this->valor),
            'valor_recebido'   => $this->toDecimal($this->valor_recebido),
            'numero_doc'       => $this->numero_doc         ?: null,
            'data_recebimento' => $this->data_recebimento   ?: null,
            'recebido'         => $this->recebido,
            'usuario_id'       => Auth::id(),
        ];

        $this->registroId
            ? Recebimento::findOrFail($this->registroId)->update($dados)
            : Recebimento::create($dados);
    }

    private function salvarApontamento(): void
    {
        $dados = [
            'processo_id' => $this->processoId,
            'advogado_id' => $this->advogado_id ?: null,
            'data'        => $this->data,
            'descricao'   => $this->descricao,
            'horas'       => $this->toDecimal($this->horas),
            'valor'       => $this->toDecimal($this->valor),
            'usuario_id'  => Auth::id(),
        ];

        $this->registroId
            ? Apontamento::findOrFail($this->registroId)->update($dados)
            : Apontamento::create($dados);
    }

    // ── Excluir ────────────────────────────────
    public function excluir(int $id): void
    {
        match ($this->aba) {
            'pagamentos'   => Pagamento::findOrFail($id)->delete(),
            'recebimentos' => Recebimento::findOrFail($id)->delete(),
            'apontamentos' => Apontamento::findOrFail($id)->delete(),
        };
        $this->atualizarTotais();
        session()->flash('sucesso', 'Registro removido.');
    }

    // ── Helpers ────────────────────────────────
    private function toDecimal(string $valor): float
    {
        return (float) str_replace(['.', ','], ['', '.'], $valor);
    }

    private function limpar(): void
    {
        $this->registroId = null;
        $this->data = today()->format('Y-m-d');
        $this->descricao = $this->valor = $this->numero_doc = $this->documento = '';
        $this->valor_pago = $this->data_vencimento = $this->data_pagamento = '';
        $this->valor_recebido = $this->data_recebimento = $this->origem_id = '';
        $this->horas = $this->advogado_id = $this->fornecedor_id = '';
        $this->pago = $this->recebido = false;
        $this->valor = $this->valor_pago = $this->valor_recebido = $this->horas = '0,00';
        $this->resetErrorBag();
    }

    public function render()
    {
        $processo = Processo::findOrFail($this->processoId);

        $pagamentos = Pagamento::with('fornecedor')
            ->where('processo_id', $this->processoId)
            ->orderByDesc('data')->paginate(10, pageName: 'pag_pag');

        $recebimentos = Recebimento::with('origem')
            ->where('processo_id', $this->processoId)
            ->orderByDesc('data')->paginate(10, pageName: 'rec_pag');

        $apontamentos = Apontamento::with('advogado')
            ->where('processo_id', $this->processoId)
            ->orderByDesc('data')->paginate(10, pageName: 'apo_pag');

        return view('livewire.financeiro', [
            'processo'     => $processo,
            'pagamentos'   => $pagamentos,
            'recebimentos' => $recebimentos,
            'apontamentos' => $apontamentos,
            'fornecedores' => Fornecedor::ativos()->orderBy('nome')->get(),
            'origens'      => OrigemRecebimento::where('ativo', true)->orderBy('descricao')->get(),
            'advogados'    => Pessoa::ativos()->doTipo('Advogado')->orderBy('nome')->get(),
        ]);
    }
}
