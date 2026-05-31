<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano Expirado — Sistema Jurídico</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; padding: 24px; }
        .card { background: #fff; border-radius: 16px; padding: 48px 40px; max-width: 480px; width: 100%; box-shadow: 0 8px 40px rgba(0,0,0,.1); text-align: center; }
        .icon { font-size: 48px; margin-bottom: 20px; }
        h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
        p  { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 24px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer; border: none; transition: filter .15s; }
        .btn:hover { filter: brightness(.92); }
        .btn-primary { background: #1a3a5c; color: #fff; }
        .btn-ghost { background: #f1f5f9; color: #374151; margin-top: 10px; }
        .actions { display: flex; flex-direction: column; gap: 10px; align-items: center; }
        .badge-trial { display: inline-flex; align-items: center; gap: 6px; background: #fff5f5; border: 1px solid #fecaca; border-radius: 8px; padding: 8px 16px; font-size: 12px; font-weight: 700; color: #dc2626; margin-bottom: 20px; }
        .badge-plan { display: inline-flex; align-items: center; gap: 6px; background: #fef9c3; border: 1px solid #fcd34d; border-radius: 8px; padding: 8px 16px; font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="card">

    <div class="icon">⏰</div>

    @if(session('motivo') === 'trial')
    <div class="badge-trial">🔴 Trial Expirado</div>
    <h1>Seu período de teste terminou</h1>
    <p>
        O trial gratuito de <strong>{{ Auth::guard('usuarios')->user()?->nome ?? 'sua conta' }}</strong> expirou.
        Para continuar usando o sistema, escolha um plano.
    </p>
    @else
    <div class="badge-plan">⚠️ Plano Expirado</div>
    <h1>Seu plano expirou</h1>
    <p>
        O plano contratado expirou. Para restabelecer o acesso, entre em contato com o suporte
        ou renove seu plano.
    </p>
    @endif

    <div class="actions">
        <a href="https://wa.me/5511999999999?text=Olá!%20Preciso%20renovar%20meu%20plano%20no%20Sistema%20Jurídico."
           target="_blank"
           class="btn btn-primary">
            💬 Falar com suporte via WhatsApp
        </a>
        <a href="mailto:suporte@kmd-ia.com.br?subject=Renovação%20de%20Plano"
           class="btn btn-ghost">
            ✉️ Enviar e-mail para o suporte
        </a>
        <form method="POST" action="{{ route('logout') }}" style="width:100%;">
            @csrf
            <button type="submit" class="btn btn-ghost" style="width:100%;justify-content:center;">
                ← Sair da conta
            </button>
        </form>
    </div>

</div>
</body>
</html>
