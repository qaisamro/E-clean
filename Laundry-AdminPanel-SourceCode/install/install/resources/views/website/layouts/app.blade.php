@extends('website.home.master')

@section('content')
    @include('website.layouts.includes.sidebar')
    <main class="min-h-screen  ">
        @include('website.layouts.includes.herosection')
        @include('website.layouts.includes.premiumService')
        @include('website.layouts.includes.premiumLaundry')

        <!-- how it works -->
        @include('website.layouts.includes.howWork')

        <!-- top rated stores -->
        {{-- @include('website.layouts.includes.topStore') --}}

        <!-- Build on trust -->
        @include('website.layouts.includes.buildTrust')


        <!-- promise of perfection -->
        @include('website.layouts.includes.promise')


        <section class="bg-cover bg-no-repeat relative py-[50px] md:py-[100px]"
            style="background-image: url('./assets/images/stores-network/bg.png');">

            <div class="absolute top-0 right-0 w-full h-full z-0 bg-[#32d3a0]/5"></div>

            <!-- Join Our Stores Network -->
            @include('website.layouts.includes.storeNetwork')

            <!--  Take Laundry With You -->
            @include('website.layouts.includes.takeLaundry')

        </section>


        <!-- get started -->
        @include('website.layouts.includes.getStarted')




    </main>
@endsection
