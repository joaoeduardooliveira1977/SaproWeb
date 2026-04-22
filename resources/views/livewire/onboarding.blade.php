<div>
<style>
.ob-card {
    background: var(--bg-primary, #fff);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    padding: 2rem;
}
.ob-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    margin-bottom: 2rem;
}
.ob-step {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 600;
    border: 2px solid #d1d5db;
    color: #9ca3af;
    background: #fff;
    transition: all .2s;
}
.ob-step.active  { border-color: #2563eb; background: #2563eb; color: #fff; }
.ob-step.done    { border-color: #16a34a; background: #16a34a; color: #fff; }
.ob-step-line { flex:1; height:2px; background:#d1d5db; max-width:60px; }
.ob-step-line.done { background:#16a34a; }
.ob-title { font-size:1.25rem; font-weight:700; color:var(--text-primary,#111827); margin-bottom:.25rem; }
.ob-subtitle { font-size:.875rem; color:var(--text-muted,#6b7280); margin-bottom:1.5rem; }
.ob-field { margin-bottom:1rem; }
.ob-field label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary,#374151); margin-bottom:.3rem; }
.ob-field input[type=text],
.ob-field input[type=email],
.ob-field input[type=tel] {
    width:100%; padding:.55rem .75rem;
    border:1px solid #d1d5db; border-radius:6px;
    font-size:.9rem; color:var(--text-primary,#111827);
    background:var(--bg-input,#fff);
    outline:none; transition:border-color .15s;
}
.ob-field input:focus { border-color:#2563eb; }
.ob-row2 { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.ob-actions { display:flex; justify-content:space-between; align-items:center; margin-top:1.75rem; }
.btn-primary {
    padding:.6rem 1.5rem; border-radius:6px; border:none; cursor:pointer;
    background:#2563eb; color:#fff; font-weight:600; font-size:.9rem;
    transition:background .15s;
}
.btn-primary:hover { background:#1d4ed8; }
.btn-secondary {
    padding:.6rem 1.25rem; border-radius:6px; border:1px solid #d1d5db; cursor:pointer;
    background:transparent; color:var(--text-secondary,#374151); font-size:.9rem;
    transition:background .15s;
}
.btn-secondary:hover { background:#f3f4f6; }
.btn-link { background:none; border:none; cursor:pointer; color:#6b7280; font-size:.85rem; text-decoration:underline; padding:0; }
.area-grid { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; }
.area-check { display:flex; align-items:center; gap:.5rem; padding:.6rem .75rem; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:all .15s; }
.area-check:has(input:checked) { border-color:#2563eb; background:#eff6ff; }
.area-check input { width:16px; height:16px; accent-color:#2563eb; cursor:pointer; }
.area-check span { font-size:.875rem; color:var(--text-primary,#374151); }
.logo-preview { width:120px; height:120px; border-radius:8px; object-fit:contain; border:1px solid #e5e7eb; }
.upload-area {
    border:2px dashed #d1d5db; border-radius:8px; padding:2rem 1rem; text-align:center;
    cursor:pointer; transition:border-color .15s;
}
.upload-area:hover { border-color:#2563eb; }
.success-icon { font-size:3.5rem; text-align:center; margin-bottom:1rem; }
.error-msg { font-size:.8rem; color:#dc2626; margin-top:.25rem; }
</style>

{{-- Indicador de passos --}}
<div class="ob-card">
    <div class="ob-steps">
        @for($i = 1; $i <= $totalSteps; $i++)
            <div class="ob-step {{ $step > $i ? 'done' : ($step === $i ? 'active' : '') }}">
                @if($step > $i)
                    ✓
                @else
                    {{ $i }}
                @endif
            </div>
            @if($i < $totalSteps)
                <div class="ob-step-line {{ $step > $i ? 'done' : '' }}"></div>
            @endif
        @endfor
    </div>

    {{-- Step 1: Dados do escritório --}}
    @if($step === 1)
        <div class="ob-title">Dados do escritório</div>
        <div class="ob-subtitle">Preencha as informações básicas do seu escritório.</div>

        <div class="ob-field">
            <label>Nome do escritório *</label>
            <input type="text" wire:model="nome" placeholder="Ex: Advocacia Silva & Associados">
            @error('nome') <div class="error-msg">{{ $message }}</div> @enderror
        </div>
        <div class="ob-row2">
            <div class="ob-field">
                <label>CNPJ</label>
                <input type="text" wire:model="cnpj" placeholder="00.000.000/0000-00">
            </div>
            <div class="ob-field">
                <label>OAB</label>
                <input type="text" wire:model="oab" placeholder="SP 123456">
            </div>
        </div>
        <div class="ob-row2">
            <div class="ob-field">
                <label>E-mail *</label>
                <input type="email" wire:model="email" placeholder="contato@escritorio.com.br">
                @error('email') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div class="ob-field">
                <label>Telefone</label>
                <input type="tel" wire:model="telefone" placeholder="(11) 99999-9999">
            </div>
        </div>
        <div class="ob-row2">
            <div class="ob-field">
                <label>Endereço</label>
                <input type="text" wire:model="endereco" placeholder="Rua, número, sala">
            </div>
            <div class="ob-field">
                <label>Cidade</label>
                <input type="text" wire:model="cidade" placeholder="São Paulo — SP">
            </div>
        </div>

        <div class="ob-actions">
            <span></span>
            <button class="btn-primary" wire:click="proximoStep" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="proximoStep">Próximo →</span>
                <span wire:loading wire:target="proximoStep">Salvando...</span>
            </button>
        </div>
    @endif

    {{-- Step 2: Logo --}}
    @if($step === 2)
        <div class="ob-title">Logo do escritório</div>
        <div class="ob-subtitle">Adicione a identidade visual do escritório (opcional).</div>

        <div style="display:flex; flex-direction:column; align-items:center; gap:1.25rem;">
            @if($logoAtual)
                <img src="{{ Storage::url($logoAtual) }}" class="logo-preview" alt="Logo atual">
            @endif

            <label class="upload-area" style="width:100%; max-width:360px;">
                <input type="file" wire:model="logo" accept="image/*" style="display:none">
                @if($logo)
                    <div style="color:#16a34a; font-weight:600;">✓ Imagem selecionada</div>
                    <div style="font-size:.8rem; color:#6b7280; margin-top:.25rem;">Clique para trocar</div>
                @else
                    <div style="font-size:1.5rem; margin-bottom:.5rem;">🖼️</div>
                    <div style="font-weight:600; color:var(--text-secondary,#374151);">Clique para enviar uma imagem</div>
                    <div style="font-size:.8rem; color:#6b7280; margin-top:.25rem;">PNG, JPG ou SVG — máx. 2 MB</div>
                @endif
                @error('logo') <div class="error-msg">{{ $message }}</div> @enderror
            </label>
        </div>

        <div class="ob-actions">
            <button class="btn-secondary" wire:click="voltarStep">← Voltar</button>
            <div style="display:flex; gap:.75rem; align-items:center;">
                <button class="btn-link" wire:click="pularStep">Pular esta etapa</button>
                <button class="btn-primary" wire:click="proximoStep" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="proximoStep">Próximo →</span>
                    <span wire:loading wire:target="proximoStep">Enviando...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Step 3: Áreas de atuação --}}
    @if($step === 3)
        <div class="ob-title">Áreas de atuação</div>
        <div class="ob-subtitle">Selecione as áreas do seu escritório para personalizar o sistema.</div>

        <div class="area-grid">
            <label class="area-check">
                <input type="checkbox" wire:model="temProcessosCivis">
                <span>⚖️ Cível</span>
            </label>
            <label class="area-check">
                <input type="checkbox" wire:model="temProcessosTrabalhistas">
                <span>👷 Trabalhista</span>
            </label>
            <label class="area-check">
                <input type="checkbox" wire:model="temProcessosFamilia">
                <span>👨‍👩‍👧 Família</span>
            </label>
            <label class="area-check">
                <input type="checkbox" wire:model="temProcessosTributarios">
                <span>🧾 Tributário</span>
            </label>
            <label class="area-check">
                <input type="checkbox" wire:model="temProcessosCriminais">
                <span>🔒 Criminal</span>
            </label>
            <label class="area-check">
                <input type="checkbox" wire:model="temProcessosEmpresariais">
                <span>🏢 Empresarial</span>
            </label>
        </div>

        <div class="ob-actions">
            <button class="btn-secondary" wire:click="voltarStep">← Voltar</button>
            <div style="display:flex; gap:.75rem; align-items:center;">
                <button class="btn-link" wire:click="pularStep">Pular esta etapa</button>
                <button class="btn-primary" wire:click="proximoStep" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="proximoStep">Próximo →</span>
                    <span wire:loading wire:target="proximoStep">Salvando...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Step 4: Conclusão --}}
    @if($step === 4)
        <div style="text-align:center; padding:1rem 0;">
            <div class="success-icon">🎉</div>
            <div class="ob-title" style="justify-content:center;">Tudo pronto!</div>
            <div class="ob-subtitle" style="margin-bottom:2rem;">
                Seu escritório está configurado. Você pode editar estas informações a qualquer momento em <strong>Configurações</strong>.
            </div>

            <button class="btn-primary" style="width:100%; padding:.75rem; font-size:1rem;" wire:click="concluir" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="concluir">Acessar o sistema →</span>
                <span wire:loading wire:target="concluir">Aguarde...</span>
            </button>
        </div>
    @endif
</div>
</div>
