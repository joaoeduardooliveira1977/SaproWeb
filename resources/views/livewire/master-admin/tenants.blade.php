section('page-title', 'Tenants')
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
        <button wire:click="abrirModalNovo" class="btn btn-primary">+ Novo Tenant</button>
    </div>

    {{-- ── Tabela ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $tenants->total() }} tenant(s)</span>
            <a href="{{ route('master.lixeira') }}" class="btn btn-sm btn-ghost" style="font-size:11px;">
                🗑️ Ver Lixeira
            </a>
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
                        <div style="font-weight:600;color:#0f172a;">{{ $t->nome }}</div>
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
                            <span class="badge badge-green">● Ativo</span>
                        @else
                            <span class="badge badge-red">● Suspenso</span>
                        @endif
                    </td>
                    <td style="text-align:center;font-weight:600;">{{ $t->usuarios_count }}</td>
                    <td style="text-align:center;font-weight:600;">{{ $t->processos_count }}</td>
                    <td style="text-align:center;font-weight:600;">{{ $t->pessoas_count }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $t->created_at->format('d/m/Y') }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $t->ultimo_acesso ? \Carbon\Carbon::parse($t->ultimo_acesso)->diffForHumans() : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            <a href="{{ route('master.tenant-show', $t->id) }}" class="btn btn-sm btn-outline">Ver</a>
                            <button wire:click="loginComoTenant({{ $t->id }})"
                                    wire:confirm="Entrar como administrador de {{ $t->nome }}?"
                                    class="btn btn-sm btn-primary">Entrar</button>
                            <button wire:click="toggleAtivo({{ $t->id }})"
                                    wire:confirm="{{ $t->ativo ? 'Suspender este tenant?' : 'Reativar este tenant?' }}"
                                    class="btn btn-sm {{ $t->ativo ? '' : 'btn-success' }}"
                                    style="{{ $t->ativo ? 'background:#f1f5f9;color:#374151;' : '' }}">
                                {{ $t->ativo ? 'Suspender' : 'Ativar' }}
                            </button>
                            <button wire:click="abrirModalExcluir({{ $t->id }})"
                                    class="btn btn-sm btn-danger"
                                    title="Mover para lixeira">🗑</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:32px;color:#94a3b8;">Nenhum tenant encontrado.</td></tr>
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

    {{-- ── Modal: Novo Tenant ── --}}
    @if($modalNovo)
    <div class="modal-overlay" wire:click.self="fecharModalNovo">
        <div class="modal" style="max-width:600px;">
            <div class="modal-header">
                <span class="modal-title">Novo Tenant</span>
                <button class="modal-close" wire:click="fecharModalNovo">✕</button>
            </div>
            <div class="modal-body" style="max-height:75vh;overflow-y:auto;">

                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Dados do Escritório</div>

                <div class="fg">
                    <label>Nome do Escritório *</label>
                    <input wire:model.live="novoNome" type="text" placeholder="Ex: Escritório Silva & Associados">
                    @error('novoNome') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="fg-row">
                    <div class="fg">
                        <label>Slug *</label>
                        <input wire:model.live="novoSlug" type="text" placeholder="silva-associados">
                        @error('novoSlug') <span class="err">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>Plano</label>
                        <select wire:model="novoPlano">
                            <option value="demo">Demo (trial 30 dias)</option>
                            <option value="starter">Starter</option>
                            <option value="pro">Pro</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                    </div>
                </div>

                <div class="fg">
                    <label>Domínio</label>
                    <input wire:model="novoDominio" type="text" placeholder="slug.kmd-ia.com.br">
                    @error('novoDominio') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="fg-row">
                    <div class="fg">
                        <label>Cor Primária</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input wire:model="novoCorPrimaria" type="color" style="width:40px;height:36px;padding:2px;border-radius:6px;cursor:pointer;">
                            <input wire:model="novoCorPrimaria" type="text" placeholder="#1a3a5c" style="flex:1;">
                        </div>
                    </div>
                    <div class="fg">
                        <label>Cor Secundária</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input wire:model="novoCorSecundaria" type="color" style="width:40px;height:36px;padding:2px;border-radius:6px;cursor:pointer;">
                            <input wire:model="novoCorSecundaria" type="text" placeholder="#c9a84c" style="flex:1;">
                        </div>
                    </div>
                </div>

                <div style="height:1px;background:var(--border);margin:16px 0;"></div>
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Administrador do Tenant</div>

                <div class="fg-row">
                    <div class="fg">
                        <label>Nome *</label>
                        <input wire:model="novoAdminNome" type="text" placeholder="Nome completo">
                        @error('novoAdminNome') <span class="err">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>E-mail *</label>
                        <input wire:model="novoAdminEmail" type="email" placeholder="admin@escritorio.com">
                        @error('novoAdminEmail') <span class="err">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="fg">
                    <label>Senha inicial *</label>
                    <input wire:model="novoAdminSenha" type="password" placeholder="Mínimo 8 caracteres">
                    @error('novoAdminSenha') <span class="err">{{ $message }}</span> @enderror
                </div>

                @if($novoSlug)
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;font-size:12px;color:#166534;margin-top:4px;">
                    Login gerado automaticamente: <strong>{{ $novoSlug }}_admin</strong>
                </div>
                @endif

            </div>
            <div class="modal-footer">
                <button wire:click="fecharModalNovo" class="btn btn-outline">Cancelar</button>
                <button wire:click="criarTenant" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="criarTenant">Criar Tenant</span>
                    <span wire:loading wire:target="criarTenant">Criando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal: Excluir para Lixeira ── --}}
    @if($modalExcluir)
    <div class="modal-overlay" wire:click.self="fecharModalExcluir">
        <div class="modal" style="max-width:460px;">
            <div class="modal-header" style="border-bottom-color:#fecaca;">
                <span class="modal-title" style="color:#dc2626;">🗑️ Mover para a Lixeira</span>
                <button class="modal-close" wire:click="fecharModalExcluir">✕</button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px;color:#374151;margin-bottom:16px;">
                    Você está prestes a mover o tenant <strong>{{ $excluindoNome }}</strong> para a lixeira.
                    O sistema ficará inacessível para todos os usuários deste tenant.
                </p>
                <div class="fg">
                    <label>Motivo da exclusão *</label>
                    <textarea wire:model="motivoExclusao"
                              placeholder="Descreva o motivo da exclusão (mínimo 10 caracteres)…"
                              rows="3"></textarea>
                    @error('motivoExclusao') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;font-size:12px;color:#b91c1c;">
                    O tenant poderá ser restaurado da lixeira a qualquer momento.
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="fecharModalExcluir" class="btn btn-outline">Cancelar</button>
                <button wire:click="excluirParaLixeira" wire:loading.attr="disabled" class="btn btn-danger">
                    <span wire:loading.remove wire:target="excluirParaLixeira">Mover para Lixeira</span>
                    <span wire:loading wire:target="excluirParaLixeira">Aguarde…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
