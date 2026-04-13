@extends('pdf.layout')

@section('content')

<div class="meta">
    <span>Status: {{ $status }}</span>
    <span>Total: {{ $total }} processos</span>
</div>

@if($tipos->isEmpty())
    <div class="empty">Nenhum processo encontrado.</div>
@else
@foreach($tipos as $grupo)
    <div class="section-title">{{ $grupo['tipo'] }} &nbsp;({{ $grupo['total'] }})</div>
    <table>
        <thead>
            <tr>
                <th style="width:25%">Número</th>
                <th style="width:15%">Status</th>
                <th style="width:30%">Cliente</th>
                <th style="width:20%">Advogado</th>
                <th style="width:10%">Fase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grupo['processos'] as $p)
            <tr>
                <td style="font-weight:bold">{{ $p->numero }}</td>
                <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
                <td>{{ $p->cliente?->nome ?? '—' }}</td>
                <td>{{ $p->advogado?->nome ?? '—' }}</td>
                <td>{{ $p->fase?->descricao ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">{{ $total }}</div>
        <div class="total-label">Total de Processos</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ $tipos->count() }}</div>
        <div class="total-label">Tipos de Ação</div>
    </div>
</div>
@endif

@endsection
