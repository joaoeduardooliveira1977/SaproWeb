<div>

{{-- KPIs --}}
<div class="stat-grid">
    @php
    $kpiDefs = [
        ['svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>', 'val' => $kpis['agendadas'],  'label' => 'Agendadas (futuras)',   'cor' => '#2563a8'],
        ['svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',                                                                                         'val' => $kpis['hoje'],       'label' => 'Hoje',                  'cor' => '#dc2626'],
        ['svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',                                                        'val' => $kpis['realizadas'], 'label' => 'Realizadas (este mês)', 'cor' => '#16a34a'],
        ['svg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',                                    'val' => $kpis['canceladas'], 'label' => 'Canceladas (este mês)', 'cor' => '#d97706'],
    ];
    @endphp
    @foreach($kpiDefs as $kpi)
    <div class="card" style="border-left:4px solid {{ $kpi['cor'] }};text-align:center;">
        <div style="display:flex;justify-content:center;margin-bottom:6px;">{!! $kpi['svg'] !!}</div>
        <div style="font-size:26px;font-weight:700;color:{{ $kpi['cor'] }};">{{ $kpi['val'] }}</div>
        <div style="font-size:12px;color:var(--muted);">{{ $kpi['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

        {{-- Busca com ícone --}}
        <div style="flex:1;min-width:200px;position:relative;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input wire:model.live.debounce.300ms="filtroBusca" type="text" placeholder="Processo, cliente..."
                style="width:100%;padding:8px 10px 8px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        </div>

        <select wire:model.live="filtroStatus"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
            <option value="">Todos os status</option>
            <option value="agendada">Agendada</option>
            <option value="realizada">Realizada</option>
            <option value="cancelada">Cancelada</option>
            <option value="redesignada">Redesignada</option>
        </select>

        <select wire:model.live="filtroTipo"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:150px;">
            <option value="">Todos os tipos</option>
            <option value="conciliacao">Conciliação</option>
            <option value="instrucao">Instrução</option>
            <option value="instrucao_julgamento">Instrução e Julgamento</option>
            <option value="julgamento">Julgamento</option>
            <option value="una">Una</option>
            <option value="outra">Outra</option>
        </select>

        <input wire:model.live="filtroDataIni" type="date" title="De"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        <input wire:model.live="filtroDataFim" type="date" title="Até"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">

        @if($filtroBusca || $filtroStatus || $filtroTipo || $filtroDataIni || $filtroDataFim)
        <button wire:click="$set('filtroBusca',''); $set('filtroStatus',''); $set('filtroTipo',''); $set('filtroDataIni',''); $set('filtroDataFim','')"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:none;color:var(--muted);cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:5px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar
        </button>
        @endif

        <div style="display:flex;gap:6px;margin-left:auto;">
            <button wire:click="exportarCsv" wire:loading.attr="disabled"
                class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
                <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </span>
                <span wire:loading wire:target="exportarCsv">Gerando…</span>
            </button>
            <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Audiência
            </button>
        </div>
    </div>
</div>

{{-- Tabela --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table style="border-collapse:collapse;width:100%;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Data/Hora</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Processo</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Juiz</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Sala/Local</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;">Status</th>
                    <th class="hide-xs" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Resultado</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audiencias as $aud)
                @php
                    $tipos = \App\Models\Audiencia::tiposLabel();
                    $resultados = \App\Models\Audiencia::resultadosLabel();
                    $ehHoje = $aud->data_hora->isToday();
                    $ehPassado = $aud->data_hora->isPast() && $aud->status === 'agendada';
                @endphp
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;{{ $ehHoje ? 'background:#fffbeb;' : '' }}{{ $ehPassado ? 'background:#fff5f5;' : '' }}"
                    onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='{{ $ehHoje ? '#fffbeb' : ($ehPassado ? '#fff5f5' : '') }}'">
                    <td style="padding:14px 16px;">
                        <div style="font-weight:600;color:var(--primary);">
                            {{ $aud->data_hora->format('d/m/Y') }}
                            @if($ehHoje)<span class="badge" style="background:#dc2626;color:#fff;font-size:10px;margin-left:4px;">HOJE</span>@endif
                        </div>
                        <div style="font-size:12px;color:var(--muted);">{{ $aud->data_hora->format('H:i') }}</div>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="font-weight:600;font-size:12px;">{{ $aud->processo?->numero }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $aud->processo?->cliente?->nome }}</div>
                    </td>
                    <td class="hide-sm" style="padding:14px 16px;font-size:13px;color:var(--muted);">{{ $tipos[$aud->tipo] ?? $aud->tipo }}</td>
                    <td class="hide-sm" style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $aud->juiz?->nome ?? '—' }}</td>
                    <td class="hide-sm" style="padding:14px 16px;font-size:12px;color:var(--muted);">
                        @if($aud->sala)Sala {{ $aud->sala }}@endif
                        @if($aud->local)<div>{{ $aud->local }}</div>@endif
                        @if(!$aud->sala && !$aud->local)—@endif
                    </td>
                    <td style="padding:14px 16px;text-align:center;">
                        <span class="badge" style="background:{{ $aud->statusBg() }};color:{{ $aud->statusCor() }};">{{ ucfirst($aud->status) }}</span>
                    </td>
                    <td class="hide-xs" style="padding:14px 16px;font-size:12px;color:var(--muted);">
                        {{ $aud->resultado ? ($resultados[$aud->resultado] ?? $aud->resultado) : '—' }}
                    </td>
                    <td style="padding:14px 16px;text-align:center;">
                        <div style="display:flex;justify-content:center;gap:4px;">
                            @if($aud->status === 'agendada')
                            <button wire:click="abrirResultado({{ $aud->id }})" title="Registrar resultado"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            @endif
                            <button wire:click="abrirModal({{ $aud->id }})" title="Editar"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="excluir({{ $aud->id }})" wire:confirm="Excluir esta audiência?" title="Excluir"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:48px;text-align:center;color:var(--muted);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <div style="font-size:14px;font-weight:500;">Nenhuma audiência encontrada</div>
                        <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou cadastre uma nova audiência.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
        <span style="font-size:13px;color:var(--muted);">
            @if($audiencias->total() > 0)
                Mostrando {{ $audiencias->firstItem() }}–{{ $audiencias->lastItem() }} de {{ $audiencias->total() }}
            @else
                Nenhum resultado
            @endif
        </span>
        <div style="display:flex;align-items:center;gap:6px;">
            <button wire:click="previousPage" @disabled($audiencias->onFirstPage())
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $audiencias->onFirstPage() ? '.4' : '1' }};">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Anterior
            </button>
            <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                {{ $audiencias->currentPage() }} / {{ $audiencias->lastPage() }}
            </span>
            <button wire:click="nextPage" @disabled(!$audiencias->hasMorePages())
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $audiencias->hasMorePages() ? '1' : '.4' }};">
                Próxima
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- ── Modal Principal ── --}}
@if($modalAberto)
<div class="modal-backdrop">
<div class="modal" style="max-width:700px;">
    <div class="modal-header">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="modal-title">{{ $audienciaId ? 'Editar' : 'Nova' }} Audiência</span>
        </div>
        <button wire:click="fecharModal" class="modal-close">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    @php
    $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
    $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
    @endphp

    {{-- Seção: Dados principais --}}
    <div style="{{ $sec }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Dados da Audiência
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label class="lbl">Processo *</label>
            <select wire:model="processo_id" style="{{ $inp }}">
                <option value="">Selecione...</option>
                @foreach($processos as $p)
                <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome }}</option>
                @endforeach
            </select>
            @error('processo_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-field">
            <label class="lbl">Data e Hora *</label>
            <input wire:model="data_hora" type="datetime-local" style="{{ $inp }}">
            @error('data_hora')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-field">
            <label class="lbl">Tipo *</label>
            <select wire:model="tipo" style="{{ $inp }}">
                <option value="conciliacao">Conciliação</option>
                <option value="instrucao">Instrução</option>
                <option value="instrucao_julgamento">Instrução e Julgamento</option>
                <option value="julgamento">Julgamento</option>
                <option value="una">Una</option>
                <option value="outra">Outra</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Status</label>
            <select wire:model="status" style="{{ $inp }}">
                <option value="agendada">Agendada</option>
                <option value="realizada">Realizada</option>
                <option value="cancelada">Cancelada</option>
                <option value="redesignada">Redesignada</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Sala</label>
            <input wire:model="sala" type="text" placeholder="Ex: Sala 3" style="{{ $inp }}">
        </div>
        <div class="form-field">
            <label class="lbl">Local / Fórum</label>
            <input wire:model="local" type="text" placeholder="Ex: Fórum Central de São Paulo" style="{{ $inp }}">
        </div>
        <div class="form-field">
            <label class="lbl">Juiz</label>
            <select wire:model="juiz_id" style="{{ $inp }}">
                <option value="">Selecione...</option>
                @foreach($juizes as $j)
                <option value="{{ $j->id }}">{{ $j->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Advogado Responsável</label>
            <select wire:model="advogado_id" style="{{ $inp }}">
                <option value="">Selecione...</option>
                @foreach($advogados as $a)
                <option value="{{ $a->id }}">{{ $a->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Preposto / Representante</label>
            <input wire:model="preposto" type="text" placeholder="Nome do preposto (se houver)" style="{{ $inp }}">
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Pauta / Observações</label>
            <textarea wire:model="pauta" rows="2" placeholder="Assuntos a tratar, testemunhas, documentos necessários..."
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>
    </div>

    @if($status === 'realizada')
    {{-- Seção: Resultado --}}
    <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span style="color:#16a34a;">Resultado da Audiência</span>
    </div>
    <div class="form-grid">
        <div class="form-field">
            <label class="lbl">Resultado</label>
            <select wire:model="resultado" style="{{ $inp }}">
                <option value="">Selecione...</option>
                <option value="acordo">Acordo</option>
                <option value="condenacao">Condenação</option>
                <option value="improcedente">Improcedente</option>
                <option value="extincao">Extinção</option>
                <option value="nao_realizada">Não Realizada</option>
                <option value="outra">Outra</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Data do Próximo Passo</label>
            <input wire:model="data_proximo" type="date" style="{{ $inp }}">
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Descrição do Resultado</label>
            <textarea wire:model="resultado_descricao" rows="2"
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Próximo Passo</label>
            <textarea wire:model="proximo_passo" rows="2" placeholder="Ex: Aguardar sentença, interpor recurso..."
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>
    </div>
    @endif

    <div class="modal-footer">
        <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
        <button wire:click="salvar" class="btn btn-primary" wire:loading.attr="disabled" style="display:flex;align-items:center;gap:6px;">
            <span wire:loading.remove wire:target="salvar" style="display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Salvar
            </span>
            <span wire:loading wire:target="salvar">Salvando...</span>
        </button>
    </div>
</div>
</div>
@endif

{{-- ── Modal Resultado Rápido ── --}}
@if($modalResultado)
<div class="modal-backdrop">
<div class="modal" style="max-width:520px;">
    <div class="modal-header" style="background:#f0fdf4;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span class="modal-title" style="color:#15803d;">Registrar Resultado</span>
        </div>
        <button wire:click="$set('modalResultado',false)" class="modal-close">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    @php
    $inp2 = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
    @endphp

    <div class="form-grid" style="margin-top:4px;">
        <div class="form-field">
            <label class="lbl">Resultado *</label>
            <select wire:model="resultado" style="{{ $inp2 }}">
                <option value="">Selecione...</option>
                <option value="acordo">Acordo</option>
                <option value="condenacao">Condenação</option>
                <option value="improcedente">Improcedente</option>
                <option value="extincao">Extinção</option>
                <option value="nao_realizada">Não Realizada</option>
                <option value="outra">Outra</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Data do Próximo Passo</label>
            <input wire:model="data_proximo" type="date" style="{{ $inp2 }}">
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Descrição do Resultado</label>
            <textarea wire:model="resultado_descricao" rows="3" placeholder="Descreva o que foi decidido/acordado..."
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Próximo Passo</label>
            <textarea wire:model="proximo_passo" rows="2" placeholder="Ex: Aguardar sentença, interpor recurso..."
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button wire:click="$set('modalResultado',false)" class="btn btn-outline">Cancelar</button>
        <button wire:click="salvarResultado" class="btn btn-success" style="display:flex;align-items:center;gap:6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Confirmar Realização
        </button>
    </div>
</div>
</div>
@endif

</div>
