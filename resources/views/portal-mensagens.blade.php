@extends('layouts.app')
@section('title', 'Mensagens do Portal')
@section('content')
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">💬 Mensagens do Portal</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">Comunicação com clientes via portal</p>
</div>
@livewire('portal-mensagens')
@endsection
