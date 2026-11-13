<?php $__env->startSection('content'); ?>
    <div class="mt-3 container-fluid">
        <div class="row d-flex align-items-center" style="min-height: 80vh">
            <div class="col-md-8 m-auto">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo e($user->profile_photo_path); ?>" width="130">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('profile.change-password')): ?>
                                <div class="mt-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root')): ?>
                                        <a href="<?php echo e(route('profile.change-password.form')); ?>" class="btn btn-primary">
                                            <?php echo e(__('Change_Password')); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('profile.edit')): ?>
                                <div class="d-flex justify-content-end">
                                    <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary btn-sm"><i
                                            class="fa fa-edit"></i>
                                        <?php echo e(__('Edit')); ?></a>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h3><?php echo e($user->name); ?></h3>
                                <p><?php echo e($user->email); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\profile\index.blade.php ENDPATH**/ ?>