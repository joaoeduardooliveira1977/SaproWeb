<div>

    {{-- Botão de entrada --}}
    @if(!$mostrarFormulario && !$minutaGerada)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#f0f7ff;border:1.5px solid #bfdbfe;border-radius:10px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:#dbeafe;border-radius:8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                </svg>
            </span>
            <div>
                <div style="font-size:13px;font-weight:700;color:#1e40af;">Gerar Minuta com IA</div>
                <div style="font-size:12px;color:#64748b;">Rascunho automático com base nos dados do processo</div>
            </div>
        </div>
        <button wire:click="abrirFormulario"
            style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#1d4ed8;border:none;border-radius:7px;color:#fff;font-size:12px;font-weight:600;cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nova Minuta IA
        </button>
    </div>
    @endif

    {{-- Formulário de geração --}}
    @if($mostrarFormulario)
    <div style="background:#f0f7ff;border:1.5px solid #bfdbfe;border-radius:12px;overflow:hidden;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:linear-gradient(135deg,#1e40af,#4f46e5);">
            <div style="display:flex;align-items:center;gap:10px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                </svg>
                <span style="font-size:14px;font-weight:700;color:#fff;">Gerar Minuta com IA</span>
            </div>
            <button wire:click="fechar" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:6px;color:#fff;width:28px;height:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div wire:loading wire:target="gerarMinuta" style="height:3px;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#3b82f6);background-size:200%;animation:iabar 1.4s linear infinite;"></div>

        <div style="padding:18px;display:flex;flex-direction:column;gap:14px;">
            <div class="form-field">
                <label class="lbl">Tipo de Documento</label>
                <select wire:model="tipoDocumento" style="padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;width:100%;background:var(--white);">
                    <option value="">— Selecione —</option>
                    @foreach($tipos as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label class="lbl">Instruções adicionais (opcional)</label>
                <textarea wire:model="instrucoes" rows="3" placeholder="Ex: incluir pedido de tutela antecipada, mencionar o contrato nº..., tom mais formal..."
                    style="padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;width:100%;resize:vertical;background:var(--white);font-family:inherit;"></textarea>
            </div>

            @if($erro)
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:13px;color:#dc2626;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $erro }}
            </div>
            @endif

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button wire:click="fechar" style="padding:8px 16px;background:var(--border);border:none;border-radius:7px;font-size:13px;cursor:pointer;color:var(--text);">Cancelar</button>
                <button wire:click="gerarMinuta" wire:loading.attr="disabled"
                    style="display:inline-flex;align-items:center;gap:7px;padding:8px 20px;background:#1d4ed8;border:none;border-radius:7px;color:#fff;font-size:13px;font-weight:600;cursor:pointer;"
                    wire:loading.class="opacity-60">
                    <svg wire:loading.remove wire:target="gerarMinuta" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                    </svg>
                    <svg wire:loading wire:target="gerarMinuta" style="animation:iapin .7s linear infinite;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <span wire:loading.remove wire:target="gerarMinuta">Gerar Rascunho</span>
                    <span wire:loading wire:target="gerarMinuta">Gerando... aguarde</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Minuta gerada --}}
    @if($minutaGerada)
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:linear-gradient(135deg,#1e40af,#4f46e5);">
            <div style="display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                <span style="font-size:13px;font-weight:700;color:#fff;">Rascunho: {{ $tipoDocumento }}</span>
                <span style="font-size:11px;padding:2px 8px;background:rgba(255,255,255,.2);border-radius:20px;color:#fff;">Gerado por IA — revise antes de usar</span>
            </div>
            <div style="display:flex;gap:6px;">
                <button onclick="copiarMinuta()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:6px;color:#fff;font-size:11px;cursor:pointer;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Copiar
                </button>
                <button wire:click="abrirFormulario" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:6px;color:rgba(255,255,255,.85);font-size:11px;cursor:pointer;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Regenerar
                </button>
            </div>
        </div>

        <div style="padding:20px;max-height:500px;overflow-y:auto;">
            <pre id="minutaIaTexto" style="font-family:inherit;font-size:13px;color:var(--text);line-height:1.8;white-space:pre-wrap;margin:0;">{{ $minutaGerada }}</pre>
        </div>
    </div>

    <script>
    function copiarMinuta() {
        const texto = document.getElementById('minutaIaTexto')?.innerText || '';
        navigator.clipboard.writeText(texto).then(() => {
            const btn = event.target.closest('button');
            const orig = btn.innerHTML;
            btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copiado!';
            setTimeout(() => btn.innerHTML = orig, 2000);
        });
    }
    </script>
    @endif

    <style>
    @keyframes iapin { to { transform: rotate(360deg); } }
    @keyframes iabar { 0%{background-position:0%} 100%{background-position:200%} }
    </style>
</div>
