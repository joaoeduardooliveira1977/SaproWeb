<div>
    {{-- ══ Cards de totais ══ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px">

        {{-- Pagamentos --}}
        <div class="stat-card" style="border-left-color:#dc2626">
            <div class="stat-icon">💸</div>
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Pagamentos</div>
            <div style="margin-top:8px;display:flex;flex-direction:column;gap:4px;font-size:13px">
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#64748b">Total:</span>
                    <strong>R$ {{ number_format($totais['pagamentos']['total'] ?? 0, 2, ',', '.') }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#16a34a">Pago:</span>
                    <strong style="color:#16a34a">R$ {{ number_format($totais['pagamentos']['pago'] ?? 0, 2, ',', '.') }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#dc2626">Pendente:</span>
                    <strong style="color:#dc2626">R$ {{ number_format($totais['pagamentos']['pendente'] ?? 0, 2, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        {{-- Recebimentos --}}
        <div class="stat-card" style="border-left-color:#16a34a">
            <div class="stat-icon">💰</div>
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Recebimentos</div>
            <div style="margin-top:8px;display:flex;flex-direction:column;gap:4px;font-size:13px">
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#64748b">Previsto:</span>
                    <strong>R$ {{ number_format($totais['recebimentos']['total'] ?? 0, 2, ',', '.') }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#16a34a">Recebido:</span>
                    <strong style="color:#16a34a">R$ {{ number_format($totais['recebimentos']['recebido'] ?? 0, 2, ',', '.') }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#d97706">Pendente:</span>
                    <strong style="color:#d97706">R$ {{ number_format($totais['recebimentos']['pendente'] ?? 0, 2, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        {{-- Apontamentos --}}
        <div class="stat-card" style="border-left-color:#7c3aed">
            <div class="stat-icon">⏱️</div>
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Apontamentos</div>
            <div style="margin-top:8px;display:flex;flex-direction:column;gap:4px;font-size:13px">
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#64748b">Total horas:</span>
                    <strong>{{ number_format($totais['apontamentos']['total_horas'] ?? 0, 1, ',', '.') }}h</strong>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#7c3aed">Valor total:</span>
                    <strong style="color:#7c3aed">R$ {{ number_format($totais['apontamentos']['total_valor'] ?? 0, 2, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Abas ══ --}}
    <div class="card">
        <div style="display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin-bottom:16px">
            @foreach(['pagamentos'=>'💸 Pagamentos','recebimentos'=>'💰 Recebimentos','apontamentos'=>'⏱️ Apontamentos'] as $key=>$label)
            <button wire:click="$set('aba','{{ $key }}')"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;
                       border-bottom:2px solid {{ $aba===$key ? '#1a3a5c' : 'transparent' }};
                       color:{{ $aba===$key ? '#1a3a5c' : '#64748b' }};margin-bottom:-2px">
                {{ $label }}
            </button>
            @endforeach
            <div style="flex:1"></div>
            <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="margin:6px 0">
                ＋ Novo
            </button>
        </div>

        {{-- ══ Tabela Pagamentos ══ --}}
        @if($aba === 'pagamentos')
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Data</th><th>Descrição</th><th>Fornecedor</th><th>Nº Doc</th><th>Vencimento</th><th>Valor</th><th>Pago</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($pagamentos as $p)
                    <tr>
                        <td>{{ $p->data->format('d/m/Y') }}</td>
                        <td>{{ $p->descricao }}</td>
                        <td>{{ $p->fornecedor?->nome ?? '—' }}</td>
                        <td>{{ $p->numero_doc ?? '—' }}</td>
                        <td>
                            @if($p->data_vencimento)
                                @php $venc = $p->data_vencimento; $atrasado = !$p->pago && $venc->isPast(); @endphp
                                <span style="color:{{ $atrasado ? '#dc2626' : 'inherit' }}">
                                    {{ $venc->format('d/m/Y') }}
                                    @if($atrasado) ⚠️ @endif
                                </span>
                            @else —
                            @endif
                        </td>
                        <td><strong>R$ {{ number_format($p->valor, 2, ',', '.') }}</strong></td>
                        <td>
                            @if($p->pago)
                                <span class="badge" style="background:#dcfce7;color:#166534">✅ Pago</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b">⏳ Pendente</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="abrirModal({{ $p->id }})" class="btn-icon">✏️</button>
                            <button wire:click="excluir({{ $p->id }})" class="btn-icon" onclick="return confirm('Remover?')">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;padding:20px">Nenhum pagamento cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $pagamentos->links() }}</div>
        @endif

        {{-- ══ Tabela Recebimentos ══ --}}
        @if($aba === 'recebimentos')
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Data</th><th>Descrição</th><th>Origem</th><th>Nº Doc</th><th>Valor Previsto</th><th>Valor Recebido</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($recebimentos as $r)
                    <tr>
                        <td>{{ $r->data->format('d/m/Y') }}</td>
                        <td>{{ $r->descricao ?? '—' }}</td>
                        <td>{{ $r->origem?->descricao ?? '—' }}</td>
                        <td>{{ $r->numero_doc ?? '—' }}</td>
                        <td>R$ {{ number_format($r->valor, 2, ',', '.') }}</td>
                        <td><strong>R$ {{ number_format($r->valor_recebido, 2, ',', '.') }}</strong></td>
                        <td>
                            @if($r->recebido)
                                <span class="badge" style="background:#dcfce7;color:#166534">✅ Recebido</span>
                            @else
                                <span class="badge" style="background:#fef3c7;color:#92400e">⏳ Aguardando</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="abrirModal({{ $r->id }})" class="btn-icon">✏️</button>
                            <button wire:click="excluir({{ $r->id }})" class="btn-icon" onclick="return confirm('Remover?')">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;padding:20px">Nenhum recebimento cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $recebimentos->links() }}</div>
        @endif

        {{-- ══ Tabela Apontamentos ══ --}}
        @if($aba === 'apontamentos')
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Data</th><th>Descrição</th><th>Advogado</th><th>Horas</th><th>Valor</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($apontamentos as $a)
                    <tr>
                        <td>{{ $a->data->format('d/m/Y') }}</td>
                        <td>{{ $a->descricao }}</td>
                        <td>{{ $a->advogado?->nome ?? '—' }}</td>
                        <td><span style="color:#7c3aed;font-weight:700">{{ number_format($a->horas, 1, ',', '.') }}h</span></td>
                        <td>R$ {{ number_format($a->valor, 2, ',', '.') }}</td>
                        <td>
                            <button wire:click="abrirModal({{ $a->id }})" class="btn-icon">✏️</button>
                            <button wire:click="excluir({{ $a->id }})" class="btn-icon" onclick="return confirm('Remover?')">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:#64748b;padding:20px">Nenhum apontamento cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $apontamentos->links() }}</div>
        @endif
    </div>

    {{-- ══ Modal ══ --}}
    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal" style="width:560px">
            <div class="modal-header">
                <span class="modal-title">
                    {{ $registroId ? '✏️ Editar' : '＋ Novo' }}
                    {{ match($aba) { 'pagamentos'=>'Pagamento','recebimentos'=>'Recebimento',default=>'Apontamento' } }}
                </span>
                <button wire:click="fecharModal" class="modal-close">×</button>
            </div>

            {{-- Campos comuns --}}
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data *</label>
                    <input type="date" wire:model="data">
                    @error('data')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                @if($aba !== 'apontamentos')
                <div class="form-field">
                    <label class="lbl">Número do Documento</label>
                    <input type="text" wire:model="numero_doc" placeholder="NF, Recibo, etc.">
                </div>
                @else
                <div class="form-field">
                    <label class="lbl">Advogado</label>
                    <select wire:model="advogado_id">
                        <option value="">Selecione...</option>
                        @foreach($advogados as $adv)
                            <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Descrição *</label>
                <input type="text" wire:model="descricao" placeholder="Descrição do lançamento">
                @error('descricao')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            {{-- Campos de Pagamento --}}
            @if($aba === 'pagamentos')
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Fornecedor</label>
                    <select wire:model="fornecedor_id">
                        <option value="">Nenhum</option>
                        @foreach($fornecedores as $f)
                            <option value="{{ $f->id }}">{{ $f->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Tipo de Documento</label>
                    <input type="text" wire:model="documento" placeholder="NF, Boleto, Recibo...">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Valor (R$)</label>
                    <input type="text" wire:model="valor" placeholder="0,00">
                </div>
                <div class="form-field">
                    <label class="lbl">Valor Pago (R$)</label>
                    <input type="text" wire:model="valor_pago" placeholder="0,00">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data Vencimento</label>
                    <input type="date" wire:model="data_vencimento">
                </div>
                <div class="form-field">
                    <label class="lbl">Data Pagamento</label>
                    <input type="date" wire:model="data_pagamento">
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-bottom:14px;cursor:pointer">
                <input type="checkbox" wire:model="pago" style="width:auto">
                ✅ Marcar como pago
            </label>
            @endif

            {{-- Campos de Recebimento --}}
            @if($aba === 'recebimentos')
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Origem</label>
                    <select wire:model="origem_id">
                        <option value="">Selecione...</option>
                        @foreach($origens as $o)
                            <option value="{{ $o->id }}">{{ $o->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="lbl">Valor Previsto (R$)</label>
                    <input type="text" wire:model="valor" placeholder="0,00">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Valor Recebido (R$)</label>
                    <input type="text" wire:model="valor_recebido" placeholder="0,00">
                </div>
                <div class="form-field">
                    <label class="lbl">Data Recebimento</label>
                    <input type="date" wire:model="data_recebimento">
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-bottom:14px;cursor:pointer">
                <input type="checkbox" wire:model="recebido" style="width:auto">
                ✅ Marcar como recebido
            </label>
            @endif

            {{-- Campos de Apontamento --}}
            @if($aba === 'apontamentos')
            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Horas Trabalhadas</label>
                    <input type="text" wire:model="horas" placeholder="0,00">
                </div>
                <div class="form-field">
                    <label class="lbl">Valor (R$)</label>
                    <input type="text" wire:model="valor" placeholder="0,00">
                </div>
            </div>
            @endif

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success">✓ Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
