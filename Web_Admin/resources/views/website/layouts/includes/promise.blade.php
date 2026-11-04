@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    use Illuminate\Support\Facades\Storage;

    $promise = $webSettings->firstWhere('key', 'our_promise');
    $promiseValue = $promise?->value ?? [];

    $promiseImg = $promiseValue['our_promise_background'] ?? null;
    $promiseImgUrl = $promiseImg
        ? (str_starts_with($promiseImg, 'assets/')
            ? asset($promiseImg)
            : Storage::url($promiseImg))
        : asset('website/assets/images/perfection/bg2.png');
@endphp

<section
    class="section_container max-w-2lg mx-auto px-4 xl-1:px-0 grid grid-cols-1 md:grid-cols-2 relative overflow-hidden" style="background:#FAF7F2;">
    <div class="relative z-10 flex justify-center md:justify-end items-center order-2 md:order-1">
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:10px;width:100%;max-width:520px;">
            <img src="{{ $promiseImgUrl }}" alt="" style="border-radius:14px;width:100%;">
        </div>
    </div>
    <div
        class="flex flex-col justify-center items-center md:items-start relative z-10 md:mt-14 order-1 md:order-2 pt-4 md:pt-0">
        <div class="space-y-[10px]" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:28px;width:100%;">
            <span style="display:inline-flex;align-items:center;background:#EAF6F8;color:#028090;border:1px solid #E2E8F0;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;">{{ __('Our Promise') }}</span>
            <p class="text-2xl lg:text-[32px] text-start font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;margin-top:10px;">
                {{ __('Your Care, Our Promise') }} <br class="hidden md:block">{{ __('of') }} <span
                    class="font-playfair italic" style="color:#028090;">{{ __('Perfection') }}</span>
            </p>
            <p class="text-base lg:text-xl text-left" style="color:#5B6B7A;">
                {{ __('We’re dedicated to quality, hygiene, And') }} <br class="hidden md:block">
                {{ __('On-time delivery — always.') }}
            </p>
            <div class="flex flex-col md:flex-row justify-start items-center gap-4 mt-4 md:mt-10">
                <button class="btn_solid" style="background:linear-gradient(135deg,#028090,#00A896);color:#fff;border-radius:14px;border:none;box-shadow:0 6px 14px rgba(11,30,46,.12);">
                    <a href="{{ route('web.service') }}">
                        <p>{{ __('Book Your Order') }}</p>
                    </a>
                    <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
                </button>

                <button class="btn_outline" style="border-radius:14px;background:#fff;border:1.5px solid #E2E8F0;color:#028090;">
                    <p>{{ __('Learn More') }}</p>
                </button>
            </div>
        </div>
    </div>


    <div class="absolute bottom-0 right-0 w-full h-full md:h-[80%] z-0 bg-mint-900 md:rounded-3xl bg-no-repeat bg-cover bg-center"
        style="background-image: url('./assets/images/common/bg.png');background-color:#083744;border-radius:20px;opacity:.06;">

    </div>
</section>
