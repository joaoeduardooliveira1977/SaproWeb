<div>

<style>
.com-table { width:100%;border-collapse:collapse; }
.com-table th { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;padding:8px 12px;border-bottom:2px solid var(--border);text-align:left;background:var(--bg); }
.com-table td { padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.com-table tr:hover td { background:var(--bg); }
.com-badge-pendente { display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#fef9c3;color:#854d0e; }
.com-badge-pago     { display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#dcfce7;color:#16a34a; }
</style>

{{-- Cabeçalho --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:22px;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Comissões de Indicadores
        </h2>
        <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Comissões geradas automaticamente sobre honorários e recebimentos de clientes indicados.</p>
    </div>
    @if(!empty($selecionados))
    <button wire:click="abrirPagamento" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Marcar Pagas ({{ count($selecionados) }})
    </button>
    @endif
</div>

{{-- KPIs --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:20px;">
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #7c3aed;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Total do Filtro</div>
        <div style="font-size:20px;font-weight:800;color:#7c3aed;">R$ {{ number_format((float)$totais->total,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #d97706;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Pendente</div>
        <div style="font-size:20px;font-weight:800;color:#d97706;">R$ {{ number_format((float)$totais->pendente,2,',','.') }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:16px 20px;border:1px solid var(--border);border-left:4px solid #16a34a;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Pago</div>
        <div style="font-size:20px;font-weight:800;color:#16a34a;">R$ {{ number_format((float)$totais->pago,2,',','.') }}</div>
    </div>
</div>

{{-- Filtros --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:12px 16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:16px;">
    <select wire:model.live="filtroIndicador" style="padding:6px 10px;border:1px solid var(--border);border-radius:7px;font-size:13px;background:var(--bg);">
        <option value="">Todos os indicadores</option>
        @foreach($indicadores as $ind)
            <option value="{{ $ind->id }}">{{ $ind->nome }}</option>
        @endforeach
    </select>
    <input wire:model.live="filtroCompetencia" type="month"
           style="padding:6px 10px;border:1px solid var(--border);border-radius:7px;font-size:13px;background:var(--bg);">
    <select wire:model.live="filtroStatus" style="padding:6px 10px;border:1px solid var(--border);border-radius:7px;font-size:13px;background:var(--bg);">
        <option value="">Todos os status</option>
        <option value="pendente">Pendente</option>
        <option value="pago">Pago</option>
    </select>
    <div style="margin-left:auto;display:flex;gap:6px;">
        <button wire:click="selecionarTodos" class="btn btn-outline" style="padding:5px 12px;font-size:12px;">Selecionar todos</button>
        <button wire:click="desmarcarTodos"  class="btn btn-outline" style="padding:5px 12px;font-size:12px;">Desmarcar</button>
    </div>
</div>

{{-- Tabela --}}
<div style="background:var(--white);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
    @if($comissoes->isEmpty())
        <div style="padding:40px;text-align:center;color:var(--muted);font-size:14px;">
            Nenhuma comissão encontrada para o filtro selecionado.
        </div>
    @else
        <table class="com-table">
            <thead>
                <tr>
                    <th style="width:36px;"></th>
                    <th>Indicador</th>
                    <th>Cliente</th>
                    <th>Origem</th>
                    <th>Competência</th>
                    <th style="text-align:right;">Valor Base</th>
                    <th style="text-align:center;">%</th>
                    <th style="text-align:right;">Comissão</th>
                    <th style="text-align:center;">Status</th>
                    <th>Pagamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comissoes as $com)
                <tr>
                    <td>
                        <input type="checkbox"
                               wire:click="toggleSelecionado({{ $com->id }})"
                               @checked(in_array($com->id, $selecionados))
                               @disabled($com->status === 'pago')
                               style="width:15px;height:15px;cursor:pointer;">
                    </td>
                    <td style="font-weight:600;">{{ $com->indicador?->nome ?? '—' }}</td>
                    <td style="color:var(--muted);">{{ $com->pessoa?->nome ?? '—' }}</td>
                    <td style="color:var(--muted);font-size:12px;">
                        {{ $com->origem_tipo === 'recebimento' ? 'Recebimento' : 'Honorário' }}
                        <span style="color:#aaa;">#{{ $com->origem_id }}</span>
                    </td>
                    <td style="color:var(--muted);">{{ $com->competencia?->format('m/Y') }}</td>
                    <td style="text-align:right;">R$ {{ number_format((float)$com->valor_base,2,',','.') }}</td>
                    <td style="text-align:center;color:#7c3aed;font-weight:700;">{{ number_format((float)$com->percentual,2,',','') }}%</td>
                    <td style="text-align:right;font-weight:700;color:#7c3aed;">R$ {{ number_format((float)$com->valor_comissao,2,',','.') }}</td>
                    <td style="text-align:center;">
                        <span class="com-badge-{{ $com->status }}">{{ $com->status === 'pago' ? 'Pago' : 'Pendente' }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:12px;">
                        {{ $com->data_pagamento?->format('d/m/Y') ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div style="margin-top:16px;">{{ $comissoes->links() }}</div>

{{-- Modal pagamento --}}
@if($modalPagamento)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:var(--white);border-radius:14px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.3);" @click.stop>

        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:16px;font-weight:800;margin:0;color:var(--primary);">Registrar Pagamento</h3>
            <button wire:click="$set('modalPagamento', false)" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;line-height:1;">&times;</button>
        </div>

        <div style="padding:20px 24px;">
            <p style="font-size:13px;color:var(--muted);margin:0 0 16px;">
                {{ count($selecionados) }} comissão(ões) selecionada(s) serão marcadas como pagas.
            </p>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Data de Pagamento *</label>
                <input wire:model="dataPagamento" type="date"
                       style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
        </div>

        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="$set('modalPagamento', false)" class="btn btn-outline">Cancelar</button>
            <button wire:click="confirmarPagamento" wire:loading.attr="disabled" class="btn btn-primary">
                <span wire:loading.remove wire:target="confirmarPagamento">Confirmar Pagamento</span>
                <span wire:loading wire:target="confirmarPagamento">Salvando…</span>
            </button>
        </div>

    </div>
</div>
@endif

</div>
