
<?php $__env->startSection('titulo', 'Usuários'); ?>
<?php $__env->startSection('page-title', 'Gerenciar Usuários'); ?>
<?php $__env->startSection('breadcrumb'); ?>Administração <span class="sep">›</span> <span class="current">Usuários</span><?php $__env->stopSection(); ?>
<?php $__env->startSection('conteudo'); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('usuarios');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1454127205-0', $__key);

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\saproweb-base\resources\views/usuarios.blade.php ENDPATH**/ ?>