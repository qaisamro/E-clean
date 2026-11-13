@extends('website.layouts.app')

@section('content')
    <!-- manage-address-modal -->
    <section id="role" class="modal_container">

        <!-- backdrop -->
        <div onclick="toggleModal('role')" class="modal_backdrop"></div>

        <!-- modal content -->
        <div class="modal_content">
            {{-- new test  --}}
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
                    <input type="text" placeholder="{{ __('Address label example') }}"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">{{ __('Street Address') }}</label>
                    <input type="text" placeholder="{{ __('Address example') }}"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">{{ __('City') }}</label>
                        <input type="text" placeholder="{{ __('City example') }}"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">{{ __('State') }}</label>
                        <input type="text" placeholder="NY"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">{{ __('ZIP Code') }}</label>
                    <input type="text" placeholder="10003"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-[30px]">
                    <label class="text-neutral-700 text-base font-medium">{{ __('Phone Number') }}</label>
                    <input type="text" placeholder="+1 (555) 545-5421"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <button
                    class="rs-add-new-address-btn text-sm bg-[linear-gradient(135deg,#028090,#00A896)] text-white h-[48px] text-center leading-[48px] w-[100%] rounded-[14px]">
                    {{ __('Save Address') }}
                </button>
            </form>
        </div>
    </section>

    <!-- navbar -->


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

    <!-- Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-[.5] z-40 hidden" onclick="closeFilterSidebar()">
    </div>
    <!-- Filter Sidebar -->
    <div id="sidebar-filter"
        class="fixed top-0 right-0 w-full max-w-[320px] h-full bg-white text-neutral-900 z-50 transform translate-x-full transition-transform duration-300 shadow-lg">
        <div class="h-full w-full flex flex-col relative p-4">
            <button
                class="absolute top-4 right-4 border h-10 w-10 border-[#E2E8F0] rounded-[12px] flex justify-center items-center"
                onclick="closeFilterSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="overflow-y-auto mt-12">
                <h3 class="text-sm md:text-base font-semibold mb-4">{{ __('Filter by') }}</h3>
                <form class="mb-4" id="sidebar-filter-form" action="{{ route('web.services') }}" method="GET">
                    <label class="text-xs font-medium text-neutral-700">{{ __('Search') }}</label>
                    <div class="relative mt-1">
                        <input type="text" id="service-search" name="search"
                            class="border border-[#E2E8F0] rounded-[14px] h-10 w-full pl-10 pr-10 text-sm placeholder:text-neutral-400"
                            placeholder="{{ __('Search services ...') }}" value="{{ $search ?? '' }}">
                        <button type="submit"
                            class="absolute right-3 top-1/2 -translate-y-1/2 bg-none border-none p-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-neutral-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <main style="background-color:#FAF7F2;">
        <!-- breadcrumb -->
        <section
            style="background:linear-gradient(135deg,#083744,#156172),url('{{ asset('website/assets/images/header/breadcrumb.png') }}');background-size:cover;background-position:center;" class="rs-breadcrumb-area bg-[linear-gradient(135deg,#083744,#156172)] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    {{ __('All Services') }}
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="{{ route('web.home') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Home / ') }}</a>
                    <a href="{{ route('web.services') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Services') }}</a>
                </div>
            </div>
        </section>

        <!-- {{ __('Services') }} area -->
        <section class="rs-services-section pt-[60px] pb-[100px] px-4 xl:px-0 bg-[#FAF7F2]">
            <div class="rs-services-area max-w-2lg  mx-auto grid grid-cols-1 md:grid-cols-12 gap-[24px]">

                <!-- Left Column -->
                <div class="rs-services-left-area hidden lg:block col-span-12 md:col-span-4 lg:col-span-3">
                    <div class="rs-services-filter-area bg-white py-6 px-4 rounded-[14px]">
                        <h3
                            class="rs-services-filter-title text-sm md:text-base text-neutral-900 font-semibold leading-[140%] mb-4">
                            {{ __('Filter by') }}
                        </h3>
                        <form action="{{ route('web.services') }}" method="GET" class="mb-4"
                            id="desktop-filter-form">
                            <label class="text-xs font-medium leading-[120%] text-neutral-700">{{ __('Search') }}</label>
                            <div class="relative flex items-center">
                                <img class="absolute rs-filter-search-icon"
                                    src="{{ asset('website/assets/icons/search-icon.svg') }}" alt="">
                                <input
                                    class="promo-input filter-input border-[1.50px] mt-[5px] border-[#E2E8F0] leading-[41px] rounded-[14px] h-[40px] w-full px-[16px] pl-[42px]"
                                    type="text" name="search" placeholder="{{ __('Search services ...') }}"
                                    value="{{ $search ?? '' }}">
                                <button type="submit" class="absolute right-3 bg-none border-none p-0 cursor-pointer">
                                    {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-neutral-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                                    </svg> --}}
                                </button>
                                @if ($search)
                                    <button type="button" onclick="clearDesktop{{ __('Search') }}()"
                                        class="absolute right-10 text-neutral-400 hover:text-neutral-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="rs-services-right-area col-span-12 md:col-span-12 lg:col-span-9">
                    <div class="rs-services-right-top flex items-center justify-between mb-6 flex-wrap gap-4">
                        <p class="text-lg font-normal leading-[100%] text-neutral-500">{{ __('Showing :count services', ['count' => $services->count()]) }}</p>
                        <!-- filter sidebar opening button  -->
                        <button
                            class="h-10 w-10 border border-primary2-600 rounded p-1 flex justify-center items-center ml-auto block lg:hidden"
                            onclick="toggleFilterSidebar()">
                            <i class="fa-solid fa-filter"></i>
                        </button>

                        @php
                            $sortLabels = [
                                'recommended' => __('Recommended'),
                                'low_to_high' => __('Price: Low to High'),
                                'high_to_low' => __('Price: High to Low'),
                            ];
                            $sortKey = $sort ?? 'recommended';
                        @endphp

                        <!-- Sort Dropdown -->
                        <div class="select-dropdown leading-[41px] text-start relative cursor-pointer border border-[#E2E8F0] rounded-[12px] px-3 py-1 bg-white"
                            onclick="toggleSortDropdown()">
                            <span class="text-sm text-neutral-600">{{ __('Sort by:') }}</span> <b class="text-sm text-neutral-800"
                                id="current-sort">{{ $sortLabels[$sortKey] ?? __('Recommended') }}</b>
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                            <ul id="sort-dropdown"
                                class="select-dropdown-option transition-all duration-300 absolute w-full bg-white left-0 top-full z-20 p-2 opacity-0 invisible shadow-lg rounded-[12px] border border-[#E2E8F0] mt-1">
                                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm rounded"
                                    onclick="applySort('recommended')">{{ __('Recommended') }}</li>
                                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm rounded"
                                    onclick="applySort('low_to_high')">{{ __('Price: Low to High') }}</li>
                                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm rounded"
                                    onclick="applySort('high_to_low')">{{ __('Price: High to Low') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="rs-services-wrapper grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 "
                        id="services-wrapper">

                        @forelse($services as $service)
                            <div
                                class="rs-services-box rounded-[20px] overflow-hidden bg-white hover:border hover:border-mint-400">
                                <div class="rs-services-thumb relative">
                                    <img class="object-contain w-full"
                                        src="{{ $service->thumbnail_path ?? asset('website/assets/images/service/default.jpg') }}"
                                        alt="">

                                    @if ($service->is_popular)
                                        <div class="thumb-popular">{{ __('Popular') }}</div>
                                    @endif
                                </div>

                                <div class="rs-services-content p-4">
                                    <div class="rs-services-rat flex gap-[3px] mb-[10px]">
                                        <img src="{{ asset('website/assets/icons/star-small.svg') }}" alt="">
                                        <span class="text-sm text-neutral-700 font-medium">
                                            {{ number_format($service->rating ?? 0, 1) }}
                                        </span>
                                        <span class="text-sm text-neutral-700">
                                            ({{ $service->reviews_count ?? 0 }})
                                        </span>
                                    </div>

                                    <h3 class="text-sm text-neutral-900 font-semibold mb-[15px]">
                                        {{ $service->name_bn ?? $service->name }}
                                    </h3>

                                    <p class="text-[13px] text-neutral-500 mb-[15px]">
                                        {{ Str::limit($service->description, 80) }}
                                    </p>

                                    <p class="text-xs text-neutral-500 mb-[15px]">
                                        {{ __('By :provider', ['provider' => $service->provider_name ?? __('Service Provider')]) }}
                                    </p>

                                    <div class="rs-services-bottom-content flex items-center">
                                        <h5 class="text-sm text-[#028090] font-semibold">
                                            @if ($service->lowestProduct)
                                                  {{ currencyPosition($service->lowestProduct->final_price) }}{{ __(' / Item') }}
                                            @else
                                                ${{ __(' / Item') }}
                                            @endif
                                        </h5>


                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full text-center text-neutral-500">
                                {{ __('No services available right now.') }}
                            </p>
                        @endforelse

                    </div>

                </div>
            </div>
            {{-- Pagination --}}
            @if ($services instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-10">
                    {{ $services->links() }}
                </div>
            @endif

        </section>


        <!-- get started -->
        <section style="background:linear-gradient(135deg,#083744,#156172);" class="bg-[linear-gradient(135deg,#083744,#156172)] bottom-0">
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
                        <p>{{ __('Book Now') }}</p>

                        <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p>{{ __('Become a Partner') }}</p>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Toggle sort dropdown visibility
        function toggleSortDropdown() {
            const dropdown = document.getElementById('sort-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('opacity-0');
                dropdown.classList.toggle('invisible');
            }
        }

        // Apply sorting and redirect to sorted results
        function applySort(sortType) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortType);
            window.location.href = url.toString();
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const sortDropdown = document.getElementById('sort-dropdown');
            const sortTrigger = document.querySelector('.select-dropdown');

            if (sortDropdown && sortTrigger && !sortTrigger.contains(e.target)) {
                sortDropdown.classList.add('opacity-0');
                sortDropdown.classList.add('invisible');
            }
        });

        // Clear search for mobile sidebar
        function clearSearch(inputId) {
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        }

        // Clear search for desktop
        function clearDesktopSearch() {
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Search forms will submit naturally via GET method
        });
    </script>
@endsection
