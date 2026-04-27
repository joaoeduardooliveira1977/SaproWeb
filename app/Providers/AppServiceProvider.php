<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{Andamento, Processo, Recebimento, HonorarioParcela};
use App\Observers\{AndamentoObserver, ProcessoObserver, RecebimentoObserver, HonorarioParcelaObserver};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Processo::observe(ProcessoObserver::class);
        Andamento::observe(AndamentoObserver::class);
        Recebimento::observe(RecebimentoObserver::class);
        HonorarioParcela::observe(HonorarioParcelaObserver::class);
    }
}
