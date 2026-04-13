<div>
@php
    $etapaLabels = \App\Models\CrmOportunidade::$etapas;
    $origemLabels= \App\Models\CrmOportunidade::$origens;
    $etapaCores  = [
        'novo_contato'=> ['bg'=>'#dbeafe','color'=>'#1d4ed8'],
        'qualificacao'=> ['bg'=>'#ede9fe','color'=>'#6d28d9'],
        'reuniao'     => ['bg'=>'#fef9c3','color'=>'#92400e'],
        'proposta'    => ['bg'=>'#ffedd5','color'=>'#c2410c'],
        'negociacao'  => ['bg'=>'#fce7f3','color'=>'#9d174d'],
        'ganho'       => ['bg'=>'#dcfce7','color'=>'#15803d'],
        'perdido'     => ['bg'=>'#fee2e2','color'=>'#b91c1c'],
    ];
@endphp

{{-- ── Cabeçalho / guia ─────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">CRM Jurídico</h1>
        <p style="font-size:13px;color:var(--muted);margin:4px 0 0;line-height:1.5;">Acompanhe interessados, negociações e próximos contatos até a conversão em cliente.</p>
    </div>
    <button wire:click="novaOportunidade()" class="btn btn-primary btn-sm">
        <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nova oportunidade
    </button>
</div>

<div class="crm-guide" style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;margin-bottom:16px;display:grid;grid-template-columns:minmax(260px,1fr) repeat(3,minmax(150px,1fr));gap:12px;align-items:center;">
    <div style="display:flex;gap:12px;align-items:flex-start;">
        <div style="width:38px;height:38px;border-radius:8px;background:#eff6ff;color:#2563a8;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg aria-hidden="true" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg></div>
        <div><div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:3px;">Como usar o CRM</div><div style="font-size:12px;color:var(--muted);line-height:1.5;">Use o funil para não perder contatos comerciais: registre o lead, mova pelas etapas e crie atividades de retorno.</div></div>
    </div>
    <div style="border-left:3px solid #2563a8;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">1. Novo contato</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Cadastre quem procurou o escritório.</span></div>
    <div style="border-left:3px solid #7c3aed;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">2. Evolução</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Passe por qualificação, reunião e proposta.</span></div>
    <div style="border-left:3px solid #059669;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">3. Conversão</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Quando fechar, converta em cliente.</span></div>
</div>

<div class="crm-actions" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
    <button wire:click="novaOportunidade('novo_contato')" style="text-align:left;background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:14px;cursor:pointer;">
        <strong style="display:block;font-size:13px;color:var(--primary);margin-bottom:4px;">Cadastrar lead</strong>
        <span style="font-size:12px;color:var(--muted);line-height:1.45;">Registre nome, telefone, origem e área de interesse.</span>
    </button>
    <button wire:click="$set('aba','atividades')" style="text-align:left;background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:14px;cursor:pointer;">
        <strong style="display:block;font-size:13px;color:var(--primary);margin-bottom:4px;">Ver retornos pendentes</strong>
        <span style="font-size:12px;color:var(--muted);line-height:1.45;">Acompanhe tarefas, ligações, reuniões e follow-ups.</span>
    </button>
    <button wire:click="$set('aba','lista')" style="text-align:left;background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:14px;cursor:pointer;">
        <strong style="display:block;font-size:13px;color:var(--primary);margin-bottom:4px;">Consultar oportunidades</strong>
        <span style="font-size:12px;color:var(--muted);line-height:1.45;">Busque contatos e veja status, responsável e previsão.</span>
    </button>
</div>

{{-- ── KPIs ──────────────────────────────────────────────────── --}}
<div class="stat-grid" style="margin-bottom:20px;">
    <div class="stat-card" style="border-left-color:var(--primary);">
        <div class="stat-val">{{ $totalAtivas }}</div>
        <div class="stat-label">Oportunidades ativas</div>
    </div>
    <div class="stat-card" style="border-left-color:var(--success);">
        <div class="stat-val" style="color:var(--success);">{{ $ganhasmes }}</div>
        <div class="stat-label">Ganhas este mês</div>
    </div>
    <div class="stat-card" style="border-left-color:var(--danger);">
        <div class="stat-val" style="color:var(--danger);">{{ $perdidasmes }}</div>
        <div class="stat-label">Perdidas este mês</div>
    </div>
    <div class="stat-card" style="border-left-color:var(--accent);">
        <div class="stat-val" style="font-size:18px;padding-top:6px;">R$ {{ number_format($valorPipeline, 0, ',', '.') }}</div>
        <div class="stat-label">Valor total no pipeline</div>
    </div>
</div>

{{-- ── Abas ──────────────────────────────────────────────────── --}}
<div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);">
    @foreach([['pipeline','Pipeline'],['lista','Lista'],['atividades','Atividades']] as [$v,$l])
    <button wire:click="$set('aba','{{ $v }}')"
        style="padding:8px 18px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:600;
               color:{{ $aba===$v ? 'var(--primary)' : 'var(--muted)' }};
               border-bottom:{{ $aba===$v ? '2px solid var(--primary)' : '2px solid transparent' }};
               margin-bottom:-2px;">{{ $l }}</button>
    @endforeach
    <div style="margin-left:auto;display:flex;gap:8px;align-items:center;padding-bottom:6px;">
        <select wire:model.live="filtroResponsavel" style="padding:6px 10px;font-size:12px;border:1.5px solid var(--border);border-radius:6px;max-width:160px;">
            <option value="">Todos responsáveis</option>
            @foreach($usuarios as $u)
            <option value="{{ $u->id }}">{{ $u->nome }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroOrigem" style="padding:6px 10px;font-size:12px;border:1.5px solid var(--border);border-radius:6px;max-width:140px;">
            <option value="">Todas origens</option>
            @foreach($origemLabels as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA: PIPELINE (KANBAN)                                      --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'pipeline')
<div style="background:#f8fafc;border:1.5px solid var(--border);border-left:4px solid #2563a8;border-radius:10px;padding:12px 14px;margin-bottom:14px;font-size:12px;color:var(--muted);line-height:1.5;">
    <strong style="color:var(--text);">Dica:</strong> clique em um card para editar a oportunidade, registrar atividades ou converter em cliente. Use o sinal <strong>+</strong> em cada coluna para criar uma oportunidade já naquela etapa.
</div>
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;align-items:start;overflow-x:auto;">
    @foreach(['novo_contato','qualificacao','reuniao','proposta','negociacao'] as $etapa)
    @php $cor = $etapaCores[$etapa]; $cards = $kanban[$etapa]; @endphp
    <div style="background:var(--bg);border-radius:10px;padding:12px;">
        {{-- Cabeçalho da coluna --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <div>
                <span style="font-size:12px;font-weight:700;color:{{ $cor['color'] }};">{{ $etapaLabels[$etapa] }}</span>
                <span style="font-size:11px;color:var(--muted);margin-left:6px;">{{ $cards->count() }}</span>
            </div>
            <button wire:click="novaOportunidade('{{ $etapa }}')"
                style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px;line-height:1;padding:0 4px;"
                title="Nova oportunidade">+</button>
        </div>

        {{-- Cards --}}
        @forelse($cards as $op)
        <div wire:click="editarOportunidade({{ $op->id }})"
            style="background:var(--white);border-radius:8px;padding:12px;margin-bottom:8px;cursor:pointer;
                   border:1px solid var(--border);transition:box-shadow .15s;
                   {{ $op->isVencida() ? 'border-left:3px solid var(--danger);' : '' }}"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
            onmouseout="this.style.boxShadow='none'">
            <div style="font-size:13px;font-weight:600;margin-bottom:4px;">{{ $op->nome }}</div>
            @if($op->area_direito)
                <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">{{ $op->area_direito }}</div>
            @endif
            @if($op->valor_estimado)
                <div style="font-size:12px;font-weight:700;color:var(--success);">
                    R$ {{ number_format($op->valor_estimado, 0, ',', '.') }}
                </div>
            @endif
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                <span style="font-size:10px;padding:1px 6px;border-radius:8px;background:{{ $etapaCores[$origemLabels[$op->origem] ? 'novo_contato' : 'novo_contato']['bg'] }};color:var(--muted);">
                    {{ $origemLabels[$op->origem] ?? $op->origem }}
                </span>
                @if($op->data_previsao)
                    <span style="font-size:10px;color:{{ $op->isVencida() ? 'var(--danger)' : 'var(--muted)' }};">
                        {{ $op->data_previsao->format('d/m/Y') }}
                    </span>
                @endif
            </div>
            @if($op->responsavel)
                <div style="font-size:10px;color:var(--muted);margin-top:4px;">{{ $op->responsavel->nome }}</div>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:20px 0;color:var(--muted);font-size:12px;">Nenhuma</div>
        @endforelse
    </div>
    @endforeach
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA: LISTA                                                  --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'lista')
<div class="card">
    <div class="filter-bar" style="margin-bottom:16px;">
        <input type="text" wire:model.live.debounce.400ms="filtroBusca" placeholder="Buscar nome, e-mail, telefone..." style="flex:2;">
        <select wire:model.live="filtroEtapa" style="max-width:180px;">
            <option value="">Todas as etapas</option>
            @foreach($etapaLabels as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </select>
    </div>

    @if($lista->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:40px 0;">Nenhuma oportunidade encontrada.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Contato</th>
                    <th>Área</th>
                    <th>Origem</th>
                    <th>Etapa</th>
                    <th style="text-align:right;">Valor</th>
                    <th>Responsável</th>
                    <th>Previsão</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lista as $op)
                @php $cor = $etapaCores[$op->etapa] ?? $etapaCores['novo_contato']; @endphp
                <tr>
                    <td style="font-weight:600;font-size:13px;">
                        {{ $op->nome }}
                        @if($op->convertido)
                            <span style="font-size:10px;background:#dcfce7;color:#15803d;padding:1px 5px;border-radius:6px;margin-left:4px;">Cliente</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">
                        @if($op->telefone)<div>{{ $op->telefone }}</div>@endif
                        @if($op->email)<div style="color:var(--muted);">{{ $op->email }}</div>@endif
                    </td>
                    <td style="font-size:12px;">{{ $op->area_direito ?: '—' }}</td>
                    <td style="font-size:12px;">{{ $origemLabels[$op->origem] ?? $op->origem }}</td>
                    <td>
                        <span style="font-size:11px;padding:2px 8px;border-radius:10px;font-weight:700;
                            background:{{ $cor['bg'] }};color:{{ $cor['color'] }};">
                            {{ $etapaLabels[$op->etapa] }}
                        </span>
                    </td>
                    <td style="text-align:right;font-weight:600;color:var(--success);">
                        {{ $op->valor_estimado ? 'R$ ' . number_format($op->valor_estimado, 0, ',', '.') : '—' }}
                    </td>
                    <td style="font-size:12px;">{{ $op->responsavel?->nome ?? '—' }}</td>
                    <td style="font-size:12px;{{ $op->isVencida() ? 'color:var(--danger);font-weight:600;' : '' }}">
                        {{ $op->data_previsao?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions">
                            <button wire:click="editarOportunidade({{ $op->id }})" class="btn-action btn-action-blue" title="Editar">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="excluirOportunidade({{ $op->id }})"
                                wire:confirm="Excluir esta oportunidade?"
                                class="btn-action btn-action-red" title="Excluir">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA: ATIVIDADES                                             --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'atividades')
<div class="card">
    <div class="card-header">
        <span class="card-title">Atividades pendentes</span>
    </div>

    @if($atividades->isEmpty())
        <p style="text-align:center;color:var(--muted);padding:40px 0;">Nenhuma atividade pendente.</p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data prevista</th>
                    <th>Oportunidade</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($atividades as $at)
                @php $atrasada = $at->isAtrasada(); $tipos = \App\Models\CrmAtividade::$tipos; @endphp
                <tr style="{{ $atrasada ? 'background:#fff1f2;' : '' }}">
                    <td style="font-size:12px;{{ $atrasada ? 'color:var(--danger);font-weight:700;' : '' }}">
                        {{ $at->data_prevista?->format('d/m/Y') ?? '—' }}
                        @if($atrasada) <span style="font-size:10px;">(atrasada)</span> @endif
                    </td>
                    <td style="font-size:13px;font-weight:600;">
                        <button wire:click="editarOportunidade({{ $at->oportunidade_id }})"
                            style="background:none;border:none;cursor:pointer;color:var(--primary);font-weight:600;font-size:13px;padding:0;">
                            {{ $at->oportunidade?->nome ?? '—' }}
                        </button>
                    </td>
                    <td style="font-size:12px;">{{ $tipos[$at->tipo]['icon'] ?? '' }} {{ $tipos[$at->tipo]['label'] ?? $at->tipo }}</td>
                    <td style="font-size:12px;">{{ $at->descricao }}</td>
                    <td style="text-align:center;">
                        <div class="btn-actions">
                            <button wire:click="concluirAtividade({{ $at->id }})" class="btn-action btn-action-green" title="Marcar concluída">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            <button wire:click="excluirAtividade({{ $at->id }})" wire:confirm="Excluir esta atividade?" class="btn-action btn-action-red" title="Excluir">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Oportunidade (criar / editar)                        --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($modalOp)
@php
    $etapasOrdem = ['novo_contato','qualificacao','reuniao','proposta','negociacao','ganho'];
    $tipos = \App\Models\CrmAtividade::$tipos;
    $areas = \App\Models\CrmOportunidade::$areas;
    $origens = \App\Models\CrmOportunidade::$origens;
@endphp
<div class="modal-backdrop" wire:click.self="fecharModal">
    <div class="modal" style="max-width:780px;padding:0;overflow:hidden;">

        {{-- Cabeçalho --}}
        <div style="background:var(--primary);color:#fff;padding:18px 24px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="font-size:16px;font-weight:700;">{{ $opId ? $opNome : 'Nova Oportunidade' }}</div>
                @if($opId && $opDados)
                <div style="font-size:11px;opacity:.7;margin-top:2px;">
                    Criado em {{ $opDados->created_at->format('d/m/Y') }}
                    @if($opDados->responsavel) · Resp: {{ $opDados->responsavel->nome }} @endif
                </div>
                @endif
            </div>
            <button wire:click="fecharModal" style="background:none;border:none;color:#fff;font-size:22px;cursor:pointer;opacity:.7;">×</button>
        </div>

        {{-- Progress bar de etapas --}}
        <div style="display:flex;background:#f8fafc;border-bottom:1px solid var(--border);overflow-x:auto;">
            @foreach($etapasOrdem as $etapa)
            @php $ativo = $opEtapa === $etapa; $passou = array_search($etapa,$etapasOrdem) < array_search($opEtapa,$etapasOrdem); @endphp
            <button wire:click="moverEtapa('{{ $etapa }}')"
                style="flex:1;min-width:90px;padding:10px 6px;border:none;cursor:pointer;font-size:11px;font-weight:600;
                    background:{{ $ativo ? 'var(--primary)' : ($passou ? '#e0f2fe' : 'transparent') }};
                    color:{{ $ativo ? '#fff' : ($passou ? '#0369a1' : 'var(--muted)') }};
                    border-right:1px solid var(--border);">
                {{ $etapaLabels[$etapa] }}
            </button>
            @endforeach
            <button wire:click="moverEtapa('perdido')"
                style="flex:1;min-width:90px;padding:10px 6px;border:none;cursor:pointer;font-size:11px;font-weight:600;
                    background:{{ $opEtapa==='perdido' ? '#dc2626' : 'transparent' }};
                    color:{{ $opEtapa==='perdido' ? '#fff' : 'var(--danger)' }};">
                Perdido
            </button>
        </div>

        <div style="display:grid;grid-template-columns:1fr 340px;max-height:70vh;overflow:hidden;">

            {{-- Coluna esquerda: dados --}}
            <div style="padding:20px;overflow-y:auto;border-right:1px solid var(--border);">
                <div class="form-grid">
                    <div class="form-field" style="grid-column:1/-1;">
                        <label class="lbl">Nome do lead *</label>
                        <input type="text" wire:model="opNome" placeholder="Nome completo">
                        @error('opNome') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label class="lbl">Telefone</label>
                        <input type="text" wire:model="opTelefone" placeholder="(11) 99999-9999">
                    </div>
                    <div class="form-field">
                        <label class="lbl">E-mail</label>
                        <input type="email" wire:model="opEmail" placeholder="email@exemplo.com">
                        @error('opEmail') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label class="lbl">CPF / CNPJ</label>
                        <input type="text" wire:model="opCpfCnpj" placeholder="000.000.000-00">
                    </div>
                    <div class="form-field">
                        <label class="lbl">Origem *</label>
                        <select wire:model="opOrigem">
                            @foreach($origens as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="lbl">Área do Direito</label>
                        <select wire:model="opArea">
                            <option value="">— Selecione —</option>
                            @foreach($areas as $a)
                            <option value="{{ $a }}">{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="lbl">Valor estimado (R$)</label>
                        <input type="text" wire:model="opValor" placeholder="0,00">
                    </div>
                    <div class="form-field">
                        <label class="lbl">Responsável</label>
                        <select wire:model="opResponsavel">
                            <option value="">— Nenhum —</option>
                            @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="lbl">Previsão de fechamento</label>
                        <input type="date" wire:model="opPrevisao">
                    </div>
                    <div class="form-field" style="grid-column:1/-1;">
                        <label class="lbl">Observações</label>
                        <textarea wire:model="opDescricao" rows="3" placeholder="Detalhes do caso, necessidades do cliente..."></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:space-between;margin-top:4px;">
                    <div style="display:flex;gap:8px;">
                        @if($opId)
                        <button wire:click="excluirOportunidade({{ $opId }})"
                            wire:confirm="Excluir esta oportunidade permanentemente?"
                            class="btn btn-danger btn-sm">Excluir</button>
                        @if(!($opDados?->convertido) && $opEtapa === 'ganho')
                        <button wire:click="converterCliente({{ $opId }})" class="btn btn-success btn-sm">
                            <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Converter em Cliente
                        </button>
                        @endif
                        @if($opDados?->convertido)
                        <span style="font-size:12px;background:#dcfce7;color:#15803d;padding:6px 10px;border-radius:6px;font-weight:600;">✓ Convertido em cliente</span>
                        @endif
                        @endif
                    </div>
                    <button wire:click="salvarOportunidade" class="btn btn-primary btn-sm">
                        {{ $opId ? 'Salvar alterações' : 'Criar oportunidade' }}
                    </button>
                </div>
            </div>

            {{-- Coluna direita: atividades --}}
            <div style="display:flex;flex-direction:column;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:12px;font-weight:700;color:var(--primary);">
                    Atividades
                </div>

                {{-- Lista de atividades --}}
                <div style="flex:1;overflow-y:auto;padding:10px 14px;">
                    @if($opId)
                        @forelse($opAtividades as $at)
                        <div style="display:flex;gap:8px;padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;
                            {{ $at->concluida ? 'opacity:.55;' : '' }}">
                            <div style="flex-shrink:0;font-size:16px;line-height:1.4;">
                                {{ $tipos[$at->tipo]['icon'] ?? '📋' }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="{{ $at->concluida ? 'text-decoration:line-through;' : '' }}">{{ $at->descricao }}</div>
                                <div style="color:var(--muted);font-size:11px;margin-top:2px;">
                                    {{ $tipos[$at->tipo]['label'] ?? $at->tipo }}
                                    @if($at->data_prevista) · {{ $at->data_prevista->format('d/m/Y') }} @endif
                                    @if($at->concluida) · <span style="color:var(--success);">concluída</span> @endif
                                    @if($at->isAtrasada()) · <span style="color:var(--danger);">atrasada</span> @endif
                                </div>
                            </div>
                            @if(!$at->concluida)
                            <button wire:click="concluirAtividade({{ $at->id }})"
                                style="background:none;border:none;cursor:pointer;color:var(--success);flex-shrink:0;"
                                title="Concluir">
                                <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            @endif
                        </div>
                        @empty
                        <p style="color:var(--muted);font-size:12px;text-align:center;padding:20px 0;">Nenhuma atividade ainda.</p>
                        @endforelse
                    @else
                        <p style="color:var(--muted);font-size:12px;text-align:center;padding:20px 0;">Salve primeiro para adicionar atividades.</p>
                    @endif
                </div>

                {{-- Form nova atividade --}}
                @if($opId)
                <div style="padding:12px 14px;border-top:1px solid var(--border);background:var(--bg);">
                    <div style="display:flex;gap:6px;margin-bottom:6px;">
                        <select wire:model="atTipo" style="flex:1;padding:6px 8px;font-size:12px;border:1.5px solid var(--border);border-radius:6px;">
                            @foreach($tipos as $k=>$t)
                            <option value="{{ $k }}">{{ $t['icon'] }} {{ $t['label'] }}</option>
                            @endforeach
                        </select>
                        <input type="date" wire:model="atData" style="flex:1;padding:6px 8px;font-size:12px;border:1.5px solid var(--border);border-radius:6px;">
                    </div>
                    <div style="display:flex;gap:6px;">
                        <input type="text" wire:model="atDescricao" placeholder="Descreva a atividade..."
                            wire:keydown.enter="salvarAtividade"
                            style="flex:1;padding:6px 8px;font-size:12px;border:1.5px solid var(--border);border-radius:6px;">
                        <button wire:click="salvarAtividade" class="btn btn-primary btn-sm" style="padding:6px 10px;">+</button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Motivo de perda                                      --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($modalPerda)
<div class="modal-backdrop" wire:click.self="$set('modalPerda',false)">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Marcar como Perdida</span>
            <button class="modal-close" wire:click="$set('modalPerda',false)">×</button>
        </div>
        <div class="form-field">
            <label class="lbl">Motivo da perda (opcional)</label>
            <textarea wire:model="opMotivo" rows="3" placeholder="Ex.: cliente contratou outro escritório, sem orçamento, desistiu..."></textarea>
        </div>
        <div class="modal-footer">
            <button wire:click="$set('modalPerda',false)" class="btn btn-secondary">Cancelar</button>
            <button wire:click="confirmarPerda" class="btn btn-danger">Confirmar perda</button>
        </div>
    </div>
</div>
@endif

<style>
    @media (max-width: 1100px) {
        .crm-guide {
            grid-template-columns: 1fr 1fr !important;
        }

        .crm-actions {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 760px) {
        .crm-guide {
            grid-template-columns: 1fr !important;
        }
    }
</style>

</div>
