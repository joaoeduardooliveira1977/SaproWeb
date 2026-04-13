<div>
@verbatim
<style>
.sla-card-resumo {
    display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:20px;
}
.sla-resumo-item {
    border-radius:10px;padding:16px 18px;display:flex;flex-direction:column;gap:4px;cursor:pointer;
    border:2px solid transparent;transition:all .15s;
}
.sla-resumo-item:hover { filter:brightness(.95); }
.sla-resumo-item.ativo { border-color:currentColor;filter:brightness(.92); }
.sla-resumo-num  { font-size:28px;font-weight:800;line-height:1; }
.sla-resumo-label{ font-size:12px;font-weight:600;opacity:.8; }

.sla-filtros {
    display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;
    background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;
    padding:14px 16px;margin-bottom:20px;
}
.sla-filtro-group { display:flex;flex-direction:column;gap:4px; }
.sla-filtro-label { font-size:11px;font-weight:600;color:#94a3b8; }

.sla-table { width:100%;border-collapse:collapse; }
.sla-table th {
    font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;
    padding:10px 12px;border-bottom:2px solid #e2e8f0;text-align:left;white-space:nowrap;
}
.sla-table td { padding:11px 12px;border-bottom:1px solid #f1f5f9;font-size:13px;vertical-align:middle; }
.sla-table tr:last-child td { border-bottom:none; }
.sla-table tr:hover td { background:#f8fafc; }

.sla-dot {
    width:10px;height:10px;border-radius:50%;flex-shrink:0;display:inline-block;margin-right:6px;
}
.sla-tipo-badge {
    display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
    border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;
}
.sla-dias-badge {
    display:inline-block;padding:3px 10px;border-radius:20px;
    font-size:11px;font-weight:700;white-space:nowrap;
}
.sla-fatal-star { color:#dc2626;font-size:13px;margin-left:2px; }

@media (max-width:640px) {
    .sla-col-cliente,.sla-col-resp { display:none; }
}
</style>
@endverbatim

{{-- ── Cards de resumo ── --}}
<div class="sla-card-resumo">

    <div class="sla-resumo-item {{ $filtroTipo==='todos' ? 'ativo' : '' }}"
        style="background:#f8fafc;color:#475569;"
        wire:click="$set('filtroTipo','todos')" title="Ver todos">
        <span class="sla-resumo-num" style="color:#1e293b;">{{ $counts['total'] }}</span>
        <span class="sla-resumo-label">Total</span>
    </div>

    <div class="sla-resumo-item"
        style="background:#fef2f2;color:#dc2626;"
        wire:click="$set('filtroTipo','todos')" title="Filtrar vencidos">
        <span class="sla-resumo-num">{{ $counts['vencido'] }}</span>
        <span class="sla-resumo-label">Vencidos</span>
    </div>

    <div class="sla-resumo-item"
        style="background:#fff7ed;color:#ea580c;"
        wire:click="$set('filtroTipo','todos')" title="Filtrar urgentes">
        <span class="sla-resumo-num">{{ $counts['urgente'] }}</span>
        <span class="sla-resumo-label">Hoje / Urgente</span>
    </div>

    <div class="sla-resumo-item"
        style="background:#fefce8;color:#ca8a04;"
        wire:click="$set('filtroTipo','todos')">
        <span class="sla-resumo-num">{{ $counts['atencao'] }}</span>
        <span class="sla-resumo-label">Até 3 dias</span>
    </div>

    <div class="sla-resumo-item"
        style="background:#f0fdf4;color:#16a34a;"
        wire:click="$set('filtroTipo','todos')">
        <span class="sla-resumo-num">{{ $counts['alerta'] + $counts['normal'] }}</span>
        <span class="sla-resumo-label">Até {{ $filtroDias }} dias</span>
    </div>

</div>

{{-- ── Barra de filtros ── --}}
<div class="sla-filtros">
    <div class="sla-filtro-group">
        <span class="sla-filtro-label">Tipo</span>
        <select wire:model.live="filtroTipo"
            style="padding:7px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;outline:none;">
            <option value="todos">Todos</option>
            <option value="prazo">Prazos</option>
            <option value="audiencia">Audiências</option>
            <option value="agenda">Agenda</option>
        </select>
    </div>
    <div class="sla-filtro-group">
        <span class="sla-filtro-label">Horizonte</span>
        <select wire:model.live="filtroDias"
            style="padding:7px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;outline:none;">
            <option value="0">Somente hoje</option>
            <option value="3">Vencidos + 3 dias</option>
            <option value="7">Vencidos + 7 dias</option>
            <option value="30">Vencidos + 30 dias</option>
        </select>
    </div>
    <div class="sla-filtro-group">
        <span class="sla-filtro-label">Responsável</span>
        <select wire:model.live="filtroResp"
            style="padding:7px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;outline:none;min-width:160px;">
            <option value="">Todos</option>
            @foreach($responsaveis as $r)
            <option value="{{ $r->id }}">{{ $r->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="sla-filtro-group" style="justify-content:flex-end;padding-bottom:2px;">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;font-weight:600;color:#dc2626;">
            <input type="checkbox" wire:model.live="filtroFatal" style="width:16px;height:16px;accent-color:#dc2626;">
            Só fatais / urgentes
        </label>
    </div>
    <div style="margin-left:auto;align-self:flex-end;padding-bottom:2px;">
        <span style="font-size:12px;color:#94a3b8;">
            <span wire:loading><span style="color:var(--primary)">Carregando…</span></span>
            <span wire:loading.remove>{{ $counts['total'] }} item{{ $counts['total'] !== 1 ? 's' : '' }}</span>
        </span>
    </div>
</div>

{{-- ── Tabela ── --}}
<div class="card" style="overflow:hidden;">
    @if($items->isEmpty())
        <div class="empty-state" style="padding:48px 0;">
            <div class="empty-state-icon">
                <svg aria-hidden="true" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="empty-state-title" style="color:#16a34a;">Tudo em dia!</div>
            <div class="empty-state-sub">Nenhum item pendente para o horizonte selecionado.</div>
        </div>
    @else
    <div style="overflow-x:auto;">
        <table class="sla-table">
            <thead>
                <tr>
                    <th style="width:8px;"></th>
                    <th>Tipo</th>
                    <th>Evento</th>
                    <th class="sla-col-cliente">Processo / Cliente</th>
                    <th>Data</th>
                    <th>Prazo</th>
                    <th class="sla-col-resp">Responsável</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
            @php
            $urgCor = match($item['urgencia']) {
                'vencido' => ['bg'=>'#fef2f2','txt'=>'#dc2626','dot'=>'#dc2626'],
                'urgente' => ['bg'=>'#fff7ed','txt'=>'#ea580c','dot'=>'#f97316'],
                'atencao' => ['bg'=>'#fefce8','txt'=>'#ca8a04','dot'=>'#eab308'],
                'alerta'  => ['bg'=>'#eff6ff','txt'=>'#2563eb','dot'=>'#3b82f6'],
                default   => ['bg'=>'#f8fafc','txt'=>'#64748b','dot'=>'#94a3b8'],
            };
            $tipoCor = match($item['tipo']) {
                'prazo'     => ['bg'=>'#fef3c7','txt'=>'#92400e','icon'=>'⏱'],
                'audiencia' => ['bg'=>'#dbeafe','txt'=>'#1e40af','icon'=>'⚖'],
                'agenda'    => ['bg'=>'#f3e8ff','txt'=>'#6d28d9','icon'=>'📅'],
                default     => ['bg'=>'#f1f5f9','txt'=>'#475569','icon'=>'•'],
            };
            $diasLabel = match(true) {
                $item['dias'] < 0  => abs($item['dias']).'d atraso',
                $item['dias'] === 0 => 'Hoje',
                $item['dias'] === 1 => 'Amanhã',
                default            => 'em '.$item['dias'].'d',
            };
            @endphp
            <tr>
                <td style="padding:0;width:5px;">
                    <div style="width:5px;height:100%;min-height:44px;background:{{ $urgCor['dot'] }};border-radius:2px 0 0 2px;"></div>
                </td>
                <td>
                    <span class="sla-tipo-badge" style="background:{{ $tipoCor['bg'] }};color:{{ $tipoCor['txt'] }};">
                        {{ $tipoCor['icon'] }} {{ ucfirst($item['tipo']) }}
                    </span>
                </td>
                <td style="max-width:260px;">
                    <span style="font-weight:600;color:#1e293b;">
                        {{ $item['titulo'] }}
                        @if($item['fatal'])<span class="sla-fatal-star" title="Fatal / Urgente">★</span>@endif
                    </span>
                    @if($item['subtipo'])
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $item['subtipo'] }}</div>
                    @endif
                </td>
                <td class="sla-col-cliente" style="max-width:200px;">
                    @if($item['processo_id'])
                    <a href="{{ route('processos.show', $item['processo_id']) }}"
                        style="color:var(--primary);font-weight:600;font-size:12px;text-decoration:none;">
                        {{ $item['processo_num'] }}
                    </a>
                    @endif
                    @if($item['cliente'])
                    <div style="font-size:11px;color:#64748b;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">
                        {{ $item['cliente'] }}
                    </div>
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    <span style="font-weight:600;color:#1e293b;">{{ $item['data_fmt'] }}</span>
                    @if($item['hora_fmt'])
                    <span style="font-size:11px;color:#94a3b8;margin-left:4px;">{{ $item['hora_fmt'] }}</span>
                    @endif
                </td>
                <td>
                    <span class="sla-dias-badge" style="background:{{ $urgCor['bg'] }};color:{{ $urgCor['txt'] }};">
                        {{ $diasLabel }}
                    </span>
                </td>
                <td class="sla-col-resp" style="font-size:12px;color:#64748b;white-space:nowrap;">
                    {{ $item['responsavel'] ?? '—' }}
                </td>
                <td>
                    @if($item['processo_id'])
                    <a href="{{ route('processos.show', $item['processo_id']) }}"
                        class="btn btn-secondary btn-sm" style="white-space:nowrap;">
                        Ver processo
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
</div>
