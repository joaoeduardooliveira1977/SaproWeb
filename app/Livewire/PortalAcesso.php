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
    public ?string $mensagem = null;
    public ?string $erro = null;

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
        $this->mensagem = 'Acesso ao portal ativado!';
    }

    public function desativar(int $id): void
    {
        Pessoa::where('id', $id)->update(['portal_ativo' => false]);
        $this->mensagem = 'Acesso ao portal desativado.';
    }

    public function abrirDefinirSenha(int $id): void
    {
        $this->pessoaId     = $id;
        $this->novaSenha    = '';
        $this->confirmaSenha = '';
        $this->erro = null;
    }

    public function definirSenha(): void
    {
        if (strlen($this->novaSenha) < 6) {
            $this->erro = 'A senha deve ter pelo menos 6 caracteres.';
            return;
        }
        if ($this->novaSenha !== $this->confirmaSenha) {
            $this->erro = 'As senhas não conferem.';
            return;
        }

        Pessoa::where('id', $this->pessoaId)->update([
            'portal_senha' => Hash::make($this->novaSenha),
            'portal_ativo' => true,
        ]);

        $this->pessoaId = null;
        $this->mensagem = 'Senha definida e acesso ativado com sucesso!';
    }

    public function render()
    {
        return view('livewire.portal-acesso', [
            'pessoas' => $this->get(),
        ]);
    }
}
