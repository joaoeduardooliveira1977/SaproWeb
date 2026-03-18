<div>

@php
$inp = "width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
@endphp

{{-- Alertas de vencimento --}}
@if($vencidas > 0)
<div style="display:flex;gap:10px;padding:12px 16px;background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;margin-bottom:12px;align-items:center;">
    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span style="font-size:13px;color:#dc2626;font-weight:600;">{{ $vencidas }} procuração(ões) vencida(s)</span>
</div>
@endif
@if($vencendoEm30 > 0)
<div style="display:flex;gap:10px;padding:12px 16px;background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;margin-bottom:12px;align-items:center;">
    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    <span style="font-size:13px;color:#d97706;font-weight:600;">{{ $vencendoEm30 }} procuração(ões) vencendo nos próximos 30 dias</span>
</div>
@endif

{{-- Cabeçalho --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <div style="display:flex;align-items:center;gap:10px;flex:1;">
            <div style="position:relative;flex:1;max-width:360px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por cliente..."
                    style="{{ $inp }}padding-left:34px;">
            </div>
        </div>
        <button wire:click="novo" class="btn btn-primary btn-sm">+ Nova Procuração</button>
    </div>
</div>

{{-- Formulário --}}
@if($mostrarForm)
<div class="card" style="margin-bottom:16px;border-left:4px solid var(--primary-light);">
    <h3 style="font-size:15px;font-weight:700;color:var(--primary);margin-bottom:20px;">
        {{ $editandoId ? 'Editar Procuração' : 'Nova Procuração' }}
    </h3>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

        {{-- Cliente --}}
        <div class="form-field" style="grid-column:1/-1;" x-data x-on:click.outside="$wire.set('clienteSugs', [])">
            <label class="lbl">Cliente *</label>
            @if($cliente_id)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1.5px solid var(--success);border-radius:8px;background:#f0fdf4;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span style="font-size:13px;flex:1;">{{ $clienteNome }}</span>
                <button type="button" wire:click="$set('cliente_id', null)" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            @else
            <div style="position:relative;">
                <input type="text" wire:model.live.debounce.300ms="clienteBusca" placeholder="Digite o nome do cliente..."
                    style="{{ $inp }}" autocomplete="off">
                @if(count($clienteSugs) > 0)
                <div style="position:absolute;top:100%;left:0;right:0;background:var(--white);border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:100;max-height:200px;overflow-y:auto;margin-top:2px;">
                    @foreach($clienteSugs as $s)
                    <div wire:click="selecionarCliente({{ $s['id'] }}, '{{ addslashes($s['nome']) }}')"
                        style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);"
                        onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                        {{ $s['nome'] }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
            @error('cliente_id') <span style="color:var(--danger);font-size:11px;">{{ $message }}</span> @enderror
        </div>

        {{-- Processo (opcional) --}}
        <div class="form-field" style="grid-column:1/-1;" x-data x-on:click.outside="$wire.set('processoSugs', [])">
            <label class="lbl">Processo (opcional)</label>
            @if($processo_id)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--bg);">
                <span style="font-size:13px;flex:1;">{{ $processoBusca }}</span>
                <button type="button" wire:click="$set('processo_id', null); $set('processoBusca', '')" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
                    <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            @else
            <div style="position:relative;">
                <input type="text" wire:model.live.debounce.300ms="processoBusca" placeholder="Número do processo..."
                    style="{{ $inp }}" autocomplete="off">
                @if(count($processoSugs) > 0)
                <div style="position:absolute;top:100%;left:0;right:0;background:var(--white);border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:100;max-height:200px;overflow-y:auto;margin-top:2px;">
                    @foreach($processoSugs as $s)
                    <div wire:click="selecionarProcesso({{ $s['id'] }}, '{{ addslashes($s['numero']) }}')"
                        style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);"
                        onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                        {{ $s['numero'] }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Tipo --}}
        <div class="form-field">
            <label class="lbl">Tipo *</label>
            <select wire:model="tipo" style="{{ $inp }}">
                <option value="ad judicia">Ad Judicia</option>
                <option value="ad negotia">Ad Negotia</option>
                <option value="especial">Especial</option>
                <option value="geral">Geral</option>
            </select>
        </div>

        {{-- Ativa --}}
        <div class="form-field" style="display:flex;align-items:flex-end;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 12px;border:1.5px solid {{ $ativa ? '#bbf7d0' : 'var(--border)' }};border-radius:8px;font-size:13px;background:{{ $ativa ? '#f0fdf4' : 'var(--white)' }};width:100%;box-sizing:border-box;">
                <input wire:model.live="ativa" type="checkbox" style="width:15px;height:15px;cursor:pointer;accent-color:#16a34a;margin:0;">
                Procuração ativa
            </label>
        </div>

        {{-- Data Emissão --}}
        <div class="form-field">
            <label class="lbl">Data de Emissão *</label>
            <input wire:model="data_emissao" type="date" style="{{ $inp }}">
            @error('data_emissao') <span style="color:var(--danger);font-size:11px;">{{ $message }}</span> @enderror
        </div>

        {{-- Data Validade --}}
        <div class="form-field">
            <label class="lbl">Validade (deixe em branco = indeterminada)</label>
            <input wire:model="data_validade" type="date" style="{{ $inp }}">
            @error('data_validade') <span style="color:var(--danger);font-size:11px;">{{ $message }}</span> @enderror
        </div>

        {{-- Poderes --}}
        <div class="form-field" style="grid-column:1/-1;">
            <label class="lbl">Poderes conferidos</label>
            <textarea wire:model="poderes" rows="3" placeholder="Descreva os poderes conferidos..."
                style="{{ $inp }}resize:vertical;font-family:inherit;"></textarea>
        </div>

        {{-- Observações --}}
        <div class="form-field" style="grid-column:1/-1;">
            <label class="lbl">Observações</label>
            <input wire:model="observacoes" type="text" placeholder="Observações adicionais..." style="{{ $inp }}">
        </div>

        {{-- Arquivo --}}
        <div class="form-field" style="grid-column:1/-1;">
            <label class="lbl">Documento (PDF/imagem)</label>
            <input wire:model="arquivo" type="file" accept=".pdf,.jpg,.jpeg,.png"
                style="font-size:13px;color:var(--text);">
            @error('arquivo') <span style="color:var(--danger);font-size:11px;">{{ $message }}</span> @enderror
        </div>

    </div>

    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px;">
        <button wire:click="cancelar" class="btn btn-outline btn-sm">Cancelar</button>
        <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary btn-sm">
            <span wire:loading.remove wire:target="salvar">Salvar</span>
            <span wire:loading wire:target="salvar">Salvando…</span>
        </button>
    </div>
</div>
@endif

{{-- Tabela --}}
@if($procuracoes->isEmpty() && !$busca)
<div class="empty-state">
    <div class="empty-state-icon">
        <svg aria-hidden="true" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <div class="empty-state-title">Nenhuma procuração cadastrada</div>
    <div class="empty-state-sub">Clique em "+ Nova Procuração" para começar.</div>
</div>
@else
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Processo</th>
                    <th>Tipo</th>
                    <th>Emissão</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th>Arquivo</th>
                    <th style="text-align:right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($procuracoes as $p)
                <tr>
                    <td style="font-weight:600;">{{ $p->cliente?->nome ?? '—' }}</td>
                    <td>
                        @if($p->processo)
                        <a href="{{ route('processos.show', $p->processo_id) }}" class="text-primary" style="font-size:12px;">
                            {{ $p->processo->numero }}
                        </a>
                        @else
                        <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:12px;font-weight:600;text-transform:capitalize;">{{ $p->tipo }}</span>
                    </td>
                    <td style="font-size:12px;">{{ $p->data_emissao->format('d/m/Y') }}</td>
                    <td style="font-size:12px;">
                        @if($p->data_validade)
                            <span style="color:{{ $p->statusCor() }};font-weight:600;">
                                {{ $p->data_validade->format('d/m/Y') }}
                            </span>
                        @else
                            <span style="color:var(--muted)">Indeterminada</span>
                        @endif
                    </td>
                    <td>
                        @php $sv = $p->statusVencimento(); @endphp
                        <span class="badge" style="
                            background:{{ $sv === 'vencida' ? '#fef2f2' : ($sv === 'vencendo' ? '#fffbeb' : '#f0fdf4') }};
                            color:{{ $p->statusCor() }};">
                            {{ $sv === 'vencida' ? 'Vencida' : ($sv === 'vencendo' ? 'Vencendo' : ($p->ativa ? 'Vigente' : 'Inativa')) }}
                        </span>
                    </td>
                    <td>
                        @if($p->arquivo)
                        <a href="{{ Storage::url($p->arquivo) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size:11px;">
                            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Ver
                        </a>
                        @else
                        <span style="color:var(--muted);font-size:11px;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;white-space:nowrap;">
                        @if($excluindoId === $p->id)
                        <span style="font-size:11px;color:var(--danger);">Confirmar?</span>
                        <button wire:click="excluir" class="btn btn-sm" style="background:#dc2626;color:#fff;font-size:11px;padding:3px 10px;">Sim</button>
                        <button wire:click="cancelar" class="btn btn-secondary btn-sm" style="font-size:11px;">Não</button>
                        @else
                        <button wire:click="editar({{ $p->id }})" class="btn btn-secondary btn-sm" style="font-size:11px;">Editar</button>
                        <button wire:click="confirmarExclusao({{ $p->id }})" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;font-size:11px;padding:4px 10px;border-radius:6px;">Excluir</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:24px;color:var(--muted);">Nenhuma procuração encontrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($procuracoes->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
        {{ $procuracoes->links() }}
    </div>
    @endif
</div>
@endif

</div>
