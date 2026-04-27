<?php

namespace App\Observers;

use App\Models\Recebimento;
use App\Services\Financeiro\ComissaoService;

class RecebimentoObserver
{
    public function updated(Recebimento $recebimento): void
    {
        if ($recebimento->wasChanged('recebido') && $recebimento->recebido) {
            app(ComissaoService::class)->gerarParaRecebimento($recebimento);
        }
    }
}
