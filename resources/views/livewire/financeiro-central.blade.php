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
    <div style="display:flex;gap:8px;align-items:center;">
        <button wire:click="exportarCsv" wire:loading.attr="disabled" class="btn btn-outline" style="display:flex;align-items:center;gap:6px;">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando…</span>
        </button>
        <button wire:click="abrirModal()" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Lançamento
        </button>
    </div>
</div>

{{-- ── Filtros ── --}}
<style>
.fin-filter-bar { background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:10px 12px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;margin-bottom:16px; }
.fin-filter-bar input,.fin-filter-bar select { padding:7px 9px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:var(--white);color:var(--text);outline:none;transition:border-color .15s; }
.fin-filter-bar input:focus,.fin-filter-bar select:focus { border-color:var(--primary-light); }
</style>
<div class="fin-filter-bar">
    <div style="position:relative;flex:0 1 240px;min-width:160px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
            style="position:absolute;left:9px;top:50%;transform:translateY(-50%);pointer-events:none;">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar cliente ou descrição..."
            style="width:100%;padding-left:28px;box-sizing:border-box;">
    </div>

    <div style="display:flex;align-items:center;gap:2px;">
        <button wire:click="mesAnterior" title="Mês anterior"
            style="padding:7px 8px;border:1.5px solid var(--border);border-radius:7px 0 0 7px;background:var(--white);cursor:pointer;color:var(--muted);line-height:1;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <input wire:model.live="filtroMes" type="month"
            style="width:130px;border-radius:0;border-left:none;border-right:none;">
        <button wire:click="mesSeguinte" title="Próximo mês"
            style="padding:7px 8px;border:1.5px solid var(--border);border-radius:0 7px 7px 0;background:var(--white);cursor:pointer;color:var(--muted);line-height:1;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

    <select wire:model.live="filtroStatus" style="width:120px;">
        <option value="">Todos status</option>
        <option value="previsto">Previsto</option>
        <option value="recebido">Recebido</option>
        <option value="atrasado">Atrasado</option>
        <option value="cancelado">Cancelado</option>
    </select>

    <select wire:model.live="filtroTipo" style="width:110px;">
        <option value="">Todos tipos</option>
        <option value="receita">Receita</option>
        <option value="despesa">Despesa</option>
        <option value="repasse">Repasse</option>
    </select>

    <select wire:model.live="filtroCliente" style="width:170px;">
        <option value="">Todos os clientes</option>
        @foreach($clientes as $cl)
        <option value="{{ $cl['id'] }}">{{ $cl['nome'] }}</option>
        @endforeach
    </select>

    @if($busca || $filtroStatus || $filtroTipo || $filtroMes !== now()->format('Y-m') || $filtroCliente)
    <button wire:click="$set('busca',''); $set('filtroStatus',''); $set('filtroTipo',''); $set('filtroCliente',''); $set('filtroMes','{{ now()->format('Y-m') }}')"
        style="padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:none;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:4px;white-space:nowrap;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Limpar
    </button>
    @endif
</div>

{{-- ── Tabela de lançamentos ── --}}
<div style="background:var(--white);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            @php
            $thBase = "padding:11px 14px;font-weight:700;color:var(--muted);font-size:11px;white-space:nowrap;cursor:pointer;user-select:none;";
            $seta = fn($col) => $ordenarPor === $col ? ($ordenarDir === 'asc' ? ' ↑' : ' ↓') : '';
            @endphp
            <tr style="background:#f8fafc;border-bottom:1.5px solid var(--border);">
                <th wire:click="ordenar('vencimento')" style="{{ $thBase }}text-align:left;">Vencimento{{ $seta('vencimento') }}</th>
                <th wire:click="ordenar('cliente')"    style="{{ $thBase }}text-align:left;">Cliente{{ $seta('cliente') }}</th>
                <th style="padding:11px 14px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;">Descrição</th>
                <th wire:click="ordenar('tipo')"       style="{{ $thBase }}text-align:left;">Tipo{{ $seta('tipo') }}</th>
                <th wire:click="ordenar('valor')"      style="{{ $thBase }}text-align:right;">Valor{{ $seta('valor') }}</th>
                <th wire:click="ordenar('status')"     style="{{ $thBase }}text-align:center;">Status{{ $seta('status') }}</th>
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
                @php
                $srv = $l->servico;
                if ($srv && $srv->tipo === 'exito') {
                    $origemLabel = 'Êxito';
                    $origemBg    = '#fef3c7'; $origemCor = '#92400e';
                } elseif ($srv && $srv->tipo === 'consultoria') {
                    $origemLabel = 'Consultoria';
                    $origemBg    = '#ede9fe'; $origemCor = '#6d28d9';
                } elseif ($srv) {
                    $origemLabel = 'Serviço';
                    $origemBg    = '#dbeafe'; $origemCor = '#1d4ed8';
                } elseif ($l->processo_id && str_starts_with($l->descricao, 'Reembolso')) {
                    $origemLabel = 'Reembolso de Custa';
                    $origemBg    = '#ffedd5'; $origemCor = '#c2410c';
                } elseif ($l->contrato_id) {
                    $origemLabel = 'Contrato';
                    $origemBg    = '#dcfce7'; $origemCor = '#15803d';
                } else {
                    $origemLabel = 'Manual';
                    $origemBg    = '#f1f5f9'; $origemCor = '#64748b';
                }
                @endphp
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap;">
                    <span style="font-size:10px;font-weight:700;padding:1px 7px;border-radius:99px;background:{{ $origemBg }};color:{{ $origemCor }};white-space:nowrap;">
                        {{ $origemLabel }}
                    </span>
                    @if($srv && $srv->tipo === 'exito' && $srv->percentual)
                    <span style="font-size:10px;color:#92400e;">{{ $srv->percentual }}%</span>
                    @endif
                </div>
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
                    @elseif($l->status === 'cancelado')
                        <button wire:click="excluir({{ $l->id }})" wire:confirm="Excluir permanentemente este lançamento?" title="Excluir"
                            style="padding:5px 7px;border:1.5px solid #fecaca;border-radius:6px;background:#fef2f2;cursor:pointer;display:flex;align-items:center;gap:4px;font-size:11px;color:#dc2626;font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            Excluir
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
                            <option value="{{ $f['id'] }}">{{ $f['nome'] }}</option>
                            @endforeach
                        @else
                            @foreach($clientes as $cl)
                            <option value="{{ $cl['id'] }}">{{ $cl['nome'] }}</option>
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
