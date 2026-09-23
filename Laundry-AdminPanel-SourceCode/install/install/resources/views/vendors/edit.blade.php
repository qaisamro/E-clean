@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card col-lg-8 mx-auto">
        <div class="card-header d-flex justify-content-between"><h3>تعديل متجر: {{ $vendor->name }}</h3><a href="{{ route('vendors.index') }}" class="btn btn-light">رجوع</a></div>
        <div class="card-body">
            <form action="{{ route('vendors.update', $vendor->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3"><label>اسم المحل *</label><input type="text" name="name" class="form-control" required value="{{ old('name', $vendor->name) }}"></div>
                    <div class="col-md-6 mb-3"><label>رقم الجوال (للدخول) *</label><input type="text" name="phone" class="form-control" required value="{{ old('phone', $vendor->phone) }}"></div>
                    <div class="col-12 mb-3"><label>العنوان *</label><textarea name="address" class="form-control" required rows="2">{{ old('address', $vendor->address) }}</textarea></div>
                    <div class="col-md-6 mb-3"><label>الشعار الحالي</label><br><img src="{{ $vendor->logoPath }}" width="80" class="rounded"></div>
                    <div class="col-md-6 mb-3"><label>تغيير الشعار</label><input type="file" name="logo" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label>كلمة مرور جديدة (اتركه فارغاً للاحتفاظ)</label><input type="password" name="password" class="form-control"><input type="password" name="password_confirmation" class="form-control mt-1" placeholder="تأكيد"></div>
                </div>
                <button class="btn btn-primary w-100">تحديث</button>
            </form>
        </div>
    </div>
</div>
@endsection
