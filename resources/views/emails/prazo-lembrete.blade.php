<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { margin:0; padding:0; background:#f1f5f9; font-family:Arial,Helvetica,sans-serif; }
  .wrap { max-width:580px; margin:24px auto; }
  .header { padding:28px 36px 24px; border-radius:8px 8px 0 0; }
  .body { background:#fff; border:1px solid #e2e8f0; border-top:none; padding:28px 36px; }
  .field { display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
  .field-label { color:#64748b; }
  .field-value { font-weight:600; color:#1e293b; }
  .footer { background:#e2e8f0; border:1px solid #cbd5e1; border-top:none; padding:14px 36px; border-radius:0 0 8px 8px; text-align:center; }
</style>
</head>
<body>
<div class="wrap">

  @php
    $fatal     = $prazo->prazo_fatal;
    $headerBg  = $fatal ? '#7f1d1d' : ($dias === 0 ? '#1e3a5f' : '#1a3a5c');
    $diasLabel = $dias === 0 ? 'Vence HOJE' : ($dias < 0 ? abs($dias).' dia(s) em atraso' : $dias.' dia(s) restante(s)');
    $diasColor = $fatal || $dias <= 1 ? '#fca5a5' : ($dias <= 5 ? '#fde68a' : '#93c5fd');
  @endphp

  <div class="header" style="background:{{ $headerBg }};">
    <div style="color:rgba(255,255,255,.5);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">
      Software Jur�dico
    </div>
    <div style="color:#fff;font-size:20px;font-weight:700;line-height:1.3;">
      {{ $fatal ? '🚨 PRAZO FATAL' : '⏳ Lembrete de Prazo' }}
    </div>
    <div style="display:inline-block;margin-top:10px;padding:4px 14px;border-radius:20px;
                background:{{ $diasColor }}22;border:1px solid {{ $diasColor }};
                color:{{ $diasColor }};font-size:13px;font-weight:700;">
      {{ $diasLabel }}
    </div>
  </div>

  <div class="body">
    <div style="font-size:16px;font-weight:700;color:#1a3a5c;margin-bottom:18px;">
      {{ $prazo->titulo }}
    </div>

    <div class="field">
      <span class="field-label">Data do Prazo</span>
      <span class="field-value">{{ $prazo->data_prazo->format('d/m/Y') }}</span>
    </div>
    <div class="field">
      <span class="field-label">Tipo</span>
      <span class="field-value">{{ $prazo->tipo }}</span>
    </div>
    @if($prazo->processo)
    <div class="field">
      <span class="field-label">Processo</span>
      <span class="field-value">{{ $prazo->processo->numero }}
        @if($prazo->processo->cliente) — {{ $prazo->processo->cliente->nome }} @endif
      </span>
    </div>
    @endif
    @if($prazo->descricao)
    <div class="field">
      <span class="field-label">Descrição</span>
      <span class="field-value" style="text-align:right;max-width:300px;">{{ $prazo->descricao }}</span>
    </div>
    @endif
    @if($prazo->observacoes)
    <div class="field">
      <span class="field-label">Observações</span>
      <span class="field-value" style="text-align:right;max-width:300px;">{{ $prazo->observacoes }}</span>
    </div>
    @endif

    <div style="margin-top:20px;padding:14px 16px;border-radius:6px;
                background:{{ $fatal ? '#fef2f2' : '#eff6ff' }};
                border:1px solid {{ $fatal ? '#fecaca' : '#bfdbfe' }};">
      <div style="font-size:12px;color:{{ $fatal ? '#991b1b' : '#1e40af' }};">
        @if($fatal)
          ⚠️ <strong>Este é um prazo fatal.</strong> O descumprimento pode causar consequências irreversíveis.
        @else
          Acesse o sistema para visualizar e gerenciar este prazo.
        @endif
      </div>
    </div>
  </div>

  <div class="footer">
    <span style="font-size:11px;color:#64748b;">
      Gerado pelo Software Jur�dico &nbsp;·&nbsp; {{ $geradoEm }}
    </span>
  </div>

</div>
</body>
</html>
