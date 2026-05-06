@extends('layouts.app')
@section('page-title', 'Central de Processos')

@section('content')
@php
    $prazosVencidos   = App\Models\Prazo::where('status', 'aberto')->where('data_prazo', '<', today())->count();
    $processosParados = App\Models\Processo::where('status', 'Ativo')
                            ->whereNotExists(fn($q) => $q->from('andamentos')
                                ->whereColumn('andamentos.processo_id', 'processos.id')
                                ->where('andamentos.created_at', '>=', now()->subDays(30)))
                            ->count();
    $prazos7dias      = App\Models\Prazo::where('status', 'aberto')
                            ->whereBetween('data_prazo', [today(), today()->addDays(7)])->count();
    $audienciasAmanha = App\Models\Agenda::where('tipo', 'Audiência')->where('concluido', false)
                            ->whereDate('data_hora', today()->addDay())->count();

    $itensAtencao = [
        ['label' => 'Prazos vencidos',   'valor' => $prazosVencidos,   'route' => route('prazos'),     'cor' => '#dc2626', 'bg' => '#fef2f2'],
        ['label' => 'Prazos em 7 dias',  'valor' => $prazos7dias,      'route' => route('prazos'),     'cor' => '#d97706', 'bg' => '#fffbeb'],
        ['label' => 'Sem atualização',   'valor' => $processosParados, 'route' => route('processos'),  'cor' => '#7c3aed', 'bg' => '#f5f3ff'],
        ['label' => 'Audiências amanhã', 'valor' => $audienciasAmanha, 'route' => route('audiencias'), 'cor' => '#2563a8', 'bg' => '#eff6ff'],
    ];
@endphp

<div style="display:flex;flex-direction:column;">

{{-- ── Cabeçalho ── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Central de Processos</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Gerencie processos, partes, documentos, prazos e procurações.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('processos.novo') }}"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Processo
        </a>
        <a href="{{ route('assistente') }}"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#fff;color:#1d4ed8;border:1px solid var(--border);border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            Resumo com IA
        </a>
    </div>
</div>

{{-- ── Atenção necessária ── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:18px 20px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:var(--text);">Atenção necessária</div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">Priorize o que pode exigir uma providência hoje.</div>
        </div>
        @if(collect($itensAtencao)->sum('valor') === 0)
        <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;background:#f0fdf4;color:#15803d;font-size:12px;font-weight:700;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Tudo em ordem
        </span>
        @endif
    </div>
    <div class="hub-atencao" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        @foreach($itensAtencao as $item)
        @php $semPendencia = (int) $item['valor'] === 0; @endphp
        <a href="{{ $item['route'] }}" style="text-decoration:none;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:13px 14px;border-radius:8px;background:{{ $semPendencia ? '#f8fafc' : $item['bg'] }};border:1px solid {{ $semPendencia ? '#e2e8f0' : 'transparent' }};">
            <span style="display:flex;align-items:center;gap:9px;min-width:0;">
                <span style="width:10px;height:10px;border-radius:50%;background:{{ $semPendencia ? '#94a3b8' : $item['cor'] }};flex-shrink:0;"></span>
                <span style="font-size:13px;font-weight:700;color:{{ $semPendencia ? 'var(--muted)' : $item['cor'] }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['label'] }}</span>
            </span>
            <span style="font-size:18px;font-weight:800;color:{{ $semPendencia ? 'var(--muted)' : $item['cor'] }};">{{ $item['valor'] }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- ── Grid de processos ── --}}
@livewire('processos', ['embutido' => true])

</div>

<style>
@media (max-width: 1024px) {
    .hub-atencao { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 640px) {
    .hub-atencao { grid-template-columns: 1fr !important; }
}
</style>
@endsection
