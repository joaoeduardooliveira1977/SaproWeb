<div>
    {{-- Stats --}}
    <div class="grid-3 mb-4">
        @php
        $cards = [
            ['icon'=>'⚖️', 'label'=>'Processos Ativos',    'val'=>$stats['processos_ativos'],                           'cor'=>'#2563a8'],
            ['icon'=>'📅', 'label'=>'Audiências Hoje',      'val'=>$stats['audiencias_hoje'],                             'cor'=>'#d97706'],
            ['icon'=>'⏰', 'label'=>'Prazos (7 dias)',      'val'=>$stats['prazos_vencendo'],                             'cor'=>'#dc2626'],
            ['icon'=>'👤', 'label'=>'Clientes',              'val'=>$stats['clientes'],                                    'cor'=>'#16a34a'],
            ['icon'=>'💰', 'label'=>'Custas Pendentes',     'val'=>'R$ '.number_format($stats['custas_pendentes'],2,',','.'), 'cor'=>'#e8a020'],
            ['icon'=>'📁', 'label'=>'Proc. Encerrados',     'val'=>$stats['processos_encerrados'],                        'cor'=>'#64748b'],
        ];
        @endphp

        @foreach($cards as $c)
        <div class="stat-card" style="border-left-color: {{ $c['cor'] }}">
            <div class="stat-icon">{{ $c['icon'] }}</div>
            <div class="stat-val">{{ $c['val'] }}</div>
            <div class="stat-label">{{ $c['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Agenda + Processos --}}
    <div class="grid-2">
        {{-- Agenda Hoje --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Agenda de Hoje — {{ now()->format('d/m/Y') }}</span>
                <a href="{{ route('agenda') }}" class="btn btn-primary btn-sm">Ver tudo</a>
            </div>
            @if(count($agendaHoje))
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Hora</th><th>Evento</th><th>Tipo</th></tr>
                    </thead>
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
                                @php
                                $cor = match($ev['tipo']) { 'Prazo'=>'#dc2626', 'Audiência'=>'#d97706', default=>'#2563a8' };
                                @endphp
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

        {{-- Processos Recentes --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚖️ Processos Recentes</span>
                <a href="{{ route('processos') }}" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Processo</th><th>Cliente</th><th>Fase</th><th>Risco</th></tr>
                    </thead>
                    <tbody>
                        @foreach($processos as $p)
                        <tr>
                            <td><a href="{{ route('processos.show', $p['id']) }}" class="text-primary">{{ $p['numero'] }}</a></td>
                            <td>{{ $p['cliente'] }}</td>
                            <td><span class="badge" style="background:#2563a822;color:#2563a8">{{ $p['fase'] ?? '—' }}</span></td>
                            <td><span class="badge" style="background:{{ $p['risco_cor'] }}22;color:{{ $p['risco_cor'] }}">{{ $p['risco'] ?? '—' }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
