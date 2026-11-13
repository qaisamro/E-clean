<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">قوالب الرسائل</h2>
                <p class="text-muted mb-0">قوالب SMS وEmail وWhatsApp وPush. المتغيرات المتاحة: code, name, minutes, app, time.</p>
            </div>
            <a href="<?php echo e(route('integrations.index')); ?>" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>إضافة قالب</strong></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('integrations.templates.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">القناة</label>
                            <select name="channel" class="form-select" required>
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="push">Push</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">المفتاح</label>
                            <input name="key" class="form-control" placeholder="otp_registration" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الاسم</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">اللغة</label>
                            <input name="language" class="form-control" value="ar" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">اسم قالب المزود (WhatsApp)</label>
                            <input name="provider_template_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الموضوع (Email)</label>
                            <input name="subject" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المتغيرات (مفصولة بفاصلة)</label>
                            <input name="variables" class="form-control" placeholder="code,name,minutes">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">النص</label>
                            <textarea name="body" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-check">
                                <input type="checkbox" name="active" value="1" class="form-check-input" checked>
                                <span class="form-check-label">مفعل</span>
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">إضافة</button>
                </form>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $templates->groupBy('channel'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header"><strong><?php echo e(strtoupper($channel)); ?></strong></div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <form method="POST" action="<?php echo e(route('integrations.templates.update', $template)); ?>"
                            class="border-bottom pb-3 mb-3">
                            <?php echo csrf_field(); ?>
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <input name="key" class="form-control form-control-sm" value="<?php echo e($template->key); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <input name="name" class="form-control form-control-sm" value="<?php echo e($template->name); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <input name="language" class="form-control form-control-sm" value="<?php echo e($template->language); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <input name="provider_template_name" class="form-control form-control-sm"
                                        value="<?php echo e($template->provider_template_name); ?>" placeholder="Provider template name">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-check">
                                        <input type="checkbox" name="active" value="1" class="form-check-input" <?php echo e($template->active ? 'checked' : ''); ?>>
                                        <span class="form-check-label">مفعل</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input name="subject" class="form-control form-control-sm" value="<?php echo e($template->subject); ?>"
                                        placeholder="Subject (Email)">
                                </div>
                                <div class="col-md-6">
                                    <input name="variables" class="form-control form-control-sm"
                                        value="<?php echo e(implode(',', $template->variables ?? [])); ?>" placeholder="code,name">
                                </div>
                                <div class="col-md-12">
                                    <textarea name="body" class="form-control form-control-sm" rows="2" required><?php echo e($template->body); ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <input type="hidden" name="channel" value="<?php echo e($template->channel); ?>">
                                    <button class="btn btn-primary btn-sm">تحديث</button>
                                </div>
                            </div>
                        </form>
                        <form method="POST" action="<?php echo e(route('integrations.templates.destroy', $template)); ?>"
                            onsubmit="return confirm('حذف القالب؟')" class="mb-3">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-outline-danger btn-sm">حذف القالب</button>
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\integrations\templates.blade.php ENDPATH**/ ?>