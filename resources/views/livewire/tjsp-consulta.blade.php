<div @if($verificacao?->emAndamento()) wire:poll.2000ms @endif>

{{-- ── Cabeçalho ── --}}
<div style="margin-bottom:20px;">
    <a href="{{ route('ferramentas.hub') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:8px;"
       onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar
    </a>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;margin:0;">
                <svg aria-hidden="true" width="20" height="20" fill="none" stroke="#2563a8" stroke-width="1.5" viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
                Consulta Judicial
            </h2>
            <p style="font-size:13px;color:var(--muted);margin-top:4px;">Verifique novos andamentos via DATAJUD/CNJ — tribunal detectado automaticamente pelo número do processo</p>
        </div>
    </div>
</div>

{{-- ── Analista IA ── --}}
<div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:12px;padding:14px 20px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
        </svg>
        <input wire:model="perguntaIA" wire:keydown.enter="perguntarIA" type="text"
            placeholder="Pergunte sobre as consultas... Ex: 'Quantos processos nunca foram consultados?'"
            style="flex:1;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:9px 16px;color:#fff;font-size:13px;outline:none;">
        <button wire:click="perguntarIA" wire:loading.attr="disabled"
            style="background:#2563a8;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;">
            <svg wire:loading.remove wire:target="perguntarIA" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            <svg wire:loading wire:target="perguntarIA" style="animation:spin .7s linear infinite;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            <span wire:loading.remove wire:target="perguntarIA">✨ Analisar</span>
            <span wire:loading wire:target="perguntarIA">Analisando...</span>
        </button>
    </div>
    @if($respostaIA)
    <div style="margin-top:12px;background:rgba(255,255,255,.08);border-radius:8px;padding:12px 16px;font-size:13px;color:#e2e8f0;line-height:1.6;display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
        <span>{{ $respostaIA }}</span>
        <button wire:click="limparIA" style="background:none;border:none;cursor:pointer;color:#93c5fd;flex-shrink:0;padding:0;display:flex;align-items:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    @endif
</div>

{{-- ── Layout principal ── --}}
<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;">

    {{-- ── Coluna esquerda — Filtros ── --}}
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;position:sticky;top:20px;">

        {{-- Busca cliente --}}
        <div style="margin-bottom:16px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Busca</div>
            <div style="position:relative;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input wire:model.live.debounce.300ms="filtroCliente" type="text"
                    placeholder="Buscar cliente..."
                    style="width:100%;padding:9px 12px 9px 32px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;">
            </div>
        </div>

        {{-- Número do Processo --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Número do Processo</div>
            <div style="position:relative;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input wire:model.live.debounce.300ms="filtroNumero" type="text"
                    placeholder="Ex: 0001234-56.2023..."
                    style="width:100%;padding:8px 10px 8px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--white);color:var(--text);box-sizing:border-box;">
            </div>
            @if($filtroNumero && $totalFiltrado === 1)
            <div style="margin-top:6px;padding:6px 10px;background:#f0fdf4;border:1px solid #86efac;border-radius:6px;font-size:11px;color:#16a34a;font-weight:600;">
                ✓ 1 processo encontrado
            </div>
            @elseif($filtroNumero && $totalFiltrado === 0)
            <div style="margin-top:6px;padding:6px 10px;background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;font-size:11px;color:#dc2626;font-weight:600;">
                ✗ Nenhum processo encontrado
            </div>
            @endif
        </div>

        {{-- Status --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Status</div>
            <div style="display:flex;flex-direction:column;gap:5px;">
                @php
                $statusOpts = [
                    ['val'=>'Ativo',    'label'=>'Apenas Ativos',  'dot'=>'#16a34a'],
                    ['val'=>'',         'label'=>'Todos',           'dot'=>'#64748b'],
                    ['val'=>'Arquivado','label'=>'Arquivados',      'dot'=>'#dc2626'],
                ];
                @endphp
                @foreach($statusOpts as $opt)
                <button wire:click="$set('filtroStatus', '{{ $opt['val'] }}')"
                    style="display:flex;align-items:center;gap:8px;width:100%;padding:7px 10px;border-radius:8px;font-size:13px;border:none;cursor:pointer;text-align:left;
                           background:{{ $filtroStatus === $opt['val'] ? '#eff6ff' : 'transparent' }};
                           font-weight:{{ $filtroStatus === $opt['val'] ? '600' : '400' }};
                           color:{{ $filtroStatus === $opt['val'] ? 'var(--primary)' : 'var(--text)' }};">
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $opt['dot'] }};flex-shrink:0;"></span>
                    {{ $opt['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Fase --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Fase</div>
            <select wire:model.live="filtroFase"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;">
                <option value="">Todas as fases</option>
                @foreach($fases as $fase)
                <option value="{{ $fase->descricao }}">{{ $fase->descricao }}</option>
                @endforeach
            </select>
        </div>

        {{-- Advogado --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Advogado</div>
            <select wire:model.live="filtroAdvogado"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;">
                <option value="">Todos os advogados</option>
                @foreach($advogados as $adv)
                <option value="{{ $adv->nome }}">{{ $adv->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Última Consulta --}}
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Última Consulta</div>
            <div style="display:flex;flex-direction:column;gap:5px;">
                @php
                $consultaOpts = [
                    ['val'=>'',       'label'=>'Todos'],
                    ['val'=>'nunca',  'label'=>'Nunca consultados'],
                    ['val'=>'semana', 'label'=>'Não consultados esta semana'],
                    ['val'=>'mes',    'label'=>'Não consultados este mês'],
                ];
                @endphp
                @foreach($consultaOpts as $opt)
                <button wire:click="$set('filtroConsulta', '{{ $opt['val'] }}')"
                    style="display:flex;align-items:center;gap:8px;width:100%;padding:7px 10px;border-radius:8px;font-size:13px;border:none;cursor:pointer;text-align:left;
                           background:{{ $filtroConsulta === $opt['val'] ? '#eff6ff' : 'transparent' }};
                           font-weight:{{ $filtroConsulta === $opt['val'] ? '600' : '400' }};
                           color:{{ $filtroConsulta === $opt['val'] ? 'var(--primary)' : 'var(--text)' }};">
                    {{ $opt['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Período da Última Consulta --}}
        <div style="border-top:1px solid var(--border);padding-top:14px;margin-top:14px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Período da Última Consulta</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div>
                    <label style="font-size:11px;color:var(--muted);margin-bottom:3px;display:block;">De</label>
                    <input wire:model.live="filtroDataIni" type="date"
                        style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--white);color:var(--text);box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:11px;color:var(--muted);margin-bottom:3px;display:block;">Até</label>
                    <input wire:model.live="filtroDataFim" type="date"
                        style="width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--white);color:var(--text);box-sizing:border-box;">
                </div>
            </div>
        </div>

        {{-- Contador de selecionados --}}
        <div style="background:var(--primary);color:#fff;border-radius:8px;padding:12px;text-align:center;margin-bottom:12px;">
            <div style="font-size:28px;font-weight:800;line-height:1;">{{ $totalFiltrado }}</div>
            <div style="font-size:12px;opacity:.8;margin-top:4px;">processos selecionados</div>
        </div>

        {{-- Botão Verificar --}}
        @php $bloqueado = $consultando || $verificacao?->emAndamento() || $totalFiltrado === 0; @endphp
        <button wire:click="iniciarVerificacao"
            wire:loading.attr="disabled" wire:target="iniciarVerificacao"
            @if($bloqueado) disabled @endif
            style="width:100%;padding:12px;background:{{ $bloqueado ? '#94a3b8' : 'linear-gradient(135deg,#16a34a,#15803d)' }};color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:{{ $bloqueado ? 'not-allowed' : 'pointer' }};display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:8px;">
            <span wire:loading.remove wire:target="iniciarVerificacao" style="display:inline-flex;align-items:center;gap:8px;">
                @if($verificacao?->emAndamento())
                    <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Verificando...
                @else
                    <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                    Verificar Atualizações
                @endif
            </span>
            <span wire:loading wire:target="iniciarVerificacao" style="display:inline-flex;align-items:center;gap:8px;">
                <svg style="animation:spin .7s linear infinite;" width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Iniciando...
            </span>
        </button>

        {{-- Limpar --}}
        <button wire:click="limparFiltros"
            style="width:100%;padding:8px;background:transparent;border:1.5px solid var(--border);border-radius:8px;font-size:12px;color:var(--muted);cursor:pointer;font-weight:500;">
            Limpar filtros
        </button>

    </div>
    {{-- /filtros --}}

    {{-- ── Coluna direita ── --}}
    <div>

        {{-- Cards de métricas --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
            {{-- Total Ativos --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #2563a8;">
                <div style="width:40px;height:40px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="1.5"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['total_ativos'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">processos monitorados</div>
                </div>
            </div>
            {{-- Nunca Consultados --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #d97706;">
                <div style="width:40px;height:40px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['nunca_consultados'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">aguardando 1ª consulta</div>
                </div>
            </div>
            {{-- Consultados Hoje --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #16a34a;">
                <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['consultados_hoje'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">atualizados hoje</div>
                </div>
            </div>
            {{-- Novos Andamentos --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;border-left:4px solid #7c3aed;">
                <div style="width:40px;height:40px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1;">{{ $metricas['novos_andamentos'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">andamentos encontrados hoje</div>
                </div>
            </div>
        </div>

        {{-- Barra de progresso + feed ao vivo --}}
        @if($verificacao?->emAndamento())
        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <span style="font-size:14px;font-weight:700;color:var(--primary);display:inline-flex;align-items:center;gap:8px;">
                    <svg aria-hidden="true" width="16" height="16" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                    Consultando processos no DATAJUD/CNJ...
                </span>
                <span style="font-size:16px;font-weight:800;color:var(--primary);">{{ $verificacao->porcentagem() }}%</span>
            </div>
            <div style="background:var(--border);border-radius:99px;height:10px;overflow:hidden;margin-bottom:10px;">
                <div style="background:linear-gradient(90deg,var(--primary),#16a34a);height:10px;border-radius:99px;transition:width .5s ease;width:{{ $verificacao->porcentagem() }}%;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted);margin-bottom:12px;">
                <span>{{ $verificacao->processado }} / {{ $verificacao->total }} processos</span>
                @if($verificacao->novos_total > 0)
                <span style="color:var(--success);font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ $verificacao->novos_total }} andamento(s) novo(s) encontrado(s)
                </span>
                @endif
            </div>
            @if(!empty($verificacao->log_linhas))
            @php
                $linhas = array_reverse(array_slice($verificacao->log_linhas, -15));
                $icones = [
                    'consultando' => '<svg aria-hidden="true" width="12" height="12" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
                    'ok'          => '<svg aria-hidden="true" width="12" height="12" fill="none" stroke="var(--success)" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
                    'sem_novos'   => '<svg aria-hidden="true" width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
                    'erro'        => '<svg aria-hidden="true" width="12" height="12" fill="none" stroke="var(--danger)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
                    'ignorado'    => '<svg aria-hidden="true" width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
                ];
                $cores = ['consultando'=>'var(--primary)','ok'=>'var(--success)','sem_novos'=>'var(--muted)','erro'=>'var(--danger)','ignorado'=>'var(--muted)'];
            @endphp
            <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;">
                <div style="padding:8px 12px;background:var(--bg);border-bottom:1px solid var(--border);font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">
                    Log em tempo real
                </div>
                <div style="max-height:220px;overflow-y:auto;font-family:monospace;font-size:12px;">
                    @foreach($linhas as $linha)
                    <div style="display:flex;align-items:baseline;gap:8px;padding:5px 12px;border-bottom:1px solid #f8fafc;{{ $loop->first ? 'background:#f0fdf4;' : '' }}">
                        <span style="color:var(--muted);flex-shrink:0;font-size:10px;">{{ $linha['ts'] }}</span>
                        <span style="flex-shrink:0;display:inline-flex;align-items:center;">{!! $icones[$linha['tipo']] ?? '•' !!}</span>
                        <span style="color:var(--text);font-weight:600;flex-shrink:0;min-width:220px;">{{ $linha['numero'] }}</span>
                        @if($linha['tribunal'])
                        <span style="background:#e0f2fe;color:#0369a1;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;flex-shrink:0;">{{ $linha['tribunal'] }}</span>
                        @endif
                        <span style="color:{{ $cores[$linha['tipo']] ?? 'var(--muted)' }};">{{ $linha['msg'] }}</span>
                        @if(($linha['novos'] ?? 0) > 0)
                        <span style="background:var(--success);color:#fff;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;margin-left:auto;flex-shrink:0;">+{{ $linha['novos'] }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Resultado concluído --}}
        @if($verificacao?->status === 'concluido')
        <div style="margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#94a3b8;margin-bottom:16px;">
                <span>Verificação concluída em {{ $verificacao->concluido_em?->format('d/m/Y H:i') }}</span>
                <span>{{ $verificacao->total }} processos em {{ $verificacao->iniciado_em->diffForHumans($verificacao->concluido_em, true) }}</span>
            </div>

            @if(count($verificacao->novos_andamentos ?? []) > 0)
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <strong style="color:#16a34a;font-size:15px;display:inline-flex;align-items:center;gap:6px;">
                    <svg aria-hidden="true" width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ count($verificacao->novos_andamentos) }} processo(s) com andamentos novos!
                </strong>
                @if(($verificacao->prazos_criados ?? 0) > 0)
                <a href="{{ route('prazos') }}"
                   style="display:inline-flex;align-items:center;gap:6px;background:#1a3a5c;color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;">
                    <svg aria-hidden="true" width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $verificacao->prazos_criados }} prazo(s) criado(s) automaticamente →
                </a>
                @endif
            </div>

            @foreach($verificacao->novos_andamentos as $item)
            <div class="card" style="margin-bottom:16px;overflow:hidden;border-left:4px solid #16a34a;padding:0;">
                <div style="padding:14px 20px;background:#f0fdf4;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <span style="font-size:14px;font-weight:700;color:var(--primary);">{{ $item['numero'] }}</span>
                        <span style="font-size:13px;color:#64748b;margin-left:12px;">{{ $item['cliente'] }}</span>
                        @if(!empty($item['tribunal']))
                        <span style="background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;margin-left:8px;">{{ $item['tribunal'] }}</span>
                        @endif
                    </div>
                    <span style="background:#16a34a;color:white;padding:3px 10px;border-radius:99px;font-size:12px;font-weight:600;">
                        {{ count($item['andamentos']) }} novo(s)
                    </span>
                </div>
                <div style="padding:12px 20px;">
                    @foreach($item['andamentos'] as $a)
                    <div style="display:flex;gap:16px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                        <span style="color:#16a34a;font-weight:600;min-width:90px;">{{ \Carbon\Carbon::parse($a['data'])->format('d/m/Y') }}</span>
                        <span>{{ $a['descricao'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            @else
            <div class="card" style="text-align:center;padding:60px 24px;">
                <div style="margin-bottom:12px;display:flex;justify-content:center;">
                    <svg aria-hidden="true" width="48" height="48" fill="none" stroke="var(--success)" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <p style="font-size:16px;font-weight:600;color:var(--primary);">Nenhum andamento novo encontrado!</p>
                <p style="font-size:13px;color:#64748b;margin-top:4px;">Todos os processos selecionados estão atualizados.</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Estado inicial / erro --}}
        @if(!$verificacao || $verificacao->status === 'erro')
        <div class="card" style="text-align:center;padding:60px 24px;">
            <div style="margin-bottom:16px;display:flex;justify-content:center;">
                <svg aria-hidden="true" width="48" height="48" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
            </div>
            <p style="font-size:16px;font-weight:600;color:var(--primary);">Selecione os filtros e clique em "Verificar Atualizações"</p>
            <p style="font-size:13px;color:#64748b;margin-top:6px;">
                Use o painel lateral para filtrar quais processos consultar<br>
                e o sistema mostrará apenas os que tiveram novos andamentos.
            </p>
        </div>
        @endif

    </div>
    {{-- /coluna direita --}}

</div>
{{-- /grid --}}

@push('styles')
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@media (max-width: 768px) {
    .tjsp-grid { grid-template-columns: 1fr !important; }
}
</style>
@endpush

</div>
