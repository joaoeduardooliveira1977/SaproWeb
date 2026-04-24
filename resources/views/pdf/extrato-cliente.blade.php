@extends('pdf.layout')
@section('content')

{{-- Cabeçalho do cliente --}}
<div class="meta">
    <span><strong>Cliente:</strong> {{ $cliente->nome }}</span>
    @if($cliente->cpf_cnpj)
    <span><strong>CPF/CNPJ:</strong> {{ $cliente->cpf_cnpj }}</span>
    @endif
    @if($cliente->email)
    <span><strong>E-mail:</strong> {{ $cliente->email }}</span>
    @endif
    <span><strong>Período:</strong> {{ $data_ini }} a {{ $data_fim }}</span>
</div>

{{-- Resumo --}}
<div class="total-box" style="margin-bottom:18px;">
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($totais['cobrado'], 2, ',', '.') }}</div>
        <div class="total-label">Total Cobrado</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:#16a34a;">R$ {{ number_format($totais['recebido'], 2, ',', '.') }}</div>
        <div class="total-label">Total Recebido</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:{{ $totais['a_receber'] > 0 ? '#dc2626' : '#64748b' }};">R$ {{ number_format($totais['a_receber'], 2, ',', '.') }}</div>
        <div class="total-label">A Receber</div>
    </div>
    @if($totais['custas_pendentes'] > 0)
    <div class="total-item">
        <div class="total-valor" style="color:#ea580c;">R$ {{ number_format($totais['custas_pendentes'], 2, ',', '.') }}</div>
        <div class="total-label">Custas Pendentes ({{ $totais['custas_qtd'] }})</div>
    </div>
    @endif
    <div class="total-item">
        <div class="total-valor" style="color:{{ $totais['saldo_devedor'] > 0 ? '#7c2d12' : '#15803d' }};font-size:18px;">R$ {{ number_format($totais['saldo_devedor'], 2, ',', '.') }}</div>
        <div class="total-label">Saldo Devedor</div>
    </div>
</div>

{{-- Lançamentos --}}
@if($lancamentos->isNotEmpty())
<div class="section-title">Lançamentos Financeiros ({{ $lancamentos->count() }})</div>
<table>
    <thead>
        <tr>
            <th style="width:80px;">Vencimento</th>
            <th>Descrição</th>
            <th>Processo</th>
            <th style="text-align:right;width:90px;">Valor</th>
            <th style="text-align:right;width:90px;">Recebido</th>
            <th style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lancamentos as $l)
        @php
            $statusCor = match($l->status) {
                'recebido' => '#16a34a',
                'atrasado' => '#dc2626',
                default    => '#64748b',
            };
            $statusLabel = match($l->status) {
                'previsto' => 'Previsto',
                'atrasado' => 'Atrasado',
                'recebido' => 'Recebido',
                default    => $l->status,
            };
        @endphp
        <tr>
            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($l->vencimento)->format('d/m/Y') }}</td>
            <td>
                {{ $l->descricao }}
                @if($l->numero_parcela && $l->total_parcelas)
                <span style="color:#94a3b8;font-size:10px;">({{ $l->numero_parcela }}/{{ $l->total_parcelas }})</span>
                @endif
                @if($l->contrato_desc)
                <div style="font-size:10px;color:#64748b;">{{ $l->contrato_desc }}</div>
                @endif
            </td>
            <td style="color:#64748b;font-size:11px;">{{ $l->processo_numero ?? '—' }}</td>
            <td style="text-align:right;font-weight:600;">R$ {{ number_format($l->valor, 2, ',', '.') }}</td>
            <td style="text-align:right;color:#16a34a;">
                {{ $l->valor_pago ? 'R$ ' . number_format($l->valor_pago, 2, ',', '.') : '—' }}
                @if($l->data_pagamento)
                <div style="font-size:9px;color:#94a3b8;">{{ \Carbon\Carbon::parse($l->data_pagamento)->format('d/m/Y') }}</div>
                @endif
            </td>
            <td style="text-align:center;">
                <span style="font-size:10px;font-weight:700;color:{{ $statusCor }};">{{ $statusLabel }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Custas a reembolsar --}}
@if($custasReembolso->isNotEmpty())
<div class="section-title" style="margin-top:20px;">Custas a Reembolsar ({{ $custasReembolso->count() }})</div>
<table>
    <thead>
        <tr>
            <th style="width:80px;">Data</th>
            <th>Descrição</th>
            <th>Processo</th>
            <th style="text-align:right;width:100px;">Valor</th>
            <th style="width:70px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($custasReembolso as $c)
        <tr>
            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($c->data)->format('d/m/Y') }}</td>
            <td>{{ $c->descricao }}</td>
            <td style="color:#64748b;font-size:11px;">{{ $c->processo_numero ?? '—' }}</td>
            <td style="text-align:right;font-weight:bold;color:#ea580c;">R$ {{ number_format($c->valor, 2, ',', '.') }}</td>
            <td style="text-align:center;font-size:10px;font-weight:700;color:#ea580c;">Pendente</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($lancamentos->isEmpty() && $custasReembolso->isEmpty())
<div class="empty">Nenhum lançamento encontrado para o período selecionado.</div>
@endif

@endsection
