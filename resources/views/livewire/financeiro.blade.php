<div>
    {{-- ══ Cards de totais ══ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px">

        {{-- Pagamentos --}}
        <div class="stat-card" style="border-left-color:#dc2626">
            <div class="stat-icon"><svg width="20" height="20" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
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
            <div class="stat-icon"><svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></div>
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
            <div class="stat-icon"><svg width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
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
            <button wire:click="$set('aba','pagamentos')"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;
                       border-bottom:2px solid {{ $aba==='pagamentos' ? '#1a3a5c' : 'transparent' }};
                       color:{{ $aba==='pagamentos' ? '#1a3a5c' : '#64748b' }};margin-bottom:-2px;display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Pagamentos
            </button>
            <button wire:click="$set('aba','recebimentos')"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;
                       border-bottom:2px solid {{ $aba==='recebimentos' ? '#1a3a5c' : 'transparent' }};
                       color:{{ $aba==='recebimentos' ? '#1a3a5c' : '#64748b' }};margin-bottom:-2px;display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Recebimentos
            </button>
            <button wire:click="$set('aba','apontamentos')"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;
                       border-bottom:2px solid {{ $aba==='apontamentos' ? '#1a3a5c' : 'transparent' }};
                       color:{{ $aba==='apontamentos' ? '#1a3a5c' : '#64748b' }};margin-bottom:-2px;display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Apontamentos
            </button>
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
                    <tr>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Data</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Descrição</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Fornecedor</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Nº Doc</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Vencimento</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Valor</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Pago</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Ações</th>
                    </tr>
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
                                <span style="color:{{ $atrasado ? '#dc2626' : 'inherit' }};display:inline-flex;align-items:center;gap:4px;">
                                    {{ $venc->format('d/m/Y') }}
                                    @if($atrasado) <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> @endif
                                </span>
                            @else —
                            @endif
                        </td>
                        <td><strong>R$ {{ number_format($p->valor, 2, ',', '.') }}</strong></td>
                        <td>
                            @if($p->pago)
                                <span class="badge" style="background:#dcfce7;color:#166534">Pago</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b">Pendente</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="abrirModal({{ $p->id }})" title="Editar" style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button wire:click="excluir({{ $p->id }})" wire:confirm="Remover?" title="Excluir" style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></button>
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
                    <tr>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Data</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Descrição</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Origem</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Nº Doc</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Valor Previsto</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Valor Recebido</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Status</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Ações</th>
                    </tr>
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
                                <span class="badge" style="background:#dcfce7;color:#166534">Recebido</span>
                            @else
                                <span class="badge" style="background:#fef3c7;color:#92400e">Aguardando</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="abrirModal({{ $r->id }})" title="Editar" style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button wire:click="excluir({{ $r->id }})" wire:confirm="Remover?" title="Excluir" style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></button>
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
                    <tr>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Data</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Descrição</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Advogado</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Horas</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Valor</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Ações</th>
                    </tr>
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
                            <button wire:click="abrirModal({{ $a->id }})" title="Editar" style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button wire:click="excluir({{ $a->id }})" wire:confirm="Remover?" title="Excluir" style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></button>
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
        <div class="modal" style="max-width:560px">
            <div class="modal-header">
                <span class="modal-title" style="display:inline-flex;align-items:center;gap:6px;">
                    @if($registroId)
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Editar
                    @else
                        ＋ Novo
                    @endif
                    {{ match($aba) { 'pagamentos'=>'Pagamento','recebimentos'=>'Recebimento',default=>'Apontamento' } }}
                </span>
                <button wire:click="fecharModal" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
                Marcar como pago
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
                Marcar como recebido
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
                <button wire:click="salvar" class="btn btn-success" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
