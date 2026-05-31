<?php

namespace App\Livewire\MasterAdmin;

use App\Models\{MasterAdminLog, Tenant, Usuario};
use Illuminate\Support\Facades\{Auth, Cache, DB, Hash};
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Tenants extends Component
{
    use WithPagination;

    // Filtros
    public string $busca       = '';
    public string $filtroAtivo = '';
    public string $filtroPlano = '';

    // Modal: Novo Tenant
    public bool   $modalNovo         = false;
    public string $novoNome          = '';
    public string $novoSlug          = '';
    public string $novoDominio       = '';
    public string $novoPlano         = 'demo';
    public string $novoAdminNome     = '';
    public string $novoAdminEmail    = '';
    public string $novoAdminSenha    = '';
    public string $novoCorPrimaria   = '#1a3a5c';
    public string $novoCorSecundaria = '#c9a84c';

    // Modal: Excluir para lixeira
    public bool   $modalExcluir   = false;
    public ?int   $excluindoId    = null;
    public string $excluindoNome  = '';
    public string $motivoExclusao = '';

    public function updatingBusca(): void       { $this->resetPage(); }
    public function updatingFiltroAtivo(): void  { $this->resetPage(); }
    public function updatingFiltroPlano(): void  { $this->resetPage(); }

    // ── Slug auto-gerado ──────────────────────────────────────────

    public function updatedNovoNome(string $value): void
    {
        $this->novoSlug    = Str::slug($value);
        $this->novoDominio = $this->novoSlug . '.kmd-ia.com.br';
    }

    public function updatedNovoSlug(string $value): void
    {
        $this->novoDominio = Str::slug($value) . '.kmd-ia.com.br';
    }

    // ── Modal Novo Tenant ─────────────────────────────────────────

    public function abrirModalNovo(): void
    {
        $this->resetForm();
        $this->modalNovo = true;
    }

    public function fecharModalNovo(): void
    {
        $this->modalNovo = false;
        $this->resetForm();
    }

    public function criarTenant(): void
    {
        $this->validate([
            'novoNome'       => 'required|min:3|max:100',
            'novoSlug'       => 'required|min:2|max:50|unique:tenants,slug|regex:/^[a-z0-9-]+$/',
            'novoDominio'    => 'required|max:150|unique:tenants,dominio',
            'novoPlano'      => 'required|in:demo,starter,pro,enterprise',
            'novoAdminNome'  => 'required|min:3|max:100',
            'novoAdminEmail' => 'required|email|max:150|unique:usuarios,email',
            'novoAdminSenha' => 'required|min:8',
        ], [
            'novoNome.required'       => 'Informe o nome do escritório.',
            'novoSlug.unique'         => 'Este slug já está em uso.',
            'novoSlug.regex'          => 'Apenas letras minúsculas, números e hífens.',
            'novoDominio.unique'      => 'Este domínio já está em uso.',
            'novoAdminEmail.unique'   => 'Este e-mail já está cadastrado.',
            'novoAdminSenha.min'      => 'Senha: mínimo 8 caracteres.',
        ]);

        $limites = Tenant::limitesPlano($this->novoPlano);

        $tenant = Tenant::create([
            'nome'               => trim($this->novoNome),
            'slug'               => $this->novoSlug,
            'dominio'            => $this->novoDominio,
            'plano'              => $this->novoPlano,
            'ativo'              => true,
            'trial_expira_em'    => $this->novoPlano === 'demo' ? now()->addDays(30) : null,
            'limite_processos'   => $limites['processos'],
            'limite_usuarios'    => $limites['usuarios'],
            'ia_habilitada'      => $limites['ia'],
            'datajud_habilitado' => $limites['datajud'],
            'whatsapp_habilitado'=> $limites['whatsapp'],
            'cor_primaria'       => $this->novoCorPrimaria,
            'cor_secundaria'     => $this->novoCorSecundaria,
        ]);

        Usuario::create([
            'tenant_id' => $tenant->id,
            'nome'      => trim($this->novoAdminNome),
            'login'     => $this->novoSlug . '_admin',
            'email'     => $this->novoAdminEmail,
            'password'  => Hash::make($this->novoAdminSenha),
            'perfil'    => 'administrador',
            'ativo'     => true,
        ]);

        MasterAdminLog::registrar(
            'tenant_criado',
            $tenant->id,
            $tenant->nome,
            "Tenant criado. Plano: {$this->novoPlano}. Admin: {$this->novoAdminEmail}"
        );

        $this->fecharModalNovo();
        $this->dispatch('toast', message: "Tenant \"{$tenant->nome}\" criado com sucesso.", type: 'success');
    }

    // ── Suspender / Ativar ────────────────────────────────────────

    public function toggleAtivo(int $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['ativo' => !$tenant->ativo]);
        Cache::forget("tenant_{$id}");
        Cache::forget("tenant_host_{$tenant->dominio}");

        $novoStatus = $tenant->fresh()->ativo ? 'ativado' : 'suspenso';
        MasterAdminLog::registrar(
            "tenant_{$novoStatus}",
            $tenant->id,
            $tenant->nome,
            "Tenant {$novoStatus} via listagem."
        );

        $this->dispatch('toast', message: "Tenant {$novoStatus}.", type: 'success');
    }

    // ── Soft Delete (lixeira) ─────────────────────────────────────

    public function abrirModalExcluir(int $id): void
    {
        $tenant              = Tenant::findOrFail($id);
        $this->excluindoId   = $id;
        $this->excluindoNome = $tenant->nome;
        $this->motivoExclusao = '';
        $this->modalExcluir  = true;
    }

    public function fecharModalExcluir(): void
    {
        $this->modalExcluir   = false;
        $this->excluindoId    = null;
        $this->excluindoNome  = '';
        $this->motivoExclusao = '';
    }

    public function excluirParaLixeira(): void
    {
        $this->validate([
            'motivoExclusao' => 'required|min:10',
        ], [
            'motivoExclusao.required' => 'Informe o motivo da exclusão.',
            'motivoExclusao.min'      => 'O motivo deve ter pelo menos 10 caracteres.',
        ]);

        $tenant = Tenant::findOrFail($this->excluindoId);

        $tenant->update([
            'deleted_by'    => auth('usuarios')->id(),
            'delete_reason' => trim($this->motivoExclusao),
            'ativo'         => false,
        ]);

        $tenant->delete(); // SoftDelete

        Cache::forget("tenant_{$tenant->id}");
        Cache::forget("tenant_host_{$tenant->dominio}");

        MasterAdminLog::registrar(
            'tenant_excluido_lixeira',
            $tenant->id,
            $tenant->nome,
            "Movido para lixeira. Motivo: {$this->motivoExclusao}"
        );

        $this->fecharModalExcluir();
        $this->dispatch('toast', message: "Tenant movido para a lixeira.", type: 'warning');
    }

    // ── Impersonation ─────────────────────────────────────────────

    public function loginComoTenant(int $id): mixed
    {
        $tenant  = Tenant::findOrFail($id);
        $usuario = Usuario::where('tenant_id', $id)->where('perfil', 'administrador')->first();

        if (!$usuario) {
            $this->dispatch('toast', message: 'Nenhum administrador encontrado neste tenant.', type: 'error');
            return null;
        }

        MasterAdminLog::registrar(
            'login_como_tenant',
            $tenant->id,
            $tenant->nome,
            "Entrou como: {$usuario->nome} ({$usuario->email})"
        );

        session(['master_admin_id' => auth('usuarios')->id()]);
        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('dashboard');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->novoNome          = '';
        $this->novoSlug          = '';
        $this->novoDominio       = '';
        $this->novoPlano         = 'demo';
        $this->novoAdminNome     = '';
        $this->novoAdminEmail    = '';
        $this->novoAdminSenha    = '';
        $this->novoCorPrimaria   = '#1a3a5c';
        $this->novoCorSecundaria = '#c9a84c';
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $tenants = Tenant::query()
            ->when($this->busca, fn($q) => $q->where(function ($q) {
                $q->where('nome', 'ilike', "%{$this->busca}%")
                  ->orWhere('slug', 'ilike', "%{$this->busca}%")
                  ->orWhere('email', 'ilike', "%{$this->busca}%");
            }))
            ->when($this->filtroAtivo !== '', fn($q) => $q->where('ativo', (bool) $this->filtroAtivo))
            ->when($this->filtroPlano, fn($q) => $q->where('plano', $this->filtroPlano))
            ->withCount(['processos', 'usuarios'])
            ->addSelect([
                'pessoas_count' => DB::table('pessoas')->selectRaw('count(*)')->whereColumn('tenant_id', 'tenants.id'),
                'ultimo_acesso' => DB::table('usuarios')->selectRaw('max(ultimo_acesso)')->whereColumn('tenant_id', 'tenants.id'),
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.master-admin.tenants', compact('tenants'))
            ->extends('layouts.master')
            ->section('content');
    }
}
