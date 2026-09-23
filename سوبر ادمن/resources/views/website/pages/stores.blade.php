@extends('website.layouts.app')

@section('content')
    <!-- manage-address-modal -->
    <section id="role" class="modal_container">

        <!-- backdrop -->
        <div onclick="toggleModal('role')" class="modal_backdrop"></div>

        <!-- modal content -->
        <div class="modal_content">
            <form action="#" class="rs-manage-addresses-form">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold">Add New Address</h3>
                    <button type="button" onclick="toggleModal('role')"
                        class="transition-transform duration-300 hover:rotate-90">
                        <img src="{{asset('website/assets/icons/close.svg')}}" alt="">
                    </button>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">Label</label>
                    <input type="text" placeholder="Example : Home, Office ..."
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">Street Address</label>
                    <input type="text" placeholder="123 Lovely Road, Apt 6B"
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">City</label>
                        <input type="text" placeholder="New York"
                            class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">State</label>
                        <input type="text" placeholder="NY"
                            class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">ZIP Code</label>
                    <input type="text" placeholder="10003"
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-[30px]">
                    <label class="text-neutral-700 text-base font-medium">Phone Number</label>
                    <input type="text" placeholder="+1 (555) 545-5421"
                        class="w-full h-[40px] mt-2.5 border border-gray-300 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <button
                    class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white h-[48px] text-center leading-[48px] w-[100%] rounded-xl">
                    Save Address
                </button>
            </form>
        </div>
    </section>

    <!-- navbar -->
    {{-- <nav class="bg-white sticky top-0 py-4 md:py-6 z-40 shadow-md">
        <section class="max-w-2lg mx-auto px-4 xl:px-0 flex items-center justify-between">
            <a href="#" class="w-auto h-6 md:w-auto md:h-8 inline-block">
                <img src="{{asset('website/assets/logo/logo-green.png')}}" alt="" class="h-full w-full">

            </a>


            <div class=" items-center justify-center gap-6 hidden lg:flex">
                <a class="menu_link menu_link_active" href="#">Home</a>
                <a class="menu_link" href="#Services">Services</a>
                <a class="menu_link" href="#Services">Nearest Store</a>
                <a class="menu_link" href="#FAQ">FAQ</a>
                <a class="menu_link" href="#Contact">Contact</a>
            </div>



            <div class="hidden lg:flex justify-center items-center gap-4">
                <button class="btn_solid">
                    <p>Sign In</p>

                    <img src="{{asset('website/assets/icons/arrow-left.svg')}}" alt="">
                </button>

                <button class="btn_outline">
                    <p>Sign Up</p>
                </button>
            </div>
            <!-- sidebar opening button  -->
            <button
                class="h-10 w-10 border border-primary2-600 rounded p-1 flex lg:hidden flex-col justify-around items-center  "
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-7 text-primary2-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        class="text-primary2-600" />
                </svg>

            </button>
        </section>
    </nav> --}}


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
        <!-- breadcrumb -->
        <section
            class="rs-breadcrumb-area bg-[#1A7058] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    Find Stores Near You!
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">Home / </a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">Stores</a>
                </div>
            </div>
        </section>

        <!-- Services area -->
        <section class="rs-services-section pt-[60px] pb-[100px] px-4 xl:px-0 bg-neutral-50">
            <div class="rs-services-area max-w-2lg  mx-auto grid grid-cols-1 md:grid-cols-12 gap-[24px]">

                <div class="rs-find-store-area col-span-12">
                    <div class="rs-services-right-top flex items-center justify-between mb-6 flex-wrap gap-4">
                        <p class="text-lg font-normal leading-[100%] text-neutral-500">Found 5 Stores</p>
                    </div>
                    <div class="rs-services-wrapper grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="{{asset('website/assets/images/stores/store-01.jpg')}}"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    20% OFF
                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="{{asset('website/assets/icons/love-icon.svg')}}"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                                        <img class="object-contain w-full h-full"
                                            src="{{asset('website/assets/images/stores/store-1.png')}}" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            CleanPro Express
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                            <span class="text-sm text-left text-neutral-500">(624)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    Premium quality dry cleaning services with same-day delivery
                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Luxury
                                        Service </span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Leather
                                        Care</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/location.svg')}}"
                                            alt="">2.1
                                        km</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/clock-gray.svg')}}"
                                            alt="">24
                                        hours</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/truck.svg')}}" alt="">Free
                                        Delivery</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/dolar.svg')}}" alt="">Min
                                        $15</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                        Now <img class="filter brightness-0 invert"
                                            src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="{{asset('website/assets/images/stores/store-02.jpg')}}"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    30% OFF
                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="{{asset('website/assets/icons/love-icon.svg')}}"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                                        <img class="object-contain w-full h-full"
                                            src="{{asset('website/assets/images/stores/store-1.png')}}" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            FreshFold Dry Cleaning
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.8</span>
                                            <span class="text-sm text-left text-neutral-500">(512)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    Professional dry cleaning with eco-friendly washing solutions.
                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Eco
                                        Wash </span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Express
                                        Clean</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/location.svg')}}"
                                            alt="">1.8
                                        km</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/clock-gray.svg')}}"
                                            alt="">12
                                        hours</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/truck.svg')}}" alt="">Free
                                        Delivery</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/dolar.svg')}}" alt="">Min
                                        $12</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                        Now <img class="filter brightness-0 invert"
                                            src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="{{asset('website/assets/images/stores/store-03.jpg')}}"
                                    alt="">
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="{{asset('website/assets/icons/love-icon.svg')}}"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                                        <img class="object-contain w-full h-full"
                                            src="{{asset('website/assets/images/stores/store-1.png')}}" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            QuickWash Pro
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                            <span class="text-sm text-left text-neutral-500">(62)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    Fast and reliable dry cleaning service with guaranteed next-day delivery.
                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Speed
                                        Service</span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Ironing
                                        Included</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/location.svg')}}"
                                            alt="">3.1
                                        km</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/clock-gray.svg')}}"
                                            alt="">24
                                        hours</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/truck.svg')}}" alt="">Free
                                        Delivery</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/dolar.svg')}}" alt="">Min
                                        $10</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                        Now <img class="filter brightness-0 invert"
                                            src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="{{asset('website/assets/images/stores/store-04.jpg')}}"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    15% OFF
                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="{{asset('website/assets/icons/love-icon.svg')}}"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                                        <img class="object-contain w-full h-full"
                                            src="{{asset('website/assets/images/stores/store-1.png')}}" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            PureCare Dry Cleaning Hub
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.8</span>
                                            <span class="text-sm text-left text-neutral-500">(842)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    Premium washing and folding for all types of fabrics — handled with care.
                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">
                                        Fabric Safe </span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">
                                        Steam Press</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/location.svg')}}"
                                            alt="">2.1
                                        km</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/clock-gray.svg')}}"
                                            alt="">
                                        Same Day</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/truck.svg')}}" alt="">Free
                                        Delivery</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/dolar.svg')}}" alt="">Min
                                        $18</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                        Now <img class="filter brightness-0 invert"
                                            src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="{{asset('website/assets/images/stores/store-05.jpg')}}"
                                    alt="">
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="{{asset('website/assets/icons/love-icon.svg')}}"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                                        <img class="object-contain w-full h-full"
                                            src="{{asset('website/assets/images/stores/store-1.png')}}" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            Urban Wash Co.
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">5.0</span>
                                            <span class="text-sm text-left text-neutral-500">(93)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    Luxury cleaning and garment care for your daily and designer wear.
                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">
                                        Designer Care </span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">
                                        Delicate Wash</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/location.svg')}}"
                                            alt="">1.5
                                        km</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/clock-gray.svg')}}"
                                            alt="">48
                                        hours</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/truck.svg')}}" alt="">Free
                                        Delivery</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/dolar.svg')}}" alt="">Min
                                        $23</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                        Now <img class="filter brightness-0 invert"
                                            src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                                </div>
                            </div>
                        </div>
                        <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                            <div class="rs-services-thumb relative">
                                <img class="object-contain w-full" src="{{asset('website/assets/images/stores/store-06.jpg')}}"
                                    alt="">
                                <div class="thumb-popular thumb-discount">
                                    20% OFF
                                </div>
                                <div
                                    class="love-icon w-[28px] h-[28px] bg-white backdrop-blur-[4px] bg-opacity-[.3] absolute top-[10px] right-[10px] leading-[28px] rounded-full text-center flex items-center justify-center cursor-pointer">
                                    <img class="w-[13px] h-[14px] mt-[2px]" src="{{asset('website/assets/icons/love-icon.svg')}}"
                                        alt="">
                                </div>
                            </div>
                            <div class="rs-services-content p-4">
                                <div class="flex justify-start gap-4 mb-[15px]">
                                    <div
                                        class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                                        <img class="object-contain w-full h-full"
                                            src="{{asset('website/assets/images/stores/store-1.png')}}" alt="">
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-left text-neutral-900">
                                            CleanPro Express
                                        </p>

                                        <div class="flex justify-start items-center gap-1">
                                            <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                            <span class="text-sm font-medium text-left text-neutral-800">4.7</span>
                                            <span class="text-sm text-left text-neutral-500">(624)</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                    Premium quality dry cleaning services with same-day delivery
                                <p>
                                <div class="flex gap-[5px] items-center pb-[15px] mb-[15px] border-b border-b-neutral-100">
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Luxury
                                        Service </span>
                                    <span
                                        class="text-xs text-neutral-500 font-normal leading-[24px] h-6 inline-block px-2 bg-neutral-50 rounded-sm">Leather
                                        Care</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-[15px] mb-[15px]">
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/location.svg')}}"
                                            alt="">2.5
                                        km</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/clock-gray.svg')}}"
                                            alt="">12
                                        hours</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/truck.svg')}}" alt="">Free
                                        Delivery</span>
                                    <span
                                        class="text-[13px] text-neutral-500 font-normal leading-[140%] flex items-center gap-[5px] w-[44%]"><img
                                            class="w-[12px] h-[14px]" src="{{asset('website/assets/icons/dolar.svg')}}" alt="">Min
                                        $22</span>
                                </div>
                                <div class="rs-services-bottom-content flex items-center">
                                    <button type="submit"
                                        class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                        Now <img class="filter brightness-0 invert"
                                            src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>


        <!-- get started -->
        <section class="bg-mint-600 bottom-0">
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
                        <p>Book Now</p>

                        <img src="{{asset('website/assets/icons/arrow-left-green.svg')}}" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p>Become a Partner</p>
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection
