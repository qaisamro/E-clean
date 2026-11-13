<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 mb-3 shadow rounded-12">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h2 class="card-title m-0"><?php echo e(__('Orders')); ?></h2>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'store|root|admin')): ?>
                                <form class="d-flex justify-content-end" action="<?php echo e(route('order.index')); ?>" method="GET">
                                    <select class="form-control mr-1" name="sort" style="width: 150px;height: 43px;">
                                        <option value="newest" <?php echo e(request('sort') == 'oldest' ? '' : 'selected'); ?>>
                                            <?php echo e(__('Newest') . ' ' . __('First')); ?>

                                        </option>
                                        <option value="oldest" <?php echo e(request('sort') == 'oldest' ? 'selected' : ''); ?>>
                                            <?php echo e(__('Oldest') . ' ' . __('First')); ?>

                                        </option>
                                    </select>
                                    <select class="form-control mr-1" name="status" style="width: 200px;height: 43px;">
                                        <option value=""><?php echo e(__('All')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orderStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($status->value); ?>"
                                                <?php echo e($status->value == \request('status') ? 'selected' : ''); ?>><?php echo e(__($status->value)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>

                                    <button class="btn btn-primary mx-md-0 mx-2"> <i  class="fa fa-search"></i> </button>
                                </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="myTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col"><?php echo e(__('Order_ID')); ?></th>
                                        <th scope="col"><?php echo e(__('Order_By')); ?></th>
                                        <th scope="col"><?php echo e(__('Pickup_Date')); ?></th>
                                        <th scope="col"><?php echo e(__('Amount')); ?></th>
                                        <th scope="col"><?php echo e(__('Order_Status')); ?></th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['order.show', 'order.print.invioce'])): ?>
                                            <th scope="col" class="px-2"><?php echo e(__('Actions')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($order->order_code); ?></td>
                                            <td>
                                                <?php echo e($order->customer->user?->name ?? 'N/A'); ?></td>
                                            <td>
                                                <?php echo e($order->pick_date); ?>

                                            </td>
                                            <td><?php echo e(currencyPosition($order->total_amount)); ?></td>
                                            <td><?php echo e(($order->order_status)); ?></td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['order.show', 'order.print.invioce'])): ?>
                                                <td class="p-1 ">

                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order.show')): ?>
                                                        <a href="<?php echo e(route('order.show', $order->id)); ?>"
                                                            class="btn btn-primary btn-sm mb-1">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order.edit')): ?>
                                                        <a href="<?php echo e(route('order.edit', $order->id)); ?>"
                                                            class="btn btn-warning btn-sm mb-1">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order.print.invioce')): ?>
                                                        <a class="btn btn-danger btn-sm mb-1 print-same-page"
                                                            href="<?php echo e(route('order.print.invioce', $order->id)); ?>"><i class="fas fa-print"></i>
                                                        </a>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\pos\sales.blade.php ENDPATH**/ ?>