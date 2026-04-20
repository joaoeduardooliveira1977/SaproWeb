<div>
<style>
@media (max-width: 768px) {
    .contratos-grid   { grid-template-columns: 1fr !important; }
    .contratos-kpis   { grid-template-columns: 1fr 1fr !important; }
    .filtros-contratos { position: static !important; }
}
@media (max-width: 480px) {
    .contratos-kpis { grid-template-columns: 1fr !important; }
}
</style>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:14px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--text);margin:0 0 4px;">Contratos</h1>
        <p style="font-size:13px;color:var(--muted);margin:0;max-width:620px;line-height:1.5;">
            Gerencie contratos de honorários, consultoria e serviços avulsos. Os lançamentos financeiros são gerados automaticamente.
        </p>
    </div>
    <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Novo Contrato
    </button>
</div>

{{-- ── Resumo rápido ── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:14px;display:grid;grid-template-columns:1.15fr .85fr;gap:16px;align-items:center;" class="contratos-help">
    <div>
        <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:4px;">Como usar esta tela</div>
        <div style="font-size:13px;color:var(--muted);line-height:1.6;">
            Crie um contrato para o cliente, adicione os serviços e configure repasses se houver indicador. Os lançamentos financeiros são gerados automaticamente ao ativar o contrato.
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:10px 12px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Contratos ativos</div>
            <div style="font-size:20px;font-weight:800;color:var(--text);margin-top:2px;">{{ $totalAtivos }}</div>
        </div>
        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:10px 12px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Valor contratado</div>
            <div style="font-size:20px;font-weight:800;color:var(--text);margin-top:2px;">R$ {{ number_format($totalValor, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- ── Grid: filtros + conteúdo ── --}}
<div class="contratos-grid" style="display:grid;grid-template-columns:260px 1fr;gap:18px;align-items:start;">

    {{-- COLUNA ESQUERDA: Filtros --}}
    <div class="filtros-contratos" style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px;position:sticky;top:20px;">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-size:14px;font-weight:800;color:var(--text);">Filtros</div>
            @if($busca || $filtroTipo || $filtroStatus !== 'ativo')
            <button wire:click="$set('busca',''); $set('filtroTipo',''); $set('filtroStatus','ativo')"
                style="border:0;background:transparent;color:var(--primary);font-size:12px;font-weight:700;cursor:pointer;padding:0;">
                Limpar
            </button>
            @endif
        </div>

        {{-- Busca --}}
        <div style="margin-bottom:16px;">
            <div style="position:relative;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="busca"
                    placeholder="Cliente ou descrição..."
                    style="width:100%;box-sizing:border-box;padding:9px 10px 9px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
            </div>
        </div>

        {{-- Filtro Status --}}
        <div style="margin-bottom:16px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">Status</div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                @foreach(['' => ['Todos', '#64748b'], 'ativo' => ['Ativo', '#16a34a'], 'rascunho' => ['Rascunho', '#2563eb'], 'suspenso' => ['Suspenso', '#ca8a04'], 'encerrado' => ['Encerrado', '#94a3b8']] as $val => $opt)
                @php $sel = $filtroStatus === $val; @endphp
                <button wire:click="$set('filtroStatus', '{{ $val }}')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;border:1.5px solid {{ $sel ? $opt[1].'44' : 'transparent' }};background:{{ $sel ? $opt[1].'14' : 'transparent' }};color:{{ $sel ? $opt[1] : 'var(--text)' }};text-align:left;width:100%;transition:all .15s;">
                    <span style="display:flex;align-items:center;gap:7px;font-weight:{{ $sel ? '600' : '400' }};">
                        @if($val !== '')<span style="width:8px;height:8px;border-radius:50%;background:{{ $opt[1] }};flex-shrink:0;display:inline-block;"></span>@endif
                        {{ $opt[0] }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Filtro Tipo --}}
        <div>
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">Tipo</div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                @php
                $tipoOpcoes = ['' => ['Todos', '#64748b']] + array_map(fn($l) => [$l, '#7c3aed'], $tiposLabels);
                $tipoCores = ['honorario_processo'=>'#2563eb','consultivo'=>'#7c3aed','avulso'=>'#ea580c'];
                @endphp
                <button wire:click="$set('filtroTipo', '')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;border:1.5px solid {{ $filtroTipo==='' ? '#94a3b844' : 'transparent' }};background:{{ $filtroTipo==='' ? '#f1f5f9' : 'transparent' }};color:var(--text);text-align:left;width:100%;font-weight:{{ $filtroTipo==='' ? '600' : '400' }};">
                    Todos
                </button>
                @foreach($tiposLabels as $val => $label)
                @php $cor = $tipoCores[$val] ?? '#64748b'; $sel = $filtroTipo === $val; @endphp
                <button wire:click="$set('filtroTipo', '{{ $sel ? '' : $val }}')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;border:1.5px solid {{ $sel ? $cor.'44' : 'transparent' }};background:{{ $sel ? $cor.'14' : 'transparent' }};color:{{ $sel ? $cor : 'var(--text)' }};text-align:left;width:100%;transition:all .15s;">
                    <span style="display:flex;align-items:center;gap:7px;font-weight:{{ $sel ? '600' : '400' }};">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $cor }};flex-shrink:0;display:inline-block;"></span>
                        {{ $label }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>

    </div>

    {{-- COLUNA DIREITA: KPIs + Tabela --}}
    <div>

        {{-- KPIs --}}
        <div class="contratos-kpis" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1.1;">{{ $totalAtivos }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">ativos</div>
                </div>
            </div>
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <div>
                    <div style="font-size:18px;font-weight:800;color:#16a34a;line-height:1.1;">R$ {{ number_format($totalValor, 0, ',', '.') }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">valor total</div>
                </div>
            </div>
            @if($podeValidar)
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#d97706;line-height:1.1;">{{ $totalNaoValid }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">não validados</div>
                </div>
            </div>
            @endif
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#faf5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#7c3aed;line-height:1.1;">{{ $contratos->total() }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">filtrados</div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="table-wrap">
                <table style="border-collapse:collapse;width:100%;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);">
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Cliente</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Descrição</th>
                            <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo / Forma</th>
                            <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:right;">Valor</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;">Status</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($contratos as $c)
                    @php
                        $statusCor = match($c->status) {
                            'ativo'     => ['#dcfce7','#16a34a'],
                            'suspenso'  => ['#fef9c3','#ca8a04'],
                            'encerrado' => ['#f1f5f9','#64748b'],
                            default     => ['#eff6ff','#2563eb'],
                        };
                        $tipoCor = match($c->tipo) {
                            'honorario_processo' => '#2563eb',
                            'consultivo'         => '#7c3aed',
                            'avulso'             => '#ea580c',
                            default              => '#64748b',
                        };
                        $iniciais = collect(explode(' ', trim($c->cliente?->nome ?? '?')))->filter()->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                    @endphp
                    <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">

                        <td style="padding:14px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:{{ $tipoCor }}22;color:{{ $tipoCor }};font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1.5px solid {{ $tipoCor }}44;">
                                    {{ $iniciais }}
                                </div>
                                <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $c->cliente?->nome ?? '—' }}</div>
                            </div>
                        </td>

                        <td style="padding:14px 16px;max-width:220px;">
                            <div style="font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->descricao }}</div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                                Início {{ $c->data_inicio->format('d/m/Y') }}
                                @if($c->data_fim) · Fim {{ $c->data_fim->format('d/m/Y') }}@endif
                                @if($c->advogadoResponsavel) · Adv. {{ $c->advogadoResponsavel->nome }}@endif
                                @if($c->processo) · Proc. {{ $c->processo->numero }}@endif
                                · {{ $c->servicos->count() }} serviço(s)
                            </div>
                        </td>

                        <td class="hide-sm" style="padding:14px 16px;">
                            <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:99px;background:{{ $tipoCor }}18;color:{{ $tipoCor }};display:inline-block;margin-bottom:4px;">
                                {{ $tiposLabels[$c->tipo] ?? $c->tipo }}
                            </span>
                            <div style="font-size:11px;color:var(--muted);">{{ $formasLabels[$c->forma_cobranca] ?? $c->forma_cobranca }}</div>
                        </td>

                        <td class="hide-sm" style="padding:14px 16px;text-align:right;">
                            <div style="font-size:14px;font-weight:700;color:var(--primary);">R$ {{ number_format($c->valor, 2, ',', '.') }}</div>
                            @if($c->percentual_exito)
                            <div style="font-size:11px;color:var(--muted);">+ {{ number_format($c->percentual_exito, 0) }}% êxito</div>
                            @endif
                        </td>

                        <td style="padding:14px 16px;text-align:center;">
                            <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;background:{{ $statusCor[0] }};color:{{ $statusCor[1] }};">
                                {{ ucfirst($c->status) }}
                            </span>
                            @if(!$c->validado && $c->status === 'ativo')
                            <div style="font-size:10px;color:#d97706;margin-top:3px;">⚠ não validado</div>
                            @endif
                        </td>

                        <td style="padding:14px 16px;text-align:center;">
                            <div style="display:flex;justify-content:center;gap:4px;">
                                <button wire:click="abrirDetalhe({{ $c->id }})" title="Ver detalhes"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button wire:click="abrirModal({{ $c->id }})" title="Editar"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                @if($podeValidar && !$c->validado && $c->status === 'ativo')
                                <button wire:click="validar({{ $c->id }})" title="Validar"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#fffbeb;color:#d97706;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                                @endif
                                @if($c->status === 'ativo')
                                <button wire:click="encerrar({{ $c->id }})" wire:confirm="Encerrar este contrato?" title="Encerrar"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <div style="font-size:14px;font-weight:500;">Nenhum contrato encontrado</div>
                            <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou crie um novo contrato.</div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
                <span style="font-size:13px;color:var(--muted);">
                    @if($contratos->total() > 0)
                        Mostrando {{ $contratos->firstItem() }}–{{ $contratos->lastItem() }} de {{ $contratos->total() }}
                    @else
                        Nenhum resultado
                    @endif
                </span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <button wire:click="previousPage" @disabled($contratos->onFirstPage())
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $contratos->onFirstPage() ? '.4' : '1' }};">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        Anterior
                    </button>
                    <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                        {{ $contratos->currentPage() }} / {{ $contratos->lastPage() }}
                    </span>
                    <button wire:click="nextPage" @disabled(!$contratos->hasMorePages())
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $contratos->hasMorePages() ? '1' : '.4' }};">
                        Próxima
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /coluna direita --}}
</div>{{-- /grid --}}

{{-- ════════════════════════════════════════════════
     MODAL: Novo / Editar Contrato
════════════════════════════════════════════════ --}}
@if($modal)
<div style="position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharModal" style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);z-index:1;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--white);z-index:2;">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">
                {{ $contratoId ? 'Editar Contrato' : 'Novo Contrato' }}
            </h3>
            <button wire:click="fecharModal" style="background:none;border:none;cursor:pointer;color:var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

            {{-- Cliente --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Cliente <span style="color:var(--danger);">*</span></label>
                <select wire:model.live="clienteId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="0">— Selecione o cliente —</option>
                    @foreach($clientes as $cl)
                    <option value="{{ $cl->id }}">{{ $cl->nome }}</option>
                    @endforeach
                </select>
                @error('clienteId')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Advogado responsável <span style="color:var(--danger);">*</span></label>
                    <select wire:model="advogadoResponsavelId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        <option value="0">— Selecione o advogado —</option>
                        @foreach($advogados as $advogado)
                        <option value="{{ $advogado->id }}">{{ $advogado->nome }}</option>
                        @endforeach
                    </select>
                    @error('advogadoResponsavelId')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Processo vinculado</label>
                    <select wire:model="processoContratoId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        <option value="0">— Contrato independente —</option>
                        @foreach($processosContrato as $processoContrato)
                        <option value="{{ $processoContrato->id }}">{{ $processoContrato->numero }} — {{ $processoContrato->titulo }}</option>
                        @endforeach
                    </select>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                        @if($clienteId && empty($processosContrato))
                        Este cliente não possui processos ativos no momento.
                        @else
                        Opcional. O contrato pode seguir sem vínculo com processo.
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tipo + Forma de cobrança --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Tipo de Contrato *</label>
                    <select wire:model="tipo" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        @foreach($tiposLabels as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Forma de Cobrança *</label>
                    <select wire:model.live="formaCobranca" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        @foreach($formasLabels as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Descrição --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Descrição *</label>
                <input wire:model="descricao" type="text" placeholder="Ex: Assessoria jurídica mensal — Condomínio ABC"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('descricao')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>

            {{-- Valor + Percentual êxito + Dia vencimento --}}
            <div style="display:grid;grid-template-columns:1fr 1fr {{ $formaCobranca === 'mensal_recorrente' ? '1fr' : '' }};gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">
                        {{ $formaCobranca === 'exito' ? 'Valor base (R$)' : 'Valor (R$)' }} *
                    </label>
                    <input wire:model="valor" type="text" placeholder="0,00"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('valor')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                @if($formaCobranca === 'exito')
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">% Êxito</label>
                    <input wire:model="percentualExito" type="text" placeholder="Ex: 10"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
                @endif
                @if($formaCobranca === 'mensal_recorrente')
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Dia de vencimento</label>
                    <input wire:model="diaVencimento" type="number" min="1" max="28" placeholder="Ex: 10"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
                @endif
            </div>

            {{-- Vigência --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Data de Início *</label>
                    <input wire:model="dataInicio" type="date"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('dataInicio')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Data de Fim <span style="font-weight:400;color:var(--muted);">(opcional)</span></label>
                    <input wire:model="dataFim" type="date"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Status</label>
                <select wire:model="status" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="ativo">Ativo</option>
                    <option value="rascunho">Rascunho</option>
                    <option value="suspenso">Suspenso</option>
                    <option value="encerrado">Encerrado</option>
                </select>
            </div>

            {{-- Observações --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Considerações adicionais..."
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
            </div>

            {{-- Contrato assinado (anexo) --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">
                    Contrato Assinado <span style="font-weight:400;color:var(--muted);">(opcional)</span>
                </label>
                @if($arquivoAtual)
                <div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;margin-bottom:8px;font-size:12px;color:#15803d;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $arquivoNome }}
                    <a href="/storage/{{ $arquivoAtual }}" target="_blank" style="margin-left:auto;color:#15803d;font-weight:600;">Ver</a>
                </div>
                @endif
                <input wire:model="arquivo" type="file" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;cursor:pointer;">
                <div wire:loading wire:target="arquivo" style="font-size:11px;color:var(--muted);margin-top:4px;">Carregando...</div>
            </div>

        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--border);position:sticky;bottom:0;background:var(--white);">
            <button wire:click="fecharModal" style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
            <button wire:click="salvar" wire:loading.attr="disabled"
                style="padding:9px 20px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <span wire:loading.remove wire:target="salvar">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span wire:loading wire:target="salvar" style="font-size:11px;">Salvando...</span>
                {{ $contratoId ? 'Salvar Alterações' : 'Criar Contrato' }}
            </button>
        </div>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════
     MODAL: Detalhe do Contrato + Serviços
════════════════════════════════════════════════ --}}
@if($modalDetalhe && $detalhe)
<div style="position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharDetalhe" style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:680px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);z-index:1;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--white);z-index:2;">
            <div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">{{ $detalhe->cliente?->nome }}</h3>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $detalhe->descricao }}</div>
            </div>
            <button wire:click="fecharDetalhe" style="background:none;border:none;cursor:pointer;color:var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;display:flex;flex-direction:column;gap:20px;">

            {{-- Resumo --}}
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;">
                <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;">
                    <div style="font-size:11px;color:var(--muted);">Tipo</div>
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-top:2px;">{{ $tiposLabels[$detalhe->tipo] ?? $detalhe->tipo }}</div>
                </div>
                <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;">
                    <div style="font-size:11px;color:var(--muted);">Valor</div>
                    <div style="font-size:13px;font-weight:700;color:var(--primary);margin-top:2px;">R$ {{ number_format($detalhe->valor, 2, ',', '.') }}</div>
                </div>
                <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;">
                    <div style="font-size:11px;color:var(--muted);">Vigência</div>
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-top:2px;">
                        {{ $detalhe->data_inicio->format('d/m/Y') }}
                        @if($detalhe->data_fim) → {{ $detalhe->data_fim->format('d/m/Y') }} @else → indeterminado @endif
                    </div>
                </div>
                <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;">
                    <div style="font-size:11px;color:var(--muted);">Advogado responsável</div>
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-top:2px;">{{ $detalhe->advogadoResponsavel?->nome ?? 'Não definido' }}</div>
                </div>
                <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;">
                    <div style="font-size:11px;color:var(--muted);">Processo vinculado</div>
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-top:2px;">{{ $detalhe->processo?->numero ?? 'Contrato independente' }}</div>
                </div>
            </div>

            {{-- Validação --}}
            @if($podeValidar)
            <div style="padding:12px 16px;border-radius:10px;background:{{ $detalhe->validado ? '#f0fdf4' : '#fef9c3' }};border:1px solid {{ $detalhe->validado ? '#bbf7d0' : '#fde047' }};">
                @if($detalhe->validado)
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#15803d;font-weight:600;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Validado por {{ $detalhe->validado_por }} em {{ \Carbon\Carbon::parse($detalhe->validado_em)->format('d/m/Y H:i') }}
                </div>
                @else
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div style="font-size:13px;color:#92400e;font-weight:600;">⚠ Aguardando validação financeira</div>
                    <button wire:click="validar({{ $detalhe->id }})"
                        style="padding:6px 14px;background:#16a34a;color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
                        Validar agora
                    </button>
                </div>
                @endif
            </div>
            @endif

            {{-- Serviços --}}
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <h4 style="font-size:14px;font-weight:700;color:var(--text);margin:0;">Serviços</h4>
                    <button wire:click="abrirServico({{ $detalhe->id }})"
                        style="display:flex;align-items:center;gap:5px;padding:6px 12px;background:var(--primary);color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Adicionar Serviço
                    </button>
                </div>

                @forelse($detalhe->servicos as $srv)
                @php $cor = match($srv->tipo) { 'exito'=>'#7c3aed','consultoria'=>'#0891b2','avulso'=>'#ea580c','repasse'=>'#d97706', default=>'#2563eb' }; @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:11px 14px;border:1px solid var(--border);border-radius:9px;margin-bottom:8px;background:#fafafa;">
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:{{ $cor }}18;color:{{ $cor }};white-space:nowrap;">
                        {{ $servicosTipos[$srv->tipo] ?? $srv->tipo }}
                    </span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $srv->descricao }}</div>
                        @if($srv->processo)
                        <div style="font-size:11px;color:var(--muted);">Proc. {{ $srv->processo->numero }}</div>
                        @endif
                    </div>
                    <div style="font-size:13px;font-weight:700;color:var(--primary);white-space:nowrap;">
                        @if($srv->tipo === 'exito' && $srv->percentual)
                            {{ number_format($srv->percentual, 0) }}%
                        @endif
                        @if($srv->valor > 0 || $srv->tipo !== 'exito')
                        R$ {{ number_format($srv->valor, 2, ',', '.') }}
                        @endif
                    </div>
                    <div style="display:flex;gap:5px;">
                        <button wire:click="abrirServico({{ $detalhe->id }}, {{ $srv->id }})"
                            style="padding:5px 7px;border:1px solid var(--border);border-radius:6px;background:var(--white);cursor:pointer;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button wire:click="excluirServico({{ $srv->id }})" wire:confirm="Remover este serviço?"
                            style="padding:5px 7px;border:1px solid #fecaca;border-radius:6px;background:#fef2f2;cursor:pointer;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        </button>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:24px;color:var(--muted);font-size:13px;border:2px dashed var(--border);border-radius:10px;">
                    Nenhum serviço adicionado ainda.
                </div>
                @endforelse
            </div>

            {{-- Repasses --}}
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <h4 style="font-size:14px;font-weight:700;color:var(--text);margin:0;">Repasses</h4>
                    <button wire:click="abrirRepasse({{ $detalhe->id }})"
                        style="display:flex;align-items:center;gap:5px;padding:6px 12px;background:#7c3aed;color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Adicionar Repasse
                    </button>
                </div>

                @php $repasses = $detalhe->repasses ?? collect(); @endphp
                @forelse($repasses as $rep)
                @php $indicadorNome = $rep->indicador?->nome ?? '—'; @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:11px 14px;border:1px solid #ede9fe;border-radius:9px;margin-bottom:8px;background:#faf5ff;">
                    <div style="width:34px;height:34px;border-radius:8px;background:#7c3aed18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $indicadorNome }}</div>
                        <div style="font-size:11px;color:var(--muted);">
                            {{ $rep->tipo_calculo === 'percentual' ? number_format($rep->percentual, 1).'%' : 'R$ '.number_format($rep->valor_fixo, 2, ',', '.') }}
                            &bull; {{ ucfirst($rep->frequencia) }}
                        </div>
                    </div>
                    <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:99px;background:{{ $rep->status === 'ativo' ? '#dcfce7' : '#f1f5f9' }};color:{{ $rep->status === 'ativo' ? '#16a34a' : '#64748b' }};">
                        {{ ucfirst($rep->status) }}
                    </span>
                    <div style="display:flex;gap:5px;">
                        <button wire:click="abrirRepasse({{ $detalhe->id }}, {{ $rep->id }})"
                            style="padding:5px 7px;border:1px solid var(--border);border-radius:6px;background:var(--white);cursor:pointer;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button wire:click="excluirRepasse({{ $rep->id }})" wire:confirm="Remover este repasse e seus lançamentos?"
                            style="padding:5px 7px;border:1px solid #fecaca;border-radius:6px;background:#fef2f2;cursor:pointer;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        </button>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;border:2px dashed #ede9fe;border-radius:10px;">
                    Nenhum repasse configurado.
                </div>
                @endforelse
            </div>

            {{-- Arquivo --}}
            @if($detalhe->arquivo)
            <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span style="font-size:13px;color:#15803d;font-weight:600;">{{ $detalhe->arquivo_original }}</span>
                <a href="/storage/{{ $detalhe->arquivo }}" target="_blank"
                    style="margin-left:auto;padding:5px 12px;background:#16a34a;color:#fff;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;">
                    Baixar
                </a>
            </div>
            @endif

        </div>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════
     MODAL: Serviço
════════════════════════════════════════════════ --}}
@if($modalServico)
<div style="position:fixed;inset:0;z-index:1100;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharServico" style="position:absolute;inset:0;background:rgba(0,0,0,.5);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.25);z-index:1;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">{{ $servicoId ? 'Editar Serviço' : 'Novo Serviço' }}</h3>
            <button wire:click="fecharServico" style="background:none;border:none;cursor:pointer;color:var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;display:flex;flex-direction:column;gap:14px;">
            @php $servicoCtx = $servicosContexto[$servicoTipo] ?? $servicosContexto['honorario']; @endphp

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Descrição *</label>
                <input wire:model="servicoDescricao" type="text" placeholder="{{ $servicoCtx['placeholder'] }}"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('servicoDescricao')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Tipo *</label>
                    <select wire:model.live="servicoTipo" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        @foreach($servicosTipos as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;line-height:1.5;">{{ $servicoCtx['descricao'] }}</div>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">{{ $servicoCtx['label_valor'] }}</label>
                    <input wire:model="servicoValor" type="text" placeholder="0,00"
                        style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('servicoValor')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
            </div>

            @if($servicoTipo === 'exito')
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Percentual de Êxito (%)</label>
                <input wire:model="servicoPercentual" type="text" placeholder="Ex: 10"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('servicoPercentual')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>
            @endif

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Processo vinculado <span style="font-weight:400;color:var(--muted);">(opcional)</span></label>
                <select wire:model="servicoProcessoId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="0">— Nenhum —</option>
                    @foreach($processos as $proc)
                    <option value="{{ $proc->id }}">{{ $proc->numero }} — {{ $proc->cliente_nome }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Observações</label>
                <textarea wire:model="servicoObs" rows="2" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
            </div>

        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--border);">
            <button wire:click="fecharServico" style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
            <button wire:click="salvarServico" wire:loading.attr="disabled"
                style="padding:9px 20px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove wire:target="salvarServico">Salvar</span>
                <span wire:loading wire:target="salvarServico">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════
     MODAL: Repasse
════════════════════════════════════════════════ --}}
@if($modalRepasse)
<div style="position:fixed;inset:0;z-index:1200;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div wire:click="fecharRepasse" style="position:absolute;inset:0;background:rgba(0,0,0,.5);"></div>
    <div style="position:relative;background:var(--white);border-radius:14px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.25);z-index:1;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">
                {{ $repasseId ? 'Editar Repasse' : 'Novo Repasse' }}
            </h3>
            <button wire:click="fecharRepasse" style="background:none;border:none;cursor:pointer;color:var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

            {{-- Indicador --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Indicador / Correspondente *</label>
                <select wire:model="repasseIndicadorId" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="0">— Selecione —</option>
                    @foreach($indicadores as $ind)
                    <option value="{{ $ind->id }}">{{ $ind->nome }}</option>
                    @endforeach
                </select>
                @error('repasseIndicadorId')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>

            {{-- Tipo de cálculo --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:8px;">Tipo de Cálculo *</label>
                <div style="display:flex;gap:12px;">
                    <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;padding:10px 16px;border:2px solid {{ $repasseTipoCalculo === 'percentual' ? '#7c3aed' : 'var(--border)' }};border-radius:8px;flex:1;background:{{ $repasseTipoCalculo === 'percentual' ? '#faf5ff' : 'var(--white)' }};">
                        <input type="radio" wire:model.live="repasseTipoCalculo" value="percentual" style="accent-color:#7c3aed;">
                        Percentual (%)
                    </label>
                    <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;padding:10px 16px;border:2px solid {{ $repasseTipoCalculo === 'fixo' ? '#7c3aed' : 'var(--border)' }};border-radius:8px;flex:1;background:{{ $repasseTipoCalculo === 'fixo' ? '#faf5ff' : 'var(--white)' }};">
                        <input type="radio" wire:model.live="repasseTipoCalculo" value="fixo" style="accent-color:#7c3aed;">
                        Valor Fixo (R$)
                    </label>
                </div>
            </div>

            {{-- Valor conforme tipo --}}
            @if($repasseTipoCalculo === 'percentual')
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Percentual (%) *</label>
                <input wire:model="repassePercentual" type="text" placeholder="Ex: 10"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('repassePercentual')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Calculado sobre cada lançamento de receita do contrato.</div>
            </div>
            @else
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Valor Fixo (R$) *</label>
                <input wire:model="repasseValorFixo" type="text" placeholder="Ex: 500,00"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                @error('repasseValorFixo')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>
            @endif

            {{-- Frequência --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:5px;">Frequência</label>
                <select wire:model="repasseFrequencia" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                    <option value="mensal">Mensal</option>
                    <option value="parcela">Por parcela recebida</option>
                    <option value="unico">Único (na conclusão)</option>
                </select>
            </div>

        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--border);">
            <button wire:click="fecharRepasse" style="padding:9px 18px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
            <button wire:click="salvarRepasse" wire:loading.attr="disabled"
                style="padding:9px 20px;background:#7c3aed;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove wire:target="salvarRepasse">Salvar Repasse</span>
                <span wire:loading wire:target="salvarRepasse">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>
