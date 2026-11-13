<?php $__env->startSection('content'); ?>
    <div class="mt-3 container-fluid">
        <div class="row d-flex align-items-center" style="min-height: 80vh">
            <div class="col-md-8 m-auto">
                <form action="<?php echo e(route('profile.change-password')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary py-2">
                            <h3 class="m-0 text-white"><?php echo e(__('Change_Password')); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="mb-0"><?php echo e(__('Current_Password')); ?></label>
                                <input type="text" name="current_password" placeholder="<?php echo e(__('Current_Password')); ?>"
                                    class="form-control" value="<?php echo e(old('current_password')); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="mb-0"><?php echo e(__('New_Password')); ?></label>
                                <input type="text" name="password" placeholder="<?php echo e(__('New_Password')); ?>" class="form-control" value="<?php echo e(old('password')); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="form-group mb-0">
                                <label class="mb-0"><?php echo e(__('Confirm_Password')); ?></label>
                                <input type="text" name="password_confirmation" placeholder="<?php echo e(__('Confirm_Password')); ?>" class="form-control">
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between py-2">
                            <a href="<?php echo e(route('profile.index')); ?>" class="btn btn-danger">
                               <i class="fa fa-arrow-left"></i> <?php echo e(__('Back')); ?>

                            </a>
                            <button class="btn btn-primary <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__('Submit')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\profile\change-password.blade.php ENDPATH**/ ?>