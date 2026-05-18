<div>
{{-- KPIs --}}
<div class="stat-grid" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-label">Contratos Ativos</div>
        <div class="stat-val">{{ $totalAtivos }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Receita Mensal Prevista</div>
        <div class="stat-val">R$ {{ number_format($receitaMensal, 2, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Em Aberto</div>
        <div class="stat-val" style="color:var(--danger);">R$ {{ number_format($emAberto, 2, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Recebido Este Mês</div>
        <div class="stat-val" style="color:#16a34a;">R$ {{ number_format($recebidoMes, 2, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    {{-- Header --}}
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h2 style="margin:0;font-size:17px;font-weight:700;color:var(--primary);">Contratos Mensais</h2>
        <button wire:click="abrirModal()" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Contrato
        </button>
    </div>

    {{-- Filtros --}}
    <div class="filter-bar">
        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por descrição ou cliente…" style="flex:2;min-width:180px;">
        <select wire:model.live="filtroStatus" style="max-width:160px;">
            <option value="">Todos os status</option>
            <option value="ativo">Ativo</option>
            <option value="suspenso">Suspenso</option>
            <option value="encerrado">Encerrado</option>
        </select>
    </div>

    {{-- Tabela --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Dia Vcto</th>
                    <th>Periodicidade</th>
                    <th>Início</th>
                    <th>Status</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contratos as $c)
                <tr>
                    <td>{{ $c->cliente?->nome ?? '—' }}</td>
                    <td>{{ $c->descricao }}</td>
                    <td>R$ {{ number_format($c->valor, 2, ',', '.') }}</td>
                    <td>Dia {{ $c->dia_vencimento }}</td>
                    <td>{{ $c->periodicidade_label }}</td>
                    <td>{{ $c->data_inicio->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $badge = match($c->status) {
                                'ativo'     => ['#dcfce7','#166534','#bbf7d0'],
                                'suspenso'  => ['#fef9c3','#92400e','#fde68a'],
                                default     => ['#f1f5f9','#475569','#cbd5e1'],
                            };
                        @endphp
                        <span style="background:{{ $badge[0] }};color:{{ $badge[1] }};border:1px solid {{ $badge[2] }};border-radius:6px;padding:2px 8px;font-size:12px;font-weight:600;">
                            {{ $c->status_label }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-actions" style="justify-content:center;">
                            <a href="{{ route('contratos-mensais.mensalidades', $c->id) }}" class="btn-action btn-action-blue" title="Ver Mensalidades">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button wire:click="abrirModal({{ $c->id }})" class="btn-action btn-action-yellow" title="Editar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="confirmarExcluir({{ $c->id }})" class="btn-action btn-action-red" title="Excluir">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M8 8h8M8 16h5"/></svg></div>
                            <div class="empty-state-title">Nenhum contrato encontrado</div>
                            <div class="empty-state-sub">Clique em "Novo Contrato" para começar.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($contratos->hasPages())
    <div class="pagination-bar">
        <span>{{ $contratos->total() }} registro(s)</span>
        <div class="page-btns">
            <button class="page-btn" wire:click="previousPage" @disabled(!$contratos->onFirstPage())>‹ Ant.</button>
            <span class="page-current">{{ $contratos->currentPage() }} / {{ $contratos->lastPage() }}</span>
            <button class="page-btn" wire:click="nextPage" @disabled(!$contratos->hasMorePages())>Próx. ›</button>
        </div>
    </div>
    @endif
</div>

{{-- Modal Formulário --}}
@if($modal)
<div class="modal-backdrop" style="display:flex;" wire:click.self="fecharModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <h3 style="margin:0;font-size:16px;font-weight:700;">
                {{ $contratoId ? 'Editar Contrato' : 'Novo Contrato Mensal' }}
            </h3>
            <button wire:click="fecharModal" class="btn-action" style="width:32px;height:32px;">✕</button>
        </div>
        <div class="modal-body" style="padding:20px 0 0;">
            <form wire:submit.prevent="salvar">

                {{-- Cliente (autocomplete) --}}
                <div class="form-group" style="position:relative;">
                    <label class="form-label">Cliente *</label>
                    <input type="text" wire:model.live.debounce.300ms="clienteBusca"
                        placeholder="Digite o nome para buscar…"
                        autocomplete="off"
                        style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('cliente_id') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                    @if(count($clienteSugestoes) > 0)
                    <div style="position:absolute;top:100%;left:0;right:0;background:var(--white);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:200px;overflow-y:auto;">
                        @foreach($clienteSugestoes as $s)
                        <div wire:click="selecionarCliente({{ $s->id }}, '{{ addslashes($s->nome) }}')"
                            style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);"
                            onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                            {{ $s->nome }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @if($cliente_id && $clienteBusca)
                    <button wire:click="limparCliente" type="button" style="position:absolute;right:10px;top:34px;background:none;border:none;cursor:pointer;color:var(--muted);font-size:16px;padding:0;">✕</button>
                    @endif
                    @error('cliente_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                {{-- Descrição --}}
                <div class="form-group">
                    <label class="form-label">Descrição *</label>
                    <input type="text" wire:model="descricao" placeholder="Ex.: Assessoria Jurídica Mensal"
                        style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('descricao') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                    @error('descricao')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:14px;">
                    {{-- Valor --}}
                    <div class="form-group">
                        <label class="form-label">Valor (R$) *</label>
                        <input type="number" step="0.01" min="0" wire:model="valor"
                            style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('valor') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                        @error('valor')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Dia Vencimento --}}
                    <div class="form-group">
                        <label class="form-label">Dia Vencimento *</label>
                        <input type="number" min="1" max="28" wire:model="dia_vencimento"
                            style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('dia_vencimento') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                        @error('dia_vencimento')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:14px;">
                    {{-- Periodicidade --}}
                    <div class="form-group">
                        <label class="form-label">Periodicidade *</label>
                        <select wire:model="periodicidade"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                            @foreach(\App\Models\ContratoMensal::periodicidadeLabels() as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select wire:model="status"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                            @foreach(\App\Models\ContratoMensal::statusLabels() as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Data Início *</label>
                        <input type="date" wire:model="data_inicio"
                            style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('data_inicio') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                        @error('data_inicio')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data Fim</label>
                        <input type="date" wire:model="data_fim"
                            style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('data_fim') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                        @error('data_fim')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Observações --}}
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <textarea wire:model="observacoes" rows="2"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:vertical;"></textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <button type="button" wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="salvar">{{ $contratoId ? 'Salvar' : 'Criar Contrato' }}</span>
                        <span wire:loading wire:target="salvar">Salvando…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Confirmação Exclusão --}}
@if($modalExcluir)
<div class="modal-backdrop" style="display:flex;">
    <div class="modal" style="max-width:400px;text-align:center;padding:32px 28px;">
        <div style="font-size:36px;margin-bottom:12px;">🗑️</div>
        <p style="font-size:15px;color:var(--text);margin-bottom:24px;line-height:1.6;">
            Tem certeza que deseja excluir este contrato?<br>
            <span style="font-size:13px;color:var(--muted);">As mensalidades pendentes serão canceladas.</span>
        </p>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button wire:click="cancelarExcluir" class="btn btn-secondary">Cancelar</button>
            <button wire:click="excluir" class="btn btn-danger">Excluir</button>
        </div>
    </div>
</div>
@endif
</div>
