@extends('layouts.app')

@section('content')
    <style>
        .template-div {
            border: 2px dashed #ccc;
        }

        .no-hover:hover {
            background-color: transparent !important;
        }

        .ck-blurred,
        .ck-focused {
            padding: 1.5rem !important
        }

        .ant-upload.ant-upload-select-picture-card {
            margin-right: 8px;
            margin-bottom: 8px;
            margin-top: 10px;
            text-align: center;
            vertical-align: top;
            border-radius: 2px;
            cursor: pointer;
            transition: border-color .3s;
            height: 100px
        }

        .ant-upload .file-input {
            display: none;
        }

        .template-div {
            height: 8rem;
            width: 100%;
        }

        .image_preview {
            object-fit: cover;
        }

        .div-position {
            position: relative;
        }

        .plus__icon {
            position: absolute;
            right: 6px;
            top: 6px;
        }
    </style>

    <div>
        <h1 class="ml-4">صفحات الموقع</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Site pages quick nav (only these 4 sections) --}}
        <div class="mx-4 mb-2">
            <div class="btn-group flex-wrap" role="group" aria-label="Site pages">
                <a href="#section-terms" class="btn btn-outline-primary">شروط الخدمة</a>
                <a href="#section-privacy" class="btn btn-outline-primary">سياسة الخصوصية</a>
                <a href="#section-contact" class="btn btn-outline-primary">تواصل معنا</a>
                <a href="#section-about" class="btn btn-outline-primary">المزيد عنا</a>
                <a href="#section-app-partners" class="btn btn-outline-warning">إعدادات التطبيق — «شركاؤنا الموثوقون»</a>
            </div>
        </div>

        {{-- إعدادات التطبيق — التحكم بظهور قسم شركاؤنا الموثوقون في التطبيق --}}
        @php
            $partners = $webSettings->firstWhere('key', 'app_partners_section');
            $partnersEnabled = (bool) ($partners?->value['enabled'] ?? true);
        @endphp
        <div id="section-app-partners" class="mx-4 card mt-3 rounded-lg border-none site-page-card" style="scroll-margin-top: 90px;">
            <div class="card-header py-2 bg-primary d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="text-white mb-0">إعدادات التطبيق — «شركاؤنا الموثوقون»</h2>
                <span class="badge {{ $partnersEnabled ? 'badge-success' : 'badge-secondary' }}">
                    {{ $partnersEnabled ? 'مفعّل في التطبيق' : 'مخفي في التطبيق' }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('web.settingUpdate', 'app_partners') }}" method="POST">
                    @csrf
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="partnersToggle"
                            name="enabled" value="1" {{ $partnersEnabled ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold" for="partnersToggle">
                            إظهار قسم «شركاؤنا الموثوقون» في تطبيق الأندرويد (الصفحة الرئيسية)
                        </label>
                    </div>
                    <p class="small text-muted mt-2 mb-0">
                        عند إلغاء التفعيل يختفي القسم بالكامل من التطبيق فور التحديث (كل أجهزة المستخدمين)،
                        ولا يؤثر على ظهور المتاجر في باقي أقسام التطبيق. الوضع الافتراضي: مفعّل.
                    </p>
                    <div class="mt-3">
                        <button class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Terms & Conditions --}}
        @php
            $termsConditions = $webSettings->firstWhere('key', 'terms_conditions');
            $termsValue = $termsConditions?->value ?? [];
        @endphp
        <div id="section-terms" class="mx-4 card mt-3 rounded-lg border-none site-page-card" style="scroll-margin-top: 90px;">
            <div class="card-header py-2 bg-primary">
                <h2 class="text-white">{{ __('Terms & Conditions') }}</h2>
            </div>
            <div class="d-flex justify-content-center">
                <div class="row card-body">
                    <div class="col-12">
                        <form action="{{ route('web.settingUpdate', 'terms_conditions') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-12 p-4">
                                    <label for="title" class="form-label">{{ __('Title') }}</label>
                                    <input type="text" name="title" id="editor_terms_title" class="form-control"
                                        value="{{ $termsValue['title'] ?? '' }}" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 p-4">
                                    <label for="content" class="form-label">{{ __('Content') }}</label>
                                    <textarea name="content" id="editor_terms_content" class="form-control" rows="10">{{ $termsValue['content'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button class="btn btn-primary">{{ __('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Privacy Policy --}}
        @php
            $privacyPolicy = $webSettings->firstWhere('key', 'privacy_policy');
            $privacyValue = $privacyPolicy?->value ?? [];
        @endphp
        <div id="section-privacy" class="mx-4 card mt-3 rounded-lg border-none site-page-card" style="scroll-margin-top: 90px;">
            <div class="card-header py-2 bg-primary">
                <h2 class="text-white">{{ __('Privacy Policy') }}</h2>
            </div>
            <div class="d-flex justify-content-center">
                <div class="row card-body">
                    <div class="col-12">
                        <form action="{{ route('web.settingUpdate', 'privacy_policy') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-12 p-4">
                                    <label for="title" class="form-label">{{ __('Title') }}</label>
                                    <input type="text" name="title" id="editor_privacy_title" class="form-control"
                                        value="{{ $privacyValue['title'] ?? '' }}" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 p-4">
                                    <label for="content" class="form-label">{{ __('Content') }}</label>
                                    <textarea name="content" id="editor_privacy_content" class="form-control" rows="10">{{ $privacyValue['content'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button class="btn btn-primary">{{ __('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- Contact Us --}}
        @php
            $contactUs = $webSettings->firstWhere('key', 'contact_us');
            $contactValue = $contactUs?->value ?? [];
        @endphp
        <div id="section-contact" class="mx-4 card mt-3 rounded-lg border-none site-page-card" style="scroll-margin-top: 90px;">
            <div class="card-header py-2 bg-primary">
                <h2 class="text-white">{{ __('تواصل معنا') }}</h2>
            </div>
            <div class="d-flex justify-content-center">
                <div class="row card-body">
                    <div class="col-12">
                        <form action="{{ route('web.settingUpdate', 'contact_us') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="contact_title" class="form-label">{{ __('العنوان') }}</label>
                                <input type="text" name="title" id="contact_title" class="form-control" value="{{ $contactValue['title'] ?? '' }}" />
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_phone" class="form-label">{{ __('رقم الهاتف (يظهر في التطبيق)') }}</label>
                                    <input type="text" name="phone_number" id="contact_phone" class="form-control" dir="ltr"
                                        placeholder="0592925567" value="{{ $contactValue['phone_number'] ?? '' }}" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contact_address" class="form-label">{{ __('العنوان / الموقع (يظهر في التطبيق)') }}</label>
                                    <input type="text" name="address" id="contact_address" class="form-control" value="{{ $contactValue['address'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="contact_content" class="form-label">{{ __('المحتوى') }}</label>
                                <textarea name="content" id="contact_content" class="form-control" rows="6">{{ $contactValue['content'] ?? '' }}</textarea>
                            </div>
                            <p class="text-muted small">{{ __('عند الحفظ يظهر الرقم والعنوان مباشرة في شاشة تواصل معنا داخل التطبيق.') }}</p>
                            <button class="btn btn-primary">{{ __('حفظ') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- About Us --}}
        @php
            $aboutUs = $webSettings->firstWhere('key', 'about_us');
            $aboutValue = $aboutUs?->value ?? [];
        @endphp
        <div id="section-about" class="mx-4 card mt-3 rounded-lg border-none site-page-card" style="scroll-margin-top: 90px;">
            <div class="card-header py-2 bg-primary">
                <h2 class="text-white">{{ __('المزيد عنا') }}</h2>
            </div>
            <div class="d-flex justify-content-center">
                <div class="row card-body">
                    <div class="col-12">
                        <form action="{{ route('web.settingUpdate', 'about_us') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="about_title" class="form-label">{{ __('العنوان') }}</label>
                                <input type="text" name="title" id="about_title" class="form-control" value="{{ $aboutValue['title'] ?? '' }}" />
                            </div>
                            <div class="mb-3">
                                <label for="about_content" class="form-label">{{ __('المحتوى') }}</label>
                                <textarea name="content" id="about_content" class="form-control" rows="6">{{ $aboutValue['content'] ?? '' }}</textarea>
                            </div>
                            <button class="btn btn-primary">{{ __('حفظ') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // التمرير التلقائي لقسم صفحات الموقع المطلوب من الشريط الجانبي
            (function scrollToSiteSection() {
                var map = {
                    'terms_conditions': 'section-terms',
                    'privacy_policy': 'section-privacy',
                    'contact_us': 'section-contact',
                    'about_us': 'section-about'
                };
                var params = new URLSearchParams(window.location.search);
                var section = params.get('section');
                var targetId = map[section] || (window.location.hash ? window.location.hash.substring(1) : null);
                if (targetId) {
                    var el = document.getElementById(targetId);
                    if (el) {
                        setTimeout(function() {
                            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            el.style.boxShadow = '0 0 0 3px rgba(0,123,255,.45)';
                            setTimeout(function() { el.style.boxShadow = ''; }, 2500);
                        }, 300);
                    }
                }
            })();
        });
    </script>
@endpush
