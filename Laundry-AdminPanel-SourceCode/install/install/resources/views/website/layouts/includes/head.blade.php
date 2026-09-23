   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>{{ config('app.name', 'Elite Cleaning') }}</title>
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
   <link rel="stylesheet" href="{{ asset('website/css/home.css') }}">
   @if (app()->getLocale() === 'ar')
       <link rel="stylesheet" href="{{ asset('website/css/rtl.css') }}">
   @endif
   @php
       $websetting = App\Models\WebSetting::first();
   @endphp
   <link rel="shortcut icon" type="image/x-icon"
       href="{{ $websetting?->websiteFaviconPath ?? asset('website/assets/icons/favicon.png') }}">
