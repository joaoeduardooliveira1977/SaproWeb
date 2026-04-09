<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Gera notificações internas e envia e-mail resumo diário às 7h
Schedule::command('notificacoes:gerar')->dailyAt('07:00');

// Envia notificações WhatsApp/SMS: prazos + audiências às 7:15, cobranças às 8h
Schedule::command('notificacoes:whatsapp --tipo=prazo')->dailyAt('07:15');
Schedule::command('notificacoes:whatsapp --tipo=audiencia')->dailyAt('07:15');
Schedule::command('notificacoes:whatsapp --tipo=cobranca')->dailyAt('08:00');

// Atualiza índices monetários (IPCA, IGPM, SELIC, TR) todo dia 15 às 6h
Schedule::command('indices:atualizar')->monthlyOn(15, '06:00');

// Verifica novos andamentos no DataJud para todos os processos ativos (dias úteis, 6h)
Schedule::command('datajud:verificar')->weekdays()->dailyAt('06:00');

// Busca publicações AASP do dia automaticamente (dias úteis, 8h30)
Schedule::command('aasp:buscar')->weekdays()->dailyAt('08:30');

// Dispara gatilhos de workflow baseados em tempo (prazos vencendo/vencidos, inatividade)
Schedule::command('workflow:verificar-agendados')
    ->dailyAt('07:30')
    ->timezone('America/Sao_Paulo');

// Verifica monitoramentos automáticos de processos: 7h e 13h
Schedule::job(new \App\Jobs\VerificarMonitoramentos())
    ->dailyAt('07:00')
    ->timezone('America/Sao_Paulo');

Schedule::job(new \App\Jobs\VerificarMonitoramentos())
    ->dailyAt('13:00')
    ->timezone('America/Sao_Paulo');
