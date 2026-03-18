<div>

<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:var(--primary);margin:0;">Tabelas de Domínio</h2>
    <p style="font-size:13px;color:var(--muted);margin-top:4px;">Clique em um card para gerenciar os registros</p>
</div>

@php
$icons = [
    'fases'          => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg>',
    'graus_risco'    => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    'tipos_acao'     => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>',
    'tipos_processo' => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
    'assuntos'       => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    'reparticoes'    => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="13" y2="15"/></svg>',
    'secretarias'    => '<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
];
$bgs = [
    'fases'          => '#eff6ff',
    'graus_risco'    => '#fef2f2',
    'tipos_acao'     => '#eff6ff',
    'tipos_processo' => '#fdf4ff',
    'assuntos'       => '#fffbeb',
    'reparticoes'    => '#eff6ff',
    'secretarias'    => '#f0fdf4',
];
@endphp

{{-- ── Cards ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:20px;">
    @foreach($config as $tabela => $cfg)
    <button wire:click="abrirTabela('{{ $tabela }}')"
        style="text-align:left;padding:18px 20px;border-radius:12px;border:2px solid {{ $tabelaAtiva === $tabela ? 'var(--primary-light)' : 'transparent' }};background:{{ $tabelaAtiva === $tabela ? '#eff6ff' : 'var(--white)' }};box-shadow:0 1px 3px rgba(0,0,0,.08);cursor:pointer;transition:all .15s;width:100%;"
        onmouseover="if('{{ $tabela }}' !== '{{ $tabelaAtiva }}') { this.style.borderColor='var(--border)'; }"
        onmouseout="if('{{ $tabela }}' !== '{{ $tabelaAtiva }}') { this.style.borderColor='transparent'; }">
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $bgs[$tabela] ?? '#f1f5f9' }};display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
            {!! $icons[$tabela] ?? '' !!}
        </div>
        <div style="font-weight:600;color:{{ $tabelaAtiva === $tabela ? 'var(--primary-light)' : 'var(--text)' }};font-size:14px;margin-bottom:3px;">
            {{ $cfg['label'] }}
        </div>
        <div style="font-size:12px;color:var(--muted);">{{ $contagens[$tabela] }} registros</div>
    </button>
    @endforeach
</div>

{{-- ── Painel da tabela ativa ── --}}
@if($tabelaAtiva && isset($config[$tabelaAtiva]))
@php $cfg = $config[$tabelaAtiva]; @endphp
<div class="card" style="padding:0;overflow:hidden;">

    {{-- cabeçalho painel --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border);background:var(--bg);">
        <div style="font-weight:700;font-size:15px;color:var(--text);">{{ $cfg['label'] }}</div>
        <button wire:click="abrirModal()"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:none;border-radius:8px;background:var(--primary);color:#fff;font-size:13px;font-weight:600;cursor:pointer;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Adicionar
        </button>
    </div>

    {{-- tabela --}}
    <div class="table-wrap">
        <table style="border-collapse:collapse;width:100%;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:left;">Código</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:left;">Descrição</th>
                    @if($cfg['temOrdem'] ?? false)
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;width:80px;">Ordem</th>
                    @endif
                    @if($cfg['temCor'] ?? false)
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;width:80px;">Cor</th>
                    @endif
                    @if($cfg['temAtivo'] ?? false)
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;width:80px;">Ativo</th>
                    @endif
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;width:90px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $row)
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px;font-size:12px;color:var(--muted);font-family:monospace;">{{ $row->codigo ?? '—' }}</td>
                    <td style="padding:12px 16px;font-size:13px;font-weight:500;color:var(--text);">{{ $row->descricao }}</td>
                    @if($cfg['temOrdem'] ?? false)
                    <td style="padding:12px 16px;text-align:center;font-size:13px;color:var(--muted);">{{ $row->ordem ?? '—' }}</td>
                    @endif
                    @if($cfg['temCor'] ?? false)
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:{{ $row->cor_hex }};border:2px solid #fff;box-shadow:0 0 0 1px #cbd5e1;" title="{{ $row->cor_hex }}"></span>
                    </td>
                    @endif
                    @if($cfg['temAtivo'] ?? false)
                    <td style="padding:12px 16px;text-align:center;">
                        <button wire:click="toggleAtivo({{ $row->id }})" title="{{ $row->ativo ? 'Desativar' : 'Ativar' }}"
                            style="padding:3px 10px;border-radius:20px;border:none;cursor:pointer;font-size:11px;font-weight:600;
                                background:{{ $row->ativo ? '#dcfce7' : '#f1f5f9' }};
                                color:{{ $row->ativo ? '#16a34a' : '#94a3b8' }};">
                            {{ $row->ativo ? 'Ativo' : 'Inativo' }}
                        </button>
                    </td>
                    @endif
                    <td style="padding:12px 16px;text-align:center;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:5px;">
                            <button wire:click="abrirModal({{ $row->id }})" title="Editar"
                                style="width:28px;height:28px;border-radius:6px;border:1.5px solid var(--border);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);"
                                onmouseover="this.style.borderColor='var(--primary-light)';this.style.color='var(--primary-light)'"
                                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                                <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="excluir({{ $row->id }})"
                                wire:confirm="Excluir este registro? Esta ação não pode ser desfeita."
                                title="Excluir"
                                style="width:28px;height:28px;border-radius:6px;border:1.5px solid var(--border);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);"
                                onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                                <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state">
                    <div class="empty-state-icon"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
                    <div class="empty-state-title">Nenhum registro cadastrado</div>
                    <div class="empty-state-sub">Clique em <strong>+ Adicionar</strong> para criar o primeiro item.</div>
                </div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── Modal ── --}}
@if($modal && $tabelaAtiva)
@php $cfg = $config[$tabelaAtiva]; @endphp
<div style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;"
    wire:click.self="fecharModal">
    <div style="background:var(--white);border-radius:12px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">

        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:15px;font-weight:700;color:var(--text);margin:0;">
                {{ $registroId ? 'Editar' : 'Adicionar' }} — {{ $cfg['label'] }}
            </h3>
            <button wire:click="fecharModal"
                style="width:30px;height:30px;border-radius:8px;border:none;background:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;justify-content:center;"
                onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='none'">
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:20px 22px;display:flex;flex-direction:column;gap:14px;">

            {{-- Código --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">
                    Código <span style="font-weight:400;">(opcional — gerado automaticamente se vazio)</span>
                </label>
                <input type="text" wire:model="codigo" placeholder="Ex: FASE_01"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);font-family:monospace;">
            </div>

            {{-- Descrição --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">
                    Descrição <span style="color:var(--danger);">*</span>
                </label>
                <input type="text" wire:model="descricao" placeholder="Nome do registro..."
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('descricao') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                @error('descricao') <p style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            {{-- Ordem --}}
            @if($cfg['temOrdem'] ?? false)
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Ordem</label>
                <input type="number" wire:model="ordem" placeholder="1, 2, 3..."
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
            </div>
            @endif

            {{-- Cor --}}
            @if($cfg['temCor'] ?? false)
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Cor</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" wire:model="cor_hex"
                        style="width:44px;height:36px;padding:2px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;background:none;">
                    <input type="text" wire:model="cor_hex" placeholder="#1a3a5c"
                        style="flex:1;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);font-family:monospace;">
                </div>
            </div>
            @endif

            {{-- Ativo --}}
            @if($cfg['temAtivo'] ?? false)
            <div style="display:flex;align-items:center;gap:10px;">
                <input type="checkbox" wire:model="ativo" id="chk_ativo" style="width:16px;height:16px;cursor:pointer;">
                <label for="chk_ativo" style="font-size:13px;color:var(--text);cursor:pointer;">Ativo</label>
            </div>
            @endif
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid var(--border);background:var(--bg);">
            <button wire:click="fecharModal"
                style="padding:8px 16px;border:1.5px solid var(--border);border-radius:8px;background:none;font-size:13px;color:var(--muted);cursor:pointer;">
                Cancelar
            </button>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:8px 16px;border:none;border-radius:8px;background:var(--primary);color:#fff;font-size:13px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove wire:target="salvar">Salvar</span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>

    </div>
</div>
@endif

</div>
