<div>
{{-- ══ 4 KPI Cards ══════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:18px;">

    {{-- KPI 1: Receita Mensal --}}
    <div style="background:var(--white);border-radius:10px;padding:14px 16px;border:1px solid var(--border);border-left:4px solid #2563eb;">
        <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Receita Mensal</div>
        <div style="font-size:20px;font-weight:800;color:#2563eb;margin-top:4px;">
            @if($kpiReceitaMensal['valor'] > 0)
                R$ {{ number_format($kpiReceitaMensal['valor'], 2, ',', '.') }}
            @else
                —
            @endif
        </div>
        <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $kpiReceitaMensal['label'] }}</div>
    </div>

    {{-- KPI 2: Em Aberto --}}
    <div style="background:var(--white);border-radius:10px;padding:14px 16px;border:1px solid var(--border);border-left:4px solid {{ $kpiEmAberto > 0 ? '#dc2626' : '#94a3b8' }};">
        <div style="font-size:10px;color:{{ $kpiEmAberto > 0 ? '#dc2626' : 'var(--muted)' }};font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Em Aberto</div>
        <div style="font-size:20px;font-weight:800;color:{{ $kpiEmAberto > 0 ? '#dc2626' : 'var(--muted)' }};margin-top:4px;">
            R$ {{ number_format($kpiEmAberto, 2, ',', '.') }}
        </div>
    </div>

    {{-- KPI 3: Recebido no Ano --}}
    <div style="background:var(--white);border-radius:10px;padding:14px 16px;border:1px solid var(--border);border-left:4px solid #16a34a;">
        <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Recebido {{ now()->year }}</div>
        <div style="font-size:20px;font-weight:800;color:#16a34a;margin-top:4px;">R$ {{ number_format($kpiRecebidoAno, 2, ',', '.') }}</div>
    </div>

    {{-- KPI 4: Próximo Vencimento --}}
    <div style="background:var(--white);border-radius:10px;padding:14px 16px;border:1px solid var(--border);border-left:4px solid #f59e0b;">
        <div style="font-size:10px;color:#b45309;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Próximo Vcto.</div>
        @if($proximoLanc)
        <div style="font-size:16px;font-weight:800;color:#b45309;margin-top:4px;">{{ $proximoLanc->vencimento->format('d/m/Y') }}</div>
        <div style="font-size:10px;color:var(--muted);">{{ Str::limit($proximoLanc->descricao, 28) }} · R$ {{ number_format($proximoLanc->valor,2,',','.') }}</div>
        @else
        <div style="font-size:14px;font-weight:600;color:var(--muted);margin-top:4px;">—</div>
        @endif
    </div>
</div>

{{-- ══ Contratos e Receitas Ativas ══════════════════════════════════ --}}
@if($contratos->isNotEmpty() || $receitasProcesso->isNotEmpty())
<div style="background:var(--white);border-radius:10px;border:1px solid var(--border);padding:14px 18px;margin-bottom:14px;">
    <div style="font-size:12px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px;">Receitas Recorrentes</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach($contratos as $ct)
        @php $badge = match($ct->status) {'ativo'=>['#dcfce7','#166534','#bbf7d0'],'suspenso'=>['#fef9c3','#92400e','#fde68a'],default=>['#f1f5f9','#475569','#cbd5e1']}; @endphp
        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:10px;flex-shrink:0;">
            <div>
                <div style="font-size:12px;font-weight:700;color:var(--text);">{{ $ct->descricao }}</div>
                <div style="font-size:11px;color:var(--muted);">R$ {{ number_format($ct->valor,2,',','.') }}/mês · Dia {{ $ct->dia_vencimento }}</div>
            </div>
            <span style="background:{{ $badge[0] }};color:{{ $badge[1] }};border:1px solid {{ $badge[2] }};border-radius:4px;padding:1px 7px;font-size:10px;font-weight:700;">{{ ucfirst($ct->status) }}</span>
            <button wire:click="abrirModalContrato({{ $ct->id }})" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:13px;" title="Editar">✎</button>
            @if($ct->status === 'ativo')
            <button wire:click="suspenderContrato({{ $ct->id }})" wire:confirm="Suspender este contrato?" style="background:none;border:none;cursor:pointer;color:#b45309;font-size:11px;">Suspender</button>
            @endif
        </div>
        @endforeach
        @foreach($receitasProcesso as $rp)
        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:8px 12px;flex-shrink:0;">
            <div style="font-size:12px;font-weight:700;color:#0369a1;">Honorários por Processos</div>
            <div style="font-size:11px;color:var(--muted);">R$ {{ number_format($rp->valor_unitario,2,',','.') }}/processo · Dia {{ $rp->dia_vencimento }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ══ Botões de Ação ═══════════════════════════════════════════════ --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
    <button wire:click="abrirModalContrato()" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#eff6ff;color:#2563eb;border:1.5px solid #bfdbfe;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Contrato Mensal
    </button>
    <button wire:click="abrirModalProcesso()" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#f0fdf4;color:#16a34a;border:1.5px solid #86efac;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Receita por Processos
    </button>
    <button wire:click="abrirModalPontual()" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#faf5ff;color:#7c3aed;border:1.5px solid #e9d5ff;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Receita Pontual
    </button>
</div>

{{-- ══ Filtros ══════════════════════════════════════════════════════ --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
    <select wire:model.live="filtroOrigem"
        style="flex:1;min-width:120px;padding:6px 10px;border:1.5px solid var(--border);
               border-radius:8px;font-size:12px;background:var(--white);cursor:pointer;">
        <option value="">Todas origens</option>
        <option value="mensal">Mensal</option>
        <option value="processo">Por Processo</option>
        <option value="pontual">Pontual</option>
    </select>

    <select wire:model.live="filtroStatus"
        style="flex:1;min-width:120px;padding:6px 10px;border:1.5px solid var(--border);
               border-radius:8px;font-size:12px;background:var(--white);cursor:pointer;">
        <option value="">Todos status</option>
        <option value="previsto">Previsto</option>
        <option value="recebido">Recebido</option>
        <option value="parcial">Parcial</option>
        <option value="vencido">Vencido</option>
        <option value="cancelado">Cancelado</option>
    </select>

    <select wire:model.live="filtroAno"
        style="flex:1;min-width:90px;padding:6px 10px;border:1.5px solid var(--border);
               border-radius:8px;font-size:12px;background:var(--white);cursor:pointer;">
        <option value="">Todos anos</option>
        @foreach($anos as $ano)
        <option value="{{ $ano }}">{{ $ano }}</option>
        @endforeach
    </select>

    <select wire:model.live="filtroMes"
        style="flex:1;min-width:110px;padding:6px 10px;border:1.5px solid var(--border);
               border-radius:8px;font-size:12px;background:var(--white);cursor:pointer;">
        <option value="">Todos meses</option>
        @foreach($meses as $num => $nome)
        <option value="{{ $num }}">{{ $nome }}</option>
        @endforeach
    </select>
</div>

{{-- ══ Grid Lançamentos ═════════════════════════════════════════════ --}}
@if($lancamentos->isEmpty())
<div style="text-align:center;padding:32px 0;color:var(--muted);font-size:13px;">
    Nenhum lançamento encontrado para os filtros aplicados.
</div>
@else
<div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">
    <div style="display:grid;grid-template-columns:90px 1fr 85px 85px 70px 80px 80px auto;gap:6px;padding:8px 12px;background:#f8fafc;border-bottom:1px solid var(--border);">
        @foreach(['Competência','Descrição','Vencimento','Valor','Status','Forma','Recebido em',''] as $col)
        <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">{{ $col }}</div>
        @endforeach
    </div>

    @foreach($lancamentos as $l)
    @php
        $stBg = match($l->status) {
            'recebido' => ['#dcfce7','#166534'],
            'parcial'  => ['#fff7ed','#9a3412'],
            'atrasado','vencido' => ['#fee2e2','#991b1b'],
            'cancelado'=> ['#f1f5f9','#94a3b8'],
            default    => ['#eff6ff','#1e40af'],
        };
        $origemBg = match($l->origem_tipo) {
            'mensal'   => ['#f0f9ff','#0369a1'],
            'processo' => ['#faf5ff','#7c3aed'],
            'pontual'  => ['#f0fdf4','#15803d'],
            default    => ['#f1f5f9','#475569'],
        };
    @endphp
    <div style="display:grid;grid-template-columns:90px 1fr 85px 85px 70px 80px 80px auto;gap:6px;padding:9px 12px;border-bottom:1px solid var(--border);align-items:center;"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">

        {{-- Competência/Origem --}}
        <div>
            <span style="font-size:9px;font-weight:700;padding:1px 5px;border-radius:99px;background:{{ $origemBg[0] }};color:{{ $origemBg[1] }};">{{ ucfirst($l->origem_tipo) }}</span>
        </div>

        {{-- Descrição --}}
        <div>
            <div style="font-size:12px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $l->descricao }}</div>
            @if($l->numero_parcela)<div style="font-size:10px;color:var(--muted);">Parcela {{ $l->numero_parcela }}</div>@endif
        </div>

        {{-- Vencimento --}}
        <div style="font-size:11px;{{ in_array($l->status,['atrasado','vencido']) ? 'color:#dc2626;font-weight:700;' : 'color:var(--muted);' }}">
            {{ $l->vencimento->format('d/m/Y') }}
        </div>

        {{-- Valor --}}
        <div style="font-size:12px;font-weight:700;color:#16a34a;">R$ {{ number_format($l->valor,2,',','.') }}</div>

        {{-- Status badge --}}
        <div>
            <span style="background:{{ $stBg[0] }};color:{{ $stBg[1] }};border-radius:99px;padding:2px 7px;font-size:10px;font-weight:700;">{{ ucfirst($l->status) }}</span>
        </div>

        {{-- Forma --}}
        <div style="font-size:11px;color:var(--muted);">{{ $l->forma_pagamento ? ucfirst($l->forma_pagamento) : '—' }}</div>

        {{-- Recebido em --}}
        <div style="font-size:11px;color:var(--muted);">
            @if($l->data_pagamento){{ $l->data_pagamento->format('d/m/Y') }}@else—@endif
        </div>

        {{-- Ações --}}
        <div style="display:flex;gap:3px;justify-content:flex-end;">
            @if(in_array($l->status, ['previsto','atrasado','vencido','parcial']))
            <button wire:click="abrirBaixa({{ $l->id }})"
                style="padding:3px 8px;background:#f0fdf4;color:#16a34a;border:1px solid #86efac;border-radius:5px;font-size:10px;cursor:pointer;font-weight:700;"
                title="Registrar Baixa">✓ Baixar</button>
            @endif
        </div>
    </div>
    @endforeach
</div>

@if($lancamentos->hasPages())
<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;font-size:12px;color:var(--muted);">
    <span>{{ $lancamentos->total() }} registro(s)</span>
    <div style="display:flex;gap:6px;">
        <button class="page-btn" wire:click="previousPage" @disabled(!$lancamentos->onFirstPage())>‹ Ant.</button>
        <span class="page-current">{{ $lancamentos->currentPage() }}/{{ $lancamentos->lastPage() }}</span>
        <button class="page-btn" wire:click="nextPage" @disabled(!$lancamentos->hasMorePages())>Próx. ›</button>
    </div>
</div>
@endif
@endif

{{-- ══════════════════════════════════════════════════════════════════
     MODAL: CONTRATO MENSAL
══════════════════════════════════════════════════════════════════ --}}
@if($modalContrato)
<div class="modal-backdrop" style="display:flex;" wire:click.self="$set('modalContrato', false)">
<div class="modal" style="max-width:600px;">
    <div class="modal-header">
        <h3 style="margin:0;font-size:15px;font-weight:700;">{{ $contratoId ? 'Editar Contrato Mensal' : 'Novo Contrato Mensal' }}</h3>
        <button wire:click="$set('modalContrato', false)" class="btn-action" style="width:30px;height:30px;">✕</button>
    </div>
    <div style="padding:18px 0 0;max-height:70vh;overflow-y:auto;">
    <form wire:submit.prevent="salvarContrato">

        {{-- Seção 1: Dados Gerais --}}
        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border);">Dados Gerais</div>
            <div class="form-group">
                <label class="form-label">Descrição *</label>
                <input type="text" wire:model="cm_descricao" placeholder="Ex: Assessoria Jurídica Mensal"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('cm_descricao') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;">
                @error('cm_descricao')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">
                    Responsável
                    <span style="font-weight:400;color:var(--muted);">(opcional)</span>
                </label>
                <input wire:model="cm_responsavel" type="text" placeholder="Ex: Dr. João Silva"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select wire:model="cm_status" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                        <option value="ativo">Ativo</option>
                        <option value="suspenso">Suspenso</option>
                        <option value="encerrado">Encerrado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <input type="text" wire:model="cm_observacoes" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                </div>
            </div>
        </div>

        {{-- Seção 2: Dados Financeiros --}}
        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border);">Dados Financeiros</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Valor Mensal (R$) *</label>
                    <input type="number" step="0.01" wire:model="cm_valor"
                        style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('cm_valor') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;">
                    @if($cm_valor > 0)
                    <div style="font-size:10px;color:#16a34a;margin-top:3px;">Parcela: R$ {{ number_format((float)$cm_valor,2,',','.') }}/mês</div>
                    @endif
                    @error('cm_valor')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Dia Vencimento *</label>
                    <select wire:model="cm_dia_vencimento" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                        @for($d=1;$d<=28;$d++)<option value="{{ $d }}">Dia {{ $d }}</option>@endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periodicidade</label>
                    <select wire:model="cm_periodicidade" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                        @foreach(\App\Models\ContratoMensal::periodicidadeLabels() as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Data Início *</label>
                    <input type="date" wire:model="cm_data_inicio"
                        style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('cm_data_inicio') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;">
                    @error('cm_data_inicio')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Data Fim</label>
                    <input type="date" wire:model="cm_data_fim"
                        style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                </div>
            </div>
        </div>

        {{-- Seção 3: Reajuste Automático (placeholder) --}}
        <div style="background:#f8faff;border:1.5px dashed #cbd5e1;border-radius:10px;padding:16px;margin-bottom:14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:13px;font-weight:700;color:var(--text);">Reajuste Automático</span>
                <span style="font-size:10px;background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:20px;font-weight:700;">EM BREVE</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;opacity:0.5;pointer-events:none;">
                <span style="font-size:13px;color:var(--muted);">Possui reajuste automático?</span>
                <div style="display:flex;gap:8px;">
                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;cursor:not-allowed;"><input type="radio" disabled> Sim</label>
                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;cursor:not-allowed;"><input type="radio" disabled checked> Não</label>
                </div>
            </div>
            <p style="font-size:11px;color:var(--muted);margin-top:10px;margin-bottom:0;">Índices disponíveis na próxima versão: IPCA, IGPM, Salário Mínimo, Manual.</p>
        </div>

        {{-- Seção 4: Dados de Cobrança --}}
        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border);">Dados de Cobrança</div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                @foreach(['pix'=>'PIX','boleto'=>'Boleto','transferencia'=>'Transferência','cartao'=>'Cartão'] as $fk=>$fv)
                <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;">
                    <input type="radio" wire:model="cm_forma_cobranca" value="{{ $fk }}">{{ $fv }}
                </label>
                @endforeach
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">E-mail Financeiro</label>
                    <input type="email" wire:model="cm_email" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" wire:model="cm_whatsapp" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;" placeholder="(11) 99999-9999">
                </div>
            </div>
            {{-- Toggle: Envio automático (funcional) + NF (em breve) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                <div style="background:#f8faff;border:1.5px solid var(--border);border-radius:8px;padding:12px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--text);">Enviar cobrança automática</div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">E-mail automático de vencimento</div>
                        </div>
                        <input type="checkbox" wire:model.live="cm_envio_automatico" id="toggle_envio_cli"
                            style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary);">
                    </div>
                    @if($cm_envio_automatico)
                    <div style="margin-top:8px;padding:6px 8px;background:#dcfce7;border-radius:6px;font-size:11px;color:#166534;">
                        E-mails de vencimento serão enviados automaticamente ao cliente.
                    </div>
                    @endif
                </div>
                <div style="background:#f8faff;border:1.5px dashed #cbd5e1;border-radius:8px;padding:12px;opacity:0.5;pointer-events:none;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--text);">Emitir NF automaticamente</div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Nota Fiscal eletrônica</div>
                        </div>
                        <div style="width:36px;height:20px;background:#e2e8f0;border-radius:20px;position:relative;cursor:not-allowed;">
                            <div style="width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:2px;left:2px;box-shadow:0 1px 3px rgba(0,0,0,.2);"></div>
                        </div>
                    </div>
                    <span style="font-size:10px;background:#e0f2fe;color:#0369a1;padding:1px 6px;border-radius:10px;font-weight:700;margin-top:6px;display:inline-block;">EM BREVE</span>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border);">
            @if($contratoId)
            <button type="button" wire:click="suspenderContrato({{ $contratoId }})" wire:confirm="Suspender este contrato?"
                style="padding:7px 14px;background:#fff7ed;color:#b45309;border:1.5px solid #fed7aa;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                Suspender
            </button>
            <button type="button" wire:click="encerrarContrato({{ $contratoId }})" wire:confirm="Encerrar este contrato?"
                style="padding:7px 14px;background:#fef2f2;color:#dc2626;border:1.5px solid #fca5a5;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                Encerrar
            </button>
            @endif
            <button type="button" wire:click="$set('modalContrato', false)" class="btn btn-secondary" style="font-size:12px;">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="font-size:12px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvarContrato">{{ $contratoId ? 'Salvar' : 'Criar Contrato' }}</span>
                <span wire:loading wire:target="salvarContrato">Salvando…</span>
            </button>
        </div>
    </form>
    </div>
</div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     MODAL: RECEITA POR PROCESSOS
══════════════════════════════════════════════════════════════════ --}}
@if($modalProcesso)
<div class="modal-backdrop" style="display:flex;" wire:click.self="$set('modalProcesso', false)">
<div class="modal" style="max-width:480px;">
    <div class="modal-header">
        <h3 style="margin:0;font-size:15px;font-weight:700;">Receita por Processos</h3>
        <button wire:click="$set('modalProcesso', false)" class="btn-action" style="width:30px;height:30px;">✕</button>
    </div>
    <div style="padding:16px 0 0;">
    <form wire:submit.prevent="salvarReceita">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label class="form-label">Valor por Processo (R$) *</label>
                <input type="number" step="0.01" wire:model="rp_valor_unit"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('rp_valor_unit') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;">
                @error('rp_valor_unit')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Dia Vencimento *</label>
                <select wire:model="rp_dia_vcto" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                    @for($d=1;$d<=28;$d++)<option value="{{ $d }}">Dia {{ $d }}</option>@endfor
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Data Início *</label>
            <input type="date" wire:model="rp_data_inicio" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
        </div>
        <div class="form-group">
            <label class="form-label">Observações</label>
            <input type="text" wire:model="rp_observacoes" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
        </div>

        {{-- Simulação --}}
        <div style="margin-bottom:12px;">
            <button type="button" wire:click="simularReceita"
                style="padding:6px 14px;background:#eff6ff;color:#2563eb;border:1.5px solid #bfdbfe;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                Simular Valor
            </button>
        </div>
        @if($rp_simulacao)
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px;margin-bottom:12px;">
            <div style="font-size:13px;font-weight:700;color:#166534;">
                {{ $rp_simulacao['processos'] }} processo(s) ativo(s) × R$ {{ number_format($rp_simulacao['unitario'],2,',','.') }}
                = <span style="font-size:15px;">R$ {{ number_format($rp_simulacao['total'],2,',','.') }}/mês</span>
            </div>
        </div>
        @endif

        <div style="display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" wire:click="$set('modalProcesso', false)" class="btn btn-secondary" style="font-size:12px;">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="font-size:12px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvarReceita">Salvar e Gerar Receita</span>
                <span wire:loading wire:target="salvarReceita">Salvando…</span>
            </button>
        </div>
    </form>
    </div>
</div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     MODAL: RECEITA PONTUAL
══════════════════════════════════════════════════════════════════ --}}
@if($modalPontual)
<div class="modal-backdrop" style="display:flex;" wire:click.self="$set('modalPontual', false)">
<div class="modal" style="max-width:480px;">
    <div class="modal-header">
        <h3 style="margin:0;font-size:15px;font-weight:700;">Receita Pontual</h3>
        <button wire:click="$set('modalPontual', false)" class="btn-action" style="width:30px;height:30px;">✕</button>
    </div>
    <div style="padding:16px 0 0;">
    <form wire:submit.prevent="salvarPontual">
        <div class="form-group">
            <label class="form-label">Descrição *</label>
            <input type="text" wire:model="pt_descricao" placeholder="Ex: Análise contratual, Parecer jurídico"
                style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('pt_descricao') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;">
            @error('pt_descricao')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label class="form-label">Valor (R$) *</label>
                <input type="number" step="0.01" wire:model="pt_valor"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('pt_valor') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;">
                @error('pt_valor')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Vencimento *</label>
                <input type="date" wire:model="pt_vencimento" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            @foreach(['pix'=>'PIX','boleto'=>'Boleto','transferencia'=>'Transferência','cartao'=>'Cartão'] as $fk=>$fv)
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;">
                <input type="radio" wire:model="pt_forma" value="{{ $fk }}">{{ $fv }}
            </label>
            @endforeach
        </div>
        <div class="form-group">
            <label class="form-label">Observações</label>
            <input type="text" wire:model="pt_observacoes" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
        </div>

        {{-- Toggle: Já recebido --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding:10px;background:#f8fafc;border-radius:8px;">
            <input type="checkbox" wire:model.live="pt_ja_recebido" id="ptJaRecebido" style="width:16px;height:16px;cursor:pointer;">
            <label for="ptJaRecebido" style="font-size:13px;cursor:pointer;">Já foi recebido?</label>
        </div>
        @if($pt_ja_recebido)
        <div class="form-group">
            <label class="form-label">Data de Recebimento</label>
            <input type="date" wire:model="pt_data_receb" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
        </div>
        @endif

        <div style="display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" wire:click="$set('modalPontual', false)" class="btn btn-secondary" style="font-size:12px;">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="font-size:12px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvarPontual">{{ $pt_ja_recebido ? 'Receber Agora' : 'Salvar Receita' }}</span>
                <span wire:loading wire:target="salvarPontual">Salvando…</span>
            </button>
        </div>
    </form>
    </div>
</div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     MODAL: BAIXA FINANCEIRA
══════════════════════════════════════════════════════════════════ --}}
@if($modalBaixa && $baixaLancDados)
<div class="modal-backdrop" style="display:flex;" wire:click.self="fecharBaixa">
<div class="modal" style="max-width:480px;">
    <div class="modal-header">
        <h3 style="margin:0;font-size:15px;font-weight:700;">Registrar Baixa</h3>
        <button wire:click="fecharBaixa" class="btn-action" style="width:30px;height:30px;">✕</button>
    </div>
    <div style="padding:16px 0 0;">

    {{-- Dados do lançamento --}}
    <div style="background:#f8fafc;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:12px;">
        <div style="font-weight:700;color:var(--text);">{{ $baixaLancDados['descricao'] }}</div>
        <div style="color:var(--muted);margin-top:3px;">Valor: R$ {{ number_format($baixaLancDados['valor'],2,',','.') }} · Vcto: {{ $baixaLancDados['vencimento'] }}</div>
    </div>

    <form wire:submit.prevent="registrarBaixa">
        {{-- Tipo recebimento --}}
        <div style="display:flex;gap:10px;margin-bottom:12px;">
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;font-weight:700;">
                <input type="radio" wire:model.live="bx_tipo_rec" value="total"> Total
            </label>
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;font-weight:700;">
                <input type="radio" wire:model.live="bx_tipo_rec" value="parcial"> Parcial
            </label>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label class="form-label">Valor Recebido (R$) *</label>
                <input type="number" step="0.01" wire:model.live="bx_valor"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('bx_valor') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;"
                    {{ $bx_tipo_rec === 'total' ? 'readonly' : '' }}>
                @error('bx_valor')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Data Recebimento *</label>
                <input type="date" wire:model="bx_data" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            @foreach(['pix'=>'PIX','boleto'=>'Boleto','transferencia'=>'Transferência','cartao'=>'Cartão','dinheiro'=>'Dinheiro'] as $fk=>$fv)
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;">
                <input type="radio" wire:model="bx_forma" value="{{ $fk }}">{{ $fv }}
            </label>
            @endforeach
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px;">
            <div class="form-group">
                <label class="form-label">Juros (R$)</label>
                <input type="number" step="0.01" wire:model.live="bx_juros" style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;">
            </div>
            <div class="form-group">
                <label class="form-label">Multa (R$)</label>
                <input type="number" step="0.01" wire:model.live="bx_multa" style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;">
            </div>
            <div class="form-group">
                <label class="form-label">Desconto (R$)</label>
                <input type="number" step="0.01" wire:model.live="bx_desconto" style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;">
            </div>
        </div>

        {{-- Cálculo em tempo real --}}
        @php
            $bxLiq = (float)$bx_valor + (float)$bx_juros + (float)$bx_multa - (float)$bx_desconto;
        @endphp
        <div style="background:#f0fdf4;border-radius:7px;padding:8px 12px;margin-bottom:10px;font-size:12px;font-weight:700;color:#166534;">
            Valor líquido: R$ {{ number_format($bxLiq, 2, ',', '.') }}
        </div>

        <div class="form-group">
            <label class="form-label">Observações</label>
            <input type="text" wire:model="bx_observacoes" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
        </div>

        <div style="display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" wire:click="fecharBaixa" class="btn btn-secondary" style="font-size:12px;">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="font-size:12px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="registrarBaixa">Confirmar Baixa</span>
                <span wire:loading wire:target="registrarBaixa">Salvando…</span>
            </button>
        </div>
    </form>
    </div>
</div>
</div>
@endif

</div>
