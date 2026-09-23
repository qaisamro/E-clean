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
                                    <img src="{{ asset('website/assets/images/dashboard/user.png') }}" alt=""
                                        class="w-full h-full object-cover">
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
                            <form method="POST" action="{{ route('logout') }}">
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

                    <form action="{{ route('web.settings.update') }}" method="POST"
                        class="rounded-3xl p-6 bg-white shadow-sm">
                        @csrf
                        <div class="flex justify-between items-center">
                            <h3 class="text-neutral-700 text-lg font-semibold"> Account Setting</h3>
                        </div>


                        <div class="space-y-4 mt-6 mb-[30px]">
                            <div>
                                <label for="name"
                                    class="text-sm md:text-base font-medium text-left text-neutral-700">Full
                                    Name</label>
                                <input type="text" value="{{ old('first_name', $user->first_name ?? '') }}"
                                    id="name" name="name" placeholder="John Doe"
                                    class="w-full h-[40px] mt-2.5 border border-neutral-100 p-3 rounded-xl text-sm focus:outline-mint-100 ">
                            </div>
                            <div>
                                <label for="email"
                                    class="text-sm md:text-base font-medium text-left text-neutral-700">Email
                                    Address</label>
                                <input type="text" value="{{ old('email', $user->email ?? '') }}" id="email"
                                    name="email" placeholder="john.doe@email.com"
                                    class="w-full h-[40px] mt-2.5 border border-neutral-100 p-3 rounded-xl text-sm focus:outline-mint-100 ">
                            </div>
                            <div>
                                <label for="phone"
                                    class="text-sm md:text-base font-medium text-left text-neutral-700">Phone
                                    Number</label>
                                <input type="phone" value="{{ old('phone', $user->mobile ?? '') }}" id="phone"
                                    name="phone" placeholder="Your phone number"
                                    class="w-full h-[40px] mt-2.5 border border-neutral-100 p-3 rounded-xl text-sm focus:outline-mint-100 ">
                            </div>
                        </div>


                        <button type="submit"
                            class="flex justify-center items-center w-full h-12 overflow-hidden px-5 py-3.5 rounded-xl bg-mint-600">
                            <p class="text-sm font-semibold text-center text-white">
                                Save Changes
                            </p>
                        </button>
                    </form>



                    <!-- notification -->
                    <div class="rounded-3xl p-6 bg-white shadow-sm">
                        <div class="flex justify-between items-center">
                            <h3 class="text-neutral-700 text-lg font-semibold">Notifications</h3>
                        </div>

                        <div class="mt-6">
                            <div class="flex justify-start items-center py-4 border-b border-neutral-200">
                                <label for="notification" class="flex-1">
                                    <p class="text-sm font-semibold text-left text-neutral-700">Order Updates</p>
                                    <p class="text-sm text-left text-neutral-500">Get notified about your order status
                                    </p>
                                </label>
                                <label for="notification">
                                    <input type="checkbox" name="notification" id="notification" class="peer sr-only">
                                    <img src="{{ asset('website/assets/icons/unchecked-green.svg') }}" alt=""
                                        class="w-6 h-6 peer-checked:hidden">
                                    <img src="{{ asset('website/assets/icons/checked.svg') }}" alt=""
                                        class="w-6 h-6 hidden peer-checked:block">
                                </label>
                            </div>
                            <div class="flex justify-start items-center py-4">
                                <label for="promotion" class="flex-1">
                                    <p class="text-sm font-semibold text-left text-neutral-700">Promotions</p>
                                    <p class="text-sm text-left text-neutral-500">Receive special offers and discounts
                                    </p>
                                </label>
                                <label for="promotion">
                                    <input type="checkbox" name="promotion" id="promotion" class="peer sr-only">
                                    <img src="{{ asset('website/assets/icons/unchecked-green.svg') }}" alt=""
                                        class="w-6 h-6 peer-checked:hidden">
                                    <img src="{{ asset('website/assets/icons/checked.svg') }}" alt=""
                                        class="w-6 h-6 hidden peer-checked:block">
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

            </div>




        </section>


        <!-- get started -->
        <section class="bg-mint-600 ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        Ready to Get Started?
                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        Book your first order and experience hassle-free dry cleaning service
                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <a href="{{ route('web.service') }}">
                            <p>Book Now</p>
                        </a>

                        <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection
