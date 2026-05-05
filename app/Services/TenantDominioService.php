<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class TenantDominioService
{
    private array $tabelas = [
        'fornecedores',
        'origens_recebimento',
        'whatsapp_templates',
        'graus_risco',
        'tipos_acao',
        'tipos_processo',
    ];

    /**
     * Copia todos os registros padrão (tenant_id = null) para o novo tenant.
     * Chamado automaticamente ao criar um novo tenant no SuperAdminController.
     */
    public function copiarPadroesParaTenant(int $tenantId): void
    {
        foreach ($this->tabelas as $tabela) {
            if (!Schema::hasColumn($tabela, 'tenant_id')) {
                continue;
            }

            $registros = DB::table($tabela)->whereNull('tenant_id')->get();

            foreach ($registros as $registro) {
                $dados = (array) $registro;
                unset($dados['id']);
                $dados['tenant_id']   = $tenantId;
                $dados['created_at']  = now();
                $dados['updated_at']  = now();

                if (!$this->jaExisteParaTenant($tabela, $tenantId, $dados)) {
                    DB::table($tabela)->insert($dados);
                }
            }
        }
    }

    /**
     * Retorna registros para o tenant: os próprios se existirem,
     * senão os padrões do sistema (tenant_id = null) como fallback.
     */
    public function registrosParaTenant(string $tabela, int $tenantId): Collection
    {
        $temProprios = DB::table($tabela)->where('tenant_id', $tenantId)->exists();

        if ($temProprios) {
            return DB::table($tabela)->where('tenant_id', $tenantId)->get();
        }

        return DB::table($tabela)->whereNull('tenant_id')->get();
    }

    private function jaExisteParaTenant(string $tabela, int $tenantId, array $dados): bool
    {
        // Prioridade: codigo > nome > descricao (dependendo da tabela)
        $campo = match(true) {
            isset($dados['codigo'])   => 'codigo',
            isset($dados['nome'])     => 'nome',
            isset($dados['descricao']) => 'descricao',
            default                  => null,
        };

        if (!$campo || !isset($dados[$campo])) {
            return false;
        }

        return DB::table($tabela)
            ->where('tenant_id', $tenantId)
            ->where($campo, $dados[$campo])
            ->exists();
    }
}
