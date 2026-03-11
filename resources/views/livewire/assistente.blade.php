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

    <div style="display: flex; flex-direction: column; height: calc(100vh - 140px); max-width: 900px; margin: 0 auto;">

        {{-- Header --}}
        <div class="card" style="margin-bottom: 12px; padding: 16px 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px;">🤖</div>
                <div>
                    <div style="font-weight: 700; font-size: 16px; color: var(--primary);">Assistente Jurídico SAPRO</div>
                    <div style="font-size: 12px; color: var(--success); display: flex; align-items: center; gap: 4px;">
                        <span style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; display: inline-block;"></span>
                        Online — powered by Google Gemini
                    </div>
                </div>
            </div>
        </div>

        {{-- Mensagens --}}
        <div class="card" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px;" id="chat-messages">

            @foreach($mensagens as $msg)
                @if($msg['tipo'] === 'bot')
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <div style="width: 34px; height: 34px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">🤖</div>
                        <div style="max-width: 75%;">
                            <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 0 12px 12px 12px; padding: 12px 16px; font-size: 14px; line-height: 1.6; white-space: pre-wrap;">{{ $msg['texto'] }}</div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px; padding-left: 4px;">{{ $msg['hora'] }}</div>
                        </div>
                    </div>
                @else
                    <div style="display: flex; align-items: flex-start; gap: 10px; flex-direction: row-reverse;">
                        <div style="width: 34px; height: 34px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">👤</div>
                        <div style="max-width: 75%;">
                            <div style="background: var(--primary); color: #fff; border-radius: 12px 0 12px 12px; padding: 12px 16px; font-size: 14px; line-height: 1.6;">{{ $msg['texto'] }}</div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px; text-align: right; padding-right: 4px;">{{ $msg['hora'] }}</div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if($carregando)
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <div style="width: 34px; height: 34px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">🤖</div>
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
        <div style="padding: 10px 0; display: flex; gap: 8px; flex-wrap: wrap;">
            <button wire:click="$set('pergunta', 'Quais processos vencem prazo essa semana?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">📅 Prazos da semana</button>
            <button wire:click="$set('pergunta', 'Tem alguma audiência hoje ou amanhã?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">⚖️ Audiências</button>
            <button wire:click="$set('pergunta', 'Quantos processos ativos temos?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">📋 Processos ativos</button>
            <button wire:click="$set('pergunta', 'Quais clientes têm mais processos?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">👥 Clientes</button>
            <button wire:click="$set('pergunta', 'Quais processos são de alto risco?')" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">⚠️ Alto risco</button>
        </div>

        {{-- Input --}}
        <div class="card" style="padding: 16px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <input
                    wire:model="pergunta"
                    wire:keydown.enter="enviar"
                    id="input-pergunta"
                    type="text"
                    placeholder="Pergunte por texto ou use o microfone 🎤"
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
                >🎤</button>

                {{-- Botão Enviar --}}
                <button
                    wire:click="enviar"
                    class="btn btn-primary"
                    style="border-radius: 50%; width: 46px; height: 46px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"
                    @if($carregando) disabled @endif
                >➤</button>
            </div>

            <div id="mic-status" style="font-size: 12px; color: var(--danger); margin-top: 6px; text-align: center; display: none;">
                🔴 Gravando... fale sua pergunta e pare de falar para enviar
            </div>

            <div style="font-size: 11px; color: var(--muted); margin-top: 8px; text-align: center;">
                Os dados são consultados em tempo real do banco de dados do SAPRO
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
            document.getElementById('btn-mic').innerText = '⏹️';
            document.getElementById('mic-status').style.display = 'block';
        };

        recognition.onresult = (event) => {
            let transcript = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                transcript += event.results[i][0].transcript;
            }
            // Atualiza o input com o texto reconhecido
            const input = document.getElementById('input-pergunta');
            input.value = transcript;
            // Sincroniza com Livewire
            input.dispatchEvent(new Event('input'));
        };

        recognition.onend = () => {
            gravando = false;
            document.getElementById('btn-mic').classList.remove('gravando');
            document.getElementById('btn-mic').innerText = '🎤';
            document.getElementById('mic-status').style.display = 'none';

            // Envia automaticamente se tiver texto
            const input = document.getElementById('input-pergunta');
            if (input.value.trim()) {
                setTimeout(() => {
                    Livewire.dispatch('enviarPergunta');
                }, 300);
            }
        };

        recognition.onerror = (event) => {
            gravando = false;
            document.getElementById('btn-mic').classList.remove('gravando');
            document.getElementById('btn-mic').innerText = '🎤';
            document.getElementById('mic-status').style.display = 'none';
            if (event.error !== 'aborted') {
                alert('Erro no microfone: ' + event.error);
            }
        };

        recognition.start();
    }
    </script>
</div>