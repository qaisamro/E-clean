<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">الموردون / فواتير الشراء /</span> فاتورة #{{ $bill->id }}
    </h4>

    @session('success-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 3000)" class="mb-3">
        <div class="alert alert-info alert-dismissible fade show text-dark" role="alert"><strong>{{ $value }}</strong></div>
    </div>
    @endsession
    @session('error-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 5000)" class="mb-3">
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert"><strong>{{ $value }}</strong></div>
    </div>
    @endsession

    <div class="card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-header">
                فاتورة شراء #{{ $bill->id }}
                @if($bill->bill_number)<span class="text-muted small"> — رقم المورد: {{ $bill->bill_number }}</span>@endif
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('supplier-payments.create', ['supplier' => $bill->supplier_id, 'bill' => $bill->id]) }}" class="btn btn-primary">
                    <i class="bx bx-money"></i> تسجيل دفعة
                </a>
                <a href="{{ route('suppliers.show', ['supplier' => $bill->supplier_id, 'tab' => 'statement']) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-receipt"></i> كشف حساب المورد
                </a>
                @if($bill->status !== 'paid' && $bill->payments->isEmpty())
                    <button class="btn btn-outline-danger" wire:click="deleteBill" onclick="return confirm('سيتم حذف الفاتورة وعكس الكميات من المخزن نهائيًا. متابعة؟')">
                        <i class="bx bx-trash"></i> حذف الفاتورة
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row gy-2 text-center">
                <div class="col-md-2 col-6">
                    <div class="small text-muted">المورد</div>
                    <div class="fw-bold">{{ $bill->supplier?->name }}</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="small text-muted">تاريخ الفاتورة</div>
                    <div class="fw-bold">{{ $bill->bill_date->format('Y-m-d') }}</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="small text-muted">تاريخ الاستحقاق</div>
                    <div class="fw-bold">{{ $bill->due_date?->format('Y-m-d') ?? '—' }}</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="small text-muted">الإجمالي</div>
                    <div class="fw-bold">{{ number_format((float) $bill->total, 2) }} ₪</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="small text-muted">المدفوع</div>
                    <div class="fw-bold">{{ number_format((float) $bill->paid, 2) }} ₪</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="small text-muted">المتبقي</div>
                    <div @class(['fw-bold', 'text-danger' => $bill->remaining > 0])>{{ number_format((float) $bill->remaining, 2) }} ₪</div>
                </div>
            </div>
            @if($bill->notes)
                <div class="small text-muted mt-3"><strong>ملاحظات:</strong> {{ $bill->notes }}</div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>النوع</th>
                    <th>البيان</th>
                    <th class="text-end">الكمية</th>
                    <th class="text-end">سعر الوحدة</th>
                    <th class="text-end">الإجمالي</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bill->lines as $i => $line)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($line->product_id)
                                <span class="badge bg-label-primary">صنف</span>
                            @else
                                <span class="badge bg-label-secondary">خدمة</span>
                            @endif
                        </td>
                        <td>
                            {{ $line->description }}
                            @if($line->product)
                                <span class="text-muted small">({{ $line->product->unit }})</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format((float) $line->quantity, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $line->unit_price, 2) }} ₪</td>
                        <td class="text-end fw-bold">{{ number_format((float) $line->total, 2) }} ₪</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold">الإجمالي</td>
                    <td class="text-end fw-bold fs-5">{{ number_format((float) $bill->total, 2) }} ₪</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($bill->payments->isNotEmpty())
    <div class="card">
        <h5 class="card-header">الدفعات على هذه الفاتورة</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>الدفعة</th>
                    <th>التاريخ</th>
                    <th class="text-end">المبلغ</th>
                    <th>الطريقة</th>
                    <th>ملاحظات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bill->payments as $payment)
                    <tr>
                        <td>#{{ $payment->id }}</td>
                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td class="text-end fw-bold">{{ number_format((float) $payment->amount, 2) }} ₪</td>
                        <td>{{ $payment->methodLabel() }}</td>
                        <td class="text-muted small">{{ $payment->notes }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
