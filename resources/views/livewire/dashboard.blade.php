<div>
    {{-- ── Stat Cards ── --}}
    <div class="stat-grid">
        @php
        $cards = [
            ['icon'=>'⚖️', 'label'=>'Processos Ativos',       'val'=>$stats['processos_ativos'],                                          'cor'=>'#2563a8'],
            ['icon'=>'📅', 'label'=>'Audiências Hoje',         'val'=>$stats['audiencias_hoje'],                                           'cor'=>'#d97706'],
            ['icon'=>'⏳', 'label'=>'Prazos (próx. 7 dias)',  'val'=>$stats['prazos_7dias'],                                              'cor'=>'#f59e0b'],
            ['icon'=>'🚨', 'label'=>'Prazos Vencidos',         'val'=>$stats['prazos_vencidos'],                                           'cor'=>'#dc2626'],
            ['icon'=>'💰', 'label'=>'A Receber',               'val'=>'R$ '.number_format($stats['recebimentos_pendentes'],2,',','.'),     'cor'=>'#16a34a'],
            ['icon'=>'👤', 'label'=>'Clientes',                'val'=>$stats['clientes'],                                                  'cor'=>'#7c3aed'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="stat-card" style="border-left-color: {{ $c['cor'] }}">
            <div class="stat-icon">{{ $c['icon'] }}</div>
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
                <span class="card-title">📅 Agenda — {{ now()->translatedFormat('d \d\e F') }}</span>
                <a href="{{ route('agenda') }}" class="btn btn-primary btn-sm">Ver tudo</a>
            </div>
            @if(count($agendaHoje))
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Hora</th><th>Evento</th><th>Tipo</th></tr></thead>
                    <tbody>
                        @foreach($agendaHoje as $ev)
                        <tr>
                            <td><strong>{{ $ev['hora'] }}</strong></td>
                            <td>
                                @if($ev['urgente'])<span style="color:#dc2626;margin-right:4px">●</span>@endif
                                {{ $ev['titulo'] }}
                                @if($ev['processo'])<br><small style="color:#64748b">{{ $ev['processo'] }}</small>@endif
                            </td>
                            <td>
                                @php $cor = match($ev['tipo']) { 'Prazo'=>'#dc2626', 'Audiência'=>'#d97706', default=>'#2563a8' }; @endphp
                                <span class="badge" style="background:{{ $cor }}22;color:{{ $cor }}">{{ $ev['tipo'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p style="color:#64748b;font-size:13px;text-align:center;padding:20px 0">Nenhum compromisso hoje 🎉</p>
            @endif
        </div>

        {{-- Prazos Próximos --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">⏳ Prazos — próximos 15 dias</span>
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
                            @if($pz['fatal'])<span title="Prazo fatal" style="color:#dc2626;margin-right:3px">⚠</span>@endif
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
                <p style="color:#64748b;font-size:13px;text-align:center;padding:20px 0">Nenhum prazo nos próximos 15 dias ✅</p>
            @endif
        </div>
    </div>

    {{-- ── Linha 3: Processos + Publicações AASP ── --}}
    <div class="grid-2">

        {{-- Processos Recentes --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚖️ Processos Recentes</span>
                <a href="{{ route('processos') }}" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Processo</th><th>Cliente</th><th>Fase</th><th>Risco</th></tr></thead>
                    <tbody>
                        @foreach($processos as $p)
                        <tr>
                            <td><a href="{{ route('processos.show', $p['id']) }}" class="text-primary">{{ $p['numero'] }}</a></td>
                            <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p['cliente'] }}</td>
                            <td><span class="badge" style="background:#2563a822;color:#2563a8">{{ $p['fase'] ?? '—' }}</span></td>
                            <td><span class="badge" style="background:{{ $p['risco_cor'] }}22;color:{{ $p['risco_cor'] }}">{{ $p['risco'] ?? '—' }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mercado Financeiro --}}
        @livewire('mercado-financeiro')
    </div>
</div>
