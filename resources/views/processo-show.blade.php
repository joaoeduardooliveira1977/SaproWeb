@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp



@section('content')
<div style="width:100%;min-height:200px;">

    {{-- ── Cabecalho ── --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
           



	    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h2 style="font-size:20px;font-weight:700;color:var(--primary);">&#9878; {{ $processo->numero }}</h2>
                <span style="padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;
                    background:{{ $processo->status === 'Ativo' ? '#dcfce7' : '#f1f5f9' }};
                    color:{{ $processo->status === 'Ativo' ? '#16a34a' : '#64748b' }};">
                    {{ $processo->status }}
                </span>
                @if($processo->risco)
                <span style="padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;
                    background:{{ $processo->risco->cor_hex }}22;color:{{ $processo->risco->cor_hex }};">
                    {{ $processo->risco->descricao }}
                </span>
                @endif
            </div>

<p style="font-size:13px;color:#64748b;margin-top:4px;">
    {{ $processo->cliente?->nome ?? '—' }}
    @if($processo->tipoAcao) &nbsp;&middot;&nbsp; {{ $processo->tipoAcao->descricao }} @endif
    @if($processo->vara) &nbsp;&middot;&nbsp; {{ $processo->vara }} @endif
</p>


@if($processo->parte_contraria)
<p style="font-size:12px;color:#94a3b8;margin-top:2px;">
    ⚖ vs. {{ $processo->parte_contraria }} 

</p>


@endif
            </div>


	<div class="card-actions">
            @if($processo->tjsp_ultima_consulta)
                <span style="font-size:11px;color:var(--muted)">
                    <span style="display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="13" y2="15"/></svg> DATAJUD: {{ \Carbon\Carbon::parse($processo->tjsp_ultima_consulta)->format('d/m/Y H:i') }}</span>
                </span>
            @endif
            <a href="{{ route('tjsp') }}" class="btn btn-secondary btn-sm" title="Consultar andamentos no DATAJUD" style="display:inline-flex;align-items:center;gap:5px;"><svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg> DATAJUD</a>
            <a href="{{ route('processos.editar', $processo->id) }}" class="btn btn-primary btn-sm">Editar</a>
            <a href="{{ route('processos') }}" class="btn btn-secondary btn-sm">&larr; Voltar</a>
        </div>
    </div>



 

    <script>
    function toggleAnaliseIA() {
        const bloco = document.getElementById('bloco-analise-ia');
        const txt   = document.getElementById('btn-analise-ia-txt');
        if (bloco.style.display === 'none') {
            bloco.style.display = 'block';
            txt.textContent = '✕ Fechar Análise IA';
        } else {
            bloco.style.display = 'none';
            txt.textContent = '✨ Análise IA';
        }
    }
    </script>







<div>
    {{-- ── Abas ── --}}
    <div style="display:flex;gap:2px;border-bottom:2px solid var(--border);margin-bottom:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
        @php
        $totalTarefas = \Illuminate\Support\Facades\DB::selectOne(
            'SELECT COUNT(*) as total, SUM(CASE WHEN concluida THEN 1 ELSE 0 END) as concluidas FROM processo_tarefas WHERE processo_id = ?',
            [$processo->id]
        );
        $abasPrincipais = [
            'dados'           => 'Dados',
            'andamentos'      => 'Andamentos (' . $processo->andamentos->count() . ')',
            'audiencias'      => 'Audiências (' . $processo->audiencias->count() . ')',
            'prazos'          => 'Prazos e Agenda',
            'financeiro'      => 'Financeiro',
            'documentos'      => 'Documentos (' . count($documentos) . ')',
        ];

        $abasSecundarias = [
            'historico'        => 'Histórico',
            'jurisprudencia'  => 'Jurisprudência',
        ];
        @endphp
        @foreach($abasPrincipais as $key => $label)
        <button onclick="showTab('{{ $key }}')" id="tab-btn-{{ $key }}"
            style="padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;background:none;border:none;
                   white-space:nowrap;border-bottom:3px solid transparent;color:var(--muted);
                   margin-bottom:-2px;transition:all .15s;">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:20px;padding:8px 10px;background:#f8fafc;border:1px solid var(--border);border-radius:8px;">
        <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-right:2px;">Mais opções</span>
        @foreach($abasSecundarias as $key => $label)
        <button onclick="showTab('{{ $key }}')" id="tab-btn-{{ $key }}"
            style="padding:6px 10px;font-size:12px;font-weight:600;cursor:pointer;background:var(--white);border:1px solid var(--border);
                   white-space:nowrap;color:var(--muted);text-align:left;border-radius:6px;transition:all .15s;">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── ABA: DADOS ── --}}
    <div id="tab-dados" class="tab-content">
        <div class="grid-2" style="margin-bottom:20px;">

            <div class="card">
                <div class="card-header"><span class="card-title">Dados do Processo</span></div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
                    <div style="grid-column:1/-1;padding:12px;border:1px solid var(--border);border-radius:8px;background:#f8fafc;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Numero do processo</div>
                        <div style="font-size:15px;font-weight:800;color:var(--text);word-break:break-word;">{{ $processo->numero }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Distribuicao</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->data_distribuicao?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Fase</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->fase?->descricao ?? '—' }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Tipo de Acao</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->tipoAcao?->descricao ?? '—' }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Tipo do Processo</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->tipoProcesso?->descricao ?? '—' }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Vara</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->vara ?? '—' }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Reparticao</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->reparticao?->descricao ?? '—' }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid #bbf7d0;border-radius:8px;background:#f0fdf4;">
                        <div style="font-size:11px;color:#166534;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Valor da Causa</div>
                        <div style="font-size:14px;font-weight:800;color:#16a34a;">R$ {{ number_format($processo->valor_causa, 2, ',', '.') }}</div>
                    </div>
                    <div style="padding:10px;border:1px solid #fed7aa;border-radius:8px;background:#fff7ed;">
                        <div style="font-size:11px;color:#9a3412;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Valor em Risco</div>
                        <div style="font-size:14px;font-weight:800;color:#d97706;">R$ {{ number_format($processo->valor_risco, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><span class="card-title">Partes</span></div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="padding:12px;border:1px solid var(--border);border-radius:8px;background:#f8fafc;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Cliente</div>
                        <div style="font-size:14px;font-weight:800;color:var(--text);">{{ $processo->cliente?->nome ?? '—' }}</div>
                    </div>
                    <div style="padding:12px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Parte Contraria</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->parte_contraria ?? '—' }}</div>
                    </div>
                    <div style="padding:12px;border:1px solid var(--border);border-radius:8px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Advogado</div>
                        <div style="font-size:13px;color:var(--text);">{{ $processo->advogado?->nome ?? '—' }}</div>
                    </div>
                </div>

                @if($processo->observacoes)
                <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b;border-left:3px solid var(--border);">
                    <strong>Observacoes:</strong><br>{{ $processo->observacoes }}
                </div>
                @endif
            </div>
        </div>

        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg> Checklist de Tarefas</span>
            </div>
            @livewire('processo-checklist', ['processoId' => $processo->id])
        </div>

        {{-- Rentabilidade --}}
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Rentabilidade
                </span>
            </div>
            @php
                $recTotais = \App\Models\Recebimento::totaisPorProcesso($processo->id);
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:12px;">
                <div style="background:#f0fdf4;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#64748b;margin-bottom:2px;">Total</div>
                    <div style="font-size:13px;font-weight:700;color:#16a34a;">R$ {{ number_format($recTotais['total'],2,',','.') }}</div>
                </div>
                <div style="background:#eff6ff;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#64748b;margin-bottom:2px;">Recebido</div>
                    <div style="font-size:13px;font-weight:700;color:#2563a8;">R$ {{ number_format($recTotais['recebido'],2,',','.') }}</div>
                </div>
                <div style="background:#fef2f2;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#64748b;margin-bottom:2px;">Pendente</div>
                    <div style="font-size:13px;font-weight:700;color:#dc2626;">R$ {{ number_format($recTotais['pendente'],2,',','.') }}</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;">
                <div style="background:#f0fdf4;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:10px;color:#64748b;margin-bottom:2px;text-transform:uppercase;font-weight:600;">Recebido</div>
                    <div style="font-size:14px;font-weight:700;color:#16a34a;">R$ {{ number_format($rentabilidade['recebido'],2,',','.') }}</div>
                </div>
                <div style="background:#fff7ed;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:10px;color:#64748b;margin-bottom:2px;text-transform:uppercase;font-weight:600;">A Receber</div>
                    <div style="font-size:14px;font-weight:700;color:#d97706;">R$ {{ number_format($rentabilidade['pendente'],2,',','.') }}</div>
                </div>
                <div style="background:#f1f5f9;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:10px;color:#64748b;margin-bottom:2px;text-transform:uppercase;font-weight:600;">Horas</div>
                    <div style="font-size:14px;font-weight:700;color:#475569;">{{ number_format($rentabilidade['horas'],1,',','.') }}h</div>
                </div>
                <div style="background:#fef2f2;padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:10px;color:#64748b;margin-bottom:2px;text-transform:uppercase;font-weight:600;">Custo (apontamentos)</div>
                    <div style="font-size:14px;font-weight:700;color:#dc2626;">R$ {{ number_format($rentabilidade['custo_estimado'],2,',','.') }}</div>
                </div>
                <div style="background:{{ $rentabilidade['saldo'] >= 0 ? '#f0fdf4' : '#fef2f2' }};padding:10px;border-radius:8px;text-align:center;">
                    <div style="font-size:10px;color:#64748b;margin-bottom:2px;text-transform:uppercase;font-weight:600;">Saldo</div>
                    <div style="font-size:14px;font-weight:700;color:{{ $rentabilidade['saldo'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        R$ {{ number_format($rentabilidade['saldo'],2,',','.') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

 

    {{-- ── ABA: ANDAMENTOS ── --}}
    <div id="tab-andamentos" class="tab-content" style="display:none;">
        @livewire('processo-andamentos', ['processoId' => $processo->id, 'embed' => true])
    </div>

    {{-- ── ABA: AUDIÊNCIAS ── --}}
    <div id="tab-audiencias" class="tab-content" style="display:none;">
        @livewire('App\Livewire\Audiencias', ['embed' => true, 'processoId' => $processo->id], key('audiencias-'.$processo->id))
    </div>

    {{-- ── ABA: PRAZOS E AGENDA ── --}}
    <div id="tab-prazos" class="tab-content" style="display:none;">
        @livewire('App\Livewire\Prazos', ['embed' => true, 'processoId' => $processo->id], key('prazos-'.$processo->id))
        <div style="margin-top:16px;">
            @livewire('App\Livewire\Agenda', ['embed' => true, 'processoId' => $processo->id], key('agenda-'.$processo->id))
        </div>
    </div>

    {{-- ── ABA: FINANCEIRO ── --}}
    <div id="tab-financeiro" class="tab-content" style="display:none;">
        @livewire('financeiro', ['processoId' => $processo->id])
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Apontamento de Horas</span>
            </div>
            @livewire('processo-apontamentos', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: HISTÓRICO ── --}}
    <div id="tab-historico" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg> Histórico de Fases</span>
            </div>
            @livewire('processo-historico-fases', ['processoId' => $processo->id])
        </div>

        {{-- Barra de filtro por tipo --}}
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;align-items:center;">
            <span style="font-size:12px;color:var(--muted);font-weight:600;margin-right:4px;">Filtrar:</span>
            @php
            $filtrosBtns = [
                'todos'     => ['label'=>'Todos',      'cor'=>'#475569'],
                'andamento' => ['label'=>'Andamentos', 'cor'=>'#2563eb'],
                'prazo'     => ['label'=>'Prazos',     'cor'=>'#d97706'],
                'agenda'    => ['label'=>'Agenda',     'cor'=>'#7c3aed'],
                'audiencia' => ['label'=>'Audiências', 'cor'=>'#0891b2'],
                'documento' => ['label'=>'Documentos', 'cor'=>'#64748b'],
            ];
            @endphp
            @foreach($filtrosBtns as $fKey => $fDef)
            <button onclick="filtrarTimeline('{{ $fKey }}')" id="tl-btn-{{ $fKey }}"
                style="padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;border-radius:20px;
                       border:2px solid {{ $fDef['cor'] }};color:{{ $fDef['cor'] }};background:transparent;
                       transition:all .15s;">
                {{ $fDef['label'] }}
            </button>
            @endforeach
            <span style="margin-left:auto;font-size:11px;color:var(--muted);" id="tl-count-label">{{ $timeline->count() }} eventos</span>
        </div>

        @if($timeline->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><svg aria-hidden="true" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/></svg></div>
                <div class="empty-state-title">Sem eventos</div>
                <div class="empty-state-sub">Andamentos, prazos e audiências aparecerão aqui.</div>
            </div>
        @else
        @php
        $tipoIcon = [
            'andamento' => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
            'prazo'     => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            'agenda'    => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
            'audiencia' => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
            'documento' => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
        ];
        $tipoLabel = ['andamento'=>'Andamento','prazo'=>'Prazo','agenda'=>'Agenda','audiencia'=>'Audiência','documento'=>'Documento'];
        $lastMes   = null;
        @endphp

        <div style="position:relative;padding-left:36px;" id="tl-container">
            {{-- Linha vertical --}}
            <div style="position:absolute;left:17px;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,transparent,var(--border) 30px,var(--border) calc(100% - 30px),transparent);"></div>

            @foreach($timeline as $ev)
            @php
            $evDate = $ev['data'] ? (is_string($ev['data']) ? \Carbon\Carbon::parse($ev['data']) : $ev['data']) : null;
            if (!$evDate) { continue; }
            $mesesPt = ['January'=>'Janeiro','February'=>'Fevereiro','March'=>'Março','April'=>'Abril','May'=>'Maio','June'=>'Junho','July'=>'Julho','August'=>'Agosto','September'=>'Setembro','October'=>'Outubro','November'=>'Novembro','December'=>'Dezembro'];
            $mesSep  = ($mesesPt[$evDate->format('F')] ?? $evDate->format('F')) . ' de ' . $evDate->format('Y');
            $showMes = $mesSep !== $lastMes;
            $lastMes = $mesSep;
            $extra   = $ev['extra'] ?? [];
            @endphp

            @if($showMes)
            {{-- Separador de mês --}}
            <div class="tl-month-sep" data-tipo="{{ $ev['tipo'] }}"
                 style="display:flex;align-items:center;gap:10px;margin:22px 0 10px -36px;padding-left:36px;position:relative;">
                <div style="position:absolute;left:10px;top:50%;transform:translateY(-50%);
                            width:16px;height:16px;border-radius:50%;background:#f8fafc;
                            border:2px solid #cbd5e1;z-index:1;"></div>
                <span style="font-size:11px;font-weight:800;color:#94a3b8;text-transform:uppercase;
                             letter-spacing:1px;">{{ $mesSep }}</span>
                <div style="flex:1;height:1px;background:var(--border);"></div>
            </div>
            @endif

            {{-- Card de evento --}}
            <div class="tl-evento" data-tipo="{{ $ev['tipo'] }}"
                 style="position:relative;margin-bottom:10px;">
                {{-- Ponto da linha --}}
                <div style="position:absolute;left:-25px;top:14px;width:12px;height:12px;border-radius:50%;
                            background:{{ $ev['cor'] }};border:2px solid #fff;
                            box-shadow:0 0 0 2px {{ $ev['cor'] }}40;z-index:1;"></div>

                <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;
                            padding:12px 16px;border-left:4px solid {{ $ev['cor'] }};
                            transition:box-shadow .15s;"
                     onmouseover="this.style.boxShadow='0 4px 16px {{ $ev['cor'] }}20'"
                     onmouseout="this.style.boxShadow='none'">

                    {{-- Linha superior: ícone + título + badge + hora --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:7px;flex:1;min-width:0;">
                            <span style="color:{{ $ev['cor'] }};flex-shrink:0;">{!! $tipoIcon[$ev['tipo']] ?? '' !!}</span>
                            <span style="font-size:13px;font-weight:700;color:#1e293b;line-height:1.3;">{{ $ev['titulo'] }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                            <span style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:20px;
                                         background:{{ $ev['cor'] }}18;color:{{ $ev['cor'] }};">
                                {{ $tipoLabel[$ev['tipo']] ?? $ev['tipo'] }}
                            </span>
                            <span style="font-size:11px;color:#94a3b8;white-space:nowrap;">
                                {{ $evDate->format('d/m') }}
                                @if($evDate->format('H:i') !== '00:00')
                                    · {{ $evDate->format('H:i') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Sub-linha: responsável / autor / tipo --}}
                    @if($ev['sub'])
                    <div style="font-size:11px;color:#64748b;margin-top:4px;display:flex;align-items:center;gap:4px;">
                        <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $ev['sub'] }}
                    </div>
                    @endif

                    {{-- Badges/detalhes extras por tipo --}}
                    @if(!empty($extra))
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:7px;">

                        {{-- PRAZO extras --}}
                        @if($ev['tipo'] === 'prazo')
                            @if($extra['fatal'] ?? false)
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                ⚠ Fatal
                            </span>
                            @endif
                            @if($extra['cumprido'] ?? false)
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                ✓ Cumprido
                            </span>
                            @elseif($extra['vencido'] ?? false)
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                ✕ Vencido
                            </span>
                            @else
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fff7ed;color:#d97706;border:1px solid #fed7aa;">
                                ⏳ Aberto
                            </span>
                            @endif
                        @endif

                        {{-- AGENDA extras --}}
                        @if($ev['tipo'] === 'agenda')
                            @if($extra['local'] ?? null)
                            <span style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:3px;">
                                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $extra['local'] }}
                            </span>
                            @endif
                            @if($extra['urgente'] ?? false)
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                🔴 Urgente
                            </span>
                            @endif
                            @if($extra['concluido'] ?? false)
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                ✓ Concluído
                            </span>
                            @endif
                        @endif

                        {{-- AUDIÊNCIA extras --}}
                        @if($ev['tipo'] === 'audiencia')
                            @if($extra['local'] ?? null)
                            <span style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:3px;">
                                <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $extra['local'] }}
                            </span>
                            @endif
                        @endif

                        {{-- DOCUMENTO extras --}}
                        @if($ev['tipo'] === 'documento' && ($extra['arquivo'] ?? null))
                        <a href="{{ Storage::url($extra['arquivo']) }}" target="_blank"
                           style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:20px;
                                  background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;
                                  text-decoration:none;display:flex;align-items:center;gap:3px;">
                            <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download
                        </a>
                        @endif

                    </div>
                    @endif

                </div>
            </div>
            @endforeach
        </div>
        @endif

        <script>
        (function(){
            var current = 'todos';
            var corMap = {
                'todos':     '#475569',
                'andamento': '#2563eb',
                'prazo':     '#d97706',
                'agenda':    '#7c3aed',
                'audiencia': '#0891b2',
                'documento': '#64748b',
            };

            window.filtrarTimeline = function(tipo) {
                current = tipo;
                var container = document.getElementById('tl-container');
                if (!container) return;

                var eventos  = container.querySelectorAll('.tl-evento');
                var meses    = container.querySelectorAll('.tl-month-sep');
                var visivel  = 0;

                eventos.forEach(function(el) {
                    var show = (tipo === 'todos' || el.dataset.tipo === tipo);
                    el.style.display = show ? 'block' : 'none';
                    if (show) visivel++;
                });

                // Esconder separadores de mês que não têm eventos visíveis após eles
                meses.forEach(function(sep) {
                    // Mostra o separador se tipo = todos, ou se o mês tem eventos do tipo selecionado
                    var show = (tipo === 'todos' || sep.dataset.tipo === tipo);
                    // Percorrer próximos irmãos para ver se há algum evento visível neste mês
                    if (tipo !== 'todos') {
                        show = false;
                        var el = sep.nextElementSibling;
                        while (el && !el.classList.contains('tl-month-sep')) {
                            if (el.classList.contains('tl-evento') && el.dataset.tipo === tipo) {
                                show = true; break;
                            }
                            el = el.nextElementSibling;
                        }
                    }
                    sep.style.display = show ? 'flex' : 'none';
                });

                // Atualizar label de contagem
                var label = document.getElementById('tl-count-label');
                if (label) label.textContent = visivel + ' evento' + (visivel !== 1 ? 's' : '');

                // Atualizar estilo dos botões
                var cor = corMap[tipo] || '#475569';
                Object.keys(corMap).forEach(function(k) {
                    var btn = document.getElementById('tl-btn-' + k);
                    if (!btn) return;
                    if (k === tipo) {
                        btn.style.background = corMap[k];
                        btn.style.color = '#fff';
                        btn.style.borderColor = corMap[k];
                    } else {
                        btn.style.background = 'transparent';
                        btn.style.color = corMap[k];
                        btn.style.borderColor = corMap[k];
                    }
                });
            };

            // Ativar "Todos" por padrão
            filtrarTimeline('todos');
        })();
        </script>
    </div>

    {{-- ── ABA: DOCUMENTOS ── --}}
    <div id="tab-documentos" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Documentos do Processo</span>
                <a href="{{ route('documentos') }}" class="btn btn-secondary btn-sm">Gerenciar Documentos</a>
            </div>
            @if(empty($documentos))
                <div class="empty-state">
                    <div class="empty-state-icon"><svg aria-hidden="true" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></div>
                    <div class="empty-state-title">Nenhum documento</div>
                    <div class="empty-state-sub">Faça upload de documentos em <a href="{{ route('documentos') }}" style="color:var(--primary-light)">Documentos</a>.</div>
                </div>
            @else
            @php
            $tipoLabel = [
                'peticao'=>'Peticao','contrato'=>'Contrato','sentenca'=>'Sentenca',
                'documento_cliente'=>'Doc. Cliente','procuracao'=>'Procuracao',
                'recurso'=>'Recurso','parecer'=>'Parecer','outros'=>'Outros',
            ];
            @endphp
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Titulo</th><th>Tipo</th><th>Data</th><th>Tamanho</th><th>Portal</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($documentos as $doc)
                        <tr>
                            <td style="font-weight:600;">{{ $doc->titulo }}</td>
                            <td><span class="badge" style="background:#2563a822;color:#2563a8;">{{ $tipoLabel[$doc->tipo] ?? $doc->tipo }}</span></td>
                            <td>{{ $doc->data_documento ? \Carbon\Carbon::parse($doc->data_documento)->format('d/m/Y') : '—' }}</td>
                            <td style="color:var(--muted);font-size:12px;">
                                @if($doc->tamanho) {{ round($doc->tamanho / 1024) }} KB @else &mdash; @endif
                            </td>
                            <td>
                                @if($doc->portal_visivel)
                                    <span class="badge" style="background:#dcfce7;color:#16a34a;">Visivel</span>
                                @else
                                    <span class="badge" style="background:#f1f5f9;color:#64748b;">Privado</span>
                                @endif
                            </td>
                            <td>
                                @if($doc->arquivo)
                                <a href="{{ Storage::url($doc->arquivo) }}" target="_blank"
                                    class="btn btn-secondary btn-sm">Download</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @livewire(\App\Livewire\MinutaIA::class, ['processoId' => $processo->id])
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Gerar Minuta</span>
                <a href="{{ route('minutas') }}" class="btn btn-secondary btn-sm" target="_blank">Gerenciar Templates</a>
            </div>
            @livewire('processo-minuta', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: JURISPRUDÊNCIA ── --}}
    <div id="tab-jurisprudencia" class="tab-content" style="display:none;">
        @livewire('jurisprudencia-search', ['processoId' => $processo->id, 'embed' => true])
    </div>

</div>{{-- última aba --}}
</div>{{-- /processo-tabs-wrapper --}}

<script>
const TAB_KEY = 'processo_{{ $processo->id }}_tab';

function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.style.borderBottomColor = 'transparent';
        btn.style.color = 'var(--muted)';
    });

    const tab = document.getElementById('tab-' + name);
    if (tab) tab.style.display = 'block';

    const btn = document.getElementById('tab-btn-' + name);
    if (btn) {
        btn.style.borderBottomColor = 'var(--primary)';
        btn.style.color = 'var(--primary)';
    }

    localStorage.setItem(TAB_KEY, name);
}

const saved = localStorage.getItem(TAB_KEY) || 'dados';
showTab(saved);
</script>
@endsection
