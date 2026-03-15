<div>

  {{-- KPIs --}}
  <div class="stat-grid">
    <div class="stat-card" style="border-left-color:#dc2626">
      <div class="stat-icon"><svg width="20" height="20" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Clientes Inadimplentes</div>
      <div style="font-size:26px;font-weight:800;color:#dc2626;margin-top:4px">{{ $kpis->clientes_inadimplentes ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#f97316">
      <div class="stat-icon"><svg width="20" height="20" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Parcelas em Atraso</div>
      <div style="font-size:26px;font-weight:800;color:#f97316;margin-top:4px">{{ $kpis->total_parcelas ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#1a3a5c">
      <div class="stat-icon"><svg width="20" height="20" fill="none" stroke="#1a3a5c" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Total Devido</div>
      <div style="font-size:22px;font-weight:800;color:#1a3a5c;margin-top:4px">R$ {{ number_format($kpis->total_valor ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon"><svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Média de Atraso</div>
      <div style="font-size:26px;font-weight:800;color:#d97706;margin-top:4px">{{ $kpis->media_dias ?? 0 }}d</div>
    </div>
    <div class="stat-card" style="border-left-color:#991b1b">
      <div class="stat-icon"><svg width="20" height="20" fill="none" stroke="#991b1b" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.5px">Crítico (+30 dias)</div>
      <div style="font-size:20px;font-weight:800;color:#991b1b;margin-top:4px">R$ {{ number_format($kpis->valor_critico ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>

  {{-- Filtros --}}
  <div class="card" style="margin-bottom:16px">
    <div class="filter-bar">
      <div style="position:relative;flex:1;min-width:180px;">
        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
        <input wire:model.live.debounce.300ms="filtroCliente" type="text" placeholder="Buscar cliente..." style="padding-left:34px;">
      </div>
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
      <button wire:click="exportarCsv" wire:loading.attr="disabled"
          class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
          <span wire:loading.remove wire:target="exportarCsv" style="display:inline-flex;align-items:center;gap:5px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> CSV</span>
          <span wire:loading wire:target="exportarCsv">Gerando…</span>
      </button>
    </div>
  </div>

  {{-- Lista de clientes --}}
  @if(empty($clientes))
    <div class="card" style="text-align:center;padding:48px">
      <div style="margin-bottom:12px;display:flex;justify-content:center;"><svg width="36" height="36" fill="none" stroke="var(--success)" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
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
                  <span style="display:inline-flex;align-items:center;gap:3px;"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.67 19.79 19.79 0 01.1 2.14 2 2 0 012.11 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.34a16 16 0 006.29 6.29l.75-.75a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg> {{ $cliente->cliente_celular }}</span>
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
                <span style="display:inline-flex;align-items:center;gap:5px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg> Contato</span>
              </button>

              <button wire:click="abrirEmail({{ $cliente->cliente_id }})" class="btn btn-sm"
                      style="background:#7c3aed;color:#fff;border-color:#7c3aed">
                <span style="display:inline-flex;align-items:center;gap:5px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2-2-2z"/><polyline points="22,6 12,13 2,6"/></svg> E-mail</span>
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
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Parcela</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Descrição</th>
                    <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Vencimento</th>
                    <th style="text-align:right;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Valor</th>
                    <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Atraso</th>
                    <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;padding:10px 12px;font-weight:600;">Tentativas</th>
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
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:8px;">
          <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#eff6ff;border-radius:6px;"><svg width="14" height="14" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
          Registrar Tentativa de Contato
        </span>
        <button wire:click="$set('modalContato', false)" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>

      <div class="form-field" style="margin:20px 20px 0">
        <label class="lbl">Tipo de Contato</label>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:4px">
          @php
          $tiposContato = [
            'ligacao'    => ['svg' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.67 19.79 19.79 0 01.1 2.14 2 2 0 012.11 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.34a16 16 0 006.29 6.29l.75-.75a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>', 'label' => 'Ligação'],
            'whatsapp'   => ['svg' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>', 'label' => 'WhatsApp'],
            'email'      => ['svg' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2-2-2z"/><polyline points="22,6 12,13 2,6"/></svg>', 'label' => 'E-mail'],
            'reuniao'    => ['svg' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>', 'label' => 'Reunião'],
            'negociacao' => ['svg' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>', 'label' => 'Negociação'],
            'acordo'     => ['svg' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>', 'label' => 'Acordo'],
          ];
          @endphp
          @foreach($tiposContato as $val => $info)
            <label style="display:flex;align-items:center;justify-content:center;padding:8px;border:1px solid {{ $tipoContato === $val ? '#1a3a5c' : 'var(--border)' }};
                          border-radius:6px;cursor:pointer;font-size:12px;font-weight:500;
                          background:{{ $tipoContato === $val ? '#1a3a5c' : '#fff' }};
                          color:{{ $tipoContato === $val ? '#fff' : 'var(--text)' }}">
              <input type="radio" wire:model.live="tipoContato" value="{{ $val }}" style="display:none">
              <span style="display:flex;align-items:center;gap:5px;">{!! $info['svg'] !!} {{ $info['label'] }}</span>
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
          <span style="display:inline-flex;align-items:center;gap:5px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Registrar como "Acordo" indica que houve negociação. Registre o pagamento separadamente quando for efetivado.</span>
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
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:8px;">
          <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#f5f3ff;border-radius:6px;"><svg width="14" height="14" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2-2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
          Enviar E-mail de Cobrança
        </span>
        <button wire:click="$set('modalEmail', false)" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
          <span style="display:inline-flex;align-items:center;gap:5px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> O e-mail listará todas as parcelas atrasadas deste cliente com os respectivos valores e datas de vencimento.</span>
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
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:8px;">
          <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#f0fdf4;border-radius:6px;"><svg width="14" height="14" fill="none" stroke="#166534" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span>
          Registrar Pagamento
        </span>
        <button wire:click="$set('modalPagamento', false)" class="modal-close"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
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
        <button wire:click="registrarPagamento" class="btn btn-success"><span style="display:inline-flex;align-items:center;gap:5px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Confirmar Pagamento</span></button>
      </div>
    </div>
  </div>
  @endif

</div>
