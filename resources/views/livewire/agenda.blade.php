<div class="agenda-page">
@verbatim
<style>
.agenda-page svg[width="10"] { width: 10px; height: 10px; }
.agenda-page svg[width="11"] { width: 11px; height: 11px; }
.agenda-page svg[width="12"] { width: 12px; height: 12px; }
.agenda-page svg[width="13"] { width: 13px; height: 13px; }
.agenda-page svg[width="14"] { width: 14px; height: 14px; }
.agenda-page svg[width="15"] { width: 15px; height: 15px; }
.agenda-page svg[width="16"] { width: 16px; height: 16px; }
.agenda-page svg[width="18"] { width: 18px; height: 18px; }
.agenda-page svg[width="20"] { width: 20px; height: 20px; }
.agenda-page svg[width="40"] { width: 40px; height: 40px; }

.agenda-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 12px;
}
.agenda-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary);
    margin: 0;
}
.agenda-subtitle {
    font-size: 13px;
    color: var(--muted);
    margin: 2px 0 0;
    line-height: 1.5;
}
.agenda-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.agenda-guide {
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
.agenda-guide-title {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.agenda-guide-icon {
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
.agenda-guide h2 {
    font-size: 15px;
    margin: 0 0 3px;
    color: var(--text);
}
.agenda-guide p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.45;
}
.agenda-guide-step {
    border-left: 3px solid var(--border);
    padding-left: 10px;
}
.agenda-guide-step strong {
    display: block;
    font-size: 12px;
    color: var(--text);
    margin-bottom: 3px;
}
.agenda-filter-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
    margin-right: 4px;
    white-space: nowrap;
}
.agenda-ia-bar {
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.agenda-list-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
    flex-wrap: wrap;
}
.agenda-list-head strong {
    font-size: 14px;
    color: var(--text);
}
.agenda-list-head span {
    font-size: 12px;
    color: var(--muted);
}
@media (max-width: 1180px) {
    .agenda-guide { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 900px) {
    .agenda-filter-bar { flex-wrap: wrap; }
    .agenda-filter-bar select,
    .agenda-filter-bar input[type=date] { width: 100%; }
    .agenda-filter-busca-wrap { min-width: 100%; }
    .agenda-filter-label { width: 100%; }
}
@media (max-width: 768px) {
    .metricas-ag { grid-template-columns: 1fr 1fr !important; }
    .agenda-guide { grid-template-columns: 1fr; }
    .agenda-actions { justify-content: flex-start; }
}
@media (max-width: 480px) {
    .metricas-ag { grid-template-columns: 1fr !important; }
}
</style>
@endverbatim

{{-- Cabecalho --}}
@if(!$embed)
<div class="agenda-header">
    <div>
        <h1 class="agenda-title">Agenda</h1>
        <p class="agenda-subtitle">
            {{ $eventos->total() }} evento{{ $eventos->total() !== 1 ? 's' : '' }} encontrado{{ $eventos->total() !== 1 ? 's' : '' }}
            <span style="color:#cbd5e1;margin:0 6px;">|</span>
            <a href="{{ route('processos.hub') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Voltar para central</a>
        </p>
    </div>


    <div class="agenda-actions">
        {{-- Toggle Vista --}}
        <div style="display:flex;border:1.5px solid var(--border);border-radius:8px;overflow:hidden;">
            <button wire:click="{{ $vistaCalendario ? 'toggleVista' : '' }}"
                style="padding:5px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;gap:5px;background:{{ !$vistaCalendario ? 'var(--primary)' : 'transparent' }};color:{{ !$vistaCalendario ? '#fff' : 'var(--muted)' }};">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Lista
            </button>
            <button wire:click="{{ !$vistaCalendario ? 'toggleVista' : '' }}"
                style="padding:5px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;gap:5px;background:{{ $vistaCalendario ? 'var(--primary)' : 'transparent' }};color:{{ $vistaCalendario ? '#fff' : 'var(--muted)' }};">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Cal.
            </button>
        </div>
        <button wire:click="exportarCsv" wire:loading.attr="disabled" class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando...</span>
        </button>
        <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo evento
        </button>
    </div>
</div>
<div class="agenda-guide">
    <div class="agenda-guide-title">
        <div class="agenda-guide-icon">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <h2>Como usar esta agenda?</h2>
            <p>Comece pelo que está atrasado, confirme os compromissos de hoje e depois organize os próximos 7 dias.</p>
        </div>
    </div>
    <div class="agenda-guide-step" style="border-left-color:#ea580c;">
        <strong>1. Atrasados</strong>
        <p>Resolva ou conclua eventos que já passaram.</p>
    </div>
    <div class="agenda-guide-step" style="border-left-color:#2563eb;">
        <strong>2. Hoje</strong>
        <p>Use como pauta de atendimento e rotina diária.</p>
    </div>
    <div class="agenda-guide-step" style="border-left-color:#ca8a04;">
        <strong>3. Semana</strong>
        <p>Planeje audiências, reuniões e prazos próximos.</p>
    </div>
</div>
@endif {{-- /!embed header --}}

{{-- Visão rápida --}}
@if(!$embed)
<div class="metricas-ag" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-bottom:16px;">

    {{-- Hoje --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div style="font-size:22px;font-weight:800;color:#2563eb;line-height:1.1;">{{ $metricas['hoje'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">eventos hoje</div>
        </div>
    </div>

    {{-- Proximos 7 dias --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:9px;background:#fefce8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <div style="font-size:22px;font-weight:800;color:#ca8a04;line-height:1.1;">{{ $metricas['semana'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">proximos 7 dias</div>
        </div>
    </div>

    {{-- Urgentes --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:9px;background:#fff1f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <div style="font-size:22px;font-weight:800;color:#dc2626;line-height:1.1;">{{ $metricas['urgentes'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">urgentes</div>
        </div>
    </div>

    {{-- Atrasados --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:9px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
            </svg>
        </div>
        <div>
            <div style="font-size:22px;font-weight:800;color:#ea580c;line-height:1.1;">{{ $metricas['atrasados'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">em atraso</div>
        </div>
    </div>

</div>
@endif

@if($embed)
<div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
    <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Evento
    </button>
</div>
@endif

{{-- Analista IA --}}
@if(!$embed)
<div class="agenda-ia-bar">
    <div style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#1d4ed8;flex-shrink:0;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            <path d="M18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
        </svg>
    </div>
    <input wire:model="perguntaIA" wire:keydown.enter="perguntarIA" type="text"
        placeholder="Pergunte sobre a agenda... Ex: quantos eventos urgentes, prazos desta semana, audiencias de amanha"
        style="flex:1;background:var(--bg);border:1.5px solid var(--border);border-radius:8px;padding:9px 14px;color:var(--text);font-size:13px;outline:none;"
        onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
    <button wire:click="perguntarIA" wire:loading.attr="disabled" wire:target="perguntarIA"
        style="background:#2563a8;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;transition:background .15s;"
        onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563a8'">
        <span wire:loading.remove wire:target="perguntarIA" style="display:flex;align-items:center;gap:6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            Analisar
        </span>
        <span wire:loading wire:target="perguntarIA">Analisando...</span>
    </button>
</div>

@if($respostaIA)
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#1e40af;display:flex;gap:10px;align-items:flex-start;">
    <div style="flex-shrink:0;width:28px;height:28px;background:#dbeafe;border-radius:6px;display:flex;align-items:center;justify-content:center;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-weight:700;margin-bottom:4px;font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#1d4ed8;">Analista IA</div>
        <div style="line-height:1.6;">{{ $respostaIA }}</div>
    </div>
    <button wire:click="limparIA" style="background:none;border:none;color:#93c5fd;cursor:pointer;font-size:18px;line-height:1;padding:0 4px;flex-shrink:0;" title="Fechar">&times;</button>
</div>
@endif
@endif {{-- /!embed IA --}}

{{-- ══ Filtros horizontais ══ --}}
@if(!$embed)
<div class="agenda-filter-bar" style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:10px 12px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;margin-bottom:12px;">
    <span class="agenda-filter-label">Filtrar agenda</span>

    {{-- Tipo --}}
    <select wire:model.live="tipo"
        style="padding:7px 9px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:var(--white);color:var(--text);width:132px;outline:none;">
        <option value="">Todos os tipos</option>
        <option value="Audiência">Audiência</option>
        <option value="Prazo">Prazo</option>
        <option value="Reunião">Reunião</option>
        <option value="Consulta">Consulta</option>
        <option value="Despacho">Despacho</option>
        <option value="Outros">Outros</option>
    </select>

    {{-- Responsável --}}
    <select wire:model.live="responsavel_id"
        style="padding:7px 9px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:var(--white);color:var(--text);width:130px;outline:none;">
        <option value="">Responsável</option>
        @foreach($responsaveis as $r)
            <option value="{{ $r->id }}">{{ $r->nome }}</option>
        @endforeach
    </select>

    {{-- Período --}}
    @if(!$vistaCalendario)
    <input wire:model.live="data_ini" type="date" title="De"
        style="padding:7px 9px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:var(--white);color:var(--text);width:118px;outline:none;">
    <input wire:model.live="data_fim" type="date" title="Até"
        style="padding:7px 9px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:var(--white);color:var(--text);width:118px;outline:none;">
    @endif

    {{-- Só pendentes --}}
    <label style="display:flex;align-items:center;gap:7px;padding:7px 10px;border:1.5px solid {{ $so_pendentes ? '#2563a8' : 'var(--border)' }};border-radius:7px;cursor:pointer;font-size:12px;background:{{ $so_pendentes ? '#eff6ff' : 'transparent' }};color:{{ $so_pendentes ? '#1d4ed8' : 'var(--text)' }};white-space:nowrap;">
        <input type="checkbox" wire:model.live="so_pendentes" style="width:14px;height:14px;accent-color:#2563a8;margin:0;cursor:pointer;">
        Só pendentes
    </label>

    {{-- Limpar --}}
    @if($tipo || $responsavel_id || !$so_pendentes || $data_ini !== today()->format('Y-m-d') || $data_fim !== today()->addDays(30)->format('Y-m-d'))
    <button wire:click="$set('tipo',''); $set('responsavel_id',''); $set('so_pendentes', true); $set('data_ini', '{{ today()->format('Y-m-d') }}'); $set('data_fim', '{{ today()->addDays(30)->format('Y-m-d') }}')"
        style="padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;background:none;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap;"
        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
        <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Limpar filtros
    </button>
    @endif
</div>
@endif {{-- /!embed filters --}}

{{-- Grid principal --}}
<div class="agenda-grid" style="display:grid;grid-template-columns:1fr;gap:20px;align-items:start;">

    {{-- Coluna única --}}
    <div>

        {{-- Conteudo: Calendario + Lista --}}
        <div class="card">
@if($vistaCalendario)
        @php
            $meses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                      7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
            $diasSemana = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];

            // Ajuste: semana começa em domingo (0) — Carbon usa 0=Sunday
            $primeiroDia  = $inicioMes->copy()->startOfMonth();
            $ultimoDia    = $inicioMes->copy()->endOfMonth();
            $inicioCelula = $primeiroDia->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $hoje         = today()->format('Y-m-d');

            $coresTipo = ['Prazo'=>'#dc2626','Audiência'=>'#d97706','Reunião'=>'#7c3aed',
                          'Consulta'=>'#0891b2','Despacho'=>'#16a34a','Outros'=>'#2563a8'];
        @endphp
        {{-- Legenda unificada --}}
        <div style="display:flex;gap:14px;flex-wrap:wrap;padding:10px 16px 8px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);">
            <span style="font-weight:700;color:var(--text);">Legenda:</span>
            <span style="display:flex;align-items:center;gap:4px;">
                <span style="width:10px;height:10px;border-radius:2px;background:#f43f5e;display:inline-block;"></span> Prazo Fatal
            </span>
            <span style="display:flex;align-items:center;gap:4px;">
                <span style="width:10px;height:10px;border-radius:2px;background:#f97316;display:inline-block;"></span> Prazo
            </span>
            <span style="display:flex;align-items:center;gap:4px;">
                <span style="width:10px;height:10px;border-radius:2px;background:#3b82f6;display:inline-block;"></span> Audiência
            </span>
            @foreach(['Reunião'=>'#7c3aed','Consulta'=>'#0891b2','Despacho'=>'#16a34a','Outros'=>'#2563a8'] as $t => $c)
            <span style="display:flex;align-items:center;gap:4px;">
                <span style="width:10px;height:10px;border-radius:2px;background:{{ $c }};display:inline-block;"></span> {{ $t }}
            </span>
            @endforeach
        </div>

        {{-- Navegação de mês --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);">
            <button wire:click="mesAnterior"
                style="padding:6px 14px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Anterior
            </button>
            <span style="font-size:16px;font-weight:700;color:var(--primary);">
                {{ $meses[$mesCalendario] }} {{ $anoCalendario }}
            </span>
            <button wire:click="proximoMes"
                style="padding:6px 14px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                Próximo
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        {{-- Grade do calendário --}}
        <div style="padding:12px 16px;">

            {{-- Cabeçalho dias da semana --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:4px;">
                @foreach($diasSemana as $ds)
                <div style="text-align:center;font-size:11px;font-weight:700;color:var(--muted);padding:4px 0;text-transform:uppercase;">
                    {{ $ds }}
                </div>
                @endforeach
            </div>

            {{-- Células --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;">
                @php $cursor = $inicioCelula->copy(); @endphp
                @while($cursor->lte($ultimoDia) || $cursor->dayOfWeek !== 0)
                @php
                    $dataStr    = $cursor->format('Y-m-d');
                    $doMes      = $cursor->month === $inicioMes->month;
                    $isHoje     = $dataStr === $hoje;
                    $isSel      = $dataStr === $diaSelecionado;
                    $evsDia     = $eventosMes->get($dataStr, collect());
                    $prazDia    = $prazosMes->get($dataStr, collect());
                    $audDia     = $audienciasMes->get($dataStr, collect());
                    $totalDia   = $evsDia->count() + $prazDia->count() + $audDia->count();
                    $mostrados  = 0;
                    $maxMostrar = 3;
                @endphp
                <div wire:click="selecionarDia('{{ $dataStr }}')"
                    style="min-height:80px;padding:6px;border-radius:8px;cursor:pointer;
                           border:2px solid {{ $isSel ? '#2563a8' : ($isHoje ? '#93c5fd' : 'transparent') }};
                           background:{{ $isSel ? '#eff6ff' : ($isHoje ? '#f0f9ff' : ($doMes ? '#fff' : '#f8fafc')) }};
                           box-shadow:{{ $doMes ? '0 1px 3px rgba(0,0,0,.06)' : 'none' }};
                           transition:all .12s;"
                    onmouseover="this.style.background='#eff6ff'"
                    onmouseout="this.style.background='{{ $isSel ? '#eff6ff' : ($isHoje ? '#f0f9ff' : ($doMes ? '#fff' : '#f8fafc')) }}'">

                    <div style="font-size:13px;font-weight:{{ $isHoje ? '800' : '600' }};
                                color:{{ !$doMes ? '#cbd5e1' : ($isHoje ? '#2563a8' : '#1e293b') }};
                                margin-bottom:4px;">
                        {{ $cursor->day }}
                        @if($isHoje)<span style="font-size:9px;background:#2563a8;color:#fff;border-radius:10px;padding:0 5px;margin-left:3px;">hoje</span>@endif
                    </div>

                    @if($totalDia > 0 && $doMes)
                    <div style="display:flex;flex-direction:column;gap:2px;">

                        {{-- Prazos (fatal primeiro) --}}
                        @foreach($prazDia->sortByDesc('prazo_fatal') as $pz)
                            @if($mostrados < $maxMostrar)
                            @php $corPz = $pz->prazo_fatal ? '#f43f5e' : '#f97316'; $mostrados++; @endphp
                            <div style="font-size:10px;font-weight:600;color:#fff;background:{{ $corPz }};
                                        border-radius:3px;padding:1px 5px;white-space:nowrap;
                                        overflow:hidden;text-overflow:ellipsis;"
                                 title="{{ $pz->titulo }}{{ $pz->prazo_fatal ? ' [FATAL]' : '' }}">
                                {{ mb_strimwidth($pz->titulo, 0, 16, '…') }}
                            </div>
                            @endif
                        @endforeach

                        {{-- Audiências --}}
                        @foreach($audDia as $au)
                            @if($mostrados < $maxMostrar)
                            @php $mostrados++; @endphp
                            <div style="font-size:10px;font-weight:600;color:#fff;background:#3b82f6;
                                        border-radius:3px;padding:1px 5px;white-space:nowrap;
                                        overflow:hidden;text-overflow:ellipsis;"
                                 title="{{ $au->tipoLabel() }} — {{ $au->data_hora->format('H:i') }}">
                                {{ $au->data_hora->format('H:i') }} {{ mb_strimwidth($au->tipoLabel(), 0, 10, '…') }}
                            </div>
                            @endif
                        @endforeach

                        {{-- Eventos de agenda --}}
                        @foreach($evsDia as $ev)
                            @if($mostrados < $maxMostrar)
                            @php $cor = $coresTipo[$ev->tipo] ?? '#2563a8'; $mostrados++; @endphp
                            <div style="font-size:10px;font-weight:600;color:#fff;background:{{ $cor }};
                                        border-radius:3px;padding:1px 5px;white-space:nowrap;
                                        overflow:hidden;text-overflow:ellipsis;"
                                 title="{{ $ev->titulo }}">
                                {{ mb_strimwidth($ev->titulo, 0, 16, '…') }}
                            </div>
                            @endif
                        @endforeach

                        @if($totalDia > $maxMostrar)
                        <div style="font-size:10px;color:var(--muted);font-weight:600;padding:0 2px;">
                            +{{ $totalDia - $maxMostrar }} mais
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
                @php $cursor->addDay(); @endphp
                @endwhile
            </div>
        </div>

        {{-- Detalhe de prazos e audiências do dia selecionado --}}
        @if($diaSelecionado)
        @php
            $prazSel = $prazosMes->get($diaSelecionado, collect());
            $audSel  = $audienciasMes->get($diaSelecionado, collect());
        @endphp
        @if($prazSel->isNotEmpty() || $audSel->isNotEmpty())
        <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:8px;">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">
                Prazos e Audiências — {{ \Carbon\Carbon::parse($diaSelecionado)->format('d/m/Y') }}
            </div>
            @foreach($prazSel->sortByDesc('prazo_fatal') as $pz)
            <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;background:{{ $pz->prazo_fatal ? '#fff1f2' : '#fff7ed' }};border-left:3px solid {{ $pz->prazo_fatal ? '#f43f5e' : '#f97316' }};">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);">
                        {{ $pz->titulo }}
                        @if($pz->prazo_fatal)<span style="font-size:10px;background:#fecdd3;color:#e11d48;padding:1px 6px;border-radius:10px;margin-left:6px;font-weight:700;">FATAL</span>@endif
                    </div>
                    @if($pz->processo)<div style="font-size:12px;color:var(--muted);margin-top:2px;">Processo {{ $pz->processo->numero }}</div>@endif
                </div>
                <span style="font-size:11px;font-weight:700;color:{{ $pz->prazo_fatal ? '#e11d48' : '#ea580c' }};white-space:nowrap;">
                    {{ ucfirst($pz->status) }}
                </span>
            </div>
            @endforeach
            @foreach($audSel as $au)
            <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;background:#eff6ff;border-left:3px solid #3b82f6;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);">
                        {{ $au->tipoLabel() }} — {{ $au->data_hora->format('H:i') }}
                    </div>
                    @if($au->processo)<div style="font-size:12px;color:var(--muted);margin-top:2px;">Processo {{ $au->processo->numero }}</div>@endif
                </div>
                @if($au->processo)
                <a href="{{ route('processos.show', $au->processo_id) }}" style="font-size:11px;color:#2563eb;white-space:nowrap;text-decoration:none;">ver</a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
        @endif

        @if($diaSelecionado)
        <div style="padding:0 16px 4px;display:flex;align-items:center;gap:8px;">
            <span style="font-size:12px;color:#2563a8;font-weight:600;">
                Mostrando eventos de {{ \Carbon\Carbon::parse($diaSelecionado)->format('d/m/Y') }}
            </span>
            <button wire:click="selecionarDia('{{ $diaSelecionado }}')"
                style="background:none;border:none;cursor:pointer;font-size:11px;color:var(--muted);display:flex;align-items:center;gap:3px;">
                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                limpar
            </button>
        </div>
        @endif
        @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- LISTA (sempre visível; no calendário mostra o dia sel) --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if(!$vistaCalendario || $diaSelecionado)
        <div class="agenda-list-head">
            <div>
                <strong>{{ $diaSelecionado ? 'Eventos do dia selecionado' : 'Próximos eventos' }}</strong>
                <span>{{ $so_pendentes ? 'Mostrando compromissos pendentes.' : 'Incluindo eventos concluídos.' }}</span>
            </div>
            <span>{{ $eventos->total() }} registro(s)</span>
        </div>
        <div class="table-wrap">
            <table style="border-collapse:collapse;width:100%;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);">
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Data/Hora</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Evento</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Local</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Processo</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Responsável</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;">Urgente</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:110px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $ev)
                    @php $cor = match($ev->tipo) { 'Prazo'=>'#dc2626','Audiência'=>'#d97706','Reunião'=>'#7c3aed',default=>'#2563a8' }; @endphp
                    <tr style="border-bottom:1px solid var(--border);transition:background .15s;{{ $ev->concluido ? 'opacity:.5;' : '' }}"
                        onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px;">{{ $ev->data_hora->format('d/m/Y') }}</strong>
                            <span style="color:var(--muted);font-size:12px;"> {{ $ev->data_hora->format('H:i') }}</span>
                        </td>
                        <td style="padding:14px 16px;font-size:13px;">{{ $ev->titulo }}</td>
                        <td style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $ev->local ?? '—' }}</td>
                        <td style="padding:14px 16px;">
                            <span class="badge" style="background:{{ $cor }}22;color:{{ $cor }}">{{ $ev->tipo }}</span>
                        </td>
                        <td style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $ev->processo?->numero ?? '—' }}</td>
                        <td style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $ev->responsavel?->pessoa?->nome ?? '—' }}</td>
                        <td style="padding:14px 16px;text-align:center;">
                            @if($ev->urgente)
                                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="#dc2626" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;text-align:center;">
                            <div style="display:flex;justify-content:center;gap:4px;">
                                @if(!$ev->concluido)
                                <button wire:click="concluir({{ $ev->id }})" title="Concluir"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                                @endif
                                <button wire:click="abrirModal({{ $ev->id }})" title="Editar"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button wire:click="excluir({{ $ev->id }})"
                                    wire:confirm="Remover este evento?"
                                    title="Excluir"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--muted);padding:48px;">
                            <svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <div style="font-size:14px;font-weight:500;">{{ $diaSelecionado ? 'Nenhum evento neste dia.' : 'Nenhum evento encontrado.' }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
            <span style="font-size:13px;color:var(--muted);">
                @if($eventos->total() > 0)
                    Mostrando {{ $eventos->firstItem() }}–{{ $eventos->lastItem() }} de {{ $eventos->total() }}
                @else
                    Nenhum resultado
                @endif
            </span>
            <div style="display:flex;align-items:center;gap:6px;">
                <button wire:click="previousPage" @disabled($eventos->onFirstPage())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $eventos->onFirstPage() ? '.4' : '1' }};">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Anterior
                </button>
                <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                    {{ $eventos->currentPage() }} / {{ $eventos->lastPage() }}
                </span>
                <button wire:click="nextPage" @disabled(!$eventos->hasMorePages())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $eventos->hasMorePages() ? '1' : '.4' }};">
                    Próxima
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
        @endif

    </div>{{-- /card --}}
        </div>{{-- /card conteudo --}}

    </div>{{-- /coluna direita --}}
</div>{{-- /grid --}}

{{-- ── Modal ── --}}
    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal" style="max-width:520px">
            <div class="modal-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                        @if($eventoId)
                            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        @else
                            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        @endif
                    </div>
                    <span class="modal-title">{{ $eventoId ? 'Editar Evento' : 'Novo Evento' }}</span>
                </div>
                <button wire:click="fecharModal" class="modal-close" aria-label="Fechar">
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            @php
            $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
            $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
            @endphp

            {{-- Seção: Dados do Evento --}}
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Dados do Evento
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Título *</label>
                <input type="text" wire:model="titulo" placeholder="Descrição do evento" style="{{ $inp }}">
                @error('titulo')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data e Hora *</label>
                    <input type="datetime-local" wire:model="data_hora" style="{{ $inp }}">
                    @error('data_hora')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo *</label>
                    <select wire:model="tipo_evento" style="{{ $inp }}">
                        @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Local</label>
                    <input type="text" wire:model="local" placeholder="Local do evento" style="{{ $inp }}">
                </div>
                <div class="form-field">
                    <label class="lbl">Processo</label>
                    @if($embed)
                        <input type="hidden" wire:model="processo_id" value="{{ $processoId }}">
                        <div style="font-size:13px;color:var(--muted);padding:8px 0;">Processo vinculado automaticamente ao processo atual.</div>
                    @else
                        <select wire:model="processo_id" style="{{ $inp }}">
                            <option value="">Nenhum</option>
                            @foreach($processos as $proc)
                                <option value="{{ $proc->id }}">{{ $proc->numero }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                    <input type="checkbox" wire:model="urgente" style="width:auto">
                    <span style="display:flex;align-items:center;gap:5px;">
                        <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="#dc2626" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                        Marcar como urgente
                    </span>
                </label>
            </div>

            {{-- Seção: Observações --}}
            <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Observações
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <textarea wire:model="observacoes" rows="2" placeholder="Detalhes adicionais..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
            </div>

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success" style="display:flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

</div>
