@extends('pdf.layout')

@section('content')

<div class="meta">
    <span><strong>Sem andamento há:</strong> {{ $dias }} dias ou mais</span>
    <span><strong>Status:</strong> {{ $status }}</span>
    <span><strong>Total:</strong> {{ $total }} processo(s)</span>
</div>

@if(empty($processos))
    <div class="empty">Nenhum processo encontrado com este critério.</div>
@else

<table>
    <thead>
        <tr>
            <th>Processo</th>
            <th>Cliente</th>
            <th>Advogado</th>
            <th>Fase</th>
            <th style="text-align:center;">Último Andamento</th>
            <th style="text-align:center;">Dias Parado</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($processos as $p)
        @php
            $diasParado = $p->ultimo_andamento
                ? \Carbon\Carbon::parse($p->ultimo_andamento)->diffInDays(now())
                : null;
        @endphp
        <tr>
            <td style="font-weight:bold;">{{ $p->numero }}</td>
            <td>{{ $p->cliente_nome ?? '—' }}</td>
            <td>{{ $p->advogado_nome ?? '—' }}</td>
            <td>{{ $p->fase ?? '—' }}</td>
            <td style="text-align:center;">
                @if($p->ultimo_andamento)
                    {{ \Carbon\Carbon::parse($p->ultimo_andamento)->format('d/m/Y') }}
                @else
                    <span style="color:#dc2626;font-weight:bold;">Nunca</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($diasParado !== null)
                <span class="badge"
                    style="background:{{ $diasParado > 60 ? '#fee2e2' : ($diasParado > 30 ? '#fef3c7' : '#f1f5f9') }};
                           color:{{ $diasParado > 60 ? '#dc2626' : ($diasParado > 30 ? '#d97706' : '#64748b') }};">
                    {{ $diasParado }}d
                </span>
                @else
                <span class="badge badge-urgente">Sem registro</span>
                @endif
            </td>
            <td style="text-align:center;">
                <span class="badge {{ $p->status === 'Ativo' ? 'badge-ativo' : 'badge-encerrado' }}">
                    {{ $p->status }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">{{ $total }}</div>
        <div class="total-label">Processos Parados</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ collect($processos)->whereNull('ultimo_andamento')->count() }}</div>
        <div class="total-label">Nunca Tiveram Andamento</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ $dias }}+ dias</div>
        <div class="total-label">Critério Utilizado</div>
    </div>
</div>

@endif
@endsection
