@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="card-title"><i class="fa fa-tags text-primary"></i> كوبونات الخصم العامة</h2>
                            <small class="text-muted">هذه الكوبونات تُطبَّق على <strong>جميع المتاجر</strong> وجميع العملاء في التطبيق.</small>
                        </div>

                        @if (auth()->check() && auth()->user()->hasAnyRole(['root', 'admin']))
                        <div class="col-6 position-relative">
                            <div class="position-absolute" style="right: 1em">
                                <a href="{{ route('coupon.admin.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> إنشاء كوبون عام
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-dismiss="alert"></button>
                        </div>
                    @endif

                    @php $cn = session('coupon_notification'); @endphp
                    @if (is_array($cn))
                        @php
                            $cnClass = 'alert-success';
                            $cnIcon = 'fa-check-circle';
                            if (($cn['status'] ?? null) === 'no_recipients') {
                                $cnClass = 'alert-warning';
                                $cnIcon = 'fa-exclamation-triangle';
                            } elseif ((int) ($cn['sent'] ?? 0) === 0) {
                                $cnClass = 'alert-danger';
                                $cnIcon = 'fa-times-circle';
                            }
                            $cnText = match ($cn['status'] ?? null) {
                                'sent' => 'تم إرسال إشعار الكوبون إلى ' . (int) ($cn['sent'] ?? 0) . ' من ' . (int) ($cn['recipients'] ?? 0) . ' جهاز.',
                                'no_recipients' => 'لم يتم إرسال إشعار: لا يوجد أي جهاز مسجّل.',
                                'skipped' => 'لم يتم إرسال إشعار: ' . ($cn['message'] ?? 'الخدمة غير مفعّلة.'),
                                'not_configured' => 'لم يتم إرسال إشعار: خدمة الإشعارات (Firebase) غير مهيّأة.',
                                'error' => 'تعذّر إرسال الإشعار: ' . ($cn['message'] ?? 'خطأ غير معروف.'),
                                default => 'تم إرسال ' . (int) ($cn['sent'] ?? 0) . ' إشعار من أصل ' . (int) ($cn['recipients'] ?? 0) . ' جهاز.',
                            };
                            if (isset($cn['in_app_inserted'])) {
                                $cnText .= ' — وتم تحديث قائمة الإشعارات لدى ' . (int) $cn['in_app_inserted'] . ' عميلاً داخل التطبيق.';
                            }
                        @endphp
                        <div class="alert {{ $cnClass }} alert-dismissible fade show">
                            <i class="fa {{ $cnIcon }}"></i> {{ $cnText }}
                            <button type="button" class="btn-close" data-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle" id="myTable">
                            <thead>
                                <tr>
                                    <th>كود الكوبون</th>
                                    <th>نوع الخصم</th>
                                    <th>الخصم</th>
                                    <th>الحد الأدنى</th>
                                    <th>يبدأ في</th>
                                    <th>ينتهي في</th>
                                    <th>النطاق</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($coupons as $coupon)
                                <tr>
                                    <td><strong>{{ $coupon->code }}</strong></td>
                                    <td>{{ __($coupon->discount_type) }}</td>
                                    <td>{!! $coupon->discount_type == 'amount' ? currencyPosition($coupon->discount) : $coupon->discount.'%' !!}</td>
                                    <td>{{ currencyPosition($coupon->min_amount) }}</td>
                                    <td>{{ Carbon\Carbon::parse($coupon->started_at)->format('M d, Y h:i a') }}</td>
                                    <td>{{ Carbon\Carbon::parse($coupon->expired_at)->format('M d, Y h:i a') }}</td>
                                    <td><span class="badge bg-primary">عام — كل المتاجر</span></td>
                                    <td>
                                        <a href="{{ route('coupon.admin.edit', $coupon->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('coupon.admin.destroy', $coupon->id) }}" method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                            @csrf
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        لا توجد كوبونات عامة بعد. استخدم زر «إنشاء كوبون عام» لإضافة أول كوبون.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
