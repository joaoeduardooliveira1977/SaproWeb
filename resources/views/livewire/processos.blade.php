<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚖️ Processos</span>
            <div style="display:flex;gap:8px;">
                <button wire:click="exportarCsv" wire:loading.attr="disabled"
                    class="btn btn-sm" style="background:#f1f5f9;color:#475569;border:1.5px solid var(--border);" title="Exportar CSV">
                    <span wire:loading.remove wire:target="exportarCsv">📥 CSV</span>
                    <span wire:loading wire:target="exportarCsv">Gerando…</span>
                </button>
                <a href="{{ route('processos.novo') }}" class="btn btn-primary btn-sm">＋ Novo Processo</a>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="search-bar">
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nº, cliente, parte contrária...">
        </div>

        {{-- Tabela --}}
        <div class="table-wrap">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#1a3a5c;color:#fff;">
                        <th style="padding:11px 14px;text-align:left;font-size:13px;font-weight:600;">Nº Processo</th>
                        <th style="padding:11px 14px;text-align:left;font-size:13px;font-weight:600;">Cliente</th>
                        <th style="padding:11px 14px;text-align:left;font-size:13px;font-weight:600;">Parte Contrária</th>
                        <th style="padding:11px 14px;text-align:center;font-size:13px;font-weight:600;width:180px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processos as $i => $p)
                    <tr style="background:{{ $i % 2 === 0 ? '#ffffff' : '#eff6ff' }};border-bottom:1px solid #e2e8f0;transition:background .15s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='{{ $i % 2 === 0 ? '#ffffff' : '#eff6ff' }}'">

                        <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#1a3a5c;">
                            {{ $p->numero }}
                        </td>

                        <td style="padding:10px 14px;font-size:13px;color:#334155;">
                            {{ $p->cliente?->nome ?? '—' }}
                        </td>

                        <td style="padding:10px 14px;font-size:13px;color:#334155;">
                            {{ $p->parteContraria?->nome ?? ($p->parte_contraria ?? '—') }}
                        </td>

                        <td style="padding:10px 14px;text-align:center;">
                            <div style="display:inline-flex;gap:4px;align-items:center;">

                                {{-- Ver detalhes --}}
                                <a href="{{ route('processos.show', $p->id) }}"
                                   title="Ver detalhes"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#eff6ff;border:1px solid #bfdbfe;color:#2563a8;text-decoration:none;font-size:15px;transition:background .15s;"
                                   onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                    👁️
                                </a>

                                {{-- Editar --}}
                                <a href="{{ route('processos.editar', $p->id) }}"
                                   title="Editar processo"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;text-decoration:none;font-size:15px;transition:background .15s;"
                                   onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    ✏️
                                </a>

                                {{-- Agenda --}}
                                <a href="{{ route('agenda') }}?processo_id={{ $p->id }}"
                                   title="Agenda do processo"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#faf5ff;border:1px solid #e9d5ff;color:#7c3aed;text-decoration:none;font-size:15px;transition:background .15s;"
                                   onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#faf5ff'">
                                    📅
                                </a>

                                {{-- Custas --}}
                                <a href="{{ route('processos.custas', $p->id) }}"
                                   title="Custas do processo"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#fffbeb;border:1px solid #fde68a;color:#d97706;text-decoration:none;font-size:15px;transition:background .15s;"
                                   onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                                    💰
                                </a>

                                {{-- Andamentos --}}
                                <a href="{{ route('processos.andamentos', $p->id) }}"
                                   title="Andamentos do processo"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;text-decoration:none;font-size:15px;transition:background .15s;"
                                   onmouseover="this.style.background='#ffe4e6'" onmouseout="this.style.background='#fff1f2'">
                                    📋
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#64748b;padding:32px;font-size:14px;">
                            Nenhum processo encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;font-size:13px;color:#64748b;">
            <span>
                @if($processos->total() > 0)
                    Mostrando {{ $processos->firstItem() }} a {{ $processos->lastItem() }} de {{ $processos->total() }} processos
                @else
                    Nenhum resultado
                @endif
            </span>
            <div style="display:flex;gap:8px;">
                @if($processos->onFirstPage())
                    <span style="padding:6px 12px;background:#f1f5f9;border-radius:6px;color:#94a3b8;">← Anterior</span>
                @else
                    <button wire:click="previousPage" style="padding:6px 12px;background:#2563a8;color:white;border:none;border-radius:6px;cursor:pointer;">← Anterior</button>
                @endif

                <span style="padding:6px 12px;background:#f1f5f9;border-radius:6px;">
                    {{ $processos->currentPage() }} / {{ $processos->lastPage() }}
                </span>

                @if($processos->hasMorePages())
                    <button wire:click="nextPage" style="padding:6px 12px;background:#2563a8;color:white;border:none;border-radius:6px;cursor:pointer;">Próxima →</button>
                @else
                    <span style="padding:6px 12px;background:#f1f5f9;border-radius:6px;color:#94a3b8;">Próxima →</span>
                @endif
            </div>
        </div>

    </div>

    {{-- Modal confirmação arquivar --}}
    @if($confirmandoExclusao)
    <div class="modal-backdrop">
        <div class="modal" style="width:400px">
            <div class="modal-header">
                <span class="modal-title">🗄️ Confirmar Arquivamento</span>
            </div>
            <p style="font-size:14px;color:#64748b;margin-bottom:20px">Tem certeza que deseja arquivar este processo? Esta ação pode ser revertida posteriormente.</p>
            <div class="modal-footer">
                <button wire:click="cancelarExclusao" class="btn btn-outline">Cancelar</button>
                <button wire:click="arquivar" class="btn btn-danger">Arquivar</button>
            </div>
        </div>
    </div>
    @endif
</div>
