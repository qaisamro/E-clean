@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">مفاتيح API</h2>
                <p class="text-muted mb-0">تُخزَّن المفاتيح كتجزئة SHA-256. المفتاح الكامل يظهر مرة واحدة فقط عند الإنشاء.</p>
            </div>
            <a href="{{ route('integrations.index') }}" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('api_key_plain'))
            <div class="alert alert-warning">
                <strong>انسخ المفتاح الآن:</strong>
                <code class="d-block mt-2 text-break">{{ session('api_key_plain') }}</code>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>إنشاء مفتاح</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('integrations.api-keys.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">الاسم</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الصلاحية بالأيام</label>
                            <input name="expires_in_days" type="number" min="1" class="form-control" placeholder="بدون انتهاء">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Scopes</label>
                            <div class="row">
                                @foreach ($scopes as $scope => $label)
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input type="checkbox" name="scopes[]" value="{{ $scope }}" class="form-check-input">
                                            <span class="form-check-label">{{ $label }} <code>{{ $scope }}</code></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">إنشاء مفتاح</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>Prefix</th>
                            <th>Scopes</th>
                            <th>الحالة</th>
                            <th>آخر استخدام</th>
                            <th>ينتهي</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keys as $key)
                            <tr>
                                <td>{{ $key->id }}</td>
                                <td>{{ $key->name }}</td>
                                <td><code>{{ $key->key_prefix }}…</code></td>
                                <td class="small">{{ implode(', ', $key->scopes ?? []) }}</td>
                                <td>
                                    @if ($key->isActive())
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">ملغى/منتهي</span>
                                    @endif
                                </td>
                                <td class="small">{{ $key->last_used_at ?? '—' }}</td>
                                <td class="small">{{ $key->expires_at ?? '—' }}</td>
                                <td class="text-nowrap">
                                    @if ($key->isActive())
                                        <form method="POST" action="{{ route('integrations.api-keys.revoke', $key) }}" class="d-inline"
                                            onsubmit="return confirm('إلغاء المفتاح؟')">
                                            @csrf
                                            <button class="btn btn-outline-warning btn-sm">إلغاء</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('integrations.api-keys.destroy', $key) }}" class="d-inline"
                                        onsubmit="return confirm('حذف المفتاح نهائيًا؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">لا مفاتيح بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $keys->links() }}</div>
        </div>
    </div>
@endsection
