@php
    $joinSetting = $webSettings->firstWhere('key', 'join_our_network');
    $join = $joinSetting?->value ?? [];
@endphp

<section class=" max-w-2lg mx-auto px-4 xl-1:px-0 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-[70px]">
    <div class="flex flex-col justify-center">

        <div>
            @php
                // Use default if title is empty
                $title = strip_tags($join['title'] ?? '');
                if (empty($title)) {
                    $title = 'Join Our Network';
                }

                // Split into words
                $words = preg_split('/\s+/', $title);

                $first = $words[0] ?? '';
                $highlight = implode(' ', array_slice($words, 1, 2)); // 2nd & 3rd words
                $rest = count($words) > 3 ? implode(' ', array_slice($words, 3)) : '';
            @endphp

            <p class="headline text-left">
                {{ $first }}
                @if ($highlight)
                    <span class="text-blue-600">{{ $highlight }}</span>
                @endif
                {{ $rest }}
            </p>

            <p class="text-base lg:text-lg font-medium text-left text-neutral-700">
                {{ $join['description'] ??
                    'Partner with Laundry and connect with customers who need your services. No setup fees, flexible
                                                                                                                                                                                                                    pricing, and instant access to our growing customer base.' }}

            </p>
        </div>

        {{-- Lists --}}
        @php
            $lists = !empty($join['lists'])
                ? $join['lists']
                : [
                    ['list' => 'Commission-based pricing (no hidden fees)'],
                    ['list' => 'Get paid weekly via direct deposit'],
                    ['list' => 'Free marketing and customer acquisition'],
                ];
        @endphp

        <ul class="pt-6 pb-10 space-y-4">
            @foreach ($lists as $item)
                <li class="flex justify-start items-center gap-[10px]">
                    <img src="{{ asset('website/assets/icons/check-solid.svg') }}" alt="check"
                        class="h-[18px] w-[18px]">
                    <p class="text-base text-left text-gray-600">
                        {{ $item['list'] ?? '' }}
                    </p>
                </li>
            @endforeach
        </ul>

        <div class="flex flex-col md:flex-row justify-start items-center gap-[15px]">
            <button class="btn_solid">
                <p>Start Your Application</p>

                <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
            </button>
            <button class="btn_outline !bg-white">
                <p>Learn More</p>
            </button>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

        @if (!empty($join['facilities']))
            @foreach ($join['facilities'] as $facility)
                <div
                    class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700">

                    {{-- Icon --}}
                    <div
                        class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300">
                        <img src="{{ !empty($facility['icon']) ? Storage::url($facility['icon']) : asset('website/assets/icons/grow.svg') }}"
                            alt="{{ !empty($facility['title']) ? $facility['title'] : 'Default Facility Icon' }}"
                            class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                    </div>

                    {{-- Content --}}
                    <div class="space-y-[10px]">
                        <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">
                            {{ !empty($facility['title']) ? $facility['title'] : 'Grow Your Business' }}
                        </p>

                        <p class="text-sm text-left text-neutral-500 group-hover:text-white">
                            {{ !empty($facility['description']) ? $facility['description'] : 'Reach thousands of customers actively searching for laundry services in your area.' }}
                        </p>
                    </div>

                </div>
            @endforeach
        @else
            {{-- Fallback if no facilities exist - Default Cards --}}
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/grow.svg') }}" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">Grow Your
                        Business</p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white">Reach thousands of customers
                        actively searching for laundry services in your area.</p>
                </div>
            </div>
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/people.svg') }}" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">Manage Orders
                        Easily</p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white">Simple dashboard to track
                        orders, manage pricing, and handle customer requests.</p>
                </div>
            </div>
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/mobile.svg') }}" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">Mobile-Friendly
                        Tools</p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white">Accept orders, update status,
                        and communicate with customers on-the-go.</p>
                </div>
            </div>
            <div
                class="flex flex-col p-4 rounded-3xl shadow-sm gap-[15px] h-fit bg-white transition-all duration-200 group hover:bg-mint-700">
                <div
                    class="icon_labels w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 transition-all duration-200 group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/headset.svg') }}" alt=""
                        class="w-6 h-6 object-contain transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
                <div class="space-y-[10px]">
                    <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">Dedicated
                        Support</p>
                    <p class="text-sm text-left text-neutral-500 group-hover:text-white">24/7 store support team to help
                        you succeed and resolve any issues quickly.</p>
                </div>
            </div>
        @endif

    </div>

    <div class="grid lg:hidden grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-7">
        <!-- card 1 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group  shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700">
            <div>
                <div class="icon_labels group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/grow.svg') }}" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">
                    Grow Your Business
                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white">
                    Reach thousands of customers actively searching for laundry services in your area.
                </p>
            </div>
        </div>

        <!-- card 2 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group  shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700">
            <div>
                <div class="icon_labels group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/people.svg') }}" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">
                    Manage Orders Easily
                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white">
                    Simple dashboard to track orders, manage pricing, and handle customer requests.
                </p>
            </div>
        </div>

        <!-- card 3 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group  shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700">
            <div>
                <div class="icon_labels group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/mobile.svg') }}" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">
                    Mobile-Friendly Tools
                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white">
                    Accept orders, update status, and communicate with customers on-the-go.
                </p>
            </div>
        </div>

        <!-- card 4 -->
        <div
            class="flex flex-col p-4 rounded-3xl transition-all duration-200 group  shadow-sm gap-[15px] h-fit bg-white hover:bg-mint-700">
            <div>
                <div class="icon_labels group-hover:bg-mint-300">
                    <img src="{{ asset('website/assets/icons/headset.svg') }}" alt=""
                        class="transition-all duration-200 group-hover:brightness-0 group-hover:invert">
                </div>
            </div>
            <div class="space-y-[10px]">
                <p class="text-base font-semibold text-left text-neutral-900 group-hover:text-white">
                    Dedicated Support
                </p>
                <p class="text-sm text-left text-neutral-500 group-hover:text-white">
                    24/7 store support team to help you succeed and resolve any issues quickly.
                </p>
            </div>
        </div>

    </div>
</section>
