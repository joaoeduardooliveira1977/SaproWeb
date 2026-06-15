<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo não disponível — Software Jurídico</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8; color: #1e293b;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 24px;
        }
        .box {
            background: #fff; border-radius: 16px; padding: 48px 40px;
            max-width: 480px; width: 100%; text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,.10); border: 1px solid #e2e8f0;
        }
        .icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: #fef2f2; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .icon svg { width: 36px; height: 36px; stroke: #dc2626; fill: none; stroke-width: 1.8; }
        h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        p  { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 8px; }
        .sub { font-size: 13px; color: #94a3b8; margin-bottom: 32px; }
        .btns { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border-radius: 8px; font-size: 13px;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            transition: filter .15s;
        }
        .btn:hover { filter: brightness(.92); }
        .btn-green  { background: #16a34a; color: #fff; }
        .btn-outline { background: transparent; color: #1a3a5c; border: 1.5px solid #cbd5e1; }
        .badge {
            display: inline-block; margin-bottom: 20px; padding: 4px 14px;
            border-radius: 20px; background: #fef2f2; color: #dc2626;
            font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="box">
    <div class="icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    </div>

    <span class="badge">Módulo bloqueado</span>

    <h1>Este módulo não está disponível</h1>
    <p>O recurso que você tentou acessar não está incluído no plano atual do seu escritório.</p>
    <p class="sub">Fale com o suporte para ativar este módulo ou fazer upgrade do seu plano.</p>

    <div class="btns">
        <a href="https://wa.me/5511999999999?text=Olá!%20Quero%20saber%20mais%20sobre%20os%20módulos%20do%20SAPRO."
           target="_blank" rel="noopener" class="btn btn-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Falar com suporte
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline">
            ← Voltar ao início
        </a>
    </div>
</div>
</body>
</html>
