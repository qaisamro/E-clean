<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-receipt"></i> الموردون — فواتير الشراء</div>
                <h1 class="ec-section-title">فواتير الشراء</h1>
            </div>
            @if(auth()->user()->hasPermission('suppliers.manage'))
                <a href="{{ route('supplier-bills.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> فاتورة شراء جديدة
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
            <h5 class="card-header">الفواتير</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('supplier-payments.index') }}" class="btn btn-outline-secondary">الدفعات</a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">قائمة الموردين</a>
                <a href="{{ route('supplier-bills.create') }}" class="btn btn-primary me-3">إضافة فاتورة</a>
            </div>
        </div>

        <div class="mb-4 px-3 row align-items-center">
            <div class="col-md-4">
                <label for="search" class="form-label">بحث <i class='ms-2 bx bx-loader-circle bx-spin' wire:loading wire:target="search"></i></label>
                <input id="search" class="form-control mt-1" wire:model.live.debounce.500ms="search" placeholder="رقم فاتورة المورد أو اسم المورد أو #رقم">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">الحالة</label>
                <select id="status" class="form-select mt-1" wire:model.live="status">
                    <option value="">الكل</option>
                    <option value="unpaid">غير مسددة</option>
                    <option value="partial">جزئية</option>
                    <option value="paid">مسددة</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="supplier_id" class="form-label">المورد</label>
                <select id="supplier_id" class="form-select mt-1" wire:model.live="supplier_id">
                    <option value="">الكل</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-link mt-4" wire:click="clearFilters">مسح الفلاتر</button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped align-middle">
                <thead>
                <tr>
                    <th>الفاتورة</th>
                    <th>المورد</th>
                    <th>التاريخ</th>
                    <th class="text-end">الإجمالي</th>
                    <th class="text-end">المدفوع</th>
                    <th class="text-end">المتبقي</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($bills as $bill)
                    <tr wire:key="{{ $bill->id }}">
                        <td><strong>#{{ $bill->id }}</strong>@if($bill->bill_number)<div class="small text-muted">{{ $bill->bill_number }}</div>@endif</td>
                        <td>{{ $bill->supplier?->name }}</td>
                        <td>{{ $bill->bill_date->format('Y-m-d') }}</td>
                        <td class="text-end">{{ number_format((float) $bill->total, 2) }} ₪</td>
                        <td class="text-end">{{ number_format((float) $bill->paid, 2) }} ₪</td>
                        <td class="text-end fw-bold">{{ number_format((float) $bill->remaining, 2) }} ₪</td>
                        <td>
                            <span @class(['badge', 'bg-label-danger' => $bill->status === 'unpaid', 'bg-label-warning' => $bill->status === 'partial', 'bg-label-success' => $bill->status === 'paid'])>
                                {{ $bill->status === 'paid' ? 'مسددة' : ($bill->status === 'partial' ? 'جزئية' : 'غير مسددة') }}
                            </span>
                        </td>
                        <td><a href="{{ route('supplier-bills.show', $bill) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">لا توجد فواتير — أضف أول فاتورة شراء</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 px-2">{{ $bills->links() }}</div>
    </div>
</div>
