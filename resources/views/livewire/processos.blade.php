<div>
<style>
@media (max-width: 768px) {
    .metricas-grid { grid-template-columns: 1fr 1fr !important; }
    .filtros-bar   { flex-wrap: wrap; }
}
@media (max-width: 480px) {
    .metricas-grid { grid-template-columns: 1fr !important; }
}
</style>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <a href="{{ route('processos.hub') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:6px;"
           onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar
        </a>
        <h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">Processos</h2>
        <p style="font-size:13px;color:var(--muted);margin:2px 0 0;">
            {{ $processos->total() }} processo{{ $processos->total() !== 1 ? 's' : '' }} encontrado{{ $processos->total() !== 1 ? 's' : '' }}
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        <button wire:click="exportarCsv" wire:loading.attr="disabled"
            class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando...</span>
        </button>
        <a href="{{ route('processos.novo') }}" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Processo
        </a>
    </div>
</div>

{{-- ── Analista IA ── --}}
@if(tenant_pode('ia'))
<div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:12px;padding:14px 20px;margin-bottom:12px;display:flex;align-items:center;gap:12px;">
    <div style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            <path d="M18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
        </svg>
    </div>
    <input
        wire:model="perguntaIA"
        wire:keydown.enter="perguntarIA"
        type="text"
        placeholder="Pergunte sobre os processos... Ex: processos de risco alto, vencendo essa semana, processos do Dr. Carlos"
        style="flex:1;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;padding:10px 16px;color:#fff;font-size:13px;outline:none;"
        onfocus="this.style.borderColor='rgba(147,197,253,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.2)'">
    <button wire:click="perguntarIA" wire:loading.attr="disabled" wire:target="perguntarIA"
        style="background:#2563a8;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;transition:background .15s;"
        onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563a8'">
        <span wire:loading.remove wire:target="perguntarIA" style="display:flex;align-items:center;gap:6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            </svg>
            Analisar
        </span>
        <span wire:loading wire:target="perguntarIA">Analisando...</span>
    </button>
</div>
@else
<div style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#f1f5f9;border:1.5px solid #e2e8f0;border-radius:8px;color:#94a3b8;font-size:12px;cursor:not-allowed;margin-bottom:12px;"
    title="IA disponível nos planos Starter e Pro">
    🔒 IA — Upgrade necessário
</div>
@endif

@if($respostaIA)
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#1e40af;display:flex;gap:10px;align-items:flex-start;">
    <div style="flex-shrink:0;width:28px;height:28px;background:#dbeafe;border-radius:6px;display:flex;align-items:center;justify-content:center;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
        </svg>
    </div>
    <div style="flex:1;">
        <div style="font-weight:700;margin-bottom:4px;font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#1d4ed8;">Analista IA</div>
        <div style="line-height:1.6;">{{ $respostaIA }}</div>
    </div>
    <button wire:click="limparIA"
        style="background:none;border:none;color:#93c5fd;cursor:pointer;font-size:18px;line-height:1;padding:0 4px;flex-shrink:0;"
        title="Fechar">&times;</button>
</div>
@endif

{{-- ── Filtros horizontais ── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:16px;">

    {{-- Linha 1 --}}
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:12px;align-items:end;margin-bottom:10px;">

        {{-- Busca --}}
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">Busca</label>
            <div style="position:relative;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input wire:model.live.debounce.300ms="busca" type="text"
                    placeholder="Número, cliente, advogado..."
                    style="width:100%;padding:8px 10px 8px 32px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--bg);color:var(--text);outline:none;transition:border-color .2s;"
                    onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
            </div>
        </div>

        {{-- Status --}}
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">Status</label>
            <select wire:model.live="status"
                style="width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--bg);color:var(--text);outline:none;cursor:pointer;">
                <option value="">Todos</option>
                <option value="Ativo">Ativo</option>
                <option value="Arquivado">Arquivado</option>
                <option value="Encerrado">Encerrado</option>
            </select>
        </div>

        {{-- Fase --}}
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">Fase</label>
            <select wire:model.live="fase_id"
                style="width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--bg);color:var(--text);outline:none;cursor:pointer;">
                <option value="">Todas as fases</option>
                @foreach($fases as $f)
                <option value="{{ $f->id }}">{{ $f->descricao }}</option>
                @endforeach
            </select>
        </div>

        {{-- Risco --}}
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">Risco</label>
            <select wire:model.live="risco_id"
                style="width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--bg);color:var(--text);outline:none;cursor:pointer;">
                <option value="">Todos</option>
                @foreach($riscos as $r)
                <option value="{{ $r->id }}">{{ $r->descricao }}</option>
                @endforeach
            </select>
        </div>

        {{-- Limpar --}}
        <div>
            <label style="font-size:11px;font-weight:700;color:transparent;display:block;margin-bottom:6px;">.</label>
            <button wire:click="$set('busca',''); $set('status',''); $set('fase_id',''); $set('risco_id','')"
                style="width:100%;padding:8px 14px;background:var(--bg);border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--muted);cursor:pointer;font-weight:600;white-space:nowrap;transition:all .15s;"
                onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                Limpar filtros
            </button>
        </div>
    </div>

    {{-- Linha 2: chips de filtros ativos + contador --}}
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;padding-top:10px;border-top:1px solid var(--border);">

        @if($status)
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:20px;font-size:12px;font-weight:600;color:#1d4ed8;">
            Status: {{ $status }}
            <button wire:click="$set('status','')" style="background:none;border:none;cursor:pointer;color:#93c5fd;font-size:14px;line-height:1;padding:0;">&times;</button>
        </span>
        @endif

        @if($fase_id)
        @php $faseAtiva = $fases->firstWhere('id', $fase_id); @endphp
        @if($faseAtiva)
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:20px;font-size:12px;font-weight:600;color:#1d4ed8;">
            Fase: {{ $faseAtiva->descricao }}
            <button wire:click="$set('fase_id','')" style="background:none;border:none;cursor:pointer;color:#93c5fd;font-size:14px;line-height:1;padding:0;">&times;</button>
        </span>
        @endif
        @endif

        @if($risco_id)
        @php $riscoAtivo = $riscos->firstWhere('id', $risco_id); @endphp
        @if($riscoAtivo)
        @php $corRisco = $riscoAtivo->cor_hex ?? '#94a3b8'; @endphp
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:{{ $corRisco }}18;border:1px solid {{ $corRisco }}44;border-radius:20px;font-size:12px;font-weight:600;color:{{ $corRisco }};">
            Risco: {{ $riscoAtivo->descricao }}
            <button wire:click="$set('risco_id','')" style="background:none;border:none;cursor:pointer;color:{{ $corRisco }};opacity:.6;font-size:14px;line-height:1;padding:0;">&times;</button>
        </span>
        @endif
        @endif

        <div style="margin-left:auto;display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
            <strong style="color:var(--text);font-size:13px;">{{ $processos->total() }}</strong> processo(s)
        </div>
    </div>
</div>

{{-- ── Conteúdo ── --}}
<div>

        {{-- Metricas --}}
        <div class="metricas-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-bottom:16px;">

            {{-- Card 1: Total Ativos --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6l9-3 9 3v6c0 5.25-3.75 9.74-9 11-5.25-1.26-9-5.75-9-11V6Z"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1.1;">{{ number_format($totalAtivos) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">em andamento</div>
                </div>
            </div>

            {{-- Card 2: Valor Total --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div style="min-width:0;">
                    @php
                        $vf = $valorTotal >= 1000000
                            ? 'R$ ' . number_format($valorTotal/1000000, 1, ',', '.') . 'M'
                            : ($valorTotal >= 1000 ? 'R$ ' . number_format($valorTotal/1000, 0, ',', '.') . 'K' : 'R$ ' . number_format($valorTotal, 0, ',', '.'));
                    @endphp
                    <div style="font-size:20px;font-weight:800;color:var(--text);line-height:1.1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="R$ {{ number_format($valorTotal, 2, ',', '.') }}">{{ $vf }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">valor em litigio</div>
                </div>
            </div>

            {{-- Card 3: Risco Alto --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#ea580c;line-height:1.1;">{{ number_format($riscoAlto) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">risco alto</div>
                </div>
            </div>

            {{-- Card 4: Parados --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#fefce8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#ca8a04;line-height:1.1;">{{ number_format($parados) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">sem mov. 30d</div>
                </div>
            </div>

        </div>

        {{-- Tabela --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="table-wrap">
                <table style="border-collapse:collapse;width:100%;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);">
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Processo</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Cliente / Parte Contraria</th>
                            <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Fase / Risco</th>
                            <th class="hide-md" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Advogado</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:170px;">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processos as $p)
                        @php
                            $statusCor = match($p->status) {
                                'Ativo'     => ['bg' => '#dcfce7', 'text' => '#16a34a'],
                                'Arquivado' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                'Encerrado' => ['bg' => '#fef3c7', 'text' => '#d97706'],
                                default     => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                            };
                            $riscoCor = $p->risco?->cor_hex ?? '#94a3b8';
                        @endphp
                        <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">

                            <td style="padding:14px 16px;">
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <a href="{{ route('processos.show', $p->id) }}"
                                        style="font-weight:700;font-size:14px;color:#2563a8;text-decoration:none;letter-spacing:.3px;">
                                        {{ $p->numero }}
                                    </a>
                                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $statusCor['bg'] }};color:{{ $statusCor['text'] }};">
                                        {{ $p->status }}
                                    </span>
                                    @if($p->updated_at?->isToday())
                                    <span title="Modificado hoje" style="padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;background:#dbeafe;color:#1d4ed8;letter-spacing:.2px;">hoje</span>
                                    @elseif($p->updated_at?->isYesterday())
                                    <span title="Modificado ontem" style="padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;background:#f1f5f9;color:#64748b;letter-spacing:.2px;">ontem</span>
                                    @endif
                                </div>
                                @if($p->parte_contraria)
                                <div style="font-size:11px;color:#64748b;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">
                                    ⚖ {{ $p->parte_contraria }}
                                </div>
                                @endif
                                @if($p->data_distribuicao)
                                <div style="font-size:11px;color:var(--muted);margin-top:3px;">
                                    Distribuido em {{ $p->data_distribuicao->format('d/m/Y') }}
                                </div>
                                @endif
                            </td>

                            <td style="padding:14px 16px;">
                                <div style="font-size:13px;font-weight:600;color:var(--text);">
                                    {{ $p->cliente?->nome ?? '—' }}
                                </div>
                                @if($p->parteContraria?->nome || $p->parte_contraria)
                                <div style="font-size:12px;color:var(--muted);margin-top:2px;display:flex;align-items:center;gap:4px;">
                                    <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $p->parteContraria?->nome ?? $p->parte_contraria }}
                                </div>
                                @endif
                            </td>

                            <td class="hide-sm" style="padding:14px 16px;">
                                <div style="display:flex;flex-direction:column;gap:5px;">
                                    @if($p->fase)
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:600;background:#eff6ff;color:#1e40af;width:fit-content;">
                                        <svg aria-hidden="true" width="9" height="9" viewBox="0 0 24 24" fill="#1e40af"><circle cx="12" cy="12" r="10"/></svg>
                                        {{ $p->fase->descricao }}
                                    </span>
                                    @endif
                                    @if($p->risco)
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $riscoCor }}22;color:{{ $riscoCor }};border:1px solid {{ $riscoCor }}44;width:fit-content;">
                                        <svg aria-hidden="true" width="9" height="9" viewBox="0 0 24 24" fill="{{ $riscoCor }}"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                                        {{ $p->risco->descricao }}
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <td class="hide-md" style="padding:14px 16px;font-size:13px;color:var(--text);">
                                {{ $p->advogado?->nome ?? '—' }}
                            </td>

                            <td style="padding:14px 16px;text-align:center;">
                                <div style="display:flex;justify-content:center;gap:4px;">

                                    <a href="{{ route('processos.show', $p->id) }}#analise-ia" title="Analise IA"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0f9ff;color:#0369a1;text-decoration:none;transition:background .15s;"
                                        onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f0f9ff'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                                            <path d="M18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                                        </svg>
                                    </a>

                                    <a href="{{ route('processos.show', $p->id) }}" title="Ver detalhes"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#eff6ff;color:#2563a8;text-decoration:none;transition:background .15s;"
                                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('processos.editar', $p->id) }}" title="Editar"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;text-decoration:none;transition:background .15s;"
                                        onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <a href="{{ route('processos.andamentos', $p->id) }}" title="Andamentos" class="hide-xs"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#fff7ed;color:#d97706;text-decoration:none;transition:background .15s;"
                                        onmouseover="this.style.background='#fed7aa'" onmouseout="this.style.background='#fff7ed'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    </a>
                                    <a href="{{ route('processos.custas', $p->id) }}" title="Custas" class="hide-xs"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#faf5ff;color:#7c3aed;text-decoration:none;transition:background .15s;"
                                        onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#faf5ff'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    </a>
                                    <button wire:click="confirmarArquivar({{ $p->id }})" title="Arquivar" class="hide-xs"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                        onmouseover="this.style.background='#f1f5f9';this.style.color='#64748b'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                                <svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <div style="font-size:14px;font-weight:500;">Nenhum processo encontrado</div>
                                <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou cadastre um novo processo.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginacao --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
                <span style="font-size:13px;color:var(--muted);">
                    @if($processos->total() > 0)
                        Mostrando {{ $processos->firstItem() }}–{{ $processos->lastItem() }} de {{ $processos->total() }}
                    @else
                        Nenhum resultado
                    @endif
                </span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <button wire:click="previousPage" @disabled($processos->onFirstPage())
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $processos->onFirstPage() ? '.4' : '1' }};">
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        Anterior
                    </button>
                    <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                        {{ $processos->currentPage() }} / {{ $processos->lastPage() }}
                    </span>
                    <button wire:click="nextPage" @disabled(!$processos->hasMorePages())
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $processos->hasMorePages() ? '1' : '.4' }};">
                        Proxima
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

</div>

{{-- Modal confirmacao arquivar --}}
@if($confirmandoExclusao)
<div class="modal-backdrop">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                </div>
                <span class="modal-title">Arquivar Processo</span>
            </div>
        </div>
        <p style="font-size:14px;color:var(--muted);margin-bottom:20px;line-height:1.6;">
            Tem certeza que deseja arquivar este processo? Esta acao pode ser revertida posteriormente.
        </p>
        <div class="modal-footer">
            <button wire:click="cancelarExclusao" class="btn btn-outline">Cancelar</button>
            <button wire:click="arquivar" class="btn btn-danger">Arquivar</button>
        </div>
    </div>
</div>
@endif

</div>
