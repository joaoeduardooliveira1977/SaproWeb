<div>

<style>
.ind-table { width:100%;border-collapse:collapse; }
.ind-table th { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;padding:8px 12px;border-bottom:2px solid var(--border);text-align:left;background:var(--bg); }
.ind-table td { padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.ind-table tr:hover td { background:var(--bg); }
.ind-badge { display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700; }
.ind-badge-ativo   { background:#dcfce7;color:#16a34a; }
.ind-badge-inativo { background:#fee2e2;color:#dc2626; }
</style>

{{-- Cabeçalho --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:22px;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Indicadores
        </h2>
        <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Pessoas que indicam clientes e recebem comissão sobre honorários e recebimentos.</p>
    </div>
    <button wire:click="abrirModal()" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Indicador
    </button>
</div>

{{-- Busca --}}
<div style="margin-bottom:16px;">
    <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por nome, e-mail ou CPF…"
           style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
</div>

{{-- Tabela --}}
<div style="background:var(--white);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
    @if($indicadores->isEmpty())
        <div style="padding:40px;text-align:center;color:var(--muted);font-size:14px;">
            Nenhum indicador cadastrado.
        </div>
    @else
        <table class="ind-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Contato</th>
                    <th style="text-align:center;">% Comissão</th>
                    <th style="text-align:center;">Clientes</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($indicadores as $ind)
                <tr>
                    <td style="font-weight:600;color:var(--text);">{{ $ind->nome }}</td>
                    <td style="color:var(--muted);">{{ $ind->cpf ?? '—' }}</td>
                    <td style="color:var(--muted);font-size:12px;">
                        @if($ind->celular)<div>{{ $ind->celular }}</div>@endif
                        @if($ind->email)<div>{{ $ind->email }}</div>@endif
                    </td>
                    <td style="text-align:center;font-weight:700;color:#7c3aed;">
                        {{ number_format((float)$ind->percentual_comissao, 2, ',', '') }}%
                    </td>
                    <td style="text-align:center;color:var(--muted);">{{ $ind->pessoas_count }}</td>
                    <td style="text-align:center;">
                        <span class="ind-badge {{ $ind->ativo ? 'ind-badge-ativo' : 'ind-badge-inativo' }}">
                            {{ $ind->ativo ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                            <button wire:click="abrirModal({{ $ind->id }})" class="btn btn-outline" style="padding:4px 10px;font-size:12px;">Editar</button>
                            <button wire:click="toggleAtivo({{ $ind->id }})" wire:confirm="{{ $ind->ativo ? 'Desativar este indicador?' : 'Reativar este indicador?' }}"
                                    class="btn btn-outline" style="padding:4px 10px;font-size:12px;color:{{ $ind->ativo ? '#dc2626' : '#16a34a' }};">
                                {{ $ind->ativo ? 'Desativar' : 'Reativar' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div style="margin-top:16px;">{{ $indicadores->links() }}</div>

{{-- Modal --}}
@if($modalAberto)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:var(--white);border-radius:14px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.3);" @click.stop>

        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:16px;font-weight:800;margin:0;color:var(--primary);">
                {{ $indicadorId ? 'Editar Indicador' : 'Novo Indicador' }}
            </h3>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;line-height:1;">&times;</button>
        </div>

        <div style="padding:20px 24px;display:flex;flex-direction:column;gap:14px;">

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Nome *</label>
                <input wire:model="nome" type="text" placeholder="Nome completo"
                       style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                @error('nome')<span style="color:#dc2626;font-size:11px;">{{ $message }}</span>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">CPF</label>
                    <input wire:model="cpf" type="text" placeholder="000.000.000-00"
                           style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Celular</label>
                    <input wire:model="celular" type="text" placeholder="(11) 9 0000-0000"
                           style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                </div>
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">E-mail</label>
                <input wire:model="email" type="email" placeholder="email@exemplo.com"
                       style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                @error('email')<span style="color:#dc2626;font-size:11px;">{{ $message }}</span>@enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Percentual de Comissão (%) *</label>
                <input wire:model="percentualComissao" type="text" placeholder="Ex: 5,00"
                       style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                @error('percentualComissao')<span style="color:#dc2626;font-size:11px;">{{ $message }}</span>@enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Notas adicionais…"
                          style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:vertical;"></textarea>
            </div>

            @if($indicadorId)
            <div style="display:flex;align-items:center;gap:8px;">
                <input wire:model="ativo" type="checkbox" id="ind-ativo" style="width:16px;height:16px;">
                <label for="ind-ativo" style="font-size:13px;cursor:pointer;">Ativo</label>
            </div>
            @endif

        </div>

        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
            <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                <span wire:loading.remove wire:target="salvar">Salvar</span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>

    </div>
</div>
@endif

</div>
