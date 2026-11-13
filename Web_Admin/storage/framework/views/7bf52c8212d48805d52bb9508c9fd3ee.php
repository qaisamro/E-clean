<?php $__env->startSection('content'); ?>
    <div style="background-color:#FAF7F2;" class="max-w-2xl mx-auto px-4 py-16 text-center">
        <!-- Success Icon -->
        <div class="mb-8">
            <div class="w-24 h-24 mx-auto bg-[#E6F4F1] rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-[#028090]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <!-- Success Message -->
        <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo e(__('Order Placed Successfully!')); ?></h1>
        <p class="text-gray-600 mb-8">
            <?php echo e(__('Thank you for your order. Your order has been placed successfully.')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_type === 'cod'): ?>
                <?php echo e(__('Payment will be collected on delivery.')); ?>

            <?php else: ?>
                <?php echo e(__('Your payment has been processed successfully.')); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>

        <!-- Order Info Card -->
        <div class="bg-white rounded-[20px] shadow-lg p-6 text-left mb-8">
            <div class="border-b pb-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-800"><?php echo e(__('Order Details')); ?></h2>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600"><?php echo e(__('Order Code')); ?></span>
                    <span class="font-semibold"><?php echo e($order->order_code); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600"><?php echo e(__('Pick-up Date')); ?></span>
                    <span class="font-semibold"><?php echo e($order->pick_date); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600"><?php echo e(__('Payment Method')); ?></span>
                    <span class="font-semibold"><?php echo e($order->payment_type === 'cod' ? __('Cash on Delivery') : __($order->payment_type)); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600"><?php echo e(__('Payment Status')); ?></span>
                    <span
                        class="font-semibold <?php echo e($order->payment_status === 'paid' ? 'text-[#028090]' : 'text-yellow-600'); ?>">
                        <?php echo e(__(\Illuminate\Support\Str::ucfirst($order->payment_status))); ?>

                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600"><?php echo e(__('Order Status')); ?></span>
                    <span class="font-semibold text-[#028090]"><?php echo e(__(\Illuminate\Support\Str::ucfirst($order->order_status))); ?></span>
                </div>
                <div class="flex justify-between pt-3 border-t">
                    <span class="text-gray-800 font-semibold"><?php echo e(__('Total Amount')); ?></span>
                    <span class="text-[#028090] font-bold text-xl"><?php echo e(currencyPosition($order->total_amount, 2)); ?></span>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->products && $order->products->count() > 0): ?>
            <div class="bg-white rounded-[20px] shadow-lg p-6 text-left mb-8">
                <div class="border-b pb-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800"><?php echo e(__('Order Items')); ?></h2>
                </div>

                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $price = $product->discount_price ?? $product->price;
                            $quantity = $product->pivot->quantity;
                        ?>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo e(asset($product->thumbnailPath ?? 'website/assets/images/default.png')); ?>"
                                    class="w-12 h-12 rounded object-cover">
                                <div>
                                    <p class="font-medium"><?php echo e($product->name_bn ?? $product->name); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e(__('Qty')); ?>: <?php echo e($quantity); ?></p>
                                    <p class="text-xs text-[#028090]"><?php echo e(currencyPosition($price, 2)); ?> <?php echo e(__('each')); ?></p>
                                </div>
                            </div>
                            <span class="font-semibold"><?php echo e(currencyPosition($price * $quantity, 2)); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Delivery Address -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->address): ?>
            <div class="bg-white rounded-[20px] shadow-lg p-6 text-left mb-8">
                <div class="border-b pb-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800"><?php echo e(__('Delivery Address')); ?></h2>
                </div>

                <div class="space-y-2">
                    <p class="font-medium"><?php echo e($order->address->address_name); ?></p>
                    <p class="text-gray-600 text-sm">
                        <?php echo e(collect([
                            $order->address->house_no,
                            $order->address->flat_no,
                            $order->address->road_no,
                            $order->address->block,
                            $order->address->area,
                            $order->address->post_code,
                        ])->filter()->implode(', ')); ?>

                    </p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->address->delivery_note): ?>
                        <p class="text-gray-500 text-sm italic"><?php echo e($order->address->delivery_note); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo e(route('order.details', $order->id)); ?>"
                class="px-6 py-3 bg-[linear-gradient(135deg,#028090,#00A896)] text-white rounded-[12px] font-semibold hover:bg-[#156172] transition">
                <?php echo e(__('View Details')); ?>

            </a>
            <a href="<?php echo e(route('web.services')); ?>"
                class="px-6 py-3 border border-[#E2E8F0] text-gray-700 rounded-[12px] font-semibold hover:bg-gray-50 transition">
                <?php echo e(__('Continue Shopping')); ?>

            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<script>
    localStorage.setItem('checkboxStates', JSON.stringify({}));
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopPush(); ?>


<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\order-success.blade.php ENDPATH**/ ?>