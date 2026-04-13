@extends('layouts.app')

@section('content')
<div class="relatorios-page">
  <style>
    .relatorios-page svg[width="13"] { width: 13px; height: 13px; }
    .relatorios-page svg[width="16"] { width: 16px; height: 16px; }
    .relatorios-page svg[width="20"] { width: 20px; height: 20px; }
    .relatorios-hero {
      background: var(--white);
      border: 1.5px solid var(--border);
      border-radius: 8px;
      padding: 18px 20px;
      margin-bottom: 16px;
      display: grid;
      grid-template-columns: minmax(260px, 1fr) minmax(280px, .9fr);
      gap: 18px;
      align-items: center;
    }
    .relatorios-hero-title {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      color: var(--primary);
      font-size: 24px;
      font-weight: 800;
    }
    .relatorios-hero-icon {
      width: 42px;
      height: 42px;
      border-radius: 8px;
      background: #eff6ff;
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .relatorios-hero h1 {
      font-size: 24px;
      font-weight: 800;
      color: var(--primary);
      margin: 0 0 4px;
    }
    .relatorios-hero p {
      font-size: 13px;
      color: var(--muted);
      margin: 0;
      line-height: 1.55;
    }
    .relatorios-guide {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 10px;
      grid-column: 1 / -1;
    }
    .relatorios-guide-item {
      border-left: 3px solid var(--border);
      padding-left: 10px;
    }
    .relatorios-guide-item strong {
      display: block;
      color: var(--text);
      font-size: 12px;
      margin-bottom: 3px;
    }
    .relatorios-guide-item span {
      color: var(--muted);
      font-size: 12px;
      line-height: 1.4;
    }
    .relatorios-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin: 0 0 12px;
      flex-wrap: wrap;
    }
    .relatorios-toolbar-title {
      font-size: 15px;
      color: var(--text);
      font-weight: 800;
    }
    .relatorios-toolbar-sub {
      color: var(--muted);
      font-size: 12px;
      margin-top: 2px;
    }
    .relatorios-chip-row {
      display: flex;
      gap: 8px;
      align-items: center;
      flex-wrap: wrap;
    }
    .relatorios-chip {
      border: 1.5px solid var(--border);
      border-radius: 8px;
      padding: 6px 10px;
      background: var(--white);
      color: var(--muted);
      font-size: 12px;
      font-weight: 600;
    }
    .relatorios-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
      gap: 14px;
      align-items: stretch;
    }
    .relatorios-grid .card {
      border-radius: 8px;
      border: 1.5px solid var(--border);
      display: flex;
      flex-direction: column;
      min-height: 100%;
      transition: border-color .15s, box-shadow .15s, transform .15s;
    }
    .relatorios-grid .card:hover {
      border-color: #cbd5e1;
      box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
      transform: translateY(-1px);
    }
    .relatorios-grid .card form {
      display: flex;
      flex-direction: column;
      flex: 1;
    }
    .relatorios-grid .card form > div:last-child,
    .relatorios-grid .card form > button:last-child {
      margin-top: auto;
    }
    .relatorios-grid .form-field {
      margin-bottom: 10px;
    }
    .relatorios-grid select,
    .relatorios-grid input[type="date"] {
      border-radius: 8px;
      border: 1.5px solid var(--border);
      min-height: 36px;
    }
    @media (max-width: 980px) {
      .relatorios-hero { grid-template-columns: 1fr; }
    }
    @media (max-width: 680px) {
      .relatorios-guide { grid-template-columns: 1fr; }
      .relatorios-grid { grid-template-columns: 1fr; }
    }
  </style>

  {{-- Cabeçalho --}}
  <div class="relatorios-hero">
    <div class="relatorios-hero-title">
      <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
      Relatórios — PDF e CSV
    </div>
    <div style="font-size:12px;color:var(--muted);margin-top:2px">
      Selecione os filtros e clique em <strong>Gerar PDF</strong> ou <strong>Exportar CSV</strong> (abre/baixa em nova aba).
    </div>
    <div class="relatorios-guide">
      <div class="relatorios-guide-item" style="border-left-color:#2563a8;">
        <strong>Processos</strong>
        <span>Fase, advogado, risco, tipo de ação e lista geral.</span>
      </div>
      <div class="relatorios-guide-item" style="border-left-color:#16a34a;">
        <strong>Financeiro</strong>
        <span>Honorários, custas e movimentação por período.</span>
      </div>
      <div class="relatorios-guide-item" style="border-left-color:#d97706;">
        <strong>Rotina</strong>
        <span>Agenda, andamentos, produtividade e aniversários.</span>
      </div>
    </div>
  </div>

  <div class="relatorios-toolbar">
    <div>
      <div class="relatorios-toolbar-title">Modelos disponíveis</div>
      <div class="relatorios-toolbar-sub">Use PDF para apresentação e CSV para análise em planilha.</div>
    </div>
    <div class="relatorios-chip-row">
      <span class="relatorios-chip">Processos</span>
      <span class="relatorios-chip">Financeiro</span>
      <span class="relatorios-chip">Agenda</span>
      <span class="relatorios-chip">Produtividade</span>
    </div>
  </div>

  <div class="relatorios-grid">

    {{-- 1. Processos por Fase --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#eff6ff;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Fase</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista todos os processos agrupados por fase processual.</div>
      <form method="GET" action="{{ route('relatorios.por-fase') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </button>
          <button type="submit" name="formato" value="csv" class="btn btn-secondary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
          </button>
        </div>
      </form>
    </div>

    {{-- 2. Processos por Advogado --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#eff6ff;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Advogado</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista os processos de cada advogado do escritório.</div>
      <form method="GET" action="{{ route('relatorios.por-advogado') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </button>
          <button type="submit" name="formato" value="csv" class="btn btn-secondary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
          </button>
        </div>
      </form>
    </div>

    {{-- 3. Processos por Risco --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#fef2f2;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Risco</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista os processos agrupados pelo grau de risco.</div>
      <form method="GET" action="{{ route('relatorios.por-risco') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status" >
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 4. Agenda do Período --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#fffbeb;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Agenda do Período</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Todos os compromissos em um intervalo de datas.</div>
      <form method="GET" action="{{ route('relatorios.agenda') }}" target="_blank">
        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              >
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              >
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 5. Custas Pendentes --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#f0fdf4;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Custas Pendentes</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Custas ainda não pagas no período selecionado.</div>
      <form method="GET" action="{{ route('relatorios.custas') }}" target="_blank">
        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              >
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              >
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 6. Aniversários de Clientes --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#fdf4ff;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 3v4"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Aniversários de Clientes</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Clientes que fazem aniversário no mês selecionado.</div>
      <form method="GET" action="{{ route('relatorios.aniversarios') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Mês</label>
          <select name="mes" >
            @foreach([1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                      7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'] as $n => $nome)
              <option value="{{ $n }}" {{ now()->month == $n ? 'selected' : '' }}>{{ $nome }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 7. Andamentos por Cliente --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#eff6ff;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Andamentos por Cliente</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Histórico de andamentos por processo, filtrado por cliente e período.</div>
      <form method="GET" action="{{ route('relatorios.andamentos-cliente') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Cliente</label>
          <select name="cliente_id" >
            <option value="">Todos os Clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}">{{ $c->nome }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              >
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              >
          </div>
        </div>
        <div class="form-field">
          <label class="lbl">Tipo</label>
          <select name="tipo" >
            <option value="todos">Todos</option>
            <option value="judiciais">Somente Judiciais</option>
            <option value="extrajudiciais">Somente Extrajudiciais</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 8. Honorários em Aberto --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#eff6ff;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Honorários em Aberto</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Parcelas pendentes e atrasadas de honorários por cliente.</div>
      <form method="GET" action="{{ route('relatorios.honorarios-aberto') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Cliente</label>
          <select name="cliente_id">
            <option value="">Todos os Clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}">{{ $c->nome }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status">
            <option value="todos">Pendentes e Atrasados</option>
            <option value="pendente">Somente Pendentes</option>
            <option value="atrasado">Somente Atrasados</option>
          </select>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </button>
          <button type="submit" name="formato" value="csv" class="btn btn-secondary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
          </button>
        </div>
      </form>
    </div>

    {{-- 9. Financeiro por Período --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#f0fdf4;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Financeiro por Período</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Receitas e despesas com totais e saldo no período.</div>
      <form method="GET" action="{{ route('relatorios.financeiro-periodo') }}" target="_blank">
        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
          </div>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </button>
          <button type="submit" name="formato" value="csv" class="btn btn-secondary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
          </button>
        </div>
      </form>
    </div>

    {{-- 10. Processos sem Andamento --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#f8fafc;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos sem Andamento</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Processos que não tiveram andamentos registrados no período.</div>
      <form method="GET" action="{{ route('relatorios.sem-andamento') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Sem andamento há mais de</label>
          <select name="dias" >
            <option value="15">15 dias</option>
            <option value="30" selected>30 dias</option>
            <option value="60">60 dias</option>
            <option value="90">90 dias</option>
            <option value="180">180 dias</option>
          </select>
        </div>
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status" >
            <option value="Ativo">Somente Ativos</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 11. Produtividade por Advogado --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#fffbeb;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Produtividade por Advogado</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Processos, horas, apontamentos e taxa de cumprimento de prazos por advogado.</div>
      <form method="GET" action="{{ route('relatorios.produtividade-pdf') }}" target="_blank">
        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              >
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              >
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
          Gerar PDF
        </button>
      </form>
    </div>

    {{-- 12. Processos por Tipo de Ação --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#f0fdf4;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Tipo de Ação</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista os processos agrupados pelo tipo de ação (dano moral, rescisão, etc.).</div>
      <form method="GET" action="{{ route('relatorios.por-tipo-acao') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </button>
          <button type="submit" name="formato" value="csv" class="btn btn-secondary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
          </button>
        </div>
      </form>
    </div>

    {{-- 13. Lista Geral de Processos --}}
    <div class="card">
      <div style="margin-bottom:10px;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#eff6ff;">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
      </div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Lista Geral de Processos</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Exportação completa com filtros por cliente, tipo de ação e status — ideal para Excel.</div>
      <form method="GET" action="{{ route('relatorios.lista-geral') }}" target="_blank">
        <div class="form-field">
          <label class="lbl">Status</label>
          <select name="status">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <div class="form-field">
          <label class="lbl">Cliente</label>
          <select name="cliente_id">
            <option value="">Todos os Clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}">{{ $c->nome }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label class="lbl">Tipo de Ação</label>
          <select name="tipo_acao_id">
            <option value="">Todos os Tipos</option>
            @foreach($tiposAcao as $t)
              <option value="{{ $t->id }}">{{ $t->descricao }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </button>
          <button type="submit" name="formato" value="csv" class="btn btn-secondary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
