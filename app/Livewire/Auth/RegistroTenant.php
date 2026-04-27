<?php

namespace App\Livewire\Auth;

use App\Models\{Tenant, Usuario};
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Support\Str;
use Livewire\Component;

class RegistroTenant extends Component
{
    public int $etapa = 1;

    // Etapa 1 — Escritório
    public string $nome     = '';
    public string $email    = '';
    public string $cnpj     = '';
    public string $telefone = '';
    public string $oab      = '';
    public string $cidade   = '';

    // Etapa 2 — Admin
    public string $admin_nome                 = '';
    public string $admin_email                = '';
    public string $admin_senha                = '';
    public string $admin_senha_confirmation   = '';

    // Resultado
    public ?string $tenantNome      = null;
    public ?string $trialExpiracao  = null;

    protected function rulesEtapa1(): array
    {
        return [
            'nome'  => 'required|string|min:3|max:150',
            'email' => 'required|email|max:200|unique:tenants,email',
            'cnpj'  => 'nullable|string|max:18',
            'oab'   => 'nullable|string|max:30',
        ];
    }

    protected function messagesEtapa1(): array
    {
        return [
            'nome.required'  => 'O nome do escritório é obrigatório.',
            'nome.min'       => 'Mínimo 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'email.unique'   => 'Este e-mail já está cadastrado.',
        ];
    }

    protected function rulesEtapa2(): array
    {
        return [
            'admin_nome'  => 'required|string|min:3|max:150',
            'admin_email' => 'required|email|max:200|unique:usuarios,email',
            'admin_senha' => 'required|min:8|confirmed',
        ];
    }

    protected function messagesEtapa2(): array
    {
        return [
            'admin_nome.required'  => 'Seu nome é obrigatório.',
            'admin_nome.min'       => 'Mínimo 3 caracteres.',
            'admin_email.required' => 'O e-mail de acesso é obrigatório.',
            'admin_email.email'    => 'Informe um e-mail válido.',
            'admin_email.unique'   => 'Este e-mail já está cadastrado.',
            'admin_senha.required' => 'A senha é obrigatória.',
            'admin_senha.min'      => 'A senha deve ter no mínimo 8 caracteres.',
            'admin_senha.confirmed'=> 'As senhas não conferem.',
        ];
    }

    public function avancarEtapa1(): void
    {
        $this->validate($this->rulesEtapa1(), $this->messagesEtapa1());
        $this->etapa = 2;
    }

    public function avancarEtapa2(): void
    {
        $this->validate($this->rulesEtapa2(), $this->messagesEtapa2());

        DB::transaction(function () {
            $limites = Tenant::limitesPlano('demo');

            $tenant = Tenant::create([
                'nome'                => $this->nome,
                'email'               => $this->email,
                'slug'                => Str::slug($this->nome) . '-' . Str::random(4),
                'cnpj'                => $this->cnpj    ?: null,
                'telefone'            => $this->telefone ?: null,
                'oab'                 => $this->oab      ?: null,
                'cidade'              => $this->cidade   ?: null,
                'plano'               => 'demo',
                'ativo'               => true,
                'onboarding_concluido'=> false,
                'trial_expira_em'     => now()->addDays(30),
                'limite_processos'    => $limites['processos'],
                'limite_usuarios'     => $limites['usuarios'],
                'ia_habilitada'       => $limites['ia'],
                'datajud_habilitado'  => $limites['datajud'],
                'whatsapp_habilitado' => $limites['whatsapp'],
                'timezone'            => 'America/Sao_Paulo',
            ]);

            $usuario = Usuario::create([
                'tenant_id' => $tenant->id,
                'nome'      => $this->admin_nome,
                'email'     => $this->admin_email,
                'login'     => $this->admin_email,
                'password'  => Hash::make($this->admin_senha),
                'perfil'    => 'administrador',
                'ativo'     => true,
            ]);

            $this->tenantNome     = $tenant->nome;
            $this->trialExpiracao = $tenant->trial_expira_em->format('d/m/Y');

            Auth::guard('usuarios')->login($usuario);
        });

        $this->etapa = 3;
    }

    public function voltar(): void
    {
        if ($this->etapa > 1) $this->etapa--;
    }

    public function render()
    {
        return view('livewire.auth.registro-tenant')
            ->layout('layouts.guest');
    }
}
