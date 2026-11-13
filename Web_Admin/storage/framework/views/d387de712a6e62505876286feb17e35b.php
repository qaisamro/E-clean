<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between bg-primary py-2 align-items-center">
                <h2 class="card-title m-0 text-white"><?php echo e(__('All Social Link')); ?></h2>
                <button class="btn btn-white" data-toggle="modal" data-target="#addsocialModal"><?php echo e(__('Add Social Link')); ?></button>
            </div>

            <div class="card-body">
                <table class="table table-bordered <?php echo e(session()->get('local')); ?>" id="myTable">
                    <thead class="bg-secondary">
                        <tr>
                            <th><?php echo e(__('SL.')); ?>.</th>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Icon')); ?></th>
                            <th><?php echo e(__('Url')); ?></th>
                            <th><?php echo e(__('Action')); ?></th>
                        </tr>
                    </thead>
                    <?php
                        $i = 1;
                    ?>
                    <tbody>
                       <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $socialLink; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <tr>
                            <td> <?php echo e($i++); ?></td>
                            <td> <?php echo e($link->name); ?></td>
                            <td> <img src="<?php echo e($link->photoPath); ?>" alt="<?php echo e($link->name); ?>" width="50"></td>
                            <td> <?php echo e($link->url); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editsocial<?php echo e($link->id); ?>"><i class="fas fa-edit"></i></button>
                                <a href="<?php echo e(route('socialLink.delete', $link->id)); ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <!-- Modal -->
                            <div class="modal fade" id="editsocial<?php echo e($link->id); ?>">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary py-2">
                                            <h4 class="modal-title text-white"><?php echo e(__('Edit Social Link')); ?></h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="<?php echo e(route('socialLink.update', $link->id)); ?>" method="POST" enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="mb-0"><?php echo e(__('Social Name')); ?></label>
                                                    <input type="text" name="name" value="<?php echo e($link->name); ?>" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label class="mb-0 d-block"><?php echo e(__('Social Link Photo')); ?></label>
                                                    <img src="<?php echo e($link->photoPath); ?>" alt="<?php echo e($link->name); ?>" width="50">
                                                    <input type="file" name="photo" class="form-control-file mt-2">
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label class="mb-0"><?php echo e(__('Social Link URL')); ?></label>
                                                    <textarea name="url" class="form-control" rows="2" required><?php echo e($link->url); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer py-2">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                                <button type="submit" class="btn btn-primary <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__('Update')); ?></button>
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
        <div class="modal fade" id="addsocialModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-2">
                        <h4 class="modal-title text-white"><?php echo e(__('Add New Social Link')); ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?php echo e(route('socialLink.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="mb-0"><?php echo e(__('Social Name')); ?></label>
                                <input type="text" name="name" placeholder="<?php echo e(__('Social Name')); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="mb-0"><?php echo e(__('Social Link Photo')); ?></label>
                                <input type="file" name="photo" class="form-control-file" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="mb-0"><?php echo e(__('Social Link URL')); ?></label>
                                <textarea name="url" class="form-control" placeholder="<?php echo e(__('Social Link URL')); ?>" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                            <button type="submit" class="btn btn-primary <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__('Save')); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $('#exampleModal').on('show.bs.modal', event => {
                var button = $(event.relatedTarget);
                var modal = $(this);
                // Use above variables to manipulate the DOM

            });
        </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\social\index.blade.php ENDPATH**/ ?>