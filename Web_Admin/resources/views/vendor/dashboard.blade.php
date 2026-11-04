@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">{{ __('لوحة تحكم') }} - {{ $vendor->name }}</h2>
            <p class="text-muted mb-0">{{ $vendor->address }} | {{ $vendor->phone }}</p>
        </div>
        <div class="col-auto">
            <span class="badge {{ $vendor->is_active ? 'badge-success' : 'badge-danger' }} p-2">
                {{ $vendor->is_active ? __('نشط') : __('غير نشط') }}
            </span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('الإيرادات المحصلة') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ currencyPosition($stats['revenue']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('إجمالي الطلبات') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['total_orders'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('بانتظار التأكيد') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('تم التوصيل') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['delivered'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">{{ __('قيد التنفيذ') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">{{ __('الخدمات') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['services'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">{{ __('المنتجات') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['products'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">{{ __('العروض') }}</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $stats['offers'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('order.index') }}" class="btn btn-primary m-1"><i class="fa fa-shopping-cart"></i> {{ __('الطلبات') }}</a>
            <a href="{{ route('service.index') }}" class="btn btn-info m-1"><i class="fas fa-tools"></i> {{ __('الخدمات') }}</a>
            <a href="{{ route('product.index') }}" class="btn btn-info m-1"><i class="fas fa-tshirt"></i> {{ __('المنتجات') }}</a>
            <a href="{{ route('offer.index') }}" class="btn btn-warning m-1"><i class="fas fa-tags"></i> {{ __('العروض') }}</a>
            <a href="{{ route('pos.index') }}" class="btn btn-success m-1"><i class="fas fa-store-alt"></i> {{ __('نقطة البيع') }}</a>
            <a href="{{ route('vendor.settings') }}" class="btn btn-secondary m-1"><i class="fa fa-cog"></i> {{ __('إعدادات المتجر') }}</a>
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('أحدث الطلبات') }}</h6>
        </div>
        <div class="card-body">
            @if ($recentOrders->isEmpty())
                <p class="text-muted mb-0">{{ __('لا توجد طلبات بعد') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('الزبون') }}</th>
                                <th>{{ __('المبلغ') }}</th>
                                <th>{{ __('الحالة') }}</th>
                                <th>{{ __('التاريخ') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer?->user?->first_name ?? __('زبون') }}</td>
                                    <td>{{ currencyPosition($order->total_amount) }}</td>
                                    <td>{{ $order->order_status }}</td>
                                    <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
