@extends('pdf.layout')

@section('content')

<div class="meta">
    <span><strong>Mês:</strong> {{ $mesNome }}</span>
    <span><strong>Cliente:</strong> {{ $clienteNome }}</span>
    <span><strong>Total de lançamentos:</strong> {{ $lancamentos->count() }}</span>
</div>

{{-- KPI summary --}}
<table class="summary-grid" style="border:0;margin-bottom:16px;">
    <tr>
        <td style="border:0;background:transparent!important;padding:4px;">
            <div class="summary-card" style="border-left-color:#16a34a;">
                <div class="summary-label">Receita Prevista</div>
                <div class="summary-value" style="color:#16a34a;">R$ {{ number_format($totais['receita_prevista'], 2, ',', '.') }}</div>
                <div class="summary-note">a receber no mês</div>
            </div>
        </td>
        <td style="border:0;background:transparent!important;padding:4px;">
            <div class="summary-card" style="border-left-color:#2563a8;">
                <div class="summary-label">Receita Recebida</div>
                <div class="summary-value" style="color:#2563a8;">R$ {{ number_format($totais['receita_recebida'], 2, ',', '.') }}</div>
                <div class="summary-note">efetivamente recebido</div>
            </div>
        </td>
        <td style="border:0;background:transparent!important;padding:4px;">
            <div class="summary-card" style="border-left-color:#dc2626;">
                <div class="summary-label">Receita Atrasada</div>
                <div class="summary-value" style="color:#dc2626;">R$ {{ number_format($totais['receita_atrasada'], 2, ',', '.') }}</div>
                <div class="summary-note">vencida e não recebida</div>
            </div>
        </td>
        <td style="border:0;background:transparent!important;padding:4px;">
            <div class="summary-card" style="border-left-color:#d97706;">
                <div class="summary-label">Despesas</div>
                <div class="summary-value" style="color:#d97706;">R$ {{ number_format($totais['despesa_total'], 2, ',', '.') }}</div>
                <div class="summary-note">total previsto</div>
            </div>
        </td>
        <td style="border:0;background:transparent!important;padding:4px;">
            <div class="summary-card" style="border-left-color:{{ $totais['saldo'] >= 0 ? '#16a34a' : '#dc2626' }};">
                <div class="summary-label">Saldo Líquido</div>
                <div class="summary-value" style="color:{{ $totais['saldo'] >= 0 ? '#16a34a' : '#dc2626' }};">
                    R$ {{ number_format(abs($totais['saldo']), 2, ',', '.') }}
                </div>
                <div class="summary-note">{{ $totais['saldo'] >= 0 ? 'positivo' : 'negativo' }} (recebido − pago)</div>
            </div>
        </td>
    </tr>
</table>

{{-- Receitas --}}
@if($receitas->isNotEmpty())
<div class="section-title" style="border-left-color:#16a34a;">
    Receitas — {{ $receitas->count() }} lançamento(s)
</div>
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Descrição / Contrato</th>
            <th style="text-align:center;">Vencimento</th>
            <th style="text-align:right;">Valor</th>
            <th style="text-align:right;">Recebido</th>
            <th style="text-align:center;">Data Pgto</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receitas as $l)
        <tr>
            <td style="font-weight:bold;">{{ $l->cliente_nome }}</td>
            <td>
                <div>{{ $l->descricao }}</div>
                @if($l->contrato_desc)
                <div style="font-size:8px;color:#64748b;">{{ $l->contrato_desc }}</div>
                @endif
            </td>
            <td style="text-align:center;">{{ \Carbon\Carbon::parse($l->vencimento)->format('d/m/Y') }}</td>
            <td style="text-align:right;">R$ {{ number_format($l->valor, 2, ',', '.') }}</td>
            <td style="text-align:right;">
                @if($l->valor_pago)
                    R$ {{ number_format($l->valor_pago, 2, ',', '.') }}
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($l->data_pagamento)
                    {{ \Carbon\Carbon::parse($l->data_pagamento)->format('d/m/Y') }}
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($l->status === 'recebido')
                    <span class="badge badge-ok">Recebido</span>
                @elseif($l->status === 'atrasado')
                    <span class="badge badge-urgente">Atrasado</span>
                @else
                    <span class="badge" style="background:#fef3c7;color:#d97706;">Previsto</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Despesas --}}
@if($despesas->isNotEmpty())
<div class="section-title" style="border-left-color:#d97706;">
    Despesas — {{ $despesas->count() }} lançamento(s)
</div>
<table>
    <thead>
        <tr>
            <th>Cliente / Fornecedor</th>
            <th>Descrição / Contrato</th>
            <th style="text-align:center;">Vencimento</th>
            <th style="text-align:right;">Valor</th>
            <th style="text-align:right;">Pago</th>
            <th style="text-align:center;">Data Pgto</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($despesas as $l)
        <tr>
            <td style="font-weight:bold;">{{ $l->cliente_nome }}</td>
            <td>
                <div>{{ $l->descricao }}</div>
                @if($l->contrato_desc)
                <div style="font-size:8px;color:#64748b;">{{ $l->contrato_desc }}</div>
                @endif
            </td>
            <td style="text-align:center;">{{ \Carbon\Carbon::parse($l->vencimento)->format('d/m/Y') }}</td>
            <td style="text-align:right;">R$ {{ number_format($l->valor, 2, ',', '.') }}</td>
            <td style="text-align:right;">
                @if($l->valor_pago)
                    R$ {{ number_format($l->valor_pago, 2, ',', '.') }}
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($l->data_pagamento)
                    {{ \Carbon\Carbon::parse($l->data_pagamento)->format('d/m/Y') }}
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($l->status === 'recebido')
                    <span class="badge badge-ok">Pago</span>
                @elseif($l->status === 'atrasado')
                    <span class="badge badge-urgente">Atrasado</span>
                @else
                    <span class="badge" style="background:#fef3c7;color:#d97706;">Previsto</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Repasses --}}
@if($repassess->isNotEmpty())
<div class="section-title" style="border-left-color:#7c3aed;">
    Repasses — {{ $repassess->count() }} lançamento(s)
</div>
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Descrição</th>
            <th style="text-align:center;">Vencimento</th>
            <th style="text-align:right;">Valor</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($repassess as $l)
        <tr>
            <td style="font-weight:bold;">{{ $l->cliente_nome }}</td>
            <td>{{ $l->descricao }}</td>
            <td style="text-align:center;">{{ \Carbon\Carbon::parse($l->vencimento)->format('d/m/Y') }}</td>
            <td style="text-align:right;">R$ {{ number_format($l->valor, 2, ',', '.') }}</td>
            <td style="text-align:center;">
                @if($l->status === 'recebido')
                    <span class="badge badge-ok">Repassado</span>
                @elseif($l->status === 'atrasado')
                    <span class="badge badge-urgente">Atrasado</span>
                @else
                    <span class="badge" style="background:#f3e8ff;color:#7c3aed;">Previsto</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($lancamentos->isEmpty())
    <div class="empty">Nenhum lançamento encontrado para {{ $mesNome }}.</div>
@endif

<div class="total-box">
    <div class="total-item">
        <div class="total-valor" style="color:#16a34a;">R$ {{ number_format($totais['receita_recebida'], 2, ',', '.') }}</div>
        <div class="total-label">Recebido</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:#d97706;">R$ {{ number_format($totais['despesa_paga'], 2, ',', '.') }}</div>
        <div class="total-label">Despesas Pagas</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:#dc2626;">R$ {{ number_format($totais['receita_atrasada'], 2, ',', '.') }}</div>
        <div class="total-label">Em Atraso</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:{{ $totais['saldo'] >= 0 ? '#16a34a' : '#dc2626' }};">
            R$ {{ number_format(abs($totais['saldo']), 2, ',', '.') }}
        </div>
        <div class="total-label">Saldo Líquido</div>
    </div>
</div>

@endsection
