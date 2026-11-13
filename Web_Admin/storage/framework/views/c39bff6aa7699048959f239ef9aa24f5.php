<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h2 class="m-0"> <?php echo e(__('Drivers') . ' '.__('Details')); ?></h2>

                    <a class="btn btn-light" href="<?php echo e(url()->previous()); ?>"> <?php echo e(__('Back')); ?> </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                            <tbody>
                                <tr>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <td><?php echo e($driver->user->name); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('Driver') . ' '.__('Photo')); ?></th>
                                    <td>
                                        <img style="max-width: 100px" src="<?php echo e($driver->user->profile_photo_path); ?>" alt="">
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('join_date')); ?></th>
                                    <td><strong><?php echo e($driver->user->created_at->format('d F, Y')); ?></strong></td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <td>
                                        <form action="<?php echo e(route('driver.status.toggle', $driver->id)); ?>" method="POST" style="display:inline">
                                            <?php echo csrf_field(); ?>
                                            <label class="switch">
                                                <input <?php echo e($driver->is_approve ? 'checked':''); ?> type="checkbox" onchange="this.form.submit()">
                                                <span class="slider round"></span>
                                            </label>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('Email')); ?></th>
                                    <td><?php echo e($driver->user->email); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('Phone_number')); ?></th>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($driver->user->mobile): ?>
                                            <?php echo e($driver->user->mobile); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php echo e(__('Date_of_Birth')); ?></th>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($driver->user->date_of_birth): ?>
                                            <?php echo e(Carbon\Carbon::parse($driver->user->date_of_birth)->format('d F, Y')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm" id="myTable">
                            <thead>
                                <tr>
                                    <th class="px-2"><?php echo e(__('Id')); ?></th>
                                    <th scope="col" class="px-2"><?php echo e(__('Order') . ' '.__('Date')); ?></th>
                                    <th scope="col" class="px-2"><?php echo e(__('Pickup') . ' '.__('Date')); ?></th>
                                    <th scope="col" class="px-2"><?php echo e(__('Delivery') . ' '.__('Status')); ?></th>
                                    <th scope="col" class="px-2"><?php echo e(__('Order') . ' '.__('Status')); ?></th>
                                    <th scope="col" class="px-2 text-center"><?php echo e(__('Amount')); ?></th>
                                    <th scope="col" class="px-2 text-center"><?php echo e(__('Assign')); ?></th>
                                    <th scope="col" class="px-2 text-center"><?php echo e(__('Accepted')); ?></th>
                                    <th scope="col" class="px-2 text-center"><?php echo e(__('Action')); ?></th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $driver->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-2">
                                            <?php echo e($order->order_code); ?>

                                        </td>
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
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->pivot->is_accept): ?>
                                                <span class="text-success"><?php echo e(__('Accepted')); ?></span>
                                            <?php else: ?>
                                                <span class="text-danger"><?php echo e(__('Pending')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-2 text-center">
                                            <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $driver->orderHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-2">
                                            <?php echo e($order->order_code); ?>

                                        </td>
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
                                            <span class="text-success"><?php echo e(__('Accepted')); ?></span>
                                        </td>
                                        <td class="px-2 text-center">
                                            <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\drivers\show.blade.php ENDPATH**/ ?>