<div>
@if(session('success'))
    <div style="background:#dcfce7;border:1px solid #16a34a;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Cards Resumo --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="border-left:4px solid var(--primary);text-align:center;">
        <div style="font-size:32px;">📁</div>
        <div style="font-size:24px;font-weight:700;color:var(--primary);">{{ $resumo->total }}</div>
        <div style="font-size:12px;color:var(--muted);">Total de documentos</div>
        <div style="font-size:11px;color:var(--muted);">
            {{ $resumo->total_tamanho ? number_format($resumo->total_tamanho/1024/1024, 1) . ' MB' : '0 MB' }}
        </div>
    </div>
    <div class="card" style="border-left:4px solid #7c3aed;text-align:center;">
        <div style="font-size:32px;">📝</div>
        <div style="font-size:24px;font-weight:700;color:#7c3aed;">{{ $resumo->peticoes }}</div>
        <div style="font-size:12px;color:var(--muted);">Petições</div>
    </div>
    <div class="card" style="border-left:4px solid var(--accent);text-align:center;">
        <div style="font-size:32px;">📜</div>
        <div style="font-size:24px;font-weight:700;color:var(--accent);">{{ $resumo->contratos }}</div>
        <div style="font-size:12px;color:var(--muted);">Contratos</div>
    </div>
    <div class="card" style="border-left:4px solid var(--success);text-align:center;">
        <div style="font-size:32px;">⚖️</div>
        <div style="font-size:24px;font-weight:700;color:var(--success);">{{ $resumo->sentencas }}</div>
        <div style="font-size:12px;color:var(--muted);">Sentenças</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input wire:model.live="busca" type="text" placeholder="🔍 Buscar por título, cliente ou processo..."
            style="flex:1;min-width:200px;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
        <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos os tipos</option>
            <option value="peticao">Petição</option>
            <option value="contrato">Contrato</option>
            <option value="procuracao">Procuração</option>
            <option value="laudo">Laudo/Perícia</option>
            <option value="documento_cliente">Doc. Cliente</option>
            <option value="sentenca">Sentença/Decisão</option>
            <option value="outro">Outro</option>
        </select>
        <select wire:model.live="filtroVinculo" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos</option>
            <option value="processo">Por processo</option>
            <option value="cliente">Por cliente</option>
        </select>
        <button wire:click="novoDocumento" class="btn btn-primary">+ Novo Documento</button>
    </div>
</div>

{{-- Lista de documentos --}}
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:var(--primary);color:#fff;">
                <th style="padding:10px 12px;text-align:left;">Tipo</th>
                <th style="padding:10px 12px;text-align:left;">Título</th>
                <th style="padding:10px 12px;text-align:left;">Cliente</th>
                <th style="padding:10px 12px;text-align:left;">Processo</th>
                <th style="padding:10px 12px;text-align:center;">Data</th>
                <th style="padding:10px 12px;text-align:center;">Arquivo</th>
                <th style="padding:10px 12px;text-align:center;">Tamanho</th>
                <th style="padding:10px 12px;text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documentos as $doc)
            @php
                $tipos = [
                    'peticao'          => ['label'=>'Petição',    'icon'=>'📝', 'color'=>'#7c3aed'],
                    'contrato'         => ['label'=>'Contrato',   'icon'=>'📜', 'color'=>'#d97706'],
                    'procuracao'       => ['label'=>'Procuração', 'icon'=>'✍️', 'color'=>'#2563a8'],
                    'laudo'            => ['label'=>'Laudo',      'icon'=>'🔬', 'color'=>'#0891b2'],
                    'documento_cliente'=> ['label'=>'Doc. Cliente','icon'=>'👤','color'=>'#64748b'],
                    'sentenca'         => ['label'=>'Sentença',   'icon'=>'⚖️', 'color'=>'#16a34a'],
                    'outro'            => ['label'=>'Outro',      'icon'=>'📄', 'color'=>'#94a3b8'],
                ];
                $t = $tipos[$doc->tipo] ?? $tipos['outro'];
                $ext = strtolower(pathinfo($doc->arquivo_original ?? '', PATHINFO_EXTENSION));
                $iconeArq = match($ext) {
                    'pdf'           => '🔴',
                    'doc','docx'    => '🔵',
                    'xls','xlsx'    => '🟢',
                    'jpg','jpeg','png' => '🖼️',
                    default         => '📄',
                };
                $tamanhoFormatado = $doc->tamanho
                    ? ($doc->tamanho > 1048576
                        ? number_format($doc->tamanho/1048576,1).' MB'
                        : number_format($doc->tamanho/1024,0).' KB')
                    : '—';
            @endphp
            <tr style="border-bottom:1px solid var(--border);"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:10px 12px;">
                    <span style="background:{{ $t['color'] }}20;color:{{ $t['color'] }};padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                        {{ $t['icon'] }} {{ $t['label'] }}
                    </span>
                </td>
                <td style="padding:10px 12px;font-weight:600;">{{ $doc->titulo }}</td>
                <td style="padding:10px 12px;color:var(--muted);">{{ $doc->cliente_nome ?? '—' }}</td>
                <td style="padding:10px 12px;color:var(--muted);font-size:12px;">{{ $doc->processo_numero ?? '—' }}</td>
                <td style="padding:10px 12px;text-align:center;color:var(--muted);">
                    {{ $doc->data_documento ? \Carbon\Carbon::parse($doc->data_documento)->format('d/m/Y') : '—' }}
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    {{ $iconeArq }} {{ strtoupper($ext) }}
                </td>
                <td style="padding:10px 12px;text-align:center;color:var(--muted);font-size:12px;">{{ $tamanhoFormatado }}</td>
                <td style="padding:10px 12px;text-align:center;">
                    <div style="display:flex;gap:6px;justify-content:center;">
                        @if($doc->arquivo)
                        <a href="{{ Storage::url($doc->arquivo) }}" target="_blank"
                            title="Visualizar/Download"
                            style="background:none;border:none;cursor:pointer;font-size:16px;text-decoration:none;">⬇️</a>
                        @endif
                        <button wire:click="editarDocumento({{ $doc->id }})" title="Editar"
                            style="background:none;border:none;cursor:pointer;font-size:16px;">✏️</button>
                        <button wire:click="excluirDocumento({{ $doc->id }})"
                            onclick="return confirm('Excluir este documento?')" title="Excluir"
                            style="background:none;border:none;cursor:pointer;font-size:16px;">🗑️</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:40px;text-align:center;color:var(--muted);">
                    📁 Nenhum documento cadastrado. Clique em "+ Novo Documento" para começar.
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
            <button wire:click="$set('modalDocumento',false)" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">TIPO *</label>
                    <select wire:model="tipo" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="peticao">📝 Petição</option>
                        <option value="contrato">📜 Contrato</option>
                        <option value="procuracao">✍️ Procuração</option>
                        <option value="laudo">🔬 Laudo/Perícia</option>
                        <option value="documento_cliente">👤 Doc. Cliente</option>
                        <option value="sentenca">⚖️ Sentença/Decisão</option>
                        <option value="outro">📄 Outro</option>
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
                    <div style="background:#f0f4f8;padding:8px 12px;border-radius:6px;margin-top:8px;font-size:12px;">
                        📎 {{ $arquivo->getClientOriginalName() }} —
                        {{ number_format($arquivo->getSize()/1024, 0) }} KB
                    </div>
                    @endif
                </div>

            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="$set('modalDocumento',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarDocumento" class="btn btn-primary">💾 Salvar</button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
