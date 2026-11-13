<div class="order-create-page payment-create-page">
    <style>
        .payment-create-page .order-hero { background: linear-gradient(135deg,#0f766e,#0d9488 55%,#14b8a6); color:#fff; border-radius:1.25rem; padding:1.4rem 1.6rem; box-shadow:0 18px 40px rgba(13,148,136,.28); border:1px solid rgba(255,255,255,.18); }
        .payment-create-page .section-title { font-size:1rem; font-weight:800; display:flex; align-items:center; gap:.55rem; color:#0f172a; letter-spacing:-.01em; }
        .payment-create-page .section-title i { color:#0d9488; font-size:1.25rem; }
        .payment-create-page .search-result { cursor:pointer; border:1px solid rgba(15,23,42,.09); border-radius:.85rem; transition:.18s ease; background:#fff; text-align:start; }
        .payment-create-page .search-result:hover { border-color:#0d9488; background:#f0fdfa; transform:translateY(-1px); box-shadow:0 6px 16px rgba(13,148,136,.16); }
        .payment-create-page .search-result.active { border-color:#0d9488; background:#ccfbf1; box-shadow:0 8px 20px rgba(13,148,136,.22); color:#115e59; }
        .payment-create-page .summary-number { background:#f0fdfa; border:1px solid rgba(13,148,136,.15); border-radius:.85rem; padding:.7rem; text-align:center; }
        .payment-create-page .summary-number strong { display:block; font-size:1.15rem; color:#0f766e; }
        .payment-create-page .summary-number small { color:#115e59; font-weight:700; }
        .payment-create-page .customer-chip { display:inline-flex; align-items:center; gap:.6rem; background:#f0fdfa; border:1px solid rgba(13,148,136,.2); border-radius:.75rem; padding:.5rem .7rem; font-weight:700; color:#115e59; }
        .payment-create-page .pos-empty { color:#64748b; font-weight:600; padding:1.5rem; text-align:center; }
        @media (min-width: 992px) { .payment-create-page .payment-sticky { position:sticky; top:1rem; } }
    </style>

    @if(session('success-msg'))
        <div x-data x-init="setTimeout(() => $el.remove(), 3500)" class="alert alert-success alert-dismissible fade show mb-3">
            <strong>{{ session('success-msg') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->has('payment'))
        <div class="alert alert-danger mb-3">
            <i class="bx bx-info-circle me-1"></i>{{ $errors->first('payment') }}
        </div>
    @endif

    <div class="order-hero d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 text-white">تسجيل دفعة للعميل</h4>
            <span class="opacity-75">ابحث عن العميل، حدد الطلب للتسوية (اختياري)، ثم سجّل قيمة الدفعة.</span>
        </div>
        <div class="text-white-50 small"><i class="bx bx-lock-alt me-1"></i> تُسجّل الدفعة على الطلبات المفتوحة عند الحفظ</div>
    </div>

    <div class="row g-3">
        {{-- ── العمود الأيسر: اختيار العميل ── --}}
        <div class="col-lg-4">
            <div class="card mb-3 h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div class="section-title"><i class="bx bx-user"></i> بيانات العميل</div>
                    @if($customer)<span class="badge bg-label-primary">عميل #{{ $customer->id }}</span>@endif
                </div>
                <div class="card-body">
                    <label for="customer-search" class="form-label">بحث عن عميل محفوظ</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input id="customer-search" type="search" class="form-control"
                               wire:model.live.debounce.400ms="search"
                               placeholder="الاسم أو الهاتف أو رقم العميل">
                    </div>

                    <div class="d-flex flex-column gap-2" style="max-height: 240px; overflow-y: auto">
                        @forelse($customers as $item)
                            <button type="button" wire:click="selectCustomer({{ $item->id }})"
                                    @class(['search-result w-100 p-2 px-3 d-flex align-items-center gap-2', 'active' => (int) $customer_id === $item->id])>
                                <span class="flex-grow-1">
                                    <strong class="d-block">{{ $item->name }}</strong>
                                    <small class="text-muted" dir="ltr">#{{ $item->id }} — {{ $item->phone }}</small>
                                </span>
                                @if((int) $customer_id === $item->id)
                                    <i class="bx bx-check-circle fs-5 text-teal"></i>
                                @endif
                            </button>
                        @empty
                            <div class="pos-empty">
                                <i class="bx bx-search-alt-2 d-block fs-3 mb-2"></i>
                                {{ $search !== '' ? 'لا توجد نتيجة مطابقة' : 'ابدأ بالكتابة لعرض العملاء' }}
                            </div>
                        @endforelse
                    </div>

                    @if($customer)
                        <div class="border rounded p-3 mt-3" style="background:#f8fffd">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                                <span class="customer-chip">
                                    <i class="bx bx-user-circle fs-5"></i>
                                    <span>{{ $customer->name }}</span>
                                </span>
                                <button type="button" wire:click="clearCustomer" class="btn btn-sm btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i>مسح
                                </button>
                            </div>
                            <div class="summary-number mt-2">
                                <small>المتبقي عليه</small>
                                <strong class="text-danger">{{ number_format((float) $totalDue, 2) }} ₪</strong>
                            </div>
                        </div>
                    @else
                        <div class="pos-empty border rounded mt-3" style="border-style:dashed !important">
                            <i class="bx bx-user-plus fs-4 d-block mb-2"></i>
                            اختر عميلاً أولاً<br>
                            <small class="text-muted">لمتابعة تسديداته والطلبات المفتوحة</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── العمود الأيمن: الطلبات المفتوحة ثم فورم الدفعة ── --}}
        <div class="col-lg-8">
            @if($customer)

            {{-- طلبات العميل المفتوحة --}}
            <div class="card mb-3">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="section-title"><i class="bx bx-receipt"></i> الطلبات المفتوحة</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <input type="text" class="form-control form-control-sm" style="width:140px" wire:model.live.debounce.400ms="order_code_filter" placeholder="بحث برقم الطلب">
                        <input type="date" class="form-control form-control-sm" wire:model.live="date_from" title="من تاريخ">
                        <input type="date" class="form-control form-control-sm" wire:model.live="date_to" title="إلى تاريخ">
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th class="text-center" style="width:54px">اختيار</th>
                            <th>رقم الطلب</th>
                            <th>التاريخ</th>
                            <th class="text-end">الإجمالي</th>
                            <th class="text-end">المتبقي</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr class="table-active">
                                <td class="text-center">
                                    <input class="form-check-input" type="radio" wire:model.live="order_id" value="">
                                </td>
                                <td colspan="4">
                                    <strong>التسوية على كل الطلبات</strong>
                                    <small class="d-block text-muted">تُوزّع الدفعة على جميع الطلبات المفتوحة بترتيب الأقدم أولًا</small>
                                </td>
                            </tr>
                            @forelse($outstanding as $order)
                                <tr wire:key="out-{{ $order->id }}">
                                    <td class="text-center">
                                        <input class="form-check-input" type="radio" wire:model.live="order_id" value="{{ $order->id }}">
                                    </td>
                                    <td><strong>{{ $order->order_code }}</strong></td>
                                    <td>{{ $order->created_at->format('Y/m/d') }}</td>
                                    <td class="text-end">{{ number_format((float) $order->total_price, 2) }} ₪</td>
                                    <td class="text-end text-danger fw-bold">{{ number_format((float) $order->remaining_money, 2) }} ₪</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="pos-empty"><i class="bx bx-check-double fs-4 d-block mb-2 text-teal"></i>كل طلباته مسددة بالكامل</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- نموذج الدفعة --}}
            <form wire:submit="save" class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div class="section-title"><i class="bx bx-bank"></i> الدفعة</div>
                    @error('payment')<span class="badge bg-label-danger">خطأ</span>@enderror
                </div>
                <div class="card-body">
                    @if($errors->has('payment'))
                        <div class="alert alert-danger py-2 small"><i class="bx bx-info-circle me-1"></i>{{ $errors->first('payment') }}</div>
                    @endif
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="payment_date" class="form-label">تاريخ الدفعة <span class="text-danger">*</span></label>
                            <input type="date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" wire:model="payment_date">
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₪</span>
                                <input type="number" step="0.01" min="0.01" id="amount" inputmode="decimal" class="form-control @error('amount') is-invalid @enderror" wire:model="amount" placeholder="0.00">
                            </div>
                            @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="payment_method" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                            <select id="payment_method" class="form-select" wire:model="payment_method">
                                <option value="cash">نقدي</option>
                                <option value="visa">فيزا</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">ملاحظات / مرجع الدفعة</label>
                            <input type="text" id="notes" class="form-control" wire:model="notes" placeholder="مثل: رقم سند فيزا، ملاحظة…">
                        </div>
                    </div>

                    <div class="summary-number my-3">
                        <small>الإجمالي المطلوب بعد التسديد</small>
                        <strong>{{ number_format(max(0, (float) $totalDue - (float) ($amount === '' ? 0 : $amount)), 2) }} ₪</strong>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" wire:loading.attr="disabled">
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                        <i wire:loading.remove wire:target="save" class="bx bx-check-circle me-1"></i>
                        حفظ الدفعة
                    </button>
                </div>
            </form>
            @else
                <div class="card h-100 d-flex align-items-center justify-content-center">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-user-plus fs-1 d-block mb-2 text-teal"></i>
                        <p class="fw-bold mb-1">اختر عميلاً أولاً</p>
                        <span class="text-muted small">من القائمة على اليمين — تُعرض طلباته المفتوحة ثم يمكنك تسجيل الدفعة</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
