<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Master — SAPRO</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a1628;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background */
        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(29,78,216,.15) 0%, transparent 70%);
            top: -200px;
            right: -100px;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,.08) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            pointer-events: none;
        }

        .wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        /* Logo area */
        .logo-area {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(59,130,246,.35);
        }

        .logo-icon svg {
            width: 32px;
            height: 32px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .logo-name {
            font-size: 24px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .restricted-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 99px;
            padding: 5px 14px;
            font-size: 10px;
            font-weight: 700;
            color: #fca5a5;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Card */
        .card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 20px;
            padding: 36px 32px;
            backdrop-filter: blur(12px);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 13px;
            color: rgba(255,255,255,.4);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        /* Form */
        .fg { margin-bottom: 16px; }

        .fg label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 7px;
        }

        .fg input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,.06);
            border: 1.5px solid rgba(255,255,255,.1);
            border-radius: 10px;
            font-size: 14px;
            color: #fff;
            outline: none;
            transition: border-color .2s, background .2s;
        }

        .fg input::placeholder { color: rgba(255,255,255,.25); }

        .fg input:focus {
            border-color: #3b82f6;
            background: rgba(59,130,246,.08);
        }

        /* Error */
        .err {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #fca5a5;
            margin-bottom: 16px;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .3px;
            transition: opacity .2s, transform .1s;
            margin-top: 4px;
            box-shadow: 0 4px 16px rgba(59,130,246,.3);
        }

        .btn-login:hover { opacity: .88; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        /* Footer */
        .foot {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: rgba(255,255,255,.25);
        }

        .foot a {
            color: rgba(255,255,255,.45);
            text-decoration: none;
            font-weight: 600;
        }

        .foot a:hover { color: rgba(255,255,255,.7); }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            color: rgba(255,255,255,.15);
            font-size: 11px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.08);
        }

        @media (max-width: 480px) {
            .card { padding: 28px 22px; }
        }
    </style>
</head>
<body>

<div class="wrap">

    <div class="logo-area">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="logo-name">SAPRO</div>
        <span class="restricted-badge">🔒 Área Restrita — Administração do Sistema</span>
    </div>

    <div class="card">
        <div class="card-title">Acesso Master</div>
        <div class="card-sub">Exclusivo para administradores do sistema. Suas ações são registradas.</div>

        @if($errors->any())
        <div class="err">{{ $errors->first() }}</div>
        @endif

        @if(session('error'))
        <div class="err">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('master.login.submit') }}">
            @csrf

            <div class="fg">
                <label>Login / E-mail</label>
                <input type="text"
                       name="login"
                       placeholder="Seu login ou e-mail"
                       value="{{ old('login') }}"
                       autofocus
                       autocomplete="username"
                       required>
            </div>

            <div class="fg">
                <label>Senha</label>
                <input type="password"
                       name="senha"
                       placeholder="••••••••••"
                       autocomplete="current-password"
                       required>
            </div>

            <button type="submit" class="btn-login">Entrar na Área Master</button>
        </form>

        <div class="divider">ou</div>

        <div style="text-align:center;">
            <a href="{{ route('login') }}" style="font-size:13px;color:rgba(255,255,255,.4);text-decoration:none;">
                ← Ir para o login do sistema
            </a>
        </div>
    </div>

    <div class="foot">
        SAPRO — Administração do Sistema &bull;
        <a href="/status" target="_blank">Status do sistema</a>
    </div>

</div>

</body>
</html>
