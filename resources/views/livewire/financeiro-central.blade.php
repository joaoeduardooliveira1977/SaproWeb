<div>

{{-- ── KPIs ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:22px;">
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #2563eb;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">A Receber</div>
        <div style="font-size:20px;font-weight:800;color:#2563eb;">R$ {{ number_format($totalPrevisto,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #16a34a;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Recebido</div>
        <div style="font-size:20px;font-weight:800;color:#16a34a;">R$ {{ number_format($totalRecebido,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #dc2626;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Atrasado</div>
        <div style="font-size:20px;font-weight:800;color:#dc2626;">R$ {{ number_format($totalAtrasado,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #d97706;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Despesas</div>
        <div style="font-size:20px;font-weight:800;color:#d97706;">R$ {{ number_format($totalDespesa,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #7c3aed;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Repasses a Pagar</div>
        <div style="font-size:20px;font-weight:800;color:#7c3aed;">R$ {{ number_format($totalRepasse,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #16a34a;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Saldo do Mês</div>
        @php $saldo = $totalRecebido - $totalDespesa - $totalRepasse; @endphp
        <div style="font-size:20px;font-weight:800;color:{{ $saldo >= 0 ? '#16a34a' : '#dc2626' }};">
            R$ {{ number_format($saldo,2,',','.') }}
        </div>
    </div>
</div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Financeiro Centralizado
        </h2>
        <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Todos os lançamentos: contratos, honorários e serviços avulsos.</p>
    </div>
    <button wire:click="abrirModal()" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Lançamento
    </button>
</div>

{{-- ── Filtros ── --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
    <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar cliente ou descrição..."
        style="flex:1;min-width:200px;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
    <input wire:model.live="filtroMes" type="month"
        style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
    <select wire:model.live="filtroStatus" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
        <option value="">Todos os status</option>
        <option value="previsto">Previsto</option>
        <option value="recebido">Recebido</option>
        <option value="atrasado">Atrasado</option>
        <option value="cancelado">Cancelado</option>
    </select>
    <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
        <option value="">Todos os tipos</option>
        <option value="receita">Receita</option>
        <option value="despesa">Despesa</option>
        <option value="repasse">Repasse</option>
    </select>
    <select wire:model.live="filtroCliente" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);min-width:180px;">
        <option value="">Todos os clientes</option>
        @foreach($clientes as $cl)
        <option value="{{ $cl->id }}">{{ $cl->nome }}</option>
        @endforeach
    </select>
</div>

{{-- ── Tabela de lançamentos ── --}}
<div style="background:var(--white);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1.5px solid var(--border);">
                <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;white-space:nowrap;">Vencimento</th>
                <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;">Cliente</th>
                <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;">Descrição</th>
                <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;">Origem</th>
                <th style="padding:11px 14px;text-align:right;font-weight:700;color:var(--muted);font-size:11px;">Valor</th>
                <th style="padding:11px 14px;text-align:center;font-weight:700;color:var(--muted);font-size:11px;">Status</th>
                <th style="padding:11px 14px;text-align:right;font-weight:700;color:var(--muted);font-size:11px;"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($lancamentos as $l)
        @php
            $stCor = match($l->status) {
                'recebido' => ['#dcfce7','#16a34a'],
                'atrasado' => ['#fef2f2','#dc2626'],
                'cancelado'=> ['#f1f5f9','#94a3b8'],
                default    => ['#eff6ff','#2563eb'],
            };
            $tipoCor = $l->tipo === 'despesa' ? '#d97706' : ($l->tipo === 'repasse' ? '#7c3aed' : '#16a34a');
        @endphp
        <tr style="border-bottom:1px solid var(--border);transition:background .1s;"
            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
            <td style="padding:11px 14px;white-space:nowrap;">
                <div style="font-weight:600;color:{{ $l->status==='atrasado' ? '#dc2626' : 'var(--text)' }};">
                    {{ $l->vencimento->format('d/m/Y') }}
                </div>
                @if($l->numero_parcela)
                <div style="font-size:10px;color:var(--muted);">
                    Parcela {{ $l->numero_parcela }}{{ $l->total_parcelas ? '/'.$l->total_parcelas : '' }}
                </div>
                @endif
            </td>
            <td style="padding:11px 14px;">
                <div style="font-weight:600;color:var(--text);">{{ $l->cliente?->nome ?? '—' }}</div>
            </td>
            <td style="padding:11px 14px;max-width:260px;">
                <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text);">{{ $l->descricao }}</div>
                @if($l->contrato)
                <div style="font-size:11px;color:var(--muted);">Contrato: {{ $l->contrato->descricao }}</div>
                @endif
                @if($l->processo)
                <div style="font-size:11px;color:var(--muted);">Proc. {{ $l->processo->numero }}</div>
                @endif
            </td>
            <td style="padding:11px 14px;">
                <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:{{ $tipoCor }}18;color:{{ $tipoCor }};">
                    {{ ucfirst($l->tipo) }}
                </span>
            </td>
            <td style="padding:11px 14px;text-align:right;white-space:nowrap;">
                <div style="font-weight:700;color:{{ $tipoCor }};">R$ {{ number_format($l->valor,2,',','.') }}</div>
                @if($l->status === 'recebido' && $l->valor_pago)
                <div style="font-size:10px;color:var(--muted);">Pago: R$ {{ number_format($l->valor_pago,2,',','.') }}</div>
                @endif
            </td>
            <td style="padding:11px 14px;text-align:center;">
                <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;background:{{ $stCor[0] }};color:{{ $stCor[1] }};">
                    {{ ucfirst($l->status) }}
                </span>
                @if($l->status === 'recebido' && $l->data_pagamento)
                <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $l->data_pagamento->format('d/m/Y') }}</div>
                @endif
            </td>
            <td style="padding:11px 14px;text-align:right;">
                <div style="display:flex;gap:5px;justify-content:flex-end;">
                    @if(in_array($l->status, ['previsto','atrasado']))
                    @if($l->tipo === 'repasse')
                    <button wire:click="abrirPagamento({{ $l->id }})" title="Registrar pagamento do repasse"
                        style="padding:5px 8px;border:1.5px solid #ede9fe;border-radius:6px;background:#faf5ff;cursor:pointer;color:#7c3aed;font-size:11px;font-weight:600;white-space:nowrap;">
                        ✓ Pagar
                    </button>
                    @else
                    <button wire:click="abrirPagamento({{ $l->id }})" title="Registrar recebimento"
                        style="padding:5px 8px;border:1.5px solid #bbf7d0;border-radius:6px;background:#f0fdf4;cursor:pointer;color:#16a34a;font-size:11px;font-weight:600;white-space:nowrap;">
                        ✓ Receber
                    </button>
                    @endif
                    <button wire:click="abrirModal({{ $l->id }})" title="Editar"
                        style="padding:5px 7px;border:1.5px solid var(--border);border-radius:6px;background:var(--white);cursor:pointer;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button wire:click="cancelar({{ $l->id }})" wire:confirm="Cancelar este lançamento?" title="Cancelar"
                        style="padding:5px 7px;border:1.5px solid #fecaca;border-radius:6px;background:#fef2f2;cursor:pointer;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">
                Nenhum lançamento encontrado para os filtros selecionados.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:12px;">{{ $lancamentos->links() }}</div>

{{-- ════ MODAL: Novo/Editar Lançamento ════ --}}
@if($modal)
<div style="position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharModal" style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.2);z-index:1;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">{{ $lancamentoId ? 'Editar Lançamento' : 'Novo Lançamento' }}</h3>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;display:flex;flex-direction:column;gap:14px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">
                        {{ $tipo === 'despesa' ? 'Fornecedor' : 'Cliente' }} *
                    </label>
                    <select wire:model.live="clienteId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        <option value="0">— Selecione —</option>
                        @if($tipo === 'despesa')
                            @foreach($fornecedores as $f)
                            <option value="{{ $f->id }}">{{ $f->nome }}</option>
                            @endforeach
                        @else
                            @foreach($clientes as $cl)
                            <option value="{{ $cl->id }}">{{ $cl->nome }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('clienteId')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Tipo *</label>
                    <select wire:model="tipo" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        <option value="receita">Receita</option>
                        <option value="despesa">Despesa</option>
                        <option value="repasse">Repasse</option>
                    </select>
                </div>
            </div>

            @if($contratos)
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Contrato vinculado <span style="font-weight:400;color:var(--muted);">(opcional)</span></label>
                <select wire:model="contratoId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="">— Nenhum —</option>
                    @foreach($contratos as $ct)
                    <option value="{{ $ct['id'] }}">{{ $ct['descricao'] }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Descrição *</label>
                <input wire:model="descricao" type="text" placeholder="Ex: Honorário — Janeiro 2026"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('descricao')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Valor (R$) *</label>
                    <input wire:model="valor" type="text" placeholder="0,00"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('valor')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Vencimento *</label>
                    <input wire:model="vencimento" type="date"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('vencimento')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Observações</label>
                <textarea wire:model="observacoes" rows="2"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--border);">
            <button wire:click="fecharModal" style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:9px 20px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove wire:target="salvar">Salvar</span>
                <span wire:loading wire:target="salvar">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ════ MODAL: Registrar Pagamento ════ --}}
@if($modalPagamento)
<div style="position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharPagamento" style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);z-index:1;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">
                {{ $pagamentoTipo === 'repasse' ? 'Registrar Pagamento de Repasse' : 'Registrar Recebimento' }}
            </h3>
            <button wire:click="fecharPagamento" style="background:none;border:none;cursor:pointer;color:var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;display:flex;flex-direction:column;gap:14px;">
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Data do Recebimento *</label>
                <input wire:model="dataPagamento" type="date"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Valor Recebido (R$) *</label>
                <input wire:model="valorPago" type="text"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Forma de Pagamento</label>
                <select wire:model="formaPagamento" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="pix">PIX</option>
                    <option value="ted">TED / DOC</option>
                    <option value="boleto">Boleto</option>
                    <option value="cheque">Cheque</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao">Cartão</option>
                </select>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--border);">
            <button wire:click="fecharPagamento" style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
            <button wire:click="registrarPagamento" wire:loading.attr="disabled"
                style="padding:9px 20px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove wire:target="registrarPagamento">✓ Confirmar</span>
                <span wire:loading wire:target="registrarPagamento">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
