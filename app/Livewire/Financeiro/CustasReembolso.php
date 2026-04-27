<?php

namespace App\Livewire\Financeiro;

use App\Services\Financeiro\CustasReembolsoService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CustasReembolso extends Component
{
    public ?int   $processoId           = null;
    public string $situacao             = 'todos';
    public bool   $modalConfirmar       = false;
    public ?int   $pagamentoSelecionado = null;
    public string $observacao           = '';

    #[Computed]
    public function custas(): \Illuminate\Support\Collection
    {
        return (new CustasReembolsoService())->listar(
            processoId: $this->processoId,
            situacao:   $this->situacao,
        );
    }

    #[Computed]
    public function totalPendente(): float
    {
        if (! $this->processoId) return 0;
        return (new CustasReembolsoService())->totalPorProcesso($this->processoId);
    }

    public function abrirModal(int $pagamentoId): void
    {
        $this->pagamentoSelecionado = $pagamentoId;
        $this->observacao           = '';
        $this->modalConfirmar       = true;
    }

    public function confirmarReembolso(): void
    {
        try {
            (new CustasReembolsoService())->gerarReembolso(
                $this->pagamentoSelecionado,
                $this->observacao ?: null,
            );
            session()->flash('sucesso', 'Recebimento de reembolso gerado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('erro', $e->getMessage());
        }

        $this->modalConfirmar = false;
        unset($this->custas, $this->totalPendente);
    }

    public function gerarTodos(): void
    {
        if (! $this->processoId) return;

        $count = (new CustasReembolsoService())->gerarReembolsoCompleto($this->processoId);
        session()->flash('sucesso', "{$count} reembolso(s) gerado(s) com sucesso!");
        unset($this->custas, $this->totalPendente);
    }

    public function render()
    {
        return view('livewire.financeiro.custas-reembolso')
            ->extends('layouts.app')
            ->section('content');
    }
}
