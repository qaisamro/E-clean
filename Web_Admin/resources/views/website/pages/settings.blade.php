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
        <div class="max-w-2lg mx-auto mt-4 px-4 py-3 rounded-[14px] bg-[#E6F4F1] text-green-800">
            {{ session('success') }}
        </div>
    @endif
    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 w-full h-full bg-gradient-to-tl from-[#083744] via-[#156172] to-[#028090] text-white  z-50"
        id="sidebar">
        <div class="h-full w-full flex justify-center items-center flex-col relative">

            <button class="absolute top-6 right-6 border h-10 w-10 border-white rounded-[12px] flex justify-center items-center"
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
                                <img src="{{ $user->profile_photo_path ?? asset('website/assets/images/dashboard/user.png?v=2') }}"
                                    alt="" class="w-full h-full object-cover">
                            </div>

                            <div class="flex justify-center items-center flex-col p-3 rounded-[14px] bg-white/50">
                                <p class="text-lg font-semibold text-center text-neutral-700">
                                    {{ $user->name ?? 'John Doe' }}
                                </p>
                                <p class="text-sm text-left text-neutral-500">{{ $user->email ?? 'john.doe@email.com' }}</p>

                                <div
                                    class="flex justify-center items-center  w-fit h-[30px] gap-1 p-2 rounded-[25px] bg-neutral-900 mt-[15px]">
                                    <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                        class="h-3 w-3">
                                    <p class="text-xs text-left text-[#eab800]">{{ __('Premium Member') }}</p>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                <li onclick="toggleSidebar()">
                    <a href="#" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Home') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Services') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Nearest Store') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('FAQ') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Contact') }}</a>
                </li>
            </ul>
        </div>
    </div>


    <main style="background-color:#FAF7F2;">

        <!-- manage addresses area -->
        <section class="max-w-2lg mx-auto pt-[60px] pb-[80px] px-4 xl:px-0 space-y-5 md:space-y-10">

            <div class="w-full">
                    <p class="text-2xl md:text-4xl font-semibold text-left text-gray-700">
                    {{ __('My Dashboard') }}
                </p>
                                    <p class="text-base md:text-xl text-left text-gray-500">
                    {{ __('Manage your orders, addresses, and preferences') }}
                </p>
            </div>



            <div class="w-full grid grid-cols-12 gap-6">
                <div class="hidden lg:block col-span-12 lg:col-span-4 ">
                    <div class="flex flex-col gap-6 items-center p-4 rounded-[14px] sticky top-24 bg-white">
                        <div class="flex flex-col ">

                            <div class="flex flex-col items-center justify-center gap-6">
                                <div class="rounded-full overflow-hidden w-32 h-32">
                                    <img src="{{ asset('website/assets/images/dashboard/user.png?v=2') }}" alt=""
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
                                        <p class="text-xs text-left text-[#eab800]">{{ __('Premium Member') }}</p>

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
                                    class="px-5 py-3.5 rounded-[14px] flex justify-start items-center gap-[15px] w-full group transition-all duration-150
                  {{ $isActive ? 'bg-[linear-gradient(135deg,#028090,#00A896)]' : 'bg-[#FAF7F2] hover:bg-[linear-gradient(135deg,#028090,#00A896)]' }}">

                                    <img src="{{ asset('website/assets/icons/' . $item['icon']) }}"
                                        class="w-5 h-auto
                        {{ $isActive ? 'brightness-0 invert' : 'group-hover:brightness-0 group-hover:invert' }}">

                                    <p
                                        class="text-base font-medium transition-all duration-150
                      {{ $isActive ? 'text-white' : 'text-neutral-700 group-hover:text-white' }}">
                                        {{ __($item['label']) }}
                                    </p>
                                </a>
                            @endforeach

                            {{-- Logout --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="px-5 py-3.5 rounded-[14px] flex justify-start items-center gap-[15px] bg-[#FAF7F2] w-full group transition-all duration-150 hover:bg-red-600">

                                    <img src="{{ asset('website/assets/icons/sign-out.svg') }}"
                                        class="w-5 h-auto group-hover:brightness-0 group-hover:invert">

                                    <p
                                        class="text-base font-medium text-neutral-700 group-hover:text-white transition-all duration-150">
                                        {{ __('Sign Out') }}
                                    </p>
                                </button>
                            </form>

                        </div>


                    </div>
                </div>
                <div class=" col-span-12 lg:col-span-8 flex flex-col gap-6">

                    <form action="{{ route('web.settings.update') }}" method="POST"
                        class="rounded-[20px] p-6 bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)]">
                        @csrf
                        <div class="flex justify-between items-center">
                            <h3 class="text-neutral-700 text-lg font-semibold">{{ __('Account Settings') }}</h3>
                        </div>


                        <div class="space-y-4 mt-6 mb-[30px]">
                            <div>
                                <label for="name"
                                    class="text-sm md:text-base font-medium text-left text-neutral-700">{{ __('Full Name') }}</label>
                                <input type="text" value="{{ old('first_name', $user->first_name ?? '') }}"
                                    id="name" name="name" placeholder="{{ __('Full Name') }}"
                                    class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] p-3 rounded-[14px] text-sm focus:outline-[#028090] ">
                            </div>
                            <div>
                                <label for="email"
                                    class="text-sm md:text-base font-medium text-left text-neutral-700">{{ __('Email_Address') }}</label>
                                <input type="text" value="{{ old('email', $user->email ?? '') }}" id="email"
                                    name="email" placeholder="{{ __('Enter_Email_Address') }}"
                                    class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] p-3 rounded-[14px] text-sm focus:outline-[#028090] ">
                            </div>
                            <div>
                                <label for="phone"
                                    class="text-sm md:text-base font-medium text-left text-neutral-700">{{ __('Phone_Number') }}</label>
                                <input type="phone" value="{{ old('phone', $user->mobile ?? '') }}" id="phone"
                                    name="phone" placeholder="{{ __('Your phone number') }}"
                                    class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] p-3 rounded-[14px] text-sm focus:outline-[#028090] ">
                            </div>
                        </div>


                        <button type="submit"
                            class="flex justify-center items-center w-full h-12 overflow-hidden px-5 py-3.5 rounded-[14px] bg-[linear-gradient(135deg,#028090,#00A896)]">
                            <p class="text-sm font-semibold text-center text-white">
                                {{ __('Save_Changes') }}
                            </p>
                        </button>
                    </form>



                    <!-- notification -->
                    <div class="rounded-[20px] p-6 bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)]">
                        <div class="flex justify-between items-center">
                            <h3 class="text-neutral-700 text-lg font-semibold">{{ __('Notifications') }}</h3>
                        </div>

                        <div class="mt-6">
                            <div class="flex justify-start items-center py-4 border-b border-[#E2E8F0]">
                                <label for="notification" class="flex-1">
                                    <p class="text-sm font-semibold text-left text-neutral-700">{{ __('Order Updates') }}</p>
                                    <p class="text-sm text-left text-neutral-500">{{ __('Get notified about your order status') }}
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
                                    <p class="text-sm font-semibold text-left text-neutral-700">{{ __('Promotions') }}</p>
                                    <p class="text-sm text-left text-neutral-500">{{ __('Receive special offers and discounts') }}
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
        <section style="background:linear-gradient(135deg,#083744,#156172);" class="bg-[linear-gradient(135deg,#083744,#156172)] ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        {{ __('Ready to Get Started?') }}
                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        {{ __('Book your first order and experience hassle-free dry cleaning service') }}
                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <a href="{{ route('web.service') }}">
                            <p>{{ __('Book Now') }}</p>
                        </a>

                        <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection
