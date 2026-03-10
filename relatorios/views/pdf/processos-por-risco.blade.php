{{-- processos-por-risco.blade.php --}}
@extends('pdf.layout')
@section('content')
<div class="meta">
    <span>Status: {{ $status }}</span>
    <span>Total: {{ $total }} processos</span>
</div>
@foreach($riscos as $r)
<div class="section-title">
    <span class="risco-dot" style="background:{{ $r['cor'] }}"></span>
    {{ $r['risco'] }} — {{ $r['total'] }} processo(s)
</div>
<table>
    <thead><tr><th>Número</th><th>Cliente</th><th>Advogado</th><th>Fase</th><th>Status</th></tr></thead>
    <tbody>
        @foreach($r['processos'] as $p)
        <tr>
            <td><strong>{{ $p->numero }}</strong></td>
            <td>{{ $p->cliente?->nome ?? '—' }}</td>
            <td>{{ $p->advogado?->nome ?? '—' }}</td>
            <td>{{ $p->fase?->descricao ?? '—' }}</td>
            <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@if($total == 0)<div class="empty">Nenhum processo encontrado.</div>@endif
<div class="total-box">
    <div class="total-item"><div class="total-valor">{{ $total }}</div><div class="total-label">Total de Processos</div></div>
    <div class="total-item"><div class="total-valor">{{ count($riscos) }}</div><div class="total-label">Graus de Risco</div></div>
</div>
@endsection
