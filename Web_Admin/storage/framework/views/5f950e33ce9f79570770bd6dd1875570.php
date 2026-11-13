<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h2 class="card-title float-left"><?php echo e(__('All Products')); ?></h2>
                        </div>

                        <div class="col-md-8">
                            <form action="<?php echo e(route('product.index')); ?>" method="GET">
                                <ul class="nav nav-pills justify-content-end">
                                    <li class="nav-item ml-2 mr-md-0">
                                        <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['type' => 'text','name' => 'search','placeholder' => ''.e(__('Search')).'','value' => ''.e(request('search')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal786b6632e4e03cdf0a10e8880993f28a)): ?>
<?php $attributes = $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a; ?>
<?php unset($__attributesOriginal786b6632e4e03cdf0a10e8880993f28a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal786b6632e4e03cdf0a10e8880993f28a)): ?>
<?php $component = $__componentOriginal786b6632e4e03cdf0a10e8880993f28a; ?>
<?php unset($__componentOriginal786b6632e4e03cdf0a10e8880993f28a); ?>
<?php endif; ?>
                                    </li>
                                    <li class="nav-item ml-2 mr-md-0">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    </li>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product.create')): ?>
                                    <li class="nav-item ml-2 mr-md-0">
                                        <a href="<?php echo e(route('product.create')); ?>" class="btn btn-primary">
                                            <?php echo e(__('Add Product')); ?>

                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped <?php echo e(session()->get('local')); ?>" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__('Name')); ?></th>
                                    <th scope="col"><?php echo e(__('Thumbnail')); ?></th>
                                    <th scope="col"><?php echo e(__('Discount').' '.__('Price')); ?></th>
                                    <th scope="col"><?php echo e(__('Price')); ?></th>
                                    <th scope="col"><?php echo e(__('Description')); ?></th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product.status.toggle')): ?>
                                    <th scope="col"><?php echo e(__('Status')); ?></th>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product.edit')): ?>
                                    <th scope="col"><?php echo e(__('Action')); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($product->name_bn ?? $product->name); ?></td>
                                    <td>
                                        <img width="100" src="<?php echo e($product->thumbnailPath); ?>" alt="">
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->discount_price): ?>
                                        <?php echo e(currencyPosition($product->discount_price)); ?>

                                        <?php else: ?>
                                        <del><?php echo e(currencyPosition('00')); ?></del>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->discount_price): ?>
                                        <del><?php echo e(currencyPosition($product->price ? $product->price : '00')); ?></del>
                                        <?php else: ?>
                                            <?php echo e(currencyPosition($product->price ? $product->price : '00')); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($product->description); ?>

                                    </td>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product.status.toggle')): ?>
                                    <td>
                                        <form action="<?php echo e(route('product.status.toggle', $product->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <label class="switch">
                                                <input type="checkbox" <?php echo e($product->is_active ? 'checked' : ''); ?> onchange="this.closest('form').submit()">
                                                <span class="slider round"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product.edit')): ?>
                                    <td>
                                        <a href="<?php echo e(route('product.edit', $product->id)); ?>" class="btn btn-sm btn-primary">
                                            <i class="far fa-edit"></i>
                                        </a>

                                        <form action="<?php echo e(route('product.delete', $product->id)); ?>" method="POST" style="display:inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></button>
                                        </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\products\index.blade.php ENDPATH**/ ?>