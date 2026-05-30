<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Master Admin') — Master Admin</title>
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:   #1a3a5c;
            --sidebar:   #0f2540;
            --accent:    #1D9E75;
            --gold:      #c9a84c;
            --bg:        #f0f4f8;
            --white:     #ffffff;
            --text:      #1e293b;
            --muted:     #64748b;
            --border:    #e2e8f0;
            --danger:    #dc2626;
            --warning:   #d97706;
            --success:   #16a34a;
        }

        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); }

        /* ── Layout ── */
        .layout { display: grid; grid-template-columns: 116px 1fr; height: 100vh; overflow: hidden; }

        /* ── Sidebar ── */
        .sidebar {
            width: 116px; background: var(--sidebar);
            display: flex; flex-direction: column; align-items: center;
            padding: 14px 0; gap: 2px; flex-shrink: 0;
            height: 100vh; position: sticky; top: 0; overflow-y: auto; z-index: 100;
        }

        /* Logo / brand */
        .nav-logo {
            width: 50px; height: 50px; border-radius: 14px; background: var(--gold);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 6px; flex-shrink: 0;
        }
        .nav-logo svg { width: 26px; height: 26px; stroke: #fff; fill: none; stroke-width: 2; }

        .nav-master-badge {
            font-size: 9px; font-weight: 800; color: var(--gold);
            text-transform: uppercase; letter-spacing: 1.5px;
            margin-bottom: 12px;
        }

        /* Nav buttons */
        .nav-btn {
            width: 100px; min-height: 62px; border-radius: 12px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: pointer; border: none; background: transparent; transition: all .2s;
            position: relative; gap: 4px; text-decoration: none; padding: 6px 4px;
        }
        .nav-btn:hover  { background: rgba(255,255,255,.07); }
        .nav-btn.active { background: rgba(201,168,76,.15); }
        .nav-btn svg    { width: 22px; height: 22px; stroke: rgba(255,255,255,.4); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; transition: stroke .2s; }
        .nav-btn.active svg { stroke: var(--gold); }
        .nav-btn:hover svg  { stroke: rgba(255,255,255,.75); }
        .nav-btn-label { font-size: 11px; color: rgba(255,255,255,.3); font-weight: 500; letter-spacing: .3px; transition: color .2s; line-height: 1; text-align: center; }
        .nav-btn.active .nav-btn-label { color: var(--gold); }
        .nav-btn:hover .nav-btn-label  { color: rgba(255,255,255,.65); }

        .nav-sep    { width: 36px; height: 1px; background: rgba(255,255,255,.08); margin: 4px 0; flex-shrink: 0; }
        .nav-spacer { flex: 1; }

        /* ── Main ── */
        .main { display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .topbar {
            height: 52px; background: var(--white); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; padding: 0 24px; gap: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06); flex-shrink: 0;
        }
        .topbar-badge {
            background: var(--gold); color: #fff;
            font-size: 10px; font-weight: 800; padding: 3px 10px;
            border-radius: 4px; letter-spacing: .8px;
        }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--primary); }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
        .topbar-user  { font-size: 12px; color: var(--muted); }
        .content { flex: 1; overflow-y: auto; padding: 24px; }

        /* ── Cards / Stats ── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .stat-card { background: var(--white); border-radius: 10px; padding: 16px 18px; box-shadow: 0 1px 4px rgba(0,0,0,.07); border: 1px solid var(--border); border-left: 4px solid var(--primary); }
        .stat-val   { font-size: 26px; font-weight: 700; color: var(--primary); margin-bottom: 2px; }
        .stat-label { font-size: 12px; color: var(--muted); }
        .stat-card.gold   { border-left-color: var(--gold); }
        .stat-card.accent { border-left-color: var(--accent); }
        .stat-card.danger { border-left-color: var(--danger); }
        .stat-card.warn   { border-left-color: var(--warning); }

        /* ── Table / Card ── */
        .card { background: var(--white); border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.07); border: 1px solid var(--border); overflow: hidden; margin-bottom: 20px; }
        .card-header { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-title  { font-size: 14px; font-weight: 700; color: var(--primary); }
        .card-body   { padding: 16px 18px; }
        .table-wrap  { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #f8fafc; color: var(--muted); padding: 9px 12px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; border-bottom: 2px solid var(--border); white-space: nowrap; }
        tbody tr:hover td { background: #f8fafc; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-blue   { background: #eff6ff; color: #2563a8; }
        .badge-purple { background: #f5f3ff; color: #7c3aed; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-yellow { background: #fefce8; color: #a16207; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border: none; border-radius: 7px; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: filter .15s; }
        .btn:hover { filter: brightness(.9); }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-gold    { background: var(--gold);    color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-danger  { background: var(--danger);  color: #fff; }
        .btn-sm      { padding: 5px 10px; font-size: 11px; }
        .btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }

        /* ── Filters ── */
        .filter-bar { display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
        .filter-bar input, .filter-bar select { padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 7px; font-size: 13px; color: var(--text); background: var(--white); }
        .filter-bar input { flex: 1; min-width: 200px; }

        /* ── Charts ── */
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .chart-card  { background: var(--white); border-radius: 10px; padding: 18px; box-shadow: 0 1px 4px rgba(0,0,0,.07); border: 1px solid var(--border); }
        .chart-title { font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 14px; }

        /* ── Infra meters ── */
        .meter { height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-top: 6px; }
        .meter-fill { height: 100%; border-radius: 99px; background: var(--accent); transition: width .4s; }
        .meter-fill.warn   { background: var(--warning); }
        .meter-fill.danger { background: var(--danger); }

        /* ── Alert rows ── */
        .alert-row { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-bottom: 1px solid var(--border); }
        .alert-row:last-child { border-bottom: none; }
        .alert-row .icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-row .body { flex: 1; }
        .alert-row .body strong { display: block; font-size: 13px; font-weight: 600; color: var(--text); }
        .alert-row .body span   { font-size: 12px; color: var(--muted); }

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

        <div class="nav-logo">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="nav-master-badge">Master</div>

        {{-- Dashboard --}}
        <a href="{{ route('master-admin.dashboard') }}"
           class="nav-btn {{ request()->routeIs('master-admin.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span class="nav-btn-label">Dashboard</span>
        </a>

        {{-- Tenants --}}
        <a href="{{ route('master-admin.tenants') }}"
           class="nav-btn {{ request()->routeIs('master-admin.tenants*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="nav-btn-label">Tenants</span>
        </a>

        {{-- Alertas --}}
        <a href="{{ route('master-admin.alertas') }}"
           class="nav-btn {{ request()->routeIs('master-admin.alertas') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span class="nav-btn-label">Alertas</span>
        </a>

        {{-- Infra --}}
        <a href="{{ route('master-admin.infra') }}"
           class="nav-btn {{ request()->routeIs('master-admin.infra') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
            <span class="nav-btn-label">Infra</span>
        </a>

        <div class="nav-spacer"></div>
        <div class="nav-sep"></div>

        {{-- Super Admin --}}
        <a href="{{ route('super-admin.index') }}" class="nav-btn">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
            <span class="nav-btn-label">Admin</span>
        </a>

        {{-- Voltar --}}
        <a href="{{ route('dashboard') }}" class="nav-btn">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            <span class="nav-btn-label">Voltar</span>
        </a>

    </aside>

    {{-- ── Main ── --}}
    <div class="main">
        <div class="topbar">
            <span class="topbar-badge">MASTER</span>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            <div class="topbar-right">
                <span class="topbar-user">{{ auth('usuarios')->user()->nome ?? 'Super Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm" style="background:#f1f5f9;color:#64748b;">Sair</button>
                </form>
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
