<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { margin:0; padding:0; background:#f1f5f9; font-family:Arial,Helvetica,sans-serif; }
  .wrap { max-width:660px; margin:24px auto; }
  .header { background:#1a3a5c; padding:28px 36px 24px; border-radius:8px 8px 0 0; }
  .header-sub { color:#93c5fd; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:8px; }
  .header-title { color:#fff; font-size:22px; font-weight:700; line-height:1.2; }
  .header-greeting { color:#93c5fd; font-size:13px; margin-top:6px; }
  .resumo-bar { background:#fff; border:1px solid #e2e8f0; border-top:none; padding:16px 36px; }
  .resumo-table { width:100%; border-collapse:collapse; }
  .resumo-td { text-align:center; padding:10px 8px; }
  .resumo-num { font-size:24px; font-weight:700; line-height:1; }
  .resumo-label { font-size:10px; color:#64748b; margin-top:4px; }
  .sep { width:1px; background:#e2e8f0; }
  .body { padding:20px 36px; }
  .grupo-header { padding:10px 16px; border-radius:4px; margin-top:20px; margin-bottom:0; }
  .grupo-title { font-size:14px; font-weight:700; color:#1e293b; }
  .grupo-count { font-size:11px; color:#64748b; margin-top:2px; }
  .notif-item { background:#fff; border:1px solid #e2e8f0; border-left:4px solid #2563eb; border-radius:0 4px 4px 0; }
  .notif-title { font-size:13px; font-weight:700; color:#1e293b; margin-bottom:4px; }
  .notif-msg { font-size:12px; color:#64748b; }
  .footer { background:#e2e8f0; border:1px solid #cbd5e1; border-top:none; padding:14px 36px; border-radius:0 0 8px 8px; text-align:center; }
  .footer-text { font-size:11px; color:#64748b; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Cabeçalho --}}
  <div class="header">
    <div class="header-sub">Software Jur�dico</div>
    <div class="header-title">Notificações do dia — {{ $dataFmt }}</div>
    <div class="header-greeting">Olá, {{ $usuario->nome }}</div>
  </div>

  {{-- Resumo de contagens --}}
  <div class="resumo-bar">
    <table class="resumo-table">
      <tr>
        @php
          $resumo = [];
          if ($fatais)   $resumo[] = ['🚨', $fatais,   'Fatal(is)',  '#9d174d'];
          if ($vencidos) $resumo[] = ['❌', $vencidos,  'Vencido(s)', '#991b1b'];
          if ($vencendo) $resumo[] = ['⏳', $vencendo,  'A Vencer',   '#854d0e'];
        @endphp
        @foreach($resumo as $i => $r)
          @if($i > 0)<td class="sep"></td>@endif
          <td class="resumo-td">
            <div style="font-size:20px;line-height:1;margin-bottom:4px;">{{ $r[0] }}</div>
            <div class="resumo-num" style="color:{{ $r[3] }};">{{ $r[1] }}</div>
            <div class="resumo-label">{{ $r[2] }}</div>
          </td>
        @endforeach
        @if(empty($resumo))
          <td class="resumo-td">
            <div class="resumo-num" style="color:#1a3a5c;">{{ $total }}</div>
            <div class="resumo-label">Notificação(ões)</div>
          </td>
        @endif
      </tr>
    </table>
  </div>

  {{-- Corpo com grupos --}}
  <div class="body">
    @php
      $tiposLabel = [
        'prazo_fatal'            => 'Prazo Fatal',
        'prazo_vencendo'         => 'Prazo a Vencer',
        'prazo_vencido'          => 'Prazo Vencido',
        'honorario_atrasado'     => 'Honorário em Atraso',
        'processo_sem_andamento' => 'Processo sem Andamento',
      ];
      $grupoOrdem = ['prazo_fatal','prazo_vencido','prazo_vencendo','honorario_atrasado','processo_sem_andamento'];
    @endphp

    @foreach($grupoOrdem as $tipo)
      @if($grupos->has($tipo))
        @php
          $itens    = $grupos[$tipo];
          $primeira = $itens->first();
          $label    = $tiposLabel[$tipo] ?? $tipo;
        @endphp
        <div class="grupo-header" style="background:{{ $primeira->cor() }};">
          <div class="grupo-title">{{ $primeira->icone() }} {{ $label }}</div>
          <div class="grupo-count">{{ $itens->count() }} item(ns)</div>
        </div>
        @foreach($itens as $n)
        <div class="notif-item">
          <div style="padding:10px 14px;">
            <div class="notif-title">{{ $n->titulo }}</div>
            @if($n->mensagem)
            <div class="notif-msg">{{ $n->mensagem }}</div>
            @endif
          </div>
        </div>
        @endforeach
      @endif
    @endforeach
  </div>

  {{-- Rodapé --}}
  <div class="footer">
    <span class="footer-text">Gerado pelo Software Jur�dico &nbsp;·&nbsp; {{ $geradoEm }}</span>
  </div>

</div>
</body>
</html>
