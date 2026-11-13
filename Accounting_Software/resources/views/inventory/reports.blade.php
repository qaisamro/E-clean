<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-bar-chart-alt-2"></i> المخزن</div>
                <h1 class="ec-section-title">التقارير</h1>
            </div>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bx bx-printer me-1"></i> طباعة
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small text-muted" for="fromDate">من</label>
                    <input type="date" id="fromDate" class="form-control" wire:model.live="fromDate">
                </div>
                <div class="col-auto">
                    <label class="form-label small text-muted" for="toDate">إلى</label>
                    <input type="date" id="toDate" class="form-control" wire:model.live="toDate">
                </div>
            </div>
            <button class="btn btn-outline-primary" onclick="window.print()"><i class="bx bx-printer me-1"></i> طباعة</button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted">قيمة مخزون المنتجات</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($productValue, 2) }} <small class="fs-6 text-muted">₪</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted">منتجات منخفضة المخزون</h6>
                    <h3 class="fw-bold mb-0 text-danger">{{ $shortageCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted">حركات مسجلة (الفترة)</h6>
                    <h3 class="fw-bold mb-0">{{ $movements->sum('total') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">حركة المخزن حسب النوع ({{ $fromDate }} ← {{ $toDate }})</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>نوع الحركة</th>
                    <th>عدد الحركات</th>
                    <th>إجمالي الكمية</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($types as $type)
                    @php $row = $movements->get($type->value); @endphp
                    <tr>
                        <td><span class="badge {{ $type->isDeduction() ? 'bg-danger' : 'bg-success' }}">{{ $type->label() }}</span></td>
                        <td>{{ $row->total ?? 0 }}</td>
                        <td>{{ number_format((float) ($row->total_qty ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">حركة المنتجات (شراء/بيع) — الفترة</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>المنتج</th>
                    <th>النوع</th>
                    <th>الرصيد</th>
                    <th>المشترى</th>
                    <th>المباع</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    @if ((float) ($product->purchased ?? 0) > 0 || (float) ($product->sold ?? 0) > 0 || (float) $product->current_stock > 0)
                        <tr>
                            <td>{{ $product->name }} @if($product->sku)<small class="text-muted d-block">SKU: {{ $product->sku }}</small>@endif</td>
                            <td>{{ \App\Enums\ProductKind::from($product->kind)->label() }}</td>
                            <td>{{ number_format((float) $product->current_stock, 2) }} {{ $product->unit }}</td>
                            <td>{{ number_format((float) ($product->purchased ?? 0), 2) }}</td>
                            <td>{{ number_format((float) ($product->sold ?? 0), 2) }}</td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">لا توجد حركة في هذه الفترة</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
