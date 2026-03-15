<div>
@if(session('sucesso'))
<div style="background:#dcfce7;border:1px solid #16a34a;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
    ✅ {{ session('sucesso') }}
</div>
@endif

{{-- KPIs --}}
<div class="stat-grid">
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
    <div class="filter-bar">
        <input wire:model.live.debounce.300ms="filtroBusca" type="text" placeholder="🔍 Processo, cliente...">
        <select wire:model.live="filtroStatus">
            <option value="">Todos os status</option>
            <option value="agendada">Agendada</option>
            <option value="realizada">Realizada</option>
            <option value="cancelada">Cancelada</option>
            <option value="redesignada">Redesignada</option>
        </select>
        <select wire:model.live="filtroTipo">
            <option value="">Todos os tipos</option>
            <option value="conciliacao">Conciliação</option>
            <option value="instrucao">Instrução</option>
            <option value="instrucao_julgamento">Instrução e Julgamento</option>
            <option value="julgamento">Julgamento</option>
            <option value="una">Una</option>
            <option value="outra">Outra</option>
        </select>
        <input wire:model.live="filtroDataIni" type="date" title="De">
        <input wire:model.live="filtroDataFim" type="date" title="Até">
        <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="flex-shrink:0;">+ Nova Audiência</button>
    </div>
</div>

{{-- Tabela --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Processo</th>
                    <th class="hide-sm">Tipo</th>
                    <th class="hide-sm">Juiz</th>
                    <th class="hide-sm">Sala/Local</th>
                    <th style="text-align:center;">Status</th>
                    <th class="hide-xs">Resultado</th>
                    <th style="text-align:center;">Ações</th>
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
                <tr style="{{ $ehHoje ? 'background:#fffbeb;' : '' }}{{ $ehPassado ? 'background:#fff5f5;' : '' }}">
                    <td>
                        <div style="font-weight:600;color:var(--primary);">
                            {{ $aud->data_hora->format('d/m/Y') }}
                            @if($ehHoje)<span class="badge" style="background:#dc2626;color:#fff;font-size:10px;margin-left:4px;">HOJE</span>@endif
                        </div>
                        <div style="font-size:12px;color:var(--muted);">{{ $aud->data_hora->format('H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:12px;">{{ $aud->processo?->numero }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $aud->processo?->cliente?->nome }}</div>
                    </td>
                    <td class="hide-sm" style="color:var(--muted);">{{ $tipos[$aud->tipo] ?? $aud->tipo }}</td>
                    <td class="hide-sm" style="font-size:12px;color:var(--muted);">{{ $aud->juiz?->nome ?? '—' }}</td>
                    <td class="hide-sm" style="font-size:12px;color:var(--muted);">
                        @if($aud->sala)Sala {{ $aud->sala }}@endif
                        @if($aud->local)<div>{{ $aud->local }}</div>@endif
                        @if(!$aud->sala && !$aud->local)—@endif
                    </td>
                    <td style="text-align:center;">
                        <span class="badge" style="background:{{ $aud->statusBg() }};color:{{ $aud->statusCor() }};">{{ ucfirst($aud->status) }}</span>
                    </td>
                    <td class="hide-xs" style="font-size:12px;color:var(--muted);">
                        {{ $aud->resultado ? ($resultados[$aud->resultado] ?? $aud->resultado) : '—' }}
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions" style="justify-content:center;">
                            @if($aud->status === 'agendada')
                            <button wire:click="abrirResultado({{ $aud->id }})" title="Resultado" class="btn btn-success btn-sm" style="font-size:11px;">✓</button>
                            @endif
                            <button wire:click="abrirModal({{ $aud->id }})" title="Editar" class="btn-icon">✏️</button>
                            <button wire:click="excluir({{ $aud->id }})" wire:confirm="Excluir esta audiência?" title="Excluir" class="btn-icon">🗑️</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:48px;text-align:center;color:var(--muted);">🗓️ Nenhuma audiência encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-bar" style="padding:12px 16px;">{{ $audiencias->links() }}</div>
</div>

{{-- ── Modal Principal ── --}}
@if($modalAberto)
<div class="modal-backdrop">
<div class="modal" style="max-width:700px;">
    <div class="modal-header">
        <span class="modal-title">{{ $audienciaId ? 'Editar' : 'Nova' }} Audiência</span>
        <button wire:click="fecharModal" class="modal-close">✕</button>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label class="lbl">Processo *</label>
            <select wire:model="processo_id">
                <option value="">Selecione...</option>
                @foreach($processos as $p)
                <option value="{{ $p->id }}">{{ $p->numero }} — {{ $p->cliente?->nome }}</option>
                @endforeach
            </select>
            @error('processo_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-field">
            <label class="lbl">Data e Hora *</label>
            <input wire:model="data_hora" type="datetime-local">
            @error('data_hora')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-field">
            <label class="lbl">Tipo *</label>
            <select wire:model="tipo">
                <option value="conciliacao">Conciliação</option>
                <option value="instrucao">Instrução</option>
                <option value="instrucao_julgamento">Instrução e Julgamento</option>
                <option value="julgamento">Julgamento</option>
                <option value="una">Una</option>
                <option value="outra">Outra</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Status</label>
            <select wire:model="status">
                <option value="agendada">Agendada</option>
                <option value="realizada">Realizada</option>
                <option value="cancelada">Cancelada</option>
                <option value="redesignada">Redesignada</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Sala</label>
            <input wire:model="sala" type="text" placeholder="Ex: Sala 3">
        </div>
        <div class="form-field">
            <label class="lbl">Local / Fórum</label>
            <input wire:model="local" type="text" placeholder="Ex: Fórum Central de São Paulo">
        </div>
        <div class="form-field">
            <label class="lbl">Juiz</label>
            <select wire:model="juiz_id">
                <option value="">Selecione...</option>
                @foreach($juizes as $j)
                <option value="{{ $j->id }}">{{ $j->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Advogado Responsável</label>
            <select wire:model="advogado_id">
                <option value="">Selecione...</option>
                @foreach($advogados as $a)
                <option value="{{ $a->id }}">{{ $a->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Preposto / Representante</label>
            <input wire:model="preposto" type="text" placeholder="Nome do preposto (se houver)">
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Pauta / Observações</label>
            <textarea wire:model="pauta" rows="2" placeholder="Assuntos a tratar, testemunhas, documentos necessários..."></textarea>
        </div>
    </div>

    @if($status === 'realizada')
    <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:4px;">
        <div style="font-size:12px;font-weight:700;color:var(--success);margin-bottom:12px;">✅ RESULTADO DA AUDIÊNCIA</div>
        <div class="form-grid">
            <div class="form-field">
                <label class="lbl">Resultado</label>
                <select wire:model="resultado">
                    <option value="">Selecione...</option>
                    <option value="acordo">Acordo</option>
                    <option value="condenacao">Condenação</option>
                    <option value="improcedente">Improcedente</option>
                    <option value="extincao">Extinção</option>
                    <option value="nao_realizada">Não Realizada</option>
                    <option value="outra">Outra</option>
                </select>
            </div>
            <div class="form-field">
                <label class="lbl">Data do Próximo Passo</label>
                <input wire:model="data_proximo" type="date">
            </div>
            <div class="form-field" style="grid-column:1/-1">
                <label class="lbl">Descrição do Resultado</label>
                <textarea wire:model="resultado_descricao" rows="2"></textarea>
            </div>
            <div class="form-field" style="grid-column:1/-1">
                <label class="lbl">Próximo Passo</label>
                <textarea wire:model="proximo_passo" rows="2" placeholder="Ex: Aguardar sentença, interpor recurso..."></textarea>
            </div>
        </div>
    </div>
    @endif

    <div class="modal-footer">
        <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
        <button wire:click="salvar" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="salvar">💾 Salvar</span>
            <span wire:loading wire:target="salvar">Salvando...</span>
        </button>
    </div>
</div>
</div>
@endif

{{-- ── Modal Resultado Rápido ── --}}
@if($modalResultado)
<div class="modal-backdrop">
<div class="modal" style="max-width:520px;">
    <div class="modal-header" style="background:#f0fdf4;">
        <span class="modal-title" style="color:#15803d;">✅ Registrar Resultado</span>
        <button wire:click="$set('modalResultado',false)" class="modal-close">✕</button>
    </div>
    <div class="form-grid" style="margin-top:4px;">
        <div class="form-field">
            <label class="lbl">Resultado *</label>
            <select wire:model="resultado">
                <option value="">Selecione...</option>
                <option value="acordo">Acordo</option>
                <option value="condenacao">Condenação</option>
                <option value="improcedente">Improcedente</option>
                <option value="extincao">Extinção</option>
                <option value="nao_realizada">Não Realizada</option>
                <option value="outra">Outra</option>
            </select>
        </div>
        <div class="form-field">
            <label class="lbl">Data do Próximo Passo</label>
            <input wire:model="data_proximo" type="date">
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Descrição do Resultado</label>
            <textarea wire:model="resultado_descricao" rows="3" placeholder="Descreva o que foi decidido/acordado..."></textarea>
        </div>
        <div class="form-field" style="grid-column:1/-1">
            <label class="lbl">Próximo Passo</label>
            <textarea wire:model="proximo_passo" rows="2" placeholder="Ex: Aguardar sentença, interpor recurso..."></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button wire:click="$set('modalResultado',false)" class="btn btn-secondary">Cancelar</button>
        <button wire:click="salvarResultado" class="btn btn-success">✅ Confirmar Realização</button>
    </div>
</div>
</div>
@endif

</div>
