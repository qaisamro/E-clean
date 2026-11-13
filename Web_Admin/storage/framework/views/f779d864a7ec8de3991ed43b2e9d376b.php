<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('web/logos/fav-icon.png')); ?>">
    <title>Order Invoice</title>
    <meta name="description" content="">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/all.min.css')); ?>" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">

    <style type="text/css">
        * {
            font-size: 10px;
            line-height: 16px;
            font-family: "DejaVu Sans", sans-serif;
        }

        .btn {
            padding: 7px 10px;
            text-decoration: none;
            border: none;
            display: block;
            text-align: center;
            margin: 7px;
            cursor: pointer;
        }

        .btn-info {
            background-color: #999;
            color: #FFF;
        }

        .btn-primary {
            background-color: #6449e7;
            color: #FFF;
            width: 100%;
        }

        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }

        tr {
            border-bottom: 1px dotted #ddd;
        }

        td,
        th {
            padding: 7px 0;
        }

        table {
            width: 100%;
        }

        tfoot tr th:first-child {
            text-align: left;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        small {
            font-size: 11px;
        }

        @media print { @page { size: 72mm auto; margin: 0; } body { margin: 0; padding: 0; } }

            td,
            th {
                padding: 5px 0;
            }

            .hidden-print {
                display: none !important;
            }

            @page {
                margin: 1.5cm 0.5cm 0.5cm;
            }

            @page: first {
                margin-top: 0.5cm;
            }

            tbody::after {
                content: '';
                display: block;
                page-break-after: always;
                page-break-inside: avoid;
                page-break-before: avoid;
            }
        }
    </style>
</head>

<body>

    <div style="width:70mm;margin:0 auto; padding:2mm;">
        <div class="hidden-print">
            <table>
                <tr>
                    <td>
                        <a href="<?php echo e(route('order.show', $order->id)); ?>" class="btn btn-info"><i
                                class="fa fa-arrow-left"></i>Back To Order</a>
                    </td>
                    <td>
                        <button onclick="window.print();" class="btn btn-primary">
                            <i class="dripicons-print"></i> Print Now
                        </button>
                    </td>
                </tr>
            </table>
            <br>
        </div>
        <?php
            $freeDelivery = $deliveryCost ? $deliveryCost->fee_cost : 00;
            $deliveryCharge = $deliveryCost ? $deliveryCost->cost : 00;
        ?>
        <div id="receipt-data">
            <div class="centered">
                <img src="<?php echo e($webSetting->websiteLogoPath ?? asset('web/logo.png')); ?>" height="42"
                    style="margin-top: -10px; margin-bottom: 0px;filter: brightness(0);">
                <p style="margin-top: 0; font-size: 10px; line-height: 20px;">
                    <?php echo e($webSetting?->address); ?><br>
                    <i class="fas fa-phone-alt" style="font-size:10px"></i> <?php echo e($webSetting?->mobile); ?> <br>
                    <i class="fas fa-globe" style="font-size:10px"></i> <?php echo e($webSetting?->name); ?>

                </p>
            </div>
            <p>
                Customer: <?php echo e($order->customer->name); ?>

                <br>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->address): ?>
                    House No: <?php echo e($order->address->house_no); ?> / Flat: <?php echo e($order->address->flat_no); ?>

                    <br>
                    Road NO: <?php echo e($order->address->road_no); ?> / Block: <?php echo e($order->address->block); ?>

                    <br>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                Phone No: <?php echo e($order->customer->user->mobile); ?>

                <br>
                Pick Date: <?php echo e(Carbon\Carbon::parse($order->pick_date)->format('M d, Y')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->delivery_date): ?>
                    <br>
                    Delivery Date: <?php echo e(Carbon\Carbon::parse($order->delivery_date)->format('M d, Y')); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <br>
                Order ID: <strong><?php echo e($order->order_code); ?></strong>
            </p>
            <table class="table-data">
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $unitPrice = (float) $product->pivot->price > 0
                                ? (float) $product->pivot->price
                                : (float) ($product->discount_price ?: $product->price);
                        ?>
                        <tr>
                            <td style="padding-bottom: 0" colspan="2">
                                <h4 style="margin: 0"><?php echo e($product->pivot->title ?: ($product->name_bn ?? $product->name)); ?></h4>
                                <?php echo e($product->pivot->quantity); ?> x <?php echo e(currencyPosition($unitPrice)); ?>

                            </td>
                            <td style="text-align:right;vertical-align:bottom;padding-bottom: 0">
                                <?php echo e(currencyPosition($product->pivot->quantity * $unitPrice)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->additionals->count()): ?>
                        <tr>
                            <td colspan="2" style="padding:15px 0 0 0"><strong>Additional Service</strong></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->additionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="padding-bottom: 0" colspan="2">
                                    <h4 style="margin: 0"><?php echo e($additional->title); ?></h4>
                                </td>
                                <td style="text-align:right;vertical-align:bottom;padding-bottom: 0">
                                    <?php echo e(currencyPosition( $additional->price)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <tr>
                        <td style="padding-bottom: 0" colspan="2">
                            <h4 style="margin: 0">TOTAL</h4>
                        </td>
                
                        <?php
                            $total = $order->amount;
                        ?>
                        <td style="text-align:right;vertical-align:bottom;padding-bottom: 0">
                            <strong><?php echo e(currencyPosition($total)); ?></strong>
                        </td>
                    </tr>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount): ?>
                        <tr>
                            <td style="padding-bottom: 0" colspan="2">
                                <h4 style="margin: 0">COUPON DISCOUNT</h4>
                            </td>
                            <td style="text-align:right;vertical-align:bottom;padding-bottom: 0">
                                <strong>-<?php echo e(currencyPosition(round($order->discount, 2))); ?></strong>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <tr>
                        <td style="padding-bottom: 0" colspan="2">
                            <h4 style="margin: 0">DELIVERY COST</h4>
                        </td>
                        <td style="text-align:right;vertical-align:bottom;padding-bottom: 0">
                            <strong><?php echo e(currencyPosition($order->delivery_charge)); ?></strong>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 0" colspan="2">
                            <h4 style="margin: 0">TOTAL PAYABLE</h4>
                        </td>
                        <td style="text-align:right;vertical-align:bottom;padding-bottom: 0">
                            <strong><?php echo e(currencyPosition(round($order->total_amount, 1))); ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="margin-top: 20px">
                <tbody>
                    <tr style="background-color:#ddd;">
                        <td style="padding: 5px;width:30%">Total PCs: <?php echo e($quantity); ?></td>
                        <td style="padding: 5px;width:40%;text-align:center">Paid By: <?php echo e($order->payment_method_label); ?></td>
                        <td style="padding: 5px;width:30%;text-align:right">Total:
                            <strong><?php echo e(currencyPosition(round($order->total_amount, 1))); ?></strong>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->instruction): ?>
                        <tr>
                            <td colspan="3">
                                <p><?php echo e($order->instruction); ?></p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <tr>
                        <td class="centered" colspan="3">Thank you for choosing <?php echo e($webSetting?->name); ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <script type="text/javascript">
        localStorage.clear();

        function auto_print() {
            window.print()
        }
        setTimeout(auto_print, 1000);
    </script>

</body>

</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\pdf\posIvoice.blade.php ENDPATH**/ ?>