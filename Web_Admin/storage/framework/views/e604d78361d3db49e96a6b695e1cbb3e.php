<?php
    $menuItems = [
        [
            'label' => 'Overview',
            'icon' => 'user.svg',
            'route' => 'web.overview',
        ],
        [
            'label' => 'My Orders',
            'icon' => 'calendar2.svg',
            'route' => 'web.my.orders',
        ],
        [
            'label' => 'Addresses',
            'icon' => 'map-pin-gray.svg',
            'route' => 'web.addresses',
        ],
        [
            'label' => 'Favorites',
            'icon' => 'heart.svg',
            'route' => 'web.favourite',
        ],
        [
            'label' => 'Settings',
            'icon' => 'settings.svg',
            'route' => 'web.settings',
        ],
    ];
?>



<?php $__env->startSection('content'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="max-w-2lg mx-auto mt-4 px-4 py-3 rounded-[14px] bg-[#E6F4F1] text-green-800">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 w-full h-full bg-gradient-to-tl from-[#083744] via-[#156172] to-[#028090] text-white  z-50"
        id="sidebar">
        <div class="h-full w-full flex justify-center items-center flex-col relative">

            <button class="absolute top-6 right-6 border h-10 w-10 border-white rounded-[12px] flex justify-center items-center"
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <ul class="space-y-4 flex flex-col items-center">

                <div class="flex flex-col gap-6 items-center">
                    <div class="flex flex-col ">

                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="rounded-full overflow-hidden w-24 h-24">
                                <img src="<?php echo e($user->profile_photo_path ?? asset('website/assets/images/dashboard/user.png?v=2')); ?>"
                                    alt="" class="w-full h-full object-cover">
                            </div>

                            <div class="flex justify-center items-center flex-col p-3 rounded-[14px] bg-white/50">
                                <p class="text-lg font-semibold text-center text-neutral-700">
                                    <?php echo e($user->name ?? 'John Doe'); ?>

                                </p>
                                <p class="text-sm text-left text-neutral-500"><?php echo e($user->email ?? 'john.doe@email.com'); ?></p>

                                <div
                                    class="flex justify-center items-center  w-fit h-[30px] gap-1 p-2 rounded-[25px] bg-neutral-900 mt-[15px]">
                                    <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt=""
                                        class="h-3 w-3">
                                    <p class="text-xs text-left text-[#eab800]"><?php echo e(__('Premium Member')); ?></p>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                <li onclick="toggleSidebar()">
                    <a href="#" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Home')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Services')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Nearest Store')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('FAQ')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Contact')); ?></a>
                </li>
            </ul>
        </div>
    </div>


    <main style="background-color:#FAF7F2;">

        <!-- manage addresses area -->
        <section class="max-w-2lg mx-auto pt-[60px] pb-[80px] px-4 xl:px-0 space-y-5 md:space-y-10">

            <div class="w-full">
                <p class="text-2xl md:text-4xl font-semibold text-left text-gray-700">
                    <?php echo e(__('My Dashboard')); ?>

                </p>
                <p class="text-base md:text-xl text-left text-gray-500">
                    <?php echo e(__('Manage your orders, addresses, and preferences')); ?>

                </p>
            </div>


            <div class="w-full grid grid-cols-12 gap-6">
                <div class="hidden lg:block col-span-12 lg:col-span-4 ">
                    <div class="flex flex-col gap-6 items-center p-4 rounded-[14px] sticky top-24 bg-white">
                        <div class="flex flex-col ">

                            <div class="flex flex-col items-center justify-center gap-6">
                                <div class="rounded-full overflow-hidden w-32 h-32">
                                    <img src="<?php echo e($user->profile_photo_path ?? asset('website/assets/images/dashboard/user.png?v=2')); ?>"
                                        alt="" class="w-full h-full object-cover">
                                </div>

                                <div class="flex justify-center items-center flex-col">
                                    <p class="text-lg font-semibold text-center text-neutral-700">
                                        <?php echo e($user->name ?? 'John Doe'); ?>

                                    </p>
                                    <p class="text-base text-left text-neutral-500">
                                        <?php echo e($user->email ?? 'john.doe@email.com'); ?></p>

                                    <div
                                        class="flex justify-center items-center  w-fit h-[30px] gap-1 p-2 rounded-[25px] bg-neutral-900 mt-[15px]">
                                        <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt=""
                                            class="h-3 w-3">
                                        <p class="text-xs text-left text-[#eab800]"><?php echo e(__('Premium Member')); ?></p>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="space-y-[10px] w-full">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isActive = request()->routeIs($item['route']);
                                ?>

                                <a href="<?php echo e(route($item['route'])); ?>"
                                    class="px-5 py-3.5 rounded-[14px] flex justify-start items-center gap-[15px] w-full group transition-all duration-150
                  <?php echo e($isActive ? 'bg-[linear-gradient(135deg,#028090,#00A896)]' : 'bg-[#FAF7F2] hover:bg-[linear-gradient(135deg,#028090,#00A896)]'); ?>">

                                    <img src="<?php echo e(asset('website/assets/icons/' . $item['icon'])); ?>"
                                        class="w-5 h-auto
                        <?php echo e($isActive ? 'brightness-0 invert' : 'group-hover:brightness-0 group-hover:invert'); ?>">

                                    <p
                                        class="text-base font-medium transition-all duration-150
                      <?php echo e($isActive ? 'text-white' : 'text-neutral-700 group-hover:text-white'); ?>">
                                        <?php echo e(__($item['label'])); ?>

                                    </p>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <form method="POST" action="<?php echo e(route('web.logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                    class="px-5 py-3.5 rounded-[14px] flex justify-start items-center gap-[15px] bg-[#FAF7F2] w-full group transition-all duration-150 hover:bg-red-600">

                                    <img src="<?php echo e(asset('website/assets/icons/sign-out.svg')); ?>"
                                        class="w-5 h-auto group-hover:brightness-0 group-hover:invert">

                                    <p
                                        class="text-base font-medium text-neutral-700 group-hover:text-white transition-all duration-150">
                                        <?php echo e(__('Sign Out')); ?>

                                    </p>
                                </button>
                            </form>

                        </div>


                    </div>
                </div>


                <div class=" col-span-12 lg:col-span-8 flex flex-col gap-6">

                    <!-- widgets -->
                    <div class="flex justify-between items-center gap-6 flex-nowrap overflow-x-auto scrollbar-hide">
                        <!-- widget 1 -->
                        <div class="p-4 flex justify-between items-start gap-4 w-full min-w-52 rounded-[14px]  bg-white">
                            <div class="flex flex-col justify-between">
                                <p class="text-base text-gray-700"><?php echo e(__('Total Orders')); ?></p>
                                <p class="text-[28px] font-bold text-gray-700"><?php echo e($orders->count()); ?>

                                </p>

                            </div>
                            <div class="h-16 w-16 bg-[#E6F4F1] flex items-center justify-center rounded-full">
                                <img src="<?php echo e(asset('website/assets/icons/calendar.svg')); ?>" alt="" class="w-6 h-6">
                            </div>
                        </div>

                        <!-- widget 2 -->
                        <div class="p-4 flex justify-between items-start gap-4 w-full min-w-52 rounded-[14px]  bg-white">
                            <div class="flex flex-col justify-between">
                                <p class="text-base text-gray-700"><?php echo e(__('In Progress')); ?></p>
                                <p class="text-[28px] font-bold text-gray-700">
                                    <?php echo e($orders->where('order_status', '!=', 'Delivered')->where('order_status', '!=', 'Cancelled')->count()); ?>

                                </p>

                            </div>
                            <div class="h-16 w-16 bg-aqua-50 flex items-center justify-center rounded-full">
                                <img src="<?php echo e(asset('website/assets/icons/clock-blue.svg')); ?>" alt=""
                                    class="w-6 h-6">
                            </div>
                        </div>

                        <!-- widget 3 -->
                        <div class="p-4 flex justify-between items-start gap-4 w-full min-w-52 rounded-[14px]  bg-white">
                            <div class="flex flex-col justify-between">
                                <p class="text-base text-gray-700"><?php echo e(__('Completed')); ?></p>
                                <p class="text-[28px] font-bold text-gray-700">
                                    <?php echo e($orders->where('order_status', 'Delivered')->count()); ?>

                                </p>

                            </div>
                            <div class="h-16 w-16 bg-[#E6F4F1] flex items-center justify-center rounded-full">
                                <img src="<?php echo e(asset('website/assets/icons/check-green.svg')); ?>" alt=""
                                    class="w-6 h-6">
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders-->
                    <div class="rounded-[20px] p-6 bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] flex flex-col gap-[15px]">
                        <div class="flex justify-between items-center">
                            <p class="text-base font-semibold text-left text-neutral-700">
                                <?php echo e(__('Recent Orders')); ?>

                            </p>
                            <a href="<?php echo e(route('web.my.orders')); ?>">
                                <p class="text-sm font-medium text-left text-neutral-700"><?php echo e(__('View All')); ?></p>
                            </a>

                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <!-- card -->
                            <div class="p-3 flex justify-between items-start rounded-[14px] gap-4 border border-[#E2E8F0]">
                                <div class="w-14 h-14 bg-[#E6F4F1]  justify-center items-center rounded hidden sm:flex">
                                    <img src="<?php echo e(asset('website/assets/icons/calendar.svg')); ?>" alt=""
                                        class="w-6 h-6">
                                </div>
                                <div class="flex-1 h-full">
                                    <div class="flex justify-between items-center">
                                        <div
                                            class="w-12 h-12 bg-[#E6F4F1]  justify-center items-center rounded flex  sm:hidden">
                                            <img src="<?php echo e(asset('website/assets/icons/calendar.svg')); ?>" alt=""
                                                class="w-6 h-6">
                                        </div>


                                        <div class="block sm:hidden">
                                            <div
                                                class="flex justify-center items-center px-2.5 py-[5px] rounded-[20px] bg-[#0099df]">
                                                <p class="text-[8px] sm:text-[10px] text-left text-white">
                                                    <?php echo e(__($order->order_status ?? 'In Progress')); ?></p>
                                            </div>
                                            <p class="text-[10px] sm:text-xs font-medium text-right text-neutral-500">
                                                <?php echo e($order->created_at->locale(app()->getLocale())->translatedFormat('M d, Y')); ?></p>
                                            <p class="text-xs sm:text-sm font-semibold text-right text-gray-900">
                                                <?php echo e(currencyPosition($order->total_amount ?? 0, 2)); ?></p>
                                        </div>


                                    </div>
                                    <p class="text-sm font-medium  text-neutral-900">
                                        <?php echo e($order->order_code ?? $order->id); ?>

                                    </p>
                                    <p class="text-sm  text-neutral-500">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->address): ?>
                                        <?php echo e($order->address->address_name ?? ($order->address->address_line ?? __('Order'))); ?>

                                        <?php else: ?>
                                            <?php echo e(__('Order')); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                    <p class="text-xs font-medium  text-neutral-700">
                                        <?php echo e(currencyPosition($order->total_amount ?? 0, 2)); ?> <span
                                            class="text-xs text-left text-neutral-500"><?php echo e($order->products->count() ?? 0); ?>

                                            <?php echo e(__('Items')); ?></span> </p>
                                </div>
                                <div class="hidden sm:block">
                                    <div
                                        class="flex justify-center items-center px-2.5 py-[5px] rounded-[20px]
                                        <?php if($order->order_status == 'Delivered'): ?> bg-[#34c759]
                                        <?php elseif($order->order_status == 'Cancelled'): ?> bg-red-500
                                        <?php elseif($order->order_status == 'On Going'): ?> bg-[#fbbc04]
                                        <?php else: ?> bg-[#0099df] <?php endif; ?>">
                                        <p class="text-[10px] text-left text-white">
                                            <?php echo e(__($order->order_status ?? 'In Progress')); ?>

                                        </p>
                                    </div>
                                    <p class="text-xs font-medium text-right text-neutral-500">
                                        <?php echo e($order->created_at->locale(app()->getLocale())->translatedFormat('M d, Y')); ?></p>
                                    <p class="text-sm font-semibold text-right text-gray-900">
                                        <?php echo e(currencyPosition($order->total_amount ?? 0, 2)); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-neutral-500">
                                <p><?php echo e(__('No orders yet.')); ?> <a href="<?php echo e(route('web.service')); ?>"
                                        class="text-[#028090] font-medium"><?php echo e(__('Book your first order')); ?></a></p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>


                    <!-- saved address -->
                    <div class="rounded-[20px] p-6 bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] flex flex-col gap-[15px]">
                        <div class="flex justify-between items-center">
                            <p class="text-base font-semibold text-left text-neutral-700">
                                <?php echo e(__('Saved Addresses')); ?>

                            </p>
                            <a href="<?php echo e(route('web.addresses')); ?>">
                                <p class="text-sm font-medium text-left text-neutral-700"><?php echo e(__('View All')); ?></p>
                            </a>

                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="p-3 sm:p-6 flex justify-start items-start gap-5 rounded-[14px] border border-[#E2E8F0]">
                                <div class="w-14 h-14 bg-[#E6F4F1] justify-center items-center rounded hidden sm:flex">
                                    <img src="<?php echo e(asset('website/assets/icons/home.svg')); ?>" alt=""
                                        class="w-6 h-6">
                                </div>
                                <div class="flex flex-col gap-1 items-start">

                                    <div class="flex justify-between items-start sm:hidden w-full">
                                        <div class="w-12 h-12 bg-[#E6F4F1] justify-center items-center rounded flex ">
                                            <img src="<?php echo e(asset('website/assets/icons/home.svg')); ?>" alt=""
                                                class="w-6 h-6">
                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address->is_default): ?>
                                            <span
                                                class="inline-flex justify-start items-center gap-2 bg-[#E6F4F1] px-2 py-1 rounded-full">
                                                <img src="<?php echo e(asset('website/assets/icons/star.svg')); ?>" alt="">
                                                <p class="text-xs text-left text-[#028090]"><?php echo e(__('Default')); ?></p>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <div class="flex justify-start items-center gap-2 mb-3">
                                        <p class="text-base font-medium text-left text-gray-900">
                                            <?php echo e($address->address_name ?? 'Home'); ?></p>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address->is_default): ?>
                                            <span
                                                class="hidden sm:inline-flex justify-start items-center gap-2 bg-[#E6F4F1] px-2 py-1 rounded-full">
                                                <img src="<?php echo e(asset('website/assets/icons/star.svg')); ?>" alt="">
                                                <p class="text-xs text-left text-[#028090]"><?php echo e(__('Default')); ?></p>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <p class="text-sm text-left text-gray-700 flex justify-start items-center gap-1">
                                        <img src="<?php echo e(asset('website/assets/icons/map-pin-gray.svg')); ?>" alt=""
                                            class="h-[14px] w-[14px]">
                                        <?php
                                            $addressParts = [];

                                            $fields = [
                                                'address_line' => null,
                                                'road_no' => __('Road') . ': ',
                                                'house_no' => __('House') . ': ',
                                                'flat_no' => __('Flat') . ': ',
                                                'block' => __('Block') . ': ',
                                                'area' => null,
                                                'post_code' => null,
                                            ];

                                            foreach ($fields as $field => $label) {
                                                if (!empty($address->$field)) {
                                                    $addressParts[] = $label
                                                        ? $label . $address->$field
                                                        : $address->$field;
                                                }
                                            }
                                        ?>
                                        <?php echo e(implode(', ', $addressParts)); ?>

                                    </p>
                                    <p class="text-sm text-left text-gray-500  flex justify-start items-center gap-1">
                                        <img src="<?php echo e(asset('website/assets/icons/phone-gray.svg')); ?>" alt=""
                                            class="h-[14px] w-[14px]">
                                        <?php echo e($address->phone ?? ($user->mobile ?? '')); ?>

                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-neutral-500">
                                <p><?php echo e(__('No addresses saved.')); ?> <a href="<?php echo e(route('web.addresses')); ?>"
                                        class="text-[#028090] font-medium"><?php echo e(__('Add an address')); ?></a></p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                </div>

            </div>




        </section>


        <!-- get started -->
        <section style="background:linear-gradient(135deg,#083744,#156172);" class="bg-[linear-gradient(135deg,#083744,#156172)] ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl:1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        <?php echo e(__('Ready to Get Started?')); ?>

                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        <?php echo e(__('Book your first order and experience hassle-free dry cleaning service')); ?>

                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <a href="<?php echo e(route('web.service')); ?>">
                            <p><?php echo e(__('Book Now')); ?></p>
                        </a>

                        <img src="<?php echo e(asset('website/assets/icons/arrow-left-green.svg')); ?>" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p><?php echo e(__('Become a Partner')); ?></p>
                    </button>
                </div>
            </div>
        </section>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\overview.blade.php ENDPATH**/ ?>