<div style="padding:22px 24px;background:#f0f4f8;min-height:100%;">

{{-- ── Barra de urgência ─────────────────────────────────────────── --}}
@if($prazosVencidos > 0)
<div style="display:flex;align-items:center;gap:12px;padding:12px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;margin-bottom:16px;">
    <svg width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span style="font-size:13px;font-weight:700;color:#dc2626;">
        {{ $prazosVencidos }} prazo{{ $prazosVencidos > 1 ? 's' : '' }} vencido{{ $prazosVencidos > 1 ? 's' : '' }} — ação imediata necessária!
    </span>
    <a href="{{ route('sla') }}" style="margin-left:auto;padding:5px 14px;background:#dc2626;color:#fff;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">
        Ver Painel de Alertas →
    </a>
</div>
@elseif($prazosHoje > 0)
<div style="display:flex;align-items:center;gap:12px;padding:12px 18px;background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;margin-bottom:16px;">
    <svg width="18" height="18" fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    <span style="font-size:13px;font-weight:700;color:#ea580c;">
        {{ $prazosHoje }} prazo{{ $prazosHoje > 1 ? 's' : '' }} vencem hoje — fique atento!
    </span>
    <a href="{{ route('sla') }}" style="margin-left:auto;padding:5px 14px;background:#ea580c;color:#fff;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">
        Ver Painel de Alertas →
    </a>
</div>
@endif

{{-- ── Layout 2 colunas ─────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 272px;gap:18px;align-items:start;" class="dash-layout">

    {{-- ══════════════════════════════════════════ --}}
    {{-- COLUNA ESQUERDA                            --}}
    {{-- ══════════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:18px;">

        {{-- 1. Cabeçalho + Atalhos rápidos --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
            <div>
                <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;margin:0;">
                    Bom dia, {{ auth('usuarios')->user()->nome ?? 'usuário' }} 👋
                </h2>
                <p style="font-size:13px;color:#64748b;margin-top:4px;">
                    {{ now()->locale('pt_BR')->isoFormat('dddd, D [de] MMMM') }}
                    &nbsp;·&nbsp; {{ $totalProcessos }} processos ativos
                </p>
            </div>
            {{-- Atalhos rápidos --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('processos.novo') }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#1D9E75;color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Processo
                </a>
                <a href="{{ route('prazos') }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;border:1.5px solid #e2e8f0;color:#475569;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Prazo
                </a>
                <a href="{{ route('agenda') }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;border:1.5px solid #e2e8f0;color:#475569;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Agenda
                </a>
                <a href="{{ route('tjsp') }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;border:1.5px solid #e2e8f0;color:#475569;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                    DATAJUD
                </a>
            </div>
        </div>

        {{-- 2. KPI Cards --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;" class="dash-kpis">

            <a href="{{ route('processos') }}" style="text-decoration:none;">
                <div class="dash-kpi-card" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#2563eb;border-radius:12px 12px 0 0;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <canvas id="spark-processos" width="60" height="28" style="opacity:.5;"></canvas>
                    </div>
                    <div style="font-size:26px;font-weight:800;color:#1e3a8a;letter-spacing:-1px;">{{ $totalProcessos }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;font-weight:500;">Processos ativos</div>
                </div>
            </a>

            <a href="{{ route('sla') }}" style="text-decoration:none;">
                <div class="dash-kpi-card" style="background:#fff;border-radius:12px;border:1px solid {{ $prazosVencidos > 0 ? '#fca5a5' : '#e2e8f0' }};padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#ef4444;border-radius:12px 12px 0 0;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                    </div>
                    <div style="font-size:26px;font-weight:800;color:#dc2626;letter-spacing:-1px;">{{ $prazosHoje + $prazosVencidos }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;font-weight:500;">Prazos urgentes</div>
                    @if($prazosVencidos > 0)
                    <span style="display:inline-block;margin-top:6px;padding:2px 8px;background:#fee2e2;color:#dc2626;border-radius:4px;font-size:10px;font-weight:700;">{{ $prazosVencidos }} vencido{{ $prazosVencidos > 1 ? 's' : '' }}</span>
                    @else
                    <span style="display:inline-block;margin-top:6px;padding:2px 8px;background:#fee2e2;color:#dc2626;border-radius:4px;font-size:10px;font-weight:700;">Hoje</span>
                    @endif
                </div>
            </a>

            <a href="{{ route('financeiro') }}" style="text-decoration:none;">
                <div class="dash-kpi-card" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#16a34a;border-radius:12px 12px 0 0;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        </div>
                    </div>
                    @php $recStr = number_format((float)$totalReceber, 0, ',', '.'); $recFs = strlen($recStr) > 8 ? '17' : '22'; @endphp
                    <div style="font-size:{{ $recFs }}px;font-weight:800;color:#14532d;letter-spacing:-1px;">R$ {{ $recStr }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;font-weight:500;">A receber</div>
                    <span style="display:inline-block;margin-top:6px;padding:2px 8px;background:#dcfce7;color:#16a34a;border-radius:4px;font-size:10px;font-weight:700;">pendente</span>
                </div>
            </a>

            <a href="{{ route('financeiro') }}" style="text-decoration:none;">
                <div class="dash-kpi-card" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#0891b2;border-radius:12px 12px 0 0;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                    </div>
                    @php $honStr = number_format($honorariosMes, 0, ',', '.'); $honFs = strlen($honStr) > 8 ? '15' : '20'; @endphp
                    <div style="font-size:{{ $honFs }}px;font-weight:800;color:#0c4a6e;letter-spacing:-1px;">R$ {{ $honStr }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;font-weight:500;">Honorários recebidos</div>
                    <span style="display:inline-block;margin-top:6px;padding:2px 8px;background:#e0f2fe;color:#0891b2;border-radius:4px;font-size:10px;font-weight:700;">{{ now()->locale('pt_BR')->isoFormat('MMMM') }}</span>
                </div>
            </a>
        </div>

        {{-- 2b. Alerta processos parados --}}
        @if($processosParados > 0)
        <a href="{{ route('relatorios.sem-andamento') }}" style="text-decoration:none;display:flex;align-items:center;gap:12px;padding:10px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;">
            <svg width="16" height="16" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span style="font-size:13px;color:#92400e;">
                <strong>{{ $processosParados }} processo{{ $processosParados > 1 ? 's' : '' }}</strong> sem movimentação há mais de 30 dias
            </span>
            <span style="margin-left:auto;font-size:11px;font-weight:700;color:#d97706;white-space:nowrap;">Ver relatório →</span>
        </a>
        @endif

        {{-- 3. Próximos 7 dias — timeline unificada --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#1a3a5c;">Próximos 7 dias</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">Prazos, audiências e agenda — por ordem de data</div>
                </div>
                <div style="display:flex;gap:6px;">
                    @if($prazosVencidos > 0)
                    <span style="padding:3px 10px;background:#fee2e2;color:#dc2626;border-radius:20px;font-size:11px;font-weight:700;">{{ $prazosVencidos }} vencido{{ $prazosVencidos > 1 ? 's' : '' }}</span>
                    @endif
                    <a href="{{ route('sla') }}" style="font-size:12px;font-weight:700;color:#1D9E75;text-decoration:none;padding:5px 12px;background:#f0fdf4;border-radius:7px;border:1px solid #bbf7d0;">Ver painel</a>
                </div>
            </div>

            @forelse($timeline as $ev)
            @php
                $dias = $ev['dias'];
                if ($dias < 0)       { $cor='#dc2626'; $bg='#fee2e2'; $badge=abs($dias).'d atraso'; }
                elseif ($dias === 0) { $cor='#ea580c'; $bg='#fff7ed'; $badge='Hoje'; }
                elseif ($dias === 1) { $cor='#d97706'; $bg='#fefce8'; $badge='Amanhã'; }
                else                 { $cor='#2563eb'; $bg='#eff6ff'; $badge='em '.$dias.'d'; }
                $tipoCor = match($ev['tipo']) {
                    'prazo'     => ['bg'=>'#fef3c7','txt'=>'#92400e','icon'=>'⏱'],
                    'audiencia' => ['bg'=>'#dbeafe','txt'=>'#1e40af','icon'=>'⚖'],
                    'agenda'    => ['bg'=>'#f3e8ff','txt'=>'#6d28d9','icon'=>'📅'],
                    default     => ['bg'=>'#f1f5f9','txt'=>'#475569','icon'=>'•'],
                };
                $dataFmt = $ev['data'] instanceof \Carbon\Carbon ? $ev['data']->format('d/m') : \Carbon\Carbon::parse($ev['data'])->format('d/m');
            @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:10px 10px;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:7px;transition:background .15s;"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <div style="width:4px;height:36px;border-radius:3px;background:{{ $cor }};flex-shrink:0;"></div>
                <span style="font-size:11px;font-weight:700;color:#64748b;white-space:nowrap;min-width:30px;text-align:center;">{{ $dataFmt }}</span>
                <span style="padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $tipoCor['bg'] }};color:{{ $tipoCor['txt'] }};white-space:nowrap;">
                    {{ $tipoCor['icon'] }} {{ ucfirst($ev['tipo']) }}
                </span>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ Str::limit($ev['titulo'], 44) }}
                        @if($ev['fatal'])<span style="color:#dc2626;font-size:11px;margin-left:3px;">★</span>@endif
                    </div>
                    @if($ev['numero'])
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                        {{ $ev['numero'] }}{{ $ev['cliente'] ? ' · ' . Str::limit($ev['cliente'], 26) : '' }}{{ $ev['hora'] ? ' · ' . $ev['hora'] . 'h' : '' }}
                    </div>
                    @endif
                </div>
                <span style="padding:3px 9px;background:{{ $bg }};color:{{ $cor }};border-radius:6px;font-size:10px;font-weight:700;flex-shrink:0;white-space:nowrap;">{{ $badge }}</span>
                @if($ev['processo_id'])
                <a href="{{ route('processos.show', $ev['processo_id']) }}"
                    style="padding:5px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:7px;text-decoration:none;color:#64748b;font-size:11px;font-weight:600;flex-shrink:0;white-space:nowrap;transition:all .15s;"
                    onmouseover="this.style.background='#eff6ff';this.style.borderColor='#2563eb';this.style.color='#2563eb'"
                    onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                    Ver →
                </a>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:28px;color:#64748b;font-size:14px;">
                🎉 Nenhum prazo ou compromisso para os próximos 7 dias!
            </div>
            @endforelse
        </div>

        {{-- 4. Gráficos --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="dash-charts">

            {{-- Novos processos: tendência 6 meses --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
                @php
                    $tendArr = $tendenciaMensal->toArray();
                    $diffNovos = $novosEstesMes - $novosMesAnterior;
                    $tendSinal = $diffNovos > 0 ? '↑' : ($diffNovos < 0 ? '↓' : '→');
                    $tendCor   = $diffNovos > 0 ? '#16a34a' : ($diffNovos < 0 ? '#dc2626' : '#64748b');
                @endphp
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;">
                    <div style="font-size:14px;font-weight:700;color:#1a3a5c;">Novos processos</div>
                    <span style="font-size:13px;font-weight:800;color:{{ $tendCor }};">
                        {{ $tendSinal }} {{ abs($diffNovos) }}
                        <span style="font-size:10px;font-weight:500;color:#94a3b8;">vs mês ant.</span>
                    </span>
                </div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:14px;">Últimos 6 meses</div>
                <div style="display:flex;align-items:flex-end;gap:6px;height:100px;padding-bottom:4px;">
                    @foreach($tendenciaMensal as $idx => $m)
                    @php
                        $maxT = collect($tendArr)->max('total');
                        $h = $maxT > 0 ? max(6, round($m['total'] / $maxT * 80)) : 6;
                        $isLast = $loop->last;
                    @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <span style="font-size:9px;font-weight:700;color:{{ $isLast ? '#1D9E75' : '#94a3b8' }};">{{ $m['total'] }}</span>
                        <div style="width:100%;border-radius:4px 4px 0 0;background:{{ $isLast ? '#1D9E75' : '#e2e8f0' }};height:{{ $h }}px;transition:height .3s;"></div>
                        <span style="font-size:9px;color:#94a3b8;text-transform:uppercase;">{{ $m['mes'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;">
                    <span style="font-size:11px;color:#64748b;">Este mês: <strong style="color:#1D9E75;">{{ $novosEstesMes }}</strong></span>
                    <span style="font-size:11px;color:#64748b;">Mês anterior: <strong>{{ $novosMesAnterior }}</strong></span>
                </div>
            </div>

            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;margin-bottom:4px;">Atividade da semana</div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:14px;">Andamentos registrados por dia</div>
                <div style="height:160px;">
                    <canvas id="chart-atividade" style="max-height:160px;"></canvas>
                </div>
            </div>
        </div>

        {{-- 5. Últimas movimentações --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <div style="font-size:15px;font-weight:700;color:#1a3a5c;">Últimas movimentações</div>
                <a href="{{ route('processos') }}" style="font-size:12px;color:#1D9E75;font-weight:600;text-decoration:none;">Ver processos →</a>
            </div>
            @forelse($ultimosAndamentos as $and)
            @php
                $descLower = mb_strtolower($and->descricao ?? '');
                if (str_contains($descLower,'sentença')||str_contains($descLower,'decisão')||str_contains($descLower,'acórdão'))
                    { $dot='#7c3aed'; $tag='Decisão'; }
                elseif (str_contains($descLower,'prazo')||str_contains($descLower,'intimação')||str_contains($descLower,'citação'))
                    { $dot='#dc2626'; $tag='Prazo'; }
                elseif (str_contains($descLower,'petição')||str_contains($descLower,'recurso')||str_contains($descLower,'contestação'))
                    { $dot='#2563eb'; $tag='Petição'; }
                elseif (str_contains($descLower,'audiência')||str_contains($descLower,'julgamento'))
                    { $dot='#1D9E75'; $tag='Audiência'; }
                else { $dot='#94a3b8'; $tag='Andamento'; }
            @endphp
            <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <div style="width:8px;height:8px;border-radius:50%;background:{{ $dot }};flex-shrink:0;margin-top:5px;"></div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:3px;">
                        <a href="{{ route('processos.show', $and->processo_id) }}" style="font-size:12px;font-weight:700;color:#2563eb;text-decoration:none;">{{ $and->numero ?? '—' }}</a>
                        <span style="padding:1px 6px;background:{{ $dot }}18;color:{{ $dot }};border-radius:4px;font-size:10px;font-weight:700;">{{ $tag }}</span>
                    </div>
                    <div style="font-size:12px;color:#64748b;line-height:1.4;">{{ Str::limit($and->descricao, 90) }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                        {{ $and->cliente_nome ?? '—' }} · {{ \Carbon\Carbon::parse($and->created_at)->diffForHumans() }}
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:#64748b;font-size:13px;">Nenhuma movimentação recente.</div>
            @endforelse
        </div>

    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- COLUNA DIREITA                             --}}
    {{-- ══════════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Watchlist / Alertas --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <svg width="15" height="15" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;">Alertas do escritório</div>
            </div>
            @php
                $insights = [];
                if($prazosVencidos > 0)
                    $insights[] = ['titulo' => "{$prazosVencidos} prazo(s) vencido(s)", 'sub' => 'Requer ação imediata.', 'cor' => '#dc2626', 'bg' => '#fee2e2', 'link' => route('sla')];
                if($prazosHoje > 0)
                    $insights[] = ['titulo' => "{$prazosHoje} prazo(s) vencem hoje", 'sub' => 'Fique de olho na agenda.', 'cor' => '#ea580c', 'bg' => '#fff7ed', 'link' => route('sla')];
                if($processosParados > 0)
                    $insights[] = ['titulo' => "{$processosParados} processos sem movimento", 'sub' => 'Há mais de 30 dias sem andamento.', 'cor' => '#f59e0b', 'bg' => '#fffbeb', 'link' => route('relatorios.sem-andamento')];
                if($audienciasSemanais > 0)
                    $insights[] = ['titulo' => "{$audienciasSemanais} audiência(s) esta semana", 'sub' => 'Prepare documentos e estratégia.', 'cor' => '#2563eb', 'bg' => '#eff6ff', 'link' => route('audiencias')];
            @endphp
            @if(count($insights) === 0)
            <div style="text-align:center;padding:16px 0;color:#16a34a;font-size:13px;font-weight:600;">
                ✅ Escritório em dia!
            </div>
            @endif
            @foreach($insights as $ins)
            <a href="{{ $ins['link'] }}" style="text-decoration:none;display:block;margin-bottom:8px;">
                <div style="display:flex;gap:10px;padding:10px;border-radius:9px;transition:filter .15s;"
                    style="background:{{ $ins['bg'] }}"
                    onmouseover="this.style.filter='brightness(.96)'" onmouseout="this.style.filter=''">
                    <div style="width:4px;border-radius:3px;flex-shrink:0;background:{{ $ins['cor'] }}"></div>
                    <div>
                        <div style="font-size:12px;font-weight:700;color:#1e293b;">{{ $ins['titulo'] }}</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $ins['sub'] }}</div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Top Clientes --}}
        @if($topClientes->count() > 0)
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <svg width="15" height="15" fill="none" stroke="#1D9E75" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;">Top Clientes</div>
                <span style="margin-left:auto;font-size:11px;color:#94a3b8;">por processos ativos</span>
            </div>
            @php $maxCliente = $topClientes->max('total'); @endphp
            @foreach($topClientes as $i => $cli)
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;">
                    <span style="font-size:12px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;" title="{{ $cli->nome }}">
                        <span style="font-size:10px;color:#94a3b8;margin-right:4px;">{{ $i + 1 }}.</span>{{ $cli->nome }}
                    </span>
                    <span style="font-size:12px;font-weight:700;color:#1D9E75;flex-shrink:0;margin-left:8px;">{{ $cli->total }}</span>
                </div>
                <div style="height:4px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;border-radius:4px;background:{{ $i === 0 ? '#1D9E75' : ($i === 1 ? '#2563eb' : '#94a3b8') }};width:{{ $maxCliente > 0 ? round($cli->total / $maxCliente * 100) : 0 }}%;transition:width .4s;"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Aniversariantes da semana --}}
        @if($aniversariantes->count() > 0)
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <span style="font-size:16px;">🎂</span>
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;">Aniversariantes</div>
                <span style="margin-left:auto;font-size:11px;color:#94a3b8;">próximos 7 dias</span>
            </div>
            @foreach($aniversariantes as $a)
            @php $aHoje = $a['hoje']; @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <div style="width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;
                    background:{{ $aHoje ? '#fef3c7' : '#f8fafc' }};color:{{ $aHoje ? '#d97706' : '#94a3b8' }};">
                    {{ $a['dia'] }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $a['nome'] }}</div>
                    <div style="font-size:11px;color:#94a3b8;">{{ $a['idade'] }} anos</div>
                </div>
                @if($aHoje)
                <span style="padding:2px 7px;background:#fef3c7;color:#d97706;border-radius:4px;font-size:10px;font-weight:700;">Hoje!</span>
                @else
                <span style="font-size:11px;color:#94a3b8;">em {{ $a['dias_ate'] }}d</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Agenda Hoje --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;">Agenda de Hoje</div>
                <a href="{{ route('agenda') }}" style="font-size:11px;color:#1D9E75;text-decoration:none;font-weight:600;">Ver tudo</a>
            </div>
            @forelse($agendaHoje as $ev)
            <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#1D9E75;white-space:nowrap;padding-top:1px;min-width:36px;">{{ $ev->data_hora->format('H:i') }}</span>
                <div style="min-width:0;flex:1;">
                    <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ev->titulo }}</div>
                    @if($ev->local)<div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $ev->local }}</div>@endif
                </div>
                @if($ev->urgente)
                <span style="padding:2px 6px;background:#fef2f2;color:#dc2626;border-radius:4px;font-size:9px;font-weight:700;flex-shrink:0;">Urgente</span>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:20px 0;color:#94a3b8;font-size:13px;">Nenhum compromisso hoje.</div>
            @endforelse
        </div>

        {{-- Acesso rápido IA --}}
        <a href="{{ route('assistente') }}" style="text-decoration:none;display:block;background:linear-gradient(135deg,#1a3a5c,#0f2540);border-radius:14px;padding:18px;">
            <div style="font-size:12px;color:#6ee7b7;font-weight:700;margin-bottom:8px;">✨ ASSISTENTE IA</div>
            <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;">Analisar carteira com IA</div>
            <div style="font-size:11px;color:rgba(255,255,255,.5);">Resumo inteligente dos processos e alertas</div>
        </a>

    </div>
</div>

@verbatim
<style>
@media (max-width: 1100px) { .dash-layout { grid-template-columns: 1fr !important; } }
@media (max-width: 860px)  { .dash-kpis { grid-template-columns: repeat(2,1fr) !important; } .dash-charts { grid-template-columns: 1fr !important; } }
@media (max-width: 480px)  { .dash-kpis { grid-template-columns: repeat(2,1fr) !important; } }
</style>
@endverbatim

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    var scoreData  = @json(['criticos' => $criticos, 'atencao' => $atencao, 'normais' => $normais]);
    var semanaData = @json($atividadeSemana);
    var spark7     = @json($spark7);

    ['chart-prazos','chart-atividade'].forEach(function(id) {
        var ex = Chart.getChart(id); if (ex) ex.destroy();
    });

    // ── Donut: Score ──────────────────────────────────────────
    var ctxDonut = document.getElementById('chart-prazos');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Críticos','Em atenção','Saudáveis'],
                datasets: [{ data: [scoreData.criticos, scoreData.atencao, scoreData.normais],
                    backgroundColor: ['#ef4444','#f59e0b','#10b981'], borderWidth: 0, hoverOffset: 4 }]
            },
            options: { cutout: '72%', plugins: { legend: { display: false } }, animation: { duration: 600 } }
        });
    }

    // ── Barras: Atividade da semana ───────────────────────────
    var ctxBar = document.getElementById('chart-atividade');
    if (ctxBar) {
        var dias = ['SEG','TER','QUA','QUI','SEX'];
        var mapaAtiv = {};
        semanaData.forEach(function(r) { mapaAtiv[r.dia] = r.total; });
        var abrevMap = {'MON':'SEG','TUE':'TER','WED':'QUA','THU':'QUI','FRI':'SEX','SAT':'SÁB','SUN':'DOM','SEG':'SEG','TER':'TER','QUA':'QUA','QUI':'QUI','SEX':'SEX'};
        var totaisBar = {};
        Object.keys(mapaAtiv).forEach(function(k) { var m = abrevMap[k]||k; totaisBar[m] = (totaisBar[m]||0) + mapaAtiv[k]; });
        var valores = dias.map(function(d) { return totaisBar[d]||0; });
        new Chart(ctxBar, {
            type: 'bar',
            data: { labels: dias, datasets: [{ label: 'Andamentos', data: valores, backgroundColor: '#6366f1', borderRadius: 6, borderSkipped: false, hoverBackgroundColor: '#4f46e5' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8' } },
                          y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }, beginAtZero: true } },
                animation: { duration: 600 } }
        });
    }

    // ── Sparklines ────────────────────────────────────────────
    function sparkline(id, data, color) {
        var el = document.getElementById(id);
        if (!el) return;
        var ex = Chart.getChart(id); if (ex) ex.destroy();
        new Chart(el, {
            type: 'line',
            data: { labels: data.map(function(_,i){ return i; }),
                    datasets: [{ data: data, borderColor: color, borderWidth: 2, pointRadius: 0, tension: 0.4, fill: true,
                        backgroundColor: color + '18' }] },
            options: { responsive: false, plugins: { legend: { display:false }, tooltip: { enabled:false } },
                scales: { x: { display:false }, y: { display:false, beginAtZero:true } },
                animation: { duration: 400 } }
        });
    }
    sparkline('spark-andamentos', spark7, '#6366f1');
    // processos sparkline: flat line with total value repeated
    sparkline('spark-processos', Array(7).fill(@json($totalProcessos)), '#2563eb');
})();
</script>

</div>
