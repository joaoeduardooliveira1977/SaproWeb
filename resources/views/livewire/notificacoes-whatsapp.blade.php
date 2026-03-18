<div>

  {{-- Status configuração --}}
  @if(!$svc->configurado())
  <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;margin-bottom:16px">
    <span style="display:inline-flex;margin-top:2px;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14M12 2v2m0 16v2m-8-8H2m20 0h-2"/></svg></span>
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
    <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <strong>Twilio configurado.</strong></span> Canal padrão: <code style="background:#dcfce7;padding:1px 4px;border-radius:3px">{{ config('services.twilio.canal_padrao', 'whatsapp') }}</code>
  </div>
  @endif

  {{-- KPIs --}}
  <div class="stat-grid">
    <div class="stat-card" style="border-left-color:#64748b">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
      <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Total</div>
      <div style="font-size:22px;font-weight:800;color:#64748b;margin-top:4px">{{ $stats->total ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#16a34a">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
      <div style="font-size:10px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.5px">Enviados</div>
      <div style="font-size:22px;font-weight:800;color:#166534;margin-top:4px">{{ $stats->enviados ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#dc2626">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>
      <div style="font-size:10px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.5px">Falhas</div>
      <div style="font-size:22px;font-weight:800;color:#991b1b;margin-top:4px">{{ $stats->falhas ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#7c3aed">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6d28d9" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
      <div style="font-size:10px;font-weight:700;color:#6d28d9;text-transform:uppercase;letter-spacing:.5px">WhatsApp</div>
      <div style="font-size:22px;font-weight:800;color:#6d28d9;margin-top:4px">{{ $stats->whatsapp ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#2563eb">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></div>
      <div style="font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.5px">SMS</div>
      <div style="font-size:22px;font-weight:800;color:#1d4ed8;margin-top:4px">{{ $stats->sms ?? 0 }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#d97706">
      <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
      <div style="font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px">Hoje</div>
      <div style="font-size:22px;font-weight:800;color:#92400e;margin-top:4px">{{ $stats->hoje ?? 0 }}</div>
    </div>
  </div>

  {{-- Info agendamento --}}
  <div class="card" style="background:#eff6ff;border-color:#bfdbfe;margin-bottom:16px">
    <div style="font-weight:700;font-size:13px;color:#1e40af;margin-bottom:8px;display:flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Agendamento automático (cron):</div>
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

  {{-- Abas --}}
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:16px;">
    <button wire:click="$set('aba','log')"
      style="padding:9px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:flex;align-items:center;gap:6px;
             border-bottom:2px solid {{ $aba==='log' ? 'var(--primary)' : 'transparent' }};
             color:{{ $aba==='log' ? 'var(--primary)' : 'var(--muted)' }};margin-bottom:-2px;">
      <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Log de envios
    </button>
    <button wire:click="$set('aba','templates')"
      style="padding:9px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:flex;align-items:center;gap:6px;
             border-bottom:2px solid {{ $aba==='templates' ? 'var(--primary)' : 'transparent' }};
             color:{{ $aba==='templates' ? 'var(--primary)' : 'var(--muted)' }};margin-bottom:-2px;">
      <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
      Templates
      @if($templates->count())
        <span style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;">{{ $templates->count() }}</span>
      @endif
    </button>
    <button wire:click="$set('aba','config')"
      style="padding:9px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:flex;align-items:center;gap:6px;
             border-bottom:2px solid {{ $aba==='config' ? 'var(--primary)' : 'transparent' }};
             color:{{ $aba==='config' ? 'var(--primary)' : 'var(--muted)' }};margin-bottom:-2px;">
      <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 0 0 4.93 19.07M19.07 4.93A10 10 0 1 1 4.93 19.07"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
      Configurações
    </button>
    <div style="flex:1"></div>
    <button wire:click="abrirTeste" class="btn btn-success btn-sm" style="align-self:center;margin-right:0;display:inline-flex;align-items:center;gap:6px;">
      <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg> Enviar Teste
    </button>
  </div>

  {{-- ── ABA: LOG ──────────────────────────────────────── --}}
  @if($aba === 'log')
  {{-- Filtros --}}
  <div class="card" style="margin-bottom:16px">
    <div class="filter-bar">
      <select wire:model.live="filtroStatus">
        <option value="">Todos os status</option>
        <option value="enviado">Enviado</option>
        <option value="falha">Falha</option>
      </select>
      <select wire:model.live="filtroCanal">
        <option value="">Todos os canais</option>
        <option value="whatsapp">WhatsApp</option>
        <option value="sms">SMS</option>
      </select>
      <select wire:model.live="filtroTipo">
        <option value="">Todos os tipos</option>
        <option value="prazo_fatal">Prazo Fatal</option>
        <option value="prazo_vencendo">Prazo Vencendo</option>
        <option value="prazo_vencido">Prazo Vencido</option>
        <option value="cobranca">Cobrança</option>
        <option value="audiencia">Audiência</option>
        <option value="teste">Teste</option>
      </select>
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
              <th class="hide-sm">Tipo</th>
              <th>Destinatário</th>
              <th class="hide-sm">Mensagem</th>
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
                    <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #86efac;font-size:11px;display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> WhatsApp</span>
                  @else
                    <span class="badge" style="background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd;font-size:11px;display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg> SMS</span>
                  @endif
                </td>
                <td class="hide-sm" style="font-size:12px;color:var(--muted)">
                  {{ str_replace('_', ' ', ucfirst($log->tipo)) }}
                </td>
                <td>
                  <div style="font-weight:600;color:var(--text);font-size:13px">{{ $log->destinatario_nome }}</div>
                  <div style="font-size:11px;color:var(--muted);font-family:monospace">{{ $log->destinatario_telefone }}</div>
                </td>
                <td class="hide-sm" style="max-width:250px">
                  <div style="font-size:12px;color:var(--muted);overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:pre-wrap">{{ $log->mensagem }}</div>
                  @if($log->erro)
                    <div style="font-size:11px;color:#dc2626;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $log->erro }}">
                      Erro: {{ $log->erro }}
                    </div>
                  @endif
                </td>
                <td style="text-align:center">
                  @if($log->status === 'enviado')
                    <span class="badge" style="background:#dcfce7;color:#166534;font-size:11px;display:inline-flex;align-items:center;gap:3px;"><svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Enviado</span>
                  @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:11px;display:inline-flex;align-items:center;gap:3px;"><svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Falha</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="pagination-bar" style="padding:12px 16px;">{{ $logs->links() }}</div>
    @endif
  </div>
  @endif {{-- /aba log --}}

  {{-- ── ABA: TEMPLATES ────────────────────────────────────── --}}
  @if($aba === 'templates')
  <div class="card" style="padding:0;overflow:hidden">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <span style="display:flex;align-items:center;gap:6px;font-weight:600;">
        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        Templates de Mensagem
      </span>
      <button wire:click="abrirTemplate()" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:5px;">
        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Template
      </button>
    </div>

    @if($templates->isEmpty())
      <div style="text-align:center;padding:48px 24px;color:var(--muted);">
        <svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        <div style="font-size:14px;font-weight:500;">Nenhum template cadastrado.</div>
        <div style="font-size:12px;margin-top:4px;">Crie templates reutilizáveis para agilizar o envio de mensagens.</div>
      </div>
    @else
      <div style="display:flex;flex-direction:column;gap:0;">
        @foreach($templates as $tpl)
        <div style="display:flex;align-items:flex-start;gap:16px;padding:16px;border-bottom:1px solid var(--border);transition:background .1s;"
             onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
          {{-- Canal badge --}}
          <div style="flex-shrink:0;margin-top:2px;">
            @if($tpl->canal === 'whatsapp')
              <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #86efac;font-size:11px;display:inline-flex;align-items:center;gap:3px;">
                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> WhatsApp
              </span>
            @elseif($tpl->canal === 'sms')
              <span class="badge" style="background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd;font-size:11px;display:inline-flex;align-items:center;gap:3px;">
                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg> SMS
              </span>
            @else
              <span class="badge" style="background:#f3e8ff;color:#7c3aed;border:1px solid #c4b5fd;font-size:11px;">Ambos</span>
            @endif
          </div>

          {{-- Conteúdo --}}
          <div style="flex:1;min-width:0;">
            <div style="font-weight:600;font-size:13px;color:var(--text);margin-bottom:4px;">{{ $tpl->nome }}</div>
            <div style="font-size:12px;color:var(--muted);white-space:pre-wrap;overflow:hidden;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;">{{ $tpl->mensagem }}</div>
          </div>

          {{-- Ações --}}
          <div style="flex-shrink:0;display:flex;gap:4px;">
            <button wire:click="usarTemplate({{ $tpl->id }})" title="Usar agora"
              style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;border:none;background:#f0fdf4;color:#16a34a;font-size:11px;font-weight:600;cursor:pointer;">
              <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Usar
            </button>
            <button wire:click="abrirTemplate({{ $tpl->id }})" title="Editar"
              style="width:30px;height:30px;border:none;border-radius:6px;background:#eff6ff;color:#2563a8;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
              <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button wire:click="excluirTemplate({{ $tpl->id }})" wire:confirm="Remover este template?" title="Excluir"
              style="width:30px;height:30px;border:none;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
              <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
  @endif {{-- /aba templates --}}

  {{-- ════════════════════════════════════════════════════════ --}}
  {{-- ABA: CONFIGURAÇÕES                                       --}}
  {{-- ════════════════════════════════════════════════════════ --}}
  @if($aba === 'config')
  @php
    $diasOpcoes = [1 => '1 dia', 2 => '2 dias', 3 => '3 dias', 5 => '5 dias', 7 => '7 dias', 10 => '10 dias', 15 => '15 dias'];
    $tipoIcons  = ['prazo_fatal' => '🚨', 'prazo_vencendo' => '⏳', 'audiencia' => '📅', 'cobranca' => '💳'];
    $tipoDesc   = [
        'prazo_fatal'    => 'Notifica o responsável quando um prazo fatal está próximo.',
        'prazo_vencendo' => 'Notifica o responsável quando um prazo normal está próximo.',
        'audiencia'      => 'Notifica o advogado sobre audiências agendadas.',
        'cobranca'       => 'Notifica o cliente sobre parcelas de honorários em atraso.',
    ];
  @endphp

  <div style="display:flex;flex-direction:column;gap:16px;">
    @foreach($configs as $cfg)
    <div class="card" style="padding:20px;border-left:4px solid {{ $cfg->ativo ? 'var(--success)' : 'var(--border)' }};">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">

        {{-- Info --}}
        <div style="flex:1;min-width:200px;">
          <div style="font-size:15px;font-weight:700;margin-bottom:4px;">
            {{ $tipoIcons[$cfg->tipo] ?? '' }} {{ $cfg->label }}
          </div>
          <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">
            {{ $tipoDesc[$cfg->tipo] ?? '' }}
          </div>
          @if(($statsPorTipo[$cfg->tipo] ?? 0) > 0)
          <div style="font-size:11px;background:#dcfce7;color:#15803d;display:inline-block;padding:2px 8px;border-radius:8px;">
            {{ $statsPorTipo[$cfg->tipo] }} enviada(s) nas últimas 24h
          </div>
          @endif
        </div>

        {{-- Toggle ativo --}}
        <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
          <button wire:click="toggleAtivo('{{ $cfg->tipo }}')"
            style="width:48px;height:26px;border-radius:13px;border:none;cursor:pointer;position:relative;
              background:{{ $cfg->ativo ? 'var(--success)' : '#cbd5e1' }};transition:background .2s;">
            <span style="position:absolute;top:3px;width:20px;height:20px;border-radius:50%;background:#fff;transition:left .2s;
              left:{{ $cfg->ativo ? '25px' : '3px' }};box-shadow:0 1px 3px rgba(0,0,0,.3);"></span>
          </button>
          <span style="font-size:10px;color:var(--muted);">{{ $cfg->ativo ? 'Ativo' : 'Inativo' }}</span>
        </div>
      </div>

      @if($cfg->ativo)
      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start;">

        {{-- Antecedências --}}
        @if($cfg->tipo !== 'cobranca')
        <div>
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
            Avisar com antecedência
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($diasOpcoes as $dias => $label)
            @php $marcado = in_array($dias, $cfg->antecedencias); @endphp
            <button
              wire:click="salvarConfig('{{ $cfg->tipo }}', '{{ $cfg->canal }}', {{ json_encode($marcado ? array_values(array_diff($cfg->antecedencias, [$dias])) : array_values(array_unique(array_merge($cfg->antecedencias, [$dias])))) }})"
              style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;
                background:{{ $marcado ? 'var(--primary)' : 'var(--bg)' }};
                color:{{ $marcado ? '#fff' : 'var(--text)' }};
                border:1.5px solid {{ $marcado ? 'var(--primary)' : 'var(--border)' }};">
              {{ $label }}
            </button>
            @endforeach
          </div>
        </div>
        @else
        <div>
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
            Notificar quando em atraso há
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($diasOpcoes as $dias => $label)
            @php $marcado = in_array($dias, $cfg->antecedencias); @endphp
            <button
              wire:click="salvarConfig('{{ $cfg->tipo }}', '{{ $cfg->canal }}', {{ json_encode($marcado ? array_values(array_diff($cfg->antecedencias, [$dias])) : array_values(array_unique(array_merge($cfg->antecedencias, [$dias])))) }})"
              style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;
                background:{{ $marcado ? 'var(--primary)' : 'var(--bg)' }};
                color:{{ $marcado ? '#fff' : 'var(--text)' }};
                border:1.5px solid {{ $marcado ? 'var(--primary)' : 'var(--border)' }};">
              {{ $label }}
            </button>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Canal --}}
        <div>
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
            Canal de envio
          </div>
          <div style="display:flex;gap:6px;">
            @foreach(['whatsapp' => 'WhatsApp', 'sms' => 'SMS', 'ambos' => 'Ambos'] as $val => $label)
            <button
              wire:click="salvarConfig('{{ $cfg->tipo }}', '{{ $val }}', {{ json_encode($cfg->antecedencias) }})"
              style="padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;
                background:{{ $cfg->canal === $val ? 'var(--primary)' : 'var(--bg)' }};
                color:{{ $cfg->canal === $val ? '#fff' : 'var(--text)' }};
                border:1.5px solid {{ $cfg->canal === $val ? 'var(--primary)' : 'var(--border)' }};">
              {{ $label }}
            </button>
            @endforeach
          </div>
        </div>

      </div>
      @endif
    </div>
    @endforeach

    {{-- Info horários --}}
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:14px 16px;font-size:12px;color:#1e40af;">
      <strong>Horários de envio (agendados):</strong>
      Prazos e audiências — 07:15 · Cobranças — 08:00 · Dias úteis apenas.
      Para alterar os horários, edite <code>routes/console.php</code>.
    </div>
  </div>
  @endif {{-- /aba config --}}

  {{-- ─── Modal Template ──────────────────────────────────── --}}
  @if($modalTemplate)
  <div class="modal-backdrop" wire:click.self="fecharTemplate">
    <div class="modal" style="max-width:500px">
      <div class="modal-header">
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:6px;">
          <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          {{ $templateId ? 'Editar Template' : 'Novo Template' }}
        </span>
        <button wire:click="fecharTemplate" class="modal-close" aria-label="Fechar">×</button>
      </div>
      <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
        <div class="form-field">
          <label class="lbl">Nome do template *</label>
          <input wire:model="tplNome" type="text" placeholder="Ex: Lembrete de audiência" style="width:100%">
          @error('tplNome') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
          <label class="lbl">Canal</label>
          <select wire:model="tplCanal" style="width:100%">
            <option value="whatsapp">WhatsApp</option>
            <option value="sms">SMS</option>
            <option value="ambos">Ambos</option>
          </select>
        </div>
        <div class="form-field">
          <label class="lbl">Mensagem *</label>
          <textarea wire:model="tplMensagem" rows="6"
            style="width:100%;resize:vertical;font-family:monospace;font-size:12px;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);color:var(--text);"
            placeholder="Olá {{nome}}, sua audiência está marcada para {{data}}..."></textarea>
          <div style="font-size:11px;color:var(--muted);margin-top:4px;">Use <code>{{nome}}</code>, <code>{{processo}}</code>, <code>{{data}}</code> como variáveis.</div>
          @error('tplMensagem') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
      </div>
      <div class="modal-footer">
        <button wire:click="fecharTemplate" class="btn btn-outline">Cancelar</button>
        <button wire:click="salvarTemplate" class="btn btn-success" style="display:flex;align-items:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Salvar
        </button>
      </div>
    </div>
  </div>
  @endif

  {{-- ─── Modal Teste ─────────────────────────────────────── --}}
  @if($modalTeste)
  <div class="modal-backdrop" wire:click.self="$set('modalTeste', false)">
    <div class="modal" style="max-width:460px">
      <div class="modal-header">
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg> Enviar Mensagem de Teste</span>
        <button wire:click="$set('modalTeste', false)" class="modal-close" aria-label="Fechar">×</button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
        @if($testeSucesso)
          <div class="alert-success" style="display:flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> {{ $testeSucesso }}</div>
        @endif
        @if($testeErro)
          <div class="alert-error" style="display:flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> {{ $testeErro }}</div>
        @endif

        @if($templates->isNotEmpty())
        <div class="form-field">
          <label class="lbl">Usar template</label>
          <select onchange="if(this.value) $wire.usarTemplate(this.value)" style="width:100%">
            <option value="">— Selecionar template —</option>
            @foreach($templates as $tpl)
              <option value="{{ $tpl->id }}">{{ $tpl->nome }} ({{ $tpl->canal }})</option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="form-field">
          <label class="lbl">Canal</label>
          <div style="display:flex;gap:8px;margin-top:4px">
            <label style="display:flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid {{ $testeCanal === 'whatsapp' ? '#16a34a' : 'var(--border)' }};
                          border-radius:6px;cursor:pointer;font-size:13px;
                          background:{{ $testeCanal === 'whatsapp' ? '#16a34a' : '#fff' }};
                          color:{{ $testeCanal === 'whatsapp' ? '#fff' : 'var(--text)' }}">
              <input type="radio" wire:model.live="testeCanal" value="whatsapp" style="display:none">
              <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> WhatsApp
            </label>
            <label style="display:flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid {{ $testeCanal === 'sms' ? '#2563eb' : 'var(--border)' }};
                          border-radius:6px;cursor:pointer;font-size:13px;
                          background:{{ $testeCanal === 'sms' ? '#2563eb' : '#fff' }};
                          color:{{ $testeCanal === 'sms' ? '#fff' : 'var(--text)' }}">
              <input type="radio" wire:model.live="testeCanal" value="sms" style="display:none">
              <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg> SMS
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
