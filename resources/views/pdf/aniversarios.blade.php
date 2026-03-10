@extends('pdf.layout')
@section('content')
<div class="meta">
    <span>Mês: {{ $mes_nome }}</span>
    <span>Total: {{ $total }} cliente(s)</span>
</div>
<table>
    <thead>
        <tr><th>Dia</th><th>Nome</th><th>Nascimento</th><th>Idade</th><th>E-mail</th><th>Telefone</th></tr>
    </thead>
    <tbody>
        @forelse($clientes as $c)
        <tr>
            <td style="font-size:16px; font-weight:bold; color:#2563a8; text-align:center;">{{ $c['dia'] }}</td>
            <td><strong>{{ $c['nome'] }}</strong></td>
            <td>{{ $c['nascimento'] }}</td>
            <td>{{ $c['idade'] }} anos</td>
            <td>{{ $c['email'] ?? '—' }}</td>
            <td>{{ $c['telefone'] ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty">🎂 Nenhum cliente aniversaria em {{ $mes_nome }}.</td></tr>
        @endforelse
    </tbody>
</table>
@if($total > 0)
<div class="total-box">
    <div class="total-item"><div class="total-valor">🎂 {{ $total }}</div><div class="total-label">Aniversariantes em {{ $mes_nome }}</div></div>
</div>
@endif
@endsection
