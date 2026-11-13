@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">سجل التدقيق — Audit Logs</h2>
                <p class="text-muted mb-0">كل تغيير حساس مع إخفاء الأسرار ورموز OTP تلقائيًا.</p>
            </div>
            <a href="{{ route('integrations.index') }}" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input name="search" class="form-control" placeholder="بحث في المفتاح/الإجراء" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="group" class="form-select">
                    <option value="">كل المجموعات</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group }}" {{ request('group') === $group ? 'selected' : '' }}>{{ $group }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">تصفية</button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الإجراء</th>
                            <th>المجموعة</th>
                            <th>المفتاح</th>
                            <th>القيمة القديمة</th>
                            <th>القيمة الجديدة</th>
                            <th>IP</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user?->name ?? $log->user?->email ?? '—' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->group_name }}</td>
                                <td><code>{{ $log->setting_key }}</code></td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($log->old_value, 80) }}</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($log->new_value, 80) }}</td>
                                <td class="small">{{ $log->ip_address }}</td>
                                <td class="small">{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-3">لا سجلات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection
