@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px">
        <div>
            <h2 class="mb-1">عملاء المتجر</h2>
            <p class="text-muted mb-0">
                {{ $vendor->name }} — يظهر هنا فقط العملاء الذين لديهم طلبات مكتملة (Delivered) من متجرك.
            </p>
        </div>
        <div class="d-flex flex-wrap" style="gap:8px">
            <a href="{{ route('vendor.notifications.index') }}" class="btn btn-success">
                <i class="fa fa-paper-plane"></i> إرسال إشعار
            </a>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-light border">
                <i class="fa fa-arrow-right"></i> لوحة التحكم
            </a>
        </div>
    </div>

    <div class="row" id="vc-summary-row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">عملاء المتجر</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['customers'] }}</div>
                    <small class="text-muted">عميل مؤهل من الطلبات المكتملة</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">الطلبات المكتملة</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['orders'] }}</div>
                    <small class="text-muted">طلب Delivered من متجرك</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">عملاء آخر 30 يومًا</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['recent_customers'] }}</div>
                    <small class="text-muted">عميل لديه طلب مكتمل حديث</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">أوقفوا إشعارات المتاجر</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['disabled_customers'] }}</div>
                    <small class="text-muted">لن يستلموا إشعارات المتجر</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:12px">
                <div>
                    <h5 class="m-0">قائمة العملاء</h5>
                    <small class="text-muted">
                        تحديث تلقائي كل 30 ثانية · آخر تحديث:
                        <span id="vc-last-updated">—</span>
                    </small>
                </div>
                <div class="d-flex align-items-center" style="gap:8px">
                    <button type="button" id="vc-refresh-now" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-sync-alt"></i> تحديث الآن
                    </button>
                    <form method="GET" action="{{ route('vendor.customers.index') }}" class="form-inline">
                    <div class="input-group">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               class="form-control"
                               placeholder="ابحث بالاسم أو رقم الجوال أو البريد"
                               style="min-width:280px">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-search"></i> بحث
                            </button>
                            @if ($search !== '')
                                <a href="{{ route('vendor.customers.index') }}" class="btn btn-light border">
                                    إلغاء
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>العميل</th>
                            <th>رقم الجوال</th>
                            <th>البريد الإلكتروني</th>
                            <th>الطلبات المكتملة</th>
                            <th>إجمالي المشتريات</th>
                            <th>آخر طلب مكتمل</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="vc-table-body">
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:10px">
                                        <span class="d-inline-flex align-items-center justify-content-center"
                                              style="width:38px;height:38px;border-radius:50%;background:#e8f6f4;color:#028090;font-weight:800">
                                            {{ mb_substr(trim($customer->name), 0, 1) }}
                                        </span>
                                        <div>
                                            <div class="font-weight-bold">{{ $customer->name }}</div>
                                            <small class="text-muted">عميل #{{ $customer->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td dir="ltr" class="text-right">{{ $customer->user->mobile ?? '—' }}</td>
                                <td dir="ltr" class="text-right">{{ $customer->user->email ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-success" style="font-size:13px">
                                        {{ (int) $customer->delivered_orders }}
                                    </span>
                                </td>
                                <td>{{ currencyPosition((float) ($customer->total_spent ?? 0)) }}</td>
                                <td>
                                    {{ $customer->last_order_at ? \Carbon\Carbon::parse($customer->last_order_at)->format('Y/m/d') : '—' }}
                                </td>
                                <td class="text-right" style="white-space:nowrap">
                                    <a href="{{ route('vendor.customers.edit', $customer->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="تعديل بيانات العميل">
                                        <i class="fa fa-edit"></i> تعديل
                                    </a>
                                    <a href="{{ route('vendor.notifications.index', ['customer_id' => $customer->id]) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fa fa-paper-plane"></i> إرسال إشعار
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-2x d-block mb-2" style="opacity:.35"></i>
                                    لا يوجد عملاء مؤهلون بعد. يظهر العميل هنا تلقائيًا بعد أول طلب مكتمل من متجرك.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div id="vc-pagination">
            @if ($customers->hasPages())
                <div class="card-footer bg-white">
                    {{ $customers->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';
    var REFRESH_MS = 30000;
    var endpoint = window.location.href;
    var timer = null;
    var inFlight = false;
    var abortController = null;

    function isTyping() {
        var el = document.activeElement;
        if (!el) return false;
        var tag = (el.tagName || '').toLowerCase();
        return tag === 'input' || tag === 'textarea' || tag === 'select' || !!el.isContentEditable;
    }

    function setLastUpdated() {
        var el = document.getElementById('vc-last-updated');
        if (el) el.textContent = new Date().toLocaleTimeString();
    }

    async function refresh() {
        if (document.hidden) return;
        if (isTyping()) return;
        if (inFlight) return;
        inFlight = true;
        try {
            abortController = new AbortController();
            var res = await fetch(endpoint, {
                credentials: 'same-origin',
                cache: 'no-store',
                signal: abortController.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            var html = await res.text();
            var doc = new DOMParser().parseFromString(html, 'text/html');

            var pairs = [
                ['vc-summary-row', 'vc-summary-row'],
                ['vc-table-body', 'vc-table-body'],
                ['vc-pagination', 'vc-pagination']
            ];
            pairs.forEach(function (pair) {
                var cur = document.getElementById(pair[0]);
                var neu = doc.getElementById(pair[1]);
                if (cur && neu) cur.innerHTML = neu.innerHTML;
            });
            setLastUpdated();
        } catch (e) {
            // ignore network/parse errors; next cycle will retry
        } finally {
            inFlight = false;
            abortController = null;
        }
    }

    function schedule() {
        if (timer) clearInterval(timer);
        timer = setInterval(refresh, REFRESH_MS);
    }

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refresh();
    });

    var refreshBtn = document.getElementById('vc-refresh-now');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () { refresh(); });
    }

    // Resume polling shortly after the user stops typing in the search box.
    var searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('blur', function () {
            setTimeout(refresh, 300);
        });
    }

    window.addEventListener('pagehide', function () {
        if (timer) clearInterval(timer);
        if (abortController) abortController.abort();
    });
    window.addEventListener('beforeunload', function () {
        if (timer) clearInterval(timer);
        if (abortController) abortController.abort();
    });

    schedule();
    setLastUpdated();
    window.refreshVendorCustomers = refresh;
})();
</script>
@endpush
@endsection
