@extends('pdf.layout')

@section('content')

<div class="meta">
    <span><strong>Período:</strong> {{ $data_ini }} a {{ $data_fim }}</span>
</div>

{{-- Resumo --}}
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div style="flex:1;background:#dcfce7;border-radius:6px;padding:12px;min-width:120px;">
        <div style="font-size:9px;color:#166534;font-weight:bold;text-transform:uppercase;">Recebido</div>
        <div style="font-size:16px;font-weight:bold;color:#166534;margin-top:4px;">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</div>
    </div>
    <div style="flex:1;background:#dbeafe;border-radius:6px;padding:12px;min-width:120px;">
        <div style="font-size:9px;color:#1d4ed8;font-weight:bold;text-transform:uppercase;">A Receber</div>
        <div style="font-size:16px;font-weight:bold;color:#1d4ed8;margin-top:4px;">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</div>
    </div>
    <div style="flex:1;background:#fee2e2;border-radius:6px;padding:12px;min-width:120px;">
        <div style="font-size:9px;color:#dc2626;font-weight:bold;text-transform:uppercase;">Pago</div>
        <div style="font-size:16px;font-weight:bold;color:#dc2626;margin-top:4px;">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
    </div>
    <div style="flex:1;background:#fef3c7;border-radius:6px;padding:12px;min-width:120px;">
        <div style="font-size:9px;color:#d97706;font-weight:bold;text-transform:uppercase;">A Pagar</div>
        <div style="font-size:16px;font-weight:bold;color:#d97706;margin-top:4px;">R$ {{ number_format($totalAPagar, 2, ',', '.') }}</div>
    </div>
    <div style="flex:1;background:{{ $saldo >= 0 ? '#1a3a5c' : '#7f1d1d' }};border-radius:6px;padding:12px;min-width:120px;">
        <div style="font-size:9px;color:#93c5fd;font-weight:bold;text-transform:uppercase;">Saldo (recebido - pago)</div>
        <div style="font-size:16px;font-weight:bold;color:#fff;margin-top:4px;">R$ {{ number_format($saldo, 2, ',', '.') }}</div>
    </div>
</div>

{{-- Recebimentos --}}
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
                <span class="badge {{ $r->recebido ? 'badge-ok' : '' }}"
                    style="{{ !$r->recebido ? 'background:#dbeafe;color:#1d4ed8;' : '' }}">
                    {{ $r->recebido ? 'Recebido' : 'Pendente' }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Pagamentos --}}
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
