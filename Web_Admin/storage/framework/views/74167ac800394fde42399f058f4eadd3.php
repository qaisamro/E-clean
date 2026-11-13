<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">سجلات إرسال الرسائل</h2>
                <p class="text-muted mb-0">لا تُسجَّل رموز OTP أو محتوى الرسائل أو الأسرار. المستلم يظهر مخفيًا.</p>
            </div>
            <a href="<?php echo e(route('integrations.index')); ?>" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="channel" class="form-select">
                    <option value="">كل القنوات</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['sms', 'email', 'whatsapp', 'push']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($channel); ?>" <?php echo e(request('channel') === $channel ? 'selected' : ''); ?>><?php echo e($channel); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['sent', 'failed', 'not_configured', 'logged', 'pending', 'delivered', 'read']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') === $status ? 'selected' : ''); ?>><?php echo e($status); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary">تصفية</button></div>
        </form>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>القناة</th>
                            <th>المزود</th>
                            <th>القالب</th>
                            <th>المستلم</th>
                            <th>الحالة</th>
                            <th>خطأ</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($log->id); ?></td>
                                <td><?php echo e($log->channel); ?></td>
                                <td><?php echo e($log->provider); ?></td>
                                <td><?php echo e($log->template_key); ?></td>
                                <td><?php echo e($log->recipient_masked); ?></td>
                                <td><?php echo e($log->status); ?></td>
                                <td class="small text-danger"><?php echo e($log->error_message); ?></td>
                                <td class="small"><?php echo e($log->created_at); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="8" class="text-center text-muted py-3">لا سجلات.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body"><?php echo e($logs->links()); ?></div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\integrations\message-logs.blade.php ENDPATH**/ ?>