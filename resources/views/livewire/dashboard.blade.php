

<div>

    {{-- TOPO SaaS PROFISSIONAL --}}
    <div style="margin-bottom:18px;">

        {{-- Faixa de atenção --}}
        
	<div style="
            background: linear-gradient(90deg, #3b82f6 0%, #4f46e5 45%, #60a5fa 100%);
            border-radius: 16px;
            padding: 22px 24px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.18);
            margin-bottom: 18px;
        ">
            <div class="dash-alert-topo" style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:18px;
                flex-wrap:wrap;
            ">

                <div style="display:flex; align-items:center; gap:16px; min-width:0;">
                    <div style="
                        width:56px;
                        height:56px;
                        border-radius:18px;
                        background: rgba(255,255,255,.12);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        flex-shrink:0;
                        box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
                    ">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>

                    <div style="min-width:0;">
                        <div style="font-size:16px; font-weight:800; margin-bottom:6px;">
                            Atenção, {{ Auth::user()?->nome ?? 'admin' }}!
                        </div>

                        <div style="font-size:14px; color:rgba(255,255,255,.92); line-height:1.7;">
                            Você tem
                            <strong style="color:#fde68a;">{{ $stats['prazos_7dias'] ?? 0 }} prazo(s)</strong>
                            nos próximos dias,
                            <strong style="color:#fca5a5;">{{ $stats['processos_parados'] ?? 0 }} processo(s) parado(s)</strong>
                            há mais de 30 dias
                            e
                            <strong style="color:#bfdbfe;">{{ ($stats['prazos_vencidos'] ?? 0) + ($stats['processos_parados'] ?? 0) }} alerta(s)</strong>.
                        </div>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <a href="{{ route('prazos') }}"
                       style="
                            background:#fff;
                            color:#1e3a8a;
                            text-decoration:none;
                            font-size:13px;
                            font-weight:700;
                            padding:12px 16px;
                            border-radius:12px;
                            display:inline-flex;
                            align-items:center;
                            gap:8px;
                            box-shadow: 0 6px 18px rgba(0,0,0,.10);
                       ">
                        Resolver agora
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Cards principais --}}




        <div class="dash-kpis-v2" style="
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:16px;
            margin-bottom:18px;
        ">

            {{-- Card 1 --}}
            <a href="{{ route('processos') }}" style="text-decoration:none;">
                <div style="
                    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                    border-radius:16px;
                    padding:22px 20px;
                    color:#fff;
                    min-height:150px;
                    box-shadow:0 10px 22px rgba(37,99,235,.18);
                    transition:transform .15s ease, box-shadow .15s ease;
                "
                onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 16px 30px rgba(37,99,235,.24)'"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 22px rgba(37,99,235,.18)'">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>

                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>

                    <div style="font-size:18px; font-weight:800; line-height:1; margin-bottom:8px;">
                        {{ $stats['processos_ativos'] ?? 0 }}
                    </div>

                    <div style="font-size:13px; color:rgba(255,255,255,.9); margin-bottom:10px;">
                        Processos Ativos
                    </div>

                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <span style="
                            background:rgba(255,255,255,.14);
                            color:#fff;
                            border-radius:999px;
                            padding:4px 10px;
                            font-size:11px;
                            font-weight:700;
                        ">
                            {{ $stats['processos_parados'] ?? 0 }} parados
                        </span>
                    </div>
                </div>
            </a>

            {{-- Card 2 --}}
            <a href="{{ route('prazos') }}" style="text-decoration:none;">
                <div style="
                    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
                    border-radius:16px;
                    padding:22px 20px;
                    color:#fff;
                    min-height:150px;
                    box-shadow:0 10px 22px rgba(124,58,237,.18);
                    transition:transform .15s ease, box-shadow .15s ease;
                "
                onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 16px 30px rgba(124,58,237,.24)'"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 22px rgba(124,58,237,.18)'">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>

                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>

                    <div style="font-size:18px; font-weight:800; line-height:1; margin-bottom:8px;">
                        {{ $stats['prazos_7dias'] ?? 0 }}
                    </div>

                    <div style="font-size:13px; color:rgba(255,255,255,.9); margin-bottom:10px;">
                        Prazos (7 dias)
                    </div>

                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <span style="
                            background:#fde68a;
                            color:#6b21a8;
                            border-radius:999px;
                            padding:4px 10px;
                            font-size:11px;
                            font-weight:700;
                        ">
                            {{ $stats['prazos_vencidos'] ?? 0 }} vencidos
                        </span>
                    </div>
                </div>
            </a>

            {{-- Card 3 --}}
            <a href="{{ route('financeiro') }}" style="text-decoration:none;">
                <div style="
                    background: linear-gradient(135deg, #059669 0%, #16a34a 100%);
                    border-radius:16px;
                    padding:22px 20px;
                    color:#fff;
                    min-height:150px;
                    box-shadow:0 10px 22px rgba(5,150,105,.18);
                    transition:transform .15s ease, box-shadow .15s ease;
                "
                onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 16px 30px rgba(5,150,105,.24)'"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 22px rgba(5,150,105,.18)'">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>

                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>

                    <div style="font-size:18px; font-weight:800; line-height:1; margin-bottom:8px;">
                        R$ {{ number_format($stats['recebimentos_pendentes'] ?? 0, 2, ',', '.') }}
                    </div>

                    <div style="font-size:13px; color:rgba(255,255,255,.9); margin-bottom:10px;">
                        A Receber
                    </div>
                </div>
            </a>

            {{-- Card 4 --}}
            <a href="{{ route('prazos') }}" style="text-decoration:none;">
                <div style="
                    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    border-radius:16px;
                    padding:22px 20px;
                    color:#fff;
                    min-height:150px;
                    box-shadow:0 10px 22px rgba(239,68,68,.18);
                    transition:transform .15s ease, box-shadow .15s ease;
                "
                onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 16px 30px rgba(239,68,68,.24)'"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 22px rgba(239,68,68,.18)'">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2">
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>

                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>

                    <div style="font-size:18px; font-weight:800; line-height:1; margin-bottom:8px;">
                        {{ ($stats['prazos_vencidos'] ?? 0) + ($stats['processos_parados'] ?? 0) }}
                    </div>

                    <div style="font-size:13px; color:rgba(255,255,255,.9); margin-bottom:10px;">
                        Alertas Pendentes
                    </div>

                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <span style="
                            background:rgba(255,255,255,.14);
                            color:#fff;
                            border-radius:999px;
                            padding:4px 10px;
                            font-size:11px;
                            font-weight:700;
                        ">
                            críticos
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>

  


{{-- ETAPA 2 — BLOCO CENTRAL DECISIONAL --}}

<div class="dash-blocos-centrais" style="
    display:grid;
    grid-template-columns: 1.15fr 1fr 1.15fr;
    gap:16px;
    margin-bottom:20px;
">

    {{-- 1. PRÓXIMOS PRAZOS --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:20px;
        box-shadow:0 8px 20px rgba(15,23,42,.04);
        min-height:100%;
    ">


        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">⏰ Próximos Prazos</div>
                <div style="font-size:12px;color:#64748b;">Prioridades dos próximos dias</div>
            </div>

            <a href="{{ route('prazos') }}"
               style="
                    text-decoration:none;
                    font-size:12px;
                    font-weight:700;
                    color:#2563eb;
                    background:#eff6ff;
                    padding:8px 12px;
                    border-radius:999px;
               ">
                Ver todos
            </a>
        </div>

        @if(!empty($prazosProximos) && count($prazosProximos))
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($prazosProximos as $pz)
                    @php
                        $dias = $pz['dias'] ?? null;
                        $titulo = $pz['titulo'] ?? 'Prazo';
                        $processo = $pz['processo'] ?? null;
                        $data = $pz['data'] ?? null;

                        $cor = '#2563eb';
                        $bg = '#eff6ff';

                        if ($dias !== null) {
                            if ($dias < 0) {
                                $cor = '#dc2626';
                                $bg = '#fef2f2';
                            } elseif ($dias === 0) {
                                $cor = '#b45309';
                                $bg = '#fff7ed';
                            } elseif ($dias <= 3) {
                                $cor = '#d97706';
                                $bg = '#fffbeb';
                            }
                        }
                    @endphp

                    <div style="
                        display:flex;
                        gap:12px;
                        align-items:flex-start;
                        padding:12px;
                        border-radius:12px;
                        background:{{ $bg }};
                        border:1px solid rgba(0,0,0,.04);
                    ">
                        <div style="
                            min-width:44px;
                            height:44px;
                            border-radius:12px;
                            background:#fff;
                            display:flex;
                            flex-direction:column;
                            align-items:center;
                            justify-content:center;
                            font-size:11px;
                            font-weight:800;
                            color:{{ $cor }};
                            box-shadow:0 2px 8px rgba(0,0,0,.05);
                        ">
                            {{ $data ?? '—' }}
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:#0f172a;line-height:1.4;">
                                {{ $titulo }}
                            </div>

                            @if($processo)
                                <div style="font-size:12px;color:#64748b;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $processo }}
                                </div>
                            @endif
                        </div>

                        <div style="
                            font-size:11px;
                            font-weight:800;
                            color:{{ $cor }};
                            white-space:nowrap;
                            margin-top:2px;
                        ">
                            @if($dias !== null)
                                @if($dias < 0)
                                    {{ abs($dias) }}d atraso
                                @elseif($dias === 0)
                                    Hoje
                                @else
                                    {{ $dias }}d
                                @endif
                            @else
                                —
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="
                min-height:180px;
                display:flex;
                align-items:center;
                justify-content:center;
                text-align:center;
                color:#64748b;
                font-size:13px;
            ">
                Nenhum prazo relevante no momento.
            </div>
        @endif
    </div>

    {{-- 2. INTELIGÊNCIA DO SISTEMA --}}
    <div style="
        background:linear-gradient(180deg,#0f172a 0%, #111827 100%);
        border-radius:16px;
        padding:20px;
        color:#fff;
        box-shadow:0 12px 26px rgba(15,23,42,.18);
        min-height:100%;
        position:relative;
        overflow:hidden;
    ">
       <div style="
            position:absolute;
            right:-20px;
            top:-20px;
            width:120px;
            height:120px;
            border-radius:999px;
            background:rgba(59,130,246,.18);
            filter:blur(4px);
        "></div>

        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                <div style="
                    width:42px;
                    height:42px;
                    border-radius:14px;
                    background:linear-gradient(135deg,#3b82f6,#7c3aed);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:18px;
                ">
                    🤖
                </div>

                <div>
                    <div style="font-size:15px;font-weight:800;">Inteligência do Sistema</div>
                    <div style="font-size:12px;color:rgba(255,255,255,.72);">Insights automáticos do dia</div>
                </div>
            </div>





            <div style="
                background:rgba(255,255,255,.06);
                border:1px solid rgba(255,255,255,.08);
                border-radius:14px;
                padding:16px;
                margin-bottom:14px;
            ">
                <div style="font-size:13px;line-height:1.7;color:rgba(255,255,255,.92);">
                    Hoje detectamos:
                </div>

                <ul style="margin:10px 0 0 18px;padding:0;color:#e5e7eb;font-size:13px;line-height:1.8;">
                    <li>{{ $stats['prazos_vencidos'] ?? 0 }} prazo(s) vencido(s)</li>
                    <li>{{ $stats['processos_parados'] ?? 0 }} processo(s) parado(s) há mais de 30 dias</li>
                    <li>{{ $stats['prazos_7dias'] ?? 0 }} prazo(s) nos próximos 7 dias</li>
                </ul>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('processos') }}"
                   style="
                        text-decoration:none;
                        background:#fff;
                        color:#111827;
                        font-size:13px;
                        font-weight:800;
                        padding:12px 14px;
                        border-radius:12px;
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                   ">
                    Ver análises
                    <span>→</span>
                </a>

                <a href="{{ route('minutas') }}"
                   style="
                        text-decoration:none;
                        background:rgba(255,255,255,.08);
                        color:#fff;
                        border:1px solid rgba(255,255,255,.1);
                        font-size:13px;
                        font-weight:700;
                        padding:12px 14px;
                        border-radius:12px;
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                   ">
                    Gerar minuta IA
                    <span>✦</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 3. PROCESSOS PARADOS --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:20px;
        box-shadow:0 8px 20px rgba(15,23,42,.04);
        min-height:100%;
    ">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">🚨 Processos Parados</div>
                <div style="font-size:12px;color:#64748b;">Sem movimentação há +30 dias</div>
            </div>

            <a href="{{ route('processos') }}"
               style="
                    text-decoration:none;
                    font-size:12px;
                    font-weight:700;
                    color:#dc2626;
                    background:#fef2f2;
                    padding:8px 12px;
                    border-radius:999px;
               ">
                Ver todos
            </a>
        </div>

        @if(!empty($processosParados) && count($processosParados))
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($processosParados as $p)
                    @php
                        $numero = is_array($p) ? ($p['numero'] ?? 'Processo') : ($p->numero ?? 'Processo');
                        $cliente = is_array($p) ? ($p['cliente'] ?? null) : ($p->cliente ?? null);
                        $dias = is_array($p) ? ($p['dias'] ?? null) : ($p->dias ?? null);
                    @endphp

                    <div style="
                        display:flex;
                        gap:12px;
                        align-items:flex-start;
                        padding:12px;
                        border-radius:12px;
                        background:#fff7ed;
                        border:1px solid #fed7aa;
                    ">
                        <div style="
                            width:44px;
                            height:44px;
                            border-radius:12px;
                            background:#fff;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#c2410c;
                            font-size:16px;
                            font-weight:800;
                            flex-shrink:0;
                            box-shadow:0 2px 8px rgba(0,0,0,.05);
                        ">
                            !
                        </div>



                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:#0f172a;line-height:1.4;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $numero }}
                            </div>

                            @if($cliente)
                                <div style="font-size:12px;color:#64748b;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $cliente }}
                                </div>
                            @endif
                        </div>

                        <div style="
                            font-size:11px;
                            font-weight:800;
                            color:#c2410c;
                            white-space:nowrap;
                            margin-top:2px;
                        ">
                            {{ $dias ?? '—' }}d
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="
                min-height:180px;
                display:flex;
                align-items:center;
                justify-content:center;
                text-align:center;
                color:#64748b;
                font-size:13px;
            ">
                Nenhum processo parado no momento.
            </div>
        @endif
    </div>
</div>


{{-- ETAPA 3 — BLOCO INFERIOR --}}
<div class="dash-blocos-inferiores" style="
    display:grid;
    grid-template-columns: 1.2fr 1fr 1fr;
    gap:16px;
    margin-bottom:20px;
">

    {{-- 1. ÚLTIMAS ATIVIDADES --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:20px;
        box-shadow:0 8px 20px rgba(15,23,42,.04);
        min-height:100%;
    ">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">📌 Últimas Atividades</div>
                <div style="font-size:12px;color:#64748b;">Movimentações recentes do sistema</div>
            </div>

            <a href="{{ route('processos') }}"
               style="
                    text-decoration:none;
                    font-size:12px;
                    font-weight:700;
                    color:#2563eb;
                    background:#eff6ff;
                    padding:8px 12px;
                    border-radius:999px;
               ">
                Ver todas
            </a>
        </div>

        @if(!empty($ultimasAtividades) && count($ultimasAtividades))
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($ultimasAtividades as $at)
                    @php
                        $usuario  = $at['usuario'] ?? 'Sistema';
                        $numero   = $at['numero'] ?? 'Processo';
                        $descricao = $at['descricao'] ?? 'Atualização registrada';
                        $quando   = $at['quando'] ?? '';
                        $processoId = $at['processo_id'] ?? null;
                        $inicial = strtoupper(substr($usuario, 0, 1));
                    @endphp

                    <div style="
                        display:flex;
                        gap:12px;
                        align-items:flex-start;
                        padding:12px;
                        border-radius:12px;
                        background:#f8fafc;
                        border:1px solid #e5e7eb;
                    ">
                        <div style="
                            width:40px;
                            height:40px;
                            border-radius:12px;
                            background:#ede9fe;
                            color:#6d28d9;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:13px;
                            font-weight:800;
                            flex-shrink:0;
                        ">
                            {{ $inicial }}
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;color:#0f172a;line-height:1.5;">
                                <strong>{{ $usuario }}</strong>
                                em
                                @if($processoId)
                                    <a href="{{ route('processos.show', $processoId) }}"
                                       style="color:#2563eb;font-weight:700;text-decoration:none;">
                                        {{ $numero }}
                                    </a>
                                @else
                                    <strong>{{ $numero }}</strong>
                                @endif
                            </div>

                            <div style="
                                font-size:12px;
                                color:#64748b;
                                margin-top:4px;
                                white-space:nowrap;
                                overflow:hidden;
                                text-overflow:ellipsis;
                            ">
                                {{ $descricao }}
                            </div>
                        </div>

                        <div style="
                            font-size:11px;
                            color:#94a3b8;
                            white-space:nowrap;
                            margin-top:2px;
                        ">
                            {{ $quando }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="
                min-height:180px;
                display:flex;
                align-items:center;
                justify-content:center;
                text-align:center;
                color:#64748b;
                font-size:13px;
            ">
                Nenhuma atividade recente.
            </div>
        @endif
    </div>

    {{-- 2. AGENDA DO DIA --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:20px;
        box-shadow:0 8px 20px rgba(15,23,42,.04);
        min-height:100%;
    ">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">📅 Agenda do Dia</div>
                <div style="font-size:12px;color:#64748b;">Compromissos e prazos de hoje</div>
            </div>

            <a href="{{ route('agenda') }}"
               style="
                    text-decoration:none;
                    font-size:12px;
                    font-weight:700;
                    color:#2563eb;
                    background:#eff6ff;
                    padding:8px 12px;
                    border-radius:999px;
               ">
                Ver agenda
            </a>
        </div>

        @if(!empty($agendaHoje) && count($agendaHoje))
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($agendaHoje as $ev)
                    @php
                        $hora = $ev['hora'] ?? '--:--';
                        $titulo = $ev['titulo'] ?? 'Compromisso';
                        $tipo = $ev['tipo'] ?? 'Evento';
                        $processo = $ev['processo'] ?? null;

                        $cor = '#2563eb';
                        $bg = '#eff6ff';

                        if ($tipo === 'Prazo') {
                            $cor = '#d97706';
                            $bg = '#fff7ed';
                        } elseif ($tipo === 'Audiência') {
                            $cor = '#7c3aed';
                            $bg = '#f5f3ff';
                        }
                    @endphp

                    <div style="
                        display:flex;
                        gap:12px;
                        align-items:flex-start;
                        padding:12px;
                        border-radius:12px;
                        background:{{ $bg }};
                        border:1px solid rgba(0,0,0,.04);
                    ">
                        <div style="
                            min-width:52px;
                            height:42px;
                            border-radius:12px;
                            background:#fff;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:12px;
                            font-weight:800;
                            color:{{ $cor }};
                            flex-shrink:0;
                            box-shadow:0 2px 8px rgba(0,0,0,.05);
                        ">
                            {{ $hora }}
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:#0f172a;line-height:1.4;">
                                {{ $titulo }}
                            </div>

                            @if($processo)
                                <div style="font-size:12px;color:#64748b;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $processo }}
                                </div>
                            @endif
                        </div>

                        <div style="
                            font-size:11px;
                            font-weight:800;
                            color:{{ $cor }};
                            white-space:nowrap;
                            margin-top:2px;
                        ">
                            {{ $tipo }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="
                min-height:180px;
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                text-align:center;
                color:#64748b;
                font-size:13px;
                gap:8px;
            ">
                <div style="font-size:22px;">✔</div>
                <div>Nenhum compromisso hoje.</div>
            </div>
        @endif
    </div>

    {{-- 3. RISCOS PRIORITÁRIOS --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:20px;
        box-shadow:0 8px 20px rgba(15,23,42,.04);
        min-height:100%;
    ">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">⚖️ Riscos Prioritários</div>
                <div style="font-size:12px;color:#64748b;">Processos com maior atenção</div>
            </div>

            <a href="{{ route('processos') }}"
               style="
                    text-decoration:none;
                    font-size:12px;
                    font-weight:700;
                    color:#dc2626;
                    background:#fef2f2;
                    padding:8px 12px;
                    border-radius:999px;
               ">
                Ver riscos
            </a>
        </div>

        @if(!empty($riscosPrioritarios) && count($riscosPrioritarios))
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($riscosPrioritarios as $r)
                    @php
                        $numero = is_array($r) ? ($r['numero'] ?? 'Processo') : ($r->numero ?? 'Processo');
                        $cliente = is_array($r) ? ($r['cliente'] ?? null) : ($r->cliente ?? null);
                        $grau = is_array($r) ? ($r['risco'] ?? 'Médio') : ($r->risco ?? 'Médio');

                        $cor = '#d97706';
                        $bg = '#fffbeb';

                        if (strtolower($grau) === 'alto') {
                            $cor = '#dc2626';
                            $bg = '#fef2f2';
                        } elseif (strtolower($grau) === 'baixo') {
                            $cor = '#16a34a';
                            $bg = '#f0fdf4';
                        }
                    @endphp

                    <div style="
                        display:flex;
                        gap:12px;
                        align-items:flex-start;
                        padding:12px;
                        border-radius:12px;
                        background:{{ $bg }};
                        border:1px solid rgba(0,0,0,.04);
                    ">
                        <div style="
                            width:40px;
                            height:40px;
                            border-radius:12px;
                            background:#fff;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:{{ $cor }};
                            font-size:16px;
                            font-weight:800;
                            flex-shrink:0;
                            box-shadow:0 2px 8px rgba(0,0,0,.05);
                        ">
                            ⚠
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:#0f172a;line-height:1.4;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $numero }}
                            </div>

                            @if($cliente)
                                <div style="font-size:12px;color:#64748b;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $cliente }}
                                </div>
                            @endif
                        </div>

                        <div style="
                            font-size:11px;
                            font-weight:800;
                            color:{{ $cor }};
                            background:#fff;
                            border-radius:999px;
                            padding:5px 9px;
                            white-space:nowrap;
                            margin-top:2px;
                        ">
                            {{ $grau }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="
                min-height:180px;
                display:flex;
                align-items:center;
                justify-content:center;
                text-align:center;
                color:#64748b;
                font-size:13px;
            ">
                Nenhum risco prioritário encontrado.
            </div>
        @endif
    </div>
</div>




<style>
@media (max-width: 1180px) {
    .dash-blocos-inferiores {
        grid-template-columns: 1fr !important;
    }
}
</style>



<style>
@media (max-width: 1100px) {
    .dash-blocos-centrais {
        grid-template-columns: 1fr !important;
    }
}
</style>




<style>
@media (max-width: 768px) {
    .dash-kpis   { grid-template-columns: 1fr 1fr !important; }
    .dash-acoes  { grid-template-columns: 1fr 1fr !important; }
    .dash-grid-3 { grid-template-columns: 1fr !important; }
    .dash-grid-2 { grid-template-columns: 1fr !important; }

    /* Card saudação empilha no mobile */
    .dash-saudacao { flex-wrap: wrap !important; }
    .dash-briefing { width: 100% !important; flex-shrink: unset !important; }
    .dash-briefing > div { width: 100% !important; }

    /* Topbar compacta */
    .topbar-search { max-width: 160px !important; }

    /* Tabelas com scroll */
    .table-wrap { overflow-x: auto !important; }
    table { min-width: 500px; }
}

@media (max-width: 480px) {
    .dash-kpis  { grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
    .dash-acoes { grid-template-columns: 1fr 1fr !important; gap: 6px !important; }

    /* KPI valores menores */
    .dash-kpis div[style*="font-size:30px"] { font-size: 22px !important; }
}
</style>


</div>

