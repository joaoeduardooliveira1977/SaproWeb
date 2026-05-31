<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Usuarios extends Component
{
    public bool $modal = false;
    public ?int $usuarioId = null;

    public string $nome = '';
    public string $login = '';
    public string $email = '';
    public string $senha = '';
    public string $senha_confirmacao = '';
    public string $perfil = 'estagiario';
    public string $telefone = '';
    public bool $ativo = true;

    public string $busca = '';
    public string $filtroPerfil = '';

    protected function rules(): array
    {
        return [
            'nome'   => 'required|min:3',
            'login'  => 'required|min:3',
            'email'  => 'nullable|email',
            'perfil' => 'required|in:admin,advogado,estagiario,financeiro,recepcionista',
            'senha'  => $this->usuarioId ? 'nullable|min:6' : 'required|min:6',
            'senha_confirmacao' => 'same:senha',
        ];
    }

    protected array $messages = [
        'nome.required'             => 'Informe o nome.',
        'nome.min'                  => 'Nome deve ter ao menos 3 caracteres.',
        'login.required'            => 'Informe o login.',
        'login.min'                 => 'Login deve ter ao menos 3 caracteres.',
        'senha.required'            => 'A senha é obrigatória para novos usuários.',
        'senha.min'                 => 'A senha deve ter ao menos 6 caracteres.',
        'senha_confirmacao.same'    => 'As senhas não coincidem.',
        'perfil.required'           => 'Selecione um perfil.',
        'perfil.in'                 => 'Perfil inválido.',
        'email.email'               => 'E-mail inválido.',
    ];

    private function tenantId(): int
    {
        return auth('usuarios')->user()->tenant_id;
    }

    public function novoUsuario(): void
    {
        $this->reset(['usuarioId','nome','login','email','senha','senha_confirmacao','telefone']);
        $this->perfil = 'estagiario';
        $this->ativo = true;
        $this->modal = true;
    }

    public function editarUsuario(int $id): void
    {
        $u = DB::selectOne(
            "SELECT * FROM usuarios WHERE id = ? AND tenant_id = ?",
            [$id, $this->tenantId()]
        );
        if (!$u) return;

        $this->usuarioId         = $u->id;
        $this->nome              = $u->nome ?? '';
        $this->login             = $u->login;
        $this->email             = $u->email ?? '';
        $this->perfil            = $u->perfil ?? 'estagiario';
        $this->telefone          = $u->telefone ?? '';
        $this->ativo             = (bool)($u->ativo ?? true);
        $this->senha             = '';
        $this->senha_confirmacao = '';
        $this->modal = true;
    }

    public function salvar(): void
    {
        $this->validate();

        $tenantId = $this->tenantId();

        // Verificar unicidade do login dentro do tenant
        $existe = DB::selectOne(
            "SELECT id FROM usuarios WHERE login = ? AND tenant_id = ? AND id != ?",
            [$this->login, $tenantId, $this->usuarioId ?? 0]
        );
        if ($existe) {
            $this->addError('login', 'Este login já está em uso.');
            return;
        }

        $now = now()->toDateTimeString();

        if ($this->usuarioId) {
            $sql = "UPDATE usuarios SET nome=?, login=?, email=?, perfil=?, telefone=?, ativo=?, updated_at=?";
            $params = [$this->nome, $this->login, $this->email ?: null, $this->perfil, $this->telefone ?: null, $this->ativo, $now];

            if ($this->senha) {
                $sql .= ", password=?";
                $params[] = Hash::make($this->senha);
            }

            $sql .= " WHERE id=? AND tenant_id=?";
            $params[] = $this->usuarioId;
            $params[] = $tenantId;
            DB::update($sql, $params);

        } else {
            DB::insert("
                INSERT INTO usuarios (tenant_id, nome, login, email, password, perfil, telefone, ativo, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $tenantId,
                $this->nome, $this->login, $this->email ?: null,
                Hash::make($this->senha), $this->perfil,
                $this->telefone ?: null, $this->ativo,
                $now, $now,
            ]);
        }

        $this->modal = false;
        $this->dispatch('toast', message: 'Usuário salvo com sucesso!', type: 'success');
    }

    public function toggleAtivo(int $id): void
    {
        if ($id === auth('usuarios')->id()) {
            $this->dispatch('toast', message: 'Você não pode desativar sua própria conta!', type: 'error');
            return;
        }
        DB::update(
            "UPDATE usuarios SET ativo = NOT ativo WHERE id = ? AND tenant_id = ?",
            [$id, $this->tenantId()]
        );
    }

    public function excluir(int $id): void
    {
        if ($id === auth('usuarios')->id()) {
            $this->dispatch('toast', message: 'Você não pode excluir sua própria conta!', type: 'error');
            return;
        }
        DB::delete("DELETE FROM usuarios WHERE id = ? AND tenant_id = ?", [$id, $this->tenantId()]);
        $this->dispatch('toast', message: 'Usuário excluído.', type: 'success');
    }

    public function render()
    {
        $tenantId = $this->tenantId();
        $where = "WHERE tenant_id = ?";
        $params = [$tenantId];

        if ($this->busca) {
            $where .= " AND (COALESCE(nome, login) ILIKE ? OR login ILIKE ?)";
            $params[] = "%{$this->busca}%";
            $params[] = "%{$this->busca}%";
        }
        if ($this->filtroPerfil) {
            $where .= " AND perfil = ?";
            $params[] = $this->filtroPerfil;
        }

        $usuarios = DB::select("
            SELECT *, COALESCE(nome, login) as nome FROM usuarios {$where} ORDER BY COALESCE(nome, login)
        ", $params);

        $totais = DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN ativo THEN 1 ELSE 0 END) as ativos,
                SUM(CASE WHEN perfil='admin' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN perfil='advogado' THEN 1 ELSE 0 END) as advogados
            FROM usuarios
            WHERE tenant_id = ?
        ", [$tenantId]);

        return view('livewire.usuarios', compact('usuarios', 'totais'));
    }
}
