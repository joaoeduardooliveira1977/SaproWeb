<?php

namespace App\Livewire\Configuracoes;

use App\Models\{MasterAdminLog, Tenant, Usuario};
use Illuminate\Support\Facades\{DB, Hash, Storage};
use Livewire\Component;
use Livewire\WithFileUploads;

class EscritorioConfig extends Component
{
    use WithFileUploads;

    public string $abaAtiva = 'identidade';

    // ── Aba 1: Identidade Visual ──────────────────────────────────
    public string  $corPrimaria   = '#1a3a5c';
    public string  $corSecundaria = '#c9a84c';
    public ?string $logoAtual     = null;
    public         $logoUpload    = null;

    // ── Aba 2: Dados do Escritório ────────────────────────────────
    public string $nome      = '';
    public string $cnpj      = '';
    public string $telefone  = '';
    public string $emailContato = '';
    public string $endereco  = '';
    public string $cidade    = '';
    public string $oab       = '';
    public string $site      = '';

    // ── Aba 3: Domínio ────────────────────────────────────────────
    public string $dominioSolicitado = '';
    public bool   $dominioEnviado    = false;

    // ── Aba 4: Usuários ───────────────────────────────────────────
    public bool   $modalUsuario = false;
    public ?int   $usuarioEditId = null;
    public string $uNome        = '';
    public string $uEmail       = '';
    public string $uLogin       = '';
    public string $uPerfil      = 'advogado';
    public string $uSenha       = '';
    public bool   $uAtivo       = true;

    public ?int   $confirmarExcluirUsuario = null;

    public array $perfisDisponiveis = [
        'administrador' => 'Administrador — acesso total',
        'advogado'      => 'Advogado — processos e clientes',
        'financeiro'    => 'Financeiro — módulo financeiro',
        'estagiario'    => 'Estagiário — acesso limitado',
        'recepcionista' => 'Recepcionista — acesso básico',
    ];

    // ── Aba 5: Segurança ──────────────────────────────────────────
    public string $senhaAtual   = '';
    public string $senhaNova    = '';
    public string $senhaConfirm = '';

    // ── Mount ─────────────────────────────────────────────────────

    public function mount(): void
    {
        $tenant = $this->tenant();

        $this->corPrimaria   = $tenant->cor_primaria   ?? '#1a3a5c';
        $this->corSecundaria = $tenant->cor_secundaria ?? '#c9a84c';
        $this->logoAtual     = $tenant->logo;

        $this->nome         = $tenant->nome     ?? '';
        $this->cnpj         = $tenant->cnpj     ?? '';
        $this->telefone     = $tenant->telefone ?? '';
        $this->emailContato = $tenant->email    ?? '';
        $this->endereco     = $tenant->endereco ?? '';
        $this->cidade       = $tenant->cidade   ?? '';
        $this->oab          = $tenant->oab      ?? '';
        $this->site         = $tenant->configuracoes['site'] ?? '';
    }

    public function mudarAba(string $aba): void
    {
        $this->abaAtiva = $aba;
        $this->resetValidation();
    }

    // ── Aba 1: Identidade Visual ──────────────────────────────────

    public function salvarIdentidade(): void
    {
        $this->validate([
            'corPrimaria'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'corSecundaria' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logoUpload'    => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ], [
            'corPrimaria.regex'   => 'Cor primária deve ser um valor hex válido.',
            'corSecundaria.regex' => 'Cor secundária deve ser um valor hex válido.',
            'logoUpload.image'    => 'O logo deve ser uma imagem.',
            'logoUpload.max'      => 'O logo não pode ultrapassar 2 MB.',
        ]);

        $tenant = $this->tenant();
        $dados  = [
            'cor_primaria'   => $this->corPrimaria,
            'cor_secundaria' => $this->corSecundaria,
        ];

        if ($this->logoUpload) {
            if ($this->logoAtual) {
                Storage::disk('public')->delete($this->logoAtual);
            }
            $ext    = $this->logoUpload->getClientOriginalExtension();
            $caminho = $this->logoUpload->storeAs(
                "tenants/{$tenant->slug}",
                'logo.' . $ext,
                'public'
            );
            $dados['logo']   = $caminho;
            $this->logoAtual = $caminho;
            $this->logoUpload = null;
        }

        $tenant->update($dados);
        \Illuminate\Support\Facades\Cache::forget("tenant_{$tenant->id}");
        \App\Services\OnboardingService::marcar($tenant->id, 'configurar_escritorio');
        $this->dispatch('toast', message: 'Identidade visual atualizada!', type: 'success');
    }

    public function removerLogo(): void
    {
        $tenant = $this->tenant();
        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
            $tenant->update(['logo' => null]);
            $this->logoAtual = null;
        }
        $this->dispatch('toast', message: 'Logo removido.', type: 'success');
    }

    // ── Aba 2: Dados do Escritório ────────────────────────────────

    public function salvarDados(): void
    {
        $this->validate([
            'nome'         => 'required|string|min:3|max:150',
            'cnpj'         => 'nullable|string|max:18',
            'telefone'     => 'nullable|string|max:20',
            'emailContato' => 'nullable|email|max:150',
            'endereco'     => 'nullable|string|max:255',
            'cidade'       => 'nullable|string|max:100',
            'oab'          => 'nullable|string|max:30',
            'site'         => 'nullable|url|max:200',
        ], [
            'nome.required' => 'O nome do escritório é obrigatório.',
            'site.url'      => 'Informe uma URL válida (ex: https://escritorio.com.br).',
        ]);

        $tenant = $this->tenant();
        $cfg    = $tenant->configuracoes ?? [];
        $cfg['site'] = $this->site ?: null;

        $tenant->update([
            'nome'         => trim($this->nome),
            'cnpj'         => $this->cnpj         ?: null,
            'telefone'     => $this->telefone      ?: null,
            'email'        => $this->emailContato  ?: null,
            'endereco'     => $this->endereco      ?: null,
            'cidade'       => $this->cidade        ?: null,
            'oab'          => $this->oab           ?: null,
            'configuracoes'=> $cfg,
        ]);

        \Illuminate\Support\Facades\Cache::forget("tenant_{$tenant->id}");
        $this->dispatch('toast', message: 'Dados do escritório salvos!', type: 'success');
    }

    // ── Aba 3: Domínio ────────────────────────────────────────────

    public function solicitarDominio(): void
    {
        $this->validate([
            'dominioSolicitado' => ['required', 'regex:/^[a-z0-9.-]+\.[a-z]{2,}$/i'],
        ], [
            'dominioSolicitado.required' => 'Informe o domínio desejado.',
            'dominioSolicitado.regex'    => 'Informe um domínio válido (ex: escritorio.com.br).',
        ]);

        $tenant = $this->tenant();

        MasterAdminLog::registrar(
            'dominio_solicitado',
            $tenant->id,
            $tenant->nome,
            "Domínio solicitado: {$this->dominioSolicitado} — Tenant: {$tenant->nome} (#{$tenant->id})"
        );

        $this->dominioEnviado    = true;
        $this->dominioSolicitado = '';
        $this->dispatch('toast', message: 'Solicitação enviada! Nossa equipe entrará em contato.', type: 'success');
    }

    // ── Aba 4: Usuários ───────────────────────────────────────────

    public function novoUsuario(): void
    {
        $this->resetUsuarioForm();
        $this->modalUsuario = true;
    }

    public function editarUsuario(int $id): void
    {
        $u = Usuario::findOrFail($id);
        $this->usuarioEditId = $u->id;
        $this->uNome    = $u->nome;
        $this->uEmail   = $u->email ?? '';
        $this->uLogin   = $u->login;
        $this->uPerfil  = $u->perfil;
        $this->uAtivo   = $u->ativo;
        $this->uSenha   = '';
        $this->modalUsuario = true;
        $this->resetValidation();
    }

    public function salvarUsuario(): void
    {
        $id     = $this->usuarioEditId;
        $tenant = $this->tenant();

        $senhaRule = $id
            ? ($this->uSenha ? 'nullable|min:8|regex:/[A-Z]/|regex:/[0-9]/' : 'nullable')
            : 'required|min:8|regex:/[A-Z]/|regex:/[0-9]/';

        $rules = [
            'uNome'   => 'required|string|min:3|max:150',
            'uEmail'  => 'required|email|max:150' . ($id ? "|unique:usuarios,email,{$id}" : '|unique:usuarios,email'),
            'uLogin'  => 'required|min:3|max:60' . ($id ? "|unique:usuarios,login,{$id}" : '|unique:usuarios,login'),
            'uPerfil' => 'required|in:' . implode(',', array_keys($this->perfisDisponiveis)),
            'uSenha'  => $senhaRule,
        ];

        $this->validate($rules, [
            'uNome.required'  => 'Informe o nome.',
            'uEmail.unique'   => 'Este e-mail já está em uso.',
            'uLogin.unique'   => 'Este login já está em uso.',
            'uSenha.required' => 'A senha é obrigatória para novos usuários.',
            'uSenha.min'      => 'A senha deve ter ao menos 8 caracteres.',
            'uSenha.regex'    => 'A senha deve conter ao menos uma letra maiúscula e um número.',
        ]);

        if (!$id) {
            // Verifica limite
            if ($tenant->atingiuLimiteUsuarios()) {
                $this->dispatch('toast', message: "Limite de usuários atingido para o plano {$tenant->nomePlano()}.", type: 'error');
                return;
            }

            Usuario::create([
                'tenant_id' => $tenant->id,
                'nome'      => trim($this->uNome),
                'email'     => trim($this->uEmail),
                'login'     => trim($this->uLogin),
                'password'  => Hash::make($this->uSenha),
                'perfil'    => $this->uPerfil,
                'ativo'     => $this->uAtivo,
            ]);
        } else {
            $dados = [
                'nome'   => trim($this->uNome),
                'email'  => trim($this->uEmail),
                'login'  => trim($this->uLogin),
                'perfil' => $this->uPerfil,
                'ativo'  => $this->uAtivo,
            ];
            if ($this->uSenha) {
                $dados['password'] = Hash::make($this->uSenha);
            }
            Usuario::findOrFail($id)->update($dados);
        }

        $this->modalUsuario = false;
        $this->resetUsuarioForm();
        $this->dispatch('toast', message: $id ? 'Usuário atualizado.' : 'Usuário criado com sucesso.', type: 'success');
    }

    public function toggleAtivoUsuario(int $id): void
    {
        $u = Usuario::findOrFail($id);
        $u->update(['ativo' => !$u->ativo]);
        $this->dispatch('toast', message: $u->fresh()->ativo ? 'Usuário ativado.' : 'Usuário desativado.', type: 'success');
    }

    public function resetarSenhaUsuario(int $id, string $novaSenha): void
    {
        Usuario::findOrFail($id)->update(['password' => Hash::make($novaSenha)]);
        $this->dispatch('toast', message: 'Senha redefinida com sucesso.', type: 'success');
    }

    public function fecharModalUsuario(): void
    {
        $this->modalUsuario = false;
        $this->resetUsuarioForm();
    }

    private function resetUsuarioForm(): void
    {
        $this->usuarioEditId = null;
        $this->uNome   = '';
        $this->uEmail  = '';
        $this->uLogin  = '';
        $this->uPerfil = 'advogado';
        $this->uSenha  = '';
        $this->uAtivo  = true;
        $this->resetValidation();
    }

    // ── Aba 5: Segurança ──────────────────────────────────────────

    public function alterarSenha(): void
    {
        $this->validate([
            'senhaAtual'   => 'required',
            'senhaNova'    => 'required|min:8',
            'senhaConfirm' => 'required|same:senhaNova',
        ], [
            'senhaAtual.required'   => 'Informe a senha atual.',
            'senhaNova.required'    => 'Informe a nova senha.',
            'senhaNova.min'         => 'A nova senha deve ter ao menos 8 caracteres.',
            'senhaConfirm.same'     => 'As senhas não coincidem.',
        ]);

        $usuario = auth('usuarios')->user();

        if (!Hash::check($this->senhaAtual, $usuario->password)) {
            $this->addError('senhaAtual', 'Senha atual incorreta.');
            return;
        }

        $usuario->update(['password' => Hash::make($this->senhaNova)]);

        $this->senhaAtual   = '';
        $this->senhaNova    = '';
        $this->senhaConfirm = '';

        $this->dispatch('toast', message: 'Senha alterada com sucesso!', type: 'success');
    }

    public function forcarLogoutTodos(): void
    {
        $tenant  = $this->tenant();
        $usuario = auth('usuarios')->user();

        // Busca IDs de todos os usuários do tenant (exceto o próprio admin)
        $ids = Usuario::where('tenant_id', $tenant->id)
            ->where('id', '!=', $usuario->id)
            ->pluck('id');

        // Remove sessões do banco para esses usuários
        DB::table('sessions')->whereIn('user_id', $ids)->delete();

        $this->dispatch('toast', message: "Sessões encerradas para {$ids->count()} usuário(s).", type: 'success');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function tenant(): Tenant
    {
        $tenantId = auth('usuarios')->user()->tenant_id;

        if (!$tenantId) {
            $t = app()->bound('tenant') ? app('tenant') : null;
            abort_unless($t, 403, 'Nenhum tenant selecionado.');
            return $t;
        }

        return Tenant::findOrFail($tenantId);
    }

    // ── Render ────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $tenant   = $this->tenant();
        $usuarios = Usuario::where('tenant_id', $tenant->id)->orderBy('nome')->get();

        // Histórico de acessos do usuário logado (últimos 10 logins)
        $historico = DB::table('auditorias')
            ->where('usuario_id', auth('usuarios')->id())
            ->where('acao', 'Login')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['ip', 'created_at']);

        return view('livewire.configuracoes.escritorio-config', compact('tenant', 'usuarios', 'historico'))
            ->extends('layouts.app')
            ->section('content');
    }
}
