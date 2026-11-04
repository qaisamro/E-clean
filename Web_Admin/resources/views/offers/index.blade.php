@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h2 class="card-title float-left">{{ __('العروض') }}</h2>
                        </div>

                        <div class="col-md-8">
                            <form action="{{ route('offer.index') }}" method="GET">
                                <ul class="nav nav-pills justify-content-end">
                                    <li class="nav-item ml-2 mr-md-0">
                                        <x-input type="text" name='search' placeholder="{{__('Search')}}" value="{{ request('search') }}" />
                                    </li>
                                    <li class="nav-item ml-2 mr-md-0">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    </li>
                                    @can('offer.create')
                                    <li class="nav-item ml-2 mr-md-0">
                                        <a href="{{ route('offer.create') }}" class="btn btn-primary">
                                            {{__('إضافة عرض')}}
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped {{ session()->get('local') }}" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Title') }}</th>
                                    <th scope="col">{{ __('Vendor') }}</th>
                                    <th scope="col">{{ __('Image') }}</th>
                                    <th scope="col">{{ __('Discount') }}</th>
                                    <th scope="col">{{ __('Description') }}</th>
                                    @can('offer.status.toggle')
                                    <th scope="col">{{ __('Status') }}</th>
                                    @endcan
                                    @canany(['offer.edit', 'offer.destroy'])
                                    <th scope="col">{{ __('Action') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($offers as $offer)
                                <tr>
                                    <td>{{ $offer->title }}</td>
                                    <td>{{ $offer->vendorName }}</td>
                                    <td>
                                        <img width="100" src="{{ asset($offer->thumbnailPath) }}" alt="">
                                    </td>
                                    <td>{{ $offer->discountLabel }}</td>
                                    <td>{{ $offer->description }}</td>
                                    @can('offer.status.toggle')
                                    <td>
                                        <form action="{{ route('offer.status.toggle', $offer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <label class="switch">
                                                <input type="checkbox" {{ $offer->is_active ? 'checked' : '' }} onchange="this.closest('form').submit()">
                                                <span class="slider round"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endcan
                                    @canany(['offer.edit', 'offer.destroy'])
                                    <td>
                                        @can('offer.edit')
                                        <a href="{{ route('offer.edit', $offer->id) }}" class="btn btn-sm btn-primary">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('offer.destroy')
                                        <form action="{{ route('offer.destroy', $offer->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endcan
                                    </td>
                                    @endcanany
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No offers found') }}</td>
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