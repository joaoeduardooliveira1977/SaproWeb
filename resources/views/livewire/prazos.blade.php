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
</style>

{{-- ══ KPIs ══ --}}
<div class="stat-grid">
    <div class="stat-card" style="border-left-color:var(--primary);">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-val">{{ $totalAbertos }}</div>
        <div class="stat-label">Prazos em aberto</div>
    </div>
    <div class="stat-card" style="border-left-color:#ca8a04;">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="stat-val" style="color:#ca8a04;">{{ $vencendoHoje }}</div>
        <div class="stat-label">Vencem hoje</div>
    </div>
    <div class="stat-card" style="border-left-color:#dc2626;">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="stat-val" style="color:#dc2626;">{{ $vencidos }}</div>
        <div class="stat-label">Vencidos (não cumpridos)</div>
    </div>
    <div class="stat-card" style="border-left-color:#9d174d;">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-val" style="color:#9d174d;">{{ $fatais }}</div>
        <div class="stat-label">Prazos fatais (próx. 5 dias)</div>
    </div>
</div>

{{-- ══ Filtros + botão novo ══ --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

        {{-- Busca com ícone --}}
        <div style="flex:1;min-width:200px;position:relative;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="filtroBusca"
                   placeholder="Buscar por título…"
                   style="width:100%;padding:8px 10px 8px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        </div>

        <select wire:model.live="filtroStatus"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
            <option value="aberto">Em aberto</option>
            <option value="cumprido">Cumpridos</option>
            <option value="perdido">Perdidos</option>
            <option value="todos">Todos</option>
        </select>

        <select wire:model.live="filtroTipo"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
            <option value="">Todos os tipos</option>
            @foreach(['Prazo','Prazo Fatal','Audiência','Diligência','Recurso'] as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        <select wire:model.live="filtroResponsavel"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:140px;">
            <option value="">Todos os responsáveis</option>
            @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->nome }}</option>
            @endforeach
        </select>

        <select wire:model.live="filtroProcesso"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:150px;">
            <option value="">Todos os processos</option>
            @foreach($processos as $p)
                <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome ?? '—' }}</option>
            @endforeach
        </select>

        <input type="date" wire:model.live="filtroDataIni" title="Prazo a partir de"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        <input type="date" wire:model.live="filtroDataFim" title="Prazo até"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">

        @if($filtroBusca || $filtroTipo || $filtroResponsavel || $filtroProcesso || $filtroDataIni || $filtroDataFim)
        <button wire:click="$set('filtroBusca',''); $set('filtroTipo',''); $set('filtroResponsavel',''); $set('filtroProcesso',''); $set('filtroDataIni',''); $set('filtroDataFim','')"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:none;color:var(--muted);cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:5px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar
        </button>
        @endif

        <div style="display:flex;gap:6px;margin-left:auto;">
            <button class="btn btn-sm btn-secondary-outline"
                    wire:click="exportarPdf" wire:loading.attr="disabled" title="Exportar PDF">
                <span wire:loading.remove wire:target="exportarPdf" style="display:flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                    PDF
                </span>
                <span wire:loading wire:target="exportarPdf">Gerando…</span>
            </button>
            <button class="btn btn-sm btn-secondary-outline"
                    wire:click="exportarCsv" wire:loading.attr="disabled" title="Exportar CSV">
                <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </span>
                <span wire:loading wire:target="exportarCsv">Gerando…</span>
            </button>
            <button class="btn btn-primary btn-sm" wire:click="abrirModal()" style="display:flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Prazo
            </button>
        </div>
    </div>
</div>

{{-- ══ Lista ══ --}}
<div class="card" style="padding:0;overflow:hidden;">
    @if($prazos->isEmpty())
        <div style="text-align:center;padding:48px;color:var(--muted);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div style="font-size:14px;font-weight:500;">Nenhum prazo encontrado</div>
            <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou cadastre um novo prazo.</div>
        </div>
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
                            <span class="tag-fatal">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:middle;margin-right:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                Fatal
                            </span>
                        @endif
                        <span class="badge" style="background:#e2e8f0;color:#475569;font-size:11px;">{{ $prazo->tipo }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--muted);display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <span style="display:flex;align-items:center;gap:4px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <strong>Prazo:</strong> {{ $prazo->data_prazo->format('d/m/Y') }}
                        </span>
                        @if($prazo->processo)
                            <span style="display:flex;align-items:center;gap:4px;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                {{ $prazo->processo->numero }}
                                @if($prazo->processo->cliente) — {{ $prazo->processo->cliente->nome }} @endif
                            </span>
                        @endif
                        @if($prazo->responsavel)
                            <span style="display:flex;align-items:center;gap:4px;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $prazo->responsavel->nome }}
                            </span>
                        @endif
                        @if($prazo->dias)
                            <span>{{ $prazo->dias }} dias {{ $prazo->tipo_contagem }}</span>
                        @endif
                        @if($prazo->status === 'cumprido' && $prazo->data_cumprimento)
                            <span style="display:flex;align-items:center;gap:4px;color:#16a34a;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Cumprido em {{ $prazo->data_cumprimento->format('d/m/Y') }}
                            </span>
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
                                wire:confirm="Marcar este prazo como cumprido?" title="Marcar cumprido"
                                style="display:inline-flex;align-items:center;gap:5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Cumprido
                        </button>
                        <button class="btn btn-sm" style="background:#fce7f3;color:#9d174d;display:inline-flex;align-items:center;gap:5px;"
                                wire:click="marcarPerdido({{ $prazo->id }})"
                                wire:confirm="Marcar como prazo perdido?" title="Marcar perdido">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Perdido
                        </button>
                    @else
                        <button class="btn btn-sm btn-secondary" wire:click="reabrir({{ $prazo->id }})" title="Reabrir"
                                style="display:inline-flex;align-items:center;gap:5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg>
                            Reabrir
                        </button>
                    @endif
                    <button title="Editar" wire:click="abrirModal({{ $prazo->id }})"
                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button title="Excluir" wire:click="confirmarExcluirPrazo({{ $prazo->id }})"
                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    </button>
                </div>

            </div>
        </div>
        @endforeach

        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
            <span style="font-size:13px;color:var(--muted);">
                @if($prazos->total() > 0)
                    Mostrando {{ $prazos->firstItem() }}–{{ $prazos->lastItem() }} de {{ $prazos->total() }}
                @else
                    Nenhum resultado
                @endif
            </span>
            <div style="display:flex;align-items:center;gap:6px;">
                <button wire:click="previousPage" @disabled($prazos->onFirstPage())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $prazos->onFirstPage() ? '.4' : '1' }};">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Anterior
                </button>
                <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                    {{ $prazos->currentPage() }} / {{ $prazos->lastPage() }}
                </span>
                <button wire:click="nextPage" @disabled(!$prazos->hasMorePages())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $prazos->hasMorePages() ? '1' : '.4' }};">
                    Próxima
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
    @endif
</div>

{{-- ══ Confirmação de exclusão ══ --}}
@if($confirmarExcluir)
<div class="modal-backdrop">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </div>
                <span class="modal-title">Confirmar Exclusão</span>
            </div>
            <button class="modal-close" wire:click="fecharModal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <p style="font-size:14px;color:var(--muted);margin-bottom:20px;line-height:1.6;">Deseja realmente excluir este prazo? Esta ação não pode ser desfeita.</p>
        <div class="modal-footer">
            <button class="btn btn-outline" wire:click="fecharModal">Cancelar</button>
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
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="modal-title">{{ $prazoid ? 'Editar Prazo' : 'Novo Prazo' }}</span>
            </div>
            <button class="modal-close" wire:click="fecharModal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @php
        $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
        $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
        @endphp

        {{-- Seção: Identificação --}}
        <div style="{{ $sec }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Identificação
        </div>

        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr;">
            {{-- Título --}}
            <div class="form-field" style="grid-column:1/-1;">
                <label class="lbl">Título *</label>
                <input type="text" wire:model="titulo" placeholder="Ex: Prazo para contestação" style="{{ $inp }}">
                @error('titulo') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Tipo --}}
            <div class="form-field">
                <label class="lbl">Tipo *</label>
                <select wire:model="tipo" style="{{ $inp }}">
                    @foreach(['Prazo','Prazo Fatal','Audiência','Diligência','Recurso'] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Processo --}}
            <div class="form-field" style="grid-column:span 2;">
                <label class="lbl">Processo</label>
                <select wire:model="processo_id" style="{{ $inp }}">
                    <option value="">— Nenhum —</option>
                    @foreach($processos as $p)
                        <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome ?? '—' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Responsável --}}
            <div class="form-field">
                <label class="lbl">Responsável</label>
                <select wire:model="responsavel_id" style="{{ $inp }}">
                    <option value="">— Nenhum —</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Seção: Datas --}}
        <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Datas e Contagem
        </div>

        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr;">
            {{-- Data início --}}
            <div class="form-field">
                <label class="lbl">Data de Início *</label>
                <input type="date" wire:model.live="data_inicio" style="{{ $inp }}">
                @error('data_inicio') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Contagem --}}
            <div class="form-field">
                <label class="lbl">Tipo de contagem</label>
                <select wire:model.live="tipo_contagem" style="{{ $inp }}">
                    <option value="corridos">Dias corridos</option>
                    <option value="uteis">Dias úteis</option>
                </select>
            </div>

            {{-- Dias --}}
            <div class="form-field">
                <label class="lbl">Quantidade de dias</label>
                <input type="number" wire:model.live="dias" min="0" placeholder="Ex: 15"
                       style="{{ $inp }}font-size:15px;font-weight:600;">
                <span style="font-size:10px;color:var(--muted);">Preencha para calcular automaticamente</span>
            </div>

            {{-- Data prazo --}}
            <div class="form-field" style="grid-column:span 2;">
                <label class="lbl">Data do Prazo * <span style="color:var(--muted);font-weight:400;">(calculada ou manual)</span></label>
                <input type="date" wire:model="data_prazo"
                       style="{{ $inp }}border-color:{{ $prazo_fatal ? '#9d174d' : 'var(--border)' }};
                              font-size:15px;font-weight:700;color:{{ $prazo_fatal ? '#9d174d' : 'inherit' }};">
                @error('data_prazo') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Prazo Fatal --}}
            <div class="form-field" style="justify-content:flex-end;padding-bottom:6px;">
                <label class="lbl">Prazo Fatal</label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:8px;">
                    <input type="checkbox" wire:model="prazo_fatal" style="width:auto;accent-color:#9d174d;">
                    <span style="display:flex;align-items:center;gap:5px;color:#9d174d;font-weight:600;font-size:13px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        É prazo fatal
                    </span>
                </label>
            </div>
        </div>

        {{-- Seção: Detalhes --}}
        <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Detalhes
        </div>

        <div class="form-grid" style="grid-template-columns:1fr;">
            {{-- Descrição --}}
            <div class="form-field">
                <label class="lbl">Descrição</label>
                <textarea wire:model="descricao" rows="2" placeholder="Detalhes sobre o prazo..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
            </div>

            {{-- Observações --}}
            <div class="form-field">
                <label class="lbl">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Observações internas..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-outline" wire:click="fecharModal">Cancelar</button>
            <button class="btn btn-primary" wire:click="salvar" wire:loading.attr="disabled" style="display:flex;align-items:center;gap:6px;">
                <span wire:loading.remove wire:target="salvar" style="display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar
                </span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
