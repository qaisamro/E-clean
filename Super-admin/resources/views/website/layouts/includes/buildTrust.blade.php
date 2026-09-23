@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $buildTrust = $webSettings->firstWhere('key', 'build_on_trust');
    $buildTrustValue = $buildTrust?->value ?? [];
@endphp

<section class="section_container max-w-2lg mx-auto px-4 xl-1:px-0">
    <header class="heading_section">
        <p>
        <h2 class="text-2xl xl:text-4xl font-semibold text-neutral-900">
            {!! !empty($buildTrustValue['title'])
                ? strip_tags($buildTrustValue['title'], '<span>')
                : __('Build on Trust and') . ' <span>' . __('Excellence') . '</span>' !!}
        </h2>
        </p>
        <p>
            {{ !empty($buildTrustValue['sub_title'])
                ? $buildTrustValue['sub_title']
                : __('Our commitment to quality and customer satisfaction has made us the preferred choice.') }}
        </p>
    </header>
    <div class="grid grid-cols-2 md:grid-cols-3 xl-1:grid-cols-4 gap-6">

        @foreach ($buildTrustValue['sample'] ?? [] as $item)
            <div class="flex items-center flex-col justify-start">

                <div
                    class="bg-gradient-to-tr from-mint-600 to-mint-700 p-4 sm:p-5 rounded-2xl shadow-lg shadow-mint-200">

                    <img class="h-5 w-5 sm:h-8 sm:w-8"
                        src="{{ $item['icon_url'] ?? asset('website/assets/icons/varified.svg') }}" alt="">

                </div>

                <p class="text-sm sm:text-base font-semibold text-center text-neutral-800 mb-[10px] mt-[24px]">
                    {{ $item['title'] ?? '' }}
                </p>

                <p class="text-xs sm:text-sm text-center text-neutral-500">
                    {{ $item['description'] ?? '' }}
                </p>

            </div>
        @endforeach

    </div>

</section>
