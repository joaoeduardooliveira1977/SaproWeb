<div>

{{-- Cabeçalho --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;"><div style="display:flex;align-items:center;gap:8px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Produtividade por Advogado</div></h2>
        <p style="font-size:13px;color:#64748b;margin-top:4px;">
            Período: <strong>{{ $dataIniFmt }}</strong> a <strong>{{ $dataFimFmt }}</strong>
        </p>
    </div>
    <a href="{{ route('relatorios.produtividade-pdf', ['data_ini' => request('data_ini', now()->startOfMonth()->format('Y-m-d')), 'data_fim' => request('data_fim', now()->format('Y-m-d'))]) }}"
       target="_blank" class="btn btn-secondary btn-sm" style="align-self:flex-start;display:inline-flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Exportar PDF
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
            ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 9l9-6 9 6M3 9h18M7 21h10"/><path d="M5 9l2 6H3L5 9zM19 9l2 6h-4l2-6z"/></svg>',  'val'=> $totais['processos_ativos'],                          'label'=>'Processos Ativos',  'cor'=>'#1a3a5c'],
            ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',  'val'=> number_format($totais['total_horas'],1,',','.'),       'label'=>'Horas no Período',  'cor'=>'#0891b2'],
            ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',  'val'=> 'R$ '.number_format($totais['total_valor'],0,',','.'), 'label'=>'Valor Faturável',   'cor'=>'#16a34a'],
            ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',  'val'=> $totais['total_andamentos'],                           'label'=>'Andamentos',        'cor'=>'#7c3aed'],
            ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',  'val'=> $totais['prazos_cumpridos'],                           'label'=>'Prazos Cumpridos',  'cor'=>'#16a34a'],
            ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',  'val'=> $totais['prazos_perdidos'],                            'label'=>'Prazos Perdidos',   'cor'=>'#dc2626'],
        ];
    @endphp
    @foreach($kpis as $k)
    <div class="card" style="border-top:3px solid {{ $k['cor'] }};text-align:center;padding:14px 10px;">
        <div style="display:flex;justify-content:center;margin-bottom:4px;">{!! $k['icon'] !!}</div>
        <div style="font-size:20px;font-weight:700;color:{{ $k['cor'] }};">{{ $k['val'] }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $k['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Cards por advogado --}}
@if(empty($advogados))
<div class="card" style="text-align:center;padding:48px;color:var(--muted);">
    <div style="display:flex;justify-content:center;margin-bottom:12px;"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
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
                <div style="font-size:15px;font-weight:700;color:#fff;"><div style="display:flex;align-items:center;gap:8px;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> {{ $adv->nome }}</div></div>
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
                    ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>', 'val'=>$adv->processos_total,                                      'label'=>'Total Processos', 'cor'=>'#475569'],
                    ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>', 'val'=>number_format($adv->total_horas,1,',','.').'h',              'label'=>'Horas',           'cor'=>'#0891b2'],
                    ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>', 'val'=>'R$ '.number_format($adv->total_valor,0,',','.'),            'label'=>'Valor Faturável', 'cor'=>'#16a34a'],
                    ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>', 'val'=>$adv->total_apontamentos,                                    'label'=>'Apontamentos',    'cor'=>'#7c3aed'],
                    ['icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>', 'val'=>$adv->total_andamentos,                                      'label'=>'Andamentos',      'cor'=>'#0891b2'],
                ];
            @endphp
            @foreach($metricas as $i => $m)
            <div style="padding:14px 12px;text-align:center;{{ $i < 4 ? 'border-right:1px solid var(--border);' : '' }}">
                <div style="display:flex;justify-content:center;margin-bottom:2px;">{!! $m['icon'] !!}</div>
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
                <span style="background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> {{ $adv->prazos_cumpridos }} cumprido(s)
                </span>
                @if($adv->prazos_perdidos > 0)
                <span style="background:#fee2e2;color:#991b1b;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> {{ $adv->prazos_perdidos }} perdido(s)
                </span>
                @endif
                @if($adv->prazos_vencidos > 0)
                <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> {{ $adv->prazos_vencidos }} vencido(s) em aberto
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
