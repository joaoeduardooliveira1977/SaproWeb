<div>

  {{-- Status configuração --}}
  @if(!$svc->configurado())
  <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;margin-bottom:16px">
    <span style="font-size:18px;margin-top:2px">⚙️</span>
    <div style="font-size:13px">
      <strong style="color:#92400e">Twilio não configurado.</strong>
      <p style="color:#b45309;margin:4px 0">Adicione as seguintes variáveis ao <code style="background:#fef3c7;padding:1px 4px;border-radius:3px">.env</code> para habilitar o envio:</p>
      <pre style="background:#fef3c7;border-radius:4px;padding:8px;font-size:11px;font-family:monospace;color:#78350f;margin:4px 0">TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxx
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
TWILIO_SMS_FROM=+5511999999999
TWILIO_CANAL_PADRAO=whatsapp</pre>
      <p style="color:#b45309;font-size:12px;margin:4px 0 0">Para o sandbox do WhatsApp, o destinatário precisa enviar "join &lt;palavra&gt;" para o número sandbox antes de receber mensagens.</p>
    </div>
  </div>
  @else
  <div class="alert-success" style="margin-bottom:16px">
    ✅ <strong>Twilio configurado.</strong> Canal padrão: <code style="background:#dcfce7;padding:1px 4px;border-radius:3px">{{ config('services.twilio.canal_padrao', 'whatsapp') }}</code>
  </div>
  @endif

  {{-- KPIs --}}
  <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px">
    <div class="stat-card" style="border-left-color:#64748b">
      <div class="stat-icon">📊</div>
      <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Total</div>
      <div style="font-size:22px;font-weight:800;color:#64748b;margin-top:4px">{{ $stats->total ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#16a34a">
      <div class="stat-icon">✅</div>
      <div style="font-size:10px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.5px">Enviados</div>
      <div style="font-size:22px;font-weight:800;color:#166534;margin-top:4px">{{ $stats->enviados ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#dc2626">
      <div class="stat-icon">❌</div>
      <div style="font-size:10px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.5px">Falhas</div>
      <div style="font-size:22px;font-weight:800;color:#991b1b;margin-top:4px">{{ $stats->falhas ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#7c3aed">
      <div class="stat-icon">💬</div>
      <div style="font-size:10px;font-weight:700;color:#6d28d9;text-transform:uppercase;letter-spacing:.5px">WhatsApp</div>
      <div style="font-size:22px;font-weight:800;color:#6d28d9;margin-top:4px">{{ $stats->whatsapp ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#2563eb">
      <div class="stat-icon">📱</div>
      <div style="font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.5px">SMS</div>
      <div style="font-size:22px;font-weight:800;color:#1d4ed8;margin-top:4px">{{ $stats->sms ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon">📅</div>
      <div style="font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px">Hoje</div>
      <div style="font-size:22px;font-weight:800;color:#92400e;margin-top:4px">{{ $stats->hoje ?? 0 }}</div>
    </div>
  </div>

  {{-- Info agendamento --}}
  <div class="card" style="background:#eff6ff;border-color:#bfdbfe;margin-bottom:16px">
    <div style="font-weight:700;font-size:13px;color:#1e40af;margin-bottom:8px">📅 Agendamento automático (cron):</div>
    <ul style="font-size:12px;font-family:monospace;color:#1e3a8a;display:flex;flex-direction:column;gap:3px;margin-bottom:10px">
      <li>• <strong>07:15</strong> — Prazos (1, 3, 7 dias de antecedência)</li>
      <li>• <strong>07:15</strong> — Audiências do dia seguinte</li>
      <li>• <strong>08:00</strong> — Cobranças (parcelas com 3, 7 ou 15 dias de atraso)</li>
    </ul>
    <div style="font-size:12px;color:#1e40af">
      Executar manualmente: <code style="background:#dbeafe;padding:1px 5px;border-radius:3px">php artisan notificacoes:whatsapp</code>
      &nbsp;|&nbsp; Dry-run: <code style="background:#dbeafe;padding:1px 5px;border-radius:3px">php artisan notificacoes:whatsapp --dry-run</code>
    </div>
  </div>

  {{-- Filtros + Novo --}}
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <select wire:model.live="filtroStatus" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os status</option>
        <option value="enviado">Enviado</option>
        <option value="falha">Falha</option>
      </select>
      <select wire:model.live="filtroCanal" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os canais</option>
        <option value="whatsapp">WhatsApp</option>
        <option value="sms">SMS</option>
      </select>
      <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os tipos</option>
        <option value="prazo_fatal">Prazo Fatal</option>
        <option value="prazo_vencendo">Prazo Vencendo</option>
        <option value="prazo_vencido">Prazo Vencido</option>
        <option value="cobranca">Cobrança</option>
        <option value="audiencia">Audiência</option>
        <option value="teste">Teste</option>
      </select>
      <span style="margin-left:auto;font-size:13px;color:var(--muted)">{{ $total }} registro(s)</span>
      <button wire:click="abrirTeste" class="btn btn-success btn-sm">📲 Enviar Teste</button>
    </div>
  </div>

  {{-- Log --}}
  <div class="card" style="padding:0;overflow:hidden">
    @if($logs->isEmpty())
      <div style="padding:40px;text-align:center;color:var(--muted)">Nenhum registro encontrado.</div>
    @else
      <div class="table-wrap" style="margin:0">
        <table>
          <thead>
            <tr>
              <th>Data</th>
              <th>Canal</th>
              <th>Tipo</th>
              <th>Destinatário</th>
              <th>Mensagem</th>
              <th style="text-align:center">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($logs as $log)
              <tr>
                <td style="white-space:nowrap;font-size:12px;color:var(--muted)">
                  {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                </td>
                <td>
                  @if($log->canal === 'whatsapp')
                    <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #86efac;font-size:11px">💬 WhatsApp</span>
                  @else
                    <span class="badge" style="background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd;font-size:11px">📱 SMS</span>
                  @endif
                </td>
                <td style="font-size:12px;color:var(--muted)">
                  {{ str_replace('_', ' ', ucfirst($log->tipo)) }}
                </td>
                <td>
                  <div style="font-weight:600;color:var(--text);font-size:13px">{{ $log->destinatario_nome }}</div>
                  <div style="font-size:11px;color:var(--muted);font-family:monospace">{{ $log->destinatario_telefone }}</div>
                </td>
                <td style="max-width:250px">
                  <div style="font-size:12px;color:var(--muted);overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:pre-wrap">{{ $log->mensagem }}</div>
                  @if($log->erro)
                    <div style="font-size:11px;color:#dc2626;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $log->erro }}">
                      Erro: {{ $log->erro }}
                    </div>
                  @endif
                </td>
                <td style="text-align:center">
                  @if($log->status === 'enviado')
                    <span class="badge" style="background:#dcfce7;color:#166534;font-size:11px">✓ Enviado</span>
                  @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:11px">✗ Falha</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($logs->hasPages())
        <div class="pagination">{{ $logs->links() }}</div>
      @endif
    @endif
  </div>


  {{-- ─── Modal Teste ─────────────────────────────────────── --}}
  @if($modalTeste)
  <div class="modal-backdrop" wire:click.self="$set('modalTeste', false)">
    <div class="modal" style="width:460px">
      <div class="modal-header">
        <span class="modal-title">📲 Enviar Mensagem de Teste</span>
        <button wire:click="$set('modalTeste', false)" class="modal-close">×</button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
        @if($testeSucesso)
          <div class="alert-success">✅ {{ $testeSucesso }}</div>
        @endif
        @if($testeErro)
          <div class="alert-error">⚠️ {{ $testeErro }}</div>
        @endif

        <div class="form-field">
          <label class="lbl">Canal</label>
          <div style="display:flex;gap:8px;margin-top:4px">
            <label style="display:flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid {{ $testeCanal === 'whatsapp' ? '#16a34a' : 'var(--border)' }};
                          border-radius:6px;cursor:pointer;font-size:13px;
                          background:{{ $testeCanal === 'whatsapp' ? '#16a34a' : '#fff' }};
                          color:{{ $testeCanal === 'whatsapp' ? '#fff' : 'var(--text)' }}">
              <input type="radio" wire:model.live="testeCanal" value="whatsapp" style="display:none">
              💬 WhatsApp
            </label>
            <label style="display:flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid {{ $testeCanal === 'sms' ? '#2563eb' : 'var(--border)' }};
                          border-radius:6px;cursor:pointer;font-size:13px;
                          background:{{ $testeCanal === 'sms' ? '#2563eb' : '#fff' }};
                          color:{{ $testeCanal === 'sms' ? '#fff' : 'var(--text)' }}">
              <input type="radio" wire:model.live="testeCanal" value="sms" style="display:none">
              📱 SMS
            </label>
          </div>
        </div>

        <div class="form-field">
          <label class="lbl">Telefone * <span style="color:var(--muted);font-weight:400">(formato: +5511999999999 ou 11999999999)</span></label>
          <input wire:model="testeTel" type="text" placeholder="+5511999999999">
          @error('testeTel') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
          <label class="lbl">Mensagem *</label>
          <textarea wire:model="testeMsg" rows="4"
                    style="resize:none;font-family:monospace;font-size:12px"></textarea>
          @error('testeMsg') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="modal-footer">
        <button wire:click="$set('modalTeste', false)" class="btn btn-outline">Fechar</button>
        <button wire:click="enviarTeste" wire:loading.attr="disabled" wire:target="enviarTeste"
                class="btn btn-success">
          <span wire:loading.remove wire:target="enviarTeste">Enviar</span>
          <span wire:loading wire:target="enviarTeste">Enviando...</span>
        </button>
      </div>
    </div>
  </div>
  @endif

</div>
