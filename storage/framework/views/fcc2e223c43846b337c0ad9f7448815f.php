<div>
<?php $pixConfigurado = \App\Services\PixService::configurado(); ?>


<nav class="navbar">
    <div class="navbar-brand">
        <span><svg aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3L2 9l4 2-4 4h8m0-12l10 6-4 2 4 4h-8m0-12v18"/></svg></span>
        <div>
            <div>JURÍDICO</div>
            <div class="navbar-sub">PORTAL DO CLIENTE</div>
        </div>
    </div>
    <div class="navbar-user">
        <span style="font-size:13px;display:inline-flex;align-items:center;gap:5px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> <?php echo e($pessoa?->nome); ?></span>
        <button wire:click="sair" class="btn-sair">Sair</button>
    </div>
</nav>


<div class="portal-tabs">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
        'inicio'     => 'Início',
        'processos'  => 'Processos',
        'documentos' => 'Documentos',
        'honorarios' => 'Honorários',
        'mensagens'  => 'Mensagens',
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <button wire:click="trocarAba('<?php echo e($key); ?>')"
        class="portal-tab <?php echo e($aba === $key ? 'active' : ''); ?>">
        <?php echo e($label); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key === 'mensagens' && $stats['msgs_nao_lidas'] > 0): ?>
            <span class="tab-badge"><?php echo e($stats['msgs_nao_lidas']); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div class="container">




<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'inicio'): ?>

    <div class="stats-grid">
        <div class="stat-card" style="cursor:pointer;" wire:click="trocarAba('processos')">
            <div class="stat-value"><?php echo e($stats['total']); ?></div>
            <div class="stat-label">Processos</div>
        </div>
        <div class="stat-card" style="cursor:pointer;" wire:click="trocarAba('processos')">
            <div class="stat-value" style="color:#16a34a;"><?php echo e($stats['ativos']); ?></div>
            <div class="stat-label">Processos ativos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#2563a8;"><?php echo e($stats['agenda']); ?></div>
            <div class="stat-label">Próximos compromissos</div>
        </div>
        <div class="stat-card" style="cursor:pointer;" wire:click="trocarAba('mensagens')">
            <div class="stat-value" style="color:<?php echo e($stats['msgs_nao_lidas'] > 0 ? '#dc2626' : '#334155'); ?>;">
                <?php echo e($stats['msgs_nao_lidas']); ?>

            </div>
            <div class="stat-label">Mensagens não lidas</div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Próximos Compromissos</span></div>
        <div class="card-body" style="padding-top:8px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $proximosEventos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="agenda-item">
                <div class="agenda-hora"><?php echo e($ev->data_hora->format('d/m')); ?><br><small><?php echo e($ev->data_hora->format('H:i')); ?></small></div>
                <div class="agenda-info">
                    <div class="agenda-titulo">
                        <?php echo e($ev->titulo); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ev->urgente): ?> <span class="urgente-badge">URGENTE</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="agenda-meta">
                        <?php echo e($ev->tipo); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ev->local): ?> · <?php echo e($ev->local); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ev->processo): ?> · Proc. <?php echo e($ev->processo->numero); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty"><div class="empty-icon" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><p>Nenhum compromisso próximo.</p></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazosProximos->isNotEmpty()): ?>
    <div class="card">
        <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Prazos Próximos (30 dias)</span></div>
        <div class="card-body" style="padding-top:8px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prazosProximos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prazo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $dias = (int) now()->startOfDay()->diffInDays($prazo->data_prazo, false);
                $cor  = $dias < 0 ? '#dc2626' : ($dias <= 5 ? '#ea580c' : ($dias <= 15 ? '#ca8a04' : '#16a34a'));
            ?>
            <div style="display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:<?php echo e($cor); ?>;min-width:70px;white-space:nowrap;">
                    <?php echo e($prazo->data_prazo->format('d/m/Y')); ?>

                </span>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?php echo e($prazo->titulo); ?>

                    </div>
                    <div style="font-size:11px;color:#64748b;">
                        Proc. <?php echo e($prazo->processo?->numero); ?>

                        · <?php echo e($dias >= 0 ? $dias.' dia(s)' : abs($dias).' dia(s) em atraso'); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazo->prazo_fatal): ?> · <span style="color:#9d174d;font-weight:700;"><svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg> Fatal</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ultimosAndamentos->isNotEmpty()): ?>
    <div class="card">
        <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg> Últimas Atualizações</span></div>
        <div class="card-body" style="padding-top:8px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ultimosAndamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $and): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="display:flex;gap:12px;padding:9px 0;border-bottom:1px solid #f1f5f9;cursor:pointer;"
                 wire:click="abrirProcesso(<?php echo e($and->processo_id); ?>)">
                <div style="min-width:72px;font-size:12px;color:#94a3b8;padding-top:1px;">
                    <?php echo e($and->data->format('d/m/Y')); ?>

                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:11px;font-weight:700;color:#2563a8;margin-bottom:2px;">
                        <?php echo e($and->processo?->numero); ?>

                    </div>
                    <div style="font-size:13px;color:#334155;line-height:1.4;
                                display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        <?php echo e($and->descricao); ?>

                    </div>
                </div>
                <span style="color:#94a3b8;font-size:12px;align-self:center;">→</span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $totalAtrasadoInicio = \Illuminate\Support\Facades\DB::table('honorario_parcelas as hp')
            ->join('honorarios as h', 'h.id', '=', 'hp.honorario_id')
            ->where('h.cliente_id', $pessoa->id)
            ->whereIn('hp.status', ['pendente','atrasado'])
            ->sum('hp.valor');
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalAtrasadoInicio > 0 && $pixConfigurado): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 20px;background:#fef9c3;border:1px solid #fde047;border-radius:12px;margin-bottom:24px;">
        <div>
            <div style="font-size:14px;font-weight:600;color:#854d0e;display:flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#854d0e" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Você possui honorários em aberto</div>
            <div style="font-size:13px;color:#92400e;margin-top:2px;">
                Total pendente: <strong>R$ <?php echo e(number_format($totalAtrasadoInicio, 2, ',', '.')); ?></strong>
            </div>
        </div>
        <button wire:click="trocarAba('honorarios')"
                style="background:#854d0e;color:#fff;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
            <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Ver e Pagar via PIX</span>
        </button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card" style="background:#f0f9ff;border:1px solid #bae6fd;">
        <div class="card-body" style="padding:16px 24px;">
            <p style="font-size:13px;color:#0369a1;margin:0;">
                <span style="display:inline-flex;align-items:flex-start;gap:6px;"><svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0369a1" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Use as abas acima para acompanhar seus processos, documentos, honorários e enviar mensagens ao escritório.</span>
            </p>
        </div>
    </div>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'processos'): ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$processoAberto): ?>
    <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['todos' => 'Todos', 'judiciais' => 'Judiciais', 'extrajudiciais' => 'Extrajudiciais']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chave => $rotulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button wire:click="setFiltroProcessos('<?php echo e($chave); ?>')"
            style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;cursor:pointer;border:2px solid <?php echo e($filtroProcessos === $chave ? '#2563a8' : '#e2e8f0'); ?>;background:<?php echo e($filtroProcessos === $chave ? '#2563a8' : 'white'); ?>;color:<?php echo e($filtroProcessos === $chave ? 'white' : '#64748b'); ?>;">
            <?php echo e($rotulo); ?>

        </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span style="font-size:12px;color:#94a3b8;align-self:center;margin-left:4px;">
            <?php echo e($processos->count()); ?> processo(s)
        </span>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($processoDetalhe): ?>

        <div style="margin-bottom:16px;">
            <button wire:click="fecharProcesso" class="btn btn-outline" style="font-size:13px;">
                ← Voltar à lista
            </button>
        </div>

        <div class="card">
            <div class="card-header" style="background:#1a3a5c;color:#fff;border-radius:12px 12px 0 0;">
                <span style="display:inline-flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> <?php echo e($processoDetalhe->numero); ?></span>
                <span style="font-size:12px;font-weight:400;margin-left:8px;opacity:.8;">
                    <?php echo e($processoDetalhe->status); ?>

                </span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:20px;">
                    <div><div class="info-label">Parte Contrária</div><div class="info-val"><?php echo e($processoDetalhe->parte_contraria ?: '—'); ?></div></div>
                    <div><div class="info-label">Advogado</div><div class="info-val"><?php echo e($processoDetalhe->advogado?->nome ?? '—'); ?></div></div>
                    <div><div class="info-label">Fase</div><div class="info-val"><?php echo e($processoDetalhe->fase?->descricao ?? '—'); ?></div></div>
                    <div><div class="info-label">Distribuição</div><div class="info-val"><?php echo e($processoDetalhe->data_distribuicao?->format('d/m/Y') ?? '—'); ?></div></div>
                    <div><div class="info-label">Valor da Causa</div><div class="info-val"><?php echo e($processoDetalhe->valor_causa ? 'R$ '.number_format($processoDetalhe->valor_causa,2,',','.') : '—'); ?></div></div>
                    <div>
                        <div class="info-label">Risco</div>
                        <div class="info-val">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($processoDetalhe->risco): ?>
                                <span class="risco-dot" style="background:<?php echo e($processoDetalhe->risco->cor_hex ?? '#ccc'); ?>"></span>
                                <?php echo e($processoDetalhe->risco->descricao); ?>

                            <?php else: ?> —
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazosProcesso->isNotEmpty()): ?>
        <div class="card">
            <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Prazos em Aberto</span></div>
            <div class="card-body" style="padding-top:8px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prazosProcesso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prazo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dias = (int) now()->startOfDay()->diffInDays($prazo->data_prazo, false);
                    $cor  = $dias < 0 ? '#dc2626' : ($dias <= 5 ? '#ea580c' : ($dias <= 15 ? '#ca8a04' : '#16a34a'));
                ?>
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;font-weight:700;color:<?php echo e($cor); ?>;min-width:90px;">
                        <?php echo e($prazo->data_prazo->format('d/m/Y')); ?>

                    </span>
                    <div>
                        <div style="font-size:13px;font-weight:600;"><?php echo e($prazo->titulo); ?></div>
                        <div style="font-size:11px;color:#64748b;">
                            <?php echo e($dias >= 0 ? $dias.' dia(s) restante(s)' : abs($dias).' dia(s) em atraso'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prazo->prazo_fatal): ?> · <span style="color:#9d174d;font-weight:700;"><svg aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg> Fatal</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="card">
            <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg> Histórico de Andamentos</span></div>
            <div class="card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($andamentos->isEmpty()): ?>
                    <div class="empty"><div class="empty-icon" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg></div><p>Nenhum andamento registrado.</p></div>
                <?php else: ?>
                <div class="timeline">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $andamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $docs = $docsAndamentos->get($a->id, collect()); ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date"><?php echo e($a->data->format('d/m/Y')); ?></div>
                        <div class="timeline-text">
                            <?php echo e($a->descricao); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($docs->isNotEmpty()): ?>
                            <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:6px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(Storage::url($doc->arquivo)); ?>" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
                                          background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;
                                          font-size:11px;font-weight:600;color:#1d4ed8;text-decoration:none;">
                                    <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg> <?php echo e($doc->arquivo_original ?? $doc->titulo ?? 'Documento'); ?>

                                </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

    <?php else: ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $totalAndamentos = \App\Models\Andamento::where('processo_id', $proc->id)->count();
            $ultimoAndamento = \App\Models\Andamento::where('processo_id', $proc->id)->latest('data')->first();
        ?>
        <div class="processo-card" wire:click="abrirProcesso(<?php echo e($proc->id); ?>)">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--primary);margin-bottom:4px;">
                        <?php echo e($proc->numero); ?>

                    </div>
                    <div style="font-size:13px;color:#64748b;">
                        <?php echo e($proc->parte_contraria ?: '—'); ?>

                    </div>
                </div>
                <span class="badge <?php echo e($proc->status === 'Ativo' ? 'badge-ativo' : 'badge-encerrado'); ?>">
                    <?php echo e($proc->status); ?>

                </span>
            </div>
            <div style="display:flex;gap:20px;margin-top:10px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#64748b;">
                    <strong>Fase:</strong> <?php echo e($proc->fase?->descricao ?? '—'); ?>

                </span>
                <span style="font-size:12px;color:#64748b;">
                    <strong>Advogado:</strong> <?php echo e($proc->advogado?->nome ?? '—'); ?>

                </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ultimoAndamento): ?>
                <span style="font-size:12px;color:#64748b;">
                    <strong>Última atualização:</strong> <?php echo e($ultimoAndamento->data->format('d/m/Y')); ?>

                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span style="font-size:12px;color:#2563a8;font-weight:600;">
                    <?php echo e($totalAndamentos); ?> andamento(s) →
                </span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($processos->isEmpty()): ?>
        <div class="empty"><div class="empty-icon" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><p>Nenhum processo encontrado.</p></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'documentos'): ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentos->isEmpty()): ?>
        <div class="card">
            <div class="empty" style="padding:60px;">
                <div class="empty-icon" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
                <p>Nenhum documento disponível no portal.</p>
                <p style="font-size:12px;margin-top:8px;">O escritório disponibilizará documentos assim que estiverem prontos.</p>
            </div>
        </div>
    <?php else: ?>
    <div class="card">
        <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg> Documentos Disponíveis</span></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $icone_svg = match($doc->tipo) {
                'peticao'           => '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>',
                'contrato'          => '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                'sentenca'          => '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M12 3L2 9l4 2-4 4h8m0-12l10 6-4 2 4 4h-8m0-12v18"/></svg>',
                'documento_cliente' => '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                default             => '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>',
            };
            $tamanhoFmt = $doc->tamanho
                ? ($doc->tamanho > 1048576
                    ? number_format($doc->tamanho/1048576,1).' MB'
                    : number_format($doc->tamanho/1024,0).' KB')
                : '';
        ?>
        <div style="display:flex;align-items:center;gap:14px;padding:14px 24px;border-bottom:1px solid #f1f5f9;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;flex-shrink:0;"><?php echo $icone_svg; ?></span>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:var(--primary);"><?php echo e($doc->titulo); ?></div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    <?php echo e(ucfirst(str_replace('_',' ',$doc->tipo))); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc->processo_numero): ?> · Proc. <?php echo e($doc->processo_numero); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc->data_documento): ?> · <?php echo e(\Carbon\Carbon::parse($doc->data_documento)->format('d/m/Y')); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tamanhoFmt): ?> · <?php echo e($tamanhoFmt); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc->descricao): ?>
                    <div style="font-size:12px;color:#94a3b8;margin-top:2px;"><?php echo e($doc->descricao); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc->arquivo): ?>
            <a href="<?php echo e(Storage::url($doc->arquivo)); ?>" target="_blank"
               style="background:#1a3a5c;color:#fff;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;">
                <span style="display:inline-flex;align-items:center;gap:5px;"><svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Baixar</span>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'honorarios'): ?>

    <?php
        $totalPago     = $honorarios->where('status','pago')->sum('valor');
        $totalPendente = $honorarios->whereIn('status',['pendente','atrasado'])->sum('valor');
        $totalAtrasado = $honorarios->where('status','atrasado')->sum('valor');
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pixPago): ?>
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;margin-bottom:20px;font-size:13px;color:#166534;">
        <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <strong>Aviso de pagamento enviado!</strong></span> Nossa equipe irá confirmar o recebimento em breve. Confira sua aba de mensagens.
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-value" style="color:#16a34a;">R$ <?php echo e(number_format($totalPago,2,',','.')); ?></div>
            <div class="stat-label"><span style="display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Total pago</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#ca8a04;">R$ <?php echo e(number_format($totalPendente,2,',','.')); ?></div>
            <div class="stat-label"><span style="display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Pendente</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#dc2626;">R$ <?php echo e(number_format($totalAtrasado,2,',','.')); ?></div>
            <div class="stat-label"><span style="display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg> Em atraso</span></div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pixConfigurado): ?>
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;margin-bottom:20px;font-size:13px;color:#1e40af;">
        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> <span>Para parcelas pendentes ou em atraso, clique em <strong>Pagar via PIX</strong> para gerar o QR Code instantaneamente.</span>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($honorarios->isEmpty()): ?>
        <div class="card">
            <div class="empty" style="padding:60px;">
                <div class="empty-icon" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                <p>Nenhum contrato de honorários registrado.</p>
            </div>
        </div>
    <?php else: ?>
    <div class="card">
        <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Parcelas de Honorários</span></div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Parcela</th>
                        <th class="portal-hide-sm">Contrato</th>
                        <th class="portal-hide-sm">Processo</th>
                        <th>Vencimento</th>
                        <th style="text-align:right;">Valor</th>
                        <th>Status</th>
                        <th class="portal-hide-sm">Pgto</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pixConfigurado): ?><th></th><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $honorarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $statusStyle = match($parc->status) {
                            'pago'     => 'background:#dcfce7;color:#16a34a;',
                            'atrasado' => 'background:#fee2e2;color:#991b1b;',
                            default    => 'background:#fef9c3;color:#854d0e;',
                        };
                        $vencido = $parc->status !== 'pago'
                            && \Carbon\Carbon::parse($parc->vencimento)->isPast();
                    ?>
                    <tr>
                        <td style="text-align:center;font-weight:600;"><?php echo e($parc->numero_parcela); ?>ª</td>
                        <td class="portal-hide-sm" style="font-size:12px;"><?php echo e($parc->contrato ?? '—'); ?></td>
                        <td class="portal-hide-sm" style="font-size:12px;font-family:monospace;"><?php echo e($parc->processo_numero ?? '—'); ?></td>
                        <td style="<?php echo e($vencido ? 'color:#dc2626;font-weight:600;' : ''); ?>">
                            <?php echo e(\Carbon\Carbon::parse($parc->vencimento)->format('d/m/Y')); ?>

                        </td>
                        <td style="text-align:right;font-weight:600;">R$ <?php echo e(number_format($parc->valor,2,',','.')); ?></td>
                        <td>
                            <span class="badge" style="<?php echo e($statusStyle); ?>">
                                <?php echo e(ucfirst($parc->status)); ?>

                            </span>
                        </td>
                        <td class="portal-hide-sm" style="font-size:12px;color:#64748b;">
                            <?php echo e($parc->data_pagamento ? \Carbon\Carbon::parse($parc->data_pagamento)->format('d/m/Y') : '—'); ?>

                        </td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pixConfigurado): ?>
                        <td style="white-space:nowrap;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($parc->status, ['pendente','atrasado'])): ?>
                            <button wire:click="abrirPix(<?php echo e($parc->id); ?>)"
                                    style="background:#22c55e;color:#fff;border:none;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Pagar via PIX
                            </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'mensagens'): ?>

<div class="card" style="max-width:720px;margin:0 auto;">
    <div class="card-header"><span style="display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Mensagens com o Escritório</span></div>

    
    <div style="padding:16px 24px;max-height:480px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;"
         id="chat-box">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $mensagens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $deCliente = $msg->de === 'cliente'; ?>
        <div style="display:flex;flex-direction:column;align-items:<?php echo e($deCliente ? 'flex-end' : 'flex-start'); ?>;">
            <div style="
                max-width:75%;
                padding:10px 14px;
                border-radius:<?php echo e($deCliente ? '14px 14px 4px 14px' : '14px 14px 14px 4px'); ?>;
                background:<?php echo e($deCliente ? '#1a3a5c' : '#f1f5f9'); ?>;
                color:<?php echo e($deCliente ? '#fff' : '#334155'); ?>;
                font-size:13px;line-height:1.5;">
                <?php echo e($msg->mensagem); ?>

            </div>
            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$deCliente && $msg->usuario_nome): ?>
                    <?php echo e($msg->usuario_nome); ?> ·
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e(\Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i')); ?>

            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="empty" style="padding:40px;">
            <div class="empty-icon" style="display:flex;justify-content:center;"><svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
            <p>Nenhuma mensagem ainda. Envie uma mensagem para o escritório.</p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div style="padding:16px 24px;border-top:1px solid #e2e8f0;">
        <div style="margin-bottom:10px;">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">
                Processo (opcional)
            </label>
            <select wire:model="msgProcessoId"
                style="width:100%;padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:4px;">
                <option value="">— Mensagem geral —</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $processosFiltro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->numero); ?> — <?php echo e($p->parte_contraria ?: 'Sem parte contrária'); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <textarea wire:model="novaMensagem"
                placeholder="Digite sua mensagem..."
                rows="3"
                style="flex:1;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;resize:none;font-family:inherit;"
                wire:keydown.ctrl.enter="enviarMensagem"></textarea>
            <button wire:click="enviarMensagem"
                style="background:#1a3a5c;color:#fff;border:none;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="enviarMensagem">Enviar →</span>
                <span wire:loading wire:target="enviarMensagem">…</span>
            </button>
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:6px;">Ctrl+Enter para enviar</p>
    </div>
</div>

<script>
    document.addEventListener('livewire:updated', () => {
        const box = document.getElementById('chat-box');
        if (box) box.scrollTop = box.scrollHeight;
    });
</script>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>



<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modalPix): ?>
<div class="modal-overlay" wire:click.self="$set('modalPix', false)">
    <div class="modal" style="max-width:440px;width:100%">
        <div class="modal-header">
            <h3 style="display:flex;align-items:center;gap:8px;"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Pagamento via PIX</h3>
            <button class="btn-close" wire:click="$set('modalPix', false)">×</button>
        </div>
        <div class="modal-body">

            
            <div style="text-align:center;margin-bottom:20px;">
                <div style="font-size:13px;color:#64748b;margin-bottom:4px;">Valor a pagar</div>
                <div style="font-size:32px;font-weight:700;color:var(--primary);">
                    R$ <?php echo e(number_format($pixValor, 2, ',', '.')); ?>

                </div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;"><?php echo e($pixDescricao); ?></div>
            </div>

            
            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;margin-bottom:20px;">
                <img src="<?php echo e($pixQrUrl); ?>" alt="QR Code PIX"
                     style="width:220px;height:220px;border:6px solid #f1f5f9;border-radius:12px;"
                     onerror="this.style.display='none';document.getElementById('pix-qr-error').style.display='block'">
                <div id="pix-qr-error" style="display:none;font-size:12px;color:#94a3b8;text-align:center;">
                    QR Code indisponível — use o código abaixo para copiar e colar.
                </div>
                <div style="font-size:12px;color:#64748b;">Aponte a câmera do celular para o QR Code</div>
            </div>

            
            <div style="margin-bottom:20px;">
                <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    Ou copie o código Pix Copia e Cola:
                </div>
                <div style="display:flex;gap:8px;align-items:stretch;">
                    <input id="pix-payload-input" readonly value="<?php echo e($pixPayload); ?>"
                           style="flex:1;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:10px;font-family:monospace;color:#475569;background:#f8fafc;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <button onclick="
                        const inp = document.getElementById('pix-payload-input');
                        navigator.clipboard.writeText(inp.value).then(() => {
                            const btn = this;
                            btn.textContent = 'Copiado!';
                            btn.style.background = '#16a34a';
                            setTimeout(() => { btn.textContent = 'Copiar'; btn.style.background = '#1a3a5c'; }, 2500);
                        });
                    " style="background:#1a3a5c;color:#fff;border:none;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">
                        <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
                        Copiar
                    </button>
                </div>
            </div>

            
            <div style="padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;font-size:12px;color:#166534;line-height:1.5;margin-bottom:20px;">
                <strong>Como pagar:</strong><br>
                1. Abra o app do seu banco e selecione <strong>PIX → Ler QR Code</strong> ou <strong>Pix Copia e Cola</strong>.<br>
                2. Insira o código ou escaneie o QR Code acima.<br>
                3. Confirme o valor de <strong>R$ <?php echo e(number_format($pixValor, 2, ',', '.')); ?></strong> e conclua o pagamento.<br>
                4. Clique em <strong>"Já paguei"</strong> para notificar o escritório.
            </div>

            
            <div style="display:flex;gap:10px;">
                <button wire:click="$set('modalPix', false)"
                        class="btn btn-outline" style="flex:1;">
                    Fechar
                </button>
                <button wire:click="confirmarPagamentoPix"
                        style="flex:1;background:#16a34a;color:#fff;border:none;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Já paguei</span>
                </button>
            </div>

        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


</div>
<?php /**PATH C:\projetos\saproweb-base\resources\views/livewire/portal/dashboard.blade.php ENDPATH**/ ?>