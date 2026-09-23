@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="m-0">طلباتي</h2>
                    <div class="d-flex align-items-center">
                        <img src="{{ $driver->user->profile_photo_path ?? asset('website/assets/images/common/user.png') }}"
                            alt="" width="45" height="45" class="rounded-circle mr-2">
                        <div>
                            <h5 class="m-0">{{ $driver->user->name }}</h5>
                            <small class="text-muted">{{ $driver->user->email ?? '' }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="text-primary mb-3">الطلبات المعيّنة لك</h5>

                    @forelse ($orders as $order)
                        @php
                            $quantity = 0;
                            foreach ($order->products as $product) {
                                $quantity += $product->pivot->quantity;
                            }
                            $productNames = $order->products->pluck('name')->implode('، ');
                            $addr = $order->address ? trim(implode('، ', array_filter([
                                $order->address->area,
                                $order->address->address_name,
                                $order->address->house_no ? 'منزل ' . $order->address->house_no : '',
                                $order->address->flat_no ? 'شقة ' . $order->address->flat_no : '',
                                $order->address->block ? 'قطعة ' . $order->address->block : '',
                                $order->address->road_no ? 'طريق ' . $order->address->road_no : '',
                            ]))) : '';
                        @endphp
                        <div class="card border shadow-sm mb-4">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                                <div class="d-flex flex-wrap align-items-center">
                                    <h5 class="m-0">#{{ $order->order_code }}</h5>
                                    <span class="badge badge-info mr-2">
                                        {{ $order->pivot->status == 'pick-up' ? 'استلام' : 'تسليم' }}
                                    </span>
                                </div>
                                <div style="position: relative; z-index: 1060;">
                                    <div class="dropdown">
                                        <button class="btn btn-info dropdown-toggle" style="min-width:165px; border-radius:30px; font-weight:600;"
                                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __($order->order_status) }}
                                        </button>
                                        <div class="dropdown-menu" style="min-width:210px; border-radius:14px; border:1px solid rgba(13,27,62,.08); box-shadow:0 12px 32px rgba(15,23,42,.18); padding:6px; max-height:340px; overflow:auto;">
                                            @foreach (config('enums.order_status') as $key => $order_status)
                                                <button class="dropdown-item driver-status-change {{ $order->order_status == $order_status ? 'active' : '' }}"
                                                    style="border-radius:10px; font-weight:600; font-size:13px;"
                                                    data-order="{{ $order->id }}" data-status="{{ $key }}" type="button">
                                                    {{ __($order_status) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card h-100" style="border-radius:14px;">
                                            <div class="card-header py-2" style="background:#f8fafc; border-radius:14px 14px 0 0;">
                                                <h6 class="m-0 font-weight-bold">تفاصيل الطلب</h6>
                                            </div>
                                            <div class="card-body pt-2">
                                                <table class="table table-bordered table-striped mb-0">
                                                    <tr>
                                                        <th class="py-2">{{ __('Order') . ' ' . __('Id') }}</th>
                                                        <td class="py-2">#{{ $order->order_code }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تاريخ الطلب</th>
                                                        <td class="py-2">{{ Carbon\Carbon::parse($order->created_at)->format('d M, Y') }} <small>({{ $order->created_at->format('h:i a') }})</small></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تاريخ الاستلام</th>
                                                        <td class="py-2">{{ Carbon\Carbon::parse($order->pick_date)->format('d M, Y') }} <span class="badge badge-light">{{ $order->getTime(substr($order->pick_hour, 0, 2)) }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تاريخ التسليم</th>
                                                        <td class="py-2">{{ Carbon\Carbon::parse($order->delivery_date)->format('d M, Y') }} <span class="badge badge-light">{{ $order->getTime(substr($order->delivery_hour, 0, 2)) }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">حالة الطلب</th>
                                                        <td class="py-2">{{ __($order->order_status) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">النوع</th>
                                                        <td class="py-2 text-capitalize">{{ $order->pivot->status == 'pick-up' ? 'استلام' : 'تسليم' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">حالة الدفع</th>
                                                        <td class="py-2">{{ __($order->payment_status) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">طريقة الدفع</th>
                                                        <td class="py-2">{{ $order->payment_method_label ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">عدد القطع</th>
                                                        <td class="py-2">{{ $quantity }} {{ __('Pieces') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">الإجمالي</th>
                                                        <td class="py-2">{{ currencyPosition($order->total_amount ?? ($order->amount - $order->discount)) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">المنتجات</th>
                                                        <td class="py-2">{{ $productNames ?: 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100" style="border-radius:14px;">
                                            <div class="card-header py-2" style="background:#f8fafc; border-radius:14px 14px 0 0;">
                                                <h6 class="m-0 font-weight-bold">بيانات العميل</h6>
                                            </div>
                                            <div class="card-body pt-2">
                                                <table class="table table-bordered table-striped mb-0">
                                                    <tr>
                                                        <th class="py-2">اسم العميل</th>
                                                        <td class="py-2">{{ $order->customer?->user?->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">البريد الإلكتروني</th>
                                                        <td class="py-2">{{ $order->customer?->user?->email ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">رقم الهاتف</th>
                                                        <td class="py-2" dir="ltr">{{ $order->customer?->user?->mobile ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">العنوان</th>
                                                        <td class="py-2">{{ $addr ?: 'لا يوجد عنوان' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-2">تعليمات إضافية</th>
                                                        <td class="py-2">{{ $order->instruction ?? 'لا توجد' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">لا توجد طلبات معيّنة لك حالياً</div>
                    @endforelse

                    <hr>

                    <h5 class="text-secondary mb-3">سجل الطلبات المكتملة</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th class="px-2">رقم الطلب</th>
                                    <th class="px-2">تاريخ الطلب</th>
                                    <th class="px-2">حالة الطلب</th>
                                    <th class="px-2 text-center">النوع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($histories as $order)
                                <tr>
                                    <td class="px-2">{{ $order->order_code }}</td>
                                    <td class="px-2">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-2">{{ __($order->order_status) }}</td>
                                    <td class="px-2 text-center text-capitalize">{{ $order->pivot->status == 'pick-up' ? 'استلام' : 'تسليم' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا يوجد سجل بعد</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <a class="btn btn-light" href="{{ route('pos.index') }}"> العودة إلى نقطة البيع </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        $(document).on('click', '.driver-status-change', function(e) {
            e.preventDefault();
            const order = $(this).attr('data-order');
            const status = $(this).attr('data-status');
            const url = "{{ route('driver.order.status.change', 'ORDER_ID') }}".replace('ORDER_ID', order);

            $.ajax({
                url: url,
                method: 'POST',
                data: { status: status, _token: "{{ csrf_token() }}" },
                success: function(response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    setTimeout(() => location.reload(), 800);
                },
                error: function(error) {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON?.message || 'فشل تحديث الحالة',
                    });
                },
            });
        });
    </script>
@endpush