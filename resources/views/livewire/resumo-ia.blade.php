<div>
    @if($aberto && $resumo)
    {{-- Card expandido --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 100%);border-radius:12px;overflow:hidden;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:rgba(255,255,255,.1);border-radius:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                </span>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#fff;">Briefing do dia</div>
                    <div style="font-size:11px;color:rgba(255,255,255,.5);">{{ today()->format('d/m/Y') }} — gerado por IA</div>
                </div>
            </div>
            <div style="display:flex;gap:6px;">
                <button wire:click="gerar" wire:loading.attr="disabled"
                    style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:6px;color:rgba(255,255,255,.8);font-size:11px;cursor:pointer;transition:background .15s;"
                    onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                    <svg wire:loading.remove wire:target="gerar" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <svg wire:loading wire:target="gerar" style="animation:iapin .7s linear infinite;" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Atualizar
                </button>
                <button wire:click="fechar"
                    style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:6px;color:rgba(255,255,255,.6);cursor:pointer;font-size:14px;transition:background .15s;"
                    onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.08)'">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <div wire:loading wire:target="gerar" style="height:2px;background:linear-gradient(90deg,#6366f1,#a78bfa,#6366f1);background-size:200%;animation:iabar 1.2s linear infinite;"></div>

        <div style="padding:4px 18px 18px;">
            <p style="font-size:13px;color:rgba(255,255,255,.85);line-height:1.7;white-space:pre-line;margin:0;">{{ $resumo }}</p>
        </div>
    </div>

    @else
    {{-- Card recolhido / botão --}}
    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#f0f7ff;border:1.5px solid #bfdbfe;border-radius:10px;margin-bottom:16px;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#dbeafe;border-radius:8px;flex-shrink:0;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            </svg>
        </span>
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:600;color:#1e40af;">Briefing do dia com IA</div>
            <div style="font-size:12px;color:#64748b;">Receba um resumo inteligente das prioridades de hoje.</div>
        </div>
        <button wire:click="gerar" wire:loading.attr="disabled"
            style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#1d4ed8;border:none;border-radius:7px;color:#fff;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;transition:opacity .15s;"
            wire:loading.class="opacity-60">
            <svg wire:loading.remove wire:target="gerar" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
            </svg>
            <svg wire:loading wire:target="gerar" style="animation:iapin .7s linear infinite;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            <span wire:loading.remove wire:target="gerar">Gerar Briefing</span>
            <span wire:loading wire:target="gerar">Gerando...</span>
        </button>
    </div>

    @if($erro)
    <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;margin-bottom:12px;font-size:13px;color:#dc2626;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $erro }}
    </div>
    @endif
    @endif

    <style>
    @keyframes iapin { to { transform: rotate(360deg); } }
    @keyframes iabar { 0%{background-position:0%} 100%{background-position:200%} }
    </style>
</div>
