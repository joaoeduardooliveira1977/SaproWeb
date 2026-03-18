<div>
    {{-- ── Barra de progresso ── --}}
    @if($total > 0)
    <div style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:13px;font-weight:600;color:#1e293b;">Progresso das Tarefas</span>
            <span style="font-size:13px;font-weight:700;color:#2563a8;">{{ $concluidas }}/{{ $total }} ({{ $progresso }}%)</span>
        </div>
        <div style="height:8px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ $progresso }}%;background:{{ $progresso === 100 ? '#16a34a' : '#2563a8' }};border-radius:99px;transition:width .4s;"></div>
        </div>
    </div>
    @endif

    {{-- ── Lista de tarefas ── --}}
    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:16px;">
        @forelse($tarefas as $t)
        <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border-radius:8px;
                    background:{{ $t->concluida ? '#f0fdf4' : '#f8fafc' }};
                    border:1px solid {{ $t->concluida ? '#bbf7d0' : '#e2e8f0' }};
                    opacity:{{ $t->concluida ? '.75' : '1' }};">

            {{-- Checkbox --}}
            <button wire:click="toggleConcluida({{ $t->id }})"
                style="flex-shrink:0;width:20px;height:20px;border-radius:50%;border:2px solid {{ $t->concluida ? '#16a34a' : '#94a3b8' }};
                       background:{{ $t->concluida ? '#16a34a' : 'transparent' }};cursor:pointer;
                       display:flex;align-items:center;justify-content:center;padding:0;margin-top:1px;">
                @if($t->concluida)
                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 10 10" fill="none">
                    <path d="M1.5 5l2.5 2.5 4.5-4.5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @endif
            </button>

            {{-- Conteúdo --}}
            @if($editandoId === $t->id)
            {{-- Modo edição inline --}}
            <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                <input type="text" wire:model="editTitulo" placeholder="Título da tarefa"
                    style="width:100%;padding:6px 10px;border:1px solid #93c5fd;border-radius:6px;font-size:13px;">
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <input type="date" wire:model="editDataLimite"
                        style="padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;">
                    <select wire:model="editResponsavel"
                        style="padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;">
                        <option value="0">— Responsável —</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nome }}</option>
                        @endforeach
                    </select>
                    <button wire:click="salvarEdicao" class="btn btn-primary btn-sm">Salvar</button>
                    <button wire:click="cancelarEdicao" class="btn btn-secondary btn-sm">Cancelar</button>
                </div>
                @error('editTitulo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>
            @else
            {{-- Modo visualização --}}
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:{{ $t->concluida ? '400' : '600' }};
                            color:{{ $t->concluida ? '#64748b' : '#1e293b' }};
                            text-decoration:{{ $t->concluida ? 'line-through' : 'none' }};">
                    {{ $t->titulo }}
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:3px;">
                    @if($t->data_limite)
                    @php
                        $dl = \Carbon\Carbon::parse($t->data_limite);
                        $vencida = !$t->concluida && $dl->isPast();
                        $dlCor = $vencida ? '#dc2626' : '#64748b';
                    @endphp
                    <span style="font-size:11px;color:{{ $dlCor }};display:inline-flex;align-items:center;gap:3px;">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $dl->format('d/m/Y') }}
                        @if($vencida) <strong>(vencida)</strong> @endif
                    </span>
                    @endif
                    @if($t->resp_nome)
                    <span style="font-size:11px;color:#64748b;display:inline-flex;align-items:center;gap:3px;">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $t->resp_nome }}
                    </span>
                    @endif
                    @if($t->concluida && $t->concluida_em)
                    <span style="font-size:11px;color:#16a34a;display:inline-flex;align-items:center;gap:3px;">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ \Carbon\Carbon::parse($t->concluida_em)->format('d/m/Y H:i') }}
                    </span>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:4px;flex-shrink:0;">
                <button wire:click="abrirEdicao({{ $t->id }})" title="Editar"
                    style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#e0f2fe;color:#0369a1;">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button wire:click="excluir({{ $t->id }})"
                    wire:confirm="Excluir esta tarefa?"
                    title="Excluir"
                    style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
            </div>
            @endif
        </div>
        @empty
        <p style="color:#64748b;font-size:13px;text-align:center;padding:24px 0;">
            Nenhuma tarefa cadastrada. Clique em "+ Tarefa" para adicionar.
        </p>
        @endforelse
    </div>

    {{-- ── Formulário nova tarefa ── --}}
    @if($mostrarForm)
    <div style="padding:14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;margin-bottom:12px;">
        <div style="font-size:13px;font-weight:600;color:#1e40af;margin-bottom:10px;">Nova Tarefa</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <input type="text" wire:model="novoTitulo" placeholder="Título da tarefa *"
                style="width:100%;padding:7px 10px;border:1px solid {{ $errors->has('novoTitulo') ? '#dc2626' : '#93c5fd' }};border-radius:6px;font-size:13px;">
            @error('novoTitulo') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <input type="date" wire:model="novaDataLimite"
                    style="padding:6px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;">
                <select wire:model="novaResponsavel"
                    style="padding:6px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;">
                    <option value="0">— Responsável —</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}">{{ $u->nome }}</option>
                    @endforeach
                </select>
                <button wire:click="salvarNova" class="btn btn-primary btn-sm">Salvar</button>
                <button wire:click="cancelarForm" class="btn btn-secondary btn-sm">Cancelar</button>
            </div>
        </div>
    </div>
    @else
    <button wire:click="abrirForm"
        style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px dashed #93c5fd;border-radius:8px;
               background:none;cursor:pointer;font-size:13px;font-weight:600;color:#2563a8;width:100%;justify-content:center;">
        + Tarefa
    </button>
    @endif
</div>
