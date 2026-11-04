@php
    $webSettings = $webSettings ?? app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    $footer = $webSettings->get('footer')?->value ?? [];
    $contact = $footer['contact_us'] ?? [];
@endphp


@extends('website.layouts.app')

@section('content')
    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 w-full h-full bg-gradient-to-tl from-mint-500 from-10% via-mint-500 via-30% to-aqua-500 to-90% text-white  z-50"
        id="sidebar">
        <div class="h-full w-full flex justify-center items-center flex-col relative">

            <button class="absolute top-6 right-6 border h-10 w-10 border-white rounded-lg flex justify-center items-center"
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <ul class="space-y-4 flex flex-col items-center">
                <li onclick="toggleSidebar()">
                    <a href="/" class="text-lg hover:text-blue-400 cursor-pointer">Home</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="{{ route('web.service') }}" class="text-lg hover:text-blue-400 cursor-pointer">Services</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="{{ route('web.faq') }}" class="text-lg hover:text-blue-400 cursor-pointer">FAQ</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="{{ route('web.contact') }}" class="text-lg hover:text-blue-400 cursor-pointer">Contact</a>
                </li>
            </ul>
        </div>
    </div>


    <main>
        <!-- breadcrumb -->
        <section
            class="rs-breadcrumb-area bg-[#083744] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    {{ __('Contact Us') }}
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="{{ route('web.home') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Home / ') }}</a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Contact') }}</a>
                </div>
            </div>
        </section>

        <!-- manage addresses area -->
        <section class="max-w-2lg mx-auto pt-[60px] pb-[80px] px-4 xl:px-0">
            <a href="tel:0592925567" dir="ltr"
                class="mx-auto mb-8 flex w-fit items-center gap-3 rounded-2xl bg-mint-600 px-8 py-4 text-white shadow-sm transition hover:bg-mint-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
                <span class="text-xl font-bold tracking-wide">0592925567</span>
            </a>
            <div class="mx-auto w-full max-w-3xl">

                @if (session('errors'))
                    <div class="p-4 rounded-lg bg-red-50 border border-red-200">
                        <p class="text-red-800 font-semibold">{{ session('errors') }}</p>
                    </div>
                @endif
                @if (session('success'))
                    <div class="p-4 rounded-lg bg-green-50 border border-green-200">
                        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('web.contact.submit') }}" method="POST"
                    class="rounded-3xl p-3 sm:p-6 bg-white shadow-sm" novalidate>
                    @csrf
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-neutral-700 text-lg font-semibold">{{ __('Send Us A Message') }}</h3>
                    </div>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="text-base font-medium text-left text-neutral-700">
                                Your Name
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required minlength="2" maxlength="255"
                                placeholder="John Doe" value="{{ old('name') }}"
                                class="w-full h-[40px] mt-2.5 border p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 transition-all
                                {{ $errors->has('name') ? 'border-red-500 focus:ring-red-500' : 'border-neutral-100' }}" />
                            @error('name')
                                <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="text-base font-medium text-left text-neutral-700">
                                Email Address
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required maxlength="255"
                                placeholder="john.doe@email.com" value="{{ old('email') }}"
                                class="w-full h-[40px] mt-2.5 border p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 transition-all
                                {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-neutral-100' }}" />
                            @error('email')
                                <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone Number Field -->
                    <div class="mb-6">
                        <label for="phone_number" class="text-base font-medium text-left text-neutral-700">
                            Phone Number
                            <span class="text-neutral-500 text-sm">(Optional)</span>
                        </label>
                        <input type="tel" id="phone_number" name="phone_number" maxlength="20"
                            placeholder="+1 (555) 123-4567" value="{{ old('phone_number') }}"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 transition-all
                            {{ $errors->has('phone_number') ? 'border-red-500 focus:ring-red-500' : 'border-neutral-100' }}" />
                        @error('phone_number')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Subject Field -->
                    <div class="mb-6">
                        <label for="subject" class="text-base font-medium text-left text-neutral-700">
                            Subject
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="subject" name="subject" required maxlength="255"
                            placeholder="How can we help you?" value="{{ old('subject') }}"
                            class="w-full h-[40px] mt-2.5 border p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 transition-all
                            {{ $errors->has('subject') ? 'border-red-500 focus:ring-red-500' : 'border-neutral-100' }}" />
                        <div class="flex justify-between items-center mt-1">
                            <div></div>
                            <p id="subject-count" class="text-xs text-neutral-500">0 / 255</p>
                        </div>
                        @error('subject')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Message Field -->
                    <div class="mb-6">
                        <label for="message" class="text-base font-medium text-left text-neutral-700">
                            Message
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" cols="30" rows="6" required minlength="10" maxlength="2000"
                            placeholder="Please describe your inquiry in detail..."
                            class="w-full mt-2.5 border p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-mint-500 transition-all resize-none
                            {{ $errors->has('message') ? 'border-red-500 focus:ring-red-500' : 'border-neutral-100' }}">{{ old('message') }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p id="message-min" class="text-xs text-red-500 hidden">{{ __('Minimum 10 characters required') }}</p>
                            <p id="message-count" class="text-xs text-neutral-500">0 / 2000</p>
                        </div>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2 mb-6">
                        <input type="checkbox" id="terms" name="terms" required
                            class="w-4 h-4 text-mint-600 rounded focus:ring-mint-500 cursor-pointer" />
                        <label for="terms" class="text-sm text-neutral-700 cursor-pointer">
                            I agree to the terms and conditions
                            <span class="text-red-500">*</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="flex justify-center items-center w-full max-w-[344px] h-12 overflow-hidden px-5 py-3.5 rounded-xl bg-mint-600 hover:bg-mint-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <p class="text-sm font-semibold text-center text-white">
                            Send Message
                        </p>
                    </button>
                </form>
            </div>
        </section>


                
<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const subjectInput = document.getElementById('subject');
                                const messageInput = document.getElementById('message');
                                const subjectCount = document.getElementById('subject-count');
                                const messageCount = document.getElementById('message-count');
                                const messageMinWarning = document.getElementById('message-min');
                                const contactForm = document.querySelector('form');

                                // Character counter for subject
                                if (subjectInput && subjectCount) {
                                    subjectInput.addEventListener('input', function() {
                                        subjectCount.textContent = `${this.value.length} / 255`;
                                    });
                                    // Initialize counter
                                    subjectCount.textContent = `${subjectInput.value.length} / 255`;
                                }

                                // Character counter and validation for message
                                if (messageInput && messageCount) {
                                    messageInput.addEventListener('input', function() {
                                        const length = this.value.length;
                                        messageCount.textContent = `${length} / 2000`;

                                        // Show/hide minimum character warning
                                        if (length < 10 && length > 0) {
                                            messageMinWarning.classList.remove('hidden');
                                        } else {
                                            messageMinWarning.classList.add('hidden');
                                        }
                                    });
                                    // Initialize counter
                                    const initialLength = messageInput.value.length;
                                    messageCount.textContent = `${initialLength} / 2000`;
                                    if (initialLength < 10 && initialLength > 0) {
                                        messageMinWarning.classList.remove('hidden');
                                    }
                                }

                                // Form validation
                                if (contactForm) {
                                    contactForm.addEventListener('submit', function(e) {
                                        // Check terms checkbox
                                        const termsCheckbox = document.getElementById('terms');
                                        if (!termsCheckbox.checked) {
                                            e.preventDefault();
                                            alert('Please agree to the terms and conditions.');
                                            termsCheckbox.focus();
                                            return false;
                                        }

                                        // Validate message length
                                        if (messageInput.value.length < 10) {
                                            e.preventDefault();
                                            messageMinWarning.classList.remove('hidden');
                                            messageInput.focus();
                                            return false;
                                        }

                                        return true;
                                    });
                                }

                                // Real-time validation styling
                                const inputs = document.querySelectorAll(
                                    'input[type="text"], input[type="email"], input[type="tel"], textarea');
                                inputs.forEach(input => {
                                    input.addEventListener('blur', function() {
                                        if (this.hasAttribute('required') && this.value.trim() === '') {
                                            this.classList.add('border-red-500');
                                            this.classList.add('focus:ring-red-500');
                                        } else if (this.classList.contains('border-red-500')) {
                                            this.classList.remove('border-red-500');
                                        }
                                    });

                                    input.addEventListener('input', function() {
                                        if (this.value.trim() !== '') {
                                            this.classList.remove('border-red-500');
                                        }
                                    });
                                });
                            });
                        </script>

    </main>
@endsection
