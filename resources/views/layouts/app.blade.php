<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Sistema Jurídico') — SAPRO</title>
    {{-- Anti-FOUC: aplica tema antes do render --}}
    <script>(function(){var t=localStorage.getItem('sapro-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);}());</script>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SAPRO">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

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
        .nav-group-label {
            font-size: 10px; font-weight: 700; color: rgba(255,255,255,.35); text-transform: uppercase;
            letter-spacing: 1.5px; padding: 0 18px; margin-bottom: 4px;
            display: flex; align-items: center; justify-content: space-between;
            cursor: pointer; user-select: none; transition: color .15s;
        }
        .nav-group-label:hover { color: rgba(255,255,255,.6); }
        .nav-group-label .chevron {
            font-size: 10px; transition: transform .25s ease; display: inline-block;
        }
        .nav-group-label.collapsed .chevron { transform: rotate(-90deg); }
        .nav-group-items {
            overflow: hidden; transition: max-height .28s ease, opacity .2s ease;
            max-height: 500px; opacity: 1;
        }
        .nav-group-items.collapsed { max-height: 0; opacity: 0; }
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
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 52px; background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); overflow: visible; position: relative; z-index: 100; }
        .topbar-title { font-size: 16px; font-weight: 600; color: var(--primary); flex-shrink: 0; }
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

        /* ── PWA / Safe area ── */
        @supports (padding: env(safe-area-inset-bottom)) {
            .sidebar { padding-bottom: env(safe-area-inset-bottom); }
            .content { padding-bottom: calc(16px + env(safe-area-inset-bottom)); }
        }

        /* ── Stat Grid (auto-fit) ── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; margin-bottom: 20px; }

        /* ── Card Actions ── */
        .card-header { flex-wrap: wrap; gap: 8px; }
        .card-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        /* ── Action buttons in tables ── */
        .btn-actions { display: inline-flex; gap: 4px; align-items: center; flex-wrap: wrap; }
        .btn-action {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 6px; text-decoration: none;
            font-size: 15px; border: 1px solid transparent; cursor: pointer;
            background: none; transition: filter .15s;
        }
        .btn-action:hover { filter: brightness(.9); }
        .btn-action-blue   { background: #eff6ff; border-color: #bfdbfe; color: #2563a8; }
        .btn-action-green  { background: #f0fdf4; border-color: #bbf7d0; color: #16a34a; }
        .btn-action-purple { background: #faf5ff; border-color: #e9d5ff; color: #7c3aed; }
        .btn-action-yellow { background: #fffbeb; border-color: #fde68a; color: #d97706; }
        .btn-action-red    { background: #fff1f2; border-color: #fecdd3; color: #e11d48; }

        /* ── Pagination Bar ── */
        .pagination-bar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 12px 0; gap: 8px; font-size: 13px; color: var(--muted); }
        .page-btns { display: flex; gap: 6px; flex-wrap: wrap; }
        .page-btn { padding: 6px 12px; background: var(--white); color: var(--primary); border: 1px solid var(--border); border-radius: 6px; font-size: 12px; cursor: pointer; transition: background .15s; }
        .page-btn:hover:not(:disabled) { background: #eff6ff; }
        .page-btn:disabled { background: #f1f5f9; color: #94a3b8; cursor: default; }
        .page-current { padding: 6px 12px; background: #f1f5f9; border-radius: 6px; font-size: 12px; color: var(--text); }

        /* ── Btn secondary outline ── */
        .btn-secondary-outline { background: #f1f5f9; color: #475569; border: 1.5px solid var(--border); }

        /* ── Filter Bar ── */
        .filter-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; align-items: center; }
        .filter-bar input, .filter-bar select { padding: 7px 10px; border: 1.5px solid var(--border); border-radius: 7px; font-size: 13px; flex: 1; min-width: 120px; max-width: 220px; }
        .filter-bar input[type=text] { flex: 2; min-width: 160px; }
        .filter-bar .filter-actions { display: flex; gap: 6px; margin-left: auto; flex-wrap: wrap; }

        /* ── Responsive visibility ── */
        .hide-sm { display: table-cell; }
        .hide-xs { display: table-cell; }

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
            .hide-sm { display: none !important; }
            .filter-bar input, .filter-bar select { max-width: 100%; }
            .filter-bar .filter-actions { margin-left: 0; width: 100%; justify-content: flex-end; }
            .topbar > div[style*="max-width:440px"] { max-width: 200px; }
        }
        @media (max-width: 480px) {
            .grid-3 { grid-template-columns: 1fr; }
            .topbar-title { font-size: 14px; }
            .card { padding: 14px; }
            .hide-xs { display: none !important; }
            .btn-action { width: 28px; height: 28px; font-size: 13px; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .topbar > div[style*="max-width:440px"] { display: none; }
        }

        /* ── Print ─────────────────────────────────────────────────────────── */
        @media print {
            @page { margin: 1.5cm; }

            /* Oculta elementos de navegação e interação */
            .sidebar, .sidebar-overlay, .topbar, #toast-container,
            .hamburger, .filter-bar, .pagination-bar, .btn-actions,
            .card-actions, #themeToggle,
            button:not(.print-keep), a.btn { display: none !important; }

            /* Layout: remove flex/overflow para impressão linear */
            html, body { height: auto !important; background: #fff !important; }
            .layout { display: block !important; height: auto !important; }
            .main   { display: block !important; }
            .content { overflow: visible !important; padding: 0 !important; }

            /* Cards: sem sombra, sem overflow */
            .card, .stat-card { box-shadow: none !important; border: 1px solid #ccc !important; page-break-inside: avoid; }
            .stat-grid { grid-template-columns: repeat(4, 1fr) !important; }

            /* Tabelas: repete cabeçalho em cada página, sem quebra de linha */
            .table-wrap { overflow: visible !important; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
            thead th { background: #1a3a5c !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            /* Mostra colunas ocultas em telas pequenas */
            .hide-sm, .hide-xs { display: table-cell !important; }

            /* Preserva cores de fundo (badges, status) */
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* ── Dark Mode ─────────────────────────────────────────────────────── */
        [data-theme="dark"] {
            --bg:    #0f172a;
            --white: #1e293b;
            --text:  #e2e8f0;
            --muted: #94a3b8;
            --border:#334155;
        }
        [data-theme="dark"] body { background: var(--bg); }
        [data-theme="dark"] .topbar { box-shadow: 0 1px 4px rgba(0,0,0,.4); }
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea { background: #0f172a; color: var(--text); border-color: var(--border); }
        [data-theme="dark"] tbody tr:nth-child(even) td { background: #243044; }
        [data-theme="dark"] tbody tr:hover td { background: #1e3a5f; }
        [data-theme="dark"] tbody td { border-bottom-color: var(--border); }
        [data-theme="dark"] .btn-secondary { background: #334155; color: var(--text); }
        [data-theme="dark"] .btn-secondary-outline { background: #243044; color: #cbd5e1; border-color: var(--border); }
        [data-theme="dark"] .modal-backdrop { background: rgba(0,0,0,.7); }
        [data-theme="dark"] .alert-success { background: #14532d; color: #86efac; border-color: #166534; }
        [data-theme="dark"] .alert-error   { background: #450a0a; color: #fca5a5; border-color: #991b1b; }
        [data-theme="dark"] .page-btn { background: #1e293b; border-color: var(--border); color: var(--text); }
        [data-theme="dark"] .page-btn:hover:not(:disabled) { background: #243044; }
        [data-theme="dark"] .page-current { background: #243044; color: var(--text); }
        [data-theme="dark"] .filter-bar input,
        [data-theme="dark"] .filter-bar select { background: #0f172a; }

        /* ── Toasts ── */
        #toast-container {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 8px;
            pointer-events: none; max-width: 360px; width: calc(100vw - 40px);
        }
        .toast {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,.18); pointer-events: all;
            animation: toastIn .25s ease; transition: opacity .3s, transform .3s;
            word-break: break-word;
        }
        .toast.hiding { opacity: 0; transform: translateX(20px); }
        .toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .toast-warning { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }
        .toast-info    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .toast-body    { flex: 1; line-height: 1.4; }
        .toast-close   { background: none; border: none; cursor: pointer; font-size: 16px; color: currentColor; opacity: .45; padding: 0; line-height: 1; flex-shrink: 0; }
        .toast-close:hover { opacity: 1; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }
        [data-theme="dark"] .toast-success { background: #14532d; color: #86efac; border-color: #166534; }
        [data-theme="dark"] .toast-error   { background: #450a0a; color: #fca5a5; border-color: #991b1b; }
        [data-theme="dark"] .toast-warning { background: #451a03; color: #fed7aa; border-color: #92400e; }
        [data-theme="dark"] .toast-info    { background: #1e3a8a; color: #bfdbfe; border-color: #1d4ed8; }
        @media (max-width: 480px) { #toast-container { top: 12px; right: 12px; width: calc(100vw - 24px); } }

        /* ── Confirm Modal ── */
        #confirmModal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 9997;
            align-items: center; justify-content: center; padding: 16px;
        }
        #confirmModal .confirm-box {
            background: var(--white); border-radius: 14px; padding: 32px 28px;
            width: 100%; max-width: 400px; text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            animation: toastIn .2s ease;
        }
        #confirmModal .confirm-icon { font-size: 40px; margin-bottom: 14px; }
        #confirmModal .confirm-msg  { font-size: 14px; line-height: 1.6; color: var(--text); margin-bottom: 28px; white-space: pre-line; }
        #confirmModal .confirm-btns { display: flex; gap: 10px; justify-content: center; }
        #confirmModal .confirm-btns button { min-width: 110px; }
        @media print { #confirmModal { display: none !important; } }
    </style>
</head>
<body>
<div class="layout">

    <div id="toast-container"></div>

    {{-- Confirm Modal --}}
    <div id="confirmModal">
        <div class="confirm-box">
            <div class="confirm-icon">⚠️</div>
            <p class="confirm-msg" id="confirmMsg"></p>
            <div class="confirm-btns">
                <button id="confirmCancel" class="btn btn-secondary">Cancelar</button>
                <button id="confirmOk"     class="btn btn-danger">Confirmar</button>
            </div>
        </div>
    </div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">⚖️</div>
            <div class="sidebar-title">SISTEMA JURÍDICO</div>
            <div class="sidebar-sub">Web</div>
        </div>

        @php
            $rota       = request()->route()->getName();
            $perfil     = auth('usuarios')->user()?->perfil ?? 'estagiario';
            $isAdmin    = $perfil === 'admin';
            $isAdvogado = in_array($perfil, ['admin','advogado']);
            $isFinanc   = in_array($perfil, ['admin','financeiro']);
            $canProc    = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canPessoas = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canAgenda  = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canDocs    = in_array($perfil, ['admin','advogado','estagiario']);
        @endphp

        {{-- ── GERAL ── --}}
        <div class="nav-group" data-group="geral">
            <div class="nav-group-label" onclick="toggleGroup('geral')">
                Geral <span class="chevron">▾</span>
            </div>
            <div class="nav-group-items" id="group-geral">
                <a href="{{ route('dashboard') }}" class="nav-item {{ $rota === 'dashboard' ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span> Dashboard
                </a>
                @if($isAdvogado || $isAdmin)
                <a href="{{ route('analytics') }}" class="nav-item {{ $rota === 'analytics' ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Analytics
                </a>
                <a href="{{ route('produtividade') }}" class="nav-item {{ $rota === 'produtividade' ? 'active' : '' }}">
                    <span class="nav-icon">👨‍⚖️</span> Produtividade
                </a>
                @endif
                @if($canAgenda)
                <a href="{{ route('agenda') }}" class="nav-item {{ $rota === 'agenda' ? 'active' : '' }}">
                    <span class="nav-icon">📅</span> Agenda
                </a>
                <a href="{{ route('prazos') }}" class="nav-item {{ $rota === 'prazos' ? 'active' : '' }}">
                    <span class="nav-icon">⏳</span> Prazos
                </a>
                <a href="{{ route('audiencias') }}" class="nav-item {{ $rota === 'audiencias' ? 'active' : '' }}">
                    <span class="nav-icon">🗓️</span> Audiências
                </a>
                @endif
            </div>
        </div>

        {{-- ── PROCESSOS ── --}}
        @if($canProc || $canPessoas || $canDocs)
        <div class="nav-group" data-group="processos">
            <div class="nav-group-label" onclick="toggleGroup('processos')">
                Processos <span class="chevron">▾</span>
            </div>
            <div class="nav-group-items" id="group-processos">
                @if($canProc)
                <a href="{{ route('processos') }}" class="nav-item {{ str_contains($rota ?? '', 'processos') ? 'active' : '' }}">
                    <span class="nav-icon">⚖️</span> Processos
                </a>
                @endif
                @if($canPessoas)
                <a href="{{ route('pessoas') }}" class="nav-item {{ $rota === 'pessoas' ? 'active' : '' }}">
                    <span class="nav-icon">👥</span> Pessoas
                </a>
                <a href="{{ route('correspondentes') }}" class="nav-item {{ request()->is('correspondentes*') ? 'active' : '' }}">
                    <span class="nav-icon">🤝</span> Correspondentes
                </a>
                @endif
                @if($canDocs)
                <a href="{{ route('documentos') }}" class="nav-item {{ request()->is('documentos*') ? 'active' : '' }}">
                    <span class="nav-icon">📁</span> Documentos
                </a>
                @endif
                <a href="{{ route('minutas') }}" class="nav-item {{ $rota === 'minutas' ? 'active' : '' }}">
                    <span class="nav-icon">📄</span> Minutas
                </a>
                <a href="{{ route('assinatura-digital') }}" class="nav-item {{ request()->is('assinatura-digital*') ? 'active' : '' }}">
                    <span class="nav-icon">✍️</span> Assinatura Digital
                </a>
            </div>
        </div>
        @endif

        {{-- ── FINANCEIRO ── --}}
        @if($isFinanc)
        <div class="nav-group" data-group="financeiro">
            <div class="nav-group-label" onclick="toggleGroup('financeiro')">
                Financeiro <span class="chevron">▾</span>
            </div>
            <div class="nav-group-items" id="group-financeiro">
                <a href="{{ route('financeiro.consolidado') }}" class="nav-item {{ $rota === 'financeiro.consolidado' ? 'active' : '' }}">
                    <span class="nav-icon">💰</span> Visão Geral
                </a>
                <a href="{{ route('financeiro') }}" class="nav-item {{ $rota === 'financeiro' ? 'active' : '' }}">
                    <span class="nav-icon">💳</span> Por Processo
                </a>
                <a href="{{ route('honorarios') }}" class="nav-item {{ request()->is('honorarios*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span> Honorários
                </a>
                <a href="{{ route('inadimplencia') }}" class="nav-item {{ request()->is('inadimplencia*') ? 'active' : '' }}">
                    <span class="nav-icon">⚠️</span> Inadimplência
                </a>
                @if($isAdvogado || $isFinanc)
                <a href="{{ route('relatorios.index') }}" class="nav-item {{ str_contains($rota ?? '', 'relatorios') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Relatórios
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- ── FERRAMENTAS ── --}}
        @if($isAdvogado)
        <div class="nav-group" data-group="ferramentas">
            <div class="nav-group-label" onclick="toggleGroup('ferramentas')">
                Ferramentas <span class="chevron">▾</span>
            </div>
            <div class="nav-group-items" id="group-ferramentas">
                <a href="{{ route('calculadora') }}" class="nav-item {{ $rota === 'calculadora' ? 'active' : '' }}">
                    <span class="nav-icon">🧮</span> Calculadora
                </a>
                <a href="{{ route('tjsp') }}" class="nav-item {{ $rota === 'tjsp' ? 'active' : '' }}">
                    <span class="nav-icon">🏛️</span> Consulta Judicial
                </a>
                <a href="{{ route('aasp-publicacoes') }}" class="nav-item {{ $rota === 'aasp-publicacoes' ? 'active' : '' }}">
                    <span class="nav-icon">📰</span> Publicações AASP
                </a>
                <a href="{{ route('assistente') }}" class="nav-item {{ request()->is('assistente*') ? 'active' : '' }}">
                    <span class="nav-icon">🤖</span> Assistente IA
                </a>
                @if(!$isFinanc)
                <a href="{{ route('relatorios.index') }}" class="nav-item {{ str_contains($rota ?? '', 'relatorios') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Relatórios
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- ── PORTAL ── --}}
        @if($isAdmin)
        <div class="nav-group" data-group="portal">
            <div class="nav-group-label" onclick="toggleGroup('portal')">
                Portal Cliente <span class="chevron">▾</span>
            </div>
            <div class="nav-group-items" id="group-portal">
                <a href="{{ route('admin.portal-acesso') }}" class="nav-item {{ $rota === 'admin.portal-acesso' ? 'active' : '' }}">
                    <span class="nav-icon">🌐</span> Acessos
                </a>
                <a href="{{ route('admin.portal-mensagens') }}" class="nav-item {{ $rota === 'admin.portal-mensagens' ? 'active' : '' }}">
                    <span class="nav-icon">💬</span> Mensagens
                </a>
                <a href="{{ route('admin.notificacoes-whatsapp') }}" class="nav-item {{ request()->is('admin/notificacoes-whatsapp*') ? 'active' : '' }}">
                    <span class="nav-icon">📲</span> WhatsApp/SMS
                </a>
            </div>
        </div>
        @endif

        {{-- ── ADMINISTRAÇÃO ── --}}
        @if($isAdmin)
        <div class="nav-group" data-group="admin">
            <div class="nav-group-label" onclick="toggleGroup('admin')">
                Administração <span class="chevron">▾</span>
            </div>
            <div class="nav-group-items" id="group-admin">
                <a href="{{ route('usuarios') }}" class="nav-item {{ $rota === 'usuarios' ? 'active' : '' }}">
                    <span class="nav-icon">👨‍💼</span> Usuários
                </a>
                <a href="{{ route('tabelas') }}" class="nav-item {{ $rota === 'tabelas' ? 'active' : '' }}">
                    <span class="nav-icon">🗂️</span> Tabelas
                </a>
                <a href="{{ route('indices') }}" class="nav-item {{ $rota === 'indices' ? 'active' : '' }}">
                    <span class="nav-icon">📈</span> Índices
                </a>
                <a href="{{ route('auditoria') }}" class="nav-item {{ $rota === 'auditoria' ? 'active' : '' }}">
                    <span class="nav-icon">🔍</span> Auditoria
                </a>
            </div>
        </div>
        @endif

        {{-- ── CONTA ── --}}
        <div class="nav-group">
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
            <span class="topbar-title" style="flex-shrink:0;">@yield('page-title', 'Dashboard')</span>
            <div style="flex:1;display:flex;justify-content:center;padding:0 16px;max-width:440px;margin:0 auto;">
                @livewire('busca-global')
            </div>
            <div class="topbar-user">
                <button id="themeToggle" onclick="toggleTheme()" title="Alternar tema claro/escuro (Theme)"
                    style="background:none;border:none;cursor:pointer;font-size:18px;padding:4px 6px;color:var(--muted);line-height:1;flex-shrink:0;">
                    🌙
                </button>
                <button onclick="window.print()" title="Imprimir página (Ctrl+P)"
                    style="background:none;border:1.5px solid var(--border);cursor:pointer;font-size:13px;padding:2px 7px;color:var(--muted);line-height:1.6;border-radius:5px;flex-shrink:0;">
                    🖨️
                </button>
                <button onclick="document.dispatchEvent(new KeyboardEvent('keydown',{key:'?',bubbles:true}))"
                    title="Atalhos de teclado (?)"
                    style="background:none;border:1.5px solid var(--border);cursor:pointer;font-size:11px;font-weight:700;padding:2px 7px;color:var(--muted);line-height:1.6;border-radius:5px;flex-shrink:0;">
                    ?
                </button>
                @livewire('notificacoes-bell')
                <div class="avatar">{{ strtoupper(substr(auth('usuarios')->user()?->nome ?? 'U', 0, 2)) }}</div>
                <span style="display:none" class="topbar-username">{{ auth('usuarios')->user()?->nome }}</span>
            </div>
        </div>

        <div class="content">
            @yield('content')
            @yield('conteudo')
        </div>
    </div>
</div>

<script>
    // ── Sidebar mobile ──
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

    // ── Accordion ──
    const STORAGE_KEY = 'navGroups';

    function getState() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch { return {}; }
    }
    function saveState(state) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    }

    function toggleGroup(group) {
        const items = document.getElementById('group-' + group);
        const label = items?.previousElementSibling;
        if (!items) return;
        const isCollapsed = items.classList.toggle('collapsed');
        label?.classList.toggle('collapsed', isCollapsed);
        const state = getState();
        state[group] = !isCollapsed; // true = open
        saveState(state);
    }

    function initAccordion() {
        const state   = getState();
        const groups  = document.querySelectorAll('.nav-group[data-group]');

        groups.forEach(g => {
            const group = g.dataset.group;
            const items = document.getElementById('group-' + group);
            const label = items?.previousElementSibling;
            if (!items) return;

            // Abre apenas se tiver item ativo ou se o usuário abriu manualmente antes
            const hasActive = items.querySelector('.nav-item.active');
            const userOpened = Object.prototype.hasOwnProperty.call(state, group) && state[group] === true;
            const shouldOpen = hasActive || userOpened;

            if (!shouldOpen) {
                items.classList.add('collapsed');
                label?.classList.add('collapsed');
            }
        });
    }

    initAccordion();

    // ── Toasts ──
    const TOAST_ICONS = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    window.toast = function (message, type = 'success', duration = 4000) {
        const el = document.createElement('div');
        el.className = 'toast toast-' + type;
        el.innerHTML = '<span class="toast-body">' + (TOAST_ICONS[type] || '') + ' ' + message + '</span>'
                     + '<button class="toast-close" onclick="this.closest(\'.toast\').remove()">×</button>';
        document.getElementById('toast-container').appendChild(el);
        if (duration > 0) {
            setTimeout(() => {
                el.classList.add('hiding');
                setTimeout(() => el.remove(), 320);
            }, duration);
        }
    };
    // Session flash → toast
    @if(session('sucesso') || session('success'))
        toast(@json(session('sucesso') ?? session('success')), 'success');
    @endif
    @if(session('erro') || session('error'))
        toast(@json(session('erro') ?? session('error')), 'error');
    @endif
    // Livewire events → toast
    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', ({ message, type = 'success', duration = 4000 }) => {
            toast(message, type, duration);
        });
    });

    // ── Dark Mode ──
    function toggleTheme() {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('sapro-theme', next);
        document.getElementById('themeToggle').textContent = next === 'dark' ? '☀️' : '🌙';
    }
    (function () {
        const t = document.documentElement.getAttribute('data-theme') || 'light';
        const btn = document.getElementById('themeToggle');
        if (btn) btn.textContent = t === 'dark' ? '☀️' : '🌙';
    }());

    // ── Custom Confirm Modal (substitui wire:confirm nativo) ──
    (function () {
        const modal     = document.getElementById('confirmModal');
        const msgEl     = document.getElementById('confirmMsg');
        const btnOk     = document.getElementById('confirmOk');
        const btnCancel = document.getElementById('confirmCancel');
        let _resolve = null;

        function close(result) {
            modal.style.display = 'none';
            if (_resolve) { _resolve(result); _resolve = null; }
        }

        btnOk.addEventListener('click',     () => close(true));
        btnCancel.addEventListener('click', () => close(false));
        modal.addEventListener('click', e => { if (e.target === modal) close(false); });
        document.addEventListener('keydown', e => {
            if (modal.style.display !== 'flex') return;
            if (e.key === 'Escape') { e.preventDefault(); close(false); }
            if (e.key === 'Enter')  { e.preventDefault(); close(true);  }
        });

        // Livewire 3 faz `await window.confirm(msg)`, então uma Promise funciona
        window.confirm = function (message) {
            msgEl.textContent = message;
            const isDelete = /exclu|remov|apag|delet/i.test(message);
            btnOk.textContent  = isDelete ? 'Excluir' : 'Confirmar';
            btnOk.className    = 'btn ' + (isDelete ? 'btn-danger' : 'btn-primary');
            modal.style.display = 'flex';
            setTimeout(() => btnCancel.focus(), 50); // foco no "Cancelar" por segurança
            return new Promise(r => { _resolve = r; });
        };
    }());

    // ── Keyboard Shortcuts ──
    (function () {
        function inTextField() {
            const t = document.activeElement?.tagName;
            return t === 'INPUT' || t === 'TEXTAREA' || t === 'SELECT' || document.activeElement?.isContentEditable;
        }

        document.addEventListener('keydown', function (e) {
            // Ignora combinações com Ctrl/Meta (não interfere com atalhos do browser/OS)
            if (e.ctrlKey || e.metaKey) return;

            // / → foca busca global
            if (e.key === '/' && !inTextField()) {
                e.preventDefault();
                const input = document.querySelector('.topbar input[type="text"]');
                if (input) { input.focus(); input.select(); }
                return;
            }

            // Esc → fecha modal aberto ou sidebar mobile
            if (e.key === 'Escape') {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    const close = backdrop.querySelector('.modal-close');
                    close ? close.click() : backdrop.click();
                    return;
                }
                if (document.getElementById('sidebar')?.classList.contains('open')) {
                    toggleSidebar();
                }
                return;
            }

            // Alt+N → clica no primeiro botão "Novo" da página
            if (e.altKey && (e.key === 'n' || e.key === 'N') && !inTextField()) {
                e.preventDefault();
                const content = document.querySelector('.content');
                if (!content) return;
                const btn = [...content.querySelectorAll('button, a')]
                    .find(el => /novo|new/i.test(el.textContent.trim()));
                if (btn) btn.click();
                return;
            }

            // ? → exibe atalhos disponíveis
            if (e.key === '?' && !inTextField()) {
                toast(
                    '<strong>Atalhos de teclado</strong><br>' +
                    '<code style="font-size:11px">/</code> Busca global &nbsp;|&nbsp; ' +
                    '<code style="font-size:11px">Esc</code> Fechar modal &nbsp;|&nbsp; ' +
                    '<code style="font-size:11px">Alt+N</code> Novo registro',
                    'info', 6000
                );
            }
        });
    }());

    // ── Service Worker (PWA) ──
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
</script>
@livewireScripts
</body>
</html>
