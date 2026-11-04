<section style="background:linear-gradient(135deg,#028090,#00A896);position:relative;overflow:hidden;">
    <div aria-hidden="true" style="position:absolute;top:-70px;inset-inline-start:10%;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.12);"></div>
    <div aria-hidden="true" style="position:absolute;bottom:-90px;inset-inline-end:8%;width:280px;height:280px;border-radius:50%;background:rgba(8,55,68,.18);"></div>
    <div
        class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0" style="position:relative;z-index:1;">
        <div class="flex-1">
            <div style="display:flex;justify-content:center;margin-bottom:10px;" class="lg:justify-start">
                <span style="display:inline-flex;align-items:center;background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;">{{ __('Get Started') }}</span>
            </div>
            <p class="text-[32px] font-semibold text-center lg:text-left text-white" style="font-family:Cairo,sans-serif;">
                {{ __('Ready to Get Started?') }}
            </p>
            <p class="text-lg text-center lg:text-left" style="color:#EAF6F8;">
                {{ __('Book your first order and experience hassle-free dry cleaning service') }}
            </p>
        </div>
        <div class="flex flex-col md:flex-row justify-center items-center gap-4">
            <button class="btn_solid_white_lg" style="background:#fff;color:#028090;border-radius:14px;border:1px solid #E2E8F0;box-shadow:0 6px 14px rgba(11,30,46,.12);">
                <a href="{{ route('web.service') }}">
                    <p>{{ __('Book Now') }}</p>
                </a>


                <img src="{{ asset('website/assets/icons/arrow-left-green.svg') }}" alt="">
            </button>
        </div>
    </div>
</section>
