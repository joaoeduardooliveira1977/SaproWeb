<div>
<style>
    .tabs { display: flex; gap: 4px; margin-bottom: 20px; border-bottom: 2px solid var(--border); overflow-x: auto; }
    .tab-btn {
        padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer;
        background: none; border: none; border-bottom: 3px solid transparent;
        color: var(--muted); margin-bottom: -2px; transition: all .15s;
    }
    .tab-btn:hover { color: var(--primary); }
    .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }

    .log-box { background: #0f2540; border-radius: 8px; padding: 14px; margin-bottom: 16px; font-size: 12px; font-family: monospace; max-height: 180px; overflow-y: auto; }
    .log-sucesso { color: #4ade80; }
    .log-erro    { color: #f87171; }
    .log-aviso   { color: #fbbf24; }
    .log-info    { color: #60a5fa; }

    .chave-col { font-family: monospace; font-size: 11px; color: var(--muted); }

    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
    @media (max-width: 600px) { .stats-row { grid-template-columns: 1fr; } }

    .action-bar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
    .action-bar input[type=date] { width: 170px; }
    .action-bar select { width: 200px; }

    .pub-texto { font-size: 11px; color: var(--muted); white-space: pre-wrap; max-height: 80px; overflow: hidden; }
    .expand-btn { background: none; border: none; cursor: pointer; font-size: 11px; color: var(--primary-light); padding: 0; }

    .pill-ativo   { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .pill-inativo { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
</style>

{{-- ══ Abas ══ --}}
<div class="tabs">
    <button class="tab-btn {{ $aba === 'publicacoes' ? 'active' : '' }}" wire:click="$set('aba','publicacoes')">
        Publicações
    </button>
    <button class="tab-btn {{ $aba === 'advogados' ? 'active' : '' }}" wire:click="$set('aba','advogados')">
        Advogados
    </button>
    <button class="tab-btn {{ $aba === 'configuracoes' ? 'active' : '' }}" wire:click="$set('aba','configuracoes')">
        Configurações
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: PUBLICAÇÕES                                          --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'publicacoes')

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card" style="border-left-color: var(--primary);">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v16a4 4 0 01-4-4V6H2v14a4 4 0 004 4z"/><line x1="12" y1="6" x2="18" y2="6"/><line x1="12" y1="10" x2="18" y2="10"/><line x1="8" y1="14" x2="18" y2="14"/><line x1="8" y1="18" x2="18" y2="18"/></svg></div>
            <div class="stat-val">{{ $totalDia }}</div>
            <div class="stat-label">Publicações no dia filtrado</div>
        </div>
        <div class="stat-card" style="border-left-color: var(--accent);">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="stat-val">{{ $advogados->where('ativo', true)->count() }}</div>
            <div class="stat-label">Advogados ativos</div>
        </div>
        <div class="stat-card" style="border-left-color: var(--success);">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="stat-val">{{ \App\Models\AaspPublicacao::count() }}</div>
            <div class="stat-label">Total de publicações salvas</div>
        </div>
    </div>

    {{-- Barra de busca --}}
    <div class="card mb-4">
        <div class="card-header">
            <span class="card-title">Buscar Publicações na API AASP</span>
        </div>
        <div class="action-bar">
            <input type="date" wire:model="dataBusca" class="form-control">
            <button class="btn btn-primary" wire:click="buscarPublicacoes" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="buscarPublicacoes" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> Buscar Publicações</span>
                <span wire:loading wire:target="buscarPublicacoes" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Buscando…</span>
            </button>
        </div>
        <p style="font-size:11px;color:var(--muted);">
            Busca para todos os advogados ativos cadastrados em sequência e salva os resultados.
        </p>
    </div>

    {{-- Log de busca --}}
    @if(count($logBusca) > 0)
    <div class="log-box mb-4">
        @foreach($logBusca as $linha)
            <div class="log-{{ $linha['tipo'] }}">
                [{{ $linha['tipo'] === 'sucesso' ? '✔' : ($linha['tipo'] === 'erro' ? '✘' : ($linha['tipo'] === 'aviso' ? '!' : 'i')) }}]
                {{ $linha['msg'] }}
            </div>
        @endforeach
    </div>
    @endif

    {{-- Filtros da tabela --}}
    <div class="action-bar">
        <input type="date" wire:model.live="filtroData">
        <select wire:model.live="filtroAdvogado">
            <option value="">Todos os advogados</option>
            @foreach($advogados as $adv)
                <option value="{{ $adv->codigo_aasp }}">{{ $adv->nome }}</option>
            @endforeach
        </select>
        @if(count($publicacoes) > 0)
            <button class="btn btn-success btn-sm" wire:click="gerarPdf" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="gerarPdf" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Gerar PDF</span>
                <span wire:loading wire:target="gerarPdf" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Gerando…</span>
            </button>
            <button class="btn btn-primary btn-sm" wire:click="enviarEmail" wire:loading.attr="disabled"
                    wire:confirm="Enviar as publicações por e-mail para os destinatários configurados?">
                <span wire:loading.remove wire:target="enviarEmail" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> Enviar por E-mail</span>
                <span wire:loading wire:target="enviarEmail" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Enviando…</span>
            </button>
        @endif
    </div>

    {{-- Tabela de resultados --}}
    <div class="card">
        @if($publicacoes->isEmpty())
            <p style="text-align:center;color:var(--muted);padding:30px 0;">
                Nenhuma publicação encontrada para os filtros selecionados.
            </p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Data</th>
                            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Advogado</th>
                            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Jornal</th>
                            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Processo</th>
                            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Título / Texto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publicacoes as $pub)
                            @php
                                $nomeAdv = $advogados->firstWhere('codigo_aasp', $pub->codigo_aasp)?->nome ?? $pub->codigo_aasp;
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;">
                                    {{ $pub->data ? $pub->data->format('d/m/Y') : '—' }}
                                </td>
                                <td>{{ $nomeAdv }}</td>
                                <td class="hide-sm" style="font-size:11px;">{{ $pub->jornal ?: '—' }}</td>
                                <td class="hide-sm" style="font-family:monospace;font-size:11px;white-space:nowrap;">
                                    {{ $pub->numero_processo ?: '—' }}
                                </td>
                                <td>
                                    @if($pub->titulo)
                                        <div style="font-weight:600;font-size:12px;margin-bottom:4px;">
                                            {{ Str::limit($pub->titulo, 120) }}
                                        </div>
                                    @endif
                                    @if($pub->texto)
                                        <div class="pub-texto">{{ Str::limit($pub->texto, 300) }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="font-size:11px;color:var(--muted);margin-top:10px;">
                {{ $publicacoes->count() }} publicação(ões) exibida(s).
            </p>
        @endif
    </div>

@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: ADVOGADOS                                            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'advogados')

    <div class="card">
        <div class="card-header">
            <span class="card-title">Advogados Cadastrados</span>
            <button class="btn btn-primary btn-sm" wire:click="novoAdvogado">+ Novo Advogado</button>
        </div>

        @if($advogados->isEmpty())
            <p style="text-align:center;color:var(--muted);padding:24px 0;">
                Nenhum advogado cadastrado ainda.
            </p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Nome</th>
                            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Código AASP</th>
                            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Chave AASP</th>
                            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">E-mail</th>
                            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Status</th>
                            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($advogados as $adv)
                            <tr>
                                <td>{{ $adv->nome }}</td>
                                <td class="chave-col hide-sm">{{ $adv->codigo_aasp }}</td>
                                <td class="chave-col hide-sm">
                                    {{ substr($adv->chave_aasp, 0, 6) }}••••••••
                                </td>
                                <td class="hide-sm" style="font-size:12px;">{{ $adv->email ?: '—' }}</td>
                                <td>
                                    @if($adv->ativo)
                                        <span class="pill-ativo">Ativo</span>
                                    @else
                                        <span class="pill-inativo">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <button title="Editar" wire:click="editarAdvogado({{ $adv->id }})" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#e0f2fe;color:#0369a1;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                    <button title="Excluir" wire:click="confirmarExcluirAdvogado({{ $adv->id }})" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Confirmação de exclusão --}}
    @if($confirmarExcluirAdv)
        <div class="modal-backdrop">
            <div class="modal" style="max-width:420px;">
                <div class="modal-header">
                    <span class="modal-title">Confirmar Exclusão</span>
                    <button class="modal-close" wire:click="fecharModal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                </div>
                <p>Deseja realmente excluir este advogado? As publicações salvas não serão removidas.</p>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="fecharModal">Cancelar</button>
                    <button class="btn btn-danger" wire:click="excluirAdvogado">Excluir</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Advogado --}}
    @if($modalAdvogado)
        <div class="modal-backdrop">
            <div class="modal">
                <div class="modal-header">
                    <span class="modal-title">{{ $advogadoId ? 'Editar Advogado' : 'Novo Advogado' }}</span>
                    <button class="modal-close" wire:click="fecharModal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                </div>

                <div class="form-grid">
                    <div class="form-field" style="grid-column:1/-1;">
                        <label class="lbl">Nome *</label>
                        <input type="text" wire:model="nomeAdv" placeholder="Nome completo do advogado">
                        @error('nomeAdv') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label class="lbl">Código AASP *</label>
                        <input type="text" wire:model="codigoAasp" placeholder="Ex: 123456">
                        @error('codigoAasp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label class="lbl">Chave AASP *</label>
                        <input type="text" wire:model="chaveAasp" placeholder="Chave individual">
                        @error('chaveAasp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label class="lbl">E-mail</label>
                        <input type="email" wire:model="emailAdv" placeholder="email@exemplo.com">
                        @error('emailAdv') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field" style="justify-content:flex-end;padding-bottom:6px;">
                        <label class="lbl">Status</label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:8px;">
                            <input type="checkbox" wire:model="ativoAdv" style="width:auto;">
                            <span>Ativo</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="fecharModal">Cancelar</button>
                    <button class="btn btn-primary" wire:click="salvarAdvogado">
                        {{ $advogadoId ? 'Salvar Alterações' : 'Cadastrar' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: CONFIGURAÇÕES                                        --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'configuracoes')

    <div class="card" style="max-width:600px;">
        <div class="card-header">
            <span class="card-title">Configurações do Módulo AASP</span>
        </div>

        <div class="form-field" style="margin-bottom:14px;">
            <label class="lbl">E-mails de Destino</label>
            <textarea wire:model="emailsDestino" rows="4"
                placeholder="Um e-mail por linha ou separados por vírgula&#10;email1@dominio.com&#10;email2@dominio.com"></textarea>
            <span style="font-size:11px;color:var(--muted);">
                Destinatários para o envio automático e manual das publicações.
            </span>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label class="lbl">Horário da Rotina</label>
                <input type="time" wire:model="horarioRotina">
                @error('horarioRotina') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <span style="font-size:11px;color:var(--muted);">Referência para agendamento externo (cron).</span>
            </div>
            <div class="form-field" style="justify-content:flex-end;padding-bottom:6px;">
                <label class="lbl">Rotina Ativa</label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:8px;">
                    <input type="checkbox" wire:model="configAtiva" style="width:auto;">
                    <span>Habilitada</span>
                </label>
            </div>
        </div>

        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:16px;">
            <p style="font-size:12px;color:var(--muted);margin:0;">
                <strong>Comando cron sugerido</strong> (executar via artisan schedule):<br>
                <code style="font-size:11px;">{{ $horarioRotina ?? '08:00' }} * * * php artisan aasp:buscar-publicacoes</code>
            </p>
        </div>

        <div style="text-align:right;">
            <button class="btn btn-primary" wire:click="salvarConfig" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar Configurações</button>
        </div>
    </div>

@endif

</div>
