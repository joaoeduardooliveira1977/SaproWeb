<div>

    {{-- ── Header ── --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="🔍 Buscar template..."
            style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;width:280px;">
        @if(!$mostrarForm)
        <button wire:click="novo" class="btn btn-primary btn-sm">+ Novo Template</button>
        @endif
    </div>

    {{-- ── Formulário ── --}}
    @if($mostrarForm)
    <div class="card" style="margin-bottom:20px;border:1px solid #bfdbfe;background:#f0f7ff;">
        <div class="card-header">
            <span class="card-title">{{ $editandoId ? '✏️ Editar Template' : '➕ Novo Template' }}</span>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;padding:4px 0;">

            <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:start;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Título *</label>
                    <input type="text" wire:model="titulo" placeholder="Nome do template"
                        style="width:100%;padding:8px 10px;border:1px solid {{ $errors->has('titulo') ? '#dc2626' : 'var(--border)' }};border-radius:6px;font-size:13px;">
                    @error('titulo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Categoria</label>
                    <select wire:model="categoria"
                        style="padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                        @foreach($categorias as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Corpo --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">
                    Corpo do Template *
                    <span style="font-weight:400;color:#64748b;">— use os placeholders abaixo para inserir dados do processo</span>
                </label>
                <textarea wire:model="corpo" rows="12" placeholder="Digite o texto da minuta aqui. Use @{{cliente_nome}}, @{{processo_numero}}, etc."
                    style="width:100%;padding:10px;border:1px solid {{ $errors->has('corpo') ? '#dc2626' : 'var(--border)' }};border-radius:6px;font-size:13px;font-family:monospace;resize:vertical;"></textarea>
                @error('corpo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            {{-- Placeholders de referência --}}
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:12px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Placeholders disponíveis — clique para copiar</div>
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                    @foreach(\App\Livewire\Minutas::$placeholders as $ph => $desc)
                    <button type="button"
                        data-ph="{{ $ph }}"
                        onclick="navigator.clipboard.writeText(this.dataset.ph).then(() => { this.style.background='#dcfce7'; setTimeout(()=>this.style.background='',1000); })"
                        title="{{ $desc }}"
                        style="padding:3px 8px;background:#e2e8f0;border:none;border-radius:4px;font-size:11px;font-family:monospace;cursor:pointer;color:#334155;transition:background .2s;">
                        {{ $ph }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:16px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" wire:model="ativo"> Ativo
                </label>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <button wire:click="salvar" class="btn btn-primary btn-sm">Salvar</button>
                    <button wire:click="cancelar" class="btn btn-secondary btn-sm">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Lista ── --}}
    @if($minutas->isEmpty())
    <div class="card">
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
            Nenhum template cadastrado ainda. Clique em "+ Novo Template" para começar.
        </p>
    </div>
    @else
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Atualizado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($minutas as $m)
                    <tr style="{{ !$m->ativo ? 'opacity:.5;' : '' }}">
                        <td style="font-weight:600;">{{ $m->titulo }}</td>
                        <td>
                            <span class="badge" style="background:#2563a822;color:#2563a8;">
                                {{ $categorias[$m->categoria] ?? $m->categoria }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <button wire:click="toggleAtivo({{ $m->id }})"
                                style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;border:none;cursor:pointer;
                                       background:{{ $m->ativo ? '#dcfce7' : '#f1f5f9' }};color:{{ $m->ativo ? '#16a34a' : '#64748b' }};">
                                {{ $m->ativo ? 'Ativo' : 'Inativo' }}
                            </button>
                        </td>
                        <td style="text-align:center;color:var(--muted);font-size:12px;">
                            {{ $m->updated_at->format('d/m/Y') }}
                        </td>
                        <td style="text-align:right;">
                            <button wire:click="editar({{ $m->id }})"
                                style="background:none;border:none;cursor:pointer;font-size:14px;padding:3px 6px;" title="Editar">✏️</button>
                            <button wire:click="excluir({{ $m->id }})"
                                wire:confirm="Excluir o template '{{ $m->titulo }}'?"
                                style="background:none;border:none;cursor:pointer;font-size:14px;padding:3px 6px;" title="Excluir">🗑️</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
