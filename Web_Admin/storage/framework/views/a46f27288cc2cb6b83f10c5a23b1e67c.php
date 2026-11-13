<?php $__env->startSection('content'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <!-- breadcrumb -->
        <section
            style="background:linear-gradient(135deg,#083744,#156172),url('<?php echo e(asset('website/assets/images/header/breadcrumb.png')); ?>');background-size:cover;background-position:center;" class="rs-breadcrumb-area bg-[linear-gradient(135deg,#083744,#156172)] h-[260px] w-full bg-[url('<?php echo e(asset('website/assets/images/header/breadcrumb.png')); ?>')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px] md:text-4xl text-white font-semibold leading-[140%]">
                    <?php echo e(__('My Addresses')); ?>

                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="<?php echo e(route('web.settings')); ?>"
                        class="text-base md:text-lg text-white font-normal leading-[100%]"><?php echo e(__('Dashboard')); ?>

                        / </a>
                    <a href="<?php echo e(route('web.addresses')); ?>"
                        class="text-base md:text-lg text-white font-normal leading-[100%]"><?php echo e(__('Addresses')); ?></a>
                </div>
            </div>
        </section>

        <!-- Add Address Modal -->
        <div id="addAddressModal"
            class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-[20px] p-6 w-full max-w-md my-auto">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold"><?php echo e(__('Add New Address')); ?></h3>
                    <button onclick="toggleModal('addAddressModal')"
                        class="transition-transform duration-300 hover:rotate-90 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="<?php echo e(route('web.addresses.store')); ?>" method="POST"
                    class="overflow-y-auto max-h-[calc(100vh-200px)]">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Label')); ?></label>
                        <input type="text" name="address_name" placeholder="<?php echo e(__('Example')); ?>: <?php echo e(__('Home')); ?>, <?php echo e(__('Office')); ?>..."
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('House No')); ?></label>
                        <input type="text" name="house_no" placeholder="123"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Flat No')); ?></label>
                        <input type="text" name="flat_no" placeholder="1A"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Road No')); ?></label>
                        <input type="text" name="road_no" placeholder="<?php echo e(__('Road')); ?> <?php echo e(__('main road example')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Block')); ?></label>
                        <input type="text" name="block" placeholder="<?php echo e(__('Block')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Area')); ?></label>
                        <input type="text" name="area" placeholder="<?php echo e(__('Area')); ?>" required
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Address Line')); ?></label>
                        <input type="text" name="address_line" placeholder="<?php echo e(__('Address example')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Post Code')); ?></label>
                        <input type="text" name="post_code" placeholder="10003"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-[30px]">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Phone Number')); ?></label>
                        <input type="text" name="phone" placeholder="+1 (555) 545-5421"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <button type="submit"
                        class="rs-add-new-address-btn text-sm bg-[linear-gradient(135deg,#028090,#00A896)] text-white h-[48px] text-center leading-[48px] w-[100%] rounded-[14px]">
                        <?php echo e(__('Save Address')); ?>

                    </button>
                </form>
            </div>
        </div>

        <!-- <?php echo e(__('Edit')); ?> Address Modal -->
        <div id="editAddressModal"
            class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-[20px] p-6 w-full max-w-md my-auto">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold"><?php echo e(__('Edit')); ?> <?php echo e(__('Address')); ?></h3>
                    <button onclick="toggleModal('editAddressModal')"
                        class="transition-transform duration-300 hover:rotate-90 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="editForm" method="POST" class="overflow-y-auto max-h-[calc(100vh-200px)]">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Label')); ?></label>
                        <input type="text" name="address_name" id="edit_address_name"
                            placeholder="<?php echo e(__('Example')); ?>: <?php echo e(__('Home')); ?>, <?php echo e(__('Office')); ?>..."
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('House No')); ?></label>
                        <input type="text" name="house_no" id="edit_house_no" placeholder="123"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Flat No')); ?></label>
                        <input type="text" name="flat_no" id="edit_flat_no" placeholder="1A"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Road No')); ?></label>
                        <input type="text" name="road_no" id="edit_road_no" placeholder="<?php echo e(__('Road')); ?> <?php echo e(__('main road example')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Block')); ?></label>
                        <input type="text" name="block" id="edit_block" placeholder="<?php echo e(__('Block')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Area')); ?></label>
                        <input type="text" name="area" id="edit_area" placeholder="<?php echo e(__('Area')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Address Line')); ?></label>
                        <input type="text" name="address_line" id="edit_address_line" placeholder="<?php echo e(__('Address example')); ?>"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Post Code')); ?></label>
                        <input type="text" name="post_code" id="edit_post_code" placeholder="10003"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-[30px]">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Phone Number')); ?></label>
                        <input type="text" name="phone" id="edit_phone" placeholder="+1 (555) 545-5421"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <button type="submit"
                        class="rs-add-new-address-btn text-sm bg-[linear-gradient(135deg,#028090,#00A896)] text-white h-[48px] text-center leading-[48px] w-[100%] rounded-[14px]">
                        <?php echo e(__('Update Address')); ?>

                    </button>
                </form>
            </div>
        </div>

        <!-- manage addresses area -->
        <section class="rs-manage-addresses-section pt-[60px] pb-[80px] px-4 xl:px-0">
            <div class="rs-manage-addresses-area max-w-2lg mx-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                    <div class="max-w-2lg mx-auto mb-6 bg-[#E6F4F1] border border-green-400 text-[#156172] px-4 py-3 rounded">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <button type="button" onclick="toggleModal('addAddressModal')"
                    class="rs-add-new-address-btn text-sm bg-[linear-gradient(135deg,#028090,#00A896)] text-white mb-[30px] h-[48px] inline-flex items-center justify-center leading-[48px] w-[190px] sm:w-[290px] rounded-[14px]">
                    <i class="fa-solid fa-plus mr-[8px]"></i>
                    <?php echo e(__('Add New Address')); ?>

                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div
                        class="rs-manage-addresses-box bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] rounded-[14px] p-[24px] mb-[24px] border border-transparent hover:border-[1.5px] hover:border-[#028090] transition-all duration-300">
                        <div class="rs-manage-addresses-content flex gap-[20px] border-b-[1.5px] border-[#E2E8F0] mb-[20px]">
                            <div
                                class="rs-manage-addresses-icon flex-none w-[48px] h-[48px] sm:w-[56px] sm:h-[56px] flex bg-[#E6F4F1] rounded-[12px]">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($address->label) && strtolower($address->label) === 'home'): ?>
                                    <img src="<?php echo e(asset('website/assets/icons/home-icon.svg')); ?>" alt=""
                                        class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                                <?php elseif(isset($address->label) && strtolower($address->label) === 'office'): ?>
                                    <img src="<?php echo e(asset('website/assets/icons/city-icon.svg')); ?>" alt=""
                                        class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('website/assets/icons/location-green.svg')); ?>" alt=""
                                        class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="rs-manage-addresses-info">
                                <div class="rs-manage-addresses-info-top flex text-center gap-[12px] mb-[12px]">
                                    <span
                                        class="text-neutral-900 text-base leading-[140%] font-medium"><?php echo e($address->address_name ?? __('Address')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address->is_default): ?>
                                        <span
                                            class="text-[#028090] rounded-[25px] border-[1.5px] border-[#E2E8F0] flex gap-[4px] justify-center bg-[#E6F4F1] h-[24px] w-[78px] leading-[24px] text-xs font-normal">
                                            <img class="h-[10px] w-[10px] mt-auto mb-auto"
                                                src="<?php echo e(asset('website/assets/icons/star.svg')); ?>" alt="">
                                            <?php echo e(__('Default')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div
                                    class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                    <img src="<?php echo e(asset('website/assets/icons/location.svg')); ?>" alt="">
                                    <?php echo e(implode(', ', array_filter([$address->house_no, $address->flat_no, $address->road_no, $address->block, $address->area, $address->address_line, $address->address_line2, $address->post_code]))); ?>

                                </div>
                                <a href="tel:<?php echo e($address->phone ?? ($user->mobile ?? '')); ?>"
                                    class="leading-[140%] text-sm mb-[24px] font-normal flex gap-[6px] text-neutral-500">
                                    <img src="<?php echo e(asset('website/assets/icons/call.svg')); ?>" alt="" srcset="">
                                    <?php echo e($address->phone ?? ($user->mobile ?? 'N/A')); ?>

                                </a>
                            </div>
                        </div>
                        <div class="rs-manage-addresses-box-bottom-area">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$address->is_default): ?>
                                <div class="rs-manage-addresses-btn flex flex-wrap gap-[15px] md:flex-nowrap">
                                    <form action="<?php echo e(route('web.addresses.setDefault', $address->id)); ?>" method="POST"
                                        style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[124px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-[#028090] transition-all duration-300">
                                            <?php echo e(__('Set as Default')); ?>

                                        </button>
                                    </form>
                                    <button type="button" data-id="<?php echo e($address->id); ?>"
                                        data-name="<?php echo e($address->address_name); ?>" data-house="<?php echo e($address->house_no); ?>"
                                        data-flat="<?php echo e($address->flat_no); ?>" data-road="<?php echo e($address->road_no); ?>"
                                        data-block="<?php echo e($address->block); ?>" data-area="<?php echo e($address->area); ?>"
                                        data-address="<?php echo e($address->address_line); ?>" data-post="<?php echo e($address->post_code); ?>"
                                        data-phone="<?php echo e($address->phone); ?>"
                                        class="edit-address-btn leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-[#028090] transition-all duration-300">
                                        <img class="h-[12px] w-[12px] mt-auto mb-auto"
                                            src="<?php echo e(asset('website/assets/icons/edit-btn.svg')); ?>" alt="">
                                        <?php echo e(__('Edit')); ?>

                                    </button>
                                    <form action="<?php echo e(route('web.addresses.destroy', $address->id)); ?>" method="POST"
                                        style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="leading-[40px] text-xs font-medium flex gap-[5px] text-danger-600 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-danger-600 transition-all duration-300">
                                            <img class="h-[12px] w-[12px] mt-auto mb-auto"
                                                src="<?php echo e(asset('website/assets/icons/trash-red.svg')); ?>" alt="">
                                            <?php echo e(__('Delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="rs-manage-addresses-edit-btn">
                                    <button type="button" data-id="<?php echo e($address->id); ?>"
                                        data-name="<?php echo e($address->address_name); ?>" data-house="<?php echo e($address->house_no); ?>"
                                        data-flat="<?php echo e($address->flat_no); ?>" data-road="<?php echo e($address->road_no); ?>"
                                        data-block="<?php echo e($address->block); ?>" data-area="<?php echo e($address->area); ?>"
                                        data-address="<?php echo e($address->address_line); ?>" data-post="<?php echo e($address->post_code); ?>"
                                        data-phone="<?php echo e($address->phone); ?>"
                                        class="edit-address-btn leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-[#028090] transition-all duration-300">
                                        <img class="h-[12px] w-[12px] mt-auto mb-auto"
                                            src="<?php echo e(asset('website/assets/icons/edit-btn.svg')); ?>" alt="">
                                        <?php echo e(__('Edit')); ?>

                                    </button>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bg-white p-8 rounded-[20px] text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo e(__('No')); ?> <?php echo e(__('Addresses')); ?> <?php echo e(__('Saved')); ?></h3>
                        <p class="text-gray-500 mb-6"><?php echo e(__("You haven't added any delivery addresses yet.")); ?></p>
                        <button type="button" onclick="toggleModal('addAddressModal')"
                            class="inline-block bg-[linear-gradient(135deg,#028090,#00A896)] text-white px-6 py-3 rounded-[12px] hover:bg-[#156172] transition">
                            <?php echo e(__('Add New Address')); ?>

                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <script>
            function toggleModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.toggle('hidden');
                }
            }

            // Handle edit button clicks
            document.querySelectorAll('.edit-address-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name || '';
                    const house = this.dataset.house || '';
                    const flat = this.dataset.flat || '';
                    const road = this.dataset.road || '';
                    const block = this.dataset.block || '';
                    const area = this.dataset.area || '';
                    const address = this.dataset.address || '';
                    const post = this.dataset.post || '';
                    const phone = this.dataset.phone || '';

                    document.getElementById('edit_address_name').value = name;
                    document.getElementById('edit_house_no').value = house;
                    document.getElementById('edit_flat_no').value = flat;
                    document.getElementById('edit_road_no').value = road;
                    document.getElementById('edit_block').value = block;
                    document.getElementById('edit_area').value = area;
                    document.getElementById('edit_address_line').value = address;
                    document.getElementById('edit_post_code').value = post;
                    document.getElementById('edit_phone').value = phone;
                    document.getElementById('editForm').action = `/addresses/${id}`;
                    toggleModal('editAddressModal');
                });
            });
        </script>
    <?php else: ?>
        <section class="py-20 text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4"><?php echo e(__('Please Login')); ?></h2>
            <p class="text-gray-600 mb-6"><?php echo e(__('You need to login to view your addresses.')); ?></p>
            <a href="<?php echo e(route('login')); ?>" class="bg-[linear-gradient(135deg,#028090,#00A896)] text-white px-6 py-3 rounded-[12px] hover:bg-[#156172] transition">
                <?php echo e(__('Login')); ?>

            </a>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\addresses.blade.php ENDPATH**/ ?>