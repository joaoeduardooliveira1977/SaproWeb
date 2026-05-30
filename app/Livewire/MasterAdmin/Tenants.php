<?php

namespace App\Livewire\MasterAdmin;

use App\Models\{Tenant, Usuario};
use Illuminate\Support\Facades\{Auth, Cache, DB};
use Livewire\Component;
use Livewire\WithPagination;

class Tenants extends Component
{
    use WithPagination;

    public string $busca       = '';
    public string $filtroAtivo = '';
    public string $filtroPlano = '';

    public function updatingBusca(): void      { $this->resetPage(); }
    public function updatingFiltroAtivo(): void { $this->resetPage(); }
    public function updatingFiltroPlano(): void { $this->resetPage(); }

    public function toggleAtivo(int $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['ativo' => !$tenant->ativo]);
        Cache::forget("tenant_{$id}");
        Cache::forget("tenant_host_{$tenant->dominio}");
        $this->dispatch('toast', message: $tenant->fresh()->ativo ? 'Tenant ativado.' : 'Tenant suspenso.', type: 'success');
    }

    public function loginComoTenant(int $id): mixed
    {
        $tenant  = Tenant::findOrFail($id);
        $usuario = Usuario::where('tenant_id', $id)->where('perfil', 'administrador')->first();

        if (!$usuario) {
            $this->dispatch('toast', message: 'Nenhum administrador encontrado neste tenant.', type: 'error');
            return null;
        }

        session(['super_admin_id' => auth('usuarios')->id()]);
        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('dashboard');
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
            ->when($this->filtroPlano,       fn($q) => $q->where('plano', $this->filtroPlano))
            ->withCount(['processos', 'usuarios'])
            ->addSelect([
                'pessoas_count'  => DB::table('pessoas')->selectRaw('count(*)')->whereColumn('tenant_id', 'tenants.id'),
                'ultimo_acesso'  => DB::table('usuarios')->selectRaw('max(ultimo_acesso)')->whereColumn('tenant_id', 'tenants.id'),
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.master-admin.tenants', compact('tenants'))
            ->extends('layouts.master-admin')
            ->section('content');
    }
}
