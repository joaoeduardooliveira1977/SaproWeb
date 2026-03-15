@extends('layouts.app')
@section('page-title', 'Templates de Minutas')

@section('content')
<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">📄 Templates de Minutas</h2>
            <p style="font-size:13px;color:var(--muted);margin-top:2px;">
                Crie modelos de documentos com placeholders para preencher automaticamente com dados do processo.
            </p>
        </div>
    </div>

    @livewire('minutas')
</div>
@endsection
