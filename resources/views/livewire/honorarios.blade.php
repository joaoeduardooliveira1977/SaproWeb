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
        <div style="position:relative;flex:1;min-width:200px;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input wire:model.live="busca" type="text" placeholder="Buscar cliente ou descrição..." style="padding-left:34px;width:100%;">
        </div>
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
            <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> CSV</span>
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
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Cliente</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Processo</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Tipo</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Descrição</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:right;">Contrato</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:right;">Recebido</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:right;">Pendente</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:center;">Parcelas</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:center;">Status</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:center;">Ações</th>
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
                        @if($h->parcelas_atrasadas > 0)<span style="font-size:10px;display:inline-flex;align-items:center;gap:2px;"> <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>{{ $h->parcelas_atrasadas }}</span>@endif
                    </td>
                    <td class="hide-sm" style="text-align:center;font-size:12px;">{{ $h->parcelas_pagas }}/{{ $h->total_parcelas_count }}</td>
                    <td style="text-align:center;">
                        @php $statusCores = ['ativo'=>'var(--success)','encerrado'=>'var(--muted)','suspenso'=>'var(--warning)']; @endphp
                        <span style="color:{{ $statusCores[$h->status] ?? 'var(--muted)' }};font-size:12px;font-weight:600;">{{ ucfirst($h->status) }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions" style="justify-content:center;">
                            <button wire:click="verParcelas({{ $h->id }})" title="Ver parcelas" style="width:30px;height:30px;border:none;border-radius:6px;background:#f0fdf4;color:#16a34a;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></button>
                            <button wire:click="editarHonorario({{ $h->id }})" title="Editar" style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button wire:click="excluirHonorario({{ $h->id }})" wire:confirm="Excluir este honorário?" title="Excluir" style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></button>
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
            <button wire:click="$set('modalHonorario',false)" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="3" x2="12" y2="21"/><path d="M3 6l9-3 9 3"/><path d="M3 18l4-8 4 8"/><path d="M13 18l4-8 4 8"/><line x1="2" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="22" y2="18"/></svg> <span>Valor da causa: <strong>R$ {{ number_format((float)$valorCausa,2,',','.') }}</strong></span>
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
                        <div style="font-size:11px;color:var(--muted);margin-bottom:5px;display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> Sugestões rápidas:</div>
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
                        <div style="font-size:12px;color:#166534;margin-bottom:8px;display:flex;align-items:center;gap:4px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="8" y2="10"/><line x1="12" y1="10" x2="12" y2="10"/><line x1="16" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="8" y2="14"/><line x1="12" y1="14" x2="12" y2="14"/><line x1="16" y1="14" x2="16" y2="14"/><line x1="8" y1="18" x2="12" y2="18"/><line x1="16" y1="18" x2="16" y2="18"/></svg> <strong>Cálculo:</strong>
                            {{ number_format((float)$percentual_exito,2,',','.') }}%
                            × R$ {{ number_format((float)$valorCausa,2,',','.') }}
                            = <strong style="font-size:14px;">R$ {{ number_format($valorCalculado,2,',','.') }}</strong>
                        </div>
                        <button wire:click="usarValorCalculado" type="button"
                                style="background:#16a34a;color:#fff;border:none;padding:6px 16px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Usar este valor
                        </button>
                    </div>
                    @elseif($valorCausa && !$percentual_exito)
                    <div style="margin-top:8px;font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg> Selecione um percentual acima para calcular automaticamente.
                    </div>
                    @elseif(!$valorCausa)
                    <div style="margin-top:8px;font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> Selecione um processo com valor da causa para usar o cálculo automático.
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
            <div style="background:#f0f4f8;padding:12px;border-radius:8px;font-size:13px;color:var(--primary);display:flex;align-items:center;gap:6px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> Serão geradas <strong>{{ $total_parcelas }} parcelas</strong> de
                <strong>R$ {{ $valor_contrato ? number_format((float)$valor_contrato/$total_parcelas,2,',','.') : '0,00' }}</strong>
                a partir de {{ $data_inicio ? \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') : '—' }}
            </div>
            @endif

            <div class="modal-footer">
                <button wire:click="$set('modalHonorario',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarHonorario" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar</button>
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
                <span class="modal-title" style="display:inline-flex;align-items:center;gap:8px;"><span style="width:24px;height:24px;border-radius:6px;background:#f0fdf4;color:#16a34a;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span> Parcelas</span>
                <div style="font-size:13px;color:var(--muted);">{{ $honorarioNome }}</div>
            </div>
            <button wire:click="$set('modalParcelas',false)" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:center;">#</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Vencimento</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:right;">Valor</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:center;">Status</th>
                        <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Pagamento</th>
                        <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:right;">Valor Pago</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;text-align:center;">Ação</th>
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
                                $labels = ['pago'=>'Pago','pendente'=>'Pendente','atrasado'=>'Atrasado','cancelado'=>'Cancelado'];
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
                            <button wire:click="abrirPagamento({{ $p->id }})" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Pagar</button>
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
            <span class="modal-title" style="display:inline-flex;align-items:center;gap:8px;"><span style="width:24px;height:24px;border-radius:6px;background:#f0fdf4;color:#16a34a;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span> Registrar Pagamento</span>
            <button wire:click="$set('modalPagamento',false)" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
                <button wire:click="registrarPagamento" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
