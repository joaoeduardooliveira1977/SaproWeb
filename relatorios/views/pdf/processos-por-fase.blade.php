@extends('pdf.layout')
@section('content')

<div class="meta">
    <span>Status: {{ $status }}</span>
    <span>Total geral: {{ $total }} processos</span>
</div>

@foreach($fases as $f)
<div class="section-title">🔄 {{ $f['fase'] }} — {{ $f['total'] }} processo(s)</div>
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Cliente</th>
            <th>Advogado</th>
            <th>Risco</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($f['processos'] as $p)
        <tr>
            <td><strong>{{ $p->numero }}</strong></td>
            <td>{{ $p->cliente?->nome ?? '—' }}</td>
            <td>{{ $p->advogado?->nome ?? '—' }}</td>
            <td>
                @if($p->risco)
                <span class="risco-dot" style="background:{{ $p->risco->cor_hex }}"></span>{{ $p->risco->descricao }}
                @else —
                @endif
            </td>
            <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach

@if($total == 0)
<div class="empty">Nenhum processo encontrado.</div>
@endif

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">{{ $total }}</div>
        <div class="total-label">Total de Processos</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ count($fases) }}</div>
        <div class="total-label">Fases com processos</div>
    </div>
</div>
@endsection
