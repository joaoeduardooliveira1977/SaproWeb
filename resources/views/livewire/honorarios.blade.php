<div>

{{-- Cards Resumo --}}
<div class="stat-grid">
    <div class="card" style="border-left:4px solid var(--primary);">
        <div class="stat-label">Total Contratado</div>
        <div class="stat-val" style="color:var(--primary);font-size:20px;">R$ {{ number_format($resumo->total_contratado,2,',','.') }}</div>
        <div style="font-size:12px;color:var(--muted);">{{ $resumo->total_contratos }} contratos ativos</div>
    </div>
    <div class="card" style="border-left:4px solid var(--success);">
        <div class="stat-label">Recebido</div>
        <div class="stat-val" style="color:var(--success);font-size:20px;">R$ {{ number_format($resumo->total_recebido,2,',','.') }}</div>
    </div>
    <div class="card" style="border-left:4px solid var(--warning);">
        <div class="stat-label">Pendente</div>
        <div class="stat-val" style="color:var(--warning);font-size:20px;">R$ {{ number_format($resumo->total_pendente,2,',','.') }}</div>
    </div>
    <div class="card" style="border-left:4px solid var(--danger);">
        <div class="stat-label">Atrasado</div>
        <div class="stat-val" style="color:var(--danger);font-size:20px;">R$ {{ number_format($resumo->total_atrasado,2,',','.') }}</div>
    </div>
</div>

{{-- Filtros + Botão --}}
<div class="card" style="margin-bottom:16px;">
    <div class="filter-bar">
        <input wire:model.live="busca" type="text" placeholder="🔍 Buscar cliente ou descrição...">
        <select wire:model.live="filtroTipo">
            <option value="">Todos os tipos</option>
            <option value="fixo_mensal">Fixo Mensal</option>
            <option value="exito">Êxito</option>
            <option value="hora">Por Hora</option>
            <option value="ato_diligencia">Ato/Diligência</option>
        </select>
        <select wire:model.live="filtroStatus">
            <option value="">Todos os status</option>
            <option value="ativo">Ativo</option>
            <option value="encerrado">Encerrado</option>
            <option value="suspenso">Suspenso</option>
        </select>
        <button wire:click="exportarCsv" wire:loading.attr="disabled"
            class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv">📥 CSV</span>
            <span wire:loading wire:target="exportarCsv">Gerando…</span>
        </button>
        <button wire:click="novoHonorario" class="btn btn-primary" style="flex-shrink:0;">+ Novo Honorário</button>
    </div>
</div>

{{-- Tabela --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th class="hide-sm">Processo</th>
                    <th>Tipo</th>
                    <th class="hide-sm">Descrição</th>
                    <th style="text-align:right;">Contrato</th>
                    <th class="hide-sm" style="text-align:right;">Recebido</th>
                    <th class="hide-sm" style="text-align:right;">Pendente</th>
                    <th class="hide-sm" style="text-align:center;">Parcelas</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($honorarios as $h)
                <tr>
                    <td style="font-weight:600;">{{ $h->cliente_nome }}</td>
                    <td class="hide-sm" style="color:var(--muted);">{{ $h->processo_numero ?? '—' }}</td>
                    <td>
                        @php
                            $tipos = ['fixo_mensal'=>['label'=>'Fixo','color'=>'#2563a8'],
                                      'exito'=>['label'=>'Êxito','color'=>'#16a34a'],
                                      'hora'=>['label'=>'Hora','color'=>'#d97706'],
                                      'ato_diligencia'=>['label'=>'Ato','color'=>'#7c3aed']];
                            $t = $tipos[$h->tipo] ?? ['label'=>$h->tipo,'color'=>'#64748b'];
                        @endphp
                        <span class="badge" style="background:{{ $t['color'] }}20;color:{{ $t['color'] }};">{{ $t['label'] }}</span>
                    </td>
                    <td class="hide-sm">{{ $h->descricao }}</td>
                    <td style="text-align:right;font-weight:600;">R$ {{ number_format($h->valor_contrato,2,',','.') }}</td>
                    <td class="hide-sm" style="text-align:right;color:var(--success);">R$ {{ number_format($h->valor_recebido,2,',','.') }}</td>
                    <td class="hide-sm" style="text-align:right;color:{{ $h->parcelas_atrasadas > 0 ? 'var(--danger)' : 'var(--warning)' }};">
                        R$ {{ number_format($h->valor_pendente,2,',','.') }}
                        @if($h->parcelas_atrasadas > 0)<span style="font-size:10px;"> ⚠️{{ $h->parcelas_atrasadas }}</span>@endif
                    </td>
                    <td class="hide-sm" style="text-align:center;font-size:12px;">{{ $h->parcelas_pagas }}/{{ $h->total_parcelas_count }}</td>
                    <td style="text-align:center;">
                        @php $statusCores = ['ativo'=>'var(--success)','encerrado'=>'var(--muted)','suspenso'=>'var(--warning)']; @endphp
                        <span style="color:{{ $statusCores[$h->status] ?? 'var(--muted)' }};font-size:12px;font-weight:600;">{{ ucfirst($h->status) }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions" style="justify-content:center;">
                            <button wire:click="verParcelas({{ $h->id }})" title="Ver parcelas" class="btn-icon">💰</button>
                            <button wire:click="editarHonorario({{ $h->id }})" title="Editar" class="btn-icon">✏️</button>
                            <button wire:click="excluirHonorario({{ $h->id }})" title="Excluir" onclick="return confirm('Excluir este honorário?')" class="btn-icon">🗑️</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding:32px;text-align:center;color:var(--muted);">Nenhum honorário cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Honorário --}}
@if($modalHonorario)
<div class="modal-backdrop">
    <div class="modal" style="max-width:620px;">
        <div class="modal-header">
            <span class="modal-title">{{ $honorarioId ? 'Editar' : 'Novo' }} Honorário</span>
            <button wire:click="$set('modalHonorario',false)" class="modal-close">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="form-grid">
                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">CLIENTE *</label>
                    <select wire:model.live="cliente_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="0">Selecione...</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->nome }}</option>
                        @endforeach
                    </select>
                </div>

                @if(count($processos) > 0)
                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">PROCESSO (opcional)</label>
                    <select wire:model.live="processo_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="">Sem processo vinculado</option>
                        @foreach($processos as $p)
                            <option value="{{ $p->id }}">{{ $p->numero }}{{ $p->vara ? ' — '.$p->vara : '' }}{{ $p->valor_causa ? ' · R$ '.number_format($p->valor_causa,2,',','.') : '' }}</option>
                        @endforeach
                    </select>
                    @if($valorCausa)
                    <div style="margin-top:6px;padding:8px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;color:#1d4ed8;display:flex;align-items:center;gap:6px;">
                        ⚖️ <span>Valor da causa: <strong>R$ {{ number_format((float)$valorCausa,2,',','.') }}</strong></span>
                    </div>
                    @endif
                </div>
                @endif

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">TIPO *</label>
                    <select wire:model.live="tipo" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="fixo_mensal">Fixo Mensal</option>
                        <option value="exito">Por Êxito</option>
                        <option value="hora">Por Hora</option>
                        <option value="ato_diligencia">Ato/Diligência</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">STATUS</label>
                    <select wire:model="status" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="ativo">Ativo</option>
                        <option value="suspenso">Suspenso</option>
                        <option value="encerrado">Encerrado</option>
                    </select>
                </div>

                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">DESCRIÇÃO *</label>
                    <input wire:model="descricao" type="text" placeholder="Ex: Honorário mensal — Ação trabalhista" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">
                        VALOR TOTAL (R$) *
                        @if($tipo === 'exito' && $valorCalculado > 0)
                            <span style="font-weight:400;color:#16a34a;margin-left:6px;">← calculado automaticamente</span>
                        @endif
                    </label>
                    <input wire:model="valor_contrato" type="number" step="0.01" placeholder="0,00"
                           style="width:100%;padding:8px 12px;border:1px solid {{ ($tipo==='exito'&&$valorCalculado>0&&(float)$valor_contrato===$valorCalculado) ? '#86efac' : 'var(--border)' }};border-radius:6px;margin-top:4px;">
                </div>

                @if($tipo === 'exito')
                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">% ÊXITO SOBRE A CAUSA</label>
                    <div style="display:flex;gap:8px;margin-top:4px;align-items:center;">
                        <input wire:model.live="percentual_exito" type="number" step="0.01" min="0" max="100"
                               placeholder="Ex: 20"
                               style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                        <span style="font-size:13px;color:var(--muted);">%</span>
                    </div>

                    {{-- Sugestões de percentual --}}
                    <div style="margin-top:8px;">
                        <div style="font-size:11px;color:var(--muted);margin-bottom:5px;">⚡ Sugestões rápidas:</div>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @foreach([5, 10, 15, 20, 25, 30] as $perc)
                            <button wire:click="aplicarPercentual({{ $perc }})"
                                    type="button"
                                    style="padding:4px 12px;border-radius:16px;font-size:12px;font-weight:600;cursor:pointer;
                                           border:1px solid {{ (float)$percentual_exito == $perc ? 'var(--primary)' : 'var(--border)' }};
                                           background:{{ (float)$percentual_exito == $perc ? 'var(--primary)' : '#fff' }};
                                           color:{{ (float)$percentual_exito == $perc ? '#fff' : 'var(--muted)' }};">
                                {{ $perc }}%
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Preview do cálculo --}}
                    @if($valorCalculado > 0)
                    <div style="margin-top:10px;padding:12px 14px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;">
                        <div style="font-size:12px;color:#166534;margin-bottom:8px;">
                            📐 <strong>Cálculo:</strong>
                            {{ number_format((float)$percentual_exito,2,',','.') }}%
                            × R$ {{ number_format((float)$valorCausa,2,',','.') }}
                            = <strong style="font-size:14px;">R$ {{ number_format($valorCalculado,2,',','.') }}</strong>
                        </div>
                        <button wire:click="usarValorCalculado" type="button"
                                style="background:#16a34a;color:#fff;border:none;padding:6px 16px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                            ✓ Usar este valor
                        </button>
                    </div>
                    @elseif($valorCausa && !$percentual_exito)
                    <div style="margin-top:8px;font-size:12px;color:var(--muted);">
                        👆 Selecione um percentual acima para calcular automaticamente.
                    </div>
                    @elseif(!$valorCausa)
                    <div style="margin-top:8px;font-size:12px;color:var(--muted);">
                        ℹ️ Selecione um processo com valor da causa para usar o cálculo automático.
                    </div>
                    @endif
                </div>
                @endif

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">Nº DE PARCELAS *</label>
                    <input wire:model="total_parcelas" type="number" min="1" max="360" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">DATA INÍCIO *</label>
                    <input wire:model="data_inicio" type="date" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">DATA FIM (opcional)</label>
                    <input wire:model="data_fim" type="date" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">OBSERVAÇÕES</label>
                    <textarea wire:model="observacoes" rows="2" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;resize:vertical;"></textarea>
                </div>
            </div>

            @if($total_parcelas > 1)
            <div style="background:#f0f4f8;padding:12px;border-radius:8px;font-size:13px;color:var(--primary);">
                💡 Serão geradas <strong>{{ $total_parcelas }} parcelas</strong> de
                <strong>R$ {{ $valor_contrato ? number_format((float)$valor_contrato/$total_parcelas,2,',','.') : '0,00' }}</strong>
                a partir de {{ $data_inicio ? \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') : '—' }}
            </div>
            @endif

            <div class="modal-footer">
                <button wire:click="$set('modalHonorario',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarHonorario" class="btn btn-primary">💾 Salvar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Parcelas --}}
@if($modalParcelas && $parcelasModal !== null)
<div class="modal-backdrop">
    <div class="modal" style="max-width:700px;">
        <div class="modal-header">
            <div>
                <span class="modal-title">💰 Parcelas</span>
                <div style="font-size:13px;color:var(--muted);">{{ $honorarioNome }}</div>
            </div>
            <button wire:click="$set('modalParcelas',false)" class="modal-close">✕</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="text-align:center;">#</th>
                        <th>Vencimento</th>
                        <th style="text-align:right;">Valor</th>
                        <th style="text-align:center;">Status</th>
                        <th class="hide-sm">Pagamento</th>
                        <th class="hide-sm" style="text-align:right;">Valor Pago</th>
                        <th style="text-align:center;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcelasModal as $p)
                    <tr>
                        <td style="text-align:center;font-weight:600;">{{ $p->numero_parcela }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y') }}</td>
                        <td style="text-align:right;">R$ {{ number_format($p->valor,2,',','.') }}</td>
                        <td style="text-align:center;">
                            @php
                                $cores = ['pago'=>'var(--success)','pendente'=>'var(--warning)','atrasado'=>'var(--danger)','cancelado'=>'var(--muted)'];
                                $labels = ['pago'=>'✅ Pago','pendente'=>'⏳ Pendente','atrasado'=>'⚠️ Atrasado','cancelado'=>'❌ Cancelado'];
                            @endphp
                            <span style="color:{{ $cores[$p->status] ?? 'var(--muted)' }};font-size:12px;font-weight:600;">
                                {{ $labels[$p->status] ?? $p->status }}
                            </span>
                        </td>
                        <td class="hide-sm" style="color:var(--muted);">
                            {{ $p->data_pagamento ? \Carbon\Carbon::parse($p->data_pagamento)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="hide-sm" style="text-align:right;color:var(--success);">
                            {{ $p->valor_pago ? 'R$ ' . number_format($p->valor_pago,2,',','.') : '—' }}
                        </td>
                        <td style="text-align:center;">
                            @if(in_array($p->status, ['pendente','atrasado']))
                            <button wire:click="abrirPagamento({{ $p->id }})" class="btn btn-primary btn-sm">💵 Pagar</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Modal Pagamento --}}
@if($modalPagamento)
<div class="modal-backdrop" style="z-index:1100;">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">💵 Registrar Pagamento</span>
            <button wire:click="$set('modalPagamento',false)" class="modal-close">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);">DATA DO PAGAMENTO</label>
                <input wire:model="data_pagamento" type="date" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);">VALOR PAGO (R$)</label>
                <input wire:model="valor_pago" type="number" step="0.01" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);">FORMA DE PAGAMENTO</label>
                <select wire:model="forma_pagamento" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                    <option value="pix">PIX</option>
                    <option value="transferencia">Transferência</option>
                    <option value="boleto">Boleto</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cheque">Cheque</option>
                    <option value="cartao">Cartão</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);">OBSERVAÇÕES</label>
                <input wire:model="obs_pagamento" type="text" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
            </div>
            <div class="modal-footer">
                <button wire:click="$set('modalPagamento',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="registrarPagamento" class="btn btn-primary">✅ Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
