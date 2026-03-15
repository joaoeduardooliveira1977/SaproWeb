<div>
    @if($minutas->isEmpty())
    <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
        Nenhum template cadastrado. <a href="{{ route('minutas') }}" class="text-primary">Clique aqui</a> para criar templates.
    </p>
    @else

    {{-- ── Seleção de template ── --}}
    @if(!$minutaId)
    <div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:14px;">
            Selecione um template para gerar a minuta preenchida com os dados deste processo:
        </p>
        <div style="display:flex;flex-direction:column;gap:6px;">
            @foreach($minutas->groupBy('categoria') as $cat => $items)
            @php $cats = \App\Models\Minuta::categorias(); @endphp
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;padding:8px 0 4px;">
                {{ $cats[$cat] ?? $cat }}
            </div>
            @foreach($items as $m)
            <button wire:click="selecionar({{ $m->id }})"
                style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--border);border-radius:8px;
                       background:#f8fafc;cursor:pointer;text-align:left;width:100%;transition:all .15s;"
                onmouseover="this.style.background='#eff6ff';this.style.borderColor='#93c5fd';"
                onmouseout="this.style.background='#f8fafc';this.style.borderColor='var(--border)';">
                <span style="font-size:20px;">📄</span>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1e293b;">{{ $m->titulo }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:1px;">
                        {{ $cats[$m->categoria] ?? $m->categoria }}
                        · atualizado {{ $m->updated_at->format('d/m/Y') }}
                    </div>
                </div>
                <span style="margin-left:auto;font-size:12px;color:#2563a8;font-weight:600;">Usar →</span>
            </button>
            @endforeach
            @endforeach
        </div>
    </div>

    {{-- ── Minuta gerada ── --}}
    @else
    <div>
        {{-- Barra de ações --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px;">
            <div style="font-size:14px;font-weight:700;color:#1e293b;">📄 {{ $titulo }}</div>
            <div style="display:flex;gap:8px;">
                <button wire:click="gerarPdf" class="btn btn-primary btn-sm">⬇️ Baixar PDF</button>
                <button onclick="copiarMinuta()" class="btn btn-secondary btn-sm">📋 Copiar Texto</button>
                <button wire:click="limpar" class="btn btn-secondary btn-sm">← Trocar template</button>
            </div>
        </div>

        {{-- Área de edição --}}
        <div style="position:relative;">
            <textarea id="corpo-minuta" wire:model="corpoGerado" rows="24"
                style="width:100%;padding:16px;border:1px solid var(--border);border-radius:8px;
                       font-size:13px;font-family:'Segoe UI',sans-serif;line-height:1.7;
                       background:#fff;resize:vertical;color:#1e293b;"></textarea>
        </div>

        <p style="font-size:11px;color:var(--muted);margin-top:8px;">
            💡 O texto acima pode ser editado antes de copiar ou baixar o PDF.
        </p>
    </div>

    <script>
    function copiarMinuta() {
        const txt = document.getElementById('corpo-minuta');
        txt.select();
        navigator.clipboard.writeText(txt.value).then(() => {
            const btn = event.target;
            const original = btn.textContent;
            btn.textContent = '✅ Copiado!';
            setTimeout(() => btn.textContent = original, 2000);
        });
    }
    </script>
    @endif

    @endif
</div>
