<?php

namespace App\Livewire;

use App\Jobs\PopularDadosFicticios;
use App\Jobs\ProcessarSequenciaEmailsTrial;
use App\Mail\TrialBoasVindas;
use App\Models\Tenant;
use App\Models\TrialEmail;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class CadastroTrial extends Component
{
    public string $responsavel_nome = '';
    public string $nome_escritorio  = '';
    public string $email            = '';
    public string $telefone         = '';
    public string $cidade           = '';
    public string $estado           = 'SP';
    public string $senha            = '';
    public string $senha_confirmacao = '';
    public bool   $aceitar_termos   = false;
    public bool   $carregando       = false;

    protected function rules(): array
    {
        return [
            'responsavel_nome'   => 'required|string|min:3|max:150',
            'nome_escritorio'    => 'required|string|min:3|max:150',
            'email'              => 'required|email|max:150|unique:usuarios,email',
            'telefone'           => 'nullable|string|max:30',
            'cidade'             => 'nullable|string|max:80',
            'estado'             => 'required|string|size:2',
            'senha'              => 'required|string|min:8',
            'senha_confirmacao'  => 'required|same:senha',
            'aceitar_termos'     => 'accepted',
        ];
    }

    protected $messages = [
        'responsavel_nome.required'  => 'Informe seu nome completo.',
        'responsavel_nome.min'       => 'O nome deve ter ao menos 3 caracteres.',
        'nome_escritorio.required'   => 'Informe o nome do escritório.',
        'nome_escritorio.min'        => 'O nome do escritório deve ter ao menos 3 caracteres.',
        'email.required'             => 'Informe seu email.',
        'email.email'                => 'Email inválido.',
        'email.unique'               => 'Este email já está cadastrado. Faça login ou use outro email.',
        'senha.required'             => 'Defina uma senha.',
        'senha.min'                  => 'A senha deve ter ao menos 8 caracteres.',
        'senha_confirmacao.required' => 'Confirme a senha.',
        'senha_confirmacao.same'     => 'As senhas não coincidem.',
        'aceitar_termos.accepted'    => 'Você precisa aceitar os termos de uso para continuar.',
        'estado.required'            => 'Selecione seu estado.',
    ];

    public function cadastrar(): void
    {
        $this->validate();
        $this->carregando = true;

        $slug = $this->gerarSlugUnico($this->nome_escritorio);
        $limites = Tenant::limitesPlanoCompleto('demo');

        $tenant = Tenant::create([
            'nome'                 => $this->nome_escritorio,
            'slug'                 => $slug,
            'dominio'              => "{$slug}.kmd-ia.com.br",
            'email'                => $this->email,
            'telefone'             => $this->telefone ?: null,
            'cidade'               => $this->cidade ?: null,
            'plano'                => 'demo',
            'trial_iniciado_em'    => now(),
            'trial_expira_em'      => now()->addDays(15),
            'ativo'                => true,
            'origem'               => 'formulario',
            'responsavel_nome'     => $this->responsavel_nome,
            'responsavel_telefone' => $this->telefone ?: null,
            'onboarding_concluido' => false,
            ...$limites,
        ]);

        $usuario = Usuario::create([
            'tenant_id' => $tenant->id,
            'nome'      => $this->responsavel_nome,
            'email'     => $this->email,
            'login'     => $this->email,
            'password'  => Hash::make($this->senha),
            'perfil'    => 'admin',
            'ativo'     => true,
        ]);

        // Dados fictícios (queue)
        PopularDadosFicticios::dispatch($tenant->id, $usuario->id);

        // Email de boas-vindas imediato + registrar no controle
        try {
            Mail::to($this->email)->send(new TrialBoasVindas($tenant));
            TrialEmail::create([
                'tenant_id'  => $tenant->id,
                'tipo'       => 'boas_vindas',
                'enviado_em' => now(),
                'sucesso'    => true,
            ]);
        } catch (\Throwable) {
            // Não bloquear o cadastro se email falhar
        }

        session()->flash('sucesso', 'Conta criada! Acesse com seu email e senha. Verifique também seu email — enviamos as instruções de acesso.');

        $this->redirect(route('login'), navigate: false);
    }

    private function gerarSlugUnico(string $nome): string
    {
        $base = Str::slug($nome);

        if (!Tenant::where('slug', $base)->exists()) {
            return $base;
        }

        $i = 2;
        while (Tenant::where('slug', "{$base}-{$i}")->exists()) {
            $i++;
        }
        return "{$base}-{$i}";
    }

    public function render()
    {
        return view('livewire.cadastro-trial')
            ->layout('layouts.guest');
    }
}
