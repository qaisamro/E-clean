<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white ec-sidebar" id="sidenav-main">
    <div class="container-fluid ec-sidebar-inner" style="min-height:0">

        @php
            $websetting = App\Models\WebSetting::first();
        @endphp
        <!-- Brand -->
        <a class="navbar-brand ec-brand" href="{{ route('root') }}">
            <img src="{{ $websetting->websiteLogoPath ?? asset('web/logo.jpg?v=2') }}" class="navbar-brand-img"
                alt="Admin Logo">
        </a>

        <!-- User card (top) -->
        <div class="ec-user-top">
            <div class="ec-user-card">
                <div class="ec-avatar">
                    <img src="{{ auth()->user()->profile_photo_path ?? asset('website/assets/images/common/user.png?v=2') }}"
                        alt="" width="50" height="50">
                    <span class="ec-online" title="{{ __('متصل') }}"></span>
                </div>
                <div class="ec-who">
                    <h3 class="name m-0">{{ auth()->user()->name ?? 'Guest User' }}</h3>
                    <p class="email m-0" dir="ltr">{{ auth()->user()->mobile ?? auth()->user()->email ?? '' }}</p>
                </div>
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerGoldStar"
            aria-controls="navbarTogglerGoldStar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="navbarTogglerGoldStar">
            {{-- Main Admin --}}
            <ul class="navbar-nav">
                <div class="position-absolute top-0 right-0 d-md-none navbarCloseBtn" data-toggle="collapse"
                    data-target="#navbarTogglerGoldStar">
                    <i class="fas fa-angle-left"></i>
                </div>

                <li class="ec-section-label" aria-hidden="true"><span>{{ __('القائمة الرئيسية') }}</span></li>

                @php
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
                @endphp
                @if (!$isDriver)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('root', 'vendor.dashboard') ? 'active' : '' }}" href="{{ auth()->user()?->hasRole('vendor_admin') ? route('vendor.dashboard') : route('root') }}">
                            
                            <span class="nav-link-text">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif

                @role('vendor_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.customers.*') ? 'active' : '' }}"
                            href="{{ route('vendor.customers.index') }}">
                            <span class="nav-link-text">عملاء المتجر</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.notifications.*') ? 'active' : '' }}"
                            href="{{ route('vendor.notifications.index') }}">
                            <span class="nav-link-text">إرسال إشعار</span>
                        </a>
                    </li>
                @endrole

                @role('vendor_admin')
                    @can('order.index')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('order.*') ? 'active' : '' }}"
                                href="{{ route('order.index') }}">
                                
                                <span class="nav-link-text">{{ __('Orders') }}</span>
                            </a>
                        </li>
                    @endcan
                @endrole

                @role('vendor_admin')
                    @canany(['product.index', 'coupon.index', 'variant.index', 'service.index'])
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('service.*', 'variant.*', 'product.*', 'coupon.*') ? 'active' : '' }}"
                            href="#product_manage" data-toggle="collapse" aria-expanded="false" role="button"
                            aria-controls="navbar-examples">
                            
                            <span class="nav-link-text">{{ __('Product_Manage') }}</span>
                        </a>

                        <div class="collapse {{ request()->routeIs('service.*', 'variant.*', 'product.*', 'coupon.*') ? 'show' : '' }}"
                            id="product_manage">
                            <ul class="nav nav-sm flex-column">
                                @can('service.index')
                                    <a class="nav-link sub-menu {{ request()->routeIs('service.*') ? 'active' : '' }}"
                                        href="{{ route('service.index') }}">
                                        {{--  --}}
                                        
                                        <span class="nav-link-text">{{ __('Services') }}</span>
                                    </a>
                                @endcan

                                @can('variant.index')
                                    <a class="nav-link sub-menu {{ request()->routeIs('variant.*') ? 'active' : '' }}"
                                        href="{{ route('variant.index') }}">
                                        
                                        <span class="nav-link-text">{{ __('Variants') }}</span>
                                    </a>
                                @endcan
                                @can('product.index')
                                    <a class="nav-link sub-menu {{ request()->routeIs('product.*') ? 'active' : '' }}"
                                        href="{{ route('product.index') }}">
                                        
                                        <span class="nav-link-text">{{ __('Products') }}</span>
                                    </a>
                                @endcan

                                @if (auth()->check() && auth()->user()->hasRole('vendor_admin'))
                                    <div class="d-flex align-items-center justify-content-between pe-2">
                                        <a class="nav-link sub-menu {{ request()->routeIs('coupon.*') ? 'active' : '' }}"
                                            href="{{ route('coupon.index') }}">
                                            <span class="nav-link-text">كوبونات متجري</span>
                                        </a>
                                        @canany(['coupon.create', 'coupon.store'])
                                            <a href="{{ route('coupon.create') }}" class="btn btn-sm btn-link p-0 ms-1"
                                                title="إضافة كوبون خاص بمتجري" style="line-height:1">
                                                <i class="fa fa-plus-circle" style="font-size:1.1rem"></i>
                                                <span class="sr-only">إضافة كوبون</span>
                                            </a>
                                        @endcanany
                                    </div>
                                @endif

                                @can('additional.index')
                                    <a class="nav-link sub-menu {{ request()->routeIs('additional.*') ? 'active' : '' }}"
                                        href="{{ route('additional.index') }}">
                                        
                                        <span class="nav-link-text">{{ __('Additional_Services') }}</span>
                                    </a>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @endrole

                @role('vendor_admin')
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('pos.*', 'vendor.invoice-template.*') ? 'active' : '' }}" href="#posManage"
                            data-toggle="collapse" aria-expanded="false" role="button" aria-controls="navbar-examples">
                            
                            <span class="nav-link-text">{{ __('Pos_Manage') }}</span>
                        </a>
                        <div class="collapse {{ request()->routeIs('pos.*', 'vendor.invoice-template.*') ? 'show' : '' }}" id="posManage">
                            <ul class="nav nav-sm flex-column">
                                <a class="nav-link sub-menu {{ request()->routeIs('pos.index') ? 'active' : '' }}"
                                    href="{{ route('pos.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('POS') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('pos.sales') ? 'active' : '' }}"
                                    href="{{ route('pos.sales') }}">
                                    
                                    <span class="nav-link-text">{{ __('Pos_Sales') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('vendor.invoice-template.*') ? 'active' : '' }}" href="{{ route('vendor.invoice-template.edit') }}">
                                    <i class="fas fa-file-invoice" aria-hidden="true"></i><span class="nav-link-text">تصميم قالب الفاتورة</span>
                                </a>
                            </ul>
                        </div>
                    </li>
                @endrole
                @role('driver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.account') ? 'active' : '' }}" href="{{ route('driver.account') }}">
                            
                            <span class="nav-link-text">طلباتي</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                            
                            <span class="nav-link-text">{{ __('POS') }}</span>
                        </a>
                    </li>
                @endrole

                @can('notification.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('notification.*') ? 'active' : '' }}"
                            href="{{ route('notification.index') }}">
                            
                            <span class="nav-link-text">{{ __('Notifications') }}</span>
                        </a>
                    </li>
                @endcan

                @can('revenue.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('revenue.*') ? 'active' : '' }}"
                            href="{{ route('revenue.index', ['from' => now()->subMonth(1)->format('Y-m-d'), 'to' => now()->addDay(1)->format('Y-m-d')]) }}">
                            
                            <span class="nav-link-text">{{ __('Reports') }}</span>
                        </a>
                    </li>
                @endcan

                @can('banner.promotional')
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('banner.promotional') ? 'active' : '' }}"
                            href="{{ route('banner.promotional') }}">
                            
                            <span class="nav-link-text">{{ __('App_Banners') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('banner.wide') ? 'active' : '' }}"
                            href="{{ route('banner.wide') }}">
                            
                            <span class="nav-link-text">{{ __('لافتات عريضة (1200 × 318)') }}</span>
                        </a>
                    </li>
                @endcan

                @role('vendor_admin')
                    @can('offer.index')
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('offer.*') ? 'active' : '' }}"
                            href="{{ route('offer.index') }}">
                            
                            <span class="nav-link-text">{{ __('العروض') }}</span>
                        </a>
                    </li>
                    @endcan
                @endrole

                @if (!auth()->user()?->hasRole('vendor_admin') && $ecAllows('customer.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}"
                            href="{{ route('customer.index') }}">

                            <span class="nav-link-text">{{ __('Customer') }}</span>
                        </a>
                    </li>
                @endif

                @role('vendor_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.*') ? 'active' : '' }}"
                            href="{{ route('driver.index') }}">
                            
                            <span class="nav-link-text">{{ __('Drivers') }}</span>
                        </a>
                    </li>
                @endrole

                {{-- @can('contact')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">
                            
                            <span class="nav-link-text">{{ __('Contacts') }}</span>
                        </a>
                    </li>
                @endcan --}}
                {{-- صفحات الموقع - Site pages (السوبر أدمن أو من يملك صلاحية web.setting) --}}
                @if ($ecAllows('web.setting'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.setting') ? 'active' : '' }}" href="#sitePagesManage"
                            data-toggle="collapse" aria-expanded="false" role="button" aria-controls="navbar-examples">
                            
                            <span class="nav-link-text">الصفحات والسياسات</span>
                        </a>

                        <div class="collapse {{ request()->routeIs('web.setting') ? 'show' : '' }}" id="sitePagesManage">
                            <ul class="nav nav-sm flex-column">
                                <a class="nav-link sub-menu {{ request()->routeIs('web.setting') && request()->get('section', 'terms_conditions') === 'terms_conditions' ? 'active' : '' }}"
                                    href="{{ route('web.setting', ['section' => 'terms_conditions']) }}#section-terms">
                                    
                                    <span class="nav-link-text">شروط الخدمة</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('web.setting') && request()->get('section') === 'privacy_policy' ? 'active' : '' }}"
                                    href="{{ route('web.setting', ['section' => 'privacy_policy']) }}#section-privacy">
                                    
                                    <span class="nav-link-text">سياسة الخصوصية</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('web.setting') && request()->get('section') === 'contact_us' ? 'active' : '' }}"
                                    href="{{ route('web.setting', ['section' => 'contact_us']) }}#section-contact">
                                    
                                    <span class="nav-link-text">تواصل معنا</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('web.setting') && request()->get('section') === 'about_us' ? 'active' : '' }}"
                                    href="{{ route('web.setting', ['section' => 'about_us']) }}#section-about">
                                    
                                    <span class="nav-link-text">المزيد عنا</span>
                                </a>
                            </ul>
                        </div>
                    </li>
                @endif
                {{-- اعدادات الموقع - Website Setting (معلّق) --}}
                {{-- <a class="nav-link sub-menu {{ request()->is('your-url-slug*') ? 'active' : '' }}"
                    href="{{ route('web.setting') }}">
                    
                    <span class="nav-link-text">{{ __('Website Setting') }}</span>
                </a> --}}
                {{-- الاسئلة الشائعة - Faq (معلّق) --}}
                {{-- <a class="nav-link  {{ request()->routeIs('web.faq.*') ? 'active' : '' }}" href="#faqManage"
                    data-toggle="collapse" aria-expanded="false" role="button" aria-controls="navbar-examples">
                    
                    <span class="nav-link-text">{{ __('Faq') }}</span>
                </a>

                <div class="collapse {{ request()->routeIs('web.faq.*') ? 'show' : '' }}" id="faqManage">
                    <ul class="nav nav-sm flex-column">
                        <a class="nav-link sub-menu {{ request()->routeIs('web.faq.list') ? 'active' : '' }}"
                            href="{{ route('web.faq.list') }}">
                            
                            <span class="nav-link-text">{{ __('Faq List') }}</span>
                        </a>
                        <a class="nav-link sub-menu {{ request()->routeIs('web.faq.category.*') ? 'active' : '' }}"
                            href="{{ route('web.faq.category.index') }}">
                            
                            <span class="nav-link-text">{{ __('Faq Category') }}</span>
                        </a>
                    </ul>
                </div> --}}
                @role('root|admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('coupon.admin.*') ? 'active' : '' }}"
                            href="{{ route('coupon.admin.index') }}">

                            <span class="nav-link-text">كوبونات الخصم</span>
                        </a>
                    </li>
                @endrole
                @role('root|admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('integrations.*') ? 'active' : '' }}"
                            href="{{ route('integrations.index') }}">
                            
                            <span class="nav-link-text">مركز التكاملات</span>
                        </a>
                    </li>
                @endrole
                @if ($ecAllows('vendors.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}"
                            href="{{ route('vendors.index') }}">
                            
                            <span class="nav-link-text">إدارة المحلات</span>
                        </a>
                    </li>
                @endif
                @role('root|admin')
                    <li class="nav-item">
                        @php
                            try {
                                $cjrPendingCount = \Illuminate\Support\Facades\Cache::remember('customer_join_requests.pending_count', 60, fn () => \App\Models\CustomerJoinRequest::pending()->count());
                            } catch (\Throwable $e) {
                                $cjrPendingCount = null;
                            }
                        @endphp
                        <a class="nav-link {{ request()->routeIs('customer-join-requests.*') ? 'active' : '' }}"
                            href="{{ route('customer-join-requests.index') }}">
                            
                            <span class="nav-link-text">طلبات الانضمام</span>
                            @if ($cjrPendingCount !== null && $cjrPendingCount > 0)
                                <span class="badge bg-danger rounded-pill ms-1 cjr-pending-badge">{{ $cjrPendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endrole
                @role('root|visitor')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                            href="{{ route('admin.index') }}">
                            
                            <span class="nav-link-text">{{ __('Admins') }}</span>
                        </a>
                    </li>


                    @if(false) {{-- الاعدادات - Settings (معلّق حسب الطلب) --}}
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('setting.*', 'deliveryCost', 'mobileApp', 'socialLink.*', 'webSetting.*', 'stripeKey.*', 'notification.manage', 'fcm.*', 'mail-config.*') ? 'active' : '' }}"
                            href="#setting" data-toggle="collapse" aria-expanded="false" role="button"
                            aria-controls="navbar-examples">
                            
                            <span class="nav-link-text">{{ __('Settings') }}</span>
                        </a>

                        <div class="collapse {{ request()->routeIs('setting.*', 'deliveryCost', 'mobileApp', 'socialLink.*', 'profile.*', 'webSetting.*', 'stripeKey.*', 'schedule.*', 'invoiceManage.*', 'areas.*', 'sms-gateway.*', 'notification.manage', 'fcm.*', 'mail-config.*') ? 'show' : '' }}"
                            id="setting">
                            <ul class="nav nav-sm flex-column">
                                @foreach (config('enums.settings') as $index => $item)
                                    <a class="nav-link sub-menu {{ url()->full() == config('app.url') . '/settings/' . $index || url()->full() == config('app.url') . '/settings/' . $index . '/edit' ? 'active' : '' }}"
                                        href="{{ route('setting.show', $index) }}">

                                        @if ($index == 'privacy-policy')
                                            
                                        @endif
                                        @if ($index == 'trams-of-service')
                                            
                                        @endif
                                        @if ($index == 'contact-us')
                                            
                                        @endif
                                        @if ($index == 'about-us')
                                            
                                        @endif
                                        <span class="nav-link-text">{{ __($item) }}</span>

                                    </a>
                                @endforeach
                                <a class="nav-link sub-menu {{ request()->routeIs('deliveryCost') ? 'active' : '' }}"
                                    href="{{ route('deliveryCost') }}">
                                    
                                    <span class="nav-link-text">{{ __('Delivery_Cost') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('mobileApp') ? 'active' : '' }}"
                                    href="{{ route('mobileApp') }}">
                                    
                                    <span class="nav-link-text">{{ __('Mobile_App_Link') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('socialLink.*') ? 'active' : '' }}"
                                    href="{{ route('socialLink.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('Social_Links') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ url()->full() == config('app.url') . '/pickup/scheduls' ? 'active' : '' }}"
                                    href="{{ route('schedule.index', 'pickup') }}">
                                    
                                    <span class="nav-link-text">{{ __('Pickup_Schedules') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ url()->full() == config('app.url') . '/delivery/scheduls' ? 'active' : '' }}"
                                    href="{{ route('schedule.index', 'delivery') }}">
                                    
                                    <span class="nav-link-text">{{ __('Delivery_Schedules') }}</span>
                                </a>

                                <a class="nav-link sub-menu {{ request()->routeIs('webSetting.*') ? 'active' : '' }}"
                                    href="{{ route('webSetting.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('Web_Setting') }}</span>
                                </a>
                                {{-- @can('payment-gateway.index') --}}
                                <a href="{{ route('payment-gateway.index') }}"
                                    class="nav-link sub-menu  {{ request()->routeIs('payment-gateway.index') ? 'active' : '' }}">
                                    
                                    <span class="nav-link-text">{{ __('Payment_gateway') }}</span>
                                </a>
                                {{-- @endcan --}}
                                {{-- <a class="nav-link sub-menu {{ request()->routeIs('stripeKey.*') ? 'active' : '' }}"
                                    href="{{ route('stripeKey.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('payment') }}</span>
                                </a> --}}
                                <a class="nav-link sub-menu {{ request()->routeIs('sms-gateway.*') ? 'active' : '' }}"
                                    href="{{ route('sms-gateway.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('SMS_Gateway') }}</span>
                                </a>
                                <a class="nav-link sub-menu {{ request()->routeIs('invoiceManage.*') ? 'active' : '' }}"
                                    href="{{ route('invoiceManage.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('Invoice_Manage') }}</span>
                                </a>

                                <a class="nav-link sub-menu {{ request()->routeIs('notification.manage') ? 'active' : '' }}"
                                    href="{{ route('notification.manage') }}">
                                    
                                    <span class="nav-link-text">{{ __('Notify_Manage') }}</span>
                                </a>

                                <a class="nav-link sub-menu {{ request()->routeIs('fcm.*') ? 'active' : '' }}"
                                    href="{{ route('fcm.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('FCM_Config') }}</span>
                                </a>

                                <a class="nav-link sub-menu {{ request()->routeIs('mail-config.*') ? 'active' : '' }}"
                                    href="{{ route('mail-config.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('Mail_Config') }}</span>
                                </a>

                                <a class="nav-link sub-menu {{ request()->routeIs('areas.*') ? 'active' : '' }}"
                                    href="{{ route('areas.index') }}">
                                    
                                    <span class="nav-link-text">{{ __('Areas') }}</span>
                                </a>


                            </ul>
                        </div>
                    </li>
                    @endif {{-- نهاية تعليق الاعدادات --}}
                @endrole

                @can('profile.index')
                    <li class="nav-item">
                        <a class="nav-link sub-menu {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                            href="{{ route('profile.index') }}">
                            
                            <span class="nav-link-text">{{ __('Profile') }}</span>
                        </a>
                    </li>
                @endcan

                {{-- اللغة - Language (معلّق) --}}
                {{-- <li class="nav-item">
                    <a class="nav-link sub-menu {{ request()->routeIs('language.*') ? 'active' : '' }}"
                        href="{{ route('language.index') }}">
                        
                        <span class="nav-link-text">{{ __('language') }}</span>
                    </a>
                </li> --}}

                <li class="nav-item">
                    <a class="nav-link"
                        onclick="event.preventDefault(); if(confirm('{{ __('Are you sure you want to logout?') }}')){ document.getElementById('logout').submit(); }"
                        href="#">
                        
                        <span class="nav-link-text">{{ __('Logout') }}</span>
                    </a>
                    <form id="logout" action="{{ route('logout') }}" method="POST"> @csrf </form>
                </li>

            </ul>
        </div>
    </div>

</nav>
