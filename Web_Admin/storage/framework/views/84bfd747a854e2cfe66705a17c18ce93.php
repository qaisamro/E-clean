<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('web/fav-icon/fav_icon.png')); ?>">
    <title><?php echo e(config('app.name')); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('web/css/bootstrap.css')); ?>">
    <style type="text/css">
        .panel-title {
            display: inline;
            font-weight: bold;
        }

        .display-table {
            display: table;
        }

        .display-tr {
            display: table-row;
        }

        .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 61%;
        }

        .order {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            font-weight: 600;
            font-size: 14px;
            margin: 2px 0
        }

        .alert {
            margin-bottom: 0 !important;
        }

        .web-logo {
            width: 200px;
            height: auto;
            border-radius: 50%;
            margin: auto;
            margin-bottom: 20px
        }

        .hide {
            display: none;
        }

        .has-error .form-control {
            border-color: #f75676 !important;
        }

        .form-control {
            color: #000 !important;
            font-size: 1rem !important;
        }

        .form-control:focus {
            border-color: #DFA312;
        }

        #card-element {
            font-size: 1rem;
            line-height: 1.5;
            display: block;
            width: 100%;
            height: calc(2.75rem + 2px);
            padding: 0.625rem 0.75rem;
            transition: all .2s cubic-bezier(.68, -.55, .265, 1.55);
            color: #8898aa;
            border: 1px solid #cad1d7;
            border-radius: 0.375rem;
            background-color: #fff;
            background-clip: padding-box;
            box-shadow: none;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container my-3">
        <div class="content-page">
            <div class="web-logo">
                <a href="#"><img src="<?php echo e($websetting?->websiteLogoPath ?? asset('web/logo.png')); ?>" alt="" width="100%"></a>
            </div>

            <div class="row">
                <div class="col-lg-6 m-auto">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_status == 'Paid'): ?>
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h2>Hello</h2>
                                <h3 class="card-title">We already received your payment</h3>
                                <p class="card-text text-lite">Some quick example text to build on the card title and
                                    make up the bulk of the card's content.</p>
                                <h1 class="card-title">Thanks for stay with us.</h1>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card rounded-0 border-0">
                            <div class="card-header">
                                <h3 class="panel-title display-td">Order Details</h3>
                            </div>
                            <div class="card-body">

                                <div class="card mb-2">
                                    <div class="card-header py-2">
                                        <h4 class="panel-title display-td">Customer</h4>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="mb-0 order">
                                            Name : <span class="right"><?php echo e($order->customer->user->name); ?></span>
                                        </h4>
                                        <p class="mb-0 order">
                                            Phone :<span class="right"><?php echo e($order->customer->user->mobile); ?></span>
                                        </p>
                                        <p class="mb-0 order">
                                            Email :<span class="right"><?php echo e($order->customer->user->email); ?></span>
                                        </p>
                                    </div>
                                </div>
                                <?php
                                    $freeDelivery = $deliveryCost ? $deliveryCost->fee_cost : 00;
                                    $deliveryCharge = $deliveryCost ? $deliveryCost->cost : 00;
                                ?>
                                <div class="card">
                                    <div class="card-header py-2">
                                        <h4 class="panel-title display-td">Order</h4>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="mb-0 order">
                                            Order ID <span><?php echo e($order->order_code); ?></span>
                                        </h4>
                                        <p class="mb-0 order">
                                            Picked At :
                                            <span><?php echo e(Carbon\Carbon::parse($order->pick_date)->format('d M, Y')); ?> -
                                                <?php echo e(substr($order->pick_hour, 0, 5)); ?></span>
                                        </p>

                                        <p class="mb-0 order">
                                            Delivery At :
                                            <span><?php echo e(Carbon\Carbon::parse($order->delivery_date)->format('d M, Y')); ?> -
                                                <?php echo e(substr($order->delivery_hour, 0, 5)); ?></span>
                                        </p>

                                        <p class="mb-0 order">
                                            Delivery Status : <span><?php echo e($order->order_status); ?></span>
                                        </p>

                                        <p class="mb-0 order">
                                            Total Product : <span><?php echo e($order->products->count()); ?></span>
                                        </p>

                                        <p class="mb-0 order">
                                            Sub total : <span> <?php echo e(currencyPosition($order->amount)); ?></span>
                                        </p>

                                        <p class="mb-0 order">
                                            Delivery Cost :
                                            <span><?php echo e(currencyPosition( $order->delivery_charge)); ?></span>
                                        </p>

                                        <p class="mb-0 order">
                                            Discount : <span class="text-danger">
                                                -<?php echo e(currencyPosition($order->discount)); ?></span>
                                        </p>

                                        <p class="mb-0 order text-dark border-top py-2">
                                            Total Amount : <span> <?php echo e(currencyPosition($order->total_amount)); ?></span>
                                        </p>

                                        <div class="col-md-12 pr-0 mt-2 text-right">
                                            <button class="btn btn-primary btn-lg px-5 paynow" id="pay-now">Pay Now
                                                (<?php echo e(config('enums.currency')[0] . $order->total_amount); ?>)
                                                <img width="20" id="load" class="ml-2 position-absolute"
                                                    style="display: none" src="<?php echo e(asset('images/loader/loader.gif')); ?>"
                                                    alt="">
                                            </button>
                                            <p class="m-0 pr-1 text-right">
                                                <span id="success" style="display: none" class="text-success">Payment
                                                    is received successfully.</span>
                                                <span id="error" style="display: none" class="text-danger">Somthing
                                                    was wrong.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe("<?php echo e($stripeKey?->public_key); ?>");
    var cardButton = document.getElementById('pay-now');
    var params = new URLSearchParams(window.location.search);
    var paymentIntentId = params.get('pi');
    var paymentMethodId = params.get('pm');
    var paymentIntent;
    var customer = "<?php echo e($customer->stripe_customer); ?>"
    var card = "<?php echo e($card); ?>"
    var amount = "<?php echo e($order->total_amount); ?>"
    var order = "<?php echo e($order->id); ?>"

    fetch(`/setup-intents/${customer}/${card}/${amount}/${order}/?id=${paymentIntentId}`).then(function(r) {
        return r.json();
    }).then(function(response) {
        paymentIntent = response;
        if (response.last_payment_error) {
            cardError.innerText = response.last_payment_error.message;
        }
    });

    // Add click handler to start handleCardPayment process.
    cardButton.addEventListener('click', function(event) {
        document.getElementById('load').style.display = 'inline-block'
        cardButton.setAttribute('disabled', 'disabled')
        event.preventDefault();
        stripe.handleCardPayment(
            paymentIntent.client_secret, {
                payment_method: paymentMethodId
            }
        ).then(function(response) {
            document.getElementById('load').style.display = 'none'
            if (response.error) {
                document.getElementById('error').style.display = 'block'
            } else {
                document.getElementById('success').style.display = 'block'
                fetch(`/order-update/${order}`)
                // window.location.replace("http://laundry.razinsoft.com/order-finished?code=" + "<?php echo e($order->order_code); ?>");
            }
        });
    });
</script>

</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\web\payment.blade.php ENDPATH**/ ?>