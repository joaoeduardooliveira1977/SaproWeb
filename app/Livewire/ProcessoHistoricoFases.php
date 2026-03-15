<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ProcessoHistoricoFases extends Component
{
    public int $processoId;

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
    }

    public function render()
    {
        $historico = DB::select(
            'SELECT pfh.created_at,
                    fa.descricao AS fase_anterior,
                    fn.descricao AS fase_nova,
                    p.nome       AS usuario_nome
             FROM   processo_fases_historico pfh
             LEFT JOIN fases fa  ON fa.id = pfh.fase_anterior_id
             LEFT JOIN fases fn  ON fn.id = pfh.fase_nova_id
             LEFT JOIN usuarios u ON u.id = pfh.usuario_id
             LEFT JOIN pessoas p  ON p.id = u.pessoa_id
             WHERE  pfh.processo_id = ?
             ORDER  BY pfh.created_at DESC',
            [$this->processoId]
        );

        return view('livewire.processo-historico-fases', compact('historico'));
    }
}
