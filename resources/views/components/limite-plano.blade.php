@php
    $t = tenant();
    if (!$t) return;
    $diasRestantes = $t->trial_expira_em ? (int) now()->diffInDays($t->trial_expira_em, false) : null;
    $processosUsados = $t->processos()->where('status', 'Ativo')->count();
    $pctProcessos = $t->limite_processos > 0 ? round(($processosUsados / $t->limite_processos) * 100) : 0;
@endphp

@if($t->plano === 'demo')
<div style="background:linear-gradient(135deg,#1a3a5c,#1d4ed8);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:12px;color:#93c5fd;font-weight:600;">
            ✨ PLANO DEMO
        </span>
        @if($diasRestantes !== null && $diasRestantes >= 0)
        <span style="font-size:12px;color:#fff;">
            {{ $diasRestantes }} dia(s) restante(s) no trial
        </span>
        @endif
        <span style="font-size:12px;color:#93c5fd;">
            {{ $processosUsados }}/{{ $t->limite_processos }} processos
        </span>
        <div style="width:100px;height:6px;background:rgba(255,255,255,.2);border-radius:99px;overflow:hidden;">
            <div style="width:{{ $pctProcessos }}%;height:100%;background:{{ $pctProcessos >= 80 ? '#ef4444' : '#34d399' }};border-radius:99px;"></div>
        </div>
    </div>
    <a href="{{ route('tenant.planos') }}"
        style="display:inline-flex;align-items:center;gap:6px;background:#fff;color:#1d4ed8;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
        ⬆️ Fazer Upgrade
    </a>
</div>
@endif
