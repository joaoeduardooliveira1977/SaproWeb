<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { margin:0; padding:0; background:#f1f5f9; font-family:Arial,Helvetica,sans-serif; }
  .wrap { max-width:580px; margin:24px auto; }
  .header { background:#1a3a5c; padding:28px 36px 24px; border-radius:8px 8px 0 0; }
  .body { background:#fff; border:1px solid #e2e8f0; border-top:none; padding:28px 36px; }
  .footer { background:#e2e8f0; border:1px solid #cbd5e1; border-top:none; padding:14px 36px;
            border-radius:0 0 8px 8px; text-align:center; }
  .btn { display:inline-block; background:#2563a8; color:#fff; padding:12px 28px;
         border-radius:8px; text-decoration:none; font-size:14px; font-weight:700; margin-top:20px; }
  .bubble { background:#f0f9ff; border-left:4px solid #2563a8; border-radius:0 8px 8px 0;
            padding:16px 20px; font-size:14px; color:#1e293b; line-height:1.7;
            margin:20px 0; white-space:pre-wrap; }
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div style="color:rgba(255,255,255,.5);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">
      {{ $escritorioNome }}
    </div>
    <div style="color:#fff;font-size:20px;font-weight:700;line-height:1.3;">
      💬 Nova mensagem no portal
    </div>
    <div style="color:#93c5fd;font-size:13px;margin-top:6px;">{{ $dataEnvio }}</div>
  </div>

  <div class="body">
    <p style="font-size:15px;color:#1e293b;margin:0 0 8px;">
      Olá, <strong>{{ $clienteNome }}</strong>!
    </p>
    <p style="font-size:14px;color:#475569;margin:0 0 4px;line-height:1.6;">
      Você recebeu uma nova mensagem de <strong>{{ $remetente }}</strong>:
    </p>

    <div class="bubble">{{ $mensagem }}</div>

    <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0 0 4px;">
      Acesse o portal do cliente para visualizar e responder:
    </p>

    <a href="{{ $portalUrl }}" class="btn">Acessar Portal →</a>

    <p style="font-size:12px;color:#94a3b8;margin-top:24px;line-height:1.5;">
      Este é um aviso automático. Caso não reconheça esta mensagem, entre em contato diretamente com o escritório.
    </p>
  </div>

  <div class="footer">
    <span style="font-size:11px;color:#64748b;">
      {{ $escritorioNome }} &nbsp;·&nbsp; {{ $dataEnvio }}
    </span>
  </div>

</div>
</body>
</html>
