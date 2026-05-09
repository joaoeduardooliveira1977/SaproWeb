<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GestaoUsuarios extends Component
{
    public bool   $modalAberto = false;
    public ?int   $usuarioId   = null;

    public string $nome              = '';
    public string $email             = '';
    public string $login             = '';
    public string $telefone          = '';
    public string $perfil            = 'advogado';
    public string $senha             = '';
    public string $senhaConfirmacao  = '';
    public bool   $ativo             = true;

    public string $busca        = '';
    public string $filtroPerfil = '';

    public array $perfisDisponiveis = [
        'administrador' => 'Administrador — acesso total',
        'advogado'      => 'Advogado — processos e clientes',
        'financeiro'    => 'Financeiro — inclui módulo financeiro',
        'estagiario'    => 'Estagiário — acesso limitado',
        'recepcionista' => 'Recepcionista — acesso básico',
    ];

    protected function rules(): array
    {
        $rules = [
            'nome'             => 'required|string|min:3|max:150',
            'email'            => 'required|email|max:150',
            'login'            => 'required|string|min:3|max:60',
            'telefone'         => 'nullable|string|max:30',
            'perfil'           => 'required|in:administrador,advogado,financeiro,estagiario,recepcionista',
            'ativo'            => 'boolean',
            'senhaConfirmacao' => 'same:senha',
        ];

        if (!$this->usuarioId) {
            $rules['senha'] = 'required|min:8';
            $rules['email'] = "required|email|unique:usuarios,email";
            $rules['login'] = "required|string|unique:usuarios,login";
        } else {
            $rules['senha'] = 'nullable|min:8';
            $rules['email'] = "required|email|unique:usuarios,email,{$this->usuarioId}";
            $rules['login'] = "required|string|unique:usuarios,login,{$this->usuarioId}";
        }

        return $rules;
    }

    protected array $messages = [
        'nome.required'         => 'Informe o nome.',
        'nome.min'              => 'Nome deve ter ao menos 3 caracteres.',
        'email.required'        => 'Informe o e-mail.',
        'email.email'           => 'E-mail inválido.',
        'email.unique'          => 'Este e-mail já está em uso.',
        'login.required'        => 'Informe o login.',
        'login.min'             => 'Login deve ter ao menos 3 caracteres.',
        'login.unique'          => 'Este login já está em uso.',
        'senha.required'        => 'A senha é obrigatória para novos usuários.',
        'senha.min'             => 'A senha deve ter ao menos 8 caracteres.',
        'senhaConfirmacao.same' => 'As senhas não coincidem.',
        'perfil.required'       => 'Selecione um perfil.',
        'perfil.in'             => 'Perfil inválido.',
    ];

    #[Computed]
    public function usuarios(): \Illuminate\Database\Eloquent\Collection
    {
        return Usuario::where('tenant_id', auth('usuarios')->user()->tenant_id)
            ->when($this->busca, fn($q) => $q->where(function ($q) {
                $q->where('nome', 'ilike', "%{$this->busca}%")
                  ->orWhere('email', 'ilike', "%{$this->busca}%")
                  ->orWhere('login', 'ilike', "%{$this->busca}%");
            }))
            ->when($this->filtroPerfil, fn($q) => $q->where('perfil', $this->filtroPerfil))
            ->orderBy('nome')
            ->get();
    }

    #[Computed]
    public function tenant(): Tenant
    {
        return Tenant::findOrFail(auth('usuarios')->user()->tenant_id);
    }

    #[Computed]
    public function totalAtivos(): int
    {
        return Usuario::where('tenant_id', auth('usuarios')->user()->tenant_id)
            ->where('ativo', true)
            ->count();
    }

    #[Computed]
    public function limite(): int
    {
        return $this->tenant->limite_usuarios ?? 3;
    }

    #[Computed]
    public function atingiuLimite(): bool
    {
        return $this->limite > 0 && $this->totalAtivos >= $this->limite;
    }

    public function abrirModal(?int $id = null): void
    {
        $this->limpar();
        $this->usuarioId   = $id;
        $this->modalAberto = true;

        if ($id) {
            $u = Usuario::findOrFail($id);
            $this->nome     = $u->nome;
            $this->email    = $u->email ?? '';
            $this->login    = $u->login;
            $this->telefone = $u->telefone ?? '';
            $this->perfil   = in_array($u->perfil, array_keys($this->perfisDisponiveis))
                ? $u->perfil
                : 'advogado';
            $this->ativo    = $u->ativo;
        }
    }

    public function salvar(): void
    {
        $this->validate();

        if (!$this->usuarioId && $this->atingiuLimite) {
            $this->dispatch('toast', type: 'erro', msg: "Limite de {$this->limite} usuários atingido. Contate o suporte para ampliar.");
            $this->modalAberto = false;
            return;
        }

        $dados = [
            'nome'      => trim($this->nome),
            'email'     => trim($this->email),
            'login'     => trim($this->login),
            'telefone'  => $this->telefone ?: null,
            'perfil'    => $this->perfil,
            'ativo'     => $this->ativo,
            'tenant_id' => auth('usuarios')->user()->tenant_id,
        ];

        if ($this->senha) {
            $dados['password'] = Hash::make($this->senha);
        }

        if ($this->usuarioId) {
            Usuario::findOrFail($this->usuarioId)->update($dados);
            $this->dispatch('toast', type: 'sucesso', msg: 'Usuário atualizado!');
        } else {
            Usuario::create($dados);
            $this->dispatch('toast', type: 'sucesso', msg: 'Usuário criado com sucesso!');
        }

        $this->modalAberto = false;
        $this->limpar();
        unset($this->usuarios, $this->totalAtivos, $this->atingiuLimite);
    }

    public function toggleAtivo(int $id): void
    {
        if ($id === auth('usuarios')->id()) {
            $this->dispatch('toast', type: 'erro', msg: 'Você não pode desativar seu próprio usuário.');
            return;
        }

        $u = Usuario::findOrFail($id);
        $novoEstado = !$u->ativo;

        if ($novoEstado && $this->atingiuLimite) {
            $this->dispatch('toast', type: 'erro', msg: "Limite de {$this->limite} usuários atingido.");
            return;
        }

        $u->update(['ativo' => $novoEstado]);
        $this->dispatch('toast', type: 'sucesso', msg: $novoEstado ? 'Usuário ativado!' : 'Usuário desativado.');
        unset($this->usuarios, $this->totalAtivos, $this->atingiuLimite);
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->limpar();
    }

    public function sugerirLogin(): void
    {
        if (!$this->usuarioId && !$this->login && $this->nome) {
            $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $this->nome)));
            $this->login = substr($base, 0, 30);
        }
    }

    private function limpar(): void
    {
        $this->usuarioId        = null;
        $this->nome             = '';
        $this->email            = '';
        $this->login            = '';
        $this->telefone         = '';
        $this->perfil           = 'advogado';
        $this->senha            = '';
        $this->senhaConfirmacao = '';
        $this->ativo            = true;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.gestao-usuarios', [
            'usuarios'      => $this->usuarios,
            'atingiuLimite' => $this->atingiuLimite,
            'limite'        => $this->limite,
            'totalAtivos'   => $this->totalAtivos,
        ]);
    }
}