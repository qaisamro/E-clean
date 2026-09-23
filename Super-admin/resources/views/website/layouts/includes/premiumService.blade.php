@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $premiumServiceSetting = $webSettings->firstWhere('key', 'premium_services');
    $premiumService = $premiumServiceSetting ? $premiumServiceSetting->value : null;

    // Get title from settings or use default
    $titleText = strip_tags($premiumService['title'] ?? __('Our Premium Services'));
    if (empty($titleText)) {
        $titleText = __('Our Premium Services');
    }

    // Get subtitle from settings or use default
    $subTitleText =
        $premiumService['sub_title'] ?? __('Professional care for all your dry cleaning needs with competitive pricing');

    // Split title into words to highlight the last word
    $words = preg_split('/\s+/', $titleText);
    $firstPart = implode(' ', array_slice($words, 0, -1));
    $highlightedPart = end($words);

    // If services not passed or not a collection of Service models, fetch them
    $isServiceCollection =
        isset($services) && $services->isNotEmpty() && $services->first() instanceof \App\Models\Service;
    if (!$isServiceCollection) {
        $services = \App\Models\Service::with('lowestProduct')->where('is_active', true)->limit(8)->get();
    }
@endphp
@if (isset($services) && $services->count() > 0)
    <section class="section_container max-w-2lg mx-auto px-4 xl-1:px-0">
        <header class="heading_section">
            <p class="text-3xl md:text-4xl font-bold text-gray-900">
                {{ $firstPart }}
                <span class="text-mint-600">{{ $highlightedPart }}</span>
            </p>
            <p class="text-base md:text-lg text-gray-500 mt-2">
                {{ $subTitleText }}
            </p>
        </header>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($services as $service)
                <div
                    class="flex flex-col p-4 rounded-3xl transition-all duration-200
                  outline outline-transparent outline-1
                  hover:outline-offset-2 hover:outline-mint-600
                  hover:shadow-xl hover:shadow-mint-100
                  bg-white shadow-sm">

                    {{-- Icon --}}
                    <div class="icon_labels">
                        <img src="{{ asset($service?->thumbnailPath ?? '') }}" alt="{{ $service->name }}">
                    </div>

                    {{-- Content --}}
                    <div class="space-y-[15px] my-[15px]">
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $service->name }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ Str::limit($service->description, 90) }}
                        </p>

                        {{-- Features --}}
                        @if (!empty($service->features) && is_array($service->features))
                            <ul class="list-disc list-inside space-y-[10px]">
                                @foreach ($service->features as $feature)
                                    <li class="text-xs text-left text-gray-500 marker:text-mint-500">
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-between border-t pt-5 border-gray-100 mt-auto">
                        <div class="w-full">
                            <p class="text-[10px] font-medium text-gray-700">
                                {{ __('Starts From') }}
                            </p>
                            <p class="text-base font-semibold text-mint-600">
                                @if ($service->lowestProduct)
                                      {{ currencyPosition($service->lowestProduct->final_price) }} {{ __(' / Item') }}
                                @else
                                    {{ __('Price on request') }}
                                @endif

                            </p>
                        </div>

                        <a href="{{ route('web.cart', ['service_id' => $service->id]) }}"
                            class="w-full flex justify-center items-center gap-2 px-3 py-2 rounded-lg
             transition-all duration-150 border-[1.5px] border-gray-100
             group hover:bg-gradient-to-t from-mint-200 to-mint-700">

                            <span class="text-xs font-semibold text-mint-600 group-hover:text-white transition">
                                {{ __('Book Service') }}
                            </span>

                            <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}"
                                class="w-[10px] h-[10px] group-hover:hidden transition">

                            <img src="{{ asset('website/assets/icons/arrow-left.svg') }}"
                                class="w-[10px] h-[10px] hidden group-hover:block transition">
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </section>
@endif
