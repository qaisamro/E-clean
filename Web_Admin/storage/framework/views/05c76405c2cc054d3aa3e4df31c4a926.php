<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="m-0"> <?php echo e(__('Drivers List')); ?></h2>
                    <div>
                        <a href="<?php echo e(route('driver.create')); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> <?php echo e(__('Add Driver')); ?>

                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->deactive): ?>
                        <a href="<?php echo e(route('driver.index')); ?>" class="btn btn-info">
                            <?php echo e(__('Active Driver')); ?>

                        </a>
                        <?php elseif(!\App\Support\VendorAccount::isStoreStaff(auth()->user())): ?>
                        <a href="<?php echo e(route('driver.index','deactive=1')); ?>" class="btn btn-danger">
                            <?php echo e(__('Inactive Driver')); ?>

                        </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card border shadow-sm mb-4">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0"><?php echo e($driver->user->name); ?></h5>
                                <small class="text-muted"><?php echo e($driver->user->email); ?> &nbsp;|&nbsp; <?php echo e($driver->user->mobile ?? 'N/A'); ?> &nbsp;|&nbsp; <?php echo e($driver->user->created_at->format('d M, Y')); ?></small>
                            </div>
                            <div class="d-flex gap-1 align-items-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->deactive): ?>
                                <form action="<?php echo e(route('user.status.toggle', $driver->user->id)); ?>" method="POST" style="display:inline">
                                    <?php echo csrf_field(); ?>
                                    <label class="switch">
                                        <input <?php echo e($driver->user->is_active ? 'checked':''); ?> type="checkbox" onchange="this.form.submit()">
                                        <span class="slider round"></span>
                                    </label>
                                </form>
                                <?php else: ?>
                                <form action="<?php echo e(route('driver.status.toggle', $driver->id)); ?>" method="POST" style="display:inline">
                                    <?php echo csrf_field(); ?>
                                    <label class="switch">
                                        <input <?php echo e($driver->is_approve ? 'checked':''); ?> type="checkbox" onchange="this.form.submit()">
                                        <span class="slider round"></span>
                                    </label>
                                </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a href="<?php echo e(route('driver.details', $driver->id)); ?>" class="btn btn-primary btn-sm" title="<?php echo e(__('Show')); ?>">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('driver.edit', $driver->id)); ?>" class="btn btn-warning btn-sm" title="<?php echo e(__('Edit')); ?>">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('driver.destroy', $driver->id)); ?>" method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف السائق؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" title="<?php echo e(__('Delete')); ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary mb-3"><?php echo e(__('Assigned Orders')); ?></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-2"><?php echo e(__('Id')); ?></th>
                                            <th scope="col" class="px-2"><?php echo e(__('Order') . ' '.__('Date')); ?></th>
                                            <th scope="col" class="px-2"><?php echo e(__('Pickup') . ' '.__('Date')); ?></th>
                                            <th scope="col" class="px-2"><?php echo e(__('Delivery') . ' '.__('Date')); ?></th>
                                            <th scope="col" class="px-2"><?php echo e(__('Order') . ' '.__('Status')); ?></th>
                                            <th scope="col" class="px-2 text-center"><?php echo e(__('Amount')); ?></th>
                                            <th scope="col" class="px-2 text-center"><?php echo e(__('Assign')); ?></th>
                                            <th scope="col" class="px-2 text-center"><?php echo e(__('Action')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $driver->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <tr>
                                            <td class="px-2"><?php echo e($order->order_code); ?></td>
                                            <td class="px-2">
                                                <?php echo e($order->created_at->format('M d, Y')); ?><br>
                                                <small><?php echo e($order->created_at->format('h:i a')); ?></small>
                                            </td>
                                            <td class="px-2">
                                                <span style="font-size: 14px">
                                                    <?php echo e(Carbon\Carbon::parse($order->pick_date)->format('M d, Y')); ?><br>
                                                </span>
                                                <small>
                                                    <?php echo e($order->getTime(substr($order->pick_hour, 0, 2))); ?>

                                                </small>
                                            </td>
                                            <td class="px-2">
                                                <span style="font-size: 14px">
                                                    <?php echo e(Carbon\Carbon::parse($order->delivery_date)->format('M d, Y')); ?><br>
                                                </span>
                                                <small>
                                                    <?php echo e($order->getTime(substr($order->delivery_hour, 0, 2))); ?>

                                                </small>
                                            </td>
                                            <td class="px-2"><?php echo e($order->order_status); ?></td>
                                            <td class="px-2 text-center"><?php echo e($order->amount - $order->discount); ?></td>
                                            <td class="px-2 text-center text-capitalize"><?php echo e($order->pivot->status); ?></td>
                                            <td class="px-2 text-center">
                                                <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted"><?php echo e(__('No assigned orders yet')); ?></td>
                                        </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-4"><?php echo e(__('No drivers found')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\drivers\index.blade.php ENDPATH**/ ?>