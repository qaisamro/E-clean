@extends('layouts.app')

@section('content')
    <style>
        .client_payment_box {
            cursor: pointer;
            transition: all 0.3s;
            height: 50px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            background: #fff;
        }
        .client_payment_box.selected {
            border: 1px solid #487be9;
            border-radius: 5px;
            background: #f0f4ff;
        }
        .client_payment_box .visa-label {
            font-weight: 800;
            font-size: 20px;
            letter-spacing: 1px;
            color: #1a1f71;
            line-height: 1;
        }
        .client_payment_box.selected .visa-label {
            color: #0a0e5c;
        }
    </style>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
            <h3 class="mb-0">
                {{ __('Edit_Order') }} - <span class="text-primary">{{ $order->order_code }}</span>
            </h3>
            <a href="{{ route('order.show', $order->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>{{ __('Back') }}
            </a>
        </div>

        <form id="editOrderForm" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-xl-8">
                    {{-- services --}}
                    <div class="card border-0 overflow-hidden">
                        <div class="card-header py-3">
                            <h3 class="m-0">{{ __('Add_More') . ' ' . __('Service') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3 flex-wrap">
                                @forelse ($services as $service)
                                    <div class="service-card" onclick="selectService({{ $service->id }})"
                                        id="service-{{ $service->id }}">
                                        <img src="{{ asset($service->thumbnailPath) }}" alt="" width="100%"
                                            class="rounded">
                                        <h3 class="text-center mb-0">{{ $service->name }}</h3>
                                    </div>
                                @empty
                                    <h3 class="text-center">
                                        {{ __('No') . ' ' . __('Service') }}
                                    </h3>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- products --}}
                    <div class="card mt-3 border-0 overflow-hidden" style="border-radius: 12px;">
                        <div class="card-header py-3">
                            <h3 class="m-0">{{ __('Select') . ' ' . __('Products') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap mb-3" id="varients" style="gap: 12px 0">

                            </div>
                            <div class="d-flex gap-3 flex-wrap" id="products">

                            </div>
                        </div>
                    </div>

                    {{-- current items --}}
                    <div class="card mt-3 border-0 overflow-hidden">
                        <div class="card-header py-3">
                            <h3 class="m-0">{{ __('Current_Items') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3" id="basketProducts">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mt-2 mt-xl-0">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="m-0">{{ __('Order_Details') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-3 border-bottom border-light pb-3 mb-3">
                                <div class="flex-grow-1">
                                    <label class="form-label mb-1">{{ __('Customer') }}</label>
                                    <select name="customer_id" class="form-control select2" style="width: 100%;"
                                        id="customerId">
                                        <option selected value="">
                                            {{ __('Select Customer') }}
                                        </option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ $customer->id == $order->customer_id ? 'selected' : '' }}>
                                                {{ $customer->user?->name . '-(' . $customer->user?->mobile . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 border-bottom border-light pb-3">
                                <label class="form-label mb-1">{{ __('Address') }}</label>
                                <select name="address_id" class="form-control select2" style="width: 100%;"
                                    id="addressId">
                                    <option selected value="">
                                        {{ __('Select Address') }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label mb-1">{{ __('Delivery_Date') }}</label>
                                <input type="date" name="delivery_date" id="deliveryDateInput" class="form-control"
                                    value="{{ optional($order->delivery_date)->format('Y-m-d') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label mb-1">{{ __('Notes') }}</label>
                                <textarea name="instruction" id="instructionInput" class="form-control" rows="2"
                                    placeholder="{{ __('Enter order notes') }}">{{ $order->instruction }}</textarea>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <p
                                    class="mb-0 border-bottom border-light py-2 d-flex justify-content-between font-weight-500">
                                    {{ __('Total_Amount:') }} <span id="totalAmount"></span>
                                </p>

                                <p
                                    class="mb-0 border-bottom border-light py-2 d-flex justify-content-between font-weight-500">
                                    {{ __('Discount:') }} <input type="text" name="discount" id="discountInput"
                                        value="{{ $order->discount }}" placeholder="{{ currencyPosition(0) }}"
                                        class="form-control w-25" onkeydown="return event.key !== 'Enter';">
                                </p>

                                <p
                                    class="mb-0 border-bottom border-light py-2 d-flex justify-content-between font-weight-500">
                                    {{ __('Delivery_Charge:') }} <input type="text" name="delivery_charge"
                                        id="deliveryChargeInput" value="{{ $order->delivery_charge }}"
                                        placeholder="{{ currencyPosition(0) }}" class="form-control w-25"
                                        onkeydown="return event.key !== 'Enter';">
                                </p>

                                <h3 class="mb-0 border-bottom border-light py-2 d-flex justify-content-between">
                                    {{ __('Grand_Total:') }} <span id="grandTotal"></span>
                                </h3>

                                <span class="detail mt-2 d-block">{{ __('Payment_method') }}</span>
                                <div class="credit rounded flex row mt-3 row mx-1">
                                    <div
                                        class="col-6 client_payment_box d-flex justify-content-center align-items-center {{ $order->payment_method == 'visa' || $order->payment_method == 'card' ? '' : 'selected' }}">
                                        <input type="radio" name="payment_method" value="cash" id="paymentMethodCash"
                                            class="d-none" {{ $order->payment_method != 'visa' && $order->payment_method != 'card' ? 'checked' : '' }}>
                                        <label for="paymentMethodCash" class="mb-0">
                                            <img src="{{ asset('images/cash2.png') }}" class="rounded" width="100"
                                                alt="">
                                        </label>
                                    </div>
                                    <div
                                        class="col-6 client_payment_box d-flex justify-content-center align-items-center {{ $order->payment_method == 'visa' || $order->payment_method == 'card' ? 'selected' : '' }}">
                                        <input type="radio" name="payment_method" value="visa" id="paymentMethodVisa"
                                            class="d-none" {{ $order->payment_method == 'visa' || $order->payment_method == 'card' ? 'checked' : '' }}>
                                        <label for="paymentMethodVisa" class="mb-0 d-flex align-items-center gap-2">
                                            <i class="bi bi-credit-card-2-front fs-4 text-primary"></i>
                                            <span class="visa-label">VISA</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary btn-sm mb-1 py-2 mt-3 w-100"
                                    onclick="submitEdit()">
                                    <i class="bi bi-check-lg me-1"></i>{{ __('Save_Changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        var canEditPrice = @json(auth()->user()->can('order.edit.price'));

        @php
            $basket = $order->products->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image_path' => $p->thumbnailPath,
                    'quantity' => $p->pivot->quantity,
                    'current_price' => (float) ($p->pivot->price ?: ($p->discount_price ?: $p->price)),
                ];
            })->values();
        @endphp
        var basket = @json($basket);

        $(document).ready(function() {
            if (basket.length) {
                renderBasket();
                updateTotalAmount();
            }

            $('#customerId').on('change', function() {
                let customerId = $(this).val();
                let addressSelect = $('#addressId');

                if (customerId) {
                    addressSelect.html('<option selected>{{ __('Loading...') }}</option>');

                    $.ajax({
                        url: '/customers/' + customerId + '/addresses',
                        type: 'GET',
                        success: function(response) {
                            addressSelect.html(
                                '<option selected value="">{{ __('Select Address') }}</option>'
                            );

                            response.forEach(function(address) {
                                addressSelect.append(
                                    `<option value="${address.id}" ${address.id == {{ $order->address_id }} ? 'selected' : ''}>${address.id} ${address.address_name} (${address.road_no})</option>`
                                );
                            });
                        },
                        error: function() {
                            addressSelect.html(
                                '<option selected value="">{{ __('Failed to load addresses') }}</option>'
                            );
                        },
                    });
                } else {
                    addressSelect.html('<option selected value="">{{ __('Select Address') }}</option>');
                }
            });

            if ($('#customerId').val()) {
                $('#customerId').trigger('change');
            }

            $(document).on('click', '.client_payment_box', function() {
                $(this).addClass('selected').siblings().removeClass('selected');
                $(this).find('input[type="radio"]').prop('checked', true);
            });

            $('#discountInput').on('input', function() {
                var input = document.getElementById('discountInput');
                input.value = input.value.replace(/[^0-9.]/g, '');
                updateTotalAmount();
            });

            $('#deliveryChargeInput').on('input', function() {
                var input = document.getElementById('deliveryChargeInput');
                input.value = input.value.replace(/[^0-9.]/g, '');
                updateTotalAmount();
            });
        });

        var currentServiceId = null;

        function selectService(serviceId) {
            $('.service-card').removeClass('active');
            $('#service-' + serviceId).addClass('active');
            currentServiceId = serviceId;
            fetchVariants(serviceId);
        }

        function fetchVariants(serviceId) {
            var variantId = null;
            $('#varients').empty();
            $.ajax({
                url: "{{ route('pos.fetch.variants') }}",
                type: 'GET',
                data: {
                    service_id: serviceId
                },
                success: function(response) {
                    $('#varients').empty();

                    response.data.variants.forEach(function(variant, index) {
                        var variantButton = $('<button>')
                            .addClass('btn border')
                            .text(variant.name)
                            .attr('onclick', 'selectVariant(' + variant.id + ')')
                            .attr('id', 'variant-' + variant.id)
                            .attr('type', 'button');

                        if (index === 0) {
                            variantButton.addClass('btn-primary');
                            variantId = variant.id;
                        }

                        $('#varients').append(variantButton);
                    });

                    if (variantId) {
                        fetchProducts(variantId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching variants:', error);
                }
            });
        }

        function selectVariant(variantId) {
            $('#varients .btn').removeClass('btn-primary');
            $('#variant-' + variantId).addClass('btn-primary');
            fetchProducts(variantId);
        }

        function fetchProducts(varintId) {
            $.ajax({
                url: "{{ route('pos.fetch.products') }}",
                type: 'GET',
                data: {
                    service_id: currentServiceId,
                    variant_id: varintId
                },
                success: function(response) {
                    $('#products').empty();

                    response.data.products.forEach(function(product) {
                        var productCard = $('<div>')
                            .addClass('service-card')
                            .attr('onclick', 'addProductToBasket(' + JSON.stringify(product) + ')');

                        var productImage = $('<img>')
                            .attr('src', product.image_path)
                            .attr('alt', '')
                            .css('width', '100%')
                            .addClass('rounded');

                        var productTitle = $('<h3>')
                            .addClass('text-center mb-0')
                            .text(product.name);

                        productCard.append(productImage).append(productTitle);
                        $('#products').append(productCard);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });
        }

        function addProductToBasket(product) {
            let existingProduct = basket.find(p => p.id === product.id);

            if (existingProduct) {
                existingProduct.quantity += 1;
            } else {
                product.quantity = 1;
                basket.push(product);
            }

            renderBasket();
            updateTotalAmount();
        }

        function renderBasket() {
            $('#basketProducts').empty();

            basket.forEach(product => {
                let priceInput = canEditPrice
                    ? `<input type="number" step="0.01" min="0" class="form-control form-control-sm basket-price" style="width: 90px" value="${product.current_price}">`
                    : `<span class="font-weight-500">${currencyPosition(product.current_price)}</span>`;

                let productDiv = $(`
                    <div class="d-flex gap-3 align-items-center pt-2" style="border-top: 1px dashed #eee" data-product-id="${product.id}">
                        <div class="border rounded">
                            <img src="${product.image_path}" alt="" width="100" height="90" class="rounded object-fit-cover">
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="m-0 d-flex justify-content-between flex-wrap gap-3">
                                ${product.name}
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeProductFromBasket(${product.id})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </h3>
                            <p class="m-0 d-flex align-items-center gap-2 font-weight-500">
                                ${priceInput}
                            </p>
                            <div class="d-flex align-items-center gap-3">
                                <button class="btn btn-sm m-0" onclick="changeQuantity(${product.id}, -1)">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="text" class="text-center border rounded" value="${product.quantity}" readonly style="width: 46px">
                                <button class="btn btn-sm m-0" onclick="changeQuantity(${product.id}, 1)">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                $('#basketProducts').append(productDiv);
            });

            updateTotalAmount();
        }

        $(document).on('input', '.basket-price', function() {
            let productId = $(this).closest('[data-product-id]').data('product-id');
            let product = basket.find(p => p.id === productId);
            if (product) {
                product.current_price = parseFloat($(this).val()) || 0;
                updateTotalAmount();
            }
        });

        function changeQuantity(productId, change) {
            let product = basket.find(p => p.id === productId);

            if (product) {
                product.quantity += change;
                if (product.quantity <= 0) {
                    removeProductFromBasket(productId);
                } else {
                    renderBasket();
                }
            }
        }

        function removeProductFromBasket(productId) {
            basket = basket.filter(p => p.id !== productId);
            renderBasket();
        }

        function updateTotalAmount() {
            let totalAmount = basket.reduce((total, product) => {
                return total + (product.current_price * product.quantity);
            }, 0);

            $('#totalAmount').text(currencyPosition(totalAmount.toFixed(2)));

            let discount = parseFloat($('#discountInput').val()) || 0;
            let deliveryCharge = parseFloat($('#deliveryChargeInput').val()) || 0;
            let grandTotal = totalAmount - discount + deliveryCharge;

            if (grandTotal < 0) {
                grandTotal = 0;
            }

            $('#grandTotal').text(currencyPosition(grandTotal.toFixed(2)));
        }

        function currencyPosition(amount) {
            @php $curr = \App\Models\WebSetting::first()?->currency ?? '₪'; $pos = config('app.currency_position','Prefix'); @endphp
            var curr = @json($curr);
            var pos = @json($pos);
            if (pos === 'Suffix') return amount + ' ' + curr;
            return curr + ' ' + amount;
        }

        function submitEdit() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            if (!basket.length) {
                Toast.fire({
                    icon: 'error',
                    title: 'يجب تحديد منتج واحد على الأقل',
                });
                return;
            }

            var products = basket.map(product => ({
                id: product.id,
                quantity: product.quantity,
                price: product.current_price
            }));

            $.ajax({
                url: "{{ route('order.update', $order->id) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    customer_id: $('#customerId').val(),
                    address_id: $('#addressId').val(),
                    delivery_date: $('#deliveryDateInput').val(),
                    delivery_charge: $('#deliveryChargeInput').val(),
                    discount: $('#discountInput').val(),
                    payment_method: $('input[name="payment_method"]:checked').val() || 'cash',
                    instruction: $('#instructionInput').val(),
                    products: products,
                },
                success: (response) => {
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message,
                    });
                    setTimeout(() => {
                        window.location.href = "{{ route('order.show', $order->id) }}";
                    }, 1200);
                },
                error: (error) => {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON.message,
                    });
                },
            });
        }
    </script>
@endpush