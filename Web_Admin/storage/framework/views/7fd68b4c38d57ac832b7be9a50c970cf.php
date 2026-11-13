<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="m-0">طلباتي</h2>
                    <div class="d-flex align-items-center">
                        <img src="<?php echo e($driver->user->profile_photo_path ?? asset('website/assets/images/common/user.png?v=2')); ?>"
                            alt="" width="45" height="45" class="rounded-circle mr-2">
                        <div>
                            <h5 class="m-0"><?php echo e($driver->user->name); ?></h5>
                            <small class="text-muted"><?php echo e($driver->user->email ?? ''); ?></small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="text-primary mb-3">الطلبات المعيّنة لك</h5>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $quantity = 0;
                            foreach ($order->products as $product) {
                                $quantity += $product->pivot->quantity;
                            }
                            $productNames = $order->products->pluck('name')->implode('، ');
                            $addr = $order->address ? trim(implode('، ', array_filter([
                                $order->address->area,
                                $order->address->address_name,
                                $order->address->house_no ? 'منزل ' . $order->address->house_no : '',
                                $order->address->flat_no ? 'شقة ' . $order->address->flat_no : '',
                                $order->address->block ? 'قطعة ' . $order->address->block : '',
                                $order->address->road_no ? 'طريق ' . $order->address->road_no : '',
                            ]))) : '';
                        ?>
                        <div class="card border shadow-sm mb-4">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                                <div class="d-flex flex-wrap align-items-center">
                                    <h5 class="m-0"><?php echo e($order->order_code); ?></h5>
                                    <span class="badge badge-info mr-2">
                                        <?php echo e($order->pivot->status == 'pick-up' ? 'استلام' : 'تسليم'); ?>

                                    </span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap" style="position: relative; z-index: 1060; gap:8px;">
                                    <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-info"
                                       style="border-radius:30px; font-weight:600; min-width:150px; padding:8px 16px;"
                                       title="عرض تفاصيل الطلب دون تعديل" aria-label="عرض تفاصيل الطلب">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-info dropdown-toggle" style="min-width:165px; border-radius:30px; font-weight:600;"
                                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo e(__($order->order_status)); ?>

                                        </button>
                                        <div class="dropdown-menu" style="min-width:210px; border-radius:14px; border:1px solid rgba(13,27,62,.08); box-shadow:0 12px 32px rgba(15,23,42,.18); padding:6px; max-height:340px; overflow:auto;">
                                            <?php
                                                // Drivers may only use these three statuses:
                                                $driverAllowedStatuses = ['picked_order', 'on_the_way', 'delivered'];
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('enums.order_status'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order_status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(!in_array($key, $driverAllowedStatuses, true)) continue; ?>
                                                <button class="dropdown-item driver-status-change <?php echo e($order->order_status == $order_status ? 'active' : ''); ?>"
                                                    style="border-radius:10px; font-weight:600; font-size:13px;"
                                                    data-order="<?php echo e($order->id); ?>" data-status="<?php echo e($key); ?>" type="button">
                                                    <?php echo e(__($order_status)); ?>

                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card h-100" style="border-radius:14px;">
                                            <div class="card-header py-2" style="background:#f8fafc; border-radius:14px 14px 0 0;">
                                                <h6 class="m-0 font-weight-bold">تفاصيل الطلب</h6>
                                            </div>
                                            <div class="card-body pt-2">
                                                <table class="table table-bordered table-striped mb-0">
                                                    <tr>
                                                        <th class="py-2"><?php echo e(__('Order') . ' ' . __('Id')); ?></th>
                                                        <td class="py-2"><?php echo e($order->order_code); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تاريخ الطلب</th>
                                                        <td class="py-2"><?php echo e(Carbon\Carbon::parse($order->created_at)->format('d M, Y')); ?> <small>(<?php echo e($order->created_at->format('h:i a')); ?>)</small></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تاريخ الاستلام</th>
                                                        <td class="py-2"><?php echo e(Carbon\Carbon::parse($order->pick_date)->format('d M, Y')); ?> <span class="badge badge-light"><?php echo e($order->getTime(substr($order->pick_hour, 0, 2))); ?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تاريخ التسليم</th>
                                                        <td class="py-2"><?php echo e(Carbon\Carbon::parse($order->delivery_date)->format('d M, Y')); ?> <span class="badge badge-light"><?php echo e($order->getTime(substr($order->delivery_hour, 0, 2))); ?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">حالة الطلب</th>
                                                        <td class="py-2"><?php echo e(__($order->order_status)); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">النوع</th>
                                                        <td class="py-2 text-capitalize"><?php echo e($order->pivot->status == 'pick-up' ? 'استلام' : 'تسليم'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">حالة الدفع</th>
                                                        <td class="py-2"><?php echo e(__($order->payment_status)); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">طريقة الدفع</th>
                                                        <td class="py-2"><?php echo e($order->payment_method_label ?? 'N/A'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">عدد القطع</th>
                                                        <td class="py-2"><?php echo e($quantity); ?> <?php echo e(__('Pieces')); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">الإجمالي</th>
                                                        <td class="py-2"><?php echo e(currencyPosition($order->total_amount ?? ($order->amount - $order->discount))); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">المنتجات</th>
                                                        <td class="py-2"><?php echo e($productNames ?: 'N/A'); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100" style="border-radius:14px;">
                                            <div class="card-header py-2" style="background:#f8fafc; border-radius:14px 14px 0 0;">
                                                <h6 class="m-0 font-weight-bold">بيانات العميل</h6>
                                            </div>
                                            <div class="card-body pt-2">
                                                <table class="table table-bordered table-striped mb-0">
                                                    <tr>
                                                        <th class="py-2">اسم العميل</th>
                                                        <td class="py-2"><?php echo e($order->customer?->user?->name ?? 'N/A'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">البريد الإلكتروني</th>
                                                        <td class="py-2"><?php echo e($order->customer?->user?->email ?? 'N/A'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">رقم الهاتف</th>
                                                        <td class="py-2" dir="ltr"><?php echo e($order->customer?->user?->mobile ?? 'N/A'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">العنوان</th>
                                                        <td class="py-2">
                                                            <span><?php echo e($addr ?: 'لا يوجد عنوان'); ?></span>
                                                            <?php
                                                                $mapUrl = \App\Support\MapLink::addressUrl($order->address);
                                                            ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mapUrl): ?>
                                                                <a href="<?php echo e($mapUrl); ?>" target="_blank" rel="noopener"
                                                                    class="btn btn-sm d-inline-flex align-items-center gap-1 ms-2"
                                                                    style="background:#028090 !important; color:#fff !important; border:1px solid #028090; border-radius:16px; font-weight:700; padding:4px 14px;">
                                                                    <i class="fas fa-map-marker-alt"></i> الموقع
                                                                </a>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تعليمات إضافية</th>
                                                        <td class="py-2"><?php echo e($order->instruction ?? 'لا توجد'); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted py-4">لا توجد طلبات معيّنة لك حالياً</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <hr>

                    <h5 class="mb-3 font-weight-bold" style="color:#000;">سجل الطلبات المكتملة</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th class="px-2">رقم الطلب</th>
                                    <th class="px-2">تاريخ الطلب</th>
                                    <th class="px-2">حالة الطلب</th>
                                    <th class="px-2 text-center">النوع</th>
                                    <th class="px-2 text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-2"><?php echo e($order->order_code); ?></td>
                                    <td class="px-2"><?php echo e($order->created_at->format('M d, Y')); ?></td>
                                    <td class="px-2"><?php echo e(__($order->order_status)); ?></td>
                                    <td class="px-2 text-center text-capitalize"><?php echo e($order->pivot->status == 'pick-up' ? 'استلام' : 'تسليم'); ?></td>
                                    <td class="px-2 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <?php
                                                $mapUrl = \App\Support\MapLink::addressUrl($order->address);
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mapUrl): ?>
                                                <a href="<?php echo e($mapUrl); ?>" target="_blank" rel="noopener"
                                                    class="btn btn-sm"
                                                    style="background:#028090 !important; color:#fff !important; border:1px solid #028090; border-radius:20px; font-weight:700; padding:4px 14px;">
                                                    <i class="fas fa-map-marker-alt"></i> الموقع
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-info btn-sm" style="border-radius:20px; font-weight:600; padding:4px 14px;" title="عرض تفاصيل الطلب دون تعديل" aria-label="عرض تفاصيل الطلب">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">لا يوجد سجل بعد</td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        $(document).on('click', '.driver-status-change', function(e) {
            e.preventDefault();
            const order = $(this).attr('data-order');
            const status = $(this).attr('data-status');
            const url = "<?php echo e(route('driver.order.status.change', 'ORDER_ID')); ?>".replace('ORDER_ID', order);

            $.ajax({
                url: url,
                method: 'POST',
                data: { status: status, _token: "<?php echo e(csrf_token()); ?>" },
                success: function(response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    setTimeout(() => location.reload(), 800);
                },
                error: function(error) {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON?.message || 'فشل تحديث الحالة',
                    });
                },
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\drivers\account.blade.php ENDPATH**/ ?>