<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 Agenda</span>
            <button wire:click="abrirModal()" class="btn btn-primary btn-sm">＋ Novo Evento</button>
        </div>

        <div class="search-bar" style="flex-wrap:wrap">
            <input type="date" wire:model.live="data_ini" style="width:160px">
            <input type="date" wire:model.live="data_fim" style="width:160px">
            <select wire:model.live="tipo" style="width:140px">
                <option value="">Todos os tipos</option>
                @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px">
                <input type="checkbox" wire:model.live="so_pendentes" style="width:auto"> Só pendentes
            </label>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Data/Hora</th><th>Evento</th><th>Local</th><th>Tipo</th><th>Processo</th><th>Urgente</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($eventos as $ev)
                    @php $cor = match($ev->tipo) { 'Prazo'=>'#dc2626','Audiência'=>'#d97706',default=>'#2563a8' }; @endphp
                    <tr style="{{ $ev->concluido ? 'opacity:.5' : '' }}">
                        <td><strong>{{ $ev->data_hora->format('d/m/Y') }}</strong> {{ $ev->data_hora->format('H:i') }}</td>
                        <td>{{ $ev->titulo }}</td>
                        <td>{{ $ev->local ?? '—' }}</td>
                        <td><span class="badge" style="background:{{ $cor }}22;color:{{ $cor }}">{{ $ev->tipo }}</span></td>
                        <td>{{ $ev->processo?->numero ?? '—' }}</td>
                        <td>{{ $ev->urgente ? '🔴 Sim' : '—' }}</td>
                        <td>
                            @if(!$ev->concluido)
                            <button wire:click="concluir({{ $ev->id }})" class="btn-icon" title="Concluir">✅</button>
                            @endif
                            <button wire:click="abrirModal({{ $ev->id }})" class="btn-icon">✏️</button>
                            <button wire:click="excluir({{ $ev->id }})" class="btn-icon" onclick="return confirm('Remover evento?')">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:#64748b;padding:24px">Nenhum evento encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $eventos->links() }}</div>
    </div>

    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal" style="width:500px">
            <div class="modal-header">
                <span class="modal-title">{{ $eventoId ? '✏️ Editar Evento' : '📅 Novo Evento' }}</span>
                <button wire:click="fecharModal" class="modal-close">×</button>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Título *</label>
                <input type="text" wire:model="titulo" placeholder="Descrição do evento">
                @error('titulo')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data e Hora *</label>
                    <input type="datetime-local" wire:model="data_hora">
                    @error('data_hora')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo *</label>
                    <select wire:model="tipo_evento">
                        @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Local</label>
                    <input type="text" wire:model="local" placeholder="Local do evento">
                </div>
                <div class="form-field">
                    <label class="lbl">Processo</label>
                    <select wire:model="processo_id">
                        <option value="">Nenhum</option>
                        @foreach($processos as $proc)
                            <option value="{{ $proc->id }}">{{ $proc->numero }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                    <input type="checkbox" wire:model="urgente" style="width:auto">
                    <span>🔴 Marcar como urgente</span>
                </label>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Detalhes adicionais..."></textarea>
            </div>

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success">✓ Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
