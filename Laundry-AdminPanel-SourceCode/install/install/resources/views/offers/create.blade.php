@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
                <a href="{{ route('offer.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>
                    {{ __('Back') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title m-0">{{ __('إضافة') . ' ' . __('العرض') }}</h2>
                    </div>
                    <div class="card-body">
                        <x-form route="offer.store" type="Submit">
                            <label class="mb-1">{{ __('العرض') . ' ' . __('عنوان') }}</label>
                            <x-input name="title" type="text"
                                placeholder="{{ __('العرض') . ' ' . __('عنوان') }}" />

                            <label class="mb-1 mt-3">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" placeholder="{{ __('Description') }}"></textarea>

                            @role('root')
                            <label class="mb-1 mt-3">{{ __('Select') . ' ' . __('Vendor') }}</label>
                            <x-select name="vendor_id">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </x-select>
                            @endrole

                            <label class="mb-1 mt-3">{{ __('Discount') . ' ' . __('Type') }}</label>
                            <x-select name="discount_type">
                                <option value="percentage">% {{ __('Percentage') }}</option>
                                <option value="fixed">{{ __('Fixed') }} {{ __('Amount') }}</option>
                            </x-select>

                            <label class="mb-1 mt-3">{{ __('Discount') . ' ' . __('Value') }}</label>
                            <input name="discount_value" type='text' class="form-control"
                                placeholder="{{ __('Discount') . ' ' . __('Value') }}" onkeypress="onlyNumber(event)" />

                            <label class="mb-1 mt-3">{{ __('العرض') . ' ' . __('صورة') }}</label>
                            <x-input-file name="image" type="file" />

                            <div class="form-group mt-3">
                                <label for="active" class="mr-2">
                                    <input checked type="radio" id="active" name="active" value="1">
                                    {{ __('Active') }}
                                </label>

                                <label for="inActive">
                                    <input type="radio" id="inActive" name="active" value="0">
                                    {{ __('Inactive') }}
                                </label>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function onlyNumber(evt) {
            var chars = String.fromCharCode(evt.which);
            if (!(/[0-9.]/.test(chars))) {
                evt.preventDefault();
            }
        }
    </script>
@endpush
