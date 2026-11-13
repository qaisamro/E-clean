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
    <section class="section_container max-w-2lg mx-auto px-4 xl-1:px-0" style="background:#FAF7F2;">
        <header class="heading_section" style="text-align:center;max-width:640px;margin-inline:auto;">
            <span style="display:inline-flex;align-items:center;background:#EAF6F8;color:#028090;border:1px solid #E2E8F0;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;">{{ __('Premium Services') }}</span>
            <p class="text-3xl md:text-4xl font-bold" style="color:#0B1E2E;font-family:Cairo,sans-serif;margin-top:12px;">
                {{ $firstPart }}
                <span style="color:#028090;">{{ $highlightedPart }}</span>
            </p>
            <p class="text-base md:text-lg mt-2" style="color:#5B6B7A;">
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
                  bg-white shadow-sm" style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;box-shadow:0 6px 14px rgba(11,30,46,.06);">

                    {{-- Icon --}}
                    <div class="icon_labels" style="background:#EAF6F8;border-radius:14px;width:52px;height:52px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;">
                        <img src="{{ asset($service?->thumbnailPath ?? '') }}" alt="{{ $service->name }}">
                    </div>

                    {{-- Content --}}
                    <div class="space-y-[15px] my-[15px]">
                        <p class="text-lg font-semibold" style="color:#0B1E2E;font-family:Cairo,sans-serif;">
                            {{ $service->name }}
                        </p>

                        <p class="text-sm" style="color:#5B6B7A;">
                            {{ Str::limit($service->description, 90) }}
                        </p>

                        {{-- Features --}}
                        @if (!empty($service->features) && is_array($service->features))
                            <ul class="list-disc list-inside space-y-[10px]">
                                @foreach ($service->features as $feature)
                                    <li class="text-xs text-left marker:text-mint-500" style="color:#5B6B7A;">
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="border-t pt-5 mt-auto" style="border-color:#E2E8F0;">
                        <div class="w-full">
                            <p class="text-[10px] font-medium" style="color:#5B6B7A;">
                                {{ __('Starts From') }}
                            </p>
                            <p class="text-base font-semibold" style="color:#028090;">
                                @if ($service->lowestProduct)
                                      {{ currencyPosition($service->lowestProduct->final_price) }} {{ __(' / Item') }}
                                @else
                                    {{ __('Price on request') }}
                                @endif

                            </p>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </section>
@endif
