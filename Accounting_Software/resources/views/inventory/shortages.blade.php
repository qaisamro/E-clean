<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-error-circle"></i> المخزن</div>
                <h1 class="ec-section-title">نواقص المخزون</h1>
            </div>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> المنتجات
            </a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            المنتجات عند أو دون حد التنبيه
            <span class="badge bg-label-warning">{{ $products->count() }}</span>
        </h5>
        <div class="px-4 pb-2 text-muted small">
            تظهر المنتجات التجارية والمواد عند نفاد الرصيد، أو عندما يصبح الرصيد الحالي أقل من أو يساوي «حد التنبيه». 
            أما أصناف الغسيل فلا تظهر إلا إذا حُدد لها حد تنبيه أكبر من صفر.
            الكمية تُدخل كرصد افتتاحي عند إضافة المنتج، ثم تُعدّل فقط من «حركات المخزن» لضمان وجود سجل تدقيق.
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>المنتج</th>
                    <th>النوع</th>
                    <th>الوحدة</th>
                    <th>الرصيد</th>
                    <th>حد التنبيه</th>
                    <th>حالة</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr wire:key="{{ $product->id }}">
                        <td>{{ $product->name }} @if($product->sku)<small class="text-muted d-block">SKU: {{ $product->sku }}</small>@endif</td>
                        <td>{{ \App\Enums\ProductKind::from($product->kind)->label() }}</td>
                        <td>{{ $product->unit }}</td>
                        <td class="fw-bold">{{ number_format((float) $product->current_stock, 2) }}</td>
                        <td>{{ number_format((float) $product->min_stock_alert, 2) }}</td>
                        <td>
                            @if($product->is_empty)
                                <span class="badge bg-label-warning">نافذ</span>
                            @else
                                <span class="badge bg-warning text-dark">على وشك النفاذ</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">لا توجد منتجات نواقص — كل المنتجات بمستويات مريحة ✓</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
