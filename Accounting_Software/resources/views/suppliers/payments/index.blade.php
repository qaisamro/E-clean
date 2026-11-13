<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-credit-card"></i> الموردون — الدفعات</div>
                <h1 class="ec-section-title">الدفعات</h1>
            </div>
            @if(auth()->user()->hasPermission('suppliers.manage'))
                <a href="{{ route('supplier-payments.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> تسجيل دفعة
                </a>
            @endif
        </div>
    </div>

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

    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="card-header">الدفعات</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('supplier-bills.index') }}" class="btn btn-outline-secondary">فواتير الشراء</a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">قائمة الموردين</a>
                <a href="{{ route('supplier-payments.create') }}" class="btn btn-primary me-3">تسجيل دفعة</a>
            </div>
        </div>

        <div class="mb-4 px-3 row align-items-center">
            <div class="col-md-5">
                <label for="search" class="form-label">بحث <i class='ms-2 bx bx-loader-circle bx-spin' wire:loading wire:target="search"></i></label>
                <input id="search" class="form-control mt-1" wire:model.live.debounce.500ms="search" placeholder="اسم المورد أو ملاحظة">
            </div>
            <div class="col-md-4">
                <label for="supplier_id" class="form-label">المورد</label>
                <select id="supplier_id" class="form-select mt-1" wire:model.live="supplier_id">
                    <option value="">الكل</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-link mt-4" wire:click="clearFilters">مسح الفلاتر</button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped align-middle">
                <thead>
                <tr>
                    <th>الدفعة</th>
                    <th>المورد</th>
                    <th>التاريخ</th>
                    <th class="text-end">المبلغ</th>
                    <th>الطريقة</th>
                    <th>لفاتورة</th>
                    <th>ملاحظات</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr wire:key="{{ $payment->id }}">
                        <td><strong>#{{ $payment->id }}</strong></td>
                        <td>{{ $payment->supplier?->name }}</td>
                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td class="text-end fw-bold">{{ number_format((float) $payment->amount, 2) }} ₪</td>
                        <td>{{ $payment->methodLabel() }}</td>
                        <td>@if($payment->bill_id) <a href="{{ route('supplier-bills.show', $payment->bill_id) }}">#{{ $payment->bill_id }}</a>@else — الحساب @endif</td>
                        <td class="text-muted small">{{ $payment->notes }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" wire:click="deletePayment({{ $payment->id }})" onclick="return confirm('حذف هذه الدفعة؟ سيُعاد احتساب المتبقي على الفاتورة.')" title="حذف">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">لا توجد دفعات — سجّل أول دفعة</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 px-2">{{ $payments->links() }}</div>
    </div>
</div>
