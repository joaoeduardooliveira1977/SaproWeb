<div>

{{-- ── KPIs rápidos da importação atual ─────────────────────── --}}
@if($importacaoAtual)
<div class="stat-grid" style="margin-bottom:20px;">
    <div class="stat-card" style="border-left-color:var(--primary);">
        <div class="stat-icon"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
        <div class="stat-val">{{ $importacaoAtual->total_lancamentos }}</div>
        <div class="stat-label">Total de lançamentos</div>
    </div>
    <div class="stat-card" style="border-left-color:var(--success);">
        <div class="stat-icon"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
        <div class="stat-val" style="color:var(--success);">{{ $importacaoAtual->conciliados }}</div>
        <div class="stat-label">Conciliados</div>
    </div>
    <div class="stat-card" style="border-left-color:var(--warning);">
        <div class="stat-icon"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
        <div class="stat-val" style="color:var(--warning);">{{ $importacaoAtual->pendentes }}</div>
        <div class="stat-label">Pendentes</div>
    </div>
    <div class="stat-card" style="border-left-color:var(--muted);">
        <div class="stat-icon"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
        <div class="stat-val" style="font-size:16px;padding-top:4px;">{{ $importacaoAtual->banco ?: '—' }}</div>
        <div class="stat-label">{{ $importacaoAtual->conta ? 'Conta ' . $importacaoAtual->conta : 'Arquivo: ' . $importacaoAtual->arquivo }}</div>
    </div>
</div>
@endif

{{-- ── Abas ──────────────────────────────────────────────────── --}}
<div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0;">
    @foreach([['importar','Importar OFX'],['lancamentos','Lançamentos'],['historico','Histórico']] as [$val,$label])
    <button wire:click="$set('aba','{{ $val }}')"
        style="padding:8px 18px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:600;
               color:{{ $aba===$val ? 'var(--primary)' : 'var(--muted)' }};
               border-bottom:{{ $aba===$val ? '2px solid var(--primary)' : '2px solid transparent' }};
               margin-bottom:-2px;">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA: IMPORTAR                                               --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'importar')
<div class="card">
    <div class="card-header">
        <span class="card-title">Importar arquivo OFX / QFX</span>
    </div>

    {{-- Upload --}}
    <div style="border:2px dashed var(--border);border-radius:10px;padding:32px;text-align:center;margin-bottom:20px;background:var(--bg);">
        <svg aria-hidden="true" width="40" height="40" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:12px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <p style="font-size:14px;color:var(--muted);margin-bottom:12px;">Selecione o arquivo exportado pelo seu banco</p>
        <input type="file" wire:model="arquivo" accept=".ofx,.qfx,.OFX,.QFX"
               style="display:block;margin:0 auto;font-size:13px;">
        <p style="font-size:11px;color:var(--muted);margin-top:8px;">Formatos suportados: OFX, QFX (Itaú, Bradesco, BB, Santander, Sicoob...)</p>
    </div>

    @if($previewErro)
        <div class="alert alert-error">{{ $previewErro }}</div>
    @endif

    @if(!empty($preview['lancamentos']))
    {{-- Resumo do arquivo --}}
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;font-size:13px;color:var(--muted);">
        @if($preview['banco'])   <span><strong>Banco:</strong> {{ $preview['banco'] }}</span> @endif
        @if($preview['agencia']) <span><strong>Agência:</strong> {{ $preview['agencia'] }}</span> @endif
        @if($preview['conta'])   <span><strong>Conta:</strong> {{ $preview['conta'] }}</span> @endif
        @if($preview['data_ini'])<span><strong>Período:</strong> {{ \Carbon\Carbon::parse($preview['data_ini'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($preview['data_fim'])->format('d/m/Y') }}</span> @endif
        <span><strong>Lançamentos:</strong> {{ count($preview['lancamentos']) }}</span>
    </div>

    {{-- Preview da tabela --}}
    <div class="table-wrap" style="max-height:380px;overflow-y:auto;margin-bottom:16px;">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th style="text-align:right;">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preview['lancamentos'] as $l)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($l['data'])->format('d/m/Y') }}</td>
                    <td style="font-size:12px;">{{ $l['descricao'] ?: '—' }}</td>
                    <td><span style="font-size:11px;padding:2px 6px;border-radius:4px;
                        background:{{ $l['valor']>0 ? '#dcfce7' : '#fee2e2' }};
                        color:{{ $l['valor']>0 ? '#16a34a' : '#dc2626' }};">
                        {{ $l['valor'] > 0 ? 'Crédito' : 'Débito' }}
                    </span></td>
                    <td style="text-align:right;font-weight:600;color:{{ $l['valor']>0 ? 'var(--success)' : 'var(--danger)' }};">
                        {{ $l['valor'] > 0 ? '+' : '' }}{{ number_format($l['valor'], 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;">
        <button wire:click="$set('arquivo',null)" class="btn btn-secondary">Cancelar</button>
        <button wire:click="confirmarImportacao" class="btn btn-primary">
            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Confirmar importação ({{ count($preview['lancamentos']) }} lançamentos)
        </button>
    </div>
    @endif
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA: LANÇAMENTOS                                            --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'lancamentos')
<div class="card">
    <div class="card-header">
        <span class="card-title">Lançamentos — Conciliação</span>
    </div>

    {{-- Filtros --}}
    <div class="filter-bar">
        <select wire:model.live="importacaoId" style="min-width:220px;max-width:300px;">
            <option value="">— Selecione a importação —</option>
            @foreach($importacoes as $imp)
            <option value="{{ $imp->id }}">
                {{ $imp->created_at->format('d/m/Y') }}
                {{ $imp->banco ? '— ' . $imp->banco : '' }}
                {{ $imp->conta ? '(' . $imp->conta . ')' : '' }}
                — {{ $imp->total_lancamentos }} lançamentos
            </option>
            @endforeach
        </select>
        <select wire:model.live="filtroStatus" style="max-width:160px;">
            <option value="">Todos</option>
            <option value="pendente">Pendentes</option>
            <option value="conciliado">Conciliados</option>
        </select>
        <select wire:model.live="filtroTipo" style="max-width:140px;">
            <option value="">Débito/Crédito</option>
            <option value="credito">Créditos</option>
            <option value="debito">Débitos</option>
        </select>
    </div>

    @if(!$importacaoId)
        <div class="empty-state">
            <div class="empty-state-icon"><svg aria-hidden="true" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
            <div class="empty-state-title">Nenhuma importação selecionada</div>
            <div class="empty-state-sub">Selecione uma importação acima para visualizar os lançamentos.</div>
        </div>
    @elseif($lancamentos->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"><svg aria-hidden="true" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></div>
            <div class="empty-state-title">Nenhum lançamento encontrado</div>
            <div class="empty-state-sub">Tente ajustar os filtros aplicados.</div>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição OFX</th>
                    <th style="text-align:right;">Valor</th>
                    <th>Status</th>
                    <th>Vinculado a</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lancamentos as $lance)
                @php
                    $ref = $lance->conciliado ? $lance->referenciaModel() : null;
                    $processo = $ref?->processo;
                @endphp
                <tr style="{{ $lance->conciliado ? 'background:#f0fdf4;' : '' }}">
                    <td style="white-space:nowrap;font-size:12px;">{{ $lance->data->format('d/m/Y') }}</td>
                    <td style="font-size:12px;max-width:220px;">{{ $lance->descricao ?: '—' }}</td>
                    <td style="text-align:right;font-weight:700;white-space:nowrap;
                        color:{{ $lance->valor > 0 ? 'var(--success)' : 'var(--danger)' }};">
                        {{ $lance->valor > 0 ? '+' : '' }}{{ number_format($lance->valor, 2, ',', '.') }}
                    </td>
                    <td>
                        @if($lance->conciliado)
                            <span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">Conciliado</span>
                        @else
                            <span style="background:#fef9c3;color:#92400e;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">Pendente</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">
                        @if($lance->conciliado && $ref)
                            <div style="font-weight:600;color:var(--primary);">{{ $processo?->numero ?? '—' }}</div>
                            <div style="color:var(--muted);font-size:11px;">
                                {{ $lance->referencia_tipo === 'recebimentos' ? 'Recebimento' : 'Pagamento' }}
                                · {{ $ref->descricao ?? '—' }}
                            </div>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($lance->conciliado)
                            <button wire:click="desvincular({{ $lance->id }})"
                                wire:confirm="Remover o vínculo deste lançamento?"
                                class="btn-action btn-action-red" title="Desvincular">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        @else
                            <button wire:click="abrirConciliar({{ $lance->id }})"
                                class="btn-action btn-action-blue" title="Conciliar manualmente">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA: HISTÓRICO                                              --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'historico')
<div class="card">
    <div class="card-header">
        <span class="card-title">Histórico de importações</span>
    </div>

    @if($importacoes->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"><svg aria-hidden="true" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></div>
            <div class="empty-state-title">Nenhuma importação realizada</div>
            <div class="empty-state-sub">Importe um arquivo OFX ou CSV para iniciar a conciliação.</div>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data import.</th>
                    <th>Arquivo</th>
                    <th>Banco / Conta</th>
                    <th>Período</th>
                    <th style="text-align:center;">Total</th>
                    <th style="text-align:center;">Conciliados</th>
                    <th style="text-align:center;">Pendentes</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($importacoes as $imp)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                    <td style="font-size:12px;">{{ $imp->arquivo }}</td>
                    <td style="font-size:12px;">
                        {{ $imp->banco ?: '—' }}
                        @if($imp->conta) <span style="color:var(--muted);">/ {{ $imp->conta }}</span> @endif
                    </td>
                    <td style="font-size:12px;white-space:nowrap;">
                        @if($imp->data_ini)
                            {{ $imp->data_ini->format('d/m/Y') }} – {{ $imp->data_fim?->format('d/m/Y') }}
                        @else —
                        @endif
                    </td>
                    <td style="text-align:center;font-weight:600;">{{ $imp->total_lancamentos }}</td>
                    <td style="text-align:center;color:var(--success);font-weight:600;">{{ $imp->conciliados }}</td>
                    <td style="text-align:center;color:{{ $imp->pendentes > 0 ? 'var(--warning)' : 'var(--muted)' }};font-weight:600;">
                        {{ $imp->pendentes }}
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-actions">
                            <button wire:click="$set('importacaoId',{{ $imp->id }});$set('aba','lancamentos')"
                                class="btn-action btn-action-blue" title="Ver lançamentos">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button wire:click="excluirImportacao({{ $imp->id }})"
                                wire:confirm="Excluir esta importação e todos os seus lançamentos?"
                                class="btn-action btn-action-red" title="Excluir">
                                <svg aria-hidden="true" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Conciliação manual                                   --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($modalConciliar)
@php $lance = \App\Models\OfxLancamento::find($lancamentoSel); @endphp
<div class="modal-backdrop" wire:click.self="fecharConciliar">
    <div class="modal" style="max-width:700px;">
        <div class="modal-header">
            <span class="modal-title">Conciliar lançamento</span>
            <button class="modal-close" wire:click="fecharConciliar">×</button>
        </div>

        @if($lance)
        {{-- Lançamento OFX --}}
        <div style="background:var(--bg);border-radius:8px;padding:14px 16px;margin-bottom:18px;font-size:13px;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                <div>
                    <div style="font-weight:700;font-size:14px;">{{ $lance->descricao ?: '(sem descrição)' }}</div>
                    <div style="color:var(--muted);font-size:12px;">{{ $lance->data->format('d/m/Y') }} · {{ $lance->tipo }}</div>
                </div>
                <div style="font-size:22px;font-weight:700;color:{{ $lance->valor>0 ? 'var(--success)' : 'var(--danger)' }};">
                    {{ $lance->valor > 0 ? '+' : '' }}R$ {{ number_format(abs($lance->valor), 2, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Tipo de registro --}}
        <div style="display:flex;gap:8px;margin-bottom:14px;">
            <button wire:click="$set('tipoConciliar','{{ $lance->isCredito() ? 'recebimentos' : 'pagamentos' }}')"
                class="btn {{ $tipoConciliar === ($lance->isCredito() ? 'recebimentos' : 'pagamentos') ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                {{ $lance->isCredito() ? 'Recebimentos' : 'Pagamentos' }}
            </button>
            <button wire:click="$set('tipoConciliar','{{ $lance->isCredito() ? 'pagamentos' : 'recebimentos' }}')"
                class="btn {{ $tipoConciliar !== ($lance->isCredito() ? 'recebimentos' : 'pagamentos') ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                {{ $lance->isCredito() ? 'Pagamentos' : 'Recebimentos' }}
            </button>
        </div>

        {{-- Busca --}}
        <div style="margin-bottom:14px;">
            <input type="text" wire:model.live.debounce.400ms="buscaConciliar"
                placeholder="Buscar por descrição ou nº processo..."
                style="width:100%;">
        </div>

        {{-- Sugestões --}}
        @if($sugestoes->isEmpty())
            <div style="text-align:center;padding:24px;color:var(--muted);font-size:13px;">
                {{ $buscaConciliar ? 'Nenhum resultado para a busca.' : 'Nenhuma sugestão automática. Use a busca acima.' }}
            </div>
        @else
        <div class="table-wrap" style="max-height:300px;overflow-y:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Processo</th>
                        <th>Descrição</th>
                        <th style="text-align:right;">Valor</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sugestoes as $sug)
                    <tr>
                        <td style="font-size:12px;white-space:nowrap;">{{ $sug['data'] }}</td>
                        <td style="font-size:12px;font-weight:600;">{{ $sug['processo'] }}</td>
                        <td style="font-size:12px;">
                            {{ $sug['descricao'] }}
                            @if($sug['cliente'] !== '—')
                                <div style="color:var(--muted);font-size:11px;">{{ $sug['cliente'] }}</div>
                            @endif
                        </td>
                        <td style="text-align:right;font-weight:700;color:var(--primary);">
                            R$ {{ number_format($sug['valor'], 2, ',', '.') }}
                        </td>
                        <td>
                            <button wire:click="vincular('{{ $sug['tipo'] }}',{{ $sug['id'] }})"
                                class="btn btn-success btn-sm">Vincular</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @endif

        <div class="modal-footer">
            <button class="btn btn-secondary" wire:click="fecharConciliar">Fechar</button>
        </div>
    </div>
</div>
@endif

</div>
