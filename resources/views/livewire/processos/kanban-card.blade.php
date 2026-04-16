@php
    $riscoCores = [
        'Alto'  => ['bg'=>'#fef2f2','color'=>'#dc2626'],
        'Médio' => ['bg'=>'#fffbeb','color'=>'#d97706'],
        'Baixo' => ['bg'=>'#f0fdf4','color'=>'#16a34a'],
    ];
    $riscoDesc  = $proc->risco?->descricao;
    $riscoCor   = $riscoCores[$riscoDesc] ?? ['bg'=>'#f1f5f9','color'=>'#64748b'];

    $scoreCores = [
        'critico' => ['bg'=>'#fef2f2','color'=>'#dc2626','label'=>'Crítico'],
        'atencao' => ['bg'=>'#fffbeb','color'=>'#d97706','label'=>'Atenção'],
        'normal'  => ['bg'=>'#f0fdf4','color'=>'#16a34a','label'=>'Normal'],
    ];
    $scoreCor = $scoreCores[$proc->score ?? ''] ?? null;
@endphp

<div class="kanban-card"
    data-id="{{ $proc->id }}"
    onclick="window.location='{{ route('processos.show', $proc->id) }}'"
    title="Abrir processo {{ $proc->numero }}">

    {{-- Número + badges --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:7px;">
        <span style="font-size:11px;font-family:monospace;font-weight:700;color:var(--primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $proc->numero }}</span>
        <div style="display:flex;gap:4px;flex-shrink:0;">
            @if($scoreCor)
            <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:99px;background:{{ $scoreCor['bg'] }};color:{{ $scoreCor['color'] }};">
                {{ $scoreCor['label'] }}
            </span>
            @endif
            @if($riscoDesc)
            <span style="font-size:10px;font-weight:600;padding:2px 6px;border-radius:99px;background:{{ $riscoCor['bg'] }};color:{{ $riscoCor['color'] }};">
                {{ $riscoDesc }}
            </span>
            @endif
        </div>
    </div>

    {{-- Cliente --}}
    <div style="font-size:13px;font-weight:600;color:var(--text);line-height:1.3;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
        {{ $proc->cliente?->nome ?? '—' }}
    </div>

    {{-- Advogados --}}
    @if($proc->advogados->isNotEmpty())
    <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:6px;">
        @foreach($proc->advogados->take(3) as $adv)
        @php
            $ini = strtoupper(mb_substr($adv->nome, 0, 1));
            $cores = ['#2563a8','#16a34a','#7c3aed','#d97706','#dc2626'];
            $cor   = $cores[ord($ini) % count($cores)];
        @endphp
        <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:{{ $cor }}22;color:{{ $cor }};font-size:10px;font-weight:700;border:1px solid {{ $cor }}44;"
            title="{{ $adv->nome }}">{{ $ini }}</span>
        @endforeach
        @if($proc->advogados->count() > 3)
        <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#f1f5f9;color:#64748b;font-size:10px;font-weight:700;">
            +{{ $proc->advogados->count() - 3 }}
        </span>
        @endif
    </div>
    @endif

    {{-- Valor da causa --}}
    @if($proc->valor_causa > 0)
    <div style="font-size:11px;color:var(--muted);display:flex;align-items:center;gap:4px;margin-top:2px;">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        R$ {{ number_format($proc->valor_causa, 2, ',', '.') }}
    </div>
    @endif

</div>
