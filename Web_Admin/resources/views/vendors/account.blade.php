@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="m-0">كشف حساب المحل: {{ $vendor->name }}</h3>
            <a href="{{ route('vendors.index') }}" class="btn btn-light">رجوع</a>
        </div>
        <div class="card-body">
            <form method="GET" class="row mb-4">
                <div class="col-md-4"><label>من تاريخ</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
                <div class="col-md-4"><label>إلى تاريخ</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
                <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">فلترة</button></div>
            </form>
            <div class="row text-center">
                <div class="col-md-3"><div class="card bg-light p-3"><h5>إجمالي الطلبات</h5><h3>{{ $stats['total_orders'] }}</h3></div></div>
                <div class="col-md-3"><div class="card bg-success text-white p-3"><h5>إجمالي المبيعات</h5><h3>{{ currencyPosition($stats['total_sales']) }}</h3></div></div>
                <div class="col-md-3"><div class="card bg-info text-white p-3"><h5>صافي الإيرادات (مدفوع)</h5><h3>{{ currencyPosition($stats['total_revenue']) }}</h3></div></div>
                <div class="col-md-3"><div class="card bg-warning p-3"><h5>غير مدفوع</h5><h3>{{ $stats['unpaid_orders'] }}</h3></div></div>
            </div>
            <hr>
            <h5>سجل الطلبات المفصل</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="myTable">
                    <thead><tr><th>رقم الطلب</th><th>العميل</th><th>المبلغ</th><th>حالة الدفع</th><th>التاريخ</th></tr></thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->customer->user->name ?? '' }}</td>
                            <td>{{ currencyPosition($order->total_amount) }}</td>
                            <td>{{ $order->payment_status }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
