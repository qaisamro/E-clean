<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand justify-content-center demo">
        <a href="{{ route('overview') }}" class="app-brand-link">
            <img src="{{ $logo ?? asset('assets/img/logo.webp') }}" style="width: 160px" alt="">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- Overview --}}
        <li @class(['menu-item', 'active' => request()->routeIs('overview')])>
            <a href="{{ route('overview') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">نظرة عامة</div>
            </a>
        </li>

        {{-- Customers --}}
        <li class="mt-2 menu-item {{ activeMainLi('customers.*') }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class='bx bx-run me-3'></i>
                <div>العملاء</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ activeChildLi('customers.index') }}">
                    <a href="{{ route('customers.index') }}" class="menu-link">
                        <div>قائمة العملاء</div>
                    </a>
                </li>
                <li class="menu-item {{ activeChildLi('customers.debts') }}">
                    <a href="{{ route('customers.debts') }}" class="menu-link">
                        <div>
                            الديون المستحقة
                            @php $debtCount = \App\Models\Order::where('remaining_money', '>', 0)->distinct('customer_id')->count('customer_id'); @endphp
                            @if($debtCount > 0)
                                <span class="badge bg-danger ms-1 rounded-pill">{{ $debtCount }}</span>
                            @endif
                        </div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Orders --}}
        <li class="mt-2 menu-item {{ activeMainLi('orders.*') }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class='bx bxs-cart me-3'></i>
                <div>الطلبات</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ activeChildLi('orders.index') }}">
                    <a href="{{ route('orders.index') }}" class="menu-link">
                        <div>قائمة الطلبات</div>
                    </a>
                </li>
                <li class="menu-item {{ activeChildLi('orders.customer.index') }}">
                    <a href="{{ route('orders.customer.index') }}" class="menu-link">
                        <div>طلبات العملاء</div>
                    </a>
                </li>
                <li class="menu-item {{ activeChildLi('orders.create') }}">
                    <a href="{{ route('orders.create') }}" class="menu-link">
                        <div>إضافة طلب جديد</div>
                    </a>
                </li>
            </ul>
        </li>

        @if (auth()->user()->isAdministrator())
            <li class="menu-header small text-uppercase"><span class="menu-header-text">الضبط و التحكم</span></li>

            {{-- Users / Employees - Hidden for Elite Accounting --}}
            <!--
            <li class="mt-2 menu-item {{ activeMainLi('users.*') }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='bx bxs-user-badge me-3'></i>
                    <div>الموظفون</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeChildLi('users.index') }}">
                        <a href="{{ route('users.index') }}" class="menu-link">
                            <div>قائمة الموظفين</div>
                        </a>
                    </li>
                    <li class="menu-item {{ activeChildLi('users.create') }}">
                        <a href="{{ route('users.create') }}" class="menu-link">
                            <div>إضافة موظف</div>
                        </a>
                    </li>
                </ul>
            </li>
            -->

            {{-- Item Prices --}}
            <li class="mt-2 menu-item {{ activeMainLi('itemServices.*') }} {{ activeMainLi('itemPrices.show') }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='bx bx-dollar bx-tada me-3'></i>
                    <div>أسعار الخدمات</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeChildLi('itemServices.index') }}">
                        <a href="{{ route('itemServices.index') }}" class="menu-link">
                            <div>قائمة الأسعار</div>
                        </a>
                    </li>
                    <li class="menu-item {{ activeChildLi('itemServices.create') }}">
                        <a href="{{ route('itemServices.create') }}" class="menu-link">
                            <div>إضافة تسعيرة</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Items - Hidden for Elite Accounting --}}
            <!--
            <li class="mt-2 menu-item {{ activeMainLi('items.*') }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='bx bx-category-alt me-3'></i>
                    <div>الأصناف</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeChildLi('items.index') }}">
                        <a href="{{ route('items.index') }}" class="menu-link">
                            <div>قائمة الأصناف</div>
                        </a>
                    </li>
                    <li class="menu-item {{ activeChildLi('items.create') }}">
                        <a href="{{ route('items.create') }}" class="menu-link">
                            <div>إضافة صنف</div>
                        </a>
                    </li>
                </ul>
            </li>
            -->

            {{-- Services - Hidden for Elite Accounting --}}
            <!--
            <li class="mt-2 menu-item {{ activeMainLi('services.*') }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='bx bx-briefcase-alt-2 me-3'></i>
                    <div>الخدمات</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeChildLi('services.index') }}">
                        <a href="{{ route('services.index') }}" class="menu-link">
                            <div>قائمة الخدمات</div>
                        </a>
                    </li>
                    <li class="menu-item {{ activeChildLi('services.create') }}">
                        <a href="{{ route('services.create') }}" class="menu-link">
                            <div>إضافة خدمة</div>
                        </a>
                    </li>
                </ul>
            </li>
            -->

            @if (auth()->user()->isSuperAdmin())

                {{-- Expenses --}}
                <li class="mt-2 menu-item {{ activeMainLi('expenses.*') }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='bx bx-wallet me-3'></i>
                        <div>المصروفات</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('expenses.index') }}">
                            <a href="{{ route('expenses.index') }}" class="menu-link">
                                <div>قائمة المصروفات</div>
                            </a>
                        </li>
                        <li class="menu-item {{ activeChildLi('expenses.create') }}">
                            <a href="{{ route('expenses.create') }}" class="menu-link">
                                <div>إضافة مصروف</div>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Salaries - Commented for now --}}
                <!--
                <li class="mt-2 menu-item {{ activeMainLi('salaries.*') }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='bx bx-id-card me-3'></i>
                        <div>المرتبات</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('salaries.index') }}">
                            <a href="{{ route('salaries.index') }}" class="menu-link">
                                <div>قائمة المرتبات</div>
                            </a>
                        </li>
                        <li class="menu-item {{ activeChildLi('salaries.create') }}">
                            <a href="{{ route('salaries.create') }}" class="menu-link">
                                <div>إضافة مرتب</div>
                            </a>
                        </li>
                    </ul>
                </li>
                -->

                {{-- Reports --}}
                <li class="mt-2 menu-item {{ activeChildLi('reports.index') }}">
                    <a href="{{ route('reports.index') }}" class="menu-link">
                        <i class='bx bx-bar-chart-alt-2 me-3'></i>
                        <div>التقارير المالية</div>
                    </a>
                </li>

                {{-- Settings - Hidden for Elite Accounting --}}
                <!--
                <li class="mt-2 menu-item {{ activeMainLi('settings.*') }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class='bx bx-cog bx-spin me-3'></i>
                        <div>إعدادات الموقع</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ activeChildLi('settings.index') }}">
                            <a href="{{ route('settings.index') }}" class="menu-link">
                                <div>الإعدادات</div>
                            </a>
                        </li>
                    </ul>
                </li>
                -->

            @endif
        @endif

        {{-- Logout - Visible for all --}}
        <li class="menu-item mt-4" style="border-top: 1px solid #eee; padding-top: 10px;">
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="menu-link" style="color: #ff3e1d;">
                    <i class='bx bx-log-out me-3' style="color: #ff3e1d;"></i>
                    <div>تسجيل خروج</div>
                </a>
            </form>
        </li>
    </ul>
</aside>
