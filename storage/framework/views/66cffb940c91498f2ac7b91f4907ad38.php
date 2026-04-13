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

    $itensAtencao = [
        ['label' => 'Prazos vencidos', 'valor' => $prazosVencidos, 'route' => route('prazos'), 'cor' => '#dc2626', 'bg' => '#fef2f2'],
        ['label' => 'Prazos em 7 dias', 'valor' => $prazos7dias, 'route' => route('prazos'), 'cor' => '#d97706', 'bg' => '#fffbeb'],
        ['label' => 'Sem atualização', 'valor' => $processosParados, 'route' => route('processos'), 'cor' => '#7c3aed', 'bg' => '#f5f3ff'],
        ['label' => 'Audiências amanhã', 'valor' => $audienciasAmanha, 'route' => route('audiencias'), 'cor' => '#2563a8', 'bg' => '#eff6ff'],
    ];
?>

<div style="display:flex;flex-direction:column;">


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
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#fff;color:#1d4ed8;border:1px solid var(--border);border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            Resumo com IA
        </a>
    </div>
</div>


<div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:18px 20px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:var(--text);">Atenção necessária</div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">Priorize o que pode exigir uma providência hoje.</div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(collect($itensAtencao)->sum('valor') === 0): ?>
        <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;background:#f0fdf4;color:#15803d;font-size:12px;font-weight:700;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Tudo em ordem
        </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="hub-atencao" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $itensAtencao; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $semPendencia = (int) $item['valor'] === 0; ?>
        <a href="<?php echo e($item['route']); ?>" style="text-decoration:none;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:13px 14px;border-radius:8px;background:<?php echo e($semPendencia ? '#f8fafc' : $item['bg']); ?>;border:1px solid <?php echo e($semPendencia ? '#e2e8f0' : 'transparent'); ?>;">
            <span style="display:flex;align-items:center;gap:9px;min-width:0;">
                <span style="width:10px;height:10px;border-radius:50%;background:<?php echo e($semPendencia ? '#94a3b8' : $item['cor']); ?>;flex-shrink:0;"></span>
                <span style="font-size:13px;font-weight:700;color:<?php echo e($semPendencia ? 'var(--muted)' : $item['cor']); ?>;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($item['label']); ?></span>
            </span>
            <span style="font-size:18px;font-weight:800;color:<?php echo e($semPendencia ? 'var(--muted)' : $item['cor']); ?>;"><?php echo e($item['valor']); ?></span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div style="order:2;background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:14px 16px;margin-bottom:16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:12px;min-width:260px;flex:1;">
            <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:3px;">Resumo Inteligente</div>
                <div style="font-size:13px;color:var(--muted);line-height:1.5;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazosVencidos > 0 || $prazosHoje > 0): ?>
                        <?php echo e($prazosVencidos); ?> prazo(s) vencido(s), <?php echo e($prazos7dias); ?> nos próximos 7 dias e <?php echo e($processosParados); ?> processo(s) sem atualização.
                    <?php else: ?>
                        Tudo em ordem. Nenhum prazo urgente ou vencido.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($processosParados > 0): ?>
            <a href="<?php echo e(route('processos')); ?>" class="btn btn-outline btn-sm">Ver processos parados</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazos7dias > 0 || $prazosVencidos > 0): ?>
            <a href="<?php echo e(route('prazos')); ?>" class="btn btn-outline btn-sm">Ver prazos</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('assistente')); ?>" class="btn btn-primary btn-sm">Resumo com IA</a>
        </div>
    </div>
</div>


<div style="order:1;background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div>
            <div style="font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Acessos rápidos
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">Principais áreas de trabalho e apoio do escritório.</div>
        </div>
    </div>
    <div class="hub-acessos" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <?php
        $acessosRapidos = [
            ['label'=>'Processos',        'sub'=>$totalAtivos.' ativos',                       'cor'=>'#1d4ed8','route'=>route('processos')],
            ['label'=>'Prazos',           'sub'=>$prazosHoje.' hoje / '.$prazos7dias.' sem.',   'cor'=>'#dc2626','route'=>route('prazos')],
            ['label'=>'Audiências',       'sub'=>$audienciasSemana.' nesta sem.',               'cor'=>'#059669','route'=>route('audiencias')],
            ['label'=>'Agenda',           'sub'=>'Compromissos',                                'cor'=>'#d97706','route'=>route('agenda')],
            ['label'=>'Pessoas',          'sub'=>'Partes e clientes',                           'cor'=>'#2563a8','route'=>route('pessoas')],
            ['label'=>'Correspondentes',  'sub'=>'Jurídicos',                                   'cor'=>'#0891b2','route'=>route('correspondentes')],
            ['label'=>'Procurações',      'sub'=>$procuracoesVenc.' vencendo',                  'cor'=>'#dc2626','route'=>route('procuracoes')],
            ['label'=>'Documentos',       'sub'=>'Arquivos do processo',                        'cor'=>'#7c3aed','route'=>route('documentos')],
        ];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $acessosRapidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($item['route']); ?>" style="text-decoration:none;display:flex;align-items:center;gap:10px;padding:13px 14px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);transition:all .15s;"
            onmouseover="this.style.borderColor='<?php echo e($item['cor']); ?>';this.style.background='#fff'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#f8fafc'">
            <div style="width:10px;height:10px;border-radius:50%;background:<?php echo e($item['cor']); ?>;flex-shrink:0;"></div>
            <div style="min-width:0;">
                <div style="font-size:13px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($item['label']); ?></div>
                <div style="font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($item['sub']); ?></div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div class="hub-bottom hub-grid-2" style="order:3;display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

    
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
    .hub-atencao           { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-acessos           { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-bottom, .hub-grid-2 { grid-template-columns: 1fr !important; }
}
@media (max-width: 640px) {
    .hub-atencao  { grid-template-columns: 1fr !important; }
    .hub-acessos  { grid-template-columns: 1fr 1fr !important; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/hubs/processos.blade.php ENDPATH**/ ?>