<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-truck"></i> الموردون</div>
                <h1 class="ec-section-title">قائمة الموردين</h1>
            </div>
            @if(auth()->user()->hasPermission('suppliers.manage'))
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> إضافة مورد
                </a>
            @endif
        </div>
    </div>

    @session('success-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 3000)" class="mb-3">
        <div class="alert alert-info alert-dismissible fade show text-dark" role="alert">
            <strong>{{ $value }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endsession
    @session('error-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 4000)" class="mb-3">
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
            <strong>{{ $value }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endsession

    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="card-header">الموردون</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('supplier-payments.index') }}" class="btn btn-outline-secondary">الدفعات</a>
                <a href="{{ route('supplier-bills.index') }}" class="btn btn-outline-secondary">فواتير الشراء</a>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary me-3">إضافة مورد</a>
            </div>
        </div>

        <div class="mb-4 px-3 row align-items-center">
            <div class="col-md-4">
                <label for="search" class="form-label">بحث
                    <i class='ms-2 bx bx-loader-circle bx-spin' wire:loading wire:target="search"></i>
                </label>
                <input id="search" class="form-control mt-1" wire:model.live.debounce.500ms="search" placeholder="اسم المورد أو رقم الهاتف">
            </div>
            <div class="col-md-3">
                <label for="active" class="form-label">الحالة</label>
                <select id="active" class="form-select mt-1" wire:model.live="active">
                    <option value="">الكل</option>
                    <option value="1">فعال</option>
                    <option value="0">غير فعال</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end pb-2">
                <label class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" wire:model.live="balanceOnly">
                    <span class="form-check-label">عليه متبقي (دين فقط)</span>
                </label>
            </div>
            <div class="col-md-2">
                <button class="btn btn-link mt-4" wire:click="clearFilters">مسح الفلاتر</button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped align-middle">
                <thead>
                <tr>
                    <th>المورد</th>
                    <th>الهاتف</th>
                    <th>عدد الفواتير</th>
                    <th>إجمالي الفواتير</th>
                    <th>المتبقي</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @forelse($suppliers as $supplier)
                    @php
                        $balance = (float) $supplier->opening_balance + (float) ($supplier->bills_total ?? 0) - (float) ($supplier->payments_total ?? 0);
                    @endphp
                    <tr wire:key="{{ $supplier->id }}">
                        <td>
                            <strong>{{ $supplier->name }}</strong>
                            @if($supplier->address)<div class="small text-muted">{{ $supplier->address }}</div>@endif
                        </td>
                        <td>{{ $supplier->phone ?? '—' }}</td>
                        <td>{{ $supplier->bills_count }}</td>
                        <td>{{ number_format((float) ($supplier->bills_total ?? 0), 2) }} ₪</td>
                        <td @class(['fw-bold', 'text-danger' => $balance > 0.0001])>
                            {{ number_format(max(0, $balance), 2) }} ₪
                        </td>
                        <td>
                            <span @class(['badge', 'bg-label-success' => $supplier->is_active, 'bg-label-secondary' => ! $supplier->is_active])>
                                {{ $supplier->is_active ? 'فعال' : 'معطل' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-primary" title="كشف حساب تفصيلي">
                                    <i class="bx bx-receipt"></i> كشف حساب
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-{{ $supplier->is_active ? 'warning' : 'success' }}" wire:click="toggleActive({{ $supplier->id }})" title="تبديل الحالة">
                                    <i class="bx bx-{{ $supplier->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteSupplier({{ $supplier->id }})" onclick="return confirm('سيتم حذف المورد نهائيًا (فقط إن لم يكن عليه فواتير أو دفعات). متابعة؟')" title="حذف">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد موردون بعد — ابدأ بإضافة أول مورد</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 px-2">{{ $suppliers->links() }}</div>
    </div>
</div>
