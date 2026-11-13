<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card px-4 pb-2">
                <div class="card-header py-2 d-flex align-items-center justify-content-between mb-3">
                    <h2 class="card-title m-0"><?php echo e(__('All Admins')); ?></h2>
                    <div class="d-flex justify-content-end" >
                        <a href="<?php echo e(route('admin.create')); ?>" class="btn btn-primary"><?php echo e(__('Create New Admin')); ?></a>
                    </div>
                </div>

                <table class="table table-bordered table-striped <?php echo e(session()->get('local')); ?>" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo e(__('Name')); ?></th>
                            <th scope="col"><?php echo e(__('Email')); ?></th>
                            <th scope="col"><?php echo e(__('Mobile')); ?></th>
                            <th scope="col"><?php echo e(__('Gender')); ?></th>
                            <th scope="col"><?php echo e(__('Created_At')); ?></th>
                            <th scope="col"><?php echo e(__('Status')); ?></th>
                            <th scope="col" class="px-2"><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($admin->name); ?></td>
                            <td><?php echo e($admin->email); ?></td>
                            <td><?php echo e($admin->mobile); ?></td>
                            <td><?php echo e($admin->gender ?? '--'); ?></td>
                            <td><?php echo e($admin->created_at->format('M d, Y')); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.status-update', $admin->id)); ?>" method="POST" style="display:inline">
                                    <?php echo csrf_field(); ?>
                                    <label class="switch">
                                        <input type="checkbox" <?php echo e($admin->is_active ? 'checked' : ''); ?> onchange="this.form.submit()">
                                        <span class="slider round"></span>
                                    </label>
                                </form>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.edit', $admin->id)); ?>" class="btn btn-danger py-1 px-2"><?php echo e(__('Edit')); ?></a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->id() !== $admin->id && !$admin->hasRole('root')): ?>
                                <form action="<?php echo e(route('admin.destroy', $admin->id)); ?>" method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المدير؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-dark py-1 px-2"><i class="fa fa-trash"></i></button>
                                </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\root\admin\index.blade.php ENDPATH**/ ?>