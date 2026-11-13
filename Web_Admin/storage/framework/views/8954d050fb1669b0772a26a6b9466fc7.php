<?php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $joinSetting = $webSettings->firstWhere('key', 'join_our_network');
    $join = $joinSetting?->value ?? [];
?>

<section class=" max-w-2lg mx-auto px-4 xl-1:px-0 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-[70px]" style="background:#FAF7F2;">
    <div class="flex flex-col justify-center">

        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:28px;">
            <?php
                // Use default if title is empty
                $title = strip_tags($join['title'] ?? '');
                if (empty($title)) {
                    $title = __('Join Our Network');
                }

                // Split into words
                $words = preg_split('/\s+/', $title);

                $first = $words[0] ?? '';
                $highlight = implode(' ', array_slice($words, 1, 2)); // 2nd & 3rd words
                $rest = count($words) > 3 ? implode(' ', array_slice($words, 3)) : '';
            ?>

            <span style="display:inline-flex;align-items:center;background:#EAF6F8;color:#028090;border:1px solid #E2E8F0;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;"><?php echo e(__('Partner Network')); ?></span>

            <p class="headline text-left" style="color:#0B1E2E;font-family:Cairo,sans-serif;margin-top:12px;">
                <?php echo e($first); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($highlight): ?>
                    <span style="color:#028090;"><?php echo e($highlight); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($rest); ?>

            </p>

            <p class="text-base lg:text-lg font-medium text-left" style="color:#5B6B7A;">
                <?php echo e($join['description'] ??
                    __('Partner with Elite Cleaning and connect with customers who need your services. No setup fees, flexible pricing, and instant access to our growing customer base.')); ?>


            </p>
        </div>

        
        <?php
            $lists = !empty($join['lists'])
                ? $join['lists']
                : [
                    ['list' => __('Commission-based pricing (no hidden fees)')],
                    ['list' => __('Get paid weekly via direct deposit')],
                    ['list' => __('Free marketing and customer acquisition')],
                ];
        ?>

        <ul class="pt-6 pb-10 space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex justify-start items-center gap-[10px]" style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:10px 14px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                    <span style="background:#EAF6F8;border-radius:10px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <img src="<?php echo e(asset('website/assets/icons/check-solid.svg')); ?>" alt="check"
                            class="h-[18px] w-[18px]">
                    </span>
                    <p class="text-base text-left" style="color:#0B1E2E;">
                        <?php echo e($item['list'] ?? ''); ?>

                    </p>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>

        <div class="flex flex-col md:flex-row justify-start items-center gap-[15px]">
            <button class="btn_solid" style="background:linear-gradient(135deg,#028090,#00A896);color:#fff;border-radius:14px;border:none;box-shadow:0 6px 14px rgba(11,30,46,.12);">
                <p><?php echo e(__('Start Your Application')); ?></p>

                <img src="<?php echo e(asset('website/assets/icons/arrow-left.svg')); ?>" alt="">
            </button>
            <button class="btn_outline !bg-white" style="border-radius:14px;background:#fff;border:1.5px solid #E2E8F0;color:#028090;">
                <p><?php echo e(__('Learn More')); ?></p>
            </button>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($join['facilities'])): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $join['facilities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div
                    class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    
                    <div
                        class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                        <img src="<?php echo e(!empty($facility['icon']) ? Storage::url($facility['icon']) : asset('website/assets/icons/grow.svg')); ?>"
                            alt="<?php echo e(!empty($facility['title']) ? $facility['title'] : 'Default Facility Icon'); ?>"
                            class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                    </div>

                    
                    <div class="space-y-[10px]">
                        <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;">
                            <?php echo e(!empty($facility['title']) ? $facility['title'] : __('Grow Your Business')); ?>

                        </p>

                        <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;">
                            <?php echo e(!empty($facility['description']) ? $facility['description'] : __('Reach thousands of customers actively searching for dry cleaning services in your area.')); ?>

                        </p>
                    </div>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/grow.svg')); ?>" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;"><?php echo e(__('Grow Your Business')); ?></p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;"><?php echo e(__('Reach thousands of customers actively searching for dry cleaning services in your area.')); ?></p>
                </div>
            </div>
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/people.svg')); ?>" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;"><?php echo e(__('Manage Orders Easily')); ?></p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;"><?php echo e(__('Simple dashboard to track orders, manage pricing, and handle customer requests.')); ?></p>
                </div>
            </div>
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/mobile.svg')); ?>" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;"><?php echo e(__('Mobile-Friendly Tools')); ?></p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;"><?php echo e(__('Accept orders, update status, and communicate with customers on-the-go.')); ?></p>
                </div>
            </div>
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/headset.svg')); ?>" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;"><?php echo e(__('Dedicated Support')); ?></p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;"><?php echo e(__('24/7 store support team to help you succeed and resolve any issues quickly.')); ?></p>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>

    <div class="grid lg:hidden grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-7">
        <!-- card 1 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
            <div>
                <div class="icon_labels group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/grow.svg')); ?>" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;">
                    <?php echo e(__('Grow Your Business')); ?>

                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;">
                    <?php echo e(__('Reach thousands of customers actively searching for dry cleaning services in your area.')); ?>

                </p>
            </div>
        </div>

        <!-- card 2 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
            <div>
                <div class="icon_labels group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/people.svg')); ?>" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;">
                    <?php echo e(__('Manage Orders Easily')); ?>

                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;">
                    <?php echo e(__('Simple dashboard to track orders, manage pricing, and handle customer requests.')); ?>

                </p>
            </div>
        </div>

        <!-- card 3 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
            <div>
                <div class="icon_labels group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/mobile.svg')); ?>" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;">
                    <?php echo e(__('Mobile-Friendly Tools')); ?>

                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;">
                    <?php echo e(__('Accept orders, update status, and communicate with customers on-the-go.')); ?>

                </p>
            </div>
        </div>

        <!-- card 4 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
            <div>
                <div class="icon_labels group-hover:bg-mint-300" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                    <img src="<?php echo e(asset('website/assets/icons/headset.svg')); ?>" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white" style="font-family:Cairo,sans-serif;">
                    <?php echo e(__('Dedicated Support')); ?>

                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white" style="color:#5B6B7A;">
                    <?php echo e(__('24/7 store support team to help you succeed and resolve any issues quickly.')); ?>

                </p>
            </div>
        </div>

    </div>
</section>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\layouts\includes\storeNetwork.blade.php ENDPATH**/ ?>