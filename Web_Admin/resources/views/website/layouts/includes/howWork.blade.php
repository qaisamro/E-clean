@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $howItWork = $webSettings->firstWhere('key', 'how_it_works');
    $howItWorkValue = $howItWork?->value ?? [];
    $steps = $howItWorkValue['work_steps'] ?? [];
@endphp


<section class="max-w-2lg mx-auto px-4 xl-1:px-0 grid grid-cols-1 md:grid-cols-2 gap-5 xl:gap-[66px] mb-10" style="background:#FAF7F2;">
    <div class="space-y-5 xl:space-y-10 order-2 md:order-1">
        <div class="space-y-1 md:space-y-[10px]">
            <div class="flex justify-center md:justify-start">
                <span style="display:inline-flex;align-items:center;background:#EAF6F8;color:#028090;border:1px solid #E2E8F0;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;">{{ __('How It Works') }}</span>
            </div>
            <div class="text-center md:text-start text-3xl font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">
                {{ !empty($howItWorkValue['title'])
                    ? $howItWorkValue['title']
                    : __('Dry Cleaning Done in 4 Simple Steps - Fast, Easy & Convenient') }}
            </div>


        </div>




        <!-- Dynamic Cards -->
        <div class="space-y-4">

            @forelse($steps as $index => $step)
                <div class="w-full p-4 rounded-2xl bg-white flex items-start gap-4" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    <button class="h-9 w-14 rounded-[56px]" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;min-width:56px;">
                        <p class="text-lg font-semibold text-center text-white">
                            {{ str_pad($step['number'] ?? $index + 1, 2, '0', STR_PAD_LEFT) }}
                        </p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">
                            {{ $step['title'] ?? '' }}
                        </p>

                        <p class="text-sm text-left" style="color:#5B6B7A;">
                            {{ $step['sub_title'] ?? '' }}
                        </p>
                    </div>

                </div>
            @empty
                <!-- Default Cards -->
                <div class="w-full p-4 rounded-2xl bg-white flex items-start gap-4" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    <button class="h-9 w-14 rounded-[56px]" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;min-width:56px;">
                        <p class="text-lg font-semibold text-center text-white">01</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">{{ __('Schedule Pickup') }}</p>

                        <p class="text-sm text-left" style="color:#5B6B7A;">{{ __('Book your dry cleaning service online or through our app') }}</p>
                    </div>

                </div>
                <div class="w-full p-4 rounded-2xl bg-white flex items-start gap-4" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    <button class="h-9 w-14 rounded-[56px]" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;min-width:56px;">
                        <p class="text-lg font-semibold text-center text-white">02</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">{{ __('We Collect') }}</p>

                        <p class="text-sm text-left" style="color:#5B6B7A;">{{ __('Our driver picks up your dry cleaning from your location') }}</p>
                    </div>

                </div>
                <div class="w-full p-4 rounded-2xl bg-white flex items-start gap-4" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    <button class="h-9 w-14 rounded-[56px]" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;min-width:56px;">
                        <p class="text-lg font-semibold text-center text-white">03</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">{{ __('We Wash & Fold') }}</p>

                        <p class="text-sm text-left" style="color:#5B6B7A;">{{ __('Professional cleaning and careful folding of your clothes') }}</p>
                    </div>

                </div>
                <div class="w-full p-4 rounded-2xl bg-white flex items-start gap-4" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    <button class="h-9 w-14 rounded-[56px]" style="background:linear-gradient(135deg,#028090,#00A896);border-radius:14px;min-width:56px;">
                        <p class="text-lg font-semibold text-center text-white">04</p>
                    </button>

                    <div>
                        <p class="text-lg font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">{{ __('We Deliver') }}</p>

                        <p class="text-sm text-left" style="color:#5B6B7A;">{{ __('Fresh dry cleaning delivered back to your doorstep') }}</p>
                    </div>

                </div>
            @endforelse

        </div>
    </div>
    <div class="flex items-end order-1 md:order-2">
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:10px;width:100%;">
            <img src="{{ isset($howItWorkValue['right_side_img'])
            ? Storage::url($howItWorkValue['right_side_img'])
            : asset('website/assets/images/how-it-works/bg.png') }}"
            alt="" class="min-h-[90%] w-full object-cover rounded-3xl" style="border-radius:14px;">
        </div>
    </div>
</section>
