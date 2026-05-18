<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{Andamento, ContratoMensal, Processo, Recebimento, HonorarioParcela, ReceitaProcesso};
use App\Observers\{AndamentoObserver, ContratoMensalObserver, ProcessoObserver, RecebimentoObserver, HonorarioParcelaObserver, ReceitaProcessoObserver};

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
        ContratoMensal::observe(ContratoMensalObserver::class);
        ReceitaProcesso::observe(ReceitaProcessoObserver::class);
    }
}
