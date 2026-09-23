<nav class="bg-white sticky top-0 py-4 md:py-6 z-40">
    <section class="max-w-2lg mx-auto px-4 xl-1:px-0 flex items-center justify-between">

        <a href="#" class="w-auto h-6 md:w-auto md:h-8 inline-block">
            <img src="{{ $websetting->websiteLogoPath ?? asset('website/assets/logo/logo-green.png') }}" alt=""
                class="h-full w-full">
        </a>

        <div class=" items-center justify-center gap-6 hidden lg:flex">
            <a class="menu_link {{ request()->routeIs('web.home') ? 'menu_link_active' : '' }}" href="/">{{ __('Home') }}</a>
            <a class="menu_link {{ request()->routeIs('web.service') ? 'menu_link_active' : '' }}"
                href="{{ route('web.service') }}">{{ __('Services') }}</a>
            <a class="menu_link {{ request()->routeIs('web.faq') ? 'menu_link_active' : '' }}"
                href="{{ route('web.faq') }}">{{ __('FAQ') }}</a>
            <a class="menu_link {{ request()->routeIs('web.contact') ? 'menu_link_active' : '' }}"
                href="{{ route('web.contact') }}">{{ __('Contact') }}</a>
            {{-- @auth
                <a class="menu_link {{ request()->routeIs('web.settings') ? 'menu_link_active' : '' }}"
                    href="{{ route('web.settings') }}">{{ __('Settings') }}</a>
            @endauth --}}
        </div>



        <div class="hidden lg:flex justify-cente items-center gap-4">
            <!-- Language Switcher -->
            <details class="relative group">
                <summary
                    class="list-none flex cursor-pointer items-center gap-2 rounded-full border border-neutral-200 px-3 py-2 text-sm font-medium text-neutral-700 transition hover:border-mint-500 hover:bg-mint-50">
                    <i class="fa-solid fa-globe"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </summary>
                <div
                    class="absolute right-0 top-[calc(100%+8px)] w-40 overflow-hidden rounded-xl border border-neutral-100 bg-white shadow-lg">
                    <a href="{{ route('change.local', 'ln=ar') }}"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm transition hover:bg-mint-50 {{ app()->getLocale() === 'ar' ? 'font-bold text-mint-700' : 'text-neutral-600' }}">العربية</a>
                    <a href="{{ route('change.local', 'ln=en') }}"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm transition hover:bg-mint-50 {{ app()->getLocale() === 'en' ? 'font-bold text-mint-700' : 'text-neutral-600' }}">English</a>
                </div>
            </details>
           @guest
            <!-- Sign In Button -->
            <a href="{{ route('web.showLogin') }}" class="btn_solid" style="text-decoration: none; display: inline-flex; align-items: center;">
                {{ __('Sign In') }}
                <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="" style="margin-left: 8px;">
            </a>

            <!-- Sign Up Button -->
            <a href="{{ route('web.showRegister') }}" class="btn_outline" style="text-decoration: none; display: inline-flex; align-items: center;">
                {{ __('Sign Up') }}
            </a>
        @endguest


            @auth
                <details class="relative group">
                    <summary
                        class="list-none flex h-12 w-12 cursor-pointer items-center justify-center rounded-full border border-neutral-200 bg-neutral-50 shadow-sm transition hover:border-mint-500 hover:bg-mint-50">
                        <span class="sr-only">Open user menu</span>

                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ auth()->user()->profile_photo_path }}" alt="{{ auth()->user()->name }}"
                                class="h-full w-full rounded-full object-cover">
                        @else
                            <span class="text-sm font-semibold text-neutral-700">
                                {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                    </summary>

                    <div
                        class="absolute right-0 top-[calc(100%+12px)] w-52 overflow-hidden rounded-2xl border border-neutral-100 bg-white shadow-xl">
                        <div class="border-b border-neutral-100 px-4 py-3">
                            <p class="text-sm font-semibold text-neutral-800">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="p-2">
                            <a href="{{ route('web.overview') }}"
                                class="flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-700 transition hover:bg-mint-50 hover:text-mint-700">
                                {{ __('Dashboard') }}
                            </a>

                            <a href="{{ route('web.settings') }}"
                                class="flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-700 transition hover:bg-mint-50 hover:text-mint-700">
                                {{ __('Profile') }}
                            </a>

                            <form method="POST" action="{{ route('web.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </details>
            @endauth
        </div>


        <!-- sidebar opening button  -->
        <button
            class="h-10 w-10 border border-primary2-600 rounded p-1 flex lg:hidden flex-col justify-around items-center  "
            onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-7 text-primary2-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                    class="text-primary2-600" />
            </svg>
        </button>
    </section>
</nav>

