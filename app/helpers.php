<?php

use App\Models\MasterAdminLog;

if (!function_exists('master_log')) {
    /**
     * Registra uma ação no log do Master Admin.
     */
    function master_log(string $acao, ?string $descricao = null, ?int $tenantAfetadoId = null, ?string $contexto = null): void
    {
        try {
            MasterAdminLog::registrar($acao, $tenantAfetadoId, null, $descricao, $contexto);
        } catch (\Exception) {
            // Não deixa falha de log derrubar a requisição
        }
    }
}
