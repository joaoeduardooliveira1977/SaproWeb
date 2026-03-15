<div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">Pessoas</h2>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            {{ $pessoas->total() }} pessoa{{ $pessoas->total() !== 1 ? 's' : '' }} cadastrada{{ $pessoas->total() !== 1 ? 's' : '' }}
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        <button wire:click="exportarCsv" wire:loading.attr="disabled"
            class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando…</span>
        </button>
        <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nova Pessoa
        </button>
    </div>
</div>

{{-- ── Filtros ── --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <div style="flex:1;min-width:200px;position:relative;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="busca"
                placeholder="Buscar por nome, CPF, e-mail..."
                style="width:100%;padding:8px 10px 8px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        </div>
        <select wire:model.live="tipo"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:150px;">
            <option value="">Todos os tipos</option>
            @foreach($tiposDisponiveis as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
        @if($busca || $tipo)
        <button wire:click="$set('busca',''); $set('tipo','')"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:none;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:5px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar
        </button>
        @endif
    </div>
</div>

{{-- ── Tabela ── --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table style="border-collapse:collapse;width:100%;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Pessoa</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">CPF / CNPJ</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Contato</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pessoas as $p)
                @php
                    $iniciais = collect(explode(' ', trim($p->nome)))->filter()->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                    $avatarCores = ['#2563a8','#16a34a','#d97706','#dc2626','#7c3aed','#0891b2','#be185d'];
                    $avatarCor   = $avatarCores[ord($p->nome[0] ?? 'A') % count($avatarCores)];
                @endphp
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">

                    {{-- Nome + avatar --}}
                    <td style="padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:{{ $avatarCor }}22;color:{{ $avatarCor }};font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1.5px solid {{ $avatarCor }}44;">
                                {{ $iniciais }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $p->nome }}</div>
                                @if($p->oab)
                                <div style="font-size:11px;color:var(--muted);margin-top:1px;">OAB {{ $p->oab }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- CPF/CNPJ --}}
                    <td class="hide-sm" style="padding:14px 16px;font-size:13px;color:var(--muted);font-family:monospace;">
                        {{ $p->cpf_cnpj ?? '—' }}
                    </td>

                    {{-- Tipos --}}
                    <td style="padding:14px 16px;">
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($tiposPorPessoa->get($p->id, []) as $tipoPessoa)
                            @php
                            $corTipo = match($tipoPessoa) {
                                'Cliente'         => ['#1d4ed8','#dbeafe'],
                                'Advogado'        => ['#15803d','#dcfce7'],
                                'Juiz'            => ['#b45309','#fef3c7'],
                                'Parte Contrária' => ['#b91c1c','#fee2e2'],
                                default           => ['#6d28d9','#ede9fe'],
                            };
                            @endphp
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $corTipo[1] }};color:{{ $corTipo[0] }};">
                                {{ $tipoPessoa }}
                            </span>
                            @endforeach
                        </div>
                    </td>

                    {{-- Contato --}}
                    <td class="hide-sm" style="padding:14px 16px;">
                        @if($p->email)
                        <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text);margin-bottom:3px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $p->email }}
                        </div>
                        @endif
                        @if($p->celular || $p->telefone)
                        <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted);">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $p->celular ?? $p->telefone }}
                        </div>
                        @endif
                        @if(!$p->email && !$p->celular && !$p->telefone)
                        <span style="font-size:12px;color:var(--muted);">—</span>
                        @endif
                    </td>

                    {{-- Ações --}}
                    <td style="padding:14px 16px;text-align:center;">
                        <div style="display:flex;justify-content:center;gap:4px;">
                            <button wire:click="abrirModal({{ $p->id }})" title="Editar"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="desativar({{ $p->id }})" title="Desativar"
                                wire:confirm="Desativar {{ $p->nome }}?"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <div style="font-size:14px;font-weight:500;">Nenhuma pessoa encontrada</div>
                        <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou cadastre uma nova pessoa.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
        <span style="font-size:13px;color:var(--muted);">
            @if($pessoas->total() > 0)
                Mostrando {{ $pessoas->firstItem() }}–{{ $pessoas->lastItem() }} de {{ $pessoas->total() }}
            @else
                Nenhum resultado
            @endif
        </span>
        <div style="display:flex;align-items:center;gap:6px;">
            <button wire:click="previousPage" @disabled($pessoas->onFirstPage())
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $pessoas->onFirstPage() ? '.4' : '1' }};">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Anterior
            </button>
            <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                {{ $pessoas->currentPage() }} / {{ $pessoas->lastPage() }}
            </span>
            <button wire:click="nextPage" @disabled(!$pessoas->hasMorePages())
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $pessoas->hasMorePages() ? '1' : '.4' }};">
                Próxima
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- Nota informativa --}}
<div style="margin-top:12px;padding:10px 14px;background:#f0f9ff;border-radius:8px;font-size:12px;color:#64748b;border:1px solid #bae6fd;display:flex;align-items:center;gap:8px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Uma pessoa pode ter múltiplos tipos (ex: Advogado que também é Cliente) sem duplicar o cadastro.
</div>

{{-- ── Modal Cadastro ── --}}
@if($modalAberto)
<div class="modal-backdrop" wire:click.self="fecharModal">
    <div class="modal" style="max-width:580px;max-height:90vh;overflow-y:auto;">

        {{-- Header --}}
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="modal-title">{{ $pessoaId ? 'Editar Pessoa' : 'Nova Pessoa' }}</span>
            </div>
            <button wire:click="fecharModal" class="modal-close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @php
        $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
        $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
        @endphp

        {{-- Seção: Dados Pessoais --}}
        <div style="{{ $sec }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Dados Pessoais
        </div>
        <div class="form-field" style="margin-bottom:12px;">
            <label class="lbl">Nome Completo *</label>
            <input type="text" wire:model="nome" placeholder="Nome completo" style="{{ $inp }}">
            @error('nome')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div class="form-field">
                <label class="lbl">CPF / CNPJ</label>
                <input type="text" wire:model="cpf_cnpj" placeholder="000.000.000-00" style="{{ $inp }}">
                @error('cpf_cnpj')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label class="lbl">RG</label>
                <input type="text" wire:model="rg" placeholder="Número do RG" style="{{ $inp }}">
            </div>
            <div class="form-field">
                <label class="lbl">Data de Nascimento</label>
                <input type="date" wire:model="data_nascimento" style="{{ $inp }}">
            </div>
            <div class="form-field">
                <label class="lbl">OAB <span style="font-weight:400;color:var(--muted);">(se Advogado)</span></label>
                <input type="text" wire:model="oab" placeholder="Número da OAB" style="{{ $inp }}">
            </div>
        </div>

        {{-- Seção: Contato --}}
        <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Contato
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div class="form-field">
                <label class="lbl">Telefone</label>
                <input type="text" wire:model="telefone" placeholder="(00) 0000-0000" style="{{ $inp }}">
            </div>
            <div class="form-field">
                <label class="lbl">Celular</label>
                <input type="text" wire:model="celular" placeholder="(00) 00000-0000" style="{{ $inp }}">
            </div>
        </div>
        <div class="form-field" style="margin-bottom:12px;">
            <label class="lbl">E-mail</label>
            <input type="email" wire:model="email" placeholder="email@exemplo.com" style="{{ $inp }}">
            @error('email')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>

        {{-- Seção: Endereço --}}
        <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Endereço
        </div>
        <div class="form-field" style="margin-bottom:12px;">
            <label class="lbl">Logradouro</label>
            <input type="text" wire:model="logradouro" placeholder="Rua, número, complemento" style="{{ $inp }}">
        </div>
        <div style="display:grid;grid-template-columns:1fr 80px 110px;gap:10px;margin-bottom:12px;">
            <div class="form-field">
                <label class="lbl">Cidade</label>
                <input type="text" wire:model="cidade" placeholder="Cidade" style="{{ $inp }}">
            </div>
            <div class="form-field">
                <label class="lbl">UF</label>
                <input type="text" wire:model="estado" placeholder="SP" maxlength="2" style="{{ $inp }}text-transform:uppercase;">
            </div>
            <div class="form-field">
                <label class="lbl">CEP</label>
                <input type="text" wire:model="cep" placeholder="00000-000" style="{{ $inp }}">
            </div>
        </div>

        {{-- Seção: Tipos --}}
        <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Tipos de Pessoa *
        </div>
        @error('tipos_selecionados')<span style="color:var(--danger);font-size:11px;display:block;margin-bottom:8px;">{{ $message }}</span>@enderror
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
            @foreach($tiposDisponiveis as $t)
            @php
            $ativo = in_array($t, $tipos_selecionados);
            $corTipoM = match($t) {
                'Cliente'         => ['#1d4ed8','#dbeafe','#bfdbfe'],
                'Advogado'        => ['#15803d','#dcfce7','#bbf7d0'],
                'Juiz'            => ['#b45309','#fef3c7','#fde68a'],
                'Parte Contrária' => ['#b91c1c','#fee2e2','#fecaca'],
                default           => ['#6d28d9','#ede9fe','#ddd6fe'],
            };
            @endphp
            <label style="display:flex;align-items:center;gap:6px;padding:7px 12px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;border:1.5px solid {{ $ativo ? $corTipoM[2] : 'var(--border)' }};background:{{ $ativo ? $corTipoM[1] : 'var(--white)' }};color:{{ $ativo ? $corTipoM[0] : 'var(--text)' }};transition:all .15s;">
                <input type="checkbox" wire:model.live="tipos_selecionados" value="{{ $t }}" style="width:14px;height:14px;accent-color:{{ $corTipoM[0] }};margin:0;flex-shrink:0;cursor:pointer;">
                {{ $t }}
            </label>
            @endforeach
        </div>

        {{-- Observações --}}
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:4px;">
            <label class="lbl" style="display:block;margin-bottom:6px;">Observações</label>
            <textarea wire:model="observacoes" rows="2" placeholder="Observações internas..."
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>

        {{-- Footer --}}
        <div class="modal-footer" style="margin-top:16px;">
            <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
            <button wire:click="salvar" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Salvar
            </button>
        </div>
    </div>
</div>
@endif

</div>
