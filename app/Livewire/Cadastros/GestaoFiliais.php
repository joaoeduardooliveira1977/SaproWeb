<?php

namespace App\Livewire\Cadastros;

use App\Models\Filial;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GestaoFiliais extends Component
{
    public string $busca       = '';
    public bool   $modalAberto = false;
    public ?int   $filialId    = null;
    public string $nome        = '';
    public bool   $ativo       = true;
    public ?int   $confirmarExcluirId = null;

    protected function rules(): array
    {
        return [
            'nome'  => 'required|string|min:2|max:150',
            'ativo' => 'boolean',
        ];
    }

    protected array $messages = [
        'nome.required' => 'Informe o nome da filial.',
        'nome.min'      => 'O nome deve ter ao menos 2 caracteres.',
    ];

    #[Computed]
    public function filiais(): \Illuminate\Database\Eloquent\Collection
    {
        return Filial::when($this->busca, fn($q) => $q->where('nome', 'ilike', "%{$this->busca}%"))
            ->orderBy('nome')
            ->get();
    }

    public function abrir(?int $id = null): void
    {
        $this->filialId = $id;
        $this->nome     = '';
        $this->ativo    = true;

        if ($id) {
            $f = Filial::findOrFail($id);
            $this->nome  = $f->nome;
            $this->ativo = $f->ativo;
        }

        $this->resetErrorBag();
        $this->modalAberto = true;
    }

    public function fechar(): void
    {
        $this->modalAberto = false;
        $this->filialId    = null;
    }

    public function salvar(): void
    {
        $this->validate();

        if ($this->filialId) {
            Filial::findOrFail($this->filialId)->update(['nome' => trim($this->nome), 'ativo' => $this->ativo]);
            $this->dispatch('toast', type: 'sucesso', msg: 'Filial atualizada!');
        } else {
            Filial::create(['nome' => trim($this->nome), 'ativo' => $this->ativo]);
            $this->dispatch('toast', type: 'sucesso', msg: 'Filial criada!');
        }

        $this->modalAberto = false;
        unset($this->filiais);
    }

    public function excluir(int $id): void
    {
        Filial::findOrFail($id)->delete();
        $this->confirmarExcluirId = null;
        unset($this->filiais);
        $this->dispatch('toast', type: 'sucesso', msg: 'Filial excluída.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.cadastros.gestao-filiais', [
            'filiais' => $this->filiais,
        ])->extends('layouts.app')->section('content');
    }
}
