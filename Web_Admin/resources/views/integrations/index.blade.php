@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('web/css/integrations.css?v=2') }}" type="text/css">
@endsection

@section('content')
@php
    $badgeClass = static fn (string $status): string => match ($status) {
        'connected' => 'ic-badge--connected',
        'configured' => 'ic-badge--configured',
        'not_configured' => 'ic-badge--not_configured',
        'error' => 'ic-badge--error',
        'disabled' => 'ic-badge--disabled',
        default => 'ic-badge--disabled',
    };
    $otpSections = [
        'enabled' => 'حالة OTP العامة',
        'enabled_registration' => 'التفعيل لكل عملية',
        'length' => 'طول الرمز والمدة',
        'resend_after' => 'إعادة الإرسال',
        'max_attempts' => 'الحدود والحظر',
        'show_in_app' => 'خيارات العرض والاختبار',
        'channels_registration' => 'القنوات لكل عملية',
    ];
    $fieldIcon = static function (string $key): string {
        return match (true) {
            str_starts_with($key, 'enabled') => 'fa-toggle-on',
            str_starts_with($key, 'channels_') => 'fa-share-alt',
            str_starts_with($key, 'max_') => 'fa-shield-alt',
            str_starts_with($key, 'rate_limit') => 'fa-tachometer-alt',
            str_contains($key, 'limit') => 'fa-tachometer-alt',
            str_contains($key, 'lockout') => 'fa-hourglass-half',
            str_contains($key, 'resend_after') => 'fa-clock',
            str_contains($key, 'keep') || str_contains($key, 'ttl') => 'fa-hourglass-start',
            str_contains($key, 'password') || str_contains($key, 'secret') => 'fa-key',
            str_contains($key, 'username') => 'fa-user',
            str_contains($key, 'from_') => 'fa-at',
            str_contains($key, 'api_url') => 'fa-link',
            str_contains($key, 'project') => 'fa-project-diagram',
            str_contains($key, 'credentials') => 'fa-file-alt',
            str_contains($key, 'phone_number_id') || str_contains($key, 'business') => 'fa-building',
            str_contains($key, 'app_secret') || str_contains($key, 'webhook') => 'fa-webhook',
            str_contains($key, 'api_version') => 'fa-code-branch',
            str_contains($key, 'access_token') || str_contains($key, 'server_key') => 'fa-ticket-alt',
            str_contains($key, 'encryption') => 'fa-lock',
            str_contains($key, 'timeout') => 'fa-stopwatch',
            str_contains($key, 'port') => 'fa-plug',
            str_contains($key, 'host') => 'fa-server',
            str_contains($key, 'token') => 'fa-token',
            str_contains($key, 'length') => 'fa-ruler',
            str_contains($key, 'country_code') => 'fa-globe',
            str_contains($key, 'device_limit') => 'fa-mobile-alt',
            str_contains($key, 'ip_limit') => 'fa-network-wired',
            default => 'fa-sliders',
        };
    };
@endphp
<div class="container-fluid ic-page">

    {{-- Header --}}
    <header class="ic-header">
        <div class="ic-header-title">
            <h1><i class="fas fa-plug me-2" aria-hidden="true"></i>مركز التكاملات والخدمات</h1>
            <p><i class="fas fa-info-circle me-1" aria-hidden="true"></i>إدارة خدمات API والتكاملات الخارجية، مع أسرار مشفّرة، سجلات تدقيق واختبارات اتصال.</p>
        </div>
        <div class="ic-toolbar" role="toolbar" aria-label="إجراءات مركز التكاملات">
            <a href="{{ route('integrations.templates') }}" class="ic-btn ic-btn--outline">
                <i class="fas fa-comment-alt" aria-hidden="true"></i>قوالب الرسائل
            </a>
            <a href="{{ route('integrations.message-logs') }}" class="ic-btn ic-btn--outline">
                <i class="fas fa-envelope-open-text" aria-hidden="true"></i>سجلات الإرسال
            </a>
            <a href="{{ route('integrations.api-keys') }}" class="ic-btn ic-btn--outline">
                <i class="fas fa-key" aria-hidden="true"></i>API Keys
            </a>
            <a href="{{ route('integrations.webhooks') }}" class="ic-btn ic-btn--outline">
                <i class="fas fa-link" aria-hidden="true"></i>Webhooks
            </a>
            <a href="{{ route('integrations.audit-logs') }}" class="ic-btn ic-btn--outline">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>Audit Logs
            </a>
        </div>
    </header>

    @if (session('success'))
        <div class="ic-alert ic-alert--success" role="alert">
            <i class="fas fa-check-circle me-1" aria-hidden="true"></i>{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="ic-alert ic-alert--danger" role="alert">
            <i class="fas fa-exclamation-circle me-1" aria-hidden="true"></i>{{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="ic-alert ic-alert--danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ic-alert ic-alert--info" role="note">
        <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
        لا تظهر أي خدمة كـ«متصل ومختبر» إلا بعد نجاح اختبار فعلي حديث. حالة «مهيأ (غير مختبر)» تعني أن البيانات محفوظة لكن لم يتم التحقق من المزود.
    </div>

    {{-- Status grid --}}
    <section class="ic-stats" aria-label="حالة الخدمات">
        @foreach ($statuses as $group => $status)
            @php
                $schema = \App\Support\IntegrationSchema::group($group);
            @endphp
            <article class="ic-stat">
                <div class="ic-stat-top">
                    <span class="ic-stat-icon"><i class="fas {{ $schema['icon'] ?? 'fa-plug' }}" aria-hidden="true"></i></span>
                    <span class="ic-badge {{ $badgeClass($status['status']) }}">{{ $status['label'] }}</span>
                </div>
                <div class="ic-stat-name">{{ $schema['label'] ?? $group }}</div>
                <div class="ic-stat-desc">{{ $schema['description'] ?? '' }}</div>
                <div class="ic-stat-meta">
                    @if ($status['last_test_at'])
                        آخر اختبار: <strong>{{ $status['last_test_at'] }}</strong>
                        @if ($status['last_test_message'])
                            <br><span class="ic-muted">{{ \Illuminate\Support\Str::limit($status['last_test_message'], 100) }}</span>
                        @endif
                    @else
                        لم يُختبر بعد.
                    @endif
                </div>
            </article>
        @endforeach
    </section>

    {{-- Tabs --}}
    <nav class="ic-tabs nav" role="tablist" aria-label="مجموعات التكاملات">
        @foreach ($data as $group => $payload)
            @php $schema = \App\Support\IntegrationSchema::group($group); @endphp
            <button class="ic-tab {{ $loop->first ? 'active' : '' }}"
                data-target="#group-{{ $group }}" type="button" role="tab"
                aria-controls="group-{{ $group }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                <i class="fas {{ $schema['icon'] ?? 'fa-plug' }}" aria-hidden="true"></i>{{ $schema['label'] ?? $group }}
            </button>
        @endforeach
    </nav>

    <div class="tab-content">
        @foreach ($data as $group => $payload)
            @php
                $schema = \App\Support\IntegrationSchema::group($group);
                $values = $payload['values'];
                $secrets = $payload['secrets'];
                $status = $statuses[$group];
                $hasSecretField = collect($schema['fields'] ?? [])->contains(fn ($f) => (bool) ($f['secret'] ?? false));
            @endphp
            <section class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="group-{{ $group }}"
                role="tabpanel" aria-labelledby="group-{{ $group }}">

                <div class="ic-panel">

                    {{-- Settings column --}}
                    <div class="ic-stack">
                        <form class="ic-card" method="POST" action="{{ route('integrations.update', $group) }}">
                            @csrf
                            <div class="ic-card-header">
                                <h2><i class="fas {{ $schema['icon'] ?? 'fa-sliders' }}" aria-hidden="true"></i>الإعدادات<span class="ic-card-sub">{{ $schema['label'] }}</span></h2>
                                <i class="fas fa-sliders-h ic-muted" aria-hidden="true"></i>
                            </div>
                            <div class="ic-card-body">
                                <div class="ic-form-grid">
                                    @foreach ($schema['fields'] as $key => $field)
                                        @php
                                            $value = $values[$key] ?? ($field['default'] ?? '');
                                            $secretSaved = $secrets[$key] ?? false;
                                            if ($group === 'otp' && array_key_exists($key, $otpSections)):
                                        @endphp
                                        <div class="ic-form-section"><i class="fas fa-layer-group" aria-hidden="true"></i>{{ $otpSections[$key] }}</div>
                                        @endif
                                        <div class="{{ in_array($field['type'], ['textarea','bool','multiselect']) || str_starts_with($key, 'channels_') ? 'ic-field ic-field--full' : 'ic-field' }}">
                                            <label class="ic-label" for="{{ $group }}-{{ $key }}"><i class="fas {{ $fieldIcon($key) }} me-1" aria-hidden="true"></i>{{ $field['label'] }}</label>

                                            @if ($field['type'] === 'bool')
                                                @php $isChecked = (string) $value === '1'; @endphp
                                                <input type="hidden" name="{{ $key }}" value="0">
                                                <label class="ic-switch" for="{{ $group }}-{{ $key }}">
                                                    <input class="" id="{{ $group }}-{{ $key }}" type="checkbox" name="{{ $key }}" value="1" {{ $isChecked ? 'checked' : '' }} role="switch" aria-checked="{{ $isChecked ? 'true' : 'false' }}">
                                                    <span class="ic-switch-track" aria-hidden="true"></span>
                                                    <span class="ic-switch-label">{{ $isChecked ? 'مفعل' : 'معطل' }}</span>
                                                </label>
                                            @elseif ($field['type'] === 'select')
                                                <select id="{{ $group }}-{{ $key }}" name="{{ $key }}" class="ic-control">
                                                    @foreach ($field['options'] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" {{ (string) $value === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif ($field['type'] === 'multiselect')
                                                @php $selected = array_filter(array_map('trim', explode(',', (string) $value))); @endphp
                                                <div class="ic-check-grid" role="group" aria-label="{{ $field['label'] }}">
                                                    @foreach ($field['options'] as $optionValue => $optionLabel)
                                                        <label class="ic-check-card" for="{{ $group }}-{{ $key }}-{{ $optionValue }}">
                                                            <input id="{{ $group }}-{{ $key }}-{{ $optionValue }}" type="checkbox" name="{{ $key }}[]" value="{{ $optionValue }}" {{ in_array((string) $optionValue, $selected, true) ? 'checked' : '' }}>
                                                            <span>{{ $optionLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <span class="ic-helper">يمكن اختيار أكثر من قناة. القناة تعمل فقط إذا كانت خدمتها مهيأة ومفعلة.</span>
                                            @elseif ($field['type'] === 'textarea')
                                                <textarea id="{{ $group }}-{{ $key }}" name="{{ $key }}" class="ic-control" rows="4" placeholder="{{ $secretSaved ? '******** (محفوظ — اتركه فارغًا للإبقاء عليه)' : '' }}"></textarea>
                                                @if ($secretSaved)
                                                    <span class="ic-helper ic-helper--success">سر محفوظ ومشفّر. اترك الحقل فارغًا لعدم تغييره.</span>
                                                @endif
                                            @elseif ($field['type'] === 'password')
                                                <input id="{{ $group }}-{{ $key }}" type="password" name="{{ $key }}" class="ic-control" autocomplete="new-password" placeholder="{{ $secretSaved ? '******** (محفوظ — اتركه فارغًا للإبقاء عليه)' : '' }}">
                                                @if ($secretSaved)
                                                    <span class="ic-helper ic-helper--success">سر محفوظ ومشفّر.</span>
                                                @else
                                                    <span class="ic-helper">يُخزَّن مشفّرًا ولا يظهر مرة أخرى.</span>
                                                @endif
                                            @elseif ($field['type'] === 'number')
                                                <input id="{{ $group }}-{{ $key }}" type="number" name="{{ $key }}" class="ic-control" value="{{ $value }}" step="any">
                                            @else
                                                <input id="{{ $group }}-{{ $key }}" type="text" name="{{ $key }}" class="ic-control" value="{{ $value }}">
                                            @endif
                                        </div>
                                    @endforeach

                                    @if ($hasSecretField)
                                        <div class="ic-field">
                                            <label class="ic-label" for="{{ $group }}-current_password"><i class="fas fa-lock me-1" aria-hidden="true"></i>كلمة مرورك الحالية <span class="ic-muted">(لتغيير الأسرار)</span></label>
                                            <input id="{{ $group }}-current_password" type="password" name="current_password" class="ic-control" autocomplete="current-password">
                                            <span class="ic-helper">مطلوبة فقط عند إدخال سر جديد.</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="ic-toolbar" style="margin-top:20px; justify-content:flex-start;">
                                    <button class="ic-btn ic-btn--primary" type="submit">
                                        <i class="fas fa-save" aria-hidden="true"></i>حفظ الإعدادات
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if (!empty($schema['setup_notes']))
                            <div class="ic-card">
                                <div class="ic-card-header">
                                    <h2><i class="fas fa-clipboard-check" aria-hidden="true"></i>إعداد خارجي مطلوب</h2>
                                </div>
                                <div class="ic-card-body">
                                    <ol class="ic-notes-list">
                                        @foreach ($schema['setup_notes'] as $note)
                                            <li>{{ $note }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Diagnostics column --}}
                    <div class="ic-stack">

                        @if ($group === 'firebase' && !empty($status['credentials_in_public']))
                            <div class="ic-alert ic-alert--danger" role="alert">
                                <strong>تحذير أمني:</strong> يوجد ملف اعتماد Firebase داخل المجلد العام. انقله إلى <code>storage/app/private/firebase/credentials.json</code> ودوّر مفتاح الخدمة من Google Cloud لأنه قد يكون مكشوفًا.
                            </div>
                        @endif

                        {{-- Test connection --}}
                        <form class="ic-card ic-card--test" method="POST" action="{{ route('integrations.test', $group) }}">
                            @csrf
                            <div class="ic-card-header">
                                <h2><i class="fas fa-plug" aria-hidden="true"></i>اختبار الاتصال</h2>
                                <span class="ic-card-sub">آمن وغير مُدمِّر</span>
                            </div>
                            <div class="ic-card-body">
                                <div class="ic-form">
                                    @if ($group === 'sms')
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-label" for="{{ $group }}-recipient"><i class="fas fa-phone me-1" aria-hidden="true"></i>رقم الاختبار</label>
                                            <input id="{{ $group }}-recipient" name="recipient" class="ic-control" placeholder="+970..." required>
                                        </div>
                                    @elseif ($group === 'smtp')
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-label" for="{{ $group }}-recipient"><i class="fas fa-envelope me-1" aria-hidden="true"></i>بريد الاختبار (اختياري)</label>
                                            <input id="{{ $group }}-recipient" name="recipient" type="email" class="ic-control" placeholder="اتركه فارغًا لاختبار الاتصال والمصادقة فقط">
                                            <span class="ic-helper">بدون بريد: اتصال + TLS + مصادقة دون إرسال. مع بريد: إرسال فعلي.</span>
                                        </div>
                                    @elseif ($group === 'whatsapp')
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-label" for="{{ $group }}-recipient"><i class="fas fa-comment-dots me-1" aria-hidden="true"></i>رقم WhatsApp للاختبار (اختياري)</label>
                                            <input id="{{ $group }}-recipient" name="recipient" class="ic-control" placeholder="970...">
                                            <span class="ic-helper">بدون رقم: قراءة بيانات رقم الأعمال دون إرسال. مع رقم: إرسال رسالة اختبار فعلية.</span>
                                        </div>
                                    @elseif ($group === 'firebase')
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-label" for="{{ $group }}-device_token"><i class="fas fa-mobile-alt me-1" aria-hidden="true"></i>Device Token (اختياري)</label>
                                            <input id="{{ $group }}-device_token" name="device_token" class="ic-control" placeholder="اتركه فارغًا لاختبار الاعتماد دون إرسال">
                                        </div>
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-label" for="{{ $group }}-role"><i class="fas fa-user-cog me-1" aria-hidden="true"></i>الدور (لاختيار Token تلقائيًا عند الإرسال)</label>
                                            <select id="{{ $group }}-role" name="role" class="ic-control">
                                                <option value="customer">Customer</option>
                                                <option value="driver">Driver</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-switch" for="{{ $group }}-send_test">
                                                <input id="{{ $group }}-send_test" class="" type="checkbox" name="send_test" value="1">
                                                <span class="ic-switch-track" aria-hidden="true"></span>
                                                <span class="ic-switch-label">إرسال إشعار فعلي (يتطلب Device Token)</span>
                                            </label>
                                            <span class="ic-helper">الافتراضي: OAuth + FCM validate_only، دون إرسال أي إشعار.</span>
                                        </div>
                                    @elseif ($group === 'webhooks')
                                        <div class="ic-field ic-field--full">
                                            <label class="ic-label" for="{{ $group }}-endpoint_id"><i class="fas fa-map-marker-alt me-1" aria-hidden="true"></i>Endpoint ID (اختياري)</label>
                                            <input id="{{ $group }}-endpoint_id" name="endpoint_id" type="number" class="ic-control" placeholder="اختبر Endpoint محدد">
                                        </div>
                                    @endif
                                </div>

                                <div class="ic-toolbar" style="justify-content:flex-start; margin-top:4px;">
                                    <button class="ic-btn ic-btn--primary" type="submit">
                                        <i class="fas fa-play-circle" aria-hidden="true"></i>تشغيل الاختبار
                                    </button>
                                </div>

                                <div class="ic-test-status" aria-label="حالة الاتصال">
                                    <div class="ic-test-status-row"><span class="ic-k">الحالة</span><span class="ic-v">{{ $status['label'] }}</span></div>
                                    <div class="ic-test-status-row"><span class="ic-k">مهيأ</span><span class="ic-v">{{ $status['configured'] ? 'نعم' : 'لا' }}</span></div>
                                    <div class="ic-test-status-row"><span class="ic-k">آخر اختبار</span><span class="ic-v">{{ $status['last_test_at'] ?? '—' }}</span></div>
                                    @if ($status['last_test_message'])
                                        <div class="ic-test-status-message ic-muted">{{ $status['last_test_message'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </form>

                        {{-- Firebase management --}}
                        @if ($group === 'firebase')
                            <div class="ic-card">
                                <div class="ic-card-header">
                                    <h2><i class="fas fa-fire-alt" aria-hidden="true"></i>إدارة Firebase Credentials</h2>
                                    <span class="ic-card-sub">Service Account</span>
                                </div>
                                <div class="ic-card-body">
                                    <div class="ic-test-status" style="margin-bottom:16px;">
                                        <div class="ic-test-status-row"><span class="ic-k">Project</span><span class="ic-v">{{ $firebaseInfo['project_id'] ?: '—' }}</span></div>
                                        <div class="ic-test-status-row"><span class="ic-k">Client Email</span><span class="ic-v" style="word-break:break-all;">{{ $firebaseInfo['client_email'] ?: '—' }}</span></div>
                                        <div class="ic-test-status-row"><span class="ic-k">Source</span><span class="ic-v">{{ $firebaseInfo['source'] }}</span></div>
                                    </div>

                                    <div class="ic-upload mb-3">
                                        <form method="POST" action="{{ route('integrations.firebase.credentials.upload') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="ic-field ic-field--full">
                                                <label class="ic-label" for="{{ $group }}-service_account"><i class="fas fa-file-alt me-1" aria-hidden="true"></i>Service Account JSON (رفع من الجهاز)</label>
                                                <input id="{{ $group }}-service_account" type="file" name="service_account" class="ic-file-input" accept="application/json,.json" required>
                                            </div>
                                            <div class="ic-field ic-field--full">
                                                <label class="ic-label" for="{{ $group }}-test_device"><i class="fas fa-mobile me-1" aria-hidden="true"></i>Device Token للاختبار (اختياري)</label>
                                                <input id="{{ $group }}-test_device" type="text" name="device_token" class="ic-control ic-device-token" placeholder="اتركه فارغًا لاختبار validate-only">
                                            </div>
                                            <div class="ic-toolbar" style="justify-content:flex-start;">
                                                <button type="submit" class="ic-btn ic-btn--primary">
                                                    <i class="fas fa-upload" aria-hidden="true"></i>رفع واختبار وتفعيل
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    @if (!empty($firebaseInfo['has_backup']))
                                        <form method="POST" action="{{ route('integrations.firebase.credentials.rollback') }}" style="margin-bottom:16px;">
                                            @csrf
                                            <button type="submit" class="ic-btn ic-btn--warning" onclick="return confirm('التراجع سيُعيد المفتاح السابق (مكشوف) — تأكد من إلغائه من Google Cloud. متابعة؟')">
                                                <i class="fas fa-undo" aria-hidden="true"></i>تراجع إلى النسخة الاحتياطية
                                            </button>
                                        </form>
                                    @endif

                                    <div class="ic-note" style="margin-bottom:16px;">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        رفع <code>google-services.json</code> على الخادم لا يغيّر إعدادات تطبيق Android المثبت لدى المستخدمين. تحديث الإعدادات المدمجة يتطلب إعادة بناء التطبيق وإصدار تحديث عبر Google Play.
                                    </div>

                                    <div class="ic-upload">
                                        <form method="POST" action="{{ route('integrations.firebase.android-config.upload') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="ic-field ic-field--full">
                                                <label class="ic-label" for="{{ $group }}-android_config"><i class="fas fa-mobile-alt me-1" aria-hidden="true"></i>Android google-services.json</label>
                                                @if (!empty($firebaseAndroidInfo))
                                                    <span class="ic-helper ic-helper--success">محفوظ — Project: {{ $firebaseAndroidInfo['project_id'] ?: '—' }}</span>
                                                @endif
                                                <input id="{{ $group }}-android_config" type="file" name="android_config" class="ic-file-input" accept="application/json,.json" required>
                                            </div>
                                            <div class="ic-toolbar" style="justify-content:flex-start;">
                                                <button type="submit" class="ic-btn ic-btn--outline">
                                                    <i class="fas fa-mobile-alt" aria-hidden="true"></i>رفع/تحديث
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- WhatsApp Webhook --}}
                        @if ($group === 'whatsapp')
                            <div class="ic-card">
                                <div class="ic-card-header">
                                    <h2><i class="fas fa-link" aria-hidden="true"></i>Webhook</h2>
                                </div>
                                <div class="ic-card-body">
                                    <label class="ic-label" for="whatsapp-webhook"><i class="fas fa-link me-1" aria-hidden="true"></i>عنوان Webhook</label>
                                    <code id="whatsapp-webhook" class="d-block text-break ic-note" style="background:#fbfcfe; border-color: var(--ic-border); color: var(--ic-text);">{{ url('/api/webhooks/whatsapp') }}</code>
                                    <p class="ic-muted" style="margin-top:10px;">Verify Token محفوظ ومشفّر، ويتم التحقق من توقيع <code>X-Hub-Signature-256</code> لكل طلب.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Google Maps quick test --}}
                        @if ($group === 'google_maps')
                            <div class="ic-card">
                                <div class="ic-card-header">
                                    <h2><i class="fas fa-map-marked-alt" aria-hidden="true"></i>اختبار سريع للعنوان</h2>
                                </div>
                                <div class="ic-card-body">
                                    <div class="ic-form-grid" style="grid-template-columns: 1fr 1fr;">
                                        <input id="map-lat" class="ic-control" placeholder="Latitude" aria-label="Latitude">
                                        <input id="map-lng" class="ic-control" placeholder="Longitude" aria-label="Longitude">
                                    </div>
                                    <div class="ic-toolbar" style="justify-content:flex-start; margin-top:12px;">
                                        <button type="button" class="ic-btn ic-btn--outline" onclick="testReverseGeocode()">
                                            <i class="fas fa-search" aria-hidden="true"></i>جلب العنوان
                                        </button>
                                    </div>
                                    <div id="map-result" class="ic-muted" role="status" aria-live="polite" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </section>
        @endforeach
    </div>

    {{-- Logs --}}
    <div class="ic-panel" style="grid-template-columns: 1fr 1fr;">
        <div class="ic-card">
            <div class="ic-card-header">
                <h2><i class="fas fa-envelope-open-text" aria-hidden="true"></i>آخر رسائل الإرسال</h2>
                <a href="{{ route('integrations.message-logs') }}" class="ic-btn ic-btn--ghost ic-btn--sm">عرض الكل <i class="fas fa-arrow-left ms-1" aria-hidden="true"></i></a>
            </div>
            <div class="table-responsive" style="border: 0;">
                <table class="ic-table">
                    <thead>
                        <tr><th><i class="fas fa-layer-group me-1" aria-hidden="true"></i>القناة</th><th><i class="fas fa-user me-1" aria-hidden="true"></i>المستلم</th><th><i class="fas fa-circle-notch me-1" aria-hidden="true"></i>الحالة</th><th><i class="fas fa-clock me-1" aria-hidden="true"></i>الوقت</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentLogs as $log)
                            <tr>
                                <td>{{ $log->channel }}</td>
                                <td>{{ $log->recipient_masked }}</td>
                                <td><span class="ic-log-pill @if($log->status === 'sent') ic-log-pill--ok @elseif($log->status === 'failed') ic-log-pill--err @else ic-log-pill--pending @endif">{{ $log->status }}</span></td>
                                <td class="ic-muted">{{ $log->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="ic-muted" style="text-align:center; padding:28px 12px;">لا سجلات بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ic-card">
            <div class="ic-card-header">
                <h2><i class="fas fa-link" aria-hidden="true"></i>آخر Webhook Deliveries</h2>
                <a href="{{ route('integrations.webhooks') }}" class="ic-btn ic-btn--ghost ic-btn--sm">عرض الكل <i class="fas fa-arrow-left ms-1" aria-hidden="true"></i></a>
            </div>
            <div class="table-responsive" style="border: 0;">
                <table class="ic-table">
                    <thead>
                        <tr><th>الحدث</th><th>الحالة</th><th>المحاولات</th><th>الوقت</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($webhookDeliveries as $delivery)
                            <tr>
                                <td>{{ $delivery->event }}</td>
                                <td><span class="ic-log-pill @if($delivery->status === 'delivered') ic-log-pill--ok @elseif($delivery->status === 'failed') ic-log-pill--err @else ic-log-pill--pending @endif">{{ $delivery->status }}</span></td>
                                <td>{{ $delivery->attempts }}</td>
                                <td class="ic-muted">{{ $delivery->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="ic-muted" style="text-align:center; padding:28px 12px;">لا عمليات إرسال بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab switching handler — pure jQuery, no Bootstrap tab() call at all.
    // NOTE: the layout prints the page content before it loads jQuery, so
    // this script is pushed into @stack('scripts') (rendered after jQuery)
    // instead of inlining it in the content, otherwise "$" is undefined.
    $(function () {
        var $tabs = $('.ic-tabs');
        var $panes = $('.tab-content > .tab-pane');

        function activateTab(target, updateUrl) {
            if (!target || target.charAt(0) !== '#') {
                return;
            }

            var $tab = $tabs.find('.ic-tab[data-target="' + target + '"]');
            var $pane = $(target).filter('.tab-pane');

            // Unknown hash (e.g. a typo in the URL): keep the current tab.
            if (!$tab.length || !$pane.length) {
                return;
            }

            // 1. Deactivate the previously active tab.
            $tabs.find('.ic-tab').removeClass('active').attr('aria-selected', 'false');

            // 2. Activate the clicked tab.
            $tab.addClass('active').attr('aria-selected', 'true');

            // 3. Show its pane immediately.
            $panes.removeClass('show active');
            $pane.addClass('show active');

            // 4. Keep the URL in sync so the tab can be linked / replayed.
            if (updateUrl && window.location.hash !== target) {
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, '', target);
                } else {
                    window.location.hash = target;
                }
            }
        }

        // Delegated click: works for every tab, any number of times.
        $tabs.off('click.icTabs').on('click.icTabs', '.ic-tab', function (e) {
            e.preventDefault();
            activateTab($(this).attr('data-target'), true);
        });

        // Browser back / forward between tabs.
        $(window).off('popstate.icTabs hashchange.icTabs').on('popstate.icTabs hashchange.icTabs', function () {
            if (window.location.hash) {
                activateTab(window.location.hash, false);
            }
        });

        // Direct link: /integrations#group-smtp
        if (window.location.hash) {
            activateTab(window.location.hash, false);
        }
    });

    function testReverseGeocode() {
        const lat = document.getElementById('map-lat').value;
        const lng = document.getElementById('map-lng').value;
        const box = document.getElementById('map-result');
        box.textContent = 'جارٍ البحث...';
        fetch('{{ route('integrations.reverse-geocode') }}?lat=' + encodeURIComponent(lat) + '&lng=' + encodeURIComponent(lng), {
            headers: { 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            box.textContent = (data.address || data.message || 'لا نتيجة') + (data.provider ? ' (' + data.provider + ')' : '');
        }).catch(() => { box.textContent = 'فشل الاختبار.'; });
    }
</script>
@endpush
@endsection
