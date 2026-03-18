<div>

  {{-- KPIs --}}
  <div class="stat-grid">
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Pendentes</div>
      <div style="font-size:26px;font-weight:800;color:#d97706;margin-top:4px">{{ $kpis['pendentes'] }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#2563eb">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aceitas</div>
      <div style="font-size:26px;font-weight:800;color:#2563eb;margin-top:4px">{{ $kpis['aceitas'] }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#7c3aed">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Realizadas (mês)</div>
      <div style="font-size:26px;font-weight:800;color:#7c3aed;margin-top:4px">{{ $kpis['realizadas_mes'] }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px">A Pagar</div>
      <div style="font-size:22px;font-weight:800;color:#92400e;margin-top:4px">R$ {{ number_format($kpis['a_pagar'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#16a34a">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#166534" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.5px">Pago (mês)</div>
      <div style="font-size:22px;font-weight:800;color:#166534;margin-top:4px">R$ {{ number_format($kpis['pagas_mes'], 0, ',', '.') }}</div>
    </div>
  </div>

  {{-- Filtros + Novo --}}
  <div class="card" style="margin-bottom:16px">
    <div class="filter-bar">
      <div style="position:relative;flex:1;min-width:180px;">
        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
        <input wire:model.live.debounce.300ms="filtroBusca" type="text"
               placeholder="Buscar comarca, advogado, processo..." style="padding-left:34px;">
      </div>
      <select wire:model.live="filtroStatus">
        <option value="">Todos os status</option>
        @foreach(\App\Models\Correspondente::statusLabel() as $val => $label)
          <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
      </select>
      <select wire:model.live="filtroTipo">
        <option value="">Todos os tipos</option>
        @foreach(\App\Models\Correspondente::tiposLabel() as $val => $label)
          <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
      </select>
      <select wire:model.live="filtroAdvogado">
        <option value="">Todos os advogados</option>
        @foreach($advogados as $adv)
          <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
        @endforeach
      </select>
      <button wire:click="exportarCsv" wire:loading.attr="disabled"
          class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
          <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:5px;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> CSV</span>
          <span wire:loading wire:target="exportarCsv">Gerando…</span>
      </button>
      <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="flex-shrink:0;">＋ Nova Correspondência</button>
    </div>
  </div>

  {{-- Tabela --}}
  <div class="card" style="padding:0;overflow:hidden">
    @if($correspondencias->isEmpty())
      <div class="empty-state">
        <div class="empty-state-icon"><svg aria-hidden="true" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><line x1="12" y1="3" x2="12" y2="21"/><path d="M3 6l9-3 9 3"/><path d="M3 18l4-8 4 8"/><path d="M13 18l4-8 4 8"/><line x1="2" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="22" y2="18"/></svg></div>
        <div class="empty-state-title">Nenhuma correspondência encontrada</div>
        <div class="empty-state-sub">Cadastre diligências delegadas a advogados correspondentes.</div>
      </div>
    @else
      <div class="table-wrap" style="margin:0">
        <table>
          <thead>
            <tr>
              <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Advogado</th>
              <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Comarca / UF</th>
              <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Tipo</th>
              <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Processo</th>
              <th class="hide-xs" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Prazo</th>
              <th style="text-align:right;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Valor</th>
              <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($correspondencias as $c)
              @php
                $prazoAtrasado = $c->data_prazo && $c->data_prazo->isPast()
                    && !in_array($c->status, ['realizado', 'pago', 'cancelado']);
              @endphp
              <tr style="{{ $prazoAtrasado ? 'background:#fff5f5' : '' }}">
                <td>
                  <div style="font-weight:600;color:var(--text)">{{ $c->advogado->nome }}</div>
                  @if($c->advogado->oab)
                    <div style="font-size:11px;color:var(--muted)">OAB {{ $c->advogado->oab }}</div>
                  @endif
                </td>
                <td class="hide-sm">
                  <div style="color:var(--text)">{{ $c->comarca }}</div>
                  @if($c->estado)
                    <div style="font-size:11px;color:var(--muted)">{{ $c->estado }}</div>
                  @endif
                </td>
                <td class="hide-sm">
                  <span class="badge" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:11px">
                    {{ $c->tipoLabel() }}
                  </span>
                </td>
                <td class="hide-sm">
                  @if($c->processo)
                    <div style="font-size:12px;font-family:monospace;color:var(--muted)">{{ $c->processo->numero }}</div>
                    <div style="font-size:11px;color:var(--muted)">{{ $c->processo->cliente?->nome }}</div>
                  @else
                    <span style="color:var(--muted)">—</span>
                  @endif
                </td>
                <td class="hide-xs">
                  @if($c->data_prazo)
                    <div style="font-size:13px;{{ $prazoAtrasado ? 'color:#dc2626;font-weight:700' : 'color:var(--text)' }}">
                      {{ $c->data_prazo->format('d/m/Y') }}
                      @if($prazoAtrasado)
                        <div style="font-size:11px;color:#dc2626">{{ $c->data_prazo->diffForHumans() }}</div>
                      @endif
                    </div>
                  @else
                    <span style="color:var(--muted)">—</span>
                  @endif
                </td>
                <td style="text-align:right">
                  @if($c->valor_combinado)
                    <div style="font-weight:600;color:var(--text)">R$ {{ number_format($c->valor_combinado, 2, ',', '.') }}</div>
                    @if($c->status === 'pago' && $c->valor_pago)
                      <div style="font-size:11px;color:#16a34a">Pago: R$ {{ number_format($c->valor_pago, 2, ',', '.') }}</div>
                    @endif
                  @else
                    <span style="color:var(--muted)">—</span>
                  @endif
                </td>
                <td style="text-align:center">
                  <span class="badge" style="{{ $c->statusCor() }};font-size:11px;padding:3px 10px;border-radius:12px">
                    {{ \App\Models\Correspondente::statusLabel()[$c->status] ?? $c->status }}
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:4px;justify-content:flex-end;align-items:center">
                    @if($c->proximoStatus())
                      @php
                        $proximoLabel = \App\Models\Correspondente::statusLabel()[$c->proximoStatus()];
                        $proximoStyle = match($c->proximoStatus()) {
                          'aceito'    => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe',
                          'realizado' => 'background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe',
                          'pago'      => 'background:#f0fdf4;color:#15803d;border:1px solid #86efac',
                          default     => 'background:#f8fafc;color:var(--muted);border:1px solid var(--border)',
                        };
                      @endphp
                      <button wire:click="abrirAvancar({{ $c->id }})"
                              class="btn btn-sm" style="{{ $proximoStyle }};padding:4px 10px;font-size:11px">
                        → {{ $proximoLabel }}
                      </button>
                    @endif
                    <button wire:click="abrirModal({{ $c->id }})" title="Editar" style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    <button wire:click="excluir({{ $c->id }})" wire:confirm="Excluir esta correspondência?" title="Excluir" style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="pagination-bar" style="padding:12px 16px;">{{ $correspondencias->links() }}</div>
    @endif
  </div>


  {{-- ─── Modal Principal ──────────────────────────────────── --}}
  @if($modalAberto)
  <div class="modal-backdrop" wire:click.self="fecharModal">
    <div class="modal" style="max-width:620px;max-height:90vh;overflow-y:auto">
      <div class="modal-header">
        <span class="modal-title">
          @if($correspondente_id)
            <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Editar Correspondência</span>
          @else
            ＋ Nova Correspondência
          @endif
        </span>
        <button wire:click="fecharModal" class="modal-close" aria-label="Fechar"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:14px">

        <div class="form-field">
          <label class="lbl">Advogado Correspondente *</label>
          <select wire:model="advogado_id">
            <option value="">Selecione o advogado...</option>
            @foreach($advogados as $adv)
              <option value="{{ $adv->id }}">{{ $adv->nome }}{{ $adv->oab ? ' — OAB ' . $adv->oab : '' }}</option>
            @endforeach
          </select>
          @error('advogado_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">Comarca *</label>
            <input wire:model="comarca" type="text" placeholder="Ex: Campinas">
            @error('comarca') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-field">
            <label class="lbl">Estado (UF)</label>
            <select wire:model="estado">
              <option value="">Selecione...</option>
              @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                <option value="{{ $uf }}">{{ $uf }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">Tipo de Diligência *</label>
            <select wire:model="tipo">
              @foreach(\App\Models\Correspondente::tiposLabel() as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('tipo') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-field">
            <label class="lbl">Processo (opcional)</label>
            <select wire:model="processo_id">
              <option value="">Sem processo vinculado</option>
              @foreach($processos as $proc)
                <option value="{{ $proc->id }}">{{ $proc->numero }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-field">
          <label class="lbl">Descrição da Diligência *</label>
          <textarea wire:model="descricao" rows="3"
                    placeholder="Descreva o que deve ser realizado..."
                    style="resize:none"></textarea>
          @error('descricao') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">Data da Solicitação *</label>
            <input wire:model="data_solicitacao" type="date">
            @error('data_solicitacao') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-field">
            <label class="lbl">Prazo para Realização</label>
            <input wire:model="data_prazo" type="date">
          </div>
        </div>

        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">Valor Combinado (R$)</label>
            <input wire:model="valor_combinado" type="number" step="0.01" min="0" placeholder="0,00">
            @error('valor_combinado') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          @if($correspondente_id)
          <div class="form-field">
            <label class="lbl">Status</label>
            <select wire:model="status">
              @foreach(\App\Models\Correspondente::statusLabel() as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          @endif
        </div>

        <div class="form-field">
          <label class="lbl">Observações</label>
          <textarea wire:model="observacoes" rows="2" style="resize:none"></textarea>
        </div>

      </div>

      <div class="modal-footer">
        <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
        <button wire:click="salvar" class="btn btn-primary">
          @if($correspondente_id)
            <span style="display:inline-flex;align-items:center;gap:5px;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Salvar Alterações</span>
          @else
            ＋ Cadastrar
          @endif
        </button>
      </div>
    </div>
  </div>
  @endif


  {{-- ─── Modal Avançar Status ─────────────────────────────── --}}
  @if($modalAvancar)
  <div class="modal-backdrop" wire:click.self="$set('modalAvancar', false)">
    <div class="modal" style="max-width:380px">
      @php $labelProximo = \App\Models\Correspondente::statusLabel()[$avancarStatus] ?? $avancarStatus; @endphp
      <div class="modal-header">
        <span class="modal-title">Marcar como {{ $labelProximo }}</span>
        <button wire:click="$set('modalAvancar', false)" class="modal-close" aria-label="Fechar"><svg aria-hidden="true" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
        <div class="form-field">
          <label class="lbl">Data {{ $avancarStatus === 'pago' ? 'do Pagamento' : 'da Realização' }}</label>
          <input wire:model="avancarData" type="date">
        </div>

        @if($avancarStatus === 'pago')
        <div class="form-field">
          <label class="lbl">Valor Pago (R$)</label>
          <input wire:model="avancarValorPago" type="number" step="0.01" min="0">
        </div>
        @endif

        @if($avancarStatus === 'realizado')
        <div class="form-field">
          <label class="lbl">Observação (resultado)</label>
          <textarea wire:model="avancarObs" rows="2"
                    placeholder="Descreva o resultado da diligência..."
                    style="resize:none"></textarea>
        </div>
        @endif
      </div>

      <div class="modal-footer">
        <button wire:click="$set('modalAvancar', false)" class="btn btn-outline">Cancelar</button>
        <button wire:click="confirmarAvancar" class="btn btn-success"><span style="display:inline-flex;align-items:center;gap:5px;"><svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Confirmar</span></button>
      </div>
    </div>
  </div>
  @endif

</div>
