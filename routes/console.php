<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Gera notificações internas e envia e-mail resumo diário às 7h
Schedule::command('notificacoes:gerar')->dailyAt('07:00');
