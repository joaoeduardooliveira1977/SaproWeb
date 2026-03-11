<div>
{{-- Flash --}}
@if(session('success'))
    <div style="background:#dcfce7;border:1px solid #16a34a;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Cards Resumo --}}
<div class="grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="border-left:4px solid var(--primary);">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Total Contratado</div>
        <div style="font-size:24px;font-weight:700;color:var(--primary);">R$ {{ number_format($resumo->total_contratado,2,',','.') }}</div>
        <div style="font-size:12px;color:var(--muted);">{{ $resumo->total_contratos }} contratos ativos</div>
    </div>
    <div class="card" style="border-left:4px solid var(--success);">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Recebido</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);">R$ {{ number_format($resumo->total_recebido,2,',','.') }}</div>
    </div>
    <div class="card" style="border-left:4px solid var(--warning);">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Pendente</div>
        <div style="font-size:24px;font-weight:700;color:var(--warning);">R$ {{ number_format($resumo->total_pendente,2,',','.') }}</div>
    </div>
    <div class="card" style="border-left:4px solid var(--danger);">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Atrasado</div>
        <div style="font-size:24px;font-weight:700;color:var(--danger);">R$ {{ number_format($resumo->total_atrasado,2,',','.') }}</div>
    </div>
</div>

{{-- Filtros + Botão --}}
<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input wire:model.live="busca" type="text" placeholder="🔍 Buscar cliente ou descrição..." style="flex:1;min-width:200px;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
        <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos os tipos</option>
            <option value="fixo_mensal">Fixo Mensal</option>
            <option value="exito">Êxito</option>
            <option value="hora">Por Hora</option>
            <option value="ato_diligencia">Ato/Diligência</option>
        </select>
        <select wire:model.live="filtroStatus" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos os status</option>
            <option value="ativo">Ativo</option>
            <option value="encerrado">Encerrado</option>
            <option value="suspenso">Suspenso</option>
        </select>
        <button wire:click="novoHonorario" class="btn btn-primary">+ Novo Honorário</button>
    </div>
</div>

{{-- Tabela --}}
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:var(--primary);color:#fff;">
                <th style="padding:10px 12px;text-align:left;">Cliente</th>
                <th style="padding:10px 12px;text-align:left;">Processo</th>
                <th style="padding:10px 12px;text-align:left;">Tipo</th>
                <th style="padding:10px 12px;text-align:left;">Descrição</th>
                <th style="padding:10px 12px;text-align:right;">Contrato</th>
                <th style="padding:10px 12px;text-align:right;">Recebido</th>
                <th style="padding:10px 12px;text-align:right;">Pendente</th>
                <th style="padding:10px 12px;text-align:center;">Parcelas</th>
                <th style="padding:10px 12px;text-align:center;">Status</th>
                <th style="padding:10px 12px;text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($honorarios as $h)
            <tr style="border-bottom:1px solid var(--border);" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:10px 12px;font-weight:600;">{{ $h->cliente_nome }}</td>
                <td style="padding:10px 12px;color:var(--muted);">{{ $h->processo_numero ?? '—' }}</td>
                <td style="padding:10px 12px;">
                    @php
                        $tipos = ['fixo_mensal'=>['label'=>'Fixo','color'=>'#2563a8'],
                                  'exito'=>['label'=>'Êxito','color'=>'#16a34a'],
                                  'hora'=>['label'=>'Hora','color'=>'#d97706'],
                                  'ato_diligencia'=>['label'=>'Ato','color'=>'#7c3aed']];
                        $t = $tipos[$h->tipo] ?? ['label'=>$h->tipo,'color'=>'#64748b'];
                    @endphp
                    <span style="background:{{ $t['color'] }}20;color:{{ $t['color'] }};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">{{ $t['label'] }}</span>
                </td>
                <td style="padding:10px 12px;">{{ $h->descricao }}</td>
                <td style="padding:10px 12px;text-align:right;font-weight:600;">R$ {{ number_format($h->valor_contrato,2,',','.') }}</td>
                <td style="padding:10px 12px;text-align:right;color:var(--success);">R$ {{ number_format($h->valor_recebido,2,',','.') }}</td>
                <td style="padding:10px 12px;text-align:right;color:{{ $h->parcelas_atrasadas > 0 ? 'var(--danger)' : 'var(--warning)' }};">
                    R$ {{ number_format($h->valor_pendente,2,',','.') }}
                    @if($h->parcelas_atrasadas > 0)
                        <span style="font-size:10px;"> ⚠️{{ $h->parcelas_atrasadas }}</span>
                    @endif
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <span style="font-size:12px;">{{ $h->parcelas_pagas }}/{{ $h->total_parcelas_count }}</span>
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    @php
                        $statusCores = ['ativo'=>'var(--success)','encerrado'=>'var(--muted)','suspenso'=>'var(--warning)'];
                        $cor = $statusCores[$h->status] ?? 'var(--muted)';
                    @endphp
                    <span style="color:{{ $cor }};font-size:12px;font-weight:600;">{{ ucfirst($h->status) }}</span>
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <div style="display:flex;gap:6px;justify-content:center;">
                        <button wire:click="verParcelas({{ $h->id }})" title="Ver parcelas" style="background:none;border:none;cursor:pointer;font-size:16px;">💰</button>
                        <button wire:click="editarHonorario({{ $h->id }})" title="Editar" style="background:none;border:none;cursor:pointer;font-size:16px;">✏️</button>
                        <button wire:click="excluirHonorario({{ $h->id }})" title="Excluir" onclick="return confirm('Excluir este honorário?')" style="background:none;border:none;cursor:pointer;font-size:16px;">🗑️</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="padding:32px;text-align:center;color:var(--muted);">Nenhum honorário cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Honorário --}}
@if($modalHonorario)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:var(--primary);">{{ $honorarioId ? 'Editar' : 'Novo' }} Honorário</h3>
            <button wire:click="$set('modalHonorario',false)" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
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
                    <select wire:model="processo_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="">Sem processo vinculado</option>
                        @foreach($processos as $p)
                            <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->vara }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">TIPO *</label>
                    <select wire:model="tipo" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
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
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">VALOR TOTAL (R$) *</label>
                    <input wire:model="valor_contrato" type="number" step="0.01" placeholder="0,00" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                @if($tipo === 'exito')
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">% ÊXITO SOBRE CAUSA</label>
                    <input wire:model="percentual_exito" type="number" step="0.01" placeholder="Ex: 20" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
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

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="$set('modalHonorario',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarHonorario" class="btn btn-primary">💾 Salvar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Parcelas --}}
@if($modalParcelas && $parcelasModal !== null)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:700px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h3 style="margin:0;color:var(--primary);">💰 Parcelas</h3>
                <div style="font-size:13px;color:var(--muted);">{{ $honorarioNome }}</div>
            </div>
            <button wire:click="$set('modalParcelas',false)" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:16px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:var(--bg);">
                        <th style="padding:8px 12px;text-align:center;">#</th>
                        <th style="padding:8px 12px;text-align:left;">Vencimento</th>
                        <th style="padding:8px 12px;text-align:right;">Valor</th>
                        <th style="padding:8px 12px;text-align:center;">Status</th>
                        <th style="padding:8px 12px;text-align:left;">Pagamento</th>
                        <th style="padding:8px 12px;text-align:right;">Valor Pago</th>
                        <th style="padding:8px 12px;text-align:center;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcelasModal as $p)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:8px 12px;text-align:center;font-weight:600;">{{ $p->numero_parcela }}</td>
                        <td style="padding:8px 12px;">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y') }}</td>
                        <td style="padding:8px 12px;text-align:right;">R$ {{ number_format($p->valor,2,',','.') }}</td>
                        <td style="padding:8px 12px;text-align:center;">
                            @php
                                $cores = ['pago'=>'var(--success)','pendente'=>'var(--warning)','atrasado'=>'var(--danger)','cancelado'=>'var(--muted)'];
                                $labels = ['pago'=>'✅ Pago','pendente'=>'⏳ Pendente','atrasado'=>'⚠️ Atrasado','cancelado'=>'❌ Cancelado'];
                            @endphp
                            <span style="color:{{ $cores[$p->status] ?? 'var(--muted)' }};font-size:12px;font-weight:600;">
                                {{ $labels[$p->status] ?? $p->status }}
                            </span>
                        </td>
                        <td style="padding:8px 12px;color:var(--muted);">
                            {{ $p->data_pagamento ? \Carbon\Carbon::parse($p->data_pagamento)->format('d/m/Y') : '—' }}
                        </td>
                        <td style="padding:8px 12px;text-align:right;color:var(--success);">
                            {{ $p->valor_pago ? 'R$ ' . number_format($p->valor_pago,2,',','.') : '—' }}
                        </td>
                        <td style="padding:8px 12px;text-align:center;">
                            @if(in_array($p->status, ['pendente','atrasado']))
                            <button wire:click="abrirPagamento({{ $p->id }})" class="btn btn-primary" style="padding:4px 10px;font-size:12px;">
                                💵 Pagar
                            </button>
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
<div style="position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1100;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:400px;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:var(--primary);">💵 Registrar Pagamento</h3>
            <button wire:click="$set('modalPagamento',false)" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
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
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="$set('modalPagamento',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="registrarPagamento" class="btn btn-primary">✅ Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
