<div wire:poll.600000ms="carregar">
<style>
  @keyframes mf-spin { to { transform: rotate(360deg); } }
  @keyframes mf-pulse { 0%,100% { opacity:1; } 50% { opacity:.3; } }
  .mf-spin { animation: mf-spin .7s linear infinite; display:inline-block; }
  .mf-live-dot {
    display:inline-block; width:7px; height:7px; border-radius:50%;
    background:#22c55e; margin-right:5px; vertical-align:middle;
    animation: mf-pulse 1.8s ease-in-out infinite;
  }
</style>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            📈 Mercado Financeiro
            @if(!$erro && !empty($cotacoes))
                <span class="mf-live-dot" title="Dados em tempo real"></span>
            @endif
        </span>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($atualizadoEm)
                <span style="font-size:11px;color:#94a3b8;">Atualizado às {{ $atualizadoEm }}</span>
            @endif
            <button wire:click="refresh" class="btn btn-secondary btn-sm" title="Atualizar">
                <span wire:loading.remove wire:target="refresh">↻</span>
                <span wire:loading wire:target="refresh" class="mf-spin">↻</span>
            </button>
        </div>
    </div>

    {{-- Skeleton loading --}}
    <div wire:loading wire:target="refresh,carregar" style="display:flex;flex-direction:column;gap:8px;padding:4px 0;">
        <div style="height:72px;border-radius:8px;background:linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);background-size:200% 100%;animation:mf-pulse 1.2s ease-in-out infinite;"></div>
        @foreach([1,2,3] as $_)
        <div style="height:58px;border-radius:8px;background:linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);background-size:200% 100%;animation:mf-pulse 1.2s ease-in-out infinite;"></div>
        @endforeach
    </div>

    <div wire:loading.remove wire:target="refresh,carregar">

    @if($erro)
        <p style="color:#dc2626;font-size:13px;text-align:center;padding:24px 0;">
            ⚠️ Não foi possível carregar os dados do mercado.<br>
            <button wire:click="refresh" style="background:none;border:none;color:#2563a8;cursor:pointer;font-size:13px;text-decoration:underline;margin-top:8px;">
                Tentar novamente
            </button>
        </p>
    @else

    {{-- IBOVESPA --}}
    @if(!empty($bovespa))
    @php
        $bvVar    = $bovespa['variacao'];
        $bvCor    = $bvVar >= 0 ? '#4ade80' : '#f87171';
        $bvBgCor  = $bvVar >= 0 ? '#16a34a' : '#dc2626';
        $bvSeta   = $bvVar >= 0 ? '▲' : '▼';
        $bvRange  = $bovespa['max'] - $bovespa['min'];
        $bvPos    = $bvRange > 0 ? round(($bovespa['valor'] - $bovespa['min']) / $bvRange * 100, 1) : 50;
        $bvPos    = max(0, min(100, $bvPos));
    @endphp
    <div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:10px;padding:16px;margin-bottom:12px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;">
            <div>
                <div style="font-size:10px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:1.2px;margin-bottom:4px;">
                    IBOVESPA · B3
                </div>
                <div style="font-size:24px;font-weight:700;color:#fff;letter-spacing:-.5px;line-height:1;">
                    {{ number_format($bovespa['valor'], 0, ',', '.') }}
                </div>
            </div>
            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;
                         background:{{ $bvBgCor }}22;border:1px solid {{ $bvBgCor }};
                         color:{{ $bvCor }};font-size:13px;font-weight:700;">
                {{ $bvSeta }} {{ number_format(abs($bvVar), 2, ',', '.') }}%
            </span>
        </div>
        {{-- Range bar --}}
        <div style="margin-top:4px;">
            <div style="display:flex;justify-content:space-between;font-size:10px;color:rgba(255,255,255,.35);margin-bottom:4px;">
                <span>Mín {{ number_format($bovespa['min'], 0, ',', '.') }}</span>
                <span>Máx {{ number_format($bovespa['max'], 0, ',', '.') }}</span>
            </div>
            <div style="position:relative;height:4px;background:rgba(255,255,255,.15);border-radius:2px;">
                <div style="position:absolute;left:0;top:0;height:100%;width:{{ $bvPos }}%;
                            background:{{ $bvCor }};border-radius:2px;transition:width .4s;"></div>
                <div style="position:absolute;top:-3px;left:calc({{ $bvPos }}% - 5px);
                            width:10px;height:10px;border-radius:50%;background:{{ $bvCor }};
                            box-shadow:0 0 4px {{ $bvCor }};transition:left .4s;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Moedas --}}
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($cotacoes as $m)
        @php
            $var    = $m['variacao'];
            $cor    = $var >= 0 ? '#16a34a' : '#dc2626';
            $corPill= $var >= 0 ? '#15803d' : '#b91c1c';
            $bg     = $var >= 0 ? '#f0fdf4' : '#fef2f2';
            $brd    = $var >= 0 ? '#bbf7d0' : '#fecaca';
            $seta   = $var >= 0 ? '▲' : '▼';
            $isBtc  = $m['sigla'] === 'BTC';
            $range  = $m['max'] - $m['min'];
            $pos    = $range > 0 ? round(($m['bid'] - $m['min']) / $range * 100, 1) : 50;
            $pos    = max(0, min(100, $pos));
            $trackBg = $var >= 0 ? '#bbf7d0' : '#fecaca';
            $fillBg  = $var >= 0 ? '#16a34a' : '#dc2626';
        @endphp
        <div style="padding:10px 14px;border-radius:8px;background:{{ $bg }};border:1px solid {{ $brd }};">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                <div style="font-size:20px;line-height:1;flex-shrink:0;">{{ $m['icone'] }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;font-weight:700;color:#1e293b;">
                        {{ $m['sigla'] }}
                        <span style="font-weight:400;color:#64748b;">{{ $m['nome'] }}</span>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:15px;font-weight:700;color:#1e293b;line-height:1.2;">
                        R$ {{ $isBtc ? number_format($m['bid'], 0, ',', '.') : number_format($m['bid'], 4, ',', '.') }}
                    </div>
                    <span style="display:inline-block;margin-top:2px;padding:1px 7px;border-radius:20px;
                                 background:{{ $corPill }};color:#fff;font-size:11px;font-weight:700;">
                        {{ $seta }} {{ number_format(abs($var), 2, ',', '.') }}%
                    </span>
                </div>
            </div>
            {{-- Range bar --}}
            <div>
                <div style="display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;margin-bottom:3px;">
                    <span>{{ $isBtc ? 'R$ '.number_format($m['min'],0,',','.') : number_format($m['min'],4,',','.') }}</span>
                    <span>{{ $isBtc ? 'R$ '.number_format($m['max'],0,',','.') : number_format($m['max'],4,',','.') }}</span>
                </div>
                <div style="position:relative;height:3px;background:{{ $trackBg }};border-radius:2px;">
                    <div style="position:absolute;left:0;top:0;height:100%;width:{{ $pos }}%;
                                background:{{ $fillBg }};border-radius:2px;"></div>
                    <div style="position:absolute;top:-3.5px;left:calc({{ $pos }}% - 4px);
                                width:8px;height:8px;border-radius:50%;background:{{ $fillBg }};
                                box-shadow:0 0 3px {{ $fillBg }};"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @endif
    </div>{{-- /wire:loading.remove --}}
</div>
</div>
