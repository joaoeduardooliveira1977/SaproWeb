<div>

{{-- Cards Resumo --}}
<div class="stat-grid">
    <div class="card" style="border-left:4px solid var(--primary);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="24" height="24" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
        </div>
        <div style="font-size:24px;font-weight:700;color:var(--primary);">{{ $resumo->total }}</div>
        <div style="font-size:12px;color:var(--muted);">Total de documentos</div>
        <div style="font-size:11px;color:var(--muted);">
            {{ $resumo->total_tamanho ? number_format($resumo->total_tamanho/1024/1024, 1) . ' MB' : '0 MB' }}
        </div>
    </div>
    <div class="card" style="border-left:4px solid #7c3aed;text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="24" height="24" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <div style="font-size:24px;font-weight:700;color:#7c3aed;">{{ $resumo->peticoes }}</div>
        <div style="font-size:12px;color:var(--muted);">Petições</div>
    </div>
    <div class="card" style="border-left:4px solid var(--accent);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="24" height="24" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div style="font-size:24px;font-weight:700;color:var(--accent);">{{ $resumo->contratos }}</div>
        <div style="font-size:12px;color:var(--muted);">Contratos</div>
    </div>
    <div class="card" style="border-left:4px solid var(--success);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="24" height="24" fill="none" stroke="var(--success)" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="3" x2="12" y2="21"/><path d="M3 6l9-3 9 3"/><path d="M3 18l4-8 4 8"/><path d="M13 18l4-8 4 8"/><line x1="2" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="22" y2="18"/></svg>
        </div>
        <div style="font-size:24px;font-weight:700;color:var(--success);">{{ $resumo->sentencas }}</div>
        <div style="font-size:12px;color:var(--muted);">Sentenças</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:16px;">
    <div class="filter-bar">
        <div style="position:relative;flex:1;min-width:200px;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por título, cliente ou processo..." style="padding-left:34px;width:100%;">
        </div>
        <select wire:model.live="filtroTipo">
            <option value="">Todos os tipos</option>
            <option value="peticao">Petição</option>
            <option value="contrato">Contrato</option>
            <option value="procuracao">Procuração</option>
            <option value="laudo">Laudo/Perícia</option>
            <option value="documento_cliente">Doc. Cliente</option>
            <option value="sentenca">Sentença/Decisão</option>
            <option value="outro">Outro</option>
        </select>
        <select wire:model.live="filtroVinculo">
            <option value="">Todos</option>
            <option value="processo">Por processo</option>
            <option value="cliente">Por cliente</option>
        </select>
        <input wire:model.live="filtroDataIni" type="date" title="Data início">
        <input wire:model.live="filtroDataFim" type="date" title="Data fim">
        <button wire:click="exportarCsv" class="btn btn-secondary-outline btn-sm" title="Exportar CSV" wire:loading.attr="disabled" style="flex-shrink:0">
            <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:4px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> CSV</span>
            <span wire:loading wire:target="exportarCsv">...</span>
        </button>
        <button wire:click="novoDocumento" class="btn btn-primary btn-sm" style="flex-shrink:0">+ Novo</button>
    </div>
</div>

{{-- Lista de documentos --}}
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            @php
                $thStyle = 'padding:10px 12px;text-align:left;cursor:pointer;user-select:none;white-space:nowrap;font-size:11px;text-transform:uppercase;letter-spacing:.5px;';
                $arrow = fn($col) => $ordenarPor === $col ? ($ordenarDir === 'ASC' ? ' ▲' : ' ▼') : '';
            @endphp
            <tr style="background:var(--primary);color:#fff;">
                <th style="{{ $thStyle }}">Tipo</th>
                <th wire:click="ordenar('titulo')" style="{{ $thStyle }}">Título{{ $arrow('titulo') }}</th>
                <th style="{{ $thStyle }}">Cliente</th>
                <th style="{{ $thStyle }}">Processo</th>
                <th wire:click="ordenar('data_documento')" style="{{ $thStyle }}text-align:center;">Data{{ $arrow('data_documento') }}</th>
                <th style="{{ $thStyle }}text-align:center;">Arquivo</th>
                <th wire:click="ordenar('tamanho')" style="{{ $thStyle }}text-align:center;">Tamanho{{ $arrow('tamanho') }}</th>
                <th style="padding:10px 12px;text-align:center;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Portal</th>
                <th style="padding:10px 12px;text-align:center;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documentos as $doc)
            @php
                $tipos = [
                    'peticao'          => ['label'=>'Petição',     'color'=>'#7c3aed'],
                    'contrato'         => ['label'=>'Contrato',    'color'=>'#d97706'],
                    'procuracao'       => ['label'=>'Procuração',  'color'=>'#2563a8'],
                    'laudo'            => ['label'=>'Laudo',       'color'=>'#0891b2'],
                    'documento_cliente'=> ['label'=>'Doc. Cliente','color'=>'#64748b'],
                    'sentenca'         => ['label'=>'Sentença',    'color'=>'#16a34a'],
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
                <td style="padding:10px 12px;text-align:center;">
                    {!! $iconeArq !!}
                </td>
                <td style="padding:10px 12px;text-align:center;color:var(--muted);font-size:12px;">{{ $tamanhoFormatado }}</td>
                <td style="padding:10px 12px;text-align:center;">
                    <button wire:click="togglePortalVisivel({{ $doc->id }})" title="{{ $doc->portal_visivel ? 'Visível no portal — clique para ocultar' : 'Oculto no portal — clique para exibir' }}"
                        style="width:30px;height:30px;border:none;border-radius:6px;background:{{ $doc->portal_visivel ? '#eff6ff' : '#f1f5f9' }};color:{{ $doc->portal_visivel ? '#2563a8' : '#94a3b8' }};cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    </button>
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <div style="display:flex;gap:6px;justify-content:center;">
                        @if($podePreview)
                        <button wire:click="abrirPreview({{ $doc->id }})" title="Visualizar"
                            style="width:30px;height:30px;border:none;border-radius:6px;background:#f1f5f9;color:#64748b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        @elseif($doc->arquivo)
                        <button wire:click="downloadUrl({{ $doc->id }})" title="Download"
                            style="width:30px;height:30px;border:none;border-radius:6px;background:#f5f3ff;color:#7c3aed;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </button>
                        @endif
                        <button wire:click="editarDocumento({{ $doc->id }})" title="Editar"
                            style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button wire:click="excluirDocumento({{ $doc->id }})"
                            wire:confirm="Excluir este documento?" title="Excluir"
                            style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding:40px;text-align:center;color:var(--muted);">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                        <svg width="32" height="32" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                        <span>Nenhum documento cadastrado. Clique em "+ Novo Documento" para começar.</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Documento --}}
@if($modalDocumento)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:var(--primary);">{{ $documentoId ? 'Editar' : 'Novo' }} Documento</h3>
            <button wire:click="$set('modalDocumento',false)" style="width:30px;height:30px;background:#f1f5f9;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#64748b;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                        {{ $arquivo->getClientOriginalName() }} —
                        {{ number_format($arquivo->getSize()/1024, 0) }} KB
                    </div>
                    @endif
                </div>

            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="$set('modalDocumento',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarDocumento" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarDocumento" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar</span>
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
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ $previewTitulo }}
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <a href="{{ $previewUrl }}" target="_blank" download
                    style="font-size:12px;color:var(--primary);text-decoration:none;padding:5px 12px;border:1px solid var(--primary);border-radius:6px;display:inline-flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                </a>
                <button wire:click="fecharPreview"
                    style="width:30px;height:30px;background:#f1f5f9;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#64748b;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>Preview não disponível para este tipo de arquivo.</div>
                    <a href="{{ $previewUrl }}" target="_blank" download style="color:var(--primary);">Clique aqui para baixar</a>
                </div>
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
