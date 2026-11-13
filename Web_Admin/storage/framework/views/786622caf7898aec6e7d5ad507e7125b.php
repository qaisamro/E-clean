<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        .bg-gray {
            background: gray;
            color: #fff;
        }

        .text-default {
            color: #00B894;
        }

        .bg-default {
            background: #00B894;
        }

        body {
            margin: 0;
            position: relative;
            font-family: sans-serif;
            font-size: 14px;
        }

        .container {
            padding: 25px !important;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 0.5rem;
            text-align: left;
            font-size: 14px;
            border: 1px solid rgba(0, 0, 0, 0.07);
        }

        img {
            max-width: 100%;
            max-height: 100%;
        }

        footer {
            position: absolute;
            top: 20%;
            left: 10px;
            transform: rotate(90deg);
            transform-origin: 3% 0% 0;
            width: 100%;
            height: 40px;
        }

        h4 {
            margin: 5px 0
        }

        .header {
            width: 100%;
            height: 28px;
            position: relative;
            background-color: #00B894;
            margin-top: 25px;
        }

        .header .logo {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 25px;
            background: #fff;
            width: 120px;
            height: 40px;
            text-align: center;
        }

        .header .logo img {
            max-width: 98%;
        }

        h1,
        h2 {
            font-weight: bold;
            margin: 0;
        }

        .float-left {
            float: left;
        }

        .address_line {
            height: 80px;
            padding: 5px 0;
        }

        h4 {
            font-weight: normal !important;
            font-size: 14px;
        }

        .w-60 {
            width: 60%
        }

        .w-70 {
            width: 70%
        }

        .w-10 {
            width: 10%
        }

        .w-15 {
            width: 15%
        }

        .w-20 {
            width: 20%
        }

        .footer {
            width: 100%;
            position: absolute;
            bottom: 0;
        }

        .contact {
            position: absolute;
            right: 25%;
            top: 50px
        }

        .footer-bottom {
            border-top: 2px solid rgba(0, 0, 0, 0.07);
            position: relative;
            height: 40px;
        }

        .footer-bottom .message {
            position: absolute;
            left: 25px;
            padding: 10px 0;
        }

        .footer-bottom .author {
            position: absolute;
            top: -2px;
            right: 25px;
            border-top: 2px solid #000;
            padding: 10px 0;
        }

        .footer-bottom .signature {
            position: absolute;
            right: 32px;
            top: -50px;
        }

        .footer-bottom .signature img {
            height: 50px;
        }
    </style>
    <title><?php echo e($order->order_code); ?> - invioce</title>
</head>

<body>
    <?php
        $websetting = App\Models\WebSetting::first();
        $currency = $websetting->currency ?? config('enums.currency');
        $freeDelivery = $deliveryCost ? $deliveryCost->fee_cost : 00;
        $deliveryCharge = $deliveryCost ? $deliveryCost->cost : 00;

        $subTotal = 0;

        foreach ($order->products as $product) {
            $price = $product->discount_price ?? $product->price;
            $subTotal += $product->pivot->quantity * $price;
        }

        foreach ($order->additionals as $additional) {
            $subTotal += $additional->price;
        }

    ?>

    <div class="header">
        <?php
            $invoiceLogoPath = public_path('web/logo.png');
            $wsLogo = $webSetting?->websiteLogo;
            if ($wsLogo && \Illuminate\Support\Facades\Storage::exists($wsLogo->src)) {
                $invoiceLogoPath = storage_path('app/public/' . $wsLogo->src);
            }
        ?>
        <div class="logo">
            <img src="<?php echo e($invoiceLogoPath); ?>" alt="">
        </div>
    </div>

    <div class="container">

        <div style="overflow: hidden; height: 30px;">
            <div style="float: right; width: 30%;">
                <h1 class="text-default">Invoice</h1>
            </div>
        </div>

        <h4 style="font-weight: normal !important"> Customer: <?php echo e($order->customer->user?->name); ?></h4>
        <div class="address_line">
            <div class="w-70 float-left">
                <h4>Area: <?php echo e($order->address?->area); ?></h4>
                <h4>Road: <?php echo e($order->address?->road_no); ?>, House: <?php echo e($order->address?->house_no); ?>, Flat:
                    <?php echo e($order->address?->flat_no); ?> </h4>
                <h4>Phone | Fax: <?php echo e($order->customer->user?->mobile); ?></h4>
                <h4>Payment Method: <strong><?php echo e($order->payment_method_label); ?></strong> | Status:
                    <strong><?php echo e($order->payment_status); ?></strong>
                </h4>
            </div>
            <div class="w-10 float-left">
                <h4 class="text-default">RECEIPT #</h4>
                <h4 class="text-default">DATE:</h4>
            </div>
            <div class="w-20 float-left" style="padding-left: 12px">
                <h4><?php echo e($order->order_code); ?></h4>
                <h4><?php echo e($order->created_at->format('F d, Y')); ?></h4>
            </div>
        </div>

        <div style="padding: 10px 0">
            <h4><strong>Pickup Date :</strong>
                <span class="badge text-dark">
                    <?php echo e(Carbon\Carbon::parse($order->pick_date)->format('M d, Y')); ?>

                </span>
            </h4>
            <h4><strong>Delivery Date :</strong>
                <span class="badge text-dark">
                    <?php echo e(Carbon\Carbon::parse($order->delivery_date)->format('M d, Y')); ?>

                </span>
            </h4>
        </div>

        <table class="table">
            <thead>
                <tr class="bg-default">
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th style="text-align: right;min-width:100px">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $unitPrice = (float) $product->pivot->price > 0
                            ? (float) $product->pivot->price
                            : (float) ($product->discount_price ?: $product->price);
                    ?>
                    <tr>
                        <td><?php echo e($product->pivot->title ?: ($product->name_bn ?? $product->name)); ?></td>
                        <td><?php echo e($product->pivot->quantity); ?></td>
                        <td><?php echo e(currencyPosition($unitPrice)); ?></td>
                        <td style="text-align: right">
                            <?php echo e(currencyPosition($product->pivot->quantity * $unitPrice)); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->additionals->count()): ?>
                    <tr>
                        <td colspan="4" style="border: none"></td>
                    </tr>
                    <tr class="bg-gray">
                        <th style="background: #fff; border:none"><strong style="color: #000">Total Items:
                                <?php echo e($quantity); ?> Pieces</strong></th>
                        <th colspan="2">ADDITIONAL ITEMS</th>
                        <th style="text-align: right">AMOUNT (<?php echo e($currency); ?>)</th>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->additionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="background: none; border:none"></td>
                            <td colspan="2"><?php echo e($additional->title); ?></td>
                            <td style="text-align: right; background: #ddd"><?php echo e($additional->price); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$order->additionals->count()): ?>
                        <td style="border:none"><strong style="color: #000">Total Items: <?php echo e($quantity); ?>

                                Pieces</strong></td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <td colspan="<?php echo e($order->additionals->count() ? 3 : 2); ?>" style="text-align: right;border: 0;">
                        <strong>SUBTOTAL</strong>
                    </td>
                    <td style="text-align: right; background: rgba(0, 0, 0, 0.07);">
                        <strong><?php echo e(currencyPosition($subTotal)); ?></strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;border: 0;">
                        <strong>DELIVERY CHARGE</strong>
                    </td>
                    <td style="text-align: right">
                        <?php echo e(currencyPosition($order->delivery_charge)); ?></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;border: 0;">
                        <strong>TOTAL</strong>
                    </td>

                    <td style="text-align: right; background: rgba(0, 0, 0, 0.07);">
                        <?php echo e(currencyPosition($order->total_amount)); ?></td>
                </tr>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount): ?>
                    <tr>
                        <td colspan="3" style="text-align: right;border: 0;">
                            <strong>COUPON DISCOUNT</strong>
                        </td>

                        <td style="text-align: right; color:red;">
                            -<?php echo e(currencyPosition(round($order->discount, 2))); ?>

                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <tr>
                    <td colspan="3" style="text-align: right;border: 0;">
                        <strong>TOTAL PAYABLE</strong>
                    </td>
                    <td style="color:#000;text-align: right; background: #ddd;">
                        <strong><?php echo e(currencyPosition($order->total_amount)); ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <h4 style="margin-top: 20px"><strong>Delivery Note:</strong> <?php echo e($order->address?->delivery_note); ?></h4>
        <p style="font-weight: 700"><?php echo e($order->instruction); ?></p>
    </div>
    <div class="footer">
        <div style="padding: 10px 25px">
            <div class="address">
                <strong><?php echo e($webSetting?->name ?? config('app.name')); ?></strong> <br>
                Address: <strong><?php echo e($webSetting?->address); ?></strong> <br>
                City: <strong><?php echo e($webSetting?->city); ?></strong> <br>
            </div>
            <div class="contact">
                Mobile: <strong><?php echo e($webSetting?->mobile); ?></strong>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="message text-default">Thank you for your business</div>
            <div class="author">Authorised sign</div>
            <?php
                $sigPath = public_path('web/signature.png');
                $sigModel = $webSetting?->signature;
                if ($sigModel && \Illuminate\Support\Facades\Storage::exists($sigModel->src)) {
                    $sigPath = storage_path('app/public/' . $sigModel->src);
                }
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(file_exists($sigPath)): ?>
                <div class="signature">
                    <img src="<?php echo e($sigPath); ?>">
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\pdf\invoice.blade.php ENDPATH**/ ?>