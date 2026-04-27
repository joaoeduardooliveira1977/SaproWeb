<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Tenant — Super Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; min-height: 100vh; }
        .topbar { background: linear-gradient(135deg, #0f172a, #1e293b); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
        .topbar-title { color: #fff; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .topbar-badge { background: #dc2626; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
        .container { max-width: 900px; margin: 0 auto; padding: 32px 20px; }
        .breadcrumb { font-size: 13px; color: #64748b; margin-bottom: 20px; display: flex; align-items: center; gap: 6px; }
        .breadcrumb a { color: #2563a8; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .page-title { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .4px; }
        .form-group input,
        .form-group select { width: 100%; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1e293b; background: #fff; outline: none; transition: border-color .2s; }
        .form-group input:focus,
        .form-group select:focus { border-color: #2563a8; box-shadow: 0 0 0 3px #dbeafe44; }
        .form-error { font-size: 11px; color: #dc2626; margin-top: 4px; }
        .help-text { font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
        .badge-real { background: #f0fdf4; color: #16a34a; border: 1px solid #86efac; }
        .badge-starter { background: #eff6ff; color: #2563a8; }
        .badge-pro { background: #f5f3ff; color: #7c3aed; }
        .badge-enterprise { background: #fff7ed; color: #c2410c; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; transition: opacity .15s; }
        .btn:hover { opacity: .88; }
        .btn-primary { background: linear-gradient(135deg, #1d4ed8, #2563a8); color: #fff; }
        .btn-ghost { background: #f1f5f9; color: #475569; }
        .footer-actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 8px; }
        .plano-info { margin-top: 8px; padding: 10px 12px; background: #f8fafc; border-radius: 8px; border-left: 3px solid #e2e8f0; font-size: 12px; color: #64748b; display: none; }
        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-title">
        ⚖️ Software Jurídico
        <span class="topbar-badge">SUPER ADMIN</span>
    </div>
    <a href="{{ route('super-admin.index') }}" class="btn btn-ghost" style="color:#93c5fd;background:rgba(255,255,255,.1);">
        ← Voltar ao painel
    </a>
</div>

<div class="container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('super-admin.index') }}">Super Admin</a>
        <span>›</span>
        <span>Novo Tenant</span>
    </div>

    <div class="page-title">
        ➕ Novo Tenant
        <span class="badge badge-real">Plano Real</span>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#dc2626;">
        <strong>Corrija os erros abaixo antes de continuar:</strong>
        <ul style="margin-top:6px;padding-left:18px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('super-admin.salvar') }}">
        @csrf

        <div class="grid-2">

            {{-- Card 1: Dados do escritório --}}
            <div class="card">
                <div class="card-header">
                    🏢 Dados do Escritório
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Nome do escritório <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome') }}"
                               placeholder="Ex: Silva & Associados" autofocus>
                        @error('nome')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>E-mail do escritório <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="contato@escritorio.com.br">
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>Plano <span style="color:#dc2626;">*</span></label>
                        <select name="plano" id="select-plano" onchange="atualizarInfoPlano(this.value)">
                            <option value="">— Selecione —</option>
                            @foreach($planos as $p)
                            <option value="{{ $p }}" {{ old('plano') === $p ? 'selected' : '' }}>
                                {{ ucfirst($p) }}
                            </option>
                            @endforeach
                        </select>
                        @error('plano')<div class="form-error">{{ $message }}</div>@enderror
                        <div id="plano-info-starter" class="plano-info" style="border-left-color:#2563a8;">
                            <strong style="color:#2563a8;">Starter:</strong> até 50 processos · até 5 usuários · IA habilitada · DataJud habilitado
                        </div>
                        <div id="plano-info-pro" class="plano-info" style="border-left-color:#7c3aed;">
                            <strong style="color:#7c3aed;">Pro:</strong> processos ilimitados · usuários ilimitados · IA habilitada · DataJud habilitado
                        </div>
                        <div id="plano-info-enterprise" class="plano-info" style="border-left-color:#c2410c;">
                            <strong style="color:#c2410c;">Enterprise:</strong> processos ilimitados · usuários ilimitados · IA habilitada · DataJud habilitado · suporte dedicado
                        </div>
                    </div>

                    <div class="form-group">
                        <label>CNPJ <span style="color:#94a3b8;font-weight:400;">(opcional)</span></label>
                        <input type="text" name="cnpj" value="{{ old('cnpj') }}"
                               placeholder="00.000.000/0001-00" maxlength="18">
                        @error('cnpj')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label>Telefone <span style="color:#94a3b8;font-weight:400;">(opcional)</span></label>
                            <input type="text" name="telefone" value="{{ old('telefone') }}"
                                   placeholder="(11) 99999-9999" maxlength="20">
                        </div>
                        <div class="form-group">
                            <label>OAB <span style="color:#94a3b8;font-weight:400;">(opcional)</span></label>
                            <input type="text" name="oab" value="{{ old('oab') }}"
                                   placeholder="SP 000000" maxlength="30">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label>Cidade <span style="color:#94a3b8;font-weight:400;">(opcional)</span></label>
                        <input type="text" name="cidade" value="{{ old('cidade') }}"
                               placeholder="São Paulo" maxlength="100">
                    </div>

                </div>
            </div>

            {{-- Card 2: Primeiro usuário admin --}}
            <div class="card">
                <div class="card-header">
                    👤 Primeiro Usuário Administrador
                </div>
                <div class="card-body">

                    <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:10px 12px;margin-bottom:16px;font-size:12px;color:#92400e;">
                        Este usuário terá acesso total ao sistema do tenant como administrador.
                    </div>

                    <div class="form-group">
                        <label>Nome completo <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="admin_nome" value="{{ old('admin_nome') }}"
                               placeholder="Nome completo do administrador">
                        @error('admin_nome')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>E-mail de login <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                               placeholder="admin@escritorio.com.br">
                        @error('admin_email')<div class="form-error">{{ $message }}</div>@enderror
                        <div class="help-text">Este será o login de acesso ao sistema.</div>
                    </div>

                    <div class="form-group">
                        <label>Senha <span style="color:#dc2626;">*</span></label>
                        <input type="password" name="admin_senha" placeholder="Mínimo 8 caracteres">
                        @error('admin_senha')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label>Confirmar senha <span style="color:#dc2626;">*</span></label>
                        <input type="password" name="admin_senha_confirmation" placeholder="Repita a senha">
                    </div>

                </div>
            </div>

        </div>

        {{-- Rodapé --}}
        <div class="footer-actions">
            <a href="{{ route('super-admin.index') }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                ✅ Criar tenant
            </button>
        </div>

    </form>

</div>

<script>
function atualizarInfoPlano(plano) {
    ['starter','pro','enterprise'].forEach(function(p) {
        document.getElementById('plano-info-' + p).style.display = 'none';
    });
    if (plano && document.getElementById('plano-info-' + plano)) {
        document.getElementById('plano-info-' + plano).style.display = 'block';
    }
}
// Mostrar info do plano já selecionado (old input)
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('select-plano');
    if (sel.value) atualizarInfoPlano(sel.value);
});
</script>

</body>
</html>
