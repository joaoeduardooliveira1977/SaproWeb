<?php

namespace App\Livewire\MasterAdmin;

use App\Models\{Tenant, Usuario};
use Illuminate\Support\Facades\{Auth, Cache, DB, Hash};
use Livewire\Component;

class TenantShow extends Component
{
    public int    $tenantId;
    public Tenant $tenant;

    public array $metricas  = [];
    public array $usuarios  = [];
    public array $auditoria = [];

    // Reset senha
    public bool   $modalSenha  = false;
    public string $novaSenha   = '';
    public ?int   $usuarioSenhaId = null;

    public function mount(int $id): void
    {
        $this->tenantId = $id;
        $this->tenant   = Tenant::findOrFail($id);
        $this->carregarDados();
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
            ->get(['id', 'nome', 'email', 'perfil', 'ultimo_acesso', 'ativo'])
            ->toArray();

        $this->auditoria = DB::table('auditorias')
            ->join('usuarios', 'auditorias.usuario_id', '=', 'usuarios.id')
            ->where('usuarios.tenant_id', $tid)
            ->orderByDesc('auditorias.created_at')
            ->limit(10)
            ->get([
                'auditorias.acao',
                'auditorias.tabela',
                'auditorias.login',
                'auditorias.ip',
                'auditorias.created_at',
                'usuarios.nome as usuario_nome',
            ])
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

    public function toggleAtivo(): void
    {
        $this->tenant->update(['ativo' => !$this->tenant->ativo]);
        Cache::forget("tenant_{$this->tenant->id}");
        $this->tenant->refresh();
        $this->dispatch('toast', message: $this->tenant->ativo ? 'Tenant reativado.' : 'Tenant suspenso.', type: 'success');
    }

    public function loginComoTenant(): mixed
    {
        $usuario = Usuario::where('tenant_id', $this->tenantId)
            ->where('perfil', 'administrador')
            ->first();

        if (!$usuario) {
            $this->dispatch('toast', message: 'Nenhum administrador encontrado.', type: 'error');
            return null;
        }

        session(['super_admin_id' => auth('usuarios')->id()]);
        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('dashboard');
    }

    public function abrirModalSenha(int $usuarioId): void
    {
        $this->usuarioSenhaId = $usuarioId;
        $this->novaSenha      = '';
        $this->modalSenha     = true;
    }

    public function resetarSenha(): void
    {
        $this->validate(['novaSenha' => 'required|min:8'], ['novaSenha.required' => 'Informe a nova senha.', 'novaSenha.min' => 'Mínimo 8 caracteres.']);

        $usuario = Usuario::where('tenant_id', $this->tenantId)->findOrFail($this->usuarioSenhaId);
        $usuario->update(['password' => Hash::make($this->novaSenha)]);

        $this->modalSenha = false;
        $this->novaSenha  = '';
        $this->dispatch('toast', message: 'Senha redefinida com sucesso.', type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.master-admin.tenant-show')
            ->extends('layouts.master-admin')
            ->section('content');
    }
}
