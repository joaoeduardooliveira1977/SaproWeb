<div x-data="{ lAberto: false, lPasso: 'main' }"
     @lancamento-passo.window="lPasso = $event.detail.passo; lAberto = true">

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Financeiro Consolidado</h1>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            Visão geral de recebimentos, despesas, fluxo de caixa e honorários
            <span style="color:#cbd5e1;margin:0 6px;">|</span>
            <a href="{{ route('financeiro.hub') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Voltar para central</a>
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button onclick="window.print()"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#fff;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;color:var(--text);cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Imprimir
        </button>
        <a href="{{ route('relatorios.financeiro-periodo') }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#fff;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;color:var(--text);text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Gerar relatório
        </a>
        <button @click="lAberto=true; lPasso='main'"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:var(--primary);color:#fff;border-radius:8px;font-size:13px;font-weight:700;border:none;cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo lançamento
        </button>
    </div>
</div>

{{-- ── Resumo Inteligente + Card Saldo ── --}}
@php $saldo = $metricas['a_receber'] + $metricas['honorarios_vencer'] - $metricas['a_pagar']; @endphp
<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;margin-bottom:24px;" class="fin-resumo-grid">

    {{-- Alertas inteligentes --}}
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
        <div style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:16px;">Resumo Inteligente</div>
        <div style="display:flex;flex-direction:column;gap:10px;">

            <div style="background:{{ $saldo >= 0 ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $saldo >= 0 ? '#86efac' : '#fca5a5' }};border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:{{ $saldo >= 0 ? '#16a34a' : '#dc2626' }};">
                        Você tem <strong>R$ {{ number_format(abs($saldo), 2, ',', '.') }}</strong> de saldo projetado.
                    </div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Projeção {{ $saldo >= 0 ? 'positiva' : 'negativa' }} para os próximos 30 dias.</div>
                </div>
                <span style="font-size:20px;">{{ $saldo >= 0 ? '📈' : '📉' }}</span>
            </div>

            @if($metricas['a_receber'] > 0)
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#d97706;">
                        Existem <strong>R$ {{ number_format($metricas['a_receber'], 2, ',', '.') }}</strong> a receber.
                    </div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Priorize cobrança dos títulos mais próximos do vencimento.</div>
                </div>
                <span style="font-size:20px;">💰</span>
            </div>
            @endif

            @if($metricas['honorarios_atrasados'] > 0)
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#dc2626;">
                        Há <strong>R$ {{ number_format($metricas['honorarios_atrasados'], 2, ',', '.') }}</strong> em honorários atrasados.
                    </div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Ação necessária para reduzir inadimplência.</div>
                </div>
                <span style="font-size:20px;">⚠️</span>
            </div>
            @endif

            @if($metricas['a_receber'] == 0 && $metricas['honorarios_atrasados'] == 0)
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px 16px;text-align:center;color:#16a34a;font-size:13px;font-weight:600;">
                ✅ Nenhuma pendência financeira crítica no momento!
            </div>
            @endif
        </div>
    </div>

    {{-- Card escuro saldo --}}
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;color:var(--text);display:flex;flex-direction:column;justify-content:space-between;">
        <div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:8px;">Saldo Projetado</div>
            <div style="font-size:30px;font-weight:800;letter-spacing:-1px;margin-bottom:6px;color:{{ $saldo >= 0 ? '#16a34a' : '#dc2626' }};">
                R$ {{ number_format(abs($saldo), 2, ',', '.') }}
            </div>
            <div style="font-size:12px;color:var(--muted);">Receitas + honorários - despesas previstas</div>
        </div>
        <div style="display:flex;gap:10px;margin-top:20px;">
            <a href="{{ route('inadimplencia') }}"
                style="flex:1;text-align:center;padding:10px;background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;">
                Cobrar agora
            </a>
            <a href="{{ route('financeiro.consolidado') }}"
                style="flex:1;text-align:center;padding:10px;background:#fff;color:var(--text);border:1.5px solid var(--border);border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
                Ver contas
            </a>
        </div>
    </div>
</div>

{{-- ── 6 KPIs ── --}}
@php
$kpis = [
    ['label'=>'A receber',           'val'=>$metricas['a_receber'],           'cor'=>'#16a34a', 'tag'=>'Recebimentos em aberto',    'tag_cor'=>'#16a34a'],
    ['label'=>'Recebido este mês',   'val'=>$metricas['recebido_mes'],        'cor'=>'#2563a8', 'tag'=>'Competência ' . now()->translatedFormat('M/Y'), 'tag_cor'=>'#64748b'],
    ['label'=>'A pagar',             'val'=>$metricas['a_pagar'],             'cor'=>'#dc2626', 'tag'=>'Despesas pendentes',        'tag_cor'=>'#dc2626'],
    ['label'=>'Pago este mês',       'val'=>$metricas['pago_mes'],            'cor'=>'#d97706', 'tag'=>'Saídas registradas',        'tag_cor'=>'#d97706'],
    ['label'=>'Honorários atrasados','val'=>$metricas['honorarios_atrasados'],'cor'=>'#7c3aed', 'tag'=>'● Ação necessária',        'tag_cor'=>'#7c3aed'],
    ['label'=>'Honorários a vencer', 'val'=>$metricas['honorarios_vencer'],  'cor'=>'#0891b2', 'tag'=>'↑ Carteira futura',        'tag_cor'=>'#0891b2'],
];
@endphp
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:24px;" class="fin-kpis">
    @foreach($kpis as $k)
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:16px;border-top:3px solid {{ $k['cor'] }};">
        <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">{{ $k['label'] }}</div>
        <div style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:8px;">R$ {{ number_format($k['val'], 2, ',', '.') }}</div>
        <div style="font-size:11px;color:{{ $k['tag_cor'] }};font-weight:600;">{{ $k['tag'] }}</div>
    </div>
    @endforeach
</div>

{{-- ── Abas de navegação ── --}}
@php
$abas = [
    ['id'=>'visao',      'label'=>'📊 Visão Geral',         'count'=>null],
    ['id'=>'fluxo',      'label'=>'📈 Fluxo de Caixa',      'count'=>null],
    ['id'=>'receber',    'label'=>'💰 A Receber',            'count'=>$aReceberCount],
    ['id'=>'pagar',      'label'=>'💸 A Pagar',              'count'=>$aPagarCount],
    ['id'=>'honorarios', 'label'=>'⚠️ Honorários Atrasados', 'count'=>$inadimplentesCount],
];
@endphp
<div style="display:flex;gap:4px;background:#f8fafc;border-radius:12px;padding:4px;margin-bottom:20px;overflow-x:auto;">
    @foreach($abas as $aba)
    <button wire:click="$set('abaAtiva', '{{ $aba['id'] }}')"
        style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;transition:all .15s;
        background:{{ $abaAtiva === $aba['id'] ? '#fff' : 'transparent' }};
        color:{{ $abaAtiva === $aba['id'] ? '#1d4ed8' : '#64748b' }};
        box-shadow:{{ $abaAtiva === $aba['id'] ? '0 1px 4px rgba(0,0,0,.1)' : 'none' }};">
        {{ $aba['label'] }}
        @if(!empty($aba['count']))
        <span style="background:#dc2626;color:#fff;border-radius:99px;padding:1px 6px;font-size:10px;font-weight:800;">{{ $aba['count'] }}</span>
        @endif
    </button>
    @endforeach
</div>

{{-- ══ ABA: VISÃO GERAL ══════════════════════════════════════════ --}}
@if($abaAtiva === 'visao')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="fin-visao">

    {{-- Resumo Financeiro --}}
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:8px;">
            <div style="font-size:16px;font-weight:800;color:var(--text);">Resumo Financeiro</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                <a href="{{ route('financeiro.consolidado') }}" style="padding:6px 12px;background:#f1f5f9;border-radius:8px;font-size:12px;font-weight:600;color:#475569;text-decoration:none;">Ver contas</a>
                <a href="{{ route('inadimplencia') }}" style="padding:6px 12px;background:#f1f5f9;border-radius:8px;font-size:12px;font-weight:600;color:#475569;text-decoration:none;">Cobrar</a>
                <a href="{{ route('financeiro') }}" style="padding:6px 12px;background:var(--primary);border-radius:8px;font-size:12px;font-weight:700;color:#fff;text-decoration:none;">+ Registrar</a>
            </div>
        </div>

        @php
        $linhas = [
            ['label'=>'Recebimentos em aberto', 'desc'=>'Títulos pendentes de clientes',       'val'=>$metricas['a_receber'],          'cor'=>'#16a34a', 'btn'=>'Ver lista',   'rota'=>route('financeiro.consolidado')],
            ['label'=>'Recebido este mês',       'desc'=>'Total efetivamente recebido',          'val'=>$metricas['recebido_mes'],       'cor'=>null,      'btn'=>'Detalhar',    'rota'=>route('financeiro.consolidado')],
            ['label'=>'Despesas em aberto',      'desc'=>'Compromissos ainda não quitados',     'val'=>$metricas['a_pagar'],            'cor'=>'#dc2626', 'btn'=>'Pagar',       'rota'=>route('financeiro')],
            ['label'=>'Pago este mês',           'desc'=>'Saídas já registradas',               'val'=>$metricas['pago_mes'],           'cor'=>null,      'btn'=>'Detalhar',    'rota'=>route('financeiro.consolidado')],
            ['label'=>'Honorários atrasados',    'desc'=>'Valores que exigem atenção imediata', 'val'=>$metricas['honorarios_atrasados'],'cor'=>'#7c3aed','btn'=>'Cobrar agora','rota'=>route('inadimplencia')],
            ['label'=>'Honorários a vencer',     'desc'=>'Receita futura prevista',             'val'=>$metricas['honorarios_vencer'],  'cor'=>null,      'btn'=>'Ver agenda',  'rota'=>route('honorarios')],
        ];
        @endphp

        <div style="display:flex;flex-direction:column;">
            @foreach($linhas as $linha)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 0;border-bottom:1px solid var(--border);">
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $linha['label'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $linha['desc'] }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="font-size:14px;font-weight:800;color:{{ $linha['cor'] ?? 'var(--text)' }};">
                        R$ {{ number_format($linha['val'], 2, ',', '.') }}
                    </div>
                    <a href="{{ $linha['rota'] }}"
                        style="padding:5px 10px;background:{{ $linha['cor'] ? $linha['cor'].'18' : '#f1f5f9' }};color:{{ $linha['cor'] ?? '#475569' }};border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;white-space:nowrap;">
                        {{ $linha['btn'] }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Prioridades + Agenda --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Prioridades de Hoje --}}
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div style="font-size:16px;font-weight:800;color:var(--text);">Prioridades de Hoje</div>
                <span style="background:#fef2f2;color:#dc2626;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;">
                    {{ count($prioridades) }} ações
                </span>
            </div>
            @forelse($prioridades as $p)
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:11px 12px;border:1px solid var(--border);border-radius:10px;margin-bottom:8px;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $p['cliente'] }} — R$ {{ number_format($p['valor'], 2, ',', '.') }}
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $p['descricao'] }}</div>
                </div>
                <span style="flex-shrink:0;margin-left:10px;font-size:10px;font-weight:700;white-space:nowrap;padding:3px 8px;border-radius:6px;
                    background:{{ $p['urgencia'] === 'vencido' ? '#fef2f2' : '#fffbeb' }};
                    color:{{ $p['urgencia'] === 'vencido' ? '#dc2626' : '#d97706' }};">
                    {{ $p['tag'] }}
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">
                ✅ Nenhuma pendência crítica hoje.
            </div>
            @endforelse
        </div>

        {{-- Agenda Financeira --}}
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div style="font-size:16px;font-weight:800;color:var(--text);">Agenda Financeira</div>
                <span style="background:#f1f5f9;color:#64748b;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:600;">Próximos 7 dias</span>
            </div>
            @forelse($agendaFinanceira as $item)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 12px;border:1px solid var(--border);border-radius:10px;margin-bottom:8px;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:var(--text);">{{ $item['data'] }} — {{ $item['titulo'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $item['descricao'] }}</div>
                </div>
                <span style="flex-shrink:0;margin-left:10px;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;
                    background:{{ $item['tipo'] === 'entrada' ? '#f0fdf4' : '#fef2f2' }};
                    color:{{ $item['tipo'] === 'entrada' ? '#16a34a' : '#dc2626' }};">
                    {{ strtoupper($item['tipo']) }}
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:16px;color:var(--muted);font-size:13px;">
                Nenhum compromisso financeiro nos próximos 7 dias.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- ══ ABA: FLUXO DE CAIXA ══════════════════════════════════════ --}}
@if($abaAtiva === 'fluxo')
@php
    $labels     = collect($fluxo)->pluck('label');
    $recebidos  = collect($fluxo)->pluck('recebido');
    $honorarios = collect($fluxo)->pluck('honorarios');
    $pagos      = collect($fluxo)->pluck('pago');
    $saldos     = collect($fluxo)->pluck('saldo');
@endphp

<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:16px;font-weight:800;color:var(--text);">
            Fluxo de Caixa — Últimos {{ $periodoFluxo }} meses
        </div>
        <div style="display:flex;gap:6px;">
            @foreach([3 => '3m', 6 => '6m', 12 => '12m'] as $val => $label)
            <button wire:click="$set('periodoFluxo', {{ $val }})"
                style="padding:6px 14px;border-radius:8px;border:1.5px solid var(--border);font-size:12px;font-weight:600;cursor:pointer;
                background:{{ $periodoFluxo == $val ? '#1d4ed8' : '#fff' }};
                color:{{ $periodoFluxo == $val ? '#fff' : 'var(--muted)' }};">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
    <div style="position:relative;height:280px;">
        <canvas id="chartFluxo"></canvas>
    </div>
</div>

<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;">
    <div style="padding:20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:16px;font-weight:800;color:var(--text);">Detalhamento mensal</div>
        <button wire:click="exportarCsv" wire:loading.attr="disabled"
            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;color:#475569;cursor:pointer;">
            <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando…</span>
        </button>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Mês</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Recebimentos</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Honorários</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Despesas</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fluxo as $linha)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;font-weight:600;">{{ $linha['label'] }}</td>
                    <td style="padding:12px 16px;text-align:right;color:#16a34a;font-weight:600;">R$ {{ number_format($linha['recebido'],2,',','.') }}</td>
                    <td class="hide-sm" style="padding:12px 16px;text-align:right;color:#0891b2;font-weight:600;">R$ {{ number_format($linha['honorarios'],2,',','.') }}</td>
                    <td style="padding:12px 16px;text-align:right;color:#dc2626;font-weight:600;">R$ {{ number_format($linha['pago'],2,',','.') }}</td>
                    <td style="padding:12px 16px;text-align:right;font-weight:800;color:{{ $linha['saldo'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        R$ {{ number_format($linha['saldo'],2,',','.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;font-weight:700;">
                    <td style="padding:12px 16px;">Total</td>
                    <td style="padding:12px 16px;text-align:right;color:#16a34a;">R$ {{ number_format(collect($fluxo)->sum('recebido'),2,',','.') }}</td>
                    <td class="hide-sm" style="padding:12px 16px;text-align:right;color:#0891b2;">R$ {{ number_format(collect($fluxo)->sum('honorarios'),2,',','.') }}</td>
                    <td style="padding:12px 16px;text-align:right;color:#dc2626;">R$ {{ number_format(collect($fluxo)->sum('pago'),2,',','.') }}</td>
                    <td style="padding:12px 16px;text-align:right;color:{{ collect($fluxo)->sum('saldo') >= 0 ? '#16a34a' : '#dc2626' }};">
                        R$ {{ number_format(collect($fluxo)->sum('saldo'),2,',','.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('chartFluxo');
    if (!ctx) return;
    new Chart(ctx, {
        data: {
            labels: @json($labels),
            datasets: [
                { type:'bar',  label:'Recebimentos', data:@json($recebidos),  backgroundColor:'#16a34a33', borderColor:'#16a34a', borderWidth:2, borderRadius:5 },
                { type:'bar',  label:'Honorários',   data:@json($honorarios), backgroundColor:'#0891b233', borderColor:'#0891b2', borderWidth:2, borderRadius:5 },
                { type:'bar',  label:'Despesas',      data:@json($pagos),      backgroundColor:'#dc262633', borderColor:'#dc2626', borderWidth:2, borderRadius:5 },
                { type:'line', label:'Saldo',          data:@json($saldos),    borderColor:'#e8a020', backgroundColor:'#e8a02022', borderWidth:2.5, pointBackgroundColor:'#e8a020', pointRadius:5, tension:0.3, fill:true, yAxisID:'ySaldo' },
            ],
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            interaction:{ mode:'index', intersect:false },
            plugins: {
                legend:{ position:'top', labels:{ font:{ size:12 }, padding:16 } },
                tooltip:{ callbacks:{ label: ctx => ' R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits:2 }) } },
            },
            scales: {
                x:{ grid:{ display:false } },
                y:{ position:'left', ticks:{ callback: v => 'R$ '+(v/1000).toFixed(0)+'k', font:{size:11} }, grid:{color:'#f1f5f9'} },
                ySaldo:{ position:'right', grid:{ drawOnChartArea:false }, ticks:{ callback: v => 'R$ '+(v/1000).toFixed(0)+'k', font:{size:11} } },
            },
        },
    });
})();
</script>
@endif

{{-- ══ ABA: CONTAS A RECEBER ═════════════════════════════════════ --}}
@if($abaAtiva === 'receber')
<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;">
    <div style="padding:20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:var(--text);">Contas a Receber</div>
            @if($receber)<span style="font-size:12px;color:var(--muted);">{{ $receber->total() }} registro(s)</span>@endif
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <input type="month" wire:model.live="filtroMes"
                style="padding:6px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;">
            <select wire:model.live="filtroStatus"
                style="padding:6px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;">
                <option value="pendente">Apenas pendentes</option>
                <option value="todos">Todos</option>
            </select>
            <button wire:click="exportarCsv" wire:loading.attr="disabled"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;color:#475569;cursor:pointer;">
                <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </span>
                <span wire:loading wire:target="exportarCsv">…</span>
            </button>
            <a href="{{ route('financeiro') }}"
                style="padding:7px 14px;background:#16a34a;color:#fff;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                + Novo
            </a>
        </div>
    </div>
    @if($receber && $receber->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:40px;">✅ Nenhum recebimento pendente.</p>
    @else
    <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Data</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Processo</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Cliente</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Descrição</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Valor</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($receber ?? [] as $row)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;white-space:nowrap;font-size:13px;">
                        {{ $row->data ? \Carbon\Carbon::parse($row->data)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="hide-sm" style="padding:12px 16px;font-family:monospace;font-size:11px;color:var(--muted);">{{ $row->processo_numero ?? '—' }}</td>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;">{{ $row->cliente_nome ?? '—' }}</td>
                    <td class="hide-sm" style="padding:12px 16px;font-size:12px;color:var(--muted);">{{ $row->descricao ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:right;font-size:14px;font-weight:800;color:#16a34a;">
                        R$ {{ number_format($row->valor,2,',','.') }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($row->recebido)
                            <span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#dcfce7;color:#166534;">Recebido</span>
                        @else
                            <span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#fef9c3;color:#854d0e;">Pendente</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!$row->recebido)
                        <button wire:click="marcarRecebido({{ $row->id }})"
                            wire:confirm="Marcar como recebido hoje?"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Recebido
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($receber)
    <div style="padding:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
        <p style="font-size:12px;color:var(--muted);margin:0;">
            Total filtrado: <strong style="color:#16a34a;">R$ {{ number_format($this->queryReceber()->sum('r.valor'),2,',','.') }}</strong>
        </p>
        {{ $receber->links() }}
    </div>
    @endif
    @endif
</div>
@endif

{{-- ══ ABA: CONTAS A PAGAR ═══════════════════════════════════════ --}}
@if($abaAtiva === 'pagar')
<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;">
    <div style="padding:20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:var(--text);">Contas a Pagar</div>
            @if($pagar)<span style="font-size:12px;color:var(--muted);">{{ $pagar->total() }} registro(s)</span>@endif
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <input type="month" wire:model.live="filtroMes"
                style="padding:6px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;">
            <select wire:model.live="filtroStatus"
                style="padding:6px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;">
                <option value="pendente">Apenas pendentes</option>
                <option value="todos">Todos</option>
            </select>
            <button wire:click="exportarCsv" wire:loading.attr="disabled"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;color:#475569;cursor:pointer;">
                <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </span>
                <span wire:loading wire:target="exportarCsv">…</span>
            </button>
        </div>
    </div>
    @if($pagar && $pagar->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:40px;">✅ Nenhuma despesa pendente.</p>
    @else
    <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Vencimento</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Processo</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Cliente</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Fornecedor</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Descrição</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Valor</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagar ?? [] as $row)
                @php $vencido = !$row->pago && $row->data_vencimento && \Carbon\Carbon::parse($row->data_vencimento)->isPast(); @endphp
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;white-space:nowrap;font-size:13px;{{ $vencido ? 'color:#dc2626;font-weight:600;' : '' }}">
                        {{ $row->data_vencimento ? \Carbon\Carbon::parse($row->data_vencimento)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="hide-sm" style="padding:12px 16px;font-family:monospace;font-size:11px;color:var(--muted);">{{ $row->processo_numero ?? '—' }}</td>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;">{{ $row->cliente_nome ?? '—' }}</td>
                    <td class="hide-sm" style="padding:12px 16px;font-size:12px;color:var(--muted);">{{ $row->fornecedor_nome ?? '—' }}</td>
                    <td class="hide-sm" style="padding:12px 16px;font-size:12px;color:var(--muted);">{{ $row->descricao ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:right;font-size:14px;font-weight:800;color:#dc2626;">
                        R$ {{ number_format($row->valor,2,',','.') }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($row->pago)
                            <span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#dcfce7;color:#166534;">Pago</span>
                        @elseif($vencido)
                            <span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#fee2e2;color:#991b1b;">Vencido</span>
                        @else
                            <span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#fef9c3;color:#854d0e;">Pendente</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!$row->pago)
                        <button wire:click="marcarPago({{ $row->id }})"
                            wire:confirm="Marcar como pago hoje?"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:#fef9c3;color:#854d0e;border:1px solid #fde68a;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Pago
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($pagar)
    <div style="padding:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
        <p style="font-size:12px;color:var(--muted);margin:0;">
            Total filtrado: <strong style="color:#dc2626;">R$ {{ number_format($this->queryPagar()->sum('p.valor'),2,',','.') }}</strong>
        </p>
        {{ $pagar->links() }}
    </div>
    @endif
    @endif
</div>
@endif

{{-- ══ ABA: HONORÁRIOS ATRASADOS ════════════════════════════════ --}}
@if($abaAtiva === 'honorarios')
<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;">
    <div style="padding:20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:var(--text);">Honorários em Atraso</div>
            @if($honAtrasados)<span style="font-size:12px;color:var(--muted);">{{ $honAtrasados->total() }} parcela(s)</span>@endif
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button wire:click="exportarCsv" wire:loading.attr="disabled"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;color:#475569;cursor:pointer;">
                <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </span>
                <span wire:loading wire:target="exportarCsv">…</span>
            </button>
            <a href="{{ route('inadimplencia') }}"
                style="padding:7px 16px;background:#dc2626;color:#fff;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                Cobrar todos
            </a>
        </div>
    </div>
    @if($honAtrasados && $honAtrasados->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:40px;">✅ Nenhum honorário atrasado.</p>
    @else
    <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Vencimento</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Atraso</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Cliente</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Processo</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Contrato</th>
                    <th class="hide-xs" style="padding:12px 16px;text-align:center;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Parcela</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($honAtrasados ?? [] as $row)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;white-space:nowrap;color:#dc2626;font-weight:700;font-size:13px;">
                        {{ \Carbon\Carbon::parse($row->vencimento)->format('d/m/Y') }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:800;background:#fee2e2;color:#991b1b;">
                            {{ $row->dias_atraso }} dia(s)
                        </span>
                    </td>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;">{{ $row->cliente_nome ?? '—' }}</td>
                    <td class="hide-sm" style="padding:12px 16px;font-family:monospace;font-size:11px;color:var(--muted);">{{ $row->processo_numero ?? '—' }}</td>
                    <td class="hide-sm" style="padding:12px 16px;font-size:12px;color:var(--muted);">{{ $row->honorario_descricao ?? '—' }}</td>
                    <td class="hide-xs" style="padding:12px 16px;text-align:center;font-size:13px;">{{ $row->numero_parcela }}ª</td>
                    <td style="padding:12px 16px;text-align:right;font-size:14px;font-weight:800;color:#7c3aed;">
                        R$ {{ number_format($row->valor,2,',','.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($honAtrasados)
    <div style="padding:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
        <p style="font-size:12px;color:var(--muted);margin:0;">
            Total em atraso: <strong style="color:#7c3aed;">R$ {{ number_format($this->queryHonorariosAtrasados()->sum('hp.valor'),2,',','.') }}</strong>
        </p>
        {{ $honAtrasados->links() }}
    </div>
    @endif
    @endif
</div>
@endif

<style>
@media (max-width: 1024px) {
    .fin-resumo-grid { grid-template-columns: 1fr !important; }
    .fin-kpis        { grid-template-columns: repeat(3,1fr) !important; }
    .fin-visao       { grid-template-columns: 1fr !important; }
}
@media (max-width: 640px) {
    .fin-kpis { grid-template-columns: 1fr 1fr !important; }
}
</style>

{{-- ── Modal: Tipo de Lançamento (multi-step) ── --}}
<div :style="lAberto ? 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;' : 'display:none'"
     @click.self="lAberto=false; lPasso='main'">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:600px;box-shadow:0 24px 64px rgba(0,0,0,.2);overflow:hidden;"
         @click.stop>

        {{-- Header dinâmico --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid #e2e8f0;">
            <div>
                <div style="font-size:17px;font-weight:800;color:#1e3a5f;">
                    <span x-show="lPasso==='main'">O que deseja lançar?</span>
                    <span x-show="lPasso==='honorario'">Honorário de Processo</span>
                    <span x-show="lPasso==='honorario-fixo'">Honorário Fixo</span>
                    <span x-show="lPasso==='exito'">Honorário de Êxito</span>
                    <span x-show="lPasso==='custa'">Custa Processual</span>
                </div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    <span x-show="lPasso==='main'">Escolha o tipo para ir direto ao lugar certo</span>
                    <span x-show="lPasso==='honorario'">Qual o tipo de honorário?</span>
                    <span x-show="lPasso==='honorario-fixo'">Selecione o processo e continue</span>
                    <span x-show="lPasso==='exito'">Registre o acordo de êxito do processo</span>
                    <span x-show="lPasso==='custa'">Defina se a custa será cobrada do cliente</span>
                </div>
            </div>
            <button @click="lAberto=false; lPasso='main'"
                style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:22px;line-height:1;padding:4px;">&times;</button>
        </div>

        {{-- Passo 1: 4 opções principais --}}
        <div x-show="lPasso==='main'">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:#e2e8f0;">

                <a href="{{ route('financeiro.central') }}?novo=receita" style="text-decoration:none;">
                    <div style="background:#fff;padding:24px;display:flex;flex-direction:column;gap:10px;cursor:pointer;"
                         onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#fff'">
                        <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#15803d;">Receita / Serviço Avulso</div>
                            <div style="font-size:12px;color:#64748b;margin-top:3px;line-height:1.5;">Reunião, consultoria, cobrança direta ao cliente — sem vínculo de processo.</div>
                        </div>
                        <div style="font-size:11px;font-weight:700;color:#16a34a;display:flex;align-items:center;gap:4px;">
                            Ir para Financeiro Central
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </div>
                </a>

                <div @click="$wire.call('carregarProcessos'); lPasso='honorario'"
                     style="background:#fff;padding:24px;display:flex;flex-direction:column;gap:10px;cursor:pointer;"
                     onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#fff'">
                    <div style="width:44px;height:44px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#1d4ed8;">Honorário de Processo</div>
                        <div style="font-size:12px;color:#64748b;margin-top:3px;line-height:1.5;">Parcela, êxito ou recebimento vinculado a um processo específico.</div>
                    </div>
                    <div style="font-size:11px;font-weight:700;color:#2563eb;display:flex;align-items:center;gap:4px;">
                        Escolher tipo →
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </div>

                <div @click="lPasso='custa'"
                     style="background:#fff;padding:24px;display:flex;flex-direction:column;gap:10px;cursor:pointer;"
                     onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background='#fff'">
                    <div style="width:44px;height:44px;border-radius:12px;background:#ffedd5;display:flex;align-items:center;justify-content:center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#c2410c;">Custa Processual</div>
                        <div style="font-size:12px;color:#64748b;margin-top:3px;line-height:1.5;">Taxa de distribuição, diligência, guia — paga pelo escritório e reembolsável pelo cliente.</div>
                    </div>
                    <div style="font-size:11px;font-weight:700;color:#ea580c;display:flex;align-items:center;gap:4px;">
                        Registrar custa →
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </div>

                <a href="{{ route('financeiro.despesas-escritorio') }}?novo=1" style="text-decoration:none;">
                    <div style="background:#fff;padding:24px;display:flex;flex-direction:column;gap:10px;cursor:pointer;"
                         onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                        <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#dc2626;">Despesa do Escritório</div>
                            <div style="font-size:12px;color:#64748b;margin-top:3px;line-height:1.5;">Aluguel, software, salário, conta de luz — saída do escritório, não do processo.</div>
                        </div>
                        <div style="font-size:11px;font-weight:700;color:#dc2626;display:flex;align-items:center;gap:4px;">
                            Ir para Financeiro Central
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        {{-- Passo 2: Tipo de Honorário --}}
        <div x-show="lPasso==='honorario'" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <button @click="lPasso='main'"
                style="align-self:flex-start;display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;color:#64748b;font-size:13px;font-weight:600;padding:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar
            </button>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div @click="lPasso='honorario-fixo'"
                     style="border:2px solid #e2e8f0;border-radius:12px;padding:20px;cursor:pointer;"
                     onmouseover="this.style.borderColor='#2563eb'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div style="width:40px;height:40px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="4"/><line x1="12" y1="6" x2="12" y2="18"/><line x1="6" y1="12" x2="18" y2="12"/></svg>
                    </div>
                    <div style="font-size:14px;font-weight:700;color:#1d4ed8;">Honorário Fixo</div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.5;">Parcela mensal ou recebimento avulso vinculado ao processo.</div>
                </div>
                <div @click="$wire.call('abrirEtapaExito')"
                     style="border:2px solid #e2e8f0;border-radius:12px;padding:20px;cursor:pointer;"
                     onmouseover="this.style.borderColor='#7c3aed'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div style="font-size:14px;font-weight:700;color:#6d28d9;">Honorário de Êxito</div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.5;">Percentual ou valor a receber quando o processo for ganho.</div>
                </div>
            </div>
        </div>

        {{-- Passo 3A: Honorário Fixo — selecionar processo --}}
        <div x-show="lPasso==='honorario-fixo'" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <button @click="lPasso='honorario'"
                style="align-self:flex-start;display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;color:#64748b;font-size:13px;font-weight:600;padding:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar
            </button>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Processo</label>
                <select wire:model="honorarioFixoProcessoId"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff;">
                    <option value="">— selecione o processo —</option>
                    @foreach($processosLista as $proc)
                    <option value="{{ $proc['id'] }}">{{ $proc['numero'] }} — {{ $proc['cliente_nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="irParaHonorarioFixo"
                style="padding:11px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;align-self:flex-end;">
                Ir para o processo →
            </button>
        </div>

        {{-- Passo 3B: Honorário de Êxito — formulário --}}
        <div x-show="lPasso==='exito'" style="padding:24px;display:flex;flex-direction:column;gap:14px;">
            <button @click="lPasso='honorario'"
                style="align-self:flex-start;display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;color:#64748b;font-size:13px;font-weight:600;padding:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar
            </button>

            @if($exitoSalvo)
            <div style="padding:24px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" style="margin:0 auto 10px;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
                <div style="font-size:15px;font-weight:700;color:#15803d;">Honorário de Êxito registrado!</div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;">Uma parcela pendente foi criada no processo selecionado.</div>
                <button @click="lAberto=false; lPasso='main'"
                    style="margin-top:14px;padding:8px 24px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                    Fechar
                </button>
            </div>
            @else
            @if($exitoErro)
            <div style="padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#dc2626;font-size:13px;font-weight:600;">
                {{ $exitoErro }}
            </div>
            @endif
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">
                    Processo <span style="color:#dc2626">*</span>
                </label>
                <select wire:model="exitoProcessoId"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff;">
                    <option value="">— selecione o processo —</option>
                    @foreach($processosLista as $proc)
                    <option value="{{ $proc['id'] }}">{{ $proc['numero'] }} — {{ $proc['cliente_nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">% Êxito</label>
                    <input wire:model="exitoPercentual" type="text" placeholder="Ex: 20" inputmode="decimal"
                        style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Valor Estimado</label>
                    <input wire:model="exitoValor" type="text" placeholder="0,00" inputmode="decimal"
                        style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">
                        Data <span style="color:#dc2626">*</span>
                    </label>
                    <input wire:model="exitoData" type="date"
                        style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Descrição</label>
                    <input wire:model="exitoDescricao" type="text" placeholder="Honorário de Êxito"
                        style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>
            <button wire:click="salvarHonorarioExito"
                style="padding:11px 20px;background:#7c3aed;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;align-self:flex-end;">
                Salvar Honorário de Êxito
            </button>
            @endif
        </div>

        {{-- Passo 4: Custa — definir reembolso --}}
        <div x-show="lPasso==='custa'" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <button @click="lPasso='main'"
                style="align-self:flex-start;display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;color:#64748b;font-size:13px;font-weight:600;padding:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar
            </button>
            <div style="font-size:15px;font-weight:700;color:#1e3a5f;">Esta custa será cobrada do cliente?</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <label style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:2px solid {{ $custaReembolsavel==='sim' ? '#f97316' : '#e2e8f0' }};border-radius:10px;cursor:pointer;background:{{ $custaReembolsavel==='sim' ? '#fff7ed' : '#fff' }};width:100%;box-sizing:border-box;">
                    <input type="radio" wire:model.live="custaReembolsavel" value="sim" style="width:16px;height:16px;flex-shrink:0;margin-top:3px;padding:0;border:none;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:#c2410c;">Sim — cobrar do cliente (reembolsável)</div>
                        <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.5;">A custa aparecerá na lista de reembolsos a cobrar do cliente.</div>
                    </div>
                </label>
                <label style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:2px solid {{ $custaReembolsavel==='nao' ? '#94a3b8' : '#e2e8f0' }};border-radius:10px;cursor:pointer;background:{{ $custaReembolsavel==='nao' ? '#f8fafc' : '#fff' }};width:100%;box-sizing:border-box;">
                    <input type="radio" wire:model.live="custaReembolsavel" value="nao" style="width:16px;height:16px;flex-shrink:0;margin-top:3px;padding:0;border:none;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:#475569;">Não — despesa do escritório</div>
                        <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.5;">Será registrada como pagamento simples do escritório, sem reembolso.</div>
                    </div>
                </label>
            </div>
            <button wire:click="irParaCusta"
                style="padding:11px 20px;background:#ea580c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;align-self:flex-end;">
                Continuar → Selecionar processo
            </button>
        </div>

        {{-- Footer --}}
        <div style="padding:14px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <div style="font-size:11px;color:#94a3b8;">
                💡 <strong>Dica:</strong> Para contratos mensais (honorário de assessoria/consultoria), acesse
                <a href="{{ route('contratos') }}" style="color:#2563eb;text-decoration:none;font-weight:600;">Contratos</a>
                — os lançamentos são gerados automaticamente.
            </div>
        </div>

    </div>
</div>

</div>
