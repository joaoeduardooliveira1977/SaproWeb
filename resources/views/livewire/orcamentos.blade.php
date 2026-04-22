<div>

{{-- ── Cabeçalho ─────────────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Orçamentos & Propostas</h1>
        <p style="font-size:13px;color:var(--muted);margin:4px 0 0;">Propostas de honorários para leads e clientes</p>
    </div>
    <button wire:click="novo()" class="btn btn-primary btn-sm">
        <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nova proposta
    </button>
</div>

{{-- ── KPIs ────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:20px;">
    @foreach([
        ['label'=>'Total','value'=>$kpis['total'],'fmt'=>'int','color'=>'var(--primary)'],
        ['label'=>'Enviados','value'=>$kpis['enviados'],'fmt'=>'int','color'=>'#2563a8'],
        ['label'=>'Aceitos','value'=>$kpis['aceitos'],'fmt'=>'int','color'=>'#16a34a'],
        ['label'=>'Pipeline','value'=>$kpis['valor_pipeline'],'fmt'=>'brl','color'=>'#7c3aed'],
        ['label'=>'Convertido','value'=>$kpis['valor_aceito'],'fmt'=>'brl','color'=>'#16a34a'],
    ] as $kpi)
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 18px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">{{ $kpi['label'] }}</div>
        <div style="font-size:22px;font-weight:800;color:{{ $kpi['color'] }};margin-top:4px;">
            @if($kpi['fmt'] === 'brl') R$ {{ number_format($kpi['value'],2,',','.') }}
            @else {{ $kpi['value'] }}
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filtros ──────────────────────────────────────────────────── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
    <input type="text" wire:model.live.debounce.400ms="filtroBusca" placeholder="Buscar cliente, título ou nº..."
           class="form-control" style="font-size:13px;flex:1;min-width:200px;">
    <select wire:model.live="filtroStatus" class="form-control" style="font-size:13px;width:auto;">
        <option value="">Todos os status</option>
        @foreach(\App\Models\Orcamento::$statusLabels as $val => $lbl)
        <option value="{{ $val }}">{{ $lbl }}</option>
        @endforeach
    </select>
</div>

{{-- ── Lista ───────────────────────────────────────────────────── --}}
<div style="display:flex;flex-direction:column;gap:12px;">
    @forelse($orcamentos as $orc)
    @php
        $cor = \App\Models\Orcamento::$statusCores[$orc->status] ?? '#64748b';
        $lbl = \App\Models\Orcamento::$statusLabels[$orc->status] ?? $orc->status;
    @endphp
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:18px 20px;" wire:key="orc-{{ $orc->id }}">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                    <span style="font-size:11px;font-weight:700;color:var(--muted);">{{ $orc->numero }}</span>
                    <span style="background:{{ $cor }}20;color:{{ $cor }};padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;">{{ $lbl }}</span>
                    @if($orc->area_direito)
                    <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:99px;font-size:11px;">{{ $orc->area_direito }}</span>
                    @endif
                </div>
                <div style="font-size:15px;font-weight:700;color:var(--primary);margin-bottom:2px;">{{ $orc->titulo }}</div>
                <div style="font-size:13px;color:#475569;">{{ $orc->nome_cliente }}</div>
                @if($orc->email_cliente)
                <div style="font-size:12px;color:var(--muted);">{{ $orc->email_cliente }}</div>
                @endif
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-size:20px;font-weight:800;color:#16a34a;">
                    R$ {{ number_format($orc->valor, 2, ',', '.') }}
                </div>
                @if($orc->parcelas > 1)
                <div style="font-size:11px;color:var(--muted);">{{ $orc->parcelas }}x de R$ {{ number_format($orc->valor_parcela, 2, ',', '.') }}</div>
                @endif
                @if($orc->validade)
                <div style="font-size:11px;color:{{ $orc->isExpirado() ? '#ef4444' : 'var(--muted)' }};margin-top:4px;">
                    {{ $orc->isExpirado() ? 'EXPIRADO' : 'Válido até' }} {{ $orc->validade->format('d/m/Y') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Ações --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:12px;border-top:1px solid var(--border);align-items:center;">
            <a href="{{ route('orcamentos.pdf', $orc->id) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:7px;background:#f1f5f9;color:#475569;text-decoration:none;border:1px solid var(--border);">
                <svg aria-hidden="true" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                PDF
            </a>
            <button wire:click="editar({{ $orc->id }})"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:7px;background:#f1f5f9;color:#475569;border:1px solid var(--border);cursor:pointer;">
                <svg aria-hidden="true" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar
            </button>

            @if($orc->status === 'rascunho')
            <button wire:click="marcarEnviado({{ $orc->id }})"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:7px;background:#eff6ff;color:#2563a8;border:1px solid #bfdbfe;cursor:pointer;">
                Marcar como enviado
            </button>
            @endif

            @if($orc->status === 'enviado')
            <button wire:click="marcarAceito({{ $orc->id }})"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:7px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;cursor:pointer;">
                <svg aria-hidden="true" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Aceito
            </button>
            <button wire:click="abrirRecusa({{ $orc->id }})"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:7px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;cursor:pointer;">
                Recusado
            </button>
            @endif

            <button wire:click="excluir({{ $orc->id }})"
                    wire:confirm="Excluir esta proposta?"
                    style="margin-left:auto;display:inline-flex;align-items:center;gap:4px;font-size:12px;padding:6px 10px;border-radius:7px;background:none;color:#ef4444;border:none;cursor:pointer;">
                <svg aria-hidden="true" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </button>
        </div>
    </div>
    @empty
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:48px;text-align:center;color:var(--muted);">
        Nenhuma proposta encontrada.
    </div>
    @endforelse
</div>

{{-- ── Modal novo/editar ───────────────────────────────────────── --}}
@if($modal)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto;"
     wire:click.self="fecharModal">
    <div style="background:var(--white);border-radius:16px;width:100%;max-width:640px;box-shadow:0 20px 60px rgba(0,0,0,.2);my:16px;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;color:var(--primary);margin:0;">
                {{ $orcId ? 'Editar proposta' : 'Nova proposta' }}
            </h3>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;">&times;</button>
        </div>
        <div style="padding:20px 24px;display:flex;flex-direction:column;gap:14px;max-height:75vh;overflow-y:auto;">

            {{-- Lead / Oportunidade --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Oportunidade CRM</label>
                    <select wire:model="opOportunidade" class="form-control" style="font-size:13px;">
                        <option value="">Nenhuma</option>
                        @foreach($oportunidades as $op)
                        <option value="{{ $op->id }}">{{ $op->nome }} — {{ \App\Models\CrmOportunidade::$etapas[$op->etapa] ?? $op->etapa }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Cliente cadastrado</label>
                    <select wire:model="opPessoa" class="form-control" style="font-size:13px;">
                        <option value="">Nenhum</option>
                        @foreach($pessoas as $p)
                        <option value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Dados do destinatário --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Nome do cliente / lead *</label>
                <input type="text" wire:model="opNome" class="form-control" style="font-size:13px;" placeholder="Nome completo">
                @error('opNome')<p style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">E-mail</label>
                    <input type="email" wire:model="opEmail" class="form-control" style="font-size:13px;">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Telefone</label>
                    <input type="text" wire:model="opTelefone" class="form-control" style="font-size:13px;">
                </div>
            </div>

            {{-- Objeto --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Título da proposta *</label>
                <input type="text" wire:model="opTitulo" class="form-control" style="font-size:13px;" placeholder="Ex: Ação Trabalhista — Rescisão Indireta">
                @error('opTitulo')<p style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Área do direito</label>
                    <select wire:model="opArea" class="form-control" style="font-size:13px;">
                        <option value="">Selecione...</option>
                        @foreach(\App\Models\CrmOportunidade::$areas as $area)
                        <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Válida até</label>
                    <input type="date" wire:model="opValidade" class="form-control" style="font-size:13px;">
                </div>
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Descrição dos serviços</label>
                <textarea wire:model="opDescricao" rows="3" class="form-control" style="font-size:13px;resize:vertical;"
                          placeholder="Descreva os serviços incluídos na proposta..."></textarea>
            </div>

            {{-- Honorários --}}
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:14px;">
                <div style="font-size:12px;font-weight:700;color:var(--primary);margin-bottom:12px;">Honorários</div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Modalidade</label>
                    <select wire:model.live="opTipo" class="form-control" style="font-size:13px;">
                        @foreach(\App\Models\Orcamento::$tiposHonorario as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                    @if(in_array($opTipo, ['fixo','sucesso','hora']))
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">
                            {{ $opTipo === 'hora' ? 'Valor por hora (R$)' : 'Valor (R$)' }}
                        </label>
                        <input type="text" wire:model="opValor" class="form-control" style="font-size:13px;" placeholder="0,00">
                    </div>
                    @endif
                    @if(in_array($opTipo, ['percentual','sucesso']))
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">% sobre o êxito</label>
                        <input type="number" wire:model="opPercentual" step="0.5" min="0" class="form-control" style="font-size:13px;" placeholder="Ex: 20">
                    </div>
                    @endif
                    @if(in_array($opTipo, ['fixo','sucesso']))
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Parcelas</label>
                        <input type="number" wire:model="opParcelas" min="1" max="60" class="form-control" style="font-size:13px;" placeholder="1">
                    </div>
                    @endif
                </div>
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Observações</label>
                <textarea wire:model="opObs" rows="2" class="form-control" style="font-size:13px;resize:vertical;"
                          placeholder="Informações adicionais, exclusões, condições..."></textarea>
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="fecharModal" class="btn btn-secondary btn-sm">Cancelar</button>
            <button wire:click="salvar" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                <span wire:loading wire:target="salvar">Salvando...</span>
                <span wire:loading.remove wire:target="salvar">Salvar proposta</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal recusa ─────────────────────────────────────────────── --}}
@if($modalRecusa)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:var(--white);border-radius:16px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:#dc2626;margin:0;">Proposta recusada</h3>
        </div>
        <div style="padding:20px 24px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:6px;">Motivo da recusa (opcional)</label>
            <textarea wire:model="motivoRecusa" rows="3" class="form-control" style="font-size:13px;resize:vertical;"
                      placeholder="Ex: valor acima do esperado, cliente optou por outro escritório..."></textarea>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="$set('modalRecusa', false)" class="btn btn-secondary btn-sm">Cancelar</button>
            <button wire:click="confirmarRecusa" class="btn btn-sm" style="background:#dc2626;color:#fff;border:none;">Confirmar recusa</button>
        </div>
    </div>
</div>
@endif

</div>
