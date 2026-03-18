<div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <a href="{{ route('processos') }}"
                style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:var(--muted);text-decoration:none;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Processos
            </a>
            <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="font-size:12px;color:var(--muted);">{{ $processoId ? 'Editar' : 'Novo' }}</span>
        </div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;display:flex;align-items:center;gap:8px;">
            @if($processoId)
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar Processo
            @else
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Novo Processo
            @endif
        </h2>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('processos') }}" class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:5px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Cancelar
        </a>
        <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <span wire:loading.remove wire:target="salvar" style="display:flex;align-items:center;gap:6px;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Salvar Processo
            </span>
            <span wire:loading wire:target="salvar">Salvando…</span>
        </button>
    </div>
</div>

{{-- ── Alerta de Conflito de Interesses ── --}}
@if(count($conflitos) > 0)
<div style="display:flex;gap:10px;padding:12px 16px;background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;margin-bottom:16px;">
    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div>
        <div style="font-size:13px;font-weight:700;color:#dc2626;margin-bottom:4px;">Conflito de Interesses Detectado</div>
        @foreach($conflitos as $c)
        <div style="font-size:12px;color:#7f1d1d;">• {{ $c }}</div>
        @endforeach
        <div style="font-size:11px;color:#b91c1c;margin-top:6px;">Verifique antes de prosseguir. Você ainda pode salvar o processo.</div>
    </div>
</div>
@endif

@php
$inp  = "width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
$sel  = $inp;
$sec  = "display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border);";
// Campos judiciais ficam desabilitados quando extrajudicial=true E número vazio
$disJ = $extrajudicial && empty($numero);
$disStyle = $disJ ? 'opacity:0.45;pointer-events:none;' : '';
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- ── Coluna esquerda ── --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- ═══ IDENTIFICAÇÃO ═══ --}}
        <div class="card">
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Identificação
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;">

                {{-- 01 Cliente — Autocomplete --}}
                <div class="form-field">
                    <label class="lbl">Cliente *</label>
                    <div style="position:relative;" x-data x-on:click.outside="$wire.set('clienteSugestoes', [])">
                        @if($cliente_id)
                        {{-- Cliente selecionado --}}
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1.5px solid var(--success);border-radius:8px;background:#f0fdf4;">
                            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span style="font-size:13px;color:var(--text);flex:1;">{{ $clienteNome }}</span>
                            <button type="button" wire:click="limparCliente"
                                style="background:none;border:none;cursor:pointer;color:var(--muted);padding:0;display:flex;align-items:center;"
                                title="Remover cliente">
                                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        @else
                        {{-- Input de busca --}}
                        <div style="position:relative;">
                            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                                style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" wire:model.live.debounce.300ms="clienteBusca"
                                placeholder="Digite o nome do cliente..."
                                style="{{ $inp }}padding-left:34px;"
                                autocomplete="off">
                        </div>
                        @if(count($clienteSugestoes) > 0)
                        <div style="position:absolute;top:100%;left:0;right:0;background:var(--white);border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:100;max-height:200px;overflow-y:auto;margin-top:2px;">
                            @foreach($clienteSugestoes as $s)
                            <div wire:click="selecionarCliente({{ $s['id'] }}, '{{ addslashes($s['nome']) }}')"
                                style="padding:9px 14px;font-size:13px;cursor:pointer;color:var(--text);border-bottom:1px solid var(--border);"
                                onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                                {{ $s['nome'] }}
                            </div>
                            @endforeach
                        </div>
                        @elseif(strlen($clienteBusca) >= 2 && count($clienteSugestoes) === 0)
                        <div style="position:absolute;top:100%;left:0;right:0;background:var(--white);border:1.5px solid var(--border);border-radius:8px;padding:12px 14px;font-size:12px;color:var(--muted);z-index:100;margin-top:2px;">
                            Nenhum cliente encontrado para "{{ $clienteBusca }}"
                        </div>
                        @endif
                        @endif
                    </div>
                    @error('cliente_id') <span style="color:var(--danger);font-size:11px;">{{ $message }}</span> @enderror
                </div>

                {{-- Número CNJ --}}
                <div class="form-field">
                    <label class="lbl">Número do Processo *</label>
                    <input wire:model.live.debounce.400ms="numero" type="text"
                        placeholder="0000000-00.0000.8.26.0001"
                        style="{{ $inp }}border-color:{{ $numeroValido ? 'var(--success)' : 'var(--border)' }};">
                    @error('numero')
                        <span style="color:var(--danger);font-size:11px;">{{ $message }}</span>
                    @else
                        @if($tribunalDetectado)
                            <span style="font-size:11px;color:var(--success);font-weight:600;display:flex;align-items:center;gap:4px;">
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Tribunal: {{ $tribunalDetectado }} — compatível com Consulta Judicial
                            </span>
                        @elseif(strlen($numero) > 5)
                            <span style="font-size:11px;color:var(--warning);display:flex;align-items:center;gap:4px;">
                                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                Formato não reconhecido — use: <strong>NNNNNNN-DD.AAAA.J.TT.OOOO</strong>
                            </span>
                        @else
                            <span style="font-size:11px;color:var(--muted);">Formato CNJ: 0001234-56.2023.8.26.0001</span>
                        @endif
                    @enderror
                </div>

                {{-- Data + Extrajudicial --}}
                <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:end;">
                    <div class="form-field">
                        <label class="lbl">Data de Distribuição</label>
                        <input wire:model="data_distribuicao" type="date" style="{{ $inp }}">
                    </div>
                    <div style="padding-bottom:2px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 12px;border:1.5px solid {{ $extrajudicial ? 'var(--warning)' : 'var(--border)' }};border-radius:8px;font-size:13px;background:{{ $extrajudicial ? '#fffbeb' : 'var(--white)' }};white-space:nowrap;">
                            <input wire:model.live="extrajudicial" type="checkbox" id="extrajudicial"
                                style="width:15px;height:15px;cursor:pointer;accent-color:var(--warning);margin:0;flex-shrink:0;">
                            Extrajudicial
                        </label>
                    </div>
                </div>

                @if($extrajudicial && empty($numero))
                <div style="display:flex;align-items:center;gap:6px;padding:8px 12px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:12px;color:#92400e;">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Campos judiciais desabilitados. Preencha o número do processo para reativá-los.
                </div>
                @endif

                {{-- 02 Autor ou Réu + 03 Unidade --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-field">
                        <label class="lbl">Autor ou Réu</label>
                        <select wire:model="autorReu" style="{{ $sel }}">
                            <option value="">— Selecione —</option>
                            <option value="Autor">Autor</option>
                            <option value="Réu">Réu</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="lbl">Unidade</label>
                        <input wire:model="unidade" type="text" placeholder="Ex: Apto 42" style="{{ $inp }}">
                    </div>
                </div>

                {{-- 04 Parte Contrária — Autocomplete --}}
                <div class="form-field">
                    <label class="lbl">Parte Contrária</label>
                    <div style="position:relative;" x-data x-on:click.outside="$wire.set('parteContrariaSugestoes', [])">
                        @if($parteContrariaId)
                        {{-- Selecionado do cadastro --}}
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--bg);">
                            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span style="font-size:13px;color:var(--text);flex:1;">{{ $parteContrariaBusca }}</span>
                            <button type="button" wire:click="limparParteContraria"
                                style="background:none;border:none;cursor:pointer;color:var(--muted);padding:0;display:flex;align-items:center;"
                                title="Remover">
                                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        @else
                        <input type="text" wire:model.live.debounce.300ms="parteContrariaBusca"
                            placeholder="Nome (busca no cadastro ou texto livre)..."
                            style="{{ $inp }}"
                            autocomplete="off">
                        @if(count($parteContrariaSugestoes) > 0)
                        <div style="position:absolute;top:100%;left:0;right:0;background:var(--white);border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:100;max-height:200px;overflow-y:auto;margin-top:2px;">
                            @foreach($parteContrariaSugestoes as $s)
                            <div wire:click="selecionarParteContraria({{ $s['id'] }}, '{{ addslashes($s['nome']) }}')"
                                style="padding:9px 14px;font-size:13px;cursor:pointer;color:var(--text);border-bottom:1px solid var(--border);"
                                onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                                {{ $s['nome'] }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endif
                    </div>
                    <span style="font-size:11px;color:var(--muted);">Digite para buscar no cadastro ou escreva livremente</span>
                </div>

            </div>
        </div>

        {{-- ═══ LOCALIZAÇÃO ═══ --}}
        <div class="card">
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Localização
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                {{-- Repartição/Fórum primeiro --}}
                <div class="form-field" style="{{ $disStyle }}">
                    <label class="lbl">Repartição / Fórum</label>
                    <select wire:model="reparticao_id" style="{{ $sel }}" {{ $disJ ? 'disabled' : '' }}>
                        <option value="">— Selecione —</option>
                        @foreach($reparticoes as $r)
                            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Vara depois --}}
                <div class="form-field" style="{{ $disStyle }}">
                    <label class="lbl">Vara</label>
                    <input wire:model="vara" type="text" style="{{ $inp }}" placeholder="Ex: 3ª Vara Cível"
                        {{ $disJ ? 'disabled' : '' }}>
                </div>

                {{-- Secretaria — oculto mas preservado --}}
                <div class="form-field" style="display:none;">
                    <label class="lbl">Secretaria</label>
                    <select wire:model="secretaria_id" style="{{ $sel }}">
                        <option value="">— Selecione —</option>
                        @foreach($secretarias as $s)
                            <option value="{{ $s->id }}">{{ $s->descricao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Juiz — oculto mas preservado --}}
                <div class="form-field" style="display:none;">
                    <label class="lbl">Juiz</label>
                    <select wire:model="juiz_id" style="{{ $sel }}">
                        <option value="">— Selecione —</option>
                        @foreach($juizes as $j)
                            <option value="{{ $j->id }}">{{ $j->nome }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

    </div>

    {{-- ── Coluna direita ── --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- ═══ CLASSIFICAÇÃO ═══ --}}
        <div class="card">
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Classificação
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                {{-- 03 Advogados — múltipla seleção --}}
                <div class="form-field" style="grid-column:1/-1;">
                    <label class="lbl">Advogado(s) Responsável(is)</label>
                    @if($advogados->isEmpty())
                        <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Nenhum advogado cadastrado.</p>
                    @else
                    <div style="display:flex;flex-wrap:wrap;gap:6px;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);min-height:42px;">
                        @foreach($advogados as $a)
                        @php $checked = in_array($a->id, $advogados_selecionados); @endphp
                        <label style="display:flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;border:1.5px solid {{ $checked ? 'var(--primary-light)' : 'var(--border)' }};background:{{ $checked ? '#eff6ff' : 'var(--white)' }};cursor:pointer;font-size:12px;font-weight:{{ $checked ? '600' : '400' }};color:{{ $checked ? 'var(--primary-light)' : 'var(--text)' }};transition:all .15s;">
                            <input type="checkbox"
                                wire:model.live="advogados_selecionados"
                                value="{{ $a->id }}"
                                style="display:none;">
                            @if($checked)
                            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                            {{ $a->nome }}
                        </label>
                        @endforeach
                    </div>
                    @if(count($advogados_selecionados) > 0)
                    <span style="font-size:11px;color:var(--muted);margin-top:3px;display:block;">
                        {{ count($advogados_selecionados) }} advogado(s) selecionado(s)
                    </span>
                    @endif
                    @endif
                </div>

                {{-- Situação/Fase --}}
                <div class="form-field" style="{{ $disStyle }}">
                    <label class="lbl">Situação / Fase</label>
                    <select wire:model="fase_id" style="{{ $sel }}" {{ $disJ ? 'disabled' : '' }}>
                        <option value="">— Selecione —</option>
                        @foreach($fases as $f)
                            <option value="{{ $f->id }}">{{ $f->descricao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Grau de Risco --}}
                <div class="form-field" style="{{ $disStyle }}">
                    <label class="lbl">Grau de Risco</label>
                    <select wire:model="risco_id" style="{{ $sel }}" {{ $disJ ? 'disabled' : '' }}>
                        <option value="">— Selecione —</option>
                        @foreach($riscos as $r)
                            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de Ação --}}
                <div class="form-field" style="{{ $disStyle }}">
                    <label class="lbl">Tipo de Ação</label>
                    <select wire:model="tipo_acao_id" style="{{ $sel }}" {{ $disJ ? 'disabled' : '' }}>
                        <option value="">— Selecione —</option>
                        @foreach($tiposAcao as $t)
                            <option value="{{ $t->id }}">{{ $t->descricao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de Processo --}}
                <div class="form-field" style="{{ $disStyle }}">
                    <label class="lbl">Tipo de Processo</label>
                    <select wire:model="tipo_processo_id" style="{{ $sel }}" {{ $disJ ? 'disabled' : '' }}>
                        <option value="">— Selecione —</option>
                        @foreach($tiposProcesso as $t)
                            <option value="{{ $t->id }}">{{ $t->descricao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status — oculto, sempre salva 'Ativo' --}}
                <div class="form-field" style="display:none;">
                    <label class="lbl">Status</label>
                    <select wire:model="status" style="{{ $sel }}">
                        <option value="Ativo">Ativo</option>
                        <option value="Suspenso">Suspenso</option>
                        <option value="Arquivado">Arquivado</option>
                        <option value="Encerrado">Encerrado</option>
                    </select>
                </div>

                {{-- Assunto — oculto --}}
                <div class="form-field" style="display:none;grid-column:1/-1;">
                    <label class="lbl">Assunto</label>
                    <select wire:model="assunto_id" style="{{ $sel }}">
                        <option value="">— Selecione —</option>
                        @foreach($assuntos as $a)
                            <option value="{{ $a->id }}">{{ $a->descricao }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- ═══ FINANCEIRO ═══ --}}
        <div class="card">
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Financeiro
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-field">
                    <label class="lbl">Valor da Causa</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);font-weight:600;">R$</span>
                        <input wire:model="valor_causa" type="text" placeholder="0,00"
                            style="{{ $inp }}padding-left:32px;">
                    </div>
                </div>
                <div class="form-field">
                    <label class="lbl">Valor em Risco</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);font-weight:600;">R$</span>
                        <input wire:model="valor_risco" type="text" placeholder="0,00"
                            style="{{ $inp }}padding-left:32px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ OBSERVAÇÕES ═══ --}}
        <div class="card">
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Observações
            </div>
            <textarea wire:model="observacoes" rows="4" placeholder="Informações adicionais sobre o processo..."
                style="{{ $inp }}resize:vertical;font-family:inherit;"></textarea>
        </div>

    </div>
</div>

{{-- ── Barra de ações (rodapé) ── --}}
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;padding:16px;background:var(--white);border:1px solid var(--border);border-radius:10px;">
    <a href="{{ route('processos') }}" class="btn btn-outline" style="display:flex;align-items:center;gap:5px;">
        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Cancelar
    </a>
    <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;min-width:150px;justify-content:center;">
        <span wire:loading.remove wire:target="salvar" style="display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Salvar Processo
        </span>
        <span wire:loading wire:target="salvar">Salvando…</span>
    </button>
</div>

</div>
