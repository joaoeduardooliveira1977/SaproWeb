<div>

{{-- ── Cabeçalho + ações ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">Processos</h2>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            {{ $processos->total() }} processo{{ $processos->total() !== 1 ? 's' : '' }} encontrado{{ $processos->total() !== 1 ? 's' : '' }}
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        <button wire:click="exportarCsv" wire:loading.attr="disabled"
            class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando…</span>
        </button>
        <a href="{{ route('processos.novo') }}" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Processo
        </a>
    </div>
</div>

{{-- ── Filtros ── --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <div style="flex:1;min-width:200px;position:relative;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="busca"
                placeholder="Buscar por nº, cliente, parte contrária..."
                style="width:100%;padding:8px 10px 8px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        </div>
        <select wire:model.live="status"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
            <option value="">Todos os status</option>
            <option value="Ativo">Ativo</option>
            <option value="Arquivado">Arquivado</option>
            <option value="Encerrado">Encerrado</option>
        </select>
        <select wire:model.live="fase_id"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
            <option value="">Todas as fases</option>
            @foreach($fases as $f)
            <option value="{{ $f->id }}">{{ $f->descricao }}</option>
            @endforeach
        </select>
        <select wire:model.live="risco_id"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:120px;">
            <option value="">Todos os riscos</option>
            @foreach($riscos as $r)
            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
            @endforeach
        </select>
        @if($busca || $status || $fase_id || $risco_id)
        <button wire:click="$set('busca',''); $set('status',''); $set('fase_id',''); $set('risco_id','')"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:none;color:var(--muted);cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:5px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar
        </button>
        @endif
    </div>
</div>

{{-- ── Tabela ── --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table style="border-collapse:collapse;width:100%;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Processo</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Cliente / Parte Contrária</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Fase / Risco</th>
                    <th class="hide-md" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Advogado</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processos as $p)
                @php
                    $statusCor = match($p->status) {
                        'Ativo'     => ['bg' => '#dcfce7', 'text' => '#16a34a'],
                        'Arquivado' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                        'Encerrado' => ['bg' => '#fef3c7', 'text' => '#d97706'],
                        default     => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                    };
                    $riscoCor = $p->risco?->cor_hex ?? '#94a3b8';
                @endphp
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">

                    {{-- Número + status + data --}}
                    <td style="padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <a href="{{ route('processos.show', $p->id) }}"
                                style="font-weight:700;font-size:14px;color:#2563a8;text-decoration:none;letter-spacing:.3px;">
                                {{ $p->numero }}
                            </a>
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $statusCor['bg'] }};color:{{ $statusCor['text'] }};">
                                {{ $p->status }}
                            </span>
                        </div>
                        @if($p->data_distribuicao)
                        <div style="font-size:11px;color:var(--muted);margin-top:3px;">
                            Distribuído em {{ $p->data_distribuicao->format('d/m/Y') }}
                        </div>
                        @endif
                    </td>

                    {{-- Cliente / Parte contrária --}}
                    <td style="padding:14px 16px;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">
                            {{ $p->cliente?->nome ?? '—' }}
                        </div>
                        @if($p->parteContraria?->nome || $p->parte_contraria)
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;display:flex;align-items:center;gap:4px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ $p->parteContraria?->nome ?? $p->parte_contraria }}
                        </div>
                        @endif
                    </td>

                    {{-- Fase / Risco --}}
                    <td class="hide-sm" style="padding:14px 16px;">
                        <div style="display:flex;flex-direction:column;gap:5px;">
                            @if($p->fase)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:600;background:#eff6ff;color:#1e40af;width:fit-content;">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="#1e40af"><circle cx="12" cy="12" r="10"/></svg>
                                {{ $p->fase->descricao }}
                            </span>
                            @endif
                            @if($p->risco)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $riscoCor }}22;color:{{ $riscoCor }};border:1px solid {{ $riscoCor }}44;width:fit-content;">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="{{ $riscoCor }}"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                                {{ $p->risco->descricao }}
                            </span>
                            @endif
                        </div>
                    </td>

                    {{-- Advogado --}}
                    <td class="hide-md" style="padding:14px 16px;font-size:13px;color:var(--text);">
                        {{ $p->advogado?->nome ?? '—' }}
                    </td>

                    {{-- Ações --}}
                    <td style="padding:14px 16px;text-align:center;">
                        <div style="display:flex;justify-content:center;gap:4px;">
                            <a href="{{ route('processos.show', $p->id) }}" title="Ver detalhes"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#eff6ff;color:#2563a8;text-decoration:none;transition:background .15s;"
                                onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="{{ route('processos.editar', $p->id) }}" title="Editar"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;text-decoration:none;transition:background .15s;"
                                onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <a href="{{ route('processos.andamentos', $p->id) }}" title="Andamentos" class="hide-xs"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#fff7ed;color:#d97706;text-decoration:none;transition:background .15s;"
                                onmouseover="this.style.background='#fed7aa'" onmouseout="this.style.background='#fff7ed'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </a>
                            <a href="{{ route('processos.custas', $p->id) }}" title="Custas" class="hide-xs"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#faf5ff;color:#7c3aed;text-decoration:none;transition:background .15s;"
                                onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#faf5ff'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </a>
                            <button wire:click="confirmarArquivar({{ $p->id }})" title="Arquivar" class="hide-xs"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#f1f5f9';this.style.color='#64748b'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <div style="font-size:14px;font-weight:500;">Nenhum processo encontrado</div>
                        <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou cadastre um novo processo.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
        <span style="font-size:13px;color:var(--muted);">
            @if($processos->total() > 0)
                Mostrando {{ $processos->firstItem() }}–{{ $processos->lastItem() }} de {{ $processos->total() }}
            @else
                Nenhum resultado
            @endif
        </span>
        <div style="display:flex;align-items:center;gap:6px;">
            <button wire:click="previousPage" @disabled($processos->onFirstPage())
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $processos->onFirstPage() ? '.4' : '1' }};">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Anterior
            </button>
            <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                {{ $processos->currentPage() }} / {{ $processos->lastPage() }}
            </span>
            <button wire:click="nextPage" @disabled(!$processos->hasMorePages())
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $processos->hasMorePages() ? '1' : '.4' }};">
                Próxima
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- Modal confirmação arquivar --}}
@if($confirmandoExclusao)
<div class="modal-backdrop">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                </div>
                <span class="modal-title">Arquivar Processo</span>
            </div>
        </div>
        <p style="font-size:14px;color:var(--muted);margin-bottom:20px;line-height:1.6;">
            Tem certeza que deseja arquivar este processo? Esta ação pode ser revertida posteriormente.
        </p>
        <div class="modal-footer">
            <button wire:click="cancelarExclusao" class="btn btn-outline">Cancelar</button>
            <button wire:click="arquivar" class="btn btn-danger">Arquivar</button>
        </div>
    </div>
</div>
@endif

</div>
