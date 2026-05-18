<div>
    @if($erro)
        <div style="padding:1.5rem;text-align:center;color:#b91c1c;">
            <p style="font-size:.95rem;">{{ $erro }}</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;padding:1rem 0;">

            {{-- QR Code --}}
            @if($qrBase64)
                <img src="{{ $qrBase64 }}"
                     alt="QR Code PIX"
                     style="width:200px;height:200px;border:1px solid #e5e7eb;border-radius:8px;padding:4px;">
            @else
                <div style="width:200px;height:200px;border:2px dashed #d1d5db;border-radius:8px;
                            display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:.85rem;">
                    QR indisponível
                </div>
            @endif

            {{-- Valor --}}
            <div style="text-align:center;">
                <p style="font-size:.78rem;color:#6b7280;margin:0;">Valor a pagar</p>
                <p style="font-size:1.4rem;font-weight:700;color:#111827;margin:0;">{{ $valorFmt }}</p>
                <p style="font-size:.8rem;color:#6b7280;margin:0;">{{ $beneficiario }}</p>
            </div>

            {{-- Código PIX copia e cola --}}
            <div style="width:100%;">
                <p style="font-size:.78rem;color:#6b7280;margin-bottom:4px;">Código PIX (copia e cola)</p>
                <div style="display:flex;gap:6px;align-items:stretch;">
                    <input id="pix-payload-{{ $lancamentoId }}"
                           type="text"
                           readonly
                           value="{{ $payload }}"
                           style="flex:1;font-size:.72rem;padding:6px 8px;border:1px solid #d1d5db;
                                  border-radius:6px;background:#f9fafb;color:#374151;font-family:monospace;">
                    <button onclick="copiarPix('pix-payload-{{ $lancamentoId }}', this)"
                            style="padding:6px 12px;background:#2563eb;color:#fff;border:none;
                                   border-radius:6px;cursor:pointer;font-size:.8rem;white-space:nowrap;">
                        Copiar
                    </button>
                </div>
            </div>

            {{-- Instruções --}}
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;
                        padding:.75rem 1rem;width:100%;font-size:.8rem;color:#166534;">
                <strong>Como pagar:</strong>
                <ol style="margin:.4rem 0 0 1.1rem;padding:0;line-height:1.7;">
                    <li>Abra o aplicativo do seu banco</li>
                    <li>Escolha a opção <strong>PIX → QR Code</strong> ou <strong>Copia e Cola</strong></li>
                    <li>Confirme os dados e o valor antes de pagar</li>
                </ol>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function copiarPix(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Copiado!';
        btn.style.background = '#16a34a';
        setTimeout(() => { btn.textContent = orig; btn.style.background = '#2563eb'; }, 2000);
    }).catch(() => {
        input.select();
        document.execCommand('copy');
    });
}
</script>
@endpush
