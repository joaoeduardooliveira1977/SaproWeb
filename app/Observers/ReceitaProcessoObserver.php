<?php

namespace App\Observers;

use App\Models\ReceitaProcesso;

class ReceitaProcessoObserver
{
    public function deleted(ReceitaProcesso $receita): void
    {
        // Cancela receitas de processo quando o registro é soft-deleted
        // (lançamentos gerados ficam preservados para histórico)
    }
}
