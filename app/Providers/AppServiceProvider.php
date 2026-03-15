<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Processo;
use App\Observers\ProcessoObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Processo::observe(ProcessoObserver::class);
    }
}
