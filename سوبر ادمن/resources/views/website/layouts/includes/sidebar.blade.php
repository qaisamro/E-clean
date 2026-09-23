 <div class="sidebar fixed top-0 left-0 w-full h-full bg-gradient-to-tl from-mint-500 from-10% via-mint-500 via-30% to-aqua-500 to-90% text-white  z-50"
        id="sidebar">
        <div class="h-full w-full flex justify-center items-center flex-col relative">

            <button
                class="absolute top-6 right-6 border h-10 w-10 border-white rounded-lg flex justify-center items-center"
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <ul class="space-y-4 flex flex-col items-center">
                <li onclick="toggleSidebar()">
                    <a href="#" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Home') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Services') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Nearest Store') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('FAQ') }}</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="text-lg hover:text-blue-400 cursor-pointer">{{ __('Contact') }}</a>
                </li>
            </ul>

            <div class="mt-6 flex items-center gap-3 text-sm">
                <a href="{{ route('change.local', 'ln=ar') }}"
                    class="px-4 py-2 rounded-full border border-white/60 transition hover:bg-white hover:text-mint-700 {{ app()->getLocale() === 'ar' ? 'bg-white text-mint-700 font-bold' : 'text-white' }}">العربية</a>
                <a href="{{ route('change.local', 'ln=en') }}"
                    class="px-4 py-2 rounded-full border border-white/60 transition hover:bg-white hover:text-mint-700 {{ app()->getLocale() === 'en' ? 'bg-white text-mint-700 font-bold' : 'text-white' }}">English</a>
            </div>
        </div>
    </div>
