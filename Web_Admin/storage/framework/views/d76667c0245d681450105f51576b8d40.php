<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title float-left my-1"><?php echo e(__('All') . ' ' . __('Service')); ?></h2>
                        <div class="w-100 text-right">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('service.create')): ?>
                                <a href="<?php echo e(route('service.create')); ?>"
                                    class="text-right btn btn-primary my-md-0 my-1"><?php echo e(__('Add_New') . ' ' . __('Service')); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped verticle-middle table-responsive-sm <?php echo e(session()->get('local')); ?>"
                                id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo e(__('Name')); ?></th>
                                        <th scope="col"><?php echo e(__('Thumbnail')); ?></th>
                                        <th scope="col"><?php echo e(__('Description')); ?></th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('service.status.toggle')): ?>
                                            <th scope="col"><?php echo e(__('Status')); ?></th>
                                        <?php endif; ?>
                                        <th scope="col"><?php echo e(__('Action')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                        <tr>
                                            <td><?php echo e($service->name_bn); ?></td>
                                            <td>
                                                <img width="100" src="<?php echo e(asset($service->thumbnailPath)); ?>"
                                                    alt="">
                                            </td>
                                            <td>
                                                <?php echo e(substr($service->description, 0, 25)); ?>

                                            </td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('service.status.toggle')): ?>
                                                <td>
                                                    <form action="<?php echo e(route('service.status.toggle', $service->id)); ?>" method="POST" style="display:inline">
                                                        <?php echo csrf_field(); ?>
                                                        <label class="switch <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>">
                                                            <input <?php echo e($service->is_active ? 'checked' : ''); ?> type="checkbox" onchange="if(!this.closest('label').classList.contains('visitorMessage')) this.form.submit()">
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                            <?php endif; ?>

                                            <td>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('service.edit')): ?>
                                                    <a href="<?php echo e(route('service.edit', $service->id)); ?>"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <form action="<?php echo e(route('service.delete', $service->id)); ?>" method="POST" style="display:inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger delete-confirm"
                                                        title="<?php echo e(__('Delete')); ?> <?php echo e(__('Service')); ?>">
                                                        <i class="fas fa-trash"></i>
                                                        <span class="sr-only"><?php echo e(__('Delete')); ?> <?php echo e(__('Service')); ?></span>
                                                    </button>
                                                </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\services\index.blade.php ENDPATH**/ ?>