@section('page-title', $tenant->nome)
<div>

    {{-- ── Cabeçalho ── --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
        <a href="{{ route('master-admin.tenants') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">← Tenants</a>
        <h2 style="font-size:18px;font-weight:700;color:var(--primary);flex:1;">{{ $tenant->nome }}</h2>
        <button wire:click="loginComoTenant" wire:confirm="Entrar como administrador de {{ $tenant->nome }}?"
                class="btn btn-primary btn-sm">🔑 Entrar como</button>
        <button wire:click="toggleAtivo" wire:confirm="{{ $tenant->ativo ? 'Suspender este tenant?' : 'Reativar este tenant?' }}"
                class="btn btn-sm {{ $tenant->ativo ? 'btn-danger' : 'btn-success' }}">
            {{ $tenant->ativo ? '⏸ Suspender' : '▶ Reativar' }}
        </button>
        <a href="{{ route('master-admin.tenants') }}?branding={{ $tenant->id }}" class="btn btn-sm btn-outline">🎨 Branding</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

        {{-- ── Informações gerais ── --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Informações Gerais</span></div>
            <div class="card-body">
                <table style="font-size:13px;">
                    <tbody>
                    @foreach([
                        ['Slug',      $tenant->slug],
                        ['Plano',     ucfirst($tenant->plano)],
                        ['Status',    $tenant->ativo ? '✅ Ativo' : '🔴 Suspenso'],
                        ['Domínio',   $tenant->dominio ?: '—'],
                        ['E-mail',    $tenant->email ?? '—'],
                        ['Cadastro',  $tenant->created_at->format('d/m/Y H:i')],
                    ] as [$label, $val])
                    <tr>
                        <td style="padding:7px 0;color:#64748b;width:120px;font-weight:600;">{{ $label }}</td>
                        <td style="padding:7px 0;">{{ $val }}</td>
                    </tr>
                    @endforeach
                    @if($tenant->cor_primaria)
                    <tr>
                        <td style="padding:7px 0;color:#64748b;font-weight:600;">Cor primária</td>
                        <td style="padding:7px 0;display:flex;align-items:center;gap:8px;">
                            <span style="display:inline-block;width:18px;height:18px;background:{{ $tenant->cor_primaria }};border-radius:4px;border:1px solid #e2e8f0;"></span>
                            {{ $tenant->cor_primaria }}
                        </td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Métricas ── --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Métricas de Uso</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    @foreach([
                        ['📁 Processos',  $metricas['processos']],
                        ['👥 Pessoas',    $metricas['pessoas']],
                        ['👤 Usuários',   $metricas['usuarios']],
                        ['⏰ Prazos',     $metricas['prazos']],
                        ['📄 Documentos', $metricas['documentos']],
                        ['📰 Pub. AASP',  $metricas['publicacoes']],
                    ] as [$label, $val])
                    <div style="background:#f8fafc;border-radius:8px;padding:12px;text-align:center;">
                        <div style="font-size:20px;font-weight:700;color:var(--primary);">{{ number_format($val) }}</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:10px;padding:10px;background:#f8fafc;border-radius:8px;text-align:center;">
                    <span style="font-size:14px;font-weight:600;color:var(--primary);">{{ $metricas['disco_mb'] }} MB</span>
                    <span style="font-size:11px;color:#64748b;display:block;">💾 Disco (storage/tenants)</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Usuários ── --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header"><span class="card-title">Usuários do Tenant</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail / Login</th>
                        <th>Perfil</th>
                        <th>Último acesso</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($usuarios as $u)
                <tr>
                    <td style="font-weight:600;">{{ $u->nome }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $u->email }}</td>
                    <td><span class="badge badge-blue">{{ $u->perfil }}</span></td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $u->ultimo_acesso ? \Carbon\Carbon::parse($u->ultimo_acesso)->diffForHumans() : '—' }}
                    </td>
                    <td>
                        @if($u->ativo)
                            <span class="badge badge-green">Ativo</span>
                        @else
                            <span class="badge badge-red">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <button wire:click="abrirModalSenha({{ $u->id }})" class="btn btn-sm" style="background:#fef3c7;color:#92400e;">
                            🔑 Reset senha
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Nenhum usuário cadastrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Atividade recente ── --}}
    @if(count($auditoria))
    <div class="card">
        <div class="card-header"><span class="card-title">Atividade Recente</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Ação</th><th>Descrição</th><th>Usuário</th><th>Data</th></tr></thead>
                <tbody>
                @foreach($auditoria as $a)
                <tr>
                    <td><span class="badge badge-blue">{{ $a->acao }}</span></td>
                    <td style="font-size:12px;color:#64748b;max-width:300px;">{{ Str::limit($a->descricao ?? '—', 80) }}</td>
                    <td style="font-size:12px;">{{ $a->usuario_nome ?? '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Modal Reset Senha ── --}}
    @if($modalSenha)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:flex;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:12px;padding:28px;width:380px;box-shadow:0 20px 60px rgba(0,0,0,.25);">
            <h3 style="font-size:16px;font-weight:700;color:var(--primary);margin-bottom:16px;">🔑 Redefinir Senha</h3>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:6px;">Nova Senha</label>
                <input wire:model="novaSenha" type="password" placeholder="Mínimo 8 caracteres"
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;">
                @error('novaSenha') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="$set('modalSenha', false)" class="btn" style="background:#f1f5f9;color:#374151;">Cancelar</button>
                <button wire:click="resetarSenha" class="btn btn-primary">Redefinir</button>
            </div>
        </div>
    </div>
    @endif

</div>
