<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Jurídico') — Web</title>
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #1a3a5c;
            --primary-light:#2563a8;
            --accent:       #e8a020;
            --bg:           #f0f4f8;
            --sidebar:      #0f2540;
            --white:        #ffffff;
            --text:         #1e293b;
            --muted:        #64748b;
            --border:       #cbd5e1;
            --success:      #16a34a;
            --danger:       #dc2626;
            --warning:      #d97706;
        }

        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--bg); color: var(--text); }
        .layout { display: flex; height: 100vh; overflow: hidden; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px; background: var(--sidebar); color: #fff;
            display: flex; flex-direction: column; flex-shrink: 0; overflow-y: auto;
            transition: transform .3s ease; z-index: 100;
        }
        .sidebar-header { padding: 20px 18px 16px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-logo { font-size: 22px; margin-bottom: 4px; }
        .sidebar-title { font-size: 20px; font-weight: 700; letter-spacing: -.3px; }
        .sidebar-sub { font-size: 10px; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: 1.5px; }

        .nav-group { padding: 12px 0 4px; }
        .nav-group-label { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: 1.5px; padding: 0 18px; margin-bottom: 4px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px; padding: 9px 18px;
            font-size: 13.5px; color: rgba(255,255,255,.75); cursor: pointer;
            border-left: 3px solid transparent; text-decoration: none; transition: all .15s;
        }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,.05); }
        .nav-item.active { color: var(--accent); background: rgba(232,160,32,.12); border-left-color: var(--accent); font-weight: 600; }
        .nav-icon { width: 20px; text-align: center; font-size: 16px; }

        .perfil-badge {
            display: inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .5px; margin-top: 4px;
        }
        .sidebar-footer { margin-top: auto; padding: 12px 18px; border-top: 1px solid rgba(255,255,255,.08); font-size: 12px; color: rgba(255,255,255,.4); }

        /* ── Overlay mobile ── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 99; }
        .sidebar-overlay.active { display: block; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; font-size: 22px; color: var(--primary); padding: 4px 8px; }

        /* ── Main ── */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { height: 52px; background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        .topbar-title { font-size: 16px; font-weight: 600; color: var(--primary); flex: 1; }
        .topbar-user { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 8px; }
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
        .content { flex: 1; overflow: auto; padding: 24px; }

        /* ── Cards ── */
        .card { background: var(--white); border-radius: 10px; padding: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.07); border: 1px solid var(--border); }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .card-title { font-size: 15px; font-weight: 600; color: var(--primary); }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }

        /* ── Stat Cards ── */
        .stat-card { background: var(--white); border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 6px rgba(0,0,0,.07); border: 1px solid var(--border); border-left-width: 4px; }
        .stat-val { font-size: 28px; font-weight: 700; color: var(--primary); }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .stat-icon { font-size: 22px; margin-bottom: 4px; }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: var(--primary); color: #fff; padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; letter-spacing: .3px; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tbody tr:hover td { background: #eff6ff; }
        tbody td { padding: 9px 14px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border: none; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity .15s; text-decoration: none; }
        .btn:hover { opacity: .88; }
        .btn-primary   { background: var(--primary);  color: #fff; }
        .btn-secondary { background: var(--border);   color: var(--text); }
        .btn-success   { background: var(--success);  color: #fff; }
        .btn-danger    { background: var(--danger);   color: #fff; }
        .btn-outline   { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 4px 8px; font-size: 15px; background: transparent; border: none; cursor: pointer; }

        /* ── Forms ── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .form-field { display: flex; flex-direction: column; gap: 5px; }
        label.lbl { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
        input, select, textarea { padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 7px; font-size: 13px; color: var(--text); outline: none; width: 100%; background: var(--white); font-family: inherit; transition: border-color .2s; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary-light); }
        .invalid-feedback { color: var(--danger); font-size: 11px; margin-top: 3px; }

        /* ── Search bar ── */
        .search-bar { display: flex; gap: 8px; margin-bottom: 16px; }
        .search-bar input  { flex: 1; }
        .search-bar select { width: 160px; }

        /* ── Alert / Flash ── */
        .alert { padding: 10px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* ── Modal ── */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 50; display: flex; align-items: center; justify-content: center; }
        .modal { background: var(--white); border-radius: 12px; padding: 28px; width: 660px; max-width: 96vw; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .modal-title { font-size: 16px; font-weight: 700; color: var(--primary); }
        .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--muted); }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 4px; margin-top: 16px; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; color: var(--primary); border: 1px solid var(--border); }
        .pagination span[aria-current] { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Utilities ── */
        .text-primary { color: var(--primary-light); font-weight: 600; }
        .mb-4 { margin-bottom: 16px; }
        .gap-2 { gap: 8px; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }

        @media (max-width: 1024px) {
            .grid-3 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { position: fixed; top: 0; left: 0; bottom: 0; transform: translateX(-100%); width: 260px; }
            .sidebar.open { transform: translateX(0); }
            .hamburger { display: block; }
            .grid-2 { grid-template-columns: 1fr; }
            .grid-3 { grid-template-columns: 1fr 1fr; }
            .content { padding: 16px; }
            .topbar { padding: 0 16px; }
            .stat-val { font-size: 22px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-grid-3 { grid-template-columns: 1fr; }
            .search-bar { flex-direction: column; }
            .search-bar select { width: 100%; }
            .modal { padding: 20px; border-radius: 0; width: 100%; max-width: 100vw; height: 100vh; max-height: 100vh; }
        }
        @media (max-width: 480px) {
            .grid-3 { grid-template-columns: 1fr; }
            .topbar-title { font-size: 14px; }
            .card { padding: 14px; }
        }
    </style>
</head>
<body>
<div class="layout">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">⚖️</div>
            <div class="sidebar-title">SISTEMA JURÍDICO</div>
            <div class="sidebar-sub">Web</div>
        </div>

        @php
            $rota        = request()->route()->getName();
            $perfil      = auth('usuarios')->user()?->perfil ?? 'estagiario';
            $isAdmin     = $perfil === 'admin';
            $isAdvogado  = in_array($perfil, ['admin','advogado']);
            $isFinanc    = in_array($perfil, ['admin','financeiro']);
            $canProc     = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canPessoas  = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canAgenda   = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canDocs     = in_array($perfil, ['admin','advogado','estagiario']);
        @endphp

        <div class="nav-group">
            <div class="nav-group-label">Principal</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ $rota === 'dashboard' ? 'active' : '' }}">
                <span class="nav-icon">🏠</span> Dashboard
            </a>
            @if($canAgenda)
            <a href="{{ route('agenda') }}" class="nav-item {{ $rota === 'agenda' ? 'active' : '' }}">
                <span class="nav-icon">📅</span> Agenda
            </a>
            @endif
            @if($isAdvogado || $isFinanc)
            <a href="{{ route('relatorios.index') }}" class="nav-item {{ str_contains($rota ?? '', 'relatorios') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Relatórios
            </a>
            @endif
            @if($isAdvogado)
            <a href="{{ route('tjsp') }}" class="nav-item {{ $rota === 'tjsp' ? 'active' : '' }}">
                <span class="nav-icon">🏛️</span> Consulta TJSP
            </a>
            <a href="{{ route('assistente') }}" class="nav-item {{ request()->is('assistente*') ? 'active' : '' }}">
                <span class="nav-icon">🤖</span> Assistente IA
            </a>
            @endif
            @if($canDocs)
            <a href="{{ route('documentos') }}" class="nav-item {{ request()->is('documentos*') ? 'active' : '' }}">
                <span class="nav-icon">📁</span> Documentos
            </a>
            @endif
        </div>

        <div class="nav-group">
            <div class="nav-group-label">Cadastros</div>
            @if($canPessoas)
            <a href="{{ route('pessoas') }}" class="nav-item {{ str_contains($rota ?? '', 'pessoas') ? 'active' : '' }}">
                <span class="nav-icon">👥</span> Pessoas
            </a>
            @endif
            @if($canProc)
            <a href="{{ route('processos') }}" class="nav-item {{ str_contains($rota ?? '', 'processos') ? 'active' : '' }}">
                <span class="nav-icon">⚖️</span> Processos
            </a>
            @endif
        </div>

        <div class="nav-group">
            <div class="nav-group-label">Módulos</div>
            @if($isFinanc)
            <a href="{{ route('financeiro') }}" class="nav-item {{ $rota === 'financeiro' ? 'active' : '' }}">
                <span class="nav-icon">💳</span> Financeiro
            </a>
            <a href="{{ route('honorarios') }}" class="nav-item {{ request()->is('honorarios*') ? 'active' : '' }}">
                <span class="nav-icon">💰</span> Honorários
            </a>
            @endif
            @if($isAdmin)
            <a href="{{ route('tabelas') }}" class="nav-item {{ $rota === 'tabelas' ? 'active' : '' }}">
                <span class="nav-icon">🗂️</span> Tabelas
            </a>
            <a href="{{ route('indices') }}" class="nav-item {{ $rota === 'indices' ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Índices
            </a>
            @endif
        </div>

        <div class="nav-group">
            <div class="nav-group-label">Sistema</div>
            @if($isAdmin)
            <a href="{{ route('usuarios') }}" class="nav-item {{ $rota === 'usuarios' ? 'active' : '' }}">
                <span class="nav-icon">👨‍💼</span> Usuários
            </a>
            <a href="{{ route('auditoria') }}" class="nav-item {{ $rota === 'auditoria' ? 'active' : '' }}">
                <span class="nav-icon">🔍</span> Auditoria
            </a>
            <a href="{{ route('admin.portal-acesso') }}" class="nav-item {{ $rota === 'admin.portal-acesso' ? 'active' : '' }}">
                <span class="nav-icon">🌐</span> Portal Acesso
            </a>
            @endif
            <a href="{{ route('minha-conta') }}" class="nav-item {{ $rota === 'minha-conta' ? 'active' : '' }}">
                <span class="nav-icon">👤</span> Minha Conta
            </a>
        </div>

        <div class="sidebar-footer">
            {{ auth('usuarios')->user()?->nome ?? 'Usuário' }}<br>
            @php
                $perfilCores  = ['admin'=>'#dc2626','advogado'=>'#2563a8','estagiario'=>'#7c3aed','financeiro'=>'#16a34a','recepcionista'=>'#d97706'];
                $perfilLabels = ['admin'=>'Administrador','advogado'=>'Advogado','estagiario'=>'Estagiário','financeiro'=>'Financeiro','recepcionista'=>'Recepcionista'];
                $cor   = $perfilCores[$perfil]  ?? '#64748b';
                $label = $perfilLabels[$perfil] ?? $perfil;
            @endphp
            <span class="perfil-badge" style="background:{{ $cor }}30;color:{{ $cor }};border:1px solid {{ $cor }}50;">
                {{ $label }}
            </span><br><br>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#f87171;font-size:12px;padding:0;">
                    ⏻ Sair
                </button>
            </form>
        </div>
    </nav>

    <div class="main">
        <div class="topbar">
            <button class="hamburger" onclick="toggleSidebar()">☰</button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            <div class="topbar-user">
                <div class="avatar">{{ strtoupper(substr(auth('usuarios')->user()?->nome ?? 'U', 0, 2)) }}</div>
                <span style="display:none" class="topbar-username">{{ auth('usuarios')->user()?->nome }}</span>
            </div>
        </div>

        <div class="content">
            @if(session('sucesso') || session('success'))
                <div class="alert alert-success">✅ {{ session('sucesso') ?? session('success') }}</div>
            @endif
            @if(session('erro') || session('error'))
                <div class="alert alert-error">❌ {{ session('erro') ?? session('error') }}</div>
            @endif
            @yield('content')
            @yield('conteudo')
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => { if (window.innerWidth <= 768) toggleSidebar(); });
    });
    function checkWidth() {
        const u = document.querySelector('.topbar-username');
        if (u) u.style.display = window.innerWidth > 480 ? 'inline' : 'none';
    }
    checkWidth();
    window.addEventListener('resize', checkWidth);
</script>
@livewireScripts
</body>
</html>
