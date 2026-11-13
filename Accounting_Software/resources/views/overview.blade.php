<x-app-layout :title="config('app.name') . ' | ' . 'نظرة عامة'">
    @php
        $now = now();
        $hour = (int) $now->format('H');
        $greeting = $hour < 12 ? 'صباح الخير' : 'مساء الخير';
        $cleanName = strtok(auth()->user()->name, ' ');
        $servicesList = collect($daily_services_count ?? []);
        $maxService = $servicesList->isNotEmpty() ? (int) $servicesList->max() : 0;
        $todayDone = (int) ($daily_completed_count ?? 0);
        $todayTotal = max(1, (int) ($daily_orders_count ?? 0));
        $todayPercent = min(100, round(($todayDone / $todayTotal) * 100));
    @endphp

    {{-- ══════════════ HERO ══════════════ --}}
    <div class="card ec-overview-hero mb-3">
        <div class="card-body ec-overview-hero-body py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
                <div class="ec-overview-hero-text">
                    <span class="ec-overview-eyebrow"><i class="bx bxs-tachometer"></i> لوحة التحكم</span>
                    <h1 class="ec-overview-greeting">{{ $greeting }}، <span>{{ $cleanName }}</span></h1>
                    <p class="ec-overview-sub mb-0">هذا ملخص أداء المغسلة — العمليات الجارية والمدفوع والعملاء الجدد بنظرة واحدة.</p>
                </div>
                <div class="ec-overview-clock" aria-hidden="true">
                    <div class="ec-overview-clock-day">{{ $now->format('d') }}</div>
                    <div class="ec-overview-clock-meta">
                        <span class="ec-overview-clock-week">{{ $now->translatedFormat('l') }}</span>
                        <span class="ec-overview-clock-date">{{ $now->translatedFormat('j F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin())
        {{-- ══════════════ KPI STRIP ══════════════ --}}
        <div class="row g-3 mb-3">
            <div class="col-6 col-lg-3">
                <a class="ec-kpi-link" href="{{ route('orders.index') }}" aria-label="طلبات اليوم">
                <div class="card ec-kpi h-100">
                    <div class="card-body">
                        <div class="ec-kpi-icon ec-kpi-icon-primary"><i class="bx bx-list-ul"></i></div>
                        <div class="ec-kpi-body">
                            <span class="ec-kpi-label">طلبات اليوم</span>
                            <h3 class="ec-kpi-value">{{ $daily_orders_count }}</h3>
                            <span class="ec-kpi-foot"><i class="bx bx-trending-up"></i> منذ منتصف الليل</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a class="ec-kpi-link" href="{{ route('orders.index', ['orderStatus' => \App\Enums\OrderStatus::DELIVERED->value]) }}" aria-label="طلبات مكتملة">
                <div class="card ec-kpi h-100">
                    <div class="card-body">
                        <div class="ec-kpi-icon ec-kpi-icon-success"><i class="bx bx-check-circle"></i></div>
                        <div class="ec-kpi-body">
                            <span class="ec-kpi-label">طلبات مُكتملة</span>
                            <h3 class="ec-kpi-value">{{ $daily_completed_count }}</h3>
                            <span class="ec-kpi-foot text-teal">{{ $todayPercent }}% من اليوم</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a class="ec-kpi-link" href="{{ route('orders.index', ['orderStatus' => \App\Enums\OrderStatus::NEW->value]) }}" aria-label="طلبات معلقة">
                <div class="card ec-kpi h-100">
                    <div class="card-body">
                        <div class="ec-kpi-icon ec-kpi-icon-warning"><i class="bx bx-time-five"></i></div>
                        <div class="ec-kpi-body">
                            <span class="ec-kpi-label">طلبات معلقة</span>
                            <h3 class="ec-kpi-value">{{ $daily_pending_count }}</h3>
                            <span class="ec-kpi-foot text-amber">تحتاج تسليم</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a class="ec-kpi-link" href="{{ route('reports.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" aria-label="مدفوع اليوم">
                <div class="card ec-kpi h-100">
                    <div class="card-body">
                        <div class="ec-kpi-icon ec-kpi-icon-cash"><i class="bx bx-money-withdraw"></i></div>
                        <div class="ec-kpi-body">
                            <span class="ec-kpi-label">مدفوع اليوم</span>
                            <h3 class="ec-kpi-value text-nowrap">{{ number_format((float) $daily_paid_sum, 2) }}</h3>
                            <span class="ec-kpi-foot text-teal">₪ شيقل</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>

        {{-- ══════════════ SERVICES + MONEY ══════════════ --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-lg-7">
                <div class="card h-100 ec-service-card">
                    <div class="card-header bg-transparent border-0 pb-0 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold"><i class="bx bxs-washer text-teal me-2"></i>أداء الخدمات اليوم</h5>
                        <span class="ec-chip ec-chip-teal">{{ $servicesList->sum() }} طلب</span>
                    </div>
                    <div class="ec-service-body d-flex flex-column flex-md-row align-items-center gap-4 pt-3">
                        <div class="ec-chart-wrap flex-shrink-0">
                            {!! $services_chart->render() !!}
                        </div>
                        <ul class="ec-service-list flex-grow-1">
                            @forelse($servicesList as $serviceName => $serviceCount)
                                @php
                                    $bar = $maxService > 0 ? round(($serviceCount / $maxService) * 100) : 0;
                                @endphp
                                <li class="ec-service-item">
                                    <div class="ec-service-item-head">
                                        <span class="ec-service-item-name">{{ $serviceName }}</span>
                                        <span class="ec-service-item-count">{{ $serviceCount }}</span>
                                    </div>
                                    <div class="ec-progress"><span style="width: {{ $bar }}%"></span></div>
                                </li>
                            @empty
                                <li class="ec-service-empty"><i class="bx bx-dish"></i><span>لا توجد طلبات اليوم بعد</span></li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card h-100 ec-money-card">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="mb-0 fw-bold"><i class="bx bxs-bank text-teal me-2"></i>المدفوعات والأرباح</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            <div class="ec-money-row">
                                <div class="ec-money-label"><i class="bx bxs-credit-card text-teal-soft"></i><span>المدفوع الأسبوعي</span></div>
                                <div class="ec-money-value">{{ number_format((float) $weekly_paid_sum, 2) }} <span>₪</span></div>
                            </div>
                            <div class="ec-money-row">
                                <div class="ec-money-label"><i class="bx bxs-credit-card text-teal-soft"></i><span>المدفوع الشهري</span></div>
                                <div class="ec-money-value">{{ number_format((float) $monthly_paid_sum, 2) }} <span>₪</span></div>
                            </div>
                            <hr class="ec-divider">
                            <div class="ec-money-row">
                                <div class="ec-money-label"><i class="bx bxs-trending-up text-green-soft"></i><span>الربح الأسبوعي</span></div>
                                <div class="ec-money-value text-success-soft">{{ number_format((float) $weekly_profit, 2) }} <span>₪</span></div>
                            </div>
                            <div class="ec-money-row">
                                <div class="ec-money-label"><i class="bx bxs-trending-up text-green-soft"></i><span>الربح الشهري</span></div>
                                <div class="ec-money-value text-success-soft">{{ number_format((float) $monthly_profit, 2) }} <span>₪</span></div>
                            </div>
                            <div class="ec-money-row">
                                <div class="ec-money-label"><i class="bx bxs-crown text-amber-soft"></i><span>الربح السنوي</span></div>
                                <div class="ec-money-value text-amber-soft">{{ number_format((float) $yearly_profit, 2) }} <span>₪</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════ DETAILS ROW ══════════════ --}}
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 ec-tile-card">
                    <div class="card-header bg-transparent border-0"><h5 class="mb-0 fw-bold"><i class="bx bxs-bar-chart-alt-2 text-teal me-2"></i>إحصائيات الطلبات</h5></div>
                    <div class="card-body">
                        <div class="ec-tile-hero">
                            <span class="ec-tile-big">{{ $monthly_orders_count }}</span>
                            <span class="ec-tile-caption">طلب هذا الشهر</span>
                        </div>
                        <hr class="ec-divider">
                        <ul class="ec-stat-list">
                            <li><span>اليوم</span><strong>{{ $daily_orders_count }}</strong></li>
                            <li><span>هذا الأسبوع</span><strong>{{ $weekly_orders_count }}</strong></li>
                            <li><span>هذا الشهر</span><strong>{{ $monthly_orders_count }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 ec-tile-card">
                    <div class="card-header bg-transparent border-0"><h5 class="mb-0 fw-bold"><i class="bx bxs-user-plus text-teal me-2"></i>العملاء الجدد</h5></div>
                    <div class="card-body">
                        <div class="ec-tile-hero">
                            <span class="ec-tile-big">{{ $new_monthly_customer_count }}</span>
                            <span class="ec-tile-caption">هذا الشهر</span>
                        </div>
                        <hr class="ec-divider">
                        <ul class="ec-stat-list">
                            <li><span>اليوم</span><strong>{{ $new_daily_customer_count }}</strong></li>
                            <li><span>هذا الأسبوع</span><strong>{{ $new_weekly_customer_count }}</strong></li>
                            <li><span>هذا الشهر</span><strong>{{ $new_monthly_customer_count }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 ec-tile-card">
                    <div class="card-header bg-transparent border-0"><h5 class="mb-0 fw-bold"><i class="bx bxs-receipt text-teal me-2"></i>الطلبات المدفوعة</h5></div>
                    <div class="card-body">
                        <div class="ec-tile-hero">
                            <span class="ec-tile-big">{{ number_format((float) $daily_paid_sum, 2) }}</span>
                            <span class="ec-tile-caption">₪ اليوم — المُحصّل نقديًا</span>
                        </div>
                        <hr class="ec-divider">
                        <ul class="ec-stat-list">
                            <li><span>الأسبوعي</span><strong>{{ number_format((float) $weekly_paid_sum, 2) }} ₪</strong></li>
                            <li><span>الشهري</span><strong>{{ number_format((float) $monthly_paid_sum, 2) }} ₪</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
