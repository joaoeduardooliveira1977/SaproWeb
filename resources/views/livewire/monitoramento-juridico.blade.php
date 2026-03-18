<div>

{{-- KPIs ──────────────────────────────────────────────────────── --}}
<div class="stat-grid" style="margin-bottom:20px;">
    <div class="stat-card" style="border-left-color:#2563a8">
        <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#2563a8" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
        <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Publicações hoje</div>
        <div style="font-size:26px;font-weight:800;color:#2563a8;margin-top:4px">{{ $pubsHoje }}</div>
    </div>
    <div class="stat-card" style="border-left-color:#16a34a">
        <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
        <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Andamentos hoje</div>
        <div style="font-size:26px;font-weight:800;color:#16a34a;margin-top:4px">{{ $andamentosHoje }}</div>
        <div style="font-size:11px;color:#64748b;margin-top:2px">{{ $andamentosSemana }} nos últimos 7 dias</div>
    </div>
    <div class="stat-card" style="border-left-color:{{ $prazosCriticos > 0 ? '#dc2626' : '#64748b' }}">
        <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="{{ $prazosCriticos > 0 ? '#dc2626' : '#64748b' }}" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Prazos críticos (≤3d)</div>
        <div style="font-size:26px;font-weight:800;color:{{ $prazosCriticos > 0 ? '#dc2626' : '#64748b' }};margin-top:4px">{{ $prazosCriticos }}</div>
        @if($prazosFatais > 0)
            <div style="font-size:11px;color:#dc2626;font-weight:600;margin-top:2px">{{ $prazosFatais }} fatal(is)</div>
        @endif
    </div>
    <div class="stat-card" style="border-left-color:{{ $processosSemUpdate > 0 ? '#d97706' : '#64748b' }}">
        <div class="stat-icon"><svg aria-hidden="true" width="20" height="20" fill="none" stroke="{{ $processosSemUpdate > 0 ? '#d97706' : '#64748b' }}" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Sem consulta 30d+</div>
        <div style="font-size:26px;font-weight:800;color:{{ $processosSemUpdate > 0 ? '#d97706' : '#64748b' }};margin-top:4px">{{ $processosSemUpdate }}</div>
    </div>
</div>

{{-- Abas ──────────────────────────────────────────────────────── --}}
<div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:16px;">
    @foreach(['resumo'=>'Resumo','publicacoes'=>'Publicações AASP','andamentos'=>'Andamentos DataJud','alertas'=>'Alertas'] as $tab => $label)
    <button wire:click="$set('aba','{{ $tab }}')"
        style="padding:9px 18px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;
               border-bottom:2px solid {{ $aba===$tab ? 'var(--primary)' : 'transparent' }};
               color:{{ $aba===$tab ? 'var(--primary)' : 'var(--muted)' }};margin-bottom:-2px;">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ── RESUMO ──────────────────────────────────────────────────── --}}
@if($aba === 'resumo')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- Última verificação DataJud --}}
    <div class="card">
        <div class="card-header" style="font-weight:600;display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Última verificação DataJud
        </div>
        @if($ultimaVerif)
        <div style="padding:0 16px 16px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;">
                <span style="color:var(--muted)">Concluída em</span>
                <strong>{{ $ultimaVerif->concluido_em?->format('d/m/Y H:i') ?? '—' }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;">
                <span style="color:var(--muted)">Processos consultados</span>
                <strong>{{ $ultimaVerif->processado }}/{{ $ultimaVerif->total }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;">
                <span style="color:var(--muted)">Novos andamentos</span>
                <strong style="color:#16a34a">{{ $ultimaVerif->novos_total ?? 0 }}</strong>
            </div>
            @if(($ultimaVerif->prazos_criados ?? 0) > 0)
            <div style="display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:var(--muted)">Prazos criados auto.</span>
                <strong style="color:#d97706">{{ $ultimaVerif->prazos_criados }}</strong>
            </div>
            @endif
        </div>
        @else
        <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">Nenhuma verificação realizada ainda.</div>
        @endif
        <div style="padding:8px 16px;border-top:1px solid var(--border);">
            <a href="{{ route('tjsp') }}" style="font-size:12px;color:var(--primary);display:flex;align-items:center;gap:4px;">
                <svg aria-hidden="true" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                Ir para TJSP/DataJud
            </a>
        </div>
    </div>

    {{-- Prazos críticos --}}
    <div class="card">
        <div class="card-header" style="font-weight:600;display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Prazos vencendo em 3 dias
        </div>
        @php
            $proxPrazos = \Illuminate\Support\Facades\DB::table('prazos as pr')
                ->leftJoin('processos as p', 'p.id', '=', 'pr.processo_id')
                ->leftJoin('pessoas as pe', 'pe.id', '=', 'p.cliente_id')
                ->select('pr.titulo','pr.data_prazo','pr.prazo_fatal','p.numero','pe.nome as cliente')
                ->where('pr.status', 'aberto')
                ->whereBetween('pr.data_prazo', [today(), today()->addDays(3)])
                ->orderBy('pr.data_prazo')
                ->limit(8)->get();
        @endphp
        @forelse($proxPrazos as $pz)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 16px;border-bottom:1px solid var(--border);font-size:13px;">
            @if($pz->prazo_fatal)
                <span style="background:#fee2e2;color:#dc2626;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;flex-shrink:0;">FATAL</span>
            @else
                <span style="background:#fff7ed;color:#d97706;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;flex-shrink:0;">PRAZO</span>
            @endif
            <div style="flex:1;min-width:0;">
                <div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $pz->titulo }}</div>
                <div style="font-size:11px;color:var(--muted);">{{ $pz->numero ?? '—' }} · {{ $pz->cliente ?? '—' }}</div>
            </div>
            <span style="color:#dc2626;font-weight:600;flex-shrink:0;font-size:12px;">
                {{ \Carbon\Carbon::parse($pz->data_prazo)->format('d/m') }}
            </span>
        </div>
        @empty
        <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">Nenhum prazo crítico nos próximos 3 dias.</div>
        @endforelse
    </div>

</div>

{{-- Processos desatualizados --}}
@if($processosSemUpdate > 0)
<div class="card" style="margin-top:16px;">
    <div class="card-header" style="font-weight:600;display:flex;align-items:center;gap:6px;">
        <svg aria-hidden="true" width="14" height="14" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
        Processos sem consulta DataJud há 30+ dias ({{ $processosSemUpdate }} total)
    </div>
    <div class="table-wrap" style="margin:0;">
        <table>
            <thead><tr>
                <th>Processo</th><th>Cliente</th><th>Última consulta</th>
            </tr></thead>
            <tbody>
            @foreach($processosDesatualizados as $p)
            <tr>
                <td><a href="{{ route('processos.show', $p->id) }}" style="color:var(--primary);font-weight:600;">{{ $p->numero }}</a></td>
                <td style="color:var(--muted);">{{ $p->cliente?->nome ?? '—' }}</td>
                <td style="color:#d97706;">{{ $p->tjsp_ultima_consulta ? \Carbon\Carbon::parse($p->tjsp_ultima_consulta)->format('d/m/Y') : 'Nunca' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif

{{-- ── PUBLICAÇÕES AASP ────────────────────────────────────────── --}}
@if($aba === 'publicacoes')
<div class="card" style="padding:0;">
    <div class="card-header" style="font-weight:600;">Publicações AASP vinculadas a processos do sistema</div>
    @if($publicacoesVinculadas->isEmpty())
        <div style="padding:40px;text-align:center;color:var(--muted);">
            <div style="font-size:14px;">Nenhuma publicação vinculada ainda.</div>
            <div style="font-size:12px;margin-top:4px;">As publicações são vinculadas automaticamente quando o número do processo coincide com um cadastrado no sistema.</div>
        </div>
    @else
    <div class="table-wrap" style="margin:0;">
        <table>
            <thead><tr><th>Data</th><th>Processo</th><th>Cliente</th><th>Título</th><th>Jornal</th></tr></thead>
            <tbody>
            @foreach($publicacoesVinculadas as $pub)
            <tr>
                <td style="white-space:nowrap;font-size:12px;">{{ $pub->data?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    @if($pub->processo)
                        <a href="{{ route('processos.show', $pub->processo->id) }}" style="color:var(--primary);font-weight:600;font-size:12px;">{{ $pub->processo->numero }}</a>
                    @else
                        <span style="font-size:12px;color:var(--muted);">{{ $pub->numero_processo }}</span>
                    @endif
                </td>
                <td style="font-size:12px;color:var(--muted);">{{ $pub->processo?->cliente?->nome ?? '—' }}</td>
                <td style="font-size:12px;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $pub->titulo ?: '—' }}</td>
                <td style="font-size:11px;color:var(--muted);">{{ $pub->jornal ?? '—' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

{{-- ── ANDAMENTOS DATAJUD ──────────────────────────────────────── --}}
@if($aba === 'andamentos')
<div class="card" style="padding:0;">
    <div class="card-header" style="font-weight:600;">Andamentos importados nos últimos 7 dias ({{ $andamentosRecentes->count() }})</div>
    @if($andamentosRecentes->isEmpty())
        <div style="padding:40px;text-align:center;color:var(--muted);font-size:14px;">Nenhum andamento importado nos últimos 7 dias.</div>
    @else
    <div class="table-wrap" style="margin:0;">
        <table>
            <thead><tr><th>Importado em</th><th>Data and.</th><th>Processo</th><th>Cliente</th><th>Descrição</th></tr></thead>
            <tbody>
            @foreach($andamentosRecentes as $a)
            <tr>
                <td style="font-size:11px;color:var(--muted);white-space:nowrap;">{{ \Carbon\Carbon::parse($a->created_at)->format('d/m H:i') }}</td>
                <td style="font-size:12px;white-space:nowrap;">{{ \Carbon\Carbon::parse($a->data)->format('d/m/Y') }}</td>
                <td style="font-size:12px;font-weight:600;color:var(--primary);">{{ $a->numero }}</td>
                <td style="font-size:12px;color:var(--muted);">{{ $a->cliente_nome ?? '—' }}</td>
                <td style="font-size:12px;max-width:350px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $a->descricao }}">{{ $a->descricao }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

{{-- ── ALERTAS ─────────────────────────────────────────────────── --}}
@if($aba === 'alertas')
<div style="display:flex;flex-direction:column;gap:16px;">

    {{-- Processos sem atualização --}}
    <div class="card" style="padding:0;">
        <div class="card-header" style="font-weight:600;display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
            Processos sem consulta DataJud (30+ dias) — {{ $processosSemUpdate }}
        </div>
        @if($processosDesatualizados->isEmpty())
            <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">Todos os processos foram consultados recentemente.</div>
        @else
        <div class="table-wrap" style="margin:0;">
            <table>
                <thead><tr><th>Processo</th><th>Cliente</th><th>Fase</th><th>Última consulta</th></tr></thead>
                <tbody>
                @foreach($processosDesatualizados as $p)
                <tr>
                    <td><a href="{{ route('processos.show', $p->id) }}" style="color:var(--primary);font-weight:600;">{{ $p->numero }}</a></td>
                    <td style="color:var(--muted);font-size:13px;">{{ $p->cliente?->nome ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $p->fase?->descricao ?? '—' }}</td>
                    <td style="color:#d97706;font-size:12px;">{{ $p->tjsp_ultima_consulta ? \Carbon\Carbon::parse($p->tjsp_ultima_consulta)->format('d/m/Y') : 'Nunca' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Histórico de verificações --}}
    <div class="card">
        <div class="card-header" style="font-weight:600;">Histórico de verificações DataJud</div>
        @foreach($verificacoes as $v)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--border);font-size:13px;">
            @php
                $cor = match($v->status) { 'concluido'=>'#16a34a','rodando'=>'#2563a8','erro'=>'#dc2626',default=>'#64748b' };
            @endphp
            <span style="background:{{ $cor }}20;color:{{ $cor }};padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper($v->status) }}</span>
            <span style="color:var(--muted);flex-shrink:0;">{{ $v->iniciado_em?->format('d/m/Y H:i') ?? '—' }}</span>
            <span>{{ $v->processado }}/{{ $v->total }} processos</span>
            @if($v->novos_total)
                <span style="color:#16a34a;font-weight:600;">{{ $v->novos_total }} novos</span>
            @endif
            <span style="flex:1"></span>
            @if($v->concluido_em && $v->iniciado_em)
                <span style="color:var(--muted);font-size:11px;">{{ $v->iniciado_em->diffInMinutes($v->concluido_em) }}min</span>
            @endif
        </div>
        @endforeach
    </div>

</div>
@endif

</div>
