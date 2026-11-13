<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                   <div class="row">
                        <div class="col-6">
                            <h2 class="card-title"><i class="fa fa-tags text-primary"></i> كوبونات متجري</h2>
                            <small class="text-muted">هذه الكوبونات خاصة بمتجرك وتُطبَّق على منتجاتك فقط.</small>
                        </div>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('coupon.create')): ?>
                        <div class="col-6 position-relative" >
                            <div class="position-absolute" style="right: 1em" >
                                <a href="<?php echo e(route('coupon.create')); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo e(__('Create'). ' '.__('Coupon')); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                   </div>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-dismiss="alert"></button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php $cn = session('coupon_notification'); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($cn)): ?>
                        <?php
                            $cnClass = 'alert-success';
                            $cnIcon = 'fa-check-circle';
                            if (($cn['status'] ?? null) === 'no_recipients') {
                                $cnClass = 'alert-warning';
                                $cnIcon = 'fa-exclamation-triangle';
                            } elseif ((int) ($cn['sent'] ?? 0) === 0) {
                                $cnClass = 'alert-danger';
                                $cnIcon = 'fa-times-circle';
                            }
                            $cnText = match ($cn['status'] ?? null) {
                                'sent' => 'تم إرسال إشعار الكوبون إلى ' . (int) ($cn['sent'] ?? 0) . ' من ' . (int) ($cn['recipients'] ?? 0) . ' جهاز.',
                                'no_recipients' => 'لم يتم إرسال إشعار: لا يوجد أي جهاز مسجّل.',
                                'skipped' => 'لم يتم إرسال إشعار: ' . ($cn['message'] ?? 'الخدمة غير مفعّلة.'),
                                'not_configured' => 'لم يتم إرسال إشعار: خدمة الإشعارات (Firebase) غير مهيّأة.',
                                'error' => 'تعذّر إرسال الإشعار: ' . ($cn['message'] ?? 'خطأ غير معروف.'),
                                default => 'تم إرسال ' . (int) ($cn['sent'] ?? 0) . ' إشعار من أصل ' . (int) ($cn['recipients'] ?? 0) . ' جهاز.',
                            };
                            if (isset($cn['in_app_inserted'])) {
                                $cnText .= ' — وتم تحديث قائمة الإشعارات لدى ' . (int) $cn['in_app_inserted'] . ' عميلاً داخل التطبيق.';
                            }
                        ?>
                        <div class="alert <?php echo e($cnClass); ?> alert-dismissible fade show">
                            <i class="fa <?php echo e($cnIcon); ?>"></i> <?php echo e($cnText); ?>

                            <button type="button" class="btn-close" data-dismiss="alert"></button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm <?php echo e(session()->get('local')); ?>" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__('Code')); ?></th>
                                    <th scope="col"><?php echo e(__('Discount_Type')); ?> </th>
                                    <th scope="col"><?php echo e(__('Discount')); ?></th>
                                    <th scope="col"><?php echo e(__('Min_Amount')); ?></th>
                                    <th scope="col"><?php echo e(__('Started_at')); ?></th>
                                    <th scope="col"><?php echo e(__('Expired_at')); ?></th>
                                    <th scope="col">النطاق</th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['coupon.edit', 'coupon.destroy'])): ?>
                                    <th scope="col"><?php echo e(__('Action')); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($coupon->code); ?></td>
                                    <td><?php echo e(__($coupon->discount_type)); ?></td>
                                    <td><?php echo $coupon->discount_type == 'amount' ? currencyPosition($coupon->discount) : $coupon->discount.'%'; ?></td>
                                    <td><?php echo e(currencyPosition($coupon->min_amount)); ?></td>
                                    <td><?php echo e(Carbon\Carbon::parse($coupon->started_at)->format('M d, Y h:i a')); ?></td>
                                    <td><?php echo e(Carbon\Carbon::parse($coupon->expired_at)->format('M d, Y h:i a')); ?></td>
                                    <td><span class="badge bg-secondary">متجري فقط</span></td>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['coupon.edit', 'coupon.destroy'])): ?>
                                    <td>
                                        <a href="<?php echo e(route('coupon.edit', $coupon->id)); ?>" class="btn btn-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('coupon.destroy')): ?>
                                        <form action="<?php echo e(route('coupon.destroy', $coupon->id)); ?>" method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\coupon\index.blade.php ENDPATH**/ ?>