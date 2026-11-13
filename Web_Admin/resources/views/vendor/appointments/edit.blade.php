@extends('layouts.app')
@section('content')
<div class="appointments container-fluid" dir="rtl">
    <div class="ap-heading"><div><h2>إدارة مواعيد الاستلام والتوصيل</h2><p>{{ $vendor->name }} · توقيت فلسطين · Asia/Hebron</p></div><button class="btn btn-primary" id="ap-save">حفظ الجدول</button></div>
    <div id="ap-status" role="status" aria-live="polite"></div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <section class="ap-card">
        <label><input id="ap-enabled" type="checkbox"> تفعيل حجز المواعيد الخاص بالمتجر</label>
        <p>قبل التفعيل يستمر الجدول العام القديم. كل صف أدناه فترة حجز واحدة؛ أضف عدة فترات في اليوم حسب الحاجة. لا تُغيّر الحجوزات الحالية عند الحفظ.</p>
        <p class="alert alert-info">فعّل الجدول المخصص بعد تحديث تطبيق الزبائن. الإصدارات القديمة التي لا ترسل رقم المتجر عند جلب المواعيد تعرض الجدول العام؛ ستُرفض مواعيدها إذا لم تطابق فترات المتجر الجديدة.</p>
        <div class="ap-fields"><label>مدة تجهيز الغسيل <input id="ap-processing" type="number" min="1" step="1"></label>
            <label>الوحدة <select id="ap-unit"><option value="60">ساعة</option><option value="1440">يوم</option><option value="1">دقيقة</option></select></label>
            <label>إتاحة الحجز للأيام القادمة <input id="ap-horizon" type="number" min="1" max="60"></label></div>
        <small>تبدأ مدة التجهيز من نهاية فترة الاستلام. يتاح التوصيل بعد اكتمالها. الأيام غير المدرجة مغلقة.</small>
    </section>
    <div class="ap-columns">
    @foreach(['pickup'=>'استلام الغسيل من الزبائن','delivery'=>'إعادة الغسيل للزبائن'] as $type=>$label)
        <section class="ap-card"><h3>{{ $label }}</h3><div class="ap-table"><table><thead><tr><th>اليوم</th><th>من</th><th>إلى</th><th>عدد الطلبات</th><th></th></tr></thead><tbody id="ap-{{ $type }}"></tbody></table></div><button class="btn btn-outline-primary ap-add" data-type="{{ $type }}" type="button">+ إضافة فترة</button></section>
    @endforeach
    </div>
    <section class="ap-card"><h3>الإجازات والإغلاقات الاستثنائية</h3><p>اترك الوقتين فارغين لإغلاق اليوم كله. إغلاق جزء من فترة يجعل الفترة كاملة غير متاحة لحجوزات جديدة.</p>
        <div class="ap-table"><table><thead><tr><th>التاريخ</th><th>الخدمة</th><th>من (اختياري)</th><th>إلى (اختياري)</th><th></th></tr></thead><tbody id="ap-exceptions"></tbody></table></div><button id="ap-add-exception" class="btn btn-outline-primary" type="button">+ إضافة إغلاق</button>
    </section>
    <section class="ap-card"><h3>الحجوزات القائمة وإعادة الجدولة</h3><p>آخر 100 طلب قادم. احتفظ بالموعد المتفق عليه حتى عند إغلاق يومه. لإعادة جدولة طلب لم يبدأ تنفيذه، اتفق مع العميل أولاً، ثم اختر فترتين من الجدول الجديد؛ يُعاد التحقق من التوفر عند الحفظ.</p>
        @forelse($orders as $order)
        <details class="ap-order"><summary>طلب {{ $order->order_code }} · {{ $order->pick_date?->format('Y-m-d') }} {{ $order->pick_hour }} ← {{ $order->delivery_date?->format('Y-m-d') }} {{ $order->delivery_hour }}</summary>
        @if(in_array($order->order_status, ['Pending', 'Order confirmed'], true))
        <form action="{{ route('vendor.appointments.reschedule', $order) }}" method="post">@csrf
            <div class="ap-fields"><label>يوم الاستلام<input type="date" name="pick_date" required></label><label>بداية فترة الاستلام<input type="time" name="pick_hour" required></label>
            <label>يوم التوصيل<input type="date" name="delivery_date" required></label><label>بداية فترة التوصيل<input type="time" name="delivery_hour" required></label></div>
            <label>سبب إعادة الجدولة<input name="reason" minlength="3" maxlength="500" required></label>
            <label><input type="checkbox" name="customer_agreed" value="1" required> اتفقت مع العميل على الموعدين الجديدين</label><button class="btn btn-outline-primary">تأكيد إعادة الجدولة</button>
        </form>@else<p>بدأ تنفيذ هذا الطلب؛ الموعد محفوظ.</p>@endif</details>
        @empty<p>لا توجد حجوزات قادمة.</p>@endforelse
    </section>
</div>
@endsection
@section('styles')
<style>
.appointments{max-width:1400px;color:#173744}.ap-heading,.ap-fields{display:flex;gap:16px;align-items:center;flex-wrap:wrap}.ap-heading{justify-content:space-between;margin-bottom:20px}.ap-card{background:var(--bs-body-bg,#fff);border:1px solid #cbdcdd;border-radius:18px;padding:22px;margin-bottom:20px}.ap-card h3{font-size:19px}.ap-card p,.ap-card small{color:var(--bs-body-color,#4b626b)}.ap-columns{display:grid;grid-template-columns:1fr 1fr;gap:20px}.ap-table{overflow:auto;margin:14px 0}.ap-table table{width:100%;min-width:430px}.ap-table th,.ap-table td{padding:7px}.appointments input:not([type=checkbox]),.appointments select{display:block;padding:9px;border:1px solid #8da8af;border-radius:9px;background:var(--bs-body-bg,#fff);color:var(--bs-body-color,#173744);max-width:100%}.appointments input[type=number]{width:120px}.ap-fields{margin:15px 0}.ap-order{padding:15px 0;border-top:1px solid #d5e1e3}.ap-order form{padding-top:16px}.ap-order label{margin:8px}.ap-remove{border:0;color:#a82035;background:transparent}#ap-status{padding:10px;white-space:pre-line}@media(max-width:1000px){.ap-columns{grid-template-columns:1fr}.ap-card{padding:14px}}
</style>
@endsection
@push('scripts')
<script src="{{ asset('web/js/vendor-appointments.js') }}" defer></script>
<script type="application/json" id="ap-config">@json(['settings'=>$settings,'url'=>route('vendor.appointments.update'),'csrf'=>csrf_token()], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
@endpush
