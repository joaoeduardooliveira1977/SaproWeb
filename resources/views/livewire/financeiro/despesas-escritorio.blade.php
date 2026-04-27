<div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Despesas do Escritório</h1>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            Controle de gastos operacionais: aluguel, software, salários, contas e outros
            <span style="color:#cbd5e1;margin:0 6px;">|</span>
            <a href="{{ route('financeiro.hub') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Voltar para central</a>
        </p>
    </div>
    <button wire:click="abrirModal()"
        style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--primary);color:#fff;border-radius:8px;font-size:13px;font-weight:700;border:none;cursor:pointer;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nova Despesa
    </button>
</div>

{{-- ── KPIs ── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;padding:20px;">
        <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Total do período</div>
        <div style="font-size:26px;font-weight:800;color:var(--text);">R$ {{ number_format($totais->total ?? 0, 2, ',', '.') }}</div>
    </div>
    <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:20px;">
        <div style="font-size:12px;color:#16a34a;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Pago</div>
        <div style="font-size:26px;font-weight:800;color:#16a34a;">R$ {{ number_format($totais->pago ?? 0, 2, ',', '.') }}</div>
    </div>
    <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:14px;padding:20px;">
        <div style="font-size:12px;color:#dc2626;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Pendente</div>
        <div style="font-size:26px;font-weight:800;color:#dc2626;">R$ {{ number_format($totais->pendente ?? 0, 2, ',', '.') }}</div>
    </div>
</div>

{{-- ── Filtros ── --}}
<div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
    <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">Competência</label>
        <input type="month" wire:model.live="filtroCompetencia"
            style="padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);width:160px;">
    </div>
    <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">Categoria</label>
        <select wire:model.live="filtroCategoria"
            style="padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);width:200px;">
            <option value="">Todas</option>
            @foreach($categorias as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">Status</label>
        <select wire:model.live="filtroStatus"
            style="padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);width:140px;">
            <option value="">Todos</option>
            <option value="pendente">Pendente</option>
            <option value="pago">Pago</option>
        </select>
    </div>
    @if($filtroCompetencia || $filtroCategoria || $filtroStatus)
    <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;color:transparent;">.</label>
        <button wire:click="$set('filtroCompetencia',''); $set('filtroCategoria',''); $set('filtroStatus','')"
            style="padding:7px 12px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;color:var(--muted);cursor:pointer;">
            Limpar filtros
        </button>
    </div>
    @endif
</div>

{{-- ── Tabela ── --}}
<div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;">
    @if($despesas->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:var(--muted);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:12px;opacity:.4;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <div style="font-size:14px;font-weight:600;">Nenhuma despesa encontrada</div>
            <div style="font-size:13px;margin-top:4px;">Registre a primeira despesa do escritório clicando em "Nova Despesa".</div>
        </div>
    @else
    <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid var(--border);">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Data</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Descrição</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Categoria</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Fornecedor</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Valor</th>
                    <th class="hide-sm" style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Vencimento</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Status</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($despesas as $d)
                <tr style="border-bottom:1px solid var(--border);" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:13px 16px;font-size:13px;color:var(--muted);">{{ $d->data->format('d/m/Y') }}</td>
                    <td style="padding:13px 16px;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $d->descricao }}</div>
                    </td>
                    <td style="padding:13px 16px;">
                        @if($d->categoria)
                        <span style="display:inline-block;padding:3px 8px;border-radius:99px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;">
                            {{ $categorias[$d->categoria] ?? ucfirst($d->categoria) }}
                        </span>
                        @else
                        <span style="color:var(--muted);font-size:12px;">—</span>
                        @endif
                    </td>
                    <td class="hide-sm" style="padding:13px 16px;font-size:13px;color:var(--muted);">{{ $d->fornecedor?->nome ?? '—' }}</td>
                    <td style="padding:13px 16px;text-align:right;font-size:13px;font-weight:700;color:var(--text);">
                        R$ {{ number_format($d->valor, 2, ',', '.') }}
                    </td>
                    <td class="hide-sm" style="padding:13px 16px;text-align:center;font-size:12px;color:var(--muted);">
                        @if($d->data_vencimento)
                            @php $atrasado = !$d->pago && $d->data_vencimento->isPast(); @endphp
                            <span style="color:{{ $atrasado ? '#dc2626' : 'var(--muted)' }};font-weight:{{ $atrasado ? '700' : '400' }};">
                                {{ $d->data_vencimento->format('d/m/Y') }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding:13px 16px;text-align:center;">
                        @if($d->pago)
                            <span style="display:inline-block;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#f0fdf4;color:#16a34a;">Pago</span>
                        @else
                            <span style="display:inline-block;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#fef2f2;color:#dc2626;">Pendente</span>
                        @endif
                    </td>
                    <td style="padding:13px 16px;text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <button wire:click="abrirModal({{ $d->id }})"
                                style="padding:4px 10px;background:#eff6ff;color:#2563a8;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                Editar
                            </button>
                            <button wire:click="togglePago({{ $d->id }})" wire:loading.attr="disabled"
                                style="padding:4px 10px;background:{{ $d->pago ? '#fff7ed' : '#f0fdf4' }};color:{{ $d->pago ? '#c2410c' : '#16a34a' }};border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                {{ $d->pago ? 'Reabrir' : 'Pagar' }}
                            </button>
                            <button wire:click="excluir({{ $d->id }})"
                                wire:confirm="Excluir esta despesa?"
                                style="padding:4px 10px;background:#fef2f2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                Excluir
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($despesas->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--border);">
        {{ $despesas->links() }}
    </div>
    @endif
    @endif
</div>

{{-- ── Modal ── --}}
@if($modalAberto)
<div style="position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;" wire:click.self="fecharModal">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);">

        {{-- Header --}}
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <div style="font-size:16px;font-weight:800;color:var(--text);">
                {{ $pagamentoId ? 'Editar Despesa' : 'Nova Despesa do Escritório' }}
            </div>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;line-height:1;">×</button>
        </div>

        <form wire:submit="salvar" style="padding:24px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                {{-- Data --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                        Data <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="date" wire:model="data"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                    @error('data')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                </div>

                {{-- Categoria --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                        Categoria <span style="color:#dc2626;">*</span>
                    </label>
                    <select wire:model="categoria"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                        <option value="">— Selecione —</option>
                        @foreach($categorias as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('categoria')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                </div>

            </div>

            {{-- Descrição --}}
            <div style="margin-top:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                    Descrição <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" wire:model="descricao" placeholder="Ex: Aluguel de outubro"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                @error('descricao')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">

                {{-- Valor --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                        Valor (R$) <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="number" wire:model="valor" step="0.01" min="0.01" placeholder="0,00"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                    @error('valor')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                </div>

                {{-- Vencimento --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                        Vencimento
                    </label>
                    <input type="date" wire:model="dataVencimento"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                </div>

            </div>

            {{-- Fornecedor --}}
            @if($fornecedores->count())
            <div style="margin-top:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                    Fornecedor
                </label>
                <select wire:model="fornecedorId"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                    <option value="">— Nenhum —</option>
                    @foreach($fornecedores as $f)
                        <option value="{{ $f->id }}">{{ $f->nome }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Pago --}}
            <div style="margin-top:20px;padding:16px;background:#f8fafc;border-radius:10px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" wire:model.live="pago" style="width:16px;height:16px;cursor:pointer;padding:0;border:none;">
                    <span style="font-size:13px;font-weight:700;color:var(--text);">Despesa já paga</span>
                </label>

                @if($pago)
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                            Data do pagamento <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="date" wire:model="dataPagamento"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                        @error('dataPagamento')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">
                            Valor pago (R$) <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="number" wire:model="valorPago" step="0.01" min="0.01" placeholder="0,00"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);">
                        @error('valorPago')<div style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endif
            </div>

            {{-- Rodapé --}}
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="button" wire:click="fecharModal"
                    style="padding:9px 20px;background:#f1f5f9;color:var(--muted);border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="padding:9px 24px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                    <span wire:loading.remove wire:target="salvar">{{ $pagamentoId ? 'Salvar alterações' : 'Registrar despesa' }}</span>
                    <span wire:loading wire:target="salvar">Salvando...</span>
                </button>
            </div>

        </form>
    </div>
</div>
@endif

</div>
