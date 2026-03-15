<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 Agenda</span>
            <div style="display:flex;gap:8px;">
                {{-- Toggle vista --}}
                <div style="display:flex;border:1.5px solid var(--border);border-radius:8px;overflow:hidden;">
                    <button wire:click="{{ $vistaCalendario ? 'toggleVista' : '' }}"
                        style="padding:5px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;
                               background:{{ !$vistaCalendario ? '#1a3a5c' : 'transparent' }};
                               color:{{ !$vistaCalendario ? '#fff' : 'var(--muted)' }};">
                        ☰ Lista
                    </button>
                    <button wire:click="{{ !$vistaCalendario ? 'toggleVista' : '' }}"
                        style="padding:5px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;
                               background:{{ $vistaCalendario ? '#1a3a5c' : 'transparent' }};
                               color:{{ $vistaCalendario ? '#fff' : 'var(--muted)' }};">
                        📅 Calendário
                    </button>
                </div>
                <button wire:click="exportarCsv" wire:loading.attr="disabled"
                    class="btn btn-sm" style="background:#f1f5f9;color:#475569;border:1.5px solid var(--border);" title="Exportar CSV">
                    <span wire:loading.remove wire:target="exportarCsv">📥 CSV</span>
                    <span wire:loading wire:target="exportarCsv">Gerando…</span>
                </button>
                <button wire:click="abrirModal()" class="btn btn-primary btn-sm">＋ Novo Evento</button>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="search-bar" style="flex-wrap:wrap">
            @if(!$vistaCalendario)
            <input type="date" wire:model.live="data_ini" style="width:150px">
            <input type="date" wire:model.live="data_fim" style="width:150px">
            @endif
            <select wire:model.live="tipo" style="width:140px">
                <option value="">Todos os tipos</option>
                @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            <select wire:model.live="responsavel_id" style="width:160px">
                <option value="">Todos os resp.</option>
                @foreach($responsaveis as $r)
                    <option value="{{ $r->id }}">{{ $r->nome }}</option>
                @endforeach
            </select>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px">
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
                style="padding:6px 14px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;font-weight:600;">
                ← Anterior
            </button>
            <span style="font-size:16px;font-weight:700;color:#1a3a5c;">
                {{ $meses[$mesCalendario] }} {{ $anoCalendario }}
            </span>
            <button wire:click="proximoMes"
                style="padding:6px 14px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;font-weight:600;">
                Próximo →
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
        <div style="padding:0 16px 4px;">
            <span style="font-size:12px;color:#2563a8;font-weight:600;">
                Mostrando eventos de {{ \Carbon\Carbon::parse($diaSelecionado)->format('d/m/Y') }}
            </span>
            <button wire:click="selecionarDia('{{ $diaSelecionado }}')"
                style="background:none;border:none;cursor:pointer;font-size:11px;color:var(--muted);margin-left:6px;">
                × limpar
            </button>
        </div>
        @endif
        @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- LISTA (sempre visível; no calendário mostra o dia sel) --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if(!$vistaCalendario || $diaSelecionado)
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Evento</th>
                        <th>Local</th>
                        <th>Tipo</th>
                        <th>Processo</th>
                        <th>Responsável</th>
                        <th>Urgente</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $ev)
                    @php $cor = match($ev->tipo) { 'Prazo'=>'#dc2626','Audiência'=>'#d97706','Reunião'=>'#7c3aed',default=>'#2563a8' }; @endphp
                    <tr style="{{ $ev->concluido ? 'opacity:.5;' : '' }}">
                        <td>
                            <strong>{{ $ev->data_hora->format('d/m/Y') }}</strong>
                            <span style="color:var(--muted);font-size:12px;"> {{ $ev->data_hora->format('H:i') }}</span>
                        </td>
                        <td>{{ $ev->titulo }}</td>
                        <td style="font-size:12px;">{{ $ev->local ?? '—' }}</td>
                        <td><span class="badge" style="background:{{ $cor }}22;color:{{ $cor }}">{{ $ev->tipo }}</span></td>
                        <td style="font-size:12px;">{{ $ev->processo?->numero ?? '—' }}</td>
                        <td style="font-size:12px;">{{ $ev->responsavel?->pessoa?->nome ?? '—' }}</td>
                        <td>{{ $ev->urgente ? '🔴' : '—' }}</td>
                        <td>
                            @if(!$ev->concluido)
                            <button wire:click="concluir({{ $ev->id }})" class="btn-icon" title="Concluir">✅</button>
                            @endif
                            <button wire:click="abrirModal({{ $ev->id }})" class="btn-icon" title="Editar">✏️</button>
                            <button wire:click="excluir({{ $ev->id }})"
                                wire:confirm="Remover este evento?"
                                class="btn-icon" title="Excluir">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;padding:24px;">
                        {{ $diaSelecionado ? 'Nenhum evento neste dia.' : 'Nenhum evento encontrado.' }}
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $eventos->links() }}</div>
        @endif

    </div>{{-- /card --}}

    {{-- ── Modal ── --}}
    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal" style="width:520px">
            <div class="modal-header">
                <span class="modal-title">{{ $eventoId ? '✏️ Editar Evento' : '📅 Novo Evento' }}</span>
                <button wire:click="fecharModal" class="modal-close">×</button>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Título *</label>
                <input type="text" wire:model="titulo" placeholder="Descrição do evento">
                @error('titulo')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data e Hora *</label>
                    <input type="datetime-local" wire:model="data_hora">
                    @error('data_hora')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo *</label>
                    <select wire:model="tipo_evento">
                        @foreach(['Audiência','Prazo','Reunião','Consulta','Despacho','Outros'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Local</label>
                    <input type="text" wire:model="local" placeholder="Local do evento">
                </div>
                <div class="form-field">
                    <label class="lbl">Processo</label>
                    <select wire:model="processo_id">
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
                    <span>🔴 Marcar como urgente</span>
                </label>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Detalhes adicionais..."></textarea>
            </div>

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success">✓ Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
