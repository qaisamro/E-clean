@extends('website.layouts.app')

@section('content')

    <!-- breadcrumb -->
    <section
        style="background:linear-gradient(135deg,#083744,#156172),url('{{ asset('website/assets/images/header/breadcrumb.png') }}');background-size:cover;background-position:center;" class="rs-breadcrumb-area bg-[linear-gradient(135deg,#083744,#156172)] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
        <div class="rs-breadcrumb-content">
            <h1
                class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px] md:text-4xl text-white font-semibold leading-[140%]">
                {{ __('My Orders') }}
            </h1>
            <div class="rs-breadcrumb-top-content">
                <a href="{{ route('web.settings') }}"
                    class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Dashboard') }}
                    / </a>
                <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('My Orders') }}</a>
            </div>
        </div>
    </section>

    <!-- {{ __('My Orders') }} area -->
    <section class="rs-order-details-section pt-[60px] pb-[80px] px-4 xl:px-0 bg-[#FAF7F2]">
        <div class="rs-order-details-area max-w-2lg mx-auto">
            <a href="{{ route('web.settings') }}">
                <button class="bg-[linear-gradient(135deg,#028090,#00A896)] text-white px-4 py-2 rounded-[12px] hover:bg-[#156172] transition">
                    <i class="ml-2 fas fa-arrow-left"></i>
                    {{ __('Back to Settings') }}
                </button>
            </a>
            @if ($orders->isEmpty())
                <div class="bg-white p-8 rounded-[20px] text-center">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No Orders Yet') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __("You haven't placed any orders yet. Start shopping to see your orders here.") }}
                    </p>
                    <a href="{{ route('web.services') }}"
                        class="inline-block bg-[linear-gradient(135deg,#028090,#00A896)] text-white px-6 py-3 rounded-[12px] hover:bg-[#156172] transition">
                        {{ __('Browse') }} {{ __('Services') }}
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <div class="bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] p-4 md:p-6 rounded-[20px] border border-[#E2E8F0]">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4">
                                <div>
                                    <h4 class="text-base md:text-lg font-semibold text-gray-800">
                                        {{ __('Order Code') }} {{ $order->order_code }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $order->created_at->locale(app()->getLocale())->translatedFormat('M d, Y h:i A') }}
                                    </p>
                                </div>
                                <div class="flex gap-2 flex-wrap">
                                    @php
                                        $statusClass = match ($order->order_status) {
                                            'Pending' => 'status-pending',
                                            'Order confirmed',
                                            'Picked up',
                                            'Processing',
                                            'On Going'
                                                => 'status-processing',
                                            'Delivered' => 'status-delivered',
                                            'Cancelled' => 'status-cancelled',
                                            default => 'status-pending',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusClass }}">
                                        {{ __($order->order_status ?? 'Pending') }}
                                    </span>
                                    <span
                                        class="px-3 py-1 text-xs rounded-full {{ $order->payment_status == 'Paid' ? 'bg-[#E6F4F1] text-[#156172]' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ __($order->payment_status ?? 'Pending') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="border-t border-[#E2E8F0] pt-4">
                                @if ($order->products && $order->products->count() > 0)
                                    <div class="space-y-3">
                                        @foreach ($order->products as $product)
                                            <div class="flex items-center gap-4">
                                                @if ($product->service && $product->service->thumbnailPath)
                                                    <img src="{{ asset($product->service->thumbnailPath) }}"
                                                        alt="{{ $product->service->name_bn ?? $product->service->name ?? __('Service') }}"
                                                        class="w-16 h-16 rounded-[12px] object-cover">
                                                @else
                                                    <div
                                                        class="w-16 h-16 rounded-[12px] bg-gray-100 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-800">
                                                        {{ $product->service->name_bn ?? $product->service->name ?? __('Service') }}</p>
                                                    <p class="text-sm text-gray-500">{{ __('Qty') }}:
                                                        {{ $product->pivot->quantity ?? 1 }}</p>
                                                </div>
                                                <p class="font-semibold text-[#028090]">
                                                    {{ currencyPosition($product->final_price ?? 0, 2) }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">{{ __('No products in this order') }}</p>
                                @endif
                            </div>

                            <!-- Order Total & Action -->
                            <div
                                class="border-t border-[#E2E8F0] mt-4 pt-4 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Total Amount') }}</p>
                                    <p class="text-xl font-bold text-[#028090]">
                                        {{ currencyPosition($order->total_amount ?? 0, 2) }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @if (!$order->payment_status || $order->payment_status === config('enums.payment_status.pending'))
                                        <a href="{{ route('web.order.payment', $order->id) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-[12px] bg-[linear-gradient(135deg,#028090,#00A896)] text-white hover:bg-[#156172] transition">
                                            {{ __('Pay Now') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('web.orders', $order->id) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-[12px] border border-[#028090] text-[#028090] hover:bg-[#E6F4F1] transition">
                                        {{ __('View Details') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
