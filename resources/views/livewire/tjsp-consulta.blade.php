<div @if($verificacao?->emAndamento()) wire:poll.2000ms @endif>

<style>
@keyframes spin    { from { transform:rotate(0deg);   } to { transform:rotate(360deg);  } }
@keyframes fadeIn  { from { opacity:0;transform:translateY(6px); } to { opacity:1;transform:translateY(0); } }
.tjsp-card          { background:var(--white);border:1.5px solid var(--border);border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06); }
.tjsp-kpi-grid      { display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px; }
.tjsp-and-card      { background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:10px;transition:box-shadow .15s;animation:fadeIn .2s ease; }
.tjsp-and-card:hover{ box-shadow:0 4px 16px rgba(0,0,0,.08); }
.tjsp-search-input  { flex:1;padding:13px 18px;border:2px solid #dbeafe;border-radius:10px;font-size:15px;outline:none;font-family:monospace;transition:border-color .2s;background:var(--white);min-width:0; }
.tjsp-search-input:focus { border-color:#2563a8; }
.tjsp-btn-blue      { padding:13px 26px;background:linear-gradient(135deg,#2563a8,#1d4ed8);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;white-space:nowrap;box-shadow:0 2px 8px rgba(37,99,168,.3);transition:transform .1s,box-shadow .1s; }
.tjsp-btn-blue:hover{ transform:translateY(-1px);box-shadow:0 4px 14px rgba(37,99,168,.4); }
.tjsp-btn-blue:disabled{ background:#94a3b8;box-shadow:none;cursor:not-allowed;transform:none; }
.tjsp-badge         { display:inline-flex;align-items:center;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700; }
.tjsp-tab-btn       { padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;transition:all .15s;white-space:nowrap;display:inline-flex;align-items:center;gap:6px; }
.tjsp-th            { text-align:left;padding:10px 16px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px; }
.tjsp-td            { padding:12px 16px;font-size:13px; }
@media (max-width:768px) {
    .tjsp-kpi-grid       { grid-template-columns:1fr 1fr !important; }
    .tjsp-filtros-grid   { grid-template-columns:1fr 1fr !important; }
    .tjsp-search-row     { flex-direction:column !important; }
    .tjsp-search-wrap    { padding:20px !important; }
    .tjsp-tabs-scroll    { overflow-x:auto; }
    .tjsp-result-header  { flex-direction:column !important; }
    .tjsp-result-actions { justify-content:flex-start !important; }
}
</style>

{{-- ── Cabeçalho ── --}}
<div style="margin-bottom:22px;">
    <a href="{{ route('ferramentas.hub') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:14px;"
       onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar
    </a>
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#1e40af,#2563a8);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(37,99,168,.28);">
            <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="1.5" viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
        </div>
        <div>
            <h1 style="font-size:22px;font-weight:800;color:var(--text);margin:0;line-height:1.2;">Consulta Judicial</h1>
            <p style="font-size:13px;color:var(--muted);margin-top:4px;">DATAJUD/CNJ — tribunal detectado automaticamente pelo número CNJ do processo</p>
        </div>
    </div>
</div>

{{-- ── KPI Cards ── --}}
<div class="tjsp-kpi-grid">

    <div class="tjsp-card" style="padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #2563a8;">
        <div style="width:42px;height:42px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#2563a8" stroke-width="1.5" viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
        </div>
        <div>
            <div style="font-size:24px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['total_ativos'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Processos ativos</div>
        </div>
    </div>

    <div class="tjsp-card" style="padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #d97706;">
        <div style="width:42px;height:42px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div style="font-size:24px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['nunca_consultados'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Nunca consultados</div>
        </div>
    </div>

    <div class="tjsp-card" style="padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #16a34a;">
        <div style="width:42px;height:42px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="1.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div style="font-size:24px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['consultados_hoje'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Atualizados hoje</div>
        </div>
    </div>

    <div class="tjsp-card" style="padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #7c3aed;cursor:pointer;"
         wire:click="trocarAba('monitoramentos')"
         onmouseover="this.style.boxShadow='0 4px 14px rgba(124,58,237,.15)'"
         onmouseout="this.style.boxShadow=''">
        <div style="width:42px;height:42px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div>
            <div style="font-size:24px;font-weight:800;color:var(--text);line-height:1;">{{ $monitoramentosAtivos }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Monitorados ativos</div>
        </div>
    </div>

</div>

{{-- ── Analista IA ── --}}
<div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:12px;padding:14px 20px;margin-bottom:22px;box-shadow:0 4px 14px rgba(15,37,64,.25);">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5" style="flex-shrink:0;"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
        <input wire:model="perguntaIA" wire:keydown.enter="perguntarIA" type="text"
            placeholder="Pergunte sobre as consultas... Ex: 'Quantos processos nunca foram consultados?'"
            style="flex:1;min-width:200px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:9px 16px;color:#fff;font-size:13px;outline:none;">
        <button wire:click="perguntarIA" wire:loading.attr="disabled"
            style="background:#2563a8;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;flex-shrink:0;">
            <svg wire:loading.remove wire:target="perguntarIA" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            <svg wire:loading wire:target="perguntarIA" style="animation:spin .7s linear infinite;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            <span wire:loading.remove wire:target="perguntarIA">✨ Analisar</span>
            <span wire:loading wire:target="perguntarIA">Analisando...</span>
        </button>
    </div>
    @if($respostaIA)
    <div style="margin-top:12px;background:rgba(255,255,255,.08);border-radius:8px;padding:12px 16px;font-size:13px;color:#e2e8f0;line-height:1.6;display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
        <span>{{ $respostaIA }}</span>
        <button wire:click="limparIA" style="background:none;border:none;cursor:pointer;color:#93c5fd;flex-shrink:0;padding:2px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    @endif
</div>

{{-- ── Tabs ── --}}
<div class="tjsp-tabs-scroll" style="margin-bottom:22px;">
    <div style="display:flex;gap:0;border-bottom:2px solid var(--border);min-width:max-content;">
        <button wire:click="trocarAba('consulta')" class="tjsp-tab-btn"
            style="border-bottom:2px solid {{ $abaAtiva==='consulta' ? '#2563a8' : 'transparent' }};margin-bottom:-2px;color:{{ $abaAtiva==='consulta' ? '#2563a8' : 'var(--muted)' }};">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Consulta
        </button>
        <button wire:click="trocarAba('verificar')" class="tjsp-tab-btn"
            style="border-bottom:2px solid {{ $abaAtiva==='verificar' ? '#2563a8' : 'transparent' }};margin-bottom:-2px;color:{{ $abaAtiva==='verificar' ? '#2563a8' : 'var(--muted)' }};">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
            Verificar em Lote
        </button>
        <button wire:click="trocarAba('monitoramentos')" class="tjsp-tab-btn"
            style="border-bottom:2px solid {{ $abaAtiva==='monitoramentos' ? '#2563a8' : 'transparent' }};margin-bottom:-2px;color:{{ $abaAtiva==='monitoramentos' ? '#2563a8' : 'var(--muted)' }};">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Monitoramentos
            @if($monitoramentosAtivos > 0)
            <span style="background:#16a34a;color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;line-height:1.6;">{{ $monitoramentosAtivos }}</span>
            @endif
        </button>
        <button wire:click="trocarAba('historico')" class="tjsp-tab-btn"
            style="border-bottom:2px solid {{ $abaAtiva==='historico' ? '#2563a8' : 'transparent' }};margin-bottom:-2px;color:{{ $abaAtiva==='historico' ? '#2563a8' : 'var(--muted)' }};">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Histórico
            @if($consultasRecentes->count() > 0)
            <span style="background:#94a3b8;color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;line-height:1.6;">{{ $consultasRecentes->count() }}</span>
            @endif
        </button>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- ABA: CONSULTA RÁPIDA                                          --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($abaAtiva === 'consulta')

    {{-- Hero Search Box --}}
    <div class="tjsp-card tjsp-search-wrap" style="padding:32px;text-align:center;margin-bottom:22px;">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,#dbeafe,#eff6ff);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" fill="none" stroke="#2563a8" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <h2 style="font-size:18px;font-weight:700;color:var(--text);margin:0 0 6px;">Consultar processo no DATAJUD</h2>
        <p style="font-size:13px;color:var(--muted);margin:0 0 20px;">O tribunal é detectado automaticamente pelo código CNJ. Suporta todos os tribunais no DATAJUD.</p>
        <div class="tjsp-search-row" style="display:flex;gap:10px;align-items:stretch;max-width:680px;margin:0 auto;">
            <input wire:model="numeroBusca" wire:keydown.enter="consultarNumero"
                type="text" placeholder="Ex: 0001234-56.2023.8.26.0001"
                class="tjsp-search-input">
            <button wire:click="consultarNumero"
                wire:loading.attr="disabled" wire:target="consultarNumero"
                @if($consultandoNumero) disabled @endif
                class="tjsp-btn-blue">
                <svg wire:loading.remove wire:target="consultarNumero" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <svg wire:loading wire:target="consultarNumero" style="animation:spin .7s linear infinite;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <span wire:loading.remove wire:target="consultarNumero">Consultar</span>
                <span wire:loading wire:target="consultarNumero">Consultando...</span>
            </button>
        </div>
    </div>

    {{-- Resultado da consulta --}}
    @if($resultadoConsulta)
    @php $andsTotais = count($resultadoConsulta['andamentos']); @endphp

        {{-- Card resumo do processo --}}
        <div class="tjsp-card" style="margin-bottom:16px;border-left:4px solid #2563a8;overflow:hidden;padding:0;">
            <div class="tjsp-result-header" style="padding:16px 20px;background:linear-gradient(135deg,#eff6ff,#f0f9ff);display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:14px;border-bottom:1px solid #dbeafe;">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                        <span style="font-size:16px;font-weight:800;color:#1e40af;font-family:monospace;letter-spacing:.5px;">{{ $resultadoConsulta['numero'] }}</span>
                        @if(!empty($resultadoConsulta['tribunal']))
                        <span class="tjsp-badge" style="background:#1e40af;color:#fff;">{{ $resultadoConsulta['tribunal'] }}</span>
                        @endif
                        <span class="tjsp-badge" style="background:#dbeafe;color:#1e40af;">
                            {{ $andsTotais }} andamento{{ $andsTotais !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    @if(!empty($resultadoConsulta['classe']))
                    <p style="font-size:12px;color:#64748b;margin:0 0 8px;">
                        {{ $resultadoConsulta['classe'] }}
                        @if(!empty($resultadoConsulta['assunto'])) · {{ $resultadoConsulta['assunto'] }} @endif
                    </p>
                    @endif
                    @if($processoDoResultado)
                    <div style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:1px solid #86efac;padding:4px 12px;border-radius:8px;">
                        <svg width="12" height="12" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span style="font-size:12px;color:#166534;font-weight:600;">Cadastrado no Software Jur�dico</span>
                        <a href="/processos/{{ $processoDoResultado->id }}" style="font-size:12px;color:#2563a8;font-weight:700;text-decoration:none;">ver →</a>
                    </div>
                    @else
                    <div style="display:inline-flex;align-items:center;gap:6px;background:#fffbeb;border:1px solid #fde68a;padding:4px 12px;border-radius:8px;">
                        <svg width="12" height="12" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span style="font-size:12px;color:#92400e;font-weight:500;">Não cadastrado no Software Jur�dico</span>
                    </div>
                    @endif
                </div>

                <div class="tjsp-result-actions" style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                    @if($processoDoResultado)
                        @if($andamentosSalvos > 0)
                        <span style="background:#f0fdf4;color:#16a34a;border:1.5px solid #86efac;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                            <svg width="13" height="13" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ $andamentosSalvos }} importado(s)
                        </span>
                        <a href="{{ route('processos.show', $processoDoResultado->id) }}"
                           style="background:#eff6ff;color:#1d4ed8;border:1.5px solid #bfdbfe;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                            Abrir processo
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                        @else
                        <button wire:click="salvarAndamentos" wire:loading.attr="disabled" wire:target="salvarAndamentos"
                            style="background:#16a34a;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(22,163,74,.25);">
                            <svg wire:loading.remove wire:target="salvarAndamentos" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            <svg wire:loading wire:target="salvarAndamentos" style="animation:spin .7s linear infinite;" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            <span wire:loading.remove wire:target="salvarAndamentos">💾 Salvar andamentos</span>
                            <span wire:loading wire:target="salvarAndamentos">Salvando...</span>
                        </button>
                        @endif
                    @endif
                    <button wire:click="monitorar('{{ $resultadoConsulta['numero'] }}', {{ $processoDoResultado?->id ?? 'null' }})"
                        style="background:#1a3a5c;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(26,58,92,.25);">
                        📡 Monitorar
                    </button>
                </div>
            </div>
        </div>

        {{-- Cards de andamentos --}}
        @foreach($resultadoConsulta['andamentos'] as $idx => $a)
        @if($idx >= 20) @break @endif
        @php
            $dtAnd = \Carbon\Carbon::parse($a['data']);
        @endphp
        <div class="tjsp-and-card">
            <div style="display:flex;align-items:flex-start;gap:14px;">

                {{-- Bloco de data --}}
                <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:10px;padding:10px 14px;text-align:center;min-width:60px;flex-shrink:0;">
                    <div style="font-size:20px;font-weight:800;color:#1e40af;line-height:1;">{{ $dtAnd->format('d') }}</div>
                    <div style="font-size:11px;color:#3b82f6;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">{{ $dtAnd->format('M') }}</div>
                    <div style="font-size:11px;color:#93c5fd;font-weight:600;">{{ $dtAnd->format('Y') }}</div>
                </div>

                {{-- Conteúdo --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:7px;">
                        @if(!empty($resultadoConsulta['tribunal']))
                        <span class="tjsp-badge" style="background:#dbeafe;color:#1e40af;">{{ $resultadoConsulta['tribunal'] }}</span>
                        @endif
                        <span style="font-size:12px;color:var(--muted);font-family:monospace;">{{ $resultadoConsulta['numero'] }}</span>
                        @if($andamentosNovosSet[$idx] ?? false)
                        <span class="tjsp-badge" style="background:#dcfce7;color:#15803d;">✦ Novo</span>
                        @else
                        <span class="tjsp-badge" style="background:#f1f5f9;color:#94a3b8;">Existente</span>
                        @endif
                    </div>
                    <p style="font-size:13px;color:var(--text);line-height:1.6;margin:0;">{{ $a['descricao'] }}</p>
                </div>

            </div>
        </div>
        @endforeach

        @if($andsTotais > 20)
        <div class="tjsp-card" style="text-align:center;padding:14px;color:var(--muted);font-size:13px;font-style:italic;">
            ... e mais {{ $andsTotais - 20 }} andamento(s) não exibidos
        </div>
        @endif

    @else

        {{-- Empty state --}}
        <div class="tjsp-card" style="text-align:center;padding:64px 24px;">
            <div style="width:72px;height:72px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                <svg width="34" height="34" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <p style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 6px;">Nenhuma consulta realizada</p>
            <p style="font-size:13px;color:var(--muted);margin:0;">Digite o número de um processo acima para buscar no DATAJUD.</p>
        </div>

    @endif

@endif
{{-- /aba consulta --}}


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- ABA: VERIFICAR EM LOTE                                        --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($abaAtiva === 'verificar')

    {{-- Filtros --}}
    <div class="tjsp-card" style="padding:18px 20px;margin-bottom:18px;">
        <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filtros
        </div>
        <div class="tjsp-filtros-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;align-items:end;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Cliente</label>
                <input wire:model.live.debounce.300ms="filtroCliente" type="text" placeholder="Buscar cliente..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;background:var(--white);outline:none;">
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Número do Processo</label>
                <input wire:model.live.debounce.300ms="filtroNumero" type="text" placeholder="Ex: 0001234-56.2023..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;background:var(--white);outline:none;">
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Status</label>
                <select wire:model.live="filtroStatus"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;outline:none;">
                    <option value="Ativo">Apenas Ativos</option>
                    <option value="">Todos</option>
                    <option value="Arquivado">Arquivados</option>
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Última Consulta</label>
                <select wire:model.live="filtroConsulta"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;outline:none;">
                    <option value="">Todos</option>
                    <option value="nunca">Nunca consultados</option>
                    <option value="semana">Não consultados esta semana</option>
                    <option value="mes">Não consultados este mês</option>
                </select>
            </div>
            <div>
                @php $bloqueado = $consultando || $verificacao?->emAndamento() || $totalFiltrado === 0; @endphp
                <button wire:click="iniciarVerificacao"
                    wire:loading.attr="disabled" wire:target="iniciarVerificacao"
                    wire:confirm="Verificar {{ $totalFiltrado }} processo(s) no DATAJUD? A operação roda em segundo plano e pode levar alguns minutos."
                    @if($bloqueado) disabled @endif
                    style="width:100%;padding:9px 16px;background:{{ $bloqueado ? '#94a3b8' : 'linear-gradient(135deg,#16a34a,#15803d)' }};color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:{{ $bloqueado ? 'not-allowed' : 'pointer' }};white-space:nowrap;display:flex;align-items:center;justify-content:center;gap:6px;box-shadow:{{ $bloqueado ? 'none' : '0 2px 8px rgba(22,163,74,.25)' }};">
                    <span wire:loading.remove wire:target="iniciarVerificacao">
                        @if($verificacao?->emAndamento()) ⏳ Verificando...
                        @else 🔄 Verificar ({{ $totalFiltrado }})
                        @endif
                    </span>
                    <span wire:loading wire:target="iniciarVerificacao">Iniciando...</span>
                </button>
            </div>
        </div>
        <div class="tjsp-filtros-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;margin-top:12px;align-items:end;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Fase</label>
                <select wire:model.live="filtroFase"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;outline:none;">
                    <option value="">Todas as fases</option>
                    @foreach($fases as $fase)
                    <option value="{{ $fase->descricao }}">{{ $fase->descricao }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Advogado</label>
                <select wire:model.live="filtroAdvogado"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;outline:none;">
                    <option value="">Todos os advogados</option>
                    @foreach($advogados as $adv)
                    <option value="{{ $adv->nome }}">{{ $adv->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Período — De</label>
                <input wire:model.live="filtroDataIni" type="date"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;outline:none;">
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">Período — Até</label>
                <input wire:model.live="filtroDataFim" type="date"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;outline:none;">
            </div>
            <div>
                <button wire:click="limparFiltros"
                    style="width:100%;padding:9px 16px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:#475569;cursor:pointer;font-weight:600;">
                    Limpar
                </button>
            </div>
        </div>
    </div>

    {{-- Barra de progresso --}}
    @if($verificacao?->emAndamento())
    <div class="tjsp-card" style="margin-bottom:18px;padding:18px 20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <span style="font-size:14px;font-weight:700;color:var(--primary);display:inline-flex;align-items:center;gap:8px;">
                <svg aria-hidden="true" width="16" height="16" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                Consultando processos no DATAJUD/CNJ...
            </span>
            <span style="font-size:18px;font-weight:800;color:var(--primary);">{{ $verificacao->porcentagem() }}%</span>
        </div>
        <div style="background:var(--border);border-radius:99px;height:10px;overflow:hidden;margin-bottom:10px;">
            <div style="background:linear-gradient(90deg,#2563a8,#16a34a);height:10px;border-radius:99px;transition:width .5s ease;width:{{ $verificacao->porcentagem() }}%;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted);margin-bottom:12px;">
            <span>{{ $verificacao->processado }} / {{ $verificacao->total }} processos</span>
            @if($verificacao->novos_total > 0)
            <span style="color:var(--success);font-weight:700;">✓ {{ $verificacao->novos_total }} andamento(s) encontrado(s)</span>
            @endif
        </div>
        @if(!empty($verificacao->log_linhas))
        @php
            $linhas = array_reverse(array_slice($verificacao->log_linhas, -15));
            $icones = [
                'consultando' => '<svg width="12" height="12" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
                'ok'          => '<svg width="12" height="12" fill="none" stroke="var(--success)" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
                'sem_novos'   => '<svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
                'erro'        => '<svg width="12" height="12" fill="none" stroke="var(--danger)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
                'ignorado'    => '<svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
            ];
            $cores = ['consultando'=>'var(--primary)','ok'=>'var(--success)','sem_novos'=>'var(--muted)','erro'=>'var(--danger)','ignorado'=>'var(--muted)'];
        @endphp
        <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;">
            <div style="padding:8px 12px;background:var(--bg);border-bottom:1px solid var(--border);font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Log em tempo real</div>
            <div style="max-height:220px;overflow-y:auto;font-family:monospace;font-size:12px;">
                @foreach($linhas as $linha)
                <div style="display:flex;align-items:baseline;gap:8px;padding:5px 12px;border-bottom:1px solid #f8fafc;{{ $loop->first ? 'background:#f0fdf4;' : '' }}">
                    <span style="color:var(--muted);flex-shrink:0;font-size:10px;">{{ $linha['ts'] }}</span>
                    <span style="flex-shrink:0;">{!! $icones[$linha['tipo']] ?? '•' !!}</span>
                    <span style="font-weight:600;flex-shrink:0;min-width:220px;color:var(--text);">{{ $linha['numero'] }}</span>
                    @if($linha['tribunal'])
                    <span class="tjsp-badge" style="background:#e0f2fe;color:#0369a1;font-size:10px;">{{ $linha['tribunal'] }}</span>
                    @endif
                    <span style="color:{{ $cores[$linha['tipo']] ?? 'var(--muted)' }};">{{ $linha['msg'] }}</span>
                    @if(($linha['novos'] ?? 0) > 0)
                    <span style="background:var(--success);color:#fff;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;margin-left:auto;flex-shrink:0;">+{{ $linha['novos'] }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Resultado concluído --}}
    @if($verificacao?->status === 'concluido')
    <div style="margin-bottom:18px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:#94a3b8;margin-bottom:16px;flex-wrap:wrap;gap:6px;">
            <span>Verificação concluída em {{ $verificacao->concluido_em?->format('d/m/Y H:i') }}</span>
            <span>{{ $verificacao->total }} processos em {{ $verificacao->iniciado_em->diffForHumans($verificacao->concluido_em, true) }}</span>
        </div>

        @if(count($verificacao->novos_andamentos ?? []) > 0)
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:16px 20px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <strong style="color:#16a34a;font-size:15px;display:inline-flex;align-items:center;gap:8px;">
                <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ count($verificacao->novos_andamentos) }} processo(s) com andamentos novos!
            </strong>
            @if(($verificacao->prazos_criados ?? 0) > 0)
            <a href="{{ route('prazos') }}"
               style="display:inline-flex;align-items:center;gap:6px;background:#1a3a5c;color:#fff;padding:7px 16px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;">
                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $verificacao->prazos_criados }} prazo(s) criado(s) →
            </a>
            @endif
        </div>

        @foreach($verificacao->novos_andamentos as $item)
        <div class="tjsp-card" style="margin-bottom:14px;overflow:hidden;border-left:4px solid #16a34a;padding:0;">
            <div style="padding:14px 20px;background:#f0fdf4;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;border-bottom:1px solid #bbf7d0;">
                <div>
                    <span style="font-size:14px;font-weight:700;color:var(--primary);font-family:monospace;">{{ $item['numero'] }}</span>
                    <span style="font-size:13px;color:#64748b;margin-left:12px;">{{ $item['cliente'] }}</span>
                    @if(!empty($item['tribunal']))
                    <span class="tjsp-badge" style="background:#dbeafe;color:#1e40af;margin-left:8px;">{{ $item['tribunal'] }}</span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="tjsp-badge" style="background:#16a34a;color:#fff;">{{ count($item['andamentos']) }} novo(s)</span>
                    <button wire:click="monitorar('{{ $item['numero'] }}')"
                        style="background:#1a3a5c;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;">
                        📡 Monitorar
                    </button>
                </div>
            </div>
            <div style="padding:10px 20px 4px;">
                @foreach($item['andamentos'] as $a)
                <div style="display:flex;gap:16px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                    <span style="color:#16a34a;font-weight:600;min-width:90px;flex-shrink:0;">{{ \Carbon\Carbon::parse($a['data'])->format('d/m/Y') }}</span>
                    <span style="color:var(--text);">{{ $a['descricao'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @else
        <div class="tjsp-card" style="text-align:center;padding:60px 24px;">
            <div style="width:64px;height:64px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg width="32" height="32" fill="none" stroke="#16a34a" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <p style="font-size:16px;font-weight:700;color:var(--primary);margin:0 0 6px;">Nenhum andamento novo encontrado</p>
            <p style="font-size:13px;color:#64748b;margin:0;">Todos os processos selecionados estão atualizados.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Estado inicial --}}
    @if(!$verificacao || $verificacao->status === 'erro')
    <div class="tjsp-card" style="text-align:center;padding:60px 24px;">
        <div style="width:72px;height:72px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
            <svg width="34" height="34" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
        </div>
        <p style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 8px;">Selecione os filtros e clique em "Verificar"</p>
        <p style="font-size:13px;color:#64748b;margin:0;max-width:480px;margin:0 auto;">O sistema consultará os processos selecionados no DATAJUD e mostrará apenas os com novos andamentos.</p>
    </div>
    @endif

@endif
{{-- /aba verificar --}}


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- ABA: MONITORAMENTOS                                           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($abaAtiva === 'monitoramentos')

    {{-- Header da aba --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
        <div>
            <h2 style="font-size:16px;font-weight:700;color:var(--text);margin:0;display:flex;align-items:center;gap:8px;">
                <svg width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Processos Monitorados
            </h2>
            <p style="font-size:13px;color:var(--muted);margin:5px 0 0;">
                Job automático às <strong>07:00</strong> e <strong>13:00</strong> — notifica por e-mail quando há novos andamentos
            </p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span style="background:#f0fdf4;color:#166534;border:1.5px solid #86efac;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                {{ $monitoramentosAtivos }} ativo(s)
            </span>
        </div>
    </div>

    @if($monitoramentos->count() > 0)
    <div class="tjsp-card" style="overflow:hidden;padding:0;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);background:var(--bg);">
                    <th class="tjsp-th">Processo</th>
                    <th class="tjsp-th">Tribunal</th>
                    <th class="tjsp-th">Último andamento</th>
                    <th class="tjsp-th">Status</th>
                    <th class="tjsp-th">E-mail</th>
                    <th class="tjsp-th" style="text-align:right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monitoramentos as $mon)
                @php
                    $indicador = $mon->ultimo_andamento_hash === null
                        ? ['cor' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'dot' => false, 'label' => 'Sincronizando']
                        : ($mon->ativo
                            ? ['cor' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#86efac', 'dot' => true,  'label' => 'Monitorando']
                            : ['cor' => '#94a3b8', 'bg' => '#f8fafc', 'border' => '#e2e8f0', 'dot' => false, 'label' => 'Pausado']);
                @endphp
                <tr style="border-bottom:1px solid #f8fafc;transition:background .1s;"
                    onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td class="tjsp-td">
                        <div style="font-weight:700;color:var(--text);font-family:monospace;font-size:12px;letter-spacing:.3px;">{{ $mon->numero_processo }}</div>
                        @if($mon->processo)
                        <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $mon->processo->cliente?->nome ?? '' }}</div>
                        @endif
                    </td>
                    <td class="tjsp-td">
                        @if($mon->tribunal)
                        <span class="tjsp-badge" style="background:#dbeafe;color:#1e40af;">{{ $mon->tribunal }}</span>
                        @else
                        <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td class="tjsp-td" style="color:var(--muted);font-size:12px;">
                        @if($mon->ultimo_andamento_data)
                            <div style="font-weight:600;color:var(--text);">{{ $mon->ultimo_andamento_data->format('d/m/Y') }}</div>
                            <div style="font-size:11px;margin-top:2px;color:#94a3b8;">{{ $mon->ultimo_andamento_data->diffForHumans() }}</div>
                        @else
                            <span style="color:#d97706;font-style:italic;font-size:12px;">Aguardando 1ª sync</span>
                        @endif
                    </td>
                    <td class="tjsp-td">
                        <span style="background:{{ $indicador['bg'] }};color:{{ $indicador['cor'] }};border:1px solid {{ $indicador['border'] }};padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                            @if($indicador['dot'])
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $indicador['cor'] }};display:inline-block;"></span>
                            @endif
                            {{ $indicador['label'] }}
                        </span>
                    </td>
                    <td class="tjsp-td">
                        <button wire:click="toggleEmail({{ $mon->id }})"
                            title="{{ $mon->notificar_email ? 'Desativar notificação por e-mail' : 'Ativar notificação por e-mail' }}"
                            style="background:none;border:1px solid {{ $mon->notificar_email ? '#bae6fd' : 'var(--border)' }};border-radius:6px;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;opacity:{{ $mon->notificar_email ? '1' : '.4' }};transition:opacity .15s;">
                            <svg width="14" height="14" fill="none" stroke="{{ $mon->notificar_email ? '#0369a1' : '#94a3b8' }}" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </button>
                    </td>
                    <td class="tjsp-td">
                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                            <button wire:click="toggleMonitoramento({{ $mon->id }})"
                                title="{{ $mon->ativo ? 'Pausar monitoramento' : 'Reativar monitoramento' }}"
                                style="width:32px;height:32px;border-radius:6px;border:1px solid {{ $mon->ativo ? '#fde68a' : '#bbf7d0' }};background:{{ $mon->ativo ? '#fffbeb' : '#f0fdf4' }};display:inline-flex;align-items:center;justify-content:center;color:{{ $mon->ativo ? '#d97706' : '#16a34a' }};cursor:pointer;">
                                @if($mon->ativo)
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                @else
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                @endif
                            </button>
                            <button wire:click="removerMonitoramento({{ $mon->id }})"
                                wire:confirm="Remover o monitoramento de '{{ addslashes($mon->numero_processo) }}'?"
                                style="width:32px;height:32px;border-radius:6px;border:1px solid #fecaca;background:#fff5f5;display:inline-flex;align-items:center;justify-content:center;color:#dc2626;cursor:pointer;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else
    <div class="tjsp-card" style="text-align:center;padding:64px 24px;">
        <div style="font-size:52px;margin-bottom:16px;opacity:.3;">📡</div>
        <p style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 8px;">Nenhum processo monitorado</p>
        <p style="font-size:13px;color:#64748b;margin:0 0 20px;max-width:440px;margin:0 auto 20px;">
            Use o botão <strong>📡 Monitorar</strong> nos resultados da consulta rápida ou na verificação em lote para adicionar processos.
        </p>
        <button wire:click="trocarAba('consulta')"
            style="background:#1a3a5c;color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Fazer uma consulta rápida
        </button>
    </div>
    @endif

    {{-- Legenda --}}
    <div style="margin-top:16px;padding:14px 18px;background:var(--bg);border-radius:10px;border:1px solid var(--border);font-size:12px;color:var(--muted);line-height:1.6;">
        <strong style="color:var(--text);">Como funciona:</strong>
        O job <code style="background:#e2e8f0;padding:1px 5px;border-radius:3px;font-size:11px;">VerificarMonitoramentos</code> roda todos os dias às <strong>07:00</strong> e <strong>13:00</strong>.
        Na primeira execução, sincroniza o estado atual sem notificar. Nas seguintes, detecta novos andamentos por comparação de hash — se houver mudanças,
        salva os andamentos no processo (se cadastrado), cria notificação interna e envia e-mail para o advogado responsável.
    </div>

@endif
{{-- /aba monitoramentos --}}


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- ABA: HISTÓRICO                                                 --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($abaAtiva === 'historico')

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
        <div>
            <h2 style="font-size:16px;font-weight:700;color:var(--text);margin:0;display:flex;align-items:center;gap:8px;">
                <svg width="18" height="18" fill="none" stroke="#2563a8" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Histórico de Consultas
            </h2>
            <p style="font-size:13px;color:var(--muted);margin:5px 0 0;">Últimas 10 buscas manuais realizadas no DATAJUD</p>
        </div>
        @if($consultasRecentes->count() > 0)
        <span style="background:#f1f5f9;color:#64748b;border:1px solid var(--border);padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;">
            {{ $consultasRecentes->count() }} consulta(s)
        </span>
        @endif
    </div>

    @if($consultasRecentes->count() > 0)
    <div class="tjsp-card" style="overflow:hidden;padding:0;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);background:var(--bg);">
                    <th class="tjsp-th">Processo</th>
                    <th class="tjsp-th">Tribunal</th>
                    <th class="tjsp-th">Andamentos</th>
                    <th class="tjsp-th">Usuário</th>
                    <th class="tjsp-th">Quando</th>
                    <th class="tjsp-th" style="text-align:right;">Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultasRecentes as $c)
                <tr style="border-bottom:1px solid #f8fafc;transition:background .1s;"
                    onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td class="tjsp-td" style="font-weight:700;color:var(--text);font-family:monospace;font-size:12px;letter-spacing:.3px;">
                        {{ $c->numero_processo }}
                    </td>
                    <td class="tjsp-td">
                        @if($c->tribunal)
                        <span class="tjsp-badge" style="background:#dbeafe;color:#1e40af;">{{ $c->tribunal }}</span>
                        @else
                        <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td class="tjsp-td">
                        @if($c->resultado_count > 0)
                        <span class="tjsp-badge" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">{{ $c->resultado_count }} andamento(s)</span>
                        @else
                        <span style="color:var(--muted);font-style:italic;font-size:12px;">Nenhum retornado</span>
                        @endif
                    </td>
                    <td class="tjsp-td" style="color:var(--muted);">{{ $c->usuario?->nome ?? '—' }}</td>
                    <td class="tjsp-td" style="color:var(--muted);font-size:12px;">
                        <div>{{ $c->created_at->diffForHumans() }}</div>
                        <div style="font-size:11px;color:#cbd5e1;margin-top:2px;">{{ $c->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="tjsp-td" style="text-align:right;">
                        <button
                            onclick="Livewire.find(this.closest('[wire\\:id]').getAttribute('wire:id')).set('numeroBusca', '{{ $c->numero_processo }}').then(() => { Livewire.find(this.closest('[wire\\:id]').getAttribute('wire:id')).call('trocarAba', 'consulta'); })"
                            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#2563a8;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:5px 12px;cursor:pointer;font-weight:600;white-space:nowrap;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                            Repetir
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else
    <div class="tjsp-card" style="text-align:center;padding:64px 24px;">
        <div style="width:72px;height:72px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
            <svg width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <p style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 6px;">Nenhuma consulta realizada ainda</p>
        <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Vá para a aba Consulta e pesquise pelo número de um processo.</p>
        <button wire:click="trocarAba('consulta')"
            style="background:#2563a8;color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Fazer primeira consulta
        </button>
    </div>
    @endif

@endif
{{-- /aba historico --}}

</div>
