 <section
     class="section_container max-w-2lg mx-auto px-4 xl-1:px-0 py-10 xl-1:rounded-3xl relative overflow-hidden" style="background:linear-gradient(135deg,#083744 0%,#156172 55%,#028090 100%);border-radius:20px;border:1px solid #E2E8F0;box-shadow:0 6px 14px rgba(11,30,46,.06);">
     <!-- backdrop -->
     <div class="absolute top-0 right-0 w-full h-full bg-no-repeat bg-cover bg-center opacity-50 z-0"
         style="background-image: url('./assets/images/common/bg.png');"></div>
     <div aria-hidden="true" style="position:absolute;top:-80px;inset-inline-end:-60px;width:260px;height:260px;border-radius:50%;background:rgba(0,168,150,.25);"></div>
     <div aria-hidden="true" style="position:absolute;bottom:-100px;inset-inline-start:-40px;width:300px;height:300px;border-radius:50%;background:rgba(234,246,248,.12);"></div>
     <header class="relative z-10 space-y-[10px]" style="text-align:center;max-width:640px;margin-inline:auto;">
         <span style="display:inline-flex;align-items:center;background:rgba(255,255,255,.14);color:#fff;border:1px solid rgba(255,255,255,.25);border-radius:999px;padding:6px 14px;font-size:12px;font-weight:700;"><?php echo e(__('Premium Dry Cleaning')); ?></span>
         <p class="text-2xl md:text-[32px] text-center text-white" style="font-family:Cairo,sans-serif;font-weight:800;">
             <?php echo e(__('Ready to')); ?> <span class="font-playfair italic" style="color:#fff;text-decoration:underline;text-decoration-color:#00A896;text-underline-offset:6px;"><?php echo e(__('Experience Premium Dry Cleaning')); ?> </span>
             <?php echo e(__('Services?')); ?>

         </p>
         <p class=" text-sm md:text-base text-center" style="color:#EAF6F8;">
             <?php echo e(__('Join thousands of satisfied customers who trust us with their clothes every day.')); ?>

         </p>
     </header>
     <div class="relative z-10 flex flex-col sm:flex-row justify-center items-center gap-4" style="margin-top:20px;">
         <button class="btn_solid" style="background:#fff;color:#028090;border-radius:14px;border:1px solid #E2E8F0;box-shadow:0 6px 14px rgba(11,30,46,.12);">
             <a href="<?php echo e(route('web.service')); ?>">
                 <p><?php echo e(__('Get Started Now')); ?></p>
             </a>

             <img src="<?php echo e(asset('website/assets/icons/arrow-left.svg')); ?>" alt="" style="filter:hue-rotate(120deg);">
         </button>

         <button class="btn_outline" style="border-radius:14px;border:1.5px solid rgba(255,255,255,.5);color:#fff;background:transparent;">
             <p><?php echo e(__('Learn More')); ?></p>
         </button>
     </div>
 </section>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\layouts\includes\premiumLaundry.blade.php ENDPATH**/ ?>