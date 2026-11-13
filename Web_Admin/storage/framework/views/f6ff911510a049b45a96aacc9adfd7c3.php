<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .heading {
            font-size: 23px;
            font-weight: 700;
        }

        .text {
            font-size: 16px;
            font-weight: 500;
            color: #b1b6bd;
        }

        .pricing {
            border: 2px solid #304FFE;
            background-color: #f2f5ff;
        }

        .business {
            font-size: 20px;
            font-weight: 500;
        }

        .plan {
            color: #aba4a4;
        }

        .dollar {
            font-size: 16px;
            color: #6b6b6f;
        }

        .amount {
            font-size: 50px;
            font-weight: 500;
        }

        .year {
            font-size: 20px;
            color: #6b6b6f;
            margin-top: 19px;
        }

        .detail {
            font-size: 22px;
            font-weight: 500;
        }

        .cvv {
            height: 44px;
            width: 73px;
            border: 2px solid #eee;
        }

        .cvv:focus {
            box-shadow: none;
            border: 2px solid #304FFE;
        }

        .email-text {
            height: 55px;
            border: 2px solid #eee;
        }

        .email-text:focus {
            box-shadow: none;
            border: 2px solid #304FFE;
        }

        .payment-button {
            height: 70px;
            font-size: 20px;
        }

        #card-element {
            height: 40px;
        }

        .client_payment_box {
            cursor: pointer;
            transition: border 0.3s;
        }

        .client_payment_box.selected {
            border: 1px solid #487be9;
            /* border: 2px solid #007bff; */
            border-radius: 5px;
        }
    </style>
</head>

<body>


    <div class=" mt-5 mx-5 mb-5 ">
        <div class="card p-5 my-5">
            <?php
                $gateway = request()->query('gateway');
                $order = request()->query('order');
                $orders = DB::table('orders')->get();

            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $order == $ord->id): ?>
                    
                        <span class="detail"><?php echo e(__('Payment_method:')); ?> <?php echo e($gateway); ?></span>
                        <form id="payment-form">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="payment_method" id="selected-method"
                                value="<?php echo e($gateway); ?>">
                            <input type="hidden" name="token_id" id="token_id">

                            <input type="hidden" name="order_id" value="<?php echo e($order); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $order == $ord->id): ?>
                                    <input type="hidden" name="total_amount" id="total_amount"
                                        value="<?php echo e($ord->total_amount); ?>">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div id="card-element" class="form-control my-3" style="display: none;"></div>
                            <span class="text-danger" id="stripe-error"></span>
                            <span class="text-danger" id="razorpay-error"></span>
                            <div id="paypal-button-container" class="mt-3" style="display: none;"></div>


                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm common-btn btn-block  w-100"
                                    id="pay-btn" disabled>
                                    <?php echo e(__('Proceed_to_payment')); ?> <i class="fa fa-long-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        </div>
    </div>
    
    
    

    

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    let stripe, elements, cardElement;
    let formSubmitted = false;
    let paypalInitialized = false;
    $(document).ready(function() {
        var paymentMethod = document.getElementById("selected-method").value;

        window.addEventListener('message', function(event) {
            const {
                order
            } = event.data;

            if (order.payment_type === 'stripe') {
                initializeStripe();
            } else if (order.payment_type === 'paystack') {
                initiatePayStack(order);
            } else if (order.payment_type === 'razorpay') {
                initiateRazorpay(order);
            }

        });

        const urlParams = new URLSearchParams(window.location.search);
        const gateway = urlParams.get('gateway');

        if (gateway === 'stripe') {
            if (gateway === 'stripe') {
                $('#card-element').show();
                $('#pay-btn').prop('disabled', false);
                initializeStripe();
            } else {
                $('#card-element').hide();
                destroyStripeCardElement();
            }
        } else if (gateway === 'paystack') {
            $('.card').css('display', 'none');
            initiatePayStack();
        } else if (gateway === 'razorpay') {
            $('.card').css('display', 'none');
            initiateRazorpay();
        }


        function initializeStripe() {
            var data = <?php echo json_encode($data, 15, 512) ?>;

            if (!stripe) {
                stripe = Stripe(data.stripe_publish_key); // Use the key from response.data
                elements = stripe.elements();
            }
            if (!cardElement) {
                cardElement = elements.create('card');
                cardElement.mount('#card-element');

            }
        }
        // Destroy Stripe Elements
        function destroyStripeCardElement() {
            if (cardElement) {
                cardElement.destroy();
                cardElement = null;
            }
        }

        // Main pay button click handler
        function initiatePayStack(order) {
            var data = <?php echo json_encode($data, 15, 512) ?>;
            var paystackKey = data ? data.paystack_publish_key : '';
            var amount = order ? order.total_amount * 100 : 0;

            let selectedCurrency = "ZAR";
            let handler = PaystackPop.setup({
                key: paystackKey,
                email: "<?php echo e(auth()->user()?->email ?? 'default@example.com'); ?>",
                amount: amount,
                currency: selectedCurrency,
                callback: function(response) {
                    $('#token_id').val(response.reference);
                    formSubmit();
                },
                onClose: function() {
                    alert('Transaction cancelled.');
                }
            });

            handler.openIframe();
        }

        // Razorpay initialization
        function initiateRazorpay(order) {

            var data = <?php echo json_encode($data, 15, 512) ?>;
            var razorpayKey = data ? data.razorpay_publish_key : '';
            var amount = order ? order.total_amount * 100 : 0;
            let options = {
                key: razorpayKey,
                amount: amount,
                currency: "INR",
                description: "Subscription Payment",
                handler: function(response) {
                    $('#token_id').val(response.razorpay_payment_id);
                    formSubmit();

                },
                prefill: {
                    email: "<?php echo e(auth()->user()?->email ?? 'default@example.com'); ?>",
                    contact: "<?php echo e(auth()->user()->phone ?? ''); ?>"
                },
                theme: {
                    color: "#304FFE"
                }
            };
            let rzp = new Razorpay(options);
            rzp.open();
        }

    });
    $(document).on('click', '#pay-btn', function() {

        var paymentMethod = document.getElementById("selected-method").value;
        if (paymentMethod === 'stripe') {
            stripe.createToken(cardElement).then(function(result) {
                if (result.error) {
                    $('#stripe-error').text(result.error.message);
                    $('#pay-btn').prop('disabled', false);
                    formSubmitted = false;
                } else {
                    $('#token_id').val(result.token.id);
                    formSubmit();

                }
            });
        } else if ($('#selected-method').val() === 'paypal') {
            initiatePaypal();
        }
    });

    function formSubmit() {
        // const orderId = $('input[name="order_id"]').val();
        // const url = `<?php echo e(route('payment.process.order', ':order')); ?>`.replace(':order', orderId);
        // const totalAmount = $('input[name="total_amount"]').val();


        const orderId = $('input[name="order_id"]').val();

        let url = "<?php echo e(route('payment.process.order', ['order' => '__order__'])); ?>";
        url = url.replace('__order__', orderId);

        const totalAmount = $('input[name="total_amount"]').val();

        console.log(url);


        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                payment_method: $('input[name="payment_method"]').val(),
                token_id: $('input[name="token_id"]').val(),
                order: orderId,
                total_amount: totalAmount,
            },
            success: function(response) {
                console.log(response.success, 'res');
                if (response.success == true) {
                        window.location.href = "<?php echo e(route('payment.success', ['status' => 'success'])); ?>";

                }

            },
            error: function(xhr, status, error) {
                console.error('Payment submission failed:', error);
            }
        });
    }
</script>


</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\pos\order-payment.blade.php ENDPATH**/ ?>