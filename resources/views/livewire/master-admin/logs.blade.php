<div>
@section('page-title', 'Log de Ações Master')

    {{-- ── Filtros ── --}}
    <div class="filter-bar" style="flex-wrap:wrap;gap:8px;margin-bottom:12px;">
        <input wire:model.live.debounce.300ms="busca" type="text"
               placeholder="Buscar por usuário, tenant, IP ou detalhe…" style="min-width:220px;">
        <select wire:model.live="filtroAcao">
            <option value="">Todas as ações</option>
            @foreach($acoes as $a)
            <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroContexto">
            <option value="">Todos os contextos</option>
            <option value="login_ok">Login OK</option>
            <option value="login_falha">Login Falha</option>
            <option value="logout">Logout</option>
            <option value="login_sem_permissao">Sem Permissão</option>
        </select>
        <input wire:model.live="dataInicio" type="date" title="Data início" style="width:140px;">
        <input wire:model.live="dataFim"    type="date" title="Data fim"    style="width:140px;">
        <button wire:click="exportarCsv" class="btn btn-ghost btn-sm" style="white-space:nowrap;">
            📥 Exportar CSV
        </button>
    </div>

    {{-- ── Tabela ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $logs->total() }} registro(s)</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
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
                        @php $cor = \App\Models\MasterAdminLog::badgeClass($log->acao); @endphp
                        <span class="badge {{ $cor }}" style="font-size:10px;">{{ $log->acao }}</span>
                        @if($log->contexto)
                        <span class="badge badge-gray" style="font-size:9px;margin-left:3px;">{{ $log->contexto }}</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $log->tenant_nome ?? '—' }}</td>
                    <td style="font-size:12px;color:#64748b;max-width:260px;">
                        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->detalhes }}">
                            {{ $log->detalhes ?? '—' }}
                        </div>
                    </td>
                    <td style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $log->ip ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">
                        Nenhuma ação registrada.
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
