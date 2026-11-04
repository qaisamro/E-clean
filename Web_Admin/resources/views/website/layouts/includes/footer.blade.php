@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $footer = $webSettings->firstWhere('key', 'footer');
    $value = $footer?->value ?? [];
    // dd($footer);
@endphp

<footer class="site-footer bg-no-repeat bg-cover"
    style="background-color: #083744; background-image: linear-gradient(135deg, #083744 0%, #0A4A54 55%, #156172 130%);">

    <!-- top footer  -->
    <div class="relative overflow-hidden">
        <div class="max-w-2lg mx-auto pt-[60px] relative z-10 px-4 xl-1:px-0">


            <!-- links section -->
            <div class="site-footer-grid grid grid-cols-1 md:grid-cols-4 gap-5 lg:gap-[65px] relative z-10">
                <!-- logo section -->
                <div class="space-y-[30px] site-footer-brand">
                    <span class="site-footer-logo inline-flex items-center rounded-2xl bg-white/95 px-3 py-2 shadow-sm">
                    <img src="{{ isset($value['footer_logo']) ? Storage::url($value['footer_logo']) : asset('website/assets/logo/logo-white.png') }}"
                        alt="" class=" w-auto h-14 md:w-auto md:h-[51.58px] object-contain">
                    </span>
                    <p class="site-footer-desc text-base text-left font-normal">
                        {!! strip_tags($value['footer_title'] ?? '', '<br>') ?:
                            __('Elevate Your Business with Innovative<br />Web, App, and Software Solutions. Partner for Excellence in Tech.') !!}
                    </p>
                </div>

                <div class="site-footer-col flex flex-col gap-6">
                    <p class="site-footer-heading text-lg font-medium text-left">{{ __('Quick Links') }}</p>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Home') }}</p>
                    </a>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Our Services') }}</p>
                    </a>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('How It Works') }}</p>
                    </a>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Partner Vendors') }}</p>
                    </a>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Pricing') }}</p>
                    </a>
                </div>


                <div class="site-footer-col flex flex-col gap-6">
                    <p class="site-footer-heading text-lg font-medium text-left">{{ __('Support') }}</p>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Help Center') }}</p>
                    </a>
                    <a href="{{ route('web.terms') }}" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Terms & Conditions') }}</p>
                    </a>
                    <a href="{{ route('web.privacy') }}" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Privacy Policy') }}</p>
                    </a>
                    <a href="#" class="site-footer-link flex justify-start items-center gap-1">
                        <p class="text-base text-left">{{ __('Contact Us') }}</p>
                    </a>
                </div>



                <div class="site-footer-col">
                    <div class="flex flex-col gap-6">
                        <p class="site-footer-heading self-stretch justify-center text-lg font-semibold leading-relaxed">
                            {{ __('Contact Us') }}</p>
                        <div class="self-stretch inline-flex justify-start items-start gap-2 cursor-pointer">
                            <span class="site-footer-icon">
                            <img src="{{ asset('website/assets/icons/phone.svg') }}" alt=""
                                class="w-4 h-4 mt-1.5">
                            </span>
                            <p class="site-footer-contact flex-1 text-base text-left">
                                {{ $value['contact_us']['phone_number'] ?? '+8801937203743' }}
                            </p>
                        </div>
                        <div class="self-stretch inline-flex justify-start items-start gap-2 cursor-pointer">
                            <span class="site-footer-icon">
                            <img src="{{ asset('website/assets/icons/map-pin.svg') }}" alt=""
                                class="w-4 h-4 mt-1.5">
                            </span>
                            <p class="site-footer-contact flex-1 text-base text-left">
                                {{ $value['contact_us']['address'] ?? '123 CleanStreet, CleanCity, CC 12345' }}
                            </p>
                        </div>
                    </div>



                    <div class="mt-[30px] space-y-6">
                        <p class="site-footer-heading text-base font-medium text-left">
                            {{ __('Follow Us') }}
                        </p>

                        <div class="flex items-center gap-3">
                            @foreach ($value['follow_us'] ?? [] as $follow)
                                <a href="{{ $follow['link'] }}" target="_blank" rel="noopener" class="group site-social">
                                    <div
                                        class="site-social-btn h-14 w-14 rounded-xl
                        flex items-center justify-center
                        transition-all duration-200">
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


            <div class="relative z-0 pb-[30px] pt-[50px] site-footer-art">

                <img src="{{ isset($value['footer_background']) ? Storage::url($value['footer_background']) : asset('website/assets/images/footer/laundry.svg') }}"
                    alt="" style="opacity:.9; filter:saturate(1.05);">
            </div>


            <div
                class="site-bottombar flex flex-col sm:flex-row items-center justify-between py-6 border-t border-white/10 relative z-10">
                <p class="text-xs sm:text-sm font-normal">
                    {{ $value['footer_left_side_text'] ?? __('Professional dry cleaning services you can trust.') }}
                </p>
                <p class="text-xs sm:text-sm font-normal">â“’{{ date('Y') }}
                    {{ config('app.name', 'Elite Cleaning') }} {{ $value['footer_right_side_text'] ?? __('All Rights Reserved.') }}
                </p>
            </div>




            <!-- glows -->
            <div class="site-glow w-[400px] h-[400px] absolute -top-[70%] left-[10%] rounded-full">
            </div>
            <div class="site-glow site-glow-2 w-[400px] h-[400px] absolute -bottom-[50%] left-[30%] rounded-full">
            </div>

        </div>

    </div>
</footer>
