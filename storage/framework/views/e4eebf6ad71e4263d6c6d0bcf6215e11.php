<div style="min-height:100vh; background: linear-gradient(135deg, #1a3a5c 0%, #2563a8 100%); display:flex; align-items:center; justify-content:center; padding:24px;">
    <div style="background:white; border-radius:20px; padding:48px 40px; width:100%; max-width:420px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

        
        <div style="text-align:center; margin-bottom:36px;">
            <div style="margin-bottom:12px;display:flex;justify-content:center;color:var(--primary);"><svg aria-hidden="true" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 9l9-6 9 6M3 9h18M7 21h10"/><path d="M5 9l2 6H3L5 9zM19 9l2 6h-4l2-6z"/></svg></div>
            <h1 style="font-size:24px; font-weight:700; color:var(--primary);">JURÍDICO</h1>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Portal do Cliente — Gestão Jurídica</p>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($erro): ?>
        <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#dc2626;">
            <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> <?php echo e($erro); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($info && !$erro): ?>
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#1d4ed8;">
            <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> <?php echo e($info); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($etapa === 'credenciais'): ?>

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
            style="width:100%; padding:14px; background:#2563a8; color:white; border:none; border-radius:10px; font-size:15px; font-weight:600; cursor:pointer;">
            <span wire:loading.remove wire:target="autenticar">Entrar no Portal</span>
            <span wire:loading wire:target="autenticar">Verificando...</span>
        </button>

        <p style="text-align:center; font-size:12px; color:#94a3b8; margin-top:24px;">
            Esqueceu sua senha? Entre em contato com o escritório.
        </p>

        
        <?php elseif($etapa === 'codigo'): ?>

        
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px;padding:16px;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
            <div style="width:44px;height:44px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:#1a3a5c;">Verificação em duas etapas</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">Código enviado para <strong><?php echo e($telefoneExib); ?></strong></div>
            </div>
        </div>

        <div style="margin-bottom:28px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Código de verificação</label>
            <input
                wire:model="codigo"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                placeholder="000000"
                wire:keydown.enter="verificarCodigo"
                autofocus
                style="width:100%; padding:14px 16px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:22px; font-weight:700; letter-spacing:8px; text-align:center; outline:none; transition:border-color .2s;"
                onfocus="this.style.borderColor='#2563a8'"
                onblur="this.style.borderColor='#e2e8f0'"
            >
            <p style="font-size:12px; color:#94a3b8; margin-top:8px; text-align:center;">
                O código expira em 5 minutos.
            </p>
        </div>

        <button
            wire:click="verificarCodigo"
            wire:loading.attr="disabled"
            style="width:100%; padding:14px; background:#2563a8; color:white; border:none; border-radius:10px; font-size:15px; font-weight:600; cursor:pointer; margin-bottom:12px;">
            <span wire:loading.remove wire:target="verificarCodigo">Confirmar Código</span>
            <span wire:loading wire:target="verificarCodigo">Verificando...</span>
        </button>

        <button
            wire:click="reenviarCodigo"
            style="width:100%; padding:10px; background:transparent; color:#64748b; border:1.5px solid #e2e8f0; border-radius:10px; font-size:13px; cursor:pointer;">
            Voltar e reenviar código
        </button>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
</div>
<?php /**PATH C:\projetos\saproweb-base\resources\views/livewire/portal/login.blade.php ENDPATH**/ ?>