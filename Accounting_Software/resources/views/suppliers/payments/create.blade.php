<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-credit-card"></i> الموردون — الدفعات</div>
                <h1 class="ec-section-title">تسجيل دفعة</h1>
            </div>
            <a href="{{ route('supplier-payments.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> الدفعات
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
            <strong>تنبيه:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">بيانات الدفعة</h5>
                <div class="card-body">
                    <form wire:submit="save">
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="bill_id">الدفع على فاتورة محددة</label>
                                <select id="bill_id" class="form-select" wire:model="bill_id">
                                    <option value="">دفعة على الحساب (بدون ربط)</option>
                                    @foreach($bills as $bill)
                                        <option value="{{ $bill->id }}">
                                            فاتورة #{{ $bill->id }} — المتبقي {{ number_format((float) $bill->remaining, 2) }} ₪
                                        </option>
                                    @endforeach
                                </select>
                                @error('bill_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="payment_date">تاريخ الدفع *</label>
                                <input type="date" id="payment_date" class="form-control" wire:model="payment_date">
                                @error('payment_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="amount">المبلغ *</label>
                                <input type="number" step="0.01" min="0.01" id="amount" class="form-control" wire:model="amount" placeholder="0.00">
                                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="method">طريقة الدفع</label>
                                <select id="method" class="form-select" wire:model="method">
                                    @foreach(\App\Models\SupplierPayment::METHODS as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('method')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="notes">ملاحظات</label>
                                <input type="text" id="notes" class="form-control" wire:model="notes" placeholder="رقم الشيك / مرجع التحويل / سبب بالدفع">
                                @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">تسجيل الدفعة</button>
                            <a href="{{ $supplier_id !== '' ? route('suppliers.show', ['supplier' => $supplier_id, 'tab' => 'payments']) : route('supplier-payments.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <h5 class="card-header">ملاحظات</h5>
                <div class="card-body text-muted">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">• ربط الدفعة بغاتورة يقلل «المتبقي» على الفاتورة مباشرة ويحدد حالتها (مسددة / جزئية).</li>
                        <li class="mb-2">• بدون فاتورة، تُعامل الدفعة كذمم على الحساب وتخفض الرصيد الجاري للمورد.</li>
                        <li>• حذف الدفعة من صفحة الدفعات يُعيد احتساب الفاتورة المرتبطة تلقائيًا.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
