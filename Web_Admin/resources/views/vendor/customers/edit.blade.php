@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px">
        <div>
            <h2 class="mb-1">تعديل بيانات العميل</h2>
        </div>
        <a href="{{ route('vendor.customers.index') }}" class="btn btn-light border">
            <i class="fa fa-arrow-right"></i> عودة للعملاء
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center" style="gap:12px; border-bottom:1px solid #eef2f7">
                    <span class="d-inline-flex align-items-center justify-content-center"
                          style="width:48px;height:48px;border-radius:12px;background:#e8f6f4;color:#028090;font-weight:800;font-size:20px">
                        {{ mb_substr(trim($customer->user->first_name ?? $customer->user->name ?? '؟'), 0, 1) }}
                    </span>
                    <div>
                        <div class="font-weight-bold" style="font-size:15px">{{ trim(($customer->user->first_name ?? '') . ' ' . ($customer->user->last_name ?? '')) ?: $customer->user->name }}</div>
                        <small class="text-muted">عميل #{{ $customer->id }} — {{ $customer->user->mobile ?? '—' }}</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('vendor.customers.update', $customer->id) }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold mb-1" for="first_name">الاسم الأول <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name"
                                       value="{{ old('first_name', $customer->user->first_name) }}"
                                       placeholder="الاسم الأول" required>
                                @error('first_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold mb-1" for="last_name">اسم العائلة</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name"
                                       value="{{ old('last_name', $customer->user->last_name) }}"
                                       placeholder="اسم العائلة">
                                @error('last_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold mb-1" for="mobile">رقم الجوال <span class="text-danger">*</span></label>
                            <input type="text" inputmode="numeric" class="form-control @error('mobile') is-invalid @enderror"
                                   id="mobile" name="mobile" dir="ltr" style="text-align:right"
                                   value="{{ old('mobile', $customer->user->mobile) }}"
                                   placeholder="05xxxxxxxx" required>
                            <small class="text-muted d-block mt-1">يستخدم هذا الرقم لتسجيل الدخول في التطبيق.</small>
                            @error('mobile')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold mb-1" for="password">كلمة المرور الجديدة</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password"
                                       placeholder="********" autocomplete="new-password" minlength="6">
                                <small class="text-muted d-block mt-1">اتركها فارغة للإبقاء على كلمة المرور الحالية</small>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold mb-1" for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="********" autocomplete="new-password" minlength="6">
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold mb-1" for="email">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" dir="ltr" style="text-align:right"
                                   value="{{ old('email', $customer->user->email) }}"
                                   placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex flex-wrap" style="gap:10px">
                            <button type="submit" class="btn btn-success px-4" style="border-radius:10px; font-weight:800">
                                <i class="fa fa-save"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('vendor.customers.index') }}" class="btn btn-light border px-4" style="border-radius:10px; font-weight:700">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
