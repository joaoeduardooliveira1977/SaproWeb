@extends('layouts.app')

@section('content')
<div>
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">📊 Relatórios</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Gere relatórios em PDF do sistema</p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">

        {{-- 1. Processos por Fase --}}
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px; margin-bottom:12px;">🔄</div>
            <h3 style="font-size:15px; font-weight:700; color:#1a3a5c; margin-bottom:4px;">Processos por Fase</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Lista todos os processos agrupados por fase processual</p>
            <form method="GET" action="{{ route('relatorios.por-fase') }}" target="_blank">
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151;">Status</label>
                    <select name="status" style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                        <option value="Ativo">Ativos</option>
                        <option value="Encerrado">Encerrados</option>
                        <option value="Todos">Todos</option>
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    📄 Gerar PDF
                </button>
            </form>
        </div>

        {{-- 2. Processos por Advogado --}}
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px; margin-bottom:12px;">👨‍⚖️</div>
            <h3 style="font-size:15px; font-weight:700; color:#1a3a5c; margin-bottom:4px;">Processos por Advogado</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Lista os processos de cada advogado do escritório</p>
            <form method="GET" action="{{ route('relatorios.por-advogado') }}" target="_blank">
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151;">Status</label>
                    <select name="status" style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                        <option value="Ativo">Ativos</option>
                        <option value="Encerrado">Encerrados</option>
                        <option value="Todos">Todos</option>
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    📄 Gerar PDF
                </button>
            </form>
        </div>

        {{-- 3. Processos por Risco --}}
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px; margin-bottom:12px;">⚠️</div>
            <h3 style="font-size:15px; font-weight:700; color:#1a3a5c; margin-bottom:4px;">Processos por Risco</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Lista os processos agrupados pelo grau de risco</p>
            <form method="GET" action="{{ route('relatorios.por-risco') }}" target="_blank">
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151;">Status</label>
                    <select name="status" style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                        <option value="Ativo">Ativos</option>
                        <option value="Encerrado">Encerrados</option>
                        <option value="Todos">Todos</option>
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    📄 Gerar PDF
                </button>
            </form>
        </div>

        {{-- 4. Agenda do Período --}}
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px; margin-bottom:12px;">📅</div>
            <h3 style="font-size:15px; font-weight:700; color:#1a3a5c; margin-bottom:4px;">Agenda do Período</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Todos os compromissos em um intervalo de datas</p>
            <form method="GET" action="{{ route('relatorios.agenda') }}" target="_blank">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151;">De</label>
                        <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                            style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151;">Até</label>
                        <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                            style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    </div>
                </div>
                <button type="submit" style="width:100%; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    📄 Gerar PDF
                </button>
            </form>
        </div>

        {{-- 5. Custas Pendentes --}}
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px; margin-bottom:12px;">💰</div>
            <h3 style="font-size:15px; font-weight:700; color:#1a3a5c; margin-bottom:4px;">Custas Pendentes</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Custas ainda não pagas no período selecionado</p>
            <form method="GET" action="{{ route('relatorios.custas') }}" target="_blank">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151;">De</label>
                        <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                            style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151;">Até</label>
                        <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                            style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    </div>
                </div>
                <button type="submit" style="width:100%; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    📄 Gerar PDF
                </button>
            </form>
        </div>

        {{-- 6. Aniversários --}}
        <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px; margin-bottom:12px;">🎂</div>
            <h3 style="font-size:15px; font-weight:700; color:#1a3a5c; margin-bottom:4px;">Aniversários de Clientes</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Clientes que fazem aniversário no mês selecionado</p>
            <form method="GET" action="{{ route('relatorios.aniversarios') }}" target="_blank">
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151;">Mês</label>
                    <select name="mes" style="width:100%; margin-top:4px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px;">
                        @foreach([1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'] as $n => $nome)
                        <option value="{{ $n }}" {{ now()->month == $n ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    📄 Gerar PDF
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
