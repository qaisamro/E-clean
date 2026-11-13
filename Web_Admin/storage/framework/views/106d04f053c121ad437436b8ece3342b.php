<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-5">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
                <a href="<?php echo e(route('product.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> <?php echo e(__('Back')); ?></a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title m-0"><?php echo e(__('Edit'). ' '.__('Product')); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img width="50%" src="<?php echo e($product->thumbnailPath); ?>" alt="">
                        </div>
                        <?php if (isset($component)) { $__componentOriginal18ad2e0d264f9740dc73fff715357c28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18ad2e0d264f9740dc73fff715357c28 = $attributes; } ?>
<?php $component = App\View\Components\Form::resolve(['type' => 'Update','method' => 'true','route' => 'product.update','updateId' => ''.e($product->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Form::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <label class="mb-1"><?php echo e(__('Product').' '.__('Name')); ?></label>
                            <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['name' => 'name_bn','type' => 'text','placeholder' => ''.e(__('Product').' '.__('Name')).'','value' => ''.e(old('name_bn') ?? $product->name_bn).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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

                            <label class="mb-1"><?php echo e(__('Product').' '.__('Price')); ?></label>
                            <input name="price" type='text' class="form-control" placeholder="<?php echo e(__('Product').' '.__('Price')); ?>"
                                value="<?php echo e(old('price') ?? $product->price); ?>" onkeypress="onlyNumber(event)"/>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger d-block"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <label class="mb-1 mt-3"><?php echo e(__('Discount').' '.__('Price')); ?></label>
                            <input name="discount_price" type='text' class="form-control" placeholder="<?php echo e(__('Discount').' '.__('Price')); ?>"
                                value="<?php echo e(old('discount_price') ?? $product->discount_price); ?>" onkeypress="onlyNumber(event)"/>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['discount_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger d-block"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <label class="mb-1 mt-3"><?php echo e(__('Description')); ?></label>
                            <textarea name="description" class="form-control" placeholder="<?php echo e(__('Description')); ?>"><?php echo e($product->description); ?></textarea>

                            <input type="hidden" id="slug" name="slug" class="form-control input-default"
                                value="<?php echo e(old('slug') ?? $product->slug); ?>">

                                <label class="mb-1 mt-3"><?php echo e(__('Select').' '.__('Service')); ?></label>
                            <?php if (isset($component)) { $__componentOriginalbf566fc26595b9cc6779e170beef8a5a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbf566fc26595b9cc6779e170beef8a5a = $attributes; } ?>
<?php $component = App\View\Components\Select::resolve(['name' => 'service_id'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <option value=""><?php echo e(__('Select')); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($service->id); ?>"
                                        <?php echo e($product->service_id == $service->id ? 'selected' : ''); ?>>
                                        <?php echo e($service->name_bn ?? $service->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbf566fc26595b9cc6779e170beef8a5a)): ?>
<?php $attributes = $__attributesOriginalbf566fc26595b9cc6779e170beef8a5a; ?>
<?php unset($__attributesOriginalbf566fc26595b9cc6779e170beef8a5a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbf566fc26595b9cc6779e170beef8a5a)): ?>
<?php $component = $__componentOriginalbf566fc26595b9cc6779e170beef8a5a; ?>
<?php unset($__componentOriginalbf566fc26595b9cc6779e170beef8a5a); ?>
<?php endif; ?>

                            <label class="mb-1"><?php echo e(__('Product').' '.__('Photo')); ?></label>
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

                            <div class="form-group">
                                <label for="active" class="mr-2">
                                    <input type="radio" id="active" name="active"
                                        <?php echo e($product->is_active ? 'checked' : ''); ?> value="1"> <?php echo e(__('Active')); ?>

                                </label>

                                <label for="in_active">
                                    <input type="radio" id="in_active" name="active"
                                        <?php echo e(!$product->is_active ? 'checked' : ''); ?> value="0"> <?php echo e(__('Inactive')); ?>

                                </label>
                            </div>

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

<?php $__env->startPush('scripts'); ?>
    <script>
        function onlyNumber(evt) {
            var chars = String.fromCharCode(evt.which);
            if (!(/[0-9.]/.test(chars))) {
                evt.preventDefault();
            }
        }

        $('#name').keyup(function() {
            $('#slug').val($(this).val().toLowerCase().split(',').join('').replace(/\s/g, "-"));
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\products\edit.blade.php ENDPATH**/ ?>