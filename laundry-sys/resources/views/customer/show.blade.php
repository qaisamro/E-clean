<x-app-layout :title="config('app.name') . ' | تفاصيل عميل'">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('customers.index') }}">العملاء</a> /</span>
        تفاصيل عميل
    </h4>

    {{-- Customer Info Card --}}
    <div class="card mb-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row p-4 pb-0">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:1.5rem;font-weight:bold;">
                    {{ mb_substr($customer->name, 0, 1) }}
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $customer->name }}</h5>
                    <span class="text-muted small">{{ $customer->phone }}</span>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-edit me-1"></i> تعديل البيانات
                </a>
            </div>
        </div>
        <hr>
        <div class="row gy-3 px-4 card-body pt-0">
            <div class="col-md-3">
                <span class="text-muted small d-block">رقم المسلسل</span>
                <span class="fw-bold">#{{ $customer->id }}</span>
            </div>
            <div class="col-md-3">
                <span class="text-muted small d-block">الاسم الكامل</span>
                <span class="fw-bold">{{ $customer->name }}</span>
            </div>
            <div class="col-md-3">
                <span class="text-muted small d-block">رقم الهاتف</span>
                <span class="fw-bold">{{ $customer->phone }}</span>
            </div>
            <div class="col-md-3">
                <span class="text-muted small d-block">العنوان</span>
                <span class="fw-bold">{{ $customer->address ?: '—' }}</span>
            </div>
            <div class="col-md-3">
                <span class="text-muted small d-block">تاريخ الإضافة</span>
                <span class="fw-bold">{{ $customer->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    @php
        $totalOrders    = $customer->orders->count();
        $totalValue     = $customer->orders->sum('total_price');
        $totalPaid      = $customer->orders->sum('paid_money');
        $totalRemaining = $customer->orders->sum('remaining_money');
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
                <div class="card-body py-0">
                    <i class="bx bxs-cart text-primary" style="font-size:1.8rem"></i>
                    <h5 class="fw-bold mt-1 mb-0">{{ $totalOrders }}</h5>
                    <small class="text-muted">إجمالي الطلبات</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
                <div class="card-body py-0">
                    <i class="bx bx-shekel text-info" style="font-size:1.8rem"></i>
                    <h5 class="fw-bold mt-1 mb-0">{{ number_format($totalValue, 2) }} ₪</h5>
                    <small class="text-muted">إجمالي قيمة الطلبات</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
                <div class="card-body py-0">
                    <i class="bx bx-check-circle text-success" style="font-size:1.8rem"></i>
                    <h5 class="fw-bold mt-1 mb-0 text-success">{{ number_format($totalPaid, 2) }} ₪</h5>
                    <small class="text-muted">المبلغ المدفوع</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm py-3">
                <div class="card-body py-0">
                    <i class="bx bx-credit-card-front text-danger" style="font-size:1.8rem"></i>
                    <h5 class="fw-bold mt-1 mb-0 text-danger">{{ number_format($totalRemaining, 2) }} ₪</h5>
                    <small class="text-muted">الدين المتبقي</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card shadow-sm">
        <div class="d-flex align-items-center justify-content-between p-4 pb-2">
            <h5 class="mb-0 fw-bold">
                <i class="bx bxs-cart me-2 text-primary"></i>
                طلبات العميل السابقة ({{ $totalOrders }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">رقم الطلب</th>
                            <th>الإجمالي</th>
                            <th>المدفوع</th>
                            <th class="text-danger">المتبقي</th>
                            <th>حالة الطلب</th>
                            <th>حالة الدفع</th>
                            <th>التاريخ</th>
                            <th class="text-center">التحكم</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($customer->orders as $order)
                            <tr>
                                <td class="px-4">
                                    <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td class="fw-bold">{{ number_format($order->total_price, 2) }} ₪</td>
                                <td class="text-success fw-bold">{{ number_format($order->paid_money, 2) }} ₪</td>
                                <td class="{{ $order->remaining_money > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ number_format($order->remaining_money, 2) }} ₪
                                </td>
                                <td>
                                    <span @class([
                                        'badge me-1',
                                        'bg-label-warning' => $order->status === \App\Enums\OrderStatus::NEW,
                                        'bg-label-info'    => in_array($order->status, [\App\Enums\OrderStatus::RECEIVED, \App\Enums\OrderStatus::UNDER_CLEANING, \App\Enums\OrderStatus::UNDER_IRONING]),
                                        'bg-label-primary' => in_array($order->status, [\App\Enums\OrderStatus::READY, \App\Enums\OrderStatus::OUT_FOR_DELIVERY]),
                                        'bg-label-success' => $order->status === \App\Enums\OrderStatus::DELIVERED,
                                        'bg-label-danger'  => $order->status === \App\Enums\OrderStatus::CANCELLED,
                                    ])>{{ $order->readable_order_status }}</span>
                                </td>
                                <td>
                                    <span @class([
                                        'badge me-1',
                                        'bg-label-success' => $order->payment_status === \App\Enums\PaymentStatus::PAID,
                                        'bg-label-warning' => $order->payment_status === \App\Enums\PaymentStatus::PARTIALLY_PAID,
                                        'bg-label-danger'  => $order->payment_status === \App\Enums\PaymentStatus::UNPAID,
                                    ])>{{ $order->readable_payment_status }}</span>
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('Y/m/d') }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('orders.show', $order) }}">
                                                <i class="bx bx-show me-1"></i>عرض الفاتورة
                                            </a>
                                            <a class="dropdown-item" href="{{ route('orders.edit', $order) }}">
                                                <i class="bx bx-edit-alt me-1"></i>تعديل
                                            </a>
                                            @if(auth()->user()->isSuperAdmin())
                                                <form method="POST" action="{{ route('orders.destroy', $order) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="dropdown-item text-danger"
                                                        onclick="return confirm('هل تريد حذف هذا الطلب؟')"
                                                    >
                                                        <i class="bx bx-trash me-1"></i>حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-5" colspan="8">
                                    <i class="bx bxs-cart-download text-muted" style="font-size:3rem"></i>
                                    <p class="mt-2 text-muted fw-bold">لا يوجد طلبات لهذا العميل</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>
