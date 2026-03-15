<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { margin:0; padding:0; background:#f1f5f9; font-family:Arial,Helvetica,sans-serif; }
  .wrap { max-width:580px; margin:24px auto; }
  .header { background:#7f1d1d; padding:28px 36px 24px; border-radius:8px 8px 0 0; }
  .body { background:#fff; border:1px solid #e2e8f0; border-top:none; padding:28px 36px; }
  table { width:100%; border-collapse:collapse; margin-top:16px; }
  th { background:#f8fafc; color:#64748b; font-size:11px; font-weight:700; text-transform:uppercase;
       letter-spacing:.5px; padding:8px 10px; border:1px solid #e2e8f0; text-align:left; }
  td { padding:9px 10px; border:1px solid #e2e8f0; font-size:13px; color:#1e293b; }
  tr:nth-child(even) td { background:#f8fafc; }
  .total-row td { background:#fef2f2; font-weight:700; color:#991b1b; border-top:2px solid #fca5a5; }
  .footer { background:#e2e8f0; border:1px solid #cbd5e1; border-top:none; padding:14px 36px;
            border-radius:0 0 8px 8px; text-align:center; }
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div style="color:rgba(255,255,255,.5);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">
      {{ $escritorioNome }}
    </div>
    <div style="color:#fff;font-size:20px;font-weight:700;line-height:1.3;">
      💳 Aviso de Honorários em Aberto
    </div>
    <div style="color:#fca5a5;font-size:13px;margin-top:8px;">
      {{ $dataEnvio }}
    </div>
  </div>

  <div class="body">
    <p style="font-size:15px;color:#1e293b;margin:0 0 16px;">
      Prezado(a) <strong>{{ $clienteNome }}</strong>,
    </p>
    <p style="font-size:14px;color:#475569;margin:0 0 20px;line-height:1.6;">
      Identificamos que há honorários advocatícios com vencimento em aberto vinculados
      ao seu contrato conosco. Solicitamos a regularização dos valores abaixo relacionados.
    </p>

    <table>
      <thead>
        <tr>
          <th>Parcela</th>
          <th>Vencimento</th>
          <th>Valor</th>
          <th>Atraso</th>
        </tr>
      </thead>
      <tbody>
        @foreach($parcelas as $p)
        <tr>
          <td>{{ $p['numero'] }}ª</td>
          <td>{{ \Carbon\Carbon::parse($p['vencimento'])->format('d/m/Y') }}</td>
          <td>R$ {{ number_format($p['valor'], 2, ',', '.') }}</td>
          <td style="color:#dc2626;font-weight:600;">{{ $p['dias'] }} dia(s)</td>
        </tr>
        @endforeach
        <tr class="total-row">
          <td colspan="2">Total em aberto</td>
          <td colspan="2">R$ {{ number_format($totalDevido, 2, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    <div style="margin-top:24px;padding:16px;border-radius:6px;background:#fef2f2;border:1px solid #fecaca;">
      <div style="font-size:13px;color:#991b1b;line-height:1.6;">
        ⚠️ Para evitar medidas adicionais, solicitamos que entre em contato conosco o quanto antes
        para regularizar a situação ou negociar as condições de pagamento.
      </div>
    </div>

    <p style="font-size:13px;color:#64748b;margin-top:20px;line-height:1.6;">
      Em caso de dúvidas ou para tratar sobre formas de pagamento, entre em contato diretamente
      com nosso escritório. Este é um aviso automático — não é necessário responder este e-mail.
    </p>
  </div>

  <div class="footer">
    <span style="font-size:11px;color:#64748b;">
      {{ $escritorioNome }} &nbsp;·&nbsp; Gerado em {{ $dataEnvio }}
    </span>
  </div>

</div>
</body>
</html>
