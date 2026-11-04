@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $buildTrust = $webSettings->firstWhere('key', 'build_on_trust');
    $buildTrustValue = $buildTrust?->value ?? [];
@endphp

<section class="section_container max-w-2lg mx-auto px-4 xl-1:px-0" style="background:#FAF7F2;">
    <header class="heading_section" style="text-align:center;max-width:640px;margin-inline:auto;">
        <div style="display:flex;justify-content:center;">
            <span style="display:inline-flex;align-items:center;background:#EAF6F8;color:#028090;border:1px solid #E2E8F0;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;">{{ __('Why Choose Us') }}</span>
        </div>
        <p>
        <h2 class="text-2xl xl:text-4xl font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;margin-top:12px;">
            {!! !empty($buildTrustValue['title'])
                ? strip_tags($buildTrustValue['title'], '<span>')
                : __('Build on Trust and') . ' <span>' . __('Excellence') . '</span>' !!}
        </h2>
        </p>
        <p style="color:#5B6B7A;">
            {{ !empty($buildTrustValue['sub_title'])
                ? $buildTrustValue['sub_title']
                : __('Our commitment to quality and customer satisfaction has made us the preferred choice.') }}
        </p>
    </header>
    <div class="grid grid-cols-2 md:grid-cols-3 xl-1:grid-cols-4 gap-6">

        @foreach ($buildTrustValue['sample'] ?? [] as $item)
            <div class="flex items-center flex-col justify-start" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);padding:24px 16px;">

                <div
                    class="p-4 sm:p-5 rounded-2xl" style="background:#EAF6F8;border-radius:14px;width:64px;height:64px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;">

                    <img class="h-5 w-5 sm:h-8 sm:w-8"
                        src="{{ $item['icon_url'] ?? asset('website/assets/icons/varified.svg') }}" alt="">

                </div>

                <p class="text-sm sm:text-base font-semibold text-center mb-[10px] mt-[24px]" style="color:#0B1E2E;font-family:Cairo,sans-serif;">
                    {{ $item['title'] ?? '' }}
                </p>

                <p class="text-xs sm:text-sm text-center" style="color:#5B6B7A;">
                    {{ $item['description'] ?? '' }}
                </p>

            </div>
        @endforeach

    </div>

</section>
