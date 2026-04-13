<div>
    <style>
    @keyframes pulse {
        0%, 100% { opacity: 0.3; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1.2); }
    }
    @keyframes mic-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
    }
    .mic-btn {
        border-radius: 50%;
        width: 46px;
        height: 46px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        border: none;
        cursor: pointer;
        background: var(--bg);
        border: 1px solid var(--border);
        transition: all .2s;
    }
    .mic-btn:hover { background: #fee2e2; border-color: var(--danger); }
    .mic-btn.gravando {
        background: var(--danger);
        border-color: var(--danger);
        animation: mic-pulse 1s infinite;
    }
    </style>

    <div style="display: flex; flex-direction: column; height: calc(100dvh - 140px); max-width: 900px; margin: 0 auto;">

        {{-- Header --}}
        <div class="card" style="margin-bottom: 12px; padding: 16px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><svg aria-hidden="true" width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/><line x1="12" y1="3" x2="12" y2="7"/><circle cx="8" cy="15" r="1" fill="white"/><circle cx="16" cy="15" r="1" fill="white"/></svg></div>
                    <div>
                        <div style="font-weight: 700; font-size: 16px; color: var(--primary);">Assistente Jurídico Software Jur�dico</div>
                        <div style="font-size: 12px; color: var(--success); display: flex; align-items: center; gap: 4px;">
                            <span style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; display: inline-block;"></span>
                            Online — powered by Google Gemini
                        </div>
                    </div>
                </div>
                <button wire:click="limpar" class="btn btn-sm"
                        style="background:var(--bg);border:1px solid var(--border);color:var(--muted);font-size:12px;display:inline-flex;align-items:center;gap:5px;"
                        title="Limpar conversa">
                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg> Limpar
                </button>
            </div>
        </div>

        {{-- Mensagens --}}
        <div class="card" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px;" id="chat-messages">

            @foreach($mensagens as $msg)
                @if($msg['tipo'] === 'bot')
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <div style="width: 34px; height: 34px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/><line x1="12" y1="3" x2="12" y2="7"/><circle cx="8" cy="15" r="1" fill="white"/><circle cx="16" cy="15" r="1" fill="white"/></svg></div>
                        <div style="max-width: 75%;">
                            <div class="chat-bot-msg" style="background: var(--bg); border: 1px solid var(--border); border-radius: 0 12px 12px 12px; padding: 12px 16px; font-size: 14px; line-height: 1.6;">{!! nl2br(preg_replace(
                                ['/\*\*(.+?)\*\*/s', '/`([^`]+)`/', '/^- /m'],
                                ['<strong>$1</strong>', '<code style="background:#f1f5f9;padding:1px 5px;border-radius:3px;font-family:monospace;font-size:12px">$1</code>', '• '],
                                e($msg['texto'])
                            )) !!}</div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px; padding-left: 4px;">{{ $msg['hora'] }}</div>
                        </div>
                    </div>
                @else
                    <div style="display: flex; align-items: flex-start; gap: 10px; flex-direction: row-reverse;">
                        <div style="width: 34px; height: 34px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                        <div style="max-width: 75%;">
                            <div style="background: var(--primary); color: #fff; border-radius: 12px 0 12px 12px; padding: 12px 16px; font-size: 14px; line-height: 1.6;">{{ $msg['texto'] }}</div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px; text-align: right; padding-right: 4px;">{{ $msg['hora'] }}</div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if($carregando)
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <div style="width: 34px; height: 34px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/><line x1="12" y1="3" x2="12" y2="7"/><circle cx="8" cy="15" r="1" fill="white"/><circle cx="16" cy="15" r="1" fill="white"/></svg></div>
                    <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 0 12px 12px 12px; padding: 12px 16px;">
                        <div style="display: flex; gap: 4px; align-items: center;">
                            <span style="width: 8px; height: 8px; background: var(--muted); border-radius: 50%; animation: pulse 1s infinite;"></span>
                            <span style="width: 8px; height: 8px; background: var(--muted); border-radius: 50%; animation: pulse 1s infinite 0.2s;"></span>
                            <span style="width: 8px; height: 8px; background: var(--muted); border-radius: 50%; animation: pulse 1s infinite 0.4s;"></span>
                            <span style="font-size: 12px; color: var(--muted); margin-left: 6px;">Consultando dados...</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sugestões --}}
        <div style="padding: 10px 0; display: flex; gap: 6px; flex-wrap: wrap; overflow-x: auto;">
            <button wire:click="$set('pergunta', 'Quais processos vencem prazo essa semana?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Prazos da semana</button>
            <button wire:click="$set('pergunta', 'Tem alguma audiência hoje ou amanhã?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Audiências</button>
            <button wire:click="$set('pergunta', 'Quantos processos ativos temos?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Processos ativos</button>
            <button wire:click="$set('pergunta', 'Quais clientes têm mais processos?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Clientes</button>
            <button wire:click="$set('pergunta', 'Quais processos são de alto risco?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Alto risco</button>
            <button wire:click="$set('pergunta', 'Qual é o resumo financeiro do mês?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Financeiro do mês</button>
            <button wire:click="$set('pergunta', 'Quais clientes estão inadimplentes?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Inadimplência</button>
            <button wire:click="$set('pergunta', 'Quais honorários vencem essa semana?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">Vencimentos</button>
        </div>

        {{-- Input --}}
        <div class="card" style="padding: 16px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <input
                    wire:model="pergunta"
                    wire:keydown.enter="enviar"
                    id="input-pergunta"
                    type="text"
                    placeholder="Pergunte por texto ou use o microfone"
                    style="flex: 1; padding: 12px 16px; border: 1px solid var(--border); border-radius: 24px; font-size: 14px; outline: none; background: var(--bg);"
                    @if($carregando) disabled @endif
                >

                {{-- Botão Microfone --}}
                <button
                    id="btn-mic"
                    class="mic-btn"
                    onclick="toggleMic()"
                    title="Falar por voz"
                    type="button"
                ><svg aria-hidden="true" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg></button>

                {{-- Botão Enviar --}}
                <button
                    wire:click="enviar"
                    class="btn btn-primary"
                    style="border-radius: 50%; width: 46px; height: 46px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"
                    @if($carregando) disabled @endif
                ><svg aria-hidden="true" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></button>
            </div>

            <div id="mic-status" style="font-size: 12px; color: var(--danger); margin-top: 6px; text-align: center; display: none;">
                <span style="display:inline-block;width:8px;height:8px;background:#dc2626;border-radius:50%;"></span> Gravando... fale sua pergunta e pare de falar para enviar
            </div>

            <div style="font-size: 11px; color: var(--muted); margin-top: 8px; text-align: center;">
                Os dados são consultados em tempo real do banco de dados do Software Jur�dico
            </div>
        </div>
    </div>

    <script>
    // Auto scroll
    document.addEventListener('livewire:updated', () => {
        const chat = document.getElementById('chat-messages');
        if (chat) chat.scrollTop = chat.scrollHeight;
    });

    // Scroll inicial
    window.addEventListener('load', () => {
        const chat = document.getElementById('chat-messages');
        if (chat) chat.scrollTop = chat.scrollHeight;
    });

    // Reconhecimento de voz
    let recognition = null;
    let gravando = false;

    function toggleMic() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert('Seu navegador não suporta reconhecimento de voz. Use o Chrome!');
            return;
        }

        if (gravando) {
            recognition.stop();
            return;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'pt-BR';
        recognition.continuous = false;
        recognition.interimResults = true;

        recognition.onstart = () => {
            gravando = true;
            document.getElementById('btn-mic').classList.add('gravando');
            document.getElementById('btn-mic').innerHTML = '<svg aria-hidden="true" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>';
            document.getElementById('mic-status').style.display = 'block';
        };

        recognition.onresult = (event) => {
            let transcript = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                transcript += event.results[i][0].transcript;
            }
            // Atualiza o input visualmente
            document.getElementById('input-pergunta').value = transcript;
        };

        recognition.onend = () => {
            gravando = false;
            document.getElementById('btn-mic').classList.remove('gravando');
            document.getElementById('btn-mic').innerHTML = '<svg aria-hidden="true" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
            document.getElementById('mic-status').style.display = 'none';

            // Envia automaticamente via Livewire
            const input = document.getElementById('input-pergunta');
            if (input.value.trim()) {
                setTimeout(() => {
                    @this.set('pergunta', input.value.trim()).then(() => @this.enviar());
                }, 300);
            }
        };

        recognition.onerror = (event) => {
            gravando = false;
            document.getElementById('btn-mic').classList.remove('gravando');
            document.getElementById('btn-mic').innerHTML = '<svg aria-hidden="true" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
            document.getElementById('mic-status').style.display = 'none';
            if (event.error !== 'aborted') {
                alert('Erro no microfone: ' + event.error);
            }
        };

        recognition.start();
    }
    </script>
</div>
