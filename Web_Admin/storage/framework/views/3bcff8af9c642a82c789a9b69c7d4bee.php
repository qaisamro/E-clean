<!DOCTYPE html>
<?php
    if (!isset($webSettings)) {
        $webSettings = app(\App\Repositories\WebsiteSettingsRepository::class)->index();
    }
?>
<html lang="<?php echo e(app()->getLocale()); ?>" <?php if(app()->getLocale() === 'ar'): ?> dir="rtl" <?php endif; ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(config('app.name', 'Elite Cleaning')); ?></title>
    <!-- Inter font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === 'ar'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
            rel="stylesheet">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- accordion cdn  -->


    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="<?php echo e(asset('website/tailwind.config.js')); ?>"></script>


    <!-- custom CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('website/css/style.css?v=2')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('website/css/home.css?v=2')); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === 'ar'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('website/css/rtl.css')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php
        $websetting = App\Models\WebSetting::first();
    ?>
    <link rel="shortcut icon" type="image/x-icon"
        href="<?php echo e($websetting?->websiteFaviconPath ?? asset('website/assets/icons/favicon.png')); ?>">

</head>

<body>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!request()->routeIs('web.contact')): ?>
        <?php echo $__env->make('website.layouts.includes.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!request()->routeIs('web.contact')): ?>
        <?php echo $__env->make('website.layouts.includes.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script src="<?php echo e(asset('website/js/accordion.js')); ?>"></script>
    <script src="<?php echo e(asset('website/js/sidebar.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\home\master.blade.php ENDPATH**/ ?>