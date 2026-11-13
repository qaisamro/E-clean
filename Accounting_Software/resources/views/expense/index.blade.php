<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bxs-wallet"></i> المصروفات</div>
                <h1 class="ec-section-title">قائمة المصروفات</h1>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>إضافة مصروف
            </a>
        </div>
    </div>

    <div class="card ec-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row gap-3">
                <h5 class="mb-0 fw-bold"><i class="bx bxs-dollar-circle text-teal me-2"></i>إجمالي المصروفات</h5>
                <div x-show="$wire.search || $wire.type || $wire.from || $wire.to" x-transition x-cloak class="d-flex flex-column text-center text-md-end">
                    <span class="text-muted small fw-semibold">المجموع</span>
                    <span class="fw-bold fs-4 text-teal-soft">{{ number_format((float) $total, 2) }} ₪</span>
                </div>
            </div>

            <div class="row gy-2 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label fw-semibold">
                        بحث
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="search"></i>
                    </label>
                    <input id="search" class="form-control" wire:model.live.debounce.500ms="search" placeholder="أدخل اسم المصروف"/>
                </div>
                <div class="col-md-3">
                    <label for="expense-type" class="form-label fw-semibold">
                        نوع المصروف
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="type"></i>
                    </label>
                    <select id="expense-type" class="form-select" wire:model.live="type">
                        <option value>الكل</option>
                        @foreach(\App\Enums\ExpensesType::cases() as $expenseType)
                            <option wire:key="{{ 'Type-'. $expenseType->value }}" value="{{ $expenseType->value }}">
                                {{ $expenseType->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="expense-from" class="form-label fw-semibold">
                        من
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="from"></i>
                    </label>
                    <input type="date" id="expense-from" class="form-control" wire:model.live="from"/>
                </div>
                <div class="col-md-3">
                    <label for="expense-to" class="form-label fw-semibold">
                        إلى
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="to"></i>
                    </label>
                    <input type="date" id="expense-to" class="form-control" wire:model.live="to"/>
                </div>
            </div>

            <div x-show="$wire.search || $wire.type || $wire.from || $wire.to" x-transition x-cloak class="mt-3">
                <button class="btn btn-outline-secondary btn-sm" wire:click="clearFilters"><i class="bx bx-x me-1"></i>إلغاء الفلاتر</button>
            </div>

            <div class="table-responsive text-nowrap mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th class="text-end">القيمة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>
                                    <a href="{{ route('expenses.show', $expense) }}">
                                        <strong>{{ $expense->name }}</strong>
                                    </a>
                                </td>
                                <td>{{ $expense->type->label() }}</td>
                                <td class="text-end fw-bold">{{ number_format((float) $expense->value, 2) }} ₪</td>
                                <td>{{ $expense->created_at->diffForHumans() }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('expenses.edit', $expense) }}">
                                                <i class="bx bx-edit-alt me-1"></i>تعديل
                                            </a>
                                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                                @csrf
                                                @method('DELETE')
                                                <a class="dropdown-item text-danger" href="{{ route('expenses.destroy', $expense) }}"
                                                   style="text-align:start"
                                                   onclick="event.preventDefault(); if (confirm('هل تريد حذف المصروف من النظام ؟')) { this.closest('form').submit(); }">
                                                    <i class="bx bx-trash me-1"></i>حذف
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-4" colspan="5">
                                    <div class="ec-empty-icon mx-auto" style="max-width:300px;">
                                        <i class="bx bx-inbox d-block fs-3 text-teal"></i>
                                        <span>لا يوجد بيانات</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
