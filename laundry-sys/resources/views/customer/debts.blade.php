<div>
    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light"><a href="{{ route('customers.index') }}">العملاء</a> /</span>
            الديون المستحقة
        </h4>
    </div>

    @if(session('debt-success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i>{{ session('debt-success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
            <h5 class="mb-0"><i class="bx bx-credit-card-front text-danger me-2"></i>قائمة العملاء المدينين</h5>
            <div class="col-md-4">
                <input
                    wire:model.live.debounce.400ms="search"
                    type="text"
                    class="form-control"
                    placeholder="🔍 ابحث بالاسم أو رقم الهاتف..."
                />
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3">اسم العميل</th>
                            <th class="py-3">رقم الهاتف</th>
                            <th class="py-3 text-center">عدد الطلبات المدينة</th>
                            <th class="py-3 text-center">إجمالي الطلبات</th>
                            <th class="py-3 text-center">المدفوع</th>
                            <th class="py-3 text-center text-danger">المتبقي (الدين)</th>
                            <th class="py-3 text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="px-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('customers.show', $customer) }}" class="fw-bold text-primary">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td>{{ $customer->phone }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-warning">
                                        {{ $customer->orders_count ?? $customer->orders->where('remaining_money', '>', 0)->count() }} طلب
                                    </span>
                                </td>
                                <td class="text-center fw-bold">
                                    {{ number_format($customer->orders_sum_total_price ?? 0, 2) }} ₪
                                </td>
                                <td class="text-center text-success fw-bold">
                                    {{ number_format($customer->orders_sum_paid_money ?? 0, 2) }} ₪
                                </td>
                                <td class="text-center text-danger fw-bold fs-6">
                                    {{ number_format($customer->orders_sum_remaining_money ?? 0, 2) }} ₪
                                </td>
                                <td class="text-center">
                                    <button
                                        wire:click="openPaymentModal({{ $customer->id }})"
                                        class="btn btn-sm btn-success"
                                    >
                                        <i class="bx bx-money me-1"></i> تحصيل دفعة
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bx bx-check-circle text-success" style="font-size: 3rem"></i>
                                    <p class="mt-2 text-muted fw-bold">لا يوجد عملاء مدينون حالياً ✓</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    @if($showPaymentModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-money me-2"></i>
                            تحصيل دفعة من عميل
                        </h5>
                        <button wire:click="closePaymentModal" type="button" class="btn-close btn-close-white"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if($errors->has('payment'))
                            <div class="alert alert-danger">{{ $errors->first('payment') }}</div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">مبلغ الدفعة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">₪</span>
                                <input
                                    wire:model="paymentAmount"
                                    type="number"
                                    step="0.01"
                                    min="1"
                                    class="form-control @error('paymentAmount') is-invalid @enderror"
                                    placeholder="0.00"
                                    autofocus
                                >
                            </div>
                            @error('paymentAmount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">طريقة الدفع <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input wire:model="paymentMethod" class="form-check-input" type="radio" value="cash" id="pay-cash">
                                    <label class="form-check-label" for="pay-cash">
                                        <i class="bx bx-money text-success me-1"></i> نقدي
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input wire:model="paymentMethod" class="form-check-input" type="radio" value="visa" id="pay-visa">
                                    <label class="form-check-label" for="pay-visa">
                                        <i class="bx bx-credit-card text-primary me-1"></i> فيزا
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات (اختياري)</label>
                            <input
                                wire:model="paymentNotes"
                                type="text"
                                class="form-control"
                                placeholder="ملاحظات الدفعة..."
                            >
                        </div>

                        <div class="alert alert-info small">
                            <i class="bx bx-info-circle me-1"></i>
                            سيتم توزيع الدفعة تلقائياً على الطلبات القديمة أولاً.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closePaymentModal" type="button" class="btn btn-secondary">
                            <i class="bx bx-x me-1"></i> إلغاء
                        </button>
                        <button wire:click="submitPayment" wire:loading.attr="disabled" class="btn btn-success">
                            <span wire:loading wire:target="submitPayment" class="spinner-border spinner-border-sm me-1"></span>
                            <i wire:loading.remove wire:target="submitPayment" class="bx bx-check me-1"></i>
                            تأكيد الدفعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
