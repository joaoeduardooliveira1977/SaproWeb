

<?php $__env->startSection('page-title', 'Financeiro'); ?>
<?php $__env->startSection('breadcrumb'); ?>Financeiro <span class="sep">›</span> <span class="current">Por Processo</span><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div>
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:var(--primary);display:flex;align-items:center;gap:8px;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Financeiro</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Pagamentos, recebimentos e apontamentos por processo</p>
    </div>

    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:20px;">
        <label style="font-size:13px; font-weight:600; color:#374151;">Selecione um processo:</label>
        <select id="processo-select" onchange="trocarProcesso(this.value)"
            style="margin-left:12px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; min-width:300px;">
            <option value="">— Selecione —</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Processo::with('cliente')->where('status','Ativo')->orderBy('numero')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->id); ?>" <?php echo e(request('processo_id') == $p->id ? 'selected' : ''); ?>>
                <?php echo e($p->numero); ?> — <?php echo e($p->cliente?->nome ?? 'Sem cliente'); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('processo_id')): ?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('financeiro', ['processoId' => (int) request('processo_id')]);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395823633-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php else: ?>
    <div style="background:white; border-radius:12px; padding:48px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="margin-bottom:12px;display:flex;justify-content:center;"><svg aria-hidden="true" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <p style="color:#64748b; font-size:14px;">Selecione um processo acima para ver o financeiro.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
function trocarProcesso(id) {
    if (id) window.location.href = '/financeiro?processo_id=' + id;
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/financeiro.blade.php ENDPATH**/ ?>