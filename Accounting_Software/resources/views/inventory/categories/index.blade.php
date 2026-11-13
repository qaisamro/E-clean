<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-category"></i> المخزن</div>
                <h1 class="ec-section-title">إدارة الفئات</h1>
            </div>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> المنتجات
            </a>
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
            <strong>تحقق من الحقول:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Create / Edit inline card --}}
    @if ($creating || $editingId)
        <div class="card mb-4">
            <h5 class="card-header">{{ $editingId ? 'تعديل الفئة' : 'إضافة فئة جديدة' }}</h5>
            <div class="card-body">
                <form wire:submit="{{ $editingId ? 'update' : 'store' }}" class="row g-2">
                    <div class="col-md-10">
                        <input type="text" class="form-control" autofocus
                               wire:model="{{ $editingId ? 'editingName' : 'name' }}"
                               placeholder="اسم الفئة (مثل: منظفات / أقمشة / إلكترونيات)">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="{{ $editingId ? 'cancelEditing' : 'cancelCreating' }}">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" wire:click="startCreating">
                <i class='bx bx-plus me-1'></i> إضافة فئة
            </button>
        </div>
    @endif

    <div class="card">
        <div class="mb-4 px-3 pt-3 row align-items-center">
            <div class="col-md-5">
                <label class="form-label" for="search">بحث في الفئات</label>
                <input id="search" class="form-control mt-1" wire:model.live.debounce.400ms="search" placeholder="اكتب اسم الفئة...">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filter">الحالة</label>
                <select id="filter" class="form-select mt-1" wire:model.live="filter">
                    <option value="">الكل</option>
                    <option value="active">مفعلة</option>
                    <option value="inactive">موقفة</option>
                </select>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 90px;">الترتيب</th>
                        <th>اسم الفئة</th>
                        <th class="text-center">المنتجات</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @forelse($categories as $category)
                    <tr wire:key="{{ $category->id }}" @class(['opacity-75' => ! $category->is_active])>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-secondary" wire:click="moveUp({{ $category->id }})" title="نقل للأعلى">
                                    <i class='bx bx-up-arrow-alt'></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" wire:click="moveDown({{ $category->id }})" title="نقل للأسفل">
                                    <i class='bx bx-down-arrow-alt'></i>
                                </button>
                                <span class="badge bg-light text-dark ms-1">{{ $category->sort_order }}</span>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            @if(! $category->is_active)
                                <div class="small text-muted">لا تظهر في قائمة إضافة منتج</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $category->products_count > 0 ? 'primary' : 'secondary' }}">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}"
                                    wire:click="toggleActive({{ $category->id }})"
                                    title="{{ $category->is_active ? 'إيقاف' : 'تفعيل' }}">
                                {{ $category->is_active ? 'مفعلة' : 'موقفة' }}
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" wire:click="startEditing({{ $category->id }})" title="تعديل">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('حذف الفئة؟\n(المنتجات المرتبطة ستتحول إلى «بدون فئة» — لن تُحذف)')"
                                    wire:click="delete({{ $category->id }})" title="حذف">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">لا توجد فئات — أضف أول فئة الآن</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3">
            {{ $categories->links() }}
        </div>
    </div>
</div>
