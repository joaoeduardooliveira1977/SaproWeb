<div>
    {{-- Cabecalho (oculto quando embutido em abas) --}}
    @if(!$embed)
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:10px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--primary);">Andamentos</h2>
            <p style="font-size:13px;color:var(--muted);margin-top:4px;">
                Processo: <strong>{{ $processo->numero }}</strong> &mdash; {{ $processo->cliente->nome ?? '&mdash;' }}
            </p>
        </div>
        <div class="card-actions">
            <a href="{{ route('processos.show', $processo->id) }}" class="btn btn-secondary btn-sm">&larr; Voltar</a>
            @if(!$mostrarFormulario)
            <button wire:click="sugerirProximoPasso" wire:loading.attr="disabled"
                style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:8px;color:#1d4ed8;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s;"
                onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                <svg wire:loading.remove wire:target="sugerirProximoPasso" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                </svg>
                <svg wire:loading wire:target="sugerirProximoPasso" style="animation:spin .7s linear infinite;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <span wire:loading.remove wire:target="sugerirProximoPasso">✨ Próximo Passo</span>
                <span wire:loading wire:target="sugerirProximoPasso">Analisando...</span>
            </button>
            <button wire:click="novoAndamento" class="btn btn-primary btn-sm">+ Novo Andamento</button>
            @endif
        </div>
    </div>
    @else
    {{-- Botao inline quando embutido --}}
    @if(!$mostrarFormulario)
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:16px;">
        <button wire:click="sugerirProximoPasso" wire:loading.attr="disabled"
            style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:8px;color:#1d4ed8;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s;"
            onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
            <svg wire:loading.remove wire:target="sugerirProximoPasso" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            </svg>
            <svg wire:loading wire:target="sugerirProximoPasso" style="animation:spin .7s linear infinite;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            <span wire:loading.remove wire:target="sugerirProximoPasso">✨ Próximo Passo</span>
            <span wire:loading wire:target="sugerirProximoPasso">Analisando...</span>
        </button>
        <button wire:click="novoAndamento" class="btn btn-primary btn-sm">+ Novo Andamento</button>
    </div>
    @endif
    @endif

    {{-- ── Sugestão IA de Próximo Passo ── --}}
    @if($mostrarSugestaoIA && $sugestaoIA)
    <div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:12px;padding:20px;margin-bottom:16px;color:#fff;position:relative;">
        <button wire:click="fecharSugestaoIA"
            style="position:absolute;top:12px;right:12px;background:rgba(255,255,255,.1);border:none;border-radius:6px;width:28px;height:28px;cursor:pointer;color:#93c5fd;display:flex;align-items:center;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#fff;">Sugestão de Próximo Passo — IA</div>
                <div style="font-size:11px;color:#93c5fd;">Baseado no histórico de andamentos do processo</div>
            </div>
        </div>
        <div style="background:rgba(255,255,255,.08);border-radius:8px;padding:14px 16px;font-size:13px;color:#e2e8f0;line-height:1.7;white-space:pre-line;">{{ $sugestaoIA }}</div>
        <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;">
            <button wire:click="novoAndamento"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#2563a8;border:none;border-radius:8px;color:#fff;font-size:12px;font-weight:600;cursor:pointer;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Registrar Andamento
            </button>
            <a href="{{ route('prazos') }}"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;color:#93c5fd;font-size:12px;font-weight:600;text-decoration:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Criar Prazo
            </a>
            <button wire:click="fecharSugestaoIA"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:transparent;border:1px solid rgba(255,255,255,.2);border-radius:8px;color:#94a3b8;font-size:12px;cursor:pointer;">
                Dispensar
            </button>
        </div>
    </div>
    @endif

    {{-- Sugestão de prazo automático --}}
    @if($sugestaoAutoPrazo)
    <div style="display:flex;gap:12px;padding:14px 16px;background:#eff6ff;border:1.5px solid #93c5fd;border-radius:10px;margin-bottom:16px;align-items:flex-start;">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:700;color:#1d4ed8;">Prazo detectado — deseja criar automaticamente?</div>
            <div style="font-size:12px;color:#1e40af;margin-top:2px;"><strong>{{ $sugestaoTitulo }}</strong> — vencimento sugerido: <strong>{{ $sugestaoData }}</strong></div>
            <div style="font-size:11px;color:#3b82f6;margin-top:2px;">{{ $sugestaoDescricao }}</div>
            <div style="display:flex;gap:8px;margin-top:10px;">
                <a href="{{ route('prazos') }}" class="btn btn-primary btn-sm" style="font-size:12px;">
                    Ir para Prazos e criar
                </a>
                <button wire:click="descartarSugestaoPrazo" class="btn btn-outline btn-sm" style="font-size:12px;">
                    Dispensar
                </button>
            </div>
        </div>
        <button wire:click="descartarSugestaoPrazo" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;align-items:center;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    @endif

    {{-- Formulario --}}
    @if($mostrarFormulario)
    <div class="card" style="margin-bottom:24px;border-left:4px solid var(--primary-light);">
        <h3 style="font-size:16px;font-weight:700;color:var(--primary);margin-bottom:20px;">
            {{ $editandoId ? 'Editar Andamento' : 'Novo Andamento' }}
        </h3>

        <div class="form-grid" style="align-items:start;">
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
                <span style="display:inline-flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    Anexar Arquivo <span style="font-weight:400; color:#94a3b8;">(opcional, máx. 20MB)</span>
                </span>
            </label>
            <input wire:model="arquivo" type="file"
                style="width:100%; padding:8px; border:1.5px dashed #e2e8f0; border-radius:8px; font-size:13px; background:#f8fafc; cursor:pointer;">
            @error('arquivo') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            <div wire:loading wire:target="arquivo" style="font-size:12px; color:#2563a8; margin-top:4px;">
                Carregando arquivo...
            </div>
        </div>

        <div class="modal-footer" style="margin-top:16px;">
            <button wire:click="cancelar" class="btn btn-secondary">Cancelar</button>
            <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-success">
                <span wire:loading.remove wire:target="salvar">Salvar</span>
                <span wire:loading wire:target="salvar">Salvando...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Confirmacao de exclusao --}}
    @if($excluindoId)
    <div class="alert alert-error" style="border-radius:12px;padding:20px;margin-bottom:24px;">
        <p style="font-weight:600;margin-bottom:12px;">
            Tem certeza que deseja excluir este andamento? Os arquivos anexados também serão removidos.
        </p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button wire:click="cancelar" class="btn btn-secondary">Cancelar</button>
            <button wire:click="excluir" class="btn btn-danger">
                Excluir
            </button>
        </div>
    </div>
    @endif

    {{-- Modal de upload para andamento existente --}}
    @if($mostrarUploadModal)
    <div class="modal-backdrop">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <span class="modal-title">
                    <span style="display:inline-flex;align-items:center;gap:6px;">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                        Anexar Arquivo ao Andamento
                    </span>
                </span>
                <button wire:click="fecharUploadModal" style="background:none;border:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="form-field" style="margin-bottom:16px;">
                <label class="lbl">Arquivo <span style="font-weight:400;color:var(--muted)">(máx. 20MB)</span></label>
                <input wire:model="arquivoUpload" type="file">
                @error('arquivoUpload') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div wire:loading wire:target="arquivoUpload" style="font-size:12px;color:var(--primary-light);margin-top:4px;">Carregando arquivo...</div>
            </div>
            <div class="modal-footer">
                <button wire:click="fecharUploadModal" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvarUploadAndamento" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="salvarUploadAndamento">
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Salvar
                        </span>
                    </span>
                    <span wire:loading wire:target="salvarUploadAndamento">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Lista de andamentos --}}
    <div class="card" style="padding:0;overflow:hidden;">
        @if($andamentos->isEmpty())
        <div style="padding:60px; text-align:center; color:#94a3b8;">
            <div style="margin-bottom:12px;">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </div>
            <p style="font-size:15px; font-weight:600;">Nenhum andamento cadastrado</p>
            <p style="font-size:13px; margin-top:4px;">Clique em "Novo Andamento" para adicionar</p>
        </div>
        @else
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                    <th style="padding:12px 16px; text-align:left; font-size:11px; text-transform:uppercase; color:var(--muted); letter-spacing:.5px; width:120px;">DATA</th>
                    <th style="padding:12px 16px; text-align:left; font-size:11px; text-transform:uppercase; color:var(--muted); letter-spacing:.5px;">DESCRICAO</th>
                    <th style="padding:12px 16px; text-align:center; font-size:11px; text-transform:uppercase; color:var(--muted); letter-spacing:.5px; width:130px;">ACOES</th>
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
                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                    {{ Str::limit($doc->arquivo_original, 30) }}
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
                            <span title="{{ $docsAndamento->count() }} arquivo(s)" style="color:#0369a1;display:inline-flex;align-items:center;">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                            </span>
                            @endif
                            {{-- Botão upload --}}
                            <button wire:click="abrirUploadModal({{ $andamento->id }})" title="Anexar arquivo"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#eff6ff;color:#0369a1;">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </button>
                            <button wire:click="editar({{ $andamento->id }})" title="Editar"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#e0f2fe;color:#0369a1;">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="confirmarExclusao({{ $andamento->id }})" title="Excluir"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
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
