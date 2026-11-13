@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="card-title">إنشاء كوبون خصم عام</h2>
                        </div>

                        <div class="col-6 position-relative">
                            <div class="position-absolute" style="right: 1em">
                                <a href="{{ route('coupon.admin.index') }}" class="btn btn-dark"><i class="fa fa-arrow-left"></i> رجوع</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        هذا الكوبون <strong>عام</strong>: يُطبَّق على جميع المتاجر ويمكن لجميع العملاء استخدامه في التطبيق.
                        كوبونات المتاجر الخاصة تُدار من واجهة صاحب المتجر ولا تظهر هنا.
                    </div>
                    <div class="col-8 m-auto">
                        <form action="{{ route('coupon.admin.store') }}" method="POST"> @csrf
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <label class="mb-1">كود الكوبون</label>
                                    <x-input name="code" type="text" placeholder="أدخل كود الكوبون"/>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="mb-1">نوع الخصم</label>
                                    <select name="discount_type" class="form-control @error('discount_type') is-invalid @enderror">
                                        <option value="">اختر نوع الخصم</option>
                                        @foreach (config('enums.coupons.discount_types') as $discountType)
                                        <option value="{{ $discountType }}">{{ __($discountType) }}</option>
                                        @endforeach
                                    </select>
                                    @error('discount_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="mb-1">قيمة الخصم</label>
                                    <x-input name="discount" type="text" placeholder="أدخل قيمة الخصم"/>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="mb-1">الحد الأدنى للطلب</label>
                                    <x-input name="min_amount" type="text" placeholder="أدخل الحد الأدنى"/>
                                </div>

                                <div class="col-12">
                                    <label>يبدأ في</label>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <x-input type="date" name="start_date"/>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <x-input type="time" name="start_time"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label>ينتهي في</label>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <x-input type="date" name="expired_date"/>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <x-input type="time" name="expired_time"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <label for="yes">
                                                <input type="radio" id="yes" name="notify" value="1" checked> إشعار جميع العملاء.
                                            </label>
                                            <label for="no" class="ml-3">
                                                <input type="radio" id="no" name="notify" value="0"> بدون إشعار
                                            </label>
                                        </div>
                                        <div class="col-12 col-md-6 text-right">
                                            <button type="submit" class="btn btn-primary px-5">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
