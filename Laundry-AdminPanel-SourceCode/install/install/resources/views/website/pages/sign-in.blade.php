<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry</title>
    <!-- Inter font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- accordion cdn  -->


    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>


    <script src="{{ asset('website/tailwind.config.js') }}"></script>

    <!-- custom CSS -->
    <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/sign-in.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('website/assets/icons/favicon.png') }}">


</head>

<body class="bg-neutral-50">
    <main>
        <div class="rs-sigin-area max-w-2lg  mx-auto mt-[50px] mb-6 px-4 xl:px-0">
            <div class="col-span-12">
                <div class="grid grid-cols-12 gap-6 md:gap-7">
                    <div class="col-span-12 md:col-span-6">
                        <div
                            class="sigin-left-side bg-mint-600 rounded-3xl p-6
                            lg:py-[56.5px] lg:px-[48px] relative overflow-hidden">
                            <div class="content">
                                <div
                                    class="logo w-[70px] h-[70px] md:w-[86px] md:h-[86px] bg-white rounded-lg md:rounded-2xl text-center flex items-center justify-center mb-4 md:mb-[30px]">
                                    <a href="{{ route('web.home') }}">
                                        <img class="h-content"
                                            src="{{ asset('website/assets/logo/logo-green-sm.png') }}" alt="">
                                    </a>
                                </div>
                                <h2
                                    class="text-xl md:text-[28px] font-semibold leading-[120%] text-neutral-100 mb-[10px]">
                                    Welcome to Laundry
                                </h2>
                                <p class="text-base md:text-lg font-normal leading-[140%] text-neutral-100 mb-[24px]">
                                    Your trusted partner for professional laundry and dry cleaning services
                                </p>
                                <div class="list">
                                    <ul>
                                        <li class="mb-[16px]">
                                            <a href="#"
                                                class="text-sm md:text-base font-medium leading-[130%] text-neutral-100 flex items-center gap-[10px]">
                                                <i
                                                    class="w-9 h-9 flex items-center justify-center bg-[rgba(255,255,255,0.16)] backdrop-blur-md rounded-full">
                                                    <img class="h-content "
                                                        src="{{ asset('website/assets/icons/white-check-box.svg') }}"
                                                        alt="">
                                                </i>
                                                Free pickup and delivery
                                            </a>
                                        </li>
                                        <li class="mb-[16px]">
                                            <a href="#"
                                                class="text-sm md:text-base font-medium leading-[130%] text-neutral-100 flex items-center gap-[10px]">
                                                <i
                                                    class="w-9 h-9 flex items-center justify-center bg-[rgba(255,255,255,0.16)] backdrop-blur-md rounded-full">
                                                    <img class="h-content "
                                                        src="{{ asset('website/assets/icons/white-check-box.svg') }}"
                                                        alt="">
                                                </i>
                                                24-48 hour turnaround
                                            </a>
                                        </li>
                                        <li class="mb-[16px]">
                                            <a href="#"
                                                class="text-sm md:text-base font-medium leading-[130%] text-neutral-100 flex items-center gap-[10px]">
                                                <i
                                                    class="w-9 h-9 flex items-center justify-center bg-[rgba(255,255,255,0.16)] backdrop-blur-md rounded-full">
                                                    <img class="h-content "
                                                        src="{{ asset('website/assets/icons/white-check-box.svg') }}"
                                                        alt="">
                                                </i>
                                                100% satisfaction guarantee
                                            </a>
                                        </li>
                                        <li class="mb-6">
                                            <a href="#"
                                                class="text-sm md:text-base font-medium leading-[130%] text-neutral-100 flex items-center gap-[10px]">
                                                <i
                                                    class="w-9 h-9 flex items-center justify-center bg-[rgba(255,255,255,0.16)] backdrop-blur-md rounded-full">
                                                    <img class="h-content "
                                                        src="{{ asset('website/assets/icons/white-check-box.svg') }}"
                                                        alt="">
                                                </i>
                                                Eco-friendly cleaning
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="box bg-[rgba(255,255,255,0.16)] backdrop-blur-md rounded-2xl p-4 md:p-6">
                                    <div class="log-in-customers flex items-center gap-[10px] mb-4">
                                        <div class="log-in-customers-img flex items-center">
                                            <img class="w-9 h-9 object-cover border border-white rounded-full"
                                                src="{{ asset('website/assets/images/login/login-author-01.png') }}"
                                                alt="">
                                            <img class="w-9 h-9 object-cover border border-white rounded-full"
                                                src="{{ asset('website/assets/images/login/login-author-02.png') }}"
                                                alt="">
                                            <img class="w-9 h-9 object-cover border border-white rounded-full"
                                                src="{{ asset('website/assets/images/login/login-author-03.png') }}"
                                                alt="">
                                        </div>
                                        <div>
                                            <h3
                                                class="text-sm md:text-base font-normal leading-[100%] text-neutral-50 flex items-center mb-[5px]">
                                                Trusted by
                                            </h3>
                                            <h4
                                                class="text-base md:text-lg font-semibold leading-[100%] text-neutral-50 flex items-center">
                                                50,000+ customers
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="log-in-rat flex items-center  ">
                                        <img class="w-[17px] h-[17px]"
                                            src="{{ asset('website/assets/icons/star-gold.svg') }}" alt="">
                                        <img class="w-[17px] h-[17px]"
                                            src="{{ asset('website/assets/icons/star-gold.svg') }}" alt="">
                                        <img class="w-[17px] h-[17px]"
                                            src="{{ asset('website/assets/icons/star-gold.svg') }}" alt="">
                                        <img class="w-[17px] h-[17px]"
                                            src="{{ asset('website/assets/icons/star-gold.svg') }}" alt="">
                                        <img class="w-[17px] h-[17px]"
                                            src="{{ asset('website/assets/icons/star-gold.svg') }}" alt="">
                                        <span
                                            class="text-sm font-medium leading-[140%] text-neutral-50 flex items-center ml-2">
                                            4.9/5 Rating
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <div class="sigin-right-side bg-white  rounded-3xl
                            p-6 relative">
                            {{-- <form action="{{ route('web.login') }}" method="POST" class="content"> --}}
                                <form method="POST" action="{{ url('/login') }}?service_id={{ request('service_id') }}">

                                @csrf

                                @if ($errors->any())
                                    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4">
                                        <ul class="list-disc list-inside text-sm text-red-600">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <h3
                                    class="text-xl md:text-[28px]  font-semibold leading-[120%] text-neutral-500 mb-[10px] text-center">
                                    Welcome Back
                                </h3>
                                <p
                                    class="text-base md:text-lg font-normal leading-[140%] text-neutral-500 mb-[24px] text-center">
                                    Sign in to your account to continue
                                </p>
                                <div
                                    class="login-tab flex items-center justify-between [52px] leading-[40px] p-[6px] bg-mint-50 rounded-[50px] mb-6">
                                    <a class="text-base font-medium leading-[40px] w-[49%] text-center inline-block text-neutral-500 h-[40px] active"
                                        href="{{ route('web.showLogin') }}">
                                        Log In
                                    </a>
                                    <a class="text-base font-medium leading-[40px] w-[49%] text-center inline-block text-neutral-500 h-[40px]"
                                        href="{{ route('web.showRegister') }}">
                                        Register
                                    </a>
                                </div>
                                <div class="input-items mb-4">
                                    <label
                                        class="block text-base md:text-lg font-medium leading-[120%] text-neutral-700 mb-[10px]">Email
                                        Address</label>
                                    <div class="relative">
                                        <img class="absolute"
                                            src="{{ asset('website/assets/icons/envelope-grey.svg') }}"
                                            alt="">
                                        <input class="w-full" name="email" type="email"
                                            placeholder="Enter your email address">
                                    </div>
                                </div>
                                <div class="input-items mb-6">
                                    <label
                                        class="block text-base md:text-lg font-medium leading-[120%] text-neutral-700 mb-[10px]">Password</label>
                                    <div class="relative">
                                        <img class="absolute" src="{{ asset('website/assets/icons/lock.svg') }}"
                                            alt="">
                                        <input class="w-full" name="password" type="password"
                                            placeholder="Enter password">
                                    </div>
                                </div>
                                <a class="text-base font-medium leading-[100%] text-mint-600 text-right block mb-6"
                                    href="#">
                                    Forgot Password?
                                </a>
                                <button type="submit"
                                    class="flex items-center w-full h-[48px] bg-mint-600 text-white justify-center gap-[10px] ml-auto text-base text-mint-600 font-semibold leading-[133%] h-[32px] w-[110px] border-[1.50px] border-neutral-100 rounded-xl mb-4 md:mb-6">
                                    Sign In <img class="filter brightness-0 invert w-[14px] h-[14px]"
                                        src="{{ asset('website/assets/icons/green-right-arrow.svg') }}"
                                        alt="">
                                </button>

                                {{-- <div class="outher-login mb-6">
                                    <span
                                        class="text-base font-normal leading-[125%] text-neutral-500 mb-[24px] text-center block text-center relative">Or
                                        continue with</span>
                                </div> --}}
                                {{-- <div class="outher-login-icon flex gap-3 justify-between mb-6">
                                    <a href="#"
                                        class="text-base font-semibold h-12 w-[49%] leading-[] text-neutral-700 flex items-center gap-2 border rounded-xl border-neutral-200 justify-center">
                                        <img src="{{ asset('website/assets/icons/google-icon.svg') }}"
                                            alt="">
                                        Google
                                    </a>
                                    <a href="#"
                                        class="text-base font-semibold h-12 w-[49%] leading-[] text-neutral-700 flex items-center gap-2 border rounded-xl border-neutral-200 justify-center">
                                        <img src="{{ asset('website/assets/icons/facebook-icon.svg') }}"
                                            alt="">
                                        Facebook
                                    </a>
                                </div> --}}
                                <div class="block text-center">
                                    <span
                                        class="text-base md:text-lg font-normal leading-[140%] text-neutral-500">Don’t
                                        have an account?
                                        <a href="{{ route('web.showRegister') }}"
                                            class="text-base md:text-lg font-semibold leading-[100%] text-mint-600">Sign
                                            Up</a></span>
                                </div>


                                @php
                                    $showDemoUser = filter_var(config('services.demo_mode') ?? env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN)
                                        && filled(config('app.demo_user_email'))
                                        && filled(config('app.demo_user_password'));
                                @endphp

                                @if ($showDemoUser)
                                    <div
                                        class="mb-6 border border-mint-100 bg-mint-50 rounded-2xl p-4 flex items-start justify-between gap-4">

                                        <div>
                                            <h6 class="text-sm font-bold text-mint-700 mb-2">
                                                Demo User Credentials
                                            </h6>

                                            <p class="text-sm text-neutral-700 mb-1">
                                                <span class="font-semibold">Email:</span>
                                                <span id="demoEmail">{{ config('app.demo_user_email') }}</span>
                                            </p>

                                            <p class="text-sm text-neutral-700">
                                                <span class="font-semibold">Password:</span>
                                                <span id="demoPassword">{{ config('app.demo_user_password') }}</span>
                                            </p>
                                        </div>

                                        <button
                                            id="demoUserFillTrigger"
                                            type="button"
                                            class="min-w-[42px] h-[42px] rounded-xl border border-mint-200 bg-white text-mint-600 hover:bg-mint-600 hover:text-white transition duration-300 flex items-center justify-center">

                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                @endif

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
    document.getElementById('demoUserFillTrigger')?.addEventListener('click', function () {

        document.querySelector('input[name="email"]').value =
            document.getElementById('demoEmail').innerText;

        document.querySelector('input[name="password"]').value =
            document.getElementById('demoPassword').innerText;
    });
</script>

</body>

</html>
