<div>

{{-- ── Cabeçalho ─────────────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Controle de Horas</h1>
        <p style="font-size:13px;color:var(--muted);margin:4px 0 0;">Apontamentos de tempo por processo e advogado</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button wire:click="exportarCsv" class="btn btn-secondary btn-sm">
            <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Exportar CSV
        </button>
        <button wire:click="novoApontamento" class="btn btn-primary btn-sm">
            <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo apontamento
        </button>
    </div>
</div>

{{-- ── KPIs ────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;">
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Total de horas</div>
        <div style="font-size:28px;font-weight:800;color:var(--primary);margin-top:4px;">{{ number_format($totalHoras,1,'.',',') }}h</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">no período filtrado</div>
    </div>
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Valor total</div>
        <div style="font-size:28px;font-weight:800;color:#16a34a;margin-top:4px;">R$ {{ number_format($totalValor,2,',','.') }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">honorários apontados</div>
    </div>
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Registros</div>
        <div style="font-size:28px;font-weight:800;color:var(--primary);margin-top:4px;">{{ $apontamentos->count() }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">apontamentos listados</div>
    </div>
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Média por dia</div>
        @php
            $dias = max(1, \Carbon\Carbon::parse($filtroPeriodoInicio)->diffInDays(\Carbon\Carbon::parse($filtroPeriodoFim)) + 1);
            $mediaDia = $totalHoras / $dias;
        @endphp
        <div style="font-size:28px;font-weight:800;color:#7c3aed;margin-top:4px;">{{ number_format($mediaDia,1,'.',',') }}h</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">por dia no período</div>
    </div>
</div>

{{-- ── Rankings ─────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
    {{-- Por advogado --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;">
        <div style="font-size:13px;font-weight:700;color:var(--primary);margin-bottom:12px;">Horas por advogado</div>
        @php $maxAdv = $porAdvogado->max('horas') ?: 1; @endphp
        @forelse($porAdvogado as $adv)
        <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px;">
                <span style="color:#1e293b;font-weight:500;">{{ $adv['nome'] }}</span>
                <span style="color:var(--muted);">{{ number_format($adv['horas'],1,'.',',') }}h</span>
            </div>
            <div style="height:6px;background:#f1f5f9;border-radius:99px;">
                <div style="height:6px;background:#6366f1;border-radius:99px;width:{{ round($adv['horas']/$maxAdv*100) }}%;"></div>
            </div>
        </div>
        @empty
        <p style="font-size:13px;color:var(--muted);">Nenhum dado.</p>
        @endforelse
    </div>
    {{-- Por cliente --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;">
        <div style="font-size:13px;font-weight:700;color:var(--primary);margin-bottom:12px;">Horas por cliente</div>
        @php $maxCli = $porCliente->max('horas') ?: 1; @endphp
        @forelse($porCliente as $cli)
        <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px;">
                <span style="color:#1e293b;font-weight:500;">{{ $cli['nome'] }}</span>
                <span style="color:var(--muted);">{{ number_format($cli['horas'],1,'.',',') }}h</span>
            </div>
            <div style="height:6px;background:#f1f5f9;border-radius:99px;">
                <div style="height:6px;background:#0891b2;border-radius:99px;width:{{ round($cli['horas']/$maxCli*100) }}%;"></div>
            </div>
        </div>
        @empty
        <p style="font-size:13px;color:var(--muted);">Nenhum dado.</p>
        @endforelse
    </div>
</div>

{{-- ── Filtros ──────────────────────────────────────────────────── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:16px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;align-items:end;">
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">De</label>
            <input type="date" wire:model.live="filtroPeriodoInicio" class="form-control" style="font-size:13px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Até</label>
            <input type="date" wire:model.live="filtroPeriodoFim" class="form-control" style="font-size:13px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Advogado</label>
            <select wire:model.live="filtroAdvogado" class="form-control" style="font-size:13px;">
                <option value="">Todos</option>
                @foreach($advogados as $adv)
                <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Cliente</label>
            <select wire:model.live="filtroCliente" class="form-control" style="font-size:13px;">
                <option value="">Todos</option>
                @foreach($clientes as $cli)
                <option value="{{ $cli->id }}">{{ $cli->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Nº processo</label>
            <input type="text" wire:model.live.debounce.400ms="filtroProcesso" class="form-control" placeholder="Buscar..." style="font-size:13px;">
        </div>
    </div>
</div>

{{-- ── Tabela ───────────────────────────────────────────────────── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);background:#f8fafc;">
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Data</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Processo</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Cliente</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Advogado</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Descrição</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Horas</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">Valor</th>
                    <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($apontamentos as $ap)
                <tr style="border-bottom:1px solid var(--border);" wire:key="ap-{{ $ap->id }}">
                    <td style="padding:10px 16px;white-space:nowrap;color:var(--muted);">
                        {{ \Carbon\Carbon::parse($ap->data)->format('d/m/Y') }}
                    </td>
                    <td style="padding:10px 16px;white-space:nowrap;">
                        <a href="{{ route('processos.show', $ap->processo_id) }}"
                           style="color:var(--primary);font-weight:600;text-decoration:none;font-size:12px;">
                            {{ $ap->processo_numero }}
                        </a>
                    </td>
                    <td style="padding:10px 16px;max-width:160px;">
                        <span style="color:#1e293b;">{{ $ap->cliente_nome ?? '—' }}</span>
                    </td>
                    <td style="padding:10px 16px;white-space:nowrap;color:#475569;">
                        {{ $ap->advogado_nome ?? '—' }}
                    </td>
                    <td style="padding:10px 16px;max-width:260px;">
                        <span style="color:#1e293b;line-height:1.4;">{{ $ap->descricao }}</span>
                    </td>
                    <td style="padding:10px 16px;text-align:right;font-weight:700;color:#6366f1;white-space:nowrap;">
                        {{ number_format($ap->horas, 2, ',', '.') }}h
                    </td>
                    <td style="padding:10px 16px;text-align:right;font-weight:600;color:#16a34a;white-space:nowrap;">
                        @if($ap->valor)
                            R$ {{ number_format($ap->valor, 2, ',', '.') }}
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 16px;text-align:center;white-space:nowrap;">
                        <button wire:click="editar({{ $ap->id }})"
                                style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;"
                                title="Editar">
                            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button wire:click="excluir({{ $ap->id }})"
                                wire:confirm="Excluir este apontamento?"
                                style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px;"
                                title="Excluir">
                            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:48px;text-align:center;color:var(--muted);">
                        Nenhum apontamento encontrado para o período.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($apontamentos->count() > 0)
            <tfoot>
                <tr style="background:#f8fafc;border-top:2px solid var(--border);">
                    <td colspan="5" style="padding:10px 16px;font-weight:700;font-size:13px;color:var(--primary);">Total</td>
                    <td style="padding:10px 16px;text-align:right;font-weight:800;color:#6366f1;font-size:14px;">
                        {{ number_format($totalHoras, 2, ',', '.') }}h
                    </td>
                    <td style="padding:10px 16px;text-align:right;font-weight:800;color:#16a34a;font-size:14px;">
                        R$ {{ number_format($totalValor, 2, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ── Modal novo/editar ───────────────────────────────────────── --}}
@if($modal)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;"
     wire:click.self="fecharModal">
    <div style="background:var(--white);border-radius:16px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;color:var(--primary);margin:0;">
                {{ $editandoId ? 'Editar apontamento' : 'Novo apontamento' }}
            </h3>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px 24px;display:flex;flex-direction:column;gap:14px;">

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Processo *</label>
                <select wire:model="opProcesso" class="form-control" style="font-size:13px;">
                    <option value="">Selecione...</option>
                    @foreach($processos as $pr)
                    <option value="{{ $pr->id }}">{{ $pr->numero }}</option>
                    @endforeach
                </select>
                @error('opProcesso')<p style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Data *</label>
                    <input type="date" wire:model="opData" class="form-control" style="font-size:13px;">
                    @error('opData')<p style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Advogado</label>
                    <select wire:model="opAdvogado" class="form-control" style="font-size:13px;">
                        <option value="">Nenhum</option>
                        @foreach($advogados as $adv)
                        <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Horas *</label>
                    <input type="number" wire:model="opHoras" step="0.25" min="0.01" class="form-control"
                           placeholder="Ex: 1.50" style="font-size:13px;">
                    @error('opHoras')<p style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Valor (R$)</label>
                    <input type="number" wire:model="opValor" step="0.01" min="0" class="form-control"
                           placeholder="Opcional" style="font-size:13px;">
                </div>
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Descrição *</label>
                <textarea wire:model="opDescricao" rows="3" class="form-control"
                          placeholder="Descreva a atividade realizada..." style="font-size:13px;resize:vertical;"></textarea>
                @error('opDescricao')<p style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="fecharModal" class="btn btn-secondary btn-sm">Cancelar</button>
            <button wire:click="salvar" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                <span wire:loading wire:target="salvar">Salvando...</span>
                <span wire:loading.remove wire:target="salvar">Salvar</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
