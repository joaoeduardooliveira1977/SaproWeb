<div class="datajud-page">


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'lote' && $temVerificando): ?>
    <div wire:poll.3s></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<div wire:poll.60s style="display:none"></div>


<div class="datajud-layout" style="display:grid;grid-template-columns:1fr 300px;gap:0;align-items:flex-start;">

    
    <div style="min-width:0;padding-right:16px;">

        
        <div style="margin-bottom:20px;">
            <a href="<?php echo e(route('processos')); ?>"
               style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--muted);text-decoration:none;margin-bottom:6px;opacity:.65;transition:opacity .15s,color .15s;"
               onmouseover="this.style.opacity='1';this.style.color='#059669'" onmouseout="this.style.opacity='.65';this.style.color='var(--muted)'">
                <svg width="11" height="11" style="width:11px;height:11px;min-width:11px;min-height:11px;max-width:11px;max-height:11px;display:block;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Processos
            </a>
            <h2 style="font-size:24px;font-weight:800;color:var(--text);margin:0 0 4px;">Central DATAJUD</h2>
            <p style="font-size:13px;color:var(--muted);margin:0;">Consulte novas movimentações, acompanhe alertas e mantenha os processos importantes sob monitoramento.</p>
        </div>

        <?php
            if (($totalCriticos ?? 0) > 0) {
                $acaoRecomendada = [
                    'label' => 'Comece pelos processos críticos',
                    'desc' => 'Há processos que podem exigir providência imediata.',
                    'aba' => 'feed',
                    'cor' => '#dc2626',
                    'bg' => '#fef2f2',
                ];
            } elseif (($notificacoesNaoLidas ?? 0) > 0) {
                $acaoRecomendada = [
                    'label' => 'Revise as notificações pendentes',
                    'desc' => 'Existem avisos não lidos que podem afetar a rotina.',
                    'aba' => 'feed',
                    'cor' => '#d97706',
                    'bg' => '#fffbeb',
                ];
            } elseif (($monitorados->count() ?? 0) === 0) {
                $acaoRecomendada = [
                    'label' => 'Ative o primeiro monitoramento',
                    'desc' => 'Escolha os casos que precisam de acompanhamento recorrente.',
                    'aba' => 'monitoramentos',
                    'cor' => '#7c3aed',
                    'bg' => '#f5f3ff',
                ];
            } else {
                $acaoRecomendada = [
                    'label' => 'Consulte processos no DATAJUD',
                    'desc' => 'Busque novas movimentações em lote ou por lista.',
                    'aba' => 'lote',
                    'cor' => '#059669',
                    'bg' => '#f0fdf4',
                ];
            }
        ?>

        
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:20px;margin-bottom:18px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap;">
                <div style="min-width:260px;flex:1;">
                    <div style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:4px;">Como usar a Central DATAJUD</div>
                    <div style="font-size:13px;color:var(--muted);line-height:1.6;">
                        Comece pelos alertas do dia. Quando quiser conferir vários processos de uma vez, use a consulta. Depois mantenha monitorados apenas os casos que merecem acompanhamento constante.
                    </div>
                    <button wire:click="$set('aba','<?php echo e($acaoRecomendada['aba']); ?>')"
                        style="display:flex;align-items:flex-start;gap:10px;text-align:left;margin-top:14px;background:<?php echo e($acaoRecomendada['bg']); ?>;border:1.5px solid <?php echo e($acaoRecomendada['cor']); ?>33;border-radius:10px;padding:12px;cursor:pointer;width:100%;">
                        <span style="width:10px;height:10px;border-radius:50%;background:<?php echo e($acaoRecomendada['cor']); ?>;flex-shrink:0;margin-top:4px;"></span>
                        <span>
                            <span style="display:block;font-size:12px;font-weight:800;color:<?php echo e($acaoRecomendada['cor']); ?>;margin-bottom:3px;">Próxima ação recomendada</span>
                            <span style="display:block;font-size:13px;font-weight:700;color:var(--text);"><?php echo e($acaoRecomendada['label']); ?></span>
                            <span style="display:block;font-size:11px;color:var(--muted);line-height:1.4;margin-top:3px;"><?php echo e($acaoRecomendada['desc']); ?></span>
                        </span>
                    </button>
                </div>
                <div class="datajud-guide-actions" style="display:grid;grid-template-columns:repeat(3,minmax(150px,1fr));gap:10px;flex:1.4;min-width:420px;">
                    <button wire:click="$set('aba','feed')"
                        style="text-align:left;background:<?php echo e($aba === 'feed' ? '#eff6ff' : '#fff'); ?>;border:1.5px solid <?php echo e($aba === 'feed' ? '#2563a8' : 'var(--border)'); ?>;border-radius:10px;padding:12px;cursor:pointer;">
                        <div style="font-size:12px;font-weight:800;color:#2563a8;margin-bottom:4px;">1. Alertas</div>
                        <div style="font-size:11px;color:var(--muted);line-height:1.4;">Priorize processos críticos e novos andamentos.</div>
                    </button>
                    <button wire:click="$set('aba','lote')"
                        style="text-align:left;background:<?php echo e($aba === 'lote' ? '#f0fdf4' : '#fff'); ?>;border:1.5px solid <?php echo e($aba === 'lote' ? '#059669' : 'var(--border)'); ?>;border-radius:10px;padding:12px;cursor:pointer;">
                        <div style="font-size:12px;font-weight:800;color:#059669;margin-bottom:4px;">2. Consultar</div>
                        <div style="font-size:11px;color:var(--muted);line-height:1.4;">Verifique todos os ativos ou cole uma lista CNJ.</div>
                    </button>
                    <button wire:click="$set('aba','monitoramentos')"
                        style="text-align:left;background:<?php echo e($aba === 'monitoramentos' ? '#f5f3ff' : '#fff'); ?>;border:1.5px solid <?php echo e($aba === 'monitoramentos' ? '#7c3aed' : 'var(--border)'); ?>;border-radius:10px;padding:12px;cursor:pointer;">
                        <div style="font-size:12px;font-weight:800;color:#7c3aed;margin-bottom:4px;">3. Monitorar</div>
                        <div style="font-size:11px;color:var(--muted);line-height:1.4;">Acompanhe apenas os casos que exigem vigilância.</div>
                    </button>
                </div>
            </div>
        </div>

        
        <div class="datajud-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">

            
            <div wire:click="setFiltroFeed('critico')"
                 style="background:#fff;border-radius:10px;padding:16px;cursor:pointer;
                        border:1.5px solid var(--border);transition:border-color .15s,transform .15s;"
                 onmouseover="this.style.borderColor='#ef4444';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:8px;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;"><?php echo e($totalCriticos); ?></div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Processos Críticos</div>
            </div>

            
            <div style="background:#fff;border-radius:10px;padding:16px;cursor:default;
                        border:1.5px solid var(--border);transition:border-color .15s,transform .15s;"
                 onmouseover="this.style.borderColor='#f59e0b';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:8px;background:#fffbeb;color:#d97706;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;"><?php echo e($notificacoesNaoLidas); ?></div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Avisos Pendentes</div>
            </div>

           

 
            <div style="background:#fff;border-radius:10px;padding:16px;cursor:default;
                        border:1.5px solid var(--border);transition:border-color .15s,transform .15s;"
                 onmouseover="this.style.borderColor='#10b981';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;color:#059669;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9,11 12,14 22,4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;"><?php echo e($feedQuery->total()); ?></div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Andamentos no Feed</div>
            </div>

            
            <div wire:click="$set('aba','monitoramentos')"
                 style="background:#fff;border-radius:10px;padding:16px;cursor:pointer;
                        border:1.5px solid var(--border);transition:border-color .15s,transform .15s;"
                 onmouseover="this.style.borderColor='#3b82f6';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)'">
                <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#2563a8;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                    </svg>
                </div>
                <div style="font-size:24px;font-weight:700;color:#1e293b;line-height:1;"><?php echo e($monitorados->count()); ?></div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;font-weight:500;">Monitorados Ativos</div>
            </div>

        </div>

        
        <div style="display:flex;align-items:center;justify-content:flex-start;margin-bottom:14px;gap:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#64748b;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>Última atualização: <?php echo e(\Carbon\Carbon::parse($ultimaAtualizacao)->diffForHumans()); ?></span>
            </div>
        </div>

     

        
        <div style="display:flex;align-items:center;border-bottom:2px solid #e2e8f0;margin-bottom:16px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                'feed'           => 'Alertas',
                'lote'           => 'Consultar',
                'monitoramentos' => 'Monitorados',
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button wire:click="$set('aba','<?php echo e($tab); ?>')"
                        style="padding:9px 18px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;white-space:nowrap;
                               border-bottom:2.5px solid <?php echo e($aba===$tab ? '#059669' : 'transparent'); ?>;
                               color:<?php echo e($aba===$tab ? '#059669' : '#64748b'); ?>;margin-bottom:-2px;
                               transition:color .15s,border-color .15s;">
                    <?php echo e($label); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            

            <div style="flex:1;"></div>
            <button wire:click="$set('aba','historico')"
                style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;
                       font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;
                       border:1.5px solid <?php echo e($aba === 'historico' ? '#2563a8' : '#e2e8f0'); ?>;
                       background:<?php echo e($aba === 'historico' ? '#eff6ff' : '#fff'); ?>;
                       color:<?php echo e($aba === 'historico' ? '#2563a8' : '#64748b'); ?>;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Histórico
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'feed'): ?>
                <button wire:click="toggleFiltros"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;
                           font-size:12px;font-weight:600;cursor:pointer;
                           border:1.5px solid <?php echo e($painelFiltros ? '#1D9E75' : '#e2e8f0'); ?>;
                           background:<?php echo e($painelFiltros ? '#EAF3DE' : '#fff'); ?>;
                           color:<?php echo e($painelFiltros ? '#059669' : '#64748b'); ?>;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/></svg>
                    Outros Filtros
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php
            $dicasPorAba = [
                'feed' => [
                    'titulo' => 'Veja primeiro o que pede atenção',
                    'texto' => 'Comece pelos alertas e use os filtros apenas quando precisar localizar um processo, cliente ou status específico.',
                    'cor' => '#2563a8',
                    'bg' => '#eff6ff',
                ],
                'lote' => [
                    'titulo' => 'Consulte em lote quando fizer sentido',
                    'texto' => 'Use a consulta geral para revisar a base ou cole uma lista CNJ quando quiser conferir apenas alguns processos.',
                    'cor' => '#059669',
                    'bg' => '#f0fdf4',
                ],
                'monitoramentos' => [
                    'titulo' => 'Deixe monitorado apenas o essencial',
                    'texto' => 'Mantenha aqui os processos que precisam de acompanhamento recorrente. O restante pode ser consultado sob demanda.',
                    'cor' => '#7c3aed',
                    'bg' => '#f5f3ff',
                ],
                'historico' => [
                    'titulo' => 'Pesquise consultas anteriores',
                    'texto' => 'Use o histórico para conferência e pesquisa, sem misturar esses registros com os alertas do dia.',
                    'cor' => '#2563a8',
                    'bg' => '#eff6ff',
                ],
            ];
            $dicaAba = $dicasPorAba[$aba] ?? $dicasPorAba['feed'];
        ?>

        <div style="display:flex;align-items:flex-start;gap:10px;background:<?php echo e($dicaAba['bg']); ?>;border:1px solid <?php echo e($dicaAba['cor']); ?>22;border-left:3px solid <?php echo e($dicaAba['cor']); ?>;border-radius:10px;padding:11px 13px;margin:-4px 0 16px;">
            <span style="width:8px;height:8px;border-radius:50%;background:<?php echo e($dicaAba['cor']); ?>;flex-shrink:0;margin-top:5px;"></span>
            <span>
                <span style="display:block;font-size:12px;font-weight:800;color:<?php echo e($dicaAba['cor']); ?>;margin-bottom:2px;"><?php echo e($dicaAba['titulo']); ?></span>
                <span style="display:block;font-size:12px;color:#475569;line-height:1.45;"><?php echo e($dicaAba['texto']); ?></span>
            </span>
        </div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'feed' && $painelFiltros): ?>

<div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:16px 20px;margin-top:10px;">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:12px;">
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CLIENTE</label>
            <input wire:model.live.debounce.300ms="buscaFeed" placeholder="Buscar cliente..."
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NÚMERO DO PROCESSO</label>
            <input wire:model.live.debounce.300ms="filtroNumero" placeholder="Ex: 0001234-56.2023..."
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS</label>
            <select wire:model.live="filtroStatus"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;background:#fff;cursor:pointer;">
                <option value="Ativo">Apenas Ativos</option>
                <option value="">Todos</option>
                <option value="Encerrado">Encerrados</option>
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">ADVOGADO</label>
            <select wire:model.live="filtroAdvogado"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;background:#fff;cursor:pointer;">
                <option value="">Todos os advogados</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $advogados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($adv->id); ?>"><?php echo e($adv->nome); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;align-items:end;">
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">FASE</label>
            <select wire:model.live="filtroFase"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;background:#fff;cursor:pointer;">
                <option value="">Todas as fases</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <option value="<?php echo e($fase->id); ?>"><?php echo e($fase->descricao); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">PERÍODO — DE</label>
            <input wire:model.live="dataInicio" type="date"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;cursor:pointer;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">PERÍODO — ATÉ</label>
            <input wire:model.live="dataFim" type="date"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#1e293b;outline:none;cursor:pointer;">
        </div>
        <div>
            <button wire:click="limparFiltros"
                style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;color:#64748b;background:#f8fafc;cursor:pointer;">
                Limpar
            </button>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'feed'): ?>

        
        <?php
            $chipContagens = [
                'todos'   => ['label'=>'Todos',   'count'=>$feedQuery->total(), 'bg'=>'#e0f2fe','txt'=>'#0369a1','border'=>'#7dd3fc','dot'=>'#0369a1'],
                'critico' => ['label'=>'Crítico',  'count'=>$totalCriticos,     'bg'=>'#fee2e2','txt'=>'#991b1b','border'=>'#fca5a5','dot'=>'#ef4444'],
                'atencao' => ['label'=>'Atenção',  'count'=>$totalAtencao,      'bg'=>'#fef3c7','txt'=>'#92400e','border'=>'#fde68a','dot'=>'#f59e0b'],
                'normal'  => ['label'=>'Normal',   'count'=>$totalNormal,       'bg'=>'#d1fae5','txt'=>'#065f46','border'=>'#6ee7b7','dot'=>'#10b981'],
            ];
        ?>
        <div style="display:flex;gap:7px;margin-bottom:16px;flex-wrap:wrap;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chipContagens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button wire:click="setFiltroFeed('<?php echo e($val); ?>')"
                        style="display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:99px;
                               font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;
                               border:1.5px solid <?php echo e($filtroPrazo===$val ? $cfg['border'] : '#e2e8f0'); ?>;
                               background:<?php echo e($filtroPrazo===$val ? $cfg['bg'] : 'transparent'); ?>;
                               color:<?php echo e($filtroPrazo===$val ? $cfg['txt'] : '#64748b'); ?>;">
                    <span style="width:7px;height:7px;border-radius:50%;background:<?php echo e($cfg['dot']); ?>;flex-shrink:0;"></span>
                    <?php echo e($cfg['label']); ?>

                    <span style="font-size:11px;opacity:.75;">(<?php echo e($cfg['count']); ?>)</span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php
            $hoje       = \Carbon\Carbon::today();
            $ontem      = \Carbon\Carbon::yesterday();
            $grupoAtual = null;
            $mesesPt    = [
                '01'=>'janeiro','02'=>'fevereiro','03'=>'março','04'=>'abril',
                '05'=>'maio','06'=>'junho','07'=>'julho','08'=>'agosto',
                '09'=>'setembro','10'=>'outubro','11'=>'novembro','12'=>'dezembro',
            ];
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $feedQuery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $andamento   = $processo->andamentos->first();
                $dataAndamen = $andamento ? \Carbon\Carbon::parse($andamento->created_at) : null;
                $grupoData   = $dataAndamen ? $dataAndamen->toDateString() : null;
                $diasSemAnd  = $dataAndamen ? (int) $dataAndamen->diffInDays(now()) : null;

                // ── Detecção de tipo de andamento ──────────────────
                $tipoAndamento = 'outro';
                $descLower     = mb_strtolower($andamento->descricao ?? '');
                if (str_contains($descLower, 'sentença') || str_contains($descLower, 'acórdão')
                    || str_contains($descLower, 'decisão') || str_contains($descLower, 'conclusão')
                    || str_contains($descLower, 'despacho') || str_contains($descLower, 'julgamento')
                    || str_contains($descLower, 'recurso') || str_contains($descLower, 'transitou')) {
                    $tipoAndamento = 'sentenca';
                } elseif (str_contains($descLower, 'prazo') || str_contains($descLower, 'intimação')
                    || str_contains($descLower, 'citação') || str_contains($descLower, 'audiência')
                    || str_contains($descLower, 'notificação') || str_contains($descLower, 'mandado')
                    || str_contains($descLower, 'oficial') || str_contains($descLower, 'edital')) {
                    $tipoAndamento = 'prazo';
                } elseif (str_contains($descLower, 'petição') || str_contains($descLower, 'documento')
                    || str_contains($descLower, 'publicação') || str_contains($descLower, 'remessa')
                    || str_contains($descLower, 'juntada') || str_contains($descLower, 'distribuição')
                    || str_contains($descLower, 'baixa') || str_contains($descLower, 'protocolo')
                    || str_contains($descLower, 'carga') || str_contains($descLower, 'conclusos')) {
                    $tipoAndamento = 'peticao';
                }

                // ── Título e cores por tipo ────────────────────────
                $tipoConfig = [
                    'sentenca' => [
                        'titulo'  => 'Sentença Publicada',
                        'iconBg'  => '#d1fae5', 'iconTxt' => '#065f46',
                        'svg'     => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
                    ],
                    'prazo'    => [
                        'titulo'  => 'Prazo Iniciado',
                        'iconBg'  => '#fef3c7', 'iconTxt' => '#92400e',
                        'svg'     => '<circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>',
                    ],
                    'peticao'  => [
                        'titulo'  => 'Petição Juntada',
                        'iconBg'  => '#dbeafe', 'iconTxt' => '#1d4ed8',
                        'svg'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                    ],
                    'outro'    => [
                        'titulo'  => 'Novo Andamento',
                        'iconBg'  => '#f1f5f9', 'iconTxt' => '#475569',
                        'svg'     => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                    ],
                ];
                $tc = $tipoConfig[$tipoAndamento];

                // ── Score ──────────────────────────────────────────
                $scoreColors = [
                    'critico' => ['badgeBg'=>'#fee2e2','badgeTxt'=>'#991b1b','dot'=>'#ef4444','label'=>'Crítico','border'=>'#fca5a5'],
                    'atencao' => ['badgeBg'=>'#fef3c7','badgeTxt'=>'#92400e','dot'=>'#f59e0b','label'=>'Atenção','border'=>'#fde68a'],
                    'normal'  => ['badgeBg'=>'#d1fae5','badgeTxt'=>'#065f46','dot'=>'#10b981','label'=>'Normal', 'border'=>'#6ee7b7'],
                ];
                $sc = $scoreColors[$processo->score] ?? $scoreColors['normal'];
            ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grupoData !== $grupoAtual): ?>
                <?php $grupoAtual = $grupoData; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grupoData): ?>
                    <?php
                        $dl     = \Carbon\Carbon::parse($grupoData);
                        $diaNum = $dl->format('d');
                        $mes    = $mesesPt[$dl->format('m')] ?? $dl->format('M');
                        if ($dl->isToday()) {
                            $labelGrupo = 'Hoje, ' . $dl->format('H:i');
                        } elseif ($dl->isYesterday()) {
                            $labelGrupo = 'Ontem: ' . $diaNum . ' de ' . $mes;
                        } else {
                            $labelGrupo = 'Dia ' . $diaNum . ' de ' . $mes;
                        }
                    ?>
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;
                                padding:10px 0 6px;margin-top:4px;">
                        <?php echo e($labelGrupo); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div wire:click="abrirProcesso(<?php echo e($processo->id); ?>)"
                 style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:10px;cursor:pointer;
                        border:1.5px solid #f1f5f9;box-shadow:0 1px 3px rgba(0,0,0,.06);
                        transition:border-color .15s,transform .15s,box-shadow .15s;"
                 onmouseover="this.style.borderColor='#cbd5e1';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
                 onmouseout="this.style.borderColor='#f1f5f9';this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">

                <div style="display:flex;align-items:flex-start;gap:12px;">

                    
                    <div style="width:40px;height:40px;border-radius:12px;background:<?php echo e($tc['iconBg']); ?>;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" fill="none" stroke="<?php echo e($tc['iconTxt']); ?>" stroke-width="2" viewBox="0 0 24 24">
                            <?php echo $tc['svg']; ?>

                        </svg>
                    </div>

                    
                    <div style="flex:1;min-width:0;">
                        
                        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:3px;">
                            <span style="font-size:13px;font-weight:700;color:#0f2540;"><?php echo e($tc['titulo']); ?></span>
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;
                                         font-size:10px;font-weight:700;background:<?php echo e($sc['badgeBg']); ?>;color:<?php echo e($sc['badgeTxt']); ?>;">
                                <span style="width:5px;height:5px;border-radius:50%;background:<?php echo e($sc['dot']); ?>;flex-shrink:0;"></span>
                                <?php echo e($sc['label']); ?>

                            </span>
                        </div>
                        
                        <div style="font-size:11px;color:#64748b;margin-bottom:3px;">
                            Processo <span style="font-family:monospace;font-weight:600;color:#334155;"><?php echo e($processo->numero); ?></span>
                        </div>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($andamento && ($andamento->descricao ?? null)): ?>
                            <div style="font-size:12px;color:#475569;line-height:1.5;">
                                <?php echo e(Str::limit($andamento->descricao, 80)); ?>

                            </div>
                        <?php elseif($processo->resumo_ia): ?>
                            <div style="font-size:12px;color:#475569;line-height:1.5;"><?php echo e(Str::limit($processo->resumo_ia, 80)); ?></div>
                        <?php else: ?>
                            <span style="font-size:11px;color:#94a3b8;">sem atualização registrada</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;padding-top:2px;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:11px;color:#64748b;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right;">
                                <?php echo e($processo->cliente->nome ?? '—'); ?>

                            </span>
                            <svg width="14" height="14" style="width:14px;height:14px;min-width:14px;min-height:14px;max-width:14px;max-height:14px;display:block;flex-shrink:0;" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($diasSemAnd !== null && $diasSemAnd >= 7): ?>
                        <?php
                            $diasBg  = $diasSemAnd >= 30 ? '#fee2e2' : ($diasSemAnd >= 14 ? '#fef3c7' : '#f1f5f9');
                            $diasTxt = $diasSemAnd >= 30 ? '#991b1b' : ($diasSemAnd >= 14 ? '#92400e' : '#64748b');
                        ?>
                        <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;background:<?php echo e($diasBg); ?>;color:<?php echo e($diasTxt); ?>;white-space:nowrap;">
                            <?php echo e($diasSemAnd); ?>d sem mov.
                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                </div>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:56px 24px;color:#94a3b8;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 14px;display:block;opacity:.4;">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <p style="font-size:13px;margin:0;color:#64748b;">Nenhum andamento encontrado para o filtro selecionado.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div style="margin-top:14px;"><?php echo e($feedQuery->links()); ?></div>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'lote'): ?>
        <div style="max-width:920px;">

            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #059669;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;align-items:flex-start;gap:12px;">
                <div style="width:30px;height:30px;border-radius:8px;background:#dcfce7;color:#047857;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#064e3b;margin-bottom:3px;">Escolha o tipo de consulta</div>
                    <div style="font-size:12px;color:#475569;line-height:1.5;">
                        Use <strong>todos os processos</strong> para uma revisão completa da base. Use <strong>lista CNJ</strong> quando quiser conferir apenas processos específicos.
                    </div>
                </div>
            </div>

            <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:20px;margin-bottom:18px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div style="display:flex;gap:12px;align-items:flex-start;min-width:260px;flex:1;">
                        <div style="width:40px;height:40px;border-radius:8px;background:#f0fdf4;color:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:800;color:#059669;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Opção 1 · revisão completa</div>
                            <div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:4px;">Consultar todos os processos ativos</div>
                            <div style="font-size:12px;color:var(--muted);line-height:1.5;">
                                Consulta os <?php echo e($totalAtivos ?? 0); ?> processos ativos no DATAJUD. A busca roda em segundo plano e a fila aparece abaixo.
                            </div>
                        </div>
                    </div>
                    <button wire:click="verificarTodos"
                        wire:loading.attr="disabled"
                        wire:confirm="Isso vai verificar todos os processos ativos no DATAJUD. Pode demorar alguns minutos. Continuar?"
                        style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:700;background:#059669;color:#fff;border:none;cursor:pointer;white-space:nowrap;transition:background .15s;flex-shrink:0;"
                        onmouseover="this.style.background='#047857'"
                        onmouseout="this.style.background='#059669'">
                        <svg wire:loading.remove wire:target="verificarTodos" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                        </svg>
                        <svg wire:loading wire:target="verificarTodos" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;">
                            <path d="M21 12a9 9 0 11-6.219-8.56"/>
                        </svg>
                        <span wire:loading.remove wire:target="verificarTodos">Consultar todos os ativos</span>
                        <span wire:loading wire:target="verificarTodos">Enviando...</span>
                    </button>
                </div>
            </div>

            <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:20px;margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                    <div>
                        <div style="font-size:11px;font-weight:800;color:#059669;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Opção 2 · processos selecionados</div>
                        <div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:4px;">Consultar lista CNJ</div>
                        <div style="font-size:12px;color:var(--muted);line-height:1.5;">Use quando quiser consultar apenas alguns números CNJ. Você pode importar uma planilha ou colar um processo por linha.</div>
                    </div>
                    <span style="font-size:11px;color:#64748b;background:#f8fafc;border:1px solid var(--border);border-radius:99px;padding:4px 10px;font-weight:700;">Máx. 500 por vez</span>
                </div>

                <div class="datajud-lote-grid" style="display:grid;grid-template-columns:260px 1fr;gap:16px;align-items:stretch;">
                    <label style="border:2px dashed #dbe3ec;border-radius:10px;padding:18px;text-align:center;background:#f8fafc;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;min-height:150px;">
                        <svg width="30" height="30" fill="none" stroke="#64748b" stroke-width="1.7" viewBox="0 0 24 24" style="margin-bottom:10px;">
                            <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
                            <path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/>
                        </svg>
                        <span style="font-size:13px;color:var(--text);font-weight:700;margin-bottom:4px;">Importar planilha</span>
                        <span style="font-size:11px;color:var(--muted);line-height:1.4;">Arquivos .xlsx ou .csv</span>
                        <input type="file" wire:model="fileLote" accept=".xlsx,.csv" style="margin-top:12px;font-size:12px;color:#64748b;cursor:pointer;max-width:210px;">
                        <span wire:loading wire:target="fileLote" style="font-size:12px;color:#059669;margin-top:8px;">Carregando arquivo...</span>
                    </label>

                    <div style="display:flex;flex-direction:column;">
                        <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Ou cole os números CNJ</label>
                        <textarea wire:model="numerosBrutos"
                                  style="width:100%;height:150px;border:1.5px solid #e2e8f0;border-radius:10px;padding:12px;font-family:monospace;font-size:12px;resize:vertical;background:#fff;color:#1e293b;line-height:1.6;box-sizing:border-box;"
                                  placeholder="0001234-56.2023.8.26.0001&#10;0002345-67.2023.8.26.0001&#10;..."></textarea>
                        <div style="display:flex;align-items:center;gap:10px;margin-top:12px;flex-wrap:wrap;">
                            <button wire:click="verificarLote" wire:loading.attr="disabled"
                                    style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:700;background:#059669;color:#fff;border:none;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                                <span wire:loading.remove wire:target="verificarLote" style="display:contents;">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                                    </svg>
                                    Consultar lista
                                </span>
                                <span wire:loading wire:target="verificarLote" style="display:contents;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;">
                                        <path d="M21 12a9 9 0 11-6.219-8.56"/>
                                    </svg>
                                    Enviando...
                                </span>
                            </button>
                            <span style="font-size:11px;color:var(--muted);">A fila de consulta será exibida abaixo.</span>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filaLote->count()): ?>
            <div style="margin-top:24px;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:8px;">
                    Fila de verificação
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($temVerificando): ?>
                        <span style="font-size:11px;color:#059669;font-weight:500;margin-left:6px;">
                            <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#059669;
                                         animation:pulse 1.5s infinite;margin-right:3px;"></span>
                            consultando...
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                                <th style="text-align:left;padding:9px 12px;font-weight:600;color:#64748b;">Número</th>
                                <th style="text-align:left;padding:9px 12px;font-weight:600;color:#64748b;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filaLote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:9px 12px;font-family:monospace;color:#1e293b;"><?php echo e($item->processo_numero); ?></td>
                                <td style="padding:9px 12px;">
                                    <?php
                                        $badgeLote = [
                                            'aguardando'  => ['bg'=>'#f1f5f9','txt'=>'#64748b','label'=>'Aguardando'],
                                            'verificando' => ['bg'=>'#dbeafe','txt'=>'#1d4ed8','label'=>'Verificando'],
                                            'verificado'  => ['bg'=>'#d1fae5','txt'=>'#065f46','label'=>'Verificado'],
                                            'erro'        => ['bg'=>'#fee2e2','txt'=>'#991b1b','label'=>'Erro'],
                                        ];
                                        $bl = $badgeLote[$item->status] ?? $badgeLote['aguardando'];
                                    ?>
                                    <span style="display:inline-block;padding:2px 9px;border-radius:99px;
                                                 background:<?php echo e($bl['bg']); ?>;color:<?php echo e($bl['txt']); ?>;font-weight:600;font-size:11px;">
                                        <?php echo e($bl['label']); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->erro_mensagem): ?>
                                        <span style="font-size:10px;color:#991b1b;margin-left:4px;" title="<?php echo e($item->erro_mensagem); ?>">
                                            ⚠ <?php echo e(Str::limit($item->erro_mensagem, 40)); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'monitoramentos'): ?>

        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:18px 20px;margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                <div style="min-width:260px;flex:1;">
                    <div style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:4px;">Processos monitorados</div>
                    <div style="font-size:12px;color:var(--muted);line-height:1.5;">
                        <?php echo e($monitorados->count()); ?> processo(s) com acompanhamento recorrente. Mantenha aqui apenas os casos que precisam de atenção contínua.
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:10px;">
                        <span style="font-size:11px;color:#64748b;font-weight:700;">Prioridade:</span>
                        <span style="font-size:10px;font-weight:800;color:#dc2626;background:#fef2f2;border-radius:99px;padding:3px 8px;">Crítico</span>
                        <span style="font-size:10px;font-weight:800;color:#d97706;background:#fffbeb;border-radius:99px;padding:3px 8px;">Atenção</span>
                        <span style="font-size:10px;font-weight:800;color:#059669;background:#f0fdf4;border-radius:99px;padding:3px 8px;">Normal</span>
                    </div>
                </div>
                <button wire:click="abrirModalMonitoramento"
                        style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:8px;
                               font-size:13px;font-weight:700;background:#059669;color:#fff;border:none;cursor:pointer;
                               transition:background .15s;flex-shrink:0;"
                        onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Adicionar monitoramento
                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $monitorados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $scoreColorsM = [
                    'critico' => ['border'=>'#ef4444','iconBg'=>'#fef2f2','iconTxt'=>'#dc2626','label'=>'Crítico'],
                    'atencao' => ['border'=>'#f59e0b','iconBg'=>'#fffbeb','iconTxt'=>'#d97706','label'=>'Atenção'],
                    'normal'  => ['border'=>'#10b981','iconBg'=>'#f0fdf4','iconTxt'=>'#059669','label'=>'Normal'],
                ];
                $sc3 = $scoreColorsM[$proc->score] ?? $scoreColorsM['normal'];
                $freqLabel = ['6h'=>'a cada 6h','12h'=>'a cada 12h','diario'=>'diário'][$proc->frequencia_monitoramento] ?? 'diário';
            ?>
            <div style="background:#fff;border:1.5px solid var(--border);border-left:4px solid <?php echo e($sc3['border']); ?>;border-radius:12px;padding:16px 18px;margin-bottom:10px;display:flex;align-items:center;gap:14px;box-shadow:0 6px 18px rgba(15,37,64,.04);">

                
                <div style="width:40px;height:40px;border-radius:8px;background:<?php echo e($sc3['iconBg']); ?>;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="17" height="17" fill="none" stroke="<?php echo e($sc3['iconTxt']); ?>" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>

                
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:800;color:#0f2540;font-family:monospace;"><?php echo e($proc->numero_processo); ?></span>
                        <span style="font-size:10px;font-weight:800;color:<?php echo e($sc3['iconTxt']); ?>;background:<?php echo e($sc3['iconBg']); ?>;border-radius:99px;padding:2px 8px;">Prioridade <?php echo e($sc3['label']); ?></span>
                        <span style="font-size:10px;font-weight:800;color:<?php echo e($proc->ativo ? '#059669' : '#64748b'); ?>;background:<?php echo e($proc->ativo ? '#f0fdf4' : '#f1f5f9'); ?>;border-radius:99px;padding:2px 8px;"><?php echo e($proc->ativo ? 'Monitorando' : 'Pausado'); ?></span>
                    </div>
                    <div style="font-size:12px;color:#64748b;font-weight:600;"><?php echo e($proc->processo->cliente->nome ?? 'Cliente não informado'); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proc->processo->vara ?? null): ?>
                        <div style="font-size:11px;color:#94a3b8;"><?php echo e($proc->processo->vara); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div style="display:flex;align-items:center;gap:6px;margin-top:6px;flex-wrap:wrap;padding-top:6px;border-top:1px solid #f1f5f9;">
                        <svg width="11" height="11" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span style="font-size:11px;color:#94a3b8;">Verificação <?php echo e($freqLabel); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proc->ultimo_andamento_data): ?>
                            <span style="font-size:11px;color:#94a3b8;">
                                · atualizado <?php echo e(\Carbon\Carbon::parse($proc->ultimo_andamento_data)->diffForHumans()); ?>

                            </span>
                        <?php else: ?>
                            <span style="font-size:11px;color:#94a3b8;">sem atualização registrada</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
                    <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;">Monitoramento</span>
                    <div style="display:flex;align-items:center;gap:10px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proc->ativo): ?>
                        <span style="width:8px;height:8px;border-radius:50%;background:#059669;
                                     animation:pulse 1.5s cubic-bezier(0,0,.2,1) infinite;display:inline-block;"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button wire:click="toggleMonitoramento(<?php echo e($proc->id); ?>)"
                            title="<?php echo e($proc->ativo ? 'Pausar' : 'Ativar'); ?> monitoramento"
                            style="width:42px;height:24px;border-radius:99px;border:none;cursor:pointer;
                                   background:<?php echo e($proc->ativo ? '#059669' : '#cbd5e1'); ?>;
                                   position:relative;transition:background .2s;">
                        <span style="position:absolute;top:3px;
                                     left:<?php echo e($proc->ativo ? '20px' : '3px'); ?>;
                                     width:18px;height:18px;border-radius:50%;background:#fff;
                                     transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.25);"></span>
                    </button>
                    </div>
                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:56px 24px;color:#94a3b8;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 14px;display:block;opacity:.4;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <p style="font-size:13px;margin:0;color:#64748b;font-weight:700;">Nenhum processo em monitoramento.</p>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Clique em "Adicionar processo" para começar pelos casos mais importantes.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modalMonitoramento): ?>
        <div style="position:fixed;inset:0;background:rgba(15,37,64,.5);z-index:999;display:flex;align-items:center;justify-content:center;"
             wire:click.self="fecharModalMonitoramento">
            <div style="background:#fff;border-radius:16px;width:480px;max-width:95vw;padding:24px;
                        box-shadow:0 24px 64px rgba(0,0,0,.18);" wire:click.stop>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f2540;">Adicionar Monitoramento</h3>
                    <button wire:click="fecharModalMonitoramento"
                            style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;line-height:1;
                                   width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">&times;</button>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:5px;">
                        Buscar processo (número ou cliente)
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="buscaMonitoramento"
                           style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;outline:none;"
                           placeholder="Ex: 0001234 ou João Silva">
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($processosBusca)): ?>
                <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:12px;max-height:200px;overflow-y:auto;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $processosBusca; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div wire:click="selecionarProcessoMonit(<?php echo e($pb->id); ?>)"
                         style="padding:9px 12px;cursor:pointer;border-bottom:1px solid #f1f5f9;
                                background:<?php echo e($processoMonitId === $pb->id ? '#d1fae5' : '#fff'); ?>;
                                transition:background .1s;"
                         onmouseover="if(<?php echo e($processoMonitId !== $pb->id ? 'true' : 'false'); ?>)this.style.background='#f8fafc'"
                         onmouseout="this.style.background='<?php echo e($processoMonitId === $pb->id ? '#d1fae5' : '#fff'); ?>'">
                        <div style="font-size:12px;font-weight:700;font-family:monospace;color:#1e293b;"><?php echo e($pb->numero); ?></div>
                        <div style="font-size:11px;color:#64748b;"><?php echo e($pb->cliente->nome ?? '—'); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php elseif(strlen($buscaMonitoramento) >= 2): ?>
                    <p style="font-size:12px;color:#94a3b8;margin-bottom:12px;">Nenhum processo encontrado.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div style="margin-bottom:20px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:5px;">
                        Frequência de verificação
                    </label>
                    <select wire:model="frequenciaSelect"
                            style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff;outline:none;">
                        <option value="6h">A cada 6 horas</option>
                        <option value="12h">A cada 12 horas</option>
                        <option value="diario">Diariamente</option>
                    </select>
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button wire:click="fecharModalMonitoramento"
                            style="padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;
                                   border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;color:#64748b;">
                        Cancelar
                    </button>
                    <button wire:click="confirmarMonitoramento"
                            style="padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;
                                   background:#059669;color:#fff;border:none;cursor:pointer;
                                   transition:background .15s;"
                            onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aba === 'historico'): ?>

        <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:18px 20px;margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                <div style="min-width:260px;flex:1;">
                    <div style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:4px;">Histórico de movimentações</div>
                    <div style="font-size:12px;color:var(--muted);line-height:1.5;">
                        <?php echo e($historico->total()); ?> registro(s) encontrado(s). Use esta área para pesquisa e conferência de andamentos já recebidos.
                    </div>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:#64748b;background:#f8fafc;border:1px solid var(--border);border-radius:99px;padding:5px 10px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Mais recentes primeiro
                </span>
            </div>
        </div>

        <div style="display:flex;gap:7px;flex-wrap:wrap;margin-bottom:18px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                'todos'      => 'Todos',
                'sentencas'  => 'Sentenças',
                'prazos'     => 'Prazos',
                'andamentos' => 'Andamentos',
                'decisoes'   => 'Decisões',
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button wire:click="setFiltroHistorico('<?php echo e($val); ?>')"
                        style="padding:6px 13px;border-radius:99px;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s;
                               border:1.5px solid <?php echo e($filtroHistorico===$val ? '#059669' : '#e2e8f0'); ?>;
                               background:<?php echo e($filtroHistorico===$val ? '#f0fdf4' : '#fff'); ?>;
                               color:<?php echo e($filtroHistorico===$val ? '#065f46' : '#64748b'); ?>;">
                    <?php echo e($lbl); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div style="position:relative;padding-left:26px;">
            <div style="position:absolute;left:7px;top:0;bottom:0;width:2px;background:#e2e8f0;border-radius:1px;"></div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $historico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $tipo = $item->tipo ?? 'andamento';
                    $dotColor = match($tipo) {
                        'sentenca','decisao_urgente' => '#ef4444',
                        'prazo'                      => '#f59e0b',
                        'decisao'                    => '#3b82f6',
                        default                      => '#10b981',
                    };
                    $badgeTimeline = [
                        'sentenca'        => ['bg'=>'#fee2e2','txt'=>'#991b1b','label'=>'Sentença'],
                        'prazo'           => ['bg'=>'#fef3c7','txt'=>'#92400e','label'=>'Prazo'],
                        'andamento'       => ['bg'=>'#d1fae5','txt'=>'#065f46','label'=>'Andamento'],
                        'decisao'         => ['bg'=>'#dbeafe','txt'=>'#1e40af','label'=>'Decisão'],
                        'decisao_urgente' => ['bg'=>'#fee2e2','txt'=>'#991b1b','label'=>'Decisão Urgente'],
                    ];
                    $bt = $badgeTimeline[$tipo] ?? $badgeTimeline['andamento'];
                ?>

                <div style="position:relative;margin-bottom:14px;">
                    <div style="position:absolute;left:-23px;top:15px;width:10px;height:10px;border-radius:50%;
                                background:<?php echo e($dotColor); ?>;border:2px solid #fff;box-shadow:0 0 0 2px <?php echo e($dotColor); ?>40;"></div>
                    <div style="background:#fff;border-radius:12px;padding:14px 16px;border:1.5px solid var(--border);border-left:3px solid <?php echo e($bt['txt']); ?>;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;font-weight:800;font-family:monospace;color:#0f2540;">
                                    <?php echo e(optional($item->processo)->numero ?? '—'); ?>

                                </div>
                                <div style="font-size:11px;color:#64748b;margin-bottom:6px;font-weight:600;">
                                    <?php echo e(optional(optional($item->processo)->cliente)->nome ?? '—'); ?>

                                </div>
                                <div style="font-size:12px;color:#334155;line-height:1.5;">
                                    <?php echo e(Str::limit($item->descricao ?? $item->texto ?? '—', 120)); ?>

                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->processo): ?>
                                    <button wire:click="abrirProcesso(<?php echo e($item->processo->id); ?>)"
                                            style="margin-top:10px;display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid #e2e8f0;color:#2563a8;border-radius:7px;padding:5px 9px;font-size:11px;font-weight:800;cursor:pointer;">
                                        Abrir processo
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="9 18 15 12 9 6"/>
                                        </svg>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                                <span style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:99px;
                                             background:<?php echo e($bt['bg']); ?>;color:<?php echo e($bt['txt']); ?>;"><?php echo e($bt['label']); ?></span>
                                <span style="font-size:10px;color:#94a3b8;font-weight:700;">
                                    <?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;padding:40px 0;color:#94a3b8;">
                    <p style="font-size:13px;margin:0;">Nenhum registro encontrado.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($historico->hasMorePages()): ?>
        <div style="text-align:center;margin-top:10px;">
            <button wire:click="carregarMais"
                    style="padding:9px 24px;border-radius:8px;font-size:13px;font-weight:600;
                           border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;color:#334155;
                           transition:border-color .15s;"
                    onmouseover="this.style.borderColor='#059669'" onmouseout="this.style.borderColor='#e2e8f0'">
                <span wire:loading.remove wire:target="carregarMais">Carregar mais</span>
                <span wire:loading wire:target="carregarMais">Carregando...</span>
            </button>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

    </div>


    
    <div class="datajud-sidebar" style="width:300px;flex-shrink:0;background:#fff;border-left:1.5px solid #e2e8f0;
                position:sticky;top:0;height:calc(100vh - 52px);overflow-y:auto;
                display:flex;flex-direction:column;margin-right:-24px;margin-top:-24px;margin-bottom:-24px;">

        
        <div wire:poll.15s="$refresh" style="display:none"></div>

        
        <div style="padding:16px;border-bottom:1.5px solid #e2e8f0;display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <span style="font-size:14px;font-weight:800;color:var(--text);flex:1;">Avisos Recentes</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notificacoesNaoLidas > 0): ?>
                <span style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;
                             padding:2px 7px;border-radius:99px;min-width:20px;text-align:center;">
                    <?php echo e($notificacoesNaoLidas); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button wire:click="atualizarAgora"
                    style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;border-radius:6px;
                           display:flex;align-items:center;justify-content:center;transition:color .15s,background .15s;"
                    title="Recarregar notificações"
                    onmouseover="this.style.color='#059669';this.style.background='#f0fdf4'"
                    onmouseout="this.style.color='#94a3b8';this.style.background='none'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                </svg>
            </button>
        </div>

        
        <div style="flex:1;overflow-y:auto;padding:10px;display:flex;flex-direction:column;gap:6px;">
            <?php
                $estilosPorTipo = [
                    'critico'   => ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#fef2f2','txt'=>'#dc2626','label'=>'Crítico'],
                    'decisao'   => ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#fef2f2','txt'=>'#dc2626','label'=>'Decisão'],
                    'prazo'     => ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#fffbeb','txt'=>'#d97706','label'=>'Prazo'],
                    'atencao'   => ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#fffbeb','txt'=>'#d97706','label'=>'Atenção'],
                    'andamento' => ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#f0fdf4','txt'=>'#059669','label'=>'Andamento'],
                    'normal'    => ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#f0fdf4','txt'=>'#059669','label'=>'Normal'],
                ];
                $estiloPadrao = ['bg'=>'#fff','border'=>'#e2e8f0','iconBg'=>'#eff6ff','txt'=>'#2563a8','label'=>'Informativo'];

                // Agrupar por tipo, preservando a primeira notificação de cada grupo
                $grupos = [];
                foreach ($notificacoes as $notif) {
                    $tipo = $notif->tipo ?? 'informativo';
                    if (!isset($grupos[$tipo])) {
                        $grupos[$tipo] = ['itens' => [], 'estilo' => $estilosPorTipo[$tipo] ?? $estiloPadrao];
                    }
                    $grupos[$tipo]['itens'][] = $notif;
                }
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $st    = $grupo['estilo'];
                    $itens = $grupo['itens'];
                    $total = count($itens);
                    $first = $itens[0];
                    $grupoId = 'notif-grupo-' . $loop->index;
                ?>

                
                <div style="background:#fff;border:1px solid <?php echo e($st['border']); ?>;border-left:3px solid <?php echo e($st['txt']); ?>;border-radius:10px;overflow:hidden;">

                    
                    <div onclick="
                            var el = document.getElementById('<?php echo e($grupoId); ?>');
                            var arr = document.getElementById('<?php echo e($grupoId); ?>-arr');
                            if(el.style.display==='none'){el.style.display='flex';arr.style.transform='rotate(180deg)';}
                            else{el.style.display='none';arr.style.transform='rotate(0deg)';}
                         "
                         style="display:flex;align-items:center;gap:7px;padding:9px 11px;cursor:pointer;">
                        <div style="width:24px;height:24px;border-radius:7px;background:<?php echo e($st['iconBg']); ?>;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="12" height="12" fill="none" stroke="<?php echo e($st['txt']); ?>" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 01-3.46 0"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <span style="font-size:12px;font-weight:700;color:<?php echo e($st['txt']); ?>;"><?php echo e($st['label']); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 1): ?>
                                <span style="display:inline-block;margin-left:5px;font-size:10px;font-weight:700;
                                             padding:1px 6px;border-radius:99px;background:#f8fafc;color:<?php echo e($st['txt']); ?>;border:1px solid <?php echo e($st['txt']); ?>33;">
                                    <?php echo e($total); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <span style="font-size:10px;color:#94a3b8;white-space:nowrap;">
                            <?php echo e(\Carbon\Carbon::parse($first->created_at)->diffForHumans(null, true)); ?>

                        </span>
                        <svg id="<?php echo e($grupoId); ?>-arr" width="12" height="12" fill="none" stroke="#94a3b8" stroke-width="2.5"
                             viewBox="0 0 24 24" style="flex-shrink:0;transition:transform .2s;">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>

                    
                    <div style="padding:0 11px 9px;border-top:1px solid <?php echo e($st['border']); ?>;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($first->processo_id): ?>
                            <div style="font-size:11px;font-weight:700;color:<?php echo e($st['txt']); ?>;opacity:.8;margin-top:6px;margin-bottom:2px;font-family:monospace;">
                                <?php echo e(optional($first->processo)->numero ?? '#'.$first->processo_id); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div style="font-size:11px;color:#475569;line-height:1.5;margin-top:<?php echo e($first->processo_id ? '0' : '6px'); ?>;">
                            <?php echo e($first->mensagem); ?>

                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 1): ?>
                        <div id="<?php echo e($grupoId); ?>" style="display:none;flex-direction:column;gap:0;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($itens, 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notifExtra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="padding:7px 11px;border-top:1px solid <?php echo e($st['border']); ?>;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifExtra->processo_id): ?>
                                        <div style="font-size:11px;font-weight:700;color:<?php echo e($st['txt']); ?>;opacity:.8;margin-bottom:2px;font-family:monospace;">
                                            <?php echo e(optional($notifExtra->processo)->numero ?? '#'.$notifExtra->processo_id); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px;">
                                        <div style="font-size:11px;color:#475569;line-height:1.5;flex:1;"><?php echo e($notifExtra->mensagem); ?></div>
                                        <span style="font-size:10px;color:#94a3b8;white-space:nowrap;flex-shrink:0;">
                                            <?php echo e(\Carbon\Carbon::parse($notifExtra->created_at)->diffForHumans(null, true)); ?>

                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;padding:48px 12px;color:#94a3b8;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;opacity:.5;">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    <p style="font-size:12px;margin:0;">Sem notificações</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div style="padding:10px;border-top:1.5px solid #e2e8f0;flex-shrink:0;">
            <button style="width:100%;padding:9px;border-radius:8px;font-size:12px;font-weight:600;
                           background:#f8fafc;color:#64748b;border:1.5px solid #e2e8f0;cursor:pointer;
                           transition:border-color .15s,color .15s;"
                    onmouseover="this.style.borderColor='#059669';this.style.color='#059669'"
                    onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                Outros Filtros
            </button>
        </div>

    </div>

</div>

<style>
@keyframes spin  { to { transform: rotate(360deg); } }
@keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.5; transform:scale(1.4); } }
.datajud-page svg { flex-shrink: 0; }
.datajud-page svg[width="11"] { width: 11px !important; height: 11px !important; max-width: 11px !important; max-height: 11px !important; }
.datajud-page svg[width="12"] { width: 12px !important; height: 12px !important; max-width: 12px !important; max-height: 12px !important; }
.datajud-page svg[width="13"] { width: 13px !important; height: 13px !important; max-width: 13px !important; max-height: 13px !important; }
.datajud-page svg[width="14"] { width: 14px !important; height: 14px !important; max-width: 14px !important; max-height: 14px !important; }
.datajud-page svg[width="15"] { width: 15px !important; height: 15px !important; max-width: 15px !important; max-height: 15px !important; }
.datajud-page svg[width="16"] { width: 16px !important; height: 16px !important; max-width: 16px !important; max-height: 16px !important; }
.datajud-page svg[width="17"] { width: 17px !important; height: 17px !important; max-width: 17px !important; max-height: 17px !important; }
.datajud-page svg[width="18"] { width: 18px !important; height: 18px !important; max-width: 18px !important; max-height: 18px !important; }
.datajud-page svg[width="28"] { width: 28px !important; height: 28px !important; max-width: 28px !important; max-height: 28px !important; }
.datajud-page svg[width="30"] { width: 30px !important; height: 30px !important; max-width: 30px !important; max-height: 30px !important; }
.datajud-page svg[width="40"] { width: 40px !important; height: 40px !important; max-width: 40px !important; max-height: 40px !important; }
@media (max-width: 1180px) {
    .datajud-layout { grid-template-columns: 1fr !important; }
    .datajud-sidebar { width: auto !important; position: static !important; height: auto !important; margin: 20px 0 0 0 !important; border-left: 1.5px solid var(--border) !important; border-radius: 16px !important; }
}
@media (max-width: 900px) {
    .datajud-kpis { grid-template-columns: repeat(2, 1fr) !important; }
    .datajud-guide-actions { min-width: 100% !important; grid-template-columns: 1fr !important; }
    .datajud-lote-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 560px) {
    .datajud-kpis { grid-template-columns: 1fr !important; }
}
</style>

</div>











   
<?php /**PATH C:\projetos\saproweb-base\resources\views/livewire/processos/monitoramento.blade.php ENDPATH**/ ?>