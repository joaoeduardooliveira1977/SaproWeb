<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AssinaturaWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\RelatorioController;
use App\Livewire\Portal\PortalLogin;
use App\Livewire\Portal\PortalDashboard;
use App\Livewire\PortalAcesso;
use App\Http\Controllers\IAController;



// ─── Login / Logout ────────────────────────────
	Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
	Route::post('/login', [AuthController::class, 'login']);
	Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── Super Admin ───────────────────────────────
	Route::prefix('super-admin')->name('super-admin.')->middleware(['auth:usuarios', 'super_admin'])->group(function () {
		Route::get('/',                   [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('index');
		Route::get('/voltar',             [\App\Http\Controllers\SuperAdminController::class, 'voltarSuperAdmin'])->name('voltar');
		Route::get('/{id}',               [\App\Http\Controllers\SuperAdminController::class, 'show'])->name('show');
		Route::post('/{id}/plano',        [\App\Http\Controllers\SuperAdminController::class, 'atualizarPlano'])->name('plano');
		Route::post('/{id}/toggle',       [\App\Http\Controllers\SuperAdminController::class, 'toggleAtivo'])->name('toggle');
		Route::get('/{id}/login',         [\App\Http\Controllers\SuperAdminController::class, 'loginComoTenant'])->name('login-tenant');
		Route::delete('/{id}',            [\App\Http\Controllers\SuperAdminController::class, 'excluir'])->name('excluir');
	});

// ─── Planos ────────────────────────────────────
	Route::get('/planos', function() {
    	return view('planos');
	})->name('tenant.planos');

// ─── Registro ──────────────────────────────────
	Route::get('/registro',  [\App\Http\Controllers\RegistroController::class, 'index'])->name('registro');
	Route::post('/registro', [\App\Http\Controllers\RegistroController::class, 'store'])->name('registro.store');


// ─── Área Autenticada ──────────────────────────
	Route::middleware('auth:usuarios')->group(function () {

    // ── Geral (todos os perfis autenticados) ───────────────────
    	Route::middleware('perfil:geral')->group(function () {
        Route::get('/', fn() => view('dashboard'))->name('dashboard');
        Route::get('/dashboard-preview', fn() => view('dashboard-preview'))->name('dashboard.preview');
        Route::get('/agenda',     fn() => view('agenda'))->name('agenda');
        Route::get('/prazos',     fn() => view('prazos'))->name('prazos');
        Route::get('/sla',        fn() => view('sla'))->name('sla');
        Route::get('/audiencias', fn() => view('audiencias'))->name('audiencias');
        Route::get('/minha-conta', fn() => view('minha-conta'))->name('minha-conta');
        Route::post('/minha-conta', [AuthController::class, 'trocarSenha'])->name('minha-conta.salvar');
        Route::get('/processos-hub',   fn() => view('hubs.processos'))->name('processos.hub');
        Route::get('/cadastros-hub',   fn() => view('hubs.cadastros'))->name('cadastros.hub');
    });

    // ── Hubs de seção ──────────────────────────────────────────
    Route::middleware('perfil:financeiro')->get('/financeiro-hub',   fn() => view('hubs.financeiro'))->name('financeiro.hub');
    Route::middleware('perfil:ferramentas')->get('/ferramentas-hub', fn() => view('hubs.ferramentas'))->name('ferramentas.hub');
    Route::middleware('perfil:admin')->get('/admin-hub',             fn() => view('hubs.admin'))->name('admin.hub');

    // ── Processos ───────────────────────────────────────────────
    Route::middleware('perfil:processos')->group(function () {
        Route::get('/processos',             fn() => view('processos'))->name('processos');
        Route::get('/processos/kanban',      \App\Livewire\Processos\Kanban::class)->name('processos.kanban');
        Route::get('/processos/monitoramento', \App\Livewire\Processos\Monitoramento::class)->name('processos.monitoramento');
        Route::get('/processos/novo',        fn() => view('processo-form'))->name('processos.novo');
        Route::get('/processos/{id}/editar', fn($id) => view('processo-form', ['id' => $id]))->name('processos.editar');
        Route::get('/processos/{id}',        [ProcessoController::class, 'show'])->name('processos.show');
        Route::get('/processos/{id}/andamentos', [ProcessoController::class, 'andamentos'])->name('processos.andamentos');
        Route::get('/processos/{id}/custas',     [ProcessoController::class, 'custas'])->name('processos.custas');
	Route::get('/processos/{id}/resumo-ia', [ProcessoController::class, 'gerarResumo']);
    });

    // ── Pessoas ─────────────────────────────────────────────────
    Route::middleware('perfil:pessoas')->group(function () {
        Route::get('/pessoas',                fn() => view('pessoas'))->name('pessoas');
        Route::get('/pessoas/{clienteId}/pasta', fn($clienteId) => view('pasta-cliente', compact('clienteId')))->name('pessoas.pasta');
        Route::get('/correspondentes',        fn() => view('correspondentes'))->name('correspondentes');
        Route::get('/procuracoes',            fn() => view('procuracoes'))->name('procuracoes');
    });

    // ── Documentos & Minutas ────────────────────────────────────
    Route::middleware('perfil:documentos')->group(function () {
        Route::get('/documentos',        fn() => view('documentos'))->name('documentos');
        Route::get('/minutas',           fn() => view('minutas'))->name('minutas');
        Route::get('/assinatura-digital', fn() => view('assinatura-digital'))->name('assinatura-digital');
    });

    // ── Financeiro ──────────────────────────────────────────────
    Route::middleware('perfil:financeiro')->group(function () {
        Route::get('/financeiro',             fn() => view('financeiro'))->name('financeiro');
        Route::get('/financeiro-consolidado', fn() => view('financeiro-consolidado'))->name('financeiro.consolidado');
        Route::get('/honorarios',             fn() => view('honorarios'))->name('honorarios');
        Route::get('/inadimplencia',          fn() => view('inadimplencia'))->name('inadimplencia');
    });

    // ── Relatórios ──────────────────────────────────────────────
    Route::middleware('perfil:relatorios')->prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/',                   [RelatorioController::class, 'index'])->name('index');
        Route::get('/por-fase',           [RelatorioController::class, 'processosPorFase'])->name('por-fase');
        Route::get('/por-advogado',       [RelatorioController::class, 'processosPorAdvogado'])->name('por-advogado');
        Route::get('/por-risco',          [RelatorioController::class, 'processosPorRisco'])->name('por-risco');
        Route::get('/agenda',             [RelatorioController::class, 'agendaPeriodo'])->name('agenda');
        Route::get('/custas',             [RelatorioController::class, 'custasPendentes'])->name('custas');
        Route::get('/aniversarios',       [RelatorioController::class, 'aniversarios'])->name('aniversarios');
        Route::get('/andamentos-cliente', [RelatorioController::class, 'andamentosPorCliente'])->name('andamentos-cliente');
        Route::get('/honorarios-aberto',  [RelatorioController::class, 'honorariosEmAberto'])->name('honorarios-aberto');
        Route::get('/financeiro-periodo', [RelatorioController::class, 'financeiroPorPeriodo'])->name('financeiro-periodo');
        Route::get('/sem-andamento',         [RelatorioController::class, 'processosSemAndamento'])->name('sem-andamento');
        Route::get('/produtividade-pdf',     [RelatorioController::class, 'produtividadeAdvogado'])->name('produtividade-pdf');
        Route::get('/por-tipo-acao',         [RelatorioController::class, 'processosPorTipoAcao'])->name('por-tipo-acao');
        Route::get('/lista-geral',           [RelatorioController::class, 'listaGeral'])->name('lista-geral');
    });

    // ── Analytics & Produtividade ────────────────────────────────
    Route::middleware('perfil:relatorios')->group(function () {
        Route::get('/analytics',     fn() => view('analytics'))->name('analytics');
        Route::get('/produtividade', fn() => view('produtividade'))->name('produtividade');
    });

    // ── Ferramentas ─────────────────────────────────────────────
    Route::middleware('perfil:ferramentas')->group(function () {
        Route::get('/tjsp', \App\Livewire\Processos\Monitoramento::class)->name('tjsp');
        Route::get('/assistente',   fn() => view('assistente'))->name('assistente');
        Route::get('/aasp-publicacoes', fn() => view('aasp-publicacoes'))->name('aasp-publicacoes');
        Route::get('/calculadora',  fn() => view('calculadora'))->name('calculadora');
        Route::get('/monitoramento', \App\Livewire\Processos\Monitoramento::class)->name('monitoramento');
        Route::get('/conciliacao-bancaria', fn() => view('conciliacao-bancaria'))->name('conciliacao-bancaria');
        Route::get('/crm', fn() => view('crm'))->name('crm');
        Route::get('/workflow-regras', \App\Livewire\WorkflowRegras::class)->name('workflow.regras');
    });

    // ── Administração (admin only) ──────────────────────────────
    Route::middleware('perfil:admin')->group(function () {
        Route::get('/tabelas',         fn() => view('tabelas'))->name('tabelas');
        Route::get('/administradoras', fn() => view('administradoras'))->name('administradoras');
        Route::get('/indices',         fn() => view('indices'))->name('indices');
        Route::get('/auditoria', fn() => view('auditoria'))->name('auditoria');
        Route::get('/usuarios', fn() => view('usuarios'))->name('usuarios');
        Route::get('/admin/portal-acesso',       fn() => view('portal-acesso'))->name('admin.portal-acesso');
        Route::get('/admin/portal-mensagens',    fn() => view('portal-mensagens'))->name('admin.portal-mensagens');
        Route::get('/admin/notificacoes-whatsapp', fn() => view('notificacoes-whatsapp'))->name('admin.notificacoes-whatsapp');
    });

});


// ─── Webhooks (sem auth/csrf) ──────────────────────────────────
Route::post('/webhooks/clicksign', [AssinaturaWebhookController::class, 'handle'])
    ->name('webhooks.clicksign')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login',     PortalLogin::class)->name('login');
    Route::get('/dashboard', PortalDashboard::class)->name('dashboard');
});

// ─── IA (teste) ────────────────────────────────────────────
Route::get('/ia-teste', [IAController::class, 'teste'])->middleware('auth:usuarios');
