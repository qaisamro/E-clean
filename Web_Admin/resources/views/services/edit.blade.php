@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="col-sm-6 p-md-0  mt-2 mt-sm-0 d-flex">
            <a href="{{ route('service.index') }}" class="btn btn-primary mb-1"><i class="fa fa-arrow-left"></i>
                {{ __('Back') }}</a>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Edit') . ' ' . __('Service') }}</h4>
                    </div>
                    <div class="card-body">
                        <x-form route="service.update" updateId="{{ $service->id }}" type="Submit" method="true">
                            <div class="form-group text-center">
                                <img width="50%" src="{{ $service->thumbnailPath }}" alt="">
                            </div>

                            <label>{{ __('Name') }}</label>
                            <x-input name="name_bn" type='text' value="{{ $service->name_bn }}"
                                placeholder="{{ __('Service') . ' ' . __('Name') }}" required="true" />

                            <label>{{ __('Service') . ' ' . __('Photo') }}</label>
                            <x-input-file name="image" type="file" />

                            <label>{{ __('Service') . ' ' . __('Description') }}</label>
                            <x-textarea name="description" placeholder="{{ __('Service') . ' ' . __('Description') }}"
                                value="{{ $service->description }}" />

                        </x-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#name').keyup(function() {
            $('#slug').val($(this).val().toLowerCase().split(',').join('').replace(/\s/g, "-"));
        });
    </script>
@endpush
