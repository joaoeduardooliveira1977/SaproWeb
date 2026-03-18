<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login — Web</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a5c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="JURÍDICO">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #1a3a5c 0%, #0f2540 60%, #0a1929 100%);
        }
        .login-box {
            background: #fff; border-radius: 12px; padding: 40px 48px;
            width: 360px; box-shadow: 0 24px 64px rgba(0,0,0,.35);
        }
        .login-logo { text-align: center; margin-bottom: 28px; }
        .login-logo .ico { display: flex; justify-content: center; color: #1a3a5c; margin-bottom: 6px; }
        h1 { font-size: 28px; font-weight: 700; color: #1a3a5c; letter-spacing: -.5px; }
        .sub { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-top: 4px; }
        .field { margin-bottom: 16px; }
        label { display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        input { width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color .2s; }
        input:focus { border-color: #2563a8; }
        .btn { width: 100%; padding: 12px; background: #1a3a5c; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn:hover { background: #2563a8; }
        .error { background: #fee2e2; color: #991b1b; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }
        .footer-txt { text-align: center; font-size: 11px; color: #94a3b8; margin-top: 16px; }
        .lembrar { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">
        <div class="ico" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 9l9-6 9 6M3 9h18M7 21h10"/><path d="M5 9l2 6H3L5 9zM19 9l2 6h-4l2-6z"/></svg></div>
        <h1>JURÍDICO</h1>
        <p class="sub">Sistema de Acompanhamento de Processos</p>
    </div>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field">
            <label>Usuário</label>
            <input type="text" name="login" value="{{ old('login') }}" autofocus placeholder="Seu login" required>
        </div>
        <div class="field">
            <label>Senha</label>
            <input type="password" name="senha" placeholder="••••••••" required>
        </div>
        <label class="lembrar">
            <input type="checkbox" name="lembrar" style="width:auto"> Manter conectado
        </label>
        <button type="submit" class="btn">Entrar no Sistema</button>
    </form>

    <p class="footer-txt">JurídicoWeb — versão 1.0</p>
</div>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
</script>
</body>
</html>
