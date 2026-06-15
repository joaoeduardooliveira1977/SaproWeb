<?php

namespace App\Services;

use App\Models\{Modulo, PlanoModulo, Tenant, TenantModulo};

class ModuloService
{
    // Módulos incluídos em cada plano (chaves)
    public const PLANOS = [
        'demo'       => ['processos','pessoas','prazos','agenda','financeiro','relatorios','cadastros','admin'],
        'starter'    => ['processos','pessoas','prazos','agenda','financeiro','relatorios','cadastros','admin','mensais','ferramentas','portal_cliente'],
        'pro'        => ['processos','pessoas','prazos','agenda','financeiro','relatorios','cadastros','admin','mensais','ferramentas','portal_cliente','aasp','despesas_reembolsos','assistente_ia','pipeline_crm','automacao'],
        'enterprise' => ['processos','pessoas','prazos','agenda','financeiro','relatorios','cadastros','admin','mensais','ferramentas','portal_cliente','aasp','despesas_reembolsos','assistente_ia','pipeline_crm','automacao'],
    ];

    /**
     * Inicializa os módulos do tenant conforme o plano.
     * Seguro para rodar múltiplas vezes (firstOrCreate).
     */
    public static function inicializarModulos(Tenant $tenant): void
    {
        $plano   = $tenant->plano ?? 'demo';
        $chaves  = self::PLANOS[$plano] ?? self::PLANOS['demo'];

        $modulos = Modulo::whereIn('chave', $chaves)->get()->keyBy('chave');

        foreach ($chaves as $chave) {
            if (!isset($modulos[$chave])) continue;

            TenantModulo::firstOrCreate(
                ['tenant_id' => $tenant->id, 'modulo_id' => $modulos[$chave]->id],
                ['ativo' => true, 'ativado_em' => now()]
            );
        }

        $tenant->invalidarCacheModulos();
    }

    /**
     * Redefine os módulos do tenant para o padrão do plano:
     * desativa os que não estão no plano, ativa os que estão.
     */
    public static function aplicarPlanoPadrao(Tenant $tenant): void
    {
        $plano  = $tenant->plano ?? 'demo';
        $chaves = self::PLANOS[$plano] ?? self::PLANOS['demo'];

        $todos   = Modulo::all()->keyBy('chave');
        $noPlano = collect($chaves)->map(fn($c) => $todos[$c]->id ?? null)->filter()->values()->toArray();

        // Ativa os do plano
        foreach ($noPlano as $moduloId) {
            TenantModulo::updateOrCreate(
                ['tenant_id' => $tenant->id, 'modulo_id' => $moduloId],
                ['ativo' => true, 'ativado_em' => now()]
            );
        }

        // Desativa os que não estão no plano
        if (!empty($noPlano)) {
            TenantModulo::where('tenant_id', $tenant->id)
                ->whereNotIn('modulo_id', $noPlano)
                ->update(['ativo' => false]);
        }

        $tenant->invalidarCacheModulos();
    }

    /**
     * Popula a tabela plano_modulos a partir de PLANOS (usado pelo seeder).
     */
    public static function seedPlanoModulos(): void
    {
        $modulos = Modulo::all()->keyBy('chave');

        foreach (self::PLANOS as $plano => $chaves) {
            foreach ($chaves as $chave) {
                if (!isset($modulos[$chave])) continue;
                PlanoModulo::firstOrCreate([
                    'plano'     => $plano,
                    'modulo_id' => $modulos[$chave]->id,
                ]);
            }
        }
    }
}
