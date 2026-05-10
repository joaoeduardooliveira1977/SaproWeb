<div>
<style>
.cfg-wrap        { max-width: 860px; margin: 0 auto; }
.cfg-header      { margin-bottom: 24px; }
.cfg-tabs        { display: flex; gap: 0; margin-bottom: 24px; background: var(--white); border-radius: 10px; border: 1px solid var(--border); overflow: hidden; flex-wrap: wrap; }
.cfg-tab         { padding: 11px 20px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; border: none; background: transparent; border-bottom: 3px solid transparent; transition: all .15s; display: flex; align-items: center; gap: 7px; }
.cfg-tab:hover   { color: var(--primary); background: var(--bg); }
.cfg-tab.active  { color: var(--primary); border-bottom-color: var(--primary); background: #f0f6ff; }
.cfg-card        { background: var(--white); border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 1px 6px rgba(0,0,0,.07); padding: 28px; margin-bottom: 16px; }
.cfg-section-title { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 18px; padding-bottom: 10px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
.cfg-field       { display: flex; flex-direction: column; gap: 5px; }
.cfg-label       { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
.cfg-input       { padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 13px; color: var(--text); background: var(--white); width: 100%; transition: border-color .2s; font-family: inherit; }
.cfg-input:focus { outline: none; border-color: var(--primary-light); }
.cfg-grid-2      { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.cfg-err         { font-size: 11px; color: var(--danger); margin-top: 3px; }
.cfg-logo-preview { width: 160px; height: 60px; border-radius: 8px; object-fit: contain; border: 1px solid var(--border); background: var(--bg); padding: 6px; }
.cfg-upload-area { border: 2px dashed var(--border); border-radius: 10px; padding: 24px; text-align: center; cursor: pointer; transition: border-color .2s, background .2s; display: block; }
.cfg-upload-area:hover { border-color: var(--primary-light); background: #f8fbff; }
.cfg-toggle      { position: relative; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; }
.cfg-toggle input { position: absolute; opacity: 0; width: 0; height: 0; }
.cfg-toggle-track { width: 42px; height: 22px; border-radius: 11px; background: var(--border); transition: background .2s; flex-shrink: 0; position: relative; }
.cfg-toggle input:checked + .cfg-toggle-track { background: var(--success); }
.cfg-toggle-thumb { position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.cfg-toggle input:checked ~ .cfg-toggle-thumb { transform: translateX(20px); }
.cfg-badge       { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
.cfg-badge-ok    { background: #dcfce7; color: #166534; }
.cfg-badge-no    { background: #fee2e2; color: #991b1b; }
.cfg-plano-card  { border-radius: 10px; padding: 18px 20px; border: 1.5px solid; }
.cfg-save-btn    { display: inline-flex; align-items: center; gap: 6px; padding: 10px 24px; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: filter .15s; }
.cfg-save-btn:hover { filter: brightness(.9); }
.cfg-alert-ok    { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
@media (max-width: 640px) { .cfg-grid-2 { grid-template-columns: 1fr; } }
</style>

<div class="cfg-wrap">

    {{-- Cabeçalho --}}
    <div class="cfg-header">
        <h1 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:4px;">Configurações do Escritório</h1>
        <p style="font-size:13px;color:var(--muted);">Personalize as informações e preferências do seu escritório.</p>
    </div>

    {{-- Abas --}}
    <div class="cfg-tabs">
        <button type="button" wire:click="mudarAba('identidade')" class="cfg-tab {{ $abaAtiva==='identidade' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Identidade
        </button>
        <button type="button" wire:click="mudarAba('preferencias')" class="cfg-tab {{ $abaAtiva==='preferencias' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            Preferências
        </button>
        <button type="button" wire:click="mudarAba('integracoes')" class="cfg-tab {{ $abaAtiva==='integracoes' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
            Integrações
        </button>
        <button type="button" wire:click="mudarAba('plano')" class="cfg-tab {{ $abaAtiva==='plano' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Plano
        </button>
    </div>

    {{-- ═══ ABA: IDENTIDADE ═══ --}}
    @if($abaAtiva === 'identidade')
    <form wire:submit="salvarIdentidade">

        @if(session('sucesso_identidade'))
        <div class="cfg-alert-ok">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('sucesso_identidade') }}
        </div>
        @endif

        {{-- Logo --}}
        <div class="cfg-card">
            <div class="cfg-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Logo do Escritório
            </div>

            <div x-data="{ preview: '{{ $logoAtual ? Storage::url($logoAtual) : '' }}' }">
                @if($logoAtual)
                <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                    <img :src="preview || '{{ Storage::url($logoAtual) }}'" alt="Logo atual" class="cfg-logo-preview">
                    <div>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">Logo atual</p>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <label style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#eff6ff;color:var(--primary-light);border:1.5px solid #bfdbfe;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Alterar logo
                                <input type="file" wire:model="logoUpload" accept="image/*" style="display:none;"
                                    x-on:change="preview = URL.createObjectURL($event.target.files[0])">
                            </label>
                            <button type="button" wire:click="removerLogo" wire:confirm="Remover o logo atual?"
                                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#fef2f2;color:#dc2626;border:1.5px solid #fca5a5;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                Remover
                            </button>
                        </div>
                    </div>
                </div>
                @else
                <label class="cfg-upload-area">
                    <div x-show="!preview">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <p style="font-size:13px;font-weight:600;color:var(--muted);">Clique para enviar ou arraste o logo</p>
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">PNG, JPG ou SVG · Máx. 2 MB · Recomendado: 200×60 px</p>
                    </div>
                    <div x-show="preview" style="display:none;">
                        <img :src="preview" alt="Preview" style="max-height:60px;max-width:220px;object-fit:contain;border-radius:6px;margin:0 auto;display:block;">
                        <p style="font-size:11px;color:#16a34a;margin-top:8px;font-weight:600;">✓ Arquivo selecionado — clique em "Salvar dados" para confirmar</p>
                    </div>
                    <input type="file" wire:model="logoUpload" accept="image/*" style="display:none;"
                        x-on:change="preview = URL.createObjectURL($event.target.files[0])">
                </label>
                @endif

                <div wire:loading wire:target="logoUpload" style="font-size:12px;color:var(--muted);margin-top:8px;display:flex;align-items:center;gap:6px;">
                    <svg style="animation:spin 1s linear infinite" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.18-5.19"/></svg>
                    Carregando arquivo...
                </div>
                @error('logoUpload')<div class="cfg-err">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Dados do escritório --}}
        <div class="cfg-card">
            <div class="cfg-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Dados do Escritório
            </div>

            <div style="display:flex;flex-direction:column;gap:14px;">
                <div class="cfg-field">
                    <label class="cfg-label">Nome do escritório <span style="color:var(--danger);">*</span></label>
                    <input class="cfg-input" type="text" wire:model="nome" placeholder="Ex: Advocacia Silva & Associados">
                    @error('nome')<div class="cfg-err">{{ $message }}</div>@enderror
                </div>

                <div class="cfg-grid-2">
                    <div class="cfg-field">
                        <label class="cfg-label">E-mail <span style="color:var(--danger);">*</span></label>
                        <input class="cfg-input" type="email" wire:model="email" placeholder="contato@escritorio.com.br">
                        @error('email')<div class="cfg-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="cfg-field">
                        <label class="cfg-label">Telefone</label>
                        <input class="cfg-input" type="text" wire:model="telefone" placeholder="(11) 99999-9999">
                        @error('telefone')<div class="cfg-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="cfg-grid-2">
                    <div class="cfg-field">
                        <label class="cfg-label">CNPJ</label>
                        <input class="cfg-input" type="text" wire:model="cnpj" placeholder="00.000.000/0001-00">
                        @error('cnpj')<div class="cfg-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="cfg-field">
                        <label class="cfg-label">Número OAB</label>
                        <input class="cfg-input" type="text" wire:model="oab" placeholder="OAB/SP 123.456">
                        @error('oab')<div class="cfg-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="cfg-grid-2">
                    <div class="cfg-field">
                        <label class="cfg-label">Cidade</label>
                        <input class="cfg-input" type="text" wire:model="cidade" placeholder="São Paulo">
                        @error('cidade')<div class="cfg-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="cfg-field">
                        <label class="cfg-label">Endereço completo</label>
                        <input class="cfg-input" type="text" wire:model="endereco" placeholder="Rua das Flores, 123 — Centro">
                        @error('endereco')<div class="cfg-err">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="cfg-save-btn">
                <span wire:loading.remove wire:target="salvarIdentidade">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar dados
                </span>
                <span wire:loading wire:target="salvarIdentidade">
                    <svg style="animation:spin 1s linear infinite" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.18-5.19"/></svg>
                    Salvando...
                </span>
            </button>
        </div>

    </form>
    @endif

    {{-- ═══ ABA: PREFERÊNCIAS ═══ --}}
    @if($abaAtiva === 'preferencias')
    <form wire:submit="salvarPreferencias">

        @if(session('sucesso_preferencias'))
        <div class="cfg-alert-ok">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('sucesso_preferencias') }}
        </div>
        @endif

        <div class="cfg-card">
            <div class="cfg-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Fuso Horário
            </div>
            <div class="cfg-field" style="max-width:360px;">
                <label class="cfg-label">Fuso horário do escritório</label>
                <select class="cfg-input" wire:model="timezone">
                    @foreach($timezones as $tz => $label)
                    <option value="{{ $tz }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('timezone')<div class="cfg-err">{{ $message }}</div>@enderror
                <p style="font-size:11px;color:var(--muted);margin-top:4px;">Afeta a exibição de datas e horários no sistema.</p>
            </div>
        </div>

        <div class="cfg-card">
            <div class="cfg-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                Notificações
            </div>
            <label class="cfg-toggle">
                <input type="checkbox" wire:model="whatsapp_habilitado">
                <div class="cfg-toggle-track">
                    <div class="cfg-toggle-thumb"></div>
                </div>
                <div>
                    <span style="font-size:13px;font-weight:600;color:var(--text);">Notificações por WhatsApp habilitadas</span>
                    <p style="font-size:11px;color:var(--muted);margin-top:2px;">Habilita o envio de mensagens automáticas via WhatsApp para clientes.</p>
                </div>
            </label>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="cfg-save-btn">
                <span wire:loading.remove wire:target="salvarPreferencias">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar preferências
                </span>
                <span wire:loading wire:target="salvarPreferencias">
                    <svg style="animation:spin 1s linear infinite" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.18-5.19"/></svg>
                    Salvando...
                </span>
            </button>
        </div>
    </form>
    @endif

    {{-- ═══ ABA: INTEGRAÇÕES ═══ --}}
    @if($abaAtiva === 'integracoes')
    <form wire:submit="salvarIntegracoes">

        @if(session('sucesso_integracoes'))
        <div class="cfg-alert-ok">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('sucesso_integracoes') }}
        </div>
        @endif

        <div class="cfg-card">
            <div class="cfg-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7h1a1 1 0 011 1v3a1 1 0 01-1 1h-1v1a2 2 0 01-2 2H5a2 2 0 01-2-2v-1H2a1 1 0 01-1-1v-3a1 1 0 011-1h1a7 7 0 017-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 012-2z"/></svg>
                Inteligência Artificial (Gemini)
            </div>

            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;font-size:12px;color:#1e40af;">
                    <strong>Como obter a chave:</strong> acesse <span style="font-family:monospace;">aistudio.google.com</span> → Get API Key → Create API key. Cole a chave abaixo para habilitar a IA no sistema.
                </div>

                <div class="cfg-field" x-data="{ show: false }" style="max-width:500px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                        <label class="cfg-label" style="margin:0;">Chave API Gemini</label>
                        @if($gemini_api_key)
                        <span class="cfg-badge cfg-badge-ok">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Configurada
                        </span>
                        @else
                        <span class="cfg-badge cfg-badge-no">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Não configurada
                        </span>
                        @endif
                    </div>
                    <div style="position:relative;">
                        <input class="cfg-input" :type="show ? 'text' : 'password'" wire:model="gemini_api_key"
                            placeholder="AIza..." style="padding-right:40px;">
                        <button type="button" @click="show = !show"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);padding:2px;">
                            <svg x-show="!show" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" style="display:none;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('gemini_api_key')<div class="cfg-err">{{ $message }}</div>@enderror
                    <p style="font-size:11px;color:var(--muted);margin-top:4px;">Usada para geração de documentos e análise de processos com IA. Deixe em branco para desabilitar.</p>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="cfg-save-btn">
                <span wire:loading.remove wire:target="salvarIntegracoes">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Salvar integrações
                </span>
                <span wire:loading wire:target="salvarIntegracoes">
                    <svg style="animation:spin 1s linear infinite" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.18-5.19"/></svg>
                    Salvando...
                </span>
            </button>
        </div>
    </form>
    @endif

    {{-- ═══ ABA: PLANO ═══ --}}
    @if($abaAtiva === 'plano')
    @php
        $plano      = $tenant->plano ?? 'demo';
        $planoCor   = match($plano) {
            'starter'    => ['#eff6ff','#1d4ed8','#bfdbfe'],
            'pro'        => ['#fefce8','#854d0e','#fde68a'],
            'enterprise' => ['#faf5ff','#6b21a8','#e9d5ff'],
            default      => ['#f8fafc','#475569','#e2e8f0'],
        };
        $planoLabel = ucfirst($plano === 'demo' ? 'Demo (Trial)' : $plano);
    @endphp
    <div class="cfg-card">
        <div class="cfg-section-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Plano Atual
        </div>

        <div class="cfg-plano-card" style="background:{{ $planoCor[0] }};border-color:{{ $planoCor[2] }};margin-bottom:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <div style="font-size:11px;font-weight:700;color:{{ $planoCor[1] }};text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Plano ativo</div>
                    <div style="font-size:22px;font-weight:800;color:{{ $planoCor[1] }};">{{ $planoLabel }}</div>
                </div>
                <span style="padding:5px 14px;background:{{ $tenant->ativo ? '#dcfce7' : '#fee2e2' }};color:{{ $tenant->ativo ? '#166534' : '#991b1b' }};border-radius:99px;font-size:12px;font-weight:700;">
                    {{ $tenant->ativo ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
            <div style="background:var(--bg);border-radius:8px;padding:14px;">
                <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Processos</div>
                <div style="font-size:18px;font-weight:700;color:var(--text);">
                    {{ $tenant->limite_processos > 0 ? $tenant->limite_processos : '∞' }}
                    <span style="font-size:11px;font-weight:400;color:var(--muted);">/ limite</span>
                </div>
            </div>
            <div style="background:var(--bg);border-radius:8px;padding:14px;">
                <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Usuários</div>
                <div style="font-size:18px;font-weight:700;color:var(--text);">
                    {{ $tenant->limite_usuarios > 0 ? $tenant->limite_usuarios : '∞' }}
                    <span style="font-size:11px;font-weight:400;color:var(--muted);">/ limite</span>
                </div>
            </div>
        </div>

        @if($tenant->trial_expira_em)
        <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:12px 14px;font-size:12px;color:#92400e;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Período trial expira em <strong>{{ $tenant->trial_expira_em->format('d/m/Y') }}</strong>
            ({{ $tenant->trial_expira_em->isPast() ? 'expirado' : $tenant->trial_expira_em->diffForHumans() }})
        </div>
        @endif

       
    </div>
    @endif

</div>
</div>
