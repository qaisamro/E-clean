<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bxs-id-card"></i> المرتبات</div>
                <h1 class="ec-section-title">قائمة المرتبات</h1>
            </div>
            <a href="{{ route('salaries.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>إضافة مرتب
            </a>
        </div>
    </div>

    <div class="card ec-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row gap-3">
                <h5 class="mb-0 fw-bold"><i class="bx bxs-building text-teal me-2"></i>إجمالي المرتبات</h5>
                <div class="d-flex flex-column text-center text-md-end">
                    <span class="text-muted small fw-semibold">المجموع</span>
                    <span class="fw-bold fs-4 text-teal-soft">{{ number_format((float) $total, 2) }} ₪</span>
                </div>
            </div>

            <div class="mb-3 row gy-2 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label fw-semibold">
                        بحث
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="search"></i>
                    </label>
                    <input id="search" class="form-control" wire:model.live.debounce.500ms="search" placeholder="اسم المرتب" />
                </div>
                <div class="col-md-4">
                    <label for="salary-from" class="form-label fw-semibold">
                        من
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="from"></i>
                    </label>
                    <input type="date" id="salary-from" class="form-control" wire:model.live="from" />
                </div>
                <div class="col-md-4">
                    <label for="salary-to" class="form-label fw-semibold">
                        إلى
                        <i class="ms-1 bx bx-loader-circle bx-spin" wire:loading wire:target="to"></i>
                    </label>
                    <input type="date" id="salary-to" class="form-control" wire:model.live="to" />
                </div>
            </div>

            <div x-show="$wire.search || $wire.from || $wire.to" x-transition x-cloak class="mb-3">
                <button class="btn btn-outline-secondary btn-sm" wire:click="clearFilters"><i class="bx bx-x me-1"></i>إلغاء الفلاتر</button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>اسم الموظف</th>
                            <th class="text-end">المرتب</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                            <tr>
                                <td>
                                    <a href="{{ route('salaries.show', $salary) }}">
                                        <strong>{{ $salary->user->name }}</strong>
                                    </a>
                                </td>
                                <td class="text-end fw-bold">{{ number_format((float) $salary->value, 2) }} ₪</td>
                                <td>{{ $salary->created_at->diffForHumans() }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('salaries.edit', $salary) }}">
                                                <i class="bx bx-edit-alt me-1"></i>تعديل
                                            </a>
                                            <form method="POST" action="{{ route('salaries.destroy', $salary) }}">
                                                @csrf
                                                @method('DELETE')
                                                <a class="dropdown-item text-danger" href="{{ route('salaries.destroy', $salary) }}"
                                                   style="text-align:start"
                                                   onclick="event.preventDefault(); if (confirm('هل تريد حذف المرتب من النظام ؟')) { this.closest('form').submit(); }">
                                                    <i class="bx bx-trash me-1"></i>حذف
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-4" colspan="4">
                                    <div class="ec-empty-icon mx-auto" style="max-width:300px;">
                                        <i class="bx bx-inbox d-block fs-3 text-teal"></i>
                                        <span>لا يوجد بيانات</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                {{ $salaries->links() }}
            </div>
        </div>
    </div>
</div>
