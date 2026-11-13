<x-app-layout :title="'Elite Accounting | تصميم قالب الفاتورة'">
    @push('css')
    <link rel="stylesheet" href="{{ asset('web/css/invoice-builder.css') }}">
    @endpush

    <div class="invoice-builder" dir="rtl">
        <header class="ib-header">
            <div><span class="ib-kicker">Elite Cleaning · الفاتورة</span><h1>تصميم قالب الفاتورة</h1>
                <p>قالب واحد، وثلاث نسخ. خصّص المظهر وشاهد النتيجة مباشرة.</p></div>
            <div class="ib-actions">
                <button type="submit" form="invoice-builder-form" class="ib-primary" id="save-template">حفظ القالب</button>
                <button type="button" id="reset-template">استعادة الافتراضي</button>
                <button type="button" id="preview-print">معاينة الطباعة</button>
            </div>
        </header>
        <div id="builder-status" role="status" aria-live="polite">القيم المالية للعرض فقط؛ لا يغيّر هذا المحرر الطلب أو إعدادات المتجر الأصلية.</div>
        <div class="ib-layout">
            <form id="invoice-builder-form" class="ib-settings" enctype="multipart/form-data">
                @csrf
                <details open><summary>المقاس واللغة والمظهر</summary><div class="ib-grid">
                    @foreach (['paper' => ['المقاس', ['A4' => 'A4', '80mm' => 'حراري 80mm', '58mm' => 'حراري 58mm']], 'language' => ['اللغة', ['ar' => 'العربية', 'en' => 'English']], 'direction' => ['الاتجاه', ['rtl' => 'من اليمين لليسار', 'ltr' => 'من اليسار لليمين']], 'font' => ['الخط', ['system' => 'Tahoma / Arial', 'serif' => 'Times / Georgia', 'mono' => 'Monospace']], 'align' => ['محاذاة النص', ['start' => 'البداية', 'center' => 'الوسط', 'end' => 'النهاية']]] as $key => [$label, $options])
                        <label>{{ $label }}<select data-setting="{{ $key }}">@foreach ($options as $value => $caption)<option value="{{ $value }}">{{ $caption }}</option>@endforeach</select></label>
                    @endforeach
                    @foreach (['font_size' => ['حجم النص', 9, 24], 'heading_size' => ['حجم العنوان', 12, 36], 'spacing' => ['المسافة بين الأقسام (px)', 0, 24], 'padding' => ['الهامش الداخلي (mm)', 2, 15], 'border_width' => ['سمك الحدود (px)', 0, 3]] as $key => [$label, $min, $max])
                        <label>{{ $label }}<input type="number" data-setting="{{ $key }}" min="{{ $min }}" max="{{ $max }}" required></label>
                    @endforeach
                    @foreach (['accent' => 'لون العناوين', 'text_color' => 'لون النص', 'background' => 'لون الورق', 'border_color' => 'لون الحدود'] as $key => $label)
                        <label>{{ $label }}<input type="color" data-setting="{{ $key }}"></label>
                    @endforeach
                </div></details>
                <details><summary>الشعار</summary><div class="ib-grid">
                    <label class="ib-full">رفع شعار<input type="file" id="invoice-logo" accept="image/png,image/jpeg,image/webp"><small>PNG / JPEG / WebP · حتى 1MB و2400×2400. لا يُقبل SVG.</small></label>
                    <label>حجم الشعار (px)<input type="number" data-setting="logo_size" min="24" max="160" required></label>
                    <label>محاذاة الشعار<select data-setting="logo_align"><option value="start">البداية</option><option value="center">الوسط</option><option value="end">النهاية</option></select></label>
                    <label class="ib-check ib-full"><input type="checkbox" id="remove-logo">حذف الشعار المخصص عند الحفظ (يعود شعار الموقع إن توفر)</label>
                </div></details>
                <details open><summary>المعلومات والنصوص</summary><div class="ib-grid">
                    @foreach ($texts as $key => $label)
                        <label class="ib-full">{{ $label }}<textarea data-text="{{ $key }}" rows="{{ in_array($key, ['header', 'footer', 'notes']) ? 3 : 2 }}" maxlength="1000" placeholder="{{ in_array($key, ['store_name', 'address', 'phone', 'email']) ? 'فارغ لاستخدام بيانات الموقع' : '' }}"></textarea></label>
                    @endforeach
                </div></details>
                <details><summary>إظهار الحقول وإخفاؤها</summary><div class="ib-grid">
                    @foreach ($fields as $key => $label)
                        <label class="ib-check"><input type="checkbox" data-visible="{{ $key }}">{{ $label }}</label>
                    @endforeach
                    <p class="ib-help ib-full">تُخفى الضريبة والكاشير وخصم الصنف عند عدم وجود بيانات محفوظة لها. رقم الفاتورة يستخدم رقم الطلب الحالي؛ لا يتم إنشاء فاتورة محاسبية جديدة.</p>
                </div></details>
                <details><summary>ترتيب الأقسام</summary><p class="ib-help">استخدم الأسهم لتحريك القسم. النسخ الثلاث تستخدم الترتيب نفسه.</p><ol id="invoice-sections"></ol></details>
            </form>
            <aside class="ib-preview" aria-label="المعاينة المباشرة">
                <div class="ib-preview-top"><div><strong>المعاينة المباشرة</strong><span id="preview-badge">مباشر</span></div><small>تعديلاتك تظهر فورًا قبل الحفظ</small></div>
                <div class="ib-preview-selects">
                    <label>طلب حقيقي للمعاينة<select id="preview-order">
                        @forelse ($orders as $order)<option value="{{ $order->id }}">{{ $order->order_code }}</option>@empty<option value="">لا توجد طلبات صالحة للمعاينة</option>@endforelse
                    </select></label>
                    <label>نوع النسخة<select id="preview-copy"><option value="normal">طباعة عادية</option><option value="customer">نسخة الزبون</option><option value="store">نسخة المحل</option></select></label>
                </div>
                <iframe id="invoice-preview" title="معاينة الفاتورة" src="{{ route('invoice-template.preview', $orders->first() ? ['order' => $orders->first()->id] : []) }}"></iframe>
                <p class="ib-help">الحراري يستخدم أسطرًا مرنة بدل ضغط الجدول. اضبط مقاس الورق من تعريف الطابعة وأوقف رؤوس المتصفح. الألوان تعتمد على الطابعة.</p>
            </aside>
        </div>
    </div>
    <script>window.invoiceBuilder = {{ Illuminate\Support\Js::from(['settings' => $settings, 'defaults' => $defaults, 'logo' => $logo, 'saveUrl' => route('invoice-template.update'), 'resetUrl' => route('invoice-template.reset'), 'previewUrl' => route('invoice-template.preview')]) }};</script>
    <script src="{{ asset('web/js/invoice-builder.js') }}" defer></script>
</x-app-layout>
