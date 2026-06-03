@extends('pdf.layout')
@section('content')

{{-- Dados do cliente --}}
<div style="background:#f8fafc;border:1px solid #dbe4ef;border-radius:6px;padding:12px 14px;margin-bottom:16px;display:table;width:100%;">
    <div style="display:table-cell;width:50%;vertical-align:top;">
        <div style="font-size:8.5px;font-weight:bold;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px;">Cliente / Empresa</div>
        <div style="font-size:12px;font-weight:bold;color:#0f2540;">{{ $cliente->nome }}</div>
        @if($cliente->cpf_cnpj)<div style="font-size:9px;color:#475569;margin-top:2px;"><strong>CNPJ/CPF:</strong> {{ $cliente->cpf_cnpj }}</div>@endif
        @if($cliente->filial)<div style="font-size:9px;color:#475569;"><strong>Filial:</strong> {{ $cliente->filial->nome }}</div>@endif
        @if($cliente->referencia)<div style="font-size:9px;color:#475569;"><strong>Referência:</strong> {{ $cliente->referencia }}</div>@endif
    </div>
    <div style="display:table-cell;width:50%;vertical-align:top;padding-left:20px;border-left:1px solid #e2e8f0;">
        <div style="font-size:8.5px;font-weight:bold;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px;">Nominal (Escritório)</div>
        <div style="font-size:12px;font-weight:bold;color:#0f2540;">{{ $tenant->nome }}</div>
        <div style="font-size:9px;color:#475569;margin-top:2px;"><strong>Período:</strong> {{ \Carbon\Carbon::parse($dataIni)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</div>
        <div style="font-size:9px;color:#475569;"><strong>Emitido por:</strong> {{ $responsavel }}</div>
    </div>
</div>

@if($lancamentos->isEmpty())
    <div class="empty">Nenhum lançamento encontrado para o período selecionado.</div>
@else

<table>
    <thead>
        <tr>
            <th style="width:12%">Data</th>
            <th style="width:58%">Descrição</th>
            <th style="width:15%">Tipo</th>
            <th style="width:15%;text-align:right">Valor</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($lancamentos as $l)
        @php $total += $l->valor; @endphp
        <tr>
            <td>{{ $l->data_lancamento->format('d/m/Y') }}</td>
            <td>{{ $l->descricao }}</td>
            <td>
                @if($l->tipo === 'reembolso')
                    <span class="badge badge-ativo">Reembolso</span>
                @else
                    <span class="badge badge-urgente">Despesa</span>
                @endif
            </td>
            <td style="text-align:right;font-weight:bold;">R$ {{ number_format($l->valor,2,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total-box">
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($lancamentos->where('tipo','despesa')->sum('valor'),2,',','.') }}</div>
        <div class="total-label">Despesas</div>
    </div>
    <div class="total-item">
        <div class="total-valor">R$ {{ number_format($lancamentos->where('tipo','reembolso')->sum('valor'),2,',','.') }}</div>
        <div class="total-label">Reembolsos</div>
    </div>
    <div class="total-item">
        <div class="total-valor" style="color:#1d4ed8;">R$ {{ number_format($total,2,',','.') }}</div>
        <div class="total-label">Saldo Geral</div>
    </div>
</div>

{{-- Forma de pagamento --}}
@if($dadosBancarios)
<div style="margin-top:20px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:12px 14px;">
    <div style="font-size:9px;font-weight:bold;color:#1d4ed8;text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px;">FORMA DE PAGAMENTO</div>
    <div style="font-size:9px;color:#1e40af;white-space:pre-line;">{{ $dadosBancarios }}</div>
</div>
@endif

{{-- Assinatura --}}
<div style="margin-top:32px;border-top:1px solid #e2e8f0;padding-top:16px;display:table;width:100%;">
    <div style="display:table-cell;width:45%;text-align:center;">
        <div style="border-top:1px solid #334155;padding-top:6px;margin-top:40px;font-size:9px;color:#334155;font-weight:bold;">{{ $responsavel }}</div>
        <div style="font-size:8px;color:#64748b;">{{ $tenant->nome }}</div>
    </div>
</div>
@endif

@endsection
