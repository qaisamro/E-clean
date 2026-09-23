@extends('website.layouts.app')

@section('content')
    <main>
        <!-- Success Message -->
        @if (session('success'))
            <div class="max-w-md mx-auto mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-md mx-auto mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                <svg class="w-10 h-10 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-red-700 text-3xl">Failed to process your request: {{ session('error') }}</p>
            </div>
        @endif


        <!-- Breadcrumb -->
        <section
            class="rs-breadcrumb-area bg-[#1A7058] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px] md:text-4xl text-white font-semibold leading-[140%]">
                    Shopping Cart </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="/"
                        class="text-base md:text-lg text-white font-normal hover:text-mint-200 transition">Home / </a>
                    <span class="text-base md:text-lg text-mint-200 font-normal">Cart</span>
                </div>
            </div>
        </section>


        @if ($products->count() === 0)
            <!-- Empty Cart State -->
            <section class="py-16 px-4">
                <div class="max-w-md mx-auto text-center">
                    <div class="mb-8">
                        <svg class="w-32 h-32 mx-auto text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-8">Looks like you haven't added anything to your cart yet.</p>
                    <a href="/"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-mint-600 text-white rounded-xl hover:bg-mint-700 transition font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Start Shopping
                    </a>
                </div>
            </section>
        @else
            <section class="max-w-6xl mx-auto pt-[60px] pb-[80px] px-4 xl:px-0">
                <!-- Header with Continue Shopping -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <p class="text-sm sm:text-base font-semibold text-gray-700">
                            <span id="item-count">{{ $products->count() }}</span> items in your cart
                        </p>
                        @if ($service_id && $variants->count() > 0)
                            <div class="flex items-center gap-2">
                                <label for="variant-filter" class="text-sm text-gray-600">Variant:</label>
                                <select id="variant-filter" onchange="filterByVariant(this.value)"
                                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-mint-500">
                                    <option value="">All Variants</option>
                                    @foreach ($variants as $variant)
                                        <option value="{{ $variant->id }}"
                                            {{ $variant_id == $variant->id ? 'selected' : '' }}>
                                            {{ $variant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="custom-checkbox cursor-pointer flex items-center gap-2">
                            <input type="checkbox" id="select-all" class="hidden">
                            <span
                                class="checkbox-box w-5 h-5 border-2 border-gray-300 rounded-md flex items-center justify-center transition-all duration-200">
                                <svg class="w-3 h-3 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="text-sm text-gray-600">Select All</span>
                        </label>
                        <a href="/"
                            class="text-sm text-mint-600 hover:text-mint-700 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach ($services as $serviceId => $serviceProducts)
                            @php
                                $service = $serviceProducts->first()->service;
                            @endphp
                            <div class="rounded-2xl p-4 sm:p-6 bg-white border border-gray-100 shadow-sm">
                                <!-- Service Header -->
                                <div
                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <label class="custom-checkbox cursor-pointer">
                                            <input type="checkbox" class="select-service hidden"
                                                data-service="{{ $service->id }}">
                                            <span
                                                class="checkbox-box w-5 h-5 border-2 border-gray-300 rounded-md flex items-center justify-center transition-all duration-200">
                                                <svg class="w-3 h-3 text-white hidden" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </label>
                                        <img src="{{ asset($service->thumbnailPath ?? 'website/assets/images/service/service.png') }}"
                                            class="w-12 h-12 rounded-lg object-cover">
                                        <div>
                                            <p class="text-base font-semibold text-gray-900">{{ $service->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $serviceProducts->count() }}
                                                item{{ $serviceProducts->count() > 1 ? 's' : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right ml-8 sm:ml-0">
                                        @php
                                            $serviceTotal = $serviceProducts->sum(function ($product) {
                                                $price = $product->discount_price ?? $product->price;
                                                $qty = session("cart.{$product->id}.quantity", 1);
                                                return $price * $qty;
                                            });
                                        @endphp
                                        <p class="text-xs text-gray-500">Service Total</p>
                                        <p class="text-sm sm:text-base font-semibold text-mint-600">
                                            {{ currencyPosition(number_format($serviceTotal, 2)) }}

                                        </p>
                                    </div>
                                </div>

                                <!-- Products -->
                                <div class="mt-4 space-y-3">
                                    @foreach ($serviceProducts as $product)
                                        @php
                                            $price = $product->discount_price ?? $product->price;
                                            $qty = session("cart.{$product->id}.quantity", 1);
                                        @endphp
                                        <div class="flex gap-3 sm:gap-4 rounded-xl border border-gray-100 p-3 product-row cursor-pointer hover:border-mint-300 hover:shadow-md transition-all duration-200"
                                            data-price="{{ $price }}" data-service="{{ $service->id }}"
                                            data-id="{{ $product->id }}" onclick="toggleProductSelection(this, event)">
                                            <label class="custom-checkbox cursor-pointer self-center"
                                                onclick="event.stopPropagation()">
                                                <input type="checkbox" class="select-product hidden">
                                                <span
                                                    class="checkbox-box w-5 h-5 border-2 border-gray-300 rounded-md flex items-center justify-center transition-all duration-200">
                                                    <svg class="w-3 h-3 text-white hidden" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </label>
                                            <img src="{{ asset($product->thumbnailPath ?? 'website/assets/images/service/service.png') }}"
                                                class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg flex-shrink-0">
                                            <div class="flex-1 flex flex-col justify-between min-w-0">
                                                <div class="flex justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            {{ $product->name }}</p>
                                                        @if ($product->variant)
                                                            <p class="text-xs text-mint-600 font-medium">
                                                                {{ $product->variant->name }}</p>
                                                        @endif
                                                        <p class="text-sm text-gray-500">{{ currencyPosition(number_format($price, 2)) }}
                                                            each</p>
                                                    </div>

                                                </div>
                                                {{-- new update git  --}}
                                                <div
                                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mt-2">
                                                    <!-- Add to Cart / Quantity Controls -->
                                                    <div class="add-to-cart-section"
                                                        data-product-id="{{ $product->id }}"
                                                        data-price="{{ $price }}">
                                                        @if (session()->has("cart.{$product->id}"))
                                                            <!-- Quantity Controls (shown when item is in cart) -->
                                                            <div
                                                                class="quantity-controls flex items-center gap-1 bg-white rounded-lg border border-mint-200 p-1 shadow-sm">
                                                                <button type="button"
                                                                    class="qty-btn w-9 h-9 flex items-center justify-center rounded-md bg-mint-50 border border-mint-200 text-mint-600 hover:bg-mint-100 hover:border-mint-300 hover:text-mint-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                                    data-id="{{ $product->id }}" data-action="decrease"
                                                                    {{ $qty <= 1 ? 'disabled' : '' }}>
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M20 12H4" />
                                                                    </svg>
                                                                </button>
                                                                <span
                                                                    class="quantity w-10 text-center font-bold text-mint-700 text-lg">{{ $qty }}</span>
                                                                <button type="button"
                                                                    class="qty-btn w-9 h-9 flex items-center justify-center rounded-md bg-mint-600 border border-mint-600 text-white hover:bg-mint-700 hover:border-mint-700 transition"
                                                                    data-id="{{ $product->id }}" data-action="increase">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 4v16m8-8H4" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <!-- Add to Cart Button (shown when item is not in cart) -->
                                                            <button type="button"
                                                                class="add-btn w-full sm:w-auto px-5 py-2.5 bg-mint-600 text-white rounded-lg hover:bg-mint-700 transition font-semibold flex items-center justify-center gap-2 shadow-sm hover:shadow-md"
                                                                onclick="addToCart(this)">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                                Add to Cart
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="text-right sm:text-left">
                                                        <p class="text-xs text-gray-500">Item Total</p>
                                                        <p class="text-lg font-bold text-mint-600 product-total">
                                                            {{ currencyPosition(number_format($qty * $price, 2)) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <form id="checkout-form" action="{{ route('web.checkout') }}" method="POST"
                            class="sticky top-4">
                            @csrf
                            <input type="hidden" name="selected_ids" id="selected_ids">
                            <input type="hidden" name="coupon_id" id="coupon_id">
                            <input type="hidden" name="quantities" id="quantities">
                            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm">
                                <div class="mb-4">
                                    <p class="text-lg font-semibold text-gray-900">Order Summary</p>
                                    <p class="text-sm text-gray-500">Selected items only</p>
                                </div>

                                <div id="selected-items"
                                    class="space-y-3 py-4 border-y border-gray-100 max-h-48 overflow-y-auto">
                                    <p class="text-gray-400 text-center py-4">No items selected</p>
                                </div>

                                <div class="py-4 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-500">Subtotal</p>
                                        <p class="text-base font-medium text-gray-900" id="subtotal">
                                            {{ currencyPosition(number_format($price,2)) }}</p>
                                    </div>

                                    <!-- Coupon Section -->
                                    <div class="border border-gray-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <p class="text-sm font-medium text-gray-700">Apply Coupon</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" id="coupon-code" placeholder="Enter coupon code"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 focus:border-mint-500">
                                            <button type="button" id="apply-coupon-btn" onclick="applyCoupon()"
                                                class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700 transition font-medium text-sm">
                                                Apply
                                            </button>
                                        </div>
                                        <div id="coupon-message" class="mt-2 text-sm hidden"></div>
                                        <div id="applied-coupon" class="mt-2 hidden">
                                            <div class="flex items-center justify-between bg-mint-50 rounded-lg px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700"
                                                        id="coupon-code-display"></span>
                                                    <span class="text-xs text-gray-500"
                                                        id="coupon-discount-display"></span>
                                                </div>
                                                <button type="button" onclick="removeCoupon()"
                                                    class="text-gray-400 hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center" id="discount-row"
                                        style="display: none;">
                                        <p class="text-sm font-medium text-gray-500">Coupon Discount</p>
                                        <p class="text-base font-medium text-green-600" id="discount">
                                            {{ currencyPosition(number_format($price,2)) }}</p>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-500">Delivery Fee</p>
                                        <p class="text-base font-medium text-green-600">Free</p>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-500">Tax & Vat ({{ $taxRate ?? 0 }}%)</p>
                                        <p class="text-base font-medium text-gray-900" id="tax">
                                            {{ currencyPosition(number_format($price,2)) }}</p>
                                    </div>
                                </div>

                                <div class="py-4 border-t border-gray-100">
                                    <div class="flex justify-between items-center p-3 rounded-xl bg-mint-50">
                                        <p class="text-lg font-semibold text-gray-900">Total</p>
                                        <p class="text-xl font-bold text-mint-700" id="grand-total">
                                            {{ currencyPosition(number_format($price,2)) }}</p>
                                    </div>
                                </div>
                                @auth

                                    <button type="submit"
                                        class="w-full mt-2 px-6 py-3 bg-mint-600 text-white rounded-xl hover:bg-mint-700 focus:ring-4 focus:ring-mint-200 transition font-medium flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="checkout-btn"
                                        data-auth-disabled="@if (!auth()->user()->is_active || (!auth()->user()->email_verified_at && !auth()->user()->mobile_verified_at)) 1 @else 0 @endif"
                                        @if (!auth()->user()->is_active || (!auth()->user()->email_verified_at && !auth()->user()->mobile_verified_at)) disabled @endif>

                                        @if (!auth()->user()->is_active)
                                            <span>Account Inactive</span>
                                        @elseif(!auth()->user()->email_verified_at && !auth()->user()->mobile_verified_at)
                                            <span>Verify Email First</span>
                                        @else
                                            <span>Proceed To Checkout</span>
                                        @endif

                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('web.login', ['service_id' => $service_id, 'redirect' => 'checkout']) }}"
                                        class="w-full mt-2 px-6 py-3 bg-mint-600 text-white rounded-xl hover:bg-mint-700 focus:ring-4 focus:ring-mint-200 transition font-medium flex items-center justify-center gap-2">
                                        <span>Login to Checkout</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                @endauth


                                @auth
                                    @if (!auth()->user()->is_active)
                                        <p class="text-xs text-red-500 text-center mt-2">Your account is inactive. Please
                                            contact support.</p>
                                    @elseif(!auth()->user()->email_verified_at && !auth()->user()->mobile_verified_at)
                                        <p class="text-xs text-red-500 text-center mt-2">Please verify your email address to
                                            checkout.</p>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 text-center mt-3">
                                        <a href="{{ route('website.login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                                            class="text-mint-600 hover:text-mint-700 font-medium">Login</a> or
                                        <a href="{{ route('web.register') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                                            class="text-mint-600 hover:text-mint-700 font-medium">Create an account</a>
                                        to checkout
                                    </p>
                                @endauth
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        @endif
    </main>

    <style>
        .custom-checkbox input:checked+.checkbox-box {
            background-color: #32d3a0;
            border-color: #32d3a0;
        }

        .custom-checkbox input:checked+.checkbox-box svg {
            display: block;
        }

        .custom-checkbox:hover .checkbox-box {
            border-color: #32d3a0;
        }

        .product-row.selected {
            border-color: #32d3a0;
            background-color: #f0fdf4;
        }

        /* Toast Styles */
        .toast {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #32d3a0;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        let pendingRemoveId = null;

        // Fallback showToast function if not defined
        if (typeof showToast === 'undefined') {
            function showToast(message, type) {
                // Simple fallback - just log to console
                console.log(message);
                // Could also use alert for debugging
                // alert(message);
            }
        }

        const currencySymbol = "{{ \App\Models\WebSetting::first()?->currency ?? '$' }}";
        const currencyPosition = "{{ config('app.currency_position', 'Prefix') }}";

        function formatCurrency(amount, symbol, position) {
            const value = Number(amount) || 0;
            const formatted = value.toFixed(2);
            return position === 'Suffix' ? `${formatted} ${symbol}` : `${symbol} ${formatted}`;
        }

        // Add to Cart function - shows quantity controls when clicked
        function filterByVariant(variantId) {
            const currentUrl = new URL(window.location.href);
            if (variantId) {
                currentUrl.searchParams.set('variant_id', variantId);
            } else {
                currentUrl.searchParams.delete('variant_id');
            }
            window.location.href = currentUrl.toString();
        }

        function addToCart(btn) {
            const section = btn.closest('.add-to-cart-section');
            const productId = section.dataset.productId;
            const price = parseFloat(section.dataset.price);

            // Add item to cart with quantity 1 via AJAX
            fetch("{{ route('cart.update', ['id' => '__ID__']) }}".replace('__ID__', productId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        quantity: 1
                    })
                }).then(response => response.json())
                .then(data => {
                    // Hide add button, show quantity controls
                    btn.classList.add('hidden');
                    const qtyControls = section.querySelector('.quantity-controls');
                    qtyControls.classList.remove('hidden');
                    qtyControls.classList.add('flex');

                    // Find and check the checkbox for this product
                    const row = section.closest('.product-row');
                    const checkbox = row.querySelector('.select-product');
                    checkbox.checked = true;
                    row.classList.add('selected');

                    // Update service checkbox
                    const serviceId = row.dataset.service;
                    updateServiceCheckbox(serviceId);
                    updateSummary();

                    showToast('Added to cart!', 'success');
                })
                .catch(error => {
                    showToast('Failed to add to cart', 'error');
                });
        }

        // Deselect item from order summary (without removing from cart)
        function deselectItem(productId) {
            // Find the product row and uncheck the checkbox
            const row = document.querySelector(`.product-row[data-id="${productId}"]`);
            if (row) {
                const checkbox = row.querySelector('.select-product');
                checkbox.checked = false;
                row.classList.remove('selected');

                // Update service checkbox
                const serviceId = row.dataset.service;
                updateServiceCheckbox(serviceId);
            }

            // Update the summary
            updateSummary();
            showToast('Item removed from order summary', 'success');
        }

        // Remove from Cart function - removes item completely without page reload
        function removeFromCart(productId) {
            fetch("{{ route('cart.remove', ['id' => '__ID__']) }}".replace('__ID__', productId), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Find the product row
                    const row = document.querySelector(`.product-row[data-id="${productId}"]`);
                    if (row) {
                        // Hide quantity controls, show Add to Cart button
                        const section = row.querySelector('.add-to-cart-section');
                        const qtyControls = section.querySelector('.quantity-controls');
                        qtyControls.classList.add('hidden');
                        qtyControls.classList.remove('flex');
                        const addBtn = section.querySelector('.add-btn');
                        addBtn.classList.remove('hidden');

                        // Uncheck the checkbox
                        const checkbox = row.querySelector('.select-product');
                        checkbox.checked = false;
                        row.classList.remove('selected');

                        // Update service checkbox
                        const serviceId = row.dataset.service;
                        updateServiceCheckbox(serviceId);
                    }

                    updateSummary();
                    showToast('Item removed from cart', 'success');
                })
                .catch(error => {
                    showToast('Failed to remove item', 'error');
                });
        }

        // Remove Modal Functions
        function showRemoveModal(btn) {
            pendingRemoveId = btn.dataset.id;
            const productName = btn.dataset.name;
            document.getElementById('confirm-remove-btn').textContent =
                `Remove "${productName.substring(0, 15)}${productName.length > 15 ? '...' : ''}"`;
            document.getElementById('remove-modal').classList.remove('hidden');
            document.getElementById('remove-modal').classList.add('flex');
        }

        function closeRemoveModal() {
            document.getElementById('remove-modal').classList.add('hidden');
            document.getElementById('remove-modal').classList.remove('flex');
            pendingRemoveId = null;
        }


        // Toggle product selection
        function toggleProductSelection(row, event) {
            // Ignore clicks from quantity controls or add to cart buttons
            if (event && event.target.closest('.quantity-controls, .add-to-cart-section, .qty-btn')) {
                return;
            }

            const checkbox = row.querySelector('.select-product');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }

            const serviceId = row.dataset.service;
            updateServiceCheckbox(serviceId);
            updateSummary();
        }

        // Update quantity via AJAX
        function updateQuantity(productId, quantity) {
            fetch("{{ route('cart.update', ['id' => '__ID__']) }}".replace('__ID__', productId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        quantity: quantity
                    })
                }).then(response => response.json())
                .then(data => {
                    // Optional: show toast on quantity update
                });
        }

        // Quantity button click handler
        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".qty-btn");
            if (btn && !btn.disabled) {
                // Stop propagation to prevent triggering parent row selection
                e.stopPropagation();

                const action = btn.dataset.action;
                const row = btn.closest(".product-row");
                const section = row.querySelector('.add-to-cart-section');
                const qtyElement = row.querySelector(".quantity");
                const totalElement = row.querySelector(".product-total");
                const productId = row.dataset.id;
                let price = parseFloat(row.dataset.price);
                let quantity = parseInt(qtyElement.textContent);

                if (action === "increase") {
                    quantity++;
                    // Find the decrease button (previous sibling of parent is the decrease button)
                    const decreaseBtn = btn.parentElement.querySelector('[data-action="decrease"]');
                    if (decreaseBtn) decreaseBtn.disabled = false;
                }
                if (action === "decrease" && quantity > 1) {
                    quantity--;
                    if (quantity === 1) {
                        btn.disabled = true;
                    }
                }

                // If quantity becomes 0, remove from cart and show Add to Cart button
                if (quantity === 0) {
                    // Remove from cart
                    fetch("{{ route('cart.remove', ['id' => '__ID__']) }}".replace('__ID__', productId), {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => response.json())
                        .then(data => {
                            // Hide quantity controls, show Add to Cart button
                            const qtyControls = section.querySelector('.quantity-controls');
                            qtyControls.classList.add('hidden');
                            qtyControls.classList.remove('flex');
                            const addBtn = section.querySelector('.add-btn');
                            addBtn.classList.remove('hidden');

                            // Uncheck the checkbox
                            const checkbox = row.querySelector('.select-product');
                            checkbox.checked = false;
                            row.classList.remove('selected');

                            updateSummary();
                            showToast('Item removed from cart', 'success');
                        });
                    return;
                }

                qtyElement.textContent = quantity;
                totalElement.textContent = formatCurrency(quantity * price, currencySymbol, currencyPosition);

                // Update service total
                const serviceId = row.dataset.service;
                updateServiceTotal(serviceId);

                updateQuantity(productId, quantity);
                updateSummary();
            }
        });

        // Update service total dynamically
        function updateServiceTotal(serviceId) {
            const serviceSection = document.querySelector(`.select-service[data-service="${serviceId}"]`).closest(
                '.rounded-2xl');
            const productRows = serviceSection.querySelectorAll('.product-row');
            let serviceTotal = 0;

            productRows.forEach(row => {
                const rawText = row.querySelector('.product-total').textContent;
                const total = parseFloat(rawText.replace(/[^0-9.-]+/g, '')) || 0;
                serviceTotal += total;
            });

            const totalElement = serviceSection.querySelector('.text-mint-600');
            if (totalElement && totalElement.textContent.includes('Service Total')) {
                totalElement.textContent = formatCurrency(serviceTotal, currencySymbol, currencyPosition);
            }
        }

        // Update summary
        function updateSummary() {
            let subtotal = 0;
            const selectedItemsContainer = document.getElementById("selected-items");
            selectedItemsContainer.innerHTML = '';
            let anySelected = false;
            let selectedCount = 0;

            document.querySelectorAll(".product-row").forEach(row => {
                const checkbox = row.querySelector(".select-product");
                if (checkbox.checked) {
                    anySelected = true;
                    selectedCount++;
                    const total = parseFloat(row.querySelector(".product-total").textContent.replace(/[^0-9.-]+/g, '')) || 0;
                    subtotal += total;

                    const imgSrc = row.querySelector("img").src;
                    const name = row.querySelector(".text-sm.font-medium").textContent;
                    const qty = row.querySelector(".quantity").textContent;

                    const div = document.createElement("div");
                    div.className = "flex items-center gap-3 p-2 bg-gray-50 rounded-lg";
                    div.dataset.productId = row.dataset.id;
                    div.innerHTML = `
                        <img src="${imgSrc}" class="w-10 h-10 rounded object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${name}</p>
                            <p class="text-xs text-gray-500">Qty: ${qty}</p>
                        </div>
                        <p class="text-sm font-semibold text-mint-600 flex-shrink-0">${total.toFixed(2)}</p>
                        <button type="button" onclick="deselectItem('${row.dataset.id}')" class="remove-item-btn p-2 rounded-full text-red-500 hover:bg-red-100 transition-all flex-shrink-0"
                            title="Remove item">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>`;
                    selectedItemsContainer.appendChild(div);
                }
            });

            if (!anySelected) {
                selectedItemsContainer.innerHTML = '<p class="text-gray-400 text-center py-4">No items selected</p>';
            }

            // Calculate discount if coupon is applied
            let discount = 0;
            if (window.appliedCoupon) {
                if (window.appliedCoupon.discount_type === 'percent') {
                    discount = (subtotal / 100) * window.appliedCoupon.discount;
                } else {
                    discount = window.appliedCoupon.discount;
                }
            }

            let tax = subtotal * ({{ $taxRate ?? 0 }} / 100);
            let grandTotal = subtotal + tax - discount;
            if (grandTotal < 0) grandTotal = 0;

            document.getElementById("subtotal").textContent = formatCurrency(subtotal, currencySymbol, currencyPosition);
            document.getElementById("tax").textContent = formatCurrency(tax, currencySymbol, currencyPosition);
            document.getElementById("discount").textContent = "-" + formatCurrency(discount, currencySymbol, currencyPosition);
            document.getElementById("grand-total").textContent = formatCurrency(grandTotal, currencySymbol, currencyPosition);

            // Show/hide discount row
            const discountRow = document.getElementById('discount-row');
            if (discount > 0) {
                discountRow.style.display = 'flex';
            } else {
                discountRow.style.display = 'none';
            }

            // Update checkout button state
            const checkoutBtn = document.getElementById("checkout-btn");
            if (checkoutBtn) {
                const authDisabled = checkoutBtn.dataset.authDisabled === '1';
                checkoutBtn.disabled = !anySelected || authDisabled;

                const btnText = checkoutBtn.querySelector('span');
                if (btnText && !authDisabled) {
                    btnText.textContent = anySelected ? 'Proceed To Checkout' : 'Select items to checkout';
                }
            }
        }
        // Coupon Application
        let appliedCoupon = null;
        window.appliedCoupon = null;

        function applyCoupon() {
            const couponCode = document.getElementById("coupon-code").value.trim();
            const messageEl = document.getElementById("coupon-message");
            const applyBtn = document.getElementById("apply-coupon-btn");

            if (!couponCode) {
                showCouponMessage("Please enter a coupon code", "error");
                return;
            }

            // Get current subtotal for validation
            let subtotal = 0;
            document.querySelectorAll(".product-row").forEach(row => {
                const checkbox = row.querySelector(".select-product");
                if (checkbox.checked) {
                    const total = parseFloat(row.querySelector(".product-total").textContent.replace(/[^0-9.-]+/g, '')) || 0;
                    subtotal += total;
                }
            });

            if (subtotal === 0) {
                showCouponMessage("Please select items first to apply coupon", "error");
                return;
            }

            applyBtn.disabled = true;
            applyBtn.innerHTML = '<span class="loading-spinner"></span>';

            // Call coupon API
            fetch("{{ route('cart.applyCoupon') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        code: couponCode,
                        amount: subtotal
                    })
                })
                .then(response => response.json())
                .then(data => {
                    applyBtn.disabled = false;
                    applyBtn.innerHTML = "Apply";

                    if (data.success === false) {
                        showCouponMessage(data.message || "Invalid coupon", "error");
                        return;
                    }

                    // Store coupon data
                    window.appliedCoupon = data.coupon;
                    document.getElementById("coupon_id").value = data.coupon.id;

                    // Show success message
                    const discountText = data.coupon.discount_type === "percent" ?
                        data.coupon.discount + "% off" :
                        "$" + data.coupon.discount + " off";

                    document.getElementById("coupon-code-display").textContent = data.coupon.code;
                    document.getElementById("coupon-discount-display").textContent = "(" + discountText + ")";

                    document.getElementById("applied-coupon").classList.remove("hidden");
                    document.getElementById("coupon-code").value = "";
                    messageEl.classList.add("hidden");

                    showToast("Coupon applied successfully!", "success");
                    updateSummary();
                })
                .catch(error => {
                    applyBtn.disabled = false;
                    applyBtn.innerHTML = "Apply";
                    showCouponMessage("Failed to apply coupon. Please try again.", "error");
                });
        }

        function removeCoupon() {
            window.appliedCoupon = null;
            document.getElementById("coupon_id").value = "";
            document.getElementById("applied-coupon").classList.add("hidden");
            showToast("Coupon removed", "success");
            updateSummary();
        }

        function showCouponMessage(message, type) {
            const messageEl = document.getElementById("coupon-message");
            messageEl.textContent = message;
            messageEl.className = "mt-2 text-sm " + (type === "success" ? "text-green-600" : "text-red-500");
            messageEl.classList.remove("hidden");
        }

        // Handle Enter key on coupon input
        document.getElementById("coupon-code").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                applyCoupon();
            }
        });


        // Select All functionality
        document.getElementById("select-all").addEventListener("change", function() {
            const checked = this.checked;
            document.querySelectorAll(".select-service, .select-product").forEach(cb => cb.checked = checked);
            document.querySelectorAll(".product-row").forEach(row => {
                if (checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            });
            updateSummary();
        });

        // Checkbox listeners
        document.querySelectorAll(".select-product").forEach(cb => {
            cb.addEventListener("change", function() {
                const row = cb.closest('.product-row');
                if (cb.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
                updateSummary();
            });
        });

        document.querySelectorAll(".select-service").forEach(cb => {
            cb.addEventListener("change", function() {
                const serviceId = cb.dataset.service;
                document.querySelectorAll(`.product-row[data-service="${serviceId}"] .select-product`)
                    .forEach(p => {
                        p.checked = cb.checked;
                        const row = p.closest('.product-row');
                        if (cb.checked) {
                            row.classList.add('selected');
                        } else {
                            row.classList.remove('selected');
                        }
                    });
                updateSummary();
            });
        });

        // Update service checkbox state
        function updateServiceCheckbox(serviceId) {
            const serviceCheckbox = document.querySelector(`.select-service[data-service="${serviceId}"]`);
            const productCheckboxes = document.querySelectorAll(
                `.product-row[data-service="${serviceId}"] .select-product`);
            const allChecked = Array.from(productCheckboxes).every(p => p.checked);
            const someChecked = Array.from(productCheckboxes).some(p => p.checked);

            if (allChecked) {
                serviceCheckbox.checked = true;
                serviceCheckbox.indeterminate = false;
            } else if (someChecked) {
                serviceCheckbox.checked = false;
                serviceCheckbox.indeterminate = true;
            } else {
                serviceCheckbox.checked = false;
                serviceCheckbox.indeterminate = false;
            }

            // Update select all checkbox
            const allProducts = document.querySelectorAll(".select-product");
            const allSelected = Array.from(allProducts).every(p => p.checked);
            const someSelected = Array.from(allProducts).some(p => p.checked);
            const selectAll = document.getElementById("select-all");

            if (allSelected) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (someSelected) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }

        // Form submit
        document.getElementById("checkout-form").addEventListener("submit", function(e) {
            // Check if user is authenticated
            @auth
            // Check if user account is active
            @if (!auth()->user()->is_active)
                e.preventDefault();
                showToast('Your account is inactive. Please contact support.', 'error');
                return;
            @endif

            // Check if user email or mobile is verified
            @if (!auth()->user()->email_verified_at && !auth()->user()->mobile_verified_at)
                e.preventDefault();
                showToast('Please verify your email address to checkout.', 'error');
                return;
            @endif
        @else
            e.preventDefault();
            showToast('Please login to proceed with checkout.', 'error');
            window.location.href = '{{ route('website.login') }}?redirect=' + encodeURIComponent(window.location
                .href);
            return;
        @endauth

        const selectedRows = document.querySelectorAll(".product-row .select-product:checked");
        const selectedIds = Array.from(selectedRows)
            .map(cb => cb.closest(".product-row").dataset.id);

        if (selectedIds.length === 0) {
            e.preventDefault();
            showToast('Please select at least one item to proceed', 'error');
            return;
        }

        const quantities = {}; selectedRows.forEach(cb => {
            const row = cb.closest(".product-row");
            const id = row.dataset.id;
            const qty = parseInt(row.querySelector(".quantity").textContent);
            quantities[id] = qty;
        });

        document.getElementById("selected_ids").value = selectedIds.join(","); document.getElementById("quantities")
        .value = JSON.stringify(quantities);
        });

        // Close modal on outside click
        // document.getElementById('remove-modal').addEventListener('click', function(e) {
        //     if (e.target === this) {
        //         closeRemoveModal();
        //     }
        // });

        function resetSelectionState() {
            document.querySelectorAll('.select-product, .select-service, #select-all').forEach(cb => {
                if (cb) cb.checked = false;
            });
            document.querySelectorAll('.product-row').forEach(row => row.classList.remove('selected'));
            document.getElementById('selected_ids').value = '';
            const quantitiesInput = document.getElementById('quantities');
            if (quantitiesInput) quantitiesInput.value = '{}';
            updateSummary();
        }

        document.addEventListener('DOMContentLoaded', function() {
            resetSelectionState();

            const removeModal = document.getElementById('remove-modal');

            if (removeModal) {
                removeModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRemoveModal();
                    }
                });
            }

        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRemoveModal();
            }
        });

        // Event delegation for remove item buttons in selected items
        document.addEventListener('click', function(e) {
            // Check if clicked on remove button in selected-items container
            const selectedItemsContainer = document.getElementById('selected-items');
            if (!selectedItemsContainer.contains(e.target)) {
                return;
            }

            const removeBtn = e.target.closest('.remove-item-btn');
            if (removeBtn) {
                e.preventDefault();
                const itemDiv = removeBtn.closest('div[data-product-id]');
                if (itemDiv) {
                    const productId = itemDiv.dataset.productId;
                    if (productId && productId.length > 0) {
                        removeFromCart(productId);
                    }
                }
            }
        });
    </script>



@endsection

@push('scripts')
    <script>
        const currencySymbol = "{{ \App\Models\WebSetting::first()?->currency ?? '$' }}";
        const currencyPosition = "{{ config('app.currency_position', 'Prefix') }}";

        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.select-product, .select-service');
            const savedStates = JSON.parse(localStorage.getItem('checkboxStates')) || {};

            checkboxes.forEach((cb, index) => {
                if (savedStates[index] !== undefined) {
                    cb.checked = savedStates[index];
                    const row = cb.closest('.product-row');
                    if (cb.checked && row) row.classList.add('selected');
                }

                cb.addEventListener('change', () => {
                    const row = cb.closest('.product-row');
                    if (row) {
                        if (cb.checked) {
                            row.classList.add('selected');
                        } else {
                            row.classList.remove('selected');
                        }
                    }
                    updateSummary();
                });
            });


            const form = document.getElementById('orderForm');
            if (form) {
                form.addEventListener('submit', (e) => {
                    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                    if (!anyChecked) {
                        e.preventDefault();
                        alert('No items selected.');
                    }
                });
            }



            updateSummary();
        });


        function updateSummary() {
            const selectedItemsContainer = document.getElementById('selected-items');
            const checkboxes = document.querySelectorAll('.select-product:checked, .select-service:checked');
            let subtotal = 0;

            if (selectedItemsContainer) {
                selectedItemsContainer.innerHTML = '';
            }

            if (checkboxes.length === 0) {
                if (selectedItemsContainer) {
                    selectedItemsContainer.innerHTML = '<p class="text-gray-400 text-center py-4">No items selected</p>';
                }
            } else {
                checkboxes.forEach(cb => {
                    const row = cb.closest('.product-row');
                    if (row) {
                        const id = row.dataset.id;
                        const name = row.dataset.name || 'Item';
                        const price = parseFloat(row.dataset.price) || 0;
                        const qtyElement = row.querySelector('.quantity');
                        const qty = qtyElement ? parseInt(qtyElement.textContent) : 1;

                        const itemTotal = price * qty;
                        subtotal += itemTotal;

                        if (selectedItemsContainer) {
                            const itemHtml = `
                            <div class="flex justify-between items-center group py-2 border-b border-gray-50 last:border-0">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 line-clamp-1">${name}</p>
                                    <p class="text-xs text-gray-500">Qty: ${qty}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        ${formatCurrency(itemTotal, currencySymbol, currencyPosition)}
                                    </p>
                                    <button type="button" onclick="deselectItem('${id}')"
                                        class="text-red-500 hover:text-red-700 transition-colors p-1 bg-red-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>`;
                            selectedItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
                        }
                    }
                });
            }

            const taxRate = {{ $taxRate ?? 0 }} / 100;
            const taxAmount = subtotal * taxRate;
            const grandTotal = subtotal + taxAmount;

            const subtotalDisplay = document.getElementById('subtotal');
            const taxDisplay = document.getElementById('tax');
            const grandTotalDisplay = document.getElementById('grand-total');

            if (subtotalDisplay) subtotalDisplay.innerHTML = formatCurrency(subtotal, currencySymbol, currencyPosition);
            if (taxDisplay) taxDisplay.innerHTML = formatCurrency(taxAmount, currencySymbol, currencyPosition);
            if (grandTotalDisplay) grandTotalDisplay.innerHTML = formatCurrency(grandTotal, currencySymbol,
                currencyPosition);


            const currentState = {};
            document.querySelectorAll('.select-product, .select-service').forEach((cb, index) => {
                currentState[index] = cb.checked;
            });
            localStorage.setItem('checkboxStates', JSON.stringify(currentState));

            updateHiddenInputs();
        }

        function formatCurrency(amount, symbol, position) {
            let formatted = amount.toFixed(2);
            return position === 'Suffix' ? `${formatted} ${symbol}` : `${symbol} ${formatted}`;
        }

        function updateHiddenInputs() {
            const selectedIds = [];
            const quantities = {};

            document.querySelectorAll('.select-product:checked').forEach(cb => {
                const row = cb.closest('.product-row');
                if (row) {
                    const id = row.dataset.id;
                    const qty = row.querySelector('.quantity')?.textContent || 1;
                    selectedIds.push(id);
                    quantities[id] = qty;
                }
            });

            const idsInput = document.getElementById('selected_ids');
            const qtysInput = document.getElementById('quantities');

            if (idsInput) idsInput.value = JSON.stringify(selectedIds);
            if (qtysInput) qtysInput.value = JSON.stringify(quantities);
        }

        function deselectItem(id) {
            const row = document.querySelector(`.product-row[data-id="${id}"]`);
            if (row) {
                const checkbox = row.querySelector('.select-product, .select-service');
                if (checkbox) {
                    checkbox.checked = false;
                    row.classList.remove('selected');
                    updateSummary();
                }
            }
        }





        // window.clearCart = function () {

        //     fetch('/cart/clear', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        //         }
        //     })
        //     .then(res => res.json())
        //     .then(data => {
        //         if (data.success) {

        //             // UI reset
        //             document.querySelectorAll('.select-product, .select-service').forEach(cb => {
        //                 cb.checked = false;
        //             });

        //             document.querySelectorAll('.product-row').forEach(row => {
        //                 row.classList.remove('selected');
        //             });

        //             localStorage.removeItem('checkboxStates');

        //             document.getElementById('selected-items').innerHTML =
        //                 '<p class="text-gray-400 text-center py-4">No items selected</p>';

        //             updateSummary();
        //         }
        //     });
        // };
    </script>
@endpush
