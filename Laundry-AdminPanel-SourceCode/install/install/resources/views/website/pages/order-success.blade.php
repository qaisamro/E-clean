@extends('website.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <!-- Success Icon -->
        <div class="mb-8">
            <div class="w-24 h-24 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <!-- Success Message -->
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Order Placed Successfully!</h1>
        <p class="text-gray-600 mb-8">
            Thank you for your order. Your order has been placed successfully.
            @if ($order->payment_type === 'cod')
                Payment will be collected on delivery.
            @else
                Your payment has been processed successfully.
            @endif
        </p>

        <!-- Order Info Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 text-left mb-8">
            <div class="border-b pb-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Order Details</h2>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Code</span>
                    <span class="font-semibold">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pick-up Date</span>
                    <span class="font-semibold">{{ $order->pick_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method</span>
                    <span class="font-semibold uppercase">{{ $order->payment_type }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Status</span>
                    <span
                        class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Status</span>
                    <span class="font-semibold text-green-600">{{ ucfirst($order->order_status) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t">
                    <span class="text-gray-800 font-semibold">Total Amount</span>
                    <span class="text-green-600 font-bold text-xl">{{ currencyPosition($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        @if ($order->products && $order->products->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg p-6 text-left mb-8">
                <div class="border-b pb-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
                </div>

                <div class="space-y-4">
                    @foreach ($order->products as $product)
                        @php
                            $price = $product->discount_price ?? $product->price;
                            $quantity = $product->pivot->quantity;
                        @endphp
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset($product->thumbnailPath ?? 'website/assets/images/default.png') }}"
                                    class="w-12 h-12 rounded object-cover">
                                <div>
                                    <p class="font-medium">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">Qty: {{ $quantity }}</p>
                                    <p class="text-xs text-green-600">{{ currencyPosition($price, 2) }} each</p>
                                </div>
                            </div>
                            <span class="font-semibold">{{ currencyPosition($price * $quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Delivery Address -->
        @if ($order->address)
            <div class="bg-white rounded-2xl shadow-lg p-6 text-left mb-8">
                <div class="border-b pb-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Delivery Address</h2>
                </div>

                <div class="space-y-2">
                    <p class="font-medium">{{ $order->address->address_name }}</p>
                    <p class="text-gray-600 text-sm">
                        {{ collect([
                            $order->address->house_no,
                            $order->address->flat_no,
                            $order->address->road_no,
                            $order->address->block,
                            $order->address->area,
                            $order->address->post_code,
                        ])->filter()->implode(', ') }}
                    </p>

                    @if ($order->address->delivery_note)
                        <p class="text-gray-500 text-sm italic">{{ $order->address->delivery_note }}</p>
                    @endif
                </div>
            </div>
        @endif
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('order.details', $order->id) }}"
                class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                View Order Details
            </a>
            <a href="{{ route('web.cart') }}"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                Continue Shopping
            </a>
        </div>
    </div>
@endsection

@push('scripts')

@if(session('success'))
<script>
    localStorage.setItem('checkboxStates', JSON.stringify({}));
</script>
@endif

@endpush

