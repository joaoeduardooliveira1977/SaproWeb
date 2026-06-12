<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AssinaturaWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterLoginController;
use App\Http\Controllers\Master2FAController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\RelatorioController;
use App\Livewire\Portal\PortalLogin;
use App\Livewire\Portal\PortalDashboard;
use App\Livewire\PortalAcesso;
use App\Http\Controllers\IAController;


// ─── Raiz: landing para visitantes, dashboard para autenticados ──────
    Route::get('/', function () {
        if (auth('usuarios')->check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('landing');
    })->name('home');

// ─── Landing page pública ──────────────────────────────────────────
    Route::get('/landing', function () {
        return response()->file(public_path('landing.html'));
    })->name('landing');

// ─── Login / Logout ────────────────────────────
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── Política de Privacidade (pública) ────────────────────────
    Route::get('/privacidade', fn() => view('privacidade'))->name('privacidade');

// ─── CSRF token para a landing page (estática) ─────────────────
    Route::get('/api/csrf-token', fn() => response()->json(['token' => csrf_token()]))->name('api.csrf');

// ─── Cadastro Trial (público) ──────────────────
    Route::get('/cadastro', \App\Livewire\CadastroTrial::class)->name('cadastro');
    Route::post('/cadastro-landing', [\App\Http\Controllers\CadastroLandingController::class, 'store'])->name('cadastro.landing');
    Route::get('/verificar-email', \App\Livewire\VerificarEmail::class)->name('verificar-email');

// ─── Trial Expirado ────────────────────────────
    Route::get('/trial-expirado', fn() => view('trial-expirado'))->name('trial.expirado');

// ─── Área Master (/master) ─────────────────────────────────────────
    Route::prefix('master')->name('master.')->group(function () {

        // Login público da área master
        Route::get('/login',  [MasterLoginController::class, 'showLogin'])->name('login');
        Route::post('/login', [MasterLoginController::class, 'login'])->name('login.submit');
        Route::post('/logout',[MasterLoginController::class, 'logout'])->name('logout');

        // Verificação 2FA (requer autenticação mas não verificação 2FA)
        Route::middleware(['auth:usuarios'])->group(function () {
            Route::get('/2fa/verificar',  [Master2FAController::class, 'mostrarVerificar'])->name('2fa.verificar');
            Route::post('/2fa/verificar', [Master2FAController::class, 'verificar'])->name('2fa.verificar.post');
        });

        // Área autenticada completa (somente super_admin + 2FA)
        Route::middleware(['master_auth'])->group(function () {
            Route::get('/', fn() => redirect()->route('master.dashboard'));
            Route::get('/dashboard',       \App\Livewire\MasterAdmin\Dashboard::class)->name('dashboard');
            Route::get('/tenants',         \App\Livewire\MasterAdmin\Tenants::class)->name('tenants');
            Route::get('/tenants/{id}',    \App\Livewire\MasterAdmin\TenantShow::class)->where('id', '[0-9]+')->name('tenant-show');
            Route::get('/lixeira',         \App\Livewire\MasterAdmin\Lixeira::class)->name('lixeira');
            Route::get('/comunicados',     \App\Livewire\MasterAdmin\Comunicados::class)->name('comunicados');
            Route::get('/infra',           \App\Livewire\MasterAdmin\Infra::class)->name('infra');
            Route::get('/alertas',         \App\Livewire\MasterAdmin\Alertas::class)->name('alertas');
            Route::get('/logs',            \App\Livewire\MasterAdmin\Logs::class)->name('logs');

            // 2FA configuração (dentro do master autenticado)
            Route::get('/2fa/configurar',  [Master2FAController::class, 'mostrarConfigurar'])->name('2fa.configurar');
            Route::post('/2fa/confirmar',  [Master2FAController::class, 'confirmarSetup'])->name('2fa.confirmar');
            Route::post('/2fa/desativar',  [Master2FAController::class, 'desativar'])->name('2fa.desativar');
        });
    });

// ─── Redirecionamentos 301: /master-admin → /master ────────────────
    Route::redirect('/master-admin', '/master', 301);
    Route::redirect('/master-admin/dashboard', '/master/dashboard', 301);
    Route::redirect('/master-admin/tenants', '/master/tenants', 301);
    Route::redirect('/master-admin/infra', '/master/infra', 301);
    Route::redirect('/master-admin/alertas', '/master/alertas', 301);
    Route::redirect('/master-admin/comunicados', '/master/comunicados', 301);

// ─── Redirecionamentos 301: /super-admin → /master ─────────────────
    Route::redirect('/super-admin', '/master/tenants', 301);
    Route::redirect('/super-admin/novo', '/master/tenants', 301);
    Route::redirect('/super-admin/branding', '/master/tenants', 301);

// ─── Planos / Expirado ────────────────────────────────────────────
    Route::get('/planos', function() {
        return view('planos');
    })->name('tenant.planos');

    Route::get('/plano-expirado', fn() => view('plano-expirado'))->name('plano.expirado');

// ─── Registro ──────────────────────────────────────────────────────
    Route::get('/registro', \App\Livewire\Auth\RegistroTenant::class)->name('registro');
    Route::post('/registro', [\App\Http\Controllers\RegistroController::class, 'store'])->name('registro.store');


// ─── Área Autenticada ──────────────────────────────────────────────

Route::get('/teste-config', fn() => 'funcionou');


    Route::middleware('auth:usuarios')->group(function () {

    // ── Onboarding ─────────────────────────────────────────────
    Route::get('/onboarding', \App\Livewire\Onboarding::class)->name('onboarding');

    // ── Configurações (admin e administrador apenas) ────────────
    Route::middleware('perfil:admin')->get('/configuracoes/escritorio', \App\Livewire\Configuracoes\EscritorioConfig::class)->name('configuracoes.escritorio');
    Route::middleware('perfil:admin')->get('/configuracoes/privacidade', \App\Livewire\Configuracoes\Privacidade::class)->name('configuracoes.privacidade');

    // ── Geral (todos os perfis autenticados) ───────────────────
        Route::middleware('perfil:geral')->group(function () {
        Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
        Route::get('/dashboard-preview', fn() => view('dashboard-preview'))->name('dashboard.preview');
        Route::get('/agenda',     fn() => view('agenda'))->name('agenda');
        Route::get('/prazos',     fn() => view('prazos'))->name('prazos');
        Route::get('/sla',        fn() => view('sla'))->name('sla');
        Route::get('/audiencias', fn() => view('audiencias'))->name('audiencias');
        Route::get('/minha-conta', fn() => view('minha-conta'))->name('minha-conta');
        Route::post('/minha-conta', [AuthController::class, 'trocarSenha'])->name('minha-conta.salvar');
        Route::post('/minha-conta/perfil', [AuthController::class, 'salvarPerfil'])->name('minha-conta.perfil');
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
        Route::get('/despesas',              \App\Livewire\Relatorios\FiltrosDespesas::class)->name('despesas');
        Route::get('/despesas/pdf',          [RelatorioController::class, 'despesasClientePdf'])->name('despesas.pdf');
        Route::get('/reembolso',             \App\Livewire\Relatorios\FiltrosReembolso::class)->name('reembolso');
        Route::get('/reembolso/pdf',         [RelatorioController::class, 'pedidoReembolsoPdf'])->name('reembolso.pdf');
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
        Route::get('/aasp/publicacoes/word', [\App\Http\Controllers\AaspWordController::class, 'download'])->name('aasp.publicacoes.word');
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

    // ── Despesas & Reembolsos ────────────────────────────────────
    Route::middleware('perfil:financeiro')->get('/despesas-reembolsos', \App\Livewire\DespesasReembolsos::class)->name('despesas-reembolsos');
    Route::middleware('perfil:financeiro')->group(function () {
        Route::get('/despesas-reembolsos/relatorio/despesas',
            [\App\Http\Controllers\DespesaReembolsoRelatorioController::class, 'relatorioDespesas'])
            ->name('despesas.relatorio.despesas');
        Route::get('/despesas-reembolsos/relatorio/reembolso',
            [\App\Http\Controllers\DespesaReembolsoRelatorioController::class, 'relatorioReembolso'])
            ->name('despesas.relatorio.reembolso');
    });

    // ── Filiais (Tabelas Auxiliares) ─────────────────────────────
    Route::middleware('perfil:admin')->get('/cadastros/filiais', \App\Livewire\Cadastros\GestaoFiliais::class)->name('cadastros.filiais');

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
        Route::get('/admin/comunicados', \App\Livewire\Admin\ComunicadosCliente::class)->name('admin.comunicados');
    });


});


// ─── Webhooks (sem auth/csrf) ──────────────────────────────────────
// ─── Status público (sem autenticação) ─────────────────────────────
Route::get('/status', fn() => view('status'))->name('status');

Route::post('/webhooks/clicksign', [AssinaturaWebhookController::class, 'handle'])
    ->name('webhooks.clicksign')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login',     PortalLogin::class)->name('login');
    Route::get('/dashboard', PortalDashboard::class)->name('dashboard');
});

// ─── IA (teste) ────────────────────────────────────────────────────
Route::get('/ia-teste', [IAController::class, 'teste'])->middleware('auth:usuarios');
