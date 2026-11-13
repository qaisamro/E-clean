<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-file"></i> الموردون — فواتير الشراء</div>
                <h1 class="ec-section-title">فاتورة شراء جديدة</h1>
            </div>
            <a href="{{ route('supplier-bills.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> فواتير الشراء
            </a>
        </div>
    </div>

    @if ($errors->any() || $errors->has('lines'))
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
            <strong>تنبيه:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @session('error-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 5000)" class="mb-3">
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert"><strong>{{ $value }}</strong></div>
    </div>
    @endsession

    <form wire:submit="save">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <h5 class="card-header">بيانات الفاتورة</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="supplier_id">المورد *</label>
                                <select id="supplier_id" class="form-select" wire:model="supplier_id">
                                    <option value="">اختر المورد...</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="bill_date">تاريخ الفاتورة *</label>
                                <input type="date" id="bill_date" class="form-control" wire:model="bill_date">
                                @error('bill_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="due_date">تاريخ الاستحقاق</label>
                                <input type="date" id="due_date" class="form-control" wire:model="due_date">
                                @error('due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="bill_number">رقم فاتورة المورد</label>
                                <input type="text" id="bill_number" class="form-control" wire:model="bill_number" placeholder="اختياري">
                                @error('bill_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="notes">ملاحظات</label>
                                <input type="text" id="notes" class="form-control" wire:model="notes" placeholder="ملاحظات الفاتورة">
                                @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lines --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">البنود</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="addProductLine">+ صنف من المخزن</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="addServiceLine">+ خدمة / مصروف</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th>النوع</th>
                                <th style="min-width: 200px;">البيان</th>
                                <th style="width: 110px;">الكمية</th>
                                <th style="width: 130px;">سعر الوحدة</th>
                                <th style="width: 130px;">الإجمالي</th>
                                <th style="width: 60px;"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lines as $index => $line)
                                <tr wire:key="bill-line-{{ $index }}">
                                    <td class="text-muted">
                                        @if($line['type'] === 'product')
                                            <span class="badge bg-label-primary">صنف</span>
                                        @else
                                            <span class="badge bg-label-secondary">خدمة</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($line['type'] === 'product')
                                            <select class="form-select" wire:model.live="lines.{{ $index }}.product_id">
                                                <option value="">اختر صنفًا...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->unit }}) — {{ \App\Enums\ProductKind::from($product->kind)->label() }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control" wire:model="lines.{{ $index }}.description" placeholder="مثال: صيانة الغسالة رقم 2 / نقل بضائع...">
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" class="form-control text-center" wire:model="lines.{{ $index }}.quantity" wire:input.debounce="$refresh">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" class="form-control text-end" wire:model="lines.{{ $index }}.unit_price" wire:input.debounce="$refresh">
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($this->lineTotal($line), 2) }} ₪
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeLine({{ $index }})" title="حذف السطر">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">الإجمالي</td>
                                <td class="text-end fw-bold fs-5">{{ number_format($this->subtotal(), 2) }} ₪</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">حفظ الفاتورة</h5>
                    <div class="card-body">
                        <p class="text-muted">بمجرد الحفظ: الكميات تدخل المخزن تلقائيًا (حركة شراء لكل صنف)، والرصيد المطلوب من المورد يُضاف لذمتك ويظهر في كشف الحساب.</p>
                        <p class="text-muted small">للتسديد استخدم زر «تسجيل دفعة» من المراجعة أو من كشف الحساب.</p>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">حفظ الفاتورة</button>
                            <a href="{{ route('supplier-bills.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
