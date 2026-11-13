<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-5">
    <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
        <a href="<?php echo e(route('service.index')); ?>" class="btn btn-primary mb-1"><i class="fa fa-arrow-left"></i> <?php echo e(__('Back')); ?></a>
    </div>

    <div class="row">
        <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php echo e(__('Add') . ' '. __('Service')); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (isset($component)) { $__componentOriginal18ad2e0d264f9740dc73fff715357c28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18ad2e0d264f9740dc73fff715357c28 = $attributes; } ?>
<?php $component = App\View\Components\Form::resolve(['route' => 'service.store','type' => 'Submit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Form::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <label><?php echo e(__('Name')); ?></label>
                        <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['name' => 'name_bn','type' => 'text','placeholder' => ''.e(__('Service').' '.__('Name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['required' => 'true']); ?>
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

                        <label><?php echo e(__('Service').' '.__('Photo')); ?></label>
                        <?php if (isset($component)) { $__componentOriginal55a70e3775a14fff3c2584cc49fc912c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a70e3775a14fff3c2584cc49fc912c = $attributes; } ?>
<?php $component = App\View\Components\InputFile::resolve(['name' => 'image'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputFile::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'file','required' => 'true']); ?>
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

                        <label><?php echo e(__('Service').' '.__('Description')); ?></label>
                        <?php if (isset($component)) { $__componentOriginal0eb289d06f2ec5a97cf23eec2de5caeb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0eb289d06f2ec5a97cf23eec2de5caeb = $attributes; } ?>
<?php $component = App\View\Components\Textarea::resolve(['name' => 'description','placeholder' => ''.e(__('Service').' '.__('Description')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18ad2e0d264f9740dc73fff715357c28)): ?>
<?php $attributes = $__attributesOriginal18ad2e0d264f9740dc73fff715357c28; ?>
<?php unset($__attributesOriginal18ad2e0d264f9740dc73fff715357c28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18ad2e0d264f9740dc73fff715357c28)): ?>
<?php $component = $__componentOriginal18ad2e0d264f9740dc73fff715357c28; ?>
<?php unset($__componentOriginal18ad2e0d264f9740dc73fff715357c28); ?>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\services\create.blade.php ENDPATH**/ ?>