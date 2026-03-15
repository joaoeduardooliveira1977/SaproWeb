<div>
    {{-- Cabecalho (oculto quando embutido em abas) --}}
    @if(!$embed)
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">Andamentos</h2>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">
                Processo: <strong>{{ $processo->numero }}</strong> &mdash; {{ $processo->cliente->nome ?? '&mdash;' }}
            </p>
        </div>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('processos.show', $processo->id) }}"
                style="padding:10px 20px; background:#f1f5f9; color:#334155; border-radius:8px; font-size:13px; text-decoration:none;">
                &larr; Voltar
            </a>
            @if(!$mostrarFormulario)
            <button wire:click="novoAndamento"
                style="padding:10px 20px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                + Novo Andamento
            </button>
            @endif
        </div>
    </div>
    @else
    {{-- Botao inline quando embutido --}}
    @if(!$mostrarFormulario)
    <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
        <button wire:click="novoAndamento"
            style="padding:8px 16px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
            + Novo Andamento
        </button>
    </div>
    @endif
    @endif

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
    <div style="background:#dcfce7; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#16a34a;">
        &#10003; {{ session('sucesso') }}
    </div>
    @endif

    {{-- Formulario --}}
    @if($mostrarFormulario)
    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:24px; border-left:4px solid #2563a8;">
        <h3 style="font-size:16px; font-weight:700; color:#1a3a5c; margin-bottom:20px;">
            {{ $editandoId ? 'Editar Andamento' : 'Novo Andamento' }}
        </h3>

        <div style="display:grid; grid-template-columns:200px 1fr; gap:16px; align-items:start;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Data *</label>
                <input wire:model="data" type="date"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                @error('data') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Descricao *</label>
                <textarea wire:model="descricao" rows="3"
                    placeholder="Descreva o andamento do processo..."
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none; resize:vertical;"></textarea>
                @error('descricao') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Campo de anexo opcional --}}
        <div style="margin-top:16px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                &#128206; Anexar Arquivo <span style="font-weight:400; color:#94a3b8;">(opcional, máx. 20MB)</span>
            </label>
            <input wire:model="arquivo" type="file"
                style="width:100%; padding:8px; border:1.5px dashed #e2e8f0; border-radius:8px; font-size:13px; background:#f8fafc; cursor:pointer;">
            @error('arquivo') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            <div wire:loading wire:target="arquivo" style="font-size:12px; color:#2563a8; margin-top:4px;">
                Carregando arquivo...
            </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:12px; justify-content:flex-end;">
            <button wire:click="cancelar"
                style="padding:10px 20px; background:#f1f5f9; color:#334155; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
                Cancelar
            </button>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:10px 20px; background:#16a34a; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                <span wire:loading.remove wire:target="salvar">Salvar</span>
                <span wire:loading wire:target="salvar">Salvando...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Confirmacao de exclusao --}}
    @if($excluindoId)
    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:20px; margin-bottom:24px;">
        <p style="font-size:14px; color:#dc2626; font-weight:600; margin-bottom:12px;">
            Tem certeza que deseja excluir este andamento? Os arquivos anexados também serão removidos.
        </p>
        <div style="display:flex; gap:12px;">
            <button wire:click="cancelar"
                style="padding:8px 20px; background:#f1f5f9; color:#334155; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
                Cancelar
            </button>
            <button wire:click="excluir"
                style="padding:8px 20px; background:#dc2626; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                Excluir
            </button>
        </div>
    </div>
    @endif

    {{-- Modal de upload para andamento existente --}}
    @if($mostrarUploadModal)
    <div style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; display:flex; align-items:center; justify-content:center;">
        <div style="background:white; border-radius:12px; padding:28px; width:100%; max-width:480px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <h3 style="font-size:16px; font-weight:700; color:#1a3a5c; margin-bottom:20px;">
                &#128206; Anexar Arquivo ao Andamento
            </h3>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                    Arquivo <span style="font-weight:400; color:#94a3b8;">(máx. 20MB)</span>
                </label>
                <input wire:model="arquivoUpload" type="file"
                    style="width:100%; padding:8px; border:1.5px dashed #e2e8f0; border-radius:8px; font-size:13px; background:#f8fafc; cursor:pointer;">
                @error('arquivoUpload') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
                <div wire:loading wire:target="arquivoUpload" style="font-size:12px; color:#2563a8; margin-top:4px;">
                    Carregando arquivo...
                </div>
            </div>
            <div style="margin-top:20px; display:flex; gap:12px; justify-content:flex-end;">
                <button wire:click="fecharUploadModal"
                    style="padding:10px 20px; background:#f1f5f9; color:#334155; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
                    Cancelar
                </button>
                <button wire:click="salvarUploadAndamento" wire:loading.attr="disabled" wire:target="salvarUploadAndamento"
                    style="padding:10px 20px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                    <span wire:loading.remove wire:target="salvarUploadAndamento">&#128190; Salvar</span>
                    <span wire:loading wire:target="salvarUploadAndamento">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Lista de andamentos --}}
    <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); overflow:hidden;">
        @if($andamentos->isEmpty())
        <div style="padding:60px; text-align:center; color:#94a3b8;">
            <div style="font-size:40px; margin-bottom:12px;">&#128203;</div>
            <p style="font-size:15px; font-weight:600;">Nenhum andamento cadastrado</p>
            <p style="font-size:13px; margin-top:4px;">Clique em "Novo Andamento" para adicionar</p>
        </div>
        @else
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#64748b; width:120px;">DATA</th>
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#64748b;">DESCRICAO</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#64748b; width:130px;">ACOES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($andamentos as $andamento)
                @php
                    $docsAndamento = $docsPorAndamento->get($andamento->id, collect());
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9; {{ $loop->even ? 'background:#fafafa;' : '' }}">
                    <td style="padding:12px 16px; font-size:13px; color:#334155; font-weight:600; white-space:nowrap;">
                        {{ $andamento->data->format('d/m/Y') }}
                    </td>
                    <td style="padding:12px 16px; font-size:13px; color:#475569; line-height:1.5;">
                        {{ $andamento->descricao }}

                        {{-- Documentos vinculados a este andamento --}}
                        @if($docsAndamento->isNotEmpty())
                        <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;">
                            @foreach($docsAndamento as $doc)
                            <div style="display:inline-flex; align-items:center; gap:4px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:6px; padding:3px 8px; font-size:11px; color:#0369a1;">
                                <a href="{{ Storage::url($doc->arquivo) }}" target="_blank"
                                    style="display:inline-flex; align-items:center; gap:4px; color:#0369a1; text-decoration:none; font-weight:500;"
                                    title="{{ $doc->arquivo_original }}">
                                    &#128206; {{ Str::limit($doc->arquivo_original, 30) }}
                                </a>
                                <button wire:click="excluirDocumento({{ $doc->id }})"
                                    title="Remover arquivo"
                                    style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:12px; padding:0 2px; line-height:1;">
                                    &times;
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </td>
                    <td style="padding:12px 16px; text-align:center;">
                        <div style="display:flex; gap:6px; justify-content:center; align-items:center;">
                            {{-- Ícone de clipe se tem documento --}}
                            @if($docsAndamento->isNotEmpty())
                            <span title="{{ $docsAndamento->count() }} arquivo(s)" style="font-size:16px; color:#0369a1;">
                                &#128206;
                            </span>
                            @endif
                            {{-- Botão upload --}}
                            <button wire:click="abrirUploadModal({{ $andamento->id }})" title="Anexar arquivo"
                                style="padding:6px 8px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; border-radius:6px; cursor:pointer; font-size:13px;">
                                &#128190;
                            </button>
                            <button wire:click="editar({{ $andamento->id }})" title="Editar"
                                style="padding:6px 10px; background:#e0f2fe; color:#0369a1; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                                &#9999;
                            </button>
                            <button wire:click="confirmarExclusao({{ $andamento->id }})" title="Excluir"
                                style="padding:6px 10px; background:#fee2e2; color:#dc2626; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                                &#128465;
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:12px 16px; font-size:13px; color:#64748b; border-top:1px solid #f1f5f9;">
            Total: {{ $andamentos->count() }} andamento(s)
        </div>
        @endif
    </div>
</div>
