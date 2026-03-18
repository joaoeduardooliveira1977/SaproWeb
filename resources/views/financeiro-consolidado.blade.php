@extends('layouts.app')

@section('title', 'Financeiro Consolidado')

@section('content')
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:8px;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Financeiro Consolidado</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">Visão geral de recebimentos, despesas, fluxo de caixa e honorários</p>
</div>

@livewire('financeiro-consolidado')
@endsection
