<?php $__env->startSection('content'); ?>
    <!-- manage-address-modal -->
    <section id="role" class="modal_container">

        <!-- backdrop -->
        <div onclick="toggleModal('role')" class="modal_backdrop"></div>

        <!-- modal content -->
        <div class="modal_content">
            <form action="#" class="rs-manage-addresses-form">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold"><?php echo e(__('Add New Address')); ?></h3>
                    <button type="button" onclick="toggleModal('role')"
                        class="transition-transform duration-300 hover:rotate-90">
                        <img src="<?php echo e(asset('website/assets/icons/close.svg')); ?>" alt="">
                    </button>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Label')); ?></label>
                    <input type="text" placeholder="<?php echo e(__('Address label example')); ?>"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Street Address')); ?></label>
                    <input type="text" placeholder="<?php echo e(__('Address example')); ?>"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('City')); ?></label>
                        <input type="text" placeholder="<?php echo e(__('City example')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('State')); ?></label>
                        <input type="text" placeholder="NY"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('ZIP Code')); ?></label>
                    <input type="text" placeholder="10003"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-[30px]">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Phone Number')); ?></label>
                    <input type="text" placeholder="+1 (555) 545-5421"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <button
                    class="rs-add-new-address-btn text-sm bg-[linear-gradient(135deg,#028090,#00A896)] text-white h-[48px] text-center leading-[48px] w-[100%] rounded-[14px]">
                    <?php echo e(__('Save Address')); ?>

                </button>
            </form>
        </div>
    </section>

    <!-- navbar -->
    


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
        <!-- breadcrumb -->
        <section
            style="background:linear-gradient(135deg,#083744,#156172),url('<?php echo e(asset('website/assets/images/header/breadcrumb.png')); ?>');background-size:cover;background-position:center;" class="rs-breadcrumb-area bg-[linear-gradient(135deg,#083744,#156172)] h-[260px] w-full bg-[url('<?php echo e(asset('website/assets/images/header/breadcrumb.png')); ?>')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    <?php echo e(__('Find Stores Near You!')); ?>

                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]"><?php echo e(__('Home / ')); ?></a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]"><?php echo e(__('Stores')); ?></a>
                </div>
            </div>
        </section>

        <!-- Services area -->
        <section class="rs-services-section pt-[60px] pb-[100px] px-4 xl:px-0 bg-[#FAF7F2]">
            <div class="rs-services-area max-w-2lg  mx-auto grid grid-cols-1 md:grid-cols-12 gap-[24px]">

                <div class="rs-find-store-area col-span-12">
                    <div class="rs-services-right-top flex items-center justify-between mb-6 flex-wrap gap-4">
                        <p class="text-lg font-normal leading-[100%] text-neutral-500"><?php echo e(__('Found :count Stores', ['count' => 5])); ?></p>
                    </div>
                    <div class="rs-services-wrapper grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="rs-services-box rounded-[20px] overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="<?php echo e(asset('website/assets/images/stores/store-01.jpg')); ?>"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    20% <?php echo e(__('OFF')); ?>

                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="<?php echo e(asset('website/assets/icons/love-icon.svg')); ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-[#E2E8F0] rounded-[12px] p-1">
                                        <img class="object-contain w-full h-full"
                                            src="<?php echo e(asset('website/assets/images/stores/store-1.png')); ?>" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            CleanPro Express
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                            <span class="text-sm text-left text-neutral-500">(624)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    <?php echo e(__('Premium quality dry cleaning services with same-day delivery')); ?>

                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Luxury Service')); ?></span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Leather Care')); ?></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/location.svg')); ?>"
                                            alt="">2.1
                                        <?php echo e(__('km')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/clock-gray.svg')); ?>"
                                            alt="">24
                                        <?php echo e(__('hours')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/truck.svg')); ?>" alt=""><?php echo e(__('Free Delivery')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/dolar.svg')); ?>" alt=""><?php echo e(__('Min')); ?>

                                        $15</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white justify-center gap-[10px] ml-auto text-xs text-[#028090] font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-[#E2E8F0] rounded-[14px]"><?php echo e(__('Book Now')); ?> <img class="filter brightness-0 invert"
                                            src="<?php echo e(asset('website/assets/icons/green-right-arrow.svg')); ?>" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-[20px] overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="<?php echo e(asset('website/assets/images/stores/store-02.jpg')); ?>"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    30% OFF
                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="<?php echo e(asset('website/assets/icons/love-icon.svg')); ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-[#E2E8F0] rounded-[12px] p-1">
                                        <img class="object-contain w-full h-full"
                                            src="<?php echo e(asset('website/assets/images/stores/store-1.png')); ?>" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            FreshFold Dry Cleaning
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.8</span>
                                            <span class="text-sm text-left text-neutral-500">(512)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                     <?php echo e(__('Professional dry cleaning with eco-friendly washing solutions.')); ?>

                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Eco Wash')); ?></span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Express Clean')); ?></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/location.svg')); ?>"
                                            alt="">1.8
                                        <?php echo e(__('km')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/clock-gray.svg')); ?>"
                                            alt="">12
                                        <?php echo e(__('hours')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/truck.svg')); ?>" alt=""><?php echo e(__('Free Delivery')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/dolar.svg')); ?>" alt=""><?php echo e(__('Min')); ?>

                                        $12</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white justify-center gap-[10px] ml-auto text-xs text-[#028090] font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-[#E2E8F0] rounded-[14px]"><?php echo e(__('Book Now')); ?> <img class="filter brightness-0 invert"
                                            src="<?php echo e(asset('website/assets/icons/green-right-arrow.svg')); ?>" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-[20px] overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="<?php echo e(asset('website/assets/images/stores/store-03.jpg')); ?>"
                                    alt="">
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="<?php echo e(asset('website/assets/icons/love-icon.svg')); ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-[#E2E8F0] rounded-[12px] p-1">
                                        <img class="object-contain w-full h-full"
                                            src="<?php echo e(asset('website/assets/images/stores/store-1.png')); ?>" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            QuickWash Pro
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                            <span class="text-sm text-left text-neutral-500">(62)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    <?php echo e(__('Fast and reliable dry cleaning service with guaranteed next-day delivery.')); ?>

                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Speed Service')); ?></span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Ironing Included')); ?></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/location.svg')); ?>"
                                            alt="">3.1
                                        <?php echo e(__('km')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/clock-gray.svg')); ?>"
                                            alt="">24
                                        <?php echo e(__('hours')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/truck.svg')); ?>" alt=""><?php echo e(__('Free Delivery')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/dolar.svg')); ?>" alt=""><?php echo e(__('Min')); ?>

                                        $10</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white justify-center gap-[10px] ml-auto text-xs text-[#028090] font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-[#E2E8F0] rounded-[14px]"><?php echo e(__('Book Now')); ?> <img class="filter brightness-0 invert"
                                            src="<?php echo e(asset('website/assets/icons/green-right-arrow.svg')); ?>" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-[20px] overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="<?php echo e(asset('website/assets/images/stores/store-04.jpg')); ?>"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    15% <?php echo e(__('OFF')); ?>

                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="<?php echo e(asset('website/assets/icons/love-icon.svg')); ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-[#E2E8F0] rounded-[12px] p-1">
                                        <img class="object-contain w-full h-full"
                                            src="<?php echo e(asset('website/assets/images/stores/store-1.png')); ?>" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            PureCare Dry Cleaning Hub
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.8</span>
                                            <span class="text-sm text-left text-neutral-500">(842)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    <?php echo e(__('Premium washing and folding for all types of fabrics — handled with care.')); ?>

                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm">
                                        <?php echo e(__('Fabric Safe')); ?></span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm">
                                        <?php echo e(__('Steam Press')); ?></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/location.svg')); ?>"
                                            alt="">2.1
                                        <?php echo e(__('km')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/clock-gray.svg')); ?>"
                                            alt="">
                                        <?php echo e(__('Same Day')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/truck.svg')); ?>" alt=""><?php echo e(__('Free Delivery')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/dolar.svg')); ?>" alt=""><?php echo e(__('Min')); ?>

                                        $18</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white justify-center gap-[10px] ml-auto text-xs text-[#028090] font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-[#E2E8F0] rounded-[14px]"><?php echo e(__('Book Now')); ?> <img class="filter brightness-0 invert"
                                            src="<?php echo e(asset('website/assets/icons/green-right-arrow.svg')); ?>" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-[20px] overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="<?php echo e(asset('website/assets/images/stores/store-05.jpg')); ?>"
                                    alt="">
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="<?php echo e(asset('website/assets/icons/love-icon.svg')); ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-[#E2E8F0] rounded-[12px] p-1">
                                        <img class="object-contain w-full h-full"
                                            src="<?php echo e(asset('website/assets/images/stores/store-1.png')); ?>" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            Urban Wash Co.
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">5.0</span>
                                            <span class="text-sm text-left text-neutral-500">(93)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    <?php echo e(__('Luxury cleaning and garment care for your daily and designer wear.')); ?>

                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm">
                                        <?php echo e(__('Designer Care')); ?></span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm">
                                        <?php echo e(__('Delicate Wash')); ?></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/location.svg')); ?>"
                                            alt="">1.5
                                        <?php echo e(__('km')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/clock-gray.svg')); ?>"
                                            alt="">48
                                        <?php echo e(__('hours')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/truck.svg')); ?>" alt=""><?php echo e(__('Free Delivery')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/dolar.svg')); ?>" alt=""><?php echo e(__('Min')); ?>

                                        $23</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white justify-center gap-[10px] ml-auto text-xs text-[#028090] font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-[#E2E8F0] rounded-[14px]"><?php echo e(__('Book Now')); ?> <img class="filter brightness-0 invert"
                                            src="<?php echo e(asset('website/assets/icons/green-right-arrow.svg')); ?>" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-[20px] overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="<?php echo e(asset('website/assets/images/stores/store-06.jpg')); ?>"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    20% <?php echo e(__('OFF')); ?>

                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="<?php echo e(asset('website/assets/icons/love-icon.svg')); ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-[#E2E8F0] rounded-[12px] p-1">
                                        <img class="object-contain w-full h-full"
                                            src="<?php echo e(asset('website/assets/images/stores/store-1.png')); ?>" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            CleanPro Express
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="<?php echo e(asset('website/assets/icons/star-gold.svg')); ?>" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.7</span>
                                            <span class="text-sm text-left text-neutral-500">(624)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    <?php echo e(__('Premium quality dry cleaning services with same-day delivery')); ?>

                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Luxury Service')); ?></span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-[#FAF7F2] rounded-sm"><?php echo e(__('Leather Care')); ?></span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/location.svg')); ?>"
                                            alt="">2.5
                                        <?php echo e(__('km')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/clock-gray.svg')); ?>"
                                            alt="">12
                                        <?php echo e(__('hours')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/truck.svg')); ?>" alt=""><?php echo e(__('Free Delivery')); ?></span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="<?php echo e(asset('website/assets/icons/dolar.svg')); ?>" alt=""><?php echo e(__('Min')); ?>

                                        $22</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white justify-center gap-[10px] ml-auto text-xs text-[#028090] font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-[#E2E8F0] rounded-[14px]"><?php echo e(__('Book Now')); ?> <img class="filter brightness-0 invert"
                                            src="<?php echo e(asset('website/assets/icons/green-right-arrow.svg')); ?>" alt=""></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>


        <!-- get started -->
        <section style="background:linear-gradient(135deg,#083744,#156172);" class="bg-[linear-gradient(135deg,#083744,#156172)] bottom-0">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
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
                        <p><?php echo e(__('Book Now')); ?></p>

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

<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\stores.blade.php ENDPATH**/ ?>