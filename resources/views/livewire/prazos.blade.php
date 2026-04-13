<div class="prazos-page">
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

<style>
    .prazos-page svg[width="9"] { width: 9px; height: 9px; }
    .prazos-page svg[width="11"] { width: 11px; height: 11px; }
    .prazos-page svg[width="12"] { width: 12px; height: 12px; }
    .prazos-page svg[width="13"] { width: 13px; height: 13px; }
    .prazos-page svg[width="14"] { width: 14px; height: 14px; }
    .prazos-page svg[width="16"] { width: 16px; height: 16px; }
    .prazos-page svg[width="18"] { width: 18px; height: 18px; }
    .prazos-page svg[width="20"] { width: 20px; height: 20px; }
    .prazos-page svg[width="36"] { width: 36px; height: 36px; }

    .prazos-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .prazos-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
    }
    .prazos-subtitle {
        font-size: 13px;
        color: var(--muted);
        margin-top: 3px;
        line-height: 1.5;
    }
    .prazos-header-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .prazos-guide {
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 14px;
        display: grid;
        grid-template-columns: minmax(220px, .9fr) repeat(3, minmax(160px, 1fr));
        gap: 12px;
        align-items: center;
    }
    .prazos-guide-title {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }
    .prazos-guide-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #f1f5f9;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .prazos-guide h2 {
        font-size: 15px;
        margin: 0 0 3px;
        color: var(--text);
    }
    .prazos-guide p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.45;
    }
    .prazos-guide-step {
        border-left: 3px solid var(--border);
        padding-left: 10px;
    }
    .prazos-guide-step strong {
        display: block;
        font-size: 12px;
        color: var(--text);
        margin-bottom: 3px;
    }
    .prazos-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        align-items: start;
    }
    .prazos-metricas {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .prazos-metric-card {
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
    }
    .prazos-metric-card:hover {
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(37,99,200,.08);
    }
    .prazos-metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .prazos-metric-num {
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 2px;
    }
    .prazos-metric-lbl {
        font-size: 11px;
        color: var(--muted);
        font-weight: 500;
    }
    .prazos-filter-bar {
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .prazos-filter-bar input,
    .prazos-filter-bar select {
        padding: 7px 9px;
        border: 1.5px solid var(--border);
        border-radius: 7px;
        font-size: 12px;
        background: var(--white);
        color: var(--text);
        outline: none;
        transition: border-color .15s;
    }
    .prazos-filter-bar input:focus,
    .prazos-filter-bar select:focus { border-color: var(--primary-light); }
    .prazos-filter-busca {
        flex: 1;
        min-width: 220px;
        padding-left: 30px !important;
    }
    .prazos-filter-busca-wrap {
        position: relative;
        flex: 0 1 320px;
        min-width: 220px;
    }
    .prazos-filter-busca-wrap svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .prazos-filter-actions { margin-left: 0; display: flex; gap: 6px; align-items: center; }
    .prazos-filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        margin-right: 4px;
        white-space: nowrap;
    }
    .ia-bar {
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ia-bar-label {
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ia-bar-input {
        flex: 1;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 13px;
        outline: none;
    }
    .ia-bar-input::placeholder { color: var(--muted); }
    .ia-bar-input:focus { border-color: var(--primary); }
    .ia-bar-btn {
        padding: 8px 14px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--white);
        color: var(--primary);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s;
    }
    .ia-bar-btn:hover { background: #eff6ff; }
    .ia-resposta {
        background: #f0f9ff;
        border: 1.5px solid #bae6fd;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        color: #0c4a6e;
        line-height: 1.6;
        margin-bottom: 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .prazos-list-card {
        padding: 0;
        overflow: hidden;
        border-radius: 8px;
    }
    .prazos-list-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        flex-wrap: wrap;
    }
    .prazos-list-head strong {
        font-size: 14px;
        color: var(--text);
    }
    .prazos-list-head span {
        font-size: 12px;
        color: var(--muted);
    }
    .prazo-row {
        border-left: 4px solid transparent;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        background: var(--white);
        transition: background .15s;
    }
    .prazo-row:hover { background: #f8fafc; }
</style>
@verbatim
<style>
    @media (max-width: 1180px) {
        .prazos-guide { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 900px) {
        .prazos-filter-bar { gap: 6px; }
        .prazos-filter-bar select,
        .prazos-filter-bar input[type=date] { min-width: unset; width: 100%; }
        .prazos-filter-busca-wrap { min-width: 100%; }
        .prazos-filter-actions { width: 100%; }
    }
    @media (max-width: 768px) {
        .prazos-metricas { grid-template-columns: repeat(2, 1fr); }
        .prazos-guide { grid-template-columns: 1fr; }
        .prazos-header-actions { justify-content: flex-start; }
    }
</style>
@endverbatim







@if(!$embed)
{{-- ══ Cabeçalho ══ --}}
<div class="prazos-header">
    <div>
        <h1 class="prazos-title">Prazos</h1>
        <div class="prazos-subtitle">
            Gestão e controle de prazos processuais
            <span style="color:#cbd5e1;margin:0 6px;">|</span>
            <a href="{{ route('processos.hub') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Voltar para central</a>
        </div>
    </div>
    <div class="prazos-header-actions">
        <button class="btn btn-sm btn-secondary-outline"
                wire:click="exportarPdf" wire:loading.attr="disabled" title="Exportar PDF">
            <span wire:loading.remove wire:target="exportarPdf" style="display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                PDF
            </span>
            <span wire:loading wire:target="exportarPdf">Gerando…</span>
        </button>
        <button class="btn btn-sm btn-secondary-outline"
                wire:click="exportarCsv" wire:loading.attr="disabled" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando…</span>
        </button>
        <button class="btn btn-primary btn-sm" wire:click="abrirModal()" style="display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Prazo
        </button>
    </div>
</div>
<div class="prazos-guide">
    <div class="prazos-guide-title">
        <div class="prazos-guide-icon">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <h2>O que olhar primeiro?</h2>
            <p>Comece pelos vencidos, depois pelos que vencem hoje e finalize pelos fatais dos próximos dias.</p>
        </div>
    </div>
    <div class="prazos-guide-step" style="border-left-color:#dc2626;">
        <strong>1. Prazos vencidos</strong>
        <p>Regularize ou marque como perdido quando não houver providência possível.</p>
    </div>
    <div class="prazos-guide-step" style="border-left-color:#ca8a04;">
        <strong>2. Vencem hoje</strong>
        <p>Use este grupo como pauta de trabalho do dia.</p>
    </div>
    <div class="prazos-guide-step" style="border-left-color:#9d174d;">
        <strong>3. Prazos fatais</strong>
        <p>Confirme responsável, processo vinculado e data limite.</p>
    </div>
</div>
@endif {{-- /!embed header --}}

{{-- ── Visão rápida ── --}}
@if(!$embed)
<div class="prazos-metricas">

    {{-- Prazos em Aberto --}}
    <div class="prazos-metric-card" wire:click="$set('filtroStatus','aberto')" title="Filtrar: Em aberto">
        <div class="prazos-metric-icon" style="background:#eff6ff;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <div class="prazos-metric-num" style="color:#2563eb;">{{ $totalAbertos }}</div>
            <div class="prazos-metric-lbl">Prazos em aberto</div>
        </div>
    </div>

    {{-- Vencem Hoje --}}
    <div class="prazos-metric-card" title="Vencem hoje">
        <div class="prazos-metric-icon" style="background:#fefce8;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="prazos-metric-num" style="color:#ca8a04;">{{ $vencendoHoje }}</div>
            <div class="prazos-metric-lbl">Vencem hoje</div>
        </div>
    </div>

    {{-- Vencidos --}}
    <div class="prazos-metric-card" title="Vencidos não cumpridos">
        <div class="prazos-metric-icon" style="background:#fef2f2;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <div class="prazos-metric-num" style="color:#dc2626;">{{ $vencidos }}</div>
            <div class="prazos-metric-lbl">Vencidos (não cumpridos)</div>
        </div>
    </div>

    {{-- Prazos Fatais --}}
    <div class="prazos-metric-card" wire:click="$set('filtroTipo','Prazo Fatal')" title="Filtrar: Prazo Fatal">
        <div class="prazos-metric-icon" style="background:#fdf2f8;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div>
            <div class="prazos-metric-num" style="color:#9d174d;">{{ $fatais }}</div>
            <div class="prazos-metric-lbl">Prazos fatais (próx. 5 dias)</div>
        </div>
    </div>

</div>
@endif

@if($embed)
<div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
    <button class="btn btn-primary btn-sm" wire:click="abrirModal()" style="display:flex;align-items:center;gap:6px;">
        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Prazo
    </button>
</div>
@else





{{-- ══ Analista IA ══  --}}
<div class="ia-bar">
    <div class="ia-bar-label">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2">
            <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
        </svg>
        Analista IA
    </div>
    <input type="text"
           class="ia-bar-input"
           wire:model="perguntaIA"
           wire:keydown.enter="perguntarIA"
           placeholder="Ex: Quais prazos fatais vencem esta semana?">
    <button class="ia-bar-btn" wire:click="perguntarIA" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="perguntarIA">Perguntar</span>
        <span wire:loading wire:target="perguntarIA">Consultando…</span>
    </button>
    @if($respostaIA)
    <button class="ia-bar-btn" wire:click="limparIA" title="Limpar resposta"
            style="padding:8px 10px;border-color:rgba(239,68,68,.3);color:#fca5a5;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>
    @endif
</div>

@if($respostaIA)
<div class="ia-resposta">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
    </svg>
    <span>{{ $respostaIA }}</span>
</div>
@endif
@endif  {{-- ══ Analista IA ══  --}}








{{-- ══ Filtros horizontais ══ --}}
@if(!$embed)
<div class="prazos-filter-bar">
    <span class="prazos-filter-label">Filtrar prazos</span>

    {{-- Busca --}}
    <div class="prazos-filter-busca-wrap">
        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" wire:model.live.debounce.300ms="filtroBusca"
               placeholder="Buscar por título, processo…"
               class="prazos-filter-busca" style="width:100%;">
    </div>

    {{-- Status --}}
    <select wire:model.live="filtroStatus" style="width:112px;">
        <option value="aberto">Em aberto</option>
        <option value="cumprido">Cumpridos</option>
        <option value="perdido">Perdidos</option>
        <option value="todos">Todos</option>
    </select>

    {{-- Tipo --}}
    <select wire:model.live="filtroTipo" style="width:132px;">
        <option value="">Todos os tipos</option>
        <option value="Prazo">Prazo</option>
        <option value="Prazo Fatal">Prazo Fatal</option>
        <option value="Audiência">Audiência</option>
        <option value="Diligência">Diligência</option>
        <option value="Recurso">Recurso</option>
    </select>

    {{-- Responsável --}}
    <select wire:model.live="filtroResponsavel" style="width:130px;">
        <option value="">Responsável</option>
        @foreach($usuarios as $u)
            <option value="{{ $u->id }}">{{ $u->nome }}</option>
        @endforeach
    </select>

    {{-- Processo --}}
    <select wire:model.live="filtroProcesso" style="width:150px;">
        <option value="">Todos os processos</option>
        @foreach($processos as $p)
            <option value="{{ $p->id }}">{{ $p->numero }}</option>
        @endforeach
    </select>

    {{-- Período --}}
    <input type="date" wire:model.live="filtroDataIni" title="De" style="width:118px;">
    <input type="date" wire:model.live="filtroDataFim" title="Até" style="width:118px;">

    {{-- Limpar --}}
    <div class="prazos-filter-actions">
        @if($filtroBusca || $filtroTipo || $filtroResponsavel || $filtroProcesso || $filtroDataIni || $filtroDataFim || $filtroStatus !== 'aberto')
        <button wire:click="$set('filtroBusca',''); $set('filtroTipo',''); $set('filtroResponsavel',''); $set('filtroProcesso',''); $set('filtroDataIni',''); $set('filtroDataFim',''); $set('filtroStatus','aberto')"
            style="padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:none;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap;"
            onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
            <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar filtros
        </button>
        @endif
    </div>
</div>
@endif {{-- /!embed filters --}}

{{-- ══ Grid principal ══ --}}
<div class="prazos-grid">

    {{-- ── Coluna única ── --}}
    <div>

{{-- ══ Lista ══ --}}
<div class="card prazos-list-card">
    @if($prazos->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div class="empty-state-title">Nenhum prazo encontrado</div>
            <div class="empty-state-sub">Ajuste os filtros ou clique em <strong>+ Novo Prazo</strong> para cadastrar.</div>
        </div>
    @else
        <div class="prazos-list-head">
            <div>
                <strong>Prazos encontrados</strong>
                <span>Use a lista como fila de acompanhamento.</span>
            </div>
            <span>{{ $prazos->total() }} registro(s)</span>
        </div>
        @foreach($prazos as $prazo)
        @php
            $urg = $prazo->urgencia();
            $dias = $prazo->diasRestantes();
        @endphp
        <div class="prazo-row urgencia-{{ $urg }}">
            <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">

                {{-- Indicador de dias --}}
                <div style="min-width:90px;text-align:center;padding-top:2px;">
                    @if($urg === 'cumprido')
                        <span class="dias-badge dias-cumprido" style="display:inline-flex;align-items:center;gap:4px;">
                            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Cumprido
                        </span>
                    @elseif($urg === 'perdido')
                        <span class="dias-badge dias-perdido" style="display:inline-flex;align-items:center;gap:4px;">
                            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Perdido
                        </span>
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
                                <svg aria-hidden="true" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:middle;margin-right:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                Fatal
                            </span>
                        @endif
                        <span class="badge" style="background:#e2e8f0;color:#475569;font-size:11px;">{{ $prazo->tipo }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--muted);display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <span style="display:flex;align-items:center;gap:4px;">
                            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <strong>Prazo:</strong> {{ $prazo->data_prazo->format('d/m/Y') }}
                        </span>
                        @if($prazo->processo)
                            <span style="display:flex;align-items:center;gap:4px;">
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                {{ $prazo->processo->numero }}
                                @if($prazo->processo->cliente) — {{ $prazo->processo->cliente->nome }} @endif
                            </span>
                        @endif
                        @if($prazo->responsavel)
                            <span style="display:flex;align-items:center;gap:4px;">
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $prazo->responsavel->nome }}
                            </span>
                        @endif
                        @if($prazo->dias)
                            <span>{{ $prazo->dias }} dias {{ $prazo->tipo_contagem }}</span>
                        @endif
                        @if($prazo->status === 'cumprido' && $prazo->data_cumprimento)
                            <span style="display:flex;align-items:center;gap:4px;color:#16a34a;">
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
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
                            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Cumprido
                        </button>
                        <button class="btn btn-sm" style="background:#fce7f3;color:#9d174d;display:inline-flex;align-items:center;gap:5px;"
                                wire:click="marcarPerdido({{ $prazo->id }})"
                                wire:confirm="Marcar como prazo perdido?" title="Marcar perdido">
                            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Perdido
                        </button>
                    @else
                        <button class="btn btn-sm btn-secondary" wire:click="reabrir({{ $prazo->id }})" title="Reabrir"
                                style="display:inline-flex;align-items:center;gap:5px;">
                            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg>
                            Reabrir
                        </button>
                    @endif
                    <button title="Editar" wire:click="abrirModal({{ $prazo->id }})"
                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button title="Excluir" wire:click="confirmarExcluirPrazo({{ $prazo->id }})"
                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
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
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Anterior
                </button>
                <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                    {{ $prazos->currentPage() }} / {{ $prazos->lastPage() }}
                </span>
                <button wire:click="nextPage" @disabled(!$prazos->hasMorePages())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $prazos->hasMorePages() ? '1' : '.4' }};">
                    Próxima
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
    @endif

</div>{{-- /card lista --}}

    </div>
    {{-- /coluna --}}

</div>
{{-- /grid --}}

{{-- ══ Confirmação de exclusão ══ --}}
@if($confirmarExcluir)
<div class="modal-backdrop">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </div>
                <span class="modal-title">Confirmar Exclusão</span>
            </div>
            <button class="modal-close" wire:click="fecharModal" aria-label="Fechar">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="modal-title">{{ $prazoid ? 'Editar Prazo' : 'Novo Prazo' }}</span>
            </div>
            <button class="modal-close" wire:click="fecharModal" aria-label="Fechar">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @php
        $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
        $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
        @endphp

        {{-- Seção: Identificação --}}
        <div style="{{ $sec }}">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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
                @if($embed)
                    <input type="hidden" wire:model="processo_id" value="{{ $processoId }}">
                    <div style="font-size:13px;color:var(--muted);padding:8px 0;">Processo vinculado automaticamente ao processo atual.</div>
                @else
                    <select wire:model="processo_id" style="{{ $inp }}">
                        <option value="">— Nenhum —</option>
                        @foreach($processos as $p)
                            <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome ?? '—' }}</option>
                        @endforeach
                    </select>
                @endif
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
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
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
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        É prazo fatal
                    </span>
                </label>
            </div>
        </div>

        {{-- Seção: Detalhes --}}
        <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
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
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar
                </span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
