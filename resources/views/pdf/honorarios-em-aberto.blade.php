@extends('pdf.layout')

@section('content')

<div class="meta">
    <span><strong>Cliente:</strong> {{ $clienteNome }}</span>
    <span><strong>Status:</strong> {{ $status === 'todos' ? 'Pendentes e Atrasados' : ucfirst($status) }}</span>
    <span><strong>Total de parcelas:</strong> {{ $total }}</span>
    <span><strong>Valor total:</strong> R$ {{ number_format($valor_total, 2, ',', '.') }}</span>
</div>

@if($parcelas->isEmpty())
    <div class="empty">Nenhuma parcela em aberto encontrada.</div>
@else

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Processo</th>
            <th>Honorário / Tipo</th>
            <th style="text-align:center;">Parcela</th>
            <th style="text-align:right;">Valor</th>
            <th style="text-align:center;">Vencimento</th>
            <th style="text-align:center;">Atraso</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parcelas as $p)
        <tr>
            <td style="font-weight:bold;">{{ $p['cliente'] }}</td>
            <td>{{ $p['processo'] }}</td>
            <td>
                <div>{{ $p['honorario'] }}</div>
                <div style="font-size:8px;color:#64748b;">{{ $p['tipo'] }}</div>
            </td>
            <td style="text-align:center;">{{ $p['parcela'] }}</td>
            <td style="text-align:right;font-weight:bold;">R$ {{ number_format($p['valor'], 2, ',', '.') }}</td>
            <td style="text-align:center;">{{ $p['vencimento'] }}</td>
            <td style="text-align:center;">
                @if($p['atraso'])
                <span class="badge" style="background:#fee2e2;color:#dc2626;">{{ $p['atraso'] }}</span>
                @else
                <span style="color:#64748b;font-size:8px;">—</span>
                @endif
            </td>
            <td style="text-align:center;">
                <span class="badge {{ $p['status'] === 'atrasado' ? 'badge-urgente' : '' }}"
                    style="{{ $p['status'] === 'pendente' ? 'background:#fef3c7;color:#d97706;' : '' }}">
                    {{ ucfirst($p['status']) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">{{ $total }}</div>
        <div class="total-label">Parcelas em Aberto</div>
    </div>
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($valor_total, 2, ',', '.') }}</div>
        <div class="total-label">Total a Receber</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ $parcelas->where('status', 'atrasado')->count() }}</div>
        <div class="total-label">Parcelas Atrasadas</div>
    </div>
</div>

@endif
@endsection
