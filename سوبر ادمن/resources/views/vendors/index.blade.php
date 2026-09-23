@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="m-0">إدارة المحلات (Vendors)</h2>
            <a href="{{ route('vendors.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة متجر</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable">
                    <thead>
                        <tr>
                            <th>الشعار</th>
                            <th>اسم المحل</th>
                            <th>العنوان</th>
                            <th>رقم الجوال (للدخول)</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                        <tr>
                            <td><img src="{{ $vendor->logoPath }}" width="60" height="60" style="object-fit:cover;border-radius:8px"></td>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->address }}</td>
                            <td><span class="badge badge-light" style="font-size:14px">{{ $vendor->phone }}</span><br><small class="text-muted">الدخول به</small></td>
                            <td>
                                <form action="{{ route('vendors.toggle', $vendor->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <label class="switch"><input type="checkbox" {{ $vendor->is_active?'checked':'' }} onchange="this.form.submit()"><span class="slider round"></span></label>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('vendors.orders', $vendor->id) }}" class="btn btn-sm btn-info" title="عرض طلبات المحل"><i class="fa fa-shopping-cart"></i> الطلبات</a>
                                    <a href="{{ route('vendors.account', $vendor->id) }}" class="btn btn-sm btn-success" title="كشف حساب مفصل"><i class="fa fa-file-invoice-dollar"></i> كشف حساب</a>
                                    <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" style="display:inline" onsubmit="return confirm('حذف المتجر؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
