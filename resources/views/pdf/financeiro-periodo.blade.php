@extends('pdf.layout')

@section('content')

<div class="meta">
    <span><strong>Período:</strong> {{ $data_ini }} a {{ $data_fim }}</span>
</div>

<table class="summary-grid">
    <tr>
        <td><div class="summary-card" style="border-left-color:#16a34a;"><div class="summary-label">Recebido</div><div class="summary-value" style="color:#166534;">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</div></div></td>
        <td><div class="summary-card" style="border-left-color:#2563a8;"><div class="summary-label">A receber</div><div class="summary-value" style="color:#1d4ed8;">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</div></div></td>
        <td><div class="summary-card" style="border-left-color:#dc2626;"><div class="summary-label">Pago</div><div class="summary-value" style="color:#dc2626;">R$ {{ number_format($totalPago, 2, ',', '.') }}</div></div></td>
        <td><div class="summary-card" style="border-left-color:#d97706;"><div class="summary-label">A pagar</div><div class="summary-value" style="color:#d97706;">R$ {{ number_format($totalAPagar, 2, ',', '.') }}</div></div></td>
        <td><div class="summary-card" style="border-left-color:{{ $saldo >= 0 ? '#16a34a' : '#dc2626' }};"><div class="summary-label">Saldo</div><div class="summary-value" style="color:{{ $saldo >= 0 ? '#166534' : '#dc2626' }};">R$ {{ number_format($saldo, 2, ',', '.') }}</div><div class="summary-note">recebido - pago</div></div></td>
    </tr>
</table>

@if($recebimentos->isNotEmpty())
<div class="section-title">Recebimentos ({{ $recebimentos->count() }})</div>
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Processo</th>
            <th>Cliente</th>
            <th>Descrição</th>
            <th style="text-align:right;">Valor</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($recebimentos as $r)
        <tr>
            <td>{{ \Carbon\Carbon::parse($r->data)->format('d/m/Y') }}</td>
            <td>{{ $r->processo_numero }}</td>
            <td>{{ $r->cliente_nome }}</td>
            <td>{{ $r->descricao }}</td>
            <td style="text-align:right;">
                @if($r->recebido)
                    R$ {{ number_format($r->valor_recebido, 2, ',', '.') }}
                @else
                    <span style="color:#64748b;">R$ {{ number_format($r->valor, 2, ',', '.') }}</span>
                @endif
            </td>
            <td style="text-align:center;">
                <span class="badge {{ $r->recebido ? 'badge-ok' : '' }}" style="{{ !$r->recebido ? 'background:#dbeafe;color:#1d4ed8;' : '' }}">
                    {{ $r->recebido ? 'Recebido' : 'Pendente' }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($pagamentos->isNotEmpty())
<div class="section-title" style="margin-top:16px;">Pagamentos / Despesas ({{ $pagamentos->count() }})</div>
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Processo</th>
            <th>Descrição</th>
            <th style="text-align:right;">Valor</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagamentos as $p)
        <tr>
            <td>{{ \Carbon\Carbon::parse($p->data)->format('d/m/Y') }}</td>
            <td>{{ $p->processo_numero }}</td>
            <td>{{ $p->descricao }}</td>
            <td style="text-align:right;">
                @if($p->pago)
                    R$ {{ number_format($p->valor_pago, 2, ',', '.') }}
                @else
                    <span style="color:#64748b;">R$ {{ number_format($p->valor, 2, ',', '.') }}</span>
                @endif
            </td>
            <td style="text-align:center;">
                <span class="badge {{ $p->pago ? 'badge-ok' : 'badge-urgente' }}">
                    {{ $p->pago ? 'Pago' : 'Pendente' }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($recebimentos->isEmpty() && $pagamentos->isEmpty())
    <div class="empty">Nenhum lançamento encontrado no período.</div>
@endif

@endsection
