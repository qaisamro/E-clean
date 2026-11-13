@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                        <h2 class="card-title m-0">
                            {{ __(config('enums.order_status.' . request('status'))) }}
                            {{ __('Orders') }}
                        </h2>
                        <div class="d-flex justify-content-end">
                            <form class="" action="{{ route('order.index') }}" method="GET">

                                <select class="form-control mx-2 float-left" name="sort" style="width: 160px">
                                    <option value="newest"
                                        {{ request('sort') == 'oldest' ? '' : 'selected' }}>
                                        {{ __('Newest') . ' ' . __('First') }}
                                    </option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                        {{ __('Oldest') . ' ' . __('First') }}
                                    </option>
                                </select>

                                <select class="form-control mx-2 float-left" name="status" style="width: 200px">
                                    <option value="">
                                        {{ in_array(config('enums.order_status.' . \request('status')), config('enums.order_status')) ? __(config('enums.order_status.' . request('status'))) : __('All') }}
                                    </option>

                                    @foreach (config('enums.order_status') as $key => $order_status)
                                        <option value="{{ $key }}"

                                            {{ $key == \request('status') ? 'selected' : '' }}>{{ __($order_status) }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="btn btn-primary mx-md-0 mx-2"> <i
                                        class="fa fa-search"></i> </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered {{ session()->get('local') }}" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Order') . ' ' . __('Id') }} </th>
                                        <th scope="col">{{ __('Order') . ' ' . __('By') }}</th>
                                        <th scope="col">{{ __('Order') . ' ' . __('At') }}</th>
                                        <th scope="col">{{ __('Pickup') . ' ' . __('Date') }}</th>
                                        <th scope="col">{{ __('Delivery') . ' ' . __('Date') }}</th>
                                        <th scope="col">{{ __('Amount') }}</th>
                                        <th scope="col">{{ __('Order') . ' ' . __('Status') }}</th>
                                        @canany(['order.show', 'order.print.invioce'])
                                            <th scope="col" class="px-2">{{ __('Actions') }}</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr class="{{ $order->is_show ? '' : 'bg-color' }}">
                                            <td class="py-1">{{ $order->order_code }}</td>
                                            <td class="py-1">
                                                {{ $order->customer->user ? $order->customer->user->name : 'N/A' }}
                                            </td>
                                            <td class="py-1">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>

                                            <td class="py-1">
                                                {{ \Carbon\Carbon::parse($order->pick_date)->format('M d, Y') }}
                                            </td>
                                            <td class="py-1">
                                                {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}
                                            </td>
                                            <td class="py-1">{{ currencyPosition($order->total_amount) }}</td>
                                            <td class="py-1">{{ __($order->order_status) }}</td>
                                            @canany(['order.show', 'order.print.invioce'])
                                                <td class="p-1 ">
                                                    @can('order.show')
                                                        <a href="{{ route('order.show', $order->id) }}"
                                                            class="btn btn-primary btn-sm mb-1">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endcan

                                                    @can('order.edit')
                                                        <a href="{{ route('order.edit', $order->id) }}"
                                                            class="btn btn-warning btn-sm mb-1">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endcan

                                                    @can('order.print.invioce')
                                                        <a class="btn btn-danger btn-sm mb-1 print-same-page"
                                                            href="{{ route('order.print.invioce', $order->id) }}"><i class="fas fa-print"></i> </a>
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
