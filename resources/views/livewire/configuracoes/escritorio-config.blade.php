<div>
<style>
/* ── Layout ── */
.cfg-wrap        { max-width: 920px; margin: 0 auto; }
.cfg-header      { margin-bottom: 22px; }
.cfg-header h1   { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 3px; }
.cfg-header p    { font-size: 13px; color: var(--muted); }

/* ── Tabs ── */
.cfg-tabs        { display: flex; gap: 0; margin-bottom: 20px; background: var(--white); border-radius: 10px; border: 1px solid var(--border); overflow: hidden; flex-wrap: wrap; }
.cfg-tab         { padding: 11px 18px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; border: none; background: transparent; border-bottom: 3px solid transparent; transition: all .15s; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
.cfg-tab:hover   { color: var(--primary); background: var(--bg); }
.cfg-tab.active  { color: var(--primary); border-bottom-color: var(--primary); background: #f0f6ff; }
.cfg-tab svg     { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

/* ── Cards ── */
.cfg-card        { background: var(--white); border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 1px 6px rgba(0,0,0,.07); padding: 26px; margin-bottom: 16px; }
.cfg-sec-title   { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 7px; }

/* ── Form ── */
.cfg-grid-2      { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.cfg-grid-3      { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
.cfg-field       { display: flex; flex-direction: column; gap: 5px; }
.cfg-label       { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
.cfg-input       { padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 13px; color: var(--text); background: var(--white); width: 100%; transition: border-color .2s; font-family: inherit; }
.cfg-input:focus { outline: none; border-color: var(--primary-light); }
.cfg-err         { font-size: 11px; color: var(--danger); margin-top: 2px; }

/* ── Color pickers ── */
.cfg-color-wrap  { display: flex; align-items: center; gap: 8px; }
.cfg-color-input { width: 44px; height: 38px; padding: 2px; border: 1.5px solid var(--border); border-radius: 6px; cursor: pointer; }
.cfg-color-hex   { flex: 1; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 13px; font-family: monospace; }

/* ── Upload ── */
.cfg-upload-area { border: 2px dashed var(--border); border-radius: 10px; padding: 22px; text-align: center; cursor: pointer; transition: all .2s; }
.cfg-upload-area:hover { border-color: var(--primary-light); background: #f8fbff; }

/* ── Preview login ── */
.preview-login { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: #0d2040; padding: 20px; }
.preview-card  { background: #fff; border-radius: 12px; padding: 24px 20px; max-width: 260px; margin: 0 auto; box-shadow: 0 8px 24px rgba(0,0,0,.25); text-align: center; }
.preview-logo-area  { margin-bottom: 12px; }
.preview-logo-img   { height: 48px; width: auto; max-width: 140px; object-fit: contain; }
.preview-nome  { font-size: 13px; font-weight: 800; color: #0d1f3c; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 4px; }
.preview-sub   { font-size: 11px; color: #64748b; margin-bottom: 14px; }
.preview-inp   { width: 100%; padding: 7px 10px; border: 1.5px solid #e5e7eb; border-radius: 7px; font-size: 12px; margin-bottom: 8px; }
.preview-btn   { width: 100%; padding: 9px; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; color: #fff; cursor: default; }

/* ── Table / Usuarios ── */
.cfg-table      { width: 100%; border-collapse: collapse; font-size: 13px; }
.cfg-table th   { background: #f8fafc; color: var(--muted); padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; border-bottom: 2px solid var(--border); }
.cfg-table td   { padding: 9px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.cfg-table tr:hover td { background: #f8fafc; }

/* ── Badges ── */
.badge         { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
.badge-green   { background: #dcfce7; color: #16a34a; }
.badge-red     { background: #fee2e2; color: #dc2626; }
.badge-blue    { background: #eff6ff; color: #2563a8; }
.badge-gray    { background: #f1f5f9; color: #475569; }

/* ── Buttons ── */
.cfg-btn       { display: inline-flex; align-items: center; gap: 5px; padding: 9px 20px; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: filter .15s; }
.cfg-btn:hover { filter: brightness(.9); }
.cfg-btn-sm    { padding: 5px 12px; font-size: 12px; }
.cfg-btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
.cfg-btn-danger  { background: var(--danger); }
.cfg-btn-success { background: var(--success); }
.cfg-btn-gray    { background: #f1f5f9; color: #374151; border: 1px solid var(--border); }

/* ── Segurança ── */
.cfg-hist-row    { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--border); font-size: 12px; }
.cfg-hist-row:last-child { border-bottom: none; }
.cfg-alert-info  { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 14px; }
.cfg-alert-warn  { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 14px; }

/* ── Modal ── */
.cfg-modal-bg    { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 16px; }
.cfg-modal       { background: #fff; border-radius: 14px; padding: 28px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,.25); }
.cfg-modal-head  { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.cfg-modal-title { font-size: 16px; font-weight: 700; color: var(--primary); }
.cfg-modal-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; }
.cfg-modal-foot  { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }

@media (max-width: 640px) {
    .cfg-grid-2, .cfg-grid-3 { grid-template-columns: 1fr; }
    .cfg-tab span { display: none; }
}
</style>

<div class="cfg-wrap">

    <div class="cfg-header">
        <h1>⚙️ Configurações do Escritório</h1>
        <p>Gerencie a identidade, dados, usuários e segurança do seu escritório.</p>
    </div>

    {{-- ── Abas ── --}}
    <div class="cfg-tabs">
        @foreach([
            ['identidade', '🎨', 'Identidade Visual'],
            ['dados',      '🏢', 'Dados'],
            ['dominio',    '🌐', 'Domínio'],
            ['usuarios',   '👥', 'Usuários'],
            ['seguranca',  '🔒', 'Segurança'],
        ] as [$id, $icon, $label])
        <button type="button" wire:click="mudarAba('{{ $id }}')"
                class="cfg-tab {{ $abaAtiva === $id ? 'active' : '' }}">
            {{ $icon }} <span>{{ $label }}</span>
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Aba 1 — Identidade Visual                                --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($abaAtiva === 'identidade')

    <div class="cfg-grid-2" style="align-items:start;">

        {{-- Formulário --}}
        <div>
            <div class="cfg-card">
                <div class="cfg-sec-title">🖼️ Logotipo</div>

                @if($logoAtual && file_exists(storage_path('app/public/'.$logoAtual)))
                <div style="margin-bottom:14px;display:flex;align-items:center;gap:12px;">
                    <img src="{{ asset('storage/'.$logoAtual) }}" alt="Logo atual"
                         style="height:56px;max-width:160px;object-fit:contain;border:1px solid var(--border);border-radius:8px;background:#f8fafc;padding:6px;">
                    <button wire:click="removerLogo" wire:confirm="Remover o logo atual?"
                            class="cfg-btn cfg-btn-sm cfg-btn-danger">✕ Remover</button>
                </div>
                @endif

                <div class="cfg-field">
                    <label class="cfg-label">Upload de logo (PNG, JPG ou SVG — máx. 2 MB)</label>
                    <input wire:model="logoUpload" type="file" accept="image/*" class="cfg-input" style="padding:6px;">
                    @error('logoUpload') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>

                @if($logoUpload)
                <div style="margin-top:10px;">
                    <img src="{{ $logoUpload->temporaryUrl() }}" style="height:48px;max-width:160px;object-fit:contain;border-radius:6px;border:1px solid var(--border);" alt="Preview">
                </div>
                @endif
            </div>

            <div class="cfg-card">
                <div class="cfg-sec-title">🎨 Cores da marca</div>

                <div class="cfg-grid-2" style="margin-bottom:14px;">
                    <div class="cfg-field">
                        <label class="cfg-label">Cor primária</label>
                        <div class="cfg-color-wrap">
                            <input wire:model.live="corPrimaria" type="color" class="cfg-color-input">
                            <input wire:model="corPrimaria" type="text" maxlength="7" class="cfg-color-hex" placeholder="#1a3a5c">
                        </div>
                        @error('corPrimaria') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="cfg-field">
                        <label class="cfg-label">Cor secundária</label>
                        <div class="cfg-color-wrap">
                            <input wire:model.live="corSecundaria" type="color" class="cfg-color-input">
                            <input wire:model="corSecundaria" type="text" maxlength="7" class="cfg-color-hex" placeholder="#c9a84c">
                        </div>
                        @error('corSecundaria') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button wire:click="salvarIdentidade" wire:loading.attr="disabled" class="cfg-btn">
                    <span wire:loading.remove wire:target="salvarIdentidade">💾 Salvar identidade</span>
                    <span wire:loading wire:target="salvarIdentidade">Salvando…</span>
                </button>
            </div>
        </div>

        {{-- Preview da tela de login --}}
        <div>
            <div class="cfg-card">
                <div class="cfg-sec-title">👁️ Preview — Tela de Login</div>
                <div class="preview-login">
                    <div class="preview-card">
                        <div class="preview-logo-area">
                            @if($logoAtual && file_exists(storage_path('app/public/'.$logoAtual)))
                                <img src="{{ asset('storage/'.$logoAtual) }}" class="preview-logo-img" alt="Logo">
                            @elseif($logoUpload)
                                <img src="{{ $logoUpload->temporaryUrl() }}" class="preview-logo-img" alt="Preview logo">
                            @else
                                <div style="font-size:28px;margin-bottom:4px;">⚖️</div>
                            @endif
                        </div>
                        <div class="preview-nome">{{ Str::upper(Str::limit($tenant->nome, 20)) }}</div>
                        <div class="preview-sub">Software Jurídico</div>
                        <input class="preview-inp" type="text" value="Usuário" readonly>
                        <input class="preview-inp" type="password" value="••••••••" readonly>
                        <button class="preview-btn" style="background:{{ $corPrimaria }};">
                            Entrar no Sistema
                        </button>
                    </div>
                    <p style="text-align:center;font-size:11px;color:rgba(255,255,255,.4);margin-top:10px;">Preview em tempo real</p>
                </div>
                <p style="font-size:11px;color:var(--muted);margin-top:10px;">
                    Estas cores e logo serão exibidas na tela de login do escritório ao acessar pelo seu subdomínio.
                </p>
            </div>
        </div>

    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Aba 2 — Dados do Escritório                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($abaAtiva === 'dados')
    <div class="cfg-card">
        <div class="cfg-sec-title">🏢 Informações do Escritório</div>

        <div class="cfg-grid-2" style="margin-bottom:14px;">
            <div class="cfg-field" style="grid-column: span 2;">
                <label class="cfg-label">Nome do Escritório *</label>
                <input wire:model="nome" type="text" class="cfg-input" placeholder="Ex: Silva Advogados Associados">
                @error('nome') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="cfg-grid-3" style="margin-bottom:14px;">
            <div class="cfg-field">
                <label class="cfg-label">CNPJ</label>
                <input wire:model="cnpj" type="text" class="cfg-input" placeholder="00.000.000/0001-00">
                @error('cnpj') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
            <div class="cfg-field">
                <label class="cfg-label">Telefone</label>
                <input wire:model="telefone" type="text" class="cfg-input" placeholder="(11) 9 9999-9999">
                @error('telefone') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
            <div class="cfg-field">
                <label class="cfg-label">E-mail de contato</label>
                <input wire:model="emailContato" type="email" class="cfg-input" placeholder="contato@escritorio.com.br">
                @error('emailContato') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="cfg-grid-2" style="margin-bottom:14px;">
            <div class="cfg-field">
                <label class="cfg-label">Endereço completo</label>
                <input wire:model="endereco" type="text" class="cfg-input" placeholder="Rua, número, bairro">
                @error('endereco') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
            <div class="cfg-field">
                <label class="cfg-label">Cidade / Estado</label>
                <input wire:model="cidade" type="text" class="cfg-input" placeholder="São Paulo – SP">
                @error('cidade') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="cfg-grid-2" style="margin-bottom:20px;">
            <div class="cfg-field">
                <label class="cfg-label">OAB</label>
                <input wire:model="oab" type="text" class="cfg-input" placeholder="OAB/SP 123.456">
                @error('oab') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
            <div class="cfg-field">
                <label class="cfg-label">Site</label>
                <input wire:model="site" type="url" class="cfg-input" placeholder="https://escritorio.com.br">
                @error('site') <span class="cfg-err">{{ $message }}</span> @enderror
            </div>
        </div>

        <button wire:click="salvarDados" wire:loading.attr="disabled" class="cfg-btn">
            <span wire:loading.remove wire:target="salvarDados">💾 Salvar dados</span>
            <span wire:loading wire:target="salvarDados">Salvando…</span>
        </button>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Aba 3 — Domínio                                         --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($abaAtiva === 'dominio')
    <div class="cfg-card">
        <div class="cfg-sec-title">🌐 Subdomínio atual</div>

        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:14px 16px;margin-bottom:20px;">
            <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;margin-bottom:4px;">Endereço de acesso</div>
            <div style="font-size:17px;font-weight:700;color:var(--primary);font-family:monospace;">
                {{ $tenant->slug }}.kmd-ia.com.br
            </div>
            @if($tenant->dominio)
            <div style="font-size:13px;color:var(--success);margin-top:6px;font-weight:600;">
                ✅ Domínio personalizado ativo: <strong>{{ $tenant->dominio }}</strong>
            </div>
            @endif
        </div>

        @if(!$dominioEnviado)
        <div class="cfg-sec-title">📨 Solicitar domínio personalizado</div>

        <div class="cfg-alert-info">
            <strong>Como funciona:</strong> Você pode usar um domínio próprio (ex: <code>app.escritorio.com.br</code>) para acessar o sistema.
            Após a solicitação, nossa equipe configurará o apontamento de DNS.
            Você precisará criar um registro CNAME no seu domínio apontando para <code>kmd-ia.com.br</code>.
        </div>

        <div class="cfg-field" style="margin-bottom:14px;">
            <label class="cfg-label">Domínio desejado</label>
            <input wire:model="dominioSolicitado" type="text" class="cfg-input"
                   placeholder="app.seuescritorio.com.br" style="font-family:monospace;">
            @error('dominioSolicitado') <span class="cfg-err">{{ $message }}</span> @enderror
        </div>

        <div class="cfg-alert-warn" style="font-size:12px;">
            ⚠️ A configuração pode levar até 48h após a confirmação da equipe técnica.
        </div>

        <button wire:click="solicitarDominio" class="cfg-btn">
            📨 Enviar solicitação
        </button>
        @else
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:16px;text-align:center;">
            <div style="font-size:22px;margin-bottom:6px;">✅</div>
            <div style="font-weight:700;color:#166534;">Solicitação enviada com sucesso!</div>
            <div style="font-size:13px;color:#64748b;margin-top:4px;">Nossa equipe entrará em contato em até 48 horas.</div>
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Aba 4 — Usuários                                         --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($abaAtiva === 'usuarios')

    @php
        $limiteAtingido = $tenant->atingiuLimiteUsuarios();
    @endphp

    <div class="cfg-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div class="cfg-sec-title" style="margin:0;border:none;padding:0;">
                👥 Usuários do Escritório
                <span class="badge badge-gray" style="font-size:11px;">
                    {{ $usuarios->where('ativo',true)->count() }} / {{ $tenant->limite_usuarios ?: '∞' }}
                </span>
            </div>
            <button wire:click="novoUsuario"
                    @if($limiteAtingido) disabled title="Limite de usuários atingido" @endif
                    class="cfg-btn cfg-btn-sm {{ $limiteAtingido ? 'cfg-btn-gray' : '' }}"
                    style="{{ $limiteAtingido ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                + Novo Usuário
            </button>
        </div>

        @if($limiteAtingido)
        <div class="cfg-alert-warn">
            ⚠️ Limite de {{ $tenant->limite_usuarios }} usuários atingido no plano {{ $tenant->nomePlano() }}.
            Fale com o suporte para ampliar.
        </div>
        @endif

        <div style="overflow-x:auto;">
            <table class="cfg-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Login / E-mail</th>
                        <th>Perfil</th>
                        <th>Último acesso</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($usuarios as $u)
                <tr wire:key="{{ $u->id }}">
                    <td style="font-weight:600;">{{ $u->nome }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $u->login }}<br>
                        <span style="font-size:11px;">{{ $u->email }}</span>
                    </td>
                    <td><span class="badge badge-blue">{{ $u->perfil }}</span></td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $u->ultimo_acesso ? $u->ultimo_acesso->diffForHumans() : '—' }}
                    </td>
                    <td>
                        @if($u->ativo)
                            <span class="badge badge-green">Ativo</span>
                        @else
                            <span class="badge badge-red">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <button wire:click="editarUsuario({{ $u->id }})"
                                    class="cfg-btn cfg-btn-sm cfg-btn-outline">Editar</button>
                            <button wire:click="toggleAtivoUsuario({{ $u->id }})"
                                    class="cfg-btn cfg-btn-sm {{ $u->ativo ? 'cfg-btn-danger' : 'cfg-btn-success' }}"
                                    style="background:{{ $u->ativo ? '#fee2e2' : '#dcfce7' }};color:{{ $u->ativo ? '#dc2626' : '#16a34a' }};">
                                {{ $u->ativo ? 'Desativar' : 'Ativar' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;color:#94a3b8;">Nenhum usuário cadastrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal usuário --}}
    @if($modalUsuario)
    <div class="cfg-modal-bg">
        <div class="cfg-modal">
            <div class="cfg-modal-head">
                <span class="cfg-modal-title">{{ $usuarioEditId ? 'Editar Usuário' : 'Novo Usuário' }}</span>
                <button wire:click="fecharModalUsuario" class="cfg-modal-close">&times;</button>
            </div>

            <div class="cfg-grid-2" style="margin-bottom:14px;">
                <div class="cfg-field" style="grid-column:span 2;">
                    <label class="cfg-label">Nome completo *</label>
                    <input wire:model="uNome" type="text" class="cfg-input">
                    @error('uNome') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field">
                    <label class="cfg-label">E-mail *</label>
                    <input wire:model="uEmail" type="email" class="cfg-input">
                    @error('uEmail') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field">
                    <label class="cfg-label">Login *</label>
                    <input wire:model="uLogin" type="text" class="cfg-input">
                    @error('uLogin') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field">
                    <label class="cfg-label">Perfil *</label>
                    <select wire:model="uPerfil" class="cfg-input">
                        @foreach($perfisDisponiveis as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('uPerfil') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field">
                    <label class="cfg-label">Senha {{ $usuarioEditId ? '(deixe em branco para manter)' : '*' }}</label>
                    <input wire:model="uSenha" type="password" class="cfg-input" placeholder="Mín. 8 caracteres">
                    @error('uSenha') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field" style="justify-content:flex-end;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:24px;">
                        <input wire:model="uAtivo" type="checkbox" style="width:15px;height:15px;">
                        <span style="font-size:13px;font-weight:600;">Usuário ativo</span>
                    </label>
                </div>
            </div>

            <div class="cfg-modal-foot">
                <button wire:click="fecharModalUsuario" class="cfg-btn cfg-btn-gray">Cancelar</button>
                <button wire:click="salvarUsuario" wire:loading.attr="disabled" class="cfg-btn">
                    <span wire:loading.remove wire:target="salvarUsuario">Salvar</span>
                    <span wire:loading wire:target="salvarUsuario">Salvando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Aba 5 — Segurança                                        --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($abaAtiva === 'seguranca')

    <div class="cfg-grid-2" style="align-items:start;">

        {{-- Alterar senha --}}
        <div>
            <div class="cfg-card">
                <div class="cfg-sec-title">🔑 Alterar Senha</div>

                <div class="cfg-field" style="margin-bottom:12px;">
                    <label class="cfg-label">Senha atual *</label>
                    <input wire:model="senhaAtual" type="password" class="cfg-input">
                    @error('senhaAtual') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field" style="margin-bottom:12px;">
                    <label class="cfg-label">Nova senha *</label>
                    <input wire:model="senhaNova" type="password" class="cfg-input" placeholder="Mín. 8 caracteres">
                    @error('senhaNova') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>
                <div class="cfg-field" style="margin-bottom:20px;">
                    <label class="cfg-label">Confirmar nova senha *</label>
                    <input wire:model="senhaConfirm" type="password" class="cfg-input">
                    @error('senhaConfirm') <span class="cfg-err">{{ $message }}</span> @enderror
                </div>

                <button wire:click="alterarSenha" wire:loading.attr="disabled" class="cfg-btn">
                    <span wire:loading.remove wire:target="alterarSenha">🔑 Alterar senha</span>
                    <span wire:loading wire:target="alterarSenha">Alterando…</span>
                </button>
            </div>

            <div class="cfg-card">
                <div class="cfg-sec-title">🚪 Encerrar Sessões</div>
                <p style="font-size:13px;color:var(--muted);margin-bottom:14px;">
                    Encerra imediatamente as sessões ativas de todos os outros usuários do escritório.
                    Você permanecerá conectado.
                </p>
                <button wire:click="forcarLogoutTodos"
                        wire:confirm="Encerrar sessões de todos os outros usuários?"
                        class="cfg-btn" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;">
                    🚪 Forçar logout de todos
                </button>
            </div>
        </div>

        {{-- Histórico de acessos --}}
        <div class="cfg-card">
            <div class="cfg-sec-title">📋 Histórico de Acessos (seus últimos 10)</div>

            @forelse($historico as $h)
            <div class="cfg-hist-row">
                <div style="font-size:18px;">🔐</div>
                <div style="flex:1;">
                    <div style="font-weight:600;color:var(--text);">Login realizado</div>
                    <div style="color:var(--muted);font-size:11px;">
                        IP: {{ $h->ip ?? '—' }} &nbsp;·&nbsp;
                        {{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:#94a3b8;font-size:13px;">
                Nenhum acesso registrado.
            </div>
            @endforelse
        </div>

    </div>
    @endif

</div>
</div>
