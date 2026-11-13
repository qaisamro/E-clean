<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 m-auto">
                <form action="<?php echo e(route('mobileApp.store')); ?>" method="post">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary py-2">
                            <h3 class="m-0 text-white"><?php echo e(__('Mobile App Link')); ?></h3>
                        </div>
                        <?php echo csrf_field(); ?>
                        <div class="card-body">
                            <label class="m-0"><?php echo e(__('Android Url')); ?></label>
                            <div class="input-group mb-3">
                                <input type="text" name="android_url" class="form-control" placeholder="<?php echo e(__('Android Url')); ?>"
                                    value="<?php echo e($appLink ? $appLink->android_url : ''); ?>">
                            </div>

                            <label class="m-0"><?php echo e(__('IOS Url')); ?></label>
                            <div class="input-group">
                                <input type="text" name="ios_url" class="form-control" placeholder="<?php echo e(__('IOS Url')); ?>"
                                    value="<?php echo e($appLink ? $appLink->ios_url : ''); ?>">
                            </div>
                        </div>
                        <div class="card-footer py-2">
                            <button type="submit" class="btn btn-primary px-4 <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__('Save And Update')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\mobileAppUrl\index.blade.php ENDPATH**/ ?>