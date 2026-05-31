<div>
@if($visivel && count($modais) > 0)
@php $m = $modais[$indice]; $ti = $m['info']; @endphp
<div style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:16px;padding:36px 32px;max-width:460px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.25);text-align:center;">

        <div style="font-size:48px;margin-bottom:12px;">{{ $ti['icon'] }}</div>

        <div style="font-size:17px;font-weight:800;color:#1e293b;margin-bottom:10px;">
            {{ $m['titulo'] }}
        </div>

        <div style="font-size:14px;color:#475569;line-height:1.7;margin-bottom:24px;text-align:left;white-space:pre-line;">{{ $m['mensagem'] }}</div>

        @if(count($modais) > 1)
        <div style="font-size:12px;color:#94a3b8;margin-bottom:14px;">
            {{ $indice + 1 }} de {{ count($modais) }} comunicado(s)
        </div>
        @endif

        <button wire:click="entendido"
                style="width:100%;padding:12px;background:{{ $ti['cor'] }};color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;">
            {{ ($indice + 1 < count($modais)) ? 'Próximo →' : 'Entendido' }}
        </button>

    </div>
</div>
@endif
</div>
