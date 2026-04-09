<?php $__env->startSection('page-title', 'Central de Processos'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalAtivos      = App\Models\Processo::where('status', 'Ativo')->count();
    $prazosHoje       = App\Models\Prazo::where('status', 'aberto')->whereDate('data_prazo', today())->count();
    $audienciasSemana = App\Models\Agenda::where('tipo', 'Audiência')->where('concluido', false)
                            ->whereBetween('data_hora', [today(), today()->addDays(7)])->count();
    $procuracoesVenc  = App\Models\Procuracao::where('ativa', true)
                            ->whereNotNull('data_validade')
                            ->where('data_validade', '<=', today()->addDays(30))->count();

    $prazosVencidos   = App\Models\Prazo::where('status', 'aberto')->where('data_prazo', '<', today())->count();
    $processosParados = App\Models\Processo::where('status', 'Ativo')
                            ->whereNotExists(fn($q) => $q->from('andamentos')
                                ->whereColumn('andamentos.processo_id', 'processos.id')
                                ->where('andamentos.created_at', '>=', now()->subDays(30)))
                            ->count();
    $prazos7dias      = App\Models\Prazo::where('status', 'aberto')
                            ->whereBetween('data_prazo', [today(), today()->addDays(7)])->count();
    $audienciasAmanha = App\Models\Agenda::where('tipo', 'Audiência')->where('concluido', false)
                            ->whereDate('data_hora', today()->addDay())->count();

    $prazosUrgentes = App\Models\Prazo::with('processo.cliente')
                        ->where('status', 'aberto')
                        ->where('data_prazo', '<=', today()->addDays(7))
                        ->orderBy('data_prazo')
                        ->take(5)->get();

    $ultimasMovimentacoes = Illuminate\Support\Facades\DB::table('andamentos')
                        ->join('processos', 'processos.id', '=', 'andamentos.processo_id')
                        ->join('pessoas', 'pessoas.id', '=', 'processos.cliente_id')
                        ->select('andamentos.id', 'andamentos.descricao', 'andamentos.created_at',
                                 'processos.numero', 'processos.id as processo_id', 'pessoas.nome as cliente_nome')
                        ->orderByDesc('andamentos.created_at')
                        ->limit(5)->get();
?>

<div>


<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Central de Processos</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Gerencie processos, partes, documentos, prazos e procurações.</p>
    </div>



    <div style="display:flex;gap:10px;">
        <a href="<?php echo e(route('processos.novo')); ?>"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Processo
        </a>
        <a href="<?php echo e(route('assistente')); ?>"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            Resumo com IA
        </a>
    </div>
</div>


<div class="hub-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
    <?php
    $kpis = [
        ['label'=>'Processos Ativos',     'val'=>$totalAtivos,      'bg'=>'linear-gradient(135deg,#1d4ed8,#2563a8)', 'route'=>route('processos'),
         'svg'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'],
        ['label'=>'Prazos Hoje',          'val'=>$prazosHoje,       'bg'=>'linear-gradient(135deg,#7c3aed,#6d28d9)', 'route'=>route('prazos'),
         'svg'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'],
        ['label'=>'Audiências na Semana', 'val'=>$audienciasSemana, 'bg'=>'linear-gradient(135deg,#059669,#16a34a)', 'route'=>route('audiencias'),
         'svg'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
        ['label'=>'Procurações Vencendo', 'val'=>$procuracoesVenc,  'bg'=>'linear-gradient(135deg,#dc2626,#b91c1c)', 'route'=>route('procuracoes'),
         'svg'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'],
    ];
    ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e($k['route']); ?>" style="text-decoration:none;">
        <div style="background:<?php echo e($k['bg']); ?>;border-radius:14px;padding:22px 20px;color:#fff;cursor:pointer;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 15px rgba(0,0,0,.15);"
            onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,.2)'"
            onmouseout="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(0,0,0,.15)'">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <?php echo $k['svg']; ?>

                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div style="font-size:32px;font-weight:800;margin-bottom:4px;letter-spacing:-1px;"><?php echo e($k['val']); ?></div>
            <div style="font-size:13px;color:rgba(255,255,255,.8);font-weight:500;"><?php echo e($k['label']); ?></div>
        </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:20px 24px;margin-bottom:20px;">
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:6px;">Resumo Inteligente</div>
            <div style="font-size:13px;color:var(--muted);line-height:1.7;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazosVencidos > 0 || $prazosHoje > 0): ?>
                    Você tem <strong style="color:#dc2626;"><?php echo e($prazosVencidos); ?> prazo(s) vencido(s)</strong>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazos7dias > 0): ?> e <strong style="color:#d97706;"><?php echo e($prazos7dias); ?> prazo(s) nos próximos 7 dias</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>.
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($processosParados > 0): ?> Além disso, <strong style="color:#7c3aed;"><?php echo e($processosParados); ?> processo(s) estão parados</strong> há mais de 30 dias.<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    Tudo em ordem! Nenhum prazo urgente ou vencido. 🎉
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:12px;font-size:12px;color:var(--muted);">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazosHoje > 0): ?>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#dc2626" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#dc2626;"><?php echo e($prazosHoje); ?> prazo(s)</strong>&nbsp;vencem hoje
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazos7dias > 0): ?>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#d97706" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#d97706;"><?php echo e($prazos7dias); ?></strong>&nbsp;nesta semana
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($audienciasAmanha > 0): ?>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#2563a8" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#2563a8;"><?php echo e($audienciasAmanha); ?> audiência(s)</strong>&nbsp;amanhã
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($processosParados > 0): ?>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#7c3aed" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#7c3aed;"><?php echo e($processosParados); ?> processo(s)</strong>&nbsp;sem atualização
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Operação
        </div>
        <a href="<?php echo e(route('processos')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;">Ver todos...</a>
    </div>
    <div class="hub-operacao" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <?php
        $operacao = [
            ['label'=>'Processos',  'sub'=>$totalAtivos.' ativos',                           'cor'=>'#1d4ed8','bg'=>'#eff6ff','route'=>route('processos')],
            ['label'=>'Prazos',     'sub'=>$prazosHoje.' hoje / '.$prazos7dias.' sem.',       'cor'=>'#7c3aed','bg'=>'#f5f3ff','route'=>route('prazos')],
            ['label'=>'Audiências', 'sub'=>$audienciasSemana.' nesta sem.',                   'cor'=>'#059669','bg'=>'#f0fdf4','route'=>route('audiencias')],
            ['label'=>'Agenda',     'sub'=>'Compromissos',                                    'cor'=>'#d97706','bg'=>'#fffbeb','route'=>route('agenda')],
        ];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $operacao; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($item['route']); ?>" style="text-decoration:none;display:flex;align-items:center;gap:10px;padding:16px;border-radius:8px;background:<?php echo e($item['bg']); ?>;transition:opacity .15s;"
            onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
            <div style="width:14px;height:14px;border-radius:50%;background:<?php echo e($item['cor']); ?>;flex-shrink:0;"></div>
            <div>
                <div style="font-size:14px;font-weight:600;color:<?php echo e($item['cor']); ?>;"><?php echo e($item['label']); ?></div>
                <div style="font-size:12px;color:var(--muted);"><?php echo e($item['sub']); ?></div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Cadastro e Apoio
        </div>
        <a href="<?php echo e(route('pessoas')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;">Ver todos...</a>
    </div>
    <div class="hub-operacao" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <?php
        $cadastro = [
            ['label'=>'Pessoas',         'sub'=>'Partes e clientes',          'cor'=>'#2563a8','bg'=>'#eff6ff','route'=>route('pessoas')],
            ['label'=>'Correspondentes', 'sub'=>'Jurídicos',                  'cor'=>'#0891b2','bg'=>'#f0f9ff','route'=>route('correspondentes')],
            ['label'=>'Procurações',     'sub'=>$procuracoesVenc.' vencendo', 'cor'=>'#dc2626','bg'=>'#fef2f2','route'=>route('procuracoes')],
            ['label'=>'Documentos',      'sub'=>'Arquivos do processo',       'cor'=>'#7c3aed','bg'=>'#f5f3ff','route'=>route('documentos')],
        ];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cadastro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($item['route']); ?>" style="text-decoration:none;display:flex;align-items:center;gap:10px;padding:16px;border-radius:8px;background:<?php echo e($item['bg']); ?>;transition:opacity .15s;"
            onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
            <div style="width:14px;height:14px;border-radius:50%;background:<?php echo e($item['cor']); ?>;flex-shrink:0;"></div>
            <div>
                <div style="font-size:14px;font-weight:600;color:<?php echo e($item['cor']); ?>;"><?php echo e($item['label']); ?></div>
                <div style="font-size:12px;color:var(--muted);"><?php echo e($item['sub']); ?></div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div class="hub-bottom hub-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Prazos Urgentes
            </div>
            <a href="<?php echo e(route('prazos')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;">Ver todos...</a>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazosUrgentes->isEmpty()): ?>
        <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">
            ✅ Nenhum prazo urgente nos próximos 7 dias
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prazosUrgentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prazo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $dias = $prazo->diasRestantes();
                $cor = $dias < 0 ? '#dc2626' : ($dias === 0 ? '#dc2626' : ($dias <= 2 ? '#d97706' : '#2563a8'));
                $bg  = $dias < 0 ? '#fef2f2' : ($dias === 0 ? '#fef2f2' : ($dias <= 2 ? '#fffbeb' : '#eff6ff'));
            ?>
            <a href="<?php echo e(route('prazos')); ?>" style="text-decoration:none;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;background:<?php echo e($bg); ?>;">
                <div style="width:36px;height:36px;border-radius:50%;background:<?php echo e($cor); ?>22;color:<?php echo e($cor); ?>;font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <?php echo e($dias < 0 ? abs($dias).'d' : ($dias === 0 ? 'Hj' : $dias.'d')); ?>

                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($prazo->titulo); ?></div>
                    <div style="font-size:11px;color:var(--muted);"><?php echo e($prazo->data_prazo->format('d/m/Y')); ?> · <?php echo e($prazo->processo?->cliente?->nome ?? '—'); ?></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazo->prazo_fatal): ?>
                <span style="background:#fce7f3;color:#9d174d;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;flex-shrink:0;">FATAL</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Últimas Movimentações
            </div>
            <a href="<?php echo e(route('processos')); ?>" style="font-size:12px;color:var(--primary);text-decoration:none;">Ver todos...</a>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ultimasMovimentacoes->isEmpty()): ?>
        <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">
            Nenhuma movimentação recente
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ultimasMovimentacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $inicial = strtoupper(substr($mov->cliente_nome ?? 'S', 0, 1));
                $coresMov = ['#2563a8','#16a34a','#d97706','#7c3aed','#dc2626'];
                $corMov = $coresMov[ord($inicial) % count($coresMov)];
            ?>
            <a href="<?php echo e(route('processos.show', $mov->processo_id)); ?>" style="text-decoration:none;display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);">
                <div style="width:32px;height:32px;border-radius:8px;background:<?php echo e($corMov); ?>22;color:<?php echo e($corMov); ?>;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <?php echo e($inicial); ?>

                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;color:var(--text);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e(\Illuminate\Support\Str::limit($mov->descricao, 60)); ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                        <span style="color:var(--primary);font-weight:600;"><?php echo e($mov->numero); ?></span>
                        · <?php echo e($mov->cliente_nome); ?>

                    </div>
                </div>
                <div style="font-size:10px;color:var(--muted);flex-shrink:0;white-space:nowrap;padding-top:2px;">
                    <?php echo e(\Carbon\Carbon::parse($mov->created_at)->locale('pt_BR')->diffForHumans()); ?>

                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

</div>

</div>

<style>
@media (max-width: 1024px) {
    .hub-kpis              { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-operacao          { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-bottom, .hub-grid-2 { grid-template-columns: 1fr !important; }
}
@media (max-width: 640px) {
    .hub-kpis     { grid-template-columns: 1fr 1fr !important; }
    .hub-operacao { grid-template-columns: 1fr 1fr !important; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/hubs/processos.blade.php ENDPATH**/ ?>