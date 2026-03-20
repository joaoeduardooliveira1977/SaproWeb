<div>
<style>
@media (max-width: 768px) {
    .docs-grid    { grid-template-columns: 1fr !important; }
    .metricas-docs{ grid-template-columns: 1fr 1fr !important; }
    .filtros-docs { position: static !important; }
}
@media (max-width: 480px) {
    .metricas-docs { grid-template-columns: 1fr !important; }
}
</style>

{{-- Cabecalho --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <a href="{{ route('processos.hub') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:6px;"
           onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar
        </a>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">Documentos</h2>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            {{ $resumo->total }} documento{{ $resumo->total != 1 ? 's' : '' }} cadastrado{{ $resumo->total != 1 ? 's' : '' }}
            @if($resumo->total_tamanho)
            &mdash; {{ number_format($resumo->total_tamanho/1024/1024, 1) }} MB no total
            @endif
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button wire:click="exportarCsv" class="btn btn-sm btn-secondary-outline" wire:loading.attr="disabled" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:4px;">
                <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">...</span>
        </button>
        <button wire:click="abrirLote" class="btn btn-sm btn-secondary-outline" style="display:inline-flex;align-items:center;gap:5px;">
            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Lote
        </button>
        <button wire:click="novoDocumento" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:5px;">
            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo
        </button>
    </div>
</div>

{{-- Analista IA --}}
<div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:12px;padding:14px 20px;margin-bottom:12px;display:flex;align-items:center;gap:12px;">
    <div style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            <path d="M18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
        </svg>
    </div>
    <input wire:model="perguntaIA" wire:keydown.enter="perguntarIA" type="text"
        placeholder="Pergunte sobre os documentos... Ex: quantas peticoes temos, documentos do mes passado, contratos"
        style="flex:1;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;padding:10px 16px;color:#fff;font-size:13px;outline:none;"
        onfocus="this.style.borderColor='rgba(147,197,253,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.2)'">
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

{{-- Grid principal --}}
<div class="docs-grid" style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">

    {{-- COLUNA ESQUERDA: Filtros --}}
    <div class="filtros-docs" style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;position:sticky;top:20px;">

        {{-- Busca --}}
        <div style="margin-bottom:20px;">
            <div style="position:relative;">
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input wire:model.live.debounce.300ms="busca" type="text"
                    placeholder="Titulo, cliente, processo..."
                    style="width:100%;box-sizing:border-box;padding:9px 10px 9px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
            </div>
        </div>

        {{-- Tipo de Documento --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Tipo de Documento</div>
            <div style="display:flex;flex-direction:column;gap:3px;">
                @php
                $tipoFiltros = [
                    ''                  => ['Todos',        'var(--primary)', $resumo->total],
                    'peticao'           => ['Peticao',      '#7c3aed',        $resumo->peticoes],
                    'contrato'          => ['Contrato',     '#d97706',        $resumo->contratos],
                    'procuracao'        => ['Procuracao',   '#2563a8',        $resumo->procuracoes],
                    'laudo'             => ['Laudo',        '#0891b2',        $resumo->laudos],
                    'documento_cliente' => ['Doc. Cliente', '#64748b',        $resumo->docs_cliente],
                    'sentenca'          => ['Sentenca',     '#16a34a',        $resumo->sentencas],
                    'outro'             => ['Outro',        '#94a3b8',        $resumo->outros],
                ];
                @endphp
                @foreach($tipoFiltros as $val => [$label, $cor, $cnt])
                @php $sel = $filtroTipo === $val; @endphp
                <button wire:click="$set('filtroTipo', '{{ $val }}')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;text-align:left;width:100%;transition:all .15s;border:1.5px solid {{ $sel ? $cor.'88' : 'transparent' }};background:{{ $sel ? $cor.'18' : 'transparent' }};color:{{ $sel ? $cor : 'var(--text)' }};"
                    onmouseover="if(!{{ $sel ? 'true' : 'false' }}) this.style.background='var(--bg)'" onmouseout="if(!{{ $sel ? 'true' : 'false' }}) this.style.background='transparent'">
                    <span style="display:flex;align-items:center;gap:7px;font-weight:{{ $sel ? '600' : '400' }};">
                        @if($val !== '')
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $cor }};flex-shrink:0;display:inline-block;"></span>
                        @endif
                        {{ $label }}
                    </span>
                    @if($cnt > 0)
                    <span style="font-size:11px;font-weight:700;padding:1px 7px;border-radius:10px;background:{{ $sel ? $cor.'22' : '#f1f5f9' }};color:{{ $sel ? $cor : 'var(--muted)' }};">{{ $cnt }}</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- Vinculo --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Vinculo</div>
            <div style="display:flex;flex-direction:column;gap:3px;">
                @foreach(['' => 'Todos', 'processo' => 'Por Processo', 'cliente' => 'Por Cliente'] as $val => $label)
                @php $sel = $filtroVinculo === $val; @endphp
                <button wire:click="$set('filtroVinculo', '{{ $val }}')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;text-align:left;width:100%;transition:all .15s;border:1.5px solid {{ $sel ? 'var(--primary)' : 'transparent' }};background:{{ $sel ? '#eff6ff' : 'transparent' }};color:{{ $sel ? 'var(--primary)' : 'var(--text)' }};font-weight:{{ $sel ? '600' : '400' }};"
                    onmouseover="if(!{{ $sel ? 'true' : 'false' }}) this.style.background='var(--bg)'" onmouseout="if(!{{ $sel ? 'true' : 'false' }}) this.style.background='transparent'">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Periodo --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Periodo</div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <div>
                    <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:3px;">De</label>
                    <input wire:model.live="filtroDataIni" type="date"
                        style="width:100%;box-sizing:border-box;padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                </div>
                <div>
                    <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:3px;">Ate</label>
                    <input wire:model.live="filtroDataFim" type="date"
                        style="width:100%;box-sizing:border-box;padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                </div>
            </div>
        </div>

        {{-- Limpar --}}
        @if($busca || $filtroTipo || $filtroVinculo || $filtroDataIni || $filtroDataFim)
        <button wire:click="$set('busca',''); $set('filtroTipo',''); $set('filtroVinculo',''); $set('filtroDataIni',''); $set('filtroDataFim','')"
            style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;background:none;color:var(--muted);cursor:pointer;">
            <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar filtros
        </button>
        @endif
    </div>

    {{-- COLUNA DIREITA: Metricas + Tabela --}}
    <div>

        {{-- Metricas --}}
        <div class="metricas-docs" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-bottom:16px;">

            {{-- Total --}}
            <button wire:click="$set('filtroTipo', '')"
                style="background:var(--white);border:1.5px solid {{ $filtroTipo === '' ? 'var(--primary)' : 'var(--border)' }};border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;cursor:pointer;text-align:left;transition:border-color .15s;">
                <div style="width:40px;height:40px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1.1;">{{ number_format($resumo->total) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">documentos</div>
                </div>
            </button>

            {{-- Peticoes --}}
            <button wire:click="$set('filtroTipo', 'peticao')"
                style="background:var(--white);border:1.5px solid {{ $filtroTipo === 'peticao' ? '#7c3aed' : 'var(--border)' }};border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;cursor:pointer;text-align:left;transition:border-color .15s;">
                <div style="width:40px;height:40px;border-radius:9px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#7c3aed;line-height:1.1;">{{ number_format($resumo->peticoes) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">peticoes</div>
                </div>
            </button>

            {{-- Contratos --}}
            <button wire:click="$set('filtroTipo', 'contrato')"
                style="background:var(--white);border:1.5px solid {{ $filtroTipo === 'contrato' ? '#d97706' : 'var(--border)' }};border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;cursor:pointer;text-align:left;transition:border-color .15s;">
                <div style="width:40px;height:40px;border-radius:9px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#d97706;line-height:1.1;">{{ number_format($resumo->contratos) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">contratos</div>
                </div>
            </button>

            {{-- Sentencas --}}
            <button wire:click="$set('filtroTipo', 'sentenca')"
                style="background:var(--white);border:1.5px solid {{ $filtroTipo === 'sentenca' ? '#16a34a' : 'var(--border)' }};border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;cursor:pointer;text-align:left;transition:border-color .15s;">
                <div style="width:40px;height:40px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="3" x2="12" y2="21"/><path d="M3 6l9-3 9 3"/>
                        <path d="M3 18l4-8 4 8"/><path d="M13 18l4-8 4 8"/>
                        <line x1="2" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="22" y2="18"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#16a34a;line-height:1.1;">{{ number_format($resumo->sentencas) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">sentencas</div>
                </div>
            </button>

        </div>

        {{-- Tabela --}}
        <div class="card">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    @php
                        $thStyle = 'padding:10px 12px;text-align:left;cursor:pointer;user-select:none;white-space:nowrap;font-size:11px;text-transform:uppercase;letter-spacing:.5px;';
                        $arrow = fn($col) => $ordenarPor === $col ? ($ordenarDir === 'ASC' ? ' ▲' : ' ▼') : '';
                    @endphp
                    <tr style="background:var(--primary);color:#fff;">
                        <th style="{{ $thStyle }}">Tipo</th>
                        <th wire:click="ordenar('titulo')" style="{{ $thStyle }}">Titulo{{ $arrow('titulo') }}</th>
                        <th style="{{ $thStyle }}">Cliente</th>
                        <th style="{{ $thStyle }}">Processo</th>
                        <th wire:click="ordenar('data_documento')" style="{{ $thStyle }}text-align:center;">Data{{ $arrow('data_documento') }}</th>
                        <th style="{{ $thStyle }}text-align:center;">Arquivo</th>
                        <th wire:click="ordenar('tamanho')" style="{{ $thStyle }}text-align:center;">Tamanho{{ $arrow('tamanho') }}</th>
                        <th style="padding:10px 12px;text-align:center;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Portal</th>
                        <th style="padding:10px 12px;text-align:center;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $doc)
                    @php
                        $tipos = [
                            'peticao'          => ['label'=>'Peticao',     'color'=>'#7c3aed'],
                            'contrato'         => ['label'=>'Contrato',    'color'=>'#d97706'],
                            'procuracao'       => ['label'=>'Procuracao',  'color'=>'#2563a8'],
                            'laudo'            => ['label'=>'Laudo',       'color'=>'#0891b2'],
                            'documento_cliente'=> ['label'=>'Doc. Cliente','color'=>'#64748b'],
                            'sentenca'         => ['label'=>'Sentenca',    'color'=>'#16a34a'],
                            'outro'            => ['label'=>'Outro',       'color'=>'#94a3b8'],
                        ];
                        $t = $tipos[$doc->tipo] ?? $tipos['outro'];
                        $ext = strtolower(pathinfo($doc->arquivo_original ?? '', PATHINFO_EXTENSION));
                        $iconeArq = match($ext) {
                            'pdf'              => '<span style="background:#fee2e2;color:#dc2626;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">PDF</span>',
                            'doc','docx'       => '<span style="background:#eff6ff;color:#2563a8;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">DOC</span>',
                            'xls','xlsx'       => '<span style="background:#f0fdf4;color:#16a34a;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">XLS</span>',
                            'jpg','jpeg','png' => '<span style="background:#fdf4ff;color:#7c3aed;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">IMG</span>',
                            default            => '<span style="background:#f1f5f9;color:#64748b;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">ARQ</span>',
                        };
                        $tamanhoFormatado = $doc->tamanho
                            ? ($doc->tamanho > 1048576
                                ? number_format($doc->tamanho/1048576,1).' MB'
                                : number_format($doc->tamanho/1024,0).' KB')
                            : '—';
                        $podePreview = $doc->arquivo && in_array($ext, ['pdf','jpg','jpeg','png','gif','webp']);
                    @endphp
                    <tr style="border-bottom:1px solid var(--border);"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <td style="padding:10px 12px;">
                            <span style="background:{{ $t['color'] }}20;color:{{ $t['color'] }};padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                                {{ $t['label'] }}
                            </span>
                        </td>
                        <td style="padding:10px 12px;font-weight:600;">{{ $doc->titulo }}</td>
                        <td style="padding:10px 12px;color:var(--muted);">{{ $doc->cliente_nome ?? '—' }}</td>
                        <td style="padding:10px 12px;color:var(--muted);font-size:12px;">{{ $doc->processo_numero ?? '—' }}</td>
                        <td style="padding:10px 12px;text-align:center;color:var(--muted);">
                            {{ $doc->data_documento ? \Carbon\Carbon::parse($doc->data_documento)->format('d/m/Y') : '—' }}
                        </td>
                        <td style="padding:10px 12px;text-align:center;">{!! $iconeArq !!}</td>
                        <td style="padding:10px 12px;text-align:center;color:var(--muted);font-size:12px;">{{ $tamanhoFormatado }}</td>
                        <td style="padding:10px 12px;text-align:center;">
                            <button wire:click="togglePortalVisivel({{ $doc->id }})"
                                title="{{ $doc->portal_visivel ? 'Visivel — clique para ocultar' : 'Oculto — clique para exibir' }}"
                                style="width:30px;height:30px;border:none;border-radius:6px;background:{{ $doc->portal_visivel ? '#eff6ff' : '#f1f5f9' }};color:{{ $doc->portal_visivel ? '#2563a8' : '#94a3b8' }};cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                            </button>
                        </td>
                        <td style="padding:10px 12px;text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                @if($podePreview)
                                <button wire:click="abrirPreview({{ $doc->id }})" title="Visualizar"
                                    style="width:30px;height:30px;border:none;border-radius:6px;background:#f1f5f9;color:#64748b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                @elseif($doc->arquivo)
                                <button wire:click="downloadUrl({{ $doc->id }})" title="Download"
                                    style="width:30px;height:30px;border:none;border-radius:6px;background:#f5f3ff;color:#7c3aed;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                </button>
                                @endif
                                <button wire:click="editarDocumento({{ $doc->id }})" title="Editar"
                                    style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button wire:click="excluirDocumento({{ $doc->id }})"
                                    wire:confirm="Excluir este documento?" title="Excluir"
                                    style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="padding:40px;text-align:center;color:var(--muted);">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                                <svg aria-hidden="true" width="32" height="32" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                <span>Nenhum documento encontrado.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>{{-- /coluna direita --}}
</div>{{-- /grid --}}


{{-- Modal Documento --}}
@if($modalDocumento)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:var(--primary);">{{ $documentoId ? 'Editar' : 'Novo' }} Documento</h3>
            <button wire:click="$set('modalDocumento',false)" style="width:30px;height:30px;background:#f1f5f9;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#64748b;">
                <svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">TIPO *</label>
                    <select wire:model="tipo" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="peticao">Petição</option>
                        <option value="contrato">Contrato</option>
                        <option value="procuracao">Procuração</option>
                        <option value="laudo">Laudo/Perícia</option>
                        <option value="documento_cliente">Doc. Cliente</option>
                        <option value="sentenca">Sentença/Decisão</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">DATA DO DOCUMENTO</label>
                    <input wire:model="data_documento" type="date"
                        style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">TÍTULO *</label>
                    <input wire:model="titulo" type="text" placeholder="Ex: Petição inicial — Ação de cobrança"
                        style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                    @error('titulo') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">CLIENTE</label>
                    <select wire:model="cliente_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="">Selecione...</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">PROCESSO</label>
                    <select wire:model="processo_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="">Sem processo</option>
                        @foreach($processos as $p)
                            <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente_nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">DESCRIÇÃO</label>
                    <textarea wire:model="descricao" rows="2"
                        style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;resize:vertical;"
                        placeholder="Observações sobre o documento..."></textarea>
                </div>

                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">ARQUIVO {{ $documentoId ? '(deixe vazio para manter o atual)' : '*' }}</label>
                    <input wire:model="arquivo" type="file"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar,.txt"
                        style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;background:var(--bg);">
                    @error('arquivo') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                        Formatos aceitos: PDF, Word, Excel, Imagens, ZIP. Tamanho máximo: 20MB
                    </div>
                    @if($arquivo)
                    <div style="background:#f0f4f8;padding:8px 12px;border-radius:6px;margin-top:8px;font-size:12px;display:flex;align-items:center;gap:6px;">
                        <svg aria-hidden="true" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                        {{ $arquivo->getClientOriginalName() }} —
                        {{ number_format($arquivo->getSize()/1024, 0) }} KB
                    </div>
                    @endif
                </div>

            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="$set('modalDocumento',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarDocumento" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarDocumento" style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar</span>
                    <span wire:loading wire:target="salvarDocumento">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Preview --}}
@if($modalPreview)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:1100;display:flex;align-items:center;justify-content:center;padding:16px;"
     wire:click.self="fecharPreview">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:900px;max-height:92vh;display:flex;flex-direction:column;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <div style="font-weight:600;color:var(--primary);font-size:15px;display:flex;align-items:center;gap:6px;">
                <svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ $previewTitulo }}
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <a href="{{ $previewUrl }}" target="_blank" download
                    style="font-size:12px;color:var(--primary);text-decoration:none;padding:5px 12px;border:1px solid var(--primary);border-radius:6px;display:inline-flex;align-items:center;gap:5px;">
                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                </a>
                <button wire:click="fecharPreview"
                    style="width:30px;height:30px;background:#f1f5f9;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#64748b;">
                    <svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>
        <div style="flex:1;overflow:hidden;padding:0;">
            @php
                $isImage = str_starts_with($previewMime, 'image/');
                $isPdf   = $previewMime === 'application/pdf';
            @endphp
            @if($isPdf)
                <iframe src="{{ $previewUrl }}" style="width:100%;height:75vh;border:none;border-radius:0 0 12px 12px;"></iframe>
            @elseif($isImage)
                <div style="padding:20px;text-align:center;max-height:75vh;overflow:auto;">
                    <img src="{{ $previewUrl }}" alt="{{ $previewTitulo }}"
                        style="max-width:100%;max-height:70vh;border-radius:6px;box-shadow:0 2px 12px rgba(0,0,0,.15);">
                </div>
            @else
                <div style="padding:40px;text-align:center;color:var(--muted);">
                    <div style="display:flex;justify-content:center;margin-bottom:12px;">
                        <svg aria-hidden="true" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>Preview não disponível para este tipo de arquivo.</div>
                    <a href="{{ $previewUrl }}" target="_blank" download style="color:var(--primary);">Clique aqui para baixar</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Modal Upload em Lote --}}
@if($modalLote)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:var(--primary);display:flex;align-items:center;gap:8px;">
                <svg aria-hidden="true" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Upload em Lote
            </h3>
            <button wire:click="fecharLote" style="width:30px;height:30px;background:#f1f5f9;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#64748b;">
                <svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px;">

            {{-- Metadados compartilhados --}}
            <div style="background:#f8fafc;border-radius:8px;padding:14px;display:flex;flex-direction:column;gap:12px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Metadados (aplicados a todos os arquivos)</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);">TIPO *</label>
                        <select wire:model="loteTipo" style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;margin-top:4px;font-size:13px;">
                            <option value="peticao">Petição</option>
                            <option value="contrato">Contrato</option>
                            <option value="procuracao">Procuração</option>
                            <option value="laudo">Laudo/Perícia</option>
                            <option value="documento_cliente">Doc. Cliente</option>
                            <option value="sentenca">Sentença/Decisão</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);">DATA</label>
                        <input wire:model="loteData" type="date" style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;margin-top:4px;font-size:13px;">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);">CLIENTE</label>
                        <select wire:model="loteClienteId" style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;margin-top:4px;font-size:13px;">
                            <option value="">Nenhum</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);">PROCESSO</label>
                        <select wire:model="loteProcessoId" style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;margin-top:4px;font-size:13px;">
                            <option value="">Nenhum</option>
                            @foreach($processos as $p)
                                <option value="{{ $p->id }}">{{ $p->numero }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Seleção de arquivos --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);">ARQUIVOS *</label>
                <div style="margin-top:6px;border:2px dashed var(--border);border-radius:8px;padding:24px;text-align:center;background:#fafafa;">
                    <input wire:model="arquivosLote" type="file" multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar,.txt"
                        style="display:block;width:100%;cursor:pointer;">
                    <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                        PDF, Word, Excel, Imagens, ZIP — máx. 20 MB por arquivo
                    </div>
                </div>
                @error('arquivosLote') <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror

                {{-- Preview dos arquivos selecionados --}}
                @if(count($arquivosLote) > 0)
                <div style="margin-top:10px;display:flex;flex-direction:column;gap:4px;">
                    <div style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px;">{{ count($arquivosLote) }} arquivo(s) selecionado(s):</div>
                    @foreach($arquivosLote as $arq)
                    <div style="display:flex;align-items:center;gap:8px;padding:6px 10px;background:#f1f5f9;border-radius:6px;font-size:12px;">
                        <svg aria-hidden="true" width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $arq->getClientOriginalName() }}</span>
                        <span style="color:var(--muted);flex-shrink:0;">{{ number_format($arq->getSize()/1024, 0) }} KB</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Resultados --}}
            @if(count($loteResultados) > 0)
            <div style="border-radius:8px;overflow:hidden;border:1px solid var(--border);">
                <div style="padding:10px 14px;background:#f8fafc;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Resultado</div>
                @foreach($loteResultados as $r)
                <div style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-top:1px solid var(--border);font-size:13px;">
                    @if($r['ok'])
                        <svg aria-hidden="true" width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r['nome'] }}</span>
                        <span style="color:#16a34a;font-size:11px;font-weight:600;">Salvo</span>
                    @else
                        <svg aria-hidden="true" width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r['nome'] }}</span>
                        <span style="color:#dc2626;font-size:11px;" title="{{ $r['msg'] }}">Erro</span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

        </div>

        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:12px;justify-content:flex-end;">
            <button wire:click="fecharLote" class="btn btn-secondary">
                {{ count($loteResultados) > 0 ? 'Fechar' : 'Cancelar' }}
            </button>
            @if(count($loteResultados) === 0)
            <button wire:click="salvarLote" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvarLote" style="display:inline-flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Enviar {{ count($arquivosLote) > 0 ? count($arquivosLote).' arquivo(s)' : '' }}
                </span>
                <span wire:loading wire:target="salvarLote">Enviando...</span>
            </button>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Listener para download --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('download', ({ url }) => {
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    });
</script>

</div>

</div>
