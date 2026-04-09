<div class="jur-dashboard">
    <?php
        $usuario = auth('usuarios')->user()?->nome ?? 'Advogado';
        $primeiroNome = explode(' ', trim($usuario))[0] ?: 'Advogado';
        $horaAtual = (int) now()->format('H');
        $saudacao = $horaAtual < 12 ? 'Bom dia' : ($horaAtual < 18 ? 'Boa tarde' : 'Boa noite');
        $totalAlertas = $stats['prazos_vencidos'] + $stats['prazos_amanha'] + $stats['processos_parados'];
        $agendaResumo = count($agendaHoje) > 0
            ? count($agendaHoje) . ' compromisso(s) na agenda'
            : 'agenda livre no momento';
    ?>

    <section class="jur-context">
        <div class="jur-context-copy">
            <span class="jur-context-eyebrow">Painel do advogado</span>
            <h1><?php echo e($saudacao); ?>, <?php echo e($primeiroNome); ?></h1>
            <p>Priorize o que vence primeiro, acompanhe a pauta do dia e mantenha a carteira em movimento com menos ruído visual.</p>
        </div>
        <div class="jur-context-status">
            <span class="jur-status-pill"><?php echo e($stats['processos_ativos']); ?> processos ativos</span>
            <span class="jur-status-pill <?php echo e($totalAlertas > 0 ? 'is-alert' : ''); ?>"><?php echo e($totalAlertas); ?> pontos de atenção</span>
            <span class="jur-status-pill"><?php echo e($agendaResumo); ?></span>
        </div>
    </section>

    <section class="jur-hero">
        <div class="jur-hero-main tone-<?php echo e($painelPrioridade['destaque']['tonalidade'] ?? 'calmo'); ?>">
            <div class="jur-hero-header">
                <span class="jur-section-kicker"><?php echo e($painelPrioridade['destaque']['contexto'] ?? 'Operação jurídica'); ?></span>
                <span class="jur-hero-badge">Foco do dia</span>
            </div>
            <h2><?php echo e($painelPrioridade['destaque']['titulo'] ?? 'Sua rotina está organizada.'); ?></h2>
            <p><?php echo e($painelPrioridade['destaque']['descricao'] ?? 'Use este painel para definir a próxima melhor ação.'); ?></p>
            <div class="jur-hero-actions">
                <a class="jur-btn jur-btn-primary" href="<?php echo e($painelPrioridade['destaque']['cta_url'] ?? route('agenda')); ?>"><?php echo e($painelPrioridade['destaque']['cta_label'] ?? 'Abrir agenda'); ?></a>
                <a class="jur-btn jur-btn-secondary" href="<?php echo e($painelPrioridade['destaque']['aux_url'] ?? route('prazos')); ?>"><?php echo e($painelPrioridade['destaque']['aux_label'] ?? 'Ver prazos'); ?></a>
            </div>
        </div>

        <div class="jur-radar-card">
            <div class="jur-card-head">
                <div>
                    <span class="jur-section-kicker">Radar operacional</span>
                    <h3>Leitura rápida do dia</h3>
                </div>
            </div>
            <div class="jur-kpi-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($painelPrioridade['indicadores'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="jur-kpi tone-<?php echo e($indicador['tom']); ?>">
                        <span><?php echo e($indicador['label']); ?></span>
                        <strong><?php echo e($indicador['valor']); ?></strong>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="jur-metrics">
        <a class="jur-metric-card" href="<?php echo e(route('processos')); ?>">
            <span class="jur-metric-label">Carteira monitorada</span>
            <strong><?php echo e($stats['processos_ativos']); ?></strong>
            <small>processos ativos em acompanhamento</small>
        </a>
        <a class="jur-metric-card" href="<?php echo e(route('prazos')); ?>">
            <span class="jur-metric-label">Prazos em 7 dias</span>
            <strong><?php echo e($stats['prazos_7dias']); ?></strong>
            <small>itens para preparar com antecedência</small>
        </a>
        <a class="jur-metric-card" href="<?php echo e(route('processos')); ?>">
            <span class="jur-metric-label">Sem andamento</span>
            <strong><?php echo e($stats['processos_parados']); ?></strong>
            <small>casos parados há mais de 30 dias</small>
        </a>
        <a class="jur-metric-card" href="<?php echo e(route('agenda')); ?>">
            <span class="jur-metric-label">Audiências hoje</span>
            <strong><?php echo e($stats['audiencias_hoje']); ?></strong>
            <small>compromissos que pedem preparação</small>
        </a>
    </section>

    <div class="jur-grid">
        <div class="jur-main">
            <section class="jur-card">
                <div class="jur-card-head">
                    <div>
                        <span class="jur-section-kicker">Fila operacional</span>
                        <h3>Próximas ações do advogado</h3>
                    </div>
                    <a class="jur-link" href="<?php echo e(route('prazos')); ?>">Abrir fila completa</a>
                </div>

                <div class="jur-lanes">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tarefasPrioritarias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="jur-lane">
                            <div class="jur-lane-head">
                                <h4><?php echo e($grupo['titulo']); ?></h4>
                                <p><?php echo e($grupo['descricao']); ?></p>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $grupo['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <article class="jur-task tone-<?php echo e($item['tom']); ?>">
                                    <div class="jur-task-main">
                                        <div class="jur-task-top">
                                            <span class="jur-task-tag"><?php echo e($item['tag']); ?></span>
                                            <span class="jur-task-meta"><?php echo e($item['meta']); ?></span>
                                        </div>
                                        <h5><?php echo e($item['titulo']); ?></h5>
                                        <p class="jur-task-ref"><?php echo e($item['referencia']); ?></p>
                                        <p class="jur-task-copy"><?php echo e($item['apoio']); ?></p>
                                    </div>
                                    <a class="jur-task-cta" href="<?php echo e($item['cta_url']); ?>"><?php echo e($item['cta_label']); ?></a>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="jur-empty"><?php echo e($grupo['vazio']); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>

            <section class="jur-card">
                <div class="jur-card-head">
                    <div>
                        <span class="jur-section-kicker">Andamentos recentes</span>
                        <h3>Leitura da movimentação da carteira</h3>
                    </div>
                    <a class="jur-link" href="<?php echo e(route('processos')); ?>">Ver todos</a>
                </div>

                <div class="jur-feed">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ultimasAtividades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $atividade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $descricao = mb_strtolower($atividade['descricao'] ?? '');
                            $tom = str_contains($descricao, 'prazo') || str_contains($descricao, 'intima')
                                ? 'critico'
                                : (str_contains($descricao, 'senten') || str_contains($descricao, 'decis')
                                    ? 'agenda'
                                    : 'info');
                        ?>
                        <article class="jur-feed-item tone-<?php echo e($tom); ?>">
                            <div class="jur-feed-line"></div>
                            <div class="jur-feed-body">
                                <div class="jur-feed-top">
                                    <strong><?php echo e($atividade['numero']); ?></strong>
                                    <span><?php echo e($atividade['quando']); ?></span>
                                </div>
                                <p><?php echo e($atividade['descricao']); ?></p>
                                <small><?php echo e($atividade['cliente']); ?></small>
                            </div>
                            <a class="jur-link" href="<?php echo e(route('processos.show', $atividade['processo_id'])); ?>">Abrir</a>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="jur-empty">Nenhuma movimentação recente encontrada.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
        </div>

        <aside class="jur-side">
            <section class="jur-card">
                <div class="jur-card-head">
                    <div>
                        <span class="jur-section-kicker">Agenda</span>
                        <h3>Compromissos de hoje</h3>
                    </div>
                    <a class="jur-link" href="<?php echo e(route('agenda')); ?>">Abrir agenda</a>
                </div>

                <div class="jur-agenda">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $agendaHoje; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="jur-agenda-item <?php echo e($item['urgente'] ? 'is-urgent' : ''); ?>">
                            <span class="jur-agenda-time"><?php echo e($item['hora']); ?></span>
                            <div class="jur-agenda-body">
                                <strong><?php echo e($item['titulo']); ?></strong>
                                <p><?php echo e($item['processo'] ?: ($item['tipo'] ?: 'Compromisso do dia')); ?></p>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="jur-empty">Nenhum compromisso agendado para hoje.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>

            <section class="jur-card jur-insight-card">
                <div class="jur-card-head">
                    <div>
                        <span class="jur-section-kicker">Inteligência de apoio</span>
                        <h3>Resumo estratégico</h3>
                    </div>
                </div>
                <ul class="jur-insight-list">
                    <li><?php echo e($stats['processos_criticos']); ?> processo(s) com score crítico.</li>
                    <li><?php echo e($stats['prazos_7dias']); ?> prazo(s) exigindo preparação nesta semana.</li>
                    <li><?php echo e($stats['andamentos_hoje']); ?> novo(s) andamento(s) chegaram hoje.</li>
                </ul>
                <a class="jur-btn jur-btn-dark" href="<?php echo e(route('assistente')); ?>">Abrir assistente</a>
            </section>

            <section class="jur-card">
                <div class="jur-card-head">
                    <div>
                        <span class="jur-section-kicker">Carteira sensível</span>
                        <h3>Casos que merecem atenção</h3>
                    </div>
                </div>
                <div class="jur-watchlist">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = array_slice($processosParados, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="jur-watch-item">
                            <strong><?php echo e($processo['numero']); ?></strong>
                            <p><?php echo e($processo['cliente']); ?></p>
                            <small><?php echo e($processo['dias']); ?> dia(s) sem andamento</small>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="jur-empty">Nenhum processo parado acima do limite configurado.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
        </aside>
    </div>
    <style>
    .jur-dashboard {
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: #10233f;
    }

    .jur-context,
    .jur-card,
    .jur-metric-card,
    .jur-hero-main,
    .jur-radar-card {
        border: 1px solid #d7e0eb;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 24px 60px rgba(15, 39, 71, 0.08);
    }

    .jur-context {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        padding: 26px 28px;
        background: linear-gradient(135deg, #f8fafc 0%, #eef3f8 100%);
    }

    .jur-context-copy h1,
    .jur-hero-main h2,
    .jur-card-head h3,
    .jur-lane-head h4,
    .jur-task h5 {
        font-family: Georgia, 'Times New Roman', serif;
    }

    .jur-context-copy h1 {
        margin: 6px 0 8px;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -0.03em;
    }

    .jur-context-copy p {
        max-width: 720px;
        color: #516175;
        line-height: 1.6;
    }

    .jur-context-status {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .jur-section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #59708d;
    }

    .jur-context-eyebrow {
        display: inline-flex;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #6a7d91;
    }

    .jur-status-pill,
    .jur-hero-badge,
    .jur-task-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #edf2f7;
        color: #29425f;
    }

    .jur-status-pill.is-alert {
        background: #fff1ec;
        color: #b4532c;
    }

    .jur-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(280px, 0.95fr);
        gap: 20px;
    }

    .jur-hero-main,
    .jur-radar-card,
    .jur-card {
        padding: 24px;
    }

    .jur-hero-main {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f5f8fb 100%);
    }

    .jur-hero-main::after {
        content: '';
        position: absolute;
        inset: auto -32px -40px auto;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(31, 78, 121, 0.12) 0%, rgba(31, 78, 121, 0) 72%);
    }

    .jur-hero-main.tone-critico {
        background: linear-gradient(135deg, #fff8f6 0%, #fff0ea 100%);
    }

    .jur-hero-main.tone-atencao {
        background: linear-gradient(135deg, #fffdf8 0%, #fff7e8 100%);
    }

    .jur-hero-main.tone-agenda {
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    }

    .jur-hero-main.tone-risco {
        background: linear-gradient(135deg, #fefbf8 0%, #f7efe7 100%);
    }

    .jur-hero-header,
    .jur-card-head,
    .jur-task-top,
    .jur-feed-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .jur-hero-main h2 {
        margin: 16px 0 10px;
        max-width: 720px;
        font-size: 2rem;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .jur-hero-main p {
        max-width: 700px;
        color: #4e6279;
        line-height: 1.65;
    }

    .jur-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .jur-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 700;
        transition: transform 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
    }

    .jur-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(18, 35, 63, 0.12);
    }

    .jur-btn-primary {
        background: #143a5c;
        color: #fff;
    }

    .jur-btn-secondary {
        background: #edf2f7;
        color: #18324f;
    }

    .jur-btn-dark {
        width: 100%;
        background: linear-gradient(135deg, #102c48 0%, #183f63 100%);
        color: #fff;
    }

    .jur-kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .jur-kpi,
    .jur-metric-card {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .jur-kpi {
        padding: 16px;
        border-radius: 18px;
        background: #f4f7fa;
    }

    .jur-kpi span,
    .jur-metric-label,
    .jur-task-meta,
    .jur-feed-top span,
    .jur-agenda-body p,
    .jur-watch-item p,
    .jur-watch-item small,
    .jur-empty {
        color: #647689;
    }

    .jur-kpi strong,
    .jur-metric-card strong {
        font-size: 1.8rem;
        line-height: 1;
        color: #10233f;
    }

    .jur-kpi.tone-critico strong,
    .jur-task.tone-critico .jur-task-tag {
        color: #9f2f2b;
    }

    .jur-kpi.tone-atencao strong,
    .jur-task.tone-atencao .jur-task-tag {
        color: #a16207;
    }

    .jur-kpi.tone-agenda strong,
    .jur-task.tone-agenda .jur-task-tag {
        color: #1d4f91;
    }

    .jur-kpi.tone-info strong {
        color: #155e75;
    }

    .jur-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .jur-metric-card {
        padding: 18px 20px;
        text-decoration: none;
        transition: transform 0.16s ease, box-shadow 0.16s ease;
    }

    .jur-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(12, 31, 51, 0.1);
    }

    .jur-metric-card small {
        line-height: 1.5;
    }

    .jur-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(300px, 0.9fr);
        gap: 20px;
        align-items: start;
    }

    .jur-main,
    .jur-side {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .jur-card-head {
        margin-bottom: 20px;
    }

    .jur-card-head h3 {
        margin-top: 6px;
        font-size: 1.55rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #10233f;
    }

    .jur-link {
        color: #173f63;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .jur-lanes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .jur-lane {
        padding: 18px;
        border-radius: 20px;
        background: #f7fafc;
        border: 1px solid #dfe7ef;
    }

    .jur-lane-head h4 {
        font-size: 1.15rem;
        margin-bottom: 4px;
        color: #12233d;
    }

    .jur-lane-head p {
        color: #6c7d90;
        font-size: 0.92rem;
        line-height: 1.5;
        margin-bottom: 14px;
    }

    .jur-task {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 16px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid #dde6ef;
    }

    .jur-task + .jur-task,
    .jur-watch-item + .jur-watch-item,
    .jur-agenda-item + .jur-agenda-item {
        margin-top: 12px;
    }

    .jur-task-main h5 {
        margin: 10px 0 6px;
        font-size: 1.05rem;
        color: #10233f;
    }

    .jur-task-ref {
        font-weight: 700;
        color: #2c425d;
        margin-bottom: 6px;
    }

    .jur-task-copy,
    .jur-feed-body p,
    .jur-insight-list li {
        line-height: 1.6;
        color: #5e7185;
    }

    .jur-task-cta {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 40px;
        border-radius: 12px;
        background: #eef3f7;
        color: #18324f;
        text-decoration: none;
        font-weight: 700;
    }

    .jur-task.tone-critico {
        border-color: #f4d6d0;
        background: #fff9f8;
    }

    .jur-task.tone-atencao {
        border-color: #efe1bd;
        background: #fffdf8;
    }

    .jur-task.tone-agenda {
        border-color: #d8e3f2;
        background: #f9fbff;
    }

    .jur-task.tone-risco {
        border-color: #eadfd2;
        background: #fdfaf6;
    }

    .jur-feed {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .jur-feed-item {
        display: grid;
        grid-template-columns: 4px minmax(0, 1fr) auto;
        gap: 16px;
        padding: 18px;
        border-radius: 18px;
        background: #fbfcfd;
        border: 1px solid #e0e7ef;
    }

    .jur-feed-line {
        border-radius: 999px;
        background: #8aa0b6;
    }

    .jur-feed-item.tone-critico .jur-feed-line {
        background: #bb4a41;
    }

    .jur-feed-item.tone-agenda .jur-feed-line {
        background: #2f5b93;
    }

    .jur-feed-item.tone-info .jur-feed-line {
        background: #2d728f;
    }

    .jur-feed-top {
        margin-bottom: 8px;
    }

    .jur-agenda-item {
        display: grid;
        grid-template-columns: 64px minmax(0, 1fr);
        gap: 14px;
        padding: 14px 16px;
        border-radius: 18px;
        background: #f7fafc;
        border: 1px solid #e0e7ef;
    }

    .jur-agenda-item.is-urgent {
        background: #fff8f7;
        border-color: #f2d3cd;
    }

    .jur-agenda-time {
        font-size: 1rem;
        font-weight: 800;
        color: #173f63;
    }

    .jur-agenda-body strong,
    .jur-watch-item strong {
        color: #11243f;
    }

    .jur-insight-card {
        background: linear-gradient(180deg, #132e49 0%, #183c5e 100%);
        color: #fff;
    }

    .jur-insight-card .jur-card-head h3,
    .jur-insight-card .jur-section-kicker,
    .jur-insight-list li {
        color: #fff;
    }

    .jur-insight-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 0 0 18px;
        padding: 0;
    }

    .jur-watch-item {
        padding: 14px 16px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e0e7ef;
    }

    .jur-empty {
        padding: 18px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px dashed #cfd9e4;
        text-align: center;
    }

    @media (max-width: 1180px) {
        .jur-hero,
        .jur-grid {
            grid-template-columns: 1fr;
        }

        .jur-metrics,
        .jur-lanes {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .jur-context {
            padding: 22px 20px;
        }

        .jur-context,
        .jur-card-head,
        .jur-hero-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .jur-context-status {
            justify-content: flex-start;
        }

        .jur-metrics,
        .jur-lanes,
        .jur-kpi-grid {
            grid-template-columns: 1fr;
        }

        .jur-hero-main,
        .jur-radar-card,
        .jur-card {
            padding: 20px;
        }

        .jur-hero-main h2,
        .jur-context-copy h1,
        .jur-card-head h3 {
            font-size: 1.6rem;
        }
    }
</style>
</div>
<?php /**PATH C:\projetos\saproweb-base\resources\views/livewire/dashboard-redesign.blade.php ENDPATH**/ ?>