@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $howItWork = $webSettings->firstWhere('key', 'how_it_works');
    $howItWorkValue = $howItWork?->value ?? [];
    $steps = $howItWorkValue['work_steps'] ?? [];
@endphp


<section class="max-w-2lg mx-auto px-4 xl-1:px-0 grid grid-cols-1 md:grid-cols-2 gap-5 xl:gap-[66px] mb-10">
    <div class="space-y-5 xl:space-y-10 order-2 md:order-1">
        <div class="space-y-1 md:space-y-[10px]">
            <p class="text-center md:text-start text-lg xl:text-2xl text-neutral-500">{{ __('How It Works') }}</p>
            <div class="text-center md:text-start text-3xl font-semibold text-neutral-900">
                {!! !empty($howItWorkValue['title'])
                    ? $howItWorkValue['title']
                    : __('Dry Cleaning Done in 4 Simple Steps - Fast, Easy & Convenient') !!}
            </div>


        </div>





        <!-- Dynamic Cards -->
        <div class="space-y-4">

            @forelse($steps as $index => $step)
                <div class="w-full p-4 space-y-4 rounded-2xl shadow-lg shadow-mint-100 bg-white">

                    <button class="h-9 w-14 rounded-[56px] bg-mint-600">
                        <p class="text-lg font-semibold text-center text-white">
                            {{ str_pad($step['number'] ?? $index + 1, 2, '0', STR_PAD_LEFT) }}
                        </p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $step['title'] ?? '' }}
                        </p>

                        <p class="text-sm text-left text-neutral-500">
                            {{ $step['sub_title'] ?? '' }}
                        </p>
                    </div>

                </div>
            @empty
                <!-- Default Cards -->
                <div class="w-full p-4 space-y-4 rounded-2xl shadow-lg shadow-mint-100 bg-white">

                    <button class="h-9 w-14 rounded-[56px] bg-mint-600">
                        <p class="text-lg font-semibold text-center text-white">01</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ __('Schedule Pickup') }}</p>

                        <p class="text-sm text-left text-neutral-500">{{ __('Book your dry cleaning service online or through our app') }}</p>
                    </div>

                </div>
                <div class="w-full p-4 space-y-4 rounded-2xl shadow-lg shadow-mint-100 bg-white">

                    <button class="h-9 w-14 rounded-[56px] bg-mint-600">
                        <p class="text-lg font-semibold text-center text-white">02</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ __('We Collect') }}</p>

                        <p class="text-sm text-left text-neutral-500">{{ __('Our driver picks up your dry cleaning from your location') }}</p>
                    </div>

                </div>
                <div class="w-full p-4 space-y-4 rounded-2xl shadow-lg shadow-mint-100 bg-white">

                    <button class="h-9 w-14 rounded-[56px] bg-mint-600">
                        <p class="text-lg font-semibold text-center text-white">03</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ __('We Wash & Fold') }}</p>

                        <p class="text-sm text-left text-neutral-500">{{ __('Professional cleaning and careful folding of your clothes') }}</p>
                    </div>

                </div>
                <div class="w-full p-4 space-y-4 rounded-2xl shadow-lg shadow-mint-100 bg-white">

                    <button class="h-9 w-14 rounded-[56px] bg-mint-600">
                        <p class="text-lg font-semibold text-center text-white">04</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ __('We Deliver') }}</p>

                        <p class="text-sm text-left text-neutral-500">{{ __('Fresh dry cleaning delivered back to your doorstep') }}</p>
                    </div>

                </div>
            @endforelse

        </div>
    </div>
    <div class="flex items-end order-1 md:order-2">
        <img src="{{ isset($howItWorkValue['right_side_img'])
            ? Storage::url($howItWorkValue['right_side_img'])
            : asset('website/assets/images/how-it-works/bg.png') }}"
            alt="" class="min-h-[90%] w-full object-cover rounded-3xl">
    </div>
</section>
