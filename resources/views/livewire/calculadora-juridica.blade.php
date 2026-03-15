<div>

{{-- Cabeçalho --}}
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">🧮 Calculadora Jurídica</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">
        Correção monetária, juros moratórios, multa e honorários advocatícios
    </p>
</div>

{{-- Índices disponíveis --}}
@if(!empty($indicesDisponiveis))
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 16px;margin-bottom:20px;font-size:12px;color:#1e40af;display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
    <span style="font-weight:600;">📊 Índices cadastrados:</span>
    @foreach($indicesDisponiveis as $idx)
        <span style="background:#dbeafe;padding:2px 10px;border-radius:12px;">
            {{ $idx['sigla'] }}: {{ $idx['de'] }} → {{ $idx['ate'] }}
        </span>
    @endforeach
</div>
@endif

<div class="form-grid" style="align-items:start;">

{{-- ── FORMULÁRIO ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Parâmetros do Cálculo</span>
        @if($calculado)
        <button wire:click="limpar" class="btn btn-secondary btn-sm">↺ Novo cálculo</button>
        @endif
    </div>


    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Valor e processo --}}
        <div class="form-grid">
            <div class="form-field">
                <label class="lbl">Valor Original (R$) *</label>
                <input wire:model="valorOriginal" type="text" placeholder="0,00" style="font-weight:600;">
            </div>
            <div class="form-field">
                <label class="lbl">Processo (referência)</label>
                <input wire:model="processoRef" type="text" placeholder="Opcional">
            </div>
        </div>

        {{-- Datas --}}
        <div class="form-grid">
            <div class="form-field">
                <label class="lbl">Data Inicial *</label>
                <input wire:model="dataInicio" type="date">
            </div>
            <div class="form-field">
                <label class="lbl">Data Final *</label>
                <input wire:model="dataFim" type="date">
            </div>
        </div>

        {{-- Divisor --}}
        <div style="border-top:1px solid var(--border);padding-top:14px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">CORREÇÃO MONETÁRIA</div>
            <div style="display:grid;grid-template-columns:1fr;gap:8px;">
                @foreach([
                    'IPCA'   => 'IPCA — Índice Nacional de Preços ao Consumidor Amplo',
                    'IGPM'   => 'IGP-M — Índice Geral de Preços do Mercado',
                    'TR'     => 'TR — Taxa Referencial',
                    'SELIC'  => 'SELIC — Taxa de Referência do Banco Central',
                    'nenhum' => 'Sem correção monetária',
                ] as $val => $label)
                <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1.5px solid {{ $indiceCorrecao === $val ? '#2563a8' : 'var(--border)' }};border-radius:7px;cursor:pointer;background:{{ $indiceCorrecao === $val ? '#eff6ff' : '#fff' }};font-size:13px;line-height:1.4;">
                    <input wire:model.live="indiceCorrecao" type="radio" value="{{ $val }}" style="accent-color:#2563a8;flex-shrink:0;margin:0;width:15px;height:15px;cursor:pointer;">
                    <span>{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Juros --}}
        <div style="border-top:1px solid var(--border);padding-top:14px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">JUROS MORATÓRIOS</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
                @foreach(['mensal' => '% ao mês (simples)', 'selic' => 'SELIC acumulada', 'nenhum' => 'Sem juros'] as $val => $label)
                <label style="display:flex;align-items:center;gap:6px;padding:6px 12px;border:1.5px solid {{ $tipoJuros === $val ? '#2563a8' : 'var(--border)' }};border-radius:20px;cursor:pointer;background:{{ $tipoJuros === $val ? '#eff6ff' : '#fff' }};font-size:12px;font-weight:600;line-height:1.4;">
                    <input wire:model.live="tipoJuros" type="radio" value="{{ $val }}" style="accent-color:#2563a8;flex-shrink:0;margin:0;width:14px;height:14px;cursor:pointer;">
                    {{ $label }}
                </label>
                @endforeach
            </div>
            @if($tipoJuros === 'mensal')
            <div style="display:flex;align-items:center;gap:8px;">
                <input wire:model="percentualJuros" type="number" step="0.01" min="0" placeholder="1"
                    style="width:100px;padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                <span style="font-size:13px;color:var(--muted);">% ao mês (juros simples)</span>
            </div>
            @endif
        </div>

        {{-- Multa e Honorários --}}
        <div style="border-top:1px solid var(--border);padding-top:14px;">
            <div class="form-grid">
                <div>
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">MULTA (%)</label>
                    <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;">
                        @foreach(['0','2','10'] as $v)
                        <button wire:click="$set('percentualMulta','{{ $v }}')" type="button"
                            style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;
                                   border:1.5px solid {{ $percentualMulta == $v ? '#2563a8' : 'var(--border)' }};
                                   background:{{ $percentualMulta == $v ? '#eff6ff' : '#fff' }};
                                   color:{{ $percentualMulta == $v ? '#1e40af' : 'var(--text)' }};">
                            {{ $v }}%
                        </button>
                        @endforeach
                    </div>
                    <input wire:model="percentualMulta" type="number" step="0.01" min="0" placeholder="0"
                        style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;margin-top:8px;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">HONORÁRIOS ADV. (%)</label>
                    <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;">
                        @foreach(['0','10','20'] as $v)
                        <button wire:click="$set('percentualHonorarios','{{ $v }}')" type="button"
                            style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;
                                   border:1.5px solid {{ $percentualHonorarios == $v ? '#7c3aed' : 'var(--border)' }};
                                   background:{{ $percentualHonorarios == $v ? '#f5f3ff' : '#fff' }};
                                   color:{{ $percentualHonorarios == $v ? '#6d28d9' : 'var(--text)' }};">
                            {{ $v }}%
                        </button>
                        @endforeach
                    </div>
                    <input wire:model="percentualHonorarios" type="number" step="0.01" min="0" placeholder="0"
                        style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;margin-top:8px;">
                </div>
            </div>
        </div>

        <button wire:click="calcular" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;"
            wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="calcular">🧮 Calcular</span>
            <span wire:loading wire:target="calcular">Calculando...</span>
        </button>

    </div>
</div>

{{-- ── RESULTADO ── --}}
<div>
    @if(!$calculado)
    <div class="card" style="text-align:center;padding:60px 24px;color:var(--muted);">
        <div style="font-size:48px;margin-bottom:16px;">🧮</div>
        <div style="font-size:15px;font-weight:600;margin-bottom:8px;">Preencha os dados ao lado</div>
        <div style="font-size:13px;">O resultado aparecerá aqui após o cálculo.</div>
    </div>
    @else
    @php
        $r = $resultado;
        $fmt = fn($v) => 'R$ ' . number_format($v, 2, ',', '.');
        $fmtPct = fn($v) => number_format($v, 4, ',', '.') . '%';
    @endphp

    {{-- Card principal --}}
    <div class="card" style="border-top:4px solid #1a3a5c;margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
            <div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:2px;">
                    {{ $r['data_ini_fmt'] }} → {{ $r['data_fim_fmt'] }}
                    ({{ $r['periodo_meses'] }} meses)
                    @if($r['processo_ref']) · {{ $r['processo_ref'] }} @endif
                </div>
                <div style="font-size:11px;color:var(--muted);">
                    Correção: {{ $r['indice'] }}
                    @if($r['indices_usados'] > 0) · {{ $r['indices_usados'] }} índices @endif
                </div>
            </div>
            <button onclick="window.print()" title="Imprimir"
                style="background:none;border:1px solid var(--border);border-radius:6px;padding:5px 10px;cursor:pointer;font-size:12px;color:var(--muted);">
                🖨️ Imprimir
            </button>
        </div>

        {{-- Linha do total --}}
        <div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:10px;padding:20px;margin-bottom:16px;text-align:center;">
            <div style="font-size:11px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">Total Atualizado</div>
            <div style="font-size:32px;font-weight:700;color:#fff;letter-spacing:-1px;">{{ $fmt($r['total']) }}</div>
            @if($r['valor_original'] > 0)
            <div style="font-size:13px;color:#93c5fd;margin-top:4px;">
                ▲ {{ $fmtPct(($r['total'] / $r['valor_original'] - 1) * 100) }} sobre o valor original
            </div>
            @endif
        </div>

        {{-- Breakdown --}}
        <div style="display:flex;flex-direction:column;gap:6px;">

            @php
                $linha = fn($icon, $label, $valor, $pct, $cor = '#1e293b', $bg = '#f8fafc') => [
                    'icon' => $icon, 'label' => $label,
                    'valor' => $valor, 'pct' => $pct,
                    'cor' => $cor, 'bg' => $bg,
                ];
                $linhas = [
                    $linha('💰', 'Valor Original', $r['valor_original'], null, '#1a3a5c', '#eff6ff'),
                ];
                if ($r['indice'] !== 'Sem correção') {
                    $linhas[] = $linha('📈', 'Correção Monetária ('.$r['indice'].')', $r['correcao_reais'], $r['correcao_pct'], '#0891b2');
                }
                if ($r['juros_reais'] > 0) {
                    $linhas[] = $linha('💹', 'Juros Moratórios', $r['juros_reais'], $r['juros_pct'], '#d97706');
                }
                if ($r['multa_reais'] > 0) {
                    $linhas[] = $linha('⚠️', 'Multa ('.$r['multa_pct'].'%)', $r['multa_reais'], $r['multa_pct'], '#dc2626');
                }
                if ($r['honorarios_reais'] > 0) {
                    $linhas[] = $linha('⚖️', 'Honorários Advocatícios ('.$r['honorarios_pct'].'%)', $r['honorarios_reais'], $r['honorarios_pct'], '#7c3aed');
                }
            @endphp

            @foreach($linhas as $lin)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;border-radius:6px;background:{{ $lin['bg'] }};">
                <div style="font-size:13px;color:#475569;">
                    {{ $lin['icon'] }} {{ $lin['label'] }}
                </div>
                <div style="text-align:right;">
                    <div style="font-size:14px;font-weight:700;color:{{ $lin['cor'] }};">{{ $fmt($lin['valor']) }}</div>
                    @if($lin['pct'] !== null)
                    <div style="font-size:11px;color:var(--muted);">{{ $fmtPct($lin['pct']) }}</div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Subtotal se tem honorários --}}
            @if($r['honorarios_reais'] > 0)
            <div style="display:flex;justify-content:space-between;padding:8px 12px;border-top:2px dashed var(--border);font-size:13px;color:var(--muted);">
                <span>Subtotal (sem honorários)</span>
                <span style="font-weight:600;">{{ $fmt($r['subtotal']) }}</span>
            </div>
            @endif

            {{-- Total --}}
            <div style="display:flex;justify-content:space-between;padding:12px 14px;border-radius:8px;background:#0f2540;font-size:15px;font-weight:700;color:#fff;margin-top:4px;">
                <span>TOTAL</span>
                <span>{{ $fmt($r['total']) }}</span>
            </div>
        </div>

        @if($r['juros_desc'])
        <div style="margin-top:12px;font-size:11px;color:var(--muted);">
            ℹ️ Juros: {{ $r['juros_desc'] }}
        </div>
        @endif
        <div style="margin-top:4px;font-size:11px;color:var(--muted);">
            ⚠️ Cálculo gerado em {{ $r['gerado_em'] }}. Apenas para referência — consulte um advogado para fins processuais.
        </div>
    </div>

    {{-- Detalhes mensais --}}
    @if(!empty($detalhes))
    <div class="card">
        <div class="card-header" style="cursor:pointer;" wire:click="$toggle('mostrarDetalhes')">
            <span class="card-title">📋 Detalhes mensais — {{ $resultado['indice'] }}</span>
            <span style="font-size:18px;color:var(--muted);">{{ $mostrarDetalhes ? '▲' : '▼' }}</span>
        </div>
        @if($mostrarDetalhes)
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mês</th>
                        <th style="text-align:right;">% do mês</th>
                        <th class="hide-sm" style="text-align:right;">Fator acumulado</th>
                        <th style="text-align:right;">Valor atualizado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalhes as $d)
                    <tr>
                        <td style="font-weight:600;">{{ $d['mes'] }}</td>
                        <td style="text-align:right;color:{{ $d['percentual'] >= 0 ? '#16a34a' : '#dc2626' }};">
                            {{ number_format($d['percentual'], 4, ',', '.') }}%
                        </td>
                        <td class="hide-sm" style="text-align:right;color:var(--muted);">
                            {{ number_format($d['fator_acum'], 6, ',', '.') }}
                        </td>
                        <td style="text-align:right;font-weight:600;">
                            R$ {{ number_format($resultado['valor_original'] * $d['fator_acum'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    @endif
</div>

</div>{{-- /grid --}}

</div>
