@extends('layouts.app')
@section('title', 'Mensagens do Portal')
@section('content')
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:8px;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Mensagens do Portal</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">Comunicação com clientes via portal</p>
</div>
@livewire('portal-mensagens')
@endsection
