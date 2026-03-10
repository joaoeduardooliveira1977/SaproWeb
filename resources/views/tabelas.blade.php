@extends('layouts.app')

@section('content')
<div>
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">📋 Tabelas de Domínio</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Dados auxiliares do sistema</p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
        @foreach([
            ['Fases do Processo', 'fases', '🔄'],
            ['Graus de Risco', 'graus_risco', '⚠️'],
            ['Tipos de Ação', 'tipos_acao', '⚖️'],
            ['Tipos de Processo', 'tipos_processo', '📁'],
            ['Assuntos', 'assuntos', '📌'],
            ['Repartições', 'reparticoes', '🏛️'],
            ['Secretarias', 'secretarias', '🏢'],
            ['Índices Monetários', 'indices_monetarios', '💰'],
        ] as [$nome, $tabela, $icon])
        <div style="background:white; border-radius:12px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:24px; margin-bottom:8px;">{{ $icon }}</div>
            <div style="font-weight:600; color:#1a3a5c; margin-bottom:4px;">{{ $nome }}</div>
            <div style="font-size:13px; color:#64748b;">
                {{ DB::table($tabela)->count() }} registros
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection