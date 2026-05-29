<div>

    {{-- ── Filtros ── --}}
    <div style="display:flex;gap:12px;margin-bottom:20px;align-items:center;flex-wrap:wrap;">
        <input wire:model.live.debounce.300ms="busca"
               type="text" placeholder="Buscar por nome, slug ou domínio..."
               style="flex:1;min-width:220px;padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">

        <select wire:model.live="filtroAtivo"
                style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
            <option value="">Todos os status</option>
            <option value="1">Ativos</option>
            <option value="0">Suspensos</option>
        </select>
    </div>

    {{-- ── Tabela ── --}}
    <div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 14px;text-align:left;font-weight:700;color:#374151;">Tenant</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:700;color:#374151;">Slug / Domínio</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:700;color:#374151;">Plano</th>
                    <th style="padding:10px 14px;text-align:center;font-weight:700;color:#374151;">Cores</th>
                    <th style="padding:10px 14px;text-align:center;font-weight:700;color:#374151;">Status</th>
                    <th style="padding:10px 14px;text-align:center;font-weight:700;color:#374151;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $t)
                <tr style="border-bottom:1px solid #f1f5f9;" wire:key="{{ $t->id }}">

                    {{-- Logo + nome --}}
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($t->logo && file_exists(storage_path('app/public/' . $t->logo)))
                                <img src="{{ asset('storage/' . $t->logo) }}"
                                     alt="{{ $t->nome }}"
                                     style="width:36px;height:36px;object-fit:contain;border-radius:6px;border:1px solid #e5e7eb;">
                            @else
                                <div style="width:36px;height:36px;border-radius:6px;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:16px;">⚖️</div>
                            @endif
                            <div>
                                <div style="font-weight:600;color:#1e293b;">{{ $t->nome }}</div>
                                <div style="font-size:11px;color:#94a3b8;">ID {{ $t->id }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Slug / domínio --}}
                    <td style="padding:10px 14px;color:#475569;">
                        <div style="font-family:monospace;font-size:12px;">{{ $t->slug }}</div>
                        @if($t->dominio)
                            <div style="font-size:11px;color:#94a3b8;">{{ $t->dominio }}</div>
                        @endif
                    </td>

                    {{-- Plano --}}
                    <td style="padding:10px 14px;">
                        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:#f1f5f9;color:#475569;text-transform:uppercase;">
                            {{ $t->plano }}
                        </span>
                    </td>

                    {{-- Cores --}}
                    <td style="padding:10px 14px;text-align:center;">
                        <div style="display:inline-flex;gap:6px;align-items:center;">
                            <span title="{{ $t->cor_primaria ?? '#1a3a5c' }}"
                                  style="display:inline-block;width:20px;height:20px;border-radius:4px;background:{{ $t->cor_primaria ?? '#1a3a5c' }};border:1px solid #e5e7eb;"></span>
                            <span title="{{ $t->cor_secundaria ?? '#c9a84c' }}"
                                  style="display:inline-block;width:20px;height:20px;border-radius:4px;background:{{ $t->cor_secundaria ?? '#c9a84c' }};border:1px solid #e5e7eb;"></span>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td style="padding:10px 14px;text-align:center;">
                        @if($t->ativo)
                            <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:#dcfce7;color:#16a34a;">Ativo</span>
                        @else
                            <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:#fee2e2;color:#dc2626;">Suspenso</span>
                        @endif
                    </td>

                    {{-- Ações --}}
                    <td style="padding:10px 14px;text-align:center;">
                        <div style="display:inline-flex;gap:8px;">
                            <button wire:click="editar({{ $t->id }})"
                                    style="padding:5px 12px;background:#1a3a5c;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                Editar branding
                            </button>
                            <button wire:click="toggleAtivo({{ $t->id }})"
                                    wire:confirm="{{ $t->ativo ? 'Suspender este tenant?' : 'Reativar este tenant?' }}"
                                    style="padding:5px 12px;background:{{ $t->ativo ? '#fee2e2' : '#dcfce7' }};color:{{ $t->ativo ? '#dc2626' : '#16a34a' }};border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                {{ $t->ativo ? 'Suspender' : 'Reativar' }}
                            </button>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:32px;text-align:center;color:#94a3b8;">
                        Nenhum tenant encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Modal de edição de branding ── --}}
    @if($modalAberto)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;">
        <div style="background:#fff;border-radius:16px;padding:32px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.3);">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h2 style="font-size:18px;font-weight:700;color:#1e293b;">Editar Branding</h2>
                <button wire:click="fechar" style="background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">&times;</button>
            </div>

            {{-- Nome --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Nome do Escritório</label>
                <input wire:model="nome" type="text"
                       style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;">
                @error('nome') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            {{-- Slug (somente leitura) --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Slug (identificador)</label>
                <input wire:model="slug" type="text" readonly
                       style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;background:#f8fafc;color:#94a3b8;font-family:monospace;">
            </div>

            {{-- Domínio --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Domínio personalizado</label>
                <input wire:model="dominio" type="text" placeholder="ex: escritorio1.kmd-ia.com.br"
                       style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:monospace;">
                @error('dominio') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            {{-- Cores --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cor primária</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input wire:model.live="corPrimaria" type="color"
                               style="width:44px;height:38px;padding:2px;border:1.5px solid #e5e7eb;border-radius:6px;cursor:pointer;">
                        <input wire:model="corPrimaria" type="text" maxlength="7"
                               style="flex:1;padding:10px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:monospace;">
                    </div>
                    @error('corPrimaria') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cor secundária</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input wire:model.live="corSecundaria" type="color"
                               style="width:44px;height:38px;padding:2px;border:1.5px solid #e5e7eb;border-radius:6px;cursor:pointer;">
                        <input wire:model="corSecundaria" type="text" maxlength="7"
                               style="flex:1;padding:10px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:monospace;">
                    </div>
                    @error('corSecundaria') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Preview de cores --}}
            <div style="margin-bottom:16px;padding:12px;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb;">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">Preview</div>
                <button style="padding:8px 20px;background:{{ $corPrimaria }};color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;margin-right:8px;">
                    Botão primário
                </button>
                <span style="display:inline-block;padding:4px 14px;background:{{ $corSecundaria }};color:#fff;border-radius:99px;font-size:12px;font-weight:700;">
                    Destaque
                </span>
            </div>

            {{-- Upload de logo --}}
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Logo (PNG, JPG ou SVG — máx. 2 MB)</label>
                <input wire:model="novoLogo" type="file" accept="image/*"
                       style="width:100%;padding:10px;border:1.5px dashed #d1d5db;border-radius:8px;font-size:13px;background:#fafafa;cursor:pointer;">
                @error('novoLogo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror

                @if($novoLogo)
                    <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                        <img src="{{ $novoLogo->temporaryUrl() }}" style="height:48px;width:auto;border-radius:6px;border:1px solid #e5e7eb;" alt="Preview">
                        <span style="font-size:12px;color:#64748b;">Preview do novo logo</span>
                    </div>
                @endif
            </div>

            {{-- Status ativo --}}
            <div style="margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                <input wire:model="ativo" type="checkbox" id="ck-ativo"
                       style="width:16px;height:16px;accent-color:#1a3a5c;cursor:pointer;">
                <label for="ck-ativo" style="font-size:14px;font-weight:600;color:#374151;cursor:pointer;">Tenant ativo</label>
            </div>

            {{-- Botões --}}
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button wire:click="fechar"
                        style="padding:10px 20px;background:#f1f5f9;color:#374151;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancelar
                </button>
                <button wire:click="salvar" wire:loading.attr="disabled"
                        style="padding:10px 24px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                    <span wire:loading.remove wire:target="salvar">Salvar branding</span>
                    <span wire:loading wire:target="salvar">Salvando...</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
