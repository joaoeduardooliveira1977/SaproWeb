<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PortalLogin extends Component
{
    public string $cpf_cnpj = '';
    public string $senha    = '';
    public ?string $erro    = null;

    public function autenticar(): void
    {
        $this->erro = null;

        $cpf_limpo = preg_replace('/\D/', '', $this->cpf_cnpj);

        $pessoa = Pessoa::where(function($q) use ($cpf_limpo) {
            $q->where('cpf_cnpj', $this->cpf_cnpj)
            ->orWhereRaw("regexp_replace(cpf_cnpj, '[^0-9]', '', 'g') = ?", [$cpf_limpo]);
            })
            ->where('portal_ativo', true)
            ->first();

        if (! $pessoa || ! Hash::check($this->senha, $pessoa->portal_senha)) {
            $this->erro = 'CPF/CNPJ ou senha inválidos.';
            return;
        }

        $pessoa->update(['portal_ultimo_acesso' => now()]);

        Session::put('portal_pessoa_id', $pessoa->id);
        Session::put('portal_pessoa_nome', $pessoa->nome);

        $this->redirect(route('portal.dashboard'));
    }

    public function render()
    {
        return view('livewire.portal.login')
            ->layout('portal.layout');
    }
}
