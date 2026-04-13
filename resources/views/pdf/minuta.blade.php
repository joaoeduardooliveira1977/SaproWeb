<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#1e293b; background:white; }

    .header { background:#1a3a5c; color:white; padding:16px 24px; margin-bottom:28px; }
    .header-logo { font-size:16px; font-weight:bold; }
    .header-sub { font-size:9px; color:#93c5fd; margin-top:2px; }
    .header-titulo { font-size:15px; font-weight:bold; margin-top:10px; border-top:1px solid rgba(255,255,255,.2); padding-top:10px; }

    .meta-bar { display:flex; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
    .meta-item { background:#f1f5f9; padding:5px 12px; border-radius:4px; font-size:9px; color:#64748b; }
    .meta-item strong { color:#1e293b; }

    .corpo {
        font-size:11px; line-height:1.9; color:#1e293b;
        white-space:pre-wrap; word-break:break-word;
        padding:0 4px;
    }

    .footer { margin-top:32px; padding-top:10px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:8px; color:#94a3b8; }
</style>
</head>
<body>

<div class="header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            <div class="header-logo">âš– Software Jurídico</div>
            <div class="header-sub">Sistema de Acompanhamento de Processos</div>
        </div>
        <div style="text-align:right;font-size:9px;color:#93c5fd;">
            Gerado em: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
    <div class="header-titulo">{{ $titulo }}</div>
</div>

<div class="meta-bar">
    <div class="meta-item"><strong>Processo:</strong> {{ $processo->numero ?? 'â€”' }}</div>
    <div class="meta-item"><strong>Cliente:</strong> {{ $processo->cliente?->nome ?? 'â€”' }}</div>
    <div class="meta-item"><strong>Data:</strong> {{ now()->format('d/m/Y') }}</div>
</div>

<div class="corpo">{{ $corpo }}</div>

<div class="footer">
    <span>Software Jurídico</span>
    <span>{{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
