<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px; }
  h1   { font-size: 16px; color: #1a3a5c; margin: 0 0 4px; }
  .sub { font-size: 10px; color: #64748b; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  thead th { background: #1a3a5c; color: #fff; padding: 7px 10px; text-align: left; font-size: 10px; }
  tbody tr:nth-child(even) td { background: #f8fafc; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; }
  .footer { font-size: 9px; color: #94a3b8; text-align: center; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 8px; }
  .nome { font-weight: 700; color: #1a3a5c; }
  .verde { color: #16a34a; font-weight: 600; }
  .vermelho { color: #dc2626; font-weight: 600; }
  .laranja { color: #d97706; font-weight: 600; }
  .num { text-align: right; }
</style>
</head>
<body>

<h1>👨‍⚖️ Produtividade por Advogado</h1>
<div class="sub">
    Período: {{ $dataIniFmt }} a {{ $dataFimFmt }}
    &nbsp;·&nbsp; Gerado em {{ $gerado_em }}
    &nbsp;·&nbsp; {{ $usuario }}
</div>

<table>
    <thead>
        <tr>
            <th>Advogado</th>
            <th class="num">Proc. Ativos</th>
            <th class="num">Horas</th>
            <th class="num">Valor Faturável</th>
            <th class="num">Apontamentos</th>
            <th class="num">Andamentos</th>
            <th class="num">Prazos Cumpridos</th>
            <th class="num">Prazos Perdidos</th>
            <th class="num">Taxa Cumprimento</th>
        </tr>
    </thead>
    <tbody>
        @foreach($advogados as $adv)
        @php
            $taxa = $adv->prazos_cumpridos + $adv->prazos_perdidos > 0
                ? round($adv->prazos_cumpridos / ($adv->prazos_cumpridos + $adv->prazos_perdidos) * 100)
                : null;
            $corTaxa = $taxa === null ? '' : ($taxa >= 90 ? 'verde' : ($taxa >= 70 ? 'laranja' : 'vermelho'));
        @endphp
        <tr>
            <td>
                <span class="nome">{{ $adv->nome }}</span>
                @if($adv->oab)<br><span style="font-size:9px;color:#64748b;">OAB {{ $adv->oab }}</span>@endif
            </td>
            <td class="num">{{ $adv->processos_ativos }}</td>
            <td class="num">{{ number_format($adv->total_horas, 1, ',', '.') }}h</td>
            <td class="num">R$ {{ number_format($adv->total_valor, 2, ',', '.') }}</td>
            <td class="num">{{ $adv->total_apontamentos }}</td>
            <td class="num">{{ $adv->total_andamentos }}</td>
            <td class="num verde">{{ $adv->prazos_cumpridos }}</td>
            <td class="num {{ $adv->prazos_perdidos > 0 ? 'vermelho' : '' }}">{{ $adv->prazos_perdidos }}</td>
            <td class="num {{ $corTaxa }}">{{ $taxa !== null ? $taxa.'%' : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#1a3a5c;color:#fff;font-weight:700;">
            <td>TOTAL</td>
            <td class="num">{{ array_sum(array_column($advogados, 'processos_ativos')) }}</td>
            <td class="num">{{ number_format(array_sum(array_column($advogados, 'total_horas')), 1, ',', '.') }}h</td>
            <td class="num">R$ {{ number_format(array_sum(array_column($advogados, 'total_valor')), 2, ',', '.') }}</td>
            <td class="num">{{ array_sum(array_column($advogados, 'total_apontamentos')) }}</td>
            <td class="num">{{ array_sum(array_column($advogados, 'total_andamentos')) }}</td>
            <td class="num">{{ array_sum(array_column($advogados, 'prazos_cumpridos')) }}</td>
            <td class="num">{{ array_sum(array_column($advogados, 'prazos_perdidos')) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="footer">Sistema Jurídico SAPRO &nbsp;·&nbsp; {{ $gerado_em }}</div>
</body>
</html>
