<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;

class PortalAcesso extends Component
{
    public string $busca = '';
    public ?int $pessoaId = null;
    public string $novaSenha = '';
    public string $confirmaSenha = '';

    public function get()
    {
        return Pessoa::ativos()
            ->doTipo('Cliente')
            ->when($this->busca, fn($q) => $q->busca($this->busca))
            ->orderBy('nome')
            ->paginate(15);
    }

    public function ativar(int $id): void
    {
        Pessoa::where('id', $id)->update(['portal_ativo' => true]);
        $this->dispatch('toast', message: 'Acesso ao portal ativado!', type: 'success');
    }

    public function desativar(int $id): void
    {
        Pessoa::where('id', $id)->update(['portal_ativo' => false]);
        $this->dispatch('toast', message: 'Acesso ao portal desativado.', type: 'success');
    }

    public function abrirDefinirSenha(int $id): void
    {
        $this->pessoaId      = $id;
        $this->novaSenha     = '';
        $this->confirmaSenha = '';
    }

    public function definirSenha(): void
    {
        if (strlen($this->novaSenha) < 6) {
            $this->dispatch('toast', message: 'A senha deve ter pelo menos 6 caracteres.', type: 'error');
            return;
        }
        if ($this->novaSenha !== $this->confirmaSenha) {
            $this->dispatch('toast', message: 'As senhas não conferem.', type: 'error');
            return;
        }

        Pessoa::where('id', $this->pessoaId)->update([
            'portal_senha' => Hash::make($this->novaSenha),
            'portal_ativo' => true,
        ]);

        $this->pessoaId = null;
        $this->dispatch('toast', message: 'Senha definida e acesso ativado com sucesso!', type: 'success');
    }

    public function render()
    {
        return view('livewire.portal-acesso', [
            'pessoas'        => $this->get(),
            'totalClientes'  => Pessoa::ativos()->doTipo('Cliente')->count(),
            'portalAtivos'   => Pessoa::ativos()->doTipo('Cliente')->where('portal_ativo', true)->count(),
            'portalInativos' => Pessoa::ativos()->doTipo('Cliente')->where('portal_ativo', false)->count(),
        ]);
    }
}
