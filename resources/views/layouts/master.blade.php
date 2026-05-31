<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Master Admin') — Sistema Jurídico</title>
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar:   #0a1628;
            --sidebar2:  #0f1e36;
            --accent:    #3b82f6;
            --gold:      #f59e0b;
            --bg:        #f1f5f9;
            --white:     #ffffff;
            --text:      #1e293b;
            --muted:     #64748b;
            --border:    #e2e8f0;
            --danger:    #dc2626;
            --warning:   #d97706;
            --success:   #16a34a;
            --navy:      #1a3a5c;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* ── Layout ── */
        .layout { display: grid; grid-template-columns: 200px 1fr; height: 100vh; overflow: hidden; }

        /* ── Sidebar ── */
        .sidebar {
            width: 200px;
            background: var(--sidebar);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            z-index: 100;
            border-right: 1px solid rgba(255,255,255,.05);
        }

        .sidebar-header {
            padding: 20px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .logo-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-icon svg {
            width: 18px;
            height: 18px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
        }

        .logo-text {
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            letter-spacing: .2px;
            line-height: 1.2;
        }

        .master-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(245,158,11,.15);
            border: 1px solid rgba(245,158,11,.3);
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: 800;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Nav */
        .nav { flex: 1; padding: 10px 8px; }

        .nav-section {
            font-size: 9px;
            font-weight: 700;
            color: rgba(255,255,255,.2);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 10px 8px 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: rgba(255,255,255,.45);
            font-size: 13px;
            font-weight: 500;
            transition: all .15s;
            margin-bottom: 1px;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.8);
        }

        .nav-item.active {
            background: rgba(59,130,246,.18);
            color: #93c5fd;
        }

        .nav-item.active svg { stroke: #93c5fd; }

        .nav-item svg {
            width: 16px;
            height: 16px;
            stroke: rgba(255,255,255,.35);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
            transition: stroke .15s;
        }

        .nav-item:hover svg { stroke: rgba(255,255,255,.75); }

        .nav-item .badge-count {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 99px;
            min-width: 18px;
            text-align: center;
        }

        .nav-sep { height: 1px; background: rgba(255,255,255,.06); margin: 8px 8px; }
        .nav-spacer { flex: 1; }

        /* Bottom */
        .sidebar-bottom {
            padding: 10px 8px 16px;
            border-top: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }

        /* ── Main ── */
        .main { display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

        .topbar {
            height: 52px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            flex-shrink: 0;
        }

        .topbar-left { display: flex; align-items: center; gap: 10px; }

        .topbar-badge {
            background: linear-gradient(135deg, #1a3a5c, #1d4ed8);
            color: #fff;
            font-size: 9px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .topbar-title { font-size: 14px; font-weight: 600; color: var(--text); }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
        .topbar-user  { font-size: 12px; color: var(--muted); }

        .btn-topbar {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--white);
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
        }
        .btn-topbar:hover { background: #f8fafc; color: var(--text); }

        .content { flex: 1; overflow-y: auto; padding: 24px; }

        /* ── Cards / Stats ── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 20px; }

        .stat-card {
            background: var(--white);
            border-radius: 10px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: 1px solid var(--border);
            border-left: 4px solid var(--navy);
        }

        .stat-val   { font-size: 28px; font-weight: 800; color: var(--navy); margin-bottom: 3px; }
        .stat-label { font-size: 11px; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .5px; }
        .stat-card.blue   { border-left-color: #3b82f6; }
        .stat-card.blue   .stat-val { color: #1d4ed8; }
        .stat-card.gold   { border-left-color: var(--gold); }
        .stat-card.gold   .stat-val { color: #92400e; }
        .stat-card.green  { border-left-color: var(--success); }
        .stat-card.green  .stat-val { color: #166534; }
        .stat-card.danger { border-left-color: var(--danger); }
        .stat-card.danger .stat-val { color: var(--danger); }
        .stat-card.warn   { border-left-color: var(--warning); }
        .stat-card.warn   .stat-val { color: var(--warning); }

        /* ── Cards ── */
        .card { background: var(--white); border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid var(--border); overflow: hidden; margin-bottom: 20px; }
        .card-header { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .card-title  { font-size: 14px; font-weight: 700; color: var(--navy); }
        .card-body   { padding: 16px 18px; }
        .table-wrap  { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #f8fafc; color: var(--muted); padding: 9px 12px; text-align: left; font-size: 10px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; border-bottom: 2px solid var(--border); white-space: nowrap; }
        tbody tr:hover td { background: #f8fafc; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-orange { background: #ffedd5; color: #c2410c; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border: none; border-radius: 7px; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: filter .15s; }
        .btn:hover { filter: brightness(.92); }
        .btn-primary { background: var(--navy); color: #fff; }
        .btn-blue    { background: #3b82f6; color: #fff; }
        .btn-gold    { background: var(--gold); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-danger  { background: var(--danger); color: #fff; }
        .btn-sm      { padding: 5px 10px; font-size: 11px; }
        .btn-outline { background: transparent; color: var(--navy); border: 1.5px solid var(--border); }
        .btn-outline:hover { border-color: var(--navy); filter: none; background: #f8fafc; }
        .btn-ghost   { background: #f1f5f9; color: var(--text); }

        /* ── Filters ── */
        .filter-bar { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; }
        .filter-bar input, .filter-bar select {
            padding: 8px 12px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            font-size: 13px;
            color: var(--text);
            background: var(--white);
            outline: none;
            transition: border-color .15s;
        }
        .filter-bar input:focus, .filter-bar select:focus { border-color: #3b82f6; }
        .filter-bar input { flex: 1; min-width: 200px; }

        /* ── Charts ── */
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .chart-card  { background: var(--white); border-radius: 10px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid var(--border); }
        .chart-title { font-size: 13px; font-weight: 700; color: var(--navy); margin-bottom: 14px; }

        /* ── Infra meters ── */
        .meter { height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-top: 6px; }
        .meter-fill { height: 100%; border-radius: 99px; background: #3b82f6; transition: width .4s; }
        .meter-fill.warn   { background: var(--warning); }
        .meter-fill.danger { background: var(--danger); }

        /* ── Alert rows ── */
        .alert-row { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-bottom: 1px solid var(--border); }
        .alert-row:last-child { border-bottom: none; }
        .alert-row .icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-row .body { flex: 1; }
        .alert-row .body strong { display: block; font-size: 13px; font-weight: 600; color: var(--text); }
        .alert-row .body span   { font-size: 12px; color: var(--muted); }

        /* ── Tabs ── */
        .tabs { display: flex; gap: 0; border-bottom: 2px solid var(--border); margin-bottom: 20px; }
        .tab {
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all .15s;
            text-decoration: none;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        .tab:hover { color: var(--navy); }
        .tab.active { color: var(--navy); border-bottom-color: var(--navy); }

        /* ── Modal ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000;
            padding: 16px;
        }
        .modal {
            background: var(--white);
            border-radius: 14px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 24px 64px rgba(0,0,0,.25);
        }
        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title { font-size: 16px; font-weight: 700; color: var(--text); }
        .modal-close {
            width: 28px; height: 28px;
            border-radius: 6px;
            border: none;
            background: #f1f5f9;
            color: var(--muted);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            transition: background .15s;
        }
        .modal-close:hover { background: var(--border); }
        .modal-body    { padding: 20px 22px; }
        .modal-footer  { padding: 14px 22px; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; }

        /* Form */
        .fg { margin-bottom: 14px; }
        .fg label { display: block; font-size: 11px; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
        .fg input, .fg select, .fg textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            font-size: 13px;
            color: var(--text);
            background: var(--white);
            outline: none;
            transition: border-color .15s;
            font-family: inherit;
        }
        .fg input:focus, .fg select:focus, .fg textarea:focus { border-color: #3b82f6; }
        .fg textarea { resize: vertical; min-height: 80px; }
        .fg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .fg .err { font-size: 11px; color: var(--danger); margin-top: 4px; }

        /* Danger zone */
        .danger-zone { background: #fff5f5; border: 1.5px solid #fecaca; border-radius: 10px; padding: 16px 18px; margin-top: 20px; }
        .danger-zone-title { font-size: 12px; font-weight: 700; color: var(--danger); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .charts-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <div>
                    <div class="logo-text">Sistema Jurídico</div>
                </div>
            </div>
            <span class="master-badge">⚡ Master</span>
        </div>

        <nav class="nav">

            <div class="nav-section">Principal</div>

            <a href="{{ route('master.dashboard') }}"
               class="nav-item {{ request()->routeIs('master.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>

            <a href="{{ route('master.tenants') }}"
               class="nav-item {{ request()->routeIs('master.tenants') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Tenants
            </a>

            <a href="{{ route('master.lixeira') }}"
               class="nav-item {{ request()->routeIs('master.lixeira') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                Lixeira
            </a>

            <div class="nav-sep"></div>
            <div class="nav-section">Sistema</div>

            <a href="{{ route('master.comunicados') }}"
               class="nav-item {{ request()->routeIs('master.comunicados') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                Comunicados
            </a>

            <a href="{{ route('master.infra') }}"
               class="nav-item {{ request()->routeIs('master.infra') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                Infraestrutura
            </a>

            <a href="{{ route('master.alertas') }}"
               class="nav-item {{ request()->routeIs('master.alertas') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Alertas
            </a>

            <a href="/status" target="_blank"
               class="nav-item">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Status Público
                <svg viewBox="0 0 24 24" style="width:10px;height:10px;margin-left:auto;opacity:.4;"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>

        </nav>

        <div class="sidebar-bottom">
            <a href="{{ route('dashboard') }}" class="nav-item" style="margin-bottom:4px;">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar ao Sistema
            </a>
            <form method="POST" action="{{ route('master.logout') }}">
                @csrf
                <button type="submit" class="nav-item" style="color:#f87171;">
                    <svg viewBox="0 0 24 24" style="stroke:#f87171;"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sair
                </button>
            </form>
        </div>

    </aside>

    {{-- ── Main ── --}}
    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <span class="topbar-badge">Master Admin</span>
                <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="topbar-right">
                <span class="topbar-user">
                    <strong>{{ auth('usuarios')->user()->nome ?? 'Super Admin' }}</strong>
                </span>
                <a href="{{ route('dashboard') }}" class="btn-topbar">
                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="15 18 9 12 15 6"/></svg>
                    Sistema
                </a>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

</div>
@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
