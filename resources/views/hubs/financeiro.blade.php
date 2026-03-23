@extends('layouts.app')
@section('page-title', 'Central Financeira')

@section('content')
@php
    // Métricas financeiras
    $aReceber        = (float) \App\Models\Recebimento::where('recebido', false)->sum('valor');
    $recebidoMes     = (float) \App\Models\Recebimento::where('recebido', true)
                            ->whereMonth('data_recebimento', now()->month)
                            ->whereYear('data_recebimento', now()->year)
                            ->sum('valor');
    $inadimplentes   = \App\Models\Recebimento::where('recebido', false)
                            ->where('data', '<', today())
                            ->distinct('processo_id')->count('processo_id');
    $honorariosPend  = (float) (\Illuminate\Support\Facades\DB::table('honorarios')
                            ->where('status', '!=', 'pago')
                            ->sum('valor_contrato') ?? 0);

    // Últimos recebimentos
    $ultimosRecebimentos = \App\Models\Recebimento::with('processo.cliente')
                            ->where('recebido', true)
                            ->orderByDesc('data_recebimento')
                            ->take(5)->get();

    // Inadimplência
    $inadimplenciaLista = \App\Models\Recebimento::with('processo.cliente')
                            ->where('recebido', false)
                            ->where('data', '<', today())
                            ->orderBy('data')
                            ->take(5)->get();

    // Receita últimos 6 meses
    $receitaMensal = collect(range(5, 0))->map(function($i) {
        $mes = now()->subMonths($i);
        return [
            'mes'   => $mes->locale('pt_BR')->isoFormat('MMM'),
            'valor' => (float) \App\Models\Recebimento::where('recebido', true)
                            ->whereYear('data_recebimento', $mes->year)
                            ->whereMonth('data_recebimento', $mes->month)
                            ->sum('valor'),
        ];
    });
    $maxReceita = $receitaMensal->max('valor') ?: 1;
@endphp

<div>

{{-- Cabeçalho --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Central Financeira</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Controle financeiro, honorários, inadimplência e relatórios.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('financeiro') }}"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#059669,#16a34a);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Ver Financeiro
        </a>
        <a href="{{ route('relatorios.index') }}"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Relatórios
        </a>
    </div>
</div>

{{-- KPIs coloridos --}}
<div class="hub-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
    @php
    $kpis = [
        [
            'label' => 'A Receber',
            'val'   => 'R$ '.number_format($aReceber, 2, ',', '.'),
            'bg'    => 'linear-gradient(135deg,#059669,#16a34a)',
            'route' => route('financeiro.consolidado'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        ],
        [
            'label' => 'Recebido no Mês',
            'val'   => 'R$ '.number_format($recebidoMes, 2, ',', '.'),
            'bg'    => 'linear-gradient(135deg,#1d4ed8,#2563a8)',
            'route' => route('financeiro.consolidado'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
        ],
        [
            'label' => 'Clientes Inadimplentes',
            'val'   => $inadimplentes,
            'bg'    => 'linear-gradient(135deg,#dc2626,#b91c1c)',
            'route' => route('inadimplencia'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        ],
        [
            'label' => 'Honorários Pendentes',
            'val'   => 'R$ '.number_format($honorariosPend, 2, ',', '.'),
            'bg'    => 'linear-gradient(135deg,#d97706,#b45309)',
            'route' => route('honorarios'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        ],
    ];
    @endphp
    @foreach($kpis as $k)
    <a href="{{ $k['route'] }}" style="text-decoration:none;">
        <div style="background:{{ $k['bg'] }};border-radius:14px;padding:22px 20px;color:#fff;cursor:pointer;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 15px rgba(0,0,0,.15);"
            onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,.2)'"
            onmouseout="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(0,0,0,.15)'">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                {!! $k['svg'] !!}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div style="font-size:24px;font-weight:800;margin-bottom:4px;letter-spacing:-1px;">{{ $k['val'] }}</div>
            <div style="font-size:13px;color:rgba(255,255,255,.8);font-weight:500;">{{ $k['label'] }}</div>
        </div>
    </a>
    @endforeach
</div>

{{-- Resumo Financeiro --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:20px 24px;margin-bottom:20px;">
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#059669,#16a34a);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:6px;">Resumo Financeiro</div>
            <div style="font-size:13px;color:var(--muted);line-height:1.7;">
                @if($inadimplentes > 0 || $aReceber > 0)
                    Você tem <strong style="color:#059669;">R$ {{ number_format($aReceber, 2, ',', '.') }} a receber</strong>
                    @if($inadimplentes > 0) e <strong style="color:#dc2626;">{{ $inadimplentes }} cliente(s) inadimplente(s)</strong>@endif.
                    @if($honorariosPend > 0) Há <strong style="color:#d97706;">R$ {{ number_format($honorariosPend, 2, ',', '.') }} em honorários pendentes</strong>.@endif
                @else
                    Tudo em ordem! Nenhuma pendência financeira registrada. 🎉
                @endif
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:12px;font-size:12px;color:var(--muted);">
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#059669" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#059669;">R$ {{ number_format($recebidoMes, 2, ',', '.') }}</strong>&nbsp;recebido este mês
                </span>
                @if($inadimplentes > 0)
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#dc2626" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#dc2626;">{{ $inadimplentes }}</strong>&nbsp;inadimplente(s)
                </span>
                @endif
                @if($honorariosPend > 0)
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#d97706" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#d97706;">R$ {{ number_format($honorariosPend, 2, ',', '.') }}</strong>&nbsp;em honorários pendentes
                </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Módulos Financeiros --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px;">
    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;">Módulos Financeiros</div>
    <div class="hub-modulos" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
            @php
            $modulos = [
                ['label'=>'Visão Geral',   'desc'=>'Resumo consolidado',      'cor'=>'#059669','bg'=>'#f0fdf4','route'=>route('financeiro.consolidado'), 'svg'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>'],
                ['label'=>'Por Processo',  'desc'=>'Receitas e despesas',     'cor'=>'#2563a8','bg'=>'#eff6ff','route'=>route('financeiro'),              'svg'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'],
                ['label'=>'Honorários',    'desc'=>'Gestão de cobranças',     'cor'=>'#d97706','bg'=>'#fffbeb','route'=>route('honorarios'),              'svg'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'],
                ['label'=>'Inadimplência', 'desc'=>'Pagamentos em atraso',    'cor'=>'#dc2626','bg'=>'#fef2f2','route'=>route('inadimplencia'),           'svg'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'],
                ['label'=>'Conciliação',   'desc'=>'Extratos bancários',      'cor'=>'#7c3aed','bg'=>'#f5f3ff','route'=>route('conciliacao-bancaria'),   'svg'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>'],
                ['label'=>'Relatórios',    'desc'=>'Exportações financeiras', 'cor'=>'#0891b2','bg'=>'#f0f9ff','route'=>route('relatorios.index'),       'svg'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>'],
            ];
            @endphp
            @foreach($modulos as $m)
            <a href="{{ $m['route'] }}" style="text-decoration:none;">
                <div style="display:flex;align-items:center;gap:12px;padding:18px;border-radius:10px;background:{{ $m['bg'] }};border:1.5px solid {{ $m['cor'] }}22;transition:all .15s;cursor:pointer;"
                    onmouseover="this.style.borderColor='{{ $m['cor'] }}';this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.borderColor='{{ $m['cor'] }}22';this.style.transform=''">
                    <div style="width:40px;height:40px;border-radius:10px;background:#fff;color:{{ $m['cor'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                        {!! $m['svg'] !!}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:{{ $m['cor'] }};">{{ $m['label'] }}</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $m['desc'] }}</div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

{{-- Últimos Recebimentos + Inadimplência --}}
<div class="hub-bottom hub-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- Últimos Recebimentos --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Últimos Recebimentos
            </div>
            <a href="{{ route('financeiro.consolidado') }}" style="font-size:12px;color:var(--primary);text-decoration:none;">Ver todos...</a>
        </div>
        @if($ultimosRecebimentos->isEmpty())
        <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">Nenhum recebimento registrado</div>
        @else
        <div style="display:flex;flex-direction:column;">
            @foreach($ultimosRecebimentos as $rec)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);">
                <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;color:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $rec->processo?->cliente?->nome ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:var(--muted);">
                        {{ $rec->processo?->numero ?? '—' }} · {{ $rec->data_recebimento?->format('d/m/Y') }}
                    </div>
                </div>
                <div style="font-size:13px;font-weight:700;color:#059669;flex-shrink:0;">
                    R$ {{ number_format($rec->valor, 2, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Inadimplência --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Inadimplência
            </div>
            <a href="{{ route('inadimplencia') }}" style="font-size:12px;color:var(--primary);text-decoration:none;">Ver todos...</a>
        </div>
        @if($inadimplenciaLista->isEmpty())
        <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">✅ Nenhuma inadimplência registrada</div>
        @else
        <div style="display:flex;flex-direction:column;">
            @foreach($inadimplenciaLista as $rec)
            @php $diasAtraso = (int) now()->diffInDays($rec->data, false) * -1; @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);">
                <div style="width:36px;height:36px;border-radius:8px;background:#fef2f2;color:#dc2626;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    {{ $diasAtraso }}d
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $rec->processo?->cliente?->nome ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:var(--muted);">
                        Venceu em {{ $rec->data?->format('d/m/Y') }}
                    </div>
                </div>
                <div style="font-size:13px;font-weight:700;color:#dc2626;flex-shrink:0;">
                    R$ {{ number_format($rec->valor, 2, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

</div>

<style>
@media (max-width: 1024px) {
    .hub-kpis                { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-modulos             { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-bottom, .hub-grid-2 { grid-template-columns: 1fr !important; }
}
@media (max-width: 640px) {
    .hub-kpis    { grid-template-columns: 1fr 1fr !important; }
    .hub-modulos { grid-template-columns: 1fr 1fr !important; }
}
</style>
@endsection
