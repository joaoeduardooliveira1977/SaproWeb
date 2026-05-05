<div>
<style>
.gu-table { width:100%; border-collapse:collapse; }
.gu-table th { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; padding:10px 14px; border-bottom:2px solid var(--border); text-align:left; white-space:nowrap; }
.gu-table td { font-size:13px; color:var(--text); padding:12px 14px; border-bottom:1px solid var(--border); vertical-align:middle; }
.gu-table tr:last-child td { border-bottom:none; }
.gu-table tr:hover td { background:#f8fafc; }
.gu-table tr.eu td { background:#eff6ff; }
.gu-avatar { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; flex-shrink:0; }
.gu-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:99px; font-size:11px; font-weight:700; }
.gu-progress { height:6px; border-radius:99px; background:#e2e8f0; overflow:hidden; margin-top:6px; }
.gu-progress-bar { height:100%; border-radius:99px; transition:width .4s; }
.gu-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; display:flex; align-items:center; justify-content:center; padding:16px; }
.gu-modal { background:#fff; border-radius:14px; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(0,0,0,.18); }
.gu-modal-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px 0; }
.gu-modal-body { padding:20px 24px; }
.gu-field { margin-bottom:16px; }
.gu-label { display:block; font-size:12px; font-weight:700; color:var(--text); margin-bottom:5px; }
.gu-input { width:100%; padding:9px 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; color:var(--text); background:#fff; box-sizing:border-box; transition:border-color .15s; }
.gu-input:focus { outline:none; border-color:var(--primary); }
.gu-input.error { border-color:#dc2626; }
.gu-error { font-size:11px; color:#dc2626; margin-top:4px; }
.gu-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; padding-right:32px; }
.gu-btn-action { width:30px; height:30px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--border); background:#f8fafc; cursor:pointer; transition:all .15s; }
.gu-btn-action:hover { border-color:var(--primary); background:#fff; }
@media (max-width:768px) {
    .gu-table th:nth-child(4), .gu-table td:nth-child(4),
    .gu-table th:nth-child(7), .gu-table td:nth-child(7) { display:none; }
}
</style>

{{-- Toast listener --}}
<div
    x-data="{
        toasts: [],
        addToast(type, msg) {
            const id = Date.now();
            this.toasts.push({ id, type, msg });
            setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 4000);
        }
    }"
    x-on:toast.window="addToast($event.detail.type, $event.detail.msg)"
    style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;"
>
    <template x-for="t in toasts" :key="t.id">
        <div x-transition style="padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;color:#fff;box-shadow:0 4px 16px rgba(0,0,0,.15);"
            :style="t.type === 'sucesso' ? 'background:#16a34a' : 'background:#dc2626'">
            <span x-text="t.msg"></span>
        </div>
    </template>
</div>

{{-- Cabeçalho --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:14px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--text);margin:0 0 4px;">Usuários do Escritório</h1>
        <p style="font-size:13px;color:var(--muted);margin:0;">Gerencie quem acessa o sistema e quais permissões cada pessoa possui.</p>
    </div>
    <button wire:click="abrirModal()"
        @if($atingiuLimite) disabled title="Limite de usuários atingido" @endif
        style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;background:{{ $atingiuLimite ? '#e2e8f0' : 'linear-gradient(135deg,#1d4ed8,#2563a8)' }};color:{{ $atingiuLimite ? '#94a3b8' : '#fff' }};border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:{{ $atingiuLimite ? 'not-allowed' : 'pointer' }};transition:opacity .15s;"
        onmouseover="if(!this.disabled) this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Usuário
    </button>
</div>

{{-- Uso de licenças --}}
@php
    $pct = $limite > 0 ? min(100, round($totalAtivos / $limite * 100)) : 0;
    $corBarra = $pct >= 100 ? '#dc2626' : ($pct >= 80 ? '#d97706' : '#16a34a');
@endphp
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
        <span style="font-size:13px;font-weight:700;color:var(--text);">Licenças utilizadas</span>
        <span style="font-size:13px;font-weight:800;color:{{ $corBarra }};">
            {{ $totalAtivos }} / {{ $limite > 0 ? $limite : '∞' }}
        </span>
    </div>
    @if($limite > 0)
    <div class="gu-progress">
        <div class="gu-progress-bar" style="width:{{ $pct }}%;background:{{ $corBarra }};"></div>
    </div>
    <div style="font-size:11px;color:var(--muted);margin-top:5px;">
        @if($atingiuLimite)
            Limite atingido. Entre em contato para ampliar seu plano.
        @else
            {{ $limite - $totalAtivos }} vaga(s) disponível(eis)
        @endif
    </div>
    @endif
</div>

{{-- Aviso de limite --}}
@if($atingiuLimite)
<div style="background:#fffbeb;border:1.5px solid #fde68a;border-left:4px solid #d97706;border-radius:10px;padding:13px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span style="font-size:13px;color:#92400e;font-weight:600;">
        Você atingiu o limite de <strong>{{ $limite }}</strong> usuários do seu plano. Entre em contato para ampliar.
    </span>
</div>
@endif

{{-- Filtros --}}
<div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
    <div style="position:relative;flex:1;min-width:200px;">
        <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por nome, login ou e-mail…"
            style="width:100%;padding:9px 12px 9px 32px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box;">
    </div>
    <select wire:model.live="filtroPerfil" style="padding:9px 32px 9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:#fff;appearance:none;min-width:170px;">
        <option value="">Todos os perfis</option>
        @foreach($perfisDisponiveis as $val => $label)
            <option value="{{ $val }}">{{ ucfirst($val) }}</option>
        @endforeach
    </select>
</div>

{{-- Tabela --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
    <div style="overflow-x:auto;">
    <table class="gu-table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Login</th>
                <th>Perfil</th>
                <th>E-mail</th>
                <th>Status</th>
                <th>Último acesso</th>
                <th style="text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($usuarios as $u)
        @php
            $euMesmo = $u->id === auth('usuarios')->id();
            $iniciais = strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', trim($u->nome)), 0, 2))));
            $cores = ['#2563a8','#16a34a','#d97706','#7c3aed','#dc2626','#0891b2','#059669'];
            $cor = $cores[crc32($u->login) % count($cores)];
            $perfilCor = match($u->perfil) {
                'administrador' => ['bg'=>'#dcfce7','txt'=>'#166534'],
                'advogado'      => ['bg'=>'#dbeafe','txt'=>'#1e40af'],
                'financeiro'    => ['bg'=>'#fef3c7','txt'=>'#92400e'],
                'estagiario'    => ['bg'=>'#f1f5f9','txt'=>'#475569'],
                'recepcionista' => ['bg'=>'#f5f3ff','txt'=>'#5b21b6'],
                default         => ['bg'=>'#f1f5f9','txt'=>'#475569'],
            };
        @endphp
        <tr class="{{ $euMesmo ? 'eu' : '' }}">
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="gu-avatar" style="background:{{ $cor }}20;color:{{ $cor }};">{{ $iniciais }}</div>
                    <div>
                        <div style="font-weight:700;font-size:13px;color:var(--text);">
                            {{ $u->nome }}
                            @if($euMesmo) <span style="font-size:10px;font-weight:700;color:#1d4ed8;background:#dbeafe;padding:1px 6px;border-radius:99px;margin-left:4px;">você</span> @endif
                        </div>
                    </div>
                </div>
            </td>
            <td style="font-family:monospace;font-size:12px;color:var(--muted);">{{ $u->login }}</td>
            <td>
                <span class="gu-badge" style="background:{{ $perfilCor['bg'] }};color:{{ $perfilCor['txt'] }};">
                    {{ ucfirst($u->perfil) }}
                </span>
            </td>
            <td style="font-size:12px;color:var(--muted);">{{ $u->email ?: '—' }}</td>
            <td>
                @if($u->ativo)
                    <span class="gu-badge" style="background:#dcfce7;color:#166534;">Ativo</span>
                @else
                    <span class="gu-badge" style="background:#fee2e2;color:#991b1b;">Inativo</span>
                @endif
            </td>
            <td style="font-size:12px;color:var(--muted);">
                {{ $u->ultimo_acesso ? $u->ultimo_acesso->locale('pt_BR')->diffForHumans() : 'Nunca' }}
            </td>
            <td>
                <div style="display:flex;gap:6px;justify-content:center;">
                    <button wire:click="abrirModal({{ $u->id }})" class="gu-btn-action" title="Editar">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button wire:click="toggleAtivo({{ $u->id }})" class="gu-btn-action"
                        title="{{ $u->ativo ? 'Desativar' : 'Ativar' }}"
                        @if($euMesmo) disabled style="opacity:.4;cursor:not-allowed;" @endif>
                        @if($u->ativo)
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        @else
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                        @endif
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:32px;color:var(--muted);font-size:13px;">
                Nenhum usuário encontrado.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Modal --}}
@if($modalAberto)
<div class="gu-modal-overlay" wire:click.self="fecharModal">
    <div class="gu-modal">
        <div class="gu-modal-header">
            <div>
                <div style="font-size:16px;font-weight:800;color:var(--text);">
                    {{ $usuarioId ? 'Editar Usuário' : 'Novo Usuário' }}
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    {{ $usuarioId ? 'Altere os dados do usuário. A senha só será alterada se preenchida.' : 'Preencha os dados para criar o acesso.' }}
                </div>
            </div>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;" title="Fechar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="gu-modal-body">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 14px;">
                {{-- Nome --}}
                <div class="gu-field" style="grid-column:1/-1;">
                    <label class="gu-label">Nome completo *</label>
                    <input wire:model.blur="nome" wire:blur="sugerirLogin" type="text" placeholder="Ex: João da Silva"
                        class="gu-input @error('nome') error @enderror">
                    @error('nome') <div class="gu-error">{{ $message }}</div> @enderror
                </div>

                {{-- E-mail --}}
                <div class="gu-field">
                    <label class="gu-label">E-mail *</label>
                    <input wire:model.blur="email" type="email" placeholder="email@exemplo.com"
                        class="gu-input @error('email') error @enderror">
                    @error('email') <div class="gu-error">{{ $message }}</div> @enderror
                </div>

                {{-- Login --}}
                <div class="gu-field">
                    <label class="gu-label">Login *
                        <span style="font-weight:400;color:var(--muted);font-size:11px;">(identificador de acesso)</span>
                    </label>
                    <input wire:model.blur="login" type="text" placeholder="Ex: joaosilva"
                        class="gu-input @error('login') error @enderror">
                    @error('login') <div class="gu-error">{{ $message }}</div> @enderror
                </div>

                {{-- Telefone --}}
                <div class="gu-field">
                    <label class="gu-label">Telefone</label>
                    <input wire:model.blur="telefone" type="text" placeholder="(11) 99999-0000"
                        class="gu-input">
                </div>

                {{-- Perfil --}}
                <div class="gu-field">
                    <label class="gu-label">Perfil de acesso *</label>
                    <select wire:model.live="perfil" class="gu-input gu-select @error('perfil') error @enderror">
                        @foreach($perfisDisponiveis as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('perfil') <div class="gu-error">{{ $message }}</div> @enderror
                </div>

                {{-- Ativo --}}
                <div class="gu-field" style="display:flex;align-items:center;gap:8px;padding-top:22px;">
                    <input wire:model.live="ativo" type="checkbox" id="cb_ativo" style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary);">
                    <label for="cb_ativo" class="gu-label" style="margin:0;cursor:pointer;">Usuário ativo</label>
                </div>

                {{-- Descrição do perfil --}}
                @php
                    $descPerfis = [
                        'administrador' => 'Acesso completo a todos os módulos do sistema.',
                        'advogado'      => 'Acesso a processos, pessoas, prazos, agenda e ferramentas.',
                        'financeiro'    => 'Inclui módulo financeiro além dos acessos básicos.',
                        'estagiario'    => 'Acesso limitado — visualização de processos e agenda.',
                        'recepcionista' => 'Acesso básico — cadastros, agenda e atendimento.',
                    ];
                @endphp
                <div style="grid-column:1/-1;background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:12px;color:var(--muted);margin-bottom:4px;">
                    <strong style="color:var(--text);">{{ ucfirst($perfil) }}:</strong>
                    {{ $descPerfis[$perfil] ?? '' }}
                </div>

                {{-- Senha --}}
                <div class="gu-field">
                    <label class="gu-label">
                        Senha {{ $usuarioId ? '' : '*' }}
                        @if($usuarioId) <span style="font-weight:400;color:var(--muted);font-size:11px;">(deixe em branco para manter)</span> @endif
                    </label>
                    <input wire:model.blur="senha" type="password" placeholder="Mínimo 8 caracteres"
                        class="gu-input @error('senha') error @enderror" autocomplete="new-password">
                    @error('senha') <div class="gu-error">{{ $message }}</div> @enderror
                </div>

                {{-- Confirmar senha --}}
                <div class="gu-field">
                    <label class="gu-label">Confirmar senha {{ $usuarioId ? '' : '*' }}</label>
                    <input wire:model.blur="senhaConfirmacao" type="password" placeholder="Repita a senha"
                        class="gu-input @error('senhaConfirmacao') error @enderror" autocomplete="new-password">
                    @error('senhaConfirmacao') <div class="gu-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Botões --}}
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid var(--border);">
                <button wire:click="fecharModal" type="button"
                    style="padding:9px 20px;border:1.5px solid var(--border);background:#fff;color:var(--text);border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                    Cancelar
                </button>
                <button wire:click="salvar" wire:loading.attr="disabled" type="button"
                    style="padding:9px 24px;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    <span wire:loading.remove wire:target="salvar">Salvar</span>
                    <span wire:loading wire:target="salvar">Salvando…</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
