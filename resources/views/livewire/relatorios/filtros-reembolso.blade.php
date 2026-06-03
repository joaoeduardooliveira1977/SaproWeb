<div style="max-width:520px;margin:0 auto;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <div style="width:44px;height:44px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">📄</div>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#0f2540;margin:0;">Pedido de Reembolso</h1>
            <p style="font-size:12px;color:#64748b;margin:0;">Gera documento formal de pedido de reembolso para o cliente</p>
        </div>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;">

        {{-- Cliente autocomplete --}}
        <div style="margin-bottom:16px;position:relative;">
            <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cliente *</label>
            <div style="position:relative;">
                <input wire:model.live.debounce.300ms="clienteBusca"
                    type="text"
                    placeholder="Digite o nome, CPF ou CNPJ..."
                    autocomplete="off"
                    style="width:100%;padding:10px 36px 10px 12px;border:1.5px solid {{ $clienteId ? '#22c55e' : '#e5e7eb' }};border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                @if($clienteId)
                <button wire:click="limparCliente" type="button"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:16px;line-height:1;">✕</button>
                @endif
            </div>
            @if($clienteId)
            <div style="margin-top:4px;font-size:12px;color:#16a34a;">✓ {{ $clienteNome }}</div>
            @endif
            @error('clienteId') <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror

            {{-- Sugestões --}}
            @if(count($clienteSugestoes))
            <div style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:50;max-height:220px;overflow-y:auto;">
                @foreach($clienteSugestoes as $c)
                <div wire:click="selecionarCliente({{ $c['id'] }}, '{{ addslashes($c['nome']) }}')"
                    style="padding:10px 14px;cursor:pointer;border-bottom:1px solid #f1f5f9;font-size:13px;"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <div style="font-weight:600;color:#0f2540;">{{ $c['nome'] }}</div>
                    @if($c['cpf_cnpj'])<div style="font-size:11px;color:#94a3b8;">{{ $c['cpf_cnpj'] }}</div>@endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Período --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Data início *</label>
                <input wire:model="dataIni" type="date"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                @error('dataIni') <div style="font-size:12px;color:#dc2626;margin-top:3px;">{{ $message }}</div> @enderror
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Data fim *</label>
                <input wire:model="dataFim" type="date"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                @error('dataFim') <div style="font-size:12px;color:#dc2626;margin-top:3px;">{{ $message }}</div> @enderror
            </div>
        </div>

        <button wire:click="gerar" wire:loading.attr="disabled"
            style="width:100%;padding:12px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:opacity .15s;"
            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            <span wire:loading.remove wire:target="gerar">📥 Gerar Pedido de Reembolso PDF</span>
            <span wire:loading wire:target="gerar">Gerando...</span>
        </button>
    </div>

    <div style="margin-top:12px;text-align:center;">
        <a href="{{ route('despesas-reembolsos') }}" style="font-size:13px;color:#64748b;text-decoration:none;">← Voltar para Despesas &amp; Reembolsos</a>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('abrir-pdf', ({ url }) => {
        window.open(url, '_blank');
    });
});
</script>
