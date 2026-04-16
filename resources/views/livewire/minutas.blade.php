<div>

    {{-- ── Header ── --}}
    <div class="filter-bar" style="margin-bottom:20px;">
        <div style="position:relative;flex:1;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;display:flex;align-items:center;">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar template..." style="padding-left:34px;width:100%;">
        </div>
        @if(!$mostrarForm)
        <button wire:click="novo" class="btn btn-primary btn-sm" style="flex-shrink:0;margin-left:auto;">+ Novo Template</button>
        @endif
    </div>

    {{-- ── Formulário ── --}}
    @if($mostrarForm)
    <div class="card" style="margin-bottom:20px;border:1px solid #bfdbfe;background:#f0f7ff;">
        <div class="card-header">
            <span class="card-title">{!! $editandoId
                ? '<span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Editar Template</span>'
                : '<span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Novo Template</span>'
            !!}</span>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;padding:4px 0;">

            <div class="form-grid" style="grid-template-columns:1fr auto;align-items:start;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Título *</label>
                    <input type="text" wire:model="titulo" placeholder="Nome do template"
                        style="width:100%;padding:8px 10px;border:1px solid {{ $errors->has('titulo') ? '#dc2626' : 'var(--border)' }};border-radius:6px;font-size:13px;">
                    @error('titulo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Categoria</label>
                    <select wire:model="categoria"
                        style="padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                        @foreach($categorias as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Corpo --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">
                    Corpo do Template *
                    <span style="font-weight:400;color:#64748b;">— use os placeholders abaixo para inserir dados do processo</span>
                </label>
                <textarea wire:model="corpo" rows="12" placeholder="Digite o texto da minuta aqui. Use @{{cliente_nome}}, @{{processo_numero}}, etc."
                    style="width:100%;padding:10px;border:1px solid {{ $errors->has('corpo') ? '#dc2626' : 'var(--border)' }};border-radius:6px;font-size:13px;font-family:monospace;resize:vertical;"></textarea>
                @error('corpo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            {{-- Placeholders de referência — inserção direta no cursor --}}
            <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-top:8px;">
                <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Clique para inserir no texto
                </div>

                @php
                $grupos = [
                    [
                        'label' => '📄 Processo',
                        'cor'   => '#2563eb',
                        'bg'    => '#eff6ff',
                        'border'=> '#bfdbfe',
                        'itens' => [
                            'Número'             => '{{processo_numero}}',
                            'Vara'               => '{{processo_vara}}',
                            'Data Distribuição'  => '{{processo_data_distribuicao}}',
                            'Tipo de Ação'       => '{{processo_tipo_acao}}',
                            'Fase'               => '{{processo_fase}}',
                            'Valor da Causa'     => '{{processo_valor_causa}}',
                            'Parte Contrária'    => '{{parte_contraria}}',
                        ],
                    ],
                    [
                        'label' => '👤 Cliente',
                        'cor'   => '#16a34a',
                        'bg'    => '#f0fdf4',
                        'border'=> '#bbf7d0',
                        'itens' => [
                            'Nome'      => '{{cliente_nome}}',
                            'CPF/CNPJ'  => '{{cliente_cpf_cnpj}}',
                            'RG'        => '{{cliente_rg}}',
                            'E-mail'    => '{{cliente_email}}',
                            'Telefone'  => '{{cliente_telefone}}',
                            'Celular'   => '{{cliente_celular}}',
                            'Endereço'  => '{{cliente_endereco}}',
                            'Cidade'    => '{{cliente_cidade}}',
                            'Estado'    => '{{cliente_estado}}',
                        ],
                    ],
                    [
                        'label' => '⚖️ Advogado',
                        'cor'   => '#7c3aed',
                        'bg'    => '#f5f3ff',
                        'border'=> '#ddd6fe',
                        'itens' => [
                            'Nome do Advogado' => '{{advogado_nome}}',
                            'OAB'              => '{{advogado_oab}}',
                            'Nome do Juiz'     => '{{juiz_nome}}',
                        ],
                    ],
                    [
                        'label' => '📅 Datas',
                        'cor'   => '#d97706',
                        'bg'    => '#fffbeb',
                        'border'=> '#fde68a',
                        'itens' => [
                            'Data Atual'       => '{{data_atual}}',
                            'Data Atual Curta' => '{{data_atual_curta}}',
                        ],
                    ],
                ];
                @endphp

                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($grupos as $grupo)
                    <div>
                        <div style="font-size:11px;font-weight:700;color:{{ $grupo['cor'] }};margin-bottom:6px;">
                            {{ $grupo['label'] }}
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:5px;">
                            @foreach($grupo['itens'] as $label => $placeholder)
                            <button
                                type="button"
                                onclick="minutasInserirPlaceholder('{{ $placeholder }}')"
                                style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:{{ $grupo['bg'] }};border:1px solid {{ $grupo['border'] }};border-radius:99px;font-size:12px;font-weight:500;color:{{ $grupo['cor'] }};cursor:pointer;transition:all .15s;"
                                onmouseover="this.style.opacity='.7'"
                                onmouseout="this.style.opacity='1'"
                                title="Inserir: {{ $placeholder }}">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:16px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" wire:model="ativo"> Ativo
                </label>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <button wire:click="salvar" class="btn btn-primary btn-sm">Salvar</button>
                    <button wire:click="cancelar" class="btn btn-secondary btn-sm">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Script de inserção de placeholder no cursor --}}
    <script>
    function minutasInserirPlaceholder(placeholder) {
        const textarea = document.querySelector('textarea[wire\\:model="corpo"]')
                      || document.querySelector('textarea[wire\\:model\\.live="corpo"]')
                      || document.querySelector('textarea');
        if (!textarea) return;
        textarea.focus();
        const start = textarea.selectionStart;
        const end   = textarea.selectionEnd;
        textarea.value = textarea.value.substring(0, start) + placeholder + textarea.value.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + placeholder.length;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        textarea.dispatchEvent(new Event('change', { bubbles: true }));
    }
    </script>

    {{-- ── Lista ── --}}
    @if($minutas->isEmpty())
    <div class="card">
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
            Nenhum template cadastrado ainda. Clique em "+ Novo Template" para começar.
        </p>
    </div>
    @else
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Título</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Categoria</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;text-align:center;">Status</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;text-align:center;">Atualizado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($minutas as $m)
                    <tr style="{{ !$m->ativo ? 'opacity:.5;' : '' }}">
                        <td style="font-weight:600;">{{ $m->titulo }}</td>
                        <td>
                            <span class="badge" style="background:#2563a822;color:#2563a8;">
                                {{ $categorias[$m->categoria] ?? $m->categoria }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <button wire:click="toggleAtivo({{ $m->id }})"
                                style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;border:none;cursor:pointer;
                                       background:{{ $m->ativo ? '#dcfce7' : '#f1f5f9' }};color:{{ $m->ativo ? '#16a34a' : '#64748b' }};">
                                {{ $m->ativo ? 'Ativo' : 'Inativo' }}
                            </button>
                        </td>
                        <td style="text-align:center;color:var(--muted);font-size:12px;">
                            {{ $m->updated_at->format('d/m/Y') }}
                        </td>
                        <td style="text-align:right;">
                            <button wire:click="editar({{ $m->id }})"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#e0f2fe;color:#0369a1;" title="Editar">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="excluir({{ $m->id }})"
                                wire:confirm="Excluir o template '{{ $m->titulo }}'?"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;" title="Excluir">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
