@extends('layouts.app')

@section('title', 'Prazos')

@section('content')
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">⏳ Controle de Prazos</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">Gerencie prazos processuais com cálculo automático de dias úteis e corridos</p>
</div>

@livewire('prazos')
@endsection
