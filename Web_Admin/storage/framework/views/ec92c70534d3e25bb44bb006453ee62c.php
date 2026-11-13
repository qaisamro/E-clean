<?php $__env->startSection('content'); ?>
    <div class="container-fluid mb-3">

        <style>
            .paymentLogo {
                max-width: 400px;
                width: auto;
                height: auto;
                object-fit: contain;
            }
        </style>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="m-0"><?php echo e(__('Payment Gateways')); ?></h4>
        </div>
        <div class="row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paymentGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paymentGateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php
                    $configs = json_decode($paymentGateway->config);
                ?>
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between gap-2 py-3">
                            <p class="paymentTitle m-0">
                                <?php echo e(strtoupper($paymentGateway->name->value)); ?>

                            </p>

                            <div class="d-flex align-items-center gap-2">
                                <span class="<?php echo e($paymentGateway->is_active ? 'statusOn' : 'statusOff'); ?>">
                                    <?php echo e($paymentGateway->is_active ? __('On') : __('Off')); ?>

                                </span>
                                <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                    data-bs-title="<?php echo e($paymentGateway->is_active ? 'Turn off' : 'Turn on'); ?>">
                                    <a href="<?php echo e(route('payment-gateway.toggle', $paymentGateway->id)); ?>" class="confirm">
                                        <input type="checkbox" <?php echo e($paymentGateway->is_active ? 'checked' : ''); ?>

                                            style="display:none;">
                                        <span class="slider round"></span>
                                    </a>
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="py-2">
                                <img id="preview<?php echo e($paymentGateway->name->value); ?>" class="paymentLogo"
                                    src="<?php echo e($paymentGateway->logo); ?>" alt="logo" loading="lazy">
                            </div>
                            <form action="<?php echo e(route('payment-gateway.update', $paymentGateway->id)); ?>" method="POST"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="mt-3">
                                    <?php if (isset($component)) { $__componentOriginal2f7449dc06aaba4ce2aaec05fb0faf80 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f7449dc06aaba4ce2aaec05fb0faf80 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select1','data' => ['name' => 'mode','title' => __('Mode'),'id' => 'mode','placeholder' => 'Select Mode','required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select1'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'mode','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Mode')),'id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('mode'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Select Mode'),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
                                        <option value="test" <?php echo e($paymentGateway->mode == 'test' ? 'selected' : ''); ?>>
                                            <?php echo e(__('Test')); ?>

                                        </option>
                                        <option value="live" <?php echo e($paymentGateway->mode == 'live' ? 'selected' : ''); ?>

                                            <?php echo e(app()->environment('local') ? 'disabled' : ''); ?>>
                                            <?php echo e(__('Live')); ?>

                                        </option>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2f7449dc06aaba4ce2aaec05fb0faf80)): ?>
<?php $attributes = $__attributesOriginal2f7449dc06aaba4ce2aaec05fb0faf80; ?>
<?php unset($__attributesOriginal2f7449dc06aaba4ce2aaec05fb0faf80); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2f7449dc06aaba4ce2aaec05fb0faf80)): ?>
<?php $component = $__componentOriginal2f7449dc06aaba4ce2aaec05fb0faf80; ?>
<?php unset($__componentOriginal2f7449dc06aaba4ce2aaec05fb0faf80); ?>
<?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $configs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $label = ucwords(str_replace('_', ' ', $key));
                                    ?>
                                    <div class="mt-3">
                                        <?php if (isset($component)) { $__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input1','data' => ['value' => $value,'name' => 'config['.e($key).']','type' => 'text','placeholder' => ''.e($label).'','title' => ''.e($label).'','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input1'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'name' => 'config['.e($key).']','type' => 'text','placeholder' => ''.e($label).'','title' => ''.e($label).'','required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd)): ?>
<?php $attributes = $__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd; ?>
<?php unset($__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd)): ?>
<?php $component = $__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd; ?>
<?php unset($__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd); ?>
<?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mt-3">
                                    <?php if (isset($component)) { $__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input1','data' => ['name' => 'title','type' => 'text','title' => __('Payment_Gateway_Title'),'value' => $paymentGateway->title,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input1'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'title','type' => 'text','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Payment_Gateway_Title')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentGateway->title),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd)): ?>
<?php $attributes = $__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd; ?>
<?php unset($__attributesOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd)): ?>
<?php $component = $__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd; ?>
<?php unset($__componentOriginal7e57b8e26ad0a9f7a86112d9fc2b0bdd); ?>
<?php endif; ?>
                                </div>
                                <div class="mt-3">

                                    <?php if (isset($component)) { $__componentOriginalaf3247959ab3a65d2e34bd4ef4a74b01 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf3247959ab3a65d2e34bd4ef4a74b01 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.file','data' => ['name' => 'logo','title' => 'Choose Logo','preview' => 'preview'.e($paymentGateway->name->value).'','required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'logo','title' => 'Choose Logo','preview' => 'preview'.e($paymentGateway->name->value).'','required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaf3247959ab3a65d2e34bd4ef4a74b01)): ?>
<?php $attributes = $__attributesOriginalaf3247959ab3a65d2e34bd4ef4a74b01; ?>
<?php unset($__attributesOriginalaf3247959ab3a65d2e34bd4ef4a74b01); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaf3247959ab3a65d2e34bd4ef4a74b01)): ?>
<?php $component = $__componentOriginalaf3247959ab3a65d2e34bd4ef4a74b01; ?>
<?php unset($__componentOriginalaf3247959ab3a65d2e34bd4ef4a74b01); ?>
<?php endif; ?>
                                </div>
                                <div class="mt-3 d-flex justify-content-end">
                                    <?php if (isset($component)) { $__componentOriginald69c099a83c1f4aca6d4ca124513b864 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69c099a83c1f4aca6d4ca124513b864 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common-button','data' => ['name' => __('save_and_update')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('save_and_update'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69c099a83c1f4aca6d4ca124513b864)): ?>
<?php $attributes = $__attributesOriginald69c099a83c1f4aca6d4ca124513b864; ?>
<?php unset($__attributesOriginald69c099a83c1f4aca6d4ca124513b864); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69c099a83c1f4aca6d4ca124513b864)): ?>
<?php $component = $__componentOriginald69c099a83c1f4aca6d4ca124513b864; ?>
<?php unset($__componentOriginald69c099a83c1f4aca6d4ca124513b864); ?>
<?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(".confirm").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "هل أنت متأكد؟",
                text: "أنت على وشك تغيير الحالة!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "نعم، غيّرها!",
                cancelButtonText: "إلغاء",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\paymentGateway\index.blade.php ENDPATH**/ ?>