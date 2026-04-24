@extends('pdf.layout')
@section('content')

<div class="meta">
    <span>Total de custas: {{ $totalCustas }}</span>
    <span>Valor total a reembolsar: R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
    <span>Status: pagas pelo escritório, ainda não cobradas do cliente</span>
</div>

@forelse($porCliente as $clienteNome => $custas)
@php
    $totalCliente = $custas->sum('valor');
    $porProcesso  = $custas->groupBy(fn($c) => $c->processo?->numero ?? '—');
@endphp

<div class="section-title">{{ $clienteNome }} — R$ {{ number_format($totalCliente, 2, ',', '.') }}</div>

@foreach($porProcesso as $numeroProcesso => $custasProc)
@php $totalProc = $custasProc->sum('valor'); @endphp

<table>
    <thead>
        <tr>
            <th colspan="3" style="background:#f0f9ff;color:#0369a1;">
                Processo {{ $numeroProcesso }} — Total: R$ {{ number_format($totalProc, 2, ',', '.') }}
            </th>
        </tr>
        <tr>
            <th>Data</th>
            <th>Descrição</th>
            <th style="text-align:right;">Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($custasProc as $c)
        <tr>
            <td style="white-space:nowrap;">{{ $c->data->format('d/m/Y') }}</td>
            <td>{{ $c->descricao }}</td>
            <td style="text-align:right;font-weight:bold;color:#c2410c;">
                R$ {{ number_format($c->valor, 2, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach

@empty
<div class="empty">Nenhuma custa paga e reembolsável pendente de cobrança.</div>
@endforelse

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">{{ $totalCustas }}</div>
        <div class="total-label">Custas a Cobrar</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ $porCliente->count() }}</div>
        <div class="total-label">Clientes</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:#c2410c;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
        <div class="total-label">Total a Reembolsar</div>
    </div>
</div>

@endsection
