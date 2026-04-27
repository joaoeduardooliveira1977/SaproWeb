<div>
<style>
.reg-card {
    background: #fff;
    border-radius: 20px;
    padding: 36px 40px;
    box-shadow: 0 25px 60px rgba(0,0,0,.18);
    width: 100%;
    max-width: 480px;
    margin: 0 auto;
}
.reg-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 28px;
}
.reg-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.reg-step-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    transition: all .3s;
}
.reg-step-circle.done    { background: #16a34a; color: #fff; }
.reg-step-circle.active  { background: #1d4ed8; color: #fff; box-shadow: 0 0 0 4px #dbeafe; }
.reg-step-circle.pending { background: #f1f5f9; color: #94a3b8; border: 2px solid #e2e8f0; }
.reg-step-label { font-size: 10px; font-weight: 600; color: #94a3b8; white-space: nowrap; }
.reg-step-label.active { color: #1d4ed8; }
.reg-step-label.done   { color: #16a34a; }
.reg-connector { width: 48px; height: 2px; background: #e2e8f0; margin-bottom: 16px; }
.reg-connector.done { background: #16a34a; }
.reg-title { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
.reg-subtitle { font-size: 13px; color: #64748b; margin-bottom: 24px; }
.reg-lbl { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .4px; }
.reg-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #1e293b;
    background: #fff;
    transition: border-color .2s;
    outline: none;
}
.reg-input:focus { border-color: #2563a8; box-shadow: 0 0 0 3px #dbeafe55; }
.reg-err { font-size: 11px; color: #dc2626; margin-top: 4px; }
.reg-field { margin-bottom: 16px; }
.reg-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.reg-btn-primary {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #1d4ed8, #2563a8);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity .2s, transform .1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.reg-btn-primary:hover { opacity: .92; transform: translateY(-1px); }
.reg-btn-primary:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.reg-btn-ghost {
    background: none;
    border: none;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
}
.reg-btn-ghost:hover { color: #1e293b; }
.reg-footer { text-align: center; font-size: 13px; color: #94a3b8; margin-top: 20px; }
.reg-footer a { color: #2563a8; font-weight: 600; text-decoration: none; }
.reg-optional { font-size: 11px; color: #94a3b8; font-weight: 400; margin-left: 4px; }
@media (max-width: 520px) {
    .reg-card { padding: 28px 20px; border-radius: 14px; }
    .reg-grid-2 { grid-template-columns: 1fr; }
}
</style>

<div class="reg-card">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:24px;">
        <div style="font-size:32px;margin-bottom:4px;">⚖️</div>
        <div style="font-size:16px;font-weight:800;color:#1a3a5c;">Software Jurídico</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Crie sua conta — 30 dias grátis, sem cartão</div>
    </div>

    {{-- Progresso --}}
    <div class="reg-progress">
        <div class="reg-step">
            <div class="reg-step-circle {{ $etapa > 1 ? 'done' : ($etapa === 1 ? 'active' : 'pending') }}">
                @if($etapa > 1) ✓ @else 1 @endif
            </div>
            <div class="reg-step-label {{ $etapa > 1 ? 'done' : ($etapa === 1 ? 'active' : '') }}">Escritório</div>
        </div>
        <div class="reg-connector {{ $etapa > 1 ? 'done' : '' }}"></div>
        <div class="reg-step">
            <div class="reg-step-circle {{ $etapa > 2 ? 'done' : ($etapa === 2 ? 'active' : 'pending') }}">
                @if($etapa > 2) ✓ @else 2 @endif
            </div>
            <div class="reg-step-label {{ $etapa > 2 ? 'done' : ($etapa === 2 ? 'active' : '') }}">Administrador</div>
        </div>
        <div class="reg-connector {{ $etapa > 2 ? 'done' : '' }}"></div>
        <div class="reg-step">
            <div class="reg-step-circle {{ $etapa === 3 ? 'done' : 'pending' }}">
                @if($etapa === 3) ✓ @else 3 @endif
            </div>
            <div class="reg-step-label {{ $etapa === 3 ? 'done' : '' }}">Confirmação</div>
        </div>
    </div>

    {{-- ── ETAPA 1 — Dados do escritório ── --}}
    @if($etapa === 1)
    <div>
        <div class="reg-title">Dados do escritório</div>
        <div class="reg-subtitle">Vamos começar com as informações do seu escritório.</div>

        <div class="reg-field">
            <label class="reg-lbl">Nome do escritório <span style="color:#dc2626;">*</span></label>
            <input wire:model.lazy="nome" type="text" class="reg-input"
                   placeholder="Ex: Silva & Associados Advocacia">
            @error('nome')<div class="reg-err">{{ $message }}</div>@enderror
        </div>

        <div class="reg-field">
            <label class="reg-lbl">E-mail do escritório <span style="color:#dc2626;">*</span></label>
            <input wire:model.lazy="email" type="email" class="reg-input"
                   placeholder="contato@escritorio.com.br">
            @error('email')<div class="reg-err">{{ $message }}</div>@enderror
        </div>

        <div class="reg-grid-2">
            <div class="reg-field">
                <label class="reg-lbl">CNPJ <span class="reg-optional">(opcional)</span></label>
                <input wire:model.lazy="cnpj" type="text" class="reg-input"
                       placeholder="00.000.000/0001-00">
            </div>
            <div class="reg-field">
                <label class="reg-lbl">Telefone <span class="reg-optional">(opcional)</span></label>
                <input wire:model.lazy="telefone" type="text" class="reg-input"
                       placeholder="(11) 99999-9999">
            </div>
        </div>

        <div class="reg-grid-2">
            <div class="reg-field">
                <label class="reg-lbl">OAB <span class="reg-optional">(opcional)</span></label>
                <input wire:model.lazy="oab" type="text" class="reg-input"
                       placeholder="SP 000000">
            </div>
            <div class="reg-field">
                <label class="reg-lbl">Cidade <span class="reg-optional">(opcional)</span></label>
                <input wire:model.lazy="cidade" type="text" class="reg-input"
                       placeholder="São Paulo">
            </div>
        </div>

        <button wire:click="avancarEtapa1" wire:loading.attr="disabled" class="reg-btn-primary">
            <span wire:loading.remove wire:target="avancarEtapa1">Continuar →</span>
            <span wire:loading wire:target="avancarEtapa1">Verificando…</span>
        </button>

        <div class="reg-footer">Já tem conta? <a href="{{ route('login') }}">Entrar</a></div>
    </div>
    @endif

    {{-- ── ETAPA 2 — Dados do administrador ── --}}
    @if($etapa === 2)
    <div>
        <div class="reg-title">Dados de acesso</div>
        <div class="reg-subtitle">Crie o login do administrador principal do escritório.</div>

        <div class="reg-field">
            <label class="reg-lbl">Nome completo <span style="color:#dc2626;">*</span></label>
            <input wire:model.lazy="admin_nome" type="text" class="reg-input"
                   placeholder="Seu nome completo">
            @error('admin_nome')<div class="reg-err">{{ $message }}</div>@enderror
        </div>

        <div class="reg-field">
            <label class="reg-lbl">E-mail de login <span style="color:#dc2626;">*</span></label>
            <input wire:model.lazy="admin_email" type="email" class="reg-input"
                   placeholder="seu@email.com.br">
            @error('admin_email')<div class="reg-err">{{ $message }}</div>@enderror
        </div>

        <div class="reg-grid-2">
            <div class="reg-field">
                <label class="reg-lbl">Senha <span style="color:#dc2626;">*</span></label>
                <input wire:model.lazy="admin_senha" type="password" class="reg-input"
                       placeholder="Mín. 8 caracteres">
                @error('admin_senha')<div class="reg-err">{{ $message }}</div>@enderror
            </div>
            <div class="reg-field">
                <label class="reg-lbl">Confirmar senha</label>
                <input wire:model.lazy="admin_senha_confirmation" type="password" class="reg-input"
                       placeholder="Repita a senha">
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:4px;">
            <button wire:click="voltar" class="reg-btn-ghost">← Voltar</button>
            <button wire:click="avancarEtapa2" wire:loading.attr="disabled" class="reg-btn-primary" style="flex:1;">
                <span wire:loading.remove wire:target="avancarEtapa2">Criar conta →</span>
                <span wire:loading wire:target="avancarEtapa2">Criando conta…</span>
            </button>
        </div>

        <div class="reg-footer">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:3px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Seus dados são protegidos com criptografia.
        </div>
    </div>
    @endif

    {{-- ── ETAPA 3 — Sucesso ── --}}
    @if($etapa === 3)
    <div style="text-align:center;">
        <div style="width:64px;height:64px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:2px solid #86efac;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="reg-title" style="margin-bottom:8px;">Conta criada com sucesso!</div>
        <div class="reg-subtitle" style="margin-bottom:20px;">
            Seu período de teste de <strong>30 dias</strong> começou. Explore todas as funcionalidades sem limitações.
        </div>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:18px;margin-bottom:24px;text-align:left;">
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Resumo da conta</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#64748b;">Escritório</span>
                    <span style="font-weight:700;color:#1e293b;">{{ $tenantNome }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#64748b;">E-mail de login</span>
                    <span style="font-weight:700;color:#1e293b;">{{ $admin_email }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#64748b;">Plano</span>
                    <span style="display:inline-flex;align-items:center;gap:5px;">
                        <span style="background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:800;padding:2px 10px;border-radius:99px;">DEMO</span>
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#64748b;">Trial expira em</span>
                    <span style="font-weight:700;color:#dc2626;">{{ $trialExpiracao }}</span>
                </div>
            </div>
        </div>

        <a href="{{ route('dashboard') }}"
           style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none;transition:opacity .2s;"
           onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            Acessar o sistema
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>

        <div class="reg-footer" style="margin-top:16px;">Você já está autenticado. Bem-vindo!</div>
    </div>
    @endif

</div>

</div>
