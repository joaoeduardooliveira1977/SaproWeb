{{-- custas-pendentes.blade.php --}}
@extends('pdf.layout')
@section('content')
<div class="meta">
    <span>Período: {{ $data_ini }} a {{ $data_fim }}</span>
    <span>Total: {{ $total }} custa(s)</span>
    <span>Valor total: R$ {{ number_format($valor_total, 2, ',', '.') }}</span>
</div>
<table>
    <thead>
        <tr><th>Data</th><th>Processo</th><th>Cliente</th><th>Descrição</th><th style="text-align:right;">Valor</th></tr>
    </thead>
    <tbody>
        @forelse($custas as $c)
        <tr>
            <td>{{ $c['data'] }}</td>
            <td><strong>{{ $c['processo'] ?? '—' }}</strong></td>
            <td>{{ $c['cliente'] ?? '—' }}</td>
            <td>{{ $c['descricao'] }}</td>
            <td style="text-align:right; font-weight:bold; color:#dc2626;">R$ {{ number_format($c['valor'], 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty">Nenhuma custa pendente no período.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="total-box">
    <div class="total-item"><div class="total-valor">{{ $total }}</div><div class="total-label">Custas Pendentes</div></div>
    <div class="total-item"><div class="total-valor">R$ {{ number_format($valor_total, 2, ',', '.') }}</div><div class="total-label">Valor Total</div></div>
</div>
@endsection
