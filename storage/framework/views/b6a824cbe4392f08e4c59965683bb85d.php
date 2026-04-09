

<?php $__env->startSection('page-title', 'Prazos'); ?>






<?php $__env->startSection('content'); ?>
<div style="margin-bottom:24px;">
    <h2 style="font-size:20px;font-weight:700;color:var(--primary);">⏳ Controle de Prazos</h2>
    <p style="font-size:13px;color:#64748b;margin-top:4px;">Gerencie prazos processuais com cálculo automático de dias úteis e corridos</p>
</div>

<?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('prazos');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3013436724-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/prazos.blade.php ENDPATH**/ ?>