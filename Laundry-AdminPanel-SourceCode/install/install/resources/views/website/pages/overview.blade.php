@php
    $menuItems = [
        [
            'label' => 'Overview',
            'icon' => 'user.svg',
            'route' => 'web.overview',
        ],
        [
            'label' => 'My Orders',
            'icon' => 'calendar2.svg',
            'route' => 'web.my.orders',
        ],
        [
            'label' => 'Addresses',
            'icon' => 'map-pin-gray.svg',
            'route' => 'web.addresses',
        ],
        [
            'label' => 'Favorites',
            'icon' => 'heart.svg',
            'route' => 'web.favourite',
        ],
        [
            'label' => 'Settings',
            'icon' => 'settings.svg',
            'route' => 'web.settings',
        ],
    ];
@endphp

@extends('website.layouts.app')

@section('content')
    @if (session('success'))
        <div class="max-w-2lg mx-auto mt-4 px-4 py-3 rounded-xl bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 w-full h-full bg-gradient-to-tl from-mint-500 from-10% via-mint-500 via-30% to-aqua-500 to-90% text-white  z-50"
        id="sidebar">
        <div class="h-full w-full flex justify-center items-center flex-col relative">

            <button class="absolute top-6 right-6 border h-10 w-10 border-white rounded-lg flex justify-center items-center"
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <ul class="space-y-4 flex flex-col items-center">

                <div class="flex flex-col gap-6 items-center">
                    <div class="flex flex-col ">

                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="rounded-full overflow-hidden w-24 h-24">
                                <img src="{{ $user->profile_photo_path ?? asset('website/assets/images/dashboard/user.png') }}"
                                    alt="" class="w-full h-full object-cover">
                            </div>

                            <div class="flex justify-center items-center flex-col p-3 rounded-xl bg-white/50">
                                <p class="text-lg font-semibold text-center text-neutral-700">
                                    {{ $user->name ?? 'John Doe' }}
                                </p>
                                <p class="text-sm text-left text-neutral-500">{{ $user->email ?? 'john.doe@email.com' }}</p>

                                <div
                                    class="flex justify-center items-center  w-fit h-[30px] gap-1 p-2 rounded-[25px] bg-neutral-900 mt-[15px]">
                                    <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                        class="h-3 w-3">
                                    <p class="text-xs text-left text-[#eab800]">Premium Member</p>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                <li onclick="toggleSidebar()">
                    <a href="#" class="text-lg hover:text-blue-400 cursor-pointer">Home</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="text-lg hover:text-blue-400 cursor-pointer">Services</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="text-lg hover:text-blue-400 cursor-pointer">Nearest Store</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="text-lg hover:text-blue-400 cursor-pointer">FAQ</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="text-lg hover:text-blue-400 cursor-pointer">Contact</a>
                </li>
            </ul>
        </div>
    </div>


    <main>

        <!-- manage addresses area -->
        <section class="max-w-2lg mx-auto pt-[60px] pb-[80px] px-4 xl:px-0 space-y-5 md:space-y-10">

            <div class="w-full">
                <p class="text-2xl md:text-4xl font-semibold text-left text-gray-700">
                    My Dashboard
                </p>
                <p class="text-base md:text-xl text-left text-gray-500">
                    Manage your orders, addresses, and preferences
                </p>
            </div>


            <div class="w-full grid grid-cols-12 gap-6">
                <div class="hidden lg:block col-span-12 lg:col-span-4 ">
                    <div class="flex flex-col gap-6 items-center p-4 rounded-xl sticky top-24 bg-white">
                        <div class="flex flex-col ">

                            <div class="flex flex-col items-center justify-center gap-6">
                                <div class="rounded-full overflow-hidden w-32 h-32">
                                    <img src="{{ $user->profile_photo_path ?? asset('website/assets/images/dashboard/user.png') }}"
                                        alt="" class="w-full h-full object-cover">
                                </div>

                                <div class="flex justify-center items-center flex-col">
                                    <p class="text-lg font-semibold text-center text-neutral-700">
                                        {{ $user->name ?? 'John Doe' }}
                                    </p>
                                    <p class="text-base text-left text-neutral-500">
                                        {{ $user->email ?? 'john.doe@email.com' }}</p>

                                    <div
                                        class="flex justify-center items-center  w-fit h-[30px] gap-1 p-2 rounded-[25px] bg-neutral-900 mt-[15px]">
                                        <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                            class="h-3 w-3">
                                        <p class="text-xs text-left text-[#eab800]">Premium Member</p>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="space-y-[10px] w-full">

                            @foreach ($menuItems as $item)
                                @php
                                    $isActive = request()->routeIs($item['route']);
                                @endphp

                                <a href="{{ route($item['route']) }}"
                                    class="px-5 py-3.5 rounded-xl flex justify-start items-center gap-[15px] w-full group transition-all duration-150
                  {{ $isActive ? 'bg-mint-600' : 'bg-neutral-50 hover:bg-mint-600' }}">

                                    <img src="{{ asset('website/assets/icons/' . $item['icon']) }}"
                                        class="w-5 h-auto
                        {{ $isActive ? 'brightness-0 invert' : 'group-hover:brightness-0 group-hover:invert' }}">

                                    <p
                                        class="text-base font-medium transition-all duration-150
                      {{ $isActive ? 'text-white' : 'text-neutral-700 group-hover:text-white' }}">
                                        {{ $item['label'] }}
                                    </p>
                                </a>
                            @endforeach

                            {{-- Logout --}}
                            <form method="POST" action="{{ route('web.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="px-5 py-3.5 rounded-xl flex justify-start items-center gap-[15px] bg-neutral-50 w-full group transition-all duration-150 hover:bg-red-600">

                                    <img src="{{ asset('website/assets/icons/sign-out.svg') }}"
                                        class="w-5 h-auto group-hover:brightness-0 group-hover:invert">

                                    <p
                                        class="text-base font-medium text-neutral-700 group-hover:text-white transition-all duration-150">
                                        Sign Out
                                    </p>
                                </button>
                            </form>

                        </div>


                    </div>
                </div>


                <div class=" col-span-12 lg:col-span-8 flex flex-col gap-6">

                    <!-- widgets -->
                    <div class="flex justify-between items-center gap-6 flex-nowrap overflow-x-auto scrollbar-hide">
                        <!-- widget 1 -->
                        <div class="p-4 flex justify-between items-start gap-4 w-full min-w-52 rounded-xl  bg-white">
                            <div class="flex flex-col justify-between">
                                <p class="text-base text-gray-700">Total Orders</p>
                                <p class="text-[28px] font-bold text-gray-700">{{ $orders->count() }}
                                </p>

                            </div>
                            <div class="h-16 w-16 bg-mint-50 flex items-center justify-center rounded-full">
                                <img src="{{ asset('website/assets/icons/calendar.svg') }}" alt="" class="w-6 h-6">
                            </div>
                        </div>

                        <!-- widget 2 -->
                        <div class="p-4 flex justify-between items-start gap-4 w-full min-w-52 rounded-xl  bg-white">
                            <div class="flex flex-col justify-between">
                                <p class="text-base text-gray-700">In Progress</p>
                                <p class="text-[28px] font-bold text-gray-700">
                                    {{ $orders->where('order_status', '!=', 'Delivered')->where('order_status', '!=', 'Cancelled')->count() }}
                                </p>

                            </div>
                            <div class="h-16 w-16 bg-aqua-50 flex items-center justify-center rounded-full">
                                <img src="{{ asset('website/assets/icons/clock-blue.svg') }}" alt=""
                                    class="w-6 h-6">
                            </div>
                        </div>

                        <!-- widget 3 -->
                        <div class="p-4 flex justify-between items-start gap-4 w-full min-w-52 rounded-xl  bg-white">
                            <div class="flex flex-col justify-between">
                                <p class="text-base text-gray-700">Completed</p>
                                <p class="text-[28px] font-bold text-gray-700">
                                    {{ $orders->where('order_status', 'Delivered')->count() }}
                                </p>

                            </div>
                            <div class="h-16 w-16 bg-mint-50 flex items-center justify-center rounded-full">
                                <img src="{{ asset('website/assets/icons/check-green.svg') }}" alt=""
                                    class="w-6 h-6">
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders-->
                    <div class="rounded-3xl p-6 bg-white shadow-sm flex flex-col gap-[15px]">
                        <div class="flex justify-between items-center">
                            <p class="text-base font-semibold text-left text-neutral-700">
                                Recent Orders
                            </p>
                            <a href="{{ route('web.my.orders') }}">
                                <p class="text-sm font-medium text-left text-neutral-700">View All</p>
                            </a>

                        </div>

                        @forelse($orders as $order)
                            <!-- card -->
                            <div class="p-3 flex justify-between items-start rounded-xl gap-4 border border-neutral-200">
                                <div class="w-14 h-14 bg-mint-50  justify-center items-center rounded hidden sm:flex">
                                    <img src="{{ asset('website/assets/icons/calendar.svg') }}" alt=""
                                        class="w-6 h-6">
                                </div>
                                <div class="flex-1 h-full">
                                    <div class="flex justify-between items-center">
                                        <div
                                            class="w-12 h-12 bg-mint-50  justify-center items-center rounded flex  sm:hidden">
                                            <img src="{{ asset('website/assets/icons/calendar.svg') }}" alt=""
                                                class="w-6 h-6">
                                        </div>


                                        <div class="block sm:hidden">
                                            <div
                                                class="flex justify-center items-center px-2.5 py-[5px] rounded-[20px] bg-[#0099df]">
                                                <p class="text-[8px] sm:text-[10px] text-left text-white">
                                                    {{ $order->order_status ?? 'In Progress' }}</p>
                                            </div>
                                            <p class="text-[10px] sm:text-xs font-medium text-right text-neutral-500">
                                                {{ $order->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs sm:text-sm font-semibold text-right text-gray-900">
                                                {{ currencyPosition($order->total_amount ?? 0, 2) }}</p>
                                        </div>


                                    </div>
                                    <p class="text-sm font-medium  text-neutral-900">
                                        #{{ $order->prefix }}-{{ $order->order_code ?? $order->id }}
                                    </p>
                                    <p class="text-sm  text-neutral-500">
                                        @if ($order->address)
                                            {{ $order->address->address_name ?? ($order->address->address_line ?? 'Order') }}
                                        @else
                                            Order
                                        @endif
                                    </p>
                                    <p class="text-xs font-medium  text-neutral-700">
                                        {{ currencyPosition($order->total_amount ?? 0, 2) }} <span
                                            class="text-xs text-left text-neutral-500">{{ $order->products->count() ?? 0 }}
                                            Items</span> </p>
                                </div>
                                <div class="hidden sm:block">
                                    <div
                                        class="flex justify-center items-center px-2.5 py-[5px] rounded-[20px]
                                        @if ($order->order_status == 'Delivered') bg-[#34c759]
                                        @elseif($order->order_status == 'Cancelled') bg-red-500
                                        @elseif($order->order_status == 'On Going') bg-[#fbbc04]
                                        @else bg-[#0099df] @endif">
                                        <p class="text-[10px] text-left text-white">
                                            {{ $order->order_status ?? 'In Progress' }}
                                        </p>
                                    </div>
                                    <p class="text-xs font-medium text-right text-neutral-500">
                                        {{ $order->created_at->format('M d, Y') }}</p>
                                    <p class="text-sm font-semibold text-right text-gray-900">
                                        {{ currencyPosition($order->total_amount ?? 0, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-neutral-500">
                                <p>No orders yet. <a href="{{ route('web.service') }}"
                                        class="text-mint-600 font-medium">Book your first order</a></p>
                            </div>
                        @endforelse
                    </div>


                    <!-- saved address -->
                    <div class="rounded-3xl p-6 bg-white shadow-sm flex flex-col gap-[15px]">
                        <div class="flex justify-between items-center">
                            <p class="text-base font-semibold text-left text-neutral-700">
                                Saved Addresses
                            </p>
                            <a href="{{ route('web.addresses') }}">
                                <p class="text-sm font-medium text-left text-neutral-700">View All</p>
                            </a>

                        </div>

                        @forelse($addresses as $address)
                            <div class="p-3 sm:p-6 flex justify-start items-start gap-5 rounded-xl border border-gray-200">
                                <div class="w-14 h-14 bg-mint-50 justify-center items-center rounded hidden sm:flex">
                                    <img src="{{ asset('website/assets/icons/home.svg') }}" alt=""
                                        class="w-6 h-6">
                                </div>
                                <div class="flex flex-col gap-1 items-start">

                                    <div class="flex justify-between items-start sm:hidden w-full">
                                        <div class="w-12 h-12 bg-mint-50 justify-center items-center rounded flex ">
                                            <img src="{{ asset('website/assets/icons/home.svg') }}" alt=""
                                                class="w-6 h-6">
                                        </div>

                                        @if ($address->is_default)
                                            <span
                                                class="inline-flex justify-start items-center gap-2 bg-mint-50 px-2 py-1 rounded-full">
                                                <img src="{{ asset('website/assets/icons/star.svg') }}" alt="">
                                                <p class="text-xs text-left text-[#32d3a0]">Default</p>

                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex justify-start items-center gap-2 mb-3">
                                        <p class="text-base font-medium text-left text-gray-900">
                                            {{ $address->address_name ?? 'Home' }}</p>

                                        @if ($address->is_default)
                                            <span
                                                class="hidden sm:inline-flex justify-start items-center gap-2 bg-mint-50 px-2 py-1 rounded-full">
                                                <img src="{{ asset('website/assets/icons/star.svg') }}" alt="">
                                                <p class="text-xs text-left text-[#32d3a0]">Default</p>

                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-left text-gray-700 flex justify-start items-center gap-1">
                                        <img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" alt=""
                                            class="h-[14px] w-[14px]">
                                        @php
                                            $addressParts = [];

                                            $fields = [
                                                'address_line' => null,
                                                'road_no' => 'Road: ',
                                                'house_no' => 'House: ',
                                                'flat_no' => 'Flat: ',
                                                'block' => 'Block: ',
                                                'area' => null,
                                                'post_code' => null,
                                            ];

                                            foreach ($fields as $field => $label) {
                                                if (!empty($address->$field)) {
                                                    $addressParts[] = $label
                                                        ? $label . $address->$field
                                                        : $address->$field;
                                                }
                                            }
                                        @endphp
                                        {{ implode(', ', $addressParts) }}
                                    </p>
                                    <p class="text-sm text-left text-gray-500  flex justify-start items-center gap-1">
                                        <img src="{{ asset('website/assets/icons/phone-gray.svg') }}" alt=""
                                            class="h-[14px] w-[14px]">
                                        {{ $address->phone ?? ($user->mobile ?? '') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-neutral-500">
                                <p>No addresses saved. <a href="{{ route('web.addresses') }}"
                                        class="text-mint-600 font-medium">Add an address</a></p>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>




        </section>


        <!-- get started -->
        <section class="bg-mint-600 ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl:1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        Ready to Get Started?
                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        Book your first order and experience hassle-free laundry service
                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <a href="{{ route('web.service') }}">
                            <p>Book Now</p>
                        </a>

                        <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p>Become a Partner</p>
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection
