@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp
@section('page-title', 'Processo ' . $processo->numero)

@section('content')
<div>

    {{-- ── Cabecalho ── --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h2 style="font-size:20px;font-weight:700;color:#1a3a5c;">&#9878; {{ $processo->numero }}</h2>
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
                {{ $processo->cliente?->nome ?? '&mdash;' }}
                @if($processo->tipoAcao) &nbsp;&middot;&nbsp; {{ $processo->tipoAcao->descricao }} @endif
                @if($processo->vara) &nbsp;&middot;&nbsp; {{ $processo->vara }} @endif
            </p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            @if($processo->tjsp_ultima_consulta)
                <span style="font-size:11px;color:var(--muted)">
                    🏛️ DATAJUD: {{ \Carbon\Carbon::parse($processo->tjsp_ultima_consulta)->format('d/m/Y H:i') }}
                </span>
            @endif
            <a href="{{ route('tjsp') }}" class="btn btn-secondary btn-sm" title="Consultar andamentos no DATAJUD">🔄 DATAJUD</a>
            <a href="{{ route('processos.editar', $processo->id) }}" class="btn btn-primary btn-sm">Editar</a>
            <a href="{{ route('processos') }}" class="btn btn-secondary btn-sm">&larr; Voltar</a>
        </div>
    </div>

    {{-- ── Abas ── --}}
    <div style="display:flex;gap:2px;border-bottom:2px solid var(--border);margin-bottom:20px;overflow-x:auto;">
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
    </div>

    {{-- ── ABA: ANDAMENTOS ── --}}
    <div id="tab-andamentos" class="tab-content" style="display:none;">
        @livewire('processo-andamentos', ['processoId' => $processo->id, 'embed' => true])
    </div>

    {{-- ── ABA: AUDIÊNCIAS ── --}}
    <div id="tab-audiencias" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚖️ Audiências</span>
                <a href="{{ route('audiencias') }}" class="btn btn-secondary btn-sm">Ver Todas</a>
            </div>
            @if($processo->audiencias->isEmpty())
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">Nenhuma audiência cadastrada.</p>
            @else
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($processo->audiencias->sortByDesc('data_hora') as $aud)
                <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden">
                    {{-- Cabeçalho da audiência --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:{{ $aud->statusBg() }};flex-wrap:wrap;gap:8px">
                        <div style="display:flex;align-items:center;gap:10px">
                            <span style="font-size:15px;font-weight:800;color:{{ $aud->statusCor() }}">
                                {{ $aud->data_hora->format('d/m/Y') }}
                            </span>
                            <span style="font-size:13px;color:var(--muted)">{{ $aud->data_hora->format('H:i') }}</span>
                            <span style="background:{{ $aud->statusBg() }};color:{{ $aud->statusCor() }};border:1px solid {{ $aud->statusCor() }}33;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase">
                                {{ ucfirst($aud->status) }}
                            </span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="background:var(--bg);border:1px solid var(--border);padding:2px 10px;border-radius:20px;font-size:11px;color:var(--muted)">
                                {{ $aud->tipoLabel() }}
                            </span>
                            @if($aud->data_hora->isFuture())
                                <span class="badge" style="background:#fef9c3;color:#854d0e">Em {{ $aud->data_hora->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Corpo --}}
                    <div style="padding:12px 14px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;font-size:13px">
                        @if($aud->local)
                        <div>
                            <span style="color:var(--muted);font-size:11px;display:block">Local</span>
                            <span style="font-weight:600">{{ $aud->local }}{{ $aud->sala ? ' — Sala '.$aud->sala : '' }}</span>
                        </div>
                        @endif
                        @if($aud->juiz)
                        <div>
                            <span style="color:var(--muted);font-size:11px;display:block">Juiz</span>
                            <span>{{ $aud->juiz->nome }}</span>
                        </div>
                        @endif
                        @if($aud->advogado)
                        <div>
                            <span style="color:var(--muted);font-size:11px;display:block">Advogado</span>
                            <span>{{ $aud->advogado->nome }}</span>
                        </div>
                        @endif
                        @if($aud->preposto)
                        <div>
                            <span style="color:var(--muted);font-size:11px;display:block">Preposto</span>
                            <span>{{ $aud->preposto }}</span>
                        </div>
                        @endif
                        @if($aud->resultado)
                        <div>
                            <span style="color:var(--muted);font-size:11px;display:block">Resultado</span>
                            <span style="font-weight:600;color:var(--primary)">{{ $aud->resultadoLabel() }}</span>
                        </div>
                        @endif
                    </div>

                    @if($aud->pauta || $aud->resultado_descricao || $aud->proximo_passo)
                    <div style="padding:0 14px 12px;display:flex;flex-direction:column;gap:6px;font-size:12px">
                        @if($aud->pauta)
                        <div style="background:#f8fafc;border-radius:6px;padding:8px 10px;color:var(--text)">
                            <strong style="color:var(--muted)">Pauta:</strong> {{ $aud->pauta }}
                        </div>
                        @endif
                        @if($aud->resultado_descricao)
                        <div style="background:#f0fdf4;border-radius:6px;padding:8px 10px;color:#166534">
                            <strong>Resultado:</strong> {{ $aud->resultado_descricao }}
                        </div>
                        @endif
                        @if($aud->proximo_passo)
                        <div style="background:#eff6ff;border-radius:6px;padding:8px 10px;color:#1e40af">
                            <strong>Próximo passo:</strong> {{ $aud->proximo_passo }}
                            @if($aud->data_proximo)
                                — <strong>{{ $aud->data_proximo->format('d/m/Y') }}</strong>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── ABA: AGENDA ── --}}
    <div id="tab-agenda" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Agenda do Processo</span>
                <a href="{{ route('agenda') }}" class="btn btn-secondary btn-sm">Ver Agenda Completa</a>
            </div>
            @if($processo->agenda->isEmpty())
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">Nenhum compromisso na agenda.</p>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Data/Hora</th><th>Evento</th><th>Tipo</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($processo->agenda->sortBy('data_hora') as $ev)
                        <tr>
                            <td style="white-space:nowrap;font-weight:600;">{{ $ev->data_hora->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ $ev->titulo }}
                                @if($ev->urgente) <span class="badge" style="background:#fee2e2;color:#dc2626;margin-left:6px;">URGENTE</span> @endif
                                @if($ev->local) <br><small style="color:var(--muted);">{{ $ev->local }}</small> @endif
                            </td>
                            <td><span class="badge" style="background:#2563a822;color:#2563a8;">{{ $ev->tipo }}</span></td>
                            <td>
                                @if($ev->concluido)
                                    <span class="badge" style="background:#dcfce7;color:#16a34a;">Concluido</span>
                                @else
                                    <span class="badge" style="background:#fef9c3;color:#854d0e;">Pendente</span>
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

    {{-- ── ABA: PRAZOS ── --}}
    <div id="tab-prazos" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Prazos do Processo</span>
                <a href="{{ route('prazos') }}" class="btn btn-secondary btn-sm">Ver Todos os Prazos</a>
            </div>
            @if($prazos->isEmpty())
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">Nenhum prazo cadastrado.</p>
            @else
            @php
            $urgCor = [
                'urgente' => ['bg'=>'#fef2f2','brd'=>'#fca5a5','txt'=>'#dc2626','label'=>'URGENTE'],
                'vencido' => ['bg'=>'#fff7ed','brd'=>'#fdba74','txt'=>'#c2410c','label'=>'VENCIDO'],
                'atencao' => ['bg'=>'#fffbeb','brd'=>'#fde68a','txt'=>'#d97706','label'=>'ATENCAO'],
                'alerta'  => ['bg'=>'#f0f9ff','brd'=>'#93c5fd','txt'=>'#2563a8','label'=>'ALERTA'],
                'normal'  => ['bg'=>'#f8fafc','brd'=>'#e2e8f0','txt'=>'#64748b','label'=>''],
                'cumprido'=> ['bg'=>'#f0fdf4','brd'=>'#bbf7d0','txt'=>'#16a34a','label'=>'CUMPRIDO'],
                'perdido' => ['bg'=>'#fef2f2','brd'=>'#fecaca','txt'=>'#991b1b','label'=>'PERDIDO'],
            ];
            @endphp
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($prazos as $pz)
                @php $u = $urgCor[$pz->urgencia()] ?? $urgCor['normal']; @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:8px;background:{{ $u['bg'] }};border:1px solid {{ $u['brd'] }};">
                    <div style="min-width:60px;text-align:center;">
                        <div style="font-size:15px;font-weight:700;color:{{ $u['txt'] }};">{{ $pz->data_prazo->format('d/m') }}</div>
                        <div style="font-size:10px;color:{{ $u['txt'] }};opacity:.8;">{{ $pz->data_prazo->format('Y') }}</div>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:#1e293b;">
                            @if($pz->prazo_fatal)<span style="color:#dc2626;margin-right:4px;">&#9888;</span>@endif
                            {{ $pz->titulo }}
                        </div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                            {{ $pz->tipo }}
                            @if($pz->responsavel) &nbsp;&middot;&nbsp; {{ $pz->responsavel->nome ?? $pz->responsavel->login }} @endif
                        </div>
                    </div>
                    <div style="text-align:right;">
                        @if($u['label'])
                        <span style="padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $u['brd'] }}44;color:{{ $u['txt'] }};">
                            {{ $u['label'] }}
                        </span>
                        @endif
                        @php $dias = $pz->diasRestantes(); @endphp
                        @if(in_array($pz->status, ['aberto']))
                        <div style="font-size:11px;color:{{ $u['txt'] }};margin-top:3px;font-weight:600;">
                            @if($dias < 0) {{ abs($dias) }}d em atraso
                            @elseif($dias === 0) Hoje
                            @else {{ $dias }}d restantes
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── ABA: FINANCEIRO ── --}}
    <div id="tab-financeiro" class="tab-content" style="display:none;">
        @livewire('financeiro', ['processoId' => $processo->id])
    </div>

    {{-- ── ABA: CHECKLIST ── --}}
    <div id="tab-checklist" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">✅ Checklist de Tarefas</span>
            </div>
            @livewire('processo-checklist', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: MINUTAS ── --}}
    <div id="tab-minutas" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📄 Gerar Minuta</span>
                <a href="{{ route('minutas') }}" class="btn btn-secondary btn-sm" target="_blank">Gerenciar Templates</a>
            </div>
            @livewire('processo-minuta', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: HISTÓRICO DE FASES ── --}}
    <div id="tab-historico_fases" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📋 Histórico de Fases</span>
            </div>
            @livewire('processo-historico-fases', ['processoId' => $processo->id])
        </div>
    </div>

    {{-- ── ABA: APONTAMENTOS DE HORAS ── --}}
    <div id="tab-apontamentos" class="tab-content" style="display:none;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">⏱️ Apontamento de Horas</span>
            </div>
            @livewire('processo-apontamentos', ['processoId' => $processo->id])
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
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">Nenhum documento vinculado a este processo.</p>
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
