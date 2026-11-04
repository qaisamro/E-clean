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
                    My Dashboard
                </p>
                <p class="text-base md:text-xl text-left text-gray-500">
                    Manage your orders, addresses, and preferences
                </p>
            </div>


            <div class="w-full grid grid-cols-12 gap-6">
                <div class="hidden lg:block col-span-12 lg:col-span-4 ">
                    <div class="flex flex-col gap-6 items-center p-4 rounded-[14px] sticky top-24 bg-white">
                        <div class="flex flex-col ">

                            <div class="flex flex-col items-center justify-center gap-6">
                                <div class="rounded-full overflow-hidden w-32 h-32">
                                    <img src="{{ $user->profile_photo_path ?? asset('website/assets/images/dashboard/user.png?v=2') }}"
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
                                        {{ $item['label'] }}
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
                                        Sign Out
                                    </p>
                                </button>
                            </form>

                        </div>


                    </div>
                </div>
                <div class=" col-span-12 lg:col-span-8 flex flex-col gap-6">

                    <div class="p-6 rounded-[20px] flex flex-col justify-start w-full bg-white">
                        <p class="text-base font-semibold text-left text-gray-700">
                            Favorite Stores
                        </p>

                        <div>

                            @forelse($favoriteStores as $favorite)
                                <!-- card -->
                                <div class="py-[15px] flex justify-between items-center gap-4 border-b border-[#E2E8F0]">
                                    <div class="icon_labels">
                                        <img src="{{ asset('website/assets/icons/heart-green-solid.svg') }}"
                                            alt="">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-left text-neutral-700">
                                            {{ $favorite['store']->name ?? 'Store Name' }}
                                        </p>
                                        <div class="flex justify-start my-1 gap-2">
                                            <p class="text-sm text-left text-neutral-500">{{ $favorite['order_count'] }}
                                                Orders</p>
                                            <p
                                                class="text-sm text-left text-neutral-500 flex justify-start items-center gap-1">
                                                <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                                    class="inline">
                                                {{ number_format($favorite['rating'], 1) }}
                                                rating
                                            </p>
                                        </div>


                                        <button type="submit" onclick="window.location.href='{{ route('web.service') }}'"
                                            class="block md:hidden w-full h-10 relative rounded-[14px] text-xs font-medium text-center text-neutral-500 border-[1.5px] border-[#E2E8F0]  px-4 py-2 capitalize transition-all duration-150 hover:bg-[linear-gradient(135deg,#028090,#00A896)] hover:text-white">book
                                            Again</button>
                                    </div>
                                    <button type="submit" onclick="window.location.href='{{ route('web.service') }}'"
                                        class="hidden md:block w-40 h-10 relative rounded-[14px] text-xs font-medium text-center text-neutral-500 border-[1.5px] border-[#E2E8F0]  px-4 py-2 capitalize transition-all duration-150 hover:bg-[linear-gradient(135deg,#028090,#00A896)] hover:text-white">book
                                        Again</button>
                                </div>
                            @empty
                                <div class="py-6 text-center text-neutral-500">
                                    <p>{{ __('No favorite stores yet.') }} <a href="{{ route('web.service') }}"
                                            class="text-[#028090] font-medium">{{ __('Book your first order') }}</a></p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>




        </section>


        <!-- get started -->
        <section style="background:linear-gradient(135deg,#083744,#156172);" class="bg-[linear-gradient(135deg,#083744,#156172)] ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl:1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        Ready to Get Started?
                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        {{ __('Book your first order') }} and experience hassle-free dry cleaning service
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
