<?php $__env->startSection('content'); ?>
<div class="row px-5">
    <div class="col-6 col-md-4 ms-auto d-flex align-items-center me-5" style="height: 100vh;" id="mainCard">
        <div class="card w-100 pt-3" style="overflow: hidden;border-bottom: none;">
            <div class="loader" id="loader" style="display: none">
                <img src="https://i.ibb.co/9mWzCWCt/cog-loader.gif" alt="cog" border="0">
            </div>

            <h2 class="text-center mt-3">Verify Your Purchase</h2>
            <p class="fs-7 text-center">
                <strong class="text-danger">You're all set, and now you can upgrade.</strong>
            </p>

            <div class="w-80 m-auto">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('installer.verify_purchase')): ?>
                <form method="post" action="<?php echo e(route('updater.purcgase-verify')); ?>"> <?php echo csrf_field(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $verifyRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $verifyRule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3">
                        <label for=""><?php echo e($verifyRule['label']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(substr($verifyRule['rule'], 0, 8) === 'required'): ?><strong class="text-danger">*</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <input type="<?php echo e($verifyRule['type']); ?>" name="<?php echo e($name); ?>" placeholder="<?php echo e($verifyRule['placeholder']); ?>" value="<?php echo e(old($name)); ?>" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="my-4 py-4 absolute-bottom-left right-0 d-flex justify-content-center">
                        <button type="submit" class="btn btn-install text-uppercase">Verify Purchase</button>
                    </div>
                </form>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            

            </div>

            <div class="row">
                <div class="col" style="min-height: 3px; background: #006a4e"></div>
                <div class="col" style="min-height: 3px;  background: #f42a41"></div>
                <div class="col" style="min-height: 3px;  background: #006a4e"></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
<script>
    Swal.fire({
        title: 'Wrong!',
        text: "<?php echo e(session('error')); ?>",
        icon: 'error',
        confirmButtonText: 'Try Again'
    })
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('joynala.web-installer::app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\vendor\joynala\web-installer\resources\views\update\verify.blade.php ENDPATH**/ ?>