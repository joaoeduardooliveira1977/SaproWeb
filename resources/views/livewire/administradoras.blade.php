<div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">Administradoras</h2>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            {{ $adms->total() }} administradora{{ $adms->total() !== 1 ? 's' : '' }} cadastrada{{ $adms->total() !== 1 ? 's' : '' }}
        </p>
    </div>
    <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nova Administradora
    </button>
</div>

{{-- ── Filtros ── --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <div style="flex:1;min-width:200px;position:relative;">
            <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="busca"
                placeholder="Buscar por nome, CNPJ ou contato..."
                style="width:100%;padding:8px 10px 8px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
        </div>
        <select wire:model.live="filtroAtivo"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);min-width:130px;">
            <option value="1">Ativas</option>
            <option value="0">Inativas</option>
            <option value="">Todas</option>
        </select>
        @if($busca)
        <button wire:click="$set('busca','')"
            style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:none;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:5px;">
            <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Limpar
        </button>
        @endif
    </div>
</div>

{{-- ── Tabela ── --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table style="border-collapse:collapse;width:100%;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Nome</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">CNPJ</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Contato</th>
                    <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Telefone</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;">Clientes</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adms as $a)
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;{{ !$a->ativo ? 'opacity:.55;' : '' }}"
                    onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">

                    <td style="padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:#2563a830;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:14px;color:var(--text);">{{ $a->nome }}</div>
                                @if($a->email)
                                <div style="font-size:12px;color:var(--muted);">{{ $a->email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="hide-sm" style="padding:14px 16px;font-size:13px;color:var(--muted);">
                        {{ $a->cnpj ?? '—' }}
                    </td>

                    <td class="hide-sm" style="padding:14px 16px;font-size:13px;color:var(--text);">
                        {{ $a->contato ?? '—' }}
                    </td>

                    <td class="hide-sm" style="padding:14px 16px;font-size:13px;color:var(--muted);">
                        {{ $a->telefone ?? '—' }}
                    </td>

                    <td style="padding:14px 16px;text-align:center;">
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;
                            background:{{ $a->clientes_count > 0 ? '#2563a815' : 'var(--bg)' }};
                            color:{{ $a->clientes_count > 0 ? '#2563a8' : 'var(--muted)' }};">
                            {{ $a->clientes_count }}
                        </span>
                    </td>

                    <td style="padding:14px 16px;text-align:center;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <button wire:click="abrirModal({{ $a->id }})" title="Editar"
                                style="width:30px;height:30px;border-radius:6px;border:1.5px solid var(--border);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;"
                                onmouseover="this.style.borderColor='var(--primary-light)';this.style.color='var(--primary-light)'"
                                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="toggleAtivo({{ $a->id }})" title="{{ $a->ativo ? 'Desativar' : 'Ativar' }}"
                                style="width:30px;height:30px;border-radius:6px;border:1.5px solid var(--border);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:{{ $a->ativo ? 'var(--success)' : 'var(--muted)' }};transition:all .15s;"
                                onmouseover="this.style.background='var(--bg)'"
                                onmouseout="this.style.background='none'">
                                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    @if($a->ativo)
                                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                    @else
                                    <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                    @endif
                                </svg>
                            </button>
                            @if($a->clientes_count === 0)
                            <button wire:click="excluir({{ $a->id }})"
                                wire:confirm="Excluir a administradora '{{ addslashes($a->nome) }}'? Esta ação não pode ser desfeita."
                                title="Excluir"
                                style="width:30px;height:30px;border-radius:6px;border:1.5px solid var(--border);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;"
                                onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;color:var(--muted);">
                        <svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.4;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Nenhuma administradora encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($adms->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
        {{ $adms->links() }}
    </div>
    @endif
</div>

{{-- ── Modal ── --}}
@if($modal)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;"
    wire:click.self="fecharModal">
    <div style="background:var(--white);border-radius:12px;width:100%;max-width:540px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">

        {{-- Cabeçalho modal --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">
                {{ $admId ? 'Editar Administradora' : 'Nova Administradora' }}
            </h3>
            <button wire:click="fecharModal"
                style="width:32px;height:32px;border-radius:8px;border:none;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);"
                onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='none'">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Corpo modal --}}
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

            {{-- Nome --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">
                    Nome <span style="color:var(--danger);">*</span>
                </label>
                <input type="text" wire:model="nome" placeholder="Ex: Lello, Apsa, Habitacional..."
                    style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('nome') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                @error('nome') <p style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            {{-- CNPJ --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">CNPJ</label>
                <input type="text" wire:model="cnpj" placeholder="00.000.000/0000-00"
                    style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('cnpj') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                @error('cnpj') <p style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            {{-- Contato + Telefone --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Responsável / Contato</label>
                    <input type="text" wire:model="contato" placeholder="Nome do responsável"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Telefone</label>
                    <input type="text" wire:model="telefone" placeholder="(11) 0000-0000"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                </div>
            </div>

            {{-- E-mail --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">E-mail</label>
                <input type="email" wire:model="email" placeholder="contato@administradora.com.br"
                    style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('email') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                @error('email') <p style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            {{-- Observações --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Observações</label>
                <textarea wire:model="observacoes" rows="3" placeholder="Informações adicionais..."
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;"></textarea>
            </div>
        </div>

        {{-- Rodapé modal --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--border);background:var(--bg);">
            <button wire:click="fecharModal"
                style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:none;font-size:13px;color:var(--muted);cursor:pointer;">
                Cancelar
            </button>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:9px 18px;border:none;border-radius:8px;background:var(--primary);color:#fff;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <span wire:loading.remove wire:target="salvar">Salvar</span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>

    </div>
</div>
@endif

</div>
