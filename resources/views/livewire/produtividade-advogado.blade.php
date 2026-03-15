<div>

{{-- Cabeçalho --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">👨‍⚖️ Produtividade por Advogado</h2>
        <p style="font-size:13px;color:#64748b;margin-top:4px;">
            Período: <strong>{{ $dataIniFmt }}</strong> a <strong>{{ $dataFimFmt }}</strong>
        </p>
    </div>
    <a href="{{ route('relatorios.produtividade-pdf', ['data_ini' => request('data_ini', now()->startOfMonth()->format('Y-m-d')), 'data_fim' => request('data_fim', now()->format('Y-m-d'))]) }}"
       target="_blank" class="btn btn-secondary btn-sm" style="align-self:flex-start;">
        🖨️ Exportar PDF
    </a>
</div>

{{-- Filtro de período --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-right:4px;">Período:</span>
        @foreach([
            'mes'          => 'Este mês',
            'mes_anterior' => 'Mês anterior',
            'trimestre'    => 'Trimestre',
            'semestre'     => 'Semestre',
            'ano'          => 'Este ano',
            'custom'       => 'Personalizado',
        ] as $val => $label)
        <button wire:click="$set('periodo','{{ $val }}')" type="button"
            style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;border:1.5px solid {{ $periodo === $val ? '#1a3a5c' : 'var(--border)' }};background:{{ $periodo === $val ? '#1a3a5c' : '#fff' }};color:{{ $periodo === $val ? '#fff' : 'var(--text)' }};">
            {{ $label }}
        </button>
        @endforeach

        @if($periodo === 'custom')
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-left:8px;">
            <input wire:model.live="dataIni" type="date"
                style="padding:6px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;">
            <span style="color:var(--muted);font-size:12px;">até</span>
            <input wire:model.live="dataFim" type="date"
                style="padding:6px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;">
        </div>
        @endif
    </div>
</div>

{{-- KPIs consolidados --}}
<div class="stat-grid">
    @php
        $kpis = [
            ['icon'=>'⚖️',  'val'=> $totais['processos_ativos'],                          'label'=>'Processos Ativos',  'cor'=>'#1a3a5c'],
            ['icon'=>'⏱️',  'val'=> number_format($totais['total_horas'],1,',','.'),       'label'=>'Horas no Período',  'cor'=>'#0891b2'],
            ['icon'=>'💰',  'val'=> 'R$ '.number_format($totais['total_valor'],0,',','.'), 'label'=>'Valor Faturável',   'cor'=>'#16a34a'],
            ['icon'=>'📋',  'val'=> $totais['total_andamentos'],                           'label'=>'Andamentos',        'cor'=>'#7c3aed'],
            ['icon'=>'✅',  'val'=> $totais['prazos_cumpridos'],                           'label'=>'Prazos Cumpridos',  'cor'=>'#16a34a'],
            ['icon'=>'❌',  'val'=> $totais['prazos_perdidos'],                            'label'=>'Prazos Perdidos',   'cor'=>'#dc2626'],
        ];
    @endphp
    @foreach($kpis as $k)
    <div class="card" style="border-top:3px solid {{ $k['cor'] }};text-align:center;padding:14px 10px;">
        <div style="font-size:22px;margin-bottom:4px;">{{ $k['icon'] }}</div>
        <div style="font-size:20px;font-weight:700;color:{{ $k['cor'] }};">{{ $k['val'] }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $k['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Cards por advogado --}}
@if(empty($advogados))
<div class="card" style="text-align:center;padding:48px;color:var(--muted);">
    <div style="font-size:40px;margin-bottom:12px;">👨‍⚖️</div>
    <div>Nenhum advogado ativo cadastrado.</div>
</div>
@else
<div style="display:flex;flex-direction:column;gap:16px;">
    @foreach($advogados as $adv)
    @php
        $prazosTotal    = max(1, $adv->prazos_cumpridos + $adv->prazos_perdidos + $adv->prazos_vencidos);
        $taxaCumprimento = $adv->prazos_cumpridos + $adv->prazos_perdidos > 0
            ? round($adv->prazos_cumpridos / ($adv->prazos_cumpridos + $adv->prazos_perdidos) * 100)
            : null;
        $corTaxa = $taxaCumprimento === null ? '#94a3b8'
                 : ($taxaCumprimento >= 90 ? '#16a34a' : ($taxaCumprimento >= 70 ? '#d97706' : '#dc2626'));
    @endphp
    <div class="card" style="padding:0;overflow:hidden;">
        {{-- Cabeçalho do card --}}
        <div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="font-size:15px;font-weight:700;color:#fff;">👨‍⚖️ {{ $adv->nome }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:2px;">
                    @if($adv->oab) OAB {{ $adv->oab }} @endif
                    @if($adv->email) · {{ $adv->email }} @endif
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:22px;font-weight:700;color:#fff;">{{ $adv->processos_ativos }}</div>
                <div style="font-size:10px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.5px;">processos ativos</div>
            </div>
        </div>

        {{-- Métricas --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(90px,1fr));gap:0;border-bottom:1px solid var(--border);">
            @php
                $metricas = [
                    ['icon'=>'📁', 'val'=>$adv->processos_total,                                      'label'=>'Total Processos', 'cor'=>'#475569'],
                    ['icon'=>'⏱️', 'val'=>number_format($adv->total_horas,1,',','.').'h',              'label'=>'Horas',           'cor'=>'#0891b2'],
                    ['icon'=>'💰', 'val'=>'R$ '.number_format($adv->total_valor,0,',','.'),            'label'=>'Valor Faturável', 'cor'=>'#16a34a'],
                    ['icon'=>'📋', 'val'=>$adv->total_apontamentos,                                    'label'=>'Apontamentos',    'cor'=>'#7c3aed'],
                    ['icon'=>'📝', 'val'=>$adv->total_andamentos,                                      'label'=>'Andamentos',      'cor'=>'#0891b2'],
                ];
            @endphp
            @foreach($metricas as $i => $m)
            <div style="padding:14px 12px;text-align:center;{{ $i < 4 ? 'border-right:1px solid var(--border);' : '' }}">
                <div style="font-size:18px;margin-bottom:2px;">{{ $m['icon'] }}</div>
                <div style="font-size:16px;font-weight:700;color:{{ $m['cor'] }};">{{ $m['val'] }}</div>
                <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;">{{ $m['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Prazos --}}
        <div style="padding:14px 20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="font-size:12px;font-weight:600;color:var(--muted);min-width:60px;">PRAZOS</div>

            {{-- Barra de cumprimento --}}
            @if($adv->prazos_cumpridos + $adv->prazos_perdidos > 0)
            <div style="flex:1;min-width:200px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:4px;">
                    <span>Taxa de cumprimento</span>
                    <span style="font-weight:700;color:{{ $corTaxa }};">{{ $taxaCumprimento }}%</span>
                </div>
                <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:{{ $taxaCumprimento }}%;background:{{ $corTaxa }};border-radius:4px;transition:width .4s;"></div>
                </div>
            </div>
            @endif

            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <span style="background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    ✅ {{ $adv->prazos_cumpridos }} cumprido(s)
                </span>
                @if($adv->prazos_perdidos > 0)
                <span style="background:#fee2e2;color:#991b1b;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    ❌ {{ $adv->prazos_perdidos }} perdido(s)
                </span>
                @endif
                @if($adv->prazos_vencidos > 0)
                <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                    ⚠️ {{ $adv->prazos_vencidos }} vencido(s) em aberto
                </span>
                @endif
                @if($adv->prazos_total === 0)
                <span style="color:var(--muted);font-size:12px;font-style:italic;">Nenhum prazo atribuído</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>
