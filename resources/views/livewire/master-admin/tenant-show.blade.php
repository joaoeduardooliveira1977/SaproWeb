<div>
@section('page-title', $tenant->nome)

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
        <button class="tab {{ $aba === 'limites' ? 'active' : '' }}" wire:click="mudarAba('limites')">📊 Limites</button>
        <button class="tab {{ $aba === 'usuarios' ? 'active' : '' }}" wire:click="mudarAba('usuarios')">👥 Usuários</button>
        <button class="tab {{ $aba === 'atividade' ? 'active' : '' }}" wire:click="mudarAba('atividade')">📋 Atividade</button>
        <button class="tab {{ $aba === 'branding' ? 'active' : '' }}" wire:click="mudarAba('branding')">🎨 Branding</button>
        <button class="tab {{ $aba === 'modulos' ? 'active' : '' }}" wire:click="mudarAba('modulos')">🧩 Módulos</button>
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

    {{-- ── Aba: Limites e Uso ── --}}
    @if($aba === 'limites')
    @php
        $limUsuarios  = $tenant->limite_usuarios  ?? 5;
        $limProcessos = $tenant->limite_processos ?? 100;
        $limStorage   = $tenant->limite_storage_mb ?? 500;
        $usoUsuarios  = $metricas['usuarios'];
        $usoProcessos = $metricas['processos'];
        $usoStorage   = $metricas['disco_mb'];
        $pctUsr = $limUsuarios  ? min(100, round(($usoUsuarios  / $limUsuarios)  * 100)) : 0;
        $pctPrc = $limProcessos ? min(100, round(($usoProcessos / $limProcessos) * 100)) : 0;
        $pctStg = $limStorage   ? min(100, round(($usoStorage   / $limStorage)   * 100)) : 0;
        $corPct = fn($p) => $p >= 90 ? '#dc2626' : ($p >= 70 ? '#d97706' : '#16a34a');
    @endphp

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">Limites e Uso — Plano {{ ucfirst($tenant->plano) }}</span>
            <button wire:click="abrirModalLimites" class="btn btn-sm btn-outline">✏️ Editar Limites</button>
        </div>
        <div class="card-body">

            @foreach([
                ['Usuários',        $usoUsuarios,  $limUsuarios,  $pctUsr, 'usuários'],
                ['Processos',       $usoProcessos, $limProcessos, $pctPrc, 'processos'],
                ['Storage (MB)',    $usoStorage,   $limStorage,   $pctStg, 'MB usados'],
            ] as [$label, $uso, $limite, $pct, $unit])
            <div style="margin-bottom:18px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-size:13px;font-weight:600;color:#374151;">{{ $label }}</span>
                    <span style="font-size:12px;color:#64748b;">
                        <strong style="color:{{ $corPct($pct) }};">{{ number_format($uso) }}</strong>
                        / {{ number_format($limite) }} {{ $unit }}
                        <span style="color:{{ $corPct($pct) }};font-weight:700;">({{ $pct }}%)</span>
                    </span>
                </div>
                <div style="height:10px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $corPct($pct) }};border-radius:99px;transition:width .4s;"></div>
                </div>
                @if($pct >= 90)
                <div style="font-size:11px;color:#dc2626;margin-top:3px;">⚠️ Limite crítico atingido</div>
                @elseif($pct >= 70)
                <div style="font-size:11px;color:#d97706;margin-top:3px;">⚡ Uso elevado</div>
                @endif
            </div>
            @endforeach

            @if($tenant->trial_expira_em || $tenant->plano_expira_em)
            <div style="border-top:1px solid #f1f5f9;padding-top:14px;margin-top:4px;">
                @if($tenant->trial_expira_em)
                <div style="font-size:13px;color:#64748b;margin-bottom:4px;">
                    Trial expira em: <strong style="color:{{ $tenant->trial_expira_em->isPast() ? '#dc2626' : '#374151' }};">
                        {{ $tenant->trial_expira_em->format('d/m/Y') }}
                        {{ $tenant->trial_expira_em->isPast() ? '(EXPIRADO)' : '(' . $tenant->trial_expira_em->diffForHumans() . ')' }}
                    </strong>
                </div>
                @endif
                @if($tenant->plano_expira_em)
                <div style="font-size:13px;color:#64748b;">
                    Plano expira em: <strong style="color:{{ $tenant->plano_expira_em->isPast() ? '#dc2626' : '#374151' }};">
                        {{ $tenant->plano_expira_em->format('d/m/Y') }}
                        {{ $tenant->plano_expira_em->isPast() ? '(EXPIRADO)' : '(' . $tenant->plano_expira_em->diffForHumans() . ')' }}
                    </strong>
                </div>
                @endif
            </div>
            @endif

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

    {{-- ── Aba: Módulos ── --}}
    @if($aba === 'modulos')
    <div class="card">
        <div class="card-header">
            <span class="card-title">Módulos — {{ $tenant->nome }} (Plano: {{ ucfirst($tenant->plano) }})</span>
            <div style="display:flex;gap:8px;">
                <button wire:click="aplicarPlanoModulos"
                        wire:confirm="Aplicar os módulos padrão do plano {{ $tenant->plano }}? Isso sobrescreverá os módulos ativos."
                        class="btn btn-sm btn-outline">
                    ↺ Aplicar plano
                </button>
                <button wire:click="salvarModulos" class="btn btn-sm btn-primary">
                    Salvar módulos
                </button>
            </div>
        </div>
        <div class="card-body">

            @if(count($modulos) === 0)
            <div style="text-align:center;color:#94a3b8;padding:32px;">
                Nenhum módulo cadastrado. Execute o <code>ModulosSeeder</code> primeiro.
            </div>
            @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">
                @foreach($modulos as $modulo)
                @php $ativo = in_array($modulo['chave'], $modulosAtivos); @endphp
                <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border:1.5px solid {{ $ativo ? '#3b82f6' : '#e2e8f0' }};border-radius:10px;cursor:pointer;background:{{ $ativo ? '#eff6ff' : '#fff' }};transition:all .15s;">
                    <input type="checkbox"
                           wire:model="modulosAtivos"
                           value="{{ $modulo['chave'] }}"
                           style="margin-top:2px;accent-color:#3b82f6;width:16px;height:16px;flex-shrink:0;">
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#0f172a;">
                            @if($modulo['icone']) {{ $modulo['icone'] }} @endif
                            {{ $modulo['nome'] }}
                        </div>
                        @if($modulo['descricao'])
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $modulo['descricao'] }}</div>
                        @endif
                        <div style="font-size:10px;font-family:monospace;color:#94a3b8;margin-top:3px;">{{ $modulo['chave'] }}</div>
                    </div>
                </label>
                @endforeach
            </div>

            <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px;font-size:12px;color:#64748b;">
                <strong>{{ count($modulosAtivos) }}</strong> de <strong>{{ count($modulos) }}</strong> módulos ativos.
                Alterações afetam a sidebar e o acesso às rotas imediatamente após salvar.
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- ── Modal: Editar Limites ── --}}
    @if($modalLimites)
    <div class="modal-overlay" wire:click.self="fecharModalLimites">
        <div class="modal" style="max-width:500px;">
            <div class="modal-header">
                <span class="modal-title">📊 Editar Limites — {{ $tenant->nome }}</span>
                <button class="modal-close" wire:click="fecharModalLimites">✕</button>
            </div>
            <div class="modal-body">

                {{-- Aplicar plano predefinido --}}
                <div style="margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Aplicar plano predefinido</div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach(config('planos') as $slug => $plano)
                        <button wire:click="aplicarPlano('{{ $slug }}')"
                                class="btn btn-sm {{ $editPlano === $slug ? 'btn-primary' : 'btn-ghost' }}">
                            {{ $plano['nome'] }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div style="height:1px;background:#f1f5f9;margin:14px 0;"></div>

                <div class="fg-row">
                    <div class="fg">
                        <label>Limite de Usuários</label>
                        <input wire:model="editLimiteUsuarios" type="number" min="1">
                        @error('editLimiteUsuarios') <span class="err">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>Limite de Processos</label>
                        <input wire:model="editLimiteProcessos" type="number" min="1">
                        @error('editLimiteProcessos') <span class="err">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="fg">
                    <label>Limite de Storage (MB)</label>
                    <input wire:model="editLimiteStorage" type="number" min="1">
                    <div style="font-size:11px;color:#94a3b8;margin-top:3px;">
                        Atual: {{ $metricas['disco_mb'] ?? 0 }} MB usados
                    </div>
                    @error('editLimiteStorage') <span class="err">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="modal-footer">
                <button wire:click="fecharModalLimites" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvarLimites" wire:loading.attr="disabled" class="btn btn-primary">
                    Salvar Limites
                </button>
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
