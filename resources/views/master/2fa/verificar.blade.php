<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA — Sistema Jurídico Master</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0a1628; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; padding: 24px; }
        .wrap { width: 100%; max-width: 380px; }
        .logo-area { text-align: center; margin-bottom: 28px; }
        .logo-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .logo-icon svg { width: 28px; height: 28px; stroke: #fff; fill: none; stroke-width: 2; }
        .logo-name { font-size: 13px; font-weight: 700; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: 2px; }
        .card { background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1); border-radius: 18px; padding: 32px 28px; }
        .card-title { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px; text-align: center; }
        .card-sub { font-size: 13px; color: rgba(255,255,255,.4); margin-bottom: 24px; text-align: center; line-height: 1.5; }
        .icon-shield { font-size: 36px; display: block; text-align: center; margin-bottom: 14px; }
        .fg { margin-bottom: 16px; }
        .fg label { display: block; font-size: 10px; font-weight: 700; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .fg input { width: 100%; padding: 14px; background: rgba(255,255,255,.07); border: 1.5px solid rgba(255,255,255,.12); border-radius: 10px; font-size: 22px; font-weight: 700; color: #fff; text-align: center; letter-spacing: 6px; outline: none; transition: border-color .2s; }
        .fg input:focus { border-color: #3b82f6; }
        .fg input::placeholder { font-size: 14px; letter-spacing: 1px; color: rgba(255,255,255,.2); font-weight: 400; }
        .err { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.25); border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #fca5a5; margin-bottom: 14px; }
        .btn-2fa { width: 100%; padding: 13px; background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: #fff; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: opacity .2s; }
        .btn-2fa:hover { opacity: .88; }
        .divider { text-align: center; margin: 16px 0; color: rgba(255,255,255,.2); font-size: 12px; }
        .link-recuperacao { display: block; text-align: center; font-size: 12px; color: rgba(255,255,255,.4); text-decoration: none; }
        .link-recuperacao:hover { color: rgba(255,255,255,.7); }
        .foot { text-align: center; margin-top: 20px; }
        .foot a { font-size: 12px; color: rgba(255,255,255,.3); text-decoration: none; }
        .foot a:hover { color: rgba(255,255,255,.6); }
        .recovery-form { display: none; }
        .recovery-form.visible { display: block; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="logo-area">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="logo-name">Sistema Jurídico Master</div>
    </div>

    <div class="card">
        <span class="icon-shield">🛡️</span>
        <div class="card-title">Verificação em 2 Fatores</div>
        <div class="card-sub">Abra o Google Authenticator e digite o código de 6 dígitos.</div>

        @if($errors->any())
        <div class="err">{{ $errors->first() }}</div>
        @endif

        {{-- Formulário TOTP --}}
        <div id="form-totp">
            <form method="POST" action="{{ route('master.2fa.verificar.post') }}">
                @csrf
                <div class="fg">
                    <label>Código do Authenticator</label>
                    <input type="text" name="codigo" inputmode="numeric" pattern="[0-9]{6}"
                           maxlength="6" placeholder="000000" autofocus autocomplete="one-time-code">
                </div>
                <button type="submit" class="btn-2fa">Verificar e Entrar</button>
            </form>

            <div class="divider">ou</div>
            <a href="#" class="link-recuperacao" onclick="toggleRecuperacao(event)">
                Usar código de recuperação
            </a>
        </div>

        {{-- Formulário código de recuperação --}}
        <div id="form-recuperacao" class="recovery-form" style="margin-top:0;">
            <form method="POST" action="{{ route('master.2fa.verificar.post') }}">
                @csrf
                <div class="fg" style="margin-top:14px;">
                    <label>Código de Recuperação (XXXX-XXXX)</label>
                    <input type="text" name="codigo" placeholder="ABCD-EFGH"
                           maxlength="9" style="letter-spacing:3px;font-size:18px;">
                </div>
                <button type="submit" class="btn-2fa">Usar Código de Recuperação</button>
            </form>
            <div class="divider">ou</div>
            <a href="#" class="link-recuperacao" onclick="toggleRecuperacao(event)">
                Voltar para código TOTP
            </a>
        </div>
    </div>

    <div class="foot">
        <a href="{{ route('master.logout') }}" onclick="event.preventDefault();document.getElementById('lf').submit();">
            ← Cancelar e sair
        </a>
        <form id="lf" method="POST" action="{{ route('master.logout') }}" style="display:none;">@csrf</form>
    </div>
</div>

<script>
function toggleRecuperacao(e) {
    e.preventDefault();
    const totp = document.getElementById('form-totp');
    const rec  = document.getElementById('form-recuperacao');
    totp.style.display = totp.style.display === 'none' ? 'block' : 'none';
    rec.classList.toggle('visible');
}
</script>
</body>
</html>
