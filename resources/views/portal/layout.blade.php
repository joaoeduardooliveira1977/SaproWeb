<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Portal do Cliente</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Software Jurídico">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    @livewireStyles
    <style>
        :root { --primary: #1a3a5c; --primary-light: #2563a8; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #334155; min-height: 100vh; }

        /* Navbar */
        .navbar { background: #1a3a5c; color: white; padding: 0 24px; height: 56px; display: flex; align-items: center; justify-content: space-between; }
        .navbar-brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; }
        .navbar-brand span { font-size: 22px; }
        .navbar-sub { font-size: 11px; color: #93c5fd; font-weight: 400; }
        .navbar-user { display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .btn-sair { background: rgba(255,255,255,0.15); border: none; color: white; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .btn-sair:hover { background: rgba(255,255,255,0.25); }

        /* Container */
        .container { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }

        /* Cards */
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; overflow: hidden; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 15px; color: #1a3a5c; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .stat-value { font-size: 28px; font-weight: 700; color: #1a3a5c; }
        .stat-label { font-size: 13px; color: #64748b; margin-top: 4px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 16px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        /* Badge */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-ativo { background: #dcfce7; color: #16a34a; }
        .badge-encerrado { background: #f1f5f9; color: #64748b; }
        .risco-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; }

        /* Btn */
        .btn { padding: 7px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 13px; font-weight: 500; }
        .btn-primary { background: #2563a8; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-outline { background: white; border: 1px solid #e2e8f0; color: #334155; }
        .btn-outline:hover { background: #f8fafc; }

        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 24px; }
        .modal { background: white; border-radius: 16px; max-width: 700px; width: 100%; max-height: 80vh; overflow-y: auto; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 16px; font-weight: 600; color: #1a3a5c; }
        .modal-body { padding: 24px; }
        .btn-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b; }

        /* Timeline */
        .timeline { position: relative; padding-left: 24px; }
        .timeline::before { content: ''; position: absolute; left: 7px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
        .timeline-item { position: relative; margin-bottom: 20px; }
        .timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: #2563a8; border: 2px solid white; box-shadow: 0 0 0 2px #2563a8; }
        .timeline-date { font-size: 12px; color: #64748b; margin-bottom: 4px; }
        .timeline-text { font-size: 14px; color: #334155; line-height: 1.5; }

        /* Agenda */
        .agenda-item { display: flex; gap: 16px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .agenda-item:last-child { border-bottom: none; }
        .agenda-hora { font-size: 13px; font-weight: 600; color: #2563a8; min-width: 80px; }
        .agenda-info { flex: 1; }
        .agenda-titulo { font-size: 14px; font-weight: 500; }
        .agenda-meta { font-size: 12px; color: #64748b; margin-top: 2px; }
        .urgente-badge { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; margin-left: 8px; }

        /* Empty */
        .empty { text-align: center; padding: 40px; color: #94a3b8; font-size: 14px; }
        .empty-icon { font-size: 36px; margin-bottom: 12px; }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

        /* Tabs */
        .portal-tabs { display: flex; gap: 0; background: #fff; border-bottom: 2px solid #e2e8f0; overflow-x: auto; padding: 0 24px; }
        .portal-tab { padding: 14px 20px; font-size: 13px; font-weight: 600; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; color: #64748b; white-space: nowrap; margin-bottom: -2px; transition: all .15s; position: relative; }
        .portal-tab:hover { color: #1a3a5c; }
        .portal-tab.active { color: #1a3a5c; border-bottom-color: #1a3a5c; }
        .tab-badge { position: absolute; top: 8px; right: 6px; background: #dc2626; color: #fff; font-size: 10px; font-weight: 700; padding: 1px 5px; border-radius: 8px; }

        /* Processo card */
        .processo-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 12px; cursor: pointer; transition: box-shadow .15s, border-color .15s; }
        .processo-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); border-color: #2563a8; }

        /* Info labels */
        .info-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .info-val { font-size: 13px; color: #334155; }

        .portal-hide-sm { display: table-cell; }
        @media (max-width: 768px) {
            .portal-hide-sm { display: none !important; }
            .container { padding: 16px 12px; }
            .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .portal-tabs { padding: 0 8px; }
            .portal-tab { padding: 10px 12px; font-size: 12px; }
            .card-body { padding: 16px; }
            .card-header { padding: 12px 16px; }
            table { font-size: 12px; }
            th, td { padding: 8px 10px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .navbar-brand { font-size: 15px; }
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
