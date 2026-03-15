<div>

{{-- KPIs --}}
<div class="stat-grid">
    <div class="card" style="border-left:4px solid var(--primary);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="22" height="22" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--primary);">{{ $totais->total }}</div>
        <div style="font-size:12px;color:var(--muted);">Total de Usuários</div>
    </div>
    <div class="card" style="border-left:4px solid var(--success);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="22" height="22" fill="none" stroke="var(--success)" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--success);">{{ $totais->ativos }}</div>
        <div style="font-size:12px;color:var(--muted);">Ativos</div>
    </div>
    <div class="card" style="border-left:4px solid var(--accent);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="22" height="22" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--accent);">{{ $totais->advogados }}</div>
        <div style="font-size:12px;color:var(--muted);">Advogados</div>
    </div>
    <div class="card" style="border-left:4px solid var(--danger);text-align:center;">
        <div style="margin-bottom:8px;display:flex;justify-content:center;">
            <svg width="22" height="22" fill="none" stroke="var(--danger)" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div style="font-size:28px;font-weight:800;color:var(--danger);">{{ $totais->admins }}</div>
        <div style="font-size:12px;color:var(--muted);">Administradores</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:16px;">
    <div class="filter-bar">
        <div style="position:relative;flex:1;min-width:200px;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input wire:model.live="busca" type="text" placeholder="Buscar por nome ou login..." style="padding-left:34px;width:100%;">
        </div>
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
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Nome</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Login</th>
                    <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Email</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Perfil</th>
                    <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Status</th>
                    <th class="hide-xs" style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Último Acesso</th>
                    <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Ações</th>
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
                            {{ $u->ativo ? 'Ativo' : 'Inativo' }}
                        </button>
                    </td>
                    <td class="hide-xs" style="text-align:center;color:var(--muted);font-size:12px;">
                        {{ isset($u->ultimo_acesso) && $u->ultimo_acesso ? \Carbon\Carbon::parse($u->ultimo_acesso)->format('d/m/Y H:i') : 'Nunca' }}
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions" style="justify-content:center;">
                            <button wire:click="editarUsuario({{ $u->id }})" title="Editar"
                                style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            @if($u->id !== auth()->id())
                            <button wire:click="excluir({{ $u->id }})"
                                wire:confirm="Excluir usuário {{ $u->nome ?? $u->login }}?"
                                title="Excluir"
                                style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            </button>
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
    <div style="font-size:14px;font-weight:700;color:var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <svg width="16" height="16" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Quadro de Permissões por Perfil
    </div>
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
                    ['nome'=>'Dashboard',    'admin'=>'full', 'advogado'=>'full','estagiario'=>'full','financeiro'=>'full','recepcionista'=>'full'],
                    ['nome'=>'Processos',    'admin'=>'full', 'advogado'=>'full','estagiario'=>'view','financeiro'=>'none','recepcionista'=>'view'],
                    ['nome'=>'Pessoas',      'admin'=>'full', 'advogado'=>'full','estagiario'=>'view','financeiro'=>'none','recepcionista'=>'full'],
                    ['nome'=>'Agenda',       'admin'=>'full', 'advogado'=>'full','estagiario'=>'view','financeiro'=>'none','recepcionista'=>'full'],
                    ['nome'=>'Financeiro',   'admin'=>'full', 'advogado'=>'none','estagiario'=>'none','financeiro'=>'full','recepcionista'=>'none'],
                    ['nome'=>'Honorários',   'admin'=>'full', 'advogado'=>'none','estagiario'=>'none','financeiro'=>'full','recepcionista'=>'none'],
                    ['nome'=>'Documentos',   'admin'=>'full', 'advogado'=>'full','estagiario'=>'view','financeiro'=>'none','recepcionista'=>'none'],
                    ['nome'=>'Relatórios',   'admin'=>'full', 'advogado'=>'full','estagiario'=>'none','financeiro'=>'full','recepcionista'=>'none'],
                    ['nome'=>'TJSP',         'admin'=>'full', 'advogado'=>'full','estagiario'=>'none','financeiro'=>'none','recepcionista'=>'none'],
                    ['nome'=>'Assistente IA','admin'=>'full', 'advogado'=>'full','estagiario'=>'none','financeiro'=>'none','recepcionista'=>'none'],
                    ['nome'=>'Usuários',     'admin'=>'full', 'advogado'=>'none','estagiario'=>'none','financeiro'=>'none','recepcionista'=>'none'],
                ];
                $permIcon = fn($v) => match($v) {
                    'full' => '<svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
                    'view' => '<svg width="16" height="16" fill="none" stroke="#2563a8" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
                    default => '<svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
                };
                @endphp
                @foreach($modulos as $m)
                <tr>
                    <td style="font-weight:600;">{{ $m['nome'] }}</td>
                    <td style="text-align:center;">{!! $permIcon($m['admin']) !!}</td>
                    <td style="text-align:center;">{!! $permIcon($m['advogado']) !!}</td>
                    <td style="text-align:center;">{!! $permIcon($m['estagiario']) !!}</td>
                    <td style="text-align:center;">{!! $permIcon($m['financeiro']) !!}</td>
                    <td style="text-align:center;">{!! $permIcon($m['recepcionista']) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="font-size:11px;color:var(--muted);margin-top:8px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Acesso total</span>
        <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="16" height="16" fill="none" stroke="#2563a8" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Somente visualização</span>
        <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg> Sem acesso</span>
    </div>
</div>

{{-- Modal --}}
@if($modal)
<div class="modal-backdrop">
<div class="modal" style="max-width:520px;">
    <div class="modal-header">
        <span class="modal-title">{{ $usuarioId ? 'Editar' : 'Novo' }} Usuário</span>
        <button wire:click="$set('modal',false)" class="modal-close" style="display:inline-flex;align-items:center;justify-content:center;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
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
        <button wire:click="salvar" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar
        </button>
    </div>
</div>
</div>
@endif
</div>
