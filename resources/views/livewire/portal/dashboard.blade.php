<div>

{{-- ── Navbar ── --}}
<nav class="navbar">
    <div class="navbar-brand">
        <span>⚖️</span>
        <div>
            <div>SAPRO</div>
            <div class="navbar-sub">PORTAL DO CLIENTE</div>
        </div>
    </div>
    <div class="navbar-user">
        <span style="font-size:13px;">👤 {{ $pessoa?->nome }}</span>
        <button wire:click="sair" class="btn-sair">Sair</button>
    </div>
</nav>

{{-- ── Tabs ── --}}
<div class="portal-tabs">
    @foreach([
        'inicio'     => '🏠 Início',
        'processos'  => '⚖️ Processos',
        'documentos' => '📁 Documentos',
        'honorarios' => '💰 Honorários',
        'mensagens'  => '💬 Mensagens',
    ] as $key => $label)
    <button wire:click="trocarAba('{{ $key }}')"
        class="portal-tab {{ $aba === $key ? 'active' : '' }}">
        {{ $label }}
        @if($key === 'mensagens' && $stats['msgs_nao_lidas'] > 0)
            <span class="tab-badge">{{ $stats['msgs_nao_lidas'] }}</span>
        @endif
    </button>
    @endforeach
</div>

<div class="container">

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: INÍCIO                                               --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'inicio')

    <div class="stats-grid">
        <div class="stat-card" style="cursor:pointer;" wire:click="trocarAba('processos')">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">⚖️ Processos</div>
        </div>
        <div class="stat-card" style="cursor:pointer;" wire:click="trocarAba('processos')">
            <div class="stat-value" style="color:#16a34a;">{{ $stats['ativos'] }}</div>
            <div class="stat-label">🟢 Processos ativos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#2563a8;">{{ $stats['agenda'] }}</div>
            <div class="stat-label">📅 Próximos compromissos</div>
        </div>
        <div class="stat-card" style="cursor:pointer;" wire:click="trocarAba('mensagens')">
            <div class="stat-value" style="color:{{ $stats['msgs_nao_lidas'] > 0 ? '#dc2626' : '#334155' }};">
                {{ $stats['msgs_nao_lidas'] }}
            </div>
            <div class="stat-label">💬 Mensagens não lidas</div>
        </div>
    </div>

    {{-- Próximos eventos --}}
    <div class="card">
        <div class="card-header">📅 Próximos Compromissos</div>
        <div class="card-body" style="padding-top:8px;">
            @forelse($proximosEventos as $ev)
            <div class="agenda-item">
                <div class="agenda-hora">{{ $ev->data_hora->format('d/m') }}<br><small>{{ $ev->data_hora->format('H:i') }}</small></div>
                <div class="agenda-info">
                    <div class="agenda-titulo">
                        {{ $ev->titulo }}
                        @if($ev->urgente) <span class="urgente-badge">URGENTE</span> @endif
                    </div>
                    <div class="agenda-meta">
                        {{ $ev->tipo }}
                        @if($ev->local) · {{ $ev->local }} @endif
                        @if($ev->processo) · Proc. {{ $ev->processo->numero }} @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty"><div class="empty-icon">🗓️</div><p>Nenhum compromisso próximo.</p></div>
            @endforelse
        </div>
    </div>

    <div class="card" style="background:#f0f9ff;border:1px solid #bae6fd;">
        <div class="card-body" style="padding:16px 24px;">
            <p style="font-size:13px;color:#0369a1;margin:0;">
                💡 Use as abas acima para acompanhar seus processos, documentos, honorários e enviar mensagens ao escritório.
            </p>
        </div>
    </div>

@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: PROCESSOS                                            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'processos')

    {{-- Detalhe do processo --}}
    @if($processoDetalhe)

        <div style="margin-bottom:16px;">
            <button wire:click="fecharProcesso" class="btn btn-outline" style="font-size:13px;">
                ← Voltar à lista
            </button>
        </div>

        <div class="card">
            <div class="card-header" style="background:#1a3a5c;color:#fff;border-radius:12px 12px 0 0;">
                ⚖️ {{ $processoDetalhe->numero }}
                <span style="font-size:12px;font-weight:400;margin-left:8px;opacity:.8;">
                    {{ $processoDetalhe->status }}
                </span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:20px;">
                    <div><div class="info-label">Parte Contrária</div><div class="info-val">{{ $processoDetalhe->parte_contraria ?: '—' }}</div></div>
                    <div><div class="info-label">Advogado</div><div class="info-val">{{ $processoDetalhe->advogado?->nome ?? '—' }}</div></div>
                    <div><div class="info-label">Fase</div><div class="info-val">{{ $processoDetalhe->fase?->descricao ?? '—' }}</div></div>
                    <div><div class="info-label">Distribuição</div><div class="info-val">{{ $processoDetalhe->data_distribuicao?->format('d/m/Y') ?? '—' }}</div></div>
                    <div><div class="info-label">Valor da Causa</div><div class="info-val">{{ $processoDetalhe->valor_causa ? 'R$ '.number_format($processoDetalhe->valor_causa,2,',','.') : '—' }}</div></div>
                    <div>
                        <div class="info-label">Risco</div>
                        <div class="info-val">
                            @if($processoDetalhe->risco)
                                <span class="risco-dot" style="background:{{ $processoDetalhe->risco->cor_hex ?? '#ccc' }}"></span>
                                {{ $processoDetalhe->risco->descricao }}
                            @else —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Prazos abertos --}}
        @if($prazosProcesso->isNotEmpty())
        <div class="card">
            <div class="card-header">⏳ Prazos em Aberto</div>
            <div class="card-body" style="padding-top:8px;">
                @foreach($prazosProcesso as $prazo)
                @php
                    $dias = (int) now()->startOfDay()->diffInDays($prazo->data_prazo, false);
                    $cor  = $dias < 0 ? '#dc2626' : ($dias <= 5 ? '#ea580c' : ($dias <= 15 ? '#ca8a04' : '#16a34a'));
                @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;font-weight:700;color:{{ $cor }};min-width:90px;">
                        {{ $prazo->data_prazo->format('d/m/Y') }}
                    </span>
                    <div>
                        <div style="font-size:13px;font-weight:600;">{{ $prazo->titulo }}</div>
                        <div style="font-size:11px;color:#64748b;">
                            {{ $dias >= 0 ? $dias.' dia(s) restante(s)' : abs($dias).' dia(s) em atraso' }}
                            @if($prazo->prazo_fatal) · <span style="color:#9d174d;font-weight:700;">⚠ Fatal</span> @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Linha do tempo --}}
        <div class="card">
            <div class="card-header">📋 Histórico de Andamentos</div>
            <div class="card-body">
                @if($andamentos->isEmpty())
                    <div class="empty"><div class="empty-icon">📋</div><p>Nenhum andamento registrado.</p></div>
                @else
                <div class="timeline">
                    @foreach($andamentos as $a)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">{{ $a->data->format('d/m/Y') }}</div>
                        <div class="timeline-text">{{ $a->descricao }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    @else

        {{-- Lista de processos --}}
        @foreach($processos as $proc)
        @php
            $totalAndamentos = \App\Models\Andamento::where('processo_id', $proc->id)->count();
            $ultimoAndamento = \App\Models\Andamento::where('processo_id', $proc->id)->latest('data')->first();
        @endphp
        <div class="processo-card" wire:click="abrirProcesso({{ $proc->id }})">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#1a3a5c;margin-bottom:4px;">
                        {{ $proc->numero }}
                    </div>
                    <div style="font-size:13px;color:#64748b;">
                        {{ $proc->parte_contraria ?: '—' }}
                    </div>
                </div>
                <span class="badge {{ $proc->status === 'Ativo' ? 'badge-ativo' : 'badge-encerrado' }}">
                    {{ $proc->status }}
                </span>
            </div>
            <div style="display:flex;gap:20px;margin-top:10px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#64748b;">
                    <strong>Fase:</strong> {{ $proc->fase?->descricao ?? '—' }}
                </span>
                <span style="font-size:12px;color:#64748b;">
                    <strong>Advogado:</strong> {{ $proc->advogado?->nome ?? '—' }}
                </span>
                @if($ultimoAndamento)
                <span style="font-size:12px;color:#64748b;">
                    <strong>Última atualização:</strong> {{ $ultimoAndamento->data->format('d/m/Y') }}
                </span>
                @endif
                <span style="font-size:12px;color:#2563a8;font-weight:600;">
                    {{ $totalAndamentos }} andamento(s) →
                </span>
            </div>
        </div>
        @endforeach

        @if($processos->isEmpty())
        <div class="empty"><div class="empty-icon">⚖️</div><p>Nenhum processo encontrado.</p></div>
        @endif

    @endif
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: DOCUMENTOS                                           --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'documentos')

    @if($documentos->isEmpty())
        <div class="card">
            <div class="empty" style="padding:60px;">
                <div class="empty-icon">📁</div>
                <p>Nenhum documento disponível no portal.</p>
                <p style="font-size:12px;margin-top:8px;">O escritório disponibilizará documentos assim que estiverem prontos.</p>
            </div>
        </div>
    @else
    <div class="card">
        <div class="card-header">📁 Documentos Disponíveis</div>
        @foreach($documentos as $doc)
        @php
            $icone = match($doc->tipo) {
                'peticao'          => '📄',
                'contrato'         => '📑',
                'sentenca'         => '⚖️',
                'documento_cliente'=> '🪪',
                default            => '📎',
            };
            $tamanhoFmt = $doc->tamanho
                ? ($doc->tamanho > 1048576
                    ? number_format($doc->tamanho/1048576,1).' MB'
                    : number_format($doc->tamanho/1024,0).' KB')
                : '';
        @endphp
        <div style="display:flex;align-items:center;gap:14px;padding:14px 24px;border-bottom:1px solid #f1f5f9;">
            <span style="font-size:24px;">{{ $icone }}</span>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:#1a3a5c;">{{ $doc->titulo }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    {{ ucfirst(str_replace('_',' ',$doc->tipo)) }}
                    @if($doc->processo_numero) · Proc. {{ $doc->processo_numero }} @endif
                    @if($doc->data_documento) · {{ \Carbon\Carbon::parse($doc->data_documento)->format('d/m/Y') }} @endif
                    @if($tamanhoFmt) · {{ $tamanhoFmt }} @endif
                </div>
                @if($doc->descricao)
                    <div style="font-size:12px;color:#94a3b8;margin-top:2px;">{{ $doc->descricao }}</div>
                @endif
            </div>
            @if($doc->arquivo)
            <a href="{{ Storage::url($doc->arquivo) }}" target="_blank"
               style="background:#1a3a5c;color:#fff;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;">
                ⬇ Baixar
            </a>
            @endif
        </div>
        @endforeach
    </div>
    @endif

@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: HONORÁRIOS                                           --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'honorarios')

    @php
        $totalPago     = $honorarios->where('status','pago')->sum('valor');
        $totalPendente = $honorarios->whereIn('status',['pendente','atrasado'])->sum('valor');
        $totalAtrasado = $honorarios->where('status','atrasado')->sum('valor');
    @endphp

    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-value" style="color:#16a34a;">R$ {{ number_format($totalPago,2,',','.') }}</div>
            <div class="stat-label">✅ Total pago</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#ca8a04;">R$ {{ number_format($totalPendente,2,',','.') }}</div>
            <div class="stat-label">⏳ Pendente</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#dc2626;">R$ {{ number_format($totalAtrasado,2,',','.') }}</div>
            <div class="stat-label">⚠️ Em atraso</div>
        </div>
    </div>

    @if($honorarios->isEmpty())
        <div class="card">
            <div class="empty" style="padding:60px;">
                <div class="empty-icon">💰</div>
                <p>Nenhum contrato de honorários registrado.</p>
            </div>
        </div>
    @else
    <div class="card">
        <div class="card-header">💰 Parcelas de Honorários</div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Parcela</th>
                        <th>Contrato</th>
                        <th>Processo</th>
                        <th>Vencimento</th>
                        <th style="text-align:right;">Valor</th>
                        <th>Status</th>
                        <th>Pgto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($honorarios as $parc)
                    @php
                        $statusStyle = match($parc->status) {
                            'pago'     => 'background:#dcfce7;color:#16a34a;',
                            'atrasado' => 'background:#fee2e2;color:#991b1b;',
                            default    => 'background:#fef9c3;color:#854d0e;',
                        };
                        $vencido = $parc->status !== 'pago'
                            && \Carbon\Carbon::parse($parc->vencimento)->isPast();
                    @endphp
                    <tr>
                        <td style="text-align:center;font-weight:600;">{{ $parc->numero_parcela }}ª</td>
                        <td style="font-size:12px;">{{ $parc->contrato ?? '—' }}</td>
                        <td style="font-size:12px;font-family:monospace;">{{ $parc->processo_numero ?? '—' }}</td>
                        <td style="{{ $vencido ? 'color:#dc2626;font-weight:600;' : '' }}">
                            {{ \Carbon\Carbon::parse($parc->vencimento)->format('d/m/Y') }}
                        </td>
                        <td style="text-align:right;font-weight:600;">R$ {{ number_format($parc->valor,2,',','.') }}</td>
                        <td>
                            <span class="badge" style="{{ $statusStyle }}">
                                {{ ucfirst($parc->status) }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $parc->data_pagamento ? \Carbon\Carbon::parse($parc->data_pagamento)->format('d/m/Y') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: MENSAGENS                                            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'mensagens')

<div class="card" style="max-width:720px;margin:0 auto;">
    <div class="card-header">💬 Mensagens com o Escritório</div>

    {{-- Chat --}}
    <div style="padding:16px 24px;max-height:480px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;"
         id="chat-box">
        @forelse($mensagens as $msg)
        @php $deCliente = $msg->de === 'cliente'; @endphp
        <div style="display:flex;flex-direction:column;align-items:{{ $deCliente ? 'flex-end' : 'flex-start' }};">
            <div style="
                max-width:75%;
                padding:10px 14px;
                border-radius:{{ $deCliente ? '14px 14px 4px 14px' : '14px 14px 14px 4px' }};
                background:{{ $deCliente ? '#1a3a5c' : '#f1f5f9' }};
                color:{{ $deCliente ? '#fff' : '#334155' }};
                font-size:13px;line-height:1.5;">
                {{ $msg->mensagem }}
            </div>
            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                @if(!$deCliente && $msg->usuario_nome)
                    {{ $msg->usuario_nome }} ·
                @endif
                {{ \Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>
        @empty
        <div class="empty" style="padding:40px;">
            <div class="empty-icon">💬</div>
            <p>Nenhuma mensagem ainda. Envie uma mensagem para o escritório.</p>
        </div>
        @endforelse
    </div>

    {{-- Formulário --}}
    <div style="padding:16px 24px;border-top:1px solid #e2e8f0;">
        <div style="margin-bottom:10px;">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">
                Processo (opcional)
            </label>
            <select wire:model="msgProcessoId"
                style="width:100%;padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:4px;">
                <option value="">— Mensagem geral —</option>
                @foreach($processosFiltro as $p)
                    <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->parte_contraria ?: 'Sem parte contrária' }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <textarea wire:model="novaMensagem"
                placeholder="Digite sua mensagem..."
                rows="3"
                style="flex:1;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;resize:none;font-family:inherit;"
                wire:keydown.ctrl.enter="enviarMensagem"></textarea>
            <button wire:click="enviarMensagem"
                style="background:#1a3a5c;color:#fff;border:none;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="enviarMensagem">Enviar →</span>
                <span wire:loading wire:target="enviarMensagem">…</span>
            </button>
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:6px;">Ctrl+Enter para enviar</p>
    </div>
</div>

<script>
    document.addEventListener('livewire:updated', () => {
        const box = document.getElementById('chat-box');
        if (box) box.scrollTop = box.scrollHeight;
    });
</script>

@endif

</div>{{-- /container --}}
</div>
