<?php $notificacoes ??= collect(); $naoLidas ??= 0; ?>


<div style="position:relative;" wire:poll.60s>

    
    <button wire:click="toggle"
        style="position:relative;background:none;border:none;cursor:pointer;padding:4px 8px;line-height:1;color:var(--muted);display:inline-flex;align-items:center;"
        aria-label="Notificações" title="Notificações">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($naoLidas > 0): ?>
        <span style="
            position:absolute;top:-2px;right:-2px;
            background:#dc2626;color:#fff;
            font-size:10px;font-weight:700;line-height:1;
            padding:2px 5px;border-radius:10px;
            min-width:16px;text-align:center;">
            <?php echo e($naoLidas > 99 ? '99+' : $naoLidas); ?>

        </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aberto): ?>
    <div style="
        position:absolute;right:0;top:calc(100% + 8px);
        width:380px;max-width:95vw;
        background:#fff;border:1px solid var(--border);
        border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.15);
        z-index:200;overflow:hidden;">

        
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);background:#f8fafc;">
            <span style="font-size:13px;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:6px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notificações
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($naoLidas > 0): ?>
                    <span style="background:#dc2626;color:#fff;font-size:10px;padding:1px 6px;border-radius:8px;margin-left:4px;"><?php echo e($naoLidas); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <div style="display:flex;gap:8px;align-items:center;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($naoLidas > 0): ?>
                <button wire:click="marcarTodasLidas"
                    style="font-size:11px;background:none;border:none;cursor:pointer;color:var(--primary-light);text-decoration:underline;">
                    Marcar todas como lidas
                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="fechar" aria-label="Fechar notificações"
                    style="background:none;border:none;cursor:pointer;color:var(--muted);line-height:1;display:inline-flex;align-items:center;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        
        <div style="max-height:420px;overflow-y:auto;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notificacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div wire:key="notif-<?php echo e($n->id); ?>"
                style="padding:11px 14px;border-bottom:1px solid #f1f5f9;
                       background:<?php echo e($n->lida ? '#fff' : $n->cor()); ?>;
                       opacity:<?php echo e($n->lida ? '.65' : '1'); ?>;
                       cursor:default;transition:background .15s;">
                <?php
                $notifIcon = match($n->tipo) {
                    'prazo_fatal'            => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                    'prazo_hoje'             => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'prazo_vencendo'         => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'prazo_vencido'          => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                    'honorario_atrasado'     => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                    'lancamento_atrasado'    => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                    'processo_sem_andamento' => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
                    default                  => '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
                };
                ?>
                <div style="display:flex;align-items:flex-start;gap:8px;">
                    <span style="flex-shrink:0;margin-top:2px;display:inline-flex;"><?php echo $notifIcon; ?></span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:<?php echo e($n->lida ? '400' : '700'); ?>;color:var(--text);line-height:1.3;">
                            <?php echo e($n->titulo); ?>

                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($n->mensagem): ?>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">
                            <?php echo e($n->mensagem); ?>

                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                            <?php echo e($n->created_at->diffForHumans()); ?>

                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:4px;flex-shrink:0;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($n->link): ?>
                        <a href="<?php echo e($n->link); ?>" wire:click="marcarLida(<?php echo e($n->id); ?>)"
                           style="font-size:10px;background:var(--primary);color:#fff;padding:2px 7px;border-radius:5px;text-decoration:none;white-space:nowrap;">
                            Ver
                        </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$n->lida): ?>
                        <button wire:click="marcarLida(<?php echo e($n->id); ?>)"
                            style="font-size:10px;background:none;border:1px solid var(--border);border-radius:5px;cursor:pointer;padding:2px 5px;color:var(--muted);white-space:nowrap;display:inline-flex;align-items:center;gap:3px;">
                            <svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Lida
                        </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
                <div style="margin-bottom:8px;display:flex;justify-content:center;">
                    <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" opacity=".5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                Nenhuma notificação.
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>

    
    <div wire:click="fechar"
         style="position:fixed;inset:0;z-index:199;"
         x-data x-show="true"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH C:\projetos\saproweb-base\resources\views/livewire/notificacoes-bell.blade.php ENDPATH**/ ?>