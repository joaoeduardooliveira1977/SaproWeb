<div>
<style>
    .urgencia-normal   { border-left-color: #16a34a; }
    .urgencia-alerta   { border-left-color: #ca8a04; }
    .urgencia-atencao  { border-left-color: #ea580c; }
    .urgencia-urgente  { border-left-color: #dc2626; }
    .urgencia-vencido  { border-left-color: #991b1b; background: #fff5f5; }
    .urgencia-cumprido { border-left-color: #94a3b8; opacity: .75; }
    .urgencia-perdido  { border-left-color: #1e293b; background: #fafafa; }

    .tag-fatal { background:#fce7f3;color:#9d174d;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px; }

    .dias-badge {
        display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;
    }
    .dias-normal   { background:#dcfce7;color:#166534; }
    .dias-alerta   { background:#fef9c3;color:#854d0e; }
    .dias-atencao  { background:#ffedd5;color:#9a3412; }
    .dias-urgente  { background:#fee2e2;color:#991b1b; }
    .dias-vencido  { background:#991b1b;color:#fff; }
    .dias-cumprido { background:#f1f5f9;color:#64748b; }
    .dias-perdido  { background:#1e293b;color:#fff; }

    .filtros-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center; }
    .filtros-bar select, .filtros-bar input { padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px; }
</style>

{{-- Flash --}}
@if(session('sucesso'))
    <div class="alert alert-success">✅ {{ session('sucesso') }}</div>
@endif

{{-- ══ KPIs ══ --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    <div class="stat-card" style="border-left-color:var(--primary);">
        <div class="stat-icon">⏳</div>
        <div class="stat-val">{{ $totalAbertos }}</div>
        <div class="stat-label">Prazos em aberto</div>
    </div>
    <div class="stat-card" style="border-left-color:#ca8a04;">
        <div class="stat-icon">📅</div>
        <div class="stat-val" style="color:#ca8a04;">{{ $vencendoHoje }}</div>
        <div class="stat-label">Vencem hoje</div>
    </div>
    <div class="stat-card" style="border-left-color:#dc2626;">
        <div class="stat-icon">🚨</div>
        <div class="stat-val" style="color:#dc2626;">{{ $vencidos }}</div>
        <div class="stat-label">Vencidos (não cumpridos)</div>
    </div>
    <div class="stat-card" style="border-left-color:#9d174d;">
        <div class="stat-icon">⚠️</div>
        <div class="stat-val" style="color:#9d174d;">{{ $fatais }}</div>
        <div class="stat-label">Prazos fatais (próx. 5 dias)</div>
    </div>
</div>

{{-- ══ Filtros + botão novo ══ --}}
<div class="filtros-bar">
    <select wire:model.live="filtroStatus">
        <option value="aberto">Em aberto</option>
        <option value="cumprido">Cumpridos</option>
        <option value="perdido">Perdidos</option>
        <option value="todos">Todos</option>
    </select>

    <select wire:model.live="filtroTipo">
        <option value="">Todos os tipos</option>
        @foreach(['Prazo','Prazo Fatal','Audiência','Diligência','Recurso'] as $t)
            <option value="{{ $t }}">{{ $t }}</option>
        @endforeach
    </select>

    <select wire:model.live="filtroResponsavel" style="min-width:180px;">
        <option value="">Todos os responsáveis</option>
        @foreach($usuarios as $u)
            <option value="{{ $u->id }}">{{ $u->nome }}</option>
        @endforeach
    </select>

    <select wire:model.live="filtroProcesso" style="min-width:220px;">
        <option value="">Todos os processos</option>
        @foreach($processos as $p)
            <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome ?? '—' }}</option>
        @endforeach
    </select>

    <button class="btn btn-primary btn-sm" wire:click="abrirModal()" style="margin-left:auto;">
        + Novo Prazo
    </button>
</div>

{{-- ══ Lista ══ --}}
<div class="card" style="padding:0;overflow:hidden;">
    @if($prazos->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:40px 0;">Nenhum prazo encontrado.</p>
    @else
        @foreach($prazos as $prazo)
        @php
            $urg = $prazo->urgencia();
            $dias = $prazo->diasRestantes();
        @endphp
        <div style="border-left:4px solid transparent;padding:14px 18px;border-bottom:1px solid var(--border);"
             class="urgencia-{{ $urg }}">
            <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">

                {{-- Indicador de dias --}}
                <div style="min-width:90px;text-align:center;padding-top:2px;">
                    @if($urg === 'cumprido')
                        <span class="dias-badge dias-cumprido">✓ Cumprido</span>
                    @elseif($urg === 'perdido')
                        <span class="dias-badge dias-perdido">✗ Perdido</span>
                    @elseif($urg === 'vencido')
                        <span class="dias-badge dias-vencido">{{ abs($dias) }}d vencido</span>
                    @elseif($dias === 0)
                        <span class="dias-badge dias-urgente">Vence hoje!</span>
                    @else
                        <span class="dias-badge dias-{{ $urg }}">{{ $dias }} dia(s)</span>
                    @endif
                </div>

                {{-- Conteúdo --}}
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                        <span style="font-weight:700;font-size:14px;">{{ $prazo->titulo }}</span>
                        @if($prazo->prazo_fatal)
                            <span class="tag-fatal">⚠ Fatal</span>
                        @endif
                        <span class="badge" style="background:#e2e8f0;color:#475569;font-size:11px;">{{ $prazo->tipo }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--muted);display:flex;gap:16px;flex-wrap:wrap;">
                        <span>📅 <strong>Prazo:</strong> {{ $prazo->data_prazo->format('d/m/Y') }}</span>
                        @if($prazo->processo)
                            <span>⚖️ {{ $prazo->processo->numero }}
                                @if($prazo->processo->cliente) — {{ $prazo->processo->cliente->nome }} @endif
                            </span>
                        @endif
                        @if($prazo->responsavel)
                            <span>👤 {{ $prazo->responsavel->nome }}</span>
                        @endif
                        @if($prazo->dias)
                            <span>🔢 {{ $prazo->dias }} dias {{ $prazo->tipo_contagem }}</span>
                        @endif
                        @if($prazo->status === 'cumprido' && $prazo->data_cumprimento)
                            <span>✅ Cumprido em {{ $prazo->data_cumprimento->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    @if($prazo->descricao)
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">{{ Str::limit($prazo->descricao, 120) }}</div>
                    @endif
                </div>

                {{-- Ações --}}
                <div style="display:flex;gap:4px;align-items:center;flex-shrink:0;">
                    @if($prazo->status === 'aberto')
                        <button class="btn btn-success btn-sm" wire:click="marcarCumprido({{ $prazo->id }})"
                                wire:confirm="Marcar este prazo como cumprido?" title="Marcar cumprido">
                            ✓ Cumprido
                        </button>
                        <button class="btn btn-sm" style="background:#fce7f3;color:#9d174d;"
                                wire:click="marcarPerdido({{ $prazo->id }})"
                                wire:confirm="Marcar como prazo perdido?" title="Marcar perdido">
                            ✗ Perdido
                        </button>
                    @else
                        <button class="btn btn-sm btn-secondary" wire:click="reabrir({{ $prazo->id }})" title="Reabrir">
                            ↩ Reabrir
                        </button>
                    @endif
                    <button class="btn-icon" title="Editar" wire:click="abrirModal({{ $prazo->id }})">✏️</button>
                    <button class="btn-icon" title="Excluir" wire:click="confirmarExcluirPrazo({{ $prazo->id }})">🗑️</button>
                </div>

            </div>
        </div>
        @endforeach

        <div style="padding:12px 18px;">
            {{ $prazos->links() }}
        </div>
    @endif
</div>

{{-- ══ Confirmação de exclusão ══ --}}
@if($confirmarExcluir)
<div class="modal-backdrop">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Confirmar Exclusão</span>
            <button class="modal-close" wire:click="fecharModal">✕</button>
        </div>
        <p>Deseja realmente excluir este prazo? Esta ação não pode ser desfeita.</p>
        <div class="modal-footer">
            <button class="btn btn-secondary" wire:click="fecharModal">Cancelar</button>
            <button class="btn btn-danger" wire:click="excluir">Excluir</button>
        </div>
    </div>
</div>
@endif

{{-- ══ Modal Cadastro/Edição ══ --}}
@if($modalAberto)
<div class="modal-backdrop">
    <div class="modal" style="max-width:700px;">
        <div class="modal-header">
            <span class="modal-title">{{ $prazoid ? 'Editar Prazo' : 'Novo Prazo' }}</span>
            <button class="modal-close" wire:click="fecharModal">✕</button>
        </div>

        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr;">
            {{-- Título --}}
            <div class="form-field" style="grid-column:1/-1;">
                <label class="lbl">Título *</label>
                <input type="text" wire:model="titulo" placeholder="Ex: Prazo para contestação">
                @error('titulo') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Tipo --}}
            <div class="form-field">
                <label class="lbl">Tipo *</label>
                <select wire:model="tipo">
                    @foreach(['Prazo','Prazo Fatal','Audiência','Diligência','Recurso'] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Processo --}}
            <div class="form-field" style="grid-column:span 2;">
                <label class="lbl">Processo</label>
                <select wire:model="processo_id">
                    <option value="">— Nenhum —</option>
                    @foreach($processos as $p)
                        <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome ?? '—' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Responsável --}}
            <div class="form-field">
                <label class="lbl">Responsável</label>
                <select wire:model="responsavel_id">
                    <option value="">— Nenhum —</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Data início --}}
            <div class="form-field">
                <label class="lbl">Data de Início *</label>
                <input type="date" wire:model.live="data_inicio">
                @error('data_inicio') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Contagem --}}
            <div class="form-field">
                <label class="lbl">Tipo de contagem</label>
                <select wire:model.live="tipo_contagem">
                    <option value="corridos">Dias corridos</option>
                    <option value="uteis">Dias úteis</option>
                </select>
            </div>

            {{-- Dias --}}
            <div class="form-field">
                <label class="lbl">Quantidade de dias</label>
                <input type="number" wire:model.live="dias" min="0" placeholder="Ex: 15"
                       style="font-size:15px;font-weight:600;">
                <span style="font-size:10px;color:var(--muted);">Preencha para calcular automaticamente</span>
            </div>

            {{-- Data prazo --}}
            <div class="form-field" style="grid-column:span 2;">
                <label class="lbl">Data do Prazo * <span style="color:var(--muted);font-weight:400;">(calculada ou manual)</span></label>
                <input type="date" wire:model="data_prazo"
                       style="border-color:{{ $prazo_fatal ? '#9d174d' : 'var(--border)' }};
                              font-size:15px;font-weight:700;color:{{ $prazo_fatal ? '#9d174d' : 'inherit' }};">
                @error('data_prazo') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Prazo Fatal --}}
            <div class="form-field" style="justify-content:flex-end;padding-bottom:6px;">
                <label class="lbl">Prazo Fatal</label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:8px;">
                    <input type="checkbox" wire:model="prazo_fatal" style="width:auto;accent-color:#9d174d;">
                    <span style="color:#9d174d;font-weight:600;font-size:13px;">⚠ É prazo fatal</span>
                </label>
            </div>

            {{-- Descrição --}}
            <div class="form-field" style="grid-column:1/-1;">
                <label class="lbl">Descrição</label>
                <textarea wire:model="descricao" rows="2" placeholder="Detalhes sobre o prazo..."></textarea>
            </div>

            {{-- Observações --}}
            <div class="form-field" style="grid-column:1/-1;">
                <label class="lbl">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Observações internas..."></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" wire:click="fecharModal">Cancelar</button>
            <button class="btn btn-primary" wire:click="salvar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvar">💾 Salvar</span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
