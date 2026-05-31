section('page-title', 'Comunicados')
<div>

    {{-- â”€â”€ Filtros â”€â”€ --}}
    <div class="filter-bar">
        <select wire:model.live="filtroTipo" style="min-width:200px;">
            <option value="">Todos os tipos</option>
            @foreach(\App\Models\Comunicado::$tipos as $val => $t)
            <option value="{{ $val }}">{{ $t['icon'] }} {{ $t['label'] }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroPrioridade">
            <option value="">Todas as prioridades</option>
            <option value="banner">Banner no topo</option>
            <option value="modal">Modal ao logar</option>
            <option value="notificacao">NotificaÃ§Ã£o</option>
        </select>
        <select wire:model.live="filtroStatus">
            <option value="">Todos os status</option>
            <option value="1">Ativos</option>
            <option value="0">Inativos</option>
        </select>
        <button wire:click="novo" class="btn btn-primary">+ Novo Comunicado</button>
    </div>

    {{-- â”€â”€ Tabela â”€â”€ --}}
    <div class="card">
        <div class="card-header"><span class="card-title">{{ $comunicados->count() }} comunicado(s)</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>TÃ­tulo</th>
                        <th>Tipo</th>
                        <th>Prioridade</th>
                        <th>Destino</th>
                        <th>PerÃ­odo</th>
                        <th style="text-align:center;">Leituras</th>
                        <th>Status</th>
                        <th>AÃ§Ãµes</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($comunicados as $c)
                @php $ti = $c->tipoInfo(); @endphp
                <tr wire:key="{{ $c->id }}">
                    <td>
                        <div style="font-weight:600;">{{ $c->titulo }}</div>
                        <div style="font-size:11px;color:#94a3b8;">{{ Str::limit($c->mensagem, 50) }}</div>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:{{ $ti['bg'] }};color:{{ $ti['cor'] }};border:1px solid {{ $ti['border'] }};">
                            {{ $ti['icon'] }} {{ $ti['label'] }}
                        </span>
                    </td>
                    <td>
                        @php $priCor = ['banner'=>'badge-red','modal'=>'badge-blue','notificacao'=>'badge-gray'][$c->prioridade]; @endphp
                        <span class="badge {{ $priCor }}">{{ ucfirst($c->prioridade) }}</span>
                    </td>
                    <td style="font-size:12px;">
                        @if($c->destino === 'todos') ðŸŒ Todos
                        @elseif($c->destino === 'tenant_especifico') ðŸ¢ {{ $c->tenant?->nome ?? "Tenant #{$c->tenant_id}" }}
                        @else ðŸ“‹ Plano {{ ucfirst($c->plano) }}
                        @endif
                    </td>
                    <td style="font-size:11px;color:#64748b;">
                        <div>{{ $c->data_inicio->format('d/m/Y H:i') }}</div>
                        <div>{{ $c->data_fim ? 'atÃ© '.$c->data_fim->format('d/m/Y H:i') : 'sem expiraÃ§Ã£o' }}</div>
                    </td>
                    <td style="text-align:center;font-weight:700;color:var(--primary);">{{ $c->leituras_count }}</td>
                    <td>
                        @if($c->ativo && $c->data_inicio->isPast() && (!$c->data_fim || $c->data_fim->isFuture()))
                            <span class="badge badge-green">Ativo</span>
                        @elseif(!$c->ativo)
                            <span class="badge badge-red">Inativo</span>
                        @else
                            <span class="badge badge-gray">Agendado</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <button wire:click="editar({{ $c->id }})" class="btn btn-sm btn-outline">Editar</button>
                            <button wire:click="toggleAtivo({{ $c->id }})"
                                    class="btn btn-sm {{ $c->ativo ? 'btn-danger' : 'btn-success' }}">
                                {{ $c->ativo ? 'Pausar' : 'Ativar' }}
                            </button>
                            <button wire:click="excluir({{ $c->id }})" wire:confirm="Excluir este comunicado?"
                                    class="btn btn-sm" style="background:#fee2e2;color:#dc2626;">âœ•</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;">Nenhum comunicado criado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- â”€â”€ Modal FormulÃ¡rio â”€â”€ --}}
    @if($modalAberto)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:flex;align-items:flex-start;justify-content:center;padding:24px;overflow-y:auto;">
        <div style="background:#fff;border-radius:16px;padding:30px;width:100%;max-width:680px;box-shadow:0 24px 60px rgba(0,0,0,.25);margin:auto;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
                <h3 style="font-size:17px;font-weight:700;color:var(--primary);">
                    {{ $editandoId ? 'Editar Comunicado' : 'Novo Comunicado' }}
                </h3>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button wire:click="$toggle('preview')" class="btn btn-sm btn-outline">
                        {{ $preview ? 'âœï¸ Editar' : 'ðŸ‘ï¸ Preview' }}
                    </button>
                    <button wire:click="fechar" style="background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">&times;</button>
                </div>
            </div>

            @if(!$preview)
            {{-- FormulÃ¡rio --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">TÃ­tulo *</label>
                    <input wire:model="titulo" type="text" placeholder="Ex: ManutenÃ§Ã£o programada â€” 01/06 Ã s 22h"
                           style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                    @error('titulo') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Tipo *</label>
                    <select wire:model="tipo" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                        @foreach(\App\Models\Comunicado::$tipos as $val => $t)
                        <option value="{{ $val }}">{{ $t['icon'] }} {{ $t['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Prioridade *</label>
                    <select wire:model="prioridade" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                        <option value="banner">ðŸ“¢ Banner no topo</option>
                        <option value="modal">ðŸªŸ Modal ao logar</option>
                        <option value="notificacao">ðŸ”” NotificaÃ§Ã£o no sino</option>
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Destino *</label>
                    <select wire:model.live="destino" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                        <option value="todos">ðŸŒ Todos os tenants</option>
                        <option value="tenant_especifico">ðŸ¢ Tenant especÃ­fico</option>
                        <option value="plano_especifico">ðŸ“‹ Plano especÃ­fico</option>
                    </select>
                </div>

                <div>
                    @if($destino === 'tenant_especifico')
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Tenant *</label>
                    <select wire:model="tenantAlvo" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                        <option value="">Selecioneâ€¦</option>
                        @foreach($tenants as $t)
                        <option value="{{ $t->id }}">{{ $t->nome }}</option>
                        @endforeach
                    </select>
                    @error('tenantAlvo') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
                    @elseif($destino === 'plano_especifico')
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Plano *</label>
                    <select wire:model="planoAlvo" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                        <option value="">Selecioneâ€¦</option>
                        <option value="demo">Demo</option>
                        <option value="starter">Starter</option>
                        <option value="pro">Pro</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                    @error('planoAlvo') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
                    @endif
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Data inÃ­cio *</label>
                    <input wire:model="dataInicio" type="datetime-local" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                    @error('dataInicio') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Data fim (opcional)</label>
                    <input wire:model="dataFim" type="datetime-local" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                    @error('dataFim') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Mensagem *</label>
                    <textarea wire:model="mensagem" rows="4" placeholder="Descreva o comunicado em detalhesâ€¦"
                              style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;resize:vertical;font-family:inherit;"></textarea>
                    @error('mensagem') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex;align-items:center;gap:8px;">
                    <input wire:model="ativo" type="checkbox" id="ck-ativo-com" style="width:15px;height:15px;accent-color:var(--primary);">
                    <label for="ck-ativo-com" style="font-size:13px;font-weight:600;cursor:pointer;">Publicar imediatamente</label>
                </div>
            </div>

            @else
            {{-- Preview --}}
            @php $ti = \App\Models\Comunicado::$tipos[$tipo] ?? \App\Models\Comunicado::$tipos['informativo']; @endphp
            <div style="margin-bottom:14px;">
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:8px;">Preview â€” como aparece para os usuÃ¡rios</div>

                @if($prioridade === 'banner')
                <div style="padding:12px 16px;border-radius:8px;border:1px solid {{ $ti['border'] }};background:{{ $ti['bg'] }};color:{{ $ti['cor'] }};display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <span><strong>{{ $ti['icon'] }} {{ $titulo ?: '(sem tÃ­tulo)' }}</strong> â€” {{ $mensagem ?: '(sem mensagem)' }}</span>
                    <button style="background:none;border:none;cursor:pointer;font-size:16px;color:{{ $ti['cor'] }};">âœ•</button>
                </div>
                @elseif($prioridade === 'modal')
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;max-width:400px;margin:0 auto;box-shadow:0 8px 24px rgba(0,0,0,.12);">
                    <div style="font-size:28px;text-align:center;margin-bottom:10px;">{{ $ti['icon'] }}</div>
                    <div style="font-size:15px;font-weight:700;text-align:center;color:var(--primary);margin-bottom:10px;">{{ $titulo ?: '(sem tÃ­tulo)' }}</div>
                    <div style="font-size:13px;color:#475569;line-height:1.6;text-align:center;margin-bottom:16px;">{{ $mensagem ?: '(sem mensagem)' }}</div>
                    <button style="width:100%;padding:10px;background:{{ $ti['cor'] }};color:#fff;border:none;border-radius:8px;font-weight:700;">Entendido</button>
                </div>
                @else
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;display:flex;gap:10px;">
                    <span style="font-size:20px;">{{ $ti['icon'] }}</span>
                    <div>
                        <div style="font-weight:700;font-size:13px;">{{ $titulo ?: '(sem tÃ­tulo)' }}</div>
                        <div style="font-size:12px;color:#64748b;">{{ Str::limit($mensagem, 80) }}</div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid #e2e8f0;">
                <button wire:click="fechar" style="padding:9px 20px;background:#f1f5f9;color:#374151;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Cancelar</button>
                <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="salvar">ðŸ’¾ Salvar e publicar</span>
                    <span wire:loading wire:target="salvar">Salvandoâ€¦</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>

