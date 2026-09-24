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
                        <img src="{{ asset('website/assets/icons/close.svg') }}" alt="">
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
    <nav class="bg-white sticky top-0 py-4 md:py-6 z-40 shadow-md">
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



            <div class="hidden lg:flex justify-center items-center gap-[10px]">
                <button class="btn_solid">
                        <p>Sign In</p>

                        <img src="{{asset('website/assets/icons/arrow-left.svg')}}" alt="">
                    </button>

                    <button class="btn_outline">
                        <p>Sign Up</p>
                    </button>

                <button
                    class="w-12 h-12 rounded-full flex justify-center items-center border border-mint-100 transition-all duration-150 hover:bg-mint-600 group relative">
                    <img src="{{asset('website/assets/icons/heart-green.svg')}}" alt=""
                        class="h-[18px] w-[18px] group-hover:filter group-hover:invert group-hover:brightness-0">

                    <span
                        class="absolute -top-1 -right-2 bg-mint-600 text-xs text-white w-5 h-5 rounded-full flex justify-center items-center">
                        3
                    </span>
                </button>
                <button
                    class="w-12 h-12 rounded-full flex justify-center items-center border border-mint-100 transition-all duration-150 hover:bg-mint-600 group relative">
                    <img src="{{asset('website/assets/icons/basket-green.svg')}}" alt=""
                        class="h-[18px] w-[18px] group-hover:filter group-hover:invert group-hover:brightness-0">

                    <span
                        class="absolute -top-1 -right-2 bg-mint-600 text-xs text-white w-5 h-5 rounded-full flex justify-center items-center">
                        3
                    </span>
                </button>
                <button
                    class="w-12 h-12 rounded-full flex justify-center items-center border border-mint-100 transition-all duration-150 hover:bg-mint-600 group">
                    <img src="{{asset('website/assets/icons/user-green.svg')}}" alt=""
                        class="h-[18px] w-[18px] group-hover:filter group-hover:invert group-hover:brightness-0">
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
    </nav>


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
        <!-- Service details area -->
        <section class="rs-service-details-area max-w-2lg  mx-auto mt-[50px] mb-6 px-4 xl:px-0">
            <div class="rs-service-details-top mb-10 ">
                <div class="rs-service-details-top-link mb-[30px]">
                    <a class="text-lg font-normal leading-[100%] text-neutral-500" href="#">Home / </a>
                    <a class="text-lg font-normal leading-[100%] text-neutral-500" href="#">Services / </a>
                    <a class="text-lg font-normal leading-[100%] text-neutral-500" href="#">Dry Cleaning / </a>
                    <a class="text-lg font-normal leading-[100%] text-neutral-500 active" href="#">Premium Dry
                        Cleaning</a>
                </div>
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 md:col-span-7">
                            <div class="rs-service-details-thumb h-full">
                                <img class="rounded-3xl h-full object-cover"
                                    src="{{asset('website/assets/images/service-details/service-details.jpg')}}" alt="">
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-5">
                            <div class="rs-service-details-top-right">
                                <div
                                    class="thumb-popular thumb-premium thumb-premium-2 w-[124px] leading-[28px] mb-[22px]">
                                    Popular Choice
                                </div>
                                <h3 class="text-2xl font-bold leading-[120%] text-neutral-900 mb-[7px]">Premium Dry
                                    Cleaning</h3>
                                <h4 class="text-lg font-medium leading-[100%] text-neutral-500 mb-[15px]">
                                    By FreshFold Laundry
                                </h4>
                                <p class="text-base font-normal leading-[140%] text-neutral-500 mb-[15px]">
                                    Our premium dry cleaning service combines advanced fabric care technology with
                                    expert attention to detail. From delicate silks to formal suits, every garment is
                                    treated with precision to restore freshness, maintain texture, and extend its life —
                                    all while ensuring a flawless finish every time.
                                </p>
                                <div class="flex justify-start items-center gap-[2px] mb-[15px]">
                                    <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                    <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                    <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                    <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                    <img src="{{asset('website/assets/icons/star-gold.svg')}}" alt="" class="">
                                    <span
                                        class="text-sm font-medium text-left text-neutral-800 mr-[2px] ml-[2px]">4.9</span>
                                    <span class="text-sm text-left text-neutral-500">(624 reviews)</span>
                                </div>
                                <div class="flex items-center gap-2 mb-[20px]">
                                    <h5 class="text-2xl md:text-lg text-mint-700 font-semibold leading-[140%]">$12.99
                                    </h5>
                                    <span
                                        class="text-xl text-neutral-400 font-normal leading-[140%] line-through">$15.99</span>
                                    <div class="thumb-popular thumb-discount thumb-discount-2 ml-[7px]">
                                        20% OFF
                                    </div>
                                </div>
                                <p class="flex gap-[5px] text-base font-normal leading-[100%] text-neutral-500 mb-[14px]">
                                    <img src="{{asset('website/assets/icons/clock.svg')}}" alt="">Same-day delivery available
                                </p>
                                <p class="flex gap-[5px] text-base font-normal leading-[100%] text-neutral-500 mb-[14px]">
                                    <img class="w-4 h-4" src="{{asset('website/assets/icons/location-green.svg')}}"
                                        alt="">Available in
                                    your area
                                </p>
                                <p class="flex gap-[5px] text-base font-normal leading-[100%] text-neutral-500 mb-[24px]">
                                    <img src="{{asset('website/assets/icons/shield-check-green.svg')}}" alt="">100% satisfaction
                                    guaranteed
                                </p>

                                <div class="w-full flex  justify-between gap-4">
                                    <div class="counter_container w-full sm:w-[140px] h-[48px]" id="card-1-counter">
                                        <button class="decrease text-base font-medium text-center text-neutral-700"
                                            onclick="counterController('card-1-counter', 'decrease')">
                                            -
                                        </button>
                                        <div class="current_count ">
                                            0
                                        </div>
                                        <button class="increase"
                                            onclick="counterController('card-1-counter', 'increase')">
                                            +
                                        </button>
                                    </div>

                                    <button type="submit"
                                        class="flex items-center justify-center gap-[10px] text-xs text-mint-600 font-semibold leading-[133%] h-[48px] w-full sm:w-[306px] border-[1.50px] border-mint-600 rounded-xl">
                                        Add To Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rs-service-details-tab-area pb-[60px] border-b border-b-neutral-200">
                <div
                    class="rs-service-details-tab mb-[30px] flex items-center justify-between h-12 border-b border-b-neutral-200">
                    <div data-tab-trigger onclick="openTab('description', this)" class="active">Description</div>
                    <div data-tab-trigger onclick="openTab('details', this)">Details</div>
                    <div data-tab-trigger onclick="openTab('reviews', this)">Reviews(624)</div>
                </div>
                <!-- description area -->
                <div id="description" class="tab-panel">
                    <p class="text-base font-normal leading-[140%] text-neutral-500 mb-[25px]">
                        Our Premium Wash & Fold service is designed for busy individuals who want professional care for
                        their everyday laundry. We use eco- <br>friendly, hypoallergenic detergents and fabric softeners
                        to
                        ensure your clothes are not only clean but also soft and fresh.
                    </p>
                    <p class="text-base font-normal leading-[140%] text-neutral-500 mb-[16px]">
                        Each load is carefully sorted by color and fabric type, washed at the optimal temperature, and
                        dried
                        to perfection. Our expert team <br> then meticulously folds each item, ensuring you receive your
                        laundry
                        ready to put away.
                    </p>
                    <div class="rs-description-info">
                        <h3 class="text-2xl font-semibold leading-[140%] text-neutral-900 mb-[18px]">What's Included:
                        </h3>
                        <ul class="rs-description-list">
                            <li>Professional washing with premium detergents</li>
                            <li>Fabric softener treatment</li>
                            <li>Precise drying at optimal temperature</li>
                            <li>Expert folding and packaging</li>
                            <li>Free pickup and delivery</li>
                            <li>Stain treatment at no extra cost</li>
                        </ul>
                    </div>
                </div>

                <!-- Details area -->
                <div id="details" class="tab-panel hidden">
                    Details
                </div>

                <!-- review area -->
                <div id="reviews" class="tab-panel hidden">
                    review
                </div>
            </div>

            <div class="rs-relevant-services-area mt-[55px] mb-[80px]">
                <div class="rs-relevant-services-top flex items-center justify-between mb-6">
                    <h5 class="text-base md:text-lg font-medium leading-[100%] text-neutral-500">
                        Relevant Services
                    </h5>
                    <a href="#"
                        class="text-sm md:text-base font-semibold leading-[125%] text-neutral-500 flex gap-2 items-center justify-center h-10 md:h-12 w-[180px] md:w-[214px] border-[1.50px] border-neutral-200 rounded-lg md:rounded-xl">
                        View All Services
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="rs-services-wrapper grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 ">
                    <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                        <div class="rs-services-thumb relative">
                            <img class="object-contain w-full" src="{{asset('website/assets/images/service/service-01.jpg')}}"
                                alt="">
                            <div class="thumb-popular">
                                Popular
                            </div>
                        </div>
                        <div class="rs-services-content p-4">
                            <div class="rs-services-rat flex gap-[3px] mb-[10px]">
                                <img src="{{asset('website/assets/icons/star-small.svg')}}" alt="">
                                <span class="text-sm text-neutral-700 font-medium leading-[140%]">4.9</span>
                                <span class="text-sm text-neutral-700 font-normal leading-[140%]">(34)</span>
                            </div>
                            <h3 class="text-sm text-neutral-900 font-semibold leading-[140%] mb-[15px]">
                                Premium Wash & Fold
                            </h3>
                            <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                Professional wash, dry, and fold service with eco friendly detergents</p>
                            <p class="text-xs text-neutral-500 font-normal leading-[100%] mb-[15px]">By CleanPro
                                Laundry</p>
                            <div class="rs-services-bottom-content flex items-center">
                                <h5 class="text-sm text-mint-600 font-semibold leading-[100%]">
                                    $12.99
                                </h5>
                                <span
                                    class="text-sm text-neutral-400 font-normal leading-[100%] line-through ml-[5px]">$15.99</span>
                                <button type="submit"
                                    class="flex items-center justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                    Now <img src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                            </div>
                        </div>
                    </div>

                    <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                        <div class="rs-services-thumb relative">
                            <img class="object-contain w-full" src="{{asset('website/assets/images/service/service-02.jpg')}}"
                                alt="">
                        </div>
                        <div class="rs-services-content p-4">
                            <div class="rs-services-rat flex gap-[3px] mb-[10px]">
                                <img src="{{asset('website/assets/icons/star-small.svg')}}" alt="">
                                <span class="text-sm text-neutral-700 font-medium leading-[140%]">4.8</span>
                                <span class="text-sm text-neutral-700 font-normal leading-[140%]">(241)</span>
                            </div>
                            <h3 class="text-sm text-neutral-900 font-semibold leading-[140%] mb-[15px]">
                                Express Dry Cleaning
                            </h3>
                            <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                Same-day dry cleaning for your delicate garments</p>
                            <p class="text-xs text-neutral-500 font-normal leading-[100%] mb-[15px]">By CleanPro
                                Laundry</p>
                            <div class="rs-services-bottom-content flex items-center">
                                <h5 class="text-sm text-mint-600 font-semibold leading-[100%]">
                                    $24.99
                                </h5>
                                <button type="submit"
                                    class="flex items-center justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                    Now <img src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                            </div>
                        </div>
                    </div>

                    <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                        <div class="rs-services-thumb relative">
                            <img class="object-contain w-full" src="{{asset('website/assets/images/service/service-03.jpg')}}"
                                alt="">
                            <div class="thumb-popular thumb-premium">
                                Premium
                            </div>
                        </div>
                        <div class="rs-services-content p-4">
                            <div class="rs-services-rat flex gap-[3px] mb-[10px]">
                                <img src="{{asset('website/assets/icons/star-small.svg')}}" alt="">
                                <span class="text-sm text-neutral-700 font-medium leading-[140%]">4.9</span>
                                <span class="text-sm text-neutral-700 font-normal leading-[140%]">(92)</span>
                            </div>
                            <h3 class="text-sm text-neutral-900 font-semibold leading-[140%] mb-[15px]">
                                Luxury Wash & Fold
                            </h3>
                            <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                Premium service with fabric softener and folding</p>
                            <p class="text-xs text-neutral-500 font-normal leading-[100%] mb-[15px]">By Premium
                                Cleaners</p>
                            <div class="rs-services-bottom-content flex items-center">
                                <h5 class="text-sm text-mint-600 font-semibold leading-[100%]">
                                    $18.99
                                </h5>
                                <button type="submit"
                                    class="flex items-center justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                    Now <img src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
                            </div>
                        </div>
                    </div>

                    <div class="rs-services-box rounded-3xl overflow-hidden bg-white">
                        <div class="rs-services-thumb relative">
                            <img class="object-contain w-full" src="{{asset('website/assets/images/service/service-04.jpg')}}"
                                alt="">
                            <div class="thumb-popular thumb-fast">
                                Fast
                            </div>
                        </div>
                        <div class="rs-services-content p-4">
                            <div class="rs-services-rat flex gap-[3px] mb-[10px]">
                                <img src="{{asset('website/assets/icons/star-small.svg')}}" alt="">
                                <span class="text-sm text-neutral-700 font-medium leading-[140%]">4.9</span>
                                <span class="text-sm text-neutral-700 font-normal leading-[140%]">(94)</span>
                            </div>
                            <h3 class="text-sm text-neutral-900 font-semibold leading-[140%] mb-[15px]">
                                Shoe Spa Treatment
                            </h3>
                            <p class="text-[13px] text-neutral-500 font-medium leading-[140%] mb-[15px]">
                                Deep cleaning and restoration for all types of shoes</p>
                            <p class="text-xs text-neutral-500 font-normal leading-[100%] mb-[15px]">By ShoeCare
                                Pro
                            </p>
                            <div class="rs-services-bottom-content flex items-center">
                                <h5 class="text-sm text-mint-600 font-semibold leading-[100%]">
                                    $15.99
                                </h5>
                                <span
                                    class="text-sm text-neutral-400 font-normal leading-[100%] line-through ml-[5px]">$24.99</span>
                                <button type="submit"
                                    class="flex items-center justify-center gap-[10px] ml-auto text-xs text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl">Book
                                    Now <img src="{{asset('website/assets/icons/green-right-arrow.svg')}}" alt=""></button>
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
                        Book your first order and experience hassle-free laundry service
                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <p>Book Now</p>

                        <img src="{{asset('website')}}../assets/icons/arrow-left-green.svg" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p>Become a Partner</p>
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection
