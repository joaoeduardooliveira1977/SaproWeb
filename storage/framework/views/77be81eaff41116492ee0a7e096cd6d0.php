
<?php $__env->startSection('page-title', 'Central de Ferramentas'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Métricas
    $ultimaConsulta            = \App\Models\TjspVerificacao::where('status', 'concluido')->latest()->first();
    $totalAndamentosHoje       = (int) \App\Models\TjspVerificacao::where('status', 'concluido')
                                    ->whereDate('concluido_em', today())->sum('novos_total');
    
	$publicacoesNaoLidas = 0;
	try {
    		$publicacoesNaoLidas = (int) \Illuminate\Support\Facades\DB::table('aasp_publicacoes')
        	->whereNull('processo_id')->count();
		} catch (\Exception $e) {
    		$publicacoesNaoLidas = 0;
	}



    $totalProcessosMonitorados = \App\Models\Processo::where('status', 'Ativo')
                                    ->whereNotNull('numero')->count();
?>

<div>


<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:26px;font-weight:800;color:var(--text);margin:0;">Central de Ferramentas</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Ferramentas jurídicas, consultas e assistentes inteligentes.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="<?php echo e(route('assistente')); ?>"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            Assistente IA
        </a>
        <a href="<?php echo e(route('tjsp')); ?>"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
            Consultar DATAJUD
        </a>
    </div>
</div>


<div class="hub-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
    <?php
    $kpis = [
        [
            'label' => 'Processos Monitorados',
            'val'   => $totalProcessosMonitorados,
            'bg'    => '#eff6ff',
            'cor'   => '#2563a8',
            'tag'   => 'ativos para consulta',
            'route' => route('tjsp'),
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>',
        ],
        [
            'label' => 'Andamentos Hoje',
            'val'   => $totalAndamentosHoje,
            'bg'    => '#f0fdf4',
            'cor'   => '#059669',
            'tag'   => 'importados hoje',
            'route' => route('tjsp'),
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        ],
        [
            'label' => 'Publicações Não Vinculadas',
            'val'   => $publicacoesNaoLidas,
            'bg'    => '#fffbeb',
            'cor'   => '#d97706',
            'tag'   => 'aguardando vinculação',
            'route' => route('aasp-publicacoes', ['vinculo' => 'pendentes']),
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"/><path d="M4 6h2"/><path d="M4 10h2"/><path d="M4 14h2"/></svg>',
        ],
        [
            'label' => 'Última Consulta DATAJUD',
            'val'   => $ultimaConsulta ? $ultimaConsulta->concluido_em->format('d/m H:i') : '—',
            'bg'    => '#f5f3ff',
            'cor'   => '#7c3aed',
            'tag'   => $ultimaConsulta ? 'última sincronização' : 'consulta pendente',
            'route' => route('tjsp'),
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        ],
    ];
    ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e($k['route']); ?>" style="text-decoration:none;">
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;transition:border-color .15s,transform .15s;"
            onmouseover="this.style.transform='translateY(-2px)';this.style.borderColor='<?php echo e($k['cor']); ?>'"
            onmouseout="this.style.transform='';this.style.borderColor='var(--border)'">
            <div style="width:40px;height:40px;border-radius:8px;background:<?php echo e($k['bg']); ?>;color:<?php echo e($k['cor']); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <?php echo $k['svg']; ?>

            </div>
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


<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px;">
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="width:52px;height:52px;border-radius:10px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:6px;">Status das Ferramentas</div>
            <div style="font-size:13px;color:var(--muted);line-height:1.7;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ultimaConsulta): ?>
                    Última consulta ao DATAJUD <strong style="color:var(--primary);"><?php echo e($ultimaConsulta->concluido_em->locale('pt_BR')->diffForHumans()); ?></strong>
                    com <strong style="color:#059669;"><?php echo e($ultimaConsulta->novos_total); ?> andamento(s) novo(s)</strong> encontrado(s).
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publicacoesNaoLidas > 0): ?>
                        Há <strong style="color:#d97706;"><?php echo e($publicacoesNaoLidas); ?> publicação(ões)</strong> aguardando vinculação a processos.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    Nenhuma consulta ao DATAJUD realizada ainda. Clique em "Consultar DATAJUD" para iniciar.
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:12px;font-size:12px;color:var(--muted);">
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#2563a8" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#2563a8;"><?php echo e($totalProcessosMonitorados); ?></strong>&nbsp;processos monitorados
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#059669" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#059669;"><?php echo e($totalAndamentosHoje); ?></strong>&nbsp;andamentos importados hoje
                </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publicacoesNaoLidas > 0): ?>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#d97706" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#d97706;"><?php echo e($publicacoesNaoLidas); ?></strong>&nbsp;publicações não vinculadas
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;">
    <div style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:20px;">Ferramentas Disponíveis</div>
    <div class="hub-ferramentas" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
        <?php
        $ferramentas = [
            [
                'label' => 'Calculadora Jurídica',
                'desc'  => 'Correção monetária, juros e honorários',
                'cor'   => '#2563a8',
                'bg'    => '#eff6ff',
                'badge' => 'CALC',
                'route' => route('calculadora'),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="10" y2="10"/><line x1="12" y1="10" x2="14" y2="10"/><line x1="16" y1="10" x2="16" y2="14"/><line x1="8" y1="14" x2="10" y2="14"/><line x1="12" y1="14" x2="14" y2="14"/><line x1="8" y1="18" x2="10" y2="18"/><line x1="12" y1="18" x2="14" y2="18"/></svg>',
            ],
            [
                'label' => 'Consulta Judicial',
                'desc'  => 'Buscar andamentos no DATAJUD/CNJ',
                'cor'   => '#059669',
                'bg'    => '#f0fdf4',
                'badge' => 'CNJ',
                'route' => route('tjsp'),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>',
            ],
            [
                'label' => 'Publicações AASP',
                'desc'  => 'Acompanhar intimações e publicações',
                'cor'   => '#d97706',
                'bg'    => '#fffbeb',
                'badge' => 'AASP',
                'route' => route('aasp-publicacoes', ['vinculo' => 'pendentes']),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"/><path d="M4 6h2"/><path d="M4 10h2"/><path d="M4 14h2"/></svg>',
            ],
            [
                'label' => 'Assistente IA',
                'desc'  => 'Redação, análise e consultas inteligentes',
                'cor'   => '#7c3aed',
                'bg'    => '#f5f3ff',
                'badge' => 'IA',
                'route' => route('assistente'),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>',
            ],
            [
                'label' => 'Monitoramento',
                'desc'  => 'Monitorar processos automaticamente',
                'cor'   => '#0891b2',
                'bg'    => '#f0f9ff',
                'badge' => 'AUTO',
                'route' => route('monitoramento'),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
            ],
            [
                'label' => 'Pipeline / CRM',
                'desc'  => 'Gestão comercial e funil de clientes',
                'cor'   => '#dc2626',
                'bg'    => '#fef2f2',
                'badge' => 'CRM',
                'route' => route('crm'),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
            ],
            [
                'label' => 'Workflow de Automação',
                'desc'  => 'Regras automáticas por gatilho e ação',
                'cor'   => '#7c3aed',
                'bg'    => '#f5f3ff',
                'badge' => 'FLOW',
                'route' => route('workflow.regras'),
                'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
            ],
        ];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ferramentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($f['route']); ?>" style="text-decoration:none;">
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:16px;transition:border-color .15s,transform .15s;position:relative;min-height:148px;"
                onmouseover="this.style.borderColor='<?php echo e($f['cor']); ?>';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.transform=''">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <div style="width:38px;height:38px;border-radius:8px;background:<?php echo e($f['bg']); ?>;color:<?php echo e($f['cor']); ?>;display:flex;align-items:center;justify-content:center;"><?php echo $f['svg']; ?></div>
                    <span style="background:<?php echo e($f['cor']); ?>;color:#fff;padding:3px 8px;border-radius:99px;font-size:11px;font-weight:800;"><?php echo e($f['badge']); ?></span>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:5px;"><?php echo e($f['label']); ?></div>
                <div style="font-size:11px;color:var(--muted);line-height:1.5;margin-bottom:8px;"><?php echo e($f['desc']); ?></div>
                <div style="font-size:12px;font-weight:600;color:<?php echo e($f['cor']); ?>;">Acessar ferramenta →</div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

</div>

<style>
@media (max-width: 1024px) {
    .hub-kpis       { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-ferramentas { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 480px) {
    .hub-kpis        { grid-template-columns: 1fr 1fr !important; }
    .hub-ferramentas { grid-template-columns: 1fr !important; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/hubs/ferramentas.blade.php ENDPATH**/ ?>