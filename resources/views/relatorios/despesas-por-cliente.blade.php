<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: "DejaVu Serif", Georgia, serif; font-size:10px; color:#1a1a1a; background:#fff; }

.emissao { text-align:right; font-size:9px; color:#555; margin-bottom:6px; }

.empresa-nome { text-align:center; font-size:16px; font-weight:bold; color:#0f2540; margin-bottom:2px; letter-spacing:1px; }
.empresa-info { text-align:center; font-size:8.5px; color:#555; margin-bottom:14px; }

.titulo-box { border:1.5px solid #0f2540; text-align:center; padding:7px 0; font-size:13px; font-weight:bold; color:#0f2540; letter-spacing:2px; margin-bottom:14px; text-transform:uppercase; }

.info-linha { margin-bottom:8px; font-size:9.5px; }
.info-linha strong { font-size:10px; }

.separador { border:none; border-top:1.5px dashed #aaa; margin:12px 0; }

table.itens { width:100%; border-collapse:collapse; margin-bottom:14px; }
table.itens th { font-size:8.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#555; padding:0 0 6px 0; border-bottom:1px solid #333; }
table.itens th.right { text-align:right; }
table.itens th.center { text-align:center; }
table.itens td { padding:5px 0; font-size:9.5px; border-bottom:1px dotted #ddd; vertical-align:top; }
table.itens td.right { text-align:right; }
table.itens td.center { text-align:center; }
table.itens tr:last-child td { border-bottom:none; }

.totais-wrap { width:100%; margin-bottom:20px; }
.total-linha { display:table; width:260px; float:right; margin-bottom:3px; }
.total-label { display:table-cell; font-size:9px; color:#555; padding-right:10px; white-space:nowrap; }
.total-valor { display:table-cell; font-size:9.5px; font-weight:bold; color:#0f2540; text-align:right; border-left:2px solid #ccc; padding-left:8px; min-width:90px; }
.total-linha.destaque .total-valor { font-size:11px; color:#0f2540; border-left-color:#0f2540; }
.clearfix { clear:both; }

.rodape-wrap { margin-top:30px; }
.rodape-table { width:100%; border-collapse:collapse; }
.rodape-table td { vertical-align:top; padding:0; }
.rodape-table td:first-child { width:52%; }
.rodape-table td:last-child  { width:48%; text-align:center; }

.dados-bancarios { border:1px solid #bbb; padding:10px 12px; font-size:9px; line-height:1.7; }
.dados-bancarios .titulo { font-size:8.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#555; border-bottom:1px solid #ddd; padding-bottom:4px; margin-bottom:6px; }
.dados-bancarios .linha { margin:0; }

.assinatura { padding-top:4px; }
.assinatura .linha-assinatura { border-top:1px solid #333; width:200px; margin:0 auto 4px; }
.assinatura .nome { font-size:9px; font-weight:bold; }
.assinatura .cargo { font-size:8.5px; color:#555; }

.vazio { text-align:center; padding:24px 0; color:#999; font-style:italic; font-size:9.5px; }
</style>
</head>
<body>

<div class="emissao">Emitido em: {{ $geradoEm }}</div>

<div class="empresa-nome">{{ strtoupper($tenant?->nome ?? 'ESCRITÓRIO') }}</div>
@if($tenant?->oab)
<div class="empresa-info">OAB/SP {{ $tenant->oab }}{{ $tenant->cidade ? ' — ' . $tenant->cidade : '' }}</div>
@elseif($tenant?->cidade)
<div class="empresa-info">{{ $tenant->cidade }}</div>
@endif

<div class="titulo-box">Relatório de Despesas por Cliente</div>

<div class="info-linha">
    <strong>Período:</strong>
    {{ $dataIni->format('d/m/Y') }} a {{ $dataFim->format('d/m/Y') }}
</div>
<div class="info-linha">
    <strong>Cliente:</strong> {{ strtoupper($cliente->nome) }}
    @if($cliente->cpf_cnpj) &nbsp;·&nbsp; {{ $cliente->cpf_cnpj }} @endif
</div>

<hr class="separador">

@if($lancamentos->isEmpty())
<div class="vazio">Nenhuma despesa registrada para este cliente no período.</div>
@else
<table class="itens">
    <thead>
        <tr>
            <th style="width:80px;">Data</th>
            <th>Descrição</th>
            <th class="right" style="width:100px;">Valor (R$)</th>
            <th class="center" style="width:90px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lancamentos as $l)
        <tr>
            <td>{{ $l->data_lancamento->format('d/m/Y') }}</td>
            <td>
                {{ $l->descricao }}
                @if($l->observacoes)
                <br><span style="font-size:8.5px;color:#666;">{{ $l->observacoes }}</span>
                @endif
            </td>
            <td class="right">{{ number_format($l->valor, 2, ',', '.') }}</td>
            <td class="center">
                @php $st = match($l->status) {
                    'pendente'   => 'Pendente',
                    'aprovado'   => 'Aprovado',
                    'nao_cobrar' => 'Não cobrar',
                    'ja_cobrado' => 'Já cobrado',
                    default      => $l->status,
                }; @endphp
                {{ $st }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totais-wrap">
    @php
        $total     = $lancamentos->sum('valor');
        $aprovado  = $lancamentos->where('status','aprovado')->sum('valor');
        $pendente  = $lancamentos->where('status','pendente')->sum('valor');
        $fmt = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
    @endphp

    <div class="total-linha">
        <span class="total-label">Pendente de aprovação:</span>
        <span class="total-valor">{{ $fmt($pendente) }}</span>
    </div>
    <div class="total-linha">
        <span class="total-label">Aprovado p/ cobrança:</span>
        <span class="total-valor">{{ $fmt($aprovado) }}</span>
    </div>
    <div class="total-linha destaque">
        <span class="total-label"><strong>Total Geral:</strong></span>
        <span class="total-valor">{{ $fmt($total) }}</span>
    </div>
    <div class="clearfix"></div>
</div>
@endif

<div class="rodape-wrap">
    <table class="rodape-table">
        <tr>
            <td>
                @if(!empty($dadosBancarios) && array_filter($dadosBancarios))
                <div class="dados-bancarios">
                    <div class="titulo">Dados Bancários para Pagamento</div>
                    @if(!empty($dadosBancarios['banco']))
                    <p class="linha"><strong>Banco:</strong> {{ $dadosBancarios['banco'] }}</p>
                    @endif
                    @if(!empty($dadosBancarios['agencia']))
                    <p class="linha"><strong>Agência:</strong> {{ $dadosBancarios['agencia'] }}</p>
                    @endif
                    @if(!empty($dadosBancarios['conta_corrente']))
                    <p class="linha"><strong>Conta Corrente:</strong> {{ $dadosBancarios['conta_corrente'] }}</p>
                    @endif
                    @if(!empty($dadosBancarios['favorecido']))
                    <p class="linha"><strong>Favorecido:</strong> {{ $dadosBancarios['favorecido'] }}</p>
                    @endif
                    @if(!empty($dadosBancarios['cnpj']))
                    <p class="linha"><strong>CNPJ:</strong> {{ $dadosBancarios['cnpj'] }}</p>
                    @endif
                    @if(!empty($dadosBancarios['codigo_contabil']))
                    <p class="linha"><strong>Cód. Contábil:</strong> {{ $dadosBancarios['codigo_contabil'] }}</p>
                    @endif
                </div>
                @endif
            </td>
            <td>
                <div class="assinatura" style="margin-top:40px;">
                    <div class="linha-assinatura"></div>
                    <div class="nome">{{ $responsavel }}</div>
                    <div class="cargo">{{ $cargo }}</div>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
