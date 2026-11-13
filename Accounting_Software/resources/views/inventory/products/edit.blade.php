<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">المخزن /</span> تعديل المنتج</h4>

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

    <form wire:submit="save">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <h5 class="card-header">بيانات المنتج</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label" for="name">اسم المنتج *</label>
                                <input type="text" id="name" class="form-control" wire:model="name">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="sku">SKU / باركود</label>
                                <input type="text" id="sku" class="form-control" wire:model="sku" placeholder="اختياري">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kind">النوع *</label>
                                <select id="kind" class="form-select" wire:model="kind">
                                    @foreach ($kinds as $kind)
                                        <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="unit">الوحدة *</label>
                                <input type="text" id="unit" class="form-control" wire:model="unit" list="unit-list">
                                <datalist id="unit-list">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="inventory_category_id">الفئة
                                    <a href="{{ route('inventory.categories.index') }}" class="text-primary small ms-1">إدارة الفئات</a>
                                </label>
                                <select id="inventory_category_id" class="form-select" wire:model="inventory_category_id">
                                    <option value="">بدون فئة</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}@if(! $category->is_active) (موقوفة)@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="new_category">أو أضف فئة جديدة</label>
                                <input type="text" id="new_category" class="form-control" wire:model="new_category" placeholder="اختياري">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="purchase_price">سعر الشراء</label>
                                <input type="number" step="0.01" min="0" id="purchase_price" class="form-control" wire:model="purchase_price" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="sale_price">سعر البيع</label>
                                <input type="number" step="0.01" min="0" id="sale_price" class="form-control" wire:model="sale_price" placeholder="0.00">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="notes">ملاحظات</label>
                                <textarea id="notes" class="form-control" rows="2" wire:model="notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">الأرصدة والحالة</h5>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">الكمية</label>
                            <div class="form-control-plaintext fw-bold fs-5">{{ number_format((float) $product->current_stock, 2) }} {{ $product->unit }}</div>
                            <a href="{{ route('inventory.movements.create') }}" class="btn btn-outline-primary btn-sm mt-1"><i class="bx bx-transfer-alt me-1"></i> تسجيل حركة مخزن (وارد / شراء / جرد)</a>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="min_stock_alert">حد التنبيه للنقص *</label>
                            <input type="number" step="0.01" min="0" id="min_stock_alert" class="form-control" wire:model="min_stock_alert">
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" class="form-check-input" role="switch" id="is_active" wire:model="is_active">
                            <label class="form-check-label" for="is_active">المنتج نشط (يظهر في الكاشير والقوائم)</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
