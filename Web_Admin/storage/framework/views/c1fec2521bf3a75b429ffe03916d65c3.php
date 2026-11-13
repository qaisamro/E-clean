<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row py-4">
            <div class="col-md-10 col-lg-9 col-sm-12 m-auto">
                <form action="<?php echo e(route('fcm.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card shadow rounded-12 border-0">
                        <div class="card-header bg-primary py-3">
                            <h3 class="text-white m-0"><?php echo e(__('Firebase_Cloud_messaging')); ?></h3>
                        </div>
                        <div class="card-body pb-3">
                            <div class="mb-2">
                                <label class="mb-1 text-dark"><?php echo e(__('FCM_Server_Key')); ?><span
                                        class="text-danger">*</span></label>
                                <textarea name="fcm_key" class="form-control" rows="4"
                                    placeholder=" <?php echo e(__('Firebase_cloud_messaging_server_key')); ?>" id="content"><?php echo e(config('app.fcm_server_key')); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fcm_key'];
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
                                <button class="btn btn-primary"><?php echo e(__('Save And Update')); ?></button>
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
        $('textarea').on('keyup', function() {
            $(this).val($(this).val().replace(/\s/g, ''));
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\fcm.blade.php ENDPATH**/ ?>