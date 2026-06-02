<div style="width:100%;max-width:420px;margin:0 auto;">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:32px;">
        <div style="width:64px;height:64px;background:rgba(255,255,255,.1);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:28px;">⚖️</div>
        <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:2px;">SOFTWARE JURÍDICO</div>
    </div>

    <div style="background:#fff;border-radius:20px;padding:40px 36px;box-shadow:0 24px 64px rgba(0,0,0,.35);">

        <div style="text-align:center;margin-bottom:28px;">
            <div style="font-size:36px;margin-bottom:12px;">📧</div>
            <h1 style="font-size:20px;font-weight:800;color:#0f2540;margin:0 0 8px;">Verifique seu email</h1>
            <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">
                Enviamos um código de 6 dígitos para<br>
                <strong style="color:#0f2540;">{{ $emailDestino }}</strong>
            </p>
        </div>

        @if(session('info'))
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;font-size:13px;color:#1e40af;margin-bottom:20px;">
            {{ session('info') }}
        </div>
        @endif

        @if($erro)
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:13px;color:#dc2626;margin-bottom:20px;">
            {{ $erro }}
        </div>
        @endif

        @if($reenvioOk)
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 14px;font-size:13px;color:#16a34a;margin-bottom:20px;">
            ✅ Novo código enviado! Verifique sua caixa de entrada.
        </div>
        @endif

        {{-- 6 inputs separados --}}
        <div style="display:flex;gap:10px;justify-content:center;margin-bottom:28px;" id="codigo-inputs">
            @foreach(['d1','d2','d3','d4','d5','d6'] as $i => $field)
            <input
                wire:model.live="{{ $field }}"
                id="digit-{{ $i+1 }}"
                type="text"
                inputmode="numeric"
                maxlength="1"
                autocomplete="off"
                style="width:50px;height:58px;text-align:center;font-size:24px;font-weight:800;color:#0f2540;
                       border:2px solid #e2e8f0;border-radius:10px;outline:none;
                       transition:border-color .15s;background:#f8fafc;"
                oninput="avancarDigito(this, {{ $i+1 }})"
                onkeydown="voltarDigito(event, {{ $i+1 }})"
                onfocus="this.style.borderColor='#1a3a5c';this.style.background='#fff'"
                onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'"
            >
            @endforeach
        </div>

        <button wire:click="verificar" wire:loading.attr="disabled"
            style="width:100%;padding:13px;background:#1a3a5c;color:#fff;border:none;border-radius:10px;
                   font-size:15px;font-weight:700;cursor:pointer;letter-spacing:.3px;
                   transition:opacity .15s;"
            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            <span wire:loading.remove wire:target="verificar">✅ Verificar e acessar</span>
            <span wire:loading wire:target="verificar">Verificando...</span>
        </button>

        <div style="text-align:center;margin-top:20px;">
            <button wire:click="reenviar" wire:loading.attr="disabled"
                id="btn-reenviar"
                style="background:none;border:none;font-size:13px;color:#1a3a5c;font-weight:600;cursor:pointer;text-decoration:underline;"
                wire:loading.class="opacity-50">
                <span wire:loading.remove wire:target="reenviar">
                    Reenviar código
                    <span id="countdown-text" style="display:none;color:#94a3b8;font-weight:400;"> (aguarde <span id="countdown-num">60</span>s)</span>
                </span>
                <span wire:loading wire:target="reenviar">Enviando...</span>
            </button>
        </div>

        <div style="text-align:center;margin-top:16px;">
            <a href="{{ route('cadastro') }}" style="font-size:12px;color:#94a3b8;text-decoration:none;">
                ← Voltar e corrigir email
            </a>
        </div>
    </div>

    <div style="text-align:center;margin-top:20px;font-size:11px;color:rgba(255,255,255,.4);">
        Não recebeu? Verifique a pasta de spam.
    </div>
</div>

<script>
function avancarDigito(input, pos) {
    input.value = input.value.replace(/\D/g, '').slice(-1);
    if (input.value && pos < 6) {
        document.getElementById('digit-' + (pos + 1))?.focus();
    }
    // Dispara wire:model manualmente para cada campo
}

function voltarDigito(e, pos) {
    if (e.key === 'Backspace' && !e.target.value && pos > 1) {
        document.getElementById('digit-' + (pos - 1))?.focus();
    }
}

document.addEventListener('livewire:initialized', () => {
    // Auto-foca no primeiro campo
    document.getElementById('digit-1')?.focus();

    Livewire.on('iniciar-countdown', () => {
        const btn  = document.getElementById('btn-reenviar');
        const txt  = document.getElementById('countdown-text');
        const num  = document.getElementById('countdown-num');
        let seg    = 60;

        btn.disabled = true;
        txt.style.display = 'inline';
        num.textContent  = seg;

        const iv = setInterval(() => {
            seg--;
            num.textContent = seg;
            if (seg <= 0) {
                clearInterval(iv);
                btn.disabled = false;
                txt.style.display = 'none';
            }
        }, 1000);
    });
});
</script>
