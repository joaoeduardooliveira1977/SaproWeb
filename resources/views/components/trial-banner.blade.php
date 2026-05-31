@php
  $tenant = tenant();
  if (!$tenant || $tenant->plano !== 'demo' || !$tenant->trial_expira_em) return;
  $dias = $tenant->diasRestantesTrial();
  if ($dias <= 0) return;
  $cor   = $dias <= 3 ? '#dc2626' : ($dias <= 7 ? '#d97706' : '#059669');
  $bg    = $dias <= 3 ? '#fef2f2' : ($dias <= 7 ? '#fffbeb' : '#f0fdf4');
  $borda = $dias <= 3 ? '#fca5a5' : ($dias <= 7 ? '#fcd34d' : '#6ee7b7');
  $numero = preg_replace('/\D/', '', env('WHATSAPP_SUPORTE', '5511999999999'));
  $msg = rawurlencode("Olá! Estou usando o Software Jurídico (trial) e quero saber mais sobre os planos.");
  $linkWa = "https://wa.me/{$numero}?text={$msg}";
@endphp
<div id="trial-banner" style="
  background:{{ $bg }};
  border-bottom:1.5px solid {{ $borda }};
  padding:8px 20px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:12px;
  font-size:13px;
  color:{{ $cor }};
  font-weight:600;
  position:sticky;
  top:0;
  z-index:100;
  ">
  <span>⏳ Versão Demo —
    @if($dias <= 3)
      <strong>Apenas {{ $dias }} dia(s) restante(s)!</strong>
    @else
      {{ $dias }} dias restantes
    @endif
  </span>
  <a href="{{ $linkWa }}"
     target="_blank"
     rel="noopener"
     style="
       background:{{ $cor }};
       color:#fff;
       text-decoration:none;
       padding:4px 14px;
       border-radius:20px;
       font-size:12px;
       font-weight:700;
       white-space:nowrap;
     ">
    Falar com suporte
  </a>
</div>
