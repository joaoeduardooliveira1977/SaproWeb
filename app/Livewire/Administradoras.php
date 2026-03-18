<?php

namespace App\Livewire;

use App\Models\Administradora;
use Livewire\Component;
use Livewire\WithPagination;

class Administradoras extends Component
{
    use WithPagination;

    public string $busca  = '';
    public string $filtroAtivo = '1';

    protected $queryString = [
        'busca'       => ['except' => ''],
        'filtroAtivo' => ['except' => '1'],
    ];

    // Modal
    public bool    $modal   = false;
    public ?int    $admId   = null;
    public string  $nome    = '';
    public string  $cnpj    = '';
    public string  $telefone= '';
    public string  $email   = '';
    public string  $contato = '';
    public string  $observacoes = '';

    protected function rules(): array
    {
        return [
            'nome'  => 'required|string|max:150',
            'cnpj'  => 'nullable|string|max:18|unique:administradoras,cnpj' . ($this->admId ? ",{$this->admId}" : ''),
            'email' => 'nullable|email|max:150',
        ];
    }

    protected $messages = [
        'nome.required' => 'O nome é obrigatório.',
        'cnpj.unique'   => 'Este CNPJ já está cadastrado.',
        'email.email'   => 'E-mail inválido.',
    ];

    public function updatingBusca(): void { $this->resetPage(); }

    // ── Modal ────────────────────────────────────────────────

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();
        $this->admId = $id;
        $this->modal = true;

        if ($id) {
            $a = Administradora::findOrFail($id);
            $this->nome       = $a->nome;
            $this->cnpj       = $a->cnpj       ?? '';
            $this->telefone   = $a->telefone   ?? '';
            $this->email      = $a->email      ?? '';
            $this->contato    = $a->contato    ?? '';
            $this->observacoes= $a->observacoes ?? '';
        }
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetForm();
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'nome'        => $this->nome,
            'cnpj'        => $this->cnpj       ?: null,
            'telefone'    => $this->telefone   ?: null,
            'email'       => $this->email      ?: null,
            'contato'     => $this->contato    ?: null,
            'observacoes' => $this->observacoes ?: null,
        ];

        if ($this->admId) {
            Administradora::findOrFail($this->admId)->update($dados);
            $msg = "Administradora \"{$this->nome}\" atualizada.";
        } else {
            Administradora::create(array_merge($dados, ['ativo' => true]));
            $msg = "Administradora \"{$this->nome}\" cadastrada.";
        }

        $this->fecharModal();
        $this->dispatch('toast', tipo: 'success', msg: $msg);
    }

    public function toggleAtivo(int $id): void
    {
        $a = Administradora::findOrFail($id);
        $a->update(['ativo' => ! $a->ativo]);
        $this->dispatch('toast', tipo: 'success',
            msg: $a->ativo ? "Administradora ativada." : "Administradora desativada.");
    }

    public function excluir(int $id): void
    {
        $a = Administradora::withCount('clientes')->findOrFail($id);

        if ($a->clientes_count > 0) {
            $this->dispatch('toast', tipo: 'error',
                msg: "Não é possível excluir: há {$a->clientes_count} cliente(s) vinculado(s).");
            return;
        }

        $nome = $a->nome;
        $a->delete();
        $this->dispatch('toast', tipo: 'success', msg: "Administradora \"{$nome}\" excluída.");
    }

    private function resetForm(): void
    {
        $this->admId = null;
        $this->nome = $this->cnpj = $this->telefone = '';
        $this->email = $this->contato = $this->observacoes = '';
        $this->resetErrorBag();
    }

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        $adms = Administradora::withCount('clientes')
            ->when($this->busca, fn($q) =>
                $q->where('nome', 'ilike', "%{$this->busca}%")
                  ->orWhere('cnpj', 'ilike', "%{$this->busca}%")
                  ->orWhere('contato', 'ilike', "%{$this->busca}%")
            )
            ->when($this->filtroAtivo !== '', fn($q) =>
                $q->where('ativo', (bool) $this->filtroAtivo)
            )
            ->orderBy('nome')
            ->paginate(20);

        return view('livewire.administradoras', compact('adms'));
    }
}
