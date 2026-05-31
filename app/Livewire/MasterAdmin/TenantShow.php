<?php

namespace App\Livewire\MasterAdmin;

use App\Models\{MasterAdminLog, Tenant, Usuario};
use Illuminate\Support\Facades\{Auth, Cache, DB, Hash};
use Livewire\Component;

class TenantShow extends Component
{
    public int    $tenantId;
    public Tenant $tenant;
    public string $aba = 'geral';

    public array $metricas  = [];
    public array $usuarios  = [];
    public array $auditoria = [];

    // Modal: reset senha
    public bool   $modalSenha     = false;
    public string $novaSenha      = '';
    public ?int   $usuarioSenhaId = null;

    // Modal: excluir para lixeira
    public bool   $modalExcluir   = false;
    public string $motivoExclusao = '';

    public function mount(int $id): void
    {
        $this->tenantId = $id;
        $this->tenant   = Tenant::findOrFail($id);
        $this->carregarDados();
    }

    public function mudarAba(string $aba): void
    {
        $this->aba = $aba;
    }

    private function carregarDados(): void
    {
        $tid = $this->tenantId;

        $this->metricas = [
            'processos'   => DB::table('processos')->where('tenant_id', $tid)->whereNull('deleted_at')->count(),
            'pessoas'     => DB::table('pessoas')->where('tenant_id', $tid)->count(),
            'usuarios'    => DB::table('usuarios')->where('tenant_id', $tid)->count(),
            'prazos'      => DB::table('prazos')->where('tenant_id', $tid)->count(),
            'documentos'  => DB::table('documentos')->where('tenant_id', $tid)->count(),
            'publicacoes' => DB::table('aasp_publicacoes')->where('tenant_id', $tid)->count(),
            'disco_mb'    => $this->discoTenant($this->tenant->slug),
        ];

        $this->usuarios = DB::table('usuarios')
            ->where('tenant_id', $tid)
            ->orderByDesc('ultimo_acesso')
            ->get(['id', 'nome', 'email', 'login', 'perfil', 'ultimo_acesso', 'ativo'])
            ->toArray();

        $this->auditoria = DB::table('auditorias')
            ->join('usuarios', 'auditorias.usuario_id', '=', 'usuarios.id')
            ->where('usuarios.tenant_id', $tid)
            ->orderByDesc('auditorias.created_at')
            ->limit(20)
            ->get(['auditorias.acao', 'auditorias.tabela', 'auditorias.login', 'auditorias.ip', 'auditorias.created_at', 'usuarios.nome as usuario_nome'])
            ->toArray();
    }

    private function discoTenant(string $slug): float
    {
        $path = storage_path("app/public/tenants/{$slug}");
        if (!is_dir($path)) return 0;
        $bytes = 0;
        try {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if ($file->isFile()) $bytes += $file->getSize();
            }
        } catch (\Exception) {}
        return round($bytes / 1024 / 1024, 2);
    }

    // ── Suspender / Ativar ────────────────────────────────────────

    public function toggleAtivo(): void
    {
        $this->tenant->update(['ativo' => !$this->tenant->ativo]);
        Cache::forget("tenant_{$this->tenant->id}");
        $this->tenant->refresh();

        $novoStatus = $this->tenant->ativo ? 'ativado' : 'suspenso';
        MasterAdminLog::registrar("tenant_{$novoStatus}", $this->tenant->id, $this->tenant->nome, "Via detalhes.");
        $this->dispatch('toast', message: "Tenant {$novoStatus}.", type: 'success');
    }

    // ── Excluir para lixeira ───────────────────────────────────────

    public function abrirModalExcluir(): void
    {
        $this->motivoExclusao = '';
        $this->modalExcluir   = true;
    }

    public function fecharModalExcluir(): void
    {
        $this->modalExcluir   = false;
        $this->motivoExclusao = '';
    }

    public function excluirParaLixeira(): mixed
    {
        $this->validate(
            ['motivoExclusao' => 'required|min:10'],
            ['motivoExclusao.required' => 'Informe o motivo.', 'motivoExclusao.min' => 'Mínimo 10 caracteres.']
        );

        $this->tenant->update([
            'deleted_by'    => auth('usuarios')->id(),
            'delete_reason' => trim($this->motivoExclusao),
            'ativo'         => false,
        ]);
        $this->tenant->delete();

        Cache::forget("tenant_{$this->tenant->id}");
        Cache::forget("tenant_host_{$this->tenant->dominio}");

        MasterAdminLog::registrar(
            'tenant_excluido_lixeira',
            $this->tenant->id,
            $this->tenant->nome,
            "Movido para lixeira. Motivo: {$this->motivoExclusao}"
        );

        return redirect()->route('master.lixeira');
    }

    // ── Impersonation ─────────────────────────────────────────────

    public function loginComoTenant(): mixed
    {
        $usuario = Usuario::where('tenant_id', $this->tenantId)->where('perfil', 'administrador')->first();

        if (!$usuario) {
            $this->dispatch('toast', message: 'Nenhum administrador encontrado.', type: 'error');
            return null;
        }

        MasterAdminLog::registrar('login_como_tenant', $this->tenant->id, $this->tenant->nome, "Entrou como: {$usuario->nome}");
        session(['master_admin_id' => auth('usuarios')->id()]);
        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('dashboard');
    }

    // ── Reset senha ───────────────────────────────────────────────

    public function abrirModalSenha(int $usuarioId): void
    {
        $this->usuarioSenhaId = $usuarioId;
        $this->novaSenha      = '';
        $this->modalSenha     = true;
    }

    public function resetarSenha(): void
    {
        $this->validate(
            ['novaSenha' => 'required|min:8'],
            ['novaSenha.required' => 'Informe a nova senha.', 'novaSenha.min' => 'Mínimo 8 caracteres.']
        );

        $usuario = Usuario::where('tenant_id', $this->tenantId)->findOrFail($this->usuarioSenhaId);
        $usuario->update(['password' => Hash::make($this->novaSenha)]);

        MasterAdminLog::registrar('senha_resetada', $this->tenant->id, $this->tenant->nome, "Senha redefinida para: {$usuario->nome}");

        $this->modalSenha = false;
        $this->novaSenha  = '';
        $this->dispatch('toast', message: 'Senha redefinida com sucesso.', type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.master-admin.tenant-show')
            ->extends('layouts.master')
            ->section('content');
    }
}
