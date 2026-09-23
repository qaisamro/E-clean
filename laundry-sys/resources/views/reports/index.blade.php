<x-app-layout :title="config('app.name') . ' | التقارير المالية'">

    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <i class="bx bx-bar-chart-alt-2 text-primary me-2"></i>
            التقارير المالية الشاملة
        </h4>
    </div>

    {{-- Date Filter --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">من تاريخ</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bx bx-filter-alt me-1"></i> عرض التقرير
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
                {{-- Quick filters --}}
                <div class="col-12 d-flex gap-2 flex-wrap">
                    <span class="text-muted small fw-bold align-self-center">فلاتر سريعة:</span>
                    <a href="{{ route('reports.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}"
                       class="btn btn-sm btn-outline-secondary {{ $from == now()->toDateString() && $to == now()->toDateString() ? 'active' : '' }}">اليوم</a>
                    <a href="{{ route('reports.index', ['from' => now()->startOfWeek()->toDateString(), 'to' => now()->toDateString()]) }}"
                       class="btn btn-sm btn-outline-secondary">هذا الأسبوع</a>
                    <a href="{{ route('reports.index', ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()]) }}"
                       class="btn btn-sm btn-outline-secondary {{ $from == now()->startOfMonth()->toDateString() && $to == now()->toDateString() ? 'active' : '' }}">هذا الشهر</a>
                    <a href="{{ route('reports.index', ['from' => now()->startOfYear()->toDateString(), 'to' => now()->toDateString()]) }}"
                       class="btn btn-sm btn-outline-secondary">هذه السنة</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ========== Revenue Cards ========== --}}
    <h6 class="text-uppercase text-muted fw-bold mb-3 small">📥 الإيرادات المقبوضة فعلياً</h6>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #28a745 !important;">
                <div class="card-body text-center py-4">
                    <div class="mb-2"><i class="bx bx-money text-success" style="font-size:2.5rem"></i></div>
                    <h6 class="text-muted mb-1">إجمالي الإيرادات</h6>
                    <h3 class="fw-bold text-success mb-0">{{ number_format($total_revenue, 2) }} ₪</h3>
                    <small class="text-muted">كاش + فيزا</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #17a2b8 !important;">
                <div class="card-body text-center py-4">
                    <div class="mb-2"><i class="bx bx-money-withdraw text-info" style="font-size:2.5rem"></i></div>
                    <h6 class="text-muted mb-1">إيرادات نقدية</h6>
                    <h3 class="fw-bold text-info mb-0">{{ number_format($cash_revenue, 2) }} ₪</h3>
                    <small class="text-muted">دفعات الكاش فقط</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #6610f2 !important;">
                <div class="card-body text-center py-4">
                    <div class="mb-2"><i class="bx bx-credit-card text-purple" style="font-size:2.5rem; color:#6610f2"></i></div>
                    <h6 class="text-muted mb-1">إيرادات فيزا</h6>
                    <h3 class="fw-bold mb-0" style="color:#6610f2">{{ number_format($visa_revenue, 2) }} ₪</h3>
                    <small class="text-muted">دفعات الفيزا فقط</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== Orders & Sales Cards ========== --}}
    <h6 class="text-uppercase text-muted fw-bold mb-3 small">📦 الطلبات والمبيعات</h6>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="card-body">
                    <i class="bx bxs-cart text-primary" style="font-size:2rem"></i>
                    <h5 class="fw-bold mt-2 mb-0">{{ number_format($total_orders) }}</h5>
                    <small class="text-muted">إجمالي الطلبات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="card-body">
                    <i class="bx bx-check-circle text-success" style="font-size:2rem"></i>
                    <h5 class="fw-bold mt-2 mb-0 text-success">{{ number_format($completed_orders) }}</h5>
                    <small class="text-muted">طلبات مكتملة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="card-body">
                    <i class="bx bx-time text-warning" style="font-size:2rem"></i>
                    <h5 class="fw-bold mt-2 mb-0 text-warning">{{ number_format($pending_orders) }}</h5>
                    <small class="text-muted">طلبات قيد التنفيذ</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="card-body">
                    <i class="bx bx-x-circle text-danger" style="font-size:2rem"></i>
                    <h5 class="fw-bold mt-2 mb-0 text-danger">{{ number_format($cancelled_orders) }}</h5>
                    <small class="text-muted">طلبات ملغاة</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== Financial Summary ========== --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header fw-bold">
                    <i class="bx bx-calculator text-primary me-2"></i>الملخص المالي
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">قيمة المبيعات (إجمالي الطلبات)</td>
                                <td class="text-end fw-bold">{{ number_format($total_sales_value, 2) }} ₪</td>
                            </tr>
                            <tr>
                                <td class="text-success">✅ الإيرادات المقبوضة فعلياً</td>
                                <td class="text-end fw-bold text-success">{{ number_format($total_revenue, 2) }} ₪</td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-danger">المصروفات التشغيلية</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($total_expenses, 2) }} ₪</td>
                            </tr>
                            <tr>
                                <td class="text-danger">الرواتب المدفوعة</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($paid_salaries, 2) }} ₪</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold text-dark">إجمالي التكاليف</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($total_costs, 2) }} ₪</td>
                            </tr>
                            <tr class="border-top bg-light">
                                <td class="fw-bold fs-6">صافي الربح</td>
                                <td class="text-end fw-bold fs-5 {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $net_profit >= 0 ? '+' : '' }}{{ number_format($net_profit, 2) }} ₪
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header fw-bold">
                    <i class="bx bx-trending-up text-warning me-2"></i>أكثر الخدمات طلباً
                </div>
                <div class="card-body">
                    @forelse($top_services as $svc)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="fw-bold">{{ $svc->service->name ?? 'غير محدد' }}</span>
                                <br>
                                <small class="text-muted">{{ number_format($svc->total_qty) }} قطعة</small>
                            </div>
                            <span class="badge bg-label-primary fs-7">{{ number_format($svc->total_revenue, 2) }} ₪</span>
                        </div>
                    @empty
                        <p class="text-center text-muted py-3">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Debts Summary --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-right: 4px solid #dc3545 !important;">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="bx bx-credit-card-front text-danger" style="font-size:2.5rem"></i>
                    <div>
                        <h6 class="mb-0 text-muted">إجمالي الديون المستحقة (كل الوقت)</h6>
                        <h4 class="fw-bold text-danger mb-0">{{ number_format($total_debts, 2) }} ₪</h4>
                        <a href="{{ route('customers.debts') }}" class="small text-primary">عرض تفاصيل الديون ←</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-right: 4px solid #20c997 !important;">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="bx bx-user-plus text-success" style="font-size:2.5rem"></i>
                    <div>
                        <h6 class="mb-0 text-muted">عملاء جدد في الفترة</h6>
                        <h4 class="fw-bold text-success mb-0">{{ number_format($new_customers) }} عميل</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
