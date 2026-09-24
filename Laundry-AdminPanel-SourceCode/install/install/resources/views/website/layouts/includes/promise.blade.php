@php
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
    class="section_container max-w-2lg mx-auto px-4 xl-1:px-0   grid grid-cols-1 md:grid-cols-2 relative overflow-hidden">
    <div class="relative z-10 flex justify-center md:justify-end items-center order-2 md:order-1">
        <img src="{{ $promiseImgUrl }}" alt="">
    </div>
    <div
        class="flex flex-col justify-center items-center md:items-start relative z-10 md:mt-14 order-1 md:order-2 pt-4 md:pt-0">
        <div class="space-y-[10px]">
            <p class="text-2xl lg:text-[32px] text-start font-semibold text-white">
                Your Laundry, Our Promise <br class="hidden md:block">of <span
                    class="text-mint-600 font-playfair italic">Perfection</span>
            </p>
            <p class="text-base lg:text-xl text-left text-neutral-50">
                We’re dedicated to quality, hygiene, And <br class="hidden md:block">
                On-time delivery — always.
            </p>
        </div>
        <div class="flex flex-col md:flex-row justify-start items-center gap-4 mt-4 md:mt-10">
            <button class="btn_solid">
                <a href="{{ route('web.service') }}">
                    <p>Book Your Order</p>
                </a>
                <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
            </button>

            <button class="btn_outline">
                <p>Learn More</p>
            </button>
        </div>
    </div>


    <div class="absolute bottom-0 right-0 w-full h-full md:h-[80%] z-0 bg-mint-900 md:rounded-3xl bg-no-repeat bg-cover bg-center"
        style="background-image: url('./assets/images/common/bg.png');">

    </div>
</section>
