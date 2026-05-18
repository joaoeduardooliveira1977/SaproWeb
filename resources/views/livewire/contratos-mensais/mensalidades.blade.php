<div>
{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);margin-bottom:18px;">
    <a href="{{ route('contratos-mensais.index') }}" style="color:var(--muted);text-decoration:none;">Contratos Mensais</a>
    <span style="color:var(--border);">›</span>
    <span style="color:var(--text);font-weight:600;">{{ $this->contrato->descricao }}</span>
</div>

{{-- Info do Contrato --}}
<div class="card" style="margin-bottom:18px;padding:16px 20px;">
    <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:center;">
        <div>
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;">Cliente</div>
            <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $this->contrato->cliente?->nome ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;">Valor</div>
            <div style="font-size:14px;font-weight:600;color:var(--text);">R$ {{ number_format($this->contrato->valor, 2, ',', '.') }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;">Periodicidade</div>
            <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $this->contrato->periodicidade_label }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;">Vcto. Dia</div>
            <div style="font-size:14px;font-weight:600;color:var(--text);">Dia {{ $this->contrato->dia_vencimento }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;">Cobrança</div>
            <div style="font-size:14px;font-weight:600;color:var(--text);">{{ strtoupper($this->contrato->forma_cobranca ?? 'PIX') }}</div>
        </div>
        @php
            $badge = match($this->contrato->status) {
                'ativo'    => ['#dcfce7','#166534','#bbf7d0'],
                'suspenso' => ['#fef9c3','#92400e','#fde68a'],
                default    => ['#f1f5f9','#475569','#cbd5e1'],
            };
        @endphp
        <div style="margin-left:auto;">
            <span style="background:{{ $badge[0] }};color:{{ $badge[1] }};border:1px solid {{ $badge[2] }};border-radius:6px;padding:4px 12px;font-size:13px;font-weight:600;">
                {{ $this->contrato->status_label }}
            </span>
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="stat-grid" style="margin-bottom:18px;">
    <div class="stat-card">
        <div class="stat-label">Em Aberto</div>
        <div class="stat-val" style="color:var(--danger);">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Recebido</div>
        <div class="stat-val" style="color:#16a34a;">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Vencidas</div>
        <div class="stat-val" style="{{ $vencidas > 0 ? 'color:var(--danger);' : '' }}">{{ $vencidas }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Próximo Vcto.</div>
        <div class="stat-val" style="font-size:18px;">{{ $proximoVcto ? $proximoVcto->format('d/m/Y') : '—' }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h2 style="margin:0;font-size:17px;font-weight:700;color:var(--primary);">Mensalidades</h2>
    </div>

    {{-- Filtros --}}
    <div class="filter-bar">
        <select wire:model.live="filtroAno" style="max-width:120px;">
            @foreach($anos as $ano)
            <option value="{{ $ano }}">{{ $ano }}</option>
            @endforeach
            <option value="">Todos</option>
        </select>
        <select wire:model.live="filtroStatus" style="max-width:160px;">
            <option value="">Todos os status</option>
            <option value="pendente">Pendente</option>
            <option value="vencido">Vencido</option>
            <option value="parcial">Parcial</option>
            <option value="recebido">Recebido</option>
            <option value="cancelado">Cancelado</option>
        </select>
    </div>

    {{-- Tabela --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Competência</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Recebimento</th>
                    <th>Valor Recebido</th>
                    <th>Forma</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mensalidades as $m)
                <tr>
                    <td style="font-weight:600;">{{ $m->competencia_formatada }}</td>
                    <td>{{ $m->vencimento->format('d/m/Y') }}</td>
                    <td>R$ {{ number_format($m->valor, 2, ',', '.') }}</td>
                    <td>
                        @php
                            $bg = match($m->status) {
                                'recebido' => ['#dcfce7','#166534','#bbf7d0'],
                                'vencido'  => ['#fee2e2','#991b1b','#fecaca'],
                                'parcial'  => ['#fef9c3','#92400e','#fde68a'],
                                'pendente' => ['#eff6ff','#1e40af','#bfdbfe'],
                                default    => ['#f1f5f9','#475569','#cbd5e1'],
                            };
                        @endphp
                        <span style="background:{{ $bg[0] }};color:{{ $bg[1] }};border:1px solid {{ $bg[2] }};border-radius:6px;padding:2px 8px;font-size:12px;font-weight:600;">
                            {{ ucfirst($m->status) }}
                        </span>
                    </td>
                    <td>{{ $m->data_recebimento ? $m->data_recebimento->format('d/m/Y') : '—' }}</td>
                    <td>{{ $m->valor_recebido > 0 ? 'R$ ' . number_format($m->valor_recebido, 2, ',', '.') : '—' }}</td>
                    <td>{{ $m->forma_pagamento ? ucfirst($m->forma_pagamento) : '—' }}</td>
                    <td>
                        <div class="btn-actions" style="justify-content:center;">
                            @if(in_array($m->status, ['pendente', 'vencido', 'parcial']))
                            <button wire:click="abrirRecebimento({{ $m->id }})" class="btn-action btn-action-green" title="Registrar Recebimento">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            <button wire:click="confirmarCancelar({{ $m->id }})" class="btn-action btn-action-red" title="Cancelar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-title">Nenhuma mensalidade encontrada</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mensalidades->hasPages())
    <div class="pagination-bar">
        <span>{{ $mensalidades->total() }} registro(s)</span>
        <div class="page-btns">
            <button class="page-btn" wire:click="previousPage" @disabled(!$mensalidades->onFirstPage())>‹ Ant.</button>
            <span class="page-current">{{ $mensalidades->currentPage() }} / {{ $mensalidades->lastPage() }}</span>
            <button class="page-btn" wire:click="nextPage" @disabled(!$mensalidades->hasMorePages())>Próx. ›</button>
        </div>
    </div>
    @endif
</div>

{{-- Modal Registrar Recebimento --}}
@if($modalRecebimento)
<div class="modal-backdrop" style="display:flex;" wire:click.self="fecharRecebimento">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <h3 style="margin:0;font-size:16px;font-weight:700;">Registrar Recebimento</h3>
            <button wire:click="fecharRecebimento" class="btn-action" style="width:32px;height:32px;">✕</button>
        </div>
        <div class="modal-body" style="padding:20px 0 0;">
            <form wire:submit.prevent="registrarRecebimento">
                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Data Recebimento *</label>
                        <input type="date" wire:model="data_recebimento"
                            style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('data_recebimento') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                        @error('data_recebimento')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Valor Recebido (R$) *</label>
                        <input type="number" step="0.01" wire:model="valor_recebido"
                            style="width:100%;padding:9px 12px;border:1.5px solid {{ $errors->has('valor_recebido') ? 'var(--danger)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                        @error('valor_recebido')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Forma de Pagamento *</label>
                    <select wire:model="forma_pagamento"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                        <option value="pix">PIX</option>
                        <option value="boleto">Boleto</option>
                        <option value="transferencia">Transferência</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <input type="text" wire:model="obs_recebimento"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <button type="button" wire:click="fecharRecebimento" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="registrarRecebimento">Confirmar</span>
                        <span wire:loading wire:target="registrarRecebimento">Salvando…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Confirmar Cancelamento --}}
@if($modalCancelar)
<div class="modal-backdrop" style="display:flex;">
    <div class="modal" style="max-width:380px;text-align:center;padding:32px 28px;">
        <p style="font-size:14px;color:var(--text);margin-bottom:24px;">Cancelar esta mensalidade?</p>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button wire:click="fecharCancelar" class="btn btn-secondary">Voltar</button>
            <button wire:click="cancelarMensalidade" class="btn btn-danger">Cancelar Mensalidade</button>
        </div>
    </div>
</div>
@endif
</div>
