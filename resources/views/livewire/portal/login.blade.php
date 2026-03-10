<div style="min-height:100vh; background: linear-gradient(135deg, #1a3a5c 0%, #2563a8 100%); display:flex; align-items:center; justify-content:center; padding:24px;">
    <div style="background:white; border-radius:20px; padding:48px 40px; width:100%; max-width:420px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

        {{-- Logo --}}
        <div style="text-align:center; margin-bottom:36px;">
            <div style="font-size:48px; margin-bottom:12px;">⚖️</div>
            <h1 style="font-size:24px; font-weight:700; color:#1a3a5c;">SAPRO</h1>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Portal do Cliente — Gestão Jurídica</p>
        </div>

        {{-- Erro --}}
        @if($erro)
        <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#dc2626;">
            ⚠️ {{ $erro }}
        </div>
        @endif

        {{-- Form --}}
        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">CPF ou CNPJ</label>
            <input
                wire:model="cpf_cnpj"
                type="text"
                placeholder="000.000.000-00"
                style="width:100%; padding:12px 16px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:15px; outline:none; transition:border-color .2s;"
                onfocus="this.style.borderColor='#2563a8'"
                onblur="this.style.borderColor='#e2e8f0'"
            >
        </div>

        <div style="margin-bottom:28px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Senha</label>
            <input
                wire:model="senha"
                type="password"
                placeholder="••••••••"
                wire:keydown.enter="autenticar"
                style="width:100%; padding:12px 16px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:15px; outline:none; transition:border-color .2s;"
                onfocus="this.style.borderColor='#2563a8'"
                onblur="this.style.borderColor='#e2e8f0'"
            >
        </div>

        <button
            wire:click="autenticar"
            wire:loading.attr="disabled"
            style="width:100%; padding:14px; background:#2563a8; color:white; border:none; border-radius:10px; font-size:15px; font-weight:600; cursor:pointer;"
        >
            <span wire:loading.remove>Entrar no Portal</span>
            <span wire:loading>Verificando...</span>
        </button>

        <p style="text-align:center; font-size:12px; color:#94a3b8; margin-top:24px;">
            Esqueceu sua senha? Entre em contato com o escritório.
        </p>
    </div>
</div>
