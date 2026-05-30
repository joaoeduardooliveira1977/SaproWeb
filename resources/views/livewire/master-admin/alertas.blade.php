@section('page-title', 'Alertas')
<div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <p style="font-size:13px;color:#64748b;">{{ count($alertas) }} alerta(s) ativo(s)</p>
        <button wire:click="carregarAlertas" class="btn btn-sm btn-outline">↻ Verificar agora</button>
    </div>

    @php
        $criticos = collect($alertas)->where('nivel', 'critico');
        $avisos   = collect($alertas)->where('nivel', 'aviso');
    @endphp

    @if($criticos->isEmpty() && $avisos->isEmpty())
    <div class="card">
        <div style="padding:48px;text-align:center;">
            <div style="font-size:48px;margin-bottom:12px;">✅</div>
            <div style="font-size:16px;font-weight:700;color:var(--success);">Tudo certo!</div>
            <div style="font-size:13px;color:#64748b;margin-top:6px;">Nenhum alerta ativo no momento.</div>
        </div>
    </div>
    @else

    {{-- ── Críticos ── --}}
    @if($criticos->isNotEmpty())
    <div class="card" style="margin-bottom:16px;border-left:4px solid #dc2626;">
        <div class="card-header" style="background:#fef2f2;">
            <span class="card-title" style="color:#dc2626;">🔴 Críticos ({{ $criticos->count() }})</span>
        </div>
        @foreach($criticos as $a)
        <div class="alert-row" style="background:{{ $loop->even ? '#fef9f9' : '#fff' }};">
            <div class="icon">{{ $a['icone'] }}</div>
            <div class="body">
                <strong>{{ $a['titulo'] }}</strong>
                <span>{{ $a['detalhe'] }}</span>
            </div>
            <a href="{{ $a['link'] }}" class="btn btn-sm btn-danger">Ver →</a>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Avisos ── --}}
    @if($avisos->isNotEmpty())
    <div class="card" style="border-left:4px solid #d97706;">
        <div class="card-header" style="background:#fffbeb;">
            <span class="card-title" style="color:#d97706;">🟡 Avisos ({{ $avisos->count() }})</span>
        </div>
        @foreach($avisos as $a)
        <div class="alert-row" style="background:{{ $loop->even ? '#fffdf5' : '#fff' }};">
            <div class="icon">{{ $a['icone'] }}</div>
            <div class="body">
                <strong>{{ $a['titulo'] }}</strong>
                <span>{{ $a['detalhe'] }}</span>
            </div>
            <a href="{{ $a['link'] }}" class="btn btn-sm btn-outline">Ver →</a>
        </div>
        @endforeach
    </div>
    @endif

    @endif

</div>
