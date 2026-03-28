@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp



@section('content')
<div>

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


<p style="font-size:13px;color:#64748b;margin-top:4px;">
    {{ $processo->cliente?->nome ?? '&mdash;' }}
    @if($processo->tipoAcao) &nbsp;&middot;&nbsp; {{ $processo->tipoAcao->descricao }} @endif
    @if($processo->vara) &nbsp;&middot;&nbsp; {{ $processo->vara }} @endif
</p>
@if($processo->parte_contraria)
<p style="font-size:12px;color:#94a3b8;margin-top:2px;">
    ⚖ vs. {{ $processo->parte_contraria }}
</p>
@endif


                @if($processo->tipoAcao) &nbsp;&middot;&nbsp; {{ $processo->tipoAcao->descricao }} @endif
                @if($processo->vara) &nbsp;&middot;&nbsp; {{ $processo->vara }} @endif
            </p>
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







    {{-- ── Abas ── --}}
    <div style="display:flex;gap:2px;border-bottom:2px solid var(--border);margin-bottom:20px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
        @php
        $totalTarefas = \Illuminate\Support\Facades\DB::selectOne(
            'SELECT COUNT(*) as total, SUM(CASE WHEN concluida THEN 1 ELSE 0 END) as concluidas FROM processo_tarefas WHERE processo_id = ?',
            [$processo->id]
        );
        $abas = [
            'dados'           => 'Dados',
            'andamentos'      => 'Andamentos (' . $processo->andamentos->count() . ')',
            'audiencias'      => 'Audiências (' . $processo->audiencias->count() . ')',
            'agenda'          => 'Agenda (' . $processo->agenda->count() . ')',
            'prazos'          => 'Prazos (' . $prazos->count() . ')',
            'financeiro'      => 'Financeiro',
            'documentos'      => 'Documentos (' . count($documentos) . ')',
            'checklist'       => 'Checklist (' . ($totalTarefas->concluidas ?? 0) . '/' . ($totalTarefas->total ?? 0) . ')',
            'minutas'         => 'Minutas',
            'historico_fases' => 'Histórico de Fases',
            'apontamentos'    => 'Horas',
            'timeline'        => 'Timeline',
        ];
        @endphp
        @foreach($abas as $key => $label)
        <button onclick="showTab('{{ $key }}')" id="tab-btn-{{ $key }}"
            style="padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;background:none;border:none;
                   white-space:nowrap;border-bottom:3px solid transparent;color:var(--muted);
                   margin-bottom:-2px;transition:all .15s;">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── ABA: DADOS ── --}}
    <div id="tab-dados" class="tab-content">
        <div class="grid-2" style="margin-bottom:20px;">

            <div class="card">
                <div class="card-header"><span class="card-title">Dados do Processo</span></div>
                <table style="width:100%;font-size:13px;">
                    <tr><td style="color:var(--muted);padding:5px 0;width:45%;">Numero:</td><td style="font-weight:600;">{{ $processo->numero }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Distribuicao:</td><td>{{ $processo->data_distribuicao?->format('d/m/Y') ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Tipo de Acao:</td><td>{{ $processo->tipoAcao?->descricao ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Tipo do Processo:</td><td>{{ $processo->tipoProcesso?->descricao ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Fase:</td><td>{{ $processo->fase?->descricao ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Assunto:</td><td>{{ $processo->assunto?->descricao ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Vara:</td><td>{{ $processo->vara ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Secretaria:</td><td>{{ $processo->secretaria?->descricao ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Reparticao:</td><td>{{ $processo->reparticao?->descricao ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Valor da Causa:</td><td>R$ {{ number_format($processo->valor_causa, 2, ',', '.') }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Valor em Risco:</td><td>R$ {{ number_format($processo->valor_risco, 2, ',', '.') }}</td></tr>
                </table>
            </div>

            <div class="card">
                <div class="card-header"><span class="card-title">Partes</span></div>
                <table style="width:100%;font-size:13px;">
                    <tr><td style="color:var(--muted);padding:5px 0;width:45%;">Cliente:</td><td style="font-weight:600;">{{ $processo->cliente?->nome ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Parte Contraria:</td><td>{{ $processo->parte_contraria ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Advogado:</td><td>{{ $processo->advogado?->nome ?? '&mdash;' }}</td></tr>
                    <tr><td style="color:var(--muted);padding:5px 0;">Juiz:</td><td>{{ $processo->juiz?->nome ?? '&mdash;' }}</td></tr>
                </table>

                @if($processo->observacoes)
                <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b;border-left:3px solid var(--border);">
                    <strong>Observacoes:</strong><br>{{ $processo->observacoes }}
                </div>
                @endif

                {{-- Mini-resumo financeiro --}}
                @php
                    $recTotais = \App\Models\Recebimento::totaisPorProcesso($processo->id);
                @endphp
                <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
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
            </div>
        </div>

        {{-- Rentabilidade --}}
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Rentabilidade
                </span>
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

    {{-- ── ABA: AGENDA ── --}}
    <div id="tab-agenda" class="tab-content" style="display:none;">
        @livewire('App\Livewire\Agenda', ['embed' => true, 'processoId' => $processo->id], key('agenda-'.$processo->id))
    </div>

    {{-- ── ABA: PRAZOS ── --}}
    <div id="tab-prazos" class="tab-content" style="display:none;">
        @livewire('App\Livewire\Prazos', ['embed' => true, 'processoId' => $processo->id], key('prazos-'.$processo->id))
    </div>

    {{-- ── ABA: FINANCEIRO ── --}}
    <div id="tab-financeiro" class="tab-content" style="display:none;">
        @livewire('financeiro', ['processoId' => $processo->id])
    </div>

    {{-- ── ABA: CHECKLIST ── --}}
    <div id="tab-checklist" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg> Checklist de Tarefas</span>
            </div>
            @livewire('processo-checklist', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: MINUTAS ── --}}
    <div id="tab-minutas" class="tab-content" style="display:none;">
        @livewire(\App\Livewire\MinutaIA::class, ['processoId' => $processo->id])
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Gerar Minuta</span>
                <a href="{{ route('minutas') }}" class="btn btn-secondary btn-sm" target="_blank">Gerenciar Templates</a>
            </div>
            @livewire('processo-minuta', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: HISTÓRICO DE FASES ── --}}
    <div id="tab-historico_fases" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg> Histórico de Fases</span>
            </div>
            @livewire('processo-historico-fases', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: APONTAMENTOS DE HORAS ── --}}
    <div id="tab-apontamentos" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Apontamento de Horas</span>
            </div>
            @livewire('processo-apontamentos', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: TIMELINE ── --}}
    <div id="tab-timeline" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:7px;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Timeline do Processo
                </span>
                <span style="font-size:11px;color:var(--muted);">{{ $timeline->count() }} eventos</span>
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
                'andamento' => '<svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
                'prazo'     => '<svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                'agenda'    => '<svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                'audiencia' => '<svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 9l9-6 9 6M3 9h18"/></svg>',
            ];
            $tipoLabel = ['andamento'=>'Andamento','prazo'=>'Prazo','agenda'=>'Agenda','audiencia'=>'Audiência'];
            $lastDate  = null;
            @endphp
            <div style="position:relative;padding-left:32px;">
                <div style="position:absolute;left:15px;top:0;bottom:0;width:2px;background:var(--border);"></div>
                @foreach($timeline as $ev)
                @php
                $evDate = is_string($ev['data']) ? \Carbon\Carbon::parse($ev['data']) : $ev['data'];
                $showDate = $lastDate === null || ! $evDate->isSameDay($lastDate);
                $lastDate = $evDate;
                @endphp
                @if($showDate)
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:18px 0 8px -32px;padding-left:32px;position:relative;">
                    <div style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:14px;height:14px;border-radius:50%;background:var(--bg);border:2px solid var(--border);"></div>
                    {{ $evDate->translatedFormat('d \d\e F \d\e Y') }}
                </div>
                @endif
                <div style="position:relative;margin-bottom:10px;">
                    <div style="position:absolute;left:-23px;top:10px;width:10px;height:10px;border-radius:50%;background:{{ $ev['cor'] }};border:2px solid #fff;box-shadow:0 0 0 1px {{ $ev['cor'] }};"></div>
                    <div style="background:var(--white);border:1px solid var(--border);border-radius:8px;padding:10px 14px;border-left:3px solid {{ $ev['cor'] }};">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="color:{{ $ev['cor'] }}">{!! $tipoIcon[$ev['tipo']] ?? '' !!}</span>
                                <span style="font-size:13px;font-weight:600;color:#1e293b;">{{ $ev['titulo'] }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:10px;font-weight:700;background:{{ $ev['cor'] }}22;color:{{ $ev['cor'] }};padding:2px 8px;border-radius:20px;">
                                    {{ $tipoLabel[$ev['tipo']] ?? $ev['tipo'] }}
                                </span>
                                <span style="font-size:11px;color:var(--muted);">{{ $evDate->format('H:i') !== '00:00' ? $evDate->format('H:i') : '' }}</span>
                            </div>
                        </div>
                        @if($ev['sub'])
                        <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $ev['sub'] }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
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
                            <td>{{ $doc->data_documento ? \Carbon\Carbon::parse($doc->data_documento)->format('d/m/Y') : '&mdash;' }}</td>
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
    </div>

</div>

<script>
const TAB_KEY = 'processo_{{ $processo->id }}_tab';

function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.style.borderBottomColor = 'transparent';
        btn.style.color = 'var(--muted)';
    });
    document.getElementById('tab-' + name).style.display = 'block';
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
