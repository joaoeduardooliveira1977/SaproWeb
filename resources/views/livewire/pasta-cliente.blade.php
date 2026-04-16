<div>
<style>
.pasta-header   { background:var(--white); border-radius:12px; padding:24px; border:1px solid var(--border); box-shadow:0 1px 6px rgba(0,0,0,.07); margin-bottom:20px; }
.pasta-avatar   { width:56px; height:56px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; flex-shrink:0; }
.pasta-kpis     { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:20px; }
.pasta-kpi      { background:var(--white); border-radius:10px; padding:14px 16px; border:1px solid var(--border); border-left:4px solid; }
.pasta-kpi-val  { font-size:22px; font-weight:700; }
.pasta-kpi-lbl  { font-size:11px; color:var(--muted); margin-top:2px; }
.pasta-tabs     { display:flex; gap:0; margin-bottom:20px; background:var(--white); border-radius:10px; border:1px solid var(--border); overflow:hidden; flex-wrap:wrap; }
.pasta-tab      { padding:10px 18px; font-size:13px; font-weight:600; color:var(--muted); cursor:pointer; border:none; background:transparent; border-bottom:3px solid transparent; transition:all .15s; display:flex; align-items:center; gap:6px; }
.pasta-tab:hover  { color:var(--primary); background:var(--bg); }
.pasta-tab.active { color:var(--primary); border-bottom-color:var(--primary); background:#f0f6ff; }
.pasta-tab-badge  { background:#e2e8f0; color:var(--muted); border-radius:20px; font-size:10px; padding:1px 6px; font-weight:700; }
.pasta-tab.active .pasta-tab-badge { background:var(--primary); color:#fff; }
.pasta-secao    { background:var(--white); border-radius:12px; border:1px solid var(--border); box-shadow:0 1px 6px rgba(0,0,0,.07); overflow:hidden; }
.pasta-info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-top:16px; border-top:1px solid var(--border); padding-top:16px; }
.pasta-info-item { display:flex; flex-direction:column; gap:3px; }
.pasta-info-lbl  { font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
.pasta-info-val  { font-size:13px; color:var(--text); font-weight:500; }
.processo-row   { display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid var(--border); transition:background .12s; }
.processo-row:last-child { border-bottom:none; }
.processo-row:hover { background:#f8faff; }
.processo-numero { font-size:13px; font-weight:700; color:var(--primary-light); min-width:100px; }
.processo-info   { flex:1; min-width:0; }
.processo-titulo { font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.processo-sub    { font-size:12px; color:var(--muted); margin-top:2px; }
.prazo-item      { display:flex; align-items:center; gap:12px; padding:10px 16px; border-bottom:1px solid var(--border); }
.prazo-item:last-child { border-bottom:none; }
.prazo-data      { min-width:80px; text-align:center; }
.prazo-dia-num   { font-size:18px; font-weight:700; }
.prazo-dia-mes   { font-size:11px; color:var(--muted); }
.pasta-empty     { padding:40px 24px; text-align:center; color:var(--muted); font-size:13px; }
.doc-item        { display:flex; align-items:center; gap:12px; padding:10px 16px; border-bottom:1px solid var(--border); }
.doc-item:last-child { border-bottom:none; }
.doc-icon        { width:36px; height:36px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.hist-item       { padding:12px 16px; border-bottom:1px solid var(--border); display:flex; gap:12px; }
.hist-item:last-child { border-bottom:none; }
.hist-dot        { width:8px; height:8px; border-radius:50%; background:var(--primary-light); margin-top:5px; flex-shrink:0; }
@media(max-width:768px){ .pasta-tabs { overflow-x:auto; } .pasta-tab { white-space:nowrap; } }
</style>

<div>
    {{-- Voltar --}}
    <div style="margin-bottom:16px;">
        <a href="{{ route('pessoas') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;"
           onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Clientes
        </a>
    </div>

    {{-- Header do cliente --}}
    <div class="pasta-header">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
            <div class="pasta-avatar">{{ mb_strtoupper(mb_substr($cliente->nome, 0, 2)) }}</div>
            <div style="flex:1;min-width:200px;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">{{ $cliente->nome }}</h2>
                    <span style="font-size:11px;padding:2px 10px;border-radius:20px;background:{{ $cliente->ativo ? '#dcfce7' : '#fee2e2' }};color:{{ $cliente->ativo ? '#166534' : '#991b1b' }};font-weight:700;">
                        {{ $cliente->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <p style="font-size:13px;color:var(--muted);margin:4px 0 0;">Pasta do cliente</p>
            </div>
            <a href="{{ route('pessoas') }}" class="btn btn-sm btn-secondary-outline"
               style="display:flex;align-items:center;gap:5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar
            </a>
        </div>

        {{-- Dados de contato --}}
        <div class="pasta-info-grid">
            @if($cliente->cpf_cnpj)
            <div class="pasta-info-item">
                <span class="pasta-info-lbl">CPF/CNPJ</span>
                <span class="pasta-info-val">{{ $cliente->cpf_cnpj }}</span>
            </div>
            @endif
            @if($cliente->email)
            <div class="pasta-info-item">
                <span class="pasta-info-lbl">E-mail</span>
                <span class="pasta-info-val"><a href="mailto:{{ $cliente->email }}" style="color:var(--primary-light);text-decoration:none;">{{ $cliente->email }}</a></span>
            </div>
            @endif
            @if($cliente->celular)
            <div class="pasta-info-item">
                <span class="pasta-info-lbl">Celular</span>
                <span class="pasta-info-val"><a href="https://wa.me/55{{ preg_replace('/\D/','',$cliente->celular) }}" target="_blank" style="color:#16a34a;text-decoration:none;">{{ $cliente->celular }}</a></span>
            </div>
            @endif
            @if($cliente->cidade)
            <div class="pasta-info-item">
                <span class="pasta-info-lbl">Cidade</span>
                <span class="pasta-info-val">{{ $cliente->cidade }}{{ $cliente->estado ? ' — '.$cliente->estado : '' }}</span>
            </div>
            @endif
            @if($cliente->data_nascimento)
            <div class="pasta-info-item">
                <span class="pasta-info-lbl">Nascimento</span>
                <span class="pasta-info-val">{{ $cliente->data_nascimento->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- KPIs --}}
    <div class="pasta-kpis">
        <div class="pasta-kpi" style="border-left-color:var(--primary);">
            <div class="pasta-kpi-val" style="color:var(--primary);">{{ $totalAtivos }}</div>
            <div class="pasta-kpi-lbl">Processos ativos</div>
        </div>
        <div class="pasta-kpi" style="border-left-color:#dc2626;">
            <div class="pasta-kpi-val" style="color:#dc2626;">{{ $totalPrazosVencidos }}</div>
            <div class="pasta-kpi-lbl">Prazos vencidos</div>
        </div>
        <div class="pasta-kpi" style="border-left-color:#d97706;">
            <div class="pasta-kpi-val" style="color:#d97706;">{{ $totalPrazosHoje }}</div>
            <div class="pasta-kpi-lbl">Prazos hoje</div>
        </div>
        <div class="pasta-kpi" style="border-left-color:#16a34a;">
            <div class="pasta-kpi-val" style="color:#16a34a;">R$ {{ number_format($totalHonorarios, 2, ',', '.') }}</div>
            <div class="pasta-kpi-lbl">Honorários em aberto</div>
        </div>
        @if($valorRisco > 0)
        <div class="pasta-kpi" style="border-left-color:#7c3aed;">
            <div class="pasta-kpi-val" style="color:#7c3aed;">R$ {{ number_format($valorRisco, 0, ',', '.') }}</div>
            <div class="pasta-kpi-lbl">Valor em risco</div>
        </div>
        @endif
    </div>

    {{-- Abas --}}
    <div class="pasta-tabs">
        <button class="pasta-tab {{ $aba === 'processos' ? 'active' : '' }}" wire:click="$set('aba','processos')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Processos
            <span class="pasta-tab-badge">{{ $processos->count() }}</span>
        </button>
        <button class="pasta-tab {{ $aba === 'prazos' ? 'active' : '' }}" wire:click="$set('aba','prazos')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Prazos
            <span class="pasta-tab-badge">{{ $prazos->count() }}</span>
        </button>
        <button class="pasta-tab {{ $aba === 'honorarios' ? 'active' : '' }}" wire:click="$set('aba','honorarios')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Honorários
            <span class="pasta-tab-badge">{{ $parcelas->count() }}</span>
        </button>
        <button class="pasta-tab {{ $aba === 'documentos' ? 'active' : '' }}" wire:click="$set('aba','documentos')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Documentos
            <span class="pasta-tab-badge">{{ $documentos->count() }}</span>
        </button>
        <button class="pasta-tab {{ $aba === 'historico' ? 'active' : '' }}" wire:click="$set('aba','historico')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
            Histórico
        </button>
    </div>

    {{-- Conteúdo das abas --}}
    <div class="pasta-secao">

        {{-- ABA: Processos --}}
        @if($aba === 'processos')
            @if($processos->isEmpty())
                <div class="pasta-empty">Nenhum processo encontrado para este cliente.</div>
            @else
                @foreach($processos as $proc)
                <div class="processo-row">
                    <div class="processo-numero">
                        <a href="{{ route('processos.show', $proc->id) }}" style="color:var(--primary-light);text-decoration:none;">
                            {{ $proc->numero ?? 'S/N' }}
                        </a>
                    </div>
                    <div class="processo-info">
                        <div class="processo-titulo">
                            {{ $proc->tipoAcao?->descricao ?? '—' }}
                        </div>
                        <div class="processo-sub">
                            {{ $proc->fase?->descricao ?? '' }}
                            @if($proc->advogado) &bull; {{ $proc->advogado->nome }} @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        @if($proc->risco)
                        <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:#fff7ed;color:#ea580c;font-weight:600;">
                            {{ $proc->risco->descricao ?? '' }}
                        </span>
                        @endif
                        <span style="font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600;background:{{ $proc->status==='Ativo' ? '#dcfce7' : '#f1f5f9' }};color:{{ $proc->status==='Ativo' ? '#166534' : '#64748b' }};">
                            {{ $proc->status }}
                        </span>
                        <a href="{{ route('processos.show', $proc->id) }}" class="btn-action btn-action-blue" title="Abrir processo">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            @endif
        @endif

        {{-- ABA: Prazos --}}
        @if($aba === 'prazos')
            @if($prazos->isEmpty())
                <div class="pasta-empty">Nenhum prazo em aberto.</div>
            @else
                @foreach($prazos as $p)
                @php
                    $dias = $p->diasRestantes();
                    $urg  = $p->urgencia();
                    $corBorda = match($urg) {
                        'vencido'  => '#dc2626',
                        'critico'  => '#f97316',
                        'atencao'  => '#d97706',
                        default    => '#22c55e',
                    };
                @endphp
                <div class="prazo-item" style="border-left:3px solid {{ $corBorda }};">
                    <div class="prazo-data">
                        <div class="prazo-dia-num" style="color:{{ $corBorda }};">{{ $p->data_prazo->format('d') }}</div>
                        <div class="prazo-dia-mes">{{ $p->data_prazo->format('M/y') }}</div>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">
                            {{ $p->titulo }}
                            @if($p->prazo_fatal)
                                <span style="font-size:10px;background:#fecdd3;color:#e11d48;padding:1px 6px;border-radius:10px;margin-left:5px;font-weight:700;">FATAL</span>
                            @endif
                        </div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            @if($p->processo) Processo {{ $p->processo->numero }} @endif
                            @if($p->responsavel) &bull; {{ $p->responsavel->nome }} @endif
                        </div>
                    </div>
                    <div style="font-size:12px;font-weight:700;color:{{ $corBorda }};white-space:nowrap;">
                        @if($urg === 'vencido') {{ abs($dias) }}d vencido
                        @elseif($dias === 0) Hoje
                        @else {{ $dias }}d restantes
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        @endif

        {{-- ABA: Honorários --}}
        @if($aba === 'honorarios')
            @if($parcelas->isEmpty())
                <div class="pasta-empty">Nenhuma parcela em aberto.</div>
            @else
                <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Processo</th>
                            <th>Parcela</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parcelas as $par)
                        @php $atrasado = $par->vencimento->isPast() && $par->status === 'pendente'; @endphp
                        <tr>
                            <td>{{ $par->honorario?->processo?->numero ?? '—' }}</td>
                            <td>Parcela {{ $par->numero_parcela }}</td>
                            <td style="color:{{ $atrasado ? '#dc2626' : 'var(--text)' }};font-weight:{{ $atrasado ? '700' : '400' }};">
                                {{ $par->vencimento->format('d/m/Y') }}
                                @if($atrasado) <span style="font-size:10px;">({{ $par->vencimento->diffInDays(now()) }}d)</span> @endif
                            </td>
                            <td style="font-weight:600;">R$ {{ number_format($par->valor, 2, ',', '.') }}</td>
                            <td>
                                <span style="font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600;background:{{ $par->status==='vencido' ? '#fee2e2' : '#fef3c7' }};color:{{ $par->status==='vencido' ? '#991b1b' : '#92400e' }};">
                                    {{ ucfirst($par->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="font-weight:700;color:var(--text);">Total em aberto</td>
                            <td style="font-weight:700;color:#dc2626;">R$ {{ number_format($totalHonorarios, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            @endif
        @endif

        {{-- ABA: Documentos --}}
        @if($aba === 'documentos')
            {{-- Cabeçalho da aba --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <span style="font-size:13px;color:var(--muted);">{{ $documentos->count() }} documento(s)</span>
                <button wire:click="abrirModalDoc()"
                    style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Anexar Documento
                </button>
            </div>

            @if($documentos->isEmpty())
                <div class="pasta-empty">Nenhum documento encontrado. Clique em "Anexar Documento" para adicionar.</div>
            @else
                @foreach($documentos as $doc)
                <div class="doc-item">
                    <div class="doc-icon">
                        @php
                            $mime = $doc->mime_type ?? '';
                            $iconColor = str_contains($mime,'pdf') ? '#dc2626' : (str_contains($mime,'image') ? '#7c3aed' : '#64748b');
                        @endphp
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $doc->titulo }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            @if($doc->tipo)<span style="background:#f1f5f9;padding:1px 6px;border-radius:4px;">{{ $tiposDoc[$doc->tipo] ?? $doc->tipo }}</span>@endif
                            @if($doc->data_documento)<span>{{ $doc->data_documento->format('d/m/Y') }}</span>@endif
                            @if($doc->processo)<span>&bull; Proc. {{ $doc->processo->numero }}</span>@endif
                            @if($doc->tamanho)<span style="color:#94a3b8;">{{ number_format($doc->tamanho / 1024, 0, ',', '.') }} KB</span>@endif
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0;">
                        @if($doc->arquivo)
                        <a href="/storage/{{ $doc->arquivo }}" target="_blank" class="btn-action btn-action-blue" title="Baixar / Visualizar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </a>
                        @endif
                        <button wire:click="abrirModalDoc({{ $doc->id }})" class="btn-action" title="Editar" style="background:#f1f5f9;border:none;border-radius:6px;padding:5px 7px;cursor:pointer;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button wire:click="excluirDocumento({{ $doc->id }})"
                            wire:confirm="Excluir este documento permanentemente?"
                            class="btn-action" title="Excluir" style="background:#fef2f2;border:none;border-radius:6px;padding:5px 7px;cursor:pointer;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            @endif
        @endif

        {{-- ABA: Histórico --}}
        @if($aba === 'historico')
            @if($historico->isEmpty())
                <div class="pasta-empty">Nenhum andamento registrado.</div>
            @else
                @foreach($historico as $and)
                <div class="hist-item">
                    <div class="hist-dot" style="margin-top:6px;"></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;color:var(--muted);margin-bottom:3px;">
                            {{ $and->created_at->format('d/m/Y H:i') }}
                            @if($and->processo)
                                &bull; <a href="{{ route('processos.show', $and->processo_id) }}" style="color:var(--primary-light);text-decoration:none;">Proc. {{ $and->processo->numero }}</a>
                            @endif
                        </div>
                        <div style="font-size:13px;color:var(--text);line-height:1.5;">{{ $and->descricao }}</div>
                    </div>
                </div>
                @endforeach
            @endif
        @endif

    </div>
</div>

{{-- ── Modal: Anexar / Editar Documento ── --}}
@if($modalDoc)
<div style="position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharModalDoc()" style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.2);z-index:1;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">
                {{ $docId ? 'Editar Documento' : 'Anexar Documento' }}
            </h3>
            <button wire:click="fecharModalDoc()" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:22px;display:flex;flex-direction:column;gap:14px;">

            {{-- Título --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Título <span style="color:var(--danger);">*</span></label>
                <input wire:model="docTitulo" type="text" placeholder="Ex: Contrato de honorários"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('docTitulo')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>

            {{-- Tipo + Data (linha) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Tipo</label>
                    <select wire:model="docTipo"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        @foreach($tiposDoc as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Data do Documento</label>
                    <input wire:model="docData" type="date"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>

            {{-- Descrição --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Descrição <span style="font-weight:400;color:var(--muted);">(opcional)</span></label>
                <textarea wire:model="docDescricao" rows="2" placeholder="Observações sobre o documento..."
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
            </div>

            {{-- Arquivo --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">
                    Arquivo {{ $docId ? '(deixe em branco para manter o atual)' : '*' }}
                </label>
                <input wire:model="docArquivo" type="file"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;cursor:pointer;">
                @error('docArquivo')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                <div wire:loading wire:target="docArquivo" style="font-size:11px;color:var(--muted);margin-top:4px;">Carregando arquivo...</div>
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Tamanho máximo: 20 MB</div>
            </div>

        </div>

        {{-- Footer --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--border);">
            <button wire:click="fecharModalDoc()"
                style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;cursor:pointer;color:var(--text);">
                Cancelar
            </button>
            <button wire:click="salvarDocumento()" wire:loading.attr="disabled"
                style="padding:9px 20px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <span wire:loading.remove wire:target="salvarDocumento">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span wire:loading wire:target="salvarDocumento" style="font-size:11px;">Salvando...</span>
                {{ $docId ? 'Salvar Alterações' : 'Anexar' }}
            </button>
        </div>

    </div>
</div>
@endif

</div>{{-- fim raiz Livewire --}}
