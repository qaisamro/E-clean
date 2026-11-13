<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-transfer"></i> المخزن</div>
                <h1 class="ec-section-title">حركات المخزن</h1>
            </div>
            @if(auth()->user()->hasPermission('inventory.manage'))
                <a href="{{ route('inventory.movements.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> حركة جديدة
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

    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-header">الحركات</h5>
            <a href="{{ route('inventory.movements.create') }}" class="btn btn-primary me-3">حركة جديدة</a>
        </div>

        <div class="mb-4 px-3 row align-items-center">
            <div class="col-md-3">
                <label for="search" class="form-label">بحث</label>
                <input id="search" class="form-control mt-1" wire:model.live.debounce.500ms="search" placeholder="منتج / مرجع / ملاحظات">
            </div>
            <div class="col-md-2">
                <label for="type" class="form-label">نوع الحركة</label>
                <select id="type" class="form-select mt-1" wire:model.live="type">
                    <option value="">الكل</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="kind" class="form-label">نوع المنتج</label>
                <select id="kind" class="form-select mt-1" wire:model.live="kind">
                    <option value="">الكل</option>
                    @foreach ($kinds as $kind)
                        <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="fromDate" class="form-label">من تاريخ</label>
                <input type="date" id="fromDate" class="form-control mt-1" wire:model.live="fromDate">
            </div>
            <div class="col-md-2">
                <label for="toDate" class="form-label">إلى تاريخ</label>
                <input type="date" id="toDate" class="form-control mt-1" wire:model.live="toDate">
            </div>
            <div class="col-md-1">
                <button class="btn btn-link mt-4" wire:click="clearFilters">مسح</button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الكيان</th>
                    <th>النوع</th>
                    <th>الكمية</th>
                    <th>الرصيد بعدها</th>
                    <th>المرجع</th>
                    <th>المستخدم</th>
                    <th>ملاحظات</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @forelse($movements as $movement)
                    <tr wire:key="{{ $movement->id }}">
                        <td>{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ optional($movement->stockable)->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $movement->type->isDeduction() ? 'bg-danger' : 'bg-success' }}">{{ $movement->type->label() }}</span>
                        </td>
                        <td dir="ltr" class="text-end {{ $movement->type->isDeduction() ? 'text-danger' : 'text-success' }}">{{ $movement->type->isDeduction() ? '-' : '+' }}{{ number_format((float) $movement->quantity, 2) }}</td>
                        <td>{{ number_format((float) $movement->balance_after, 2) }}</td>
                        <td>{{ $movement->reference ?? '—' }}</td>
                        <td>{{ optional($movement->user)->name ?? 'نظام' }}</td>
                        <td>{{ $movement->notes ? Str::limit($movement->notes, 40) : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">لا توجد حركات بهذه الفلاتر</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3">{{ $movements->links() }}</div>
    </div>
</div>
