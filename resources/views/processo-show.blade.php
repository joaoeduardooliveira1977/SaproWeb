@extends('layouts.app')

@section('content')
<div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">⚖️ Processo {{ $processo->numero }}</h2>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('processos.editar', $processo->id) }}"
                style="padding:8px 18px; background:#2563a8; color:white; border-radius:8px; font-size:13px; text-decoration:none;">
                ✏️ Editar
            </a>

		<a href="{{ route('processos.andamentos', $processo->id) }}"
    		style="padding:8px 18px; background:#16a34a; color:white; border-radius:8px; font-size:13px; text-decoration:none;">
    		📋 Andamentos
		</a>






            <a href="{{ route('processos') }}"
                style="padding:8px 18px; background:#f1f5f9; color:#334155; border-radius:8px; font-size:13px; text-decoration:none;">
                ← Voltar
            </a>
        </div>
    </div>

    {{-- Dados principais --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <h3 style="font-size:14px; font-weight:700; color:#1a3a5c; margin-bottom:16px;">📋 Dados do Processo</h3>
            <table style="width:100%; font-size:13px;">
                <tr><td style="color:#64748b; padding:4px 0; width:40%;">Número:</td><td style="font-weight:600;">{{ $processo->numero }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Distribuição:</td><td>{{ $processo->data_distribuicao?->format('d/m/Y') ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Tipo de Ação:</td><td>{{ $processo->tipoAcao?->descricao ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Fase:</td><td>{{ $processo->fase?->descricao ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Vara:</td><td>{{ $processo->vara ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Status:</td>
                    <td><span style="background:{{ $processo->status === 'Ativo' ? '#dcfce7' : '#f1f5f9' }}; color:{{ $processo->status === 'Ativo' ? '#16a34a' : '#64748b' }}; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $processo->status }}</span></td>
                </tr>
                <tr><td style="color:#64748b; padding:4px 0;">Risco:</td>
                    <td>
                        @if($processo->risco)
                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:{{ $processo->risco->cor_hex }}; margin-right:6px;"></span>
                        {{ $processo->risco->descricao }}
                        @else —
                        @endif
                    </td>
                </tr>
                <tr><td style="color:#64748b; padding:4px 0;">Valor da Causa:</td><td>R$ {{ number_format($processo->valor_causa, 2, ',', '.') }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Valor em Risco:</td><td>R$ {{ number_format($processo->valor_risco, 2, ',', '.') }}</td></tr>
            </table>
        </div>

        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <h3 style="font-size:14px; font-weight:700; color:#1a3a5c; margin-bottom:16px;">👥 Partes</h3>
            <table style="width:100%; font-size:13px;">
                <tr><td style="color:#64748b; padding:4px 0; width:40%;">Cliente:</td><td style="font-weight:600;">{{ $processo->cliente?->nome ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Parte Contrária:</td><td>{{ $processo->parte_contraria ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Advogado:</td><td>{{ $processo->advogado?->nome ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Juiz:</td><td>{{ $processo->juiz?->nome ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Secretaria:</td><td>{{ $processo->secretaria?->descricao ?? '—' }}</td></tr>
                <tr><td style="color:#64748b; padding:4px 0;">Repartição:</td><td>{{ $processo->reparticao?->descricao ?? '—' }}</td></tr>
            </table>
            @if($processo->observacoes)
            <div style="margin-top:16px; padding:12px; background:#f8fafc; border-radius:8px; font-size:13px; color:#64748b;">
                <strong>Observações:</strong><br>{{ $processo->observacoes }}
            </div>
            @endif
        </div>
    </div>

    {{-- Andamentos --}}
    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:20px;">
        <h3 style="font-size:14px; font-weight:700; color:#1a3a5c; margin-bottom:16px;">📝 Andamentos</h3>
        @forelse($processo->andamentos as $a)
        <div style="display:flex; gap:16px; padding:10px 0; border-bottom:1px solid #f1f5f9;">
            <div style="font-size:12px; color:#64748b; min-width:80px;">{{ $a->data->format('d/m/Y') }}</div>
            <div style="font-size:13px;">{{ $a->descricao }}</div>
        </div>
        @empty
        <p style="color:#94a3b8; font-size:13px; text-align:center; padding:20px;">Nenhum andamento registrado.</p>
        @endforelse
    </div>

    {{-- Agenda --}}
    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <h3 style="font-size:14px; font-weight:700; color:#1a3a5c; margin-bottom:16px;">📅 Agenda</h3>
        @forelse($processo->agenda as $a)
        <div style="display:flex; gap:16px; padding:10px 0; border-bottom:1px solid #f1f5f9;">
            <div style="font-size:12px; color:#64748b; min-width:110px;">{{ $a->data_hora->format('d/m/Y H:i') }}</div>
            <div style="flex:1; font-size:13px;">
                {{ $a->titulo }}
                @if($a->urgente) <span style="background:#fee2e2; color:#dc2626; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; margin-left:6px;">URGENTE</span> @endif
            </div>
            <div style="font-size:12px; color:#64748b;">{{ $a->tipo }}</div>
        </div>
        @empty
        <p style="color:#94a3b8; font-size:13px; text-align:center; padding:20px;">Nenhum compromisso na agenda.</p>
        @endforelse
    </div>
</div>
@endsection