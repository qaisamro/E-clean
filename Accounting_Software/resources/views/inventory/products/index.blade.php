<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-package"></i> المخزن</div>
                <h1 class="ec-section-title">المنتجات</h1>
            </div>
            @if(auth()->user()->hasPermission('inventory.manage'))
                <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> إضافة منتج
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
    <div x-data x-init="setTimeout(() => $el.remove(), 3000)" class="mb-3">
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
            <strong>{{ $value }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endsession

    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-header">المنتجات</h5>
            <a href="{{ route('inventory.products.create') }}" class="btn btn-primary me-3">إضافة منتج</a>
        </div>

        <div class="mb-4 px-3 row align-items-center">
            <div class="col-md-3">
                <label for="search" class="form-label">بحث
                    <i class='ms-2 bx bx-loader-circle bx-spin' wire:loading wire:target="search"></i>
                </label>
                <input id="search" class="form-control mt-1" wire:model.live.debounce.500ms="search" placeholder="اسم المنتج أو الباركود (SKU)">
            </div>
            <div class="col-md-2">
                <label for="kind" class="form-label">النوع</label>
                <select id="kind" class="form-select mt-1" wire:model.live="kind">
                    <option value="">الكل</option>
                    @foreach ($kinds as $kind)
                        <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="isActive" class="form-label">الحالة</label>
                <select id="isActive" class="form-select mt-1" wire:model.live="isActive">
                    <option value="">الكل</option>
                    <option value="1">نشط</option>
                    <option value="0">موقوف</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end pb-2">
                <label class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" wire:model.live="lowStockOnly">
                    <span class="form-check-label">النواقص فقط</span>
                </label>
            </div>
            <div class="col-md-2">
                <button class="btn btn-link mt-4" wire:click="clearFilters">إلغاء الفلاتر</button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>المنتج</th>
                    <th>النوع</th>
                    <th>الفئة</th>
                    <th>الوحدة</th>
                    <th>الكمية</th>
                    <th>حد التنبيه</th>
                    <th>سعر البيع</th>
                    <th>الحالة</th>
                    <th>التحكم</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @forelse($products as $product)
                    <tr wire:key="{{ $product->id }}" @class(['table-warning' => ! $product->is_empty && $product->is_low_stock])>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->sku)<br><small class="text-muted">SKU: {{ $product->sku }}</small>@endif
                        </td>
                        <td>{{ \App\Enums\ProductKind::from($product->kind)->label() }}</td>
                        <td>{{ optional($product->category)->name ?? '—' }}</td>
                        <td>{{ $product->unit }}</td>
                        <td><span class="fw-bold">{{ number_format((float) $product->current_stock, 2) }}</span></td>
                        <td>{{ number_format((float) $product->min_stock_alert, 2) }}</td>
                        <td>{{ $product->sale_price !== null ? number_format((float) $product->sale_price, 2) : '—' }}</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">موقوف</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class='bx bx-edit'></i></a>
                            <button class="btn btn-sm btn-outline-secondary" wire:click="toggleActive({{ $product->id }})"><i class="bx bx-power-off"></i></button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="deleteProduct({{ $product->id }})" onclick="return confirm('حذف المنتج نهائياً؟')"><i class='bx bx-trash'></i></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">لا توجد منتجات — أضف أول منتج من «إضافة منتج»</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 pb-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
