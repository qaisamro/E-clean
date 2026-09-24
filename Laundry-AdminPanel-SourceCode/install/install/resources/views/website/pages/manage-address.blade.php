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


    <script src="../tailwind.config.js"></script>

    <!-- custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/manage-address.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/icons/favicon.png">

</head>

<body>



    <!-- manage-address-modal -->
    <section id="role" class="modal_container">

        <!-- backdrop -->
        <div onclick="toggleModal('role')" class="modal_backdrop"></div>

        <!-- modal content -->
        <div class="modal_content">
            <form action="#" class="rs-manage-addresses-form">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold">Add New Address</h3>
                    <button type="button" onclick="toggleModal('role')"
                        class="transition-transform duration-300 hover:rotate-90">
                        <img src="../assets/icons/close.svg" alt="">
                    </button>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">Label</label>
                    <input type="text" placeholder="Example : Home, Office ..."
                        class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">Street Address</label>
                    <input type="text" placeholder="123 Lovely Road, Apt 6B"
                        class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">City</label>
                        <input type="text" placeholder="New York"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium">State</label>
                        <input type="text" placeholder="NY"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium">ZIP Code</label>
                    <input type="text" placeholder="10003"
                        class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-[30px]">
                    <label class="text-neutral-700 text-base font-medium">Phone Number</label>
                    <input type="text" placeholder="+1 (555) 545-5421"
                        class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <button
                    class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white h-[48px] text-center leading-[48px] w-[100%] rounded-xl">
                    Save Address
                </button>
            </form>
        </div>
    </section>

    <!-- navbar -->
    <nav class="bg-white sticky top-0 py-4 md:py-6 z-40 shadow-md">
        <section class="max-w-2lg mx-auto px-4 xl:px-0 flex items-center justify-between">
            <a href="#" class="w-auto h-6 md:w-auto md:h-8 inline-block">
                <img src="../assets/logo/logo-green.png" alt="" class="h-full w-full">

            </a>


            <div class=" items-center justify-center gap-6 hidden lg:flex">
                <a class="menu_link menu_link_active" href="#">Home</a>
                <a class="menu_link" href="#Services">Services</a>
                <a class="menu_link" href="#Services">Nearest Store</a>
                <a class="menu_link" href="#FAQ">FAQ</a>
                <a class="menu_link" href="#Contact">Contact</a>
            </div>



            <div class="hidden lg:flex justify-center items-center gap-4">
                <button class="btn_solid">
                    <p>Sign In </p>

                    <img src="../assets/icons/arrow-left.svg" alt="">
                </button>

                <button class="btn_outline">
                    <p>Sign Up</p>
                </button>
            </div>


            <!-- sidebar opening button  -->
            <button
                class="h-10 w-10 border border-primary2-600 rounded p-1 flex lg:hidden flex-col justify-around items-center  "
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-7 text-primary2-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" class="text-primary2-600" />
                </svg>

            </button>
        </section>
    </nav>


    <!-- Sidebar -->
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
                    <a href="#" class="text-lg hover:text-blue-400 cursor-pointer">Home</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="text-lg hover:text-blue-400 cursor-pointer">Services</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="text-lg hover:text-blue-400 cursor-pointer">Nearest Store</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="text-lg hover:text-blue-400 cursor-pointer">FAQ</a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="text-lg hover:text-blue-400 cursor-pointer">Contact</a>
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
                    Order Details
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">Dashboard / </a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">My Orders / </a>
                    <a href="#" class="text-base md:text-lg text-white font-normal leading-[100%]">Order #2847</a>
                </div>
            </div>
        </section>

        <!-- manage addresses area -->
        <section class="rs-manage-addresses-section pt-[60px] pb-[80px] px-4 xl:px-0">
            <div class="rs-manage-addresses-area max-w-2lg mx-auto">
                <button onclick="toggleModal('role')"
                    class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white mb-[30px] h-[48px] text-center leading-[48px] w-[190px] sm:w-[290px] rounded-xl">
                    <i class="fa-solid fa-plus mr-[8px]"></i>
                    Add New Address
                </button>
                <div
                    class="rs-manage-addresses-box bg-[white] rounded-xl p-[24px] mb-[24px] border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                    <div
                        class="rs-manage-addresses-content flex gap-[20px] border-b-[1.5px] border-neutral-200 mb-[20px]">
                        <div
                            class="rs-manage-addresses-icon flex-none w-[48px] h-[48px] sm:w-[56px] sm:h-[56px] flex bg-mint-50 rounded-lg">
                            <img src="../assets/icons/home-icon.svg" alt=""
                                class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                        </div>
                        <div class="rs-manage-addresses-info">
                            <div class="rs-manage-addresses-info-top flex text-center gap-[12px] mb-[12px]">
                                <a href="javascript:void(0)"
                                    class="text-neutral-900 text-base leading-[140%] font-medium">Home</a>
                                <span
                                    class="text-mint-600 rounded-[25px] border-[1.5px] border-mint-200 flex gap-[4px] justify-center bg-mint-50 h-[24px] w-[78px] leading-[24px] text-xs font-normal">
                                    <img class="h-[10px] w-[10px] mt-auto mb-auto" src="../assets/icons/star.svg"
                                        alt="">
                                    Default
                                </span>
                            </div>
                            <a href="javascript:void(0)"
                                class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                <img src="../assets/icons/location.svg" alt="">
                                123 Main Street, Apt 4B New York, NY 10001
                            </a>
                            <a href="tel:+1(555)123-4567"
                                class="leading-[140%] text-sm mb-[24px] font-normal flex gap-[6px] text-neutral-500">
                                <img src="../assets/icons/call.svg" alt="" srcset="">
                                +1 (555) 123-4567
                            </a>
                        </div>
                    </div>
                    <div class="rs-manage-addresses-box-bottom-area">
                        <div class="rs-manage-addresses-edit-btn">
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                <img class="h-[12px] w-[12px] mt-auto mb-auto" src="../assets/icons/edit-btn.svg"
                                    alt="">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
                <div
                    class="rs-manage-addresses-box bg-[white] rounded-xl p-[24px] mb-[24px] border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                    <div
                        class="rs-manage-addresses-content flex gap-[20px] border-b-[1.5px] border-neutral-200 mb-[20px]">
                        <div
                            class="rs-manage-addresses-icon flex-none w-[48px] h-[48px] sm:w-[56px] sm:h-[56px] flex bg-mint-50 rounded-lg">
                            <img src="../assets/icons/location-green.svg" alt=""
                                class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                        </div>
                        <div class="rs-manage-addresses-info">
                            <div class="rs-manage-addresses-info-top flex text-center gap-[12px] mb-[12px]">
                                <a href="javascript:void(0)"
                                    class="text-neutral-900 text-base leading-[140%] font-medium">Parent's House</a>
                            </div>
                            <a href="javascript:void(0)"
                                class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                <img src="../assets/icons/location.svg" alt="">
                                789 Family Lane Brooklyn, NY 11201
                            </a>
                            <a href="tel:+1(555)123-4567"
                                class="leading-[140%] text-sm mb-[24px] font-normal flex gap-[6px] text-neutral-500">
                                <img src="../assets/icons/call.svg" alt="" srcset="">
                                +1 (555) 123-4567
                            </a>
                        </div>
                    </div>
                    <div class="rs-manage-addresses-box-bottom-area">
                        <div class="rs-manage-addresses-btn flex flex-wrap gap-[15px] md:flex-nowrap">
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[124px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                Set as Default
                            </button>
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                <img class="h-[12px] w-[12px] mt-auto mb-auto" src="../assets/icons/edit-btn.svg"
                                    alt="">
                                Edit
                            </button>
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-danger-600 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-danger-600 transition-all duration-300">
                                <img class="h-[12px] w-[12px] mt-auto mb-auto" src="../assets/icons/trash-red.svg"
                                    alt="">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div
                    class="rs-manage-addresses-box bg-[white] rounded-xl p-[24px] mb-[24px] border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                    <div
                        class="rs-manage-addresses-content flex gap-[20px] border-b-[1.5px] border-neutral-200 mb-[20px]">
                        <div
                            class="rs-manage-addresses-icon flex-none w-[48px] h-[48px] sm:w-[56px] sm:h-[56px] flex bg-mint-50 rounded-lg">
                            <img src="../assets/icons/city-icon.svg" alt=""
                                class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                        </div>
                        <div class="rs-manage-addresses-info">
                            <div class="rs-manage-addresses-info-top flex text-center gap-[12px] mb-[12px]">
                                <a href="javascript:void(0)"
                                    class="text-neutral-900 text-base leading-[140%] font-medium">Office</a>
                            </div>
                            <a href="javascript:void(0)"
                                class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                <img src="../assets/icons/location.svg" alt="">
                                Riverview Golden Street, Apt 8B New York, NY 10064
                            </a>
                            <a href="tel:+1(555)123-4567"
                                class="leading-[140%] text-sm mb-[24px] font-normal flex gap-[6px] text-neutral-500">
                                <img src="../assets/icons/call.svg" alt="" srcset="">
                                +1 (555) 123-4567
                            </a>
                        </div>
                    </div>
                    <div class="rs-manage-addresses-box-bottom-area">
                        <div class="rs-manage-addresses-btn flex flex-wrap gap-[15px] md:flex-nowrap">
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[124px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                Set as Default
                            </button>
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                <img class="h-[12px] w-[12px] mt-auto mb-auto" src="../assets/icons/edit-btn.svg"
                                    alt="">
                                Edit
                            </button>
                            <button
                                class="leading-[40px] text-xs font-medium flex gap-[5px] text-danger-600 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-danger-600 transition-all duration-300">
                                <img class="h-[12px] w-[12px] mt-auto mb-auto" src="../assets/icons/trash-red.svg"
                                    alt="">
                                Delete
                            </button>
                        </div>
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

                        <img src="../assets/icons/arrow-left-green.svg" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p>Become a Partner</p>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- footer section  -->
    <footer class=" bg-no-repeat bg-cover" style="background-image: url('../assets/images/footer/footer-bg.png');">

        <!-- top footer  -->
        <div class="relative overflow-hidden">
            <div class="max-w-2lg mx-auto pt-[60px] relative z-10 px-4 xl-1:px-0">


                <!-- links section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 lg:gap-[65px] relative z-10">
                    <!-- logo section -->
                    <div class="space-y-[30px]">
                        <img src="../assets/logo/logo-white.png" alt=""
                            class=" w-auto h-14 md:w-auto md:h-[51.58px] object-contain">
                        <p class="text-base text-left text-gray-300 font-normal ">
                            Elevate Your Business with Innovative<br />Web, App, and Software Solutions. Partner for
                            Excellence
                            in Tech.</p>
                    </div>

                    <div class="flex flex-col gap-6">
                        <p class="text-lg font-medium text-left text-neutral-50">Quick Links</p>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Home</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Our Services</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">How It Works</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Partner Vendors</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Pricing</p>
                        </a>
                    </div>


                    <div class="flex flex-col gap-6">
                        <p class="text-lg font-medium text-left text-neutral-50">Support</p>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Help Center</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Terms & Conditions</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Privacy Policy</p>
                        </a>
                        <a href="#" class="flex justify-start items-center gap-1">
                            <p class="text-base text-left text-neutral-400">Contact Us</p>
                        </a>
                    </div>



                    <div>
                        <div class="flex flex-col gap-6">
                            <p class="self-stretch justify-center text-white text-lg font-semibold  leading-relaxed">
                                Contact Us</p>
                            <div class="self-stretch inline-flex justify-start items-start gap-2 cursor-pointer">

                                <img src="../assets/icons/phone.svg" alt="" class="w-4 h-4 mt-1.5">
                                <p class="flex-1 text-base text-left text-neutral-300">
                                    +8801937203743</p>
                            </div>
                            <div class="self-stretch inline-flex justify-start items-start gap-2 cursor-pointer">

                                <img src="../assets/icons/map-pin.svg" alt="" class="w-4 h-4 mt-1.5">
                                <p class="flex-1 text-base text-left text-neutral-300">
                                    123 LaundryStreet,Clean
                                    City, CC 12345</p>
                            </div>
                        </div>



                        <div class="mt-[30px] space-y-6">
                            <p class="text-base font-medium text-left text-gray-50">
                                Follow Us
                            </p>


                            <div class="flex justify-start items-center gap-[10px]">
                                <button
                                    class="h-12 w-12 relative rounded-xl border border-white/[0.06] flex items-center justify-center transition-all duration-200 hover:bg-mint-600">
                                    <img src="../assets/icons/facebook.svg" alt="">
                                </button>
                                <button
                                    class="h-12 w-12 relative rounded-xl border border-white/[0.06] flex items-center justify-center transition-all duration-200 hover:bg-mint-600">
                                    <img src="../assets/icons/twitter.svg" alt="">

                                </button>
                                <button
                                    class="h-12 w-12 relative rounded-xl border border-white/[0.06] flex items-center justify-center transition-all duration-200 hover:bg-mint-600">
                                    <img src="../assets/icons/youtube.svg" alt="">

                                </button>
                                <button
                                    class="h-12 w-12 relative rounded-xl border border-white/[0.06] flex items-center justify-center transition-all duration-200 hover:bg-mint-600">
                                    <img src="../assets/icons/pinterest.svg" alt="">

                                </button>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="relative z-0 pb-[30px] pt-[50px]">
                    <img src="../assets/images/footer/laundry.svg" alt="">
                </div>


                <div
                    class="flex flex-col sm:flex-row items-center justify-between py-6 border-t border-white/10 relative z-10">
                    <p class="text-xs sm:text-sm text-white/40 font-normal">Professional laundry services you can trust.
                    </p>
                    <p class="text-xs sm:text-sm text-white/40 font-normal">ⓒ2025 Laundry. All Rights Reserved.</p>
                </div>




                <!-- glows -->
                <div class="w-[400px] h-[400px] absolute -top-[70%] left-[10%] bg-mint-600 rounded-full blur-[150px]">
                </div>
                <div
                    class="w-[400px] h-[400px] absolute -bottom-[50%] left-[30%] bg-mint-600 rounded-full blur-[150px]">
                </div>

            </div>

        </div>
    </footer>


    <script src="../js/sidebar.js"></script>
    <script src="../js/modal.js"></script>
</body>

</html>
