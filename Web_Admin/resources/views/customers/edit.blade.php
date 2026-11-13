@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="w-100">
                            <h2 class="float-left">{{ __('Edit Customer') }}</h2>
                            <div class="text-right">
                                <a class="btn btn-light" href="{{ route('customer.index') }}"> {{ __('Back') }} </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="first_name">{{ __('First_Name') }} <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        value="{{ old('first_name', optional($customer->user)->first_name) }}"
                                        placeholder="{{ __('First_Name') }}">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="last_name">{{ __('Last_Name') }}</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        value="{{ old('last_name', optional($customer->user)->last_name) }}"
                                        placeholder="{{ __('Last_Name') }}">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="mobile">{{ __('Mobile_number') }} <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="mobile" id="mobile"
                                        value="{{ old('mobile', optional($customer->user)->mobile) }}"
                                        placeholder="{{ __('Mobile_number') }}">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="{{ old('email', optional($customer->user)->email) }}"
                                        placeholder="{{ __('Email') }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="password">
                                        {{ __('New_Password') }}
                                        @role('root|admin')
                                            <span class="badge bg-danger ms-1" title="{{ __('Admin only') }}">{{ __('admin') }}</span>
                                        @endrole
                                        <small class="text-muted d-block">{{ __('Leave blank to keep current') }}</small>
                                    </label>
                                    @role('root|admin')
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="{{ __('New_Password') }}">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordEdit" tabindex="-1" aria-label="Toggle password visibility">
                                            <i class="fa fa-eye" id="togglePasswordEditIcon"></i>
                                        </button>
                                    </div>
                                    @else
                                    <input type="password" class="form-control" name="password" id="password" disabled placeholder="{{ __('Admin only') }}">
                                    @endrole
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="password_confirmation">{{ __('Confirm_Password') }}</label>
                                    @role('root|admin')
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                                        placeholder="{{ __('Confirm_Password') }}">
                                    @endrole
                                    @error('password_confirmation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-2 py-2">
                                    <label for="profile_photo">{{ __('Profile_Photo') }}</label>
                                    @if (optional($customer->user)->profilePhotoPath)
                                        <div class="mb-2">
                                            <img src="{{ $customer->user->profilePhotoPath }}"
                                                alt="{{ $customer->user->name }}"
                                                width="80" height="80"
                                                class="rounded-circle border">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file" name="profile_photo" id="profile_photo"
                                        accept="image/jpg,image/jpeg,image/png,image/webp">
                                    @error('profile_photo')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                @canany(['customer.edit', 'customer.update'])
                                <div class="col-12 col-md-6 mb-2 py-2 d-flex align-items-end">
                                    <button type="submit"
                                        class="btn btn-primary w-100 mt-2 @role('visitor') visitorMessage @endrole">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                                @endcanany
                            </div>
                        </form>

                        @can('customer.delete')
                        <hr class="my-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between"
                            style="gap:14px; background:#fff5f5; border:1px solid #fecaca; border-radius:14px; padding:14px 16px;">
                            <div>
                                <div class="text-danger" style="font-weight:800; font-size:14px;">{{ __('Delete') }} — {{ optional($customer->user)->first_name }} {{ optional($customer->user)->last_name }}</div>
                                <small class="text-muted d-block mt-1">حذف نهائي: سيتم حذف العميل وكل طلباته وعناوينه وأجهزته. لا يمكن التراجع.</small>
                            </div>
                            <form action="{{ route('customer.delete', $customer->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف العميل نهائياً؟ سيتم حذف جميع طلباته وعناوينه.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2" style="border-radius:10px; font-weight:700;">
                                    <i class="fa fa-trash"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const toggle = document.getElementById('togglePasswordEdit');
            if (!toggle) return;
            const field = document.getElementById('password');
            const icon = document.getElementById('togglePasswordEditIcon');
            const confirm = document.getElementById('password_confirmation');
            toggle.addEventListener('click', function () {
                const show = field.type === 'password';
                field.type = show ? 'text' : 'password';
                if (confirm) confirm.type = show ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !show);
                icon.classList.toggle('fa-eye-slash', show);
            });
        })();
    </script>
@endpush
