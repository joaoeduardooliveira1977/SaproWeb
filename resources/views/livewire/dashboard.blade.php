<div style="padding:22px 24px;background:#f0f4f8;min-height:100%;">

{{-- ── Layout 2 colunas ──────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 276px;gap:18px;align-items:start;" class="dash-layout">

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- COLUNA ESQUERDA                                          --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:18px;">

        {{-- 1. Cabeçalho --}}
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
            <a href="{{ route('tjsp') }}"
                style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:#1D9E75;color:#fff;border-radius:9px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
                onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                Atualizar andamentos
            </a>
        </div>

        {{-- 2. KPI Cards — 4 colunas --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;" class="dash-kpis">

            <a href="{{ route('processos') }}" style="text-decoration:none;">
                <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;cursor:pointer;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#2563eb;border-radius:12px 12px 0 0;"></div>
                    <div style="width:38px;height:38px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div style="font-size:24px;font-weight:800;color:#1e3a8a;letter-spacing:-1px;">{{ $totalProcessos }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;font-weight:500;">Processos ativos</div>
                    <span style="display:inline-block;margin-top:8px;padding:2px 8px;background:#dbeafe;color:#1d4ed8;border-radius:4px;font-size:10px;font-weight:700;">ativos</span>
                </div>
            </a>

            <a href="{{ route('prazos') }}" style="text-decoration:none;">
                <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;cursor:pointer;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#ef4444;border-radius:12px 12px 0 0;"></div>
                    <div style="width:38px;height:38px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div style="font-size:24px;font-weight:800;color:#dc2626;letter-spacing:-1px;">{{ $prazosHoje }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;font-weight:500;">Prazos urgentes</div>
                    <span style="display:inline-block;margin-top:8px;padding:2px 8px;background:#fee2e2;color:#dc2626;border-radius:4px;font-size:10px;font-weight:700;">Hoje</span>
                </div>
            </a>

            <a href="{{ route('financeiro') }}" style="text-decoration:none;">
                <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;cursor:pointer;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#16a34a;border-radius:12px 12px 0 0;"></div>
                    <div style="width:38px;height:38px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    @php $recStr = number_format((float)$totalReceber, 0, ',', '.'); $recFs = strlen($recStr) > 8 ? '17' : '24'; @endphp
                    <div style="font-size:{{ $recFs }}px;font-weight:800;color:#14532d;letter-spacing:-1px;">R$ {{ $recStr }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;font-weight:500;">A receber</div>
                    <span style="display:inline-block;margin-top:8px;padding:2px 8px;background:#dcfce7;color:#16a34a;border-radius:4px;font-size:10px;font-weight:700;">pendente</span>
                </div>
            </a>

            <a href="{{ route('processos') }}" style="text-decoration:none;">
                <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;cursor:pointer;"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#1D9E75;border-radius:12px 12px 0 0;"></div>
                    <div style="width:38px;height:38px;border-radius:10px;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1D9E75" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div style="font-size:24px;font-weight:800;color:#065f46;letter-spacing:-1px;">{{ $andamentosHoje }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;font-weight:500;">Andamentos</div>
                    <span style="display:inline-block;margin-top:8px;padding:2px 8px;background:#d1fae5;color:#1D9E75;border-radius:4px;font-size:10px;font-weight:700;">Hoje</span>
                </div>
            </a>
        </div>

        {{-- 3. Card "O que mover agora" --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#1a3a5c;">O que mover agora</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">Prazos prioritários dos próximos 7 dias</div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if(count($acoesUrgentes) > 0)
                    <span style="padding:3px 10px;background:#fee2e2;color:#dc2626;border-radius:20px;font-size:11px;font-weight:700;">{{ count($acoesUrgentes) }} alerta{{ count($acoesUrgentes) > 1 ? 's' : '' }}</span>
                    @endif
                    <a href="{{ route('prazos') }}" style="font-size:12px;font-weight:700;color:#1D9E75;text-decoration:none;padding:5px 12px;background:#f0fdf4;border-radius:7px;border:1px solid #bbf7d0;">Ver todos</a>
                </div>
            </div>

            @forelse($acoesUrgentes as $prazo)
            @php
                $dias = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($prazo->data_prazo)->startOfDay(), false);
                if ($dias < 0)       { $cor='#dc2626'; $bgCor='#fee2e2'; $badge='Vencido'; $tipoBadge='Urgente'; }
                elseif ($dias === 0) { $cor='#ea580c'; $bgCor='#fff7ed'; $badge='Hoje';    $tipoBadge='Urgente'; }
                elseif ($dias <= 2)  { $cor='#d97706'; $bgCor='#fffbeb'; $badge=$dias.'d'; $tipoBadge='Prazo'; }
                else                 { $cor='#2563eb'; $bgCor='#eff6ff'; $badge=$dias.'d'; $tipoBadge='Prazo'; }
                $hora = \Carbon\Carbon::parse($prazo->data_prazo)->format('d/m');
            @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:8px;transition:background .15s;"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <span style="font-size:12px;font-weight:700;color:#2563eb;white-space:nowrap;min-width:36px;text-align:center;">{{ $hora }}</span>
                <div style="width:4px;height:36px;border-radius:3px;background:{{ $cor }};flex-shrink:0;"></div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span style="font-size:13px;font-weight:700;color:#1e293b;">{{ Str::limit($prazo->titulo, 48) }}</span>
                        @if($prazo->prazo_fatal)<span style="padding:1px 6px;background:#fef2f2;color:#dc2626;border-radius:4px;font-size:10px;font-weight:700;">FATAL</span>@endif
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">
                        {{ $prazo->processo?->numero ?? '—' }}@if($prazo->processo?->cliente) · {{ Str::limit($prazo->processo->cliente->nome, 28) }}@endif
                    </div>
                </div>
                <span style="padding:3px 8px;background:{{ $bgCor }};color:{{ $cor }};border-radius:5px;font-size:10px;font-weight:700;flex-shrink:0;">{{ $badge }}</span>
                <a href="{{ route('processos.show', $prazo->processo_id) }}"
                    style="display:flex;align-items:center;padding:6px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:7px;text-decoration:none;color:#64748b;font-size:11px;font-weight:600;flex-shrink:0;white-space:nowrap;transition:all .15s;"
                    onmouseover="this.style.background='#eff6ff';this.style.borderColor='#2563eb';this.style.color='#2563eb'"
                    onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                    Ver →
                </a>
            </div>
            @empty
            <div style="text-align:center;padding:28px;color:#64748b;font-size:14px;">
                🎉 Nenhum prazo urgente para essa semana!
            </div>
            @endforelse
        </div>

        {{-- 4. Linha dupla: Gráfico pizza + Gráfico barras --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="dash-charts">

            {{-- Card: Status dos Prazos (donut) --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;margin-bottom:4px;">Leitura em 5 segundos</div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:14px;">Score da carteira ativa</div>
                <div style="position:relative;height:180px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="chart-prazos" style="max-height:180px;"></canvas>
                    @php $total3 = $criticos + $atencao + $normais; $pctOk = $total3 > 0 ? round($normais / $total3 * 100) : 0; @endphp
                    <div style="position:absolute;text-align:center;pointer-events:none;">
                        <div style="font-size:20px;font-weight:800;color:#1e293b;">{{ $pctOk }}%</div>
                        <div style="font-size:10px;color:#64748b;font-weight:500;">em dia</div>
                    </div>
                </div>
                <div style="display:flex;justify-content:center;gap:14px;margin-top:12px;flex-wrap:wrap;">
                    <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#64748b;"><span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>Críticos ({{ $criticos }})</span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#64748b;"><span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>Atenção ({{ $atencao }})</span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#64748b;"><span style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block;"></span>Saudáveis ({{ $normais }})</span>
                </div>
            </div>

            {{-- Card: Atividade da semana (barras) --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;margin-bottom:4px;">Atividade da semana</div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:14px;">Andamentos registrados por dia</div>
                <div style="height:180px;">
                    <canvas id="chart-atividade" style="max-height:180px;"></canvas>
                </div>
            </div>
        </div>

        {{-- 5. Card: Últimas movimentações --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px;">
            <div style="font-size:15px;font-weight:700;color:#1a3a5c;margin-bottom:16px;">Últimas movimentações</div>

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
                        <span style="font-size:12px;font-weight:700;color:#1e293b;">{{ $and->numero ?? '—' }}</span>
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

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- COLUNA DIREITA                                           --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- 1. Card Assistente IA --}}
        <div style="background:linear-gradient(135deg,#1a3a5c,#0f2540);border-radius:14px;padding:20px;">
            <span style="display:inline-block;padding:3px 10px;background:rgba(29,158,117,.25);color:#6ee7b7;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.5px;margin-bottom:12px;">ASSISTENTE IA</span>
            <div style="font-size:16px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:6px;">Resumo inteligente da carteira</div>
            <div style="font-size:11px;color:rgba(255,255,255,.45);margin-bottom:16px;">A IA entra como apoio estratégico</div>
            <div style="font-size:12px;color:rgba(255,255,255,.7);line-height:1.6;margin-bottom:16px;background:rgba(255,255,255,.06);border-radius:8px;padding:12px;">
                @php
                    $msgs = [];
                    if($prazosHoje > 0) $msgs[] = "<strong style='color:#fbbf24'>{$prazosHoje} prazo(s)</strong> vencem hoje.";
                    if($processosParados > 0) $msgs[] = "<strong style='color:#fbbf24'>{$processosParados} processos</strong> sem movimento há +30 dias.";
                    $msgIA = $msgs ? implode(' ', $msgs) : "✓ Tudo sob controle hoje. Carteira em ordem.";
                @endphp
                {!! $msgIA !!}
            </div>
            <a href="{{ route('assistente') }}"
                style="display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;background:#fff;border-radius:9px;text-decoration:none;color:#1a3a5c;font-size:13px;font-weight:800;transition:opacity .15s;"
                onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
                Analisar com IA
            </a>
            <div style="display:flex;flex-direction:column;gap:6px;margin-top:8px;">
                <a href="{{ route('prazos') }}"
                    style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:8px;text-decoration:none;color:rgba(255,255,255,.7);font-size:12px;font-weight:600;transition:background .15s;"
                    onmouseover="this.style.background='rgba(255,255,255,.13)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                    Ver urgentes <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="{{ route('tjsp') }}"
                    style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:8px;text-decoration:none;color:rgba(255,255,255,.7);font-size:12px;font-weight:600;transition:background .15s;"
                    onmouseover="this.style.background='rgba(255,255,255,.13)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                    Processos parados <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>

        {{-- 2. Card RADAR — Watchlist --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
            <span style="display:inline-block;padding:3px 10px;background:#fef9c3;color:#92400e;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.5px;margin-bottom:10px;">RADAR</span>
            <div style="font-size:14px;font-weight:700;color:#1a3a5c;margin-bottom:14px;">Watchlist do escritório</div>

            @php
                $insights = [];
                if($processosParados > 0)
                    $insights[] = ['titulo' => "{$processosParados} processos sem movimento", 'sub' => 'A carteira precisa de uma rodada de reativação.', 'cor' => '#f59e0b', 'bg' => '#fffbeb'];
                if($prazosHoje > 0)
                    $insights[] = ['titulo' => "{$prazosHoje} prazos com janela curta", 'sub' => 'Vale distribuir a preparação entre hoje e amanhã.', 'cor' => '#dc2626', 'bg' => '#fee2e2'];
                if($audienciasSemanais > 0)
                    $insights[] = ['titulo' => "{$audienciasSemanais} audiências na semana", 'sub' => 'Concentre estratégia e documentos no bloco da manhã.', 'cor' => '#2563eb', 'bg' => '#eff6ff'];
            @endphp

            @if(count($insights) === 0)
            <div style="text-align:center;padding:16px 0;color:#64748b;font-size:13px;">
                ✅ Escritório em dia! Nenhum alerta ativo.
            </div>
            @else
            @foreach($insights as $ins)
            <div style="display:flex;gap:10px;padding:10px;border-radius:9px;background:{{ $ins['bg'] }};margin-bottom:8px;">
                <div style="width:4px;border-radius:3px;background:{{ $ins['cor'] }};flex-shrink:0;"></div>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#1e293b;">{{ $ins['titulo'] }}</div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;line-height:1.4;">{{ $ins['sub'] }}</div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- 3. Card Agenda de Hoje --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <div style="font-size:14px;font-weight:700;color:#1a3a5c;">Agenda de Hoje</div>
                <a href="{{ route('agenda') }}" style="font-size:11px;color:#1D9E75;text-decoration:none;font-weight:600;">Ver tudo</a>
            </div>
            @forelse($agendaHoje as $ev)
            <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#1D9E75;white-space:nowrap;padding-top:1px;min-width:38px;">{{ $ev->data_hora->format('H:i') }}</span>
                <div style="min-width:0;flex:1;">
                    <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ev->titulo }}</div>
                    @if($ev->tipo)<div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $ev->tipo }}@if($ev->local) · {{ $ev->local }}@endif</div>@endif
                </div>
                @if($ev->urgente)
                <span style="margin-left:auto;padding:2px 6px;background:#fef2f2;color:#dc2626;border-radius:4px;font-size:9px;font-weight:700;flex-shrink:0;align-self:flex-start;">Urgente</span>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:20px 0;color:#94a3b8;font-size:13px;">Nenhum compromisso hoje.</div>
            @endforelse
        </div>

    </div>
</div>

@verbatim
<style>
@media (max-width: 1100px) {
    .dash-layout { grid-template-columns: 1fr !important; }
}
@media (max-width: 860px) {
    .dash-kpis  { grid-template-columns: repeat(2,1fr) !important; }
    .dash-charts { grid-template-columns: 1fr !important; }
}
@media (max-width: 480px) {
    .dash-kpis { grid-template-columns: repeat(2,1fr) !important; }
}
</style>
@endverbatim

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    var scoreData  = @json(['criticos' => $criticos, 'atencao' => $atencao, 'normais' => $normais]);
    var semanaData = @json($atividadeSemana);

    // Destrói instâncias anteriores (Livewire hot-reload)
    ['chart-prazos','chart-atividade'].forEach(function(id) {
        var existing = Chart.getChart(id);
        if (existing) existing.destroy();
    });

    // ── Donut: Score da carteira ──────────────────────────────────
    var ctxDonut = document.getElementById('chart-prazos');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Críticos', 'Em atenção', 'Saudáveis'],
                datasets: [{
                    data: [scoreData.criticos, scoreData.atencao, scoreData.normais],
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }]
            },
            options: {
                cutout: '72%',
                plugins: { legend: { display: false }, tooltip: { callbacks: {
                    label: function(ctx) { return ctx.label + ': ' + ctx.parsed; }
                }}},
                animation: { duration: 600 }
            }
        });
    }

    // ── Barras: Atividade da semana ───────────────────────────────
    var ctxBar = document.getElementById('chart-atividade');
    if (ctxBar) {
        // Garante os 5 dias da semana com valores 0 por padrão
        var dias = ['SEG','TER','QUA','QUI','SEX'];
        var mapaAtiv = {};
        semanaData.forEach(function(r) { mapaAtiv[r.dia] = r.total; });
        // Mapeia abreviações PT para os labels
        var abrevMap = {'MON':'SEG','TUE':'TER','WED':'QUA','THU':'QUI','FRI':'SEX','SAT':'SÁB','SUN':'DOM',
                        'SEG':'SEG','TER':'TER','QUA':'QUA','QUI':'QUI','SEX':'SEX'};
        var totaisBar = {};
        Object.keys(mapaAtiv).forEach(function(k) {
            var mapped = abrevMap[k] || k;
            totaisBar[mapped] = (totaisBar[mapped] || 0) + mapaAtiv[k];
        });
        var valores = dias.map(function(d) { return totaisBar[d] || 0; });

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: dias,
                datasets: [{
                    label: 'Andamentos',
                    data: valores,
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: '#4f46e5',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8' } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }, beginAtZero: true }
                },
                animation: { duration: 600 }
            }
        });
    }
})();
</script>


</div>


