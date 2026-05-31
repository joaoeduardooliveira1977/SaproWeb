@section('page-title', $tenant->nome)
<div>

    {{-- ── Cabeçalho ── --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('master.tenants') }}" class="btn btn-sm btn-ghost">← Tenants</a>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;flex:1;">{{ $tenant->nome }}</h2>
        @if($tenant->ativo)
            <span class="badge badge-green" style="font-size:12px;">● Ativo</span>
        @else
            <span class="badge badge-red" style="font-size:12px;">● Suspenso</span>
        @endif
        <button wire:click="loginComoTenant"
                wire:confirm="Entrar como administrador de {{ $tenant->nome }}?"
                class="btn btn-sm btn-primary">
            🔑 Entrar como
        </button>
        <button wire:click="toggleAtivo"
                wire:confirm="{{ $tenant->ativo ? 'Suspender este tenant?' : 'Reativar este tenant?' }}"
                class="btn btn-sm {{ $tenant->ativo ? '' : 'btn-success' }}"
                style="{{ $tenant->ativo ? 'background:#f1f5f9;color:#374151;' : '' }}">
            {{ $tenant->ativo ? '⏸ Suspender' : '▶ Reativar' }}
        </button>
        <button wire:click="abrirModalExcluir" class="btn btn-sm btn-danger">
            🗑️ Lixeira
        </button>
    </div>

    {{-- ── Tabs ── --}}
    <div class="tabs">
        <button class="tab {{ $aba === 'geral' ? 'active' : '' }}" wire:click="mudarAba('geral')">🏢 Geral</button>
        <button class="tab {{ $aba === 'usuarios' ? 'active' : '' }}" wire:click="mudarAba('usuarios')">👥 Usuários</button>
        <button class="tab {{ $aba === 'atividade' ? 'active' : '' }}" wire:click="mudarAba('atividade')">📊 Atividade</button>
        <button class="tab {{ $aba === 'branding' ? 'active' : '' }}" wire:click="mudarAba('branding')">🎨 Branding</button>
    </div>

    {{-- ── Aba: Geral ── --}}
    @if($aba === 'geral')
    <div>

        {{-- Métricas --}}
        <div class="stat-grid" style="margin-bottom:20px;">
            @foreach([
                ['Processos',  $metricas['processos'],  'blue'],
                ['Pessoas',    $metricas['pessoas'],    'green'],
                ['Usuários',   $metricas['usuarios'],   ''],
                ['Prazos',     $metricas['prazos'],     'warn'],
                ['Documentos', $metricas['documentos'], ''],
                ['Pub. AASP',  $metricas['publicacoes'], 'gold'],
            ] as [$label, $val, $cor])
            <div class="stat-card {{ $cor }}">
                <div class="stat-val">{{ number_format($val) }}</div>
                <div class="stat-label">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            {{-- Informações gerais --}}
            <div class="card">
                <div class="card-header"><span class="card-title">Informações Gerais</span></div>
                <div class="card-body">
                    <table style="font-size:13px;width:100%;">
                        <tbody>
                        @foreach([
                            ['Slug',     $tenant->slug],
                            ['Plano',    ucfirst($tenant->plano)],
                            ['Domínio',  $tenant->dominio ?: '—'],
                            ['E-mail',   $tenant->email ?? '—'],
                            ['CNPJ',     $tenant->cnpj ?? '—'],
                            ['Cidade',   $tenant->cidade ?? '—'],
                            ['Cadastro', $tenant->created_at->format('d/m/Y H:i')],
                        ] as [$label, $val])
                        <tr>
                            <td style="padding:7px 0;color:#64748b;width:110px;font-weight:600;border-bottom:1px solid #f1f5f9;">{{ $label }}</td>
                            <td style="padding:7px 0;border-bottom:1px solid #f1f5f9;">{{ $val }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recursos --}}
            <div class="card">
                <div class="card-header"><span class="card-title">Recursos e Limites</span></div>
                <div class="card-body">
                    <table style="font-size:13px;width:100%;">
                        <tbody>
                        @foreach([
                            ['IA habilitada',     $tenant->ia_habilitada ? '✅ Sim' : '❌ Não'],
                            ['DataJud',           $tenant->datajud_habilitado ? '✅ Sim' : '❌ Não'],
                            ['WhatsApp',          $tenant->whatsapp_habilitado ? '✅ Sim' : '❌ Não'],
                            ['Limite processos',  $tenant->limite_processos ?: 'Ilimitado'],
                            ['Limite usuários',   $tenant->limite_usuarios ?: 'Ilimitado'],
                            ['Trial expira em',   $tenant->trial_expira_em ? $tenant->trial_expira_em->format('d/m/Y') : '—'],
                        ] as [$label, $val])
                        <tr>
                            <td style="padding:7px 0;color:#64748b;width:150px;font-weight:600;border-bottom:1px solid #f1f5f9;">{{ $label }}</td>
                            <td style="padding:7px 0;border-bottom:1px solid #f1f5f9;">{{ $val }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div style="margin-top:14px;padding:12px;background:#f8fafc;border-radius:8px;text-align:center;">
                        <div style="font-size:20px;font-weight:800;color:#1a3a5c;">{{ $metricas['disco_mb'] }} MB</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">💾 Uso de disco (storage)</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif

    {{-- ── Aba: Usuários ── --}}
    @if($aba === 'usuarios')
    <div class="card">
        <div class="card-header">
            <span class="card-title">Usuários do Tenant ({{ count($usuarios) }})</span>
            <button wire:click="loginComoTenant" class="btn btn-sm btn-primary">
                🔑 Entrar como admin
            </button>
        </div>
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
                    <td style="font-size:12px;color:#64748b;">
                        {{ $u->email }}<br>
                        <span style="font-family:monospace;color:#94a3b8;">{{ $u->login }}</span>
                    </td>
                    <td>
                        @php
                            $perfilCor = ['administrador'=>'badge-blue','advogado'=>'badge-purple','financeiro'=>'badge-green','estagiario'=>'badge-gray','recepcionista'=>'badge-yellow'][$u->perfil] ?? 'badge-gray';
                        @endphp
                        <span class="badge {{ $perfilCor }}">{{ $u->perfil }}</span>
                    </td>
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
                        <button wire:click="abrirModalSenha({{ $u->id }})"
                                class="btn btn-sm"
                                style="background:#fef3c7;color:#92400e;">
                            🔑 Reset senha
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:28px;color:#94a3b8;">Nenhum usuário cadastrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Aba: Atividade ── --}}
    @if($aba === 'atividade')
    <div class="card">
        <div class="card-header"><span class="card-title">Últimas 20 atividades</span></div>
        @if(count($auditoria))
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Ação</th>
                        <th>Tabela</th>
                        <th>Usuário</th>
                        <th>IP</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($auditoria as $a)
                <tr>
                    <td><span class="badge badge-blue" style="font-size:10px;">{{ $a->acao }}</span></td>
                    <td style="font-size:12px;color:#64748b;font-family:monospace;">{{ $a->tabela ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $a->usuario_nome ?? $a->login ?? '—' }}</td>
                    <td style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $a->ip ?? '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="card-body" style="text-align:center;color:#94a3b8;padding:32px;">
            Nenhum registro de atividade encontrado.
        </div>
        @endif
    </div>
    @endif

    {{-- ── Aba: Branding ── --}}
    @if($aba === 'branding')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div class="card">
            <div class="card-header"><span class="card-title">Identidade Visual</span></div>
            <div class="card-body">

                <div style="margin-bottom:20px;">
                    <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Logo</div>
                    @if($tenant->logo && file_exists(storage_path('app/public/' . $tenant->logo)))
                        <img src="{{ asset('storage/' . $tenant->logo) }}"
                             alt="{{ $tenant->nome }}"
                             style="max-width:180px;max-height:90px;object-fit:contain;border:1px solid #e2e8f0;border-radius:10px;padding:8px;">
                    @else
                        <div style="width:120px;height:60px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;">
                            Sem logo
                        </div>
                    @endif
                </div>

                <div style="display:flex;gap:16px;">
                    @foreach([
                        ['Cor Primária', $tenant->cor_primaria],
                        ['Cor Secundária', $tenant->cor_secundaria],
                    ] as [$label, $cor])
                    @if($cor)
                    <div>
                        <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">{{ $label }}</div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;background:{{ $cor }};border-radius:8px;border:1px solid #e2e8f0;flex-shrink:0;"></div>
                            <span style="font-size:13px;font-weight:600;font-family:monospace;">{{ $cor }}</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Preview do Login</span></div>
            <div class="card-body">
                <div style="background:{{ $tenant->cor_primaria ?? '#1a3a5c' }};border-radius:10px;padding:20px;text-align:center;">
                    @if($tenant->logo && file_exists(storage_path('app/public/' . $tenant->logo)))
                        <img src="{{ asset('storage/' . $tenant->logo) }}"
                             style="max-width:80px;max-height:40px;object-fit:contain;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto;">
                    @else
                        <div style="font-size:28px;margin-bottom:8px;">⚖️</div>
                    @endif
                    <div style="color:#fff;font-size:14px;font-weight:800;letter-spacing:2px;text-transform:uppercase;">
                        {{ strtoupper($tenant->nome) }}
                    </div>
                </div>
                <div style="margin-top:12px;padding:10px 14px;background:#f8fafc;border-radius:8px;font-size:12px;color:#64748b;">
                    Para editar o branding, acesse o painel admin do tenant via
                    <strong>Entrar como</strong> e vá em Admin-Hub → Identidade Visual.
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ── Modal: Excluir para lixeira ── --}}
    @if($modalExcluir)
    <div class="modal-overlay" wire:click.self="fecharModalExcluir">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header" style="border-bottom-color:#fecaca;">
                <span class="modal-title" style="color:#dc2626;">🗑️ Mover para a Lixeira</span>
                <button class="modal-close" wire:click="fecharModalExcluir">✕</button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px;color:#374151;margin-bottom:16px;">
                    Você está movendo <strong>{{ $tenant->nome }}</strong> para a lixeira.
                    Todos os usuários serão deslogados automaticamente.
                </p>
                <div class="fg">
                    <label>Motivo da exclusão *</label>
                    <textarea wire:model="motivoExclusao" rows="3"
                              placeholder="Descreva o motivo (mínimo 10 caracteres)…"></textarea>
                    @error('motivoExclusao') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="fecharModalExcluir" class="btn btn-outline">Cancelar</button>
                <button wire:click="excluirParaLixeira" wire:loading.attr="disabled" class="btn btn-danger">
                    Mover para Lixeira
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal: Reset Senha ── --}}
    @if($modalSenha)
    <div class="modal-overlay" wire:click.self="$set('modalSenha', false)">
        <div class="modal" style="max-width:380px;">
            <div class="modal-header">
                <span class="modal-title">🔑 Redefinir Senha</span>
                <button class="modal-close" wire:click="$set('modalSenha', false)">✕</button>
            </div>
            <div class="modal-body">
                <div class="fg">
                    <label>Nova Senha</label>
                    <input wire:model="novaSenha" type="password" placeholder="Mínimo 8 caracteres">
                    @error('novaSenha') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('modalSenha', false)" class="btn btn-outline">Cancelar</button>
                <button wire:click="resetarSenha" class="btn btn-primary">Redefinir</button>
            </div>
        </div>
    </div>
    @endif

</div>
