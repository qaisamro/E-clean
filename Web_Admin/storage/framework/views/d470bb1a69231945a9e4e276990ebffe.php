<?php $__env->startSection('content'); ?>
<section style="background-color:#FAF7F2;" class="max-w-2lg mx-auto px-4 py-8">
    <div class="flex items-center gap-4 mb-6">
        <img src="<?php echo e($vendor->logoPath); ?>" alt="<?php echo e($vendor->name); ?>" class="w-16 h-16 rounded-[14px] object-cover border">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900"><?php echo e($vendor->name); ?></h1>
            <p class="text-sm text-gray-500 flex items-center gap-1"><i class="fa fa-map-marker-alt"></i> <?php echo e($vendor->address); ?> • <?php echo e($vendor->phone); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($services->isEmpty()): ?>
        <div class="text-center py-16">
            <p class="text-gray-500">لا توجد خدمات لهذا المحل حالياً</p>
            <a href="<?php echo e(route('web.home')); ?>" class="btn_solid mt-4 inline-block">العودة للرئيسية</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex flex-col p-4 rounded-[20px] bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] border hover:shadow-lg transition">
                    <img src="<?php echo e($service->thumbnailPath); ?>" alt="<?php echo e($service->name_bn ?? $service->name); ?>" class="w-full h-32 object-cover rounded-[14px] mb-3">
                    <h3 class="font-semibold text-gray-900"><?php echo e($service->name_bn ?? $service->name); ?></h3>
                    <p class="text-sm text-gray-500 mb-3"><?php echo e(Str::limit($service->description, 80)); ?></p>
                    <div class="mt-auto flex items-center border-t pt-3">
                        <span class="text-[#028090] font-bold">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->lowestProduct): ?> <?php echo e(currencyPosition($service->lowestProduct->final_price)); ?> <?php else: ?> <?php echo e(__('Price on request')); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\vendor_store.blade.php ENDPATH**/ ?>