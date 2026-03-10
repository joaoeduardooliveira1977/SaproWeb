@extends('pdf.layout')
@section('content')
<div class="meta">
    <span>Período: {{ $data_ini }} a {{ $data_fim }}</span>
    <span>Total: {{ $total }} compromisso(s)</span>
</div>
<table>
    <thead>
        <tr>
            <th>Data/Hora</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Processo</th>
            <th>Cliente</th>
            <th>Local</th>
            <th>Situação</th>
        </tr>
    </thead>
    <tbody>
        @forelse($compromissos as $c)
        <tr>
            <td><strong>{{ $c['data_hora'] }}</strong></td>
            <td>
                {{ $c['titulo'] }}
                @if($c['urgente']) <span class="badge badge-urgente">URGENTE</span> @endif
            </td>
            <td>{{ $c['tipo'] }}</td>
            <td>{{ $c['processo'] ?? '—' }}</td>
            <td>{{ $c['cliente'] ?? '—' }}</td>
            <td>{{ $c['local'] ?? '—' }}</td>
            <td>
                @if($c['concluido'])
                    <span class="badge badge-ok">Concluído</span>
                @else
                    <span class="badge" style="background:#fef3c7;color:#d97706;">Pendente</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty">Nenhum compromisso no período.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="total-box">
    <div class="total-item"><div class="total-valor">{{ $total }}</div><div class="total-label">Total de Compromissos</div></div>
    <div class="total-item"><div class="total-valor">{{ collect($compromissos)->where('concluido', true)->count() }}</div><div class="total-label">Concluídos</div></div>
    <div class="total-item"><div class="total-valor">{{ collect($compromissos)->where('concluido', false)->count() }}</div><div class="total-label">Pendentes</div></div>
</div>
@endsection
