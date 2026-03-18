<div>
    {{-- ── Alertas de Procurações ── --}}
    @if($procuracoesVencidas > 0)
    <div style="display:flex;gap:10px;padding:12px 16px;background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;margin-bottom:12px;align-items:center;">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span style="font-size:13px;color:#dc2626;font-weight:600;">{{ $procuracoesVencidas }} procuração(ões) vencida(s) —</span>
        <a href="{{ route('procuracoes') }}" style="font-size:13px;color:#dc2626;text-decoration:underline;">Ver Procurações</a>
    </div>
    @endif
    @if($procuracoesVencendo > 0)
    <div style="display:flex;gap:10px;padding:12px 16px;background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;margin-bottom:12px;align-items:center;">
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span style="font-size:13px;color:#d97706;font-weight:600;">{{ $procuracoesVencendo }} procuração(ões) vencendo em 30 dias —</span>
        <a href="{{ route('procuracoes') }}" style="font-size:13px;color:#d97706;text-decoration:underline;">Renovar</a>
    </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="stat-grid">
        @php
        $cards = [
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                'label' => 'Processos Ativos',
                'val'   => $stats['processos_ativos'],
                'cor'   => '#2563a8',
            ],
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                'label' => 'Audiências Hoje',
                'val'   => $stats['audiencias_hoje'],
                'cor'   => '#d97706',
            ],
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                'label' => 'Prazos (próx. 7 dias)',
                'val'   => $stats['prazos_7dias'],
                'cor'   => '#f59e0b',
            ],
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                'label' => 'Prazos Vencidos',
                'val'   => $stats['prazos_vencidos'],
                'cor'   => '#dc2626',
            ],
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'label' => 'A Receber',
                'val'   => 'R$ '.number_format($stats['recebimentos_pendentes'],2,',','.'),
                'cor'   => '#16a34a',
            ],
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                'label' => 'Clientes',
                'val'   => $stats['clientes'],
                'cor'   => '#7c3aed',
            ],
            [
                'svg'   => '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>',
                'label' => 'Parados (30d)',
                'val'   => $stats['processos_parados'],
                'cor'   => $stats['processos_parados'] > 0 ? '#dc2626' : '#16a34a',
            ],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="stat-card" style="border-left-color: {{ $c['cor'] }}">
            <div class="stat-icon" style="display:flex;justify-content:center;">{!! $c['svg'] !!}</div>
            <div class="stat-val" style="color:{{ $c['cor'] }}">{{ $c['val'] }}</div>
            <div class="stat-label">{{ $c['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Linha 2: Agenda + Prazos próximos ── --}}
    <div class="grid-2 mb-4">

        {{-- Agenda Hoje --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Agenda — {{ now()->translatedFormat('d \d\e F') }}
                </span>
                <a href="{{ route('agenda') }}" class="btn btn-primary btn-sm">Ver tudo</a>
            </div>
            @if(count($agendaHoje))
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Hora</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Evento</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendaHoje as $ev)
                        <tr>
                            <td style="padding:10px 16px;"><strong>{{ $ev['hora'] }}</strong></td>
                            <td style="padding:10px 16px;">
                                @if($ev['urgente'])
                                    <svg aria-hidden="true" width="8" height="8" viewBox="0 0 24 24" fill="#dc2626" stroke="none" style="display:inline;vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10"/></svg>
                                @endif
                                {{ $ev['titulo'] }}
                                @if($ev['processo'])<br><small style="color:#64748b">{{ $ev['processo'] }}</small>@endif
                            </td>
                            <td style="padding:10px 16px;">
                                @php $cor = match($ev['tipo']) { 'Prazo'=>'#dc2626', 'Audiência'=>'#d97706', default=>'#2563a8' }; @endphp
                                <span class="badge" style="background:{{ $cor }}22;color:{{ $cor }}">{{ $ev['tipo'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p style="color:#64748b;font-size:13px;text-align:center;padding:20px 0;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Nenhum compromisso hoje
                </p>
            @endif
        </div>

        {{-- Prazos Próximos --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Prazos — próximos 15 dias
                </span>
                <a href="{{ route('prazos') }}" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
            @php
            $urgenciaCor = [
                'urgente' => ['bg'=>'#fef2f2','border'=>'#fca5a5','text'=>'#dc2626','label'=>'URGENTE'],
                'vencido' => ['bg'=>'#fff7ed','border'=>'#fdba74','text'=>'#c2410c','label'=>'VENCIDO'],
                'atencao' => ['bg'=>'#fffbeb','border'=>'#fde68a','text'=>'#d97706','label'=>'ATENÇÃO'],
                'alerta'  => ['bg'=>'#f0f9ff','border'=>'#93c5fd','text'=>'#2563a8','label'=>'ALERTA'],
                'normal'  => ['bg'=>'#f8fafc','border'=>'#e2e8f0','text'=>'#64748b','label'=>''],
            ];
            @endphp
            @if(count($prazosProximos))
            <div style="display:flex;flex-direction:column;gap:6px">
                @foreach($prazosProximos as $pz)
                @php $u = $urgenciaCor[$pz['urgencia']] ?? $urgenciaCor['normal']; @endphp
                <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:7px;background:{{ $u['bg'] }};border:1px solid {{ $u['border'] }}">
                    <div style="min-width:36px;text-align:center;font-weight:700;font-size:13px;color:{{ $u['text'] }}">{{ $pz['data'] }}</div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            @if($pz['fatal'])
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:3px;" title="Prazo fatal"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            @endif
                            {{ $pz['titulo'] }}
                        </div>
                        @if($pz['processo'])<div style="font-size:11px;color:#64748b">{{ $pz['processo'] }}</div>@endif
                    </div>
                    <div style="font-size:11px;font-weight:700;color:{{ $u['text'] }};white-space:nowrap">
                        @if($pz['dias'] < 0)
                            {{ abs($pz['dias']) }}d atraso
                        @elseif($pz['dias'] === 0)
                            Hoje
                        @else
                            {{ $pz['dias'] }}d
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
                <p style="color:#64748b;font-size:13px;text-align:center;padding:20px 0;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Nenhum prazo nos próximos 15 dias
                </p>
            @endif
        </div>
    </div>

    {{-- ── Linha 3: Processos + Publicações AASP ── --}}
    <div class="grid-2">

        {{-- Processos Parados --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="{{ $stats['processos_parados'] > 0 ? '#dc2626' : 'currentColor' }}" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
                    Processos Parados
                    <span style="font-size:11px;font-weight:400;color:var(--muted);">sem andamento há 30+ dias</span>
                </span>
                <a href="{{ route('processos') }}" class="btn btn-secondary btn-sm">Ver todos</a>
            </div>
            @if(count($processosParados))
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Processo</th>
                            <th>Cliente</th>
                            <th>Fase</th>
                            <th style="text-align:right;">Parado há</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processosParados as $p)
                        <tr>
                            <td><a href="{{ route('processos.show', $p['id']) }}" class="text-primary" style="font-weight:600;">{{ $p['numero'] }}</a></td>
                            <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text);">{{ $p['cliente'] }}</td>
                            <td>
                                @if($p['fase'])
                                <span class="badge" style="background:#eff6ff;color:#1d4ed8;">{{ $p['fase'] }}</span>
                                @else
                                <span style="color:var(--muted)">—</span>
                                @endif
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <span style="font-size:12px;font-weight:700;color:{{ $p['dias'] > 60 ? '#dc2626' : ($p['dias'] > 30 ? '#d97706' : '#64748b') }};">
                                    {{ $p['dias'] }}d
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon"><svg aria-hidden="true" width="32" height="32" fill="none" stroke="var(--success)" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
                <div class="empty-state-title">Tudo em andamento!</div>
                <div class="empty-state-sub">Nenhum processo ativo está parado há mais de 30 dias.</div>
            </div>
            @endif
        </div>

        {{-- Mercado Financeiro --}}
        @livewire('mercado-financeiro')
    </div>
</div>
