

<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                        <h2 class="card-title m-0">
                            <?php echo e(__(config('enums.order_status.' . request('status')))); ?>

                            <?php echo e(__('Orders')); ?>

                        </h2>
                        <div class="d-flex justify-content-end">
                            <form class="" action="<?php echo e(route('order.index')); ?>" method="GET">

                                <select class="form-control mx-2 float-left" name="sort" style="width: 160px">
                                    <option value="newest"
                                        <?php echo e(request('sort') == 'oldest' ? '' : 'selected'); ?>>
                                        <?php echo e(__('Newest') . ' ' . __('First')); ?>

                                    </option>
                                    <option value="oldest" <?php echo e(request('sort') == 'oldest' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Oldest') . ' ' . __('First')); ?>

                                    </option>
                                </select>

                                <select class="form-control mx-2 float-left" name="status" style="width: 200px">
                                    <option value="">
                                        <?php echo e(in_array(config('enums.order_status.' . \request('status')), config('enums.order_status')) ? __(config('enums.order_status.' . request('status'))) : __('All')); ?>

                                    </option>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('enums.order_status'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order_status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"

                                            <?php echo e($key == \request('status') ? 'selected' : ''); ?>><?php echo e(__($order_status)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>

                                <button class="btn btn-primary mx-md-0 mx-2"> <i
                                        class="fa fa-search"></i> </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered <?php echo e(session()->get('local')); ?>" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo e(__('Order') . ' ' . __('Id')); ?> </th>
                                        <th scope="col"><?php echo e(__('Order') . ' ' . __('By')); ?></th>
                                        <th scope="col"><?php echo e(__('Order') . ' ' . __('At')); ?></th>
                                        <th scope="col"><?php echo e(__('Pickup') . ' ' . __('Date')); ?></th>
                                        <th scope="col"><?php echo e(__('Delivery') . ' ' . __('Date')); ?></th>
                                        <th scope="col"><?php echo e(__('Amount')); ?></th>
                                        <th scope="col"><?php echo e(__('Order') . ' ' . __('Status')); ?></th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['order.show', 'order.print.invioce'])): ?>
                                            <th scope="col" class="px-2"><?php echo e(__('Actions')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="<?php echo e($order->is_show ? '' : 'bg-color'); ?>">
                                            <td class="py-1"><?php echo e($order->order_code); ?></td>
                                            <td class="py-1">
                                                <?php echo e($order->customer->user ? $order->customer->user->name : 'N/A'); ?>

                                            </td>
                                            <td class="py-1">
                                                <?php echo e($order->created_at->format('M d, Y')); ?>

                                            </td>

                                            <td class="py-1">
                                                <?php echo e(\Carbon\Carbon::parse($order->pick_date)->format('M d, Y')); ?>

                                            </td>
                                            <td class="py-1">
                                                <?php echo e(\Carbon\Carbon::parse($order->delivery_date)->format('M d, Y')); ?>

                                            </td>
                                            <td class="py-1"><?php echo e(currencyPosition($order->total_amount)); ?></td>
                                            <td class="py-1"><?php echo e(__($order->order_status)); ?></td>
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
                                                            href="<?php echo e(route('order.print.invioce', $order->id)); ?>"><i class="fas fa-print"></i> </a>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\orders\index.blade.php ENDPATH**/ ?>