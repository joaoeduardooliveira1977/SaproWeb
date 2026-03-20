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

    {{-- ── Card de Saudação + Ações Rápidas ── --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:20px 24px;margin-bottom:20px;">
        <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;">
            {{-- Avatar IA --}}
            <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                </svg>
            </div>
            <div style="flex:1;">
                @php
                    $hora = now()->hour;
                    $saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');
                    $nomeUsuario = Auth::user()?->nome ?? 'Doutor(a)';
                @endphp
                <div style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:4px;">
                    {{ $saudacao }}, {{ $nomeUsuario }}!
                </div>
                <div style="font-size:13px;color:var(--muted);line-height:1.6;">
                    @if($stats['prazos_vencidos'] > 0 || $stats['prazos_7dias'] > 0)
                        Você tem <strong style="color:#dc2626;">{{ $stats['prazos_vencidos'] }} prazo(s) vencido(s)</strong>
                        @if($stats['prazos_7dias'] > 0) e <strong style="color:#d97706;">{{ $stats['prazos_7dias'] }} prazo(s) nos próximos 7 dias</strong>@endif.
                        Atenção aos prazos!
                    @elseif($stats['processos_parados'] > 0)
                        Você tem <strong style="color:#d97706;">{{ $stats['processos_parados'] }} processo(s) parado(s)</strong> há mais de 30 dias.
                    @else
                        Tudo em ordem! Nenhum prazo urgente ou processo parado hoje.
                    @endif
                </div>
            </div>
            {{-- Briefing IA --}}
            <div style="flex-shrink:0;">
                @livewire('resumo-ia')
            </div>
        </div>

        {{-- Botões de Ação Rápida --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
            <a href="{{ route('processos.novo') }}"
                style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 16px;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;transition:opacity .15s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Processo
            </a>
            <a href="{{ route('minutas') }}"
                style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 16px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;transition:opacity .15s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Gerar Minuta IA
            </a>
            <a href="{{ route('pessoas') }}"
                style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 16px;background:linear-gradient(135deg,#059669,#16a34a);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;transition:opacity .15s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Cadastrar Cliente
            </a>
            <a href="{{ route('prazos') }}"
                style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 16px;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;transition:opacity .15s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Ver Prazos
            </a>
        </div>
    </div>

    {{-- ── KPIs com fundo colorido ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
        @php
        $kpis = [
            [
                'label' => 'Processos Ativos',
                'val'   => $stats['processos_ativos'],
                'bg'    => 'linear-gradient(135deg,#1d4ed8,#2563a8)',
                'route' => route('processos'),
                'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
            ],
            [
                'label' => 'Prazos (7 dias)',
                'val'   => $stats['prazos_7dias'],
                'bg'    => 'linear-gradient(135deg,#7c3aed,#6d28d9)',
                'route' => route('prazos'),
                'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            ],
            [
                'label' => 'A Receber',
                'val'   => 'R$ '.number_format($stats['recebimentos_pendentes'],2,',','.'),
                'bg'    => 'linear-gradient(135deg,#059669,#16a34a)',
                'route' => route('financeiro'),
                'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
            ],
            [
                'label' => 'Alertas Pendentes',
                'val'   => $stats['prazos_vencidos'] + $stats['processos_parados'],
                'bg'    => 'linear-gradient(135deg,#dc2626,#b91c1c)',
                'route' => route('prazos'),
                'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            ],
        ];
        @endphp
        @foreach($kpis as $k)
        <a href="{{ $k['route'] }}" style="text-decoration:none;display:block;">
            <div style="background:{{ $k['bg'] }};border-radius:14px;padding:22px 20px;color:#fff;cursor:pointer;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 15px rgba(0,0,0,.12);"
                onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,.2)'"
                onmouseout="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(0,0,0,.12)'">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                    {!! $k['svg'] !!}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.45)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
                <div style="font-size:30px;font-weight:800;margin-bottom:4px;letter-spacing:-1px;line-height:1;">{{ $k['val'] }}</div>
                <div style="font-size:13px;color:rgba(255,255,255,.8);font-weight:500;">{{ $k['label'] }}</div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ── Linha: Prazos Urgentes + Últimas Atividades ── --}}
   

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px;align-items:stretch;">

        {{-- Prazos Próximos --}}
        <div class="card" style="height:100%;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Prazos — próximos 15 dias
                </span>
                <a href="{{ route('prazos') }}" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
            @php
            $urgenciaCor = [
                'urgente' => ['bg'=>'#fef2f2','border'=>'#fca5a5','text'=>'#dc2626'],
                'vencido' => ['bg'=>'#fff7ed','border'=>'#fdba74','text'=>'#c2410c'],
                'atencao' => ['bg'=>'#fffbeb','border'=>'#fde68a','text'=>'#d97706'],
                'alerta'  => ['bg'=>'#f0f9ff','border'=>'#93c5fd','text'=>'#2563a8'],
                'normal'  => ['bg'=>'#f8fafc','border'=>'#e2e8f0','text'=>'#64748b'],
            ];
            @endphp
            @if(count($prazosProximos))
            <div style="display:flex;flex-direction:column;gap:6px;max-height:280px;overflow-y:auto;">
                @foreach($prazosProximos as $pz)
                @php $u = $urgenciaCor[$pz['urgencia']] ?? $urgenciaCor['normal']; @endphp
                <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:7px;background:{{ $u['bg'] }};border:1px solid {{ $u['border'] }}">
                    <div style="min-width:36px;text-align:center;font-weight:700;font-size:13px;color:{{ $u['text'] }}">{{ $pz['data'] }}</div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            @if($pz['fatal'])
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:3px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            @endif
                            {{ $pz['titulo'] }}
                        </div>
                        @if($pz['processo'])<div style="font-size:11px;color:#64748b">{{ $pz['processo'] }}</div>@endif
                    </div>
                    <div style="font-size:11px;font-weight:700;color:{{ $u['text'] }};white-space:nowrap">
                        @if($pz['dias'] < 0){{ abs($pz['dias']) }}d atraso
                        @elseif($pz['dias'] === 0)Hoje
                        @else{{ $pz['dias'] }}d
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;flex:1;padding:20px 0;gap:6px;">
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="color:#64748b;font-size:13px;">Nenhum prazo nos próximos 15 dias</span>
            </div>
            @endif
        </div>

        {{-- Últimas Atividades --}}
        <div class="card" style="height:100%;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Últimas Atividades
                </span>
            </div>
            @if(count($ultimasAtividades))
            <div style="display:flex;flex-direction:column;max-height:280px;overflow-y:auto;">
                @foreach($ultimasAtividades as $at)
                @php
                    $inicial = strtoupper(substr($at['usuario'], 0, 1));
                    $cores   = ['#2563a8','#16a34a','#d97706','#7c3aed','#dc2626'];
                    $cor     = $cores[ord($inicial) % count($cores)];
                @endphp
                <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--border);">
                    <div style="width:30px;height:30px;border-radius:50%;background:{{ $cor }}22;color:{{ $cor }};font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        {{ $inicial }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;color:var(--text);">
                            <strong>{{ $at['usuario'] }}</strong> em
                            <a href="{{ route('processos.show', $at['processo_id']) }}" style="color:var(--primary);font-weight:600;">{{ $at['numero'] }}</a>
                        </div>
                        <div style="font-size:11px;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $at['descricao'] }}</div>
                        <div style="font-size:10px;color:var(--muted);margin-top:1px;">{{ $at['quando'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-title">Nenhuma atividade recente</div>
            </div>
            @endif
        </div>

        {{-- Mercado Financeiro --}}
        @livewire('mercado-financeiro')

    </div>
    {{-- /prazos + atividades + mercado --}}

    {{-- ── Linha: Agenda + Processos por Fase + Processos Parados ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px;align-items:stretch;">

        {{-- Agenda Hoje --}}
        <div class="card" style="height:100%;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Agenda — {{ \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('D [de] MMMM') }}
                </span>
                <a href="{{ route('agenda') }}" class="btn btn-primary btn-sm">Ver tudo</a>
            </div>
            @if(count($agendaHoje))
            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($agendaHoje as $ev)
                @php $corTipo = match($ev['tipo']) { 'Prazo'=>'#dc2626', 'Audiência'=>'#d97706', default=>'#2563a8' }; @endphp
                <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:7px;background:var(--bg);border:1px solid var(--border);">
                    <div style="font-size:12px;font-weight:700;color:var(--text);min-width:36px;">{{ $ev['hora'] }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            @if($ev['urgente'])<svg width="8" height="8" viewBox="0 0 24 24" fill="#dc2626" stroke="none" style="display:inline;vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/></svg>@endif
                            {{ $ev['titulo'] }}
                        </div>
                        @if($ev['processo'])<div style="font-size:10px;color:var(--muted);">{{ $ev['processo'] }}</div>@endif
                    </div>
                    <span class="badge" style="background:{{ $corTipo }}22;color:{{ $corTipo }};font-size:10px;">{{ $ev['tipo'] }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p style="color:#64748b;font-size:13px;text-align:center;padding:20px 0;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Nenhum compromisso hoje
            </p>
            @endif
        </div>

        {{-- Processos por Fase --}}
        <div class="card" style="height:100%;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                    Processos por Fase
                </span>
                <a href="{{ route('processos') }}" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
            @if(count($processosPorFase))
            @php $maxFase = collect($processosPorFase)->max('total') ?: 1; @endphp
            <div style="display:flex;flex-direction:column;gap:10px;padding:4px 0;">
                @foreach($processosPorFase as $item)
                @php $pct = round(($item['total'] / $maxFase) * 100); @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                        <span style="color:var(--text);font-weight:500;">{{ $item['fase'] }}</span>
                        <span style="color:var(--muted);font-weight:700;">{{ $item['total'] }}</span>
                    </div>
                    <div style="height:8px;background:var(--border);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:var(--primary);border-radius:4px;transition:width .5s;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-title">Nenhum processo cadastrado</div>
            </div>
            @endif
        </div>

        {{-- Processos Parados --}}
        <div class="card" style="height:100%;">
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
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text);">{{ $p['cliente'] }}</td>
                        <td>
                            @if($p['fase'])
                            <span class="badge" style="background:#eff6ff;color:#1d4ed8;">{{ $p['fase'] }}</span>
                            @else
                            <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:700;
                                color:{{ $p['dias'] > 60 ? '#dc2626' : ($p['dias'] > 30 ? '#d97706' : '#64748b') }};">
                                <svg width="8" height="8" viewBox="0 0 24 24" fill="{{ $p['dias'] > 60 ? '#dc2626' : ($p['dias'] > 30 ? '#d97706' : '#64748b') }}" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
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
        {{-- /processos parados --}}

    </div>
    {{-- /agenda + fase + parados --}}

</div>
