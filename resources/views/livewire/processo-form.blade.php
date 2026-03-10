<div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">
                {{ $processoId ? '✏️ Editar Processo' : '➕ Novo Processo' }}
            </h2>
        </div>
        <a href="{{ route('processos') }}" style="font-size:13px; color:#64748b; text-decoration:none;">← Voltar</a>
    </div>

    @if(session('sucesso'))
    <div style="background:#dcfce7; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#16a34a;">
        ✅ {{ session('sucesso') }}
    </div>
    @endif

    <div style="background:white; border-radius:12px; padding:32px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            {{-- 1. Cliente (largura total) --}}
            <div style="grid-column: 1 / -1;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Cliente *</label>
                <select wire:model="cliente_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($clientes as $c)
                    <option value="{{ $c->id }}">{{ $c->nome }}</option>
                    @endforeach
                </select>
                @error('cliente_id') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            {{-- 2. Número do Processo --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Número do Processo *</label>
                <input wire:model="numero" type="text" placeholder="0000000-00.0000.0.00.0000"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                @error('numero') <span style="color:#dc2626; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            {{-- 3. Assunto --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Assunto</label>
                <select wire:model="assunto_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($assuntos as $a)
                    <option value="{{ $a->id }}">{{ $a->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Parte Contrária (largura total) --}}
            <div style="grid-column: 1 / -1;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Parte Contrária</label>
                <input wire:model="parte_contraria" type="text"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>

            {{-- 5. Tipo de Ação --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Tipo de Ação</label>
                <select wire:model="tipo_acao_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($tiposAcao as $t)
                    <option value="{{ $t->id }}">{{ $t->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 6. Tipo de Processo --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Tipo de Processo</label>
                <select wire:model="tipo_processo_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($tiposProcesso as $t)
                    <option value="{{ $t->id }}">{{ $t->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 7. Repartição --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Repartição/Fórum</label>
                <select wire:model="reparticao_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($reparticoes as $r)
                    <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 8. Secretaria/Vara --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Secretaria/Vara</label>
                <input wire:model="vara" type="text"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>

            {{-- 9. Secretaria --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Secretaria</label>
                <select wire:model="secretaria_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($secretarias as $s)
                    <option value="{{ $s->id }}">{{ $s->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 10. Distribuição --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Data de Distribuição</label>
                <input wire:model="data_distribuicao" type="date"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>

            {{-- 11. Valor da Causa --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Valor da Causa</label>
                <input wire:model="valor_causa" type="text" placeholder="0,00"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>

            {{-- 12. Valor em Risco --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Valor em Risco</label>
                <input wire:model="valor_risco" type="text" placeholder="0,00"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>

            {{-- 13. Grau de Risco --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Grau de Risco</label>
                <select wire:model="risco_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($riscos as $r)
                    <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 14. Situação/Fase --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Situação/Fase</label>
                <select wire:model="fase_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($fases as $f)
                    <option value="{{ $f->id }}">{{ $f->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 15. Status --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Status</label>
                <select wire:model="status" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="Ativo">Ativo</option>
                    <option value="Suspenso">Suspenso</option>
                    <option value="Arquivado">Arquivado</option>
                    <option value="Encerrado">Encerrado</option>
                </select>
            </div>

            {{-- 16. Juiz --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Juiz</label>
                <select wire:model="juiz_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($juizes as $j)
                    <option value="{{ $j->id }}">{{ $j->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 17. Advogado --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Advogado Responsável</label>
                <select wire:model="advogado_id" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
                    <option value="">— Selecione —</option>
                    @foreach($advogados as $a)
                    <option value="{{ $a->id }}">{{ $a->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 18. Observações (largura total) --}}
            <div style="grid-column: 1 / -1;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Observações</label>
                <textarea wire:model="observacoes" rows="3"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none; resize:vertical;"></textarea>
            </div>

        </div>

        <div style="margin-top:24px; display:flex; gap:12px; justify-content:flex-end;">
            <a href="{{ route('processos') }}"
                style="padding:10px 24px; background:#f1f5f9; color:#334155; border-radius:8px; font-size:14px; text-decoration:none;">
                Cancelar
            </a>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:10px 24px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                <span wire:loading.remove>💾 Salvar Processo</span>
                <span wire:loading>Salvando...</span>
            </button>
        </div>
    </div>
</div>
