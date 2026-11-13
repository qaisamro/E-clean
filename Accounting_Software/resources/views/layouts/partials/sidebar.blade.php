<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <style>
        #layout-menu .app-brand { min-height: 100px; padding: 14px 12px 12px; border-bottom: 1px solid rgba(105,108,255,.12); }
        #layout-menu .brand-logo-frame { width: 166px; height: 66px; overflow: hidden; border-radius: .8rem; background: #1e647b; box-shadow: 0 5px 14px rgba(25,77,96,.18); display: flex; justify-content: center; }
        #layout-menu .brand-logo-frame img { width: 166px; min-width: 166px; max-width: none; height: 166px; object-fit: cover; transform: translateY(-53px); }
        #layout-menu .app-brand-link { display: flex; align-items: center; justify-content: center; }
        #layout-menu .layout-menu-toggle { position: absolute; inset-inline-end: .3rem; top: 1.9rem; }
        #layout-menu .elite-sidebar { padding: .75rem .55rem 1rem; }
        #layout-menu .menu-header { margin: 1rem .55rem .4rem; padding: 0; font-size: .69rem; letter-spacing: .04em; color: #8d93a7; }
        #layout-menu .menu-item { margin: .15rem 0; }
        #layout-menu .menu-link { border-radius: .55rem; min-height: 42px; font-weight: 600; }
        #layout-menu .menu-link:hover { background: rgba(105,108,255,.08); }
        #layout-menu .menu-item.active > .menu-link { box-shadow: 0 4px 12px rgba(105,108,255,.18); }
        #layout-menu .menu-sub { margin-top: .15rem; padding-bottom: .2rem; }
        #layout-menu .menu-sub .menu-link { min-height: 36px; font-size: .9rem; font-weight: 500; }
        #layout-menu .menu-icon { font-size: 1.25rem; width: 1.7rem; text-align: center; }
        #layout-menu .menu-badge { margin-inline-start: auto; }
    </style>
    <div class="app-brand justify-content-center demo position-relative">
        <a href="{{ route('overview') }}" class="app-brand-link" aria-label="الصفحة الرئيسية">
            <span class="brand-logo-frame">
                <img src="{{ asset('assets/img/logo.webp') }}" alt="Elite Clean">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $can = fn (string $permission): bool => auth()->user()->hasPermission($permission);
    @endphp

    <ul class="menu-inner elite-sidebar">

        {{-- Overview --}}
        <li @class(['menu-item', 'active' => request()->routeIs('overview')])>
            <a href="{{ route('overview') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">نظرة عامة</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">العمليات اليومية</span></li>

        {{-- Customers --}}
        @if($can('customers.view'))
        <li class="mt-2 menu-item {{ activeMainLi('customers.*') }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-group'></i>
                <div>العملاء</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ activeChildLi('customers.index') }}">
                    <a href="{{ route('customers.index') }}" class="menu-link">
                        <div>قائمة العملاء</div>
                    </a>
                </li>
                @if($can('customers.manage'))
                <li class="menu-item {{ activeChildLi('customers.create') }}">
                    <a href="{{ route('customers.create') }}" class="menu-link">
                        <div>إضافة عميل جديد</div>
                    </a>
                </li>
                @endif
                <li class="menu-item {{ activeChildLi('customers.debts') }}">
                    <a href="{{ route('customers.debts') }}" class="menu-link">
                        <div>
                            المديونية للعملاء
                            @php $debtCount = \App\Models\Order::where('remaining_money', '>', 0)->distinct('customer_id')->count('customer_id'); @endphp
                            @if($debtCount > 0)
                                <span class="badge bg-danger rounded-pill menu-badge">{{ $debtCount }}</span>
                            @endif
                        </div>
                    </a>
                </li>
                @if($can('customers.manage'))
                <li class="menu-item {{ activeChildLi('customer-payments.create') }}">
                    <a href="{{ route('customer-payments.create') }}" class="menu-link">
                        <div>إضافة دفعة عميل</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- Orders --}}
        @if($can('orders.view'))
        <li class="mt-2 menu-item {{ activeMainLi('orders.*') }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-cart'></i>
                <div>الطلبات</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ activeChildLi('orders.index') }}">
                    <a href="{{ route('orders.index') }}" class="menu-link">
                        <div>قائمة الطلبات</div>
                    </a>
                </li>
                @if($can('orders.create'))
                <li class="menu-item {{ activeChildLi('orders.create') }}">
                    <a href="{{ route('orders.create') }}" class="menu-link">
                        <div>إضافة طلب جديد</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- Control Panel: only shown when the user has at least one control permission --}}
        @if($can('item_services.view') || $can('services.view') || $can('inventory.view') || $can('expenses.view') || $can('salaries.view') || $can('reports.view') || $can('suppliers.view') || auth()->user()->isSuperAdmin())
        <li class="menu-header small text-uppercase"><span class="menu-header-text">الإدارة والتشغيل</span></li>

            {{-- Users / Employees: super admin only --}}
            @if (auth()->user()->isSuperAdmin())
                <li class="mt-2 menu-item {{ activeMainLi('users.*') }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='menu-icon tf-icons bx bx-user-check'></i>
                        <div>المستخدمون</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('users.index') }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div>قائمة المستخدمين</div>
                            </a>
                        </li>
                        <li class="menu-item {{ activeChildLi('users.create') }}">
                            <a href="{{ route('users.create') }}" class="menu-link">
                                <div>إضافة مستخدم جديد</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Item Prices --}}
            @if($can('item_services.view'))
                <li class="mt-2 menu-item {{ activeMainLi('itemServices.*') }} {{ activeMainLi('itemPrices.show') }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='menu-icon tf-icons bx bx-money'></i>
                        <div>أسعار الخدمات</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('itemServices.index') }}">
                            <a href="{{ route('itemServices.index') }}" class="menu-link">
                                <div>قائمة الأسعار</div>
                            </a>
                        </li>
                        @if($can('item_services.manage'))
                        <li class="menu-item {{ activeChildLi('itemServices.create') }}">
                            <a href="{{ route('itemServices.create') }}" class="menu-link">
                                <div>إضافة تسعيرة</div>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Services --}}
            @if($can('services.view'))
                <li class="mt-2 menu-item {{ activeMainLi('services.*') }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='menu-icon tf-icons bx bx-briefcase-alt-2'></i>
                        <div>الخدمات</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('services.index') }}">
                            <a href="{{ route('services.index') }}" class="menu-link">
                                <div>قائمة الخدمات</div>
                            </a>
                        </li>
                        @if($can('services.manage'))
                        <li class="menu-item {{ activeChildLi('services.create') }}">
                            <a href="{{ route('services.create') }}" class="menu-link">
                                <div>إضافة خدمة</div>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Inventory --}}
            @if($can('inventory.view'))

                {{-- Inventory: stock management for products, supplies and laundry items --}}
                <li class="mt-2 menu-item {{ activeMainLi('inventory.*') }}">
                    @php $inventoryShortage = app(\App\Services\StockService::class)->shortageCount(); @endphp
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='menu-icon tf-icons bx bx-package'></i>
                        <div>المخزن
                            @if($inventoryShortage > 0)
                                <span class="badge bg-danger rounded-pill menu-badge">{{ $inventoryShortage }}</span>
                            @endif
                        </div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('inventory.products.index') }} {{ activeChildLi('inventory.products.create') }} {{ activeChildLi('inventory.products.edit') }}">
                            <a href="{{ route('inventory.products.index') }}" class="menu-link">
                                <div>المنتجات</div>
                            </a>
                        </li>
                        @if($can('inventory.manage'))
                        <li class="menu-item {{ activeChildLi('inventory.categories.*') }}">
                            <a href="{{ route('inventory.categories.index') }}" class="menu-link">
                                <div>إدارة الفئات</div>
                            </a>
                        </li>
                        @endif
                        <li class="menu-item {{ activeChildLi('inventory.movements.index') }} {{ activeChildLi('inventory.movements.create') }}">
                            <a href="{{ route('inventory.movements.index') }}" class="menu-link">
                                <div>حركات المخزن</div>
                            </a>
                        </li>
                        <li class="menu-item {{ activeChildLi('inventory.shortages') }}">
                            <a href="{{ route('inventory.shortages') }}" class="menu-link">
                                <div>النواقص
                                    @if($inventoryShortage > 0)
                                        <span class="badge bg-danger menu-badge">{{ $inventoryShortage }}</span>
                                    @endif
                                </div>
                            </a>
                        </li>
                        <li class="menu-item {{ activeChildLi('inventory.reports') }}">
                            <a href="{{ route('inventory.reports') }}" class="menu-link">
                                <div>تقارير المخزن</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Suppliers --}}
            @if($can('suppliers.view'))
                <li class="mt-2 menu-item {{ activeMainLi('suppliers.*') }} {{ activeMainLi('supplier-bills.*') }} {{ activeMainLi('supplier-payments.*') }}">
                    @php $supplierDue = \App\Models\SupplierBill::where('remaining', '>', 0)->count(); @endphp
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='menu-icon tf-icons bx bx-truck'></i>
                        <div>الموردون
                            @if($supplierDue > 0)
                                <span class="badge bg-warning rounded-pill menu-badge">{{ $supplierDue }}</span>
                            @endif
                        </div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('suppliers.index') }}">
                            <a href="{{ route('suppliers.index') }}" class="menu-link">
                                <div>قائمة الموردين</div>
                            </a>
                        </li>
                        <li class="menu-item {{ activeChildLi('supplier-bills.index') }}">
                            <a href="{{ route('supplier-bills.index') }}" class="menu-link">
                                <div>فواتير الشراء</div>
                            </a>
                        </li>
                        @if($can('suppliers.manage'))
                        <li class="menu-item {{ activeChildLi('supplier-bills.create') }}">
                            <a href="{{ route('supplier-bills.create') }}" class="menu-link">
                                <div>إضافة فاتورة شراء</div>
                            </a>
                        </li>
                        @endif
                        <li class="menu-item {{ activeChildLi('supplier-payments.index') }} {{ activeChildLi('supplier-payments.create') }}">
                            <a href="{{ route('supplier-payments.index') }}" class="menu-link">
                                <div>الدفعات</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Expenses --}}
            @if($can('expenses.view'))
            <li class="mt-2 menu-item {{ activeMainLi('expenses.*') }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-wallet'></i>
                    <div>المصروفات</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeChildLi('expenses.index') }}">
                        <a href="{{ route('expenses.index') }}" class="menu-link">
                            <div>قائمة المصروفات</div>
                        </a>
                    </li>
                    @if($can('expenses.manage'))
                    <li class="menu-item {{ activeChildLi('expenses.create') }}">
                        <a href="{{ route('expenses.create') }}" class="menu-link">
                            <div>إضافة مصروف</div>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- Salaries --}}
            @if($can('salaries.view'))
            <li class="mt-2 menu-item {{ activeMainLi('salaries.*') }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-id-card'></i>
                    <div>الرواتب</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeChildLi('salaries.index') }}">
                        <a href="{{ route('salaries.index') }}" class="menu-link">
                            <div>قائمة الرواتب</div>
                        </a>
                    </li>
                    @if($can('salaries.manage'))
                    <li class="menu-item {{ activeChildLi('salaries.create') }}">
                        <a href="{{ route('salaries.create') }}" class="menu-link">
                            <div>إضافة راتب</div>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- Reports --}}
            @if($can('reports.view'))
            <li class="mt-2 menu-item {{ activeChildLi('reports.index') }}">
                <a href="{{ route('reports.index') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-bar-chart-alt-2'></i>
                    <div>التقارير المالية</div>
                </a>
            </li>
            @endif

            {{-- Invoice template - super admin only --}}
            @if(auth()->user()->isSuperAdmin())
            <li class="mt-2 menu-item {{ activeChildLi('invoice-template.edit') }}">
                <a href="{{ route('invoice-template.edit') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-receipt'></i>
                    <div>تصميم قالب الفاتورة</div>
                </a>
            </li>
            @endif
        @endif

        {{-- Profile --}}
        <li class="menu-item mt-4" style="border-top: 1px solid #e7e7f0; padding-top: 10px;">
            <a href="{{ route('profile.edit') }}" class="menu-link {{ activeChildLi('profile.edit') }}">
                <i class='menu-icon tf-icons bx bx-user'></i>
                <div>الملف الشخصي</div>
            </a>
        </li>

        {{-- Logout - Visible for all --}}
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="menu-link" style="color: #ff3e1d;">
                    <i class='menu-icon tf-icons bx bx-log-out' style="color: #ff3e1d;"></i>
                    <div>تسجيل خروج</div>
                </a>
            </form>
        </li>
    </ul>
</aside>
