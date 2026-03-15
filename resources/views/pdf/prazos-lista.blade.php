@extends('pdf.layout')

@section('title', 'Lista de Prazos')

@section('content')

<div class="meta">
    <span><strong>Status:</strong> {{ $filtroStatus === 'todos' ? 'Todos' : ucfirst($filtroStatus) }}</span>
    <span><strong>Total:</strong> {{ count($prazos) }} prazo(s)</span>
    <span><strong>Gerado em:</strong> {{ $gerado_em }}</span>
</div>

@if($prazos->isEmpty())
    <div class="empty">Nenhum prazo encontrado com os filtros selecionados.</div>
@else

<table>
    <thead>
        <tr>
            <th style="width:22%">Título</th>
            <th style="width:8%">Tipo</th>
            <th style="width:10%">Data Prazo</th>
            <th style="width:8%">Dias Rest.</th>
            <th style="width:8%">Status</th>
            <th style="width:22%">Processo / Cliente</th>
            <th style="width:14%">Responsável</th>
            <th style="width:8%">Fatal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prazos as $prazo)
        @php
            $urg  = $prazo->urgencia();
            $dias = $prazo->diasRestantes();
            $bgRow = match($urg) {
                'vencido'  => '#fff5f5',
                'urgente'  => '#fff8f8',
                'atencao'  => '#fffaf5',
                'cumprido' => '#f8fafc',
                'perdido'  => '#f8fafc',
                default    => '#fff',
            };
            $diasLabel = match(true) {
                $urg === 'cumprido'   => 'Cumprido',
                $urg === 'perdido'    => 'Perdido',
                $urg === 'vencido'    => abs($dias).'d vencido',
                $dias === 0           => 'Vence hoje',
                default               => $dias.' dia(s)',
            };
            $diasColor = match($urg) {
                'normal'  => '#166534',
                'alerta'  => '#854d0e',
                'atencao' => '#9a3412',
                'urgente' => '#991b1b',
                'vencido' => '#991b1b',
                default   => '#64748b',
            };
        @endphp
        <tr style="background:{{ $bgRow }};">
            <td style="font-weight:600;">
                {{ $prazo->titulo }}
                @if($prazo->descricao)
                    <br><small style="font-weight:400;color:#64748b;">{{ Str::limit($prazo->descricao, 60) }}</small>
                @endif
            </td>
            <td style="font-size:11px;">{{ $prazo->tipo }}</td>
            <td style="font-weight:700;">{{ $prazo->data_prazo->format('d/m/Y') }}</td>
            <td style="color:{{ $diasColor }};font-weight:700;font-size:11px;">{{ $diasLabel }}</td>
            <td>
                @if($prazo->status === 'cumprido')
                    <span style="color:#166534;font-weight:700;">✓ Cumprido</span>
                @elseif($prazo->status === 'perdido')
                    <span style="color:#1e293b;font-weight:700;">✗ Perdido</span>
                @else
                    <span style="color:#2563a8;font-weight:700;">Aberto</span>
                @endif
            </td>
            <td style="font-size:11px;">
                @if($prazo->processo)
                    {{ $prazo->processo->numero }}
                    @if($prazo->processo->cliente)
                        <br><span style="color:#64748b;">{{ $prazo->processo->cliente->nome }}</span>
                    @endif
                @else
                    —
                @endif
            </td>
            <td style="font-size:11px;">{{ $prazo->responsavel?->nome ?? '—' }}</td>
            <td style="text-align:center;">
                @if($prazo->prazo_fatal)
                    <span style="color:#9d174d;font-weight:700;">⚠ Sim</span>
                @else
                    —
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endif

@endsection
