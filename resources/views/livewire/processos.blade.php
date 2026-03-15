<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚖️ Processos</span>
            <div class="card-actions">
                <button wire:click="exportarCsv" wire:loading.attr="disabled"
                    class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
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
            <table>
                <thead>
                    <tr>
                        <th>Nº Processo</th>
                        <th>Cliente</th>
                        <th class="hide-sm">Parte Contrária</th>
                        <th style="width:160px;text-align:center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processos as $p)
                    <tr>
                        <td style="font-weight:600;color:var(--primary);">{{ $p->numero }}</td>
                        <td>{{ $p->cliente?->nome ?? '—' }}</td>
                        <td class="hide-sm">{{ $p->parteContraria?->nome ?? ($p->parte_contraria ?? '—') }}</td>
                        <td style="text-align:center;">
                            <div class="btn-actions">
                                <a href="{{ route('processos.show', $p->id) }}" title="Ver detalhes" class="btn-action btn-action-blue">👁️</a>
                                <a href="{{ route('processos.editar', $p->id) }}" title="Editar" class="btn-action btn-action-green">✏️</a>
                                <a href="{{ route('agenda') }}?processo_id={{ $p->id }}" title="Agenda" class="btn-action btn-action-purple hide-xs">📅</a>
                                <a href="{{ route('processos.custas', $p->id) }}" title="Custas" class="btn-action btn-action-yellow hide-xs">💰</a>
                                <a href="{{ route('processos.andamentos', $p->id) }}" title="Andamentos" class="btn-action btn-action-red hide-xs">📋</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:32px;">Nenhum processo encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="pagination-bar">
            <span>
                @if($processos->total() > 0)
                    Mostrando {{ $processos->firstItem() }}–{{ $processos->lastItem() }} de {{ $processos->total() }}
                @else Nenhum resultado @endif
            </span>
            <div class="page-btns">
                <button wire:click="previousPage" class="page-btn" @disabled($processos->onFirstPage())>← Anterior</button>
                <span class="page-current">{{ $processos->currentPage() }} / {{ $processos->lastPage() }}</span>
                <button wire:click="nextPage" class="page-btn" @disabled(!$processos->hasMorePages())>Próxima →</button>
            </div>
        </div>

    </div>

    {{-- Modal confirmação arquivar --}}
    @if($confirmandoExclusao)
    <div class="modal-backdrop">
        <div class="modal" style="max-width:400px">
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
