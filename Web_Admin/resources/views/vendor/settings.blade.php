@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('إعدادات المتجر') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @php
                            $vendor = \App\Models\Vendor::where('user_id', auth()->id())->first();
                        @endphp
                        <div class="mb-3">
                            <label>{{ __('اسم المتجر') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $vendor?->name) }}">
                        </div>
                        <div class="mb-3">
                            <label>{{ __('عنوان المتجر') }}</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $vendor?->address) }}">
                        </div>
                        <div class="mb-3">
                            <label>{{ __('رقم الهاتف') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $vendor?->phone) }}">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ __('حفظ') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
