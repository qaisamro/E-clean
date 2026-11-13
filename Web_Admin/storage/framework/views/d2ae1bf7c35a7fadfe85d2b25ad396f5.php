<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="w-100">
                        <h2 class="float-left"><?php echo e(__('Customer').' '.__('Details')); ?></h2>
                        <div class="text-right">
                            <a class="btn btn-light" href="<?php echo e(url()->previous()); ?>"> <?php echo e(__('Back')); ?> </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__('Title')); ?></th>
                                    <th scope="col"><?php echo e(__('Details')); ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <td><?php echo e($customer->user->first_name ? $customer->user->name : 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('Profile_Photo')); ?></th>
                                <td>
                                    <img width="100" src="<?php echo e($customer->user->profilePhotoPath); ?>" alt="<?php echo e($customer->user->name); ?>">
                                </td>
                                </tr>
                                <tr>
                                    <th><?php echo e(__('Mobile')); ?></th>
                                    <td>
                                        <?php echo e($customer->user->mobile); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->user->mobile_verified_at): ?>
                                            <span class="bg-success btn py-0 px-1"><?php echo e(__('verified')); ?></span>
                                            <?php else: ?>
                                            <span class="bg-warning btn py-0 px-1"><?php echo e(__('Unverified')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>

                                

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$customer->addresses->isEmpty()): ?>
                                <tr>
                                    <th><?php echo e(__('Address')); ?></th>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customer->addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <?php echo $key == 0 ? ' <hr class="my-2">' : ''; ?>


                                            <span><?php echo e($address->address_line . ',' . $address->address_line2); ?></span>

                                            <a href="#address_show_<?php echo e($address->id); ?>" data-toggle="modal" class="btn btn-info p-1 px-2 ml-2">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            <hr class="my-2">
                                            <!-- Modal -->
                                            <div class="modal fade" id="address_show_<?php echo e($address->id); ?>">
                                                <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel"><?php echo e($address->address_line); ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="col"><?php echo e(__('Title')); ?></th>
                                                                    <th scope="col"><?php echo e(__('Details')); ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <td><?php echo e(__('Post_Code')); ?></td>
                                                                    <td><?php echo e($address->post_code); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?php echo e(__('Address_line')); ?> 1</td>
                                                                    <td><?php echo e($address->address_line); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?php echo e(__('Address_line')); ?> 2</td>
                                                                    <td><?php echo e($address->address_line2); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?php echo e(__('Delivery').' '.__('Note')); ?></td>
                                                                    <td><?php echo e($address->delivery_note); ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()?->hasRole('root') && !$customer->orders->isEmpty()): ?>
                                <tr>
                                    <th><?php echo e(__('Orders')); ?></th>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                                            <?php echo e(__('Show_all_Orders')); ?>

                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="staticBackdrop">
                                            <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel"><?php echo e(__('All') .' '.__('Orders')); ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customer->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="position-relative">
                                                        <?php echo $key == 0 ? ' <hr class="my-2">' : ''; ?>

                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->get('local') == 'ar'): ?>
                                                        <span> <?php echo e($order->products->count()); ?> :<?php echo e(__('Products')); ?></span>,
                                                        <span><?php echo e(Carbon\Carbon::parse($order->delivery_at)->format('M d, Y')); ?> :<?php echo e(__('Delivery_Date')); ?></span>,

                                                        <?php else: ?>
                                                        <span><?php echo e(__('Delivery_Date')); ?>: <?php echo e(Carbon\Carbon::parse($order->delivery_at)->format('M d, Y')); ?></span>,
                                                        <span><?php echo e(__('Products')); ?>: <?php echo e($order->products->count()); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-info p-1 px-2 ml-2 position-absolute" style="right:0; bottom:5px">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <hr class="my-2">
                                                    </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-dark" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\customers\show.blade.php ENDPATH**/ ?>