@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title float-left my-1">{{ __('All') . ' ' . __('Service') }}</h2>
                        <div class="w-100 text-right">
                            @can('additional.index')
                                <a href="{{ route('additional.index') }}"
                                    class="text-right btn btn-primary my-md-0 my-1">{{ __('Additional_Services') }}</a>
                            @endcan
                            @can('service.create')
                                <a href="{{ route('service.create') }}"
                                    class="text-right btn btn-primary my-md-0 my-1">{{ __('Add_New') . ' ' . __('Service') }}</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped verticle-middle table-responsive-sm {{ session()->get('local') }}"
                                id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Name') . ' ' . __('of') . ' ' . __('English') }}</th>
                                        <th scope="col">{{ __('Name') . ' ' . __('of') . ' ' . __('Arabic') }}</th>
                                        <th scope="col">{{ __('Thumbnail') }}</th>
                                        <th scope="col">{{ __('Description') }}</th>
                                        @can('service.status.toggle')
                                            <th scope="col">{{ __('Status') }}</th>
                                        @endcan
                                        <th scope="col">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $service) 
                                        <tr>
                                            <td>{{ $service->name }}</td>
                                            <td>{{ $service->name_bn }}</td>
                                            <td>
                                                <img width="100" src="{{ asset($service->thumbnailPath) }}"
                                                    alt="">
                                            </td>
                                            <td>
                                                {{ substr($service->description, 0, 25) }}
                                            </td>
                                            @can('service.status.toggle')
                                                <td>
                                                    <form action="{{ route('service.status.toggle', $service->id) }}" method="POST" style="display:inline">
                                                        @csrf
                                                        <label class="switch @role('visitor') visitorMessage @endrole">
                                                            <input {{ $service->is_active ? 'checked' : '' }} type="checkbox" onchange="if(!this.closest('label').classList.contains('visitorMessage')) this.form.submit()">
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                            @endcan

                                            <td>
                                                @can('service.edit')
                                                    <a href="{{ route('service.edit', $service->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('service.delete')
                                                    <form action="{{ route('service.delete', $service->id) }}" method="POST" style="display:inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                @endcan
                                            </td>
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
