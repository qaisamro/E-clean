<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-truck"></i> الموردون</div>
                <h1 class="ec-section-title">{{ $title }}</h1>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> قائمة الموردين
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">بيانات المورد</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="name">اسم المورد *</label>
                                <input type="text" id="name" class="form-control" wire:model="name" placeholder="مثال: شركة يوني فات للمواد الكيميائية">
                                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="phone">رقم الهاتف</label>
                                <input type="text" id="phone" class="form-control" wire:model="phone" placeholder="05xxxxxxxx">
                                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="address">العنوان</label>
                                <input type="text" id="address" class="form-control" wire:model="address" placeholder="المدينة - المنطقة - اسم الشارع">
                                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="opening_balance">الرصيد الافتتاحي (المتبقي علينا سابقًا)</label>
                                <input type="number" step="0.01" id="opening_balance" class="form-control" wire:model="opening_balance">
                                <div class="form-text">هذا هو المتبقي علينا (أو له علينا لو بالسالب) قبل إدخال أي فواتير أو دفعات في البرنامج.</div>
                                @error('opening_balance')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <label class="form-check ms-2 mb-2">
                                    <input type="checkbox" class="form-check-input" wire:model="is_active" id="is_active">
                                    <span class="form-check-label">المورد فعال</span>
                                </label>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="notes">ملاحظات</label>
                                <textarea id="notes" class="form-control" rows="3" wire:model="notes" placeholder="شروط الدفع، مواعيد التوريد، مسؤول المبيعات..."></textarea>
                                @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">حفظ المورد</button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <h5 class="card-header">ما هذا القسم؟</h5>
                <div class="card-body text-muted">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">• سجّل الموردين مرة واحدة بقدر البيانات الكاملة (الاسم، الهاتف، العنوان، ملاحظات).</li>
                        <li class="mb-2">• كل فاتورة شراء من المورد تتحكم في المخزن تلقائيًا (إضافة الكميات تلقائيًا بالكميات).</li>
                        <li class="mb-2">• الكشف التفصيلي يعرض كل فواتيرك ومدفوعاتك والرصيد الجاري لحظة الحساب.</li>
                        <li>• يمكن طباعة الكشف أو حفظه PDF من زر «طباعة / حفظ PDF» داخل كشف الحساب.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
