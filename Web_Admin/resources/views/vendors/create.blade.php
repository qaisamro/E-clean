@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card col-lg-8 mx-auto">
        <div class="card-header"><h3>إضافة متجر جديد</h3></div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('vendors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3"><label>اسم المحل *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>رقم الجوال (للدخول) *</label><input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone') }}" placeholder="05xxxxxxxx">@error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>الشعار</label><input type="file" name="logo" class="form-control"></div>
                    <div class="col-12 mb-3"><label>العنوان التفصيلي *</label><textarea name="address" class="form-control @error('address') is-invalid @enderror" required rows="2">{{ old('address') }}</textarea>@error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>كلمة المرور *</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>تأكيد كلمة المرور *</label><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <button class="btn btn-primary w-100">إنشاء المتجر</button>
            </form>
        </div>
    </div>
</div>
@endsection
