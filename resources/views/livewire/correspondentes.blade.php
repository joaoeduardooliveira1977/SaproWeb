<div>

  {{-- Flash --}}
  @if(session('sucesso'))
    <div class="alert-success" style="margin-bottom:16px">✅ {{ session('sucesso') }}</div>
  @endif

  {{-- KPIs --}}
  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:20px">
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon">⏳</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Pendentes</div>
      <div style="font-size:26px;font-weight:800;color:#d97706;margin-top:4px">{{ $kpis['pendentes'] }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#2563eb">
      <div class="stat-icon">✔️</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aceitas</div>
      <div style="font-size:26px;font-weight:800;color:#2563eb;margin-top:4px">{{ $kpis['aceitas'] }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#7c3aed">
      <div class="stat-icon">📅</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Realizadas (mês)</div>
      <div style="font-size:26px;font-weight:800;color:#7c3aed;margin-top:4px">{{ $kpis['realizadas_mes'] }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon">💸</div>
      <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px">A Pagar</div>
      <div style="font-size:22px;font-weight:800;color:#92400e;margin-top:4px">R$ {{ number_format($kpis['a_pagar'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#16a34a">
      <div class="stat-icon">✅</div>
      <div style="font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.5px">Pago (mês)</div>
      <div style="font-size:22px;font-weight:800;color:#166534;margin-top:4px">R$ {{ number_format($kpis['pagas_mes'], 0, ',', '.') }}</div>
    </div>
  </div>

  {{-- Filtros + Novo --}}
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <div style="flex:1;min-width:200px">
        <input wire:model.live.debounce.300ms="filtroBusca" type="text"
               placeholder="Buscar comarca, advogado, processo..."
               class="search-bar" style="width:100%">
      </div>
      <select wire:model.live="filtroStatus" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os status</option>
        @foreach(\App\Models\Correspondente::statusLabel() as $val => $label)
          <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
      </select>
      <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os tipos</option>
        @foreach(\App\Models\Correspondente::tiposLabel() as $val => $label)
          <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
      </select>
      <select wire:model.live="filtroAdvogado" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os advogados</option>
        @foreach($advogados as $adv)
          <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
        @endforeach
      </select>
      <button wire:click="abrirModal()" class="btn btn-primary btn-sm">＋ Nova Correspondência</button>
    </div>
  </div>

  {{-- Tabela --}}
  <div class="card" style="padding:0;overflow:hidden">
    @if($correspondencias->isEmpty())
      <div style="padding:48px;text-align:center">
        <div style="font-size:36px;margin-bottom:12px">⚖️</div>
        <div style="font-weight:600;color:var(--text)">Nenhuma correspondência encontrada</div>
        <div style="color:var(--muted);font-size:13px;margin-top:4px">Cadastre diligências delegadas a advogados correspondentes.</div>
      </div>
    @else
      <div class="table-wrap" style="margin:0">
        <table>
          <thead>
            <tr>
              <th>Advogado</th>
              <th>Comarca / UF</th>
              <th>Tipo</th>
              <th>Processo</th>
              <th>Prazo</th>
              <th style="text-align:right">Valor</th>
              <th style="text-align:center">Status</th>
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
                <td>
                  <div style="color:var(--text)">{{ $c->comarca }}</div>
                  @if($c->estado)
                    <div style="font-size:11px;color:var(--muted)">{{ $c->estado }}</div>
                  @endif
                </td>
                <td>
                  <span class="badge" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:11px">
                    {{ $c->tipoLabel() }}
                  </span>
                </td>
                <td>
                  @if($c->processo)
                    <div style="font-size:12px;font-family:monospace;color:var(--muted)">{{ $c->processo->numero }}</div>
                    <div style="font-size:11px;color:var(--muted)">{{ $c->processo->cliente?->nome }}</div>
                  @else
                    <span style="color:var(--muted)">—</span>
                  @endif
                </td>
                <td>
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
                    <button wire:click="abrirModal({{ $c->id }})" class="btn-icon" title="Editar">✏️</button>
                    <button wire:click="excluir({{ $c->id }})" wire:confirm="Excluir esta correspondência?"
                            class="btn-icon" title="Excluir">🗑️</button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($correspondencias->hasPages())
        <div class="pagination">{{ $correspondencias->links() }}</div>
      @endif
    @endif
  </div>


  {{-- ─── Modal Principal ──────────────────────────────────── --}}
  @if($modalAberto)
  <div class="modal-backdrop" wire:click.self="fecharModal">
    <div class="modal" style="width:620px;max-height:90vh;overflow-y:auto">
      <div class="modal-header">
        <span class="modal-title">
          {{ $correspondente_id ? '✏️ Editar Correspondência' : '＋ Nova Correspondência' }}
        </span>
        <button wire:click="fecharModal" class="modal-close">×</button>
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
          {{ $correspondente_id ? '✓ Salvar Alterações' : '＋ Cadastrar' }}
        </button>
      </div>
    </div>
  </div>
  @endif


  {{-- ─── Modal Avançar Status ─────────────────────────────── --}}
  @if($modalAvancar)
  <div class="modal-backdrop" wire:click.self="$set('modalAvancar', false)">
    <div class="modal" style="width:380px">
      @php $labelProximo = \App\Models\Correspondente::statusLabel()[$avancarStatus] ?? $avancarStatus; @endphp
      <div class="modal-header">
        <span class="modal-title">Marcar como {{ $labelProximo }}</span>
        <button wire:click="$set('modalAvancar', false)" class="modal-close">×</button>
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
        <button wire:click="confirmarAvancar" class="btn btn-success">✓ Confirmar</button>
      </div>
    </div>
  </div>
  @endif

</div>
