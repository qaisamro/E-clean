<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">سجل التدقيق — Audit Logs</h2>
                <p class="text-muted mb-0">كل تغيير حساس مع إخفاء الأسرار ورموز OTP تلقائيًا.</p>
            </div>
            <a href="<?php echo e(route('integrations.index')); ?>" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input name="search" class="form-control" placeholder="بحث في المفتاح/الإجراء" value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="group" class="form-select">
                    <option value="">كل المجموعات</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group); ?>" <?php echo e(request('group') === $group ? 'selected' : ''); ?>><?php echo e($group); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">تصفية</button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الإجراء</th>
                            <th>المجموعة</th>
                            <th>المفتاح</th>
                            <th>القيمة القديمة</th>
                            <th>القيمة الجديدة</th>
                            <th>IP</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($log->id); ?></td>
                                <td><?php echo e($log->user?->name ?? $log->user?->email ?? '—'); ?></td>
                                <td><?php echo e($log->action); ?></td>
                                <td><?php echo e($log->group_name); ?></td>
                                <td><code><?php echo e($log->setting_key); ?></code></td>
                                <td class="small text-muted"><?php echo e(\Illuminate\Support\Str::limit($log->old_value, 80)); ?></td>
                                <td class="small text-muted"><?php echo e(\Illuminate\Support\Str::limit($log->new_value, 80)); ?></td>
                                <td class="small"><?php echo e($log->ip_address); ?></td>
                                <td class="small"><?php echo e($log->created_at); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="9" class="text-center text-muted py-3">لا سجلات.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body"><?php echo e($logs->links()); ?></div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\integrations\audit-logs.blade.php ENDPATH**/ ?>