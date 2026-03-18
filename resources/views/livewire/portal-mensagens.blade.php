<div>


<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;height:calc(100vh - 160px);min-height:400px;">

    {{-- ── Lista de clientes ── --}}
    <div class="card" style="margin:0;display:flex;flex-direction:column;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:13px;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:6px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Clientes
                @if($totalNaoLidas > 0)
                    <span style="background:#dc2626;color:#fff;font-size:10px;padding:1px 6px;border-radius:8px;margin-left:4px;">{{ $totalNaoLidas }}</span>
                @endif
            </span>
            <select wire:model.live="filtro" style="font-size:11px;padding:4px 6px;border:1px solid var(--border);border-radius:6px;">
                <option value="nao_lidas">Não lidas</option>
                <option value="todas">Todas</option>
            </select>
        </div>

        <div style="overflow-y:auto;flex:1;">
            @forelse($clientes as $cli)
            <div wire:click="selecionarCliente({{ $cli->id }})"
                style="padding:12px 16px;cursor:pointer;border-bottom:1px solid #f1f5f9;
                       background:{{ $pessoaId === $cli->id ? '#eff6ff' : '#fff' }};
                       border-left:3px solid {{ $pessoaId === $cli->id ? 'var(--primary)' : 'transparent' }};
                       transition:background .1s;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $cli->nome }}</span>
                    @if($cli->nao_lidas > 0)
                        <span style="background:#dc2626;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:8px;">{{ $cli->nao_lidas }}</span>
                    @endif
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                    {{ \Carbon\Carbon::parse($cli->ultima_msg)->diffForHumans() }}
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
                @if($filtro === 'nao_lidas')
                    Nenhuma mensagem não lida.
                @else
                    Nenhuma mensagem ainda.
                @endif
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Conversa ── --}}
    <div class="card" style="margin:0;display:flex;flex-direction:column;overflow:hidden;">
        @if($clienteSelecionado)

        <div style="padding:14px 20px;border-bottom:1px solid var(--border);background:#f8fafc;">
            <span style="font-size:14px;font-weight:700;color:var(--primary);">{{ $clienteSelecionado->nome }}</span>
        </div>

        {{-- Mensagens --}}
        <div style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;" id="admin-chat-box">
            @forelse($conversa as $msg)
            @php $deCliente = $msg->de === 'cliente'; @endphp
            <div style="display:flex;flex-direction:column;align-items:{{ $deCliente ? 'flex-start' : 'flex-end' }};">
                <div style="
                    max-width:75%;padding:10px 14px;
                    border-radius:{{ $deCliente ? '14px 14px 14px 4px' : '14px 14px 4px 14px' }};
                    background:{{ $deCliente ? '#f1f5f9' : 'var(--primary)' }};
                    color:{{ $deCliente ? '#334155' : '#fff' }};
                    font-size:13px;line-height:1.5;">
                    {{ $msg->mensagem }}
                </div>
                <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                    {{ $deCliente ? $clienteSelecionado->nome : ($msg->usuario_nome ?? 'Escritório') }}
                    @if($msg->processo_numero) · Proc. {{ $msg->processo_numero }} @endif
                    · {{ \Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i') }}
                </div>
            </div>
            @empty
            <div style="text-align:center;color:var(--muted);padding:40px;font-size:13px;">
                Nenhuma mensagem nesta conversa.
            </div>
            @endforelse
        </div>

        {{-- Responder --}}
        <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;gap:10px;align-items:flex-end;">
            <textarea wire:model="resposta"
                placeholder="Escreva sua resposta... (Ctrl+Enter para enviar)"
                rows="2"
                style="flex:1;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:none;font-family:inherit;"
                wire:keydown.ctrl.enter="responder"></textarea>
            <button wire:click="responder" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="responder">Enviar →</span>
                <span wire:loading wire:target="responder">…</span>
            </button>
        </div>

        @else
        <div style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:14px;">
            <div style="text-align:center;">
                <div style="margin-bottom:12px;display:flex;justify-content:center;">
                    <svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                Selecione um cliente para ver a conversa.
            </div>
        </div>
        @endif
    </div>

</div>

<script>
    document.addEventListener('livewire:updated', () => {
        const box = document.getElementById('admin-chat-box');
        if (box) box.scrollTop = box.scrollHeight;
    });
</script>

</div>
