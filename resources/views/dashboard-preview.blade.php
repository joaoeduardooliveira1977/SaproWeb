@extends('layouts.app')
@section('page-title', 'Preview Dashboard')
@section('content')
@php
    $kpis = [
        ['label' => 'Prazos Críticos', 'value' => 12, 'meta' => '4 vencem hoje', 'tone' => 'red'],
        ['label' => 'Audiências', 'value' => 5, 'meta' => '2 em preparação', 'tone' => 'violet'],
        ['label' => 'Andamentos', 'value' => 28, 'meta' => 'Últimas 24h', 'tone' => 'teal'],
        ['label' => 'Honorários', 'value' => 'R$ 48k', 'meta' => 'Janela de cobrança', 'tone' => 'amber'],
    ];
    $timeline = [
        ['time' => '09:00', 'title' => 'Revisar prazo recursal', 'tag' => 'Urgente', 'tone' => 'red'],
        ['time' => '10:30', 'title' => 'Audiência - Ferreira Construções', 'tag' => 'Agenda', 'tone' => 'violet'],
        ['time' => '14:00', 'title' => 'Despachar processo com andamento parado', 'tag' => 'Carteira', 'tone' => 'blue'],
        ['time' => '16:30', 'title' => 'Enviar minuta para aprovação do cliente', 'tag' => 'IA + revisão', 'tone' => 'teal'],
    ];
    $bars = [
        ['label' => 'Seg', 'value' => 35],
        ['label' => 'Ter', 'value' => 54],
        ['label' => 'Qua', 'value' => 72],
        ['label' => 'Qui', 'value' => 48],
        ['label' => 'Sex', 'value' => 63],
    ];
    $watchlist = [
        ['title' => '349 processos sem movimento', 'copy' => 'A carteira precisa de uma rodada de reativação.'],
        ['title' => '12 prazos com janela curta', 'copy' => 'Vale distribuir a preparação entre hoje e amanhã.'],
        ['title' => '5 audiências na semana', 'copy' => 'Concentrar estratégia e documentos no bloco da manhã.'],
    ];
@endphp

<div class="preview-board">
    <section class="preview-hero">
        <div class="preview-hero-copy">
            <span class="preview-kicker">Preview conceitual</span>
            <h1>Dashboard jurídico mais visual, analítico e vivo.</h1>
            <p>Esta proposta troca a sensação de tela vazia por um painel de comando com hero de prioridades, gráficos centrais e uma agenda mais executiva.</p>
            <div class="preview-hero-actions">
                <a href="{{ route('dashboard') }}" class="preview-btn preview-btn-primary">Voltar ao dashboard atual</a>
                <a href="{{ route('prazos') }}" class="preview-btn preview-btn-secondary">Abrir prazos</a>
            </div>
        </div>

        <div class="preview-signal-card">
            <div class="preview-signal-top">
                <span class="preview-dot"></span>
                <strong>Foco do dia</strong>
            </div>
            <h2>Priorizar recursais e reativar a carteira parada.</h2>
            <ul>
                <li>3 prazos exigem ação até o fim do dia</li>
                <li>2 audiências pedem revisão de documentos</li>
                <li>1 lote de processos pode gerar contato proativo</li>
            </ul>
        </div>
    </section>

    <section class="preview-kpis">
        @foreach($kpis as $kpi)
            <article class="preview-kpi tone-{{ $kpi['tone'] }}">
                <span>{{ $kpi['label'] }}</span>
                <strong>{{ $kpi['value'] }}</strong>
                <small>{{ $kpi['meta'] }}</small>
            </article>
        @endforeach
    </section>

    <section class="preview-grid">
        <div class="preview-main">
            <article class="preview-card preview-priority">
                <div class="preview-card-head">
                    <div>
                        <span class="preview-kicker">Ação imediata</span>
                        <h3>O que mover agora</h3>
                    </div>
                    <span class="preview-pill">12 alertas ativos</span>
                </div>

                <div class="preview-timeline">
                    @foreach($timeline as $item)
                        <div class="preview-timeline-item">
                            <span class="preview-time">{{ $item['time'] }}</span>
                            <div class="preview-timeline-body">
                                <strong>{{ $item['title'] }}</strong>
                                <small>{{ $item['tag'] }}</small>
                            </div>
                            <span class="preview-chip tone-{{ $item['tone'] }}">{{ $item['tag'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <div class="preview-analytics">
                <article class="preview-card preview-chart-card">
                    <div class="preview-card-head">
                        <div>
                            <span class="preview-kicker">Status dos prazos</span>
                            <h3>Leitura em 5 segundos</h3>
                        </div>
                    </div>

                    <div class="preview-donut-wrap">
                        <div class="preview-donut">
                            <div class="preview-donut-inner">
                                <strong>68%</strong>
                                <small>em dia</small>
                            </div>
                        </div>
                        <div class="preview-legend">
                            <div><span class="tone-dot red"></span> Críticos</div>
                            <div><span class="tone-dot amber"></span> Em atenção</div>
                            <div><span class="tone-dot teal"></span> Saudáveis</div>
                        </div>
                    </div>
                </article>

                <article class="preview-card preview-bars-card">
                    <div class="preview-card-head">
                        <div>
                            <span class="preview-kicker">Movimentação</span>
                            <h3>Atividade da semana</h3>
                        </div>
                    </div>

                    <div class="preview-bars">
                        @foreach($bars as $bar)
                            <div class="preview-bar-col">
                                <div class="preview-bar" style="height: {{ $bar['value'] }}%;"></div>
                                <span>{{ $bar['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </div>

        <aside class="preview-side">
            <article class="preview-card preview-ai-card">
                <span class="preview-kicker">Assistente IA</span>
                <h3>Resumo inteligente da carteira</h3>
                <p>A IA entra como apoio estratégico, não como o centro da tela.</p>
                <a href="{{ route('assistente') }}" class="preview-btn preview-btn-dark">Analisar com IA</a>
            </article>

            <article class="preview-card">
                <div class="preview-card-head">
                    <div>
                        <span class="preview-kicker">Radar</span>
                        <h3>Watchlist do escritório</h3>
                    </div>
                </div>
                <div class="preview-watchlist">
                    @foreach($watchlist as $watch)
                        <div class="preview-watch-item">
                            <strong>{{ $watch['title'] }}</strong>
                            <small>{{ $watch['copy'] }}</small>
                        </div>
                    @endforeach
                </div>
            </article>
        </aside>
    </section>
</div>

<style>
    .preview-board {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding: 8px 4px 24px;
    }
    .preview-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) 360px;
        gap: 18px;
        align-items: stretch;
    }
    .preview-hero-copy,
    .preview-signal-card,
    .preview-card,
    .preview-kpi {
        border-radius: 24px;
        border: 1px solid #dbe4ef;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
    }
    .preview-hero-copy {
        padding: 34px;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, .35), transparent 24%),
            linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        color: #fff;
    }
    .preview-kicker {
        display: inline-flex;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
        color: inherit;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .preview-hero-copy h1 {
        margin: 18px 0 10px;
        font-size: 2.4rem;
        line-height: 1.03;
        color: #fff;
        max-width: 700px;
    }
    .preview-hero-copy p {
        max-width: 620px;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255, 255, 255, .86);
    }
    .preview-hero-actions {
        display: flex;
        gap: 12px;
        margin-top: 22px;
        flex-wrap: wrap;
    }
    .preview-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 16px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
    }
    .preview-btn-primary {
        background: #fff;
        color: #0f172a;
    }
    .preview-btn-secondary {
        background: rgba(255, 255, 255, .14);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .18);
    }
    .preview-btn-dark {
        background: #111827;
        color: #fff;
        width: 100%;
    }
    .preview-signal-card {
        padding: 28px;
        background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%);
    }
    .preview-signal-top {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #9a3412;
        font-size: 13px;
    }
    .preview-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #f97316;
        box-shadow: 0 0 0 6px rgba(249, 115, 22, .12);
    }
    .preview-signal-card h2 {
        margin: 16px 0 12px;
        font-size: 1.55rem;
        line-height: 1.15;
        color: #7c2d12;
    }
    .preview-signal-card ul {
        margin: 0;
        padding-left: 18px;
        color: #7c2d12;
        line-height: 1.8;
        font-size: 13px;
    }
    .preview-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    .preview-kpi {
        padding: 18px 20px;
        background: #fff;
    }
    .preview-kpi span {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 10px;
    }
    .preview-kpi strong {
        display: block;
        font-size: 2rem;
        line-height: 1;
        color: #0f172a;
    }
    .preview-kpi small {
        display: block;
        margin-top: 8px;
        font-size: 12px;
        color: #475569;
    }
    .preview-kpi.tone-red { background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%); }
    .preview-kpi.tone-violet { background: linear-gradient(180deg, #f5f3ff 0%, #ffffff 100%); }
    .preview-kpi.tone-teal { background: linear-gradient(180deg, #ecfeff 0%, #ffffff 100%); }
    .preview-kpi.tone-amber { background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%); }
    .preview-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) 330px;
        gap: 18px;
    }
    .preview-main,
    .preview-side {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .preview-card {
        padding: 24px;
        background: #fff;
    }
    .preview-card-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 18px;
    }
    .preview-card-head h3 {
        margin-top: 8px;
        font-size: 1.2rem;
        color: #0f172a;
    }
    .preview-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 800;
    }
    .preview-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .preview-timeline-item {
        display: grid;
        grid-template-columns: 66px minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 14px;
        border-radius: 18px;
        background: #f8fafc;
    }
    .preview-time {
        font-size: 13px;
        font-weight: 800;
        color: #1d4ed8;
    }
    .preview-timeline-body strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
    }
    .preview-timeline-body small {
        color: #64748b;
        font-size: 12px;
    }
    .preview-chip {
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }
    .preview-chip.tone-red { background: #fee2e2; color: #dc2626; }
    .preview-chip.tone-violet { background: #ede9fe; color: #7c3aed; }
    .preview-chip.tone-blue { background: #dbeafe; color: #2563eb; }
    .preview-chip.tone-teal { background: #ccfbf1; color: #0f766e; }
    .preview-analytics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }
    .preview-donut-wrap {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 16px;
        align-items: center;
    }
    .preview-donut {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: conic-gradient(#14b8a6 0 68%, #f59e0b 68% 84%, #ef4444 84% 100%);
        display: grid;
        place-items: center;
    }
    .preview-donut-inner {
        width: 104px;
        height: 104px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .preview-donut-inner strong {
        font-size: 1.7rem;
        color: #0f172a;
        line-height: 1;
    }
    .preview-donut-inner small {
        color: #64748b;
        font-size: 12px;
        margin-top: 4px;
    }
    .preview-legend {
        display: flex;
        flex-direction: column;
        gap: 10px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }
    .tone-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .tone-dot.red { background: #ef4444; }
    .tone-dot.amber { background: #f59e0b; }
    .tone-dot.teal { background: #14b8a6; }
    .preview-bars {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        align-items: end;
        min-height: 220px;
    }
    .preview-bar-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: end;
        gap: 10px;
        height: 100%;
    }
    .preview-bar {
        width: 100%;
        max-width: 42px;
        min-height: 18px;
        border-radius: 14px 14px 8px 8px;
        background: linear-gradient(180deg, #a78bfa 0%, #2563eb 100%);
        box-shadow: 0 12px 24px rgba(37, 99, 235, .18);
    }
    .preview-bar-col span {
        font-size: 11px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
    }
    .preview-ai-card {
        background: linear-gradient(180deg, #102a43 0%, #1e3a8a 100%);
        color: #fff;
    }
    .preview-ai-card h3 {
        margin: 12px 0 10px;
        font-size: 1.35rem;
        color: #fff;
    }
    .preview-ai-card p {
        color: rgba(255, 255, 255, .82);
        font-size: 13px;
        line-height: 1.7;
        margin-bottom: 18px;
    }
    .preview-watchlist {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .preview-watch-item {
        padding: 14px;
        border-radius: 16px;
        background: #f8fafc;
    }
    .preview-watch-item strong {
        display: block;
        color: #0f172a;
        font-size: 13px;
    }
    .preview-watch-item small {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }
    @media (max-width: 1200px) {
        .preview-hero,
        .preview-grid,
        .preview-analytics,
        .preview-kpis {
            grid-template-columns: 1fr 1fr;
        }
        .preview-grid {
            grid-template-columns: 1fr;
        }
        .preview-side {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 860px) {
        .preview-hero,
        .preview-kpis,
        .preview-analytics,
        .preview-side {
            grid-template-columns: 1fr;
        }
        .preview-donut-wrap {
            grid-template-columns: 1fr;
            justify-items: center;
        }
        .preview-timeline-item {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
