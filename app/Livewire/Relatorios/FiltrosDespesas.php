<?php

namespace App\Livewire\Relatorios;

use App\Models\Pessoa;
use Livewire\Component;

class FiltrosDespesas extends Component
{
    public string $clienteBusca    = '';
    public ?int   $clienteId       = null;
    public string $clienteNome     = '';
    public array  $clienteSugestoes = [];
    public string $dataIni         = '';
    public string $dataFim         = '';

    public function mount(): void
    {
        $this->dataIni = now()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedClienteBusca(): void
    {
        if (strlen($this->clienteBusca) < 2) {
            $this->clienteSugestoes = [];
            return;
        }
        $this->clienteSugestoes = Pessoa::where('tenant_id', auth('usuarios')->user()->tenant_id)
            ->busca($this->clienteBusca)
            ->limit(8)
            ->get(['id', 'nome', 'cpf_cnpj'])
            ->toArray();
    }

    public function selecionarCliente(int $id, string $nome): void
    {
        $this->clienteId        = $id;
        $this->clienteNome      = $nome;
        $this->clienteBusca     = $nome;
        $this->clienteSugestoes = [];
    }

    public function limparCliente(): void
    {
        $this->clienteId        = null;
        $this->clienteNome      = '';
        $this->clienteBusca     = '';
        $this->clienteSugestoes = [];
    }

    public function gerar(): void
    {
        $this->validate([
            'clienteId' => 'required|integer',
            'dataIni'   => 'required|date',
            'dataFim'   => 'required|date|after_or_equal:dataIni',
        ], [
            'clienteId.required' => 'Selecione um cliente.',
            'dataIni.required'   => 'Informe a data de início.',
            'dataFim.required'   => 'Informe a data de fim.',
            'dataFim.after_or_equal' => 'A data final deve ser igual ou posterior à inicial.',
        ]);

        $url = route('relatorios.despesas.pdf', [
            'cliente_id' => $this->clienteId,
            'data_ini'   => $this->dataIni,
            'data_fim'   => $this->dataFim,
        ]);

        $this->dispatch('abrir-pdf', url: $url);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.relatorios.filtros-despesas')
            ->extends('layouts.app')
            ->section('content');
    }
}
