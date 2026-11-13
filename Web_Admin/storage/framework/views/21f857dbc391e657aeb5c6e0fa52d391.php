<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white ec-sidebar" id="sidenav-main">
    <div class="container-fluid ec-sidebar-inner" style="min-height:0">

        <?php
            $websetting = App\Models\WebSetting::first();
        ?>
        <!-- Brand -->
        <a class="navbar-brand ec-brand" href="<?php echo e(route('root')); ?>">
            <img src="<?php echo e($websetting->websiteLogoPath ?? asset('web/logo.jpg?v=2')); ?>" class="navbar-brand-img"
                alt="Admin Logo">
        </a>

        <!-- User card (top) -->
        <div class="ec-user-top">
            <div class="ec-user-card">
                <div class="ec-avatar">
                    <img src="<?php echo e(auth()->user()->profile_photo_path ?? asset('website/assets/images/common/user.png?v=2')); ?>"
                        alt="" width="50" height="50">
                    <span class="ec-online" title="<?php echo e(__('متصل')); ?>"></span>
                </div>
                <div class="ec-who">
                    <h3 class="name m-0"><?php echo e(auth()->user()->name ?? 'Guest User'); ?></h3>
                    <p class="email m-0" dir="ltr"><?php echo e(auth()->user()->mobile ?? auth()->user()->email ?? ''); ?></p>
                </div>
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerGoldStar"
            aria-controls="navbarTogglerGoldStar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarTogglerGoldStar">
            
            <ul class="navbar-nav">
                <div class="position-absolute top-0 right-0 d-md-none navbarCloseBtn" data-toggle="collapse"
                    data-target="#navbarTogglerGoldStar">
                    <i class="fas fa-angle-left"></i>
                </div>

                <li class="ec-section-label" aria-hidden="true"><span><?php echo e(__('القائمة الرئيسية')); ?></span></li>

                <?php
                    $isDriver = Auth::user() && Auth::user()->hasRole('driver');
                    // Super-Admin-only sections (Customers, Site pages, Stores)
                    // open up for any admin that was granted the matching
                    // permission, so granted permissions behave exactly like
                    // they do for the Super Admin.
                    $ecUser = auth()->user();
                    $ecIsRoot = (bool) optional($ecUser)->hasRole('root');
                    $ecAllows = function ($permission) use ($ecUser, $ecIsRoot) {
                        return $ecIsRoot || (bool) optional($ecUser)->can($permission);
                    };
                    // Child vendor accounts (vendor_sub) share the store scope but
                    // are limited to the capabilities their owner granted them.
                    // The store owner keeps the same visibility as before.
                    $ecIsSub = (bool) optional($ecUser)->hasRole('vendor_sub');
                    $ecIsStoreStaff = (bool) optional($ecUser)->hasRole('vendor_admin') || $ecIsSub;
                    $ecCanForSub = fn ($permission) => !$ecIsSub || (bool) optional($ecUser)->can($permission);
                    $ecCanForSubAny = fn (array $permissions) => !$ecIsSub || collect($permissions)->contains(fn ($p) => optional($ecUser)->can($p));
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isDriver && $ecCanForSub('vendor.dashboard')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('root', 'vendor.dashboard') ? 'active' : ''); ?>" href="<?php echo e($ecIsStoreStaff ? route('vendor.dashboard') : route('root')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('Dashboard')); ?></span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsRoot || optional($ecUser)->hasRole('admin') || optional($ecUser)->hasRole('Admin')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('service.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('service.index')); ?>">
                            <i class="fas fa-tools" aria-hidden="true"></i>
                            <span class="nav-link-text"><?php echo e(__('Services')); ?></span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('vendor.customers.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('vendor.customers.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('vendor.customers.index')); ?>">
                            <span class="nav-link-text">عملاء المتجر</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('vendor.notifications.send')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('vendor.notifications.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('vendor.notifications.index')); ?>">
                            <span class="nav-link-text">إرسال إشعار</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('vendor.settings')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('vendor.settings') ? 'active' : ''); ?>"
                            href="<?php echo e(route('vendor.settings')); ?>">
                            <i class="fas fa-cog" aria-hidden="true"></i>
                            <span class="nav-link-text">إعدادات المتجر</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(optional($ecUser)->hasRole('vendor_admin')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('vendor.subs.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('vendor.subs.index')); ?>">
                            <i class="fas fa-users-cog" aria-hidden="true"></i>
                            <span class="nav-link-text">الحسابات الفرعية</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('order.index')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('order.*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('order.index')); ?>">
                                
                                <span class="nav-link-text"><?php echo e(__('Orders')); ?></span>
                            </a>
                        </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSubAny(['product.index', 'coupon.index'])): ?>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo e(request()->routeIs('service.*', 'product.*', 'coupon.*') ? 'active' : ''); ?>"
                            href="#product_manage" data-toggle="collapse" aria-expanded="false" role="button"
                            aria-controls="navbar-examples">
                            
                            <span class="nav-link-text"><?php echo e(__('Product_Manage')); ?></span>
                        </a>

                        <div class="collapse <?php echo e(request()->routeIs('service.*', 'product.*', 'coupon.*') ? 'show' : ''); ?>"
                            id="product_manage">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product.index')): ?>
                                    <a class="nav-link sub-menu <?php echo e(request()->routeIs('product.*') ? 'active' : ''); ?>"
                                        href="<?php echo e(route('product.index')); ?>">
                                        
                                        <span class="nav-link-text"><?php echo e(__('Products')); ?></span>
                                    </a>
                                <?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('coupon.index')): ?>
                                    <div class="d-flex align-items-center justify-content-between pe-2">
                                        <a class="nav-link sub-menu <?php echo e(request()->routeIs('coupon.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('coupon.index')); ?>">
                                            <span class="nav-link-text">كوبونات متجري</span>
                                        </a>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['coupon.create', 'coupon.store'])): ?>
                                            <a href="<?php echo e(route('coupon.create')); ?>" class="btn btn-sm btn-link p-0 ms-1"
                                                title="إضافة كوبون خاص بمتجري" style="line-height:1">
                                                <i class="fa fa-plus-circle" style="font-size:1.1rem"></i>
                                                <span class="sr-only">إضافة كوبون</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSubAny(['pos.index', 'pos.sales', 'vendor.appointments.edit', 'vendor.invoice-template.edit'])): ?>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo e(request()->routeIs('pos.*', 'vendor.invoice-template.*', 'vendor.appointments.*') ? 'active' : ''); ?>" href="#posManage"
                            data-toggle="collapse" aria-expanded="false" role="button" aria-controls="navbar-examples">
                            
                            <span class="nav-link-text"><?php echo e(__('Pos_Manage')); ?></span>
                        </a>
                        <div class="collapse <?php echo e(request()->routeIs('pos.*', 'vendor.invoice-template.*', 'vendor.appointments.*') ? 'show' : ''); ?>" id="posManage">
                            <ul class="nav nav-sm flex-column">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecCanForSub('pos.index')): ?>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('pos.index') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('pos.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('POS')); ?></span>
                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecCanForSub('pos.sales')): ?>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('pos.sales') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('pos.sales')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Pos_Sales')); ?></span>
                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecCanForSub('vendor.appointments.edit')): ?>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('vendor.appointments.*') ? 'active' : ''); ?>" href="<?php echo e(route('vendor.appointments.edit')); ?>">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i><span class="nav-link-text">إدارة مواعيد الاستلام والتوصيل</span>
                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecCanForSub('vendor.invoice-template.edit')): ?>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('vendor.invoice-template.*') ? 'active' : ''); ?>" href="<?php echo e(route('vendor.invoice-template.edit')); ?>">
                                    <i class="fas fa-file-invoice" aria-hidden="true"></i><span class="nav-link-text">تصميم قالب الفاتورة</span>
                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'driver')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('driver.account') ? 'active' : ''); ?>" href="<?php echo e(route('driver.account')); ?>">

                            <span class="nav-link-text">طلباتي</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('notification.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('notification.index')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('Notifications')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.promotional')): ?>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo e(request()->routeIs('banner.promotional') ? 'active' : ''); ?>"
                            href="<?php echo e(route('banner.promotional')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('App_Banners')); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo e(request()->routeIs('banner.wide') ? 'active' : ''); ?>"
                            href="<?php echo e(route('banner.wide')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('لافتات عريضة (1200 × 318)')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('offer.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo e(request()->routeIs('offer.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('offer.index')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('العروض')); ?></span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$ecIsStoreStaff && $ecAllows('customer.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('customer.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('customer.index')); ?>">
 
                            <span class="nav-link-text"><?php echo e(__('Customer')); ?></span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecIsStoreStaff && $ecCanForSub('driver.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('driver.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('driver.index')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('Drivers')); ?></span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecAllows('web.setting')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('web.setting') ? 'active' : ''); ?>" href="#sitePagesManage"
                            data-toggle="collapse" aria-expanded="false" role="button" aria-controls="navbar-examples">
                            
                            <span class="nav-link-text">الصفحات والسياسات</span>
                        </a>

                        <div class="collapse <?php echo e(request()->routeIs('web.setting') ? 'show' : ''); ?>" id="sitePagesManage">
                            <ul class="nav nav-sm flex-column">
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('web.setting') && request()->get('section', 'terms_conditions') === 'terms_conditions' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('web.setting', ['section' => 'terms_conditions'])); ?>#section-terms">
                                    
                                    <span class="nav-link-text">شروط الخدمة</span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('web.setting') && request()->get('section') === 'privacy_policy' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('web.setting', ['section' => 'privacy_policy'])); ?>#section-privacy">
                                    
                                    <span class="nav-link-text">سياسة الخصوصية</span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('web.setting') && request()->get('section') === 'contact_us' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('web.setting', ['section' => 'contact_us'])); ?>#section-contact">
                                    
                                    <span class="nav-link-text">تواصل معنا</span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('web.setting') && request()->get('section') === 'about_us' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('web.setting', ['section' => 'about_us'])); ?>#section-about">
                                    
                                    <span class="nav-link-text">المزيد عنا</span>
                                </a>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('coupon.admin.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('coupon.admin.index')); ?>">

                            <span class="nav-link-text">كوبونات الخصم</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('integrations.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('integrations.index')); ?>">
                            
                            <span class="nav-link-text">مركز التكاملات</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ecAllows('vendors.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('vendors.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('vendors.index')); ?>">
                            
                            <span class="nav-link-text">إدارة المحلات</span>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?>
                    <li class="nav-item">
                        <?php
                            try {
                                $cjrPendingCount = \Illuminate\Support\Facades\Cache::remember('customer_join_requests.pending_count', 60, fn () => \App\Models\CustomerJoinRequest::pending()->count());
                            } catch (\Throwable $e) {
                                $cjrPendingCount = null;
                            }
                        ?>
                        <a class="nav-link <?php echo e(request()->routeIs('customer-join-requests.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('customer-join-requests.index')); ?>">
                            
                            <span class="nav-link-text">طلبات الانضمام</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cjrPendingCount !== null && $cjrPendingCount > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1 cjr-pending-badge"><?php echo e($cjrPendingCount); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|visitor')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.index')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('Admins')); ?></span>
                        </a>
                    </li>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(false): ?> 
                    <li class="nav-item">
                        <a class="nav-link  <?php echo e(request()->routeIs('setting.*', 'deliveryCost', 'mobileApp', 'socialLink.*', 'stripeKey.*', 'notification.manage', 'fcm.*', 'mail-config.*') ? 'active' : ''); ?>"
                            href="#setting" data-toggle="collapse" aria-expanded="false" role="button"
                            aria-controls="navbar-examples">
                            
                            <span class="nav-link-text"><?php echo e(__('Settings')); ?></span>
                        </a>

                        <div class="collapse <?php echo e(request()->routeIs('setting.*', 'deliveryCost', 'mobileApp', 'socialLink.*', 'profile.*', 'stripeKey.*', 'schedule.*', 'invoiceManage.*', 'areas.*', 'sms-gateway.*', 'notification.manage', 'fcm.*', 'mail-config.*') ? 'show' : ''); ?>"
                            id="setting">
                            <ul class="nav nav-sm flex-column">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('enums.settings'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a class="nav-link sub-menu <?php echo e(url()->full() == config('app.url') . '/settings/' . $index || url()->full() == config('app.url') . '/settings/' . $index . '/edit' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('setting.show', $index)); ?>">

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index == 'privacy-policy'): ?>
                                            
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index == 'trams-of-service'): ?>
                                            
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index == 'contact-us'): ?>
                                            
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index == 'about-us'): ?>
                                            
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="nav-link-text"><?php echo e(__($item)); ?></span>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('deliveryCost') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('deliveryCost')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Delivery_Cost')); ?></span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('mobileApp') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('mobileApp')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Mobile_App_Link')); ?></span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('socialLink.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('socialLink.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Social_Links')); ?></span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(url()->full() == config('app.url') . '/pickup/scheduls' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('schedule.index', 'pickup')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Pickup_Schedules')); ?></span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(url()->full() == config('app.url') . '/delivery/scheduls' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('schedule.index', 'delivery')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Delivery_Schedules')); ?></span>
                                </a>

                                
                                <a href="<?php echo e(route('payment-gateway.index')); ?>"
                                    class="nav-link sub-menu  <?php echo e(request()->routeIs('payment-gateway.index') ? 'active' : ''); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Payment_gateway')); ?></span>
                                </a>
                                
                                
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('sms-gateway.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('sms-gateway.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('SMS_Gateway')); ?></span>
                                </a>
                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('invoiceManage.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('invoiceManage.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Invoice_Manage')); ?></span>
                                </a>

                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('notification.manage') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('notification.manage')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Notify_Manage')); ?></span>
                                </a>

                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('fcm.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('fcm.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('FCM_Config')); ?></span>
                                </a>

                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('mail-config.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('mail-config.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Mail_Config')); ?></span>
                                </a>

                                <a class="nav-link sub-menu <?php echo e(request()->routeIs('areas.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('areas.index')); ?>">
                                    
                                    <span class="nav-link-text"><?php echo e(__('Areas')); ?></span>
                                </a>


                            </ul>
                        </div>
                    </li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('profile.index')): ?>
                    <li class="nav-item">
                        <a class="nav-link sub-menu <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.index')); ?>">
                            
                            <span class="nav-link-text"><?php echo e(__('Profile')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                
                

                <li class="nav-item">
                    <a class="nav-link"
                        onclick="event.preventDefault(); if(confirm('<?php echo e(__('Are you sure you want to logout?')); ?>')){ document.getElementById('logout').submit(); }"
                        href="#">
                        
                        <span class="nav-link-text"><?php echo e(__('Logout')); ?></span>
                    </a>
                    <form id="logout" action="<?php echo e(route('logout')); ?>" method="POST"> <?php echo csrf_field(); ?> </form>
                </li>

            </ul>
        </div>
    </div>

</nav>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\layouts\partials\sidebar.blade.php ENDPATH**/ ?>