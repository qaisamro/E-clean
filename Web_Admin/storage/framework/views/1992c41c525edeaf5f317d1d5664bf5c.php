<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row h-100vh align-items-center">
            <div class="col-md-10 col-lg-9 col-sm-12 m-auto">
                <form action="<?php echo e(route('stripeKey.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary py-2">
                            <h3 class="text-white m-0"><?php echo e(__('Stripe payment key set')); ?></h3>
                        </div>
                        <div class="card-body pb-3">
                            <div class="mb-2">
                                <label class="mb-1 text-dark"><?php echo e(__('Stripe Public Key')); ?> <span class="text-danger">*</span></label>
                                <textarea name="public_key" class="form-control" rows="2" placeholder="<?php echo e(__('Stripe Public Key')); ?>">
                                    <?php echo e(config('app.stripe_key')); ?>

                                </textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['public_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="mb-2">
                                <label class="mb-1 text-dark"><?php echo e(__('Stripe Secret Key')); ?> <span class="text-danger">*</span></label>
                                <textarea name="secret_key" class="form-control" rows="2" placeholder="<?php echo e(__('Stripe Secret Key')); ?>">
                                    <?php echo e(config('app.stripe_secret')); ?>

                                </textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['secret_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer py-3 ">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary rounded-0 <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__('Save And Update')); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $('textarea').each(function(){
            $(this).val($(this).val().trim());
        }
    );
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\stripe-key.blade.php ENDPATH**/ ?>