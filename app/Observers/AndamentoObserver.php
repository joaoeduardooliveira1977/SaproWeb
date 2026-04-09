<?php

namespace App\Observers;

use App\Jobs\ExecutarWorkflow;
use App\Models\Andamento;

class AndamentoObserver
{
    /**
     * Dispara o Workflow quando um andamento é criado.
     * Roda em background (queue) para não bloquear o request.
     */
    public function created(Andamento $andamento): void
    {
        // Garante que o andamento tem processo_id antes de disparar
        if (!$andamento->processo_id) {
            return;
        }

        ExecutarWorkflow::paraAndamento($andamento);
    }
}
