@extends('pdf.layout')

@section('content')
@php
    $tipoLabel = \App\Models\Orcamento::$tiposHonorario[$orc->tipo_honorario] ?? $orc->tipo_honorario;
    $statusLabel = \App\Models\Orcamento::$statusLabels[$orc->status] ?? $orc->status;
@endphp

{{-- Dados da proposta --}}
<div style="display:table;width:100%;margin-bottom:16px;">
    <div style="display:table-row;">
        <div style="display:table-cell;width:60%;vertical-align:top;padding-right:12px;">
            <div style="font-size:9px;font-weight:bold;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px;">Destinatário</div>
            <div style="font-size:13px;font-weight:bold;color:#0f2742;">{{ $orc->nome_cliente }}</div>
            @if($orc->email_cliente)
            <div style="font-size:9px;color:#475569;margin-top:2px;">{{ $orc->email_cliente }}</div>
            @endif
            @if($orc->telefone_cliente)
            <div style="font-size:9px;color:#475569;">{{ $orc->telefone_cliente }}</div>
            @endif
        </div>
        <div style="display:table-cell;width:40%;vertical-align:top;text-align:right;">
            <div style="font-size:9px;font-weight:bold;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px;">Proposta nº</div>
            <div style="font-size:14px;font-weight:bold;color:#2563a8;">{{ $orc->numero }}</div>
            @if($orc->validade)
            <div style="font-size:9px;color:#475569;margin-top:2px;">Válida até: {{ $orc->validade->format('d/m/Y') }}</div>
            @endif
            <div style="font-size:9px;color:#475569;">Emitida em: {{ $orc->created_at->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Objeto --}}
<div class="section-title">Objeto da Proposta</div>
<table style="margin-bottom:14px;">
    <tr>
        <td style="width:20%;font-weight:bold;color:#334155;background:#f8fafc;">Título</td>
        <td colspan="3">{{ $orc->titulo }}</td>
    </tr>
    @if($orc->area_direito)
    <tr>
        <td style="font-weight:bold;color:#334155;background:#f8fafc;">Área</td>
        <td colspan="3">{{ $orc->area_direito }}</td>
    </tr>
    @endif
    @if($orc->descricao)
    <tr>
        <td style="font-weight:bold;color:#334155;background:#f8fafc;vertical-align:top;">Serviços</td>
        <td colspan="3" style="white-space:pre-line;line-height:1.5;">{{ $orc->descricao }}</td>
    </tr>
    @endif
</table>

{{-- Honorários --}}
<div class="section-title">Honorários</div>
<table style="margin-bottom:14px;">
    <tr>
        <td style="width:30%;font-weight:bold;color:#334155;background:#f8fafc;">Modalidade</td>
        <td>{{ $tipoLabel }}</td>
        <td style="width:25%;font-weight:bold;color:#334155;background:#f8fafc;">Valor</td>
        <td style="font-size:12px;font-weight:bold;color:#2563a8;">
            @if($orc->tipo_honorario === 'percentual')
                {{ $orc->percentual_exito }}% sobre o êxito
            @elseif($orc->tipo_honorario === 'hora')
                R$ {{ number_format($orc->valor_hora, 2, ',', '.') }}/hora
            @else
                R$ {{ number_format($orc->valor, 2, ',', '.') }}
            @endif
        </td>
    </tr>
    @if($orc->parcelas > 1)
    <tr>
        <td style="font-weight:bold;color:#334155;background:#f8fafc;">Parcelamento</td>
        <td>{{ $orc->parcelas }}x de R$ {{ number_format($orc->valor_parcela, 2, ',', '.') }}</td>
        <td></td><td></td>
    </tr>
    @endif
    @if($orc->tipo_honorario === 'sucesso' && $orc->percentual_exito)
    <tr>
        <td style="font-weight:bold;color:#334155;background:#f8fafc;">% de Êxito</td>
        <td>{{ $orc->percentual_exito }}% sobre o valor recuperado</td>
        <td></td><td></td>
    </tr>
    @endif
</table>

@if($orc->observacoes)
<div class="section-title">Observações</div>
<div style="font-size:9px;color:#475569;line-height:1.6;padding:10px;border:1px solid #e2e8f0;border-radius:4px;white-space:pre-line;margin-bottom:14px;">{{ $orc->observacoes }}</div>
@endif

{{-- Assinatura --}}
<div style="margin-top:40px;display:table;width:100%;">
    <div style="display:table-row;">
        <div style="display:table-cell;width:45%;text-align:center;padding:0 20px;">
            <div style="border-top:1px solid #334155;padding-top:6px;font-size:8.5px;color:#475569;">
                {{ $escritorio }}<br>Advogado responsável
            </div>
        </div>
        <div style="display:table-cell;width:10%;"></div>
        <div style="display:table-cell;width:45%;text-align:center;padding:0 20px;">
            <div style="border-top:1px solid #334155;padding-top:6px;font-size:8.5px;color:#475569;">
                {{ $orc->nome_cliente }}<br>Cliente — Aceite e data
            </div>
        </div>
    </div>
</div>

<div style="margin-top:24px;padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;font-size:8px;color:#64748b;text-align:center;">
    Esta proposta é válida por 15 dias a partir da data de emissão. Após este prazo, os valores e condições poderão ser revisados.
    @if($orc->validade) Validade: {{ $orc->validade->format('d/m/Y') }}. @endif
</div>

@endsection
