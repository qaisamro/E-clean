@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
                <a href="{{ route('driver.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{ __('Back') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-9 col-xxl-9 col-lg-9 mt-2 mx-auto ">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title m-0">{{ __('Edit') . ' '.__('Driver') }} - {{ $driver->user->name }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('driver.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="mb-1"><b>{{ __('First_Name') }}</b></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $driver->user->first_name) }}" placeholder="{{ __('First_Name') }}" required>
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="mb-1"><b>{{ __('Last_Name') }}</b></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $driver->user->last_name) }}" placeholder="{{ __('Last_Name') }}">
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-1"><b>{{ __('Email_Address') }}</b></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $driver->user->email) }}" placeholder="{{ __('Email_Address') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="mb-1"><b>{{ __('Mobile_number') }}</b></label>
                                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $driver->user->mobile) }}" placeholder="{{ __('Mobile_number') }}" required>
                                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="mb-1"><b>{{ __('Password') }}</b> <small class="text-muted">({{ __('Leave blank to keep current') }})</small></label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="mb-1"><b>{{ __('Confirm_Password') }}</b></label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('Confirm_Password') }}">
                                </div>
                                <div class="col-12">
                                    <label class="mb-1"><b>{{ __('Profile_Photo') }}</b></label>
                                    <input type="file" name="profile_photo" class="form-control-file @error('profile_photo') is-invalid @enderror" accept="image/*">
                                    @error('profile_photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    @if($driver->user->profile_photo_path)
                                        <img src="{{ $driver->user->profile_photo_path }}" alt="" width="60" class="mt-2 rounded">
                                    @endif
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-2">{{ __('Save_Changes') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
