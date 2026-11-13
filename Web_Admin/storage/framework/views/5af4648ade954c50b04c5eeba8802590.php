<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('website.layouts.includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="min-h-screen  ">
        <?php echo $__env->make('website.layouts.includes.herosection', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('website.layouts.includes.premiumService', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('website.layouts.includes.premiumLaundry', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- how it works -->
        <?php echo $__env->make('website.layouts.includes.howWork', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- top rated stores - معلّق حسب الطلب: التحكم فقط عبر السوبر أدمن -->
        

        <!-- Build on trust -->
        <?php echo $__env->make('website.layouts.includes.buildTrust', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        <!-- promise of perfection -->
        <?php echo $__env->make('website.layouts.includes.promise', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        <section class="bg-cover bg-no-repeat relative py-[50px] md:py-[100px]"
            style="background-image: url('./assets/images/stores-network/bg.png');">

            <div class="absolute top-0 right-0 w-full h-full z-0 bg-[#028090]/5"></div>

            <!-- Join Our Stores Network -->
            <?php echo $__env->make('website.layouts.includes.storeNetwork', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!--  Take Laundry With You -->
            <?php echo $__env->make('website.layouts.includes.takeLaundry', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </section>


        <!-- get started -->
        <?php echo $__env->make('website.layouts.includes.getStarted', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>




    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.home.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\layouts\app.blade.php ENDPATH**/ ?>