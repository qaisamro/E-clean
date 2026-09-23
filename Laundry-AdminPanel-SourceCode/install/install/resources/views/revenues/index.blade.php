@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-3 text-right">
                            <h4 class="card-title text-uppercase text-muted mb-0">{{ __('Total_Sales') }}</h4>
                            <span class="display-3 text-dark font-weight-bold mb-0">
                                @can('dashboard.calculation')
                                    {{ currencyPosition($stats['total_sales']) }}
                                @else
                                    —
                                @endcan
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-3 text-right">
                            <h4 class="card-title text-uppercase text-muted mb-0">{{ __('Total_Orders') }}</h4>
                            <span class="display-3 text-dark font-weight-bold mb-0">{{ $stats['total_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-3 text-right">
                            <h4 class="card-title text-uppercase text-muted mb-0">{{ __('Paid_Orders') }}</h4>
                            <span class="display-3 text-success font-weight-bold mb-0">{{ $stats['paid_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-3 text-right">
                            <h4 class="card-title text-uppercase text-muted mb-0">{{ __('Unpaid_Orders') }}</h4>
                            <span class="display-3 text-danger font-weight-bold mb-0">{{ $stats['unpaid_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-3 text-right">
                            <h4 class="card-title text-uppercase text-muted mb-0">{{ __('Cash_Orders') }}</h4>
                            <span class="display-3 text-dark font-weight-bold mb-0">{{ $stats['cash_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-3 text-right">
                            <h4 class="card-title text-uppercase text-muted mb-0">{{ __('Visa_Orders') }}</h4>
                            <span class="display-3 text-dark font-weight-bold mb-0">{{ $stats['visa_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <h4 class="card-title text-uppercase text-muted mb-3">{{ __('Top_Products') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stats['top_products'] as $top)
                                    <tr>
                                        <td>{{ $top['name'] }}</td>
                                        <td>{{ $top['quantity'] }}</td>
                                        <td>{{ currencyPosition($top['amount']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">{{ __('No data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <!-- Revenue Charts -->
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">{{__('Income')}}</h6>
                                <h2 class="text-default mb-0">{{__('Revenue')}}</h2>
                            </div>

                            <div class="col-md-8">
                            <form action="{{ route('revenue.index') }}" method="GET">
                                <ul class="nav nav-pills justify-content-end">
                                    <li class="nav-item m-0">
                                        <x-input type="date" name='from' placeholder="Search Date" value="{{ request('from') }}" />
                                    </li>
                                    <li class="nav-item m-0 ml-1">
                                        <x-input type="date" name='to' placeholder="Search Date" value="{{ request('to') }}" />
                                    </li>
                                    <li class="nav-item m-0">
                                       <button type="submit" class="btn btn-info ml-1">{{__('Filter')}}</button>
                                    </li>
                                </ul>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Delivery_Date') }}</th>
                                        <th scope="col">{{__('Order_By')}}</th>
                                        <th scope="col">{{__('Quantity')}}</th>
                                        <th scope="col">{{__('Total')}}</th>
                                        @can('order.show')
                                        <th scope="col">{{__('Action')}}</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($revenues as $revenue)
                                        <tr>
                                            <td>
                                                {{ Carbon\Carbon::parse($revenue->delivery_date)->format('M d, Y') }} <br>
                                                <small>{{ Carbon\Carbon::parse($revenue->delivery_hour)->format('h:i a') }}</small>
                                            </td>
                                            @php
                                                $quantity = 0;
                                                foreach($revenue->products as $product){
                                                    $quantity += $product->pivot->quantity;
                                                }
                                            @endphp
                                            <td>{{ $revenue->customer->user?->name ?? 'N/A' }}</td>
                                            <td>{{ $quantity }} {{__('Pieces')}}</td>
                                            <td>{{ currencyPosition($revenue->total_amount) }}</td>
                                            @can('order.show')
                                            <td>
                                                <a href="{{ route('order.show', $revenue->id) }}" class="btn btn-primary">{{__('Details')}}</a>
                                            </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">{{__('Sorry! Revenue report not found')}}</td>
                                        </tr>
                                    @endforelse
                                    <tr>
                                        <td @can('order.show') colspan="3" @else colspan="2" @endcan class="text-right">{{__('Total_Revenue')}}</td>
                                        <td>
                                            @can('dashboard.calculation')
                                                {{ currencyPosition($revenues->sum('total_amount')) }}
                                            @else
                                                —
                                            @endcan
                                        </td>
                                        <td>
                                            @can('report.generate.pdf')
                                            <a class="btn btn-warning print-same-page"
                                            href="{{ route('report.generate.pdf', ['from' => \request('from'), 'to' => \request('to')]) }}">{{__('Print_Report')}}</a>
                                            @endcan
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
