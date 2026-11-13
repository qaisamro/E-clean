@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px">
        <div>
            <h2 class="mb-1">إرسال إشعار</h2>
            <p class="text-muted mb-0">
                أرسل رسالة لعملاء {{ $vendor->name }} الذين لديهم طلبات مكتملة من متجرك.
            </p>
        </div>
        <div class="d-flex flex-wrap" style="gap:8px">
            <a href="{{ route('vendor.customers.index') }}" class="btn btn-light border">
                <i class="fa fa-users"></i> عملاء المتجر
            </a>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-light border">
                <i class="fa fa-arrow-right"></i> لوحة التحكم
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">عملاء المتجر</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['customers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">أوقفوا إشعارات المتاجر</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['disabled_customers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">عدد حملات الإشعار</div>
                    <div class="h4 mb-0 font-weight-bold">{{ $summary['campaigns'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">آخر إرسال</div>
                    <div class="h6 mb-0 font-weight-bold">
                        {{ $summary['last_sent_at'] ? \Carbon\Carbon::parse($summary['last_sent_at'])->format('Y/m/d H:i') : 'لا يوجد' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary py-2">
            <h5 class="card-title m-0 text-white">
                <i class="fa fa-paper-plane"></i> إنشاء إشعار جديد
            </h5>
        </div>
        <div class="card-body">
            <form id="vendorNotificationForm" action="{{ route('vendor.notifications.send') }}" method="POST">
                @csrf
                <input type="hidden" name="request_token" value="{{ $requestToken }}">

                <div class="form-group">
                    <label class="font-weight-bold d-block mb-2">المستلمون</label>
                    @php
                        $selectedAudience = old('audience', $selectedCustomerId ? 'single' : 'all');
                    @endphp
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio"
                               id="audienceAll"
                               name="audience"
                               value="all"
                               class="custom-control-input"
                               {{ $selectedAudience === 'all' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="audienceAll">
                            جميع عملائي
                            <span class="text-muted">({{ $summary['customers'] }} عميل مؤهل)</span>
                        </label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio"
                               id="audienceSingle"
                               name="audience"
                               value="single"
                               class="custom-control-input"
                               {{ $selectedAudience === 'single' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="audienceSingle">عميل محدد</label>
                    </div>
                    @error('audience')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror

                    <div id="singleCustomerWrap" class="mt-2" style="{{ $selectedAudience === 'single' ? '' : 'display:none' }}">
                        <label class="mb-1">اختر العميل</label>
                        <select id="vendorCustomerSelect" name="customer_id" class="form-control">
                            <option value="">ابحث باسم العميل أو رقم الجوال...</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ (string) old('customer_id', $selectedCustomerId) === (string) $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} — {{ $customer->user->mobile ?? 'بدون رقم' }}
                                    @if ($customer->user && $customer->user->vendor_notifications_enabled === false)
                                        (أوقف إشعارات المتاجر)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                        @if ($customers->isEmpty())
                            <small class="text-muted d-block mt-1">
                                لا يوجد عملاء مؤهلون بعد. يظهر العميل بعد أول طلب مكتمل من متجرك.
                            </small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold mb-1">نص الإشعار</label>
                    <textarea id="notificationMessage"
                              name="message"
                              rows="4"
                              maxlength="255"
                              class="form-control @error('message') is-invalid @enderror"
                              placeholder="اكتب رسالة الإشعار هنا..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">الحد الأقصى 255 حرفًا.</small>
                        <small class="text-muted"><span id="notificationCharCount">0</span>/255</small>
                    </div>
                </div>

                <div class="alert alert-info mb-3">
                    <i class="fa fa-info-circle"></i>
                    سيظهر الإشعار داخل تطبيق العميل مباشرة، مع Push Notification عند تفعيل FCM.
                    @if ($summary['disabled_customers'] > 0)
                        سيتم استثناء {{ $summary['disabled_customers'] }} عميل أوقف إشعارات المتاجر من الإرسال.
                    @endif
                </div>

                <button type="submit" id="sendVendorNotification" class="btn btn-success btn-lg">
                    <i class="fa fa-paper-plane"></i> إرسال الإشعار
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="m-0">سجل الإشعارات المرسلة</h5>
        </div>
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>المستلمون</th>
                            <th>العميل</th>
                            <th>الرسالة</th>
                            <th>عدد المستلمين</th>
                            <th>Push</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $log->created_at ? $log->created_at->format('Y/m/d H:i') : '—' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $log->audience === 'all' ? 'جميع عملائي' : 'عميل محدد' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $log->audience === 'single' ? ($log->customer?->name ?? '—') : '—' }}
                                </td>
                                <td style="max-width:340px;white-space:normal">{{ $log->message }}</td>
                                <td class="text-nowrap">
                                    <span class="badge badge-light" style="font-size:13px">
                                        {{ (int) $log->recipients_count }}
                                    </span>
                                    @if ((int) $log->skipped_count > 0)
                                        <small class="text-muted d-block">استُثني {{ (int) $log->skipped_count }}</small>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{ $log->push_status_label }}
                                    @if ((int) $log->push_success_count > 0 || (int) $log->push_failed_count > 0)
                                        <small class="text-muted d-block">
                                            نجح {{ (int) $log->push_success_count }} / فشل {{ (int) $log->push_failed_count }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge badge-{{ $log->status_badge }}">
                                        {{ $log->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa fa-bell-slash fa-2x d-block mb-2" style="opacity:.35"></i>
                                    لم تُرسل أي إشعارات بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var $form = $('#vendorNotificationForm');
        var $customerSelect = $('#vendorCustomerSelect');
        var $singleWrap = $('#singleCustomerWrap');
        var $message = $('#notificationMessage');
        var $charCount = $('#notificationCharCount');
        var $submitButton = $('#sendVendorNotification');
        var selectInitialized = false;

        function singleSelected() {
            return $('input[name="audience"]:checked').val() === 'single';
        }

        function initCustomerSelect() {
            if (selectInitialized || !$customerSelect.length) {
                return;
            }
            selectInitialized = true;
            $customerSelect.select2({
                placeholder: 'ابحث باسم العميل أو رقم الجوال...',
                allowClear: true,
                width: '100%',
                dir: 'rtl',
                language: {
                    noResults: function () { return 'لا يوجد عميل مطابق من عملاء متجرك'; }
                }
            });
        }

        function refreshAudience() {
            if (singleSelected()) {
                $singleWrap.show();
                initCustomerSelect();
            } else {
                $singleWrap.hide();
            }
        }

        function refreshCharCount() {
            $charCount.text(($message.val() || '').length);
        }

        $('input[name="audience"]').on('change', refreshAudience);
        $message.on('input', refreshCharCount);
        refreshAudience();
        refreshCharCount();

        $form.on('submit', function (event) {
            event.preventDefault();

            var message = ($message.val() || '').trim();
            if (message.length < 2) {
                Swal.fire('نص الإشعار مطلوب', 'اكتب رسالة الإشعار أولًا (حرفان على الأقل).', 'warning');
                return;
            }

            if (singleSelected() && !$customerSelect.val()) {
                Swal.fire('اختر العميل', 'حدد عميلًا من قائمة عملاء متجرك قبل الإرسال.', 'warning');
                return;
            }

            var audienceText = singleSelected() ? 'عميل محدد' : 'جميع عملائي';
            Swal.fire({
                title: 'تأكيد إرسال الإشعار',
                html: '<div style="line-height:1.9">'
                    + 'سيتم إرسال الرسالة إلى: <strong>' + audienceText + '</strong><br>'
                    + 'سيظهر الإشعار داخل تطبيق العملاء، مع Push عند تفعيل FCM.'
                    + '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00A896',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، أرسل الإشعار',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                $submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> جارٍ الإرسال...');
                $form[0].submit();
            });
        });
    });
</script>
@endpush
