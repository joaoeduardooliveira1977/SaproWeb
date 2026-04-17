<?php $__env->startSection('page-title', 'Central Financeira'); ?>
<?php $__env->startSection('content'); ?>
<?php
    $aReceber        = (float) \App\Models\Recebimento::where('recebido', false)->sum('valor');
    $recebidoMes     = (float) \App\Models\Recebimento::where('recebido', true)
                            ->whereMonth('data_recebimento', now()->month)
                            ->whereYear('data_recebimento', now()->year)
                            ->sum('valor');
    $inadimplentes   = \App\Models\Recebimento::where('recebido', false)
                            ->where('data', '<', today())
                            ->distinct('processo_id')->count('processo_id');
    $honorariosPend  = (float) (\Illuminate\Support\Facades\DB::table('honorarios')
                            ->where('status', '!=', 'pago')
                            ->sum('valor_contrato') ?? 0);
    $cobrancasAberto = \App\Models\Recebimento::where('recebido', false)->count();
    $titulosAtivos   = $cobrancasAberto;

    // Saúde financeira (0–100)
    $saude = 100;
    if ($inadimplentes > 0)               $saude -= min(40, $inadimplentes * 10);
    if ($aReceber > 0 && $recebidoMes == 0) $saude -= 20;
    if ($honorariosPend > 0)              $saude -= 10;
    $saude = max(0, $saude);

    // Últimos recebimentos
    $ultimosRecebimentos = \App\Models\Recebimento::with('processo.cliente')
                            ->where('recebido', true)
                            ->orderByDesc('data_recebimento')
                            ->take(5)->get();

    // Inadimplência lista
    $inadimplenciaLista = \App\Models\Recebimento::with('processo.cliente')
                            ->where('recebido', false)
                            ->where('data', '<', today())
                            ->orderBy('data')
                            ->take(3)->get();
?>

<div>


<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:26px;font-weight:800;color:var(--text);margin:0;">Central Financeira</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Controle financeiro, honorários, inadimplência e relatórios em uma visão rápida da operação.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="<?php echo e(route('financeiro')); ?>"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#fff;border:1.5px solid var(--border);border-radius:10px;font-size:13px;font-weight:600;color:var(--text);text-decoration:none;">
            + Registrar Receita
        </a>
        <a href="<?php echo e(route('financeiro')); ?>"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:var(--primary);color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
            + Registrar Despesa
        </a>
        <a href="<?php echo e(route('relatorios.index')); ?>"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
            Ver Relatórios
        </a>
    </div>
</div>


<div style="display:grid;grid-template-columns:1fr 400px;gap:20px;margin-bottom:20px;" class="fin-hub-top">

    
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
            <div style="font-size:17px;font-weight:800;color:var(--text);">Resumo Inteligente</div>
            <span style="padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;
                background:<?php echo e($saude >= 70 ? '#f0fdf4' : ($saude >= 40 ? '#fffbeb' : '#fef2f2')); ?>;
                color:<?php echo e($saude >= 70 ? '#16a34a' : ($saude >= 40 ? '#d97706' : '#dc2626')); ?>;
                border:1px solid <?php echo e($saude >= 70 ? '#86efac' : ($saude >= 40 ? '#fde68a' : '#fca5a5')); ?>;">
                Saúde Financeira: <?php echo e($saude); ?>%
            </span>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inadimplentes === 0): ?>
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#16a34a;">Tudo em ordem: <strong>nenhuma inadimplência registrada.</strong></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Seu fluxo financeiro está saudável e sem risco imediato.</div>
                </div>
                <span style="font-size:20px;">✅</span>
            </div>
            <?php else: ?>
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#dc2626;">Existem <strong><?php echo e($inadimplentes); ?> cliente(s) inadimplente(s).</strong></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Priorize cobrança dos títulos mais atrasados.</div>
                </div>
                <span style="font-size:20px;">⚠️</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($honorariosPend > 0): ?>
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#d97706;">Existem <strong>R$ <?php echo e(number_format($honorariosPend, 2, ',', '.')); ?> em honorários pendentes.</strong></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e($cobrancasAberto); ?> cliente(s) aguardam cobrança ou confirmação de pagamento.</div>
                </div>
                <span style="font-size:20px;">💰</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recebidoMes == 0): ?>
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#2563a8;">Ainda não houve <strong>recebimentos registrados neste mês.</strong></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Registrar o primeiro pagamento melhora os indicadores da central.</div>
                </div>
                <span style="font-size:20px;">📋</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="<?php echo e(route('inadimplencia')); ?>"
                style="padding:9px 18px;background:var(--primary);color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                Cobrar Cliente
            </a>
            <a href="<?php echo e(route('financeiro')); ?>"
                style="padding:9px 18px;background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                Registrar Recebimento
            </a>
            <a href="<?php echo e(route('financeiro.consolidado')); ?>"
                style="padding:9px 18px;background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                Abrir Financeiro
            </a>
        </div>
    </div>

    
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;color:var(--text);">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);margin-bottom:8px;">Painel Executivo</div>
        <div style="font-size:30px;font-weight:800;letter-spacing:-1px;margin-bottom:8px;color:var(--primary);">
            R$ <?php echo e(number_format($honorariosPend + $aReceber, 2, ',', '.')); ?>

        </div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:18px;line-height:1.5;">
            Total atual em honorários pendentes, com carteira <?php echo e($inadimplentes === 0 ? 'estável e sem clientes inadimplentes' : 'com ' . $inadimplentes . ' clientes inadimplentes'); ?> no momento.
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <?php
            $submetricas = [
                ['val' => $inadimplentes,                                                 'label' => 'Clientes inadimplentes'],
                ['val' => $cobrancasAberto,                                               'label' => 'Cobranças em aberto'],
                ['val' => 'R$ ' . number_format($recebidoMes, 2, ',', '.'),              'label' => 'Recebido no mês'],
                ['val' => $inadimplentes === 0 ? 'Nenhum' : $inadimplentes . ' alertas', 'label' => $inadimplentes === 0 ? 'Sem alertas críticos' : 'Alertas ativos'],
            ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $submetricas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:12px;">
                <div style="font-size:17px;font-weight:800;color:var(--text);margin-bottom:4px;"><?php echo e($sm['val']); ?></div>
                <div style="font-size:11px;color:var(--muted);"><?php echo e($sm['label']); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>


<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;" class="fin-hub-kpis">
    <?php
    $kpis = [
        [
            'label'  => 'A Receber',
            'val'    => 'R$ ' . number_format($aReceber, 2, ',', '.'),
            'tag'    => $titulosAtivos . ' títulos ativos',
            'cor'    => '#059669',
            'icon_bg'=> '#f0fdf4',
            'route'  => route('financeiro.consolidado'),
            'svg'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        ],
        [
            'label'  => 'Recebido no Mês',
            'val'    => 'R$ ' . number_format($recebidoMes, 2, ',', '.'),
            'tag'    => $recebidoMes == 0 ? 'início de ciclo' : 'registrado este mês',
            'cor'    => '#2563a8',
            'icon_bg'=> '#eff6ff',
            'route'  => route('financeiro.consolidado'),
            'svg'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>',
        ],
        [
            'label'  => 'Clientes Inadimplentes',
            'val'    => $inadimplentes,
            'tag'    => $inadimplentes === 0 ? 'nenhum risco agora' : 'atenção imediata',
            'cor'    => '#dc2626',
            'icon_bg'=> '#fef2f2',
            'route'  => route('inadimplencia'),
            'svg'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        ],
        [
            'label'  => 'Honorários Pendentes',
            'val'    => 'R$ ' . number_format($honorariosPend, 2, ',', '.'),
            'tag'    => $cobrancasAberto . ' cobranças aguardando',
            'cor'    => '#d97706',
            'icon_bg'=> '#fffbeb',
            'route'  => route('honorarios'),
            'svg'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        ],
    ];
    ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e($k['route']); ?>" style="text-decoration:none;">
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;transition:border-color .15s,transform .15s;"
            onmouseover="this.style.borderColor='<?php echo e($k['cor']); ?>';this.style.transform='translateY(-2px)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.transform=''">
            <div style="width:40px;height:40px;border-radius:8px;background:<?php echo e($k['icon_bg']); ?>;color:<?php echo e($k['cor']); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?php echo $k['svg']; ?></div>
            <div style="min-width:0;">
                <div style="font-size:20px;font-weight:800;color:<?php echo e($k['cor']); ?>;line-height:1.1;margin-bottom:3px;"><?php echo e($k['val']); ?></div>
                <div style="font-size:12px;color:var(--text);font-weight:700;margin-bottom:4px;"><?php echo e($k['label']); ?></div>
                <div style="font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?php echo e($k['tag']); ?>

                </div>
            </div>
        </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;" class="fin-hub-bottom">

    
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div style="font-size:16px;font-weight:800;color:var(--text);">Módulos Financeiros</div>
            <a href="<?php echo e(route('financeiro.consolidado')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Ver consolidado →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;" class="fin-hub-modulos">
            <?php
            $modulos = [
                [
                    'label'    => 'Visão Geral',
                    'desc'     => 'Resumo consolidado da operação financeira com leitura rápida dos indicadores.',
                    'cor'      => '#059669',
                    'bg'       => '#f0fdf4',
                    'badge'    => 'OK',
                    'badge_bg' => '#059669',
                    'route'    => route('financeiro.consolidado'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>',
                ],
                [
                    'label'    => 'Por Processo',
                    'desc'     => 'Receitas e despesas vinculadas aos processos para análise de rentabilidade.',
                    'cor'      => '#2563a8',
                    'bg'       => '#eff6ff',
                    'badge'    => $titulosAtivos,
                    'badge_bg' => '#2563a8',
                    'route'    => route('financeiro'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
                ],
                [
                    'label'    => 'Honorários',
                    'desc'     => 'Gestão de cobranças, parcelas pendentes e acompanhamento contratual.',
                    'cor'      => '#d97706',
                    'bg'       => '#fffbeb',
                    'badge'    => $cobrancasAberto,
                    'badge_bg' => '#d97706',
                    'route'    => route('honorarios'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                ],
                [
                    'label'    => 'Contratos',
                    'desc'     => 'Contratos de honorários, consultoria e avulsos com geração automática de lançamentos.',
                    'cor'      => '#7c3aed',
                    'bg'       => '#faf5ff',
                    'badge'    => \App\Models\Contrato::where('status','ativo')->count(),
                    'badge_bg' => '#7c3aed',
                    'route'    => route('contratos'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                ],
                [
                    'label'    => 'Inadimplência',
                    'desc'     => 'Painel de atrasos, risco financeiro e ações de cobrança por prioridade.',
                    'cor'      => '#dc2626',
                    'bg'       => '#fef2f2',
                    'badge'    => $inadimplentes,
                    'badge_bg' => '#dc2626',
                    'route'    => route('inadimplencia'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                ],
                [
                    'label'    => 'Conciliação',
                    'desc'     => 'Conferência de extratos, lançamentos e validação das movimentações.',
                    'cor'      => '#7c3aed',
                    'bg'       => '#f5f3ff',
                    'badge'    => '●',
                    'badge_bg' => '#7c3aed',
                    'route'    => route('conciliacao-bancaria'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>',
                ],
                [
                    'label'    => 'Relatórios',
                    'desc'     => 'Exportações financeiras, visão por período e relatórios executivos.',
                    'cor'      => '#0891b2',
                    'bg'       => '#f0f9ff',
                    'badge'    => 'PDF',
                    'badge_bg' => '#0891b2',
                    'route'    => route('relatorios.index'),
                    'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
                ],
            ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($m['route']); ?>" style="text-decoration:none;">
                <div style="background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:16px;transition:border-color .15s,transform .15s;position:relative;"
                    onmouseover="this.style.borderColor='<?php echo e($m['cor']); ?>';this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.borderColor='var(--border)';this.style.transform=''">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                        <div style="width:38px;height:38px;border-radius:8px;background:<?php echo e($m['bg']); ?>;color:<?php echo e($m['cor']); ?>;display:flex;align-items:center;justify-content:center;"><?php echo $m['svg']; ?></div>
                        <span style="background:<?php echo e($m['badge_bg']); ?>;color:#fff;padding:3px 8px;border-radius:99px;font-size:11px;font-weight:800;"><?php echo e($m['badge']); ?></span>
                    </div>
                    <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:5px;"><?php echo e($m['label']); ?></div>
                    <div style="font-size:11px;color:var(--muted);line-height:1.5;margin-bottom:8px;"><?php echo e($m['desc']); ?></div>
                    <div style="font-size:12px;font-weight:600;color:<?php echo e($m['cor']); ?>;">Acessar módulo →</div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div style="display:flex;flex-direction:column;gap:16px;">

        
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div style="font-size:15px;font-weight:800;color:var(--text);">Últimos Recebimentos</div>
                <a href="<?php echo e(route('financeiro.consolidado')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Ver histórico</a>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ultimosRecebimentos->isEmpty()): ?>
            <div style="text-align:center;padding:20px;">
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:8px;">Nenhum recebimento registrado</div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:16px;">Comece registrando o primeiro pagamento para alimentar os indicadores da central financeira.</div>
                <a href="<?php echo e(route('financeiro')); ?>"
                    style="display:inline-block;padding:10px 20px;background:var(--primary);color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                    Registrar Agora
                </a>
            </div>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ultimosRecebimentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);">
                    <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;color:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?php echo e($rec->processo?->cliente?->nome ?? '—'); ?>

                        </div>
                        <div style="font-size:11px;color:var(--muted);">
                            <?php echo e($rec->processo?->numero ?? '—'); ?> · <?php echo e($rec->data_recebimento?->format('d/m/Y')); ?>

                        </div>
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#059669;flex-shrink:0;">
                        R$ <?php echo e(number_format($rec->valor, 2, ',', '.')); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div style="font-size:15px;font-weight:800;color:var(--text);">Inadimplência</div>
                <a href="<?php echo e(route('inadimplencia')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Ver detalhes</a>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inadimplenciaLista->isEmpty()): ?>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <div style="width:10px;height:10px;border-radius:50%;background:#16a34a;flex-shrink:0;"></div>
                <div style="font-size:13px;font-weight:700;color:#16a34a;">Nenhuma inadimplência registrada</div>
            </div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:16px;">Excelente cenário no momento. A operação está sem pendências críticas e com risco financeiro controlado.</div>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $inadimplenciaLista; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $diasAtraso = (int) now()->diffInDays($rec->data, false) * -1; ?>
                <div style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid #fca5a5;border-radius:8px;background:#fef2f2;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#fee2e2;color:#dc2626;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <?php echo e($diasAtraso); ?>d
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?php echo e($rec->processo?->cliente?->nome ?? '—'); ?>

                        </div>
                        <div style="font-size:11px;color:#dc2626;">Venceu em <?php echo e($rec->data?->format('d/m/Y')); ?></div>
                    </div>
                    <div style="font-size:13px;font-weight:700;color:#dc2626;flex-shrink:0;">
                        R$ <?php echo e(number_format($rec->valor, 2, ',', '.')); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <div style="background:#f8fafc;border-radius:8px;padding:12px;">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Risco atual</div>
                    <div style="font-size:13px;font-weight:700;color:<?php echo e($inadimplentes === 0 ? '#16a34a' : '#dc2626'); ?>;">
                        <?php echo e($inadimplentes === 0 ? 'Saudável' : 'Atenção'); ?>

                    </div>
                    <div style="font-size:11px;color:var(--muted);"><?php echo e($inadimplentes === 0 ? 'Sem cobranças vencidas' : $inadimplentes . ' cobranças vencidas'); ?></div>
                </div>
                <div style="background:#f8fafc;border-radius:8px;padding:12px;">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Próxima ação sugerida</div>
                    <div style="font-size:13px;font-weight:700;color:#d97706;">
                        <?php echo e($inadimplentes === 0 ? 'Preventiva' : 'Imediata'); ?>

                    </div>
                    <div style="font-size:11px;color:var(--muted);">
                        <?php echo e($inadimplentes === 0 ? 'Converter honorários pendentes' : 'Acionar cobrança dos atrasados'); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<style>
@media (max-width: 1200px) {
    .fin-hub-top    { grid-template-columns: 1fr !important; }
    .fin-hub-bottom { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    .fin-hub-kpis   { grid-template-columns: 1fr 1fr !important; }
    .fin-hub-modulos { grid-template-columns: 1fr 1fr !important; }
}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/hubs/financeiro.blade.php ENDPATH**/ ?>