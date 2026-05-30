<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

    .header { background: #1e3a5f; color: #fff; padding: 18px 24px; margin-bottom: 20px; }
    .header h1 { font-size: 18px; font-weight: 700; letter-spacing: .5px; }
    .header .sub { font-size: 11px; color: #93c5fd; margin-top: 4px; }

    .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 10px; font-weight: 700; }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-gray   { background: #f1f5f9; color: #475569; }
    .badge-yellow { background: #fef9c3; color: #713f12; }
    .badge-red    { background: #fee2e2; color: #991b1b; }

    .section { margin-bottom: 18px; padding: 0 24px; }
    .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #64748b; border-bottom: 1.5px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }

    .grid2 { display: table; width: 100%; }
    .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
    .col:last-child { padding-right: 0; }

    .field { margin-bottom: 8px; }
    .field-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 2px; }
    .field-value { font-size: 11px; color: #1e293b; }

    table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
    table.data th { background: #f8fafc; padding: 6px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b; border-bottom: 1.5px solid #e2e8f0; }
    table.data td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; color: #334155; }
    table.data tr:last-child td { border-bottom: none; }

    .empty { color: #94a3b8; font-style: italic; font-size: 10px; padding: 8px 0; }

    .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 8px 24px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; display: table; width: 100%; }
    .footer .left  { display: table-cell; text-align: left; }
    .footer .right { display: table-cell; text-align: right; }

    .valor-box { display: table; width: 100%; border: 1.5px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
    .valor-cell { display: table-cell; padding: 8px 12px; text-align: center; border-right: 1px solid #e2e8f0; }
    .valor-cell:last-child { border-right: none; }
    .valor-num  { font-size: 13px; font-weight: 700; color: #1e293b; }
    .valor-lbl  { font-size: 9px; color: #94a3b8; margin-top: 2px; }
</style>
</head>
<body>

{{-- Cabeçalho --}}
<div class="header">
    <h1>Processo {{ $processo->numero }}</h1>
    <div class="sub">
        Gerado em {{ now()->format('d/m/Y \à\s H:i') }}
        @if($processo->cliente) &nbsp;·&nbsp; {{ $processo->cliente->nome }} @endif
    </div>
</div>

{{-- Status + risco --}}
<div class="section" style="margin-bottom:12px;">
    @php
        $statusClass = match($processo->status) {
            'Ativo'     => 'badge-green',
            'Encerrado' => 'badge-yellow',
            default     => 'badge-gray',
        };
    @endphp
    <span class="badge {{ $statusClass }}">{{ $processo->status }}</span>
    @if($processo->risco)
    <span class="badge badge-red" style="margin-left:6px;">{{ $processo->risco->descricao }}</span>
    @endif
    @if($processo->fase)
    <span class="badge badge-gray" style="margin-left:6px;">{{ $processo->fase->descricao }}</span>
    @endif
</div>

{{-- Dados principais --}}
<div class="section">
    <div class="section-title">Dados do Processo</div>
    <div class="grid2">
        <div class="col">
            <div class="field">
                <div class="field-label">Cliente</div>
                <div class="field-value">{{ $processo->cliente?->nome ?? '—' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Parte Contrária</div>
                <div class="field-value">{{ $processo->parteContraria?->nome ?? $processo->parte_contraria ?? '—' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Advogado Responsável</div>
                <div class="field-value">{{ $processo->advogado?->nome ?? '—' }}</div>
            </div>
            @if($processo->advogados->isNotEmpty())
            <div class="field">
                <div class="field-label">Outros Advogados</div>
                <div class="field-value">{{ $processo->advogados->pluck('nome')->implode(', ') }}</div>
            </div>
            @endif
            <div class="field">
                <div class="field-label">Polo</div>
                <div class="field-value">{{ $processo->autor_reu ?? '—' }}</div>
            </div>
        </div>
        <div class="col">
            <div class="field">
                <div class="field-label">Tipo de Ação</div>
                <div class="field-value">{{ $processo->tipoAcao?->descricao ?? '—' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Tipo de Processo</div>
                <div class="field-value">{{ $processo->tipoProcesso?->descricao ?? '—' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Vara / Repartição</div>
                <div class="field-value">{{ $processo->vara ?? '—' }}@if($processo->reparticao) &nbsp;·&nbsp; {{ $processo->reparticao->descricao }} @endif</div>
            </div>
            <div class="field">
                <div class="field-label">Distribuição</div>
                <div class="field-value">{{ $processo->data_distribuicao?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Extrajudicial</div>
                <div class="field-value">{{ $processo->extrajudicial ? 'Sim' : 'Não' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Valores --}}
@if($processo->valor_causa || $processo->valor_risco)
<div class="section">
    <div class="section-title">Valores</div>
    <div class="valor-box">
        @if($processo->valor_causa)
        <div class="valor-cell">
            <div class="valor-num">R$ {{ number_format($processo->valor_causa, 2, ',', '.') }}</div>
            <div class="valor-lbl">Valor da Causa</div>
        </div>
        @endif
        @if($processo->valor_risco)
        <div class="valor-cell">
            <div class="valor-num">R$ {{ number_format($processo->valor_risco, 2, ',', '.') }}</div>
            <div class="valor-lbl">Valor em Risco</div>
        </div>
        @endif
        @if($processo->custas->isNotEmpty())
        <div class="valor-cell">
            <div class="valor-num">R$ {{ number_format($processo->custas->sum('valor'), 2, ',', '.') }}</div>
            <div class="valor-lbl">Total Custas</div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Observações --}}
@if($processo->observacoes)
<div class="section">
    <div class="section-title">Observações</div>
    <div style="font-size:11px;color:#334155;line-height:1.6;">{{ $processo->observacoes }}</div>
</div>
@endif

{{-- Andamentos --}}
<div class="section">
    <div class="section-title">Andamentos ({{ $processo->andamentos->count() }})</div>
    @if($processo->andamentos->isEmpty())
        <div class="empty">Nenhum andamento registrado.</div>
    @else
    <table class="data">
        <thead>
            <tr>
                <th style="width:80px;">Data</th>
                <th>Descrição</th>
                <th style="width:110px;">Usuário</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processo->andamentos->take(30) as $a)
            <tr>
                <td>{{ $a->data?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $a->descricao }}</td>
                <td>{{ $a->usuario->pessoa->nome ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($processo->andamentos->count() > 30)
    <div style="font-size:9px;color:#94a3b8;margin-top:6px;">* Exibindo os 30 andamentos mais recentes.</div>
    @endif
    @endif
</div>

{{-- Prazos --}}
@if($prazos->isNotEmpty())
<div class="section">
    <div class="section-title">Prazos ({{ $prazos->count() }})</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:80px;">Vencimento</th>
                <th>Título</th>
                <th style="width:100px;">Responsável</th>
                <th style="width:70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prazos as $p)
            @php $vencido = $p->data_prazo->isPast() && $p->status !== 'cumprido'; @endphp
            <tr>
                <td style="{{ $vencido ? 'color:#dc2626;font-weight:700;' : '' }}">{{ $p->data_prazo->format('d/m/Y') }}</td>
                <td>{{ $p->titulo }}@if($p->prazo_fatal) <span style="color:#dc2626;font-size:9px;"> [FATAL]</span>@endif</td>
                <td>{{ $p->responsavel?->nome ?? '—' }}</td>
                <td>{{ ucfirst($p->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Audiências --}}
@if($processo->audiencias->isNotEmpty())
<div class="section">
    <div class="section-title">Audiências ({{ $processo->audiencias->count() }})</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:100px;">Data/Hora</th>
                <th>Tipo</th>
                <th>Local</th>
                <th style="width:110px;">Advogado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processo->audiencias as $aud)
            <tr>
                <td>{{ $aud->data_hora?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>{{ $aud->tipoLabel() }}</td>
                <td>{{ $aud->local ?? '—' }}</td>
                <td>{{ $aud->advogado?->nome ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Custas --}}
@if($processo->custas->isNotEmpty())
<div class="section">
    <div class="section-title">Custas ({{ $processo->custas->count() }})</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:80px;">Data</th>
                <th>Descrição</th>
                <th style="width:90px;">Valor</th>
                <th style="width:60px;">Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processo->custas as $c)
            <tr>
                <td>{{ $c->data?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $c->descricao }}</td>
                <td>R$ {{ number_format($c->valor, 2, ',', '.') }}</td>
                <td>{{ $c->pago ? 'Sim' : 'Não' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Rodapé --}}
<div class="footer">
    <div class="left">Software Jurídico &nbsp;·&nbsp; Relatório do Processo {{ $processo->numero }}</div>
    <div class="right">Gerado em {{ now()->format('d/m/Y H:i') }}</div>
</div>

</body>
</html>
