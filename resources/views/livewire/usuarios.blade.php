<div>

{{-- KPIs --}}
<div class="stat-grid">
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
    <div class="filter-bar">
        <input wire:model.live="busca" type="text" placeholder="🔍 Buscar por nome ou login...">
        <select wire:model.live="filtroPerfil">
            <option value="">Todos os perfis</option>
            <option value="admin">Administrador</option>
            <option value="advogado">Advogado</option>
            <option value="estagiario">Estagiário</option>
            <option value="financeiro">Financeiro</option>
            <option value="recepcionista">Recepcionista</option>
        </select>
        <button wire:click="novoUsuario" class="btn btn-primary btn-sm" style="flex-shrink:0;">+ Novo Usuário</button>
    </div>
</div>

{{-- Tabela --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="hide-sm">Login</th>
                    <th class="hide-sm">Email</th>
                    <th>Perfil</th>
                    <th style="text-align:center;">Status</th>
                    <th class="hide-xs" style="text-align:center;">Último Acesso</th>
                    <th style="text-align:center;">Ações</th>
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
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;background:{{ $p['color'] }};border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($u->nome ?? $u->login, 0, 2)) }}
                            </div>
                            <span style="font-weight:600;">{{ $u->nome ?? $u->login }}</span>
                        </div>
                    </td>
                    <td class="hide-sm" style="color:var(--muted);">{{ $u->login }}</td>
                    <td class="hide-sm" style="color:var(--muted);">{{ $u->email ?? '—' }}</td>
                    <td>
                        <span class="badge" style="background:{{ $p['color'] }}20;color:{{ $p['color'] }};">
                            {{ $p['label'] }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <button wire:click="toggleAtivo({{ $u->id }})"
                            class="badge" style="background:{{ $u->ativo ? '#dcfce7' : '#fee2e2' }};color:{{ $u->ativo ? '#16a34a' : '#dc2626' }};border:none;cursor:pointer;">
                            {{ $u->ativo ? '✅ Ativo' : '❌ Inativo' }}
                        </button>
                    </td>
                    <td class="hide-xs" style="text-align:center;color:var(--muted);font-size:12px;">
                        {{ isset($u->ultimo_acesso) && $u->ultimo_acesso ? \Carbon\Carbon::parse($u->ultimo_acesso)->format('d/m/Y H:i') : 'Nunca' }}
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions" style="justify-content:center;">
                            <button wire:click="editarUsuario({{ $u->id }})" title="Editar" class="btn-icon">✏️</button>
                            @if($u->id !== auth()->id())
                            <button wire:click="excluir({{ $u->id }})"
                                wire:confirm="Excluir usuário {{ $u->nome ?? $u->login }}?"
                                title="Excluir" class="btn-icon">🗑️</button>
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
</div>

{{-- Quadro de Permissões --}}
<div class="card" style="margin-top:20px;">
    <div style="font-size:14px;font-weight:700;color:var(--primary);margin-bottom:16px;">🔐 Quadro de Permissões por Perfil</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Módulo</th>
                    <th style="color:#dc2626;">Admin</th>
                    <th style="color:#2563a8;">Advogado</th>
                    <th style="color:#7c3aed;">Estagiário</th>
                    <th style="color:#16a34a;">Financeiro</th>
                    <th style="color:#d97706;">Recepcionista</th>
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
                <tr>
                    <td style="font-weight:600;">{{ $m['nome'] }}</td>
                    <td style="text-align:center;">{{ $m['admin'] }}</td>
                    <td style="text-align:center;">{{ $m['advogado'] }}</td>
                    <td style="text-align:center;">{{ $m['estagiario'] }}</td>
                    <td style="text-align:center;">{{ $m['financeiro'] }}</td>
                    <td style="text-align:center;">{{ $m['recepcionista'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="font-size:11px;color:var(--muted);margin-top:8px;">✅ Acesso total &nbsp; 👁️ Somente visualização &nbsp; ❌ Sem acesso</div>
</div>

{{-- Modal --}}
@if($modal)
<div class="modal-backdrop">
<div class="modal" style="max-width:520px;">
    <div class="modal-header">
        <span class="modal-title">{{ $usuarioId ? 'Editar' : 'Novo' }} Usuário</span>
        <button wire:click="$set('modal',false)" class="modal-close">✕</button>
    </div>
    <div class="form-grid">
        <div class="form-field" style="grid-column:1/-1;">
            <label class="lbl">Nome Completo *</label>
            <input wire:model="nome" type="text">
            @error('nome') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
            <label class="lbl">Login *</label>
            <input wire:model="login" type="text">
            @error('login') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
            <label class="lbl">Perfil *</label>
            <select wire:model="perfil">
                <option value="admin">Administrador</option>
                <option value="advogado">Advogado</option>
                <option value="estagiario">Estagiário</option>
                <option value="financeiro">Financeiro</option>
                <option value="recepcionista">Recepcionista</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Email</label>
            <input wire:model="email" type="email">
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
            <label class="lbl">Telefone</label>
            <input wire:model="telefone" type="text">
        </div>
        <div class="form-field">
            <label class="lbl">Senha {{ $usuarioId ? '(vazio = manter)' : '*' }}</label>
            <input wire:model="senha" type="password">
            @error('senha') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
            <label class="lbl">Confirmar Senha</label>
            <input wire:model="senha_confirmacao" type="password">
            @error('senha_confirmacao') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-field" style="grid-column:1/-1;display:flex;align-items:center;gap:10px;">
            <input wire:model="ativo" type="checkbox" id="ativo" style="width:16px;height:16px;">
            <label for="ativo" style="font-size:13px;cursor:pointer;font-weight:400;">Usuário ativo</label>
        </div>
    </div>
    <div class="modal-footer">
        <button wire:click="$set('modal',false)" class="btn btn-secondary">Cancelar</button>
        <button wire:click="salvar" class="btn btn-primary">💾 Salvar</button>
    </div>
</div>
</div>
@endif
</div>
