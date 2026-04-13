<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; color:#334155; background:#ffffff; }

    .header { border:1px solid #dbe4ef; border-top:5px solid #2563a8; padding:14px 18px; margin-bottom:16px; }
    .header-top { width:100%; }
    .header-logo-clean { font-size:18px; font-weight:bold; color:#0f2742; }
    .header-sub { font-size:9px; color:#64748b; margin-top:2px; }
    .header-meta-clean { float:right; text-align:right; font-size:8.5px; color:#64748b; line-height:1.5; margin-top:-28px; }
    .header-titulo { clear:both; font-size:15px; font-weight:bold; color:#0f2742; margin-top:16px; border-top:1px solid #e2e8f0; padding-top:10px; }

    .meta { margin-bottom:14px; font-size:9px; color:#475569; }
    .meta span { display:inline-block; background:#f8fafc; border:1px solid #e2e8f0; padding:5px 9px; border-radius:4px; margin:0 6px 6px 0; }

    table { width:100%; border-collapse:collapse; margin-bottom:16px; border:1px solid #e2e8f0; }
    th { background:#f1f5f9; color:#0f2742; padding:7px 9px; text-align:left; font-size:8.5px; font-weight:bold; text-transform:uppercase; letter-spacing:0.3px; border-bottom:1px solid #cbd5e1; }
    td { padding:6px 9px; border-bottom:1px solid #e2e8f0; font-size:9px; vertical-align:top; }
    tr:nth-child(even) td { background:#fafcff; }

    .section-title { background:#f8fafc; padding:8px 10px; font-weight:bold; font-size:11px; color:#0f2742; margin:14px 0 6px; border:1px solid #e2e8f0; border-left:4px solid #2563a8; }
    .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:8px; font-weight:bold; }
    .badge-ativo { background:#dcfce7; color:#16a34a; }
    .badge-encerrado { background:#f1f5f9; color:#64748b; }
    .badge-urgente { background:#fee2e2; color:#dc2626; }
    .badge-ok { background:#dcfce7; color:#16a34a; }

    .total-box { background:#f8fafc; border:1px solid #dbe4ef; color:#0f2742; padding:10px 12px; border-radius:6px; margin-top:16px; display:table; width:100%; }
    .total-item { display:table-cell; text-align:center; border-right:1px solid #e2e8f0; }
    .total-item:last-child { border-right:0; }
    .total-valor { font-size:16px; font-weight:bold; color:#0f2742; }
    .total-label { font-size:8.5px; color:#64748b; margin-top:2px; text-transform:uppercase; letter-spacing:.2px; }

    .footer { margin-top:24px; padding-top:10px; border-top:1px solid #e2e8f0; font-size:8px; color:#94a3b8; }
    .footer span:last-child { float:right; }
    .footer .footer-date { float:right; }

    .risco-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:4px; }
    .empty { text-align:center; padding:24px; color:#94a3b8; font-style:italic; border:1px dashed #cbd5e1; background:#f8fafc; }
    .summary-grid { width:100%; border-collapse:separate; border-spacing:8px; margin:0 -8px 16px; border:0; }
    .summary-grid td { border:0; padding:0; background:transparent !important; }
    .summary-card { border:1px solid #dbe4ef; border-left:4px solid #2563a8; border-radius:6px; padding:10px; background:#f8fafc; }
    .summary-label { font-size:8.5px; color:#64748b; font-weight:bold; text-transform:uppercase; letter-spacing:.2px; margin-bottom:4px; }
    .summary-value { font-size:15px; color:#0f2742; font-weight:bold; }
    .summary-note { font-size:8px; color:#94a3b8; margin-top:3px; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="header-logo-clean">Software Jurídico</div>
            <div class="header-sub">Sistema de Acompanhamento de Processos</div>
        </div>
        <div class="header-meta-clean">
            Gerado em: {{ $gerado_em }}<br>
            Usuário: {{ $usuario }}
        </div>
    </div>
    <div class="header-titulo">{{ $titulo }}</div>
</div>

@yield('content')

<div class="footer">
    <span>Software Jurídico</span>
    <span>{{ $gerado_em }}</span>
</div>

</body>
</html>
