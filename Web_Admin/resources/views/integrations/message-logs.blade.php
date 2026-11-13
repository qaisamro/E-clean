@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">سجلات إرسال الرسائل</h2>
                <p class="text-muted mb-0">لا تُسجَّل رموز OTP أو محتوى الرسائل أو الأسرار. المستلم يظهر مخفيًا.</p>
            </div>
            <a href="{{ route('integrations.index') }}" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="channel" class="form-select">
                    <option value="">كل القنوات</option>
                    @foreach (['sms', 'email', 'whatsapp', 'push'] as $channel)
                        <option value="{{ $channel }}" {{ request('channel') === $channel ? 'selected' : '' }}>{{ $channel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach (['sent', 'failed', 'not_configured', 'logged', 'pending', 'delivered', 'read'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary">تصفية</button></div>
        </form>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>القناة</th>
                            <th>المزود</th>
                            <th>القالب</th>
                            <th>المستلم</th>
                            <th>الحالة</th>
                            <th>خطأ</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->channel }}</td>
                                <td>{{ $log->provider }}</td>
                                <td>{{ $log->template_key }}</td>
                                <td>{{ $log->recipient_masked }}</td>
                                <td>{{ $log->status }}</td>
                                <td class="small text-danger">{{ $log->error_message }}</td>
                                <td class="small">{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">لا سجلات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection
