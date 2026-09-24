@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $footer = $webSettings->firstWhere('key', 'footer');
    $value = $footer?->value ?? [];
    // dd($footer);
@endphp

<footer class=" bg-no-repeat bg-cover"
    style="background-image: url('{{ asset('website/assets/images/footer/footer-bg.png') }}');">

    <!-- top footer  -->
    <div class="relative overflow-hidden">
        <div class="max-w-2lg mx-auto pt-[60px] relative z-10 px-4 xl-1:px-0">


            <!-- links section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 lg:gap-[65px] relative z-10">
                <!-- logo section -->
                <div class="space-y-[30px]">
                    <img src="{{ isset($value['footer_logo']) ? Storage::url($value['footer_logo']) : asset('website/assets/logo/logo-white.png') }}"
                        alt="" class=" w-auto h-14 md:w-auto md:h-[51.58px] object-contain">
                    <p class="text-base text-left text-gray-300 font-normal">
                        {!! strip_tags($value['footer_title'] ?? '', '<br>') ?:
                            __('Elevate Your Business with Innovative<br />Web, App, and Software Solutions. Partner for Excellence in Tech.') !!}
                    </p>
                </div>

                <div class="flex flex-col gap-6">
                    <p class="text-lg font-medium text-left text-neutral-50">{{ __('Quick Links') }}</p>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Home') }}</p>
                    </a>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Our Services') }}</p>
                    </a>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('How It Works') }}</p>
                    </a>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Partner Vendors') }}</p>
                    </a>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Pricing') }}</p>
                    </a>
                </div>


                <div class="flex flex-col gap-6">
                    <p class="text-lg font-medium text-left text-neutral-50">{{ __('Support') }}</p>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Help Center') }}</p>
                    </a>
                    <a href="{{ route('web.terms') }}" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Terms & Conditions') }}</p>
                    </a>
                    <a href="{{ route('web.privacy') }}" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Privacy Policy') }}</p>
                    </a>
                    <a href="#" class="flex justify-start items-center gap-1">
                        <p class="text-base text-left text-neutral-400">{{ __('Contact Us') }}</p>
                    </a>
                </div>



                <div>
                    <div class="flex flex-col gap-6">
                        <p class="self-stretch justify-center text-white text-lg font-semibold  leading-relaxed">
                            {{ __('Contact Us') }}</p>
                        <div class="self-stretch inline-flex justify-start items-start gap-2 cursor-pointer">

                            <img src="{{ asset('website/assets/icons/phone.svg') }}" alt=""
                                class="w-4 h-4 mt-1.5">
                            <p class="flex-1 text-base text-left text-neutral-300">
                                {{ $value['contact_us']['phone_number'] ?? '+8801937203743' }}
                            </p>
                        </div>
                        <div class="self-stretch inline-flex justify-start items-start gap-2 cursor-pointer">

                            <img src="{{ asset('website/assets/icons/map-pin.svg') }}" alt=""
                                class="w-4 h-4 mt-1.5">
                            <p class="flex-1 text-base text-left text-neutral-300">
                                {{ $value['contact_us']['address'] ?? '123 CleanStreet, CleanCity, CC 12345' }}
                            </p>
                        </div>
                    </div>



                    <div class="mt-[30px] space-y-6">
                        <p class="text-base font-medium text-left text-gray-50">
                            {{ __('Follow Us') }}
                        </p>

                        <div class="flex items-center gap-3">
                            @foreach ($value['follow_us'] ?? [] as $follow)
                                <a href="{{ $follow['link'] }}" target="_blank" rel="noopener" class="group">
                                    <div
                                        class="h-14 w-14 rounded-xl bg-white/5 border border-white/10
                       flex items-center justify-center
                       transition-all duration-200
                       hover:bg-mint-600 hover:scale-105 hover:shadow-lg">
                                        <img src="{{ Storage::url($follow['icon']) }}"
                                            class="w-10 h-10 object-contain transition-transform duration-200 group-hover:scale-110"
                                            alt="">
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>


            <div class="relative z-0 pb-[30px] pt-[50px]">

                <img src="{{ isset($value['footer_background']) ? Storage::url($value['footer_background']) : asset('website/assets/images/footer/laundry.svg') }}"
                    alt="">
            </div>


            <div
                class="flex flex-col sm:flex-row items-center justify-between py-6 border-t border-white/10 relative z-10">
                <p class="text-xs sm:text-sm text-white/40 font-normal">
                    {{ $value['footer_left_side_text'] ?? __('Professional dry cleaning services you can trust.') }}
                </p>
                <p class="text-xs sm:text-sm text-white/40 font-normal">â“’{{ date('Y') }}
                    {{ config('app.name', 'Elite Cleaning') }} {{ $value['footer_right_side_text'] ?? __('All Rights Reserved.') }}
                </p>
            </div>




            <!-- glows -->
            <div class="w-[400px] h-[400px] absolute -top-[70%] left-[10%] bg-mint-600 rounded-full blur-[150px]">
            </div>
            <div class="w-[400px] h-[400px] absolute -bottom-[50%] left-[30%] bg-mint-600 rounded-full blur-[150px]">
            </div>

        </div>

    </div>
</footer>
