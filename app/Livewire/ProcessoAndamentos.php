<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Andamento;
use App\Models\Processo;
use Illuminate\Support\Facades\Auth;

class ProcessoAndamentos extends Component
{
    public int $processoId;
    public object $processo;

    // Formulário
    public string $data       = '';
    public string $descricao  = '';
    public ?int   $editandoId = null;

    // Confirmação de exclusão
    public ?int $excluindoId = null;

    public bool $mostrarFormulario = false;

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
        $this->processo   = Processo::findOrFail($processoId);
        $this->data       = now()->format('Y-m-d');
    }

    public function novoAndamento(): void
    {
        $this->resetForm();
        $this->mostrarFormulario = true;
    }

    public function editar(int $id): void
    {
        $andamento = Andamento::findOrFail($id);
        $this->editandoId       = $id;
        $this->data             = $andamento->data->format('Y-m-d');
        $this->descricao        = $andamento->descricao;
        $this->mostrarFormulario = true;
    }

    public function salvar(): void
    {
        $this->validate([
            'data'      => 'required|date',
            'descricao' => 'required|string|min:3',
        ]);

        if ($this->editandoId) {
            Andamento::findOrFail($this->editandoId)->update([
                'data'      => $this->data,
                'descricao' => $this->descricao,
            ]);
        } else {
            Andamento::create([
                'processo_id' => $this->processoId,
                'data'        => $this->data,
                'descricao'   => $this->descricao,
                'usuario_id'  => Auth::id(),
            ]);
        }

        $this->resetForm();
        $this->mostrarFormulario = false;
        session()->flash('sucesso', 'Andamento salvo com sucesso!');
    }

    public function confirmarExclusao(int $id): void
    {
        $this->excluindoId = $id;
    }

    public function excluir(): void
    {
        if ($this->excluindoId) {
            Andamento::findOrFail($this->excluindoId)->delete();
            $this->excluindoId = null;
            session()->flash('sucesso', 'Andamento excluído com sucesso!');
        }
    }

    public function cancelar(): void
    {
        $this->resetForm();
        $this->mostrarFormulario = false;
        $this->excluindoId = null;
    }

    private function resetForm(): void
    {
        $this->editandoId = null;
        $this->data       = now()->format('Y-m-d');
        $this->descricao  = '';
    }

    public function render()
    {
        $andamentos = Andamento::where('processo_id', $this->processoId)
            ->orderBy('data', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.processo-andamentos', compact('andamentos'));
    }
}
