<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\RelatorioController;
use App\Livewire\Portal\PortalLogin;
use App\Livewire\Portal\PortalDashboard;
use App\Livewire\PortalAcesso;



// ─── Login / Logout ────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── Área Autenticada ──────────────────────────
Route::middleware('auth:usuarios')->group(function () {

    // Dashboard
    Route::get('/', fn() => view('dashboard'))->name('dashboard');

    // Pessoas
    Route::get('/pessoas', fn() => view('pessoas'))->name('pessoas');

    // Processos
    Route::get('/processos',           fn() => view('processos'))->name('processos');
    Route::get('/processos/novo',      fn() => view('processo-form'))->name('processos.novo');
    Route::get('/processos/{id}/editar', fn($id) => view('processo-form', ['id' => $id]))->name('processos.editar');
    Route::get('/processos/{id}',      [ProcessoController::class, 'show'])->name('processos.show');

    // Agenda
    Route::get('/agenda', fn() => view('agenda'))->name('agenda');

    // Módulo do Processo (andamentos, custas)
    Route::get('/processos/{id}/andamentos', [ProcessoController::class, 'andamentos'])->name('processos.andamentos');
    Route::get('/processos/{id}/custas',     [ProcessoController::class, 'custas'])->name('processos.custas');

    // Tabelas de domínio
    Route::get('/tabelas', fn() => view('tabelas'))->name('tabelas');

    // Índices Monetários
    Route::get('/indices', fn() => view('indices'))->name('indices');

    // Auditoria (admin)
    Route::get('/auditoria', fn() => view('auditoria'))->name('auditoria');

    // Trocar senha
    Route::get('/minha-conta', fn() => view('minha-conta'))->name('minha-conta');

    Route::post('/minha-conta', [AuthController::class, 'trocarSenha'])->name('minha-conta.salvar');

    // Financeiro
    Route::get('/financeiro', fn() => view('financeiro'))->name('financeiro');

    // Portal Acesso
    Route::get('/admin/portal-acesso', fn() => view('portal-acesso'))->name('admin.portal-acesso');

    Route::prefix('relatorios')->name('relatorios.')->group(function () {
    Route::get('/',             fn() => view('relatorios.index'))->name('index');
    Route::get('/por-fase',     [RelatorioController::class, 'processosPorFase'])->name('por-fase');
    Route::get('/por-advogado', [RelatorioController::class, 'processosPorAdvogado'])->name('por-advogado');
    Route::get('/por-risco',    [RelatorioController::class, 'processosPorRisco'])->name('por-risco');
    Route::get('/agenda',       [RelatorioController::class, 'agendaPeriodo'])->name('agenda');
    Route::get('/custas',       [RelatorioController::class, 'custasPendentes'])->name('custas');
    Route::get('/aniversarios', [RelatorioController::class, 'aniversarios'])->name('aniversarios');
    

    });

    // Portal TJSP
    Route::get('/tjsp', fn() => view('tjsp'))->name('tjsp');

    // Assistente
    Route::get('/assistente', fn() => view('assistente'))->name('assistente');


});


Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login',     PortalLogin::class)->name('login');
    Route::get('/dashboard', PortalDashboard::class)->name('dashboard');




});
