@extends('layouts.app')
@section('page-title', 'Custas Processuais')
@section('content')

<div style="max-width:1100px;margin:0 auto;padding:24px 16px;">

  @if(session('sucesso'))
    <div style="margin-bottom:16px;padding:12px 14px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;color:#065f46;font-size:13px;font-weight:600;">
      {{ session('sucesso') }}
    </div>
  @endif

  @if(session('erro'))
    <div style="margin-bottom:16px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:600;">
      {{ session('erro') }}
    </div>
  @endif

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
      <div style="font-size:13px;color:var(--muted);margin-top:2px;">
        {{ $processo->numero }}
        @if($processo->cliente) · Cliente {{ $processo->cliente->nome }} @endif
      </div>
    </div>
    <button onclick="document.getElementById('modal-nova-custa').style.display='flex'"
      style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nova Custa
    </button>
  </div>

  {{-- Modal Nova Custa --}}
  <div id="modal-nova-custa" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:28px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2 style="font-size:16px;font-weight:700;margin:0;">Nova Custa</h2>
        <button onclick="document.getElementById('modal-nova-custa').style.display='none'"
          style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;line-height:1;">&times;</button>
      </div>
      <form method="POST" action="{{ route('processos.custas.store', $processo->id) }}">
        @csrf
        <div style="display:flex;flex-direction:column;gap:14px;">
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">Data *</label>
            <input type="date" name="data" value="{{ now()->format('Y-m-d') }}" required
              style="width:100%;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">Descrição *</label>
            <input type="text" name="descricao" placeholder="Ex: Taxa de distribuição, Diligência..." required
              style="width:100%;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">Valor (R$) *</label>
            <input type="number" name="valor" step="0.01" min="0.01" placeholder="0,00" required
              style="width:100%;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
          </div>
          <div style="display:flex;gap:20px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
              <input type="checkbox" name="reembolsavel" value="1" checked onchange="toggleReembolso(this)"> Reembolsável pelo cliente
            </label>
          </div>
          <div style="display:flex;gap:20px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
              <input type="checkbox" name="pago" value="1" id="chk-pago" onchange="toggleDataPgto(this)"> Já pago pelo escritório
            </label>
          </div>
          <div id="campo-data-pgto" style="display:none;">
            <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">Data do Pagamento</label>
            <input type="date" name="data_pagamento" value="{{ now()->format('Y-m-d') }}"
              style="width:100%;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
          </div>
          <div style="display:flex;gap:10px;margin-top:6px;">
            <button type="submit"
              style="flex:1;padding:10px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
              Salvar Custa
            </button>
            <button type="button" onclick="document.getElementById('modal-nova-custa').style.display='none'"
              style="padding:10px 16px;background:#f1f5f9;color:#475569;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
              Cancelar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <script>
    function toggleDataPgto(el) { document.getElementById('campo-data-pgto').style.display = el.checked ? 'block' : 'none'; }
  </script>

  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:24px;">
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
    <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:16px 18px;">
      <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">A cobrar</div>
      <div style="font-size:22px;font-weight:700;margin-top:4px;color:#7c3aed;">R$ {{ number_format($totais['a_cobrar'], 2, ',', '.') }}</div>
    </div>
    <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:16px 18px;">
      <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Já cobrado</div>
      <div style="font-size:22px;font-weight:700;margin-top:4px;color:#2563eb;">R$ {{ number_format($totais['cobrado'], 2, ',', '.') }}</div>
    </div>
  </div>

  <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:18px;">
    <div style="font-size:13px;color:var(--muted);line-height:1.7;">
      Custas pagas pelo escritório podem ser marcadas como <strong>reembolsáveis</strong> e depois transformadas em cobrança no
      <a href="{{ route('financeiro.central') }}" style="color:#2563eb;text-decoration:none;font-weight:700;">Financeiro Central</a>.
      Se a custa for interna, basta marcar como <strong>não reembolsável</strong>.
    </div>
  </div>

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
          <th style="padding:10px 16px;text-align:center;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Reembolso</th>
          <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Pgto.</th>
          <th style="padding:10px 16px;text-align:right;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($processo->custas->sortByDesc('data') as $custa)
        <tr style="border-bottom:1px solid var(--border);vertical-align:top;">
          <td style="padding:10px 16px;white-space:nowrap;">{{ $custa->data?->format('d/m/Y') ?? '—' }}</td>
          <td style="padding:10px 16px;">
            <div style="font-weight:600;color:var(--text);">{{ $custa->descricao }}</div>
            @if($custa->cobrado_em)
            <div style="font-size:11px;color:#2563eb;margin-top:3px;">
              Cobrado em {{ $custa->cobrado_em->format('d/m/Y H:i') }}
              @if($custa->cobrado_por) · por {{ $custa->cobrado_por }} @endif
            </div>
            @endif
          </td>
          <td style="padding:10px 16px;text-align:right;font-weight:600;">R$ {{ number_format($custa->valor, 2, ',', '.') }}</td>
          <td style="padding:10px 16px;text-align:center;">
            @if($custa->pago)
              <span style="background:#d1fae5;color:#065f46;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Pago</span>
            @else
              <span style="background:#fee2e2;color:#991b1b;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Pendente</span>
            @endif
          </td>
          <td style="padding:10px 16px;text-align:center;">
            @if(!$custa->reembolsavel)
              <span style="background:#f1f5f9;color:#475569;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Não reembolsável</span>
            @elseif($custa->cobranca_lancamento_id)
              <span style="background:#dbeafe;color:#1d4ed8;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Cobrado</span>
            @elseif($custa->pago)
              <span style="background:#f3e8ff;color:#7c3aed;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">A cobrar</span>
            @else
              <span style="background:#fff7ed;color:#c2410c;font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;">Aguardando pgto.</span>
            @endif
          </td>
          <td style="padding:10px 16px;color:var(--muted);white-space:nowrap;">{{ $custa->data_pagamento?->format('d/m/Y') ?? '—' }}</td>
          <td style="padding:10px 16px;text-align:right;">
            <div style="display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;">
              @if(!$custa->cobranca_lancamento_id)
              <form method="POST" action="{{ route('processos.custas.reembolso', [$processo->id, $custa->id]) }}">
                @csrf
                <button type="submit"
                  style="padding:6px 10px;border:1px solid {{ $custa->reembolsavel ? '#cbd5e1' : '#bbf7d0' }};background:{{ $custa->reembolsavel ? '#fff' : '#f0fdf4' }};color:{{ $custa->reembolsavel ? '#475569' : '#15803d' }};border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">
                  {{ $custa->reembolsavel ? 'Marcar não reembolsável' : 'Marcar reembolsável' }}
                </button>
              </form>
              @endif

              @if($custa->reembolsavel && $custa->pago && !$custa->cobranca_lancamento_id)
              <form method="POST" action="{{ route('processos.custas.cobranca', [$processo->id, $custa->id]) }}">
                @csrf
                <button type="submit"
                  style="padding:6px 10px;border:1px solid #ddd6fe;background:#faf5ff;color:#7c3aed;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">
                  Gerar cobrança
                </button>
              </form>
              @endif

              @if($custa->cobranca_lancamento_id)
              <a href="{{ route('financeiro.central') }}"
                style="padding:6px 10px;border:1px solid #bfdbfe;background:#eff6ff;color:#1d4ed8;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;">
                Ver no financeiro
              </a>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>

</div>
@endsection
