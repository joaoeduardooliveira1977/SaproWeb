<?php

namespace App\Observers;

use App\Jobs\ExecutarWorkflow;
use App\Models\Processo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessoObserver
{
    /**
     * Detecta mudança de fase, registra no histórico e dispara workflow.
     */
    public function updating(Processo $processo): void
    {
        if (! $processo->isDirty('fase_id')) {
            return;
        }

        $faseAnteriorId = $processo->getOriginal('fase_id');

        DB::table('processo_fases_historico')->insert([
            'processo_id'      => $processo->id,
            'fase_anterior_id' => $faseAnteriorId,
            'fase_nova_id'     => $processo->fase_id,
            'usuario_id'       => Auth::guard('usuarios')->id(),
            'created_at'       => now(),
        ]);

        ExecutarWorkflow::paraFaseMudou($processo, $faseAnteriorId);
    }
}
