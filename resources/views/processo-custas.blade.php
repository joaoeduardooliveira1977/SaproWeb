@extends('layouts.app')
@section('page-title', 'Custas Processuais')
@section('content')

<div style="max-width:900px;margin:0 auto;padding:24px 16px;">

  {{-- Cabeçalho --}}
  <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('processos.show', $processo->id) }}"
       style="color:var(--muted);text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:4px;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Voltar ao processo
    </a>
  </div>

  <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:8px;">
    <div>
      <h1 style="font-size:20px;font-weight:700;margin:0;">Custas Processuais</h1>
      <div style="font-size:13px;color:var(--muted);margin-top:2px;">{{ $processo->numero }}</div>
    </div>
  </div>

  {{-- KPIs --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">
    <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:16px 18px;">
      <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Total</div>
      <div style="font-size:22px;font-weight:700;margin-top:4px;">R$ {{ number_format($totais['total'], 2, ',', '.') }}</div>
    </div>
    <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:16px 18px;">
      <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Pago</div>
      <div style="font-size:22px;font-weight:700;margin-top:4px;color:var(--success);">R$ {{ number_format($totais['pago'], 2, ',', '.') }}</div>
    </div>
    <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:16px 18px;">
      <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Pendente</div>
      <div style="font-size:22px;font-weight:700;margin-top:4px;color:var(--danger);">R$ {{ number_format($totais['pendente'], 2, ',', '.') }}</div>
    </div>
  </div>

  {{-- Tabela --}}
  <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;overflow:hidden;">
    @if($processo->custas->isEmpty())
      <div style="padding:40px;text-align:center;color:var(--muted);font-size:14px;">Nenhuma custa registrada.</div>
    @else
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
      <thead>
        <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
          <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Data</th>
          <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Descrição</th>
          <th style="padding:10px 16px;text-align:right;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Valor</th>
          <th style="padding:10px 16px;text-align:center;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Status</th>
          <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Pgto.</th>
        </tr>
      </thead>
      <tbody>
        @foreach($processo->custas->sortByDesc('data') as $custa)
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:10px 16px;white-space:nowrap;">{{ $custa->data?->format('d/m/Y') ?? '—' }}</td>
          <td style="padding:10px 16px;">{{ $custa->descricao }}</td>
          <td style="padding:10px 16px;text-align:right;font-weight:600;">R$ {{ number_format($custa->valor, 2, ',', '.') }}</td>
          <td style="padding:10px 16px;text-align:center;">
            @if($custa->pago)
              <span style="background:#d1fae5;color:#065f46;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Pago</span>
            @else
              <span style="background:#fee2e2;color:#991b1b;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Pendente</span>
            @endif
          </td>
          <td style="padding:10px 16px;color:var(--muted);">{{ $custa->data_pagamento?->format('d/m/Y') ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>

</div>
@endsection
