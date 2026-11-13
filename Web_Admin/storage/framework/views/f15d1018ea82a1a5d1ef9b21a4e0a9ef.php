<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <?php
        $websetting = App\Models\WebSetting::first();
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title float-left"><?php echo e(__('All').' '.__('Additional_Service')); ?></h2>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('additional.create')): ?>
                    <div class="w-100 text-right">
                        <a href="<?php echo e(route('additional.create')); ?>" class="text-right btn btn-primary"><?php echo e(__('Create').' '.__('Additional_Service')); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm <?php echo e(session()->get('local')); ?>" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__('Title')); ?></th>
                                    <th scope="col"><?php echo e(__('Title')); ?></th>
                                    <th scope="col"><?php echo e(__('Description')); ?></th>
                                    <th scope="col"><?php echo e(__('Description')); ?></th>
                                    <th scope="col"><?php echo e(__('Price')); ?></th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('additional.status.toggle')): ?>
                                    <th scope="col"><?php echo e(__('Status')); ?></th>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['additional.edit'])): ?>
                                    <th style="min-width: 130px" scope="col"><?php echo e(__('Action')); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $additionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($additional->title); ?></td>
                                    <td><?php echo e($additional->title_bn); ?></td>
                                    <td>
                                        <?php echo e(substr($additional->description, 0 ,25)); ?>

                                    </td>
                                    <td>
                                        <?php echo e(substr($additional->description_bn, 0 ,25)); ?>

                                    </td>
                                    <td><?php echo e(currencyPosition($additional->price)); ?></td>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('additional.status.toggle')): ?>
                                    <td>
                                        <form action="<?php echo e(route('additional.status.toggle', $additional->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <label class="switch">
                                                <input <?php echo e($additional->is_active ? 'checked':''); ?> type="checkbox" onchange="this.closest('form').submit()">
                                                <span class="slider round"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['additional.edit'])): ?>
                                    <td>
                                        <span>
                                            <a href="<?php echo e(route('additional.edit', $additional->id)); ?>" class="btn btn-sm btn-primary">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        </span>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\additional-services\index.blade.php ENDPATH**/ ?>