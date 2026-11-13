<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">

                <div class="p-2">
                    <h3 class="card-title m-0"><?php echo e(__('Notification_Management')); ?></h3>
                </div>

                <div class="row mt-3">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notificationManage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-4 mb-4">
                            <form action="<?php echo e(route('notification.manage.update', $notification->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="card nofication">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title mb-0"><?php echo e($notification->title); ?></h4>

                                        <label class="switch mb-0">
                                            <input type="checkbox" name="status"
                                                <?php echo e($notification->is_active ? 'checked' : ''); ?> />
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="name" value="<?php echo e($notification->name); ?>">

                                        <label class="mb-1"><?php echo e(__('Message')); ?></label>
                                        <textarea name="message" class="form-control" rows="3" placeholder="<?php echo e(__('Message')); ?>"><?php echo e($notification->message); ?></textarea>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary"><?php echo e(__('Update')); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>

            </div>
        </div>
    </div>

    <style>
        .nofication .switch {
            width: 50px;
            height: 24px;
        }

        .nofication .slider:before {
            height: 18px;
            width: 18px;
            bottom: 3px;
        }

        .nofication input:checked+.slider:before {
            -webkit-transform: translateX(24px);
            -ms-transform: translateX(24px);
            transform: translateX(24px);
        }

        .nofication {
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        .nofication .card-header {
            background: #e2e8f0;
            padding: 12px 24px;
        }

        .nofication .card-body {
            padding: 12px 24px;
        }

        .nofication .card-body label {
            font-weight: 600;
            font-size: 14px;
        }

        .nofication .card-footer {
            background: #f8fafc;
            padding: 8px 24px;
            border-color: #f1f5f9
        }

        .nofication .card-footer .btn {
            padding: 6px 16px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\notifications\manage.blade.php ENDPATH**/ ?>