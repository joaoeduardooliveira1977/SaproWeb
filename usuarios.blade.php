<div>
@if(session('success'))
    <div style="background:#dcfce7;border:1px solid #16a34a;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:16px;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;border:1px solid #dc2626;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:16px;">❌ {{ session('error') }}</div>
@endif

{{-- Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="border-left:4px solid var(--primary);text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--primary);">{{ $totais->total }}</div>
        <div style="font-size:12px;color:var(--muted);">Total de Usuários</div>
    </div>
    <div class="card" style="border-left:4px solid var(--success);text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--success);">{{ $totais->ativos }}</div>
        <div style="font-size:12px;color:var(--muted);">Ativos</div>
    </div>
    <div class="card" style="border-left:4px solid var(--accent);text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--accent);">{{ $totais->advogados }}</div>
        <div style="font-size:12px;color:var(--muted);">Advogados</div>
    </div>
    <div class="card" style="border-left:4px solid var(--danger);text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--danger);">{{ $totais->admins }}</div>
        <div style="font-size:12px;color:var(--muted);">Administradores</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input wire:model.live="busca" type="text" placeholder="🔍 Buscar por nome ou login..."
            style="flex:1;min-width:200px;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
        <select wire:model.live="filtroPerfil" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos os perfis</option>
            <option value="admin">Administrador</option>
            <option value="advogado">Advogado</option>
            <option value="estagiario">Estagiário</option>
            <option value="financeiro">Financeiro</option>
            <option value="recepcionista">Recepcionista</option>
        </select>
        <button wire:click="novoUsuario" class="btn btn-primary">+ Novo Usuário</button>
    </div>
</div>

{{-- Tabela --}}
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:var(--primary);color:#fff;">
                <th style="padding:10px 12px;text-align:left;">Nome</th>
                <th style="padding:10px 12px;text-align:left;">Login</th>
                <th style="padding:10px 12px;text-align:left;">Email</th>
                <th style="padding:10px 12px;text-align:center;">Perfil</th>
                <th style="padding:10px 12px;text-align:center;">Status</th>
                <th style="padding:10px 12px;text-align:center;">Último Acesso</th>
                <th style="padding:10px 12px;text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $u)
            @php
                $perfis = [
                    'admin'         => ['label'=>'Administrador', 'color'=>'#dc2626'],
                    'advogado'      => ['label'=>'Advogado',      'color'=>'#2563a8'],
                    'estagiario'    => ['label'=>'Estagiário',    'color'=>'#7c3aed'],
                    'financeiro'    => ['label'=>'Financeiro',    'color'=>'#16a34a'],
                    'recepcionista' => ['label'=>'Recepcionista', 'color'=>'#d97706'],
                ];
                $p = $perfis[$u->perfil] ?? ['label'=>$u->perfil, 'color'=>'#64748b'];
            @endphp
            <tr style="border-bottom:1px solid var(--border);"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:10px 12px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;background:{{ $p['color'] }};border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                            {{ strtoupper(substr($u->nome, 0, 2)) }}
                        </div>
                        <span style="font-weight:600;">{{ $u->nome }}</span>
                    </div>
                </td>
                <td style="padding:10px 12px;color:var(--muted);">{{ $u->login }}</td>
                <td style="padding:10px 12px;color:var(--muted);">{{ $u->email ?? '—' }}</td>
                <td style="padding:10px 12px;text-align:center;">
                    <span style="background:{{ $p['color'] }}20;color:{{ $p['color'] }};padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;">
                        {{ $p['label'] }}
                    </span>
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <button wire:click="toggleAtivo({{ $u->id }})"
                        style="background:{{ ($u->ativo ?? true) ? '#dcfce7' : '#fee2e2' }};color:{{ ($u->ativo ?? true) ? '#16a34a' : '#dc2626' }};border:none;padding:3px 12px;border-radius:12px;font-size:11px;font-weight:700;cursor:pointer;">
                        {{ ($u->ativo ?? true) ? '✅ Ativo' : '❌ Inativo' }}
                    </button>
                </td>
                <td style="padding:10px 12px;text-align:center;color:var(--muted);font-size:12px;">
                    {{ isset($u->ultimo_acesso) && $u->ultimo_acesso ? \Carbon\Carbon::parse($u->ultimo_acesso)->format('d/m/Y H:i') : 'Nunca' }}
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <div style="display:flex;gap:6px;justify-content:center;">
                        <button wire:click="editarUsuario({{ $u->id }})" title="Editar"
                            style="background:none;border:none;cursor:pointer;font-size:16px;">✏️</button>
                        @if($u->id !== auth()->id())
                        <button wire:click="excluir({{ $u->id }})"
                            onclick="return confirm('Excluir usuário {{ $u->nome }}?')"
                            title="Excluir" style="background:none;border:none;cursor:pointer;font-size:16px;">🗑️</button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="padding:32px;text-align:center;color:var(--muted);">Nenhum usuário encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Quadro de Permissões --}}
<div class="card" style="margin-top:20px;">
    <div style="font-size:14px;font-weight:700;color:var(--primary);margin-bottom:16px;">🔐 Quadro de Permissões por Perfil</div>
    <table style="width:100%;border-collapse:collapse;font-size:12px;text-align:center;">
        <thead>
            <tr style="background:var(--bg);">
                <th style="padding:8px 12px;text-align:left;">Módulo</th>
                <th style="padding:8px 12px;color:#dc2626;">Admin</th>
                <th style="padding:8px 12px;color:#2563a8;">Advogado</th>
                <th style="padding:8px 12px;color:#7c3aed;">Estagiário</th>
                <th style="padding:8px 12px;color:#16a34a;">Financeiro</th>
                <th style="padding:8px 12px;color:#d97706;">Recepcionista</th>
            </tr>
        </thead>
        <tbody>
            @php
            $modulos = [
                ['nome'=>'Dashboard',    'admin'=>'✅','advogado'=>'✅','estagiario'=>'✅','financeiro'=>'✅','recepcionista'=>'✅'],
                ['nome'=>'Processos',    'admin'=>'✅','advogado'=>'✅','estagiario'=>'👁️','financeiro'=>'❌','recepcionista'=>'👁️'],
                ['nome'=>'Pessoas',      'admin'=>'✅','advogado'=>'✅','estagiario'=>'👁️','financeiro'=>'❌','recepcionista'=>'✅'],
                ['nome'=>'Agenda',       'admin'=>'✅','advogado'=>'✅','estagiario'=>'👁️','financeiro'=>'❌','recepcionista'=>'✅'],
                ['nome'=>'Financeiro',   'admin'=>'✅','advogado'=>'❌','estagiario'=>'❌','financeiro'=>'✅','recepcionista'=>'❌'],
                ['nome'=>'Honorários',   'admin'=>'✅','advogado'=>'❌','estagiario'=>'❌','financeiro'=>'✅','recepcionista'=>'❌'],
                ['nome'=>'Documentos',   'admin'=>'✅','advogado'=>'✅','estagiario'=>'👁️','financeiro'=>'❌','recepcionista'=>'❌'],
                ['nome'=>'Relatórios',   'admin'=>'✅','advogado'=>'✅','estagiario'=>'❌','financeiro'=>'✅','recepcionista'=>'❌'],
                ['nome'=>'TJSP',         'admin'=>'✅','advogado'=>'✅','estagiario'=>'❌','financeiro'=>'❌','recepcionista'=>'❌'],
                ['nome'=>'Assistente IA','admin'=>'✅','advogado'=>'✅','estagiario'=>'❌','financeiro'=>'❌','recepcionista'=>'❌'],
                ['nome'=>'Usuários',     'admin'=>'✅','advogado'=>'❌','estagiario'=>'❌','financeiro'=>'❌','recepcionista'=>'❌'],
            ];
            @endphp
            @foreach($modulos as $m)
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:8px 12px;text-align:left;font-weight:600;">{{ $m['nome'] }}</td>
                <td style="padding:8px 12px;">{{ $m['admin'] }}</td>
                <td style="padding:8px 12px;">{{ $m['advogado'] }}</td>
                <td style="padding:8px 12px;">{{ $m['estagiario'] }}</td>
                <td style="padding:8px 12px;">{{ $m['financeiro'] }}</td>
                <td style="padding:8px 12px;">{{ $m['recepcionista'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="font-size:11px;color:var(--muted);margin-top:8px;">✅ Acesso total &nbsp; 👁️ Somente visualização &nbsp; ❌ Sem acesso</div>
</div>

{{-- Modal --}}
@if($modal)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:var(--primary);">{{ $usuarioId ? 'Editar' : 'Novo' }} Usuário</h3>
            <button wire:click="$set('modal',false)" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">NOME COMPLETO *</label>
                    <input wire:model="nome" type="text" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                    @error('nome') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">LOGIN *</label>
                    <input wire:model="login" type="text" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                    @error('login') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">PERFIL *</label>
                    <select wire:model="perfil" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                        <option value="admin">Administrador</option>
                        <option value="advogado">Advogado</option>
                        <option value="estagiario">Estagiário</option>
                        <option value="financeiro">Financeiro</option>
                        <option value="recepcionista">Recepcionista</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">EMAIL</label>
                    <input wire:model="email" type="email" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">TELEFONE</label>
                    <input wire:model="telefone" type="text" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">SENHA {{ $usuarioId ? '(deixe vazio para manter)' : '*' }}</label>
                    <input wire:model="senha" type="password" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                    @error('senha') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);">CONFIRMAR SENHA</label>
                    <input wire:model="senha_confirmacao" type="password" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;margin-top:4px;">
                    @error('senha_confirmacao') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div style="grid-column:1/-1;display:flex;align-items:center;gap:10px;">
                    <input wire:model="ativo" type="checkbox" id="ativo" style="width:16px;height:16px;">
                    <label for="ativo" style="font-size:13px;cursor:pointer;">Usuário ativo</label>
                </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="$set('modal',false)" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvar" class="btn btn-primary">💾 Salvar</button>
            </div>
        </div>
    </div>
</div>
@endif
</div>
