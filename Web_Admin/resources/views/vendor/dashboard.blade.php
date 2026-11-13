@extends('layouts.app')
@section('styles')
    <style>
        .ec-stat-card { cursor: pointer; border-radius: 16px; overflow: hidden; transition: transform .18s ease, box-shadow .18s ease; }
        .ec-stat-card:hover { transform: translateY(-3px); box-shadow: 0 1.5rem 2rem rgba(2, 128, 144, .18) !important; }
        .ec-stat-card:focus-visible { outline: 3px solid #028090; outline-offset: 2px; }
        .ec-modal-icon { --ic:#028090; flex: 0 0 auto; width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, var(--ic), #083744); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 19px; margin-inline-end: 12px; box-shadow: 0 6px 14px rgba(2, 128, 144, .35); }
        .ec-modal-scroll { max-height: 60vh; overflow-y: auto; }
    </style>
@endsection
@section('content')
@php
    // Child (sub) vendor accounts see this page only with the dashboard permission.
    // Every stat box and the recent-orders table are individually grantable by
    // the owner — each permission stands alone.
    $ecIsSub = auth()->user()?->hasRole('vendor_sub');
    $ecCanForSub = fn ($permission) => !$ecIsSub || (bool) auth()->user()?->can($permission);
    $ecShowOrders = $ecCanForSub('order.index');
    $ecShowProducts = $ecCanForSub('product.index');
    $ecShowOffers = $ecCanForSub('offer.index');
    $ecShowPos = $ecCanForSub('pos.index');
    $ecShowSettings = $ecCanForSub('vendor.settings');
    $ecShowCustomers = $ecCanForSub('vendor.customers.index');
    $ecShowNotify = $ecCanForSub('vendor.notifications.send');
    $ecShowRevenueBox = $ecCanForSub('vendor.dashboard.revenue');
    $ecShowTotalOrdersBox = $ecCanForSub('vendor.dashboard.total_orders');
    $ecShowPendingBox = $ecCanForSub('vendor.dashboard.pending');
    $ecShowDeliveredBox = $ecCanForSub('vendor.dashboard.delivered');
    $ecShowInProgressBox = $ecCanForSub('vendor.dashboard.in_progress');
    $ecShowServicesBox = $ecCanForSub('vendor.dashboard.services');
    $ecShowProductsBox = $ecCanForSub('vendor.dashboard.products');
    $ecShowOffersBox = $ecCanForSub('vendor.dashboard.offers');
    $ecShowAnyBox = collect([
        $ecShowRevenueBox, $ecShowTotalOrdersBox, $ecShowPendingBox, $ecShowDeliveredBox,
        $ecShowInProgressBox, $ecShowServicesBox, $ecShowProductsBox, $ecShowOffersBox,
    ])->contains(fn ($v) => $v);
    $ecShowRecentOrders = $ecCanForSub('vendor.dashboard.recent_orders');
@endphp
<div class="container-fluid mt-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">{{ __('لوحة تحكم') }} - {{ $vendor->name }}</h2>
            <p class="text-muted mb-0">{{ $vendor->address }} | {{ $vendor->phone }}</p>
        </div>
        <div class="col-auto d-flex align-items-center flex-wrap" style="gap:8px">
            @if ($ecShowCustomers)
                <a href="{{ route('vendor.customers.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-users"></i> عملاء المتجر
                </a>
            @endif
            @if ($ecShowNotify)
                <a href="{{ route('vendor.notifications.index') }}" class="btn btn-sm btn-success">
                    <i class="fa fa-paper-plane"></i> إرسال إشعار
                </a>
            @endif
            <span class="badge {{ $vendor->is_active ? 'badge-success' : 'badge-danger' }} p-2">
                {{ $vendor->is_active ? __('نشط') : __('غير نشط') }}
            </span>
        </div>
    </div>

    {{-- Stats — every box is individually grantable by the owner --}}
    @if ($ecShowRevenueBox || $ecShowTotalOrdersBox || $ecShowPendingBox || $ecShowDeliveredBox)
        <div class="row">
            @if ($ecShowRevenueBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card border-left-success shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#revenueModal" aria-label="{{ __('الإيرادات المحصلة') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('الإيرادات المحصلة') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ currencyPosition($stats['revenue']) }}</div>
                        </div>
                    </button>
                </div>
            @endif
            @if ($ecShowTotalOrdersBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card border-left-primary shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#totalOrdersModal" aria-label="{{ __('إجمالي الطلبات') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('إجمالي الطلبات') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['total_orders'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
            @if ($ecShowPendingBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card border-left-warning shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#pendingModal" aria-label="{{ __('بانتظار التأكيد') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('بانتظار التأكيد') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['pending'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
            @if ($ecShowDeliveredBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card border-left-info shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#deliveredModal" aria-label="{{ __('تم التوصيل') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('تم التوصيل') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['delivered'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
        </div>
    @endif

    @if ($ecShowInProgressBox || $ecShowServicesBox || $ecShowProductsBox || $ecShowOffersBox)
        <div class="row">
            @if ($ecShowInProgressBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#inProgressModal" aria-label="{{ __('قيد التنفيذ') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('قيد التنفيذ') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['in_progress'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
            @if ($ecShowServicesBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#servicesModal" aria-label="{{ __('الخدمات') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('الخدمات') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['services'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
            @if ($ecShowProductsBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#productsModal" aria-label="{{ __('المنتجات') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('المنتجات') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['products'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
            @if ($ecShowOffersBox)
                <div class="col-xl-3 col-md-6 mb-4">
                    <button type="button" class="card shadow h-100 py-2 ec-stat-card w-100 text-start border-0"
                        data-toggle="modal" data-target="#offersModal" aria-label="{{ __('العروض') }}">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center justify-content-between">{{ __('العروض') }}
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['offers'] }}</div>
                        </div>
                    </button>
                </div>
            @endif
        </div>
    @endif

    @if (!$ecShowAnyBox && !$ecShowRecentOrders)
        <div class="card shadow-sm mb-4" style="border:1px dashed #FDE68A; background:#FFFBEB;">
            <div class="card-body text-center py-5">
                <i class="fas fa-lock fa-2x mb-2" style="color:#D97706;"></i>
                <h5 class="font-weight-bold mb-1" style="color:#92400E;">لا تُظهر لك أي بطاقات بعد</h5>
                <p class="text-muted mb-0">لم يمنحك مالك المتجر صلاحية عرض بطاقات لوحة التحكم بعد — تواصل معه لتفعيلها.</p>
            </div>
        </div>
    @endif

    {{-- Quick links — the whole row hides when nothing is granted --}}
    @if ($ecShowOrders || $ecShowProducts || $ecShowOffers || $ecShowPos || $ecShowSettings)
        <div class="row mb-4">
            <div class="col">
                @if ($ecShowOrders)
                    <a href="{{ route('order.index') }}" class="btn btn-primary m-1"><i class="fa fa-shopping-cart"></i> {{ __('الطلبات') }}</a>
                @endif
                @if ($ecShowProducts)
                    <a href="{{ route('product.index') }}" class="btn btn-info m-1"><i class="fas fa-tshirt"></i> {{ __('المنتجات') }}</a>
                @endif
                @if ($ecShowOffers)
                    <a href="{{ route('offer.index') }}" class="btn btn-warning m-1"><i class="fas fa-tags"></i> {{ __('العروض') }}</a>
                @endif
                @if ($ecShowPos)
                    <a href="{{ route('pos.index') }}" class="btn btn-success m-1"><i class="fas fa-store-alt"></i> {{ __('نقطة البيع') }}</a>
                @endif
                @if ($ecShowSettings)
                    <a href="{{ route('vendor.settings') }}" class="btn btn-secondary m-1"><i class="fa fa-cog"></i> {{ __('إعدادات المتجر') }}</a>
                @endif
            </div>
        </div>
    @endif

    {{-- Recent orders — grantable --}}
    @if ($ecShowRecentOrders)
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
    @endif

    {{-- ============ Stat modals ============ --}}
    @php
        $orderTableHead = '<tr><th style="width:52px">#</th><th>' . __('الزبون') . '</th><th>' . __('المبلغ') . '</th><th>' . __('الحالة') . '</th><th>' . __('التاريخ') . '</th></tr>';
    @endphp

    @foreach ([
        ['id' => 'revenueModal', 'icon' => 'fa-money-bill-wave', 'color' => '#16a34a', 'title' => __('الإيرادات المحصلة'), 'orders' => $paidOrders, 'link' => route('order.index'), 'perm' => 'vendor.dashboard.revenue'],
        ['id' => 'totalOrdersModal', 'icon' => 'fa-shopping-cart', 'color' => '#028090', 'title' => __('إجمالي الطلبات'), 'orders' => $allOrders, 'link' => route('order.index'), 'perm' => 'vendor.dashboard.total_orders'],
        ['id' => 'pendingModal', 'icon' => 'fa-clock', 'color' => '#d97706', 'title' => __('بانتظار التأكيد'), 'orders' => $pendingOrders, 'link' => route('order.index', ['status' => 'pending']), 'perm' => 'vendor.dashboard.pending'],
        ['id' => 'deliveredModal', 'icon' => 'fa-check-circle', 'color' => '#0f9d98', 'title' => __('تم التوصيل'), 'orders' => $deliveredOrders, 'link' => route('order.index', ['status' => 'delivered']), 'perm' => 'vendor.dashboard.delivered'],
        ['id' => 'inProgressModal', 'icon' => 'fa-spinner', 'color' => '#64748b', 'title' => __('قيد التنفيذ'), 'orders' => $inProgressOrders, 'link' => route('order.index'), 'perm' => 'vendor.dashboard.in_progress'],
    ] as $modal)
        @continue(!$ecCanForSub($modal['perm']))
        <div class="modal fade" id="{{ $modal['id'] }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modal['id'] }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center w-100">
                            <span class="ec-modal-icon" style="--ic:{{ $modal['color'] }}"><i class="fas {{ $modal['icon'] }}"></i></span>
                            <div>
                                <h5 class="modal-title mb-0 font-weight-bold" id="{{ $modal['id'] }}Label">{{ $modal['title'] }}</h5>
                                <small class="text-muted">{{ $modal['orders']->count() }} سجل</small>
                            </div>
                            <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="ec-modal-scroll table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>{!! $orderTableHead !!}</thead>
                                <tbody>
                                    @forelse ($modal['orders'] as $index => $order)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $order->customer?->user?->first_name ?? __('زبون') }} {{ $order->customer?->user?->last_name ?? '' }}</td>
                                            <td>{{ currencyPosition($order->total_amount) }}</td>
                                            <td>{{ __($order->order_status) }}</td>
                                            <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        @if ($ecShowOrders)
                            <a href="{{ $modal['link'] }}" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> {{ __('فتح صفحة الطلبات') }}</a>
                        @endif
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Services modal --}}
    @if ($ecShowServicesBox)
    <div class="modal fade" id="servicesModal" tabindex="-1" role="dialog" aria-labelledby="servicesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <span class="ec-modal-icon" style="--ic:#F59E0B"><i class="fas fa-concierge-bell"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold" id="servicesModalLabel">{{ __('الخدمات') }}</h5>
                            <small class="text-muted">{{ $vendorServices->count() }} سجل</small>
                        </div>
                        <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    <div class="ec-modal-scroll table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">#</th>
                                    <th>{{ __('Service_Name') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('الحالة') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendorServices as $index => $service)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($service->description ?? '—', 90) }}</td>
                                        <td><span class="badge {{ $service->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $service->is_active ? __('نشط') : __('غير نشط') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    @if (!auth()->user()->hasAnyRole(['vendor_admin', 'vendor_sub']))
                        {{-- Store staff are barred from the global services catalog --}}
                        <a href="{{ route('service.index') }}" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> {{ __('فتح صفحة الخدمات') }}</a>
                    @endif
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Products modal --}}
    @if ($ecShowProductsBox)
    <div class="modal fade" id="productsModal" tabindex="-1" role="dialog" aria-labelledby="productsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <span class="ec-modal-icon" style="--ic:#156172"><i class="fas fa-box-open"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold" id="productsModalLabel">{{ __('المنتجات') }}</h5>
                            <small class="text-muted">{{ $vendorProducts->count() }} سجل</small>
                        </div>
                        <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    <div class="ec-modal-scroll table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">#</th>
                                    <th>{{ __('Product_name') }}</th>
                                    <th>{{ __('Service') }}</th>
                                    <th>{{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendorProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->service?->name ?? '—' }}</td>
                                        <td>{{ currencyPosition($product->discount_price ?: $product->price) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    @if ($ecShowProducts)
                        <a href="{{ route('product.index') }}" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> {{ __('فتح صفحة المنتجات') }}</a>
                    @endif
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Offers modal --}}
    @if ($ecShowOffersBox)
    <div class="modal fade" id="offersModal" tabindex="-1" role="dialog" aria-labelledby="offersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <span class="ec-modal-icon" style="--ic:#dc2626"><i class="fas fa-tags"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold" id="offersModalLabel">{{ __('العروض') }}</h5>
                            <small class="text-muted">{{ $vendorOffers->count() }} سجل</small>
                        </div>
                        <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    <div class="ec-modal-scroll table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">#</th>
                                    <th>{{ __('العرض') }}</th>
                                    <th>{{ __('الخصم') }}</th>
                                    <th>{{ __('المنتج') }}</th>
                                    <th>{{ __('الحالة') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendorOffers as $index => $offer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $offer->title }}</td>
                                        <td>{{ $offer->discountLabel }}</td>
                                        <td>{{ $offer->product?->name ?? '—' }}</td>
                                        <td><span class="badge {{ $offer->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $offer->is_active ? __('Active') : __('Inactive') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    @if ($ecShowOffers)
                        <a href="{{ route('offer.index') }}" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> {{ __('فتح صفحة العروض') }}</a>
                    @endif
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
