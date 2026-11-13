<?php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $takeLaundry = $webSettings->firstWhere('key', 'take_with_you');
    $takeLaundryData = $takeLaundry?->value ?? [];

    $infos = !empty($takeLaundryData['infos']) && is_array($takeLaundryData['infos']) ? $takeLaundryData['infos'] : [];

    $takeInfo =
        !empty($takeLaundryData['take_info']) && is_array($takeLaundryData['take_info'])
            ? $takeLaundryData['take_info']
            : [];

    $mobileImage = !empty($takeLaundryData['right_side_image'])
        ? asset('storage/' . $takeLaundryData['right_side_image'])
        : asset('website/assets/images/stores-network/mobile.png');

    $imageGroup =
        !empty($takeLaundryData['image_group']) && is_array($takeLaundryData['image_group'])
            ? $takeLaundryData['image_group']
            : [];

    $buttonGroup =
        !empty($takeLaundryData['button_group']) && is_array($takeLaundryData['button_group'])
            ? $takeLaundryData['button_group']
            : [];
?>


<section class="section_container max-w-2lg mx-auto px-4 xl-1:px-0 relative z-10" style="background:#FAF7F2;">
    <header class="heading_section" style="text-align:center;max-width:640px;margin-inline:auto;">
        <div style="display:flex;justify-content:center;">
            <span style="display:inline-flex;align-items:center;background:#EAF6F8;color:#028090;border:1px solid #E2E8F0;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;"><?php echo e(__('Mobile App')); ?></span>
        </div>
        <p style="color:#0B1E2E;font-family:Cairo,sans-serif;font-weight:800;font-size:28px;margin-top:12px;">
            
            <?php echo e(__('Take')); ?> <span style="color:#028090;"><?php echo e(__('Elite Cleaning')); ?></span> <?php echo e(__('With You')); ?>

        </p>
        <p style="color:#5B6B7A;">
            <?php echo e($takeLaundry['sub_title'] ?? __('Download our mobile app for exclusive features and a seamless dry cleaning experience on the go.')); ?>

        </p>
    </header>
    <div class="rounded-[44px] grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12 overflow-hidden" style="background: linear-gradient(to right, #028090, #00A896);border-radius:20px;border:1px solid #E2E8F0;box-shadow:0 6px 14px rgba(11,30,46,.06);">
        <div class="p-5">
            <div class="h-full w-full p-3 md:p-6 rounded-3xl flex flex-col gap-6 justify-between bg-white" style="border-radius:20px;">
                <div class="flex justify-start items-center gap-2 md:gap-4">
                    <div
                        class="w-[56px] h-[56px] flex justify-center items-center border border-gray-100 rounded-lg p-2" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;border:1px solid #E2E8F0;">
                        <img class="object-contain w-full h-full"
                            src="<?php echo e(asset('website/assets/logo/logo-white-sm.png')); ?>" alt="">
                    </div>


                    <div class="space-y-0.5">
                        <p class="text-sm sm:text-lg font-semibold text-left" style="color:#0B1E2E;font-family:Cairo,sans-serif;">
                            <?php echo e(__('Your Care, Just a Tap Away')); ?>

                        </p>
                        <p class="text-[10px] sm:text-sm text-left" style="color:#5B6B7A;">
                            <?php echo e(__('Everything you need in your pocket')); ?>

                        </p>
                    </div>
                </div>

                <div class="flex-1 pb-6 space-y-3 md:space-y-4 border-b border-gray-100" style="border-color:#E2E8F0;">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $infos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div
                            class="flex justify-start items-center gap-3 md:gap-4 p-2 md:p-3 rounded-xl hover:bg-mint-50/50 transition-all duration-200 group" style="border:1px solid transparent;">
                            <div
                                class="bg-white border border-gray-100 shadow-sm h-12 w-12 md:h-14 md:w-14 rounded-xl flex justify-center items-center flex-shrink-0 group-hover:border-mint-200 group-hover:shadow-md transition-all duration-200" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($info['icon'])): ?>
                                    <img src="<?php echo e(asset('storage/' . $info['icon'])); ?>"
                                        class="object-contain w-6 h-6 md:w-7 md:h-7">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm md:text-[15px] font-semibold text-left text-neutral-700 group-hover:text-mint-700 transition-colors duration-200" style="color:#0B1E2E;">
                                    <?php echo e($info['title'] ?? ''); ?>

                                </p>
                                <p class="text-xs md:text-sm text-left text-neutral-400 truncate" style="color:#5B6B7A;">
                                    <?php echo e($info['sub_title'] ?? ''); ?>

                                </p>
                            </div>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div
                            class="flex justify-start items-center gap-3 md:gap-4 p-2 md:p-3 rounded-xl hover:bg-mint-50/50 transition-all duration-200 group mb-2" style="border:1px solid transparent;">
                            <div
                                class="bg-white border border-gray-100 shadow-sm h-12 w-12 md:h-14 md:w-14 rounded-xl flex justify-center items-center flex-shrink-0 group-hover:border-mint-200 group-hover:shadow-md transition-all duration-200" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                                <img src="<?php echo e(asset('website/assets/icons/question.svg')); ?>"
                                    class="object-contain w-6 h-6 md:w-7 md:h-7">
                            </div>

                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm md:text-[15px] font-semibold text-left text-neutral-700 group-hover:text-mint-700 transition-colors duration-200" style="color:#0B1E2E;">
                                    <?php echo e(__('Real-Time Tracking')); ?>

                                </p>
                                <p class="text-xs md:text-sm text-left text-neutral-400 truncate" style="color:#5B6B7A;">
                                    <?php echo e(__('Track your dry cleaning status live')); ?>

                                </p>
                            </div>

                        </div>
                        <div
                            class="flex justify-start items-center gap-3 md:gap-4 p-2 md:p-3 rounded-xl hover:bg-mint-50/50 transition-all duration-200 group mb-2" style="border:1px solid transparent;">
                            <div
                                class="bg-white border border-gray-100 shadow-sm h-12 w-12 md:h-14 md:w-14 rounded-xl flex justify-center items-center flex-shrink-0 group-hover:border-mint-200 group-hover:shadow-md transition-all duration-200" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                                <img src="<?php echo e(asset('website/assets/icons/clock.svg')); ?>"
                                    class="object-contain w-6 h-6 md:w-7 md:h-7">
                            </div>

                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm md:text-[15px] font-semibold text-left text-neutral-700 group-hover:text-mint-700 transition-colors duration-200" style="color:#0B1E2E;">
                                    <?php echo e(__('Scheduled Pickups')); ?>

                                </p>
                                <p class="text-xs md:text-sm text-left text-neutral-400 truncate" style="color:#5B6B7A;">
                                    <?php echo e(__('Book convenient pickup times')); ?>

                                </p>
                            </div>

                        </div>
                        <div
                            class="flex justify-start items-center gap-3 md:gap-4 p-2 md:p-3 rounded-xl hover:bg-mint-50/50 transition-all duration-200 group" style="border:1px solid transparent;">
                            <div
                                class="bg-white border border-gray-100 shadow-sm h-12 w-12 md:h-14 md:w-14 rounded-xl flex justify-center items-center flex-shrink-0 group-hover:border-mint-200 group-hover:shadow-md transition-all duration-200" style="background:#EAF6F8;border-radius:14px;border:1px solid #E2E8F0;">
                                <img src="<?php echo e(asset('website/assets/icons/location-green.svg')); ?>"
                                    class="object-contain w-6 h-6 md:w-7 md:h-7">
                            </div>

                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm md:text-[15px] font-semibold text-left text-neutral-700 group-hover:text-mint-700 transition-colors duration-200" style="color:#0B1E2E;">
                                    <?php echo e(__('Instant Notifications')); ?>

                                </p>
                                <p class="text-xs md:text-sm text-left text-neutral-400 truncate" style="color:#5B6B7A;">
                                    <?php echo e(__('Get updates on order progress')); ?>

                                </p>
                            </div>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-start items-center gap-2 sm:gap-[30px]">
                    <div class="space-y-4">
                        <p class="text-base font-semibold text-left" style="color:#0B1E2E;"><?php echo e(__('Get Mobile App on')); ?>

                            <br>
                            <?php echo e(__('App Store & Google Play')); ?>

                        </p>
                        <div class="flex justify-start items-center gap-[10px]">
                            <div class="flex justify-start items-center -space-x-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $imageGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <img src="<?php echo e(asset('storage/' . $image['img'])); ?>" alt=""
                                        class="w-9 h-9 overflow-hidden rounded-full border-2 border-white">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <img src="<?php echo e(asset('website/assets/images/stores-network/user-1.png')); ?>"
                                        alt=""
                                        class="w-9 h-9 overflow-hidden rounded-full border-2 border-white">
                                    <img src="<?php echo e(asset('website/assets/images/stores-network/user-1.png')); ?>"
                                        alt=""
                                        class="w-9 h-9 overflow-hidden rounded-full border-2 border-white">
                                    <img src="<?php echo e(asset('website/assets/images/stores-network/user-1.png')); ?>"
                                        alt=""
                                        class="w-9 h-9 overflow-hidden rounded-full border-2 border-white">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-left text-neutral-800">4.9 <span
                                        class="text-neutral-500">(<?php echo e(__('10k+ reviews')); ?>)</span></p>
                                <div class="flex justify-start items-center gap-[2px]">
                                    <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="">
                                    <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="">
                                    <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="">
                                    <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="">
                                    <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <img src="<?php echo e(asset('website/assets/images/stores-network/qr.png')); ?>" alt="">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-[10px]">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $takeInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e($info['link'] ?? '#'); ?>"
                            class="transition-all duration-150 flex justify-center items-center h-10 sm:h-12 w-full rounded-xl px-2 gap-1.5" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($info['icon'])): ?>
                                <img src="<?php echo e(asset('storage/' . $info['icon'])); ?>"
                                    class="h-3 w-3 sm:h-4 sm:w-4 object-contain">
                            <?php else: ?>
                                <img src="<?php echo e(asset('website/assets/icons/download-icon-white.svg')); ?>" alt=""
                                    class="h-3 w-3 sm:h-4 sm:w-4">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <p class="text-xs sm:text-sm font-semibold text-center text-white">
                                <?php echo e($info['title'] ?? __('Download')); ?>

                            </p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <a href="#"
                            class="transition-all duration-150 flex justify-center items-center h-10 sm:h-12 w-full rounded-xl px-2 gap-1.5" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;">
                            <img src="<?php echo e(asset('website/assets/icons/download-icon-white.svg')); ?>" alt=""
                                class="h-3 w-3 sm:h-4 sm:w-4">
                            <p class="text-xs sm:text-sm font-semibold text-center text-white">
                                <?php echo e(__('Download for iOS')); ?>

                            </p>
                        </a>
                        <a href="#"
                            class="transition-all duration-150 flex justify-center items-center h-10 sm:h-12 w-full rounded-xl px-2 gap-1.5" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;">
                            <img src="<?php echo e(asset('website/assets/icons/download-icon-white.svg')); ?>" alt=""
                                class="h-3 w-3 sm:h-4 sm:w-4">
                            <p class="text-xs sm:text-sm font-semibold text-center text-white">
                                <?php echo e(__('Download for Android')); ?>

                            </p>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="flex items-end justify-center">
            <img src="<?php echo e($mobileImage ?? asset('website/assets/images/stores-network/mobile.png')); ?>" alt=""
                class="bg-contain ">
        </div>
    </div>
</section>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\layouts\includes\takeLaundry.blade.php ENDPATH**/ ?>