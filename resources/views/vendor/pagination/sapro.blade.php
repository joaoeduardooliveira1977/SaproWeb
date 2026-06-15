@if ($paginator->hasPages())
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;font-size:13px;color:#64748b;">

    {{-- Contagem --}}
    <span>
        Exibindo <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
        de <strong>{{ $paginator->total() }}</strong> registros
    </span>

    {{-- Botões de página --}}
    <div style="display:flex;align-items:center;gap:4px;">

        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;color:#cbd5e1;font-size:12px;cursor:default;">‹ Ant.</span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;color:#374151;font-size:12px;cursor:pointer;">
                ‹ Ant.
            </button>
        @endif

        {{-- Páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:4px 6px;color:#94a3b8;font-size:12px;">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:4px 10px;border:1px solid #2563a8;border-radius:6px;background:#2563a8;color:#fff;font-size:12px;font-weight:700;">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                            style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;color:#374151;font-size:12px;cursor:pointer;">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Próxima --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;color:#374151;font-size:12px;cursor:pointer;">
                Próx. ›
            </button>
        @else
            <span style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;color:#cbd5e1;font-size:12px;cursor:default;">Próx. ›</span>
        @endif

    </div>
</div>
@endif
