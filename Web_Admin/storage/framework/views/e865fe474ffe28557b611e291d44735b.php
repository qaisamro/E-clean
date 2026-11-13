<?php $__env->startSection('content'); ?>
    <div class="mt-3 container-fluid">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="card-header py-2">
                        <h3 class="m-0"><?php echo e(__('Edit') . ' ' . __('Profile')); ?></h3>
                    </div>
                    <form <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('profile.update')): ?> action="<?php echo e(route('profile.update')); ?>" <?php endif; ?> method="POST"
                        enctype="multipart/form-data">
                        <div class="card-body">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo e(__('First_Name')); ?></label>
                                        <input class="form-control" type="text" name="first_name"
                                            value="<?php echo e($user->first_name); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo e(__('Last_Name')); ?></label>
                                        <input class="form-control" type="text" name="last_name"
                                            value="<?php echo e($user->last_name); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo e(__('Email_Address')); ?></label>
                                        <input class="form-control" type="text" name="email"
                                            value="<?php echo e($user->email); ?>" <?php if (\Illuminate\Support\Facades\Blade::check('role', 'root')): ?> <?php endif; ?>>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo e(__('Mobile_number')); ?></label>
                                        <input class="form-control" type="number" name="mobile"
                                            value="<?php echo e($user->mobile); ?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo e(__('Profile_Photo')); ?></label>
                                        <input class="form-control-file" type="file" name="profile_photo">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?php echo e(route('profile.index')); ?>" class="btn btn-danger">
                                <i class="fa fa-arrow-left"></i> <?php echo e(__('Back')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('profile.update')): ?>
                                <button class="btn btn-primary <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"
                                    type="submit"><?php echo e(__('Update')); ?></button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\profile\edit.blade.php ENDPATH**/ ?>