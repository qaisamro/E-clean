@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-1">تعديل «{{ $sub->user?->name }}»</h2>
                    <p class="text-muted mb-0">بيانات الدخول فقط — الصلاحيات تُدار من صفحة «الصلاحيات».</p>
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
                    <form method="POST" action="{{ route('vendor.subs.update', $sub->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">الاسم الأول <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $sub->user?->first_name) }}" required maxlength="120">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">الاسم الأخير</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $sub->user?->last_name) }}" maxlength="120">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">رقم الهاتف (لتسجيل الدخول) <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $sub->user?->mobile) }}" required maxlength="20" dir="ltr">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $sub->user?->email) }}" maxlength="190" dir="ltr">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">كلمة مرور جديدة</label>
                                <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password" placeholder="اتركه فارغاً للإبقاء على الحالية">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="6" autocomplete="new-password" placeholder="كرر كلمة المرور الجديدة">
                            </div>
                        </div>

                        @if ($sub->user?->is_active)
                            <div class="small text-muted mb-3"><i class="fas fa-check-circle text-success"></i> الحساب نشط حالياً</div>
                        @else
                            <div class="small text-muted mb-3"><i class="fas fa-user-slash text-danger"></i> الحساب معطّل حالياً (يمكن تفعيله من القائمة الرئيسية)</div>
                        @endif

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
