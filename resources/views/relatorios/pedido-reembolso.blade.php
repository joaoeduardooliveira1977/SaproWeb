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

/* Bloco cliente */
.cliente-wrap { width:100%; border-collapse:collapse; margin-bottom:12px; }
.cliente-wrap td { vertical-align:top; padding:0; }
.bloco-cliente { border:1px solid #bbb; padding:10px 12px; font-size:9.5px; line-height:1.8; }
.bloco-cliente .titulo { font-size:8.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#555; border-bottom:1px solid #ddd; padding-bottom:3px; margin-bottom:6px; }
.bloco-nominal { border:1px solid #bbb; padding:10px 12px; font-size:9.5px; line-height:1.8; margin-left:8px; }
.bloco-nominal .titulo { font-size:8.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#555; border-bottom:1px solid #ddd; padding-bottom:3px; margin-bottom:6px; }

.fechamento { font-style:italic; font-size:9px; color:#555; margin-bottom:12px; }

/* Tabela de itens */
table.itens { width:100%; border-collapse:collapse; margin-bottom:14px; }
table.itens thead tr th { font-size:8.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#333; padding:0 0 5px 0; border-bottom:1.5px solid #333; }
table.itens thead tr th.right { text-align:right; }
table.itens tbody tr td { padding:5px 0; font-size:9.5px; border-bottom:1px dotted #ddd; vertical-align:top; }
table.itens tbody tr td.right { text-align:right; }
table.itens tbody tr:last-child td { border-bottom:none; }
table.itens tfoot tr td { padding:6px 0 0 0; font-size:9.5px; border-top:1.5px solid #333; }
table.itens tfoot tr td.right { text-align:right; font-weight:bold; }

/* Totais */
.totais-wrap { margin-bottom:20px; }
.total-linha { display:table; width:280px; float:right; margin-bottom:4px; }
.total-label { display:table-cell; font-size:9px; color:#333; padding-right:10px; white-space:nowrap; }
.total-valor { display:table-cell; font-size:9.5px; font-weight:bold; text-align:right; border-left:2px solid #ccc; padding-left:8px; min-width:100px; }
.total-linha.destaque .total-label { font-weight:bold; }
.total-linha.destaque .total-valor { font-size:11px; border-left-color:#0f2540; }
.clearfix { clear:both; }

/* Rodapé */
.rodape-wrap { margin-top:24px; }
.rodape-table { width:100%; border-collapse:collapse; }
.rodape-table td { vertical-align:top; padding:0; }
.rodape-table td:first-child { width:52%; }
.rodape-table td:last-child  { width:48%; text-align:center; }

.forma-pagamento { border:1.5px solid #0f2540; padding:10px 12px; font-size:9px; line-height:1.7; }
.forma-pagamento .titulo { font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#0f2540; border-bottom:1px solid #bbb; padding-bottom:4px; margin-bottom:6px; }
.forma-pagamento .linha { margin:0; }

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

<div class="titulo-box">Pedido de Reembolso</div>

{{-- Bloco cliente + nominal --}}
<table class="cliente-wrap">
    <tr>
        <td style="width:58%;">
            <div class="bloco-cliente">
                <div class="titulo">Dados do Cliente</div>
                <strong>{{ strtoupper($cliente->nome) }}</strong><br>
                @if($cliente->cpf_cnpj)CNPJ/CPF: {{ $cliente->cpf_cnpj }}<br>@endif
                @if($cliente->filial)Filial: {{ $cliente->filial->nome }}<br>@endif
                @if($cliente->referencia)Referência: {{ $cliente->referencia }}<br>@endif
            </div>
        </td>
        <td style="width:42%;">
            <div class="bloco-nominal">
                <div class="titulo">Nominal</div>
                {{ $tenant?->nome ?? '—' }}<br>
                @if($tenant?->cnpj)CNPJ: {{ $tenant->cnpj }}<br>@endif
                @if($tenant?->cidade){{ $tenant->cidade }}<br>@endif
            </div>
        </td>
    </tr>
</table>

<div class="fechamento">Fechamento em: <em>{{ $dataFim->format('d/m/Y') }}</em></div>

@if($lancamentos->isEmpty())
<div class="vazio">Nenhum reembolso registrado para este cliente no período.</div>
@else
<table class="itens">
    <thead>
        <tr>
            <th style="width:80px;">Data</th>
            <th>Descrição</th>
            <th class="right" style="width:110px;">Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lancamentos as $l)
        <tr>
            <td>{{ $l->data_lancamento->format('d/m/Y') }}</td>
            <td>{{ $l->descricao }}</td>
            <td class="right">{{ number_format($l->valor, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totais-wrap">
    @php
        $totalEmpresa = $lancamentos->where('status', 'aprovado')->sum('valor');
        $totalGeral   = $lancamentos->sum('valor');
        $fmt = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
    @endphp

    <div class="total-linha">
        <span class="total-label">Saldo Total para a Empresa:</span>
        <span class="total-valor">{{ $fmt($totalEmpresa) }}</span>
    </div>
    <div class="total-linha destaque">
        <span class="total-label">Saldo Geral:</span>
        <span class="total-valor">{{ $fmt($totalGeral) }}</span>
    </div>
    <div class="clearfix"></div>
</div>
@endif

<div class="rodape-wrap">
    <table class="rodape-table">
        <tr>
            <td>
                <div class="forma-pagamento">
                    <div class="titulo">Forma de Pagamento</div>
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
                    @if(empty(array_filter($dadosBancarios ?? [])))
                    <p style="color:#999;font-style:italic;">Configure os dados bancários em Configurações → Dados Bancários.</p>
                    @endif
                </div>
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
