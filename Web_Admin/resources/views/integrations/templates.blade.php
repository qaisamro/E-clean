@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">قوالب الرسائل</h2>
                <p class="text-muted mb-0">قوالب SMS وEmail وWhatsApp وPush. المتغيرات المتاحة: code, name, minutes, app, time.</p>
            </div>
            <a href="{{ route('integrations.index') }}" class="btn btn-outline-secondary btn-sm">رجوع للمركز</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>إضافة قالب</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('integrations.templates.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">القناة</label>
                            <select name="channel" class="form-select" required>
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="push">Push</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">المفتاح</label>
                            <input name="key" class="form-control" placeholder="otp_registration" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الاسم</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">اللغة</label>
                            <input name="language" class="form-control" value="ar" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">اسم قالب المزود (WhatsApp)</label>
                            <input name="provider_template_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الموضوع (Email)</label>
                            <input name="subject" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المتغيرات (مفصولة بفاصلة)</label>
                            <input name="variables" class="form-control" placeholder="code,name,minutes">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">النص</label>
                            <textarea name="body" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-check">
                                <input type="checkbox" name="active" value="1" class="form-check-input" checked>
                                <span class="form-check-label">مفعل</span>
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-3">إضافة</button>
                </form>
            </div>
        </div>

        @foreach ($templates->groupBy('channel') as $channel => $items)
            <div class="card shadow-sm mb-4">
                <div class="card-header"><strong>{{ strtoupper($channel) }}</strong></div>
                <div class="card-body">
                    @foreach ($items as $template)
                        <form method="POST" action="{{ route('integrations.templates.update', $template) }}"
                            class="border-bottom pb-3 mb-3">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <input name="key" class="form-control form-control-sm" value="{{ $template->key }}" required>
                                </div>
                                <div class="col-md-3">
                                    <input name="name" class="form-control form-control-sm" value="{{ $template->name }}" required>
                                </div>
                                <div class="col-md-2">
                                    <input name="language" class="form-control form-control-sm" value="{{ $template->language }}" required>
                                </div>
                                <div class="col-md-3">
                                    <input name="provider_template_name" class="form-control form-control-sm"
                                        value="{{ $template->provider_template_name }}" placeholder="Provider template name">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-check">
                                        <input type="checkbox" name="active" value="1" class="form-check-input" {{ $template->active ? 'checked' : '' }}>
                                        <span class="form-check-label">مفعل</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input name="subject" class="form-control form-control-sm" value="{{ $template->subject }}"
                                        placeholder="Subject (Email)">
                                </div>
                                <div class="col-md-6">
                                    <input name="variables" class="form-control form-control-sm"
                                        value="{{ implode(',', $template->variables ?? []) }}" placeholder="code,name">
                                </div>
                                <div class="col-md-12">
                                    <textarea name="body" class="form-control form-control-sm" rows="2" required>{{ $template->body }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <input type="hidden" name="channel" value="{{ $template->channel }}">
                                    <button class="btn btn-primary btn-sm">تحديث</button>
                                </div>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('integrations.templates.destroy', $template) }}"
                            onsubmit="return confirm('حذف القالب؟')" class="mb-3">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">حذف القالب</button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
