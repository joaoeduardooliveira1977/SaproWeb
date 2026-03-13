<div style="position:relative;" wire:poll.60s>

    {{-- ── Sininho ── --}}
    <button wire:click="toggle"
        style="position:relative;background:none;border:none;cursor:pointer;font-size:20px;padding:4px 8px;line-height:1;color:var(--muted);"
        title="Notificações">
        🔔
        @if($naoLidas > 0)
        <span style="
            position:absolute;top:-2px;right:-2px;
            background:#dc2626;color:#fff;
            font-size:10px;font-weight:700;line-height:1;
            padding:2px 5px;border-radius:10px;
            min-width:16px;text-align:center;">
            {{ $naoLidas > 99 ? '99+' : $naoLidas }}
        </span>
        @endif
    </button>

    {{-- ── Dropdown ── --}}
    @if($aberto)
    <div style="
        position:absolute;right:0;top:calc(100% + 8px);
        width:380px;max-width:95vw;
        background:#fff;border:1px solid var(--border);
        border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.15);
        z-index:200;overflow:hidden;">

        {{-- Cabeçalho --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);background:#f8fafc;">
            <span style="font-size:13px;font-weight:700;color:var(--primary);">
                🔔 Notificações
                @if($naoLidas > 0)
                    <span style="background:#dc2626;color:#fff;font-size:10px;padding:1px 6px;border-radius:8px;margin-left:4px;">{{ $naoLidas }}</span>
                @endif
            </span>
            <div style="display:flex;gap:8px;align-items:center;">
                @if($naoLidas > 0)
                <button wire:click="marcarTodasLidas"
                    style="font-size:11px;background:none;border:none;cursor:pointer;color:var(--primary-light);text-decoration:underline;">
                    Marcar todas como lidas
                </button>
                @endif
                <button wire:click="fechar"
                    style="background:none;border:none;cursor:pointer;font-size:16px;color:var(--muted);line-height:1;">✕</button>
            </div>
        </div>

        {{-- Lista --}}
        <div style="max-height:420px;overflow-y:auto;">
            @forelse($notificacoes as $n)
            <div wire:key="notif-{{ $n->id }}"
                style="padding:11px 14px;border-bottom:1px solid #f1f5f9;
                       background:{{ $n->lida ? '#fff' : $n->cor() }};
                       opacity:{{ $n->lida ? '.65' : '1' }};
                       cursor:default;transition:background .15s;">
                <div style="display:flex;align-items:flex-start;gap:8px;">
                    <span style="font-size:16px;flex-shrink:0;margin-top:1px;">{{ $n->icone() }}</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:{{ $n->lida ? '400' : '700' }};color:var(--text);line-height:1.3;">
                            {{ $n->titulo }}
                        </div>
                        @if($n->mensagem)
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">
                            {{ $n->mensagem }}
                        </div>
                        @endif
                        <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                            {{ $n->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:4px;flex-shrink:0;">
                        @if($n->link)
                        <a href="{{ $n->link }}" wire:click="marcarLida({{ $n->id }})"
                           style="font-size:10px;background:var(--primary);color:#fff;padding:2px 7px;border-radius:5px;text-decoration:none;white-space:nowrap;">
                            Ver
                        </a>
                        @endif
                        @if(!$n->lida)
                        <button wire:click="marcarLida({{ $n->id }})"
                            style="font-size:10px;background:none;border:1px solid var(--border);border-radius:5px;cursor:pointer;padding:2px 5px;color:var(--muted);white-space:nowrap;">
                            ✓ Lida
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
                <div style="font-size:32px;margin-bottom:8px;">🎉</div>
                Nenhuma notificação.
            </div>
            @endforelse
        </div>

    </div>

    {{-- Overlay para fechar ao clicar fora --}}
    <div wire:click="fechar"
         style="position:fixed;inset:0;z-index:199;"
         x-data x-show="true"></div>
    @endif

</div>
