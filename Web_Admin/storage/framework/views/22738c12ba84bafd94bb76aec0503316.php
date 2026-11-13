<?php $__env->startSection('content'); ?>
<?php ($bannerSection = $section ?? 'promotional'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title float-left">
                        <?php echo e($bannerSection === 'wide' ? __('لافتات عريضة (1200 × 318)') : __('App_Banners')); ?>

                    </h2>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.store')): ?>
                    <div class="w-100 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                            <?php echo e(__('Add Banner')); ?>

                        </button>
                    </div>

                    <div class="modal fade" id="createModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title" id="exampleModalLabel"><?php echo e(__('Add Banner')); ?></h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form <?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?> action="<?php echo e(route('banner.store')); ?>" <?php endif; ?> method="POST" enctype="multipart/form-data"> <?php echo csrf_field(); ?>
                                <input type="hidden" name="banner_section" value="<?php echo e($bannerSection); ?>">
                                <div class="modal-body">
                                    <label class="mb-1"><?php echo e(__('Banner'). ' '.__('Title')); ?></label>
                                    <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['name' => 'title','type' => 'text','placeholder' => ''.e(__('Banner'). ' '.__('Title')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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

                                    <label class="mb-1"><?php echo e(__('Banner'). ' '.__('Photo')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bannerSection === 'wide'): ?>
                                    <small class="d-block text-muted mb-2">المقاس الموصى به: 1200 × 318 بكسل</small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginal55a70e3775a14fff3c2584cc49fc912c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a70e3775a14fff3c2584cc49fc912c = $attributes; } ?>
<?php $component = App\View\Components\InputFile::resolve(['name' => 'image'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputFile::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'file']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55a70e3775a14fff3c2584cc49fc912c)): ?>
<?php $attributes = $__attributesOriginal55a70e3775a14fff3c2584cc49fc912c; ?>
<?php unset($__attributesOriginal55a70e3775a14fff3c2584cc49fc912c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55a70e3775a14fff3c2584cc49fc912c)): ?>
<?php $component = $__componentOriginal55a70e3775a14fff3c2584cc49fc912c; ?>
<?php unset($__componentOriginal55a70e3775a14fff3c2584cc49fc912c); ?>
<?php endif; ?>

                                    <label class="mb-1"><?php echo e(__('Banner'). ' '.__('Description')); ?></label>
                                    <?php if (isset($component)) { $__componentOriginal0eb289d06f2ec5a97cf23eec2de5caeb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0eb289d06f2ec5a97cf23eec2de5caeb = $attributes; } ?>
<?php $component = App\View\Components\Textarea::resolve(['name' => 'description','placeholder' => ''.e(__('Banner'). ' '.__('Description')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Textarea::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0eb289d06f2ec5a97cf23eec2de5caeb)): ?>
<?php $attributes = $__attributesOriginal0eb289d06f2ec5a97cf23eec2de5caeb; ?>
<?php unset($__attributesOriginal0eb289d06f2ec5a97cf23eec2de5caeb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0eb289d06f2ec5a97cf23eec2de5caeb)): ?>
<?php $component = $__componentOriginal0eb289d06f2ec5a97cf23eec2de5caeb; ?>
<?php unset($__componentOriginal0eb289d06f2ec5a97cf23eec2de5caeb); ?>
<?php endif; ?>

                                    <div class="form-group">
                                        <label for="active">
                                            <input type="radio" id="active" name="active" value="1" checked> <?php echo e(__('Active')); ?>

                                        </label>
                                        <label for="in_active" class="ml-3">
                                            <input type="radio" id="in_active" name="active" value="0"> <?php echo e(__('Inactive')); ?>

                                        </label>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bannerSection !== 'wide'): ?>
                                    <div class="form-group">
                                        <label for="banner">
                                            <input type="checkbox" id="banner" class="form-control-checkbox" name="banner" value="1"> <?php echo e(__('Web'). ' '.__('Banner')); ?>

                                        </label>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                    <button type="submit" class="btn btn-primary <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?>visitorMessage <?php endif; ?>"><?php echo e(__('Save_changes')); ?></button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div> 
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm <?php echo e(session()->get('local')); ?>">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__('Title')); ?></th>
                                    <th scope="col"><?php echo e(__('Description')); ?></th>
                                    <th scope="col"><?php echo e(__('Image')); ?></th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.status.toggle')): ?>
                                    <th scope="col"><?php echo e(__('Status')); ?></th>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['banner.destroy', 'banner.edit'])): ?>
                                    <th scope="col"><?php echo e(__('Action')); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($banner->title); ?></td>
                                    <td>
                                        <?php echo e(substr($banner->description, 0 ,30)); ?>

                                    </td>
                                    <td>
                                        <img width="100" src="<?php echo e(asset($banner->thumbnailPath)); ?>" alt="">
                                    </td>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.status.toggle')): ?>
                                    <td>
                                        <form action="<?php echo e(route('banner.status.toggle', $banner->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <label class="switch">
                                                <input type="checkbox" <?php echo e($banner->is_active ? 'checked' : ''); ?> onchange="this.closest('form').submit()">
                                                <span class="slider round"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['banner.destroy', 'banner.edit'])): ?>
                                    <td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.edit')): ?>
                                        <a href="<?php echo e(route('banner.edit', $banner->id)); ?>" class="btn btn-sm btn-primary">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.destroy')): ?>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal_<?php echo e($banner->id); ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <div class="modal fade" id="deleteModal_<?php echo e($banner->id); ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h2 class="modal-title" id="exampleModalLabel"><?php echo e(__('Delete a banner')); ?></h2>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                    <div class="modal-body">
                                                        <h3 class="text-warning"><?php echo e(__('Are you sure?')); ?></h3>
                                                        <h5><?php echo e(__('You want to permanently delete this banner.')); ?></h5>
                                                        <img width="30%" src="<?php echo e($banner->thumbnailPath); ?>" alt="">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?>
                                                        <button type="button" class="btn btn-danger visitorMessage"><?php echo e(__('Delete')); ?></button>
                                                        <?php else: ?>
                                                        <form action="<?php echo e(route('banner.destroy', $banner->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-danger"><?php echo e(__('Delete')); ?></button>
                                                        </form>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\banners\index.blade.php ENDPATH**/ ?>