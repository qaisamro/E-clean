<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">مفاتيح API</h2>
                <p class="text-muted mb-0">تُخزَّن المفاتيح كتجزئة SHA-256. المفتاح الكامل يظهر مرة واحدة فقط عند الإنشاء.</p>
            </div>
            <a href="<?php echo e(route('integrations.index')); ?>" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('api_key_plain')): ?>
            <div class="alert alert-warning">
                <strong>انسخ المفتاح الآن:</strong>
                <code class="d-block mt-2 text-break"><?php echo e(session('api_key_plain')); ?></code>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>إنشاء مفتاح</strong></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('integrations.api-keys.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">الاسم</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الصلاحية بالأيام</label>
                            <input name="expires_in_days" type="number" min="1" class="form-control" placeholder="بدون انتهاء">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Scopes</label>
                            <div class="row">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $scopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scope => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input type="checkbox" name="scopes[]" value="<?php echo e($scope); ?>" class="form-check-input">
                                            <span class="form-check-label"><?php echo e($label); ?> <code><?php echo e($scope); ?></code></span>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">إنشاء مفتاح</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>Prefix</th>
                            <th>Scopes</th>
                            <th>الحالة</th>
                            <th>آخر استخدام</th>
                            <th>ينتهي</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $keys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($key->id); ?></td>
                                <td><?php echo e($key->name); ?></td>
                                <td><code><?php echo e($key->key_prefix); ?>…</code></td>
                                <td class="small"><?php echo e(implode(', ', $key->scopes ?? [])); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key->isActive()): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">ملغى/منتهي</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="small"><?php echo e($key->last_used_at ?? '—'); ?></td>
                                <td class="small"><?php echo e($key->expires_at ?? '—'); ?></td>
                                <td class="text-nowrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key->isActive()): ?>
                                        <form method="POST" action="<?php echo e(route('integrations.api-keys.revoke', $key)); ?>" class="d-inline"
                                            onsubmit="return confirm('إلغاء المفتاح؟')">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-outline-warning btn-sm">إلغاء</button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('integrations.api-keys.destroy', $key)); ?>" class="d-inline"
                                        onsubmit="return confirm('حذف المفتاح نهائيًا؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-outline-danger btn-sm">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="8" class="text-center text-muted py-3">لا مفاتيح بعد.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body"><?php echo e($keys->links()); ?></div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\integrations\api-keys.blade.php ENDPATH**/ ?>