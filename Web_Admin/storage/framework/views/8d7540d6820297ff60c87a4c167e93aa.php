<?php $__env->startSection('content'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-[20px] shadow-lg text-center">
            <div
                class="text-4xl mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-pink-500 to-yellow-500">
                🗡️ <?php echo e(session('success')); ?> !
            </div>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-[20px] shadow-lg text-center">
            <div
                class="text-4xl mb-4 bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-pink-500 to-yellow-500">
                ❌ <?php echo e(session('error')); ?> !
            </div>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    <form action="<?php echo e(route('checkout.placeOrder')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <main style="background-color:#FAF7F2;" class="max-w-6xl mx-auto px-4 py-12 flex flex-col lg:flex-row gap-8">

            <!-- Left: Order Details -->
            <div class="flex-1 space-y-6">

                <!-- Delivery Address -->
                <div class="bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-6 rounded-[20px] border border-[#E2E8F0]">
                    <h3 class="font-semibold text-lg mb-4"><?php echo e(__('Delivery Address')); ?></h3>

                    <p id="no-address-message" class="text-red-500 mb-2 <?php echo e(!$addresses->isEmpty() ? 'hidden' : ''); ?>"><?php echo e(__('Please add a delivery address first.')); ?></p>

                    <div id="address-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $fullAddress = implode(
                                    ', ',
                                    array_filter([
                                        $address->house_no,
                                        $address->flat_no,
                                        $address->road_no,
                                        $address->block,
                                        $address->area,
                                        $address->address_line,
                                        $address->address_line2,
                                        $address->post_code,
                                    ]),
                                );
                            ?>

                            <div class="mb-3 flex items-start gap-3">
                                <input type="radio" name="address_id" id="address-<?php echo e($address->id); ?>"
                                    value="<?php echo e($address->id); ?>" <?php echo e($loop->first ? 'checked' : ''); ?> required>
                                <label for="address-<?php echo e($address->id); ?>"
                                    class="flex-1 border p-3 rounded-[12px] cursor-pointer hover:border-[#028090]">
                                    <h5 class="font-semibold"><?php echo e(ucfirst($address->address_name)); ?></h5>
                                    <span class="text-sm text-gray-500"><?php echo e($fullAddress); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address->delivery_note): ?>
                                        <p class="text-xs text-gray-400 italic"><?php echo e($address->delivery_note); ?>mm</p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button type="button" class="w-full mt-2 border py-2 rounded-[12px] text-sm hover:bg-gray-50"
                        onclick="document.getElementById('add-address-modal').classList.remove('hidden')"><?php echo e(__('Add New Address')); ?></button>
                </div>

                <!-- Pick-up Schedule -->
                <div class="bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-6 rounded-[20px] border border-[#E2E8F0]">
                    <h3 class="font-semibold text-lg mb-4"><?php echo e(__('Pick-up Schedule')); ?></h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="pick_date" class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('Pick-up Date')); ?></label>
                            <input type="date" name="pick_date" id="pick_date"
                                class="w-full border p-3 rounded-[12px] focus:ring-2 focus:ring-[#028090] focus:border-transparent"
                                min="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e(old('pick_date')); ?>" required placeholder="<?php echo e(__('Select pick-up date')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['pick_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div>
                            <label for="pick_hour" class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('Pick-up Time')); ?></label>
                            <input type="time" name="pick_hour" id="pick_hour"
                                class="w-full border p-3 rounded-[12px] focus:ring-2 focus:ring-[#028090] focus:border-transparent"
                                required value="<?php echo e(old('pick_hour')); ?>" step="60">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['pick_hour'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4"><?php echo e(__('Choose your preferred pick-up date and time slot.')); ?></p>
                </div>

                <!-- Delivery Schedule -->
                <div class="bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-6 rounded-[20px] border border-[#E2E8F0]">
                    <h3 class="font-semibold text-lg mb-4"><?php echo e(__('Delivery Schedule')); ?></h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('Delivery Date')); ?></label>
                            <input type="date" name="delivery_date" id="delivery_date"
                                class="w-full border p-3 rounded-[12px] focus:ring-2 focus:ring-[#028090] focus:border-transparent"
                                min="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e(old('delivery_date')); ?>" required placeholder="<?php echo e(__('Select delivery date')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['delivery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div>
                            <label for="delivery_hour" class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('Delivery Time')); ?></label>
                            <input type="time" name="delivery_hour" id="delivery_hour"
                                class="w-full border p-3 rounded-[12px] focus:ring-2 focus:ring-[#028090] focus:border-transparent"
                                required value="<?php echo e(old('delivery_hour')); ?>" step="60">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['delivery_hour'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4"><?php echo e(__('Choose your preferred delivery date and time slot.')); ?></p>
                </div>

                <!-- Order Items -->
                <div class="bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-6 rounded-[20px] border border-[#E2E8F0] space-y-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-lg"><?php echo e(__('Order Items')); ?></h3>
                        <span class="text-sm text-gray-500"><?php echo e(count($products)); ?> <?php echo e(__('items')); ?></span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceProducts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $service = $serviceProducts->first()->service; ?>
                        <div class="border p-4 rounded-[14px] mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo e(asset($service->thumbnailPath ?? 'website/assets/images/default.png')); ?>"
                                        class="w-10 h-10 rounded">
                                    <div>
                                        <h4 class="font-semibold"><?php echo e($service->name_bn ?? $service->name); ?></h4>
                                        <span class="text-sm text-gray-500"><?php echo e($serviceProducts->count()); ?> <?php echo e(__('items')); ?></span>
                                    </div>
                                </div>
                                <span class="text-[#028090] font-semibold">
                                   <?php echo e(currencyPosition(number_format($serviceProducts->sum(fn($p) => ($p->discount_price ?? $p->price) * $p->cart_quantity), 2))); ?>

                                </span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $price = $product->discount_price ?? $product->price;
                                    $quantity = $product->cart_quantity;
                                ?>
                                <div class="flex justify-between items-center mb-2 border rounded-[12px] p-2">
                                    <div class="flex items-center gap-3">
                                        <img src="<?php echo e(asset($product->thumbnailPath ?? 'website/assets/images/default.png')); ?>"
                                            class="w-12 h-12 rounded object-cover">
                                        <div>
                                            <p class="text-sm font-medium"><?php echo e($product->name_bn ?? $product->name); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo e(__('Qty')); ?>: <?php echo e($quantity); ?></p>
                                            <p class="text-xs text-[#028090]"><?php echo e(currencyPosition(number_format($price, 2))); ?> <?php echo e(__('each')); ?></p>
                                        </div>
                                    </div>
                                    <span class="font-semibold"><?php echo e(currencyPosition(number_format($price * $quantity, 2))); ?></span>

                                    <!-- Only send quantity to backend -->
                                    <input type="hidden" name="items[<?php echo e($product->id); ?>][quantity]"
                                        value="<?php echo e($quantity); ?>">
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="w-full lg:w-1/3 bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-6 rounded-[20px] border border-[#E2E8F0] space-y-4 sticky top-20">
                <h3 class="font-semibold text-lg mb-2"><?php echo e(__('Order Summary')); ?></h3>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceProducts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $service = $serviceProducts->first()->service;
                        $storeTotal = $serviceProducts->sum(
                            fn($p) => ($p->discount_price ?? $p->price) * $p->cart_quantity,
                        );
                    ?>
                    <div class="flex justify-between items-center mb-2 border p-2 rounded-[12px]">
                        <div class="flex items-center gap-2">
                            <img src="<?php echo e(asset($service->thumbnailPath ?? 'website/assets/images/default.png')); ?>"
                                class="w-8 h-8 rounded">
                            <p class="text-sm text-gray-500"><?php echo e($service->name_bn ?? $service->name); ?></p>
                        </div>
                        <span class="font-semibold text-[#028090] whitespace-nowrap"><?php echo e(currencyPosition(number_format($storeTotal, 2))); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="border-t pt-2 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span><?php echo e(__('Subtotal')); ?></span><span><?php echo e(currencyPosition(number_format($subtotal, 2))); ?></span>
                    </div>
                    <div class="flex justify-between text-sm"><span><?php echo e(__('Delivery Fee')); ?></span><span><?php echo e(__('Free')); ?></span></div>
                    <div class="flex justify-between text-sm"><span><?php echo e(__('Tax & VAT')); ?>

                            (<?php echo e($taxRate ?? 0); ?>%)</span><span><?php echo e(currencyPosition(number_format($tax, 2))); ?></span></div>
                </div>

                <div class="flex justify-between mt-2 p-3 bg-[#E6F4F1] rounded-[12px] font-semibold text-lg">
                    <span><?php echo e(__('Total')); ?></span>
                    <span><?php echo e(currencyPosition(number_format($grandTotal, 2))); ?></span>
                </div>

                <!-- Payment Method -->
                <div class="bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-6 rounded-[20px] border border-[#E2E8F0] shadow-sm">
                    <h2 class="text-lg font-semibold text-neutral-700 mb-4"><?php echo e(__('Payment Method')); ?></h2>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-2 p-3 border rounded-[14px] cursor-pointer hover:border-[#028090]">
                            <input type="radio" name="payment_method" value="stripe" class="form-radio" checked>
                            <?php echo e(__('Stripe')); ?>

                        </label>
                        <label class="flex items-center gap-2 p-3 border rounded-[14px] cursor-pointer hover:border-[#028090]">
                            <input type="radio" name="payment_method" value="cod" class="form-radio">
                            <?php echo e(__('Cash on Delivery')); ?>

                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[linear-gradient(135deg,#028090,#00A896)] text-white py-3 rounded-[12px] font-semibold mt-2"
                    <?php echo e($addresses->isEmpty() ? 'disabled' : ''); ?>>
                    <?php echo e(__('Place Order')); ?> — <?php echo e(currencyPosition(number_format($grandTotal, 2))); ?>

                </button>
            </div>

        </main>
    </form>

    <!-- Add Address Modal -->
    <div id="add-address-modal" class="fixed inset-0  hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-[20px] p-6 w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto border border-[#E2E8F0] shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg"><?php echo e(__('Add New Address')); ?></h3>
                <button type="button" onclick="document.getElementById('add-address-modal').classList.add('hidden')"
                    class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form id="add-address-form" method="POST" action="<?php echo e(route('web.addresses.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Address Name')); ?> *</label>
                        <input type="text" name="address_name" id="address_name" class="w-full border p-3 rounded-[12px]"
                            placeholder="<?php echo e(__('e.g., Home, Office')); ?>" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('House No.')); ?></label>
                            <input type="text" name="house_no" class="w-full border p-3 rounded-[12px]"
                                placeholder="<?php echo e(__('House #')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['house_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Flat No.')); ?></label>
                            <input type="text" name="flat_no" class="w-full border p-3 rounded-[12px]"
                                placeholder="<?php echo e(__('Flat #')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['flat_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Road No.')); ?></label>
                        <input type="text" name="road_no" class="w-full border p-3 rounded-[12px]" placeholder="<?php echo e(__('Road #')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['road_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Block')); ?></label>
                        <input type="text" name="block" class="w-full border p-3 rounded-[12px]" placeholder="<?php echo e(__('Block')); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Area')); ?> *</label>
                        <input type="text" name="area" id="area" class="w-full border p-3 rounded-[12px]"
                            placeholder="<?php echo e(__('Area')); ?>" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Address Line')); ?></label>
                        <input type="text" name="address_line" class="w-full border p-3 rounded-[12px]"
                            placeholder="<?php echo e(__('Address Line')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address_line'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Post Code')); ?></label>
                        <input type="text" name="post_code" class="w-full border p-3 rounded-[12px]"
                            placeholder="<?php echo e(__('Post Code')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['post_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('Delivery Note')); ?></label>
                        <textarea name="delivery_note" class="w-full border p-3 rounded-[12px]" placeholder="<?php echo e(__('Delivery instructions')); ?>"
                            rows="2"></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['delivery_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </div>
                </div>
                <div id="address-form-message" class="mt-3 text-sm hidden"></div>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('add-address-modal').classList.add('hidden')"
                        class="flex-1 border py-3 rounded-[12px] hover:bg-gray-50"><?php echo e(__('Cancel')); ?></button>
                    <button type="submit" class="flex-1 bg-[linear-gradient(135deg,#028090,#00A896)] text-white py-3 rounded-[12px] hover:bg-[#156172]"><?php echo e(__('Save')); ?> <?php echo e(__('Address')); ?></button>
                </div>
            </form>
        </div>
    </div>



    <script>
        // Function to fetch and render addresses
        function fetchAndRenderAddresses() {
            const addressList = document.getElementById('address-list');
            const noAddressMsg = document.getElementById('no-address-message');

            fetch('<?php echo e(route('web.addresses.list')); ?>', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.addresses && data.addresses.length > 0) {
                        noAddressMsg.classList.add('hidden');
                        addressList.classList.remove('hidden');

                        let html = '';
                        data.addresses.forEach((address, index) => {
                            const fullAddress = [
                                address.house_no,
                                address.flat_no,
                                address.road_no,
                                address.block,
                                address.area,
                                address.address_line,
                                address.address_line2,
                                address.post_code
                            ].filter(Boolean).join(', ');

                            html += `
                            <div class="mb-3 flex items-start gap-3">
                                <input type="radio" name="address_id" id="address-${address.id}"
                                    value="${address.id}" ${index === 0 ? 'checked' : ''} required>
                                <label for="address-${address.id}"
                                    class="flex-1 border p-3 rounded-[12px] cursor-pointer hover:border-[#028090]">
                                    <h5 class="font-semibold">${address.address_name}</h5>
                                    <span class="text-sm text-gray-500">${fullAddress}</span>
                                    ${address.delivery_note ? `<p class="text-xs text-gray-400 italic">${address.delivery_note}</p>` : ''}
                                </label>
                            </div>
                        `;
                        });

                        addressList.innerHTML = html;

                        // Enable place order button
                        const placeOrderBtn = document.querySelector('button[type="submit"]');
                        if (placeOrderBtn) {
                            placeOrderBtn.disabled = false;
                        }
                    } else {
                        noAddressMsg.classList.remove('hidden');
                        addressList.innerHTML = '';
                    }
                })
                .catch(error => console.error('Error fetching addresses:', error));
        }

        const pickDateInput = document.getElementById('pick_date');
        const deliveryDateInput = document.getElementById('delivery_date');

        function syncDeliveryMinDate() {
            if (!pickDateInput || !deliveryDateInput) {
                return;
            }
            const currentMin = '<?php echo e(date('Y-m-d')); ?>';
            const pickValue = pickDateInput.value || currentMin;
            deliveryDateInput.min = pickValue;
            if (deliveryDateInput.value && deliveryDateInput.value < pickValue) {
                deliveryDateInput.value = pickValue;
            }
        }

        if (pickDateInput && deliveryDateInput) {
            pickDateInput.addEventListener('change', syncDeliveryMinDate);
            syncDeliveryMinDate();
        }

        document.getElementById('add-address-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const messageDiv = document.getElementById('address-form-message');
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.textContent = '<?php echo e(__('Saving...')); ?>';
            messageDiv.classList.add('hidden');
            messageDiv.classList.remove('text-red-600', 'text-[#028090]');

            // Clear previous validation errors
            form.querySelectorAll('.validation-error').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        messageDiv.textContent = data.message || '<?php echo e(__('Address added successfully!')); ?>';
                        messageDiv.className = 'mt-3 text-sm text-[#028090] font-medium';
                        messageDiv.classList.remove('hidden');

                        // Close modal, reset form and refresh addresses
                        setTimeout(() => {
                            document.getElementById('add-address-modal').classList.add('hidden');
                            form.reset();
                            messageDiv.classList.add('hidden');
                            // Fetch and display new addresses
                            fetchAndRenderAddresses();
                        }, 1500);
                    } else if (data.errors) {
                        // Show validation errors
                        let errorMessage = '';
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('border-red-500');
                                const errorSpan = document.createElement('span');
                                errorSpan.className = 'validation-error text-red-500 text-xs';
                                errorSpan.textContent = messages[0];
                                input.parentNode.appendChild(errorSpan);
                            }
                            errorMessage += messages[0] + '\n';
                        }
                        messageDiv.textContent = errorMessage;
                        messageDiv.className = 'mt-3 text-sm text-red-600';
                        messageDiv.classList.remove('hidden');
                    } else {
                        messageDiv.textContent = data.message || '<?php echo e(__('Something went wrong')); ?>';
                        messageDiv.className = 'mt-3 text-sm text-red-600';
                        messageDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    messageDiv.textContent = '<?php echo e(__('Error')); ?>: ' + error.message;
                    messageDiv.className = 'mt-3 text-sm text-red-600';
                    messageDiv.classList.remove('hidden');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = '<?php echo e(__('Save Address')); ?>';
                });
        });


    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\checkout.blade.php ENDPATH**/ ?>