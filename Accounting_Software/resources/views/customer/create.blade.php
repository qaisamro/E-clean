<div class="order-create-page customer-create-page">
    <style>
        .customer-create-page .order-hero { background: linear-gradient(135deg,#0f766e,#0d9488 55%,#14b8a6); color:#fff; border-radius:1.25rem; padding:1.4rem 1.6rem; box-shadow:0 18px 40px rgba(13,148,136,.28); border:1px solid rgba(255,255,255,.18); }
        .customer-create-page .section-title { font-size:1rem; font-weight:800; display:flex; align-items:center; gap:.55rem; color:#0f172a; letter-spacing:-.01em; }
        .customer-create-page .section-title i { color:#0d9488; font-size:1.25rem; }
        .customer-create-page .summary-number { background:#f0fdfa; border:1px solid rgba(13,148,136,.15); border-radius:.85rem; padding:.85rem; text-align:center; }
        .customer-create-page .summary-number strong { display:block; font-size:1.2rem; color:#0f766e; }
        .customer-create-page .summary-number small { color:#115e59; font-weight:700; }
        @media (min-width: 992px) { .customer-create-page .side-sticky { position:sticky; top:1rem; } }
    </style>

    @session('success-msg')
        <div x-data x-init="setTimeout(() => $el.remove(), 3000)" class="alert alert-success alert-dismissible fade show mb-3">
            <strong>{{ $value }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endsession

    <div class="order-hero d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 text-white">إضافة عميل جديد</h4>
            <span class="opacity-75">أضف الاسم والهاتف والعنوان — يُحفظ مباشرة ويصبح قابلاً للاختيار في الطلبات والمدفوعات.</span>
        </div>
        <div class="text-white-50 small"><i class="bx bx-user-plus me-1"></i> يُحفظ مرة واحدة برقم هاتف فريد</div>
    </div>

    <form wire:submit="save">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <div class="section-title"><i class="bx bx-user"></i> بيانات العميل</div>
                        <span class="badge bg-label-primary">مطلوب للحفظ</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" autofocus autocomplete="name" placeholder="مثل: سلام محمد">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input id="phone" type="tel" dir="ltr" class="form-control text-start @error('phone') is-invalid @enderror"
                                       wire:model="phone" autocomplete="tel" placeholder="0590000000">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                                       wire:model="address" autocomplete="street-address" placeholder="مثل: رام الله — عين منجد، بجانب البنك">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                                <i wire:loading.remove wire:target="save" class="bx bx-check-circle me-1"></i>
                                حفظ العميل
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="side-sticky">
                    <div class="card mb-3">
                        <div class="card-header border-bottom">
                            <div class="section-title"><i class="bx bx-info-circle"></i> ماذا يحدث بعد الحفظ؟</div>
                        </div>
                        <div class="card-body">
                            <div class="vstack gap-3">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge bg-label-primary rounded-pill mt-1">1</span>
                                    <div><strong>العميل يظهر مباشرة</strong><small class="d-block text-muted">في قائمة البحث داخل إضافة طلب جديد والطلبات المحفوظة مسبقًا.</small></div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge bg-label-primary rounded-pill mt-1">2</span>
                                    <div><strong>مديونيته تُراكم تلقائيًا</strong><small class="d-block text-muted">كل طلب آجل يُضاف للحساب، وأي دفعة تُنقص المتبقي من «إضافة دفعة عميل».</small></div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <span class="badge bg-label-primary rounded-pill mt-1">3</span>
                                    <div><strong>كشف حساب جاهز للطباعة</strong><small class="d-block text-muted">من صفحة تفاصيل العميل → «كشف حساب» لعرض وطباعة الحركات.</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header border-bottom">
                            <div class="section-title"><i class="bx bx-notepad"></i> ملاحظة</div>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 small text-muted">رقم الهاتف هو المعرف الأساسي — تأكد من صحته، وإن كان العميل موجودًا مسبقًا سيظهر في نتائج البحث في صفحة إضافة طلب جديد.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
