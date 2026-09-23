<section class="bg-mint-600 ">
    <div
        class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
        <div class="flex-1">
            <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                {{ __('Ready to Get Started?') }}
            </p>
            <p class="text-lg text-center lg:text-left text-neutral-50">
                {{ __('Book your first order and experience hassle-free dry cleaning service') }}
            </p>
        </div>
        <div class="flex flex-col md:flex-row justify-center items-center gap-4">
            <button class="btn_solid_white_lg">
                <a href="{{ route('web.service') }}">
                    <p>{{ __('Book Now') }}</p>
                </a>


                <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}" alt="">
            </button>
        </div>
    </div>
</section>
