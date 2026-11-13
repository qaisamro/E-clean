<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <?php echo e(__('Contacts') . ' '.__('List')); ?>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__('No')); ?></th>
                                    <th scope="col"><?php echo e(__('Name')); ?></th>
                                    <th scope="col"><?php echo e(__('Photo')); ?></th>
                                    <th scope="col"><?php echo e(__('Message')); ?></th>
                                    <th scope="col"><?php echo e(__('Email')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i =1;
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($i++); ?></td>
                                    <td><?php echo e($contact->name); ?></td>
                                    <td><?php echo e($contact->phone_number); ?></td>
                                    <td><?php echo e($contact->message); ?></td>
                                    <td><?php echo e($contact->email); ?></td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\contacts\index.blade.php ENDPATH**/ ?>