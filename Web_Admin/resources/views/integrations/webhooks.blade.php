@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">Webhooks</h2>
                <p class="text-muted mb-0">توقيع HMAC-SHA256، Idempotency-Key، إعادة محاولة تلقائية وسجل تسليمات.</p>
            </div>
            <a href="{{ route('integrations.index') }}" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('webhook_secret'))
            <div class="alert alert-warning">
                <strong>Secret (يظهر مرة واحدة):</strong>
                <code class="d-block mt-2 text-break">{{ session('webhook_secret') }}</code>
                استخدمه للتحقق من التوقيع: <code>X-EC-Signature: t=timestamp,v1=hmac</code>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>إضافة Endpoint</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('integrations.webhooks.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">الاسم</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">URL</label>
                            <input name="url" type="url" class="form-control" placeholder="https://example.com/webhook" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الأحداث</label>
                            <div style="max-height: 130px; overflow:auto;">
                                @foreach ($events as $event => $label)
                                    <label class="form-check">
                                        <input type="checkbox" name="events[]" value="{{ $event }}" class="form-check-input">
                                        <span class="form-check-label">{{ $label }} <code>{{ $event }}</code></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">الوصف</label>
                            <input name="description" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">إنشاء</button>
                </form>
            </div>
        </div>

        @foreach ($endpoints as $endpoint)
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $endpoint->name }}</strong>
                    <span class="badge bg-{{ $endpoint->active ? 'success' : 'secondary' }}">
                        {{ $endpoint->active ? 'نشط' : 'معطل' }}
                    </span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.webhooks.update', $endpoint) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">الاسم</label>
                                <input name="name" class="form-control form-control-sm" value="{{ $endpoint->name }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">URL</label>
                                <input name="url" class="form-control form-control-sm" value="{{ $endpoint->url }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الأحداث</label>
                                <div style="max-height: 110px; overflow:auto;">
                                    @foreach ($events as $event => $label)
                                        <label class="form-check">
                                            <input type="checkbox" name="events[]" value="{{ $event }}" class="form-check-input"
                                                {{ in_array($event, $endpoint->events ?? [], true) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $event }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">الوصف</label>
                                <input name="description" class="form-control form-control-sm" value="{{ $endpoint->description }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-check mt-4">
                                    <input type="checkbox" name="active" value="1" class="form-check-input" {{ $endpoint->active ? 'checked' : '' }}>
                                    <span class="form-check-label">نشط</span>
                                </label>
                            </div>
                            <div class="col-md-2 mt-4">
                                <button class="btn btn-primary btn-sm">تحديث</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-3 d-flex gap-2">
                        <form method="POST" action="{{ route('integrations.webhooks.test', $endpoint) }}">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm">إرسال اختبار</button>
                        </form>
                        <form method="POST" action="{{ route('integrations.webhooks.destroy', $endpoint) }}"
                            onsubmit="return confirm('حذف Endpoint وكل تسليماته؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card shadow-sm">
            <div class="card-header"><strong>سجل التسليمات</strong></div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Endpoint</th>
                            <th>الحدث</th>
                            <th>الحالة</th>
                            <th>HTTP</th>
                            <th>المحاولات</th>
                            <th>التالي</th>
                            <th>الخطأ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveries as $delivery)
                            <tr>
                                <td>{{ $delivery->id }}</td>
                                <td>{{ $delivery->endpoint?->name }}</td>
                                <td>{{ $delivery->event }}</td>
                                <td>{{ $delivery->status }}</td>
                                <td>{{ $delivery->response_code ?? '—' }}</td>
                                <td>{{ $delivery->attempts }}</td>
                                <td class="small">{{ $delivery->next_retry_at ?? '—' }}</td>
                                <td class="small text-danger">{{ $delivery->error }}</td>
                                <td>
                                    @if ($delivery->status === 'failed')
                                        <form method="POST" action="{{ route('integrations.webhooks.retry', $delivery) }}">
                                            @csrf
                                            <button class="btn btn-outline-warning btn-sm">إعادة</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-3">لا تسليمات بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $deliveries->links() }}</div>
        </div>
    </div>
@endsection
