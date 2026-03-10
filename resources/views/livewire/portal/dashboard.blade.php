{{-- Navbar --}}
<div>
<nav class="navbar">
    <div class="navbar-brand">
        <span>⚖️</span>
        <div>
            <div>SAPRO</div>
            <div class="navbar-sub">PORTAL DO CLIENTE</div>
        </div>
    </div>
    <div class="navbar-user">
        <span style="font-size:14px;">👤 {{ $pessoa?->nome }}</span>
        <button wire:click="sair" class="btn-sair">Sair</button>
    </div>
</nav>

<div class="container">

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ count($processos) }}</div>
            <div class="stat-label">⚖️ Processos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ count(array_filter($processos, fn($p) => $p['status'] === 'Ativo')) }}</div>
            <div class="stat-label">🟢 Ativos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ count($agenda) }}</div>
            <div class="stat-label">📅 Próximos compromissos</div>
        </div>
    </div>

    <div class="grid-2">

        {{-- Processos --}}
        <div class="card" style="grid-column: 1 / -1;">
            <div class="card-header">⚖️ Meus Processos</div>
            @if(count($processos) > 0)
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Parte Contrária</th>
                            <th>Fase</th>
                            <th>Risco</th>
                            <th>Advogado</th>
                            <th>Status</th>
                            <th>Andamentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processos as $p)
                        <tr>
                            <td style="font-weight:600; color:#1a3a5c;">{{ $p['numero'] }}</td>
                            <td>{{ $p['parte_contraria'] }}</td>
                            <td>{{ $p['fase'] }}</td>
                            <td>
                                <span class="risco-dot" style="background:{{ $p['risco_cor'] }}"></span>
                                {{ $p['risco'] }}
                            </td>
                            <td>{{ $p['advogado'] }}</td>
                            <td>
                                <span class="badge {{ $p['status'] === 'Ativo' ? 'badge-ativo' : 'badge-encerrado' }}">
                                    {{ $p['status'] }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="verAndamentos({{ $p['id'] }})" class="btn btn-outline" style="font-size:12px; padding:5px 12px;">
                                    Ver
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty">
                <div class="empty-icon">⚖️</div>
                <p>Nenhum processo encontrado.</p>
            </div>
            @endif
        </div>

        {{-- Agenda --}}
        <div class="card">
            <div class="card-header">📅 Próximos Compromissos</div>
            <div class="card-body" style="padding-top:8px;">
                @if(count($agenda) > 0)
                    @foreach($agenda as $a)
                    <div class="agenda-item">
                        <div class="agenda-hora">{{ $a['data_hora'] }}</div>
                        <div class="agenda-info">
                            <div class="agenda-titulo">
                                {{ $a['titulo'] }}
                                @if($a['urgente'])
                                    <span class="urgente-badge">URGENTE</span>
                                @endif
                            </div>
                            <div class="agenda-meta">
                                {{ $a['tipo'] }}
                                @if($a['local']) · {{ $a['local'] }} @endif
                                @if($a['processo']) · Proc. {{ $a['processo'] }} @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="empty">
                    <div class="empty-icon">🗓️</div>
                    <p>Nenhum compromisso próximo.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Informações do escritório --}}
        <div class="card">
            <div class="card-header">📋 Informações</div>
            <div class="card-body">
                <div style="font-size:13px; color:#64748b; line-height:1.8;">
                    <p><strong style="color:#334155;">Nome:</strong> {{ $pessoa?->nome }}</p>
                    <p><strong style="color:#334155;">CPF/CNPJ:</strong> {{ $pessoa?->cpf_cnpj ?? '—' }}</p>
                    <p><strong style="color:#334155;">E-mail:</strong> {{ $pessoa?->email ?? '—' }}</p>
                    <p><strong style="color:#334155;">Telefone:</strong> {{ $pessoa?->telefone ?? $pessoa?->celular ?? '—' }}</p>
                    <p><strong style="color:#334155;">Último acesso:</strong> {{ $pessoa?->portal_ultimo_acesso?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div style="margin-top:20px; padding:12px 16px; background:#f1f5f9; border-radius:8px; font-size:12px; color:#64748b;">
                    💡 Para dúvidas, entre em contato com o escritório.
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal de Andamentos --}}
@if($processoSelecionado)
<div class="modal-overlay" wire:click.self="fecharAndamentos">
    <div class="modal">
        <div class="modal-header">
            <h3>📋 Andamentos do Processo</h3>
            <button wire:click="fecharAndamentos" class="btn-close">×</button>
        </div>
        <div class="modal-body">
            @if(count($andamentos) > 0)
            <div class="timeline">
                @foreach($andamentos as $a)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">{{ $a['data'] }}</div>
                    <div class="timeline-text">{{ $a['descricao'] }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty">
                <div class="empty-icon">📋</div>
                <p>Nenhum andamento registrado.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
</div>