@section('page-title', 'Tenants')
<div>

    {{-- ── Filtros ── --}}
    <div class="filter-bar">
        <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por nome, slug ou e-mail…">
        <select wire:model.live="filtroAtivo">
            <option value="">Todos os status</option>
            <option value="1">Ativos</option>
            <option value="0">Suspensos</option>
        </select>
        <select wire:model.live="filtroPlano">
            <option value="">Todos os planos</option>
            <option value="demo">Demo</option>
            <option value="starter">Starter</option>
            <option value="pro">Pro</option>
            <option value="enterprise">Enterprise</option>
        </select>
        <a href="{{ route('super-admin.criar') }}" class="btn btn-primary">+ Novo Tenant</a>
    </div>

    {{-- ── Tabela ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $tenants->total() }} tenant(s) encontrado(s)</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Plano</th>
                        <th>Status</th>
                        <th style="text-align:center;">Usuários</th>
                        <th style="text-align:center;">Processos</th>
                        <th style="text-align:center;">Pessoas</th>
                        <th>Cadastro</th>
                        <th>Último acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tenants as $t)
                <tr wire:key="{{ $t->id }}">
                    <td>
                        <div style="font-weight:600;color:#1e293b;">{{ $t->nome }}</div>
                        <div style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $t->slug }}</div>
                        @if($t->dominio)
                        <div style="font-size:11px;color:#94a3b8;">{{ $t->dominio }}</div>
                        @endif
                    </td>
                    <td>
                        @php $planoCor = ['demo'=>'badge-gray','starter'=>'badge-blue','pro'=>'badge-purple','enterprise'=>'badge-yellow'][$t->plano] ?? 'badge-gray'; @endphp
                        <span class="badge {{ $planoCor }}">{{ ucfirst($t->plano) }}</span>
                    </td>
                    <td>
                        @if($t->ativo)
                            <span class="badge badge-green">Ativo</span>
                        @else
                            <span class="badge badge-red">Suspenso</span>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $t->usuarios_count }}</td>
                    <td style="text-align:center;">{{ $t->processos_count }}</td>
                    <td style="text-align:center;">{{ $t->pessoas_count }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $t->created_at->format('d/m/Y') }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $t->ultimo_acesso ? \Carbon\Carbon::parse($t->ultimo_acesso)->diffForHumans() : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            <a href="{{ route('master-admin.tenant-show', $t->id) }}" class="btn btn-sm btn-outline">Ver</a>
                            <a href="{{ route('master-admin.tenants') }}?editar={{ $t->id }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">Branding</a>
                            <button wire:click="loginComoTenant({{ $t->id }})"
                                    wire:confirm="Entrar como administrador de {{ $t->nome }}?"
                                    class="btn btn-sm btn-primary">Entrar</button>
                            <button wire:click="toggleAtivo({{ $t->id }})"
                                    wire:confirm="{{ $t->ativo ? 'Suspender este tenant?' : 'Reativar este tenant?' }}"
                                    class="btn btn-sm {{ $t->ativo ? 'btn-danger' : 'btn-success' }}">
                                {{ $t->ativo ? 'Suspender' : 'Ativar' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:28px;color:#94a3b8;">Nenhum tenant encontrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
        <div style="padding:12px 18px;border-top:1px solid var(--border);">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>

</div>
