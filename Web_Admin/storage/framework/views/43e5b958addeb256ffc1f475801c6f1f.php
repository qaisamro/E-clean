<?php $__env->startSection('title', 'FAQ Categories'); ?>

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">FAQ Categories</h4>
                        <a href="<?php echo e(route('web.faq.category.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Category
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($category->id); ?></td>
                                                <td><?php echo e($category->name); ?></td>
                                                <td>
                                                    <form action="<?php echo e(route('web.faq.category.status.toggle', $category->id)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <label class="switch">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?>
                                                            <a class="visitorMessage">toggle</a>
                                                            <?php else: ?>
                                                            <input type="checkbox"
                                                                <?php echo e($category->status == 'active' ? 'checked' : ''); ?> onchange="this.closest('form').submit()">
                                                            <span class="slider round"></span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </label>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('web.faq.category.edit', $category->id)); ?>"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('web.faq.category.delete', $category->id)); ?>"
                                                        class="btn btn-sm btn-danger delete-confirm">
                                                        <i class="fas fa-trash"></i>
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
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\faq-category\index.blade.php ENDPATH**/ ?>