<?php

namespace App\Livewire;

use App\Models\ModeloContrato;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ModelosContrato extends Component
{
    public bool   $modal    = false;
    public ?int   $modeloId = null;
    public string $nome     = '';
    public string $tipo     = 'honorario_processo';
    public string $texto    = '';
    public bool   $ativo    = true;

    public function abrirModal(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->modeloId = $id;

        if ($id) {
            $m = ModeloContrato::findOrFail($id);
            $this->nome  = $m->nome;
            $this->tipo  = $m->tipo;
            $this->texto = $m->texto;
            $this->ativo = $m->ativo;
        } else {
            $this->nome  = '';
            $this->tipo  = 'honorario_processo';
            $this->texto = ModeloContrato::templatesPadrao()[0]['texto'];
            $this->ativo = true;
        }

        $this->modal = true;
    }

    public function selecionarTemplate(string $tipo): void
    {
        $templates = collect(ModeloContrato::templatesPadrao())->keyBy('tipo');
        $tpl = $templates->get($tipo);
        if ($tpl) {
            $this->texto = $tpl['texto'];
            $this->tipo  = $tipo;
            $this->nome  = $tpl['nome'];
        }
    }

    public function salvar(): void
    {
        $this->validate([
            'nome'  => 'required|string|max:150',
            'tipo'  => 'required|string',
            'texto' => 'required|string',
        ], [
            'nome.required'  => 'Informe o nome do modelo.',
            'texto.required' => 'O texto do contrato é obrigatório.',
        ]);

        $dados = [
            'nome'  => $this->nome,
            'tipo'  => $this->tipo,
            'texto' => $this->texto,
            'ativo' => $this->ativo,
        ];

        if ($this->modeloId) {
            ModeloContrato::where('id', $this->modeloId)->update($dados);
        } else {
            ModeloContrato::create(array_merge($dados, ['tenant_id' => tenant_id()]));
        }

        $this->modal = false;
        $this->dispatch('toast', tipo: 'success', msg: 'Modelo salvo com sucesso.');
    }

    public function excluir(int $id): void
    {
        ModeloContrato::where('id', $id)->where('tenant_id', tenant_id())->delete();
        $this->dispatch('toast', tipo: 'success', msg: 'Modelo excluído.');
    }

    public function toggleAtivo(int $id): void
    {
        $m = ModeloContrato::where('id', $id)->where('tenant_id', tenant_id())->firstOrFail();
        $m->update(['ativo' => ! $m->ativo]);
    }

    public function render()
    {
        $modelos = ModeloContrato::where('tenant_id', tenant_id())
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get();

        return view('livewire.modelos-contrato', compact('modelos'))
            ->layout('layouts.app');
    }
}
