<x-guest-layout :title="config('app.name') . ' | ' . 'تسجيل الدخول'">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" key="status"/>

    <h4 class="mb-2" style="text-align:center">مرحبا بكم في {{ config('app.name') }}!</h4>
    <p class="mb-4" style="text-align:center">يرجى تسجيل الدخول </p>

    <form @submit="formSubmit = true" x-data="{formSubmit:false}" id="formAuthentication" class="mb-3" method="POST"
          action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <x-input-label for="check" class="text-start" :value="'رقم الهاتف'"/>
            <x-text-input id="phone" placeholder="أدخل رقم الهاتف" class="text-start" type="tel" name="phone"
                          :value="old('phone')" required autofocus autocomplete="tel"/>
            <x-input-error key="phone"/>
        </div>
        <div class="mb-3">
            <x-input-label for="password" value="كلمة المرور"/>
            <x-text-input id="password" placeholder="أدخل كلمة المرور" type="password" name="password"
                          required autocomplete="current-password"/>
        </div>
        <div class="mb-3">
            <div class="d-flex gap-2">
                <input class="form-check-input" name="remember" type="checkbox" id="remember-me"/>
                <label for="remember-me">تذكرني</label>
            </div>
        </div>
        <x-primary-button class="d-flex gap-3" ::disabled="formSubmit">
            <span x-show="!formSubmit">تسجيل الدخول</span>
            <i x-show="formSubmit" class='bx bx-loader-circle bx-spin'></i>
        </x-primary-button>
    </form>

    @push('css')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    @push('js')
        @vite('resources/js/app.js')
    @endpush
</x-guest-layout>
