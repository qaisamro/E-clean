@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4" style="gap:12px;">
        <div>
            <h2 class="mb-1">الحسابات الفرعية <span class="text-muted">— {{ $vendor->name }}</span></h2>
            <p class="text-muted mb-0">حسابات موظفين تُدير المتجر بنفس النطاق, وتختار أنت بدقة الصلاحيات المعطاة لكل حساب.</p>
        </div>
        <a href="{{ route('vendor.subs.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> إضافة حساب فرعي
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            @if ($subs->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-users-cog fa-3x mb-3 d-block" style="color:#CBD5E1;"></i>
                    لا توجد حسابات فرعية بعد — اضغط «إضافة حساب فرعي» لإنشاء أول حساب.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>رقم الهاتف</th>
                                <th>البريد</th>
                                <th>الحالة</th>
                                <th>منح الصلاحيات</th>
                                <th class="text-center">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subs as $index => $sub)
                                @php
                                    $granted = $sub->user?->getPermissionNames()->count() ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $sub->user?->name ?? '—' }}</div>
                                        <small class="text-muted">أُنشئ {{ $sub->created_at?->format('Y-m-d') }}</small>
                                    </td>
                                    <td dir="ltr">{{ $sub->user?->mobile ?? '—' }}</td>
                                    <td dir="ltr">{{ $sub->user?->email ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $sub->user?->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $sub->user?->is_active ? 'نشط' : 'معطّل' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $granted }} صلاحية</span>
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <a href="{{ route('vendor.subs.show', $sub->id) }}" class="btn btn-sm btn-primary" title="تحديد الصلاحيات">
                                            <i class="fas fa-key"></i> الصلاحيات
                                        </a>
                                        <a href="{{ route('vendor.subs.edit', $sub->id) }}" class="btn btn-sm btn-warning" title="تعديل بيانات الحساب">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('vendor.subs.toggle', $sub->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $sub->user?->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                title="{{ $sub->user?->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i class="fas {{ $sub->user?->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('vendor.subs.destroy', $sub->id) }}" method="POST" style="display:inline;"
                                            onsubmit="return confirm('حذف الحساب الفرعي «{{ $sub->user?->name }}» نهائياً؟');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف الحساب">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mt-4" style="border:1px dashed #FDE68A; background:#FFFBEB;">
        <div class="card-body py-3">
            <h6 class="font-weight-bold mb-1" style="color:#92400E;"><i class="fas fa-exclamation-triangle"></i> ملاحظات مهمة</h6>
            <ul class="mb-0 small" style="color:#92400E;">
                <li>الحساب الفرعي يرى بيانات <strong>هذا المتجر فقط</strong> — لا يمكنه الوصول لمتجر آخر.</li>
                <li>بلا صلاحيات ممنوحة: الحساب يرى لوحة التحكم الخاصة بالمتجر وملفه الشخصي فقط.</li>
                <li>منح «تعديل الفاتورة» أو «نقطة البيع» مثلاً يسلّم فعلياً تلك الصلاحية — تُفعّل وتُلغى فوراً من صفحة الصلاحيات.</li>
                <li>تعطيل الحساب يسجّل خروج المستخدم فوراً من كل الأجهزة.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
