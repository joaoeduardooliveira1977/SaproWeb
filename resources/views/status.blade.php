<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Status do Sistema — Software Jurídico</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f4f8; color: #1e293b; min-height: 100vh; }

        .header { background: #0f2540; padding: 20px 0; }
        .header-inner { max-width: 860px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; gap: 14px; }
        .header-logo { width: 40px; height: 40px; background: #c9a84c; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .header-logo svg { width: 20px; height: 20px; stroke: #fff; fill: none; stroke-width: 2; }
        .header-title { color: #fff; font-size: 16px; font-weight: 700; }
        .header-sub { color: rgba(255,255,255,.5); font-size: 12px; }

        .container { max-width: 860px; margin: 0 auto; padding: 32px 24px; }

        /* Status geral */
        .status-geral { border-radius: 14px; padding: 28px 32px; margin-bottom: 28px; display: flex; align-items: center; gap: 20px; }
        .status-geral.verde   { background: #f0fdf4; border: 1.5px solid #86efac; }
        .status-geral.amarelo { background: #fffbeb; border: 1.5px solid #fcd34d; }
        .status-geral.vermelho{ background: #fef2f2; border: 1.5px solid #fca5a5; }
        .status-geral.azul    { background: #eff6ff; border: 1.5px solid #bfdbfe; }
        .status-icone { font-size: 44px; flex-shrink: 0; }
        .status-texto-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: .7; margin-bottom: 4px; }
        .status-texto-desc  { font-size: 20px; font-weight: 800; }
        .status-atualizado  { font-size: 11px; opacity: .6; margin-top: 4px; }

        /* Componentes */
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 20px; overflow: hidden; }
        .card-header { padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #1a3a5c; }
        .comp-row { display: flex; align-items: center; justify-content: space-between; padding: 13px 20px; border-bottom: 1px solid #f1f5f9; }
        .comp-row:last-child { border-bottom: none; }
        .comp-nome { font-size: 13px; font-weight: 600; color: #1e293b; }
        .comp-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 700; }
        .badge-ok   { background: #dcfce7; color: #16a34a; }
        .badge-warn { background: #fffbeb; color: #d97706; }
        .badge-down { background: #fee2e2; color: #dc2626; }

        /* Incidentes */
        .incidente { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; }
        .incidente:last-child { border-bottom: none; }
        .incidente-data   { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
        .incidente-titulo { font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .incidente-msg    { font-size: 12px; color: #64748b; line-height: 1.6; }

        .footer { text-align: center; padding: 24px; font-size: 12px; color: #94a3b8; }
        .footer a { color: #1a3a5c; text-decoration: none; font-weight: 600; }

        @media (max-width: 600px) {
            .status-geral { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div class="header-logo">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <div class="header-title">Software Jurídico</div>
            <div class="header-sub">Página de Status do Sistema</div>
        </div>
    </div>
</div>

<div class="container">

    {{-- ── Status geral ── --}}
    @php
        $tipos_criticos = ['manutencao_emergencial', 'pagamento_pendente'];
        $tipos_manutencao = ['manutencao_programada'];
        $tipos_ok = ['sistema_restaurado', 'nova_funcionalidade'];

        $comunicadosAtivos = \App\Models\Comunicado::ativos()
            ->where('destino', 'todos')
            ->orderByRaw("case tipo
                when 'manutencao_emergencial' then 0
                when 'pagamento_pendente'     then 1
                when 'manutencao_programada'  then 2
                else 3 end")
            ->get();

        $critico    = $comunicadosAtivos->whereIn('tipo', $tipos_criticos)->first();
        $manutencao = $comunicadosAtivos->whereIn('tipo', $tipos_manutencao)->first();

        if ($critico) {
            $statusClasse = 'vermelho';
            $statusIcone  = '🔴';
            $statusDesc   = 'Interrupção de serviço';
        } elseif ($manutencao) {
            $statusClasse = 'azul';
            $statusIcone  = '🔧';
            $statusDesc   = 'Manutenção em andamento';
        } else {
            $statusClasse = 'verde';
            $statusIcone  = '✅';
            $statusDesc   = 'Todos os sistemas operacionais';
        }
    @endphp

    <div class="status-geral {{ $statusClasse }}">
        <div class="status-icone">{{ $statusIcone }}</div>
        <div>
            <div class="status-texto-label">Status atual</div>
            <div class="status-texto-desc">{{ $statusDesc }}</div>
            <div class="status-atualizado">Atualizado em {{ now()->format('d/m/Y H:i') }} — página atualiza automaticamente a cada 60s</div>
        </div>
    </div>

    {{-- Comunicado ativo em destaque --}}
    @if($critico || $manutencao)
    @php $destaque = $critico ?? $manutencao; $ti = $destaque->tipoInfo(); @endphp
    <div style="padding:14px 18px;border-radius:10px;background:{{ $ti['bg'] }};border:1px solid {{ $ti['border'] }};color:{{ $ti['cor'] }};margin-bottom:20px;font-size:13px;line-height:1.6;">
        <strong>{{ $ti['icon'] }} {{ $destaque->titulo }}</strong><br>
        {{ $destaque->mensagem }}
        @if($destaque->data_fim)
        <div style="margin-top:6px;font-size:11px;opacity:.8;">Previsão de resolução: {{ $destaque->data_fim->format('d/m/Y H:i') }}</div>
        @endif
    </div>
    @endif

    {{-- ── Componentes ── --}}
    <div class="card">
        <div class="card-header">Componentes do Sistema</div>
        @php
            $componentesDown = $critico ? true : false;
            $componentesDeg  = $manutencao ? true : false;

            $componentes = [
                'Sistema Principal'              => $componentesDown ? 'down' : ($componentesDeg ? 'warn' : 'ok'),
                'Autenticação'                   => $componentesDown ? 'down' : 'ok',
                'Processamento de Documentos'    => ($componentesDown || $componentesDeg) ? 'warn' : 'ok',
                'Notificações'                   => $componentesDeg ? 'warn' : 'ok',
                'API AASP'                       => 'ok',
            ];
        @endphp
        @foreach($componentes as $nome => $status)
        <div class="comp-row">
            <div class="comp-nome">{{ $nome }}</div>
            @if($status === 'ok')
                <span class="comp-badge badge-ok">✅ Operacional</span>
            @elseif($status === 'warn')
                <span class="comp-badge badge-warn">⚠️ Degradado</span>
            @else
                <span class="comp-badge badge-down">🔴 Fora</span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ── Incidentes recentes ── --}}
    @php
        $incidentes = \App\Models\Comunicado::where('ativo', true)
            ->whereIn('tipo', ['manutencao_emergencial','manutencao_programada','sistema_restaurado'])
            ->where('data_inicio', '>=', now()->subDays(30))
            ->orderByDesc('data_inicio')
            ->limit(10)
            ->get();
    @endphp

    <div class="card">
        <div class="card-header">Histórico de Incidentes (últimos 30 dias)</div>
        @forelse($incidentes as $inc)
        @php $ti = $inc->tipoInfo(); @endphp
        <div class="incidente">
            <div class="incidente-data">{{ $inc->data_inicio->format('d/m/Y H:i') }}
                @if($inc->data_fim) → {{ $inc->data_fim->format('d/m/Y H:i') }} @endif
            </div>
            <div class="incidente-titulo">
                <span style="color:{{ $ti['cor'] }};">{{ $ti['icon'] }}</span>
                {{ $inc->titulo }}
            </div>
            <div class="incidente-msg">{{ $inc->mensagem }}</div>
        </div>
        @empty
        <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">
            ✅ Nenhum incidente nos últimos 30 dias.
        </div>
        @endforelse
    </div>

</div>

<div class="footer">
    Software Jurídico &nbsp;·&nbsp; <a href="/login">Acessar o sistema</a>
    &nbsp;·&nbsp; Página atualiza automaticamente
</div>

</body>
</html>
