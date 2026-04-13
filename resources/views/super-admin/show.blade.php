<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Tenant — {{ $tenant->nome }} — Software Jur�dico Super Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; min-height: 100vh; }
        .topbar { background: linear-gradient(135deg, #0f172a, #1e293b); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
        .topbar-title { color: #fff; font-size: 18px; font-weight: 800; }
        .container { max-width: 900px; margin: 0 auto; padding: 32px 20px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); padding: 24px; margin-bottom: 20px; }
        .card-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: #1d4ed8; color: #fff; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-purple { background: #f5f3ff; color: #7c3aed; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .info-item value { display: block; font-size: 14px; font-weight: 600; color: #1e293b; margin-top: 2px; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-title">⚖️ Software Jur�dico — Super Admin</div>
    <a href="{{ route('super-admin.index') }}" class="btn btn-secondary" style="background:rgba(255,255,255,.1);color:#93c5fd;">
        ← Voltar
    </a>
</div>

<div class="container">

    @if(session('sucesso'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#16a34a;font-weight:600;">
        ✅ {{ session('sucesso') }}
    </div>
    @endif

    {{-- Info --}}
    <div class="card">
        <div class="card-title">📋 Informações do Tenant</div>
        <div class="info-grid">
            <div class="info-item">
                <label>Nome</label>
                <value>{{ $tenant->nome }}</value>
            </div>
            <div class="info-item">
                <label>E-mail</label>
                <value>{{ $tenant->email }}</value>
            </div>
            <div class="info-item">
                <label>Plano Atual</label>
                <value>{{ ucfirst($tenant->plano) }}</value>
            </div>
            <div class="info-item">
                <label>Status</label>
                <value>{{ $tenant->ativo ? '✅ Ativo' : '🚫 Suspenso' }}</value>
            </div>
            <div class="info-item">
                <label>Processos</label>
                <value>{{ $tenant->processos_count }} / {{ $tenant->limite_processos ?: '∞' }}</value>
            </div>
            <div class="info-item">
                <label>Trial expira</label>
                <value>{{ $tenant->trial_expira_em?->format('d/m/Y') ?? '—' }}</value>
            </div>
        </div>
        <div style="margin-top:16px;">
            <a href="{{ route('super-admin.login-tenant', $tenant->id) }}" class="btn btn-purple">
                👤 Entrar como este tenant
            </a>
        </div>
    </div>

    {{-- Configurações --}}
    <div class="card">
        <div class="card-title">⚙️ Configurações</div>
        <form method="POST" action="{{ route('super-admin.plano', $tenant->id) }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Plano</label>
                    <select name="plano">
                        @foreach(['demo','starter','pro','enterprise'] as $p)
                        <option value="{{ $p }}" {{ $tenant->plano === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Chave Gemini (IA)</label>
                    <input type="text" name="gemini_api_key"
                        value="{{ $tenant->gemini_api_key }}"
                        placeholder="AIza...">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
        </form>
    </div>

    {{-- Usuários --}}
    <div class="card">
        <div class="card-title">👥 Usuários do Tenant</div>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#64748b;text-transform:uppercase;">Nome</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#64748b;text-transform:uppercase;">E-mail</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#64748b;text-transform:uppercase;">Perfil</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#64748b;text-transform:uppercase;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $u)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 12px;font-size:13px;font-weight:600;">{{ $u->nome }}</td>
                    <td style="padding:10px 12px;font-size:13px;color:#475569;">{{ $u->email }}</td>
                    <td style="padding:10px 12px;font-size:13px;">{{ $u->perfil }}</td>
                    <td style="padding:10px 12px;">
                        <span style="background:{{ $u->ativo ? '#f0fdf4' : '#fef2f2' }};color:{{ $u->ativo ? '#16a34a' : '#dc2626' }};padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;">
                            {{ $u->ativo ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
