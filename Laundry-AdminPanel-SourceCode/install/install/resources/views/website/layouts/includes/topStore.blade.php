 <section class="section_container max-w-2lg mx-auto px-4 xl-1:px-0">
            <header class="flex flex-col md:flex-row justify-between items-center gap-4">

                <div class="space-y-4">
                    <div class="flex justify-center md:justify-start">
                        <div
                            class="flex justify-center items-center gap-1 w-fit bg-mint-50 px-2 p-2 border-[1.5px] border-mint-200 rounded-[25px]">
                            <img src="{{ asset('website/assets/icons/star.svg') }}" alt="">
                            <p class="text-[10px] sm:text-xs font-medium text-left text-mint-700">
                                Trusted by 50,000+ Happy Customers
                            </p>
                        </div>
                    </div>
                    <div class="heading_section_2">
                        <p>
                            Top Rated <span>Stores</span>
                        </p>
                        <p>
                            Choose from our verified partner stores near you
                        </p>
                    </div>
                </div>

                <button
                    class="px-4 py-3 md:px-5 md:py-3.5 rounded-xl flex justify-center items-center gap-3 border-[1.5px] border-gray-200">
                    <p class="text-xs md:text-base font-semibold text-center text-gray-500">View All Stores</p>
                    <img class="h-3 w-3" src="{{ asset('website/assets/icons/arrow-left-gray.svg') }}"
                        alt="">
                </button>
            </header>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl-1:grid-cols-4 gap-6">


                <!-- store card 1 -->
                <div
                    class="flex flex-col p-4 rounded-3xl transition-all duration-200 outline outline-transparent outline-1 hover:outline-offset-2 hover:outline-mint-600  bg-white">
                    <div class="flex justify-start gap-4">
                        <div class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                            <img class="object-contain w-full h-full"
                                src="{{ asset('website/assets/images/stores/store-1.png') }}" alt="">
                        </div>


                        <div class="space-y-1">
                            <p class="text-base font-semibold text-left text-neutral-900">
                                CleanPro Express
                            </p>

                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                    class="">
                                <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                <span class="text-sm text-left text-neutral-500">(624)</span>
                            </div>
                        </div>
                    </div>
                    <div class=" my-[15px] flex items-center gap-1">
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Luxury Service</p>
                        </span>
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Leather Care</p>
                        </span>
                    </div>
                    <div class="flex flex-col border-t border-gray-100 mt-auto">


                        <div class="py-[15px] flex justify-between items-center">
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">Eastend • 2.5 km</p>

                            </div>
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/clock-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">02 hours</p>

                            </div>
                        </div>

                        <button
                            class="w-full flex justify-center items-center gap-2 px-3 py-2 rounded-lg transition-all duration-150 border-[1.5px] border-mint-600 group hover:bg-mint-600">
                            <p
                                class="transition-all duration-150 text-xs font-semibold text-center text-mint-600 group-hover:text-white">
                                View Details
                            </p>
                        </button>
                    </div>
                </div>


                <!-- store card 2 -->
                <div
                    class="flex flex-col p-4 rounded-3xl transition-all duration-200 outline outline-transparent outline-1 hover:outline-offset-2 hover:outline-mint-600  bg-white">
                    <div class="flex justify-start gap-4">
                        <div class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                            <img class="object-contain w-full h-full"
                                src="{{ asset('website/assets/images/stores/store-1.png') }}" alt="">
                        </div>


                        <div class="space-y-1">
                            <p class="text-base font-semibold text-left text-neutral-900">
                                CleanPro Express
                            </p>

                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                    class="">
                                <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                <span class="text-sm text-left text-neutral-500">(624)</span>
                            </div>
                        </div>
                    </div>
                    <div class=" my-[15px] flex items-center gap-1">
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Luxury Service</p>
                        </span>
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Leather Care</p>
                        </span>
                    </div>
                    <div class="flex flex-col border-t border-gray-100 mt-auto">


                        <div class="py-[15px] flex justify-between items-center">
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">Eastend • 2.5 km</p>

                            </div>
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/clock-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">02 hours</p>

                            </div>
                        </div>

                        <button
                            class="w-full flex justify-center items-center gap-2 px-3 py-2 rounded-lg transition-all duration-150 border-[1.5px] border-mint-600 group hover:bg-mint-600">
                            <p
                                class="transition-all duration-150 text-xs font-semibold text-center text-mint-600 group-hover:text-white">
                                View Details
                            </p>
                        </button>
                    </div>
                </div>


                <!-- store card 3 -->
                <div
                    class="flex flex-col p-4 rounded-3xl transition-all duration-200 outline outline-transparent outline-1 hover:outline-offset-2 hover:outline-mint-600  bg-white">
                    <div class="flex justify-start gap-4">
                        <div class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                            <img class="object-contain w-full h-full"
                                src="{{ asset('website/assets/images/stores/store-1.png') }}" alt="">
                        </div>


                        <div class="space-y-1">
                            <p class="text-base font-semibold text-left text-neutral-900">
                                CleanPro Express
                            </p>

                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                    class="">
                                <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                <span class="text-sm text-left text-neutral-500">(624)</span>
                            </div>
                        </div>
                    </div>
                    <div class=" my-[15px] flex items-center gap-1">
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Luxury Service</p>
                        </span>
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Leather Care</p>
                        </span>
                    </div>
                    <div class="flex flex-col border-t border-gray-100 mt-auto">


                        <div class="py-[15px] flex justify-between items-center">
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">Eastend • 2.5 km</p>

                            </div>
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/clock-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">02 hours</p>

                            </div>
                        </div>

                        <button
                            class="w-full flex justify-center items-center gap-2 px-3 py-2 rounded-lg transition-all duration-150 border-[1.5px] border-mint-600 group hover:bg-mint-600">
                            <p
                                class="transition-all duration-150 text-xs font-semibold text-center text-mint-600 group-hover:text-white">
                                View Details
                            </p>
                        </button>
                    </div>
                </div>

                <!-- store card 4 -->
                <div
                    class="flex flex-col p-4 rounded-3xl transition-all duration-200 outline outline-transparent outline-1 hover:outline-offset-2 hover:outline-mint-600  bg-white">
                    <div class="flex justify-start gap-4">
                        <div class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                            <img class="object-contain w-full h-full"
                                src="{{ asset('website/assets/images/stores/store-1.png') }}" alt="">
                        </div>


                        <div class="space-y-1">
                            <p class="text-base font-semibold text-left text-neutral-900">
                                CleanPro Express
                            </p>

                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt=""
                                    class="">
                                <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                <span class="text-sm text-left text-neutral-500">(624)</span>
                            </div>
                        </div>
                    </div>
                    <div class=" my-[15px] flex items-center gap-1">
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Luxury Service</p>
                        </span>
                        <span class="block w-fit relative overflow-hidden px-2 py-1.5 rounded bg-neutral-50">
                            <p class="text-xs text-center text-gray-500">Leather Care</p>
                        </span>
                    </div>
                    <div class="flex flex-col border-t border-gray-100 mt-auto">


                        <div class="py-[15px] flex justify-between items-center">
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">Eastend • 2.5 km</p>

                            </div>
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/clock-gray.svg') }}" alt=""
                                    class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">02 hours</p>

                            </div>
                        </div>

                        <button
                            class="w-full flex justify-center items-center gap-2 px-3 py-2 rounded-lg transition-all duration-150 border-[1.5px] border-mint-600 group hover:bg-mint-600">
                            <p
                                class="transition-all duration-150 text-xs font-semibold text-center text-mint-600 group-hover:text-white">
                                View Details
                            </p>
                        </button>
                    </div>
                </div>





            </div>

            <div class="flex justify-center items-center">
                <button class="btn_solid">
                    <p>Browse All Vendors</p>

                    <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
                </button>
            </div>
        </section>
