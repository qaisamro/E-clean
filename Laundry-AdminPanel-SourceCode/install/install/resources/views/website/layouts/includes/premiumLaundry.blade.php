 <section
     class="section_container max-w-2lg mx-auto px-4 xl-1:px-0 py-10  xl-1:rounded-3xl bg-gradient-to-tr from-mint-900 to-black relative overflow-hidden">
     <!-- backdrop -->
     <div class="absolute top-0 right-0 w-full h-full bg-no-repeat bg-cover bg-center opacity-50 z-0"
         style="background-image: url('./assets/images/common/bg.png');"></div>
     <header class="relative z-10 space-y-[10px]">
         <p class="text-2xl md:text-[32px] text-center text-white">
             Ready to <span class="text-mint-600 font-playfair italic">Experience Premium Laundry </span>
             Services?
         </p>
         <p class=" text-sm md:text-base text-center text-neutral-50">
             Join thousands of satisfied customers who trust us with their clothes every day.
         </p>
     </header>
     <div class="relative z-10 flex flex-col sm:flex-row justify-center items-center gap-4">
         <button class="btn_solid">
             <a href="{{ route('web.service') }}">
                 <p>Get Started Now</p>
             </a>

             <img src="{{ asset('website/assets/icons/arrow-left.svg') }}" alt="">
         </button>

         <button class="btn_outline">
             <p>Learn More</p>
         </button>
     </div>
 </section>
