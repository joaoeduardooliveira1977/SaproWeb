@section('page-title', 'Log de Ações')
<div>

    <div class="filter-bar">
        <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por admin, tenant ou detalhe…">
        <select wire:model.live="filtroAcao">
            <option value="">Todas as ações</option>
            @foreach($acoes as $a)
            <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $logs->total() }} registro(s)</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Admin</th>
                        <th>Ação</th>
                        <th>Tenant</th>
                        <th>Detalhes</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                <tr wire:key="{{ $log->id }}">
                    <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td style="font-weight:600;font-size:13px;">{{ $log->admin_nome }}</td>
                    <td>
                        @php
                            $cor = match(true) {
                                str_contains($log->acao, 'suspenso')  => 'badge-red',
                                str_contains($log->acao, 'ativado')   => 'badge-green',
                                str_contains($log->acao, 'login')     => 'badge-blue',
                                str_contains($log->acao, 'senha')     => 'badge-yellow',
                                str_contains($log->acao, 'retry')     => 'badge-purple',
                                default                               => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $cor }}">{{ $log->acao }}</span>
                    </td>
                    <td style="font-size:12px;">{{ $log->tenant_nome ?? '—' }}</td>
                    <td style="font-size:12px;color:#64748b;max-width:280px;">{{ $log->detalhes ?? '—' }}</td>
                    <td style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $log->ip ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">
                        Nenhuma ação registrada ainda.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div style="padding:12px 18px;border-top:1px solid var(--border);">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
