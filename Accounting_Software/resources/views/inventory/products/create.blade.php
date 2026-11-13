<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-package"></i> المخزن</div>
                <h1 class="ec-section-title">إضافة منتج</h1>
            </div>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> المنتجات
            </a>
        </div>
    </div>

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
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="مثال: مسحوق غسيل فانيش 2 كجم">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="sku">SKU / باركود</label>
                                <input type="text" id="sku" class="form-control @error('sku') is-invalid @enderror" wire:model="sku" placeholder="اختياري">
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                <input type="text" id="unit" class="form-control" wire:model="unit" list="unit-list" placeholder="قطعة">
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
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="new_category">أو أضف فئة جديدة</label>
                                <input type="text" id="new_category" class="form-control" wire:model="new_category" placeholder="اسم فئة جديدة (اختياري)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="purchase_price">سعر الشراء</label>
                                <input type="number" step="0.01" min="0" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" wire:model="purchase_price" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="sale_price">سعر البيع</label>
                                <input type="number" step="0.01" min="0" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" wire:model="sale_price" placeholder="0.00">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="notes">ملاحظات</label>
                                <textarea id="notes" class="form-control" rows="2" wire:model="notes" placeholder="ملاحظات اختيارية"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">الأرصدة</h5>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="current_stock">الكمية</label>
                            <input type="number" step="0.01" min="0" id="current_stock" class="form-control @error('current_stock') is-invalid @enderror" wire:model="current_stock">
                            <div class="form-text">سيُسجل تلقائياً كحركة «رصيد افتتاحي». بعد الحفظ، عدّل الكمية من «حركات المخزن» فقط.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="min_stock_alert">حد التنبيه للنقص *</label>
                            <input type="number" step="0.01" min="0" id="min_stock_alert" class="form-control @error('min_stock_alert') is-invalid @enderror" wire:model="min_stock_alert">
                            <div class="form-text">سيظهر في «النواقص» عندما يصل الرصيد لهذا الحد أو أقل.</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ المنتج</button>
                            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
