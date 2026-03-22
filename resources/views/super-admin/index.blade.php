<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — SAPRO</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; min-height: 100vh; }
        .topbar { background: linear-gradient(135deg, #0f172a, #1e293b); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
        .topbar-title { color: #fff; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .topbar-badge { background: #dc2626; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
        .stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .stat-val { font-size: 28px; font-weight: 800; color: #1e293b; }
        .stat-label { font-size: 12px; color: #64748b; margin-top: 4px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; letter-spacing: .5px; background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
        td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
        .badge-demo     { background: #f1f5f9; color: #475569; }
        .badge-starter  { background: #eff6ff; color: #2563a8; }
        .badge-pro      { background: #f5f3ff; color: #7c3aed; }
        .badge-ativo    { background: #f0fdf4; color: #16a34a; }
        .badge-inativo  { background: #fef2f2; color: #dc2626; }
        .btn { display: inline-flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-blue   { background: #eff6ff; color: #2563a8; }
        .btn-green  { background: #f0fdf4; color: #16a34a; }
        .btn-red    { background: #fef2f2; color: #dc2626; }
        .btn-purple { background: #f5f3ff; color: #7c3aed; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-title">
        ⚖️ SAPRO
        <span class="topbar-badge">SUPER ADMIN</span>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-blue" style="color:#93c5fd;background:rgba(255,255,255,.1);">
        → Ir para o Sistema
    </a>
</div>

<div class="container">

    @if(session('sucesso'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#16a34a;font-weight:600;">
        ✅ {{ session('sucesso') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-val">{{ $stats['total_tenants'] }}</div>
            <div class="stat-label">Total de Tenants</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:#16a34a;">{{ $stats['tenants_ativos'] }}</div>
            <div class="stat-label">Ativos</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:#64748b;">{{ $stats['plano_demo'] }}</div>
            <div class="stat-label">Plano Demo</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:#2563a8;">{{ $stats['plano_starter'] }}</div>
            <div class="stat-label">Plano Starter</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:#7c3aed;">{{ $stats['plano_pro'] }}</div>
            <div class="stat-label">Plano Pro</div>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <strong style="font-size:15px;color:#1e293b;">Escritórios Cadastrados</strong>
            <span style="font-size:13px;color:#64748b;">{{ $stats['total_tenants'] }} tenant(s)</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Escritório</th>
                    <th>E-mail</th>
                    <th>Plano</th>
                    <th>Status</th>
                    <th>Processos</th>
                    <th>Usuários</th>
                    <th>Trial expira</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $t)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#1e293b;">{{ $t->nome }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $t->slug }}</div>
                    </td>
                    <td style="color:#475569;">{{ $t->email }}</td>
                    <td>
                        <form method="POST" action="{{ route('super-admin.plano', $t->id) }}" style="display:inline;">
                            @csrf
                            <select name="plano" onchange="this.form.submit()"
                                style="font-size:12px;padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc;">
                                @foreach(['demo','starter','pro','enterprise'] as $p)
                                <option value="{{ $p }}" {{ $t->plano === $p ? 'selected' : '' }}>
                                    {{ ucfirst($p) }}
                                </option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td>
                        <span class="badge {{ $t->ativo ? 'badge-ativo' : 'badge-inativo' }}">
                            {{ $t->ativo ? 'Ativo' : 'Suspenso' }}
                        </span>
                    </td>
                    <td style="text-align:center;">{{ $t->processos_count }}</td>
                    <td style="text-align:center;">{{ $t->usuarios_count }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $t->trial_expira_em ? $t->trial_expira_em->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            <a href="{{ route('super-admin.show', $t->id) }}" class="btn btn-blue">
                                ⚙️ Config
                            </a>
                            <a href="{{ route('super-admin.login-tenant', $t->id) }}" class="btn btn-purple">
                                👤 Entrar
                            </a>
                            <form method="POST" action="{{ route('super-admin.toggle', $t->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn {{ $t->ativo ? 'btn-red' : 'btn-green' }}">
                                    {{ $t->ativo ? '🚫 Suspender' : '✅ Ativar' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('super-admin.excluir', $t->id) }}" style="display:inline;"
                                onsubmit="return confirm('Tem certeza? Esta ação excluirá TODOS os dados do tenant {{ $t->nome }} e não pode ser desfeita!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-red">
                                    🗑️ Excluir
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
