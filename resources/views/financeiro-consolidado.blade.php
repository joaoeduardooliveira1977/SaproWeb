@extends('layouts.app')

@section('title', 'Financeiro Consolidado')

@section('content')
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">💰 Financeiro Consolidado</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">Visão geral de recebimentos, despesas, fluxo de caixa e honorários</p>
</div>

@livewire('financeiro-consolidado')
@endsection
