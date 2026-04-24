<div>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--text);margin:0 0 4px;">Modelos de Contrato</h1>
        <p style="font-size:13px;color:var(--muted);margin:0;">Crie e edite os templates de contrato usados na geração automática.</p>
    </div>
    <button wire:click="abrirModal()" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Modelo
    </button>
</div>

{{-- Info variáveis --}}
<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:12px;color:#0369a1;line-height:1.8;">
    <strong>Variáveis disponíveis:</strong>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}cliente{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}cpf_cnpj{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}advogado{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}oab{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}processo{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}tipo_acao{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}vara{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}valor{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}data_inicio{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}escritorio{{'}}'}}</code>
    <code style="background:#e0f2fe;padding:1px 5px;border-radius:4px;margin:0 2px;">{{'{{'}}data_hoje{{'}}'}}</code>
</div>

{{-- Lista de modelos --}}
@if($modelos->isEmpty())
<div style="text-align:center;padding:48px;color:var(--muted);">
    <div style="font-size:2.5rem;margin-bottom:12px;">📄</div>
    <div style="font-weight:600;margin-bottom:6px;">Nenhum modelo cadastrado</div>
    <div style="font-size:13px;margin-bottom:16px;">Crie modelos de contrato para agilizar a geração de documentos.</div>
    <button wire:click="abrirModal()" style="padding:8px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
        Criar primeiro modelo
    </button>
</div>
@else
<div style="display:grid;gap:12px;">
    @foreach($modelos as $m)
    @php
    $tipoLabels = ['honorario_processo'=>'Honorário de Processo','consultivo'=>'Consultivo','avulso'=>'Avulso'];
    $tipoCores  = ['honorario_processo'=>['#1d4ed8','#dbeafe'],'consultivo'=>['#15803d','#dcfce7'],'avulso'=>['#b45309','#fef3c7']];
    $cor = $tipoCores[$m->tipo] ?? ['#6d28d9','#ede9fe'];
    @endphp
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px 18px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <span style="font-weight:700;font-size:14px;color:var(--text);">{{ $m->nome }}</span>
                @if(!$m->ativo)
                    <span style="font-size:11px;background:#f3f4f6;color:#6b7280;padding:1px 6px;border-radius:20px;">Inativo</span>
                @endif
            </div>
            <div style="display:flex;gap:6px;align-items:center;">
                <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:{{ $cor[1] }};color:{{ $cor[0] }};">
                    {{ $tipoLabels[$m->tipo] ?? $m->tipo }}
                </span>
                <span style="font-size:11px;color:var(--muted);">{{ mb_strimwidth($m->texto, 0, 80, '…') }}</span>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button wire:click="toggleAtivo({{ $m->id }})" title="{{ $m->ativo ? 'Desativar' : 'Ativar' }}"
                style="padding:6px 12px;border:1px solid var(--border);border-radius:6px;background:transparent;cursor:pointer;font-size:12px;color:var(--muted);">
                {{ $m->ativo ? 'Desativar' : 'Ativar' }}
            </button>
            <button wire:click="abrirModal({{ $m->id }})"
                style="padding:6px 10px;border:1.5px solid #bfdbfe;border-radius:6px;background:#eff6ff;cursor:pointer;color:#2563eb;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button wire:click="excluir({{ $m->id }})" wire:confirm="Excluir este modelo?"
                style="padding:6px 10px;border:1.5px solid #fecaca;border-radius:6px;background:#fff1f1;cursor:pointer;color:#dc2626;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Modal edição --}}
@if($modal)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;" wire:click.self="$set('modal',false)">
    <div style="background:var(--white);border-radius:12px;width:100%;max-width:760px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25);">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <div style="font-weight:700;font-size:16px;">{{ $modeloId ? 'Editar Modelo' : 'Novo Modelo de Contrato' }}</div>
            <button wire:click="$set('modal',false)" style="background:none;border:none;cursor:pointer;color:var(--muted);">✕</button>
        </div>

        <div style="padding:20px 24px;">
            {{-- Templates rápidos --}}
            @if(!$modeloId)
            <div style="margin-bottom:16px;">
                <div style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:8px;">CARREGAR TEMPLATE PADRÃO</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button wire:click="selecionarTemplate('honorario_processo')" style="padding:6px 12px;border:1.5px solid #bfdbfe;border-radius:6px;background:#eff6ff;color:#2563eb;font-size:12px;font-weight:600;cursor:pointer;">⚖️ Honorário de Processo</button>
                    <button wire:click="selecionarTemplate('consultivo')" style="padding:6px 12px;border:1.5px solid #d1fae5;border-radius:6px;background:#f0fdf4;color:#15803d;font-size:12px;font-weight:600;cursor:pointer;">📋 Consultivo</button>
                    <button wire:click="selecionarTemplate('avulso')" style="padding:6px 12px;border:1.5px solid #fef3c7;border-radius:6px;background:#fffbeb;color:#b45309;font-size:12px;font-weight:600;cursor:pointer;">📄 Serviço Avulso</button>
                </div>
            </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px;">NOME DO MODELO *</label>
                    <input type="text" wire:model="nome" placeholder="Ex: Contrato Honorários Cível" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;">
                    @error('nome') <span style="color:#dc2626;font-size:11px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px;">TIPO</label>
                    <select wire:model="tipo" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                        <option value="honorario_processo">Honorário de Processo</option>
                        <option value="consultivo">Consultivo / Retainer</option>
                        <option value="avulso">Serviço Avulso</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px;">TEXTO DO CONTRATO *</label>
                <textarea wire:model="texto" rows="18" placeholder="Digite o texto do contrato. Use {{'{{'}}cliente{{'}}'}}, {{'{{'}}advogado{{'}}'}}, etc. para inserir dados automaticamente."
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-family:monospace;background:var(--white);color:var(--text);resize:vertical;box-sizing:border-box;line-height:1.6;"></textarea>
                @error('texto') <span style="color:#dc2626;font-size:11px;">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <input type="checkbox" wire:model="ativo" id="ativoModelo" style="width:16px;height:16px;accent-color:#2563eb;">
                <label for="ativoModelo" style="font-size:13px;color:var(--text);cursor:pointer;">Modelo ativo (disponível na criação de contratos)</label>
            </div>
        </div>

        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="$set('modal',false)" style="padding:8px 18px;border:1.5px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;font-size:13px;color:var(--text);">Cancelar</button>
            <button wire:click="salvar" style="padding:8px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;">Salvar Modelo</button>
        </div>
    </div>
</div>
@endif
</div>
