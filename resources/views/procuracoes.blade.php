@extends('layouts.app')
@section('page-title', 'Procurações')
@section('breadcrumb')<a href="{{ route('pessoas') }}">Pessoas</a> <span class="sep">›</span> <span class="current">Procurações</span>@endsection

@section('content')
<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Controle de Procurações
            </h2>
            <p style="font-size:13px;color:var(--muted);margin:4px 0 0;">Gerencie as procurações emitidas por clientes.</p>
        </div>
    </div>
    @livewire('procuracoes')
</div>
@endsection
