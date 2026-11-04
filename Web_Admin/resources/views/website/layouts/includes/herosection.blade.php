@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $headerSetting = $webSettings->firstWhere('key', 'header');
    $header = $headerSetting ? $headerSetting->value : null;
@endphp
@php
    $title = html_entity_decode(strip_tags($header['title'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Normalize spaces (replace non-breaking spaces with normal spaces)
    $title = str_replace("\xC2\xA0", ' ', $title);

    if (preg_match('/^(.*?)(\band\b)(.*)$/i', $title, $matches)) {
        $beforeAnd = trim($matches[1]);
        $andWord = $matches[2];
        $afterAnd = trim($matches[3]);
    } else {
        $beforeAnd = $title;
        $andWord = '';
        $afterAnd = '';
    }

    // Get header image URL
    $headerImg = $header['header_img'] ?? null;
    $headerImgUrl = $headerImg
        ? (str_starts_with($headerImg, 'assets/')
            ? asset($headerImg)
            : Storage::url($headerImg))
        : asset('website/assets/images/herosection/heroimg.png');
    $trustedImages = $header['trusted_client_image_group'] ?? [];
    $heroImg1 = isset($trustedImages[0]['img'])
        ? Storage::url($trustedImages[0]['img'])
        : asset('website/assets/images/herosection/1.png');
    $heroImg2 = isset($trustedImages[1]['img'])
        ? Storage::url($trustedImages[1]['img'])
        : asset('website/assets/images/herosection/2.png');
@endphp



<section style="background:#FAF7F2;position:relative;overflow:hidden;">
    <div aria-hidden="true" style="position:absolute;top:-120px;inset-inline-start:-120px;width:340px;height:340px;border-radius:50%;background:#EAF6F8;opacity:.9;"></div>
    <div aria-hidden="true" style="position:absolute;bottom:-160px;inset-inline-end:-100px;width:420px;height:420px;border-radius:50%;background:#EAF6F8;opacity:.7;"></div>
    <div class="grid grid-cols-1 lg:grid-cols-2 max-w-2lg mx-auto gap-[50px] lg:gap-[100px] px-4 xl-1:px-0" style="position:relative;z-index:1;padding-top:24px;padding-bottom:24px;">
        <div class="flex flex-col justify-center order-2 md:order-1">

            <div class="space-y-3 md:space-y-5">

                <div class="flex justify-center lg:justify-start">
                    <div class="flex justify-center items-center gap-1 w-fit px-2 p-2 rounded-[25px]" style="background:#EAF6F8;border:1.5px solid #E2E8F0;border-radius:999px;padding:8px 14px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                        {{-- <img src="asset('website/assets/icons/star.svg')" alt=""> --}}
                        <img src="{{ asset('website/assets/icons/star.svg') }}" alt="">
                        <p class="text-[10px] sm:text-xs font-medium text-left" style="color:#028090;">
                            {{ __('Trusted by 50,000+ Happy Customers') }}
                        </p>
                    </div>
                </div>

                <p class="text-2xl md:text-5xl font-bold text-center lg:text-left" style="color:#0B1E2E;font-family:Cairo,sans-serif;line-height:1.2;">
                    {{ $beforeAnd ?: __('Professional Dry Clean & Care') }} <br>
                    {{ __('And') }}
                    <span class="font-playfair italic" style="color:#028090;">
                        {{ $afterAnd ?: '' }}
                    </span>
                </p>
                <p class="text-lg md:text-2xl text-center lg:text-left" style="color:#5B6B7A;">
                    {{ $header['description'] ??
                        __('Experience hassle-free dry cleaning service with free pickup and delivery. Book in seconds, track in real-time, and enjoy fresh, clean clothes.') }}
                </p>
            </div>
            <div class="space-y-6 mt-4 md:mt-9">
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start items-center gap-4">
                    <button class="btn_solid" style="background:linear-gradient(135deg,#028090,#00A896);color:#fff;border-radius:14px;border:none;box-shadow:0 6px 14px rgba(11,30,46,.12);">
                        <a href="{{ route('web.service') }}">
                            <p>{{ __('Book Now') }}</p>
                        </a>

                        <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
                    </button>
                </div>

                <div class="flex flex-col md:flex-row justify-center lg:justify-start items-start md:items-center gap-2 md:gap-5">
                    <div class="flex justify-start items-center gap-2" style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:8px 12px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                        <img src="{{ asset('website/assets/icons/calendar.svg') }}" alt="" class="w-4 h-4">
                        <p class="text-sm md:text-base text-left" style="color:#5B6B7A;">{{ __('Same-day service') }}</p>
                    </div>
                    <div class="flex justify-start items-center gap-2" style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:8px 12px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                        <img src="{{ asset('website/assets/icons/pickup.svg') }}" alt="" class="w-4 h-4">
                        <p class="text-sm md:text-base text-left" style="color:#5B6B7A;">{{ __('Free pickup') }}</p>
                    </div>
                    <div class="flex justify-start items-center gap-2" style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:8px 12px;box-shadow:0 6px 14px rgba(11,30,46,.06);">
                        <img src="{{ asset('website/assets/icons/map-pin.svg') }}" alt="" class="w-4 h-4">
                        <p class="text-sm md:text-base text-left" style="color:#5B6B7A;">{{ __('500+ vendors') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex justify-center lg:justify-end order-1 md:order-2">
            <div style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:12px;">
                <img src="{{ $headerImgUrl }}" alt="" style="border-radius:14px;">
            </div>


            <img src="{{ $heroImg1 ?? asset('website/assets/images/herosection/1.png') }}" alt=""
                class="hidden 2xl:block absolute left-0 bottom-0 -translate-y-10" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:6px;">
            <img src="{{ $heroImg2 ?? asset('website/assets/images/herosection/2.png') }}" alt=""
                class="hidden 2xl:block absolute right-0 top-0 2xl:translate-y-52 translate-x-14" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:6px;">
        </div>
    </div>
</section>
