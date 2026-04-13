@extends('layouts.app')

@section('page-title', 'Financeiro')
@section('breadcrumb')Financeiro <span class="sep">›</span> <span class="current">Por Processo</span>@endsection
@section('content')
<div>
    <div style="margin-bottom:18px;">
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Financeiro por Processo</h1>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            Pagamentos, recebimentos e apontamentos por processo
            <span style="color:#cbd5e1;margin:0 6px;">|</span>
            <a href="{{ route('financeiro.hub') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Voltar para central</a>
        </p>
    </div>

    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Processo</label>
        <select id="processo-select" onchange="trocarProcesso(this.value)"
            style="padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;min-width:320px;background:var(--white);color:var(--text);outline:none;">
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
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:36px;text-align:center;">
        <div style="margin-bottom:12px;display:flex;justify-content:center;"><svg aria-hidden="true" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
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
