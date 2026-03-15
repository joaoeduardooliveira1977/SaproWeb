<?php

namespace App\Observers;

use App\Models\Processo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessoObserver
{
    /**
     * Detecta mudança de fase e registra no histórico.
     */
    public function updating(Processo $processo): void
    {
        if (! $processo->isDirty('fase_id')) {
            return;
        }

        DB::table('processo_fases_historico')->insert([
            'processo_id'      => $processo->id,
            'fase_anterior_id' => $processo->getOriginal('fase_id'),
            'fase_nova_id'     => $processo->fase_id,
            'usuario_id'       => Auth::guard('usuarios')->id(),
            'created_at'       => now(),
        ]);
    }
}
