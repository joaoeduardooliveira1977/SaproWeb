<div>
    {{-- Cabeçalho --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">📋 Andamentos</h2>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">
                Processo: <strong>{{ $processo->numero }}</strong> — {{ $processo->cliente->nome ?? '—' }}
            </p>
        </div>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('processos.show', $processo->id) }}"
                style="padding:10px 20px; background:#f1f5f9; color:#334155; border-radius:8px; font-size:14px; text-decoration:none;">
                ← Voltar
            </a>
            @if(!$mostrarFormulario)
            <button wire:click="novoAndamento"
                style="padding:10px 20px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                ➕ Novo Andamento
            </button>
            @endif
        </div>
    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
    <div style="background:#dcfce7; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#16a34a;">
        ✅ {{ session('sucesso') }}
    </div>
    @endif

    {{-- Formulário --}}
    @if($mostrarFormulario)
    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:24px; border-left:4px solid #2563a8;">
        <h3 style="font-size:16px; font-weight:700; color:#1a3a5c; margin-bottom:20px;">
            {{ $editandoId ? '✏️ Editar Andamento' : '➕ Novo Andamento' }}
        </h3>

        <div style="display:grid; grid-template-columns:200px 1fr; gap:16px; align-items:start;">
            {{-- Data --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Data *</label>
                <input wire:model="data" type="date"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                @error('data') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Descrição *</label>
                <textarea wire:model="descricao" rows="3"
                    placeholder="Descreva o andamento do processo..."
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none; resize:vertical;"></textarea>
                @error('descricao') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:12px; justify-content:flex-end;">
            <button wire:click="cancelar"
                style="padding:10px 20px; background:#f1f5f9; color:#334155; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
                Cancelar
            </button>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:10px 20px; background:#16a34a; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                <span wire:loading.remove>💾 Salvar</span>
                <span wire:loading>Salvando...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Confirmação de exclusão --}}
    @if($excluindoId)
    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:20px; margin-bottom:24px;">
        <p style="font-size:14px; color:#dc2626; font-weight:600; margin-bottom:12px;">
            ⚠️ Tem certeza que deseja excluir este andamento?
        </p>
        <div style="display:flex; gap:12px;">
            <button wire:click="cancelar"
                style="padding:8px 20px; background:#f1f5f9; color:#334155; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
                Cancelar
            </button>
            <button wire:click="excluir"
                style="padding:8px 20px; background:#dc2626; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                🗑️ Excluir
            </button>
        </div>
    </div>
    @endif

    {{-- Lista de andamentos --}}
    <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); overflow:hidden;">
        @if($andamentos->isEmpty())
        <div style="padding:60px; text-align:center; color:#94a3b8;">
            <div style="font-size:40px; margin-bottom:12px;">📋</div>
            <p style="font-size:15px; font-weight:600;">Nenhum andamento cadastrado</p>
            <p style="font-size:13px; margin-top:4px;">Clique em "Novo Andamento" para adicionar</p>
        </div>
        @else
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#64748b; width:120px;">DATA</th>
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#64748b;">DESCRIÇÃO</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#64748b; width:100px;">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($andamentos as $andamento)
                <tr style="border-bottom:1px solid #f1f5f9; {{ $loop->even ? 'background:#fafafa;' : '' }}">
                    <td style="padding:12px 16px; font-size:13px; color:#334155; font-weight:600; white-space:nowrap;">
                        {{ $andamento->data->format('d/m/Y') }}
                    </td>
                    <td style="padding:12px 16px; font-size:13px; color:#475569; line-height:1.5;">
                        {{ $andamento->descricao }}
                    </td>
                    <td style="padding:12px 16px; text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button wire:click="editar({{ $andamento->id }})" title="Editar"
                                style="padding:6px 10px; background:#e0f2fe; color:#0369a1; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                                ✏️
                            </button>
                            <button wire:click="confirmarExclusao({{ $andamento->id }})" title="Excluir"
                                style="padding:6px 10px; background:#fee2e2; color:#dc2626; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:12px 16px; font-size:13px; color:#64748b; border-top:1px solid #f1f5f9;">
            Total: {{ $andamentos->count() }} andamento(s)
        </div>
        @endif
    </div>
</div>
