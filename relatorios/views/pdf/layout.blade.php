<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; color:#334155; background:white; }

    .header { background:#1a3a5c; color:white; padding:16px 24px; margin-bottom:20px; }
    .header-top { display:flex; justify-content:space-between; align-items:center; }
    .header-logo { font-size:18px; font-weight:bold; }
    .header-sub { font-size:10px; color:#93c5fd; margin-top:2px; }
    .header-titulo { font-size:14px; font-weight:bold; margin-top:10px; border-top:1px solid rgba(255,255,255,0.2); padding-top:10px; }

    .meta { display:flex; gap:24px; margin-bottom:16px; font-size:9px; color:#64748b; }
    .meta span { background:#f1f5f9; padding:4px 10px; border-radius:4px; }

    table { width:100%; border-collapse:collapse; margin-bottom:16px; }
    th { background:#1a3a5c; color:white; padding:7px 10px; text-align:left; font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.3px; }
    td { padding:6px 10px; border-bottom:1px solid #f1f5f9; font-size:9px; vertical-align:top; }
    tr:nth-child(even) td { background:#f8fafc; }

    .section-title { background:#f1f5f9; padding:8px 12px; font-weight:bold; font-size:11px; color:#1a3a5c; margin:12px 0 6px; border-left:4px solid #2563a8; }
    .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:8px; font-weight:bold; }
    .badge-ativo { background:#dcfce7; color:#16a34a; }
    .badge-encerrado { background:#f1f5f9; color:#64748b; }
    .badge-urgente { background:#fee2e2; color:#dc2626; }
    .badge-ok { background:#dcfce7; color:#16a34a; }

    .total-box { background:#1a3a5c; color:white; padding:10px 16px; border-radius:6px; margin-top:16px; display:flex; justify-content:space-between; }
    .total-item { text-align:center; }
    .total-valor { font-size:16px; font-weight:bold; }
    .total-label { font-size:9px; color:#93c5fd; margin-top:2px; }

    .footer { margin-top:24px; padding-top:10px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:8px; color:#94a3b8; }

    .risco-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:4px; }
    .empty { text-align:center; padding:24px; color:#94a3b8; font-style:italic; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="header-logo">⚖ SAPRO — Gestão Jurídica</div>
            <div class="header-sub">Sistema de Acompanhamento de Processos</div>
        </div>
        <div style="text-align:right; font-size:9px; color:#93c5fd;">
            Gerado em: {{ $gerado_em }}<br>
            Usuário: {{ $usuario }}
        </div>
    </div>
    <div class="header-titulo">{{ $titulo }}</div>
</div>

@yield('content')

<div class="footer">
    <span>SAPRO — Sistema de Acompanhamento de Processos</span>
    <span>{{ $gerado_em }}</span>
</div>

</body>
</html>
