<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚖️ Processos</span>
            <a href="{{ route('processos.novo') }}" class="btn btn-primary btn-sm">＋ Novo Processo</a>
        </div>

        {{-- Filtros --}}
        <div class="search-bar">
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nº, cliente, advogado...">
            <select wire:model.live="status" style="width:140px">
                <option value="">Todos os status</option>
                <option value="Ativo">Ativo</option>
                <option value="Arquivado">Arquivado</option>
                <option value="Encerrado">Encerrado</option>
                <option value="Suspenso">Suspenso</option>
            </select>
            <select wire:model.live="fase_id" style="width:140px">
                <option value="">Todas as fases</option>
                @foreach($fases as $f)
                    <option value="{{ $f->id }}">{{ $f->descricao }}</option>
                @endforeach
            </select>
            <select wire:model.live="risco_id" style="width:120px">
                <option value="">Todos os riscos</option>
                @foreach($riscos as $r)
                    <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tabela --}}
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nº Processo</th><th>Cliente</th><th>Tipo</th>
                        <th>Fase</th><th>Risco</th><th>Advogado</th>
                        <th>Status</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processos as $p)
                    <tr>
                        <td><a href="{{ route('processos.show', $p->id) }}" class="text-primary">{{ $p->numero }}</a></td>
                        <td>{{ $p->cliente?->nome }}</td>
                        <td>{{ $p->tipoAcao?->descricao ?? '—' }}</td>
                        <td>
                            @if($p->fase)
                            <span class="badge" style="background:#2563a822;color:#2563a8">{{ $p->fase->descricao }}</span>
                            @else —
                            @endif
                        </td>
                        <td>
                            @if($p->risco)
                            <span class="badge" style="background:{{ $p->risco->cor_hex }}22;color:{{ $p->risco->cor_hex }}">{{ $p->risco->descricao }}</span>
                            @else —
                            @endif
                        </td>
                        <td>{{ $p->advogado?->nome ?? '—' }}</td>
                        <td>
                            @php $corStatus = match($p->status) { 'Ativo'=>'#16a34a', 'Arquivado'=>'#64748b', 'Encerrado'=>'#1a3a5c', default=>'#d97706' }; @endphp
                            <span class="badge" style="background:{{ $corStatus }}22;color:{{ $corStatus }}">{{ $p->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('processos.show', $p->id) }}" class="btn-icon" title="Ver">👁️</a>
                            <a href="{{ route('processos.editar', $p->id) }}" class="btn-icon" title="Editar">✏️</a>
                            <button wire:click="confirmarArquivar({{ $p->id }})" class="btn-icon" title="Arquivar">🗄️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;padding:24px">Nenhum processo encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        

<div style="display:flex; justify-content:space-between; align-items:center; padding:16px; font-size:13px; color:#64748b;">
    <span>Mostrando {{ $processos->firstItem() }} a {{ $processos->lastItem() }} de {{ $processos->total() }} processos</span>
    <div style="display:flex; gap:8px;">
        @if($processos->onFirstPage())
            <span style="padding:6px 12px; background:#f1f5f9; border-radius:6px; color:#94a3b8;">← Anterior</span>
        @else
            <button wire:click="previousPage" style="padding:6px 12px; background:#2563a8; color:white; border:none; border-radius:6px; cursor:pointer;">← Anterior</button>
        @endif

        <span style="padding:6px 12px; background:#f1f5f9; border-radius:6px;">
            {{ $processos->currentPage() }} / {{ $processos->lastPage() }}
        </span>

        @if($processos->hasMorePages())
            <button wire:click="nextPage" style="padding:6px 12px; background:#2563a8; color:white; border:none; border-radius:6px; cursor:pointer;">Próxima →</button>
        @else
            <span style="padding:6px 12px; background:#f1f5f9; border-radius:6px; color:#94a3b8;">Próxima →</span>
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
