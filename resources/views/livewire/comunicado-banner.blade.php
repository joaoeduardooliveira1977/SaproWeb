<div>
@if(count($banners) > 0)
@php $b = $banners[$indiceAtual]; $ti = $b['info']; @endphp
<div style="padding:10px 16px;background:{{ $ti['bg'] }};border-bottom:1px solid {{ $ti['border'] }};color:{{ $ti['cor'] }};">
    <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:12px;">

        <span style="font-size:16px;flex-shrink:0;">{{ $ti['icon'] }}</span>

        <div style="flex:1;font-size:13px;">
            <strong>{{ $b['titulo'] }}</strong>
            @if($b['mensagem']) — {{ $b['mensagem'] }} @endif
        </div>

        {{-- Navegação entre múltiplos banners --}}
        @if(count($banners) > 1)
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12px;">
            <button wire:click="anterior" @if($indiceAtual === 0) disabled @endif
                    style="background:none;border:1px solid {{ $ti['border'] }};border-radius:4px;padding:2px 8px;cursor:pointer;color:{{ $ti['cor'] }};">‹</button>
            <span>{{ $indiceAtual + 1 }}/{{ count($banners) }}</span>
            <button wire:click="proximo" @if($indiceAtual === count($banners) - 1) disabled @endif
                    style="background:none;border:1px solid {{ $ti['border'] }};border-radius:4px;padding:2px 8px;cursor:pointer;color:{{ $ti['cor'] }};">›</button>
        </div>
        @endif

        <button wire:click="dispensar({{ $b['id'] }})"
                style="background:none;border:none;cursor:pointer;font-size:18px;color:{{ $ti['cor'] }};line-height:1;flex-shrink:0;opacity:.7;"
                title="Dispensar">✕</button>
    </div>
</div>
@endif
</div>
