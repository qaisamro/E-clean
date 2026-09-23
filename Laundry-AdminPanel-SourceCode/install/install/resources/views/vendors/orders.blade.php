@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="m-0">طلبات المحل: {{ $vendor->name }} <small class="text-muted">({{ $vendor->phone }})</small></h3>
            <a href="{{ route('vendors.index') }}" class="btn btn-light">رجوع لإدارة المحلات</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>العنوان</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->customer->user->name ?? 'N/A' }}<br><small>{{ $order->customer->user->mobile ?? '' }}</small></td>
                            <td>{{ $order->address->area ?? '' }} - {{ Str::limit($order->address->address_line ?? '', 30) }}</td>
                            <td>{{ currencyPosition($order->total_amount) }}</td>
                            <td><span class="badge badge-info">{{ $order->order_status }}</span></td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td><a href="{{ route('order.show', $order->id) }}" class="btn btn-sm btn-primary">عرض</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">لا توجد طلبات لهذا المحل</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
