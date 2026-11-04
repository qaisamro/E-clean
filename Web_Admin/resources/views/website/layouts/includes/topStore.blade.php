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

                <a href="{{ route('web.stores') }}"
                    class="px-4 py-3 md:px-5 md:py-3.5 rounded-xl flex justify-center items-center gap-3 border-[1.5px] border-gray-200">
                    <p class="text-xs md:text-base font-semibold text-center text-gray-500">View All Stores</p>
                    <img class="h-3 w-3" src="{{ asset('website/assets/icons/arrow-left-gray.svg') }}"
                        alt="">
                </a>
            </header>

            <!-- Filter by address -->
            <form method="GET" action="{{ url()->current() }}" class="mt-6 flex gap-2 justify-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن متجر بالعنوان... (مثال: دورا، الخليل)"
                    class="w-full max-w-md px-4 py-3 rounded-xl border border-gray-200 focus:border-mint-500 focus:ring-1 focus:ring-mint-500 outline-none text-sm">
                <button type="submit" class="px-6 py-3 bg-mint-600 text-white rounded-xl text-sm font-semibold">فلترة</button>
                @if(request('search'))
                    <a href="{{ url()->current() }}" class="px-4 py-3 bg-gray-100 rounded-xl text-sm">إلغاء</a>
                @endif
            </form>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl-1:grid-cols-4 gap-6">
                @forelse($vendors ?? [] as $vendor)
                <div class="flex flex-col p-4 rounded-3xl transition-all duration-200 outline outline-transparent outline-1 hover:outline-offset-2 hover:outline-mint-600 bg-white">
                    <div class="flex justify-start gap-4">
                        <div class="w-12 h-12 flex justify-center items-center border border-gray-100 rounded-lg p-1">
                            <img class="object-contain w-full h-full" src="{{ $vendor->logoPath }}" alt="{{ $vendor->name }}">
                        </div>
                        <div class="space-y-1">
                            <p class="text-base font-semibold text-left text-neutral-900">{{ $vendor->name }}</p>
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/star-gold.svg') }}" alt="">
                                <span class="text-sm font-medium text-left text-neutral-800">4.9</span>
                                <span class="text-sm text-left text-neutral-500">(0)</span>
                            </div>
                        </div>
                    </div>
                    <div class="my-[15px]">
                        <p class="text-xs text-left text-gray-500 flex items-center gap-1"><img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" class="h-3 w-3">{{ $vendor->address }}</p>
                    </div>
                    <div class="flex flex-col border-t border-gray-100 mt-auto">
                        <div class="py-[15px] flex justify-between items-center">
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/map-pin-gray.svg') }}" alt="" class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">{{ Str::limit($vendor->address, 20) }}</p>
                            </div>
                            <div class="flex justify-start items-center gap-1">
                                <img src="{{ asset('website/assets/icons/clock-gray.svg') }}" alt="" class="h-3 w-3">
                                <p class="text-sm text-left text-gray-500">{{ $vendor->phone }}</p>
                            </div>
                        </div>
                        <a href="{{ route('web.vendor.store', $vendor->id) }}"
                            class="w-full flex justify-center items-center gap-2 px-3 py-2 rounded-lg transition-all duration-150 border-[1.5px] border-mint-600 group hover:bg-mint-600">
                            <p class="transition-all duration-150 text-xs font-semibold text-center text-mint-600 group-hover:text-white">View Details</p>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500">لا توجد محلات تطابق البحث "{{ request('search') }}"</p>
                    <p class="text-sm text-gray-400 mt-2">جرب كلمات أخرى من العنوان</p>
                </div>
                @endforelse





            </div>

            <div class="flex justify-center items-center">
                <button class="btn_solid">
                    <p>Browse All Vendors</p>

                    <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
                </button>
            </div>
        </section>
