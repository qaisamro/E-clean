@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-1">حساب فرعي جديد</h2>
                    <p class="text-muted mb-0">موظف ينضم لمتجر «{{ $vendor->name }}» — تختار صلاحياته بعد الإنشاء مباشرة.</p>
                </div>
                <a href="{{ route('vendor.subs.index') }}" class="btn btn-light">إلغاء</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('vendor.subs.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">الاسم الأول <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required maxlength="120" autocomplete="given-name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">الاسم الأخير</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" maxlength="120" autocomplete="family-name">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">رقم الهاتف (لتسجيل الدخول) <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required maxlength="20" dir="ltr" autocomplete="off">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="190" dir="ltr" autocomplete="off">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="6" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="alert alert-light border small mb-3" style="color:#475569;">
                            <i class="fas fa-info-circle text-primary"></i>
                            الحساب يُنشأ <strong>بدون صلاحيات</strong> باستثناء لوحة تحكم المتجر والملف الشخصي — تُحدّد بقية الصلاحيات من صفحة «الصلاحيات» بعد الإنشاء.
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> إنشاء الحساب الفرعي
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
