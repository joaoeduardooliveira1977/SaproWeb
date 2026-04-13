@extends('layouts.app')

@section('page-title', 'Painel de Alertas')

@section('content')
<div style="width:100%;min-height:200px;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:8px;">
                <svg aria-hidden="true" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Painel de Alertas
            </h2>
            <p style="font-size:13px;color:#64748b;margin-top:2px;">
                Prazos, audiências e agenda pendentes — ordenados por urgência
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('prazos') }}" class="btn btn-secondary btn-sm">Gerenciar Prazos</a>
            <a href="{{ route('agenda') }}"  class="btn btn-secondary btn-sm">Agenda</a>
            <a href="{{ route('audiencias') }}" class="btn btn-secondary btn-sm">Audiências</a>
        </div>
    </div>

    @livewire('sla-monitor')

</div>
@endsection
