<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; background: linear-gradient(135deg, #0f2540 0%, #1a3a5c 50%, #1e40af 100%); display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; padding: 20px; }
        .card { background: #fff; border-radius: 20px; padding: 40px; width: 100%; max-width: 520px; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo-title { font-size: 28px; font-weight: 800; color: #1a3a5c; }
        .logo-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
        .badge-trial { display: inline-flex; align-items: center; gap: 6px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 99px; padding: 6px 14px; font-size: 12px; font-weight: 600; color: #16a34a; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 12px 16px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; outline: none; transition: border-color .2s; }
        .form-group input:focus { border-color: #2563a8; }
        .form-group .error { font-size: 12px; color: #dc2626; margin-top: 4px; }
        .btn-primary { width: 100%; padding: 14px; background: linear-gradient(135deg, #1d4ed8, #2563a8); color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 8px; transition: opacity .2s; }
        .btn-primary:hover { opacity: .9; }
        .divider { text-align: center; font-size: 13px; color: #9ca3af; margin: 20px 0; }
        .link-login { text-align: center; font-size: 13px; color: #64748b; }
        .link-login a { color: #2563a8; font-weight: 600; text-decoration: none; }
        .planos { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 24px; }
        .plano-card { text-align: center; padding: 12px 8px; border: 1.5px solid #e5e7eb; border-radius: 10px; }
        .plano-card.destaque { border-color: #2563a8; background: #eff6ff; }
        .plano-nome { font-size: 12px; font-weight: 700; color: #374151; }
        .plano-preco { font-size: 11px; color: #64748b; margin-top: 2px; }
        .plano-atual { font-size: 10px; background: #2563a8; color: #fff; border-radius: 4px; padding: 2px 6px; margin-top: 4px; display: inline-block; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <div class="logo-title">⚖️</div>
        <div class="logo-sub">Sistema Jurídico Web</div>
    </div>

    <div style="text-align:center;">
        <div class="badge-trial">✅ 30 dias grátis — sem cartão de crédito</div>
    </div>

    <div class="planos">
        <div class="plano-card destaque">
            <div class="plano-nome">Demo</div>
            <div class="plano-preco">Gratuito</div>
            <div class="plano-atual">Você está aqui</div>
        </div>
        <div class="plano-card">
            <div class="plano-nome">Starter</div>
            <div class="plano-preco">clique aqui</div>
        </div>
        <div class="plano-card">
            <div class="plano-nome">Pro</div>
            <div class="plano-preco">clique aqui</div>
        </div>
    </div>

    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;color:#dc2626;">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('registro.store') }}">
        @csrf

        <div class="form-group">
            <label>Nome do Escritório *</label>
            <input type="text" name="escritorio" value="{{ old('escritorio') }}"
                placeholder="Ex: Oliveira & Associados Advocacia" required>
            @error('escritorio')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Seu Nome *</label>
            <input type="text" name="nome" value="{{ old('nome') }}"
                placeholder="Seu nome completo" required>
            @error('nome')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>E-mail *</label>
            <input type="email" name="email" value="{{ old('email') }}"
                placeholder="seu@email.com.br" required>
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Telefone</label>
            <input type="text" name="telefone" value="{{ old('telefone') }}"
                placeholder="(11) 99999-9999">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label>Senha *</label>
                <input type="password" name="senha" placeholder="Mínimo 8 caracteres" required>
                @error('senha')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Confirmar Senha *</label>
                <input type="password" name="senha_confirmation" placeholder="Repita a senha" required>
            </div>
        </div>

        <button type="submit" class="btn-primary">Criar minha conta grátis →</button>
    </form>

    <div class="divider">ou</div>
    <div class="link-login">Já tem conta? <a href="{{ route('login') }}">Entrar</a></div>
</div>
</body>
</html>
