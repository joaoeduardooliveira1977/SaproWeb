<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('titulo', 'Software Jurídico')</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background:#f4f6f9; font-family:'Segoe UI',Arial,sans-serif; color:#333; }
  .wrapper { max-width:600px; margin:0 auto; padding:20px 10px; }
  .header { background:linear-gradient(135deg,#0f2540,#1a3a5c); border-radius:12px 12px 0 0; padding:32px 40px; text-align:center; }
  .header img { height:48px; margin-bottom:16px; }
  .header h1 { color:#fff; font-size:22px; font-weight:700; letter-spacing:-0.3px; }
  .header p  { color:rgba(255,255,255,0.75); font-size:14px; margin-top:6px; }
  .body { background:#fff; padding:40px; border-left:1px solid #e2e8f0; border-right:1px solid #e2e8f0; }
  .body h2 { color:#0f2540; font-size:20px; margin-bottom:16px; }
  .body p  { color:#555; font-size:15px; line-height:1.7; margin-bottom:16px; }
  .body ul { margin:16px 0 16px 20px; }
  .body ul li { color:#555; font-size:15px; line-height:1.7; margin-bottom:8px; }
  .btn { display:inline-block; background:#1a3a5c; color:#fff !important; text-decoration:none; padding:14px 32px; border-radius:8px; font-size:15px; font-weight:600; margin:8px 0; }
  .btn-green { background:#10b981; }
  .btn-whatsapp { background:#25d366; }
  .highlight { background:#f0f9ff; border-left:4px solid #1a3a5c; padding:16px 20px; border-radius:0 8px 8px 0; margin:20px 0; }
  .divider { border:none; border-top:1px solid #e2e8f0; margin:24px 0; }
  .footer { background:#f8fafc; border:1px solid #e2e8f0; border-top:none; border-radius:0 0 12px 12px; padding:24px 40px; text-align:center; }
  .footer p { color:#94a3b8; font-size:13px; line-height:1.6; }
  .footer a { color:#1a3a5c; text-decoration:none; }
  .alert { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:16px 20px; margin:20px 0; color:#856404; font-size:14px; }
  .alert.danger { background:#fef2f2; border-color:#f87171; color:#b91c1c; }
  @media(max-width:480px) {
    .header { padding:24px 20px; }
    .body { padding:24px 20px; }
    .footer { padding:20px; }
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>⚖️ Software Jurídico</h1>
    <p>Sistema de Gestão para Escritórios de Advocacia</p>
  </div>
  <div class="body">
    @yield('conteudo')
  </div>
  <div class="footer">
    <p>Este email foi enviado para <strong>{{ $emailDestino ?? 'você' }}</strong>.<br>
    © {{ date('Y') }} Software Jurídico. Todos os direitos reservados.<br>
    <a href="{{ env('APP_URL', 'https://kmd-ia.com.br') }}">kmd-ia.com.br</a></p>
  </div>
</div>
</body>
</html>
