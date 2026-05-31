<?php

namespace App\Livewire\MasterAdmin;

use App\Models\{MasterAdminLog, Tenant, Usuario};
use Illuminate\Support\Facades\{Cache, DB, Storage};
use Livewire\Component;

class Lixeira extends Component
{
    // Modal: Confirmar exclusão definitiva
    public bool   $modalDefinitivo    = false;
    public ?int   $excluindoId        = null;
    public string $excluindoNome      = '';
    public string $confirmacaoNome    = '';
    public array  $contagensExclusao  = [];

    // ── Restaurar ─────────────────────────────────────────────────

    public function restaurar(int $id): void
    {
        $tenant = Tenant::withTrashed()->findOrFail($id);
        $tenant->restore();
        $tenant->update([
            'ativo'         => true,
            'deleted_by'    => null,
            'delete_reason' => null,
        ]);

        Cache::forget("tenant_{$id}");
        Cache::forget("tenant_host_{$tenant->dominio}");

        MasterAdminLog::registrar(
            'tenant_restaurado',
            $tenant->id,
            $tenant->nome,
            "Tenant restaurado da lixeira."
        );

        $this->dispatch('toast', message: "Tenant \"{$tenant->nome}\" restaurado com sucesso.", type: 'success');
    }

    // ── Abrir modal de exclusão definitiva ─────────────────────────

    public function abrirModalDefinitivo(int $id): void
    {
        $tenant = Tenant::withTrashed()->findOrFail($id);

        $this->excluindoId     = $id;
        $this->excluindoNome   = $tenant->nome;
        $this->confirmacaoNome = '';

        $this->contagensExclusao = [
            'processos'  => DB::table('processos')->where('tenant_id', $id)->count(),
            'pessoas'    => DB::table('pessoas')->where('tenant_id', $id)->count(),
            'usuarios'   => DB::table('usuarios')->where('tenant_id', $id)->count(),
            'documentos' => DB::table('documentos')->where('tenant_id', $id)->count(),
            'financeiro' => DB::table('financeiro_lancamentos')->where('tenant_id', $id)->count(),
        ];

        $this->modalDefinitivo = true;
    }

    public function fecharModalDefinitivo(): void
    {
        $this->modalDefinitivo  = false;
        $this->excluindoId      = null;
        $this->excluindoNome    = '';
        $this->confirmacaoNome  = '';
        $this->contagensExclusao = [];
    }

    // ── Exclusão Definitiva ────────────────────────────────────────

    public function excluirDefinitivamente(): void
    {
        if (trim($this->confirmacaoNome) !== $this->excluindoNome) {
            $this->addError('confirmacaoNome', 'O nome digitado não confere.');
            return;
        }

        $tenant = Tenant::withTrashed()->findOrFail($this->excluindoId);

        // Exclui arquivos físicos do storage
        $pastaStorage = "public/tenants/{$tenant->slug}";
        if (Storage::exists($pastaStorage)) {
            Storage::deleteDirectory($pastaStorage);
        }

        // Exclui registros em todas as tabelas relacionadas
        $tabelas = [
            'processos', 'pessoas', 'documentos', 'minutas', 'andamentos',
            'financeiro_lancamentos', 'recebimentos', 'pagamentos',
            'honorarios', 'honorario_parcelas', 'contratos', 'contrato_servicos',
            'contrato_mensais', 'mensalidades', 'custas', 'apontamentos',
            'prazos', 'audiencias', 'agenda', 'notificacoes', 'cobrancas',
            'aasp_publicacoes', 'tjsp_verificacoes', 'comunicado_leituras',
            'crm_oportunidades', 'crm_atividades', 'orcamentos', 'correspondentes',
            'procuracoes', 'assinaturas', 'workflow_execucoes', 'ofx_lancamentos',
            'monitoramentos', 'lote_verificacoes', 'usuarios',
        ];

        foreach ($tabelas as $tabela) {
            try {
                DB::table($tabela)->where('tenant_id', $tenant->id)->delete();
            } catch (\Exception) {
                // Ignora tabelas que não existem ou não têm tenant_id
            }
        }

        // Log antes de excluir
        MasterAdminLog::registrar(
            'tenant_excluido_definitivo',
            $tenant->id,
            $tenant->nome,
            "Exclusão definitiva. Arquivos e todos os dados foram apagados."
        );

        // Invalida cache
        Cache::forget("tenant_{$tenant->id}");
        Cache::forget("tenant_host_{$tenant->dominio}");

        // Exclui o tenant permanentemente
        $tenant->forceDelete();

        $this->fecharModalDefinitivo();
        $this->dispatch('toast', message: "Tenant excluído permanentemente.", type: 'error');
    }

    public function render(): \Illuminate\View\View
    {
        $tenants = Tenant::onlyTrashed()
            ->addSelect([
                'usuarios_count'  => DB::table('usuarios')->selectRaw('count(*)')->whereColumn('tenant_id', 'tenants.id'),
                'processos_count' => DB::table('processos')->selectRaw('count(*)')->whereColumn('tenant_id', 'tenants.id'),
            ])
            ->orderByDesc('deleted_at')
            ->get();

        $excluindoPorUsuario = collect();
        if ($tenants->isNotEmpty()) {
            $adminIds = $tenants->pluck('deleted_by')->filter()->unique();
            if ($adminIds->isNotEmpty()) {
                $excluindoPorUsuario = Usuario::whereIn('id', $adminIds)->pluck('nome', 'id');
            }
        }

        return view('livewire.master-admin.lixeira', compact('tenants', 'excluindoPorUsuario'))
            ->extends('layouts.master')
            ->section('content');
    }
}
