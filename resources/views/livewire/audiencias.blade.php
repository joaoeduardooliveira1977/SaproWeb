<div>
@if(session('sucesso'))
<div style="background:#dcfce7;border:1px solid #16a34a;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
    ✅ {{ session('sucesso') }}
</div>
@endif

{{-- KPIs --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @php $kpiDefs = [
        ['🗓️', $kpis['agendadas'],  'Agendadas (futuras)',    '#2563a8'],
        ['⚡', $kpis['hoje'],       'Hoje',                   '#dc2626'],
        ['✅', $kpis['realizadas'], 'Realizadas (este mês)',  '#16a34a'],
        ['❌', $kpis['canceladas'], 'Canceladas (este mês)',  '#d97706'],
    ]; @endphp
    @foreach($kpiDefs as [$icon, $val, $label, $cor])
    <div class="card" style="border-left:4px solid {{ $cor }};text-align:center;">
        <div style="font-size:28px;">{{ $icon }}</div>
        <div style="font-size:26px;font-weight:700;color:{{ $cor }};">{{ $val }}</div>
        <div style="font-size:12px;color:var(--muted);">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input wire:model.live.debounce.300ms="filtroBusca" type="text"
            placeholder="🔍 Processo, cliente..."
            style="flex:1;min-width:180px;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
        <select wire:model.live="filtroStatus" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos os status</option>
            <option value="agendada">Agendada</option>
            <option value="realizada">Realizada</option>
            <option value="cancelada">Cancelada</option>
            <option value="redesignada">Redesignada</option>
        </select>
        <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="">Todos os tipos</option>
            <option value="conciliacao">Conciliação</option>
            <option value="instrucao">Instrução</option>
            <option value="instrucao_julgamento">Instrução e Julgamento</option>
            <option value="julgamento">Julgamento</option>
            <option value="una">Una</option>
            <option value="outra">Outra</option>
        </select>
        <input wire:model.live="filtroDataIni" type="date" title="Data início"
            style="padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
        <input wire:model.live="filtroDataFim" type="date" title="Data fim"
            style="padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
        <button wire:click="abrirModal()" class="btn btn-primary">+ Nova Audiência</button>
    </div>
</div>

{{-- Tabela --}}
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:var(--primary);color:#fff;">
                <th style="padding:10px 12px;text-align:left;">Data/Hora</th>
                <th style="padding:10px 12px;text-align:left;">Processo</th>
                <th style="padding:10px 12px;text-align:left;">Tipo</th>
                <th style="padding:10px 12px;text-align:left;">Juiz</th>
                <th style="padding:10px 12px;text-align:left;">Sala/Local</th>
                <th style="padding:10px 12px;text-align:center;">Status</th>
                <th style="padding:10px 12px;text-align:left;">Resultado</th>
                <th style="padding:10px 12px;text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audiencias as $aud)
            @php
                $tipos = \App\Models\Audiencia::tiposLabel();
                $resultados = \App\Models\Audiencia::resultadosLabel();
                $ehHoje = $aud->data_hora->isToday();
                $ehPassado = $aud->data_hora->isPast() && $aud->status === 'agendada';
            @endphp
            <tr style="border-bottom:1px solid var(--border);{{ $ehHoje ? 'background:#fffbeb;' : '' }}{{ $ehPassado ? 'background:#fff5f5;' : '' }}"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ $ehHoje ? '#fffbeb' : ($ehPassado ? '#fff5f5' : '') }}'">
                <td style="padding:10px 12px;">
                    <div style="font-weight:600;color:var(--primary);">
                        {{ $aud->data_hora->format('d/m/Y') }}
                        @if($ehHoje)<span style="background:#dc2626;color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">HOJE</span>@endif
                    </div>
                    <div style="font-size:12px;color:var(--muted);">{{ $aud->data_hora->format('H:i') }}</div>
                </td>
                <td style="padding:10px 12px;">
                    <div style="font-weight:600;font-size:12px;">{{ $aud->processo?->numero }}</div>
                    <div style="font-size:11px;color:var(--muted);">{{ $aud->processo?->cliente?->nome }}</div>
                </td>
                <td style="padding:10px 12px;color:var(--muted);">{{ $tipos[$aud->tipo] ?? $aud->tipo }}</td>
                <td style="padding:10px 12px;color:var(--muted);font-size:12px;">{{ $aud->juiz?->nome ?? '—' }}</td>
                <td style="padding:10px 12px;color:var(--muted);font-size:12px;">
                    @if($aud->sala) Sala {{ $aud->sala }} @endif
                    @if($aud->local)<div>{{ $aud->local }}</div>@endif
                    @if(!$aud->sala && !$aud->local) — @endif
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <span style="background:{{ $aud->statusBg() }};color:{{ $aud->statusCor() }};padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;text-transform:capitalize;">
                        {{ ucfirst($aud->status) }}
                    </span>
                </td>
                <td style="padding:10px 12px;font-size:12px;color:var(--muted);">
                    @if($aud->resultado)
                        {{ $resultados[$aud->resultado] ?? $aud->resultado }}
                    @else
                        <span style="font-style:italic;">—</span>
                    @endif
                </td>
                <td style="padding:10px 12px;text-align:center;">
                    <div style="display:flex;gap:4px;justify-content:center;">
                        @if($aud->status === 'agendada')
                        <button wire:click="abrirResultado({{ $aud->id }})" title="Registrar resultado"
                            style="background:#16a34a;color:#fff;border:none;border-radius:5px;padding:4px 8px;cursor:pointer;font-size:11px;font-weight:600;">
                            ✓ Resultado
                        </button>
                        @endif
                        <button wire:click="abrirModal({{ $aud->id }})" title="Editar"
                            style="background:none;border:none;cursor:pointer;font-size:16px;">✏️</button>
                        <button wire:click="excluir({{ $aud->id }})" wire:confirm="Excluir esta audiência?" title="Excluir"
                            style="background:none;border:none;cursor:pointer;font-size:16px;">🗑️</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:48px;text-align:center;color:var(--muted);">
                    🗓️ Nenhuma audiência encontrada. Clique em "+ Nova Audiência" para cadastrar.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:12px;">{{ $audiencias->links() }}</div>
</div>

{{-- ── Modal Principal ── --}}
@if($modalAberto)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
<div style="background:#fff;border-radius:12px;width:100%;max-width:700px;max-height:92vh;overflow-y:auto;">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;color:var(--primary);">{{ $audienciaId ? 'Editar' : 'Nova' }} Audiência</h3>
        <button wire:click="fecharModal" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
    </div>
    <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

        {{-- Processo + Data --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">PROCESSO *</label>
                <select wire:model="processo_id" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                    <option value="">Selecione...</option>
                    @foreach($processos as $p)
                    <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome }}</option>
                    @endforeach
                </select>
                @error('processo_id')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">DATA E HORA *</label>
                <input wire:model="data_hora" type="datetime-local"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                @error('data_hora')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Tipo + Status --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">TIPO *</label>
                <select wire:model="tipo" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                    <option value="conciliacao">Conciliação</option>
                    <option value="instrucao">Instrução</option>
                    <option value="instrucao_julgamento">Instrução e Julgamento</option>
                    <option value="julgamento">Julgamento</option>
                    <option value="una">Una</option>
                    <option value="outra">Outra</option>
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">STATUS</label>
                <select wire:model="status" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                    <option value="agendada">Agendada</option>
                    <option value="realizada">Realizada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="redesignada">Redesignada</option>
                </select>
            </div>
        </div>

        {{-- Sala + Local --}}
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:14px;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">SALA</label>
                <input wire:model="sala" type="text" placeholder="Ex: Sala 3"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">LOCAL / FÓRUM</label>
                <input wire:model="local" type="text" placeholder="Ex: Fórum Central de São Paulo"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
            </div>
        </div>

        {{-- Juiz + Advogado --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">JUIZ</label>
                <select wire:model="juiz_id" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                    <option value="">Selecione...</option>
                    @foreach($juizes as $j)
                    <option value="{{ $j->id }}">{{ $j->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">ADVOGADO RESPONSÁVEL</label>
                <select wire:model="advogado_id" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                    <option value="">Selecione...</option>
                    @foreach($advogados as $a)
                    <option value="{{ $a->id }}">{{ $a->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Preposto + Pauta --}}
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">PREPOSTO / REPRESENTANTE DA PARTE</label>
            <input wire:model="preposto" type="text" placeholder="Nome do preposto (se houver)"
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">PAUTA / OBSERVAÇÕES</label>
            <textarea wire:model="pauta" rows="2"
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;resize:vertical;"
                placeholder="Assuntos a tratar, testemunhas, documentos necessários..."></textarea>
        </div>

        {{-- Resultado (se já realizada) --}}
        @if($status === 'realizada')
        <div style="border-top:1px solid var(--border);padding-top:16px;">
            <div style="font-size:12px;font-weight:700;color:#16a34a;margin-bottom:12px;">✅ RESULTADO DA AUDIÊNCIA</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:12px;">
                <div>
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">RESULTADO</label>
                    <select wire:model="resultado" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                        <option value="">Selecione...</option>
                        <option value="acordo">Acordo</option>
                        <option value="condenacao">Condenação</option>
                        <option value="improcedente">Improcedente</option>
                        <option value="extincao">Extinção</option>
                        <option value="nao_realizada">Não Realizada</option>
                        <option value="outra">Outra</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">DATA DO PRÓXIMO PASSO</label>
                    <input wire:model="data_proximo" type="date"
                        style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">DESCRIÇÃO DO RESULTADO</label>
                <textarea wire:model="resultado_descricao" rows="2"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;resize:vertical;"></textarea>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">PRÓXIMO PASSO</label>
                <textarea wire:model="proximo_passo" rows="2"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;resize:vertical;"
                    placeholder="Ex: Aguardar sentença, interpor recurso, etc."></textarea>
            </div>
        </div>
        @endif

        <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:4px;">
            <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
            <button wire:click="salvar" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvar">💾 Salvar</span>
                <span wire:loading wire:target="salvar">Salvando...</span>
            </button>
        </div>
    </div>
</div>
</div>
@endif

{{-- ── Modal Resultado Rápido ── --}}
@if($modalResultado)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
<div style="background:#fff;border-radius:12px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);background:#f0fdf4;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;color:#15803d;">✅ Registrar Resultado da Audiência</h3>
        <button wire:click="$set('modalResultado',false)" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
    </div>
    <div style="padding:24px;display:flex;flex-direction:column;gap:14px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">RESULTADO *</label>
                <select wire:model="resultado" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
                    <option value="">Selecione...</option>
                    <option value="acordo">Acordo</option>
                    <option value="condenacao">Condenação</option>
                    <option value="improcedente">Improcedente</option>
                    <option value="extincao">Extinção</option>
                    <option value="nao_realizada">Não Realizada</option>
                    <option value="outra">Outra</option>
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">DATA DO PRÓXIMO PASSO</label>
                <input wire:model="data_proximo" type="date"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;">
            </div>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">DESCRIÇÃO DO RESULTADO</label>
            <textarea wire:model="resultado_descricao" rows="3"
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;resize:vertical;"
                placeholder="Descreva o que foi decidido/acordado..."></textarea>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;">PRÓXIMO PASSO</label>
            <textarea wire:model="proximo_passo" rows="2"
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;margin-top:4px;font-size:13px;resize:vertical;"
                placeholder="Ex: Aguardar sentença, interpor recurso, redesignar..."></textarea>
        </div>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button wire:click="$set('modalResultado',false)" class="btn btn-secondary">Cancelar</button>
            <button wire:click="salvarResultado" class="btn btn-success">✅ Confirmar Realização</button>
        </div>
    </div>
</div>
</div>
@endif

</div>
