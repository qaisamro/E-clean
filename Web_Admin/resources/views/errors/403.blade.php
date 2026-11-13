<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>غير مصرح لك — النخبة دراي كلين</title>
    <link rel="icon" href="{{ asset('web/logo.jpg?v=2') }}">
    <link rel="stylesheet" href="{{ asset('web/css/all.min.css') }}" type="text/css">
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; height: 100%; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #eef6f8 0%, #f8fafb 45%, #eef2f7 100%);
            font-family: "Tajawal", "Open Sans", Tahoma, sans-serif;
            color: #0B1E2E;
            padding: 24px;
        }
        .ec-error {
            width: 100%; max-width: 560px; background: #ffffff; border-radius: 22px;
            padding: 44px 40px 34px; text-align: center;
            box-shadow: 0 24px 60px rgba(2, 128, 144, .18), 0 4px 14px rgba(13, 27, 62, .08);
            border: 1px solid #e2e8f0;
        }
        .ec-error-icon {
            width: 88px; height: 88px; margin: 0 auto 18px; border-radius: 22px;
            background: linear-gradient(135deg, #028090, #00A896);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 12px 30px rgba(2, 128, 144, .35);
        }
        .ec-error-icon i { color: #ffffff; font-size: 40px; }
        .ec-error-code {
            display: inline-block; background: #fff8e6; color: #92400E;
            border: 1px solid #f5e3ae; border-radius: 999px;
            font-weight: 800; font-size: 13px; padding: 4px 16px; letter-spacing: .12em;
            margin-bottom: 10px;
        }
        h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 10px; color: #0B1E2E; }
        p { margin: 0 0 22px; color: #475569; font-size: .95rem; line-height: 1.8; }
        p strong { color: #0B1E2E; }
        .ec-error-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .ec-btn {
            display: inline-flex; align-items: center; gap: 8px;
            border-radius: 12px; padding: 11px 22px; font-weight: 800; font-size: .9rem;
            text-decoration: none; border: none; cursor: pointer; transition: transform .15s ease, box-shadow .15s ease;
        }
        .ec-btn-primary { background: linear-gradient(135deg, #028090, #00A896); color: #fff; box-shadow: 0 8px 20px rgba(2, 128, 144, .35); }
        .ec-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(2, 128, 144, .42); }
        .ec-btn-ghost { background: #f1f5f9; color: #0B1E2E; border: 1px solid #e2e8f0; }
        .ec-btn-ghost:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="ec-error" role="alert">
        <div class="ec-error-icon"><i class="fas fa-lock"></i></div>
        <div class="ec-error-code">403 — غير مصرح</div>
        <h1>لا تملك صلاحية الدخول لهذه الصفحة</h1>
        <p>
            هذه الصفحة مقيدة. إذا كنت <strong>حساباً فرعياً</strong>، فمالك المتجر هو من يمنح الصلاحيات —<br>
            تواصل معه ليفعّل لك ما تحتاجه، أو عد إلى صفحتك الرئيسية.
        </p>
        <div class="ec-error-actions">
            <button type="button" class="ec-btn ec-btn-ghost" onclick="window.history.back()">
                <i class="fas fa-arrow-right"></i> رجوع
            </button>
            <a class="ec-btn ec-btn-primary" href="{{ auth()->check() && \App\Support\VendorAccount::isStoreStaff(auth()->user()) ? route('vendor.dashboard') : route('root') }}">
                <i class="fas fa-home"></i> صفحتي الرئيسية
            </a>
        </div>
    </div>
</body>
</html>
