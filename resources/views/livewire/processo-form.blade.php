<div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <a href="{{ route('processos') }}"
                style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:var(--muted);text-decoration:none;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Processos
            </a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="font-size:12px;color:var(--muted);">{{ $processoId ? 'Editar' : 'Novo' }}</span>
        </div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;display:flex;align-items:center;gap:8px;">
            @if($processoId)
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar Processo
            @else
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Novo Processo
            @endif
        </h2>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('processos') }}" class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Cancelar
        </a>
        <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <span wire:loading.remove wire:target="salvar" style="display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Salvar Processo
            </span>
            <span wire:loading wire:target="salvar">Salvando…</span>
        </button>
    </div>
</div>

@php
$inputStyle = "width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
$selectStyle = $inputStyle;
$sectionTitle = "display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border);";
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- ── Coluna esquerda ── --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Seção: Identificação --}}
        <div class="card">
            <div style="{{ $sectionTitle }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Identificação
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;">
                {{-- Cliente --}}
                <div class="form-field">
                    <label class="lbl">Cliente *</label>
                    <select wire:model="cliente_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->nome }}</option>
                        @endforeach
                    </select>
                    @error('cliente_id') <span style="color:var(--danger);font-size:11px;">{{ $message }}</span> @enderror
                </div>

                {{-- Número CNJ --}}
                <div class="form-field">
                    <label class="lbl">Número do Processo *</label>
                    <input wire:model.live.debounce.400ms="numero" type="text"
                        placeholder="0000000-00.0000.8.26.0001"
                        style="{{ $inputStyle }}border-color:{{ $numeroValido ? 'var(--success)' : 'var(--border)' }};">
                    @error('numero')
                        <span style="color:var(--danger);font-size:11px;">{{ $message }}</span>
                    @else
                        @if($tribunalDetectado)
                            <span style="font-size:11px;color:var(--success);font-weight:600;display:flex;align-items:center;gap:4px;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Tribunal: {{ $tribunalDetectado }} — compatível com Consulta Judicial
                            </span>
                        @elseif(strlen($numero) > 5)
                            <span style="font-size:11px;color:var(--warning);display:flex;align-items:center;gap:4px;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                Formato não reconhecido. Use: <strong>NNNNNNN-DD.AAAA.J.TT.OOOO</strong>
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
                        <input wire:model="data_distribuicao" type="date" style="{{ $inputStyle }}">
                    </div>
                    <div style="padding-bottom:2px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);white-space:nowrap;">
                            <input wire:model="extrajudicial" type="checkbox" id="extrajudicial"
                                style="width:15px;height:15px;cursor:pointer;accent-color:var(--primary);margin:0;flex-shrink:0;">
                            Extrajudicial
                        </label>
                    </div>
                </div>

                {{-- Parte Contrária --}}
                <div class="form-field">
                    <label class="lbl">Parte Contrária</label>
                    <input wire:model="parte_contraria" type="text" style="{{ $inputStyle }}" placeholder="Nome da parte contrária">
                </div>
            </div>
        </div>

        {{-- Seção: Localização --}}
        <div class="card">
            <div style="{{ $sectionTitle }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Localização
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-field">
                    <label class="lbl">Vara</label>
                    <input wire:model="vara" type="text" style="{{ $inputStyle }}" placeholder="Ex: 3ª Vara Cível">
                </div>
                <div class="form-field">
                    <label class="lbl">Repartição / Fórum</label>
                    <select wire:model="reparticao_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($reparticoes as $r)
                            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Secretaria</label>
                    <select wire:model="secretaria_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($secretarias as $s)
                            <option value="{{ $s->id }}">{{ $s->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Juiz</label>
                    <select wire:model="juiz_id" style="{{ $selectStyle }}">
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

        {{-- Seção: Classificação --}}
        <div class="card">
            <div style="{{ $sectionTitle }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Classificação
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-field">
                    <label class="lbl">Advogado Responsável</label>
                    <select wire:model="advogado_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($advogados as $a)
                            <option value="{{ $a->id }}">{{ $a->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Situação / Fase</label>
                    <select wire:model="fase_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($fases as $f)
                            <option value="{{ $f->id }}">{{ $f->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Status</label>
                    <select wire:model="status" style="{{ $selectStyle }}">
                        <option value="Ativo">Ativo</option>
                        <option value="Suspenso">Suspenso</option>
                        <option value="Arquivado">Arquivado</option>
                        <option value="Encerrado">Encerrado</option>
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Grau de Risco</label>
                    <select wire:model="risco_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($riscos as $r)
                            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo de Ação</label>
                    <select wire:model="tipo_acao_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($tiposAcao as $t)
                            <option value="{{ $t->id }}">{{ $t->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo de Processo</label>
                    <select wire:model="tipo_processo_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($tiposProcesso as $t)
                            <option value="{{ $t->id }}">{{ $t->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field" style="grid-column:1/-1;">
                    <label class="lbl">Assunto</label>
                    <select wire:model="assunto_id" style="{{ $selectStyle }}">
                        <option value="">— Selecione —</option>
                        @foreach($assuntos as $a)
                            <option value="{{ $a->id }}">{{ $a->descricao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Seção: Financeiro --}}
        <div class="card">
            <div style="{{ $sectionTitle }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Financeiro
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-field">
                    <label class="lbl">Valor da Causa</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);font-weight:600;">R$</span>
                        <input wire:model="valor_causa" type="text" placeholder="0,00"
                            style="{{ $inputStyle }}padding-left:32px;">
                    </div>
                </div>
                <div class="form-field">
                    <label class="lbl">Valor em Risco</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);font-weight:600;">R$</span>
                        <input wire:model="valor_risco" type="text" placeholder="0,00"
                            style="{{ $inputStyle }}padding-left:32px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Seção: Observações --}}
        <div class="card">
            <div style="{{ $sectionTitle }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Observações
            </div>
            <textarea wire:model="observacoes" rows="4" placeholder="Informações adicionais sobre o processo..."
                style="{{ $inputStyle }}resize:vertical;font-family:inherit;"></textarea>
        </div>

    </div>
</div>

{{-- ── Barra de ações (fixada no fim) ── --}}
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;padding:16px;background:var(--white);border:1px solid var(--border);border-radius:10px;">
    <a href="{{ route('processos') }}" class="btn btn-outline" style="display:flex;align-items:center;gap:5px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Cancelar
    </a>
    <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;min-width:150px;justify-content:center;">
        <span wire:loading.remove wire:target="salvar" style="display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Salvar Processo
        </span>
        <span wire:loading wire:target="salvar">Salvando…</span>
    </button>
</div>

</div>
