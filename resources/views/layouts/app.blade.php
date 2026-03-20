<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    @php
        $prazosHoje = 0;
        if (auth('usuarios')->check()) {
            try {
                $prazosHoje = \Illuminate\Support\Facades\DB::table('prazos')
                    ->whereDate('data_prazo', today())
                    ->where('status', 'aberto')
                    ->count();
            } catch (\Exception $e) {}
        }
    @endphp
    <title>{{ $prazosHoje > 0 ? "({$prazosHoje}) " : '' }}@yield('page-title', 'Dashboard') — JURÍDICO</title>
    {{-- Anti-FOUC: aplica tema antes do render --}}
    <script>(function(){var t=localStorage.getItem('sapro-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);}());</script>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="JURÍDICO">
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

        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); }
        .layout { display: flex; height: 100vh; overflow: hidden; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px; background: var(--sidebar); color: #fff;
            display: flex; flex-direction: column; flex-shrink: 0; overflow-y: auto;
            transition: transform .3s ease; z-index: 100;
        }
        .sidebar-header { padding: 20px 18px 16px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-logo { margin-bottom: 4px; display:flex; align-items:center; }
        .sidebar-title { font-size: 20px; font-weight: 700; letter-spacing: -.3px; }
        .sidebar-sub { font-size: 10px; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: 1.5px; }

        .nav-pinned { padding: 6px 0 4px; }
        .nav-sep { height: 1px; background: rgba(255,255,255,.08); margin: 6px 18px 10px; }
        .nav-group { padding: 2px 0 4px; }
        .nav-group-label {
            font-size: 10px; font-weight: 700; color: rgba(255,255,255,.4); text-transform: uppercase;
            letter-spacing: 1.2px; padding: 6px 18px 5px;
            display: flex; align-items: center; justify-content: space-between;
            cursor: pointer; user-select: none; transition: color .15s;
        }
        .nav-group-label:hover { color: rgba(255,255,255,.7); }
        .nav-group-label .chevron { font-size: 9px; transition: transform .22s ease; display: inline-block; opacity:.6; }
        .nav-group-label.collapsed .chevron { transform: rotate(-90deg); }
        .nav-group-dot { display:inline-block; width:6px; height:6px; border-radius:2px; flex-shrink:0; margin-right:1px; }
        .nav-group-items {
            overflow: hidden; transition: max-height .28s ease, opacity .2s ease;
            max-height: 500px; opacity: 1;
        }
        .nav-group-items.collapsed { max-height: 0; opacity: 0; }
        .nav-item {
            display: flex; align-items: center; gap: 10px; padding: 8px 18px;
            font-size: 13px; color: rgba(255,255,255,.7); cursor: pointer;
            border-left: 3px solid transparent; text-decoration: none; transition: all .15s;
        }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,.05); }
        .nav-item.active { color: var(--accent); background: rgba(232,160,32,.12); border-left-color: var(--accent); font-weight: 600; }
        .nav-icon { width: 20px; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }

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
        .stat-icon { display: inline-flex; align-items: center; margin-bottom: 4px; }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: var(--bg); color: var(--muted); padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; border-bottom: 2px solid var(--border); }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tbody tr:hover td { background: #eff6ff; }
        tbody td { padding: 9px 14px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border: none; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity .15s; text-decoration: none; }
        .btn:hover { filter: brightness(.92); transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
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
        label.lbl { font-size: 12px; font-weight: 600; color: var(--muted); }
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
        .btn-action-blue   { background: #eff6ff; border-color: #bfdbfe; color: var(--primary-light); }
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

        /* ── Skeleton Loader ── */
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position:  400px 0; }
        }
        .skeleton-card {
            border-radius: 10px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 800px 100%;
            animation: shimmer 1.4s infinite linear;
        }
        [data-theme="dark"] .skeleton-card {
            background: linear-gradient(90deg, #1e293b 25%, #243044 50%, #1e293b 75%);
            background-size: 800px 100%;
        }

        /* ── Empty State ── */
        .empty-state { padding: 56px 24px; text-align: center; color: var(--muted); }
        .empty-state-icon { display: flex; justify-content: center; margin-bottom: 14px; opacity: .3; }
        .empty-state-title { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .empty-state-sub { font-size: 13px; color: var(--muted); line-height: 1.5; }
        .empty-state-action { margin-top: 16px; }

        /* ── Quick Add ── */
        .quick-add { position: relative; flex-shrink: 0; }
        .quick-add-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; background: var(--primary); color: #fff;
            border: none; border-radius: 7px; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: filter .15s, transform .12s;
            white-space: nowrap;
        }
        .quick-add-btn:hover { filter: brightness(.88); transform: translateY(-1px); }
        .quick-add-btn:active { transform: translateY(0); }
        .quick-add-menu {
            position: absolute; top: calc(100% + 6px); left: 0;
            background: var(--white); border: 1px solid var(--border);
            border-radius: 10px; box-shadow: 0 8px 28px rgba(0,0,0,.14);
            min-width: 200px; z-index: 500; padding: 6px;
            display: none;
        }
        .quick-add-menu.open { display: block; animation: toastIn .15s ease; }
        .quick-add-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; font-size: 13px; color: var(--text);
            text-decoration: none; border-radius: 7px; transition: background .12s;
        }
        .quick-add-item:hover { background: var(--bg); color: var(--primary); }
        .quick-add-item .qa-icon { color: var(--primary-light); flex-shrink: 0; }
        .quick-add-sep { height: 1px; background: var(--border); margin: 4px 8px; }
        @media (max-width: 480px) { .quick-add .qa-label { display: none; } }

        /* ── Breadcrumb ── */
        .breadcrumb-bar { background: var(--white); border-bottom: 1px solid var(--border); padding: 0 24px; height: 34px; display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--muted); }
        .breadcrumb-bar a { color: var(--muted); text-decoration: none; transition: color .15s; }
        .breadcrumb-bar a:hover { color: var(--primary-light); }
        .breadcrumb-bar .sep { color: var(--border); font-size: 10px; }
        .breadcrumb-bar .current { color: var(--text); font-weight: 600; }
        @media (max-width: 768px) { .breadcrumb-bar { padding: 0 16px; } }
        @media print { .breadcrumb-bar { display: none !important; } }

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
            thead th { background: #f0f4f8 !important; color: #475569 !important; border-bottom: 2px solid #cbd5e1 !important; }

            /* Mostra colunas ocultas em telas pequenas */
            .hide-sm, .hide-xs { display: table-cell !important; }

            /* Preserva cores de fundo (badges, status) */
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* ── Dark Mode ─────────────────────────────────────────────────────── */
        [data-theme="dark"] {
            --bg:           #0f172a;
            --white:        #1e293b;
            --text:         #e2e8f0;
            --muted:        #94a3b8;
            --border:       #334155;
            --primary:      #7db3e8;
            --primary-light:#93c5fd;
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

        /* ── Livewire Progress Bar ── */
        #lw-bar {
            position: fixed; top: 0; left: 0; height: 3px;
            background: var(--accent); z-index: 10000;
            width: 0; opacity: 0; pointer-events: none; transition: opacity .3s;
        }
        #lw-bar.running { opacity: 1; animation: lwGrow 2s ease-in-out forwards; }
        #lw-bar.done { width: 100% !important; opacity: 0; transition: width .15s ease, opacity .3s .15s; }
        @keyframes lwGrow { 0% { width: 0 } 50% { width: 65% } 100% { width: 90% } }

        /* ── Spin icon (botões salvando) ── */
        .spin-icon { animation: spin .65s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

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
<div id="lw-bar"></div>
<div class="layout">

    <div id="toast-container"></div>

    {{-- Confirm Modal --}}
    <div id="confirmModal">
        <div class="confirm-box">
            <div class="confirm-icon" style="display:flex;justify-content:center;margin-bottom:8px;"><svg aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
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
            <div class="sidebar-logo"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M3 9l9-6 9 6M3 9h18M7 21h10"/><path d="M5 9l2 6H3L5 9zM19 9l2 6h-4l2-6z"/></svg></div>
            <div class="sidebar-title">SISTEMA JURÍDICO</div>
            <div class="sidebar-sub">Web</div>
        </div>

        @php
            $rota       = request()->route()->getName();
            $perfil     = auth('usuarios')->user()?->perfil ?? 'estagiario';
            $isAdmin    = $perfil === 'admin';
            $isAdvogado = in_array($perfil, ['admin','advogado']);
            $isFinanc   = in_array($perfil, ['admin','financeiro']);

            // Detecta o grupo ativo para destacar o item de menu
            $hubAtivo = '';
            $rotasProcessos  = ['processos','processos.novo','processos.editar','processos.show','pessoas','correspondentes','procuracoes','documentos','minutas','assinatura-digital','audiencias','prazos','agenda','processos.hub'];
            $rotasFinanceiro = ['financeiro','financeiro.consolidado','honorarios','conciliacao-bancaria','inadimplencia','relatorios.index','analytics','produtividade','financeiro.hub'];
            $rotasFerramentas= ['tjsp','assistente','aasp-publicacoes','calculadora','monitoramento','crm','ferramentas.hub'];
            $rotasAdmin      = ['usuarios','tabelas','administradoras','indices','auditoria','admin.portal-acesso','admin.portal-mensagens','admin.notificacoes-whatsapp','admin.hub'];
            if (in_array($rota, $rotasProcessos))   $hubAtivo = 'processos';
            if (in_array($rota, $rotasFinanceiro))  $hubAtivo = 'financeiro';
            if (in_array($rota, $rotasFerramentas)) $hubAtivo = 'ferramentas';
            if (in_array($rota, $rotasAdmin))       $hubAtivo = 'admin';

            // Variáveis de permissão ainda usadas no topbar / quick-add
            $canProc    = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canPessoas = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canAgenda  = in_array($perfil, ['admin','advogado','estagiario','recepcionista']);
            $canDocs    = in_array($perfil, ['admin','advogado','estagiario']);
        @endphp

        {{-- Dashboard --}}
        <div style="padding:10px 0 4px;">
            <a href="{{ route('dashboard') }}" class="nav-item {{ $rota === 'dashboard' ? 'active' : '' }}" style="font-size:14px;font-weight:600;">
                <span class="nav-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                Dashboard
            </a>
        </div>

        <div style="height:1px;background:rgba(255,255,255,.08);margin:2px 18px 8px;"></div>

        {{-- Processos --}}
        <a href="{{ route('processos.hub') }}"
           class="nav-item {{ $hubAtivo === 'processos' ? 'active' : '' }}"
           style="font-size:14px;font-weight:600;padding:10px 18px;">
            <span class="nav-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M3 9l9-6 9 6M3 9h18M7 21h10"/><path d="M5 9l2 6H3L5 9zM19 9l2 6h-4l2-6z"/></svg></span>
            Processos
        </a>

        {{-- Financeiro --}}
        @if($isFinanc)
        <a href="{{ route('financeiro.hub') }}"
           class="nav-item {{ $hubAtivo === 'financeiro' ? 'active' : '' }}"
           style="font-size:14px;font-weight:600;padding:10px 18px;">
            <span class="nav-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="6" y1="15" x2="10" y2="15"/></svg></span>
            Financeiro
        </a>
        @endif

        {{-- Ferramentas --}}
        @if($isAdvogado)
        <a href="{{ route('ferramentas.hub') }}"
           class="nav-item {{ $hubAtivo === 'ferramentas' ? 'active' : '' }}"
           style="font-size:14px;font-weight:600;padding:10px 18px;">
            <span class="nav-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg></span>
            Ferramentas
        </a>
        @endif

        {{-- Administração --}}
        @if($isAdmin)
        <a href="{{ route('admin.hub') }}"
           class="nav-item {{ $hubAtivo === 'admin' ? 'active' : '' }}"
           style="font-size:14px;font-weight:600;padding:10px 18px;">
            <span class="nav-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg></span>
            Administração
        </a>
        @endif

        <div style="height:1px;background:rgba(255,255,255,.08);margin:8px 18px 6px;"></div>

        {{-- Minha Conta --}}
        <a href="{{ route('minha-conta') }}" class="nav-item {{ $rota === 'minha-conta' ? 'active' : '' }}" style="font-size:13px;">
            <span class="nav-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
            Minha Conta
        </a>

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
            <button class="hamburger" onclick="toggleSidebar()" aria-label="Abrir menu" style="display:inline-flex;align-items:center;justify-content:center;"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <span class="topbar-title" style="flex-shrink:0;">@yield('page-title', 'Dashboard')</span>
            @if($canProc)
            
	<div class="quick-add" id="quickAdd">
                

{{--
		<button class="quick-add-btn" onclick="toggleQuickAdd(event)" aria-label="Criar novo item" title="Ações rápidas">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span class="qa-label">Novo</span>
                    <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </button>


--}}

                <div class="quick-add-menu" id="quickAddMenu">
                    @if($canProc)
                    <a href="{{ route('processos') }}" class="quick-add-item">
                        <span class="qa-icon"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 9l9-6 9 6M3 9h18"/></svg></span>
                        Novo Processo
                    </a>
                    @endif
                    @if($canPessoas)
                    <a href="{{ route('pessoas') }}" class="quick-add-item">
                        <span class="qa-icon"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                        Nova Pessoa
                    </a>
                    @endif
                    @if($canAgenda)
                    <div class="quick-add-sep"></div>
                    <a href="{{ route('prazos') }}" class="quick-add-item">
                        <span class="qa-icon"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        Novo Prazo
                    </a>
                    <a href="{{ route('audiencias') }}" class="quick-add-item">
                        <span class="qa-icon"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                        Nova Audiência
                    </a>
                    @endif
                </div>
            </div>
            @endif
            <div style="flex:1;display:flex;justify-content:center;padding:0 16px;max-width:440px;margin:0 auto;">
                @livewire('busca-global')
            </div>
            <div class="topbar-user">
                <button id="themeToggle" onclick="toggleTheme()" aria-label="Alternar tema" title="Alternar tema claro/escuro (Theme)"
                    style="background:none;border:none;cursor:pointer;padding:4px 6px;color:var(--muted);line-height:1;flex-shrink:0;display:inline-flex;align-items:center;">
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <button onclick="window.print()" aria-label="Imprimir" title="Imprimir página (Ctrl+P)"
                    style="background:none;border:1.5px solid var(--border);cursor:pointer;padding:3px 7px;color:var(--muted);line-height:1;border-radius:5px;flex-shrink:0;display:inline-flex;align-items:center;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
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

        @hasSection('breadcrumb')
        <div class="breadcrumb-bar">
            <a href="{{ route('dashboard') }}">
                <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            </a>
            <span class="sep">›</span>
            @yield('breadcrumb')
        </div>
        @endif

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
    const TOAST_ICONS = {
        success: '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>',
        error:   '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        warning: '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>',
        info:    '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    };
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

    // ── Livewire Progress Bar + Save Button States ──
    (function() {
        const bar = document.getElementById('lw-bar');
        let timer;

        function barStart() {
            if (!bar) return;
            clearTimeout(timer);
            bar.style.width = '';
            bar.className = '';
            void bar.offsetWidth;
            bar.classList.add('running');
        }
        function barDone() {
            if (!bar) return;
            bar.classList.remove('running');
            bar.classList.add('done');
            timer = setTimeout(() => { bar.className = ''; bar.style.width = ''; }, 500);
        }

        function lockSaveBtns() {
            document.querySelectorAll('[wire\\:click="salvar"]:not([data-lw-orig])').forEach(btn => {
                btn.setAttribute('data-lw-orig', btn.innerHTML);
                btn.disabled = true;
                btn.innerHTML = '<svg class="spin-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.18-5.19"/></svg>&nbsp;Salvando…';
            });
        }
        function unlockSaveBtns() {
            document.querySelectorAll('[data-lw-orig]').forEach(btn => {
                btn.innerHTML = btn.getAttribute('data-lw-orig');
                btn.removeAttribute('data-lw-orig');
                btn.disabled = false;
            });
        }

        document.addEventListener('livewire:request', () => { barStart(); lockSaveBtns(); });
        document.addEventListener('livewire:response', () => { barDone(); unlockSaveBtns(); });
        document.addEventListener('livewire:navigate-start', barStart);
        document.addEventListener('livewire:navigated', barDone);
    })();

    // ── Quick Add dropdown ──
    function toggleQuickAdd(e) {
        e.stopPropagation();
        document.getElementById('quickAddMenu')?.classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const qa = document.getElementById('quickAdd');
        if (qa && !qa.contains(e.target)) {
            document.getElementById('quickAddMenu')?.classList.remove('open');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.getElementById('quickAddMenu')?.classList.remove('open');
    });

    // ── Dark Mode ──
    function toggleTheme() {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('sapro-theme', next);
        document.getElementById('themeToggle').innerHTML = next === 'dark'
            ? '<svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
            : '<svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
    }
    (function () {
        const t = document.documentElement.getAttribute('data-theme') || 'light';
        const btn = document.getElementById('themeToggle');
        if (btn) btn.innerHTML = t === 'dark'
            ? '<svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
            : '<svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
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
                    '<code style="font-size:11px">Ctrl+K</code> Busca global &nbsp;|&nbsp; ' +
                    '<code style="font-size:11px">/</code> Focar busca &nbsp;|&nbsp; ' +
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
