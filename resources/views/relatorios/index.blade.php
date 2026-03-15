@extends('layouts.app')

@section('content')
<div>

  {{-- Cabeçalho --}}
  <div class="card" style="margin-bottom:20px">
    <div style="font-weight:700;font-size:15px;color:var(--primary)">📊 Relatórios em PDF</div>
    <div style="font-size:12px;color:var(--muted);margin-top:2px">
      Selecione os filtros e clique em <strong>Gerar PDF</strong> — o arquivo abrirá em nova aba.
    </div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px">

    {{-- 1. Processos por Fase --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">🔄</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Fase</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista todos os processos agrupados por fase processual.</div>
      <form method="GET" action="{{ route('relatorios.por-fase') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Status</label>
          <select name="status" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 2. Processos por Advogado --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">👨‍⚖️</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Advogado</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista os processos de cada advogado do escritório.</div>
      <form method="GET" action="{{ route('relatorios.por-advogado') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Status</label>
          <select name="status" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 3. Processos por Risco --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">⚠️</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos por Risco</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Lista os processos agrupados pelo grau de risco.</div>
      <form method="GET" action="{{ route('relatorios.por-risco') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Status</label>
          <select name="status" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="Ativo">Ativos</option>
            <option value="Encerrado">Encerrados</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 4. Agenda do Período --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">📅</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Agenda do Período</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Todos os compromissos em um intervalo de datas.</div>
      <form method="GET" action="{{ route('relatorios.agenda') }}" target="_blank">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 5. Custas Pendentes --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">💰</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Custas Pendentes</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Custas ainda não pagas no período selecionado.</div>
      <form method="GET" action="{{ route('relatorios.custas') }}" target="_blank">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 6. Aniversários de Clientes --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">🎂</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Aniversários de Clientes</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Clientes que fazem aniversário no mês selecionado.</div>
      <form method="GET" action="{{ route('relatorios.aniversarios') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Mês</label>
          <select name="mes" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            @foreach([1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                      7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'] as $n => $nome)
              <option value="{{ $n }}" {{ now()->month == $n ? 'selected' : '' }}>{{ $nome }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 7. Andamentos por Cliente --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">📋</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Andamentos por Cliente</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Histórico de andamentos por processo, filtrado por cliente e período.</div>
      <form method="GET" action="{{ route('relatorios.andamentos-cliente') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Cliente</label>
          <select name="cliente_id" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="">Todos os Clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}">{{ $c->nome }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
        </div>
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Tipo</label>
          <select name="tipo" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="todos">Todos</option>
            <option value="judiciais">Somente Judiciais</option>
            <option value="extrajudiciais">Somente Extrajudiciais</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 8. Honorários em Aberto --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">💼</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Honorários em Aberto</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Parcelas pendentes e atrasadas de honorários por cliente.</div>
      <form method="GET" action="{{ route('relatorios.honorarios-aberto') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Cliente</label>
          <select name="cliente_id" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="">Todos os Clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}">{{ $c->nome }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Status</label>
          <select name="status" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="todos">Pendentes e Atrasados</option>
            <option value="pendente">Somente Pendentes</option>
            <option value="atrasado">Somente Atrasados</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 9. Financeiro por Período --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">📈</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Financeiro por Período</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Receitas e despesas com totais e saldo no período.</div>
      <form method="GET" action="{{ route('relatorios.financeiro-periodo') }}" target="_blank">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 10. Processos sem Andamento --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">😴</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Processos sem Andamento</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Processos que não tiveram andamentos registrados no período.</div>
      <form method="GET" action="{{ route('relatorios.sem-andamento') }}" target="_blank">
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Sem andamento há mais de</label>
          <select name="dias" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="15">15 dias</option>
            <option value="30" selected>30 dias</option>
            <option value="60">60 dias</option>
            <option value="90">90 dias</option>
            <option value="180">180 dias</option>
          </select>
        </div>
        <div class="form-field" style="margin-bottom:10px">
          <label class="lbl">Status</label>
          <select name="status" style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
            <option value="Ativo">Somente Ativos</option>
            <option value="Todos">Todos</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

    {{-- 11. Produtividade por Advogado --}}
    <div class="card">
      <div style="font-size:24px;margin-bottom:8px">🏆</div>
      <div style="font-weight:700;font-size:14px;color:var(--primary);margin-bottom:2px">Produtividade por Advogado</div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:14px">Processos, horas, apontamentos e taxa de cumprimento de prazos por advogado.</div>
      <form method="GET" action="{{ route('relatorios.produtividade-pdf') }}" target="_blank">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
          <div class="form-field">
            <label class="lbl">De</label>
            <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
          <div class="form-field">
            <label class="lbl">Até</label>
            <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
              style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">📄 Gerar PDF</button>
      </form>
    </div>

  </div>
</div>
@endsection
