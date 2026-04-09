<div>

{{-- ══════════════════════════════════════════════════════════════
     POLLING condicional
══════════════════════════════════════════════════════════════ --}}
@if($aba === 'lote' && $temVerificando)
    <div wire:poll.3s></div>
@endif
<div wire:poll.60s style="display:none"></div>

{{-- ══════════════════════════════════════════════════════════════
     LAYOUT PRINCIPAL: grade 2 colunas
══════════════════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:0;align-items:flex-start;">

    {{-- ── Coluna principal ───────────────────────────────────── --}}
    <div style="min-width:0;padding-right:16px;">

        {{-- CABEÇALHO --}}
        <div style="margin-bottom:20px;">
            <a href="{{ route('processos') }}"
               style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--muted);text-decoration:none;margin-bottom:6px;opacity:.65;transition:opacity .15s,color .15s;"
               onmouseover="this.style.opacity='1';this.style.color='#059669'" onmouseout="this.style.opacity='.65';this.style.color='var(--muted)'">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Processos
            </a>
            <h2 style="font-size:21px;font-weight:700;color:#0f2540;margin:0 0 2px;">Monitoramento Inteligente</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">Acompanhamento em tempo real de andamentos e alertas</p>
        </div>

        {{-- STAT CARDS --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;">

            {{-- Card 1: Processos Críticos --}}
            <div wire:click="setFiltroFeed('critico')"
                 style="background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:12px;padding:14px 16px;cursor:pointer;
                        border:1.5px solid transparent;transition:border-color .15s,transform .15s;box-shadow:0 1px 4px rgba(0,0,0,.06);"
                 onmouseover="this.style.borderColor='#ef4444';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:10px;background:#ef4444;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;">{{ $totalCriticos }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Processos Críticos</div>
            </div>

            {{-- Card 2: Prazos / Notif não lidas --}}
            <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:12px;padding:14px 16px;cursor:default;
                        border:1.5px solid transparent;transition:border-color .15s,transform .15s;box-shadow:0 1px 4px rgba(0,0,0,.06);"
                 onmouseover="this.style.borderColor='#f59e0b';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:10px;background:#f59e0b;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;">{{ $notificacoesNaoLidas }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Prazos Críticos</div>
            </div>

           

 {{-- Card 3: Novos Andamentos --}}
            <div style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-radius:12px;padding:14px 16px;cursor:default;
                        border:1.5px solid transparent;transition:border-color .15s,transform .15s;box-shadow:0 1px 4px rgba(0,0,0,.06);"
                 onmouseover="this.style.borderColor='#10b981';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:10px;background:#10b981;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9,11 12,14 22,4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;">{{ $feedQuery->total() }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Novos Andamentos</div>
            </div>

            {{-- Card 4: Monitorados Ativos --}}
            <div wire:click="$set('aba','monitoramentos')"
                 style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);border-radius:12px;padding:14px 16px;cursor:pointer;
                        border:1.5px solid transparent;transition:border-color .15s,transform .15s;box-shadow:0 1px 4px rgba(0,0,0,.06);"
                 onmouseover="this.style.borderColor='#3b82f6';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:10px;background:#3b82f6;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;">{{ $monitorados->count() }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Monitorados Ativos</div>
            </div>

        </div>{{-- fim stat cards --}}

        {{-- TOOLBAR --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#64748b;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>Última atualização: {{ \Carbon\Carbon::parse($ultimaAtualizacao)->diffForHumans() }}</span>
            </div>
            <button wire:click="atualizarAgora" wire:loading.attr="disabled"
                    style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;
                           font-size:12px;font-weight:600;background:#059669;color:#fff;border:none;cursor:pointer;
                           transition:background .15s;"
                    onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                <svg wire:loading.remove wire:target="atualizarAgora" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                <svg wire:loading wire:target="atualizarAgora" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                <span wire:loading.remove wire:target="atualizarAgora">Atualizar Andamentos Agora</span>
                <span wire:loading wire:target="atualizarAgora">Atualizando...</span>
            </button>
        </div>

     

        {{-- ABAS --}}
        <div style="display:flex;align-items:center;border-bottom:2px solid #e2e8f0;margin-bottom:16px;">
            @foreach([
                'feed'           => 'Feed de Andamentos',
                'lote'           => 'Verificar em Lote',
                'monitoramentos' => 'Monitoramentos',
                'historico'      => 'Histórico',
            ] as $tab => $label)
                <button wire:click="$set('aba','{{ $tab }}')"
                        style="padding:9px 18px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;white-space:nowrap;
                               border-bottom:2.5px solid {{ $aba===$tab ? '#059669' : 'transparent' }};
                               color:{{ $aba===$tab ? '#059669' : '#64748b' }};margin-bottom:-2px;
                               transition:color .15s,border-color .15s;">
                    {{ $label }}
                </button>
            @endforeach
            

            <div style="flex:1;"></div>
            <button wire:click="toggleFiltros"
                style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;
                       font-size:12px;font-weight:600;cursor:pointer;
                       border:1.5px solid {{ $painelFiltros ? '#1D9E75' : '#e2e8f0' }};
                       background:{{ $painelFiltros ? '#EAF3DE' : '#fff' }};
                       color:{{ $painelFiltros ? '#059669' : '#64748b' }};">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/></svg>
                Outros Filtros
            </button>
        </div>

@if($painelFiltros)

<div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:16px 20px;margin-top:10px;">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:12px;">
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CLIENTE</label>
            <input wire:model.live.debounce.300ms="buscaFeed" placeholder="Buscar cliente..."
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NÚMERO DO PROCESSO</label>
            <input wire:model.live.debounce.300ms="filtroNumero" placeholder="Ex: 0001234-56.2023..."
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS</label>
            <select wire:model.live="filtroStatus"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;background:#fff;cursor:pointer;">
                <option value="Ativo">Apenas Ativos</option>
                <option value="">Todos</option>
                <option value="Encerrado">Encerrados</option>
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">ADVOGADO</label>
            <select wire:model.live="filtroAdvogado"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;background:#fff;cursor:pointer;">
                <option value="">Todos os advogados</option>
                @foreach($advogados as $adv)
                <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;align-items:end;">
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">FASE</label>
            <select wire:model.live="filtroFase"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;background:#fff;cursor:pointer;">
                <option value="">Todas as fases</option>
                @foreach($fases as $fase)
               <option value="{{ $fase->id }}">{{ $fase->descricao }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">PERÍODO — DE</label>
            <input wire:model.live="dataInicio" type="date"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;cursor:pointer;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">PERÍODO — ATÉ</label>
            <input wire:model.live="dataFim" type="date"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;cursor:pointer;">
        </div>
        <div>
            <button wire:click="limparFiltros"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#64748b;background:#f8fafc;cursor:pointer;">
                Limpar
            </button>
        </div>
    </div>
</div>
@endif


        {{-- ══════════════════════════════════════════════
             ABA 1 — FEED DE ANDAMENTOS
        ══════════════════════════════════════════════ --}}
        @if($aba === 'feed')

        {{-- Chips de filtro com contagem --}}
        @php
            $chipContagens = [
                'todos'   => ['label'=>'Todos',   'count'=>$feedQuery->total(), 'bg'=>'#e0f2fe','txt'=>'#0369a1','border'=>'#7dd3fc','dot'=>'#0369a1'],
                'critico' => ['label'=>'Crítico',  'count'=>$totalCriticos,     'bg'=>'#fee2e2','txt'=>'#991b1b','border'=>'#fca5a5','dot'=>'#ef4444'],
                'atencao' => ['label'=>'Atenção',  'count'=>$totalAtencao,      'bg'=>'#fef3c7','txt'=>'#92400e','border'=>'#fde68a','dot'=>'#f59e0b'],
                'normal'  => ['label'=>'Normal',   'count'=>$totalNormal,       'bg'=>'#d1fae5','txt'=>'#065f46','border'=>'#6ee7b7','dot'=>'#10b981'],
            ];
        @endphp
        <div style="display:flex;gap:7px;margin-bottom:16px;flex-wrap:wrap;">
            @foreach($chipContagens as $val => $cfg)
                <button wire:click="setFiltroFeed('{{ $val }}')"
                        style="display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:99px;
                               font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;
                               border:1.5px solid {{ $filtroPrazo===$val ? $cfg['border'] : '#e2e8f0' }};
                               background:{{ $filtroPrazo===$val ? $cfg['bg'] : 'transparent' }};
                               color:{{ $filtroPrazo===$val ? $cfg['txt'] : '#64748b' }};">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $cfg['dot'] }};flex-shrink:0;"></span>
                    {{ $cfg['label'] }}
                    <span style="font-size:11px;opacity:.75;">({{ $cfg['count'] }})</span>
                </button>
            @endforeach
        </div>

        {{-- Cards agrupados por data --}}
        @php
            $hoje       = \Carbon\Carbon::today();
            $ontem      = \Carbon\Carbon::yesterday();
            $grupoAtual = null;
            $mesesPt    = [
                '01'=>'janeiro','02'=>'fevereiro','03'=>'março','04'=>'abril',
                '05'=>'maio','06'=>'junho','07'=>'julho','08'=>'agosto',
                '09'=>'setembro','10'=>'outubro','11'=>'novembro','12'=>'dezembro',
            ];
        @endphp

        @forelse($feedQuery as $processo)
            @php
                $andamento   = $processo->andamentos->first();
                $dataAndamen = $andamento ? \Carbon\Carbon::parse($andamento->created_at) : null;
                $grupoData   = $dataAndamen ? $dataAndamen->toDateString() : null;

                // ── Detecção de tipo de andamento ──────────────────
                $tipoAndamento = 'outro';
                $descLower     = mb_strtolower($andamento->descricao ?? '');
                if (str_contains($descLower, 'sentença') || str_contains($descLower, 'acórdão')
                    || str_contains($descLower, 'decisão') || str_contains($descLower, 'conclusão')
                    || str_contains($descLower, 'despacho') || str_contains($descLower, 'julgamento')
                    || str_contains($descLower, 'recurso') || str_contains($descLower, 'transitou')) {
                    $tipoAndamento = 'sentenca';
                } elseif (str_contains($descLower, 'prazo') || str_contains($descLower, 'intimação')
                    || str_contains($descLower, 'citação') || str_contains($descLower, 'audiência')
                    || str_contains($descLower, 'notificação') || str_contains($descLower, 'mandado')
                    || str_contains($descLower, 'oficial') || str_contains($descLower, 'edital')) {
                    $tipoAndamento = 'prazo';
                } elseif (str_contains($descLower, 'petição') || str_contains($descLower, 'documento')
                    || str_contains($descLower, 'publicação') || str_contains($descLower, 'remessa')
                    || str_contains($descLower, 'juntada') || str_contains($descLower, 'distribuição')
                    || str_contains($descLower, 'baixa') || str_contains($descLower, 'protocolo')
                    || str_contains($descLower, 'carga') || str_contains($descLower, 'conclusos')) {
                    $tipoAndamento = 'peticao';
                }

                // ── Título e cores por tipo ────────────────────────
                $tipoConfig = [
                    'sentenca' => [
                        'titulo'  => 'Sentença Publicada',
                        'iconBg'  => '#d1fae5', 'iconTxt' => '#065f46',
                        'svg'     => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
                    ],
                    'prazo'    => [
                        'titulo'  => 'Prazo Iniciado',
                        'iconBg'  => '#fef3c7', 'iconTxt' => '#92400e',
                        'svg'     => '<circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>',
                    ],
                    'peticao'  => [
                        'titulo'  => 'Petição Juntada',
                        'iconBg'  => '#dbeafe', 'iconTxt' => '#1d4ed8',
                        'svg'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                    ],
                    'outro'    => [
                        'titulo'  => 'Novo Andamento',
                        'iconBg'  => '#f1f5f9', 'iconTxt' => '#475569',
                        'svg'     => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                    ],
                ];
                $tc = $tipoConfig[$tipoAndamento];

                // ── Score ──────────────────────────────────────────
                $scoreColors = [
                    'critico' => ['badgeBg'=>'#fee2e2','badgeTxt'=>'#991b1b','dot'=>'#ef4444','label'=>'Crítico','border'=>'#fca5a5'],
                    'atencao' => ['badgeBg'=>'#fef3c7','badgeTxt'=>'#92400e','dot'=>'#f59e0b','label'=>'Atenção','border'=>'#fde68a'],
                    'normal'  => ['badgeBg'=>'#d1fae5','badgeTxt'=>'#065f46','dot'=>'#10b981','label'=>'Normal', 'border'=>'#6ee7b7'],
                ];
                $sc = $scoreColors[$processo->score] ?? $scoreColors['normal'];
            @endphp

            {{-- Label de agrupamento por data --}}
            @if($grupoData !== $grupoAtual)
                @php $grupoAtual = $grupoData; @endphp
                @if($grupoData)
                    @php
                        $dl     = \Carbon\Carbon::parse($grupoData);
                        $diaNum = $dl->format('d');
                        $mes    = $mesesPt[$dl->format('m')] ?? $dl->format('M');
                        if ($dl->isToday()) {
                            $labelGrupo = 'Hoje, ' . $dl->format('H:i');
                        } elseif ($dl->isYesterday()) {
                            $labelGrupo = 'Ontem: ' . $diaNum . ' de ' . $mes;
                        } else {
                            $labelGrupo = 'Dia ' . $diaNum . ' de ' . $mes;
                        }
                    @endphp
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;
                                padding:10px 0 6px;margin-top:4px;">
                        {{ $labelGrupo }}
                    </div>
                @endif
            @endif

            {{-- Card do processo --}}
            <div wire:click="abrirProcesso({{ $processo->id }})"
                 style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:10px;cursor:pointer;
                        border:1.5px solid #f1f5f9;box-shadow:0 1px 3px rgba(0,0,0,.06);
                        transition:border-color .15s,transform .15s,box-shadow .15s;"
                 onmouseover="this.style.borderColor='#cbd5e1';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
                 onmouseout="this.style.borderColor='#f1f5f9';this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">

                <div style="display:flex;align-items:flex-start;gap:12px;">

                    {{-- Ícone 40x40 --}}
                    <div style="width:40px;height:40px;border-radius:12px;background:{{ $tc['iconBg'] }};
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" fill="none" stroke="{{ $tc['iconTxt'] }}" stroke-width="2" viewBox="0 0 24 24">
                            {!! $tc['svg'] !!}
                        </svg>
                    </div>

                    {{-- Conteúdo central --}}
                    <div style="flex:1;min-width:0;">
                        {{-- Linha 1: título + badge score --}}
                        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:3px;">
                            <span style="font-size:13px;font-weight:700;color:#0f2540;">{{ $tc['titulo'] }}</span>
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;
                                         font-size:10px;font-weight:700;background:{{ $sc['badgeBg'] }};color:{{ $sc['badgeTxt'] }};">
                                <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc['dot'] }};flex-shrink:0;"></span>
                                {{ $sc['label'] }}
                            </span>
                        </div>
                        {{-- Linha 2: número do processo --}}
                        <div style="font-size:11px;color:#64748b;margin-bottom:3px;">
                            Processo <span style="font-family:monospace;font-weight:600;color:#334155;">{{ $processo->numero }}</span>
                        </div>
                        {{-- Linha 3: descrição resumida --}}
                        @if($andamento && ($andamento->descricao ?? null))
                            <div style="font-size:12px;color:#475569;line-height:1.5;">
                                {{ Str::limit($andamento->descricao, 80) }}
                            </div>
                        @elseif($processo->resumo_ia)
                            <div style="font-size:12px;color:#475569;line-height:1.5;">{{ Str::limit($processo->resumo_ia, 80) }}</div>
                        @endif
                    </div>

                    {{-- Cliente + seta (canto direito) --}}
                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;padding-top:2px;">
                        <span style="font-size:11px;color:#64748b;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right;">
                            {{ $processo->cliente->nome ?? '—' }}
                        </span>
                        <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>

                </div>
            </div>

        @empty
            <div style="text-align:center;padding:56px 24px;color:#94a3b8;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 14px;display:block;opacity:.4;">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <p style="font-size:13px;margin:0;color:#64748b;">Nenhum andamento encontrado para o filtro selecionado.</p>
            </div>
        @endforelse

        <div style="margin-top:14px;">{{ $feedQuery->links() }}</div>

        @endif {{-- fim feed --}}


        {{-- ══════════════════════════════════════════════
             ABA 2 — VERIFICAR EM LOTE
        ══════════════════════════════════════════════ --}}
        @if($aba === 'lote')
        <div style="max-width:680px;">

            {{-- Área de drop --}}
            <div style="border:2px dashed #e2e8f0;border-radius:10px;padding:32px;text-align:center;
                        background:#f8fafc;margin-bottom:20px;">
                <svg width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;">
                    <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
                    <path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/>
                </svg>
                <p style="font-size:13px;color:#64748b;margin:0 0 10px;">Arraste planilha <strong>.xlsx</strong> ou <strong>.csv</strong></p>
                <input type="file" wire:model="fileLote" accept=".xlsx,.csv"
                       style="font-size:12px;color:#64748b;cursor:pointer;">
                <div wire:loading wire:target="fileLote" style="font-size:12px;color:#059669;margin-top:6px;">Carregando arquivo...</div>
            </div>

            {{-- Separador --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                <span style="font-size:12px;color:#64748b;white-space:nowrap;">ou cole os números abaixo</span>
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            </div>

            {{-- Textarea --}}
            <textarea wire:model="numerosBrutos"
                      style="width:100%;height:160px;border:1.5px solid #e2e8f0;border-radius:8px;
                             padding:10px 12px;font-family:monospace;font-size:12px;resize:vertical;
                             background:#fff;color:#1e293b;line-height:1.6;box-sizing:border-box;"
                      placeholder="0001234-56.2023.8.26.0001&#10;0002345-67.2023.8.26.0001&#10;..."></textarea>

            <div style="display:flex;align-items:center;gap:10px;margin-top:12px;">
                <button wire:click="verificarLote" wire:loading.attr="disabled"
                        style="display:inline-flex;align-items:center;gap:8px;padding:9px 20px;border-radius:8px;
                               font-size:13px;font-weight:600;background:#059669;color:#fff;border:none;cursor:pointer;
                               transition:background .15s;"
                        onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                    <span wire:loading.remove wire:target="verificarLote" style="display:contents;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                        </svg>
                        Verificar via DATAJUD
                    </span>
                    <span wire:loading wire:target="verificarLote" style="display:contents;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             style="animation:spin 1s linear infinite;">
                            <path d="M21 12a9 9 0 11-6.219-8.56"/>
                        </svg>
                        Verificando...
                    </span>
                </button>
                <span style="font-size:11px;color:#94a3b8;">Máx. 500 processos por vez</span>
            </div>

            {{-- Tabela da fila --}}
            @if($filaLote->count())
            <div style="margin-top:24px;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:8px;">
                    Fila de verificação
                    @if($temVerificando)
                        <span style="font-size:11px;color:#059669;font-weight:500;margin-left:6px;">
                            <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#059669;
                                         animation:pulse 1.5s infinite;margin-right:3px;"></span>
                            atualizando...
                        </span>
                    @endif
                </div>
                <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                                <th style="text-align:left;padding:9px 12px;font-weight:600;color:#64748b;">Número</th>
                                <th style="text-align:left;padding:9px 12px;font-weight:600;color:#64748b;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filaLote as $item)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:9px 12px;font-family:monospace;color:#1e293b;">{{ $item->processo_numero }}</td>
                                <td style="padding:9px 12px;">
                                    @php
                                        $badgeLote = [
                                            'aguardando'  => ['bg'=>'#f1f5f9','txt'=>'#64748b','label'=>'Aguardando'],
                                            'verificando' => ['bg'=>'#dbeafe','txt'=>'#1d4ed8','label'=>'Verificando'],
                                            'verificado'  => ['bg'=>'#d1fae5','txt'=>'#065f46','label'=>'Verificado'],
                                            'erro'        => ['bg'=>'#fee2e2','txt'=>'#991b1b','label'=>'Erro'],
                                        ];
                                        $bl = $badgeLote[$item->status] ?? $badgeLote['aguardando'];
                                    @endphp
                                    <span style="display:inline-block;padding:2px 9px;border-radius:99px;
                                                 background:{{ $bl['bg'] }};color:{{ $bl['txt'] }};font-weight:600;font-size:11px;">
                                        {{ $bl['label'] }}
                                    </span>
                                    @if($item->erro_mensagem)
                                        <span style="font-size:10px;color:#991b1b;margin-left:4px;" title="{{ $item->erro_mensagem }}">
                                            ⚠ {{ Str::limit($item->erro_mensagem, 40) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
        @endif {{-- fim lote --}}


        {{-- ══════════════════════════════════════════════
             ABA 3 — MONITORAMENTOS
        ══════════════════════════════════════════════ --}}
        @if($aba === 'monitoramentos')

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div style="font-size:14px;font-weight:600;color:#0f2540;">
                {{ $monitorados->count() }} processo(s) monitorado(s)
            </div>
            <button wire:click="abrirModalMonitoramento"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;
                           font-size:13px;font-weight:600;background:#059669;color:#fff;border:none;cursor:pointer;
                           transition:background .15s;"
                    onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Adicionar
            </button>
        </div>

        @forelse($monitorados as $proc)
            @php
                $scoreColorsM = [
                    'critico' => ['border'=>'#ef4444','iconBg'=>'#fee2e2','iconTxt'=>'#991b1b'],
                    'atencao' => ['border'=>'#f59e0b','iconBg'=>'#fef3c7','iconTxt'=>'#92400e'],
                    'normal'  => ['border'=>'#10b981','iconBg'=>'#d1fae5','iconTxt'=>'#065f46'],
                ];
                $sc3 = $scoreColorsM[$proc->score] ?? $scoreColorsM['normal'];
                $freqLabel = ['6h'=>'a cada 6h','12h'=>'a cada 12h','diario'=>'diário'][$proc->frequencia_monitoramento] ?? 'diário';
            @endphp
            <div style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:10px;
                        border-left:4px solid {{ $sc3['border'] }};border-top:1.5px solid #f1f5f9;
                        border-right:1.5px solid #f1f5f9;border-bottom:1.5px solid #f1f5f9;
                        box-shadow:0 1px 3px rgba(0,0,0,.06);
                        display:flex;align-items:center;gap:12px;">

                {{-- Ícone --}}
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $sc3['iconBg'] }};
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="none" stroke="{{ $sc3['iconTxt'] }}" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:#0f2540;font-family:monospace;">{{ $proc->numero_processo }}</div>
                    <div style="font-size:12px;color:#64748b;">{{ $proc->processo->cliente->nome ?? '—' }}</div>
                    @if($proc->processo->vara ?? null)
                        <div style="font-size:11px;color:#94a3b8;">{{ $proc->processo->vara }}</div>
                    @endif
                    <div style="display:flex;align-items:center;gap:6px;margin-top:4px;">
                        <svg width="11" height="11" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span style="font-size:11px;color:#94a3b8;">Verificação {{ $freqLabel }}</span>
                        @if($proc->ultimo_andamento_data)
                            <span style="font-size:11px;color:#94a3b8;">
                                · atualizado {{ \Carbon\Carbon::parse($proc->ultimo_andamento_data)->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Toggle --}}
                <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                    @if($proc->ativo)
                        <span style="width:8px;height:8px;border-radius:50%;background:#059669;
                                     animation:pulse 1.5s cubic-bezier(0,0,.2,1) infinite;display:inline-block;"></span>
                    @endif
                    <button wire:click="toggleMonitoramento({{ $proc->id }})"
                            title="{{ $proc->ativo ? 'Pausar' : 'Ativar' }} monitoramento"
                            style="width:42px;height:24px;border-radius:99px;border:none;cursor:pointer;
                                   background:{{ $proc->ativo ? '#059669' : '#cbd5e1' }};
                                   position:relative;transition:background .2s;">
                        <span style="position:absolute;top:3px;
                                     left:{{ $proc->ativo ? '20px' : '3px' }};
                                     width:18px;height:18px;border-radius:50%;background:#fff;
                                     transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.25);"></span>
                    </button>
                </div>

            </div>
        @empty
            <div style="text-align:center;padding:56px 24px;color:#94a3b8;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 14px;display:block;opacity:.4;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <p style="font-size:13px;margin:0;color:#64748b;">Nenhum processo em monitoramento.</p>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Clique em "Adicionar" para começar.</p>
            </div>
        @endforelse

        {{-- Modal Adicionar Monitoramento --}}
        @if($modalMonitoramento)
        <div style="position:fixed;inset:0;background:rgba(15,37,64,.5);z-index:999;display:flex;align-items:center;justify-content:center;"
             wire:click.self="fecharModalMonitoramento">
            <div style="background:#fff;border-radius:16px;width:480px;max-width:95vw;padding:24px;
                        box-shadow:0 24px 64px rgba(0,0,0,.18);" wire:click.stop>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f2540;">Adicionar Monitoramento</h3>
                    <button wire:click="fecharModalMonitoramento"
                            style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;line-height:1;
                                   width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">&times;</button>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:5px;">
                        Buscar processo (número ou cliente)
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="buscaMonitoramento"
                           style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;outline:none;"
                           placeholder="Ex: 0001234 ou João Silva">
                </div>

                @if(count($processosBusca))
                <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:12px;max-height:200px;overflow-y:auto;">
                    @foreach($processosBusca as $pb)
                    <div wire:click="selecionarProcessoMonit({{ $pb->id }})"
                         style="padding:9px 12px;cursor:pointer;border-bottom:1px solid #f1f5f9;
                                background:{{ $processoMonitId === $pb->id ? '#d1fae5' : '#fff' }};
                                transition:background .1s;"
                         onmouseover="if({{ $processoMonitId !== $pb->id ? 'true' : 'false' }})this.style.background='#f8fafc'"
                         onmouseout="this.style.background='{{ $processoMonitId === $pb->id ? '#d1fae5' : '#fff' }}'">
                        <div style="font-size:12px;font-weight:700;font-family:monospace;color:#1e293b;">{{ $pb->numero }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $pb->cliente->nome ?? '—' }}</div>
                    </div>
                    @endforeach
                </div>
                @elseif(strlen($buscaMonitoramento) >= 2)
                    <p style="font-size:12px;color:#94a3b8;margin-bottom:12px;">Nenhum processo encontrado.</p>
                @endif

                <div style="margin-bottom:20px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:5px;">
                        Frequência de verificação
                    </label>
                    <select wire:model="frequenciaSelect"
                            style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff;outline:none;">
                        <option value="6h">A cada 6 horas</option>
                        <option value="12h">A cada 12 horas</option>
                        <option value="diario">Diariamente</option>
                    </select>
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button wire:click="fecharModalMonitoramento"
                            style="padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;
                                   border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;color:#64748b;">
                        Cancelar
                    </button>
                    <button wire:click="confirmarMonitoramento"
                            style="padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;
                                   background:#059669;color:#fff;border:none;cursor:pointer;
                                   transition:background .15s;"
                            onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
        @endif

        @endif {{-- fim monitoramentos --}}


        {{-- ══════════════════════════════════════════════
             ABA 4 — HISTÓRICO
        ══════════════════════════════════════════════ --}}
        @if($aba === 'historico')

        <div style="display:flex;gap:7px;flex-wrap:wrap;margin-bottom:20px;">
            @foreach([
                'todos'      => 'Todos',
                'sentencas'  => 'Sentenças',
                'prazos'     => 'Prazos',
                'andamentos' => 'Andamentos',
                'decisoes'   => 'Decisões',
            ] as $val => $lbl)
                <button wire:click="setFiltroHistorico('{{ $val }}')"
                        style="padding:5px 14px;border-radius:99px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;
                               border:1.5px solid {{ $filtroHistorico===$val ? '#059669' : '#e2e8f0' }};
                               background:{{ $filtroHistorico===$val ? '#d1fae5' : 'transparent' }};
                               color:{{ $filtroHistorico===$val ? '#065f46' : '#64748b' }};">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>

        {{-- Timeline vertical --}}
        <div style="position:relative;padding-left:26px;">
            <div style="position:absolute;left:7px;top:0;bottom:0;width:2px;background:#e2e8f0;border-radius:1px;"></div>

            @forelse($historico as $item)
                @php
                    $tipo = $item->tipo ?? 'andamento';
                    $dotColor = match($tipo) {
                        'sentenca','decisao_urgente' => '#ef4444',
                        'prazo'                      => '#f59e0b',
                        'decisao'                    => '#3b82f6',
                        default                      => '#10b981',
                    };
                    $badgeTimeline = [
                        'sentenca'        => ['bg'=>'#fee2e2','txt'=>'#991b1b','label'=>'Sentença'],
                        'prazo'           => ['bg'=>'#fef3c7','txt'=>'#92400e','label'=>'Prazo'],
                        'andamento'       => ['bg'=>'#d1fae5','txt'=>'#065f46','label'=>'Andamento'],
                        'decisao'         => ['bg'=>'#dbeafe','txt'=>'#1e40af','label'=>'Decisão'],
                        'decisao_urgente' => ['bg'=>'#fee2e2','txt'=>'#991b1b','label'=>'Decisão Urgente'],
                    ];
                    $bt = $badgeTimeline[$tipo] ?? $badgeTimeline['andamento'];
                @endphp

                <div style="position:relative;margin-bottom:14px;">
                    <div style="position:absolute;left:-23px;top:15px;width:10px;height:10px;border-radius:50%;
                                background:{{ $dotColor }};border:2px solid #fff;box-shadow:0 0 0 2px {{ $dotColor }}40;"></div>
                    <div style="background:#fff;border-radius:10px;padding:12px 14px;
                                border:1.5px solid #f1f5f9;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;font-weight:700;font-family:monospace;color:#0f2540;">
                                    {{ optional($item->processo)->numero ?? '—' }}
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-bottom:4px;">
                                    {{ optional(optional($item->processo)->cliente)->nome ?? '—' }}
                                </div>
                                <div style="font-size:12px;color:#334155;line-height:1.5;">
                                    {{ Str::limit($item->descricao ?? $item->texto ?? '—', 120) }}
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                                <span style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:99px;
                                             background:{{ $bt['bg'] }};color:{{ $bt['txt'] }};">{{ $bt['label'] }}</span>
                                <span style="font-size:10px;color:#94a3b8;">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:40px 0;color:#94a3b8;">
                    <p style="font-size:13px;margin:0;">Nenhum registro encontrado.</p>
                </div>
            @endforelse
        </div>

        @if($historico->hasMorePages())
        <div style="text-align:center;margin-top:10px;">
            <button wire:click="carregarMais"
                    style="padding:9px 24px;border-radius:8px;font-size:13px;font-weight:600;
                           border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;color:#334155;
                           transition:border-color .15s;"
                    onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor='#e2e8f0'">
                <span wire:loading.remove wire:target="carregarMais">Carregar mais</span>
                <span wire:loading wire:target="carregarMais">Carregando...</span>
            </button>
        </div>
        @endif

        @endif {{-- fim historico --}}

    </div>{{-- fim coluna principal --}}


    {{-- ══════════════════════════════════════════════════════════════
         PAINEL DE NOTIFICAÇÕES (coluna direita)
    ══════════════════════════════════════════════════════════════ --}}
    <div style="width:300px;flex-shrink:0;background:#fff;border-left:1.5px solid #e2e8f0;
                position:sticky;top:0;height:calc(100vh - 52px);overflow-y:auto;
                display:flex;flex-direction:column;margin-right:-24px;margin-top:-24px;margin-bottom:-24px;">

        {{-- Poll para notificações --}}
        <div wire:poll.15s="$refresh" style="display:none"></div>

        {{-- Header --}}
        <div style="padding:16px;border-bottom:1.5px solid #e2e8f0;display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <span style="font-size:14px;font-weight:700;color:#0f2540;flex:1;">Notificações</span>
            @if($notificacoesNaoLidas > 0)
                <span style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;
                             padding:2px 7px;border-radius:99px;min-width:20px;text-align:center;">
                    {{ $notificacoesNaoLidas }}
                </span>
            @endif
            <button wire:click="atualizarAgora"
                    style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;border-radius:6px;
                           display:flex;align-items:center;justify-content:center;transition:color .15s,background .15s;"
                    title="Atualizar"
                    onmouseover="this.style.color='#059669';this.style.background='#f0fdf4'"
                    onmouseout="this.style.color='#94a3b8';this.style.background='none'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                </svg>
            </button>
        </div>

        {{-- Feed de notificações agrupado por tipo --}}
        <div style="flex:1;overflow-y:auto;padding:10px;display:flex;flex-direction:column;gap:6px;">
            @php
                $estilosPorTipo = [
                    'critico'   => ['bg'=>'#fee2e2','border'=>'#fca5a5','iconBg'=>'rgba(239,68,68,.15)','txt'=>'#7f1d1d','label'=>'Crítico'],
                    'decisao'   => ['bg'=>'#fee2e2','border'=>'#fca5a5','iconBg'=>'rgba(239,68,68,.15)','txt'=>'#7f1d1d','label'=>'Decisão'],
                    'prazo'     => ['bg'=>'#fef3c7','border'=>'#fde68a','iconBg'=>'rgba(245,158,11,.15)','txt'=>'#78350f','label'=>'Prazo'],
                    'atencao'   => ['bg'=>'#fef3c7','border'=>'#fde68a','iconBg'=>'rgba(245,158,11,.15)','txt'=>'#78350f','label'=>'Atenção'],
                    'andamento' => ['bg'=>'#d1fae5','border'=>'#6ee7b7','iconBg'=>'rgba(16,185,129,.15)','txt'=>'#064e3b','label'=>'Andamento'],
                    'normal'    => ['bg'=>'#d1fae5','border'=>'#6ee7b7','iconBg'=>'rgba(16,185,129,.15)','txt'=>'#064e3b','label'=>'Normal'],
                ];
                $estiloPadrao = ['bg'=>'#dbeafe','border'=>'#93c5fd','iconBg'=>'rgba(59,130,246,.15)','txt'=>'#1e3a8a','label'=>'Informativo'];

                // Agrupar por tipo, preservando a primeira notificação de cada grupo
                $grupos = [];
                foreach ($notificacoes as $notif) {
                    $tipo = $notif->tipo ?? 'informativo';
                    if (!isset($grupos[$tipo])) {
                        $grupos[$tipo] = ['itens' => [], 'estilo' => $estilosPorTipo[$tipo] ?? $estiloPadrao];
                    }
                    $grupos[$tipo]['itens'][] = $notif;
                }
            @endphp

            @forelse($grupos as $tipo => $grupo)
                @php
                    $st    = $grupo['estilo'];
                    $itens = $grupo['itens'];
                    $total = count($itens);
                    $first = $itens[0];
                    $grupoId = 'notif-grupo-' . $loop->index;
                @endphp

                {{-- Card do grupo --}}
                <div style="background:{{ $st['bg'] }};border:1px solid {{ $st['border'] }};border-radius:10px;overflow:hidden;">

                    {{-- Cabeçalho do grupo (clicável para expandir) --}}
                    <div onclick="
                            var el = document.getElementById('{{ $grupoId }}');
                            var arr = document.getElementById('{{ $grupoId }}-arr');
                            if(el.style.display==='none'){el.style.display='flex';arr.style.transform='rotate(180deg)';}
                            else{el.style.display='none';arr.style.transform='rotate(0deg)';}
                         "
                         style="display:flex;align-items:center;gap:7px;padding:9px 11px;cursor:pointer;">
                        <div style="width:24px;height:24px;border-radius:7px;background:{{ $st['iconBg'] }};
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="12" height="12" fill="none" stroke="{{ $st['txt'] }}" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 01-3.46 0"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <span style="font-size:12px;font-weight:700;color:{{ $st['txt'] }};">{{ $st['label'] }}</span>
                            @if($total > 1)
                                <span style="display:inline-block;margin-left:5px;font-size:10px;font-weight:700;
                                             padding:1px 6px;border-radius:99px;background:{{ $st['txt'] }};color:{{ $st['bg'] }};">
                                    {{ $total }}
                                </span>
                            @endif
                        </div>
                        <span style="font-size:10px;color:#94a3b8;white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($first->created_at)->diffForHumans(null, true) }}
                        </span>
                        <svg id="{{ $grupoId }}-arr" width="12" height="12" fill="none" stroke="#94a3b8" stroke-width="2.5"
                             viewBox="0 0 24 24" style="flex-shrink:0;transition:transform .2s;">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>

                    {{-- Primeira notificação sempre visível --}}
                    <div style="padding:0 11px 9px;border-top:1px solid {{ $st['border'] }};">
                        @if($first->processo_id)
                            <div style="font-size:11px;font-weight:700;color:{{ $st['txt'] }};opacity:.8;margin-top:6px;margin-bottom:2px;font-family:monospace;">
                                {{ optional($first->processo)->numero ?? '#'.$first->processo_id }}
                            </div>
                        @endif
                        <div style="font-size:11px;color:#475569;line-height:1.5;margin-top:{{ $first->processo_id ? '0' : '6px' }};">
                            {{ $first->mensagem }}
                        </div>
                    </div>

                    {{-- Demais notificações do grupo (colapsadas) --}}
                    @if($total > 1)
                        <div id="{{ $grupoId }}" style="display:none;flex-direction:column;gap:0;">
                            @foreach(array_slice($itens, 1) as $notifExtra)
                                <div style="padding:7px 11px;border-top:1px solid {{ $st['border'] }};">
                                    @if($notifExtra->processo_id)
                                        <div style="font-size:11px;font-weight:700;color:{{ $st['txt'] }};opacity:.8;margin-bottom:2px;font-family:monospace;">
                                            {{ optional($notifExtra->processo)->numero ?? '#'.$notifExtra->processo_id }}
                                        </div>
                                    @endif
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px;">
                                        <div style="font-size:11px;color:#475569;line-height:1.5;flex:1;">{{ $notifExtra->mensagem }}</div>
                                        <span style="font-size:10px;color:#94a3b8;white-space:nowrap;flex-shrink:0;">
                                            {{ \Carbon\Carbon::parse($notifExtra->created_at)->diffForHumans(null, true) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @empty
                <div style="text-align:center;padding:48px 12px;color:#94a3b8;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;opacity:.5;">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    <p style="font-size:12px;margin:0;">Sem notificações</p>
                </div>
            @endforelse
        </div>

        {{-- Rodapé --}}
        <div style="padding:10px;border-top:1.5px solid #e2e8f0;flex-shrink:0;">
            <button style="width:100%;padding:9px;border-radius:8px;font-size:12px;font-weight:600;
                           background:#f8fafc;color:#64748b;border:1.5px solid #e2e8f0;cursor:pointer;
                           transition:border-color .15s,color .15s;"
                    onmouseover="this.style.borderColor='#059669';this.style.color='#059669'"
                    onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                Outros Filtros
            </button>
        </div>

    </div>{{-- fim painel notificações --}}

</div>{{-- fim grid principal --}}

<style>
@keyframes spin  { to { transform: rotate(360deg); } }
@keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.5; transform:scale(1.4); } }
</style>

</div>











   {{-- BUSCA INTELIGENTE 
        <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 14px;margin-bottom:18px;">
            <div style="display:flex;align-items:center;gap:9px;margin-bottom:9px;">
                <svg width="15" height="15" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="buscaFeed"
                       style="flex:1;border:none;outline:none;font-size:13px;color:#1e293b;background:transparent;"
                       placeholder="Mostre processos com prazo vencendo...">
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding-top:9px;border-top:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#64748b;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                        <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                    Verifique atualizações de hoje
                </div>
                <button wire:click="atualizarAgora"
                        style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;
                               font-size:12px;font-weight:600;background:#1e3a5f;color:#fff;border:none;cursor:pointer;
                               transition:background .15s;white-space:nowrap;"
                        onmouseover="this.style.background='#0f2540'" onmouseout="this.style.background='#1e3a5f'">
                    Analisar com IA
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>

--}}











