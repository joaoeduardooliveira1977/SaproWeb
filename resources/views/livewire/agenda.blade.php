<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title" style="display:flex;align-items:center;gap:8px;">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Agenda
            </span>
            <div class="card-actions">
                {{-- Toggle vista --}}
                <div style="display:flex;border:1.5px solid var(--border);border-radius:8px;overflow:hidden;">
                    <button wire:click="{{ $vistaCalendario ? 'toggleVista' : '' }}"
                        style="padding:5px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;gap:5px;
                               background:{{ !$vistaCalendario ? 'var(--primary)' : 'transparent' }};
                               color:{{ !$vistaCalendario ? '#fff' : 'var(--muted)' }};">
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        Lista
                    </button>
                    <button wire:click="{{ !$vistaCalendario ? 'toggleVista' : '' }}"
                        style="padding:5px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;gap:5px;
                               background:{{ $vistaCalendario ? 'var(--primary)' : 'transparent' }};
                               color:{{ $vistaCalendario ? '#fff' : 'var(--muted)' }};">
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Cal.
                    </button>
                </div>
                <button wire:click="exportarCsv" wire:loading.attr="disabled"
                    class="btn btn-sm btn-secondary-outline hide-xs" title="Exportar CSV">
                    <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        CSV
                    </span>
                    <span wire:loading wire:target="exportarCsv">Gerando…</span>
                </button>
                <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Novo
                </button>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="filter-bar" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding:12px 16px;border-bottom:1px solid var(--border);">
            @if(!$vistaCalendario)
            <input type="date" wire:model.live="data_ini"
                style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
            <input type="date" wire:model.live="data_fim"
                style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
            @endif
            <select wire:model.live="tipo"
                style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
                <option value="">Todos os tipos</option>
                @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            <select wire:model.live="responsavel_id"
                style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
                <option value="">Todos os resp.</option>
                @foreach($responsaveis as $r)
                    <option value="{{ $r->id }}">{{ $r->nome }}</option>
                @endforeach
            </select>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;flex-shrink:0;">
                <input type="checkbox" wire:model.live="so_pendentes" style="width:auto"> Só pendentes
            </label>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- VISTA: CALENDÁRIO                                      --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if($vistaCalendario)
        @php
            $meses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                      7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
            $diasSemana = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];

            // Ajuste: semana começa em domingo (0) — Carbon usa 0=Sunday
            $primeiroDia  = $inicioMes->copy()->startOfMonth();
            $ultimoDia    = $inicioMes->copy()->endOfMonth();
            $inicioCelula = $primeiroDia->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $hoje         = today()->format('Y-m-d');

            $coresTipo = ['Prazo'=>'#dc2626','Audiência'=>'#d97706','Reunião'=>'#7c3aed',
                          'Consulta'=>'#0891b2','Despacho'=>'#16a34a','Outros'=>'#2563a8'];
        @endphp

        {{-- Navegação de mês --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);">
            <button wire:click="mesAnterior"
                style="padding:6px 14px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Anterior
            </button>
            <span style="font-size:16px;font-weight:700;color:var(--primary);">
                {{ $meses[$mesCalendario] }} {{ $anoCalendario }}
            </span>
            <button wire:click="proximoMes"
                style="padding:6px 14px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                Próximo
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        {{-- Grade do calendário --}}
        <div style="padding:12px 16px;">

            {{-- Cabeçalho dias da semana --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:4px;">
                @foreach($diasSemana as $ds)
                <div style="text-align:center;font-size:11px;font-weight:700;color:var(--muted);padding:4px 0;text-transform:uppercase;">
                    {{ $ds }}
                </div>
                @endforeach
            </div>

            {{-- Células --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;">
                @php $cursor = $inicioCelula->copy(); @endphp
                @while($cursor->lte($ultimoDia) || $cursor->dayOfWeek !== 0)
                @php
                    $dataStr   = $cursor->format('Y-m-d');
                    $doMes     = $cursor->month === $inicioMes->month;
                    $isHoje    = $dataStr === $hoje;
                    $isSel     = $dataStr === $diaSelecionado;
                    $evsDia    = $eventosMes->get($dataStr, collect());
                    $qtd       = $evsDia->count();
                @endphp
                <div wire:click="selecionarDia('{{ $dataStr }}')"
                    style="min-height:72px;padding:6px;border-radius:8px;cursor:pointer;
                           border:2px solid {{ $isSel ? '#2563a8' : ($isHoje ? '#93c5fd' : 'transparent') }};
                           background:{{ $isSel ? '#eff6ff' : ($isHoje ? '#f0f9ff' : ($doMes ? '#fff' : '#f8fafc')) }};
                           box-shadow:{{ $doMes ? '0 1px 3px rgba(0,0,0,.06)' : 'none' }};
                           transition:all .12s;"
                    onmouseover="this.style.background='#eff6ff'"
                    onmouseout="this.style.background='{{ $isSel ? '#eff6ff' : ($isHoje ? '#f0f9ff' : ($doMes ? '#fff' : '#f8fafc')) }}'">

                    <div style="font-size:13px;font-weight:{{ $isHoje ? '800' : '600' }};
                                color:{{ !$doMes ? '#cbd5e1' : ($isHoje ? '#2563a8' : '#1e293b') }};
                                margin-bottom:4px;">
                        {{ $cursor->day }}
                        @if($isHoje)<span style="font-size:9px;background:#2563a8;color:#fff;border-radius:10px;padding:0 5px;margin-left:3px;">hoje</span>@endif
                    </div>

                    {{-- Pontos/labels de eventos --}}
                    @if($qtd > 0 && $doMes)
                    <div style="display:flex;flex-direction:column;gap:2px;">
                        @foreach($evsDia->take(3) as $ev)
                        @php $cor = $coresTipo[$ev->tipo] ?? '#2563a8'; @endphp
                        <div style="font-size:10px;font-weight:600;color:#fff;
                                    background:{{ $cor }};border-radius:3px;padding:1px 5px;
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                             title="{{ $ev->titulo }}">
                            {{ mb_strimwidth($ev->titulo, 0, 16, '…') }}
                        </div>
                        @endforeach
                        @if($qtd > 3)
                        <div style="font-size:10px;color:var(--muted);font-weight:600;padding:0 2px;">
                            +{{ $qtd - 3 }} mais
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
                @php $cursor->addDay(); @endphp
                @endwhile
            </div>
        </div>

        {{-- Legenda --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;padding:8px 16px 12px;border-top:1px solid var(--border);">
            @foreach($coresTipo as $tipo => $cor)
            <span style="display:flex;align-items:center;gap:4px;font-size:11px;color:#64748b;">
                <span style="width:10px;height:10px;border-radius:2px;background:{{ $cor }};display:inline-block;"></span>
                {{ $tipo }}
            </span>
            @endforeach
        </div>

        @if($diaSelecionado)
        <div style="padding:0 16px 4px;display:flex;align-items:center;gap:8px;">
            <span style="font-size:12px;color:#2563a8;font-weight:600;">
                Mostrando eventos de {{ \Carbon\Carbon::parse($diaSelecionado)->format('d/m/Y') }}
            </span>
            <button wire:click="selecionarDia('{{ $diaSelecionado }}')"
                style="background:none;border:none;cursor:pointer;font-size:11px;color:var(--muted);display:flex;align-items:center;gap:3px;">
                <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                limpar
            </button>
        </div>
        @endif
        @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- LISTA (sempre visível; no calendário mostra o dia sel) --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if(!$vistaCalendario || $diaSelecionado)
        <div class="table-wrap">
            <table style="border-collapse:collapse;width:100%;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);">
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Data/Hora</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Evento</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Local</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Processo</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Responsável</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;">Urgente</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:110px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $ev)
                    @php $cor = match($ev->tipo) { 'Prazo'=>'#dc2626','Audiência'=>'#d97706','Reunião'=>'#7c3aed',default=>'#2563a8' }; @endphp
                    <tr style="border-bottom:1px solid var(--border);transition:background .15s;{{ $ev->concluido ? 'opacity:.5;' : '' }}"
                        onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px;">{{ $ev->data_hora->format('d/m/Y') }}</strong>
                            <span style="color:var(--muted);font-size:12px;"> {{ $ev->data_hora->format('H:i') }}</span>
                        </td>
                        <td style="padding:14px 16px;font-size:13px;">{{ $ev->titulo }}</td>
                        <td style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $ev->local ?? '—' }}</td>
                        <td style="padding:14px 16px;">
                            <span class="badge" style="background:{{ $cor }}22;color:{{ $cor }}">{{ $ev->tipo }}</span>
                        </td>
                        <td style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $ev->processo?->numero ?? '—' }}</td>
                        <td style="padding:14px 16px;font-size:12px;color:var(--muted);">{{ $ev->responsavel?->pessoa?->nome ?? '—' }}</td>
                        <td style="padding:14px 16px;text-align:center;">
                            @if($ev->urgente)
                                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="#dc2626" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;text-align:center;">
                            <div style="display:flex;justify-content:center;gap:4px;">
                                @if(!$ev->concluido)
                                <button wire:click="concluir({{ $ev->id }})" title="Concluir"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                                @endif
                                <button wire:click="abrirModal({{ $ev->id }})" title="Editar"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button wire:click="excluir({{ $ev->id }})"
                                    wire:confirm="Remover este evento?"
                                    title="Excluir"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--muted);padding:48px;">
                            <svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <div style="font-size:14px;font-weight:500;">{{ $diaSelecionado ? 'Nenhum evento neste dia.' : 'Nenhum evento encontrado.' }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
            <span style="font-size:13px;color:var(--muted);">
                @if($eventos->total() > 0)
                    Mostrando {{ $eventos->firstItem() }}–{{ $eventos->lastItem() }} de {{ $eventos->total() }}
                @else
                    Nenhum resultado
                @endif
            </span>
            <div style="display:flex;align-items:center;gap:6px;">
                <button wire:click="previousPage" @disabled($eventos->onFirstPage())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $eventos->onFirstPage() ? '.4' : '1' }};">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Anterior
                </button>
                <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                    {{ $eventos->currentPage() }} / {{ $eventos->lastPage() }}
                </span>
                <button wire:click="nextPage" @disabled(!$eventos->hasMorePages())
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $eventos->hasMorePages() ? '1' : '.4' }};">
                    Próxima
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
        @endif

    </div>{{-- /card --}}

    {{-- ── Modal ── --}}
    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal" style="max-width:520px">
            <div class="modal-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                        @if($eventoId)
                            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        @else
                            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        @endif
                    </div>
                    <span class="modal-title">{{ $eventoId ? 'Editar Evento' : 'Novo Evento' }}</span>
                </div>
                <button wire:click="fecharModal" class="modal-close" aria-label="Fechar">
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            @php
            $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
            $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
            @endphp

            {{-- Seção: Dados do Evento --}}
            <div style="{{ $sec }}">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Dados do Evento
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Título *</label>
                <input type="text" wire:model="titulo" placeholder="Descrição do evento" style="{{ $inp }}">
                @error('titulo')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data e Hora *</label>
                    <input type="datetime-local" wire:model="data_hora" style="{{ $inp }}">
                    @error('data_hora')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo *</label>
                    <select wire:model="tipo_evento" style="{{ $inp }}">
                        @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Local</label>
                    <input type="text" wire:model="local" placeholder="Local do evento" style="{{ $inp }}">
                </div>
                <div class="form-field">
                    <label class="lbl">Processo</label>
                    <select wire:model="processo_id" style="{{ $inp }}">
                        <option value="">Nenhum</option>
                        @foreach($processos as $proc)
                            <option value="{{ $proc->id }}">{{ $proc->numero }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                    <input type="checkbox" wire:model="urgente" style="width:auto">
                    <span style="display:flex;align-items:center;gap:5px;">
                        <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="#dc2626" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                        Marcar como urgente
                    </span>
                </label>
            </div>

            {{-- Seção: Observações --}}
            <div style="{{ $sec }}border-top:1px solid var(--border);padding-top:16px;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Observações
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <textarea wire:model="observacoes" rows="2" placeholder="Detalhes adicionais..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
            </div>

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success" style="display:flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
