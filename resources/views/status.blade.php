<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do Sistema — Sistema Jurídico</title>
    <meta name="description" content="Status em tempo real do Sistema Jurídico.">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:     #f8fafc;
            --white:  #ffffff;
            --text:   #1e293b;
            --muted:  #64748b;
            --border: #e2e8f0;
            --green:  #16a34a;
            --yellow: #d97706;
            --red:    #dc2626;
            --blue:   #2563eb;
            --navy:   #1a3a5c;
        }

        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        /* Header */
        .header { background: var(--navy); color: #fff; padding: 0 24px; }
        .header-inner { max-width: 860px; margin: 0 auto; padding: 20px 0; display: flex; align-items: center; justify-content: space-between; }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-box { width: 40px; height: 40px; background: rgba(255,255,255,.12); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .logo-box svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 2; }
        .logo-name { font-size: 18px; font-weight: 800; letter-spacing: 1px; }
        .logo-sub  { font-size: 12px; color: rgba(255,255,255,.5); }
        .refresh-info { font-size: 12px; color: rgba(255,255,255,.45); text-align: right; }

        /* Hero */
        .hero { background: var(--white); border-bottom: 1px solid var(--border); padding: 36px 24px; text-align: center; }
        .hero-inner { max-width: 860px; margin: 0 auto; }

        .status-overall { display: inline-flex; align-items: center; gap: 10px; padding: 12px 24px; border-radius: 99px; font-size: 16px; font-weight: 700; margin-bottom: 12px; border: 1.5px solid; }
        .status-overall.ok    { background: #f0fdf4; color: var(--green); border-color: #bbf7d0; }
        .status-overall.warn  { background: #fffbeb; color: var(--yellow); border-color: #fcd34d; }
        .status-overall.error { background: #fff5f5; color: var(--red); border-color: #fecaca; }
        .hero-sub { font-size: 13px; color: var(--muted); }

        /* Content */
        .content { max-width: 860px; margin: 0 auto; padding: 32px 24px; }
        .section-title { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; margin-bottom: 10px; }

        /* Components list */
        .comp-list { background: var(--white); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 28px; }
        .comp-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid var(--border); }
        .comp-item:last-child { border-bottom: none; }
        .comp-name { font-size: 14px; font-weight: 600; color: var(--text); }
        .comp-desc { font-size: 12px; color: var(--muted); margin-top: 2px; }

        .comp-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; flex-shrink: 0; }
        .comp-badge.ok    { background: #f0fdf4; color: var(--green); }
        .comp-badge.warn  { background: #fffbeb; color: var(--yellow); }
        .comp-badge.error { background: #fff5f5; color: var(--red); }
        .comp-badge.info  { background: #eff6ff; color: var(--blue); }

        /* Incidents */
        .incident-card { border-radius: 10px; padding: 16px 18px; margin-bottom: 10px; border: 1px solid; }
        .incident-title { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
        .incident-meta  { font-size: 12px; margin-bottom: 8px; opacity: .7; }
        .incident-body  { font-size: 13px; line-height: 1.6; }

        /* Footer */
        .footer { text-align: center; padding: 24px; font-size: 12px; color: var(--muted); border-top: 1px solid var(--border); margin-top: 20px; }

        @media (max-width: 600px) {
            .header-inner { flex-direction: column; gap: 10px; text-align: center; }
            .refresh-info { text-align: center; }
        }
    </style>
    <script>setTimeout(() => location.reload(), 60000);</script>
</head>
<body>

@php
use App\Models\Comunicado;

$comunicadosAtivos = Comunicado::ativos()->orderByDesc('created_at')->get();
$emergencial = $comunicadosAtivos->firstWhere('tipo', 'manutencao_emergencial');
$programada  = $comunicadosAtivos->firstWhere('tipo', 'manutencao_programada');

if ($emergencial) {
    $sg = ['class' => 'error', 'icon' => '🔴', 'texto' => 'Degradação de Serviço'];
} elseif ($programada) {
    $sg = ['class' => 'warn',  'icon' => '🔧', 'texto' => 'Manutenção em Andamento'];
} else {
    $sg = ['class' => 'ok',    'icon' => '✅', 'texto' => 'Todos os sistemas operacionais'];
}
@endphp

<header class="header">
    <div class="header-inner">
        <div class="logo-area">
            <div class="logo-box">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div>
                <div class="logo-name">Sistema Jurídico</div>
                <div class="logo-sub">Status do Sistema</div>
            </div>
        </div>
        <div class="refresh-info">
            Atualizado: {{ now()->format('d/m/Y H:i:s') }}<br>
            <span style="color:rgba(255,255,255,.25);">Refresh automático em 60s</span>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-inner">
        <div class="status-overall {{ $sg['class'] }}">
            {{ $sg['icon'] }} {{ $sg['texto'] }}
        </div>
        <div class="hero-sub">{{ now()->format('d/m/Y \à\s H:i') }}</div>
    </div>
</section>

<div class="content">

    {{-- Componentes --}}
    <div class="section-title">Componentes do Sistema</div>
    <div class="comp-list">
        @php
        $statusComp = $emergencial ? 'error' : ($programada ? 'warn' : 'ok');
        $labelMap = [
            'ok'    => ['class' => 'ok',    'icon' => '●', 'label' => 'Operacional'],
            'warn'  => ['class' => 'warn',  'icon' => '◐', 'label' => 'Degradado'],
            'error' => ['class' => 'error', 'icon' => '●', 'label' => 'Fora do ar'],
        ];
        $comps = [
            ['Sistema Principal',   'Acesso e autenticação de usuários',        $statusComp],
            ['Gestão de Processos', 'Cadastro e acompanhamento de processos',    $statusComp],
            ['Módulo Financeiro',   'Lançamentos, honorários e relatórios',      $statusComp],
            ['Portal do Cliente',   'Acesso para clientes externos',             $statusComp],
            ['Notificações',        'E-mail, WhatsApp e alertas',                'ok'],
            ['Monitoramento TJSP',  'Consulta automática de andamentos TJSP',    'ok'],
            ['Armazenamento',       'Upload e acesso a documentos',              'ok'],
            ['IA e Assistente',     'Análise de processos e minutas com IA',     'ok'],
        ];
        @endphp
        @foreach($comps as [$nome, $desc, $status])
        @php $st = $labelMap[$status]; @endphp
        <div class="comp-item">
            <div>
                <div class="comp-name">{{ $nome }}</div>
                <div class="comp-desc">{{ $desc }}</div>
            </div>
            <span class="comp-badge {{ $st['class'] }}">{{ $st['icon'] }} {{ $st['label'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Comunicados ativos --}}
    @if($comunicadosAtivos->isNotEmpty())
    <div class="section-title">Comunicados Ativos</div>
    @foreach($comunicadosAtivos as $c)
    @php $info = $c->tipoInfo(); @endphp
    <div class="incident-card" style="background:{{ $info['bg'] }};border-color:{{ $info['border'] }};">
        <div class="incident-title" style="color:{{ $info['cor'] }};">{{ $info['icon'] }} {{ $c->titulo }}</div>
        <div class="incident-meta" style="color:{{ $info['cor'] }};">
            {{ $info['label'] }} &bull;
            Desde {{ $c->data_inicio->format('d/m/Y H:i') }}
            @if($c->data_fim) &bull; Encerramento previsto: {{ $c->data_fim->format('d/m/Y H:i') }} @endif
        </div>
        <div class="incident-body" style="color:{{ $info['cor'] }};">{{ $c->mensagem }}</div>
    </div>
    @endforeach
    <div style="margin-bottom:28px;"></div>
    @endif

    {{-- Histórico --}}
    @php
    $historico = Comunicado::where('data_inicio', '>=', now()->subDays(30))
        ->where(function($q) { $q->whereNotNull('data_fim')->where('data_fim', '<', now()); })
        ->orderByDesc('data_inicio')
        ->limit(15)
        ->get();
    @endphp

    <div class="section-title">Histórico — Últimos 30 dias</div>
    <div class="comp-list">
        @forelse($historico as $c)
        @php $info = $c->tipoInfo(); @endphp
        <div class="comp-item">
            <div>
                <div class="comp-name" style="color:{{ $info['cor'] }};">{{ $info['icon'] }} {{ $c->titulo }}</div>
                <div class="comp-desc">
                    {{ $c->data_inicio->format('d/m/Y H:i') }}
                    @if($c->data_fim) — {{ $c->data_fim->format('d/m/Y H:i') }} @endif
                </div>
            </div>
            <span class="comp-badge info">{{ $info['label'] }}</span>
        </div>
        @empty
        <div class="comp-item">
            <div>
                <div class="comp-name" style="color:var(--green);">✅ Sem incidentes registrados</div>
                <div class="comp-desc">Nenhuma ocorrência nos últimos 30 dias.</div>
            </div>
        </div>
        @endforelse
    </div>

</div>

<footer class="footer">
    Sistema Jurídico &bull;
    <a href="{{ route('login') }}" style="color:var(--navy);font-weight:600;">Acessar o sistema</a>
    &bull; Página atualiza automaticamente a cada 60 segundos
</footer>

</body>
</html>
