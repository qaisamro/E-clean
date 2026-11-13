<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-transfer"></i> المخزن</div>
                <h1 class="ec-section-title">حركة مخزن جديدة</h1>
            </div>
            <a href="{{ route('inventory.movements.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> حركات المخزن
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

    @session('error-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 4000)" class="mb-3">
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
            <strong>{{ $value }}</strong>
        </div>
    </div>
    @endsession

    <form wire:submit="save">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <h5 class="card-header">تفاصيل الحركة</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kind">تصفية المنتجات (اختياري)</label>
                                <select id="kind" class="form-select" wire:model.live="kind">
                                    <option value="">الكل</option>
                                    @foreach ($kinds as $kind)
                                        <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="product_id">المنتج *</label>
                                <select id="product_id" class="form-select" wire:model="product_id">
                                    <option value="">اختر...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} — الرصيد: {{ number_format((float) $product->current_stock, 2) }} {{ $product->unit }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type">نوع الحركة *</label>
                                <select id="type" class="form-select" wire:model="type">
                                    @foreach ($types as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="quantity">الكمية *</label>
                                <input type="number" step="0.01" min="0.01" id="quantity" class="form-control" wire:model="quantity">
                                <div class="form-text">في حركات الصرف (بيع/تالف/استهلاك/جرد صارف/مرتجع مورد) لا يمكن أن تتجاوز الكمية المتوفر.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="reference">المرجع</label>
                                <input type="text" id="reference" class="form-control" wire:model="reference" placeholder="رقم فاتورة/أمر توريد/طلب... (اختياري)">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="notes">ملاحظات</label>
                                <textarea id="notes" class="form-control" rows="2" wire:model="notes" placeholder="ملاحظات اختيارية"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">حفظ الحركة</h5>
                    <div class="card-body">
                        <p class="text-muted">سيتم تسجيل الحركة مع «الرصيد بعدها» وتحديث رصيد المنتج تلقائياً. الحركات فدائية (غير قابلة للحذف) — استخدم حركة معاكسة للتصحيح.</p>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ الحركة</button>
                            <a href="{{ route('inventory.movements.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
