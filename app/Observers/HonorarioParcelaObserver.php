<?php

namespace App\Observers;

use App\Models\HonorarioParcela;
use App\Services\Financeiro\ComissaoService;

class HonorarioParcelaObserver
{
    public function updated(HonorarioParcela $parcela): void
    {
        if ($parcela->wasChanged('status') && $parcela->status === 'pago') {
            app(ComissaoService::class)->gerarParaHonorario($parcela);
        }
    }
}
