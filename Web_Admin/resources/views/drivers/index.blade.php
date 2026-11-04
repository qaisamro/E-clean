@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="m-0"> {{ __('Drivers List') }}</h2>
                    <div>
                        <a href="{{ route('driver.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> {{ __('Add Driver') }}
                        </a>
                        @if (request()->deactive)
                        <a href="{{ route('driver.index') }}" class="btn btn-info">
                            {{ __('Active Driver') }}
                        </a>
                        @else
                        <a href="{{ route('driver.index','deactive=1') }}" class="btn btn-danger">
                            {{ __('Inactive Driver') }}
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($drivers as $driver)
                    <div class="card border shadow-sm mb-4">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0">{{ $driver->user->name }}</h5>
                                <small class="text-muted">{{ $driver->user->email }} &nbsp;|&nbsp; {{ $driver->user->mobile ?? 'N/A' }} &nbsp;|&nbsp; {{ $driver->user->created_at->format('d M, Y') }}</small>
                            </div>
                            <div class="d-flex gap-1 align-items-center">
                                @if (request()->deactive)
                                <form action="{{ route('user.status.toggle', $driver->user->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <label class="switch">
                                        <input {{ $driver->user->is_active ? 'checked':'' }} type="checkbox" onchange="this.form.submit()">
                                        <span class="slider round"></span>
                                    </label>
                                </form>
                                @else
                                <form action="{{ route('driver.status.toggle', $driver->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <label class="switch">
                                        <input {{ $driver->is_approve ? 'checked':'' }} type="checkbox" onchange="this.form.submit()">
                                        <span class="slider round"></span>
                                    </label>
                                </form>
                                @endif
                                <a href="{{ route('driver.details', $driver->id) }}" class="btn btn-primary btn-sm" title="{{ __('Show') }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('driver.edit', $driver->id) }}" class="btn btn-warning btn-sm" title="{{ __('Edit') }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('driver.destroy', $driver->id) }}" method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف السائق؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Delete') }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary mb-3">{{ __('Assigned Orders') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-2">{{ __('Id') }}</th>
                                            <th scope="col" class="px-2">{{ __('Order') . ' '.__('Date') }}</th>
                                            <th scope="col" class="px-2">{{ __('Pickup') . ' '.__('Date') }}</th>
                                            <th scope="col" class="px-2">{{ __('Delivery') . ' '.__('Date') }}</th>
                                            <th scope="col" class="px-2">{{ __('Order') . ' '.__('Status') }}</th>
                                            <th scope="col" class="px-2 text-center">{{ __('Amount') }}</th>
                                            <th scope="col" class="px-2 text-center">{{ __('Assign') }}</th>
                                            <th scope="col" class="px-2 text-center">{{ __('Action')  }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($driver->orders as $order)
                                        <tr>
                                            <td class="px-2">{{ $order->order_code }}</td>
                                            <td class="px-2">
                                                {{ $order->created_at->format('M d, Y') }}<br>
                                                <small>{{ $order->created_at->format('h:i a') }}</small>
                                            </td>
                                            <td class="px-2">
                                                <span style="font-size: 14px">
                                                    {{ Carbon\Carbon::parse($order->pick_date)->format('M d, Y') }}<br>
                                                </span>
                                                <small>
                                                    {{ $order->getTime(substr($order->pick_hour, 0, 2)) }}
                                                </small>
                                            </td>
                                            <td class="px-2">
                                                <span style="font-size: 14px">
                                                    {{ Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}<br>
                                                </span>
                                                <small>
                                                    {{ $order->getTime(substr($order->delivery_hour, 0, 2)) }}
                                                </small>
                                            </td>
                                            <td class="px-2">{{ $order->order_status }}</td>
                                            <td class="px-2 text-center">{{ $order->amount - $order->discount }}</td>
                                            <td class="px-2 text-center text-capitalize">{{ $order->pivot->status }}</td>
                                            <td class="px-2 text-center">
                                                <a href="{{ route('order.show', $order->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">{{ __('No assigned orders yet') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">{{ __('No drivers found') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection