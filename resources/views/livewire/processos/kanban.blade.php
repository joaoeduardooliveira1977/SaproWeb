<div>

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="5" height="18" rx="1"/><rect x="10" y="3" width="5" height="18" rx="1"/><rect x="17" y="3" width="4" height="18" rx="1"/></svg>
            Kanban de Processos
        </h2>
        <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Arraste os processos entre colunas para mudar a fase. {{ $totalProcessos }} processo(s) ativo(s).</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('processos') }}" class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            Lista
        </a>
        <a href="{{ route('processos.novo') }}" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Processo
        </a>
    </div>
</div>

{{-- ── Filtros ── --}}
<div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
    <div style="position:relative;flex:1;min-width:220px;">
        <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por número ou cliente..."
            style="width:100%;padding:8px 12px 8px 32px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;">
    </div>
    <select wire:model.live="filtroAdvogado"
        style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);min-width:180px;">
        <option value="">Todos os advogados</option>
        @foreach($advogados as $adv)
        <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
        @endforeach
    </select>
    @if($busca || $filtroAdvogado)
    <button wire:click="$set('busca','');$set('filtroAdvogado','')"
        style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;background:var(--white);cursor:pointer;color:var(--muted);display:flex;align-items:center;gap:5px;white-space:nowrap;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Limpar
    </button>
    @endif
</div>

{{-- ── Tabuleiro Kanban ── --}}
<div class="kanban-board" style="display:flex;gap:14px;overflow-x:auto;padding-bottom:16px;align-items:flex-start;">

    {{-- Coluna: Sem Fase --}}
    @if($kanban['sem_fase']->count() > 0 || !$busca)
    @php $semFaseCount = $kanban['sem_fase']->count(); @endphp
    <div class="kanban-col" style="flex-shrink:0;width:280px;display:flex;flex-direction:column;gap:0;">
        {{-- Header coluna --}}
        <div style="padding:10px 14px;border-radius:10px 10px 0 0;background:#f1f5f9;border:1.5px solid #e2e8f0;border-bottom:none;display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <div style="display:flex;align-items:center;gap:7px;min-width:0;">
                <span style="width:10px;height:10px;border-radius:50%;background:#94a3b8;flex-shrink:0;"></span>
                <span style="font-size:12px;font-weight:700;color:#475569;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Sem Fase</span>
            </div>
            <span style="background:#e2e8f0;color:#64748b;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;flex-shrink:0;">{{ $semFaseCount }}</span>
        </div>
        {{-- Corpo da coluna (droppable) --}}
        <div class="kanban-col-body"
            data-fase-id="0"
            style="min-height:120px;padding:10px;background:#f8fafc;border:1.5px solid #e2e8f0;border-top:none;border-radius:0 0 10px 10px;display:flex;flex-direction:column;gap:8px;">
            @foreach($kanban['sem_fase'] as $proc)
                @include('livewire.processos.kanban-card', ['proc' => $proc])
            @endforeach
            @if($semFaseCount === 0)
            <div style="text-align:center;padding:20px 10px;color:#94a3b8;font-size:12px;">
                Sem processos
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Colunas por Fase --}}
    @foreach($fases as $fase)
    @php
        $count    = $kanban[$fase->id]->count();
        $valorTotal = $kanban[$fase->id]->sum('valor_causa');

        // Cores por índice de fase (cíclico)
        $cores = [
            ['bg'=>'#dbeafe','border'=>'#93c5fd','dot'=>'#2563eb','text'=>'#1d4ed8','header'=>'#eff6ff'],
            ['bg'=>'#ede9fe','border'=>'#c4b5fd','dot'=>'#7c3aed','text'=>'#6d28d9','header'=>'#f5f3ff'],
            ['bg'=>'#dcfce7','border'=>'#86efac','dot'=>'#16a34a','text'=>'#15803d','header'=>'#f0fdf4'],
            ['bg'=>'#fef9c3','border'=>'#fde047','dot'=>'#ca8a04','text'=>'#92400e','header'=>'#fffbeb'],
            ['bg'=>'#ffedd5','border'=>'#fdba74','dot'=>'#ea580c','text'=>'#c2410c','header'=>'#fff7ed'],
            ['bg'=>'#fce7f3','border'=>'#f9a8d4','dot'=>'#db2777','text'=>'#9d174d','header'=>'#fdf2f8'],
            ['bg'=>'#e0f2fe','border'=>'#7dd3fc','dot'=>'#0284c7','text'=>'#0369a1','header'=>'#f0f9ff'],
            ['bg'=>'#d1fae5','border'=>'#6ee7b7','dot'=>'#059669','text'=>'#065f46','header'=>'#ecfdf5'],
        ];
        $cor = $cores[$loop->index % count($cores)];
    @endphp
    <div class="kanban-col" style="flex-shrink:0;width:280px;display:flex;flex-direction:column;gap:0;">
        {{-- Header coluna --}}
        <div style="padding:10px 14px;border-radius:10px 10px 0 0;background:{{ $cor['header'] }};border:1.5px solid {{ $cor['border'] }};border-bottom:3px solid {{ $cor['dot'] }};display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <div style="display:flex;align-items:center;gap:7px;min-width:0;">
                <span style="width:10px;height:10px;border-radius:50%;background:{{ $cor['dot'] }};flex-shrink:0;"></span>
                <span style="font-size:12px;font-weight:700;color:{{ $cor['text'] }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $fase->descricao }}">{{ $fase->descricao }}</span>
            </div>
            <span style="background:{{ $cor['dot'] }};color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;flex-shrink:0;">{{ $count }}</span>
        </div>
        {{-- Valor total --}}
        @if($valorTotal > 0)
        <div style="padding:5px 14px;background:{{ $cor['bg'] }};border-left:1.5px solid {{ $cor['border'] }};border-right:1.5px solid {{ $cor['border'] }};font-size:11px;color:{{ $cor['text'] }};font-weight:600;">
            R$ {{ number_format($valorTotal, 2, ',', '.') }}
        </div>
        @endif
        {{-- Corpo da coluna (droppable) --}}
        <div class="kanban-col-body"
            data-fase-id="{{ $fase->id }}"
            style="min-height:120px;padding:10px;background:#fafafa;border:1.5px solid {{ $cor['border'] }};border-top:none;border-radius:0 0 10px 10px;display:flex;flex-direction:column;gap:8px;">
            @foreach($kanban[$fase->id] as $proc)
                @include('livewire.processos.kanban-card', ['proc' => $proc])
            @endforeach
            @if($count === 0)
            <div style="text-align:center;padding:20px 10px;color:#94a3b8;font-size:12px;border:2px dashed #e2e8f0;border-radius:8px;">
                Solte aqui
            </div>
            @endif
        </div>
    </div>
    @endforeach

</div>

{{-- ── Sortable.js ── --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    function initKanban() {
        const board = document.querySelector('.kanban-board');
        if (!board || !window.Sortable) return;

        const root   = board.closest('[wire\\:id]');
        if (!root) return;
        const wireId = root.getAttribute('wire:id');

        document.querySelectorAll('.kanban-col-body').forEach(function (col) {
            if (col._sortable) col._sortable.destroy();

            col._sortable = Sortable.create(col, {
                group:      'processos-kanban',
                animation:  150,
                ghostClass: 'kanban-ghost',
                dragClass:  'kanban-drag',
                handle:     '.kanban-card',
                onEnd: function (evt) {
                    if (evt.from === evt.to) return;

                    const processoId = parseInt(evt.item.dataset.id);
                    const faseIdRaw  = evt.to.dataset.faseId;
                    const faseId     = (faseIdRaw === '0' || faseIdRaw === '') ? null : parseInt(faseIdRaw);

                    Livewire.find(wireId).moverProcesso(processoId, faseId);
                },
            });
        });
    }

    // Inicializar na carga e após cada update do Livewire
    document.addEventListener('DOMContentLoaded', initKanban);
    document.addEventListener('livewire:update', initKanban);
    initKanban();
})();
</script>

<style>
.kanban-board::-webkit-scrollbar { height: 6px; }
.kanban-board::-webkit-scrollbar-track { background: transparent; }
.kanban-board::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

.kanban-ghost { opacity: .4; background: #e0f2fe !important; border: 2px dashed #0284c7 !important; }
.kanban-drag  { box-shadow: 0 8px 24px rgba(0,0,0,.18); transform: rotate(1.5deg); }

.kanban-card {
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 9px;
    padding: 11px 13px;
    cursor: grab;
    transition: box-shadow .15s, transform .15s;
    user-select: none;
}
.kanban-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.1); transform: translateY(-1px); }
.kanban-card:active { cursor: grabbing; }
</style>

</div>
