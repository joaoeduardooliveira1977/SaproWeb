<div style="position:relative;flex:1;max-width:400px;" x-data @click.outside="$wire.fechar()">

    {{-- Input --}}
    <div style="position:relative;">
        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted);display:flex;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            placeholder="Buscar processos, pessoas... (Ctrl+K)"
            autocomplete="off"
            id="busca-global-input"
            style="width:100%;padding:7px 10px 7px 32px;border:1.5px solid var(--border);
                   border-radius:8px;font-size:13px;background:#f8fafc;
                   color:var(--text);outline:none;transition:border-color .2s;"
            onfocus="this.style.borderColor='#2563a8';this.style.background='#fff'"
            onblur="this.style.borderColor='var(--border)';this.style.background='#f8fafc'"
        >
        <span wire:loading wire:target="query"
            style="position:absolute;right:32px;top:50%;transform:translateY(-50%);color:var(--muted);display:flex;">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </span>
        @if($aberto)
        <button wire:click="fechar"
            style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;
                   cursor:pointer;color:var(--muted);font-size:14px;line-height:1;padding:2px 4px;display:flex;align-items:center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        @endif
    </div>

    {{-- Dropdown de resultados --}}
    @if($aberto)
    <div style="position:absolute;top:calc(100% + 8px);left:0;right:0;
                background:#fff;border:1px solid var(--border);border-radius:10px;
                box-shadow:0 8px 32px rgba(0,0,0,.18);z-index:9999;overflow:hidden;min-width:380px;">

        @if(empty($resultados))
        <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">
            Nenhum resultado encontrado para "<strong>{{ $query }}</strong>"
        </div>
        @else

        {{-- Agrupa por tipo --}}
        @php $grupos = collect($resultados)->groupBy('tipo'); @endphp

        @foreach($grupos as $tipo => $itens)
        <div>
            {{-- Cabeçalho do grupo --}}
            <div style="padding:6px 14px;background:#f8fafc;font-size:10px;font-weight:700;
                        text-transform:uppercase;letter-spacing:.6px;color:var(--muted);
                        border-bottom:1px solid var(--border);">
                {{ $itens->first()['icone'] }} {{ $tipo }}s
            </div>

            @foreach($itens as $item)
            <a href="{{ $item['url'] }}" wire:navigate
                style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                       text-decoration:none;color:var(--text);border-bottom:1px solid #f1f5f9;
                       transition:background .12s;"
                onmouseover="this.style.background='#eff6ff'"
                onmouseout="this.style.background=''">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $item['titulo'] }}
                    </div>
                    @if($item['subtitulo'])
                    <div style="font-size:11px;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $item['subtitulo'] }}
                    </div>
                    @endif
                </div>
                @if($item['badge'])
                <span style="padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;
                             background:{{ $item['badge_cor'] }}22;color:{{ $item['badge_cor'] }};
                             white-space:nowrap;flex-shrink:0;">
                    {{ $item['badge'] }}
                </span>
                @endif
                <span style="color:#94a3b8;font-size:12px;flex-shrink:0;">→</span>
            </a>
            @endforeach
        </div>
        @endforeach

        <div style="padding:8px 14px;background:#f8fafc;font-size:11px;color:var(--muted);
                    border-top:1px solid var(--border);">
            {{ count($resultados) }} resultado(s) encontrado(s)
        </div>
        @endif
    </div>
    @endif

    {{-- Atalho Ctrl+K --}}
    <script>
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const input = document.getElementById('busca-global-input');
            if (input) { input.focus(); input.select(); }
        }
        if (e.key === 'Escape') {
            const input = document.getElementById('busca-global-input');
            if (document.activeElement === input) {
                @this.fechar();
                input.blur();
            }
        }
    });
    </script>
</div>
