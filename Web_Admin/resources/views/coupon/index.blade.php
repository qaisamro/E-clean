@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                   <div class="row">
                        <div class="col-6">
                            <h2 class="card-title"><i class="fa fa-tags text-primary"></i> كوبونات متجري</h2>
                            <small class="text-muted">هذه الكوبونات خاصة بمتجرك وتُطبَّق على منتجاتك فقط.</small>
                        </div>

                        @can('coupon.create')
                        <div class="col-6 position-relative" >
                            <div class="position-absolute" style="right: 1em" >
                                <a href="{{ route('coupon.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create'). ' '.__('Coupon') }}</a>
                            </div>
                        </div>
                        @endcan
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
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm {{ session()->get('local') }}" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Code') }}</th>
                                    <th scope="col">{{ __('Discount_Type') }} </th>
                                    <th scope="col">{{ __('Discount') }}</th>
                                    <th scope="col">{{ __('Min_Amount') }}</th>
                                    <th scope="col">{{ __('Started_at') }}</th>
                                    <th scope="col">{{ __('Expired_at') }}</th>
                                    <th scope="col">النطاق</th>
                                    @canany(['coupon.edit', 'coupon.destroy'])
                                    <th scope="col">{{ __('Action') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code}}</td>
                                    <td>{{ __($coupon->discount_type) }}</td>
                                    <td>{!! $coupon->discount_type == 'amount' ? currencyPosition($coupon->discount) : $coupon->discount.'%' !!}</td>
                                    <td>{{ currencyPosition($coupon->min_amount) }}</td>
                                    <td>{{ Carbon\Carbon::parse($coupon->started_at)->format('M d, Y h:i a') }}</td>
                                    <td>{{ Carbon\Carbon::parse($coupon->expired_at)->format('M d, Y h:i a') }}</td>
                                    <td><span class="badge bg-secondary">متجري فقط</span></td>
                                    @canany(['coupon.edit', 'coupon.destroy'])
                                    <td>
                                        <a href="{{ route('coupon.edit', $coupon->id) }}" class="btn btn-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @can('coupon.destroy')
                                        <form action="{{ route('coupon.destroy', $coupon->id) }}" method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                            @csrf
                                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                        @endcan
                                    </td>
                                    @endcanany
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
