@extends('website.layouts.app')

@section('content')
    @if (session('success'))
        <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg text-center">
            <div
                class="text-4xl mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-pink-500 to-yellow-500">
                ðŸ—¡ï¸ {{ session('success') }} !
            </div>
        </div>
    @endif
    <!-- manage-address-modal -->
    <section id="role" class="modal_container">

        <!-- backdrop -->
        <div onclick="toggleModal('role')" class="modal_backdrop"></div>

        <!-- modal content -->
        <div class="modal_content">
            <form action="#" class="rs-manage-addresses-form">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold">{{ __('Add New Address') }}</h3>
                    <button type="button" onclick="toggleModal('role')"
                        class="transition-transform duration-300 hover:rotate-90">
                        <img src="{{ asset('website/assets/icons/close.svg') }}" alt="">
                    </button>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">{{ __('Label') }}</label>
                    <input type="text" placeholder="Example : {{ __('Home') }}, Office ..."
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">{{ __('Street Address') }}</label>
                    <input type="text" placeholder="123 Lovely Road, Apt 6B"
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">{{ __('City') }}</label>
                        <input type="text" placeholder="New York"
                            class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">{{ __('State') }}</label>
                        <input type="text" placeholder="NY"
                            class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">{{ __('ZIP Code') }}</label>
                    <input type="text" placeholder="10003"
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-[30px]">
                    <label class="text-neutral-700 text-base font-medium">{{ __('Phone Number') }}</label>
                    <input type="text" placeholder="+1 (555) 545-5421"
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <button
                    class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white h-[48px] text-center leading-[48px] w-[100%] rounded-xl">
                    {{ __('Save Address') }}
                </button>
            </form>
        </div>
    </section>

    <main>
        <!-- breadcrumb -->
        <section
            class="rs-breadcrumb-area bg-[#1A7058] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    Order Details
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="{{ route('web.showLogin') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Dashboard / ') }}</a>
                    <a href="{{ route('web.my.orders') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('My Orders / ') }}</a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">Order
                        #{{ $order->order_code }}</a>
                </div>
            </div>
        </section>

        <!-- Order Details area -->
        <section class="rs-order-details-section pt-[60px] pb-[80px] px-4 xl:px-0 bg-neutral-50">
            <div class="rs-order-details-area max-w-2lg mx-auto flex flex-col lg:flex-row gap-[23.5px]">

                <!-- Left Column -->
                <div class="rs-order-details-left-area w-full lg:w-2/3 bg-white p-4 md:p-6 rounded-3xl">
                    <div class="rs-order-details-content mb-[24px]">
                        <div class="rs-order-details-content-top flex justify-between items-center mb-4">
                            <div class="flex flex-col gap-2">
                                <h4 class="text-sm md:text-base text-neutral-700 font-semibold leading-[140%]">
                                    Order #{{ $order->order_code }}
                                </h4>
                                <div class="flex gap-2">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $order->payment_status == 'Paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $order->payment_status ?? 'Pending' }}
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                        {{ $order->order_status ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>
                            <button
                                class="flex text-center gap-1 text-xs text-neutral-700 font-medium w-[88px] h-[40px] justify-center items-center border-[1.5px] border-neutral-200 rounded-xl">
                                <img class="w-3 h-3" src="{{ asset('website/assets/icons/download-icon.svg') }}"
                                    alt="">
                                Invoice
                            </button>
                        </div>

                        {{-- Order Steps --}}
                        @php
                            $steps = [
                                [
                                    'title' => 'Order Placed',
                                    'desc' => 'Your order has been confirmed',
                                    'time' => $order->created_at,
                                ],
                                [
                                    'title' => 'Pickup Scheduled',
                                    'desc' => 'Driver assigned and on the way',
                                    'time' => $order->pick_date_time,
                                ],
                                [
                                    'title' => 'Items Collected',
                                    'desc' => 'Your items have been picked up',
                                    'time' => $order->pick_date_time,
                                ],
                                [
                                    'title' => 'In Processing',
                                    'desc' => 'Your items are being cleaned',
                                    'time' => 'In Progress',
                                ],
                                [
                                    'title' => 'Quality Check',
                                    'desc' => 'Final inspection before delivery',
                                    'time' => 'Pending',
                                ],
                                [
                                    'title' => 'Out for Delivery',
                                    'desc' => 'On the way to your location',
                                    'time' => 'Pending',
                                ],
                                [
                                    'title' => 'Delivered',
                                    'desc' => 'Order completed successfully',
                                    'time' => $order->delivery_date_time,
                                ],
                            ];
                        @endphp

                        @foreach ($steps as $step)
                            @php
                                $stepTime = $step['time'];
                                $isPending = in_array($stepTime, ['Pending', 'In Progress']);
                            @endphp
                            <div class="rs-order-info-item flex gap-[16px] mb-[20px]">
                                <button
                                    class="rs-order-info-icon flex-[0_0_auto] w-[40px] h-[40px] flex items-center justify-center {{ $isPending ? 'bg-neutral-100' : 'bg-mint-50' }}">
                                    @if ($step['time'] != 'Pending')
                                        <img class="w-[18px] h-[18px]"
                                            src="{{ asset('website/assets/icons/check-green.svg') }}" alt="">
                                    @else
                                        <img class="w-[18px] h-[18px]"
                                            src="{{ asset('website/assets/icons/time-icon.svg') }}" alt="">
                                    @endif
                                </button>
                                <div class="rs-order-info-right">
                                    <h5 class="text-xs md:text-sm text-neutral-700 font-medium leading-[100%] mb-[10px]">
                                        {{ $step['title'] }}</h5>
                                    <p class="text-xs md:text-sm text-neutral-500 font-normal leading-[140%] italic">
                                        {{ $step['desc'] }}</p>
                                    <span class="text-xs md:text-sm text-neutral-500 font-normal leading-[140%]">
                                        @if ($step['time'] instanceof \Carbon\Carbon)
                                            {{ $step['time']->format('M d, Y - h:i A') }}
                                        @else
                                            {{ $step['time'] }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- {{ __('Order Items') }} --}}
                    <div class="rs-order-items-area">
                        <h5 class="text-sm md:text-base text-neutral-700 font-semibold leading-[140%] mb-[15px]">{{ __('Order Items') }}
                        </h5>
                        @forelse($order->products as $product)
                            @php
                                $price = $product->discount_price ?? $product->price;
                                $thumbnail = $product->thumbnailPath ?? asset('website/assets/images/default.png');
                            @endphp
                            <div
                                class="rs-order-items-box flex items-center gap-[16px] border-[1px] border-neutral-200 rounded-xl px-[12px] py-[11px] mb-[15px]">
                                <div
                                    class="rs-order-items-icon w-[48px] h-[48px] sm:w-[56px] sm:h-[56px] bg-mint-50 rounded-[4px] flex items-center justify-center overflow-hidden">
                                    <img class="w-full h-full object-cover" src="{{ asset($thumbnail) }}"
                                        alt="{{ $product->name }}">
                                </div>
                                <div class="rs-order-items-box-right-content">
                                    <h5 class="text-xs md:text-sm text-neutral-900 font-medium leading-[140%]">
                                        {{ $product->name }}</h5>
                                    <span class="text-xs md:text-sm text-neutral-500 font-normal leading-[140%]">Quantity:
                                        {{ $product->pivot->quantity }}</span>
                                    <span class="text-xs text-green-600 font-medium">{{ currencyPosition($price, 2) }}
                                        each</span>
                                </div>
                                <div class="rs-order-items-price ml-auto">
                                    <span
                                        class="text-sm md:text-base text-neutral-900 font-semibold leading-[140%]">{{ currencyPosition($price * $product->pivot->quantity, 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">{{ __('No items found') }}</p>
                        @endforelse
                    </div>

                    {{-- Pickup & Delivery Details --}}
                    <div class="rs-pickup-details-area flex flex-wrap sm:flex-nowrap gap-[24px]">
                        <div
                            class="rs-pickup-details-box border-[1px] border-neutral-200 rounded-xl py-[15px] px-[24px] w-full sm:w-[50%]">
                            <h5 class="text-sm text-neutral-700 font-medium leading-[140%] mb-[24px]">Pickup Details</h5>
                            @if ($order->address)
                                <a href="javascript:void(0)"
                                    class="leading-[140%] mb-[10px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                    <img class="self-start mt-[4px]"
                                        src="{{ asset('website/assets/icons/location.svg') }}" alt="">
                                    {{ $order->address->address_line ?? '' }}{{ $order->address->city ? ', ' . $order->address->city : '' }}{{ $order->address->state ? ', ' . $order->address->state : '' }}{{ $order->address->zip ? ' ' . $order->address->zip : '' }}
                                </a>
                            @endif
                            <a href="javascript:void(0)"
                                class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                <img src="{{ asset('website/assets/icons/celender.svg') }}" alt="">
                                @if ($order->pick_date_time)
                                    {{ $order->pick_date_time->format('M d, Y - h:i A') }}
                                @else
                                    Not scheduled
                                @endif
                            </a>
                        </div>
                        <div
                            class="rs-pickup-details-box border-[1px] border-neutral-200 rounded-xl py-[15px] px-[24px] w-full sm:w-[50%]">
                            <h5 class="text-sm text-neutral-700 font-medium leading-[140%] mb-[24px]">Delivery Details</h5>
                            @if ($order->address)
                                <a href="javascript:void(0)"
                                    class="leading-[140%] mb-[10px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                    <img class="self-start mt-[4px]"
                                        src="{{ asset('website/assets/icons/location.svg') }}" alt="">
                                    {{ $order->address->address_line ?? '' }}{{ $order->address->city ? ', ' . $order->address->city : '' }}{{ $order->address->state ? ', ' . $order->address->state : '' }}{{ $order->address->zip ? ' ' . $order->address->zip : '' }}
                                </a>
                            @endif
                            <a href="javascript:void(0)"
                                class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                <img src="{{ asset('website/assets/icons/celender.svg') }}" alt="">
                                @if ($order->delivery_date_time)
                                    {{ $order->delivery_date_time->format('M d, Y - h:i A') }}
                                @else
                                    To be scheduled
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="rs-order-details-rightarea w-full lg:w-1/3">

                    {{-- Store Details --}}
                    <div class="rs-store-details-box bg-white px-[16px] py-[24px] rounded-3xl mb-[24px]">
                        <h5 class="text-sm md:text-base text-neutral-700 font-semibold leading-[140%] mb-[16px]">Store
                            Details</h5>
                        @if ($order->products->first() && $order->products->first()->service)
                            <h6 class="text-sm text-neutral-700 font-semibold leading-[140%] mb-[5px]">
                                {{ $order->products->first()->service->name ?? 'Fresh & Fold Dry Cleaning' }}</h6>
                            <div class="flex gap-[3px] pb-[16px] border-b border-b-neutral-200 mb-[16px]">
                                <img src="{{ asset('website/assets/icons/star-small.svg') }}" alt="">
                                <span class="text-sm text-neutral-700 font-medium leading-[140%]">4.8</span>
                                <span class="text-sm text-neutral-700 font-normal leading-[140%]">(512 reviews)</span>
                            </div>
                        @endif
                        <div class="rs-details-btn">
                            <a href="tel:+15551234567"
                                class="leading-[100%] text-xs font-medium flex gap-[6px] text-neutral-700 flex items-center h-[40px] px-[16px] border-[1.50px] border-neutral-200 rounded-xl mb-[10px]">
                                <img class="w-[12px] h-[12px]" src="{{ asset('website/assets/icons/call.svg') }}"
                                    alt="">
                                +1 (555) 123-4567
                            </a>
                            <a href="#"
                                class="leading-[100%] text-xs font-medium flex gap-[6px] text-neutral-700 flex items-center h-[40px] px-[16px] border-[1.50px] border-neutral-200 rounded-xl">
                                <img class="w-[12px] h-[12px]" src="{{ asset('website/assets/icons/comment.svg') }}"
                                    alt="">
                                Chat With Store
                            </a>
                        </div>
                    </div>

                    {{-- Payment Summary --}}
                    <div class="rs-payment-details-box bg-white px-[16px] py-[24px] rounded-3xl mb-[24px]">
                        <h5 class="text-sm md:text-base text-neutral-700 font-semibold leading-[140%] mb-[16px]">Payment
                            Summary</h5>
                        <div class="rs-payment-info-item flex justify-between mb-[10px]">
                            <span class="text-sm font-medium leading-[140%] text-neutral-500">Subtotal</span>
                            <span
                                class="text-sm md:text-base font-normal leading-[140%] text-neutral-700">{{ currencyPosition($order->amount, 2) }}</span>
                        </div>
                        <div class="rs-payment-info-item flex justify-between mb-[10px]">
                            <span class="text-sm font-medium leading-[140%] text-neutral-500">Discount</span>
                            <span
                                class="text-sm md:text-base font-normal leading-[140%] text-neutral-700">{{ currencyPosition($order->discount ?? 0, 2) }}</span>
                        </div>
                        <div class="rs-payment-info-item flex justify-between mb-[10px]">
                            <span class="text-sm font-medium leading-[140%] text-neutral-500">Delivery Fee</span>
                            <span
                                class="text-sm md:text-base font-normal leading-[140%] text-neutral-700">{{ $order->delivery_charge ?? '' }}

                        </div>
                    </div>
        </section>
    </main>
   
@endsection

@push('scripts')

@if(session('success'))
<script>
    localStorage.setItem('checkboxStates', JSON.stringify({}));
</script>
@endif

@endpush
