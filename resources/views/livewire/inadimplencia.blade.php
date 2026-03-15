<div>

  {{-- Flash --}}
  @if(session('sucesso'))
    <div class="alert-success" style="margin-bottom:16px">✅ {{ session('sucesso') }}</div>
  @endif

  {{-- KPIs --}}
  <div class="stat-grid">
    <div class="stat-card" style="border-left-color:#dc2626">
      <div class="stat-icon">⚠️</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Clientes Inadimplentes</div>
      <div style="font-size:26px;font-weight:800;color:#dc2626;margin-top:4px">{{ $kpis->clientes_inadimplentes ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#f97316">
      <div class="stat-icon">📋</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Parcelas em Atraso</div>
      <div style="font-size:26px;font-weight:800;color:#f97316;margin-top:4px">{{ $kpis->total_parcelas ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#1a3a5c">
      <div class="stat-icon">💰</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Total Devido</div>
      <div style="font-size:22px;font-weight:800;color:#1a3a5c;margin-top:4px">R$ {{ number_format($kpis->total_valor ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon">📅</div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Média de Atraso</div>
      <div style="font-size:26px;font-weight:800;color:#d97706;margin-top:4px">{{ $kpis->media_dias ?? 0 }}d</div>
    </div>
    <div class="stat-card" style="border-left-color:#991b1b">
      <div class="stat-icon">🔴</div>
      <div style="font-size:11px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.5px">Crítico (+30 dias)</div>
      <div style="font-size:20px;font-weight:800;color:#991b1b;margin-top:4px">R$ {{ number_format($kpis->valor_critico ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>

  {{-- Filtros --}}
  <div class="card" style="margin-bottom:16px">
    <div class="filter-bar">
      <input wire:model.live.debounce.300ms="filtroCliente" type="text" placeholder="Buscar cliente...">
      <select wire:model.live="filtroStatus">
        <option value="">Todos os status</option>
        <option value="atrasado">Atrasado (1–15 dias)</option>
        <option value="em_cobranca">Em Cobrança (16–30 dias)</option>
        <option value="inadimplente">Inadimplente (+30 dias)</option>
      </select>
      <select wire:model.live="filtroOrdem">
        <option value="dias_desc">Maior atraso primeiro</option>
        <option value="valor_desc">Maior valor primeiro</option>
        <option value="valor_asc">Menor valor primeiro</option>
        <option value="nome_asc">Nome (A–Z)</option>
      </select>
    </div>
  </div>

  {{-- Lista de clientes --}}
  @if(empty($clientes))
    <div class="card" style="text-align:center;padding:48px">
      <div style="font-size:36px;margin-bottom:12px">🎉</div>
      <div style="font-weight:600;color:var(--text)">Nenhuma inadimplência encontrada!</div>
      <div style="color:var(--muted);font-size:13px;margin-top:4px">Todos os honorários estão em dia.</div>
    </div>
  @else
    <div style="display:flex;flex-direction:column;gap:12px">
      @foreach($clientes as $cliente)
        @php
          $dias   = $cliente->max_dias;
          $status = $dias <= 15 ? 'atrasado' : ($dias <= 30 ? 'em_cobranca' : 'inadimplente');
          $statusLabel = ['atrasado' => 'Atrasado', 'em_cobranca' => 'Em Cobrança', 'inadimplente' => 'Inadimplente'][$status];
          $statusBadge = [
            'atrasado'    => 'background:#fef3c7;color:#92400e;border:1px solid #fbbf24',
            'em_cobranca' => 'background:#fed7aa;color:#9a3412;border:1px solid #fb923c',
            'inadimplente'=> 'background:#fee2e2;color:#991b1b;border:1px solid #f87171',
          ][$status];
          $leftBorder = [
            'atrasado'    => '#f59e0b',
            'em_cobranca' => '#f97316',
            'inadimplente'=> '#dc2626',
          ][$status];
          $dCor = ['atrasado' => '#d97706', 'em_cobranca' => '#f97316', 'inadimplente' => '#dc2626'][$status];
          $parcelas = $parcelasPorCliente[$cliente->cliente_id] ?? [];
        @endphp

        <div class="card" style="padding:0;border-left:4px solid {{ $leftBorder }};overflow:hidden"
             x-data="{ open: false }">

          {{-- Cabeçalho cliente --}}
          <div style="padding:14px 16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <div style="flex:1;min-width:0">
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <span style="font-weight:700;font-size:14px;color:var(--text)">{{ $cliente->cliente_nome }}</span>
                <span class="badge" style="{{ $statusBadge }};font-size:11px;padding:2px 8px;border-radius:12px">{{ $statusLabel }}</span>
                @if($cliente->tentativas_recentes > 0)
                  <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:11px;padding:2px 8px;border-radius:12px">
                    {{ $cliente->tentativas_recentes }} contato(s)
                  </span>
                @endif
              </div>
              <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:4px;font-size:12px;color:var(--muted)">
                <span>{{ $cliente->qtd_parcelas }} parcela(s)</span>
                <span>Maior atraso: <strong style="color:{{ $dCor }}">{{ $dias }} dias</strong></span>
                @if($cliente->cliente_celular)
                  <span>📱 {{ $cliente->cliente_celular }}</span>
                @endif
                @if($cliente->ultimo_contato_em)
                  <span>Último contato: {{ \Carbon\Carbon::parse($cliente->ultimo_contato_em)->format('d/m/Y') }}
                    ({{ $cliente->ultimo_contato_tipo }})
                  </span>
                @endif
              </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
              <div style="text-align:right;margin-right:4px">
                <div style="font-size:16px;font-weight:700;color:var(--text)">R$ {{ number_format($cliente->total_devido, 2, ',', '.') }}</div>
                <div style="font-size:11px;color:var(--muted)">total devido</div>
              </div>

              <button wire:click="abrirContato({{ $parcelas[0]->id ?? 0 }}, {{ $cliente->cliente_id }})"
                      @if(empty($parcelas)) disabled @endif
                      class="btn btn-primary btn-sm">
                📝 Contato
              </button>

              <button wire:click="abrirEmail({{ $cliente->cliente_id }})" class="btn btn-sm"
                      style="background:#7c3aed;color:#fff;border-color:#7c3aed">
                ✉️ E-mail
              </button>

              <button @click="open = !open" class="btn btn-outline btn-sm">
                <span x-text="open ? '▲ Fechar' : '▼ Parcelas'"></span>
              </button>
            </div>
          </div>

          {{-- Detalhe parcelas (collapsible) --}}
          <div x-show="open" style="border-top:1px solid var(--border)">
            <div class="table-wrap" style="margin:0">
              <table>
                <thead>
                  <tr>
                    <th>Parcela</th>
                    <th>Descrição</th>
                    <th>Vencimento</th>
                    <th style="text-align:right">Valor</th>
                    <th style="text-align:center">Atraso</th>
                    <th style="text-align:center">Tentativas</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($parcelas as $parc)
                    @php
                      $d = $parc->dias_atraso;
                      $dCorPauta = $d <= 15 ? '#d97706' : ($d <= 30 ? '#f97316' : '#dc2626');
                    @endphp
                    <tr>
                      <td style="font-weight:600">{{ $parc->numero_parcela }}ª</td>
                      <td style="color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $parc->honorario_desc }}</td>
                      <td>{{ \Carbon\Carbon::parse($parc->vencimento)->format('d/m/Y') }}</td>
                      <td style="text-align:right;font-weight:600">R$ {{ number_format($parc->valor, 2, ',', '.') }}</td>
                      <td style="text-align:center;font-weight:700;color:{{ $dCorPauta }}">{{ $d }}d</td>
                      <td style="text-align:center;color:var(--muted)">{{ $parc->tentativas }}</td>
                      <td>
                        <div style="display:flex;gap:4px;justify-content:flex-end">
                          <button wire:click="abrirContato({{ $parc->id }}, {{ $cliente->cliente_id }})"
                                  class="btn btn-primary btn-sm">Contato</button>
                          <button wire:click="abrirPagamento({{ $parc->id }})"
                                  class="btn btn-success btn-sm">Pagar</button>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif


  {{-- ─── Modal Registrar Contato ─────────────────────────── --}}
  @if($modalContato)
  <div class="modal-backdrop" wire:click.self="$set('modalContato', false)">
    <div class="modal" style="max-width:500px">
      <div class="modal-header">
        <span class="modal-title">📝 Registrar Tentativa de Contato</span>
        <button wire:click="$set('modalContato', false)" class="modal-close">×</button>
      </div>

      <div class="form-field" style="margin:20px 20px 0">
        <label class="lbl">Tipo de Contato</label>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:4px">
          @foreach(['ligacao' => '📞 Ligação', 'whatsapp' => '💬 WhatsApp', 'email' => '✉️ E-mail',
                    'reuniao' => '🤝 Reunião', 'negociacao' => '🔄 Negociação', 'acordo' => '✅ Acordo'] as $val => $label)
            <label style="display:flex;align-items:center;justify-content:center;padding:8px;border:1px solid {{ $tipoContato === $val ? '#1a3a5c' : 'var(--border)' }};
                          border-radius:6px;cursor:pointer;font-size:12px;font-weight:500;
                          background:{{ $tipoContato === $val ? '#1a3a5c' : '#fff' }};
                          color:{{ $tipoContato === $val ? '#fff' : 'var(--text)' }}">
              <input type="radio" wire:model.live="tipoContato" value="{{ $val }}" style="display:none">
              {{ $label }}
            </label>
          @endforeach
        </div>
        @error('tipoContato') <span class="invalid-feedback">{{ $message }}</span> @enderror
      </div>

      <div class="form-field" style="margin:12px 20px 0">
        <label class="lbl">Observação (opcional)</label>
        <textarea wire:model="descContato" rows="3" placeholder="Descreva o resultado do contato..."
                  style="resize:none"></textarea>
      </div>

      @if($tipoContato === 'acordo')
        <div style="margin:8px 20px 0;padding:10px;background:#f0fdf4;border:1px solid #86efac;border-radius:6px;font-size:12px;color:#166534">
          ✅ Registrar como "Acordo" indica que houve negociação. Registre o pagamento separadamente quando for efetivado.
        </div>
      @endif

      <div class="modal-footer">
        <button wire:click="$set('modalContato', false)" class="btn btn-outline">Cancelar</button>
        <button wire:click="salvarContato" class="btn btn-primary">Salvar</button>
      </div>
    </div>
  </div>
  @endif


  {{-- ─── Modal Enviar E-mail ─────────────────────────────── --}}
  @if($modalEmail)
  <div class="modal-backdrop" wire:click.self="$set('modalEmail', false)">
    <div class="modal" style="max-width:460px">
      <div class="modal-header">
        <span class="modal-title">✉️ Enviar E-mail de Cobrança</span>
        <button wire:click="$set('modalEmail', false)" class="modal-close">×</button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:12px">
        @if($emailErro)
          <div class="alert-error">{{ $emailErro }}</div>
        @endif

        <div class="form-field">
          <label class="lbl">Cliente</label>
          <div style="padding:8px 12px;background:#f8fafc;border:1px solid var(--border);border-radius:6px;font-size:13px;color:var(--text)">
            {{ $clienteNomeEmail }}
          </div>
        </div>

        <div class="form-field">
          <label class="lbl">E-mail *</label>
          <input wire:model="clienteEmailAddr" type="email" placeholder="email@cliente.com.br">
          @error('clienteEmailAddr') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div style="padding:10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;color:#1d4ed8">
          ℹ️ O e-mail listará todas as parcelas atrasadas deste cliente com os respectivos valores e datas de vencimento.
        </div>
      </div>

      <div class="modal-footer">
        <button wire:click="$set('modalEmail', false)" class="btn btn-outline">Cancelar</button>
        <button wire:click="enviarEmail" wire:loading.attr="disabled" class="btn btn-sm"
                style="background:#7c3aed;color:#fff;border-color:#7c3aed;padding:8px 20px">
          <span wire:loading.remove wire:target="enviarEmail">Enviar E-mail</span>
          <span wire:loading wire:target="enviarEmail">Enviando...</span>
        </button>
      </div>
    </div>
  </div>
  @endif


  {{-- ─── Modal Pagamento Rápido ─────────────────────────── --}}
  @if($modalPagamento)
  <div class="modal-backdrop" wire:click.self="$set('modalPagamento', false)">
    <div class="modal" style="max-width:400px">
      <div class="modal-header">
        <span class="modal-title">💰 Registrar Pagamento</span>
        <button wire:click="$set('modalPagamento', false)" class="modal-close">×</button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:12px">
        <div class="form-field">
          <label class="lbl">Data do Pagamento</label>
          <input wire:model="dataPagamento" type="date">
          @error('dataPagamento') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
          <label class="lbl">Valor Pago (R$)</label>
          <input wire:model="valorPago" type="number" step="0.01" min="0.01">
          @error('valorPago') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
          <label class="lbl">Forma de Pagamento</label>
          <select wire:model="formaPagamento">
            <option value="pix">PIX</option>
            <option value="transferencia">Transferência</option>
            <option value="boleto">Boleto</option>
            <option value="cheque">Cheque</option>
            <option value="dinheiro">Dinheiro</option>
            <option value="cartao">Cartão</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button wire:click="$set('modalPagamento', false)" class="btn btn-outline">Cancelar</button>
        <button wire:click="registrarPagamento" class="btn btn-success">✓ Confirmar Pagamento</button>
      </div>
    </div>
  </div>
  @endif

</div>
