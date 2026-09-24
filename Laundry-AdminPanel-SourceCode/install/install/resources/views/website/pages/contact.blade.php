@php
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
            class="rs-breadcrumb-area bg-[#1A7058] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    Contact Us
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="{{ route('web.home') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">Home / </a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">Contact </a>
                </div>
            </div>
        </section>

        <!-- manage addresses area -->
        <section class="max-w-2lg mx-auto pt-[60px] pb-[80px] px-4 xl:px-0 grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-4 flex flex-col gap-4">
                <!-- card 1 -->
                <div class="flex flex-col p-4 rounded-3xl transition-all duration-200 shadow-sm gap-[15px] h-fit bg-white">
                    <div>
                        <div class="h-14 w-14 flex justify-center items-center bg-mint-50 rounded">
                            <img src="{{ asset('website/assets/icons/phone.svg') }}" alt=""
                                class="transition-all duration-200 w-6 h-6">
                        </div>
                    </div>
                    <div class="flex flex-col justify-start items-start self-stretch relative gap-[5px]">
                        <p class="text-lg font-semibold text-left text-gray-700">
                            Phone
                        </p>
                        <p class="text-sm text-left text-gray-500">Mon-Fri from 8am to 6pm</p>
                        <p class="text-base text-left text-mint-600"> {{ $contact['phone_number'] ?? '+1 (555) 123-4567' }}
                        </p>
                    </div>
                </div>



                <!-- card 2 -->
                <div class="flex flex-col p-4 rounded-3xl transition-all duration-200 shadow-sm gap-[15px] h-fit bg-white">
                    <div>
                        <div class="h-14 w-14 flex justify-center items-center bg-mint-50 rounded">
                            <img src="{{ asset('website/assets/icons/envelop.svg') }}" alt=""
                                class="transition-all duration-200 w-6 h-6">
                        </div>
                    </div>
                    <div class="flex flex-col justify-start items-start self-stretch relative gap-[5px]">
                        <p class="text-lg font-semibold text-left text-gray-700">
                            Email
                        </p>
                        <p class="text-sm text-left text-gray-500">Our team is here to help</p>
                        <p class="text-base text-left text-mint-600">{{ $contact['email'] ?? 'support@laundry.com' }}
                        </p>
                    </div>
                </div>


                <!-- card 3 -->
                <div class="flex flex-col p-4 rounded-3xl transition-all duration-200 shadow-sm gap-[15px] h-fit bg-white">
                    <div>
                        <div class="h-14 w-14 flex justify-center items-center bg-mint-50 rounded">
                            <img src="{{ asset('website/assets/icons/map-pin.svg') }}" alt=""
                                class="transition-all duration-200 w-6 h-6">
                        </div>
                    </div>
                    <div class="flex flex-col justify-start items-start self-stretch relative gap-[5px]">
                        <p class="text-lg font-semibold text-left text-gray-700">
                            Office
                        </p>
                        <p class="text-sm text-left text-gray-500">
                            {{ $contact['address'] ?? '123 Business Avenue Suite 100, New York, NY 10001' }}
                        </p>
                    </div>
                </div>


                <!-- card 4 -->
                <div class="flex flex-col p-4 rounded-3xl transition-all duration-200 shadow-sm gap-[15px] h-fit bg-white">
                    <div>
                        <div class="h-14 w-14 flex justify-center items-center bg-mint-50 rounded">
                            <img src="{{ asset('website') }}/assets/icons/clock.svg" alt=""
                                class="transition-all duration-200 w-6 h-6">
                        </div>
                    </div>
                    <div class="flex flex-col justify-start items-start self-stretch relative gap-[5px]">
                        <p class="text-lg font-semibold text-left text-gray-700">
                            Business Hours
                        </p>
                        <p class="text-sm text-left text-gray-500">Monday - Friday: 8am - 6pm <br>Saturday: 9am - 4pm
                            <br>Sunday: Closed
                        </p>
                    </div>
                </div>


            </div>
            <div class="col-span-12 md:col-span-8 space-y-6">
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
                        <h3 class="text-neutral-700 text-lg font-semibold">Send Us A Message</h3>
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
                            <p id="message-min" class="text-xs text-red-500 hidden">Minimum 10 characters required</p>
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


                <div class="bg-mint-50 w-full h-fit overflow-hidden rounded-3xl p-5 md:p-10">
                    <div class="space-y-[10px]">
                        <p class="text-lg md:text-2xl font-semibold text-left text-gray-700">
                            Looking for quick answers?
                        </p>

                        <p class="text-base md:text-lg text-left text-gray-500">
                            Checkout our FAQ page for answers to common questions.
                        </p>
                    </div>
                    <div class="mt-6">
                        <button class="btn_outline !bg-white">
                            <a href="{{ route('web.faq') }}" class="flex justify-center items-center gap-2">
                                <p>Visit FAQ</p>
                            </a>
                        </button>
                    </div>
                </div>
            </div>
        </section>


        <!-- get started -->
        <section class="bg-mint-600 ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        Ready to Get Started?
                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        Book your first order and experience hassle-free laundry service
                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <p>Book Now</p>

                        <img src="{{ asset('website') }}/assets/icons/arrow-left-green.svg" alt="">
                    </button>



                    <button class="btn_outline_white_lg">

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
                        <p>Become a Partner</p>
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection
