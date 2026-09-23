@extends('website.layouts.app')

@section('content')
    @if (session('success'))
        <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg text-center">
            <div
                class="text-4xl mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-pink-500 to-yellow-500">
                🗡️ {{ session('success') }} !
            </div>

        </div>
    @endif

    @if (session('error'))
        <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg text-center">
            <div
                class="text-4xl mb-4 bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-pink-500 to-yellow-500">
                ❌ {{ session('error') }} !
            </div>

        </div>
    @endif


    <form action="{{ route('checkout.placeOrder') }}" method="POST">
        @csrf
        <main class="max-w-6xl mx-auto px-4 py-12 flex flex-col lg:flex-row gap-8">

            <!-- Left: Order Details -->
            <div class="flex-1 space-y-6">

                <!-- Delivery Address -->
                <div class="bg-white p-6 rounded-3xl">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Delivery Address') }}</h3>

                    <p id="no-address-message" class="text-red-500 mb-2 {{ !$addresses->isEmpty() ? 'hidden' : '' }}">{{ __('Please add a delivery address first.') }}</p>

                    <div id="address-list">
                        @foreach ($addresses->take(3) as $address)
                            @php
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
                            @endphp

                            <div class="mb-3 flex items-start gap-3">
                                <input type="radio" name="address_id" id="address-{{ $address->id }}"
                                    value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }} required>
                                <label for="address-{{ $address->id }}"
                                    class="flex-1 border p-3 rounded-lg cursor-pointer hover:border-green-400">
                                    <h5 class="font-semibold">{{ ucfirst($address->address_name) }}</h5>
                                    <span class="text-sm text-gray-500">{{ $fullAddress }}</span>
                                    @if ($address->delivery_note)
                                        <p class="text-xs text-gray-400 italic">{{ $address->delivery_note }}mm</p>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="w-full mt-2 border py-2 rounded-lg text-sm hover:bg-gray-50"
                        onclick="document.getElementById('add-address-modal').classList.remove('hidden')">{{ __('Add New Address') }}</button>
                </div>

                <!-- Pick-up Schedule -->
                <div class="bg-white p-6 rounded-3xl">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Pick-up Schedule') }}</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="pick_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Pick-up Date') }}</label>
                            <input type="date" name="pick_date" id="pick_date"
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                min="{{ date('Y-m-d') }}" value="{{ old('pick_date') }}" required placeholder="{{ __('Select pick-up date') }}">
                            @error('pick_date')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="pick_hour" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Pick-up Time') }}</label>
                            <input type="time" name="pick_hour" id="pick_hour"
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                required value="{{ old('pick_hour') }}" step="60">
                            @error('pick_hour')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">{{ __('Choose your preferred pick-up date and time slot.') }}</p>
                </div>

                <!-- Delivery Schedule -->
                <div class="bg-white p-6 rounded-3xl">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Delivery Schedule') }}</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Delivery Date') }}</label>
                            <input type="date" name="delivery_date" id="delivery_date"
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                min="{{ date('Y-m-d') }}" value="{{ old('delivery_date') }}" required placeholder="{{ __('Select delivery date') }}">
                            @error('delivery_date')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="delivery_hour" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Delivery Time') }}</label>
                            <input type="time" name="delivery_hour" id="delivery_hour"
                                class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                required value="{{ old('delivery_hour') }}" step="60">
                            @error('delivery_hour')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">{{ __('Choose your preferred delivery date and time slot.') }}</p>
                </div>

                <!-- Order Items -->
                <div class="bg-white p-6 rounded-3xl space-y-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-lg">{{ __('Order Items') }}</h3>
                        <span class="text-sm text-gray-500">{{ count($products) }} {{ __('items') }}</span>
                    </div>

                    @foreach ($services as $serviceProducts)
                        @php $service = $serviceProducts->first()->service; @endphp
                        <div class="border p-4 rounded-xl mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset($service->thumbnailPath ?? 'website/assets/images/default.png') }}"
                                        class="w-10 h-10 rounded">
                                    <div>
                                        <h4 class="font-semibold">{{ $service->name }}</h4>
                                        <span class="text-sm text-gray-500">{{ $serviceProducts->count() }} {{ __('items') }}</span>
                                    </div>
                                </div>
                                <span class="text-green-600 font-semibold">
                                   {{ currencyPosition(number_format($serviceProducts->sum(fn($p) => ($p->discount_price ?? $p->price) * $p->cart_quantity), 2)) }}
                                </span>
                            </div>

                            @foreach ($serviceProducts as $product)
                                @php
                                    $price = $product->discount_price ?? $product->price;
                                    $quantity = $product->cart_quantity;
                                @endphp
                                <div class="flex justify-between items-center mb-2 border rounded-lg p-2">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset($product->thumbnailPath ?? 'website/assets/images/default.png') }}"
                                            class="w-12 h-12 rounded object-cover">
                                        <div>
                                            <p class="text-sm font-medium">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">{{ __('Qty') }}: {{ $quantity }}</p>
                                            <p class="text-xs text-green-600">{{ currencyPosition(number_format($price, 2)) }} {{ __('each') }}</p>
                                        </div>
                                    </div>
                                    <span class="font-semibold">{{ currencyPosition(number_format($price * $quantity, 2)) }}</span>

                                    <!-- Only send quantity to backend -->
                                    <input type="hidden" name="items[{{ $product->id }}][quantity]"
                                        value="{{ $quantity }}">
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="w-full lg:w-1/3 bg-white p-6 rounded-3xl space-y-4 sticky top-20">
                <h3 class="font-semibold text-lg mb-2">{{ __('Order Summary') }}</h3>

                @foreach ($services as $serviceProducts)
                    @php
                        $service = $serviceProducts->first()->service;
                        $storeTotal = $serviceProducts->sum(
                            fn($p) => ($p->discount_price ?? $p->price) * $p->cart_quantity,
                        );
                    @endphp
                    <div class="flex justify-between items-center mb-2 border p-2 rounded-lg">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset($service->thumbnailPath ?? 'website/assets/images/default.png') }}"
                                class="w-8 h-8 rounded">
                            <p class="text-sm text-gray-500">{{ $service->name }}</p>
                        </div>
                        <span class="font-semibold text-green-600 whitespace-nowrap">{{ currencyPosition(number_format($storeTotal, 2)) }}</span>
                    </div>
                @endforeach

                <div class="border-t pt-2 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>{{ __('Subtotal') }}</span><span>{{ currencyPosition(number_format($subtotal, 2)) }}</span>
                    </div>
                    <div class="flex justify-between text-sm"><span>{{ __('Delivery Fee') }}</span><span>{{ __('Free') }}</span></div>
                    <div class="flex justify-between text-sm"><span>{{ __('Tax & VAT') }}
                            ({{ $taxRate ?? 0 }}%)</span><span>{{ currencyPosition(number_format($tax, 2)) }}</span></div>
                </div>

                <div class="flex justify-between mt-2 p-3 bg-green-50 rounded-lg font-semibold text-lg">
                    <span>{{ __('Total') }}</span>
                    <span>{{ currencyPosition(number_format($grandTotal, 2)) }}</span>
                </div>

                <!-- Payment Method -->
                <div class="bg-white p-6 rounded-3xl shadow-sm">
                    <h2 class="text-lg font-semibold text-neutral-700 mb-4">{{ __('Payment Method') }}</h2>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:border-mint-500">
                            <input type="radio" name="payment_method" value="stripe" class="form-radio" checked>
                            {{ __('Stripe') }}
                        </label>
                        <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:border-mint-500">
                            <input type="radio" name="payment_method" value="cod" class="form-radio">
                            {{ __('Cash on Delivery') }}
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold mt-2"
                    {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                    {{ __('Place Order') }} — {{ currencyPosition(number_format($grandTotal, 2)) }}
                </button>
            </div>

        </main>
    </form>

    <!-- Add Address Modal -->
    <div id="add-address-modal" class="fixed inset-0  hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-3xl p-6 w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto border border-gray-200 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">{{ __('Add New Address') }}</h3>
                <button type="button" onclick="document.getElementById('add-address-modal').classList.add('hidden')"
                    class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form id="add-address-form" method="POST" action="{{ route('web.addresses.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Address Name') }} *</label>
                        <input type="text" name="address_name" id="address_name" class="w-full border p-3 rounded-lg"
                            placeholder="{{ __('e.g., Home, Office') }}" required>
                        @error('address_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('House No.') }}</label>
                            <input type="text" name="house_no" class="w-full border p-3 rounded-lg"
                                placeholder="{{ __('House #') }}">
                            @error('house_no')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Flat No.') }}</label>
                            <input type="text" name="flat_no" class="w-full border p-3 rounded-lg"
                                placeholder="{{ __('Flat #') }}">
                            @error('flat_no')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Road No.') }}</label>
                        <input type="text" name="road_no" class="w-full border p-3 rounded-lg" placeholder="{{ __('Road #') }}">
                        @error('road_no')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Block</label>
                        <input type="text" name="block" class="w-full border p-3 rounded-lg" placeholder="Block">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area *</label>
                        <input type="text" name="area" id="area" class="w-full border p-3 rounded-lg"
                            placeholder="Area" required>
                        @error('area')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line</label>
                        <input type="text" name="address_line" class="w-full border p-3 rounded-lg"
                            placeholder="Address Line">
                        @error('address_line')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Post Code') }}</label>
                        <input type="text" name="post_code" class="w-full border p-3 rounded-lg"
                            placeholder="{{ __('Post Code') }}">
                        @error('post_code')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Delivery Note') }}</label>
                        <textarea name="delivery_note" class="w-full border p-3 rounded-lg" placeholder="{{ __('Delivery instructions') }}"
                            rows="2"></textarea>
                        @error('delivery_note')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror

                    </div>
                </div>
                <div id="address-form-message" class="mt-3 text-sm hidden"></div>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('add-address-modal').classList.add('hidden')"
                        class="flex-1 border py-3 rounded-lg hover:bg-gray-50">{{ __('Cancel') }}</button>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">{{ __('Save')
                        }} {{ __('Address') }}</button>
                </div>
            </form>
        </div>
    </div>



    <script>
        // Function to fetch and render addresses
        function fetchAndRenderAddresses() {
            const addressList = document.getElementById('address-list');
            const noAddressMsg = document.getElementById('no-address-message');

            fetch('{{ route('web.addresses.list') }}', {
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
                        data.addresses.slice(0, 3).forEach((address, index) => {
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
                                    class="flex-1 border p-3 rounded-lg cursor-pointer hover:border-green-400">
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
            const currentMin = '{{ date('Y-m-d') }}';
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
            submitBtn.textContent = '{{ __('Saving...') }}';
            messageDiv.classList.add('hidden');
            messageDiv.classList.remove('text-red-600', 'text-green-600');

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
                        messageDiv.textContent = data.message || '{{ __('Address added successfully!') }}';
                        messageDiv.className = 'mt-3 text-sm text-green-600 font-medium';
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
                        messageDiv.textContent = data.message || '{{ __('Something went wrong') }}';
                        messageDiv.className = 'mt-3 text-sm text-red-600';
                        messageDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    messageDiv.textContent = '{{ __('Error') }}: ' + error.message;
                    messageDiv.className = 'mt-3 text-sm text-red-600';
                    messageDiv.classList.remove('hidden');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = '{{ __('Save Address') }}';
                });
        });


    </script>
@endsection
