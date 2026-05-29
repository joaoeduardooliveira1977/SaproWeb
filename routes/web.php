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
		Route::get('/novo',               [\App\Http\Controllers\SuperAdminController::class, 'criar'])->name('criar');
		Route::post('/novo',              [\App\Http\Controllers\SuperAdminController::class, 'salvar'])->name('salvar');

		// Rotas estáticas ANTES do parâmetro dinâmico {id}
		Route::get('/branding',           \App\Livewire\Admin\TenantBranding::class)->name('branding');

		// Rotas dinâmicas com restrição numérica para evitar conflito com rotas estáticas
		Route::get('/{id}',               [\App\Http\Controllers\SuperAdminController::class, 'show'])->where('id', '[0-9]+')->name('show');
		Route::post('/{id}/plano',        [\App\Http\Controllers\SuperAdminController::class, 'atualizarPlano'])->where('id', '[0-9]+')->name('plano');
		Route::post('/{id}/toggle',       [\App\Http\Controllers\SuperAdminController::class, 'toggleAtivo'])->where('id', '[0-9]+')->name('toggle');
		Route::get('/{id}/login',         [\App\Http\Controllers\SuperAdminController::class, 'loginComoTenant'])->where('id', '[0-9]+')->name('login-tenant');
		Route::delete('/{id}',            [\App\Http\Controllers\SuperAdminController::class, 'excluir'])->where('id', '[0-9]+')->name('excluir');
	});

// ─── Planos ────────────────────────────────────
	Route::get('/planos', function() {
    	return view('planos');
	})->name('tenant.planos');

// ─── Registro ──────────────────────────────────
	Route::get('/registro', \App\Livewire\Auth\RegistroTenant::class)->name('registro');
	Route::post('/registro', [\App\Http\Controllers\RegistroController::class, 'store'])->name('registro.store');


// ─── Área Autenticada ──────────────────────────


Route::get('/teste-config', fn() => 'funcionou');


	Route::middleware('auth:usuarios')->group(function () {

    // ── Onboarding ─────────────────────────────────────────────
    Route::get('/onboarding', \App\Livewire\Onboarding::class)->name('onboarding');

    // ── Configurações ───────────────────────────────────────────
    Route::get('/configuracoes/escritorio', \App\Livewire\Configuracoes\EscritorioConfig::class)->name('configuracoes.escritorio');

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
        Route::get('/processos/monitoramento', \App\Livewire\Processos\Monitoramento::class)->name('processos.monitoramento');
        Route::get('/processos/novo',        fn() => view('processo-form'))->name('processos.novo');
        Route::get('/processos/{id}/editar', fn($id) => view('processo-form', ['id' => $id]))->name('processos.editar');
        Route::get('/processos/{id}',        [ProcessoController::class, 'show'])->name('processos.show');
        Route::get('/processos/{id}/andamentos', [ProcessoController::class, 'andamentos'])->name('processos.andamentos');
        Route::get('/processos/{id}/custas',     [ProcessoController::class, 'custas'])->name('processos.custas');
        Route::post('/processos/{id}/custas',                    [ProcessoController::class, 'storeCusta'])->name('processos.custas.store');
        Route::post('/processos/{id}/custas/{custaId}/reembolso', [ProcessoController::class, 'alternarReembolsoCusta'])->name('processos.custas.reembolso');
        Route::post('/processos/{id}/custas/{custaId}/cobranca', [ProcessoController::class, 'gerarCobrancaCusta'])->name('processos.custas.cobranca');
	Route::get('/processos/{id}/resumo-ia', [ProcessoController::class, 'gerarResumo']);
        Route::get('/processos/{id}/pdf', [ProcessoController::class, 'exportarPdf'])->name('processos.pdf');
    });

    // ── Contratos ───────────────────────────────────────────────
    Route::middleware('perfil:financeiro')->group(function () {
        Route::get('/contratos', fn() => view('contratos'))->name('contratos');
    });

    // ── Contratos Mensais ────────────────────────────────────────
    Route::middleware('perfil:financeiro')->group(function () {
        Route::get('/contratos-mensais',                   \App\Livewire\ContratosMensais\Index::class)->name('contratos-mensais.index');
        Route::get('/contratos-mensais/{id}/mensalidades', \App\Livewire\ContratosMensais\Mensalidades::class)->name('contratos-mensais.mensalidades');
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
        Route::get('/financeiro-central',     fn() => view('financeiro-central'))->name('financeiro.central');
        Route::get('/financeiro/custas-reembolso', \App\Livewire\Financeiro\CustasReembolso::class)->name('financeiro.custas-reembolso');
        Route::get('/financeiro/despesas-escritorio', \App\Livewire\Financeiro\DespesasEscritorio::class)->name('financeiro.despesas-escritorio');
        Route::get('/financeiro/contratos-mensais', \App\Livewire\Financeiro\ContratosMensaisIndex::class)->name('financeiro.contratos-mensais');
        Route::get('/financeiro/clientes/{clienteId}', \App\Livewire\Financeiro\Cliente::class)->name('financeiro.cliente');
        Route::get('/config/financeiro', \App\Livewire\Config\Financeiro::class)->name('config.financeiro');
        Route::get('/honorarios',             fn() => view('honorarios'))->name('honorarios');
        Route::get('/inadimplencia',          fn() => view('inadimplencia'))->name('inadimplencia');
        Route::get('/indicadores',            fn() => view('indicadores'))->name('indicadores');
        Route::get('/comissoes',              fn() => view('comissoes'))->name('comissoes');
    });

    // ── Relatórios ──────────────────────────────────────────────
    Route::middleware('perfil:relatorios')->prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/',                   [RelatorioController::class, 'index'])->name('index');
        Route::get('/por-fase',           [RelatorioController::class, 'processosPorFase'])->name('por-fase');
        Route::get('/por-advogado',       [RelatorioController::class, 'processosPorAdvogado'])->name('por-advogado');
        Route::get('/por-risco',          [RelatorioController::class, 'processosPorRisco'])->name('por-risco');
        Route::get('/agenda',             [RelatorioController::class, 'agendaPeriodo'])->name('agenda');
        Route::get('/custas',             [RelatorioController::class, 'custasPendentes'])->name('custas');
        Route::get('/custas-reembolso',   [RelatorioController::class, 'custasReembolso'])->name('custas-reembolso');
        Route::get('/aniversarios',       [RelatorioController::class, 'aniversarios'])->name('aniversarios');
        Route::get('/andamentos-cliente', [RelatorioController::class, 'andamentosPorCliente'])->name('andamentos-cliente');
        Route::get('/honorarios-aberto',  [RelatorioController::class, 'honorariosEmAberto'])->name('honorarios-aberto');
        Route::get('/financeiro-periodo', [RelatorioController::class, 'financeiroPorPeriodo'])->name('financeiro-periodo');
        Route::get('/sem-andamento',         [RelatorioController::class, 'processosSemAndamento'])->name('sem-andamento');
        Route::get('/produtividade-pdf',     [RelatorioController::class, 'produtividadeAdvogado'])->name('produtividade-pdf');
        Route::get('/por-tipo-acao',         [RelatorioController::class, 'processosPorTipoAcao'])->name('por-tipo-acao');
        Route::get('/lista-geral',           [RelatorioController::class, 'listaGeral'])->name('lista-geral');
        Route::get('/financeiro-mensal',     [RelatorioController::class, 'relatorioFinanceiroMensal'])->name('financeiro-mensal');
        Route::get('/extrato-cliente',       [RelatorioController::class, 'extratoCliente'])->name('extrato-cliente');
    });

    // ── Analytics & Produtividade ────────────────────────────────
    Route::middleware('perfil:relatorios')->group(function () {
        Route::get('/analytics',     fn() => view('analytics'))->name('analytics');
        Route::get('/produtividade', fn() => view('produtividade'))->name('produtividade');
        Route::get('/horas',         fn() => view('horas'))->name('horas');
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
        Route::get('/orcamentos', fn() => view('orcamentos'))->name('orcamentos');
        Route::get('/orcamentos/{id}/pdf', [\App\Http\Controllers\RelatorioController::class, 'orcamentoPdf'])->name('orcamentos.pdf');
        Route::get('/contratos/{id}/pdf', [\App\Http\Controllers\RelatorioController::class, 'contratoPdf'])->name('contratos.pdf');
        Route::get('/modelos-contrato', \App\Livewire\ModelosContrato::class)->name('modelos-contrato');
        Route::get('/workflow-regras', \App\Livewire\WorkflowRegras::class)->name('workflow.regras');
    });

    // ── Administração (admin only) ──────────────────────────────
    Route::middleware('perfil:admin')->group(function () {
        Route::get('/tabelas',         fn() => view('tabelas'))->name('tabelas');
        Route::get('/administradoras', fn() => view('administradoras'))->name('administradoras');
        Route::get('/indices',         fn() => view('indices'))->name('indices');
        Route::get('/auditoria', fn() => view('auditoria'))->name('auditoria');
        Route::get('/usuarios', fn() => view('usuarios'))->name('usuarios');
        Route::get('/admin/usuarios', \App\Livewire\Admin\GestaoUsuarios::class)->name('admin.usuarios');
        Route::get('/admin/perfis', fn() => view('perfil-permissoes'))->name('admin.perfis');
        Route::get('/admin/portal-acesso',       fn() => view('portal-acesso'))->name('admin.portal-acesso');
        Route::get('/admin/portal-mensagens',    fn() => view('portal-mensagens'))->name('admin.portal-mensagens');
        Route::get('/admin/notificacoes-whatsapp', fn() => view('notificacoes-whatsapp'))->name('admin.notificacoes-whatsapp');
        Route::get('/admin/backup', \App\Livewire\Admin\Backup::class)->name('admin.backup');
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
