<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between bg-primary py-2 align-items-center">
                <h2 class="card-title m-0 text-white"><?php echo e(__('Area'). ' '.__('List')); ?></h2>
                <button class="btn btn-white" data-toggle="modal" data-target="#addAreaModal"><?php echo e(__('Add_New'). ' '.__('Area')); ?></button>
            </div>

            <div class="card-body">
                <table class="table table-bordered <?php echo e(session()->get('local')); ?>" id="myTable">
                    <thead class="bg-secondary">
                        <tr>
                            <th><?php echo e(__('SL.')); ?></th>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th><?php echo e(__('Action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td> <?php echo e($index+1); ?></td>
                                <td> <?php echo e($area->name); ?></td>
                                <td>
                                    <label class="switch">
                                        <a href="<?php echo e(route('areas.toggle', $area->id)); ?>">
                                            <input type="checkbox" <?php echo e($area->status ? 'checked' : ''); ?>>
                                            <span class="slider round"></span>
                                        </a>
                                    </label>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#editsocial<?php echo e($area->id); ?>"><i class="fas fa-edit"></i></button>
                                    <a href="<?php echo e(route('areas.delete', $area->id)); ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="editsocial<?php echo e($area->id); ?>">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary py-2">
                                            <h4 class="modal-title text-white"><?php echo e(__('Edit'). ' '.__('Area')); ?></h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="<?php echo e(route('areas.update', $area->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="mb-0"><?php echo e(__('Area'). ' '.__('Name')); ?></label>
                                                    <input type="text" name="name" value="<?php echo e($area->name); ?>"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer py-2">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                                <button type="submit" class="btn btn-primary"><?php echo e(__('Update')); ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addAreaModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary py-2">
                    <h4 class="modal-title text-white"><?php echo e(__('Add_New'). ' '.__('Area')); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('areas.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="mb-0"><?php echo e(__('Area'). ' '.__('Name')); ?></label>
                            <input type="text" name="name" placeholder="<?php echo e(__('Area'). ' '.__('Name')); ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\area\index.blade.php ENDPATH**/ ?>