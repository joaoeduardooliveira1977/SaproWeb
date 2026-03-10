<?php
// ============================================================
// ADICIONE ESTAS ROTAS NO ARQUIVO routes/web.php
// Dentro do grupo Route::middleware('auth:usuarios')
// ============================================================

use App\Http\Controllers\RelatorioController;

Route::prefix('relatorios')->name('relatorios.')->group(function () {
    Route::get('/',             fn() => view('relatorios.index'))->name('index');
    Route::get('/por-fase',     [RelatorioController::class, 'processosPorFase'])->name('por-fase');
    Route::get('/por-advogado', [RelatorioController::class, 'processosPorAdvogado'])->name('por-advogado');
    Route::get('/por-risco',    [RelatorioController::class, 'processosPorRisco'])->name('por-risco');
    Route::get('/agenda',       [RelatorioController::class, 'agendaPeriodo'])->name('agenda');
    Route::get('/custas',       [RelatorioController::class, 'custasPendentes'])->name('custas');
    Route::get('/aniversarios', [RelatorioController::class, 'aniversarios'])->name('aniversarios');
});
