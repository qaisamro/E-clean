<div class="order-create-page">
    <style>
        .order-create-page .order-hero { background: linear-gradient(135deg,#0f766e,#0d9488 55%,#14b8a6); color:#fff; border-radius:1.25rem; padding:1.4rem 1.6rem; box-shadow:0 18px 40px rgba(13,148,136,.28); border:1px solid rgba(255,255,255,.18); }
        .order-create-page .section-title { font-size:1rem; font-weight:800; display:flex; align-items:center; gap:.55rem; color:#0f172a; letter-spacing:-.01em; }
        .order-create-page .section-title i { color:#0d9488; font-size:1.25rem; }
        .order-create-page .service-choice, .order-create-page .item-choice { cursor:pointer; border:1px solid rgba(15,23,42,.09); border-radius:.85rem; transition:.2s ease; background:#fff; }
        .order-create-page .service-choice:hover, .order-create-page .item-choice:hover { border-color:#0d9488; background:#f0fdfa; transform:translateY(-1px); box-shadow:0 6px 16px rgba(13,148,136,.16); }
        .order-create-page .service-choice.active { border-color:#0d9488; background:#ccfbf1; box-shadow:0 8px 20px rgba(13,148,136,.22); color:#115e59; }
        .order-create-page .item-choice input, .order-create-page .service-choice input { position:absolute; opacity:0; pointer-events:none; }
        .order-create-page .summary-number { background:#f0fdfa; border:1px solid rgba(13,148,136,.15); border-radius:.85rem; padding:.7rem; text-align:center; }
        .order-create-page .summary-number strong { display:block; font-size:1.15rem; color:#0f766e; }
        .order-create-page .line-editor { background:#fafafa; border:1px dashed rgba(13,148,136,.35); border-radius:.85rem; padding:.85rem; }
        .order-create-page .line-editor:focus-within { border-color:#0d9488; box-shadow:0 0 0 3px rgba(13,148,136,.14); }
        .order-create-page .order-badge-count { background:#ccfbf1 !important; color:#115e59 !important; }
        .order-create-page .customer-found-badge { background:#ccfbf1 !important; color:#115e59 !important; }
        @media (min-width: 992px) { .order-create-page .payment-sticky { position:sticky; top:1rem; } }
        .order-create-page .pos-empty { color:#64748b; font-weight:600; padding:1.5rem; }
    </style>

    @session('success-msg')
        <div x-data x-init="setTimeout(()=> $el.remove(),3000)" class="alert alert-success alert-dismissible fade show"><strong>{{ $value }}</strong><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endsession

    <div class="order-hero mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><h4 class="mb-1 text-white">إضافة طلب جديد</h4><span class="opacity-75">أضف العميل والبنود ثم راجع الدفع قبل الحفظ.</span></div>
        <div class="text-white-50 small"><i class="bx bx-lock-alt me-1"></i> الأسعار والمخزون تُراجع عند الحفظ</div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center"><div class="section-title"><i class="bx bx-user"></i> بيانات العميل</div>@if($customerForm->id)<span class="badge bg-label-primary">عميل #{{ $customerForm->id }}</span>@endif</div>
                <div class="card-body">
                    <label for="customer_search_inline" class="form-label">بحث عن عميل محفوظ</label>
                    <div class="input-group mb-2"><span class="input-group-text"><i class="bx bx-search"></i></span><input id="customer_search_inline" type="search" class="form-control" placeholder="الاسم أو الهاتف أو رقم العميل" wire:model.live.debounce.500ms="customerForm.search"></div>
                    @if($customerForm->search)
                        <div class="border rounded p-2 mb-3" style="max-height:180px;overflow:auto">
                            @forelse($customerForm->customers as $customer)
                                <label wire:key="customer-inline-{{ $customer->id }}" class="d-flex align-items-center gap-2 border-bottom py-2 cursor-pointer mb-0"><input name="customer_id" class="form-check-input m-0" type="radio" wire:model.live="customerForm.id" value="{{ $customer->id }}"><span class="flex-grow-1"><strong>{{ $customer->name }}</strong><small class="d-block text-muted" dir="ltr">{{ $customer->phone }}</small></span><a href="{{ route('customers.show', $customer) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="سجل العميل"><i class="bx bx-link-external"></i></a></label>
                            @empty <span class="small text-muted">لا توجد نتيجة؛ أدخل بيانات عميل جديد أدناه.</span>
                            @endforelse
                        </div>
                    @endif
                    <div class="row g-2">
                        <div class="col-md-5"><label class="form-label" for="customer_name">اسم العميل</label><input class="form-control" id="customer_name" wire:model="customerForm.name"><x-input-error key="customerForm.name"/></div>
                        <div class="col-md-4"><label class="form-label" for="customer_phone">رقم الهاتف</label><input class="form-control text-start" id="customer_phone" dir="ltr" type="tel" wire:model="customerForm.phone"><x-input-error key="customerForm.phone"/></div>
                        <div class="col-md-3 d-flex align-items-end">@if($customerForm->id)<button type="button" wire:click="cancelCustomer" class="btn btn-outline-secondary w-100"><i class="bx bx-user-plus me-1"></i>عميل جديد</button>@endif</div>
                        <div class="col-12"><label class="form-label" for="customer_address">العنوان</label><input class="form-control" id="customer_address" wire:model="customerForm.address"><x-input-error key="customerForm.address"/></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header border-bottom"><div class="section-title"><i class="bx bx-washer"></i> بنود خدمات الغسيل</div></div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">1. اختر الخدمة</label><div class="d-flex flex-wrap gap-2">@foreach($this->orderForm->services() as $service)<label wire:key="service-{{ $service->id }}" @class(['service-choice px-3 py-2','active' => $orderForm->service_id == $service->id])><input name="service_id" type="radio" wire:model.live="orderForm.service_id" value="{{ $service->id }}"><span>{{ $service->name }}</span></label>@endforeach</div><x-input-error key="orderForm.service_id"/></div>
                    <label class="form-label">2. اختر المنتج لإضافته للطلب <i class="bx bx-loader-circle bx-spin ms-1" wire:loading wire:target="orderForm.service_id"></i></label>
                    <div class="row g-2">@forelse($orderForm->items as $item)<div wire:key="item-{{ $item->id }}" class="col-6 col-md-4 col-xl-3"><label class="item-choice d-block text-center p-2 h-100"><input name="item_id" type="radio" value="{{ $item->id }}" wire:model.live="orderForm.item_id"><strong class="small">{{ $item->name }}</strong></label></div>@empty <div class="text-muted small">اختر خدمة لعرض المنتجات المرتبطة بها.</div>@endforelse</div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center"><div class="section-title"><i class="bx bx-receipt"></i> بنود الطلب</div><span class="badge bg-label-primary">{{ count($orderForm->orders) + count($productLines) }} بند</span></div>
                <div class="card-body">
                    @if(count($orderForm->orders))
                        <div class="table-responsive mb-3"><table class="table table-hover align-middle mb-0"><thead><tr><th>المنتج</th><th>الخدمة</th><th class="text-center">الكمية</th><th class="text-center">السعر</th><th class="text-center">الإجمالي</th></tr></thead><tbody>@foreach($orderForm->orders as $index => $order)<tr wire:key="order-detail-{{ $index }}" class="cursor-pointer" wire:click="selectOrder('{{ $index }}')"><td>{{ $order['item']['name'] }}</td><td>{{ $order['service']['name'] }}</td><td class="text-center">{{ $order['quantity'] }}</td><td class="text-center">{{ number_format((float) $order['price'],2) }}</td><td class="text-center fw-bold">{{ number_format((float) $order['total_price'],2) }} ₪</td></tr>@endforeach</tbody></table></div>
                    @endif
                    @if($selectedOrder !== '')
                        <div class="line-editor mb-3"><div class="row g-2 align-items-end"><div class="col-md-3"><label class="form-label">الكمية</label><input type="number" class="form-control" min="0.01" step="0.01" wire:model="orderForm.orders.{{ $selectedOrder }}.quantity" wire:input.debounce="applyChanges"></div><div class="col-md-3"><label class="form-label">السعر</label><input type="number" class="form-control" min="0.01" step="0.01" wire:model="orderForm.orders.{{ $selectedOrder }}.price" wire:input.debounce="applyChanges"></div><div class="col-md-4"><label class="form-label">ملاحظة البند</label><input class="form-control" wire:model="orderForm.orders.{{ $selectedOrder }}.description"></div><div class="col-md-2"><button type="button" wire:click="deleteOrder" class="btn btn-outline-danger w-100"><i class="bx bx-trash"></i> حذف</button></div></div></div>
                    @endif
                    @if(!count($orderForm->orders) && !count($productLines))<div class="text-center text-muted py-3">لم تضف أي بند بعد.</div>@endif
                    <x-input-error key="error_msg"/>
                </div>
            </div>

            @if(count($productLines) || ($sellableProducts ?? collect())->isNotEmpty())
            <div class="card mb-3">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center"><div class="section-title"><i class="bx bx-package"></i> منتجات الكاشير</div><span class="badge bg-label-warning">تُخصم من المخزن عند الحفظ</span></div>
                <div class="card-body">
                    <div class="row g-2 align-items-end"><div class="col-md-9"><label for="productToAdd" class="form-label">منتج متاح للبيع</label><select id="productToAdd" class="form-select" wire:model="productToAdd"><option value="">اختر منتجًا</option>@foreach(($sellableProducts ?? []) as $product)<option value="{{ $product->id }}">{{ $product->name }} — المتوفر {{ number_format((float) $product->current_stock,2) }} {{ $product->unit }} — {{ number_format((float) $product->sale_price,2) }} ₪</option>@endforeach</select></div><div class="col-md-3"><button type="button" class="btn btn-outline-primary w-100" wire:click="addProduct"><i class="bx bx-plus me-1"></i>إضافة</button></div></div>
                    @if(count($productLines))<div class="table-responsive mt-3"><table class="table table-sm align-middle mb-0"><thead><tr><th>المنتج</th><th>المتوفر</th><th style="width:120px">الكمية</th><th>السعر</th><th>الإجمالي</th><th></th></tr></thead><tbody>@foreach($productLines as $index => $line)<tr wire:key="product-line-{{ $line['product_id'] }}"><td>{{ $line['name'] }}</td><td>{{ number_format((float)$line['max'],2) }} {{ $line['unit'] }}</td><td><input type="number" class="form-control form-control-sm" min="0.01" max="{{ $line['max'] }}" step="1" wire:model="productLines.{{ $index }}.quantity" wire:input.debounce.400ms="applyProductChanges"></td><td>{{ number_format((float)$line['price'],2) }} ₪</td><td class="fw-bold">{{ number_format((float)$line['subtotal'],2) }} ₪</td><td><button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeProductLine({{ $index }})"><i class="bx bx-trash"></i></button></td></tr>@endforeach</tbody></table></div>@endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4"><div class="payment-sticky">
            <div class="card mb-3"><div class="card-header border-bottom"><div class="section-title"><i class="bx bx-calendar"></i> التسليم والملاحظات</div></div><div class="card-body"><div class="row g-2"><div class="col-6"><label class="form-label" for="del-date">التاريخ</label><input type="date" id="del-date" class="form-control" wire:model.live="deliver_date"><x-input-error key="deliver_date"/></div><div class="col-6"><label class="form-label" for="del-time">الوقت</label><input type="time" id="del-time" class="form-control" wire:model.live="deliver_time"><x-input-error key="deliver_time"/></div><div class="col-12"><label class="form-label" for="notes">ملاحظات</label><textarea id="notes" rows="2" class="form-control" wire:model="notes" placeholder="اختياري"></textarea></div></div></div></div>
            <div class="card"><div class="card-header border-bottom"><div class="section-title"><i class="bx bx-credit-card"></i> الدفع والإجمالي</div></div><div class="card-body">
                <label class="form-label">طريقة الدفع</label><div class="vstack gap-2 mb-3">@foreach([1=>'نقدي',3=>'فيزا',2=>'آجل / على الحساب',4=>'دفع جزئي'] as $value => $label)<label @class(['border rounded p-2 d-flex align-items-center gap-2 cursor-pointer','border-primary bg-label-primary' => (int)$payment_type === $value])><input class="form-check-input m-0" type="radio" name="payment_type" value="{{ $value }}" wire:model.live="payment_type"><span>{{ $label }}</span></label>@endforeach</div>
                @if($payment_type == 4)<label class="form-label" for="paid_money">المبلغ المدفوع</label><input class="form-control mb-3" type="number" step="0.01" id="paid_money" min="0" wire:model.live.debounce="paid_money"><x-input-error key="paid_money"/>@endif
                <label class="form-label" for="discount">خصم الطلب</label><div class="input-group mb-3"><input class="form-control" type="number" step="0.01" id="discount" min="0" wire:model.live.debounce.500ms="discount"><span class="input-group-text">₪</span></div><x-input-error key="discount"/>
                <div class="row g-2 mb-3"><div class="col-6"><div class="summary-number"><small>الإجمالي</small><strong>{{ number_format((float)$total_price,2) }} ₪</strong></div></div><div class="col-6"><div class="summary-number"><small>المتبقي</small><strong class="text-danger">{{ number_format((float)$remaining_money,2) }} ₪</strong></div></div></div>
                <x-input-error key="error_msg"/>
                <button type="button" wire:click="saveOrder" wire:loading.attr="disabled" class="btn btn-primary btn-lg w-100"><span wire:loading.remove wire:target="saveOrder"><i class="bx bx-check-circle me-1"></i>حفظ الطلب</span><span wire:loading wire:target="saveOrder"><i class="bx bx-loader-circle bx-spin me-1"></i>جارٍ الحفظ...</span></button><p class="small text-muted text-center mb-0 mt-2">يُسجل الدفع وسند المخزون تلقائيًا عند الحفظ.</p>
            </div></div>
        </div></div>
    </div>
</div>
