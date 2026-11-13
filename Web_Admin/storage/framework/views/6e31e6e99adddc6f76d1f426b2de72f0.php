
<input type="hidden" name="permissions[]" value="<?php echo e($permission); ?>">
<div class="row">
    <div class="col-6">
        <?php echo e(__($title)); ?>:
    </div>
    <div class="col-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($permission, $permissions)): ?>
            <label for="<?php echo e($name); ?>_yes" class="mx-2">
                <input type="radio" id="<?php echo e($name); ?>_yes" checked name="<?php echo e($name); ?>" value="<?php echo e($permission); ?>"> <?php echo e(__('Yes')); ?>

            </label>
            <label for="<?php echo e($name); ?>_no">
                <input type="radio" id="<?php echo e($name); ?>_no" name="<?php echo e($name); ?>" value=""> <?php echo e(__('No')); ?>

            </label>
        <?php else: ?>
            <label for="<?php echo e($name); ?>_yes" class="mx-2">
                <input type="radio" id="<?php echo e($name); ?>_yes" name="<?php echo e($name); ?>" value="<?php echo e($permission); ?>"> <?php echo e(__('Yes')); ?>

            </label>
            <label for="<?php echo e($name); ?>_no">
                <input type="radio" id="<?php echo e($name); ?>_no" checked name="<?php echo e($name); ?>" value=""> <?php echo e(__('No')); ?>

            </label>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\components\permission-input.blade.php ENDPATH**/ ?>