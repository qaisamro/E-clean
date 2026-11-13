<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-lg-12 m-auto">
            <div class="card">
                <div class="card-header py-2 bg-primary">
                    <h3 class="card-title m-0 text-white"><?php echo e(__($setting->title)); ?></h3>
                </div>
                <div class="card-body">
                    <?php echo e($setting->content); ?>

                   <div class="mt-4 text-right">
                        <a href="<?php echo e(route('setting.edit', $setting->slug)); ?>" class="btn btn-primary"><?php echo e(__('Edit')); ?></a>
                   </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\settings\index.blade.php ENDPATH**/ ?>