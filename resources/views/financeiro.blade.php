@extends('layouts.app')

@section('content')
<div>
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">💰 Financeiro</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Pagamentos, recebimentos e apontamentos por processo</p>
    </div>

    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:20px;">
        <label style="font-size:13px; font-weight:600; color:#374151;">Selecione um processo:</label>
        <select id="processo-select" onchange="trocarProcesso(this.value)"
            style="margin-left:12px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; min-width:300px;">
            <option value="">— Selecione —</option>
            @foreach(\App\Models\Processo::with('cliente')->where('status','Ativo')->orderBy('numero')->get() as $p)
            <option value="{{ $p->id }}" {{ request('processo_id') == $p->id ? 'selected' : '' }}>
                {{ $p->numero }} — {{ $p->cliente?->nome ?? 'Sem cliente' }}
            </option>
            @endforeach
        </select>
    </div>

    @if(request('processo_id'))
        @livewire('financeiro', ['processoId' => (int) request('processo_id')])
    @else
    <div style="background:white; border-radius:12px; padding:48px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="font-size:48px; margin-bottom:12px;">💰</div>
        <p style="color:#64748b; font-size:14px;">Selecione um processo acima para ver o financeiro.</p>
    </div>
    @endif
</div>

<script>
function trocarProcesso(id) {
    if (id) window.location.href = '/financeiro?processo_id=' + id;
}
</script>
@endsection