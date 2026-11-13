<nav class="bg-white sticky top-0 py-4 md:py-6 z-40 site-navbar">
    <section class="max-w-2lg mx-auto px-4 xl-1:px-0 flex items-center justify-between gap-4">

        <a href="#" class="site-logo inline-block shrink-0">
            <img src="<?php echo e($websetting->websiteLogoPath ?? asset('website/assets/logo/logo-green.png')); ?>" alt=""
                class="h-6 md:h-8 w-auto object-contain">
        </a>

        <div class=" items-center justify-center gap-2 hidden lg:flex site-nav-links">
            <a class="menu_link <?php echo e(request()->routeIs('web.home') ? 'menu_link_active' : ''); ?>" href="/"><?php echo e(__('Home')); ?></a>
            <a class="menu_link <?php echo e(request()->routeIs('web.service') ? 'menu_link_active' : ''); ?>"
                href="<?php echo e(route('web.service')); ?>"><?php echo e(__('Services')); ?></a>
            <a class="menu_link <?php echo e(request()->routeIs('web.faq') ? 'menu_link_active' : ''); ?>"
                href="<?php echo e(route('web.faq')); ?>"><?php echo e(__('FAQ')); ?></a>
            <a class="menu_link <?php echo e(request()->routeIs('web.contact') ? 'menu_link_active' : ''); ?>"
                href="<?php echo e(route('web.contact')); ?>"><?php echo e(__('Contact')); ?></a>
            
        </div>



        <div class="hidden lg:flex justify-cente items-center gap-3 site-nav-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <details class="relative group">
                    <summary
                        class="list-none flex h-12 w-12 cursor-pointer items-center justify-center rounded-full border border-neutral-200 bg-neutral-50 shadow-sm transition hover:border-mint-500 hover:bg-mint-50">
                        <span class="sr-only"><?php echo e(__('Open user menu')); ?></span>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->profile_photo_path): ?>
                            <img src="<?php echo e(auth()->user()->profile_photo_path); ?>" alt="<?php echo e(auth()->user()->name); ?>"
                                class="h-full w-full rounded-full object-cover">
                        <?php else: ?>
                            <span class="text-sm font-semibold text-neutral-700">
                                <?php echo e(strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1))); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </summary>

                    <div
                        class="absolute right-0 top-[calc(100%+12px)] w-52 overflow-hidden rounded-2xl border border-neutral-100 bg-white shadow-xl">
                        <div class="border-b border-neutral-100 px-4 py-3">
                            <p class="text-sm font-semibold text-neutral-800"><?php echo e(auth()->user()->name); ?></p>
                            <p class="truncate text-xs text-neutral-500"><?php echo e(auth()->user()->email); ?></p>
                        </div>

                        <div class="p-2">
                            <a href="<?php echo e(route('web.overview')); ?>"
                                class="flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-700 transition hover:bg-mint-50 hover:text-mint-700">
                                <?php echo e(__('Dashboard')); ?>

                            </a>

                            <?php
                                $navProfileUrl = auth()->user()?->hasRole('vendor_admin')
                                    ? route('vendor.settings')
                                    : (auth()->user()?->hasRole('vendor_sub') ? route('profile.index') : route('web.settings'));
                            ?>
                            <a href="<?php echo e($navProfileUrl); ?>"
                                class="flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-700 transition hover:bg-mint-50 hover:text-mint-700">
                                <?php echo e(__('Profile')); ?>

                            </a>

                            <form method="POST" action="<?php echo e(route('web.logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                                    <?php echo e(__('Logout')); ?>

                                </button>
                            </form>
                        </div>
                    </div>
                </details>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>


        <!-- sidebar opening button  -->
        <button
            class="site-menu-btn h-10 w-10 border border-primary2-600 rounded p-1 flex lg:hidden flex-col justify-around items-center"
            onclick="toggleSidebar()" aria-label="<?php echo e(__('Open menu')); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-7 text-primary2-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                    class="text-primary2-600" />
            </svg>
        </button>
    </section>
</nav>

<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\layouts\includes\navbar.blade.php ENDPATH**/ ?>