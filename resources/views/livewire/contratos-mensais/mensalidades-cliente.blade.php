<div>
    {{-- Filtros --}}
    <div class="filter-bar" style="margin-bottom:14px;">
        <select wire:model.live="filtroAno" style="max-width:110px;">
            @foreach($anos as $ano)
            <option value="{{ $ano }}">{{ $ano }}</option>
            @endforeach
            <option value="">Todos</option>
        </select>
        <select wire:model.live="filtroStatus" style="max-width:150px;">
            <option value="">Todos status</option>
            <option value="pendente">Pendente</option>
            <option value="atrasado">Atrasado</option>
            <option value="pago">Pago</option>
            <option value="cancelado">Cancelado</option>
        </select>
    </div>

    @forelse($contratos as $contrato)
    <div class="card" style="margin-bottom:14px;padding:14px 18px;">
        {{-- Cabeçalho do contrato --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
            <div>
                <span style="font-weight:700;font-size:14px;color:var(--text);">{{ $contrato->descricao }}</span>
                <span style="font-size:12px;color:var(--muted);margin-left:10px;">{{ $contrato->periodicidade_label }} · Dia {{ $contrato->dia_vencimento }} · R$ {{ number_format($contrato->valor, 2, ',', '.') }}</span>
            </div>
            @php
                $badge = match($contrato->status) {
                    'ativo'     => ['#dcfce7','#166534','#bbf7d0'],
                    'suspenso'  => ['#fef9c3','#92400e','#fde68a'],
                    default     => ['#f1f5f9','#475569','#cbd5e1'],
                };
            @endphp
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="background:{{ $badge[0] }};color:{{ $badge[1] }};border:1px solid {{ $badge[2] }};border-radius:6px;padding:2px 8px;font-size:12px;font-weight:600;">
                    {{ $contrato->status_label }}
                </span>
                <a href="{{ route('contratos-mensais.mensalidades', $contrato->id) }}"
                    style="font-size:12px;color:var(--primary-light);text-decoration:none;font-weight:600;">
                    Ver todas →
                </a>
            </div>
        </div>

        {{-- Mensalidades --}}
        @if($contrato->mensalidades->isEmpty())
        <p style="font-size:13px;color:var(--muted);margin:0;">Nenhuma mensalidade no período.</p>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Competência</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contrato->mensalidades as $m)
                    <tr>
                        <td style="font-weight:600;">{{ $m->competencia_formatada }}</td>
                        <td>{{ $m->vencimento->format('d/m/Y') }}</td>
                        <td>R$ {{ number_format($m->valor, 2, ',', '.') }}</td>
                        <td>
                            @php
                                $bg = match($m->status) {
                                    'pago'     => ['#dcfce7','#166534','#bbf7d0'],
                                    'atrasado' => ['#fee2e2','#991b1b','#fecaca'],
                                    'pendente' => ['#eff6ff','#1e40af','#bfdbfe'],
                                    default    => ['#f1f5f9','#475569','#cbd5e1'],
                                };
                            @endphp
                            <span style="background:{{ $bg[0] }};color:{{ $bg[1] }};border:1px solid {{ $bg[2] }};border-radius:6px;padding:2px 8px;font-size:12px;font-weight:600;">
                                {{ ucfirst($m->status) }}
                            </span>
                        </td>
                        <td>{{ $m->data_pagamento ? $m->data_pagamento->format('d/m/Y') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M8 8h8M8 16h5"/></svg></div>
        <div class="empty-state-title">Nenhum contrato mensal</div>
        <div class="empty-state-sub">Este cliente não possui contratos mensais cadastrados.</div>
    </div>
    @endforelse
</div>
