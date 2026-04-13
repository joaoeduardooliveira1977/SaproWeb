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
    .aasp-hero { display:grid;grid-template-columns:minmax(0,1.4fr) minmax(280px,.8fr);gap:16px;align-items:stretch;margin-bottom:18px; }
    .aasp-guide { background:linear-gradient(135deg,#f8fafc,#eef6ff);border:1px solid var(--border);border-radius:10px;padding:18px; }
    .aasp-guide h2 { margin:0 0 8px;font-size:20px;color:var(--primary); }
    .aasp-guide p { margin:0 0 14px;color:var(--muted);font-size:13px;line-height:1.5; }
    .aasp-steps { display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px; }
    .aasp-step { background:#fff;border:1px solid var(--border);border-radius:8px;padding:10px;font-size:11px;color:var(--muted);line-height:1.35; }
    .aasp-step strong { display:block;color:var(--primary);font-size:12px;margin-bottom:3px; }
    .aasp-primary-card { background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;box-shadow:0 1px 6px rgba(0,0,0,.06); }
    .aasp-primary-card label { display:block;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px; }
    .aasp-primary-card input[type=date] { width:100%;margin-bottom:10px; }
    .aasp-stats { display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:16px; }
    .aasp-stat { background:var(--white);border:1px solid var(--border);border-radius:8px;padding:14px; }
    .aasp-stat .num { font-size:22px;font-weight:800;color:var(--primary);line-height:1; }
    .aasp-stat .label { color:var(--muted);font-size:11px;margin-top:6px; }
    .aasp-toolbar { display:flex;justify-content:space-between;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:14px; }
    .aasp-filter-group { display:flex;gap:8px;align-items:center;flex-wrap:wrap; }
    .aasp-actions { display:flex;gap:8px;align-items:center;flex-wrap:wrap; }
    .pub-list { display:grid;gap:10px; }
    .pub-card { border:1px solid var(--border);border-radius:10px;padding:14px;background:var(--white); }
    .pub-card-top { display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:10px; }
    .pub-title { font-size:14px;font-weight:700;color:var(--primary);margin-bottom:4px;line-height:1.35; }
    .pub-meta { display:flex;gap:8px;flex-wrap:wrap;color:var(--muted);font-size:11px; }
    .pub-body { color:var(--text);font-size:12px;line-height:1.55;white-space:pre-wrap;margin-top:10px; }
    .pub-footer { display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;margin-top:12px;padding-top:12px;border-top:1px solid var(--border); }
    .pub-processo { font-family:monospace;font-size:11px;color:var(--muted); }
    .pill-vinculada { background:#dcfce7;color:#166534;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700; }
    .pill-pendente { background:#fff7ed;color:#c2410c;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700; }
    @media (max-width: 980px) { .aasp-hero { grid-template-columns:1fr; } .aasp-steps,.aasp-stats { grid-template-columns:1fr 1fr; } }
    @media (max-width: 600px) { .aasp-steps,.aasp-stats { grid-template-columns:1fr; } .aasp-toolbar { align-items:stretch; } .aasp-filter-group,.aasp-actions { width:100%; } }
</style>

{{-- ══ Abas ══ --}}
<div class="tabs">
    <button class="tab-btn {{ $aba === 'publicacoes' ? 'active' : '' }}" wire:click="$set('aba','publicacoes')">
        Publicações
    </button>
    <button class="tab-btn {{ $aba === 'advogados' ? 'active' : '' }}" wire:click="$set('aba','advogados')">
        Configurar advogados
    </button>
    <button class="tab-btn {{ $aba === 'configuracoes' ? 'active' : '' }}" wire:click="$set('aba','configuracoes')">
        Configurações AASP
    </button>
</div>
{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ABA: PUBLICAÇÕES                                          --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($aba === 'publicacoes')
    @php
        $totalExibidas = $publicacoes->count();
        $totalVinculadas = $publicacoes->whereNotNull('processo_id')->count();
        $totalPendentes = max($totalExibidas - $totalVinculadas, 0);
    @endphp

    <div class="aasp-hero">
        <div class="aasp-guide">
            <h2>Publicações AASP</h2>
            <p>Use esta rotina para buscar as publicações do dia, conferir se foram vinculadas aos processos e gerar o material de acompanhamento.</p>
            <div class="aasp-steps">
                <div class="aasp-step"><strong>1. Escolha a data</strong>Informe o dia que será consultado na AASP.</div>
                <div class="aasp-step"><strong>2. Busque</strong>O sistema consulta todos os advogados ativos.</div>
                <div class="aasp-step"><strong>3. Revise</strong>Confira processo, advogado e status de vínculo.</div>
                <div class="aasp-step"><strong>4. Compartilhe</strong>Gere PDF ou envie por e-mail quando estiver tudo certo.</div>
            </div>
        </div>

        <div class="aasp-primary-card">
            <label>Data para consulta AASP</label>
            <input type="date" wire:model="dataBusca" class="form-control">
            <button class="btn btn-primary" wire:click="buscarPublicacoes" wire:loading.attr="disabled" style="width:100%;justify-content:center;">
                <span wire:loading.remove wire:target="buscarPublicacoes" style="display:inline-flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Buscar publicações da data
                </span>
                <span wire:loading wire:target="buscarPublicacoes" style="display:inline-flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Buscando...
                </span>
            </button>
            <p style="font-size:11px;color:var(--muted);line-height:1.45;margin:10px 0 0;">
                A consulta salva apenas publicações novas e evita duplicidade pelo número da publicação.
            </p>
        </div>
    </div>

    @if(count($logBusca) > 0)
    <div class="log-box mb-4">
        @foreach($logBusca as $linha)
            <div class="log-{{ $linha['tipo'] }}">
                [{{ $linha['tipo'] === 'sucesso' ? '+' : ($linha['tipo'] === 'erro' ? 'x' : ($linha['tipo'] === 'aviso' ? '!' : 'i')) }}]
                {{ $linha['msg'] }}
            </div>
        @endforeach
    </div>
    @endif

    <div class="aasp-stats">
        <div class="aasp-stat"><div class="num">{{ $totalDia }}</div><div class="label">publicações na data filtrada</div></div>
        <div class="aasp-stat"><div class="num">{{ $totalExibidas }}</div><div class="label">exibidas agora</div></div>
        <div class="aasp-stat"><div class="num">{{ $totalVinculadas }}</div><div class="label">vinculadas a processos</div></div>
        <div class="aasp-stat"><div class="num">{{ $totalPendentes }}</div><div class="label">pendentes de conferência</div></div>
    </div>

    <div class="card">
        <div class="aasp-toolbar">
            <div>
                <div class="card-title" style="margin-bottom:6px;">Revisar publicações salvas</div>
                <div style="font-size:12px;color:var(--muted);">Filtre os resultados já salvos e confira o vínculo com os processos do sistema.</div>
            </div>
            <div class="aasp-filter-group">
                <input type="date" wire:model.live="filtroData" title="Data dos resultados salvos">
                <select wire:model.live="filtroAdvogado">
                    <option value="">Todos os advogados</option>
                    @foreach($advogados as $adv)
                        <option value="{{ $adv->codigo_aasp }}">{{ $adv->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($publicacoes->isNotEmpty())
            <div class="aasp-actions" style="margin-bottom:14px;">
                <button class="btn btn-success btn-sm" wire:click="gerarPdf" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="gerarPdf" style="display:inline-flex;align-items:center;gap:6px;">Gerar PDF</span>
                    <span wire:loading wire:target="gerarPdf" style="display:inline-flex;align-items:center;gap:6px;">Gerando...</span>
                </button>
                <button class="btn btn-primary btn-sm" wire:click="enviarEmail" wire:loading.attr="disabled"
                        wire:confirm="Enviar as publicações por e-mail para os destinatários configurados?">
                    <span wire:loading.remove wire:target="enviarEmail" style="display:inline-flex;align-items:center;gap:6px;">Enviar por e-mail</span>
                    <span wire:loading wire:target="enviarEmail" style="display:inline-flex;align-items:center;gap:6px;">Enviando...</span>
                </button>
            </div>
        @endif

        @if($publicacoes->isEmpty())
            <div class="empty-state" style="padding:34px 20px;">
                <div class="empty-state-title">Nenhuma publicação encontrada</div>
                <div class="empty-state-sub">Ajuste os filtros ou faça uma nova busca na AASP para a data desejada.</div>
            </div>
        @else
            <div class="pub-list">
                @foreach($publicacoes as $pub)
                    @php
                        $nomeAdv = $advogados->firstWhere('codigo_aasp', $pub->codigo_aasp)?->nome ?? $pub->codigo_aasp;
                    @endphp
                    <article class="pub-card">
                        <div class="pub-card-top">
                            <div>
                                <div class="pub-title">{{ $pub->titulo ? Str::limit($pub->titulo, 150) : 'Publicação sem título informado' }}</div>
                                <div class="pub-meta">
                                    <span>{{ $pub->data ? $pub->data->format('d/m/Y') : 'Sem data' }}</span>
                                    <span>{{ $nomeAdv }}</span>
                                    @if($pub->jornal)<span>{{ $pub->jornal }}</span>@endif
                                </div>
                            </div>
                            @if($pub->processo_id)
                                <span class="pill-vinculada">Vinculada</span>
                            @else
                                <span class="pill-pendente">Conferir vínculo</span>
                            @endif
                        </div>

                        @if($pub->texto)
                            <div class="pub-body">{{ Str::limit($pub->texto, 520) }}</div>
                        @endif

                        <div class="pub-footer">
                            <div class="pub-processo">Processo: {{ $pub->numero_processo ?: 'não informado na publicação' }}</div>
                            <div class="aasp-actions">
                                @if($pub->processo_id && $pub->processo)
                                    <a class="btn btn-secondary-outline btn-sm" href="{{ route('processos.show', $pub->processo_id) }}">Abrir processo</a>
                                @elseif($pub->numero_processo)
                                    <a class="btn btn-secondary-outline btn-sm" href="{{ route('processos', ['busca' => $pub->numero_processo]) }}">Pesquisar processo</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <p style="font-size:11px;color:var(--muted);margin-top:12px;">{{ $totalExibidas }} publicação(ões) exibida(s).</p>
        @endif
    </div>

@endif
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
                                    <button title="Editar" wire:click="editarAdvogado({{ $adv->id }})" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#e0f2fe;color:#0369a1;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                    <button title="Excluir" wire:click="confirmarExcluirAdvogado({{ $adv->id }})" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
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
                    <button class="modal-close" wire:click="fecharModal" aria-label="Fechar"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
                    <button class="modal-close" wire:click="fecharModal" aria-label="Fechar"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
            <button class="btn btn-primary" wire:click="salvarConfig" style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar Configurações</button>
        </div>
    </div>

@endif

</div>
