<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TenantBranding extends Component
{
    use WithFileUploads;

    // ── Listagem ────────────────────────────────────────────────
    public string $busca       = '';
    public string $filtroAtivo = '';

    // ── Modal de edição ──────────────────────────────────────────
    public bool  $modalAberto = false;
    public ?int  $tenantId    = null;

    public string $nome          = '';
    public string $slug          = '';
    public string $dominio       = '';
    public string $corPrimaria   = '#1a3a5c';
    public string $corSecundaria = '#c9a84c';
    public bool   $ativo         = true;

    public $novoLogo = null;

    // ── Abrir modal ─────────────────────────────────────────────

    public function editar(int $id): void
    {
        $tenant = Tenant::findOrFail($id);

        $this->tenantId     = $tenant->id;
        $this->nome         = $tenant->nome;
        $this->slug         = $tenant->slug;
        $this->dominio      = $tenant->dominio ?? '';
        $this->corPrimaria  = $tenant->cor_primaria  ?? '#1a3a5c';
        $this->corSecundaria= $tenant->cor_secundaria ?? '#c9a84c';
        $this->ativo        = (bool) $tenant->ativo;
        $this->novoLogo     = null;

        $this->resetValidation();
        $this->modalAberto = true;
    }

    public function fechar(): void
    {
        $this->modalAberto = false;
        $this->tenantId    = null;
        $this->novoLogo    = null;
    }

    // ── Salvar branding ─────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'nome'         => 'required|min:3|max:150',
            'slug'         => 'required|max:100',
            'dominio'      => 'nullable|max:253',
            'corPrimaria'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'corSecundaria'=> ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'novoLogo'     => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ], [
            'corPrimaria.regex'  => 'Cor primária deve ser um hex válido (ex: #1a3a5c).',
            'corSecundaria.regex'=> 'Cor secundária deve ser um hex válido (ex: #c9a84c).',
            'novoLogo.image'     => 'O logo deve ser uma imagem.',
            'novoLogo.max'       => 'O logo deve ter no máximo 2 MB.',
        ]);

        $tenant = Tenant::findOrFail($this->tenantId);

        $dados = [
            'nome'          => trim($this->nome),
            'dominio'       => trim($this->dominio) ?: null,
            'cor_primaria'  => $this->corPrimaria,
            'cor_secundaria'=> $this->corSecundaria,
            'ativo'         => $this->ativo,
        ];

        // Upload do logo
        if ($this->novoLogo) {
            // Remove logo antigo
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }

            $caminho = $this->novoLogo->storeAs(
                "tenants/{$tenant->slug}",
                'logo.' . $this->novoLogo->getClientOriginalExtension(),
                'public'
            );

            $dados['logo'] = $caminho;
        }

        $tenant->update($dados);

        // Invalida o cache do tenant para que o middleware recarregue
        Cache::forget("tenant_{$tenant->id}");
        Cache::forget("tenant_host_{$tenant->dominio}");
        if ($this->dominio) {
            Cache::forget("tenant_host_{$this->dominio}");
        }

        $this->fechar();
        $this->dispatch('toast', message: 'Branding atualizado com sucesso.', type: 'success');
    }

    // ── Toggle ativo/inativo ────────────────────────────────────

    public function toggleAtivo(int $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['ativo' => !$tenant->ativo]);

        Cache::forget("tenant_{$tenant->id}");

        $status = $tenant->fresh()->ativo ? 'ativado' : 'suspenso';
        $this->dispatch('toast', message: "Tenant {$status} com sucesso.", type: 'success');
    }

    // ── Query ────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $tenants = Tenant::query()
            ->when($this->busca, fn($q) => $q->where(function ($q) {
                $q->where('nome', 'ilike', "%{$this->busca}%")
                  ->orWhere('slug', 'ilike', "%{$this->busca}%")
                  ->orWhere('dominio', 'ilike', "%{$this->busca}%");
            }))
            ->when($this->filtroAtivo !== '', fn($q) => $q->where('ativo', (bool) $this->filtroAtivo))
            ->orderBy('nome')
            ->get();

        return view('livewire.admin.tenant-branding', compact('tenants'))
            ->extends('layouts.app')
            ->section('content');
    }
}
