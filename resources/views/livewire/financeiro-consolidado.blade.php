<div>

{{-- ══ KPIs ══════════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">

    <div class="stat-card" style="border-left-color:#16a34a;">
        <div class="stat-icon">📥</div>
        <div class="stat-val" style="color:#16a34a;">R$ {{ number_format($kpis['aReceber'],2,',','.') }}</div>
        <div class="stat-label">A receber (em aberto)</div>
    </div>

    <div class="stat-card" style="border-left-color:#2563a8;">
        <div class="stat-icon">✅</div>
        <div class="stat-val" style="color:#2563a8;">R$ {{ number_format($kpis['recebidoMes'],2,',','.') }}</div>
        <div class="stat-label">Recebido este mês</div>
    </div>

    <div class="stat-card" style="border-left-color:#dc2626;">
        <div class="stat-icon">📤</div>
        <div class="stat-val" style="color:#dc2626;">R$ {{ number_format($kpis['aPagar'],2,',','.') }}</div>
        <div class="stat-label">A pagar (em aberto)</div>
    </div>

    <div class="stat-card" style="border-left-color:#d97706;">
        <div class="stat-icon">💸</div>
        <div class="stat-val" style="color:#d97706;">R$ {{ number_format($kpis['pagoMes'],2,',','.') }}</div>
        <div class="stat-label">Pago este mês</div>
    </div>

    <div class="stat-card" style="border-left-color:#7c3aed;">
        <div class="stat-icon">⚠️</div>
        <div class="stat-val" style="color:#7c3aed;">R$ {{ number_format($kpis['honAtrasado'],2,',','.') }}</div>
        <div class="stat-label">Honorários em atraso</div>
    </div>

    <div class="stat-card" style="border-left-color:#0891b2;">
        <div class="stat-icon">📋</div>
        <div class="stat-val" style="color:#0891b2;">R$ {{ number_format($kpis['honPendente'],2,',','.') }}</div>
        <div class="stat-label">Honorários a vencer</div>
    </div>

</div>

{{-- ══ Abas ══════════════════════════════════════════════════════ --}}
<div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);">
    @foreach([
        'visao-geral' => '📊 Visão Geral',
        'fluxo'       => '📈 Fluxo de Caixa',
        'receber'     => '📥 Contas a Receber',
        'pagar'       => '📤 Contas a Pagar',
        'honorarios'  => '⚠️ Honorários Atrasados',
    ] as $key => $label)
    <button wire:click="$set('aba','{{ $key }}')"
        style="padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;background:none;border:none;
               border-bottom:3px solid {{ $aba === $key ? 'var(--primary)' : 'transparent' }};
               color:{{ $aba === $key ? 'var(--primary)' : 'var(--muted)' }};
               margin-bottom:-2px;transition:all .15s;">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ══ Filtros (abas com lista) ══════════════════════════════════ --}}
@if(in_array($aba, ['receber','pagar','honorarios']))
<div style="display:flex;gap:8px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
    @if($aba !== 'honorarios')
    <div>
        <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-right:6px;">Mês</label>
        <input type="month" wire:model.live="filtroMes"
            style="padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
    </div>
    <div>
        <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-right:6px;">Status</label>
        <select wire:model.live="filtroStatus"
            style="padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
            <option value="pendente">Apenas pendentes</option>
            <option value="todos">Todos</option>
        </select>
    </div>
    @endif
</div>
@endif

{{-- ══ ABA: VISÃO GERAL ══════════════════════════════════════════ --}}
@if($aba === 'visao-geral')
<div class="card">
    <div class="card-header">
        <span class="card-title">Resumo Financeiro</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <div>
            <h3 style="font-size:13px;font-weight:700;color:var(--primary);margin-bottom:12px;border-bottom:1px solid var(--border);padding-bottom:8px;">Recebimentos</h3>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                <span style="color:var(--muted);">Em aberto</span>
                <span style="color:#16a34a;font-weight:700;">R$ {{ number_format($kpis['aReceber'],2,',','.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;">
                <span style="color:var(--muted);">Recebido este mês</span>
                <span style="font-weight:700;">R$ {{ number_format($kpis['recebidoMes'],2,',','.') }}</span>
            </div>
        </div>
        <div>
            <h3 style="font-size:13px;font-weight:700;color:var(--primary);margin-bottom:12px;border-bottom:1px solid var(--border);padding-bottom:8px;">Despesas</h3>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                <span style="color:var(--muted);">Em aberto</span>
                <span style="color:#dc2626;font-weight:700;">R$ {{ number_format($kpis['aPagar'],2,',','.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;">
                <span style="color:var(--muted);">Pago este mês</span>
                <span style="font-weight:700;">R$ {{ number_format($kpis['pagoMes'],2,',','.') }}</span>
            </div>
        </div>
        <div>
            <h3 style="font-size:13px;font-weight:700;color:var(--primary);margin-bottom:12px;border-bottom:1px solid var(--border);padding-bottom:8px;">Honorários</h3>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                <span style="color:var(--muted);">Em atraso</span>
                <span style="color:#7c3aed;font-weight:700;">R$ {{ number_format($kpis['honAtrasado'],2,',','.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;">
                <span style="color:var(--muted);">A vencer</span>
                <span style="font-weight:700;">R$ {{ number_format($kpis['honPendente'],2,',','.') }}</span>
            </div>
        </div>
        <div style="background:#f0fdf4;border-radius:8px;padding:16px;display:flex;flex-direction:column;justify-content:center;align-items:center;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Saldo Projetado</div>
            @php $saldo = $kpis['aReceber'] + $kpis['honPendente'] - $kpis['aPagar']; @endphp
            <div style="font-size:28px;font-weight:700;color:{{ $saldo >= 0 ? '#16a34a' : '#dc2626' }};">
                R$ {{ number_format(abs($saldo),2,',','.') }}
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                {{ $saldo >= 0 ? 'Superávit projetado' : 'Déficit projetado' }}
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══ ABA: FLUXO DE CAIXA ══════════════════════════════════════ --}}
@if($aba === 'fluxo')
<div class="card">
    <div class="card-header">
        <span class="card-title">Fluxo de Caixa — Últimos 6 meses</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mês</th>
                    <th style="text-align:right;">Recebimentos</th>
                    <th style="text-align:right;">Honorários recebidos</th>
                    <th style="text-align:right;">Despesas pagas</th>
                    <th style="text-align:right;">Saldo do mês</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fluxo as $linha)
                <tr>
                    <td style="font-weight:600;">{{ $linha['label'] }}</td>
                    <td style="text-align:right;color:#16a34a;">R$ {{ number_format($linha['recebido'],2,',','.') }}</td>
                    <td style="text-align:right;color:#0891b2;">R$ {{ number_format($linha['honorarios'],2,',','.') }}</td>
                    <td style="text-align:right;color:#dc2626;">R$ {{ number_format($linha['pago'],2,',','.') }}</td>
                    <td style="text-align:right;font-weight:700;color:{{ $linha['saldo'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        R$ {{ number_format($linha['saldo'],2,',','.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;">
                    <td style="font-weight:700;">Total</td>
                    <td style="text-align:right;font-weight:700;color:#16a34a;">
                        R$ {{ number_format(collect($fluxo)->sum('recebido'),2,',','.') }}
                    </td>
                    <td style="text-align:right;font-weight:700;color:#0891b2;">
                        R$ {{ number_format(collect($fluxo)->sum('honorarios'),2,',','.') }}
                    </td>
                    <td style="text-align:right;font-weight:700;color:#dc2626;">
                        R$ {{ number_format(collect($fluxo)->sum('pago'),2,',','.') }}
                    </td>
                    <td style="text-align:right;font-weight:700;color:{{ collect($fluxo)->sum('saldo') >= 0 ? '#16a34a' : '#dc2626' }};">
                        R$ {{ number_format(collect($fluxo)->sum('saldo'),2,',','.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ══ ABA: CONTAS A RECEBER ═════════════════════════════════════ --}}
@if($aba === 'receber')
<div class="card">
    <div class="card-header">
        <span class="card-title">Contas a Receber</span>
        @if($receber)
        <span style="font-size:12px;color:var(--muted);">{{ $receber->total() }} registro(s)</span>
        @endif
    </div>
    @if($receber && $receber->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:30px 0;">Nenhum registro encontrado.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Processo</th>
                    <th>Cliente</th>
                    <th>Descrição</th>
                    <th style="text-align:right;">Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receber ?? [] as $row)
                <tr>
                    <td style="white-space:nowrap;">
                        {{ $row->data ? \Carbon\Carbon::parse($row->data)->format('d/m/Y') : '—' }}
                    </td>
                    <td style="font-family:monospace;font-size:11px;">{{ $row->processo_numero ?? '—' }}</td>
                    <td>{{ $row->cliente_nome ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $row->descricao ?? '—' }}</td>
                    <td style="text-align:right;font-weight:600;color:#16a34a;">
                        R$ {{ number_format($row->valor,2,',','.') }}
                    </td>
                    <td>
                        @if($row->recebido)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Recebido</span>
                        @else
                            <span class="badge" style="background:#fef9c3;color:#854d0e;">Pendente</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($receber)
        <div style="margin-top:12px;">{{ $receber->links() }}</div>
        <p style="font-size:12px;color:var(--muted);margin-top:8px;">
            Total filtrado:
            <strong style="color:#16a34a;">
                R$ {{ number_format($this->queryReceber()->sum('r.valor'),2,',','.') }}
            </strong>
        </p>
    @endif
    @endif
</div>
@endif

{{-- ══ ABA: CONTAS A PAGAR ═══════════════════════════════════════ --}}
@if($aba === 'pagar')
<div class="card">
    <div class="card-header">
        <span class="card-title">Contas a Pagar</span>
        @if($pagar)
        <span style="font-size:12px;color:var(--muted);">{{ $pagar->total() }} registro(s)</span>
        @endif
    </div>
    @if($pagar && $pagar->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:30px 0;">Nenhum registro encontrado.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Vencimento</th>
                    <th>Processo</th>
                    <th>Cliente</th>
                    <th>Fornecedor</th>
                    <th>Descrição</th>
                    <th style="text-align:right;">Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagar ?? [] as $row)
                @php
                    $vencido = !$row->pago && $row->data_vencimento && \Carbon\Carbon::parse($row->data_vencimento)->isPast();
                @endphp
                <tr>
                    <td style="white-space:nowrap;{{ $vencido ? 'color:#dc2626;font-weight:600;' : '' }}">
                        {{ $row->data_vencimento ? \Carbon\Carbon::parse($row->data_vencimento)->format('d/m/Y') : '—' }}
                        @if($vencido) <span style="font-size:10px;">⚠️</span> @endif
                    </td>
                    <td style="font-family:monospace;font-size:11px;">{{ $row->processo_numero ?? '—' }}</td>
                    <td>{{ $row->cliente_nome ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $row->fornecedor_nome ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $row->descricao ?? '—' }}</td>
                    <td style="text-align:right;font-weight:600;color:#dc2626;">
                        R$ {{ number_format($row->valor,2,',','.') }}
                    </td>
                    <td>
                        @if($row->pago)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Pago</span>
                        @elseif($vencido)
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Vencido</span>
                        @else
                            <span class="badge" style="background:#fef9c3;color:#854d0e;">Pendente</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($pagar)
        <div style="margin-top:12px;">{{ $pagar->links() }}</div>
        <p style="font-size:12px;color:var(--muted);margin-top:8px;">
            Total filtrado:
            <strong style="color:#dc2626;">
                R$ {{ number_format($this->queryPagar()->sum('p.valor'),2,',','.') }}
            </strong>
        </p>
    @endif
    @endif
</div>
@endif

{{-- ══ ABA: HONORÁRIOS ATRASADOS ════════════════════════════════ --}}
@if($aba === 'honorarios')
<div class="card">
    <div class="card-header">
        <span class="card-title">Honorários em Atraso</span>
        @if($honAtrasados)
        <span style="font-size:12px;color:var(--muted);">{{ $honAtrasados->total() }} parcela(s)</span>
        @endif
    </div>
    @if($honAtrasados && $honAtrasados->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:30px 0;">Nenhuma parcela em atraso.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Vencimento</th>
                    <th>Dias em atraso</th>
                    <th>Cliente</th>
                    <th>Processo</th>
                    <th>Contrato</th>
                    <th>Parcela</th>
                    <th style="text-align:right;">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($honAtrasados ?? [] as $row)
                <tr>
                    <td style="white-space:nowrap;color:#dc2626;font-weight:600;">
                        {{ \Carbon\Carbon::parse($row->vencimento)->format('d/m/Y') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge" style="background:#fee2e2;color:#991b1b;">
                            {{ $row->dias_atraso }} dia(s)
                        </span>
                    </td>
                    <td>{{ $row->cliente_nome ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:11px;">{{ $row->processo_numero ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $row->honorario_descricao ?? '—' }}</td>
                    <td style="text-align:center;">{{ $row->numero_parcela }}ª</td>
                    <td style="text-align:right;font-weight:600;color:#7c3aed;">
                        R$ {{ number_format($row->valor,2,',','.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($honAtrasados)
        <div style="margin-top:12px;">{{ $honAtrasados->links() }}</div>
        <p style="font-size:12px;color:var(--muted);margin-top:8px;">
            Total em atraso:
            <strong style="color:#7c3aed;">
                R$ {{ number_format($this->queryHonorariosAtrasados()->sum('hp.valor'),2,',','.') }}
            </strong>
        </p>
    @endif
    @endif
</div>
@endif

</div>
