<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">Webhooks</h2>
                <p class="text-muted mb-0">توقيع HMAC-SHA256، Idempotency-Key، إعادة محاولة تلقائية وسجل تسليمات.</p>
            </div>
            <a href="<?php echo e(route('integrations.index')); ?>" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('webhook_secret')): ?>
            <div class="alert alert-warning">
                <strong>Secret (يظهر مرة واحدة):</strong>
                <code class="d-block mt-2 text-break"><?php echo e(session('webhook_secret')); ?></code>
                استخدمه للتحقق من التوقيع: <code>X-EC-Signature: t=timestamp,v1=hmac</code>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>إضافة Endpoint</strong></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('integrations.webhooks.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">الاسم</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">URL</label>
                            <input name="url" type="url" class="form-control" placeholder="https://example.com/webhook" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الأحداث</label>
                            <div style="max-height: 130px; overflow:auto;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="form-check">
                                        <input type="checkbox" name="events[]" value="<?php echo e($event); ?>" class="form-check-input">
                                        <span class="form-check-label"><?php echo e($label); ?> <code><?php echo e($event); ?></code></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">الوصف</label>
                            <input name="description" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">إنشاء</button>
                </form>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $endpoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $endpoint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><?php echo e($endpoint->name); ?></strong>
                    <span class="badge bg-<?php echo e($endpoint->active ? 'success' : 'secondary'); ?>">
                        <?php echo e($endpoint->active ? 'نشط' : 'معطل'); ?>

                    </span>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('integrations.webhooks.update', $endpoint)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">الاسم</label>
                                <input name="name" class="form-control form-control-sm" value="<?php echo e($endpoint->name); ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">URL</label>
                                <input name="url" class="form-control form-control-sm" value="<?php echo e($endpoint->url); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الأحداث</label>
                                <div style="max-height: 110px; overflow:auto;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="form-check">
                                            <input type="checkbox" name="events[]" value="<?php echo e($event); ?>" class="form-check-input"
                                                <?php echo e(in_array($event, $endpoint->events ?? [], true) ? 'checked' : ''); ?>>
                                            <span class="form-check-label"><?php echo e($event); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">الوصف</label>
                                <input name="description" class="form-control form-control-sm" value="<?php echo e($endpoint->description); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-check mt-4">
                                    <input type="checkbox" name="active" value="1" class="form-check-input" <?php echo e($endpoint->active ? 'checked' : ''); ?>>
                                    <span class="form-check-label">نشط</span>
                                </label>
                            </div>
                            <div class="col-md-2 mt-4">
                                <button class="btn btn-primary btn-sm">تحديث</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-3 d-flex gap-2">
                        <form method="POST" action="<?php echo e(route('integrations.webhooks.test', $endpoint)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-outline-primary btn-sm">إرسال اختبار</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('integrations.webhooks.destroy', $endpoint)); ?>"
                            onsubmit="return confirm('حذف Endpoint وكل تسليماته؟')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-outline-danger btn-sm">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header"><strong>سجل التسليمات</strong></div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Endpoint</th>
                            <th>الحدث</th>
                            <th>الحالة</th>
                            <th>HTTP</th>
                            <th>المحاولات</th>
                            <th>التالي</th>
                            <th>الخطأ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($delivery->id); ?></td>
                                <td><?php echo e($delivery->endpoint?->name); ?></td>
                                <td><?php echo e($delivery->event); ?></td>
                                <td><?php echo e($delivery->status); ?></td>
                                <td><?php echo e($delivery->response_code ?? '—'); ?></td>
                                <td><?php echo e($delivery->attempts); ?></td>
                                <td class="small"><?php echo e($delivery->next_retry_at ?? '—'); ?></td>
                                <td class="small text-danger"><?php echo e($delivery->error); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delivery->status === 'failed'): ?>
                                        <form method="POST" action="<?php echo e(route('integrations.webhooks.retry', $delivery)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-outline-warning btn-sm">إعادة</button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="9" class="text-center text-muted py-3">لا تسليمات بعد.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body"><?php echo e($deliveries->links()); ?></div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\integrations\webhooks.blade.php ENDPATH**/ ?>