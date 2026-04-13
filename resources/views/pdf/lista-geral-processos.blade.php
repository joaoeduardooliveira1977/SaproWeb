@extends('pdf.layout')

@section('content')

<div class="meta">
    <span>Status: {{ $status }}</span>
    <span>Cliente: {{ $clienteNome }}</span>
    <span>Tipo de Ação: {{ $tipoNome }}</span>
    <span>Total: {{ $total }} processos</span>
</div>

@if($processos->isEmpty())
    <div class="empty">Nenhum processo encontrado com os filtros selecionados.</div>
@else
<table class="summary-grid">
    <tr>
        <td><div class="summary-card" style="border-left-color:#2563a8;"><div class="summary-label">Total</div><div class="summary-value">{{ $total }}</div><div class="summary-note">processos encontrados</div></div></td>
        <td><div class="summary-card" style="border-left-color:#16a34a;"><div class="summary-label">Ativos</div><div class="summary-value" style="color:#166534;">{{ $processos->where('status','Ativo')->count() }}</div></div></td>
        <td><div class="summary-card" style="border-left-color:#64748b;"><div class="summary-label">Encerrados</div><div class="summary-value" style="color:#475569;">{{ $processos->where('status','Encerrado')->count() }}</div></div></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:18%">Número</th>
            <th style="width:10%">Status</th>
            <th style="width:22%">Cliente</th>
            <th style="width:18%">Advogado</th>
            <th style="width:18%">Tipo de Ação</th>
            <th style="width:14%">Fase / Vara</th>
        </tr>
    </thead>
    <tbody>
        @foreach($processos as $p)
        <tr>
            <td style="font-weight:bold">
                {{ $p->numero }}
                @if($p->data_distribuicao)
                <div style="font-size:8px;color:#94a3b8;margin-top:1px;">{{ $p->data_distribuicao->format('d/m/Y') }}</div>
                @endif
            </td>
            <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
            <td>{{ $p->cliente?->nome ?? '—' }}</td>
            <td>{{ $p->advogado?->nome ?? '—' }}</td>
            <td>{{ $p->tipoAcao?->descricao ?? '—' }}</td>
            <td>
                {{ $p->fase?->descricao ?? '—' }}
                @if($p->vara)
                <div style="font-size:8px;color:#94a3b8;margin-top:1px;">{{ $p->vara }}</div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@endsection
