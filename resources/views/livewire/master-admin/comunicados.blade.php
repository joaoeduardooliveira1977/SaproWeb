@section('page-title', 'Comunicados')
<div>

    <div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
        <button wire:click="abrirNovo" class="btn btn-primary">+ Novo Comunicado</button>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">{{ $comunicados->count() }} comunicado(s)</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Expira em</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($comunicados as $c)
                <tr wire:key="{{ $c->id }}">
                    <td>
                        <div style="font-weight:600;">{{ $c->titulo }}</div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ Str::limit($c->mensagem, 60) }}</div>
                    </td>
                    <td>
                        @php $cor = ['info'=>'badge-blue','aviso'=>'badge-yellow','critico'=>'badge-red'][$c->tipo] ?? 'badge-gray'; @endphp
                        <span class="badge {{ $cor }}">{{ ucfirst($c->tipo) }}</span>
                    </td>
                    <td>
                        @if($c->ativo && (!$c->expira_em || $c->expira_em->isFuture()))
                            <span class="badge badge-green">Ativo</span>
                        @elseif($c->expira_em && $c->expira_em->isPast())
                            <span class="badge badge-gray">Expirado</span>
                        @else
                            <span class="badge badge-red">Inativo</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $c->expira_em ? $c->expira_em->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td style="font-size:12px;color:#64748b;">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <button wire:click="editar({{ $c->id }})" class="btn btn-sm btn-outline">Editar</button>
                            <button wire:click="toggleAtivo({{ $c->id }})"
                                    class="btn btn-sm {{ $c->ativo ? 'btn-danger' : 'btn-success' }}">
                                {{ $c->ativo ? 'Desativar' : 'Ativar' }}
                            </button>
                            <button wire:click="excluir({{ $c->id }})"
                                    wire:confirm="Excluir este comunicado?"
                                    class="btn btn-sm" style="background:#fee2e2;color:#dc2626;">✕</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">Nenhum comunicado criado ainda.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Preview ── --}}
    @if($comunicados->where('ativo', true)->isNotEmpty())
    <div class="card" style="margin-top:16px;">
        <div class="card-header"><span class="card-title">👁️ Preview — como aparece para os tenants</span></div>
        <div class="card-body">
            @foreach($comunicados->where('ativo', true)->take(3) as $c)
            @php
                $styles = [
                    'info'    => 'background:#eff6ff;border-color:#bfdbfe;color:#1e40af;',
                    'aviso'   => 'background:#fffbeb;border-color:#fcd34d;color:#92400e;',
                    'critico' => 'background:#fef2f2;border-color:#fca5a5;color:#991b1b;',
                ];
                $icones = ['info'=>'ℹ️','aviso'=>'⚠️','critico'=>'🔴'];
            @endphp
            <div style="padding:12px 16px;border-radius:8px;border:1px solid;margin-bottom:8px;{{ $styles[$c->tipo] ?? $styles['info'] }}">
                <strong>{{ $icones[$c->tipo] ?? 'ℹ️' }} {{ $c->titulo }}</strong>
                <div style="font-size:13px;margin-top:4px;">{{ $c->mensagem }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Modal ── --}}
    @if($modalAberto)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;">
        <div style="background:#fff;border-radius:14px;padding:28px;width:100%;max-width:540px;box-shadow:0 20px 60px rgba(0,0,0,.25);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 style="font-size:16px;font-weight:700;color:var(--primary);">
                    {{ $editandoId ? 'Editar Comunicado' : 'Novo Comunicado' }}
                </h3>
                <button wire:click="fechar" style="background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">&times;</button>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Título</label>
                <input wire:model="titulo" type="text" placeholder="Ex: Manutenção programada"
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;">
                @error('titulo') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Mensagem</label>
                <textarea wire:model="mensagem" rows="3" placeholder="Texto do comunicado…"
                          style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;resize:vertical;"></textarea>
                @error('mensagem') <span style="font-size:11px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Tipo</label>
                    <select wire:model="tipo" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;">
                        <option value="info">ℹ️ Info</option>
                        <option value="aviso">⚠️ Aviso</option>
                        <option value="critico">🔴 Crítico</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:5px;">Expira em (opcional)</label>
                    <input wire:model="expiraEm" type="datetime-local"
                           style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;">
                </div>
            </div>

            <div style="margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                <input wire:model="ativo" type="checkbox" id="ck-ativo-com" style="width:15px;height:15px;accent-color:var(--primary);">
                <label for="ck-ativo-com" style="font-size:14px;font-weight:600;color:#374151;cursor:pointer;">Publicar imediatamente</label>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="fechar" class="btn" style="background:#f1f5f9;color:#374151;">Cancelar</button>
                <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="salvar">Salvar</span>
                    <span wire:loading wire:target="salvar">Salvando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
