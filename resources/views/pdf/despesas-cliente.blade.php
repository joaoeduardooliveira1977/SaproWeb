@extends('pdf.layout')
@section('content')

<div class="meta">
    <span><strong>Cliente:</strong> {{ $cliente->nome }}</span>
    <span><strong>Período:</strong> {{ \Carbon\Carbon::parse($dataIni)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</span>
    <span><strong>Total:</strong> {{ count($lancamentos) }} lançamento(s)</span>
</div>

@if($cliente->cpf_cnpj)
<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:9px;color:#475569;">
    <strong>CNPJ/CPF:</strong> {{ $cliente->cpf_cnpj }}
    @if($cliente->filial) &nbsp;·&nbsp; <strong>Filial:</strong> {{ $cliente->filial->nome }} @endif
    @if($cliente->referencia) &nbsp;·&nbsp; <strong>Referência:</strong> {{ $cliente->referencia }} @endif
</div>
@endif

@if($lancamentos->isEmpty())
    <div class="empty">Nenhum lançamento encontrado para o período selecionado.</div>
@else

<div class="section-title">DESPESAS</div>
@php $totalDespesas = 0; @endphp
@if($lancamentos->where('tipo','despesa')->count())
<table>
    <thead>
        <tr>
            <th style="width:12%">Data</th>
            <th style="width:58%">Descrição</th>
            <th style="width:15%">Responsável</th>
            <th style="width:15%;text-align:right">Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lancamentos->where('tipo','despesa') as $l)
        @php $totalDespesas += $l->valor; @endphp
        <tr>
            <td>{{ $l->data_lancamento->format('d/m/Y') }}</td>
            <td>{{ $l->descricao }}</td>
            <td>{{ $l->responsavel?->nome ?? '—' }}</td>
            <td style="text-align:right;font-weight:bold;">R$ {{ number_format($l->valor,2,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="font-size:9px;color:#94a3b8;padding:8px 0;">Nenhuma despesa no período.</p>
@endif

<div class="section-title">REEMBOLSOS</div>
@php $totalReembolsos = 0; @endphp
@if($lancamentos->where('tipo','reembolso')->count())
<table>
    <thead>
        <tr>
            <th style="width:12%">Data</th>
            <th style="width:58%">Descrição</th>
            <th style="width:15%">Responsável</th>
            <th style="width:15%;text-align:right">Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lancamentos->where('tipo','reembolso') as $l)
        @php $totalReembolsos += $l->valor; @endphp
        <tr>
            <td>{{ $l->data_lancamento->format('d/m/Y') }}</td>
            <td>{{ $l->descricao }}</td>
            <td>{{ $l->responsavel?->nome ?? '—' }}</td>
            <td style="text-align:right;font-weight:bold;">R$ {{ number_format($l->valor,2,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="font-size:9px;color:#94a3b8;padding:8px 0;">Nenhum reembolso no período.</p>
@endif

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($totalDespesas,2,',','.') }}</div>
        <div class="total-label">Total Despesas</div>
    </div>
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($totalReembolsos,2,',','.') }}</div>
        <div class="total-label">Total Reembolsos</div>
    </div>
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($totalDespesas + $totalReembolsos,2,',','.') }}</div>
        <div class="total-label">Total Geral</div>
    </div>
</div>
@endif

@endsection
