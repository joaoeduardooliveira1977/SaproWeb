@extends('pdf.layout')

@section('content')

<div class="meta">
    <span>Cliente: <strong>{{ $clienteNome }}</strong></span>
    <span>Período: {{ $data_ini }} a {{ $data_fim }}</span>
    <span>Tipo: {{ $tipoLabel }}</span>
    <span>Total de andamentos: {{ $total }}</span>
</div>

@if($processos->isEmpty())
    <div class="empty">Nenhum andamento encontrado para os filtros selecionados.</div>
@else

@foreach($processos as $processo)
<div class="section-title">
    ⚖ {{ $processo->numero ?: '(sem número)' }}
    @if($processo->cliente) — {{ $processo->cliente->nome }} @endif
    @if($processo->parte_contraria) · {{ $processo->parte_contraria }} @endif
</div>

<table>
    <thead>
        <tr>
            <th style="width:90px;">Data</th>
            <th>Descrição do Andamento</th>
        </tr>
    </thead>
    <tbody>
        @foreach($processo->andamentos as $andamento)
        <tr>
            <td style="white-space:nowrap; font-weight:bold;">
                {{ $andamento->data->format('d/m/Y') }}
            </td>
            <td style="line-height:1.5;">{{ $andamento->descricao }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="text-align:right; font-size:9px; color:#64748b; margin-bottom:16px; margin-top:-8px;">
    Total neste processo: <strong>{{ $processo->andamentos->count() }}</strong> andamento(s)
</div>
@endforeach

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">{{ $processos->count() }}</div>
        <div class="total-label">Processos</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ $total }}</div>
        <div class="total-label">Total de Andamentos</div>
    </div>
    <div class="total-item">
        <div class="total-valor">{{ $data_ini }} — {{ $data_fim }}</div>
        <div class="total-label">Período</div>
    </div>
</div>

@endif

@endsection
