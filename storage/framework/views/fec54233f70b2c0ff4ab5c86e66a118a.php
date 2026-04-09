<div>

    
    <div class="filter-bar" style="margin-bottom:20px;">
        <div style="position:relative;flex:1;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;display:flex;align-items:center;">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar template..." style="padding-left:34px;width:100%;">
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$mostrarForm): ?>
        <button wire:click="novo" class="btn btn-primary btn-sm" style="flex-shrink:0;margin-left:auto;">+ Novo Template</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mostrarForm): ?>
    <div class="card" style="margin-bottom:20px;border:1px solid #bfdbfe;background:#f0f7ff;">
        <div class="card-header">
            <span class="card-title"><?php echo $editandoId
                ? '<span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Editar Template</span>'
                : '<span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Novo Template</span>'; ?></span>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;padding:4px 0;">

            <div class="form-grid" style="grid-template-columns:1fr auto;align-items:start;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Título *</label>
                    <input type="text" wire:model="titulo" placeholder="Nome do template"
                        style="width:100%;padding:8px 10px;border:1px solid <?php echo e($errors->has('titulo') ? '#dc2626' : 'var(--border)'); ?>;border-radius:6px;font-size:13px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span style="font-size:12px;color:#dc2626;"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Categoria</label>
                    <select wire:model="categoria"
                        style="padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">
                    Corpo do Template *
                    <span style="font-weight:400;color:#64748b;">— use os placeholders abaixo para inserir dados do processo</span>
                </label>
                <textarea wire:model="corpo" rows="12" placeholder="Digite o texto da minuta aqui. Use {{cliente_nome}}, {{processo_numero}}, etc."
                    style="width:100%;padding:10px;border:1px solid <?php echo e($errors->has('corpo') ? '#dc2626' : 'var(--border)'); ?>;border-radius:6px;font-size:13px;font-family:monospace;resize:vertical;"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['corpo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span style="font-size:12px;color:#dc2626;"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:12px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Placeholders disponíveis — clique para copiar</div>
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Livewire\Minutas::$placeholders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                        data-ph="<?php echo e($ph); ?>"
                        onclick="navigator.clipboard.writeText(this.dataset.ph).then(() => { this.style.background='#dcfce7'; setTimeout(()=>this.style.background='',1000); })"
                        title="<?php echo e($desc); ?>"
                        style="padding:3px 8px;background:#e2e8f0;border:none;border-radius:4px;font-size:11px;font-family:monospace;cursor:pointer;color:#334155;transition:background .2s;">
                        <?php echo e($ph); ?>

                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:16px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" wire:model="ativo"> Ativo
                </label>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <button wire:click="salvar" class="btn btn-primary btn-sm">Salvar</button>
                    <button wire:click="cancelar" class="btn btn-secondary btn-sm">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($minutas->isEmpty()): ?>
    <div class="card">
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
            Nenhum template cadastrado ainda. Clique em "+ Novo Template" para começar.
        </p>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Título</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Categoria</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;text-align:center;">Status</th>
                        <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;text-align:center;">Atualizado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $minutas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="<?php echo e(!$m->ativo ? 'opacity:.5;' : ''); ?>">
                        <td style="font-weight:600;"><?php echo e($m->titulo); ?></td>
                        <td>
                            <span class="badge" style="background:#2563a822;color:#2563a8;">
                                <?php echo e($categorias[$m->categoria] ?? $m->categoria); ?>

                            </span>
                        </td>
                        <td style="text-align:center;">
                            <button wire:click="toggleAtivo(<?php echo e($m->id); ?>)"
                                style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;border:none;cursor:pointer;
                                       background:<?php echo e($m->ativo ? '#dcfce7' : '#f1f5f9'); ?>;color:<?php echo e($m->ativo ? '#16a34a' : '#64748b'); ?>;">
                                <?php echo e($m->ativo ? 'Ativo' : 'Inativo'); ?>

                            </button>
                        </td>
                        <td style="text-align:center;color:var(--muted);font-size:12px;">
                            <?php echo e($m->updated_at->format('d/m/Y')); ?>

                        </td>
                        <td style="text-align:right;">
                            <button wire:click="editar(<?php echo e($m->id); ?>)"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#e0f2fe;color:#0369a1;" title="Editar">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="excluir(<?php echo e($m->id); ?>)"
                                wire:confirm="Excluir o template '<?php echo e($m->titulo); ?>'?"
                                style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;" title="Excluir">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH C:\projetos\saproweb-base\resources\views/livewire/minutas.blade.php ENDPATH**/ ?>