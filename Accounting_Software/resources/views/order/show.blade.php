<x-app-layout :title="config('app.name') . ' | تفاصيل طلب ' . $order->order_code">

    {{-- =========== Breadcrumb & Actions (مخفية عند الطباعة) =========== --}}
    <div id="hide-on-print" class="d-flex justify-content-between align-items-center flex-column flex-md-row mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">
                <a href="{{ route('orders.index') }}">الطلبات</a> /
            </span>
            تفاصيل طلب {{ $order->order_code }}
        </h4>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a class="btn btn-outline-secondary" href="{{ route('orders.edit', $order) }}">
                <i class='bx bx-edit-alt me-1'></i> تعديل
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class='bx bx-printer me-1'></i> طباعة
            </button>

            <button id="btnRppShare" class="btn btn-outline-info">
                <i class='bx bx-share-alt me-1'></i> حفظ / مشاركة صورة الفاتورة
            </button>
            <button onclick="downloadPDF()" class="btn btn-outline-success">
                <i class='bx bxs-file-pdf me-1'></i> تحميل PDF
            </button>
            <a href="{{ route('orders.stickers', $order) }}" class="btn btn-outline-info">
                <i class='bx bx-tag me-1'></i> ملصقات القطع
            </a>
            @if(auth()->user()->isSuperAdmin())
                <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class='bx bx-trash me-1'></i> حذف
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- حالة الاتصال وشريط التقدم للطباعة الحرارية --}}
    <div id="rppStatusWrap" class="no-print mb-3" style="display:none">
        <div id="rppStatusAlert" class="alert alert-info py-2 small mb-2 d-flex justify-content-between align-items-center">
            <span id="rppStatusText"></span>
            <button type="button" class="btn-close btn-sm" onclick="document.getElementById('rppStatusWrap').style.display='none'"></button>
        </div>
        <div id="rppProgressWrap" style="display:none !important;"></div>
        <canvas id="rppCanvas" width="576" height="1200" style="display:none"></canvas>
    </div>
    <div id="rppBrowserNotice" class="alert alert-warning no-print py-2 small" style="display:none"></div>

    {{-- =========== Invoice Card =========== --}}
    <div class="card shadow-sm" id="invoice-card">

            {{-- Invoice Header - Elite Cleaning Thermal Optimized --}}
            <div class="card-body pb-0">
                <div class="row align-items-center border-bottom pb-3 mb-3">
                    <div class="col-md-6 d-flex align-items-center gap-3">
                        <img src="{{ $logo ?? asset('assets/img/logo.webp') }}" style="width: 70px; height: auto; border-radius: 8px;" alt="Elite Logo">
                        <div>
                            <h2 class="fw-bold text-primary mb-1">شركة النخبة للتنظيف</h2>
                            <p class="text-muted mb-0 small">Elite Cleaning Company</p>
                            <p class="text-muted mb-0 small">نظام إدارة الغسيل والتنظيف الجاف</p>
                        </div>
                    </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h4 class="fw-bold mb-1">فاتورة / Invoice</h4>
                    <p class="mb-0 text-muted"># {{ $order->order_code }}</p>
                    <p class="mb-0 text-muted small">
                        تاريخ الإنشاء: {{ $order->created_at->format('Y/m/d H:i') }}
                    </p>
                    @if($order->deliver_date)
                        <p class="mb-0 text-muted small">
                            موعد التسليم: {{ \Carbon\Carbon::parse($order->deliver_date)->format('Y/m/d') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Customer & Order Status --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted text-uppercase small mb-2">بيانات العميل</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-bold py-1 ps-0" style="width:100px">الاسم:</td>
                            <td class="fw-bold py-1">{{ $order->customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold py-1 ps-0">الهاتف:</td>
                            <td class="fw-bold py-1">{{ $order->customer->phone }}</td>
                        </tr>
                        @if($order->customer->address)
                            <tr>
                                <td class="text-muted fw-bold py-1 ps-0">العنوان:</td>
                                <td class="fw-bold py-1">{{ $order->customer->address }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <h6 class="fw-bold text-muted text-uppercase small mb-2">حالة الطلب</h6>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="text-muted small">حالة الطلب: </span>
                            @php
                                $statusColors = [
                                    'pending'    => 'warning',
                                    'processing' => 'info',
                                    'ready'      => 'primary',
                                    'completed'  => 'success',
                                    'delivered'  => 'success',
                                    'cancelled'  => 'danger',
                                ];
                                $statusKey = is_string($order->status) ? $order->status : $order->status->value;
                                $statusColor = $statusColors[$statusKey] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">{{ $order->readable_order_status }}</span>
                        </div>
                        <div>
                            <span class="text-muted small">حالة الدفع: </span>
                            @php
                                $payColors = ['paid' => 'success', 'unpaid' => 'danger', 'partially_paid' => 'warning'];
                                $payKey = is_string($order->payment_status) ? $order->payment_status : $order->payment_status->value;
                                $payColor = $payColors[$payKey] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $payColor }}">{{ $order->readable_payment_status }}</span>
                        </div>
                        <div>
                            <span class="text-muted small">طريقة الدفع: </span>
                            <span class="fw-bold">{{ $order->readable_payment_type }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items Table --}}
            <h6 class="fw-bold text-muted text-uppercase small mb-2">تفاصيل الخدمات</h6>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:45px">#</th>
                            <th>الصنف</th>
                            <th>الخدمة</th>
                            <th class="text-center">الكمية</th>
                            <th class="text-center">سعر الوحدة</th>
                            <th class="text-center">الإجمالي</th>
                            <th class="text-center hide-on-print">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 1; @endphp
                        @forelse ($order->orderDetails as $detail)
                            <tr>
                                <td class="text-muted">{{ $rowNum++ }}</td>
                                <td class="fw-bold">{{ $detail->item->name }}</td>
                                <td>{{ $detail->service->name }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td class="text-center">
                                    @if($detail->price)
                                        {{ number_format($detail->price, 2) }} ₪
                                    @else
                                        <span class="badge bg-label-warning">مؤجل</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    @if($detail->total_price)
                                        {{ number_format($detail->total_price, 2) }} ₪
                                    @else
                                        <span class="badge bg-label-warning">مؤجل</span>
                                    @endif
                                </td>
                                <td class="text-center hide-on-print">
                                    @if($detail->description)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detail-{{ $detail->id }}"
                                        >
                                            <i class="bx bx-info-circle"></i>
                                        </button>
                                        <div class="modal fade" id="detail-{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ملاحظات الصنف</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">{{ $detail->description }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-4" colspan="7">
                                    <strong>لا توجد بنود في هذا الطلب</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Invoice Totals --}}
            <div class="row justify-content-end mb-4">
                <div class="col-md-5">
                    <div class="card bg-light border-0">
                        <div class="card-body py-3">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold text-muted">الإجمالي:</td>
                                    <td class="text-end fw-bold">{{ number_format($order->total_price, 2) }} ₪</td>
                                </tr>
                                @if($order->discount && $order->discount > 0)
                                    <tr>
                                        <td class="text-danger fw-bold">الخصم:</td>
                                        <td class="text-end text-danger fw-bold">- {{ number_format($order->discount, 2) }} ₪</td>
                                    </tr>
                                @endif
                                @if($order->paid_money > 0)
                                    <tr>
                                        <td class="text-success fw-bold">المدفوع:</td>
                                        <td class="text-end text-success fw-bold">{{ number_format($order->paid_money, 2) }} ₪</td>
                                    </tr>
                                @endif
                                @if($order->remaining_money > 0)
                                    <tr class="border-top">
                                        <td class="text-danger fw-bold">المتبقي (دين):</td>
                                        <td class="text-end text-danger fw-bold fs-6">{{ number_format($order->remaining_money, 2) }} ₪</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($order->notes)
                <div class="alert alert-light border mb-4">
                    <strong><i class="bx bx-note me-1"></i> ملاحظات:</strong> {{ $order->notes }}
                </div>
            @endif

            {{-- Payment History --}}
            @if($order->payments && $order->payments->count() > 0)
                <div id="hide-on-print" class="mb-4">
                    <h6 class="fw-bold text-muted text-uppercase small mb-2">سجل الدفعات</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المبلغ</th>
                                    <th>الطريقة</th>
                                    <th>بواسطة</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold text-success">{{ number_format($payment->amount, 2) }} ₪</td>
                                        <td>
                                            @if($payment->payment_method == 'cash')
                                                <span class="badge bg-label-success">نقدي</span>
                                            @elseif($payment->payment_method == 'visa')
                                                <span class="badge bg-label-primary">فيزا</span>
                                            @else
                                                <span class="badge bg-label-secondary">{{ $payment->payment_method }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->creator->name ?? '—' }}</td>
                                        <td>{{ $payment->notes ?? '—' }}</td>
                                        <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Staff Info --}}
            @if($order->user_id)
                <div class="border-top pt-3 pb-2 text-muted small">
                    <i class="bx bx-user me-1"></i>
                    أنشأ الطلب: <strong>{{ $order->user->name }}</strong>
                    في {{ $order->created_at->format('Y/m/d H:i') }}
                </div>
            @endif

            {{-- Print Footer --}}
            <div class="print-only border-top pt-3 text-center text-muted small" style="display:none">
                <p class="mb-0">شكراً لاختياركم {{ config('app.name', 'شركة النخبة دراي كلين') }} ❤️</p>
                <p class="mb-0">للاستفسار يرجى الاتصال بنا</p>
            </div>
        </div>

    </div>

</x-app-layout>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

<style>
    /* ===== Arabic-friendly base for invoice (screen + print) ===== */
    #invoice-card {
        font-family: 'Tajawal', 'Cairo', Tahoma, Arial, sans-serif !important;
        letter-spacing: 0 !important;
        word-spacing: 0 !important;
        line-height: 1.7;
    }
    #invoice-card * {
        letter-spacing: 0 !important;
        word-spacing: 0 !important;
    }

    /* ================= A4 PRINT ================= */
    @media print {
        /* Print ONLY the invoice using visibility technique */
        body * { visibility: hidden !important; }
        #invoice-card, #invoice-card * { visibility: visible !important; }
        #invoice-card {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            margin: 0 auto !important;
            width: 100% !important;
            max-width: 180mm !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: white !important;
        }
        .print-only { display: block !important; }
        #hide-on-print, .hide-on-print { display: none !important; }

        body, html { background: white !important; margin: 0 !important; padding: 0 !important; }
        @page { size: A4 portrait; margin: 10mm; }

        /* Clean tables & badges for print */
        #invoice-card table { width: 100% !important; border-collapse: collapse; }
        #invoice-card th, #invoice-card td { border: 1px solid #999 !important; padding: 5px 7px !important; text-align: right; }
        #invoice-card thead th { background: #eee !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        #invoice-card tr, #invoice-card td, #invoice-card th { break-inside: avoid; page-break-inside: avoid; }
        .badge { border: 1px solid #666 !important; background: white !important; color: black !important; padding: 2px 8px; }
        a { color: inherit !important; text-decoration: none !important; }
    }

    /* ================= THERMAL 80mm ================= */
    body.thermal-print #invoice-card { width: 74mm !important; max-width: 74mm !important; margin: 0 auto !important; font-size: 11px !important; }
    body.thermal-print #invoice-card h2 { font-size: 15px !important; }
    body.thermal-print #invoice-card h4 { font-size: 12px !important; }
    body.thermal-print #invoice-card h6 { font-size: 11px !important; }
    body.thermal-print #invoice-card img { width: 55px !important; }
    body.thermal-print #invoice-card .row { display: block !important; }
    body.thermal-print #invoice-card [class*="col-"] { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
    body.thermal-print #invoice-card .table { font-size: 9px !important; display: table !important; }
    body.thermal-print #invoice-card .table th, body.thermal-print #invoice-card .table td { padding: 2px 3px !important; }
    body.thermal-print #invoice-card .card-body { padding: 2mm !important; }
    @media print {
        body.thermal-print * { visibility: hidden !important; }
        body.thermal-print #invoice-card, body.thermal-print #invoice-card * { visibility: visible !important; }
        body.thermal-print #invoice-card {
            position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important;
            margin: 0 auto !important;
        }
        @page { size: 80mm auto; margin: 3mm; }
    }
</style>

@php
    $rppInvoice = [
        'store' => 'شركة النخبة للتنظيف',
        'customer' => $order->customer?->name ?? '',
        'phone' => $order->customer?->phone ?? '',
        'order' => (string) $order->order_code,
        'date' => $order->created_at ? $order->created_at->format('Y/m/d H:i') : '',
        'address' => $order->customer?->address ?? '',
        'status' => $order->readable_order_status ?? '',
        'pay_status_text' => $order->readable_payment_status ?? '',
        'created_by' => $order->user?->name ?? '',
        'deliver' => $order->deliver_date ? \Carbon\Carbon::parse($order->deliver_date)->format('Y/m/d') : '',
        'items' => $order->orderDetails->map(function ($d) {
            return [
                'name' => trim(($d->item?->name ?? '') . ($d->service?->name ? ' - ' . $d->service->name : '')),
                'qty' => (float) ($d->quantity ?? 1),
                'price' => (float) ($d->price ?? 0),
                'total' => (float) ($d->total_price ?? 0),
            ];
        })->values()->toArray(),
        'pay_method' => $order->readable_payment_type ?? '',
        'pay_status' => $order->readable_payment_status ?? '',
        'notes' => (string) ($order->notes ?? ''),
        'total' => (float) $order->total_price,
        'discount' => (float) ($order->discount ?? 0),
        'paid' => (float) ($order->paid_money ?? 0),
        'remaining' => (float) ($order->remaining_money ?? 0),
    ];
@endphp
<script>
window.RPP_INVOICE = @json($rppInvoice);
function downloadPDF() {
    var src = document.getElementById('invoice-card');
    if (!src) return;
    var clone = src.cloneNode(true);
    var actions = clone.querySelector('#hide-on-print');
    if (actions) actions.remove();

    var w = window.open('', '_blank');
    if (!w) { alert('يرجى السماح بالنوافذ المنبثقة لتحميل الفاتورة PDF'); return; }

    var html = '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">' +
        '<title>invoice-{{ $order->order_code }}</title>' +
        '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' +
        '<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">' +
        '<style>' +
        'body{font-family:"Tajawal",Tahoma,Arial,sans-serif !important;direction:rtl;margin:0;padding:14px;background:#fff;' +
        'letter-spacing:0 !important;word-spacing:0 !important;line-height:1.8;}' +
        '*{letter-spacing:0 !important;word-spacing:0 !important;}' +
        'h1,h2,h3,h4,h5,h6,p,span,td,th,strong,small,div{font-family:"Tajawal",Tahoma,Arial,sans-serif !important;}' +
        '#invoice-card{width:100% !important;max-width:190mm !important;margin:0 auto !important;border:none !important;box-shadow:none !important;font-size:13px}' +
        '#invoice-card .row{display:flex;flex-wrap:wrap}' +
        'table{width:100%;border-collapse:collapse}' +
        'th,td{border:1px solid #999;padding:6px 9px;text-align:right;font-size:13px}' +
        'thead th{background:#eee !important;color:#000 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact}' +
        '.badge{border:1px solid #666;padding:2px 10px;border-radius:4px;color:#000;background:#fff;display:inline-block}' +
        'img{max-width:75px;height:auto}' +
        '.print-only{display:block !important}' +
        '@media print{@page{size:A4 portrait;margin:10mm}}' +
        '</style></head><body>' + clone.outerHTML +
        '<scr'+'ipt>window.onload=function(){setTimeout(function(){window.print();},600);};<\/scr'+'ipt>' +
        '</body></html>';

    w.document.open();
    w.document.write(html);
    w.document.close();
}
function paperThermal() {
    document.body.classList.add('thermal-print');
    setTimeout(function() {
        window.print();
        setTimeout(function() { document.body.classList.remove('thermal-print'); }, 500);
    }, 50);
}
function printThermal() {
    if (!navigator.bluetooth) {
        var ua = navigator.userAgent || '';
        var isIos = /iPad|iPhone|iPod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        if (isIos) {
            var msg = 'المتصفح الحالي لا يدعم Web Bluetooth على iOS.\nللطباعة الحرارية المباشرة بضغطة زر: افتح هذه الصفحة داخل متصفح Bluefy المجاني من App Store.\n\nهل ترغب في استخدام الطباعة الورقية العادية الآن؟';
            if (confirm(msg)) {
                paperThermal();
            }
        } else {
            var msg2 = 'المتصفح لا يدعم Web Bluetooth — استخدم متصفح Chrome أو Edge مع رابط HTTPS آمن.\n\nهل ترغب في استخدام الطباعة الورقية العادية الآن؟';
            if (confirm(msg2)) {
                paperThermal();
            }
        }
        return;
    }
    if (window.rppPrintOneClick) {
        window.rppPrintOneClick();
    }
}

/* ===== طباعة بلوتوث مباشرة — Rongta RPP320 (80مم، ESC/POS) ===== */
(function () {
'use strict';
var RPP = {
    dotsWidth: 576,
    chunkSize: 20,
    chunkDelayMs: 12,
    minChunkSize: 20,
    maxReconnectAttempts: 3,
    writeTimeoutMs: 5000
};

var CANDIDATES = [
    // 1) 0xFF00 (Rongta primary POS service)
    { service: 0xFF00, write: [0xFF02, 0xFF01, '0000ff02-0000-1000-8000-00805f9b34fb', '0000ff01-0000-1000-8000-00805f9b34fb'] },
    { service: '0000ff00-0000-1000-8000-00805f9b34fb', write: ['0000ff02-0000-1000-8000-00805f9b34fb', '0000ff01-0000-1000-8000-00805f9b34fb', 0xFF02, 0xFF01] },
    // 2) Nordic UART Service
    { service: '6e400001-b5a3-f393-e0a9-e50e24dcca9e', write: ['6e400002-b5a3-f393-e0a9-e50e24dcca9e'] },
    // 3) 0xFFE0
    { service: 0xFFE0, write: [0xFFE1, '0000ffe1-0000-1000-8000-00805f9b34fb'] },
    { service: '0000ffe0-0000-1000-8000-00805f9b34fb', write: ['0000ffe1-0000-1000-8000-00805f9b34fb', 0xFFE1] },
    // 4) 0xFFF0
    { service: 0xFFF0, write: [0xFFF1, 0xFFF2, 0xFFF3, '0000fff1-0000-1000-8000-00805f9b34fb', '0000fff2-0000-1000-8000-00805f9b34fb'] },
    { service: '0000fff0-0000-1000-8000-00805f9b34fb', write: ['0000fff1-0000-1000-8000-00805f9b34fb', '0000fff2-0000-1000-8000-00805f9b34fb', 0xFFF1, 0xFFF2, 0xFFF3] },
    // 5) ISSC Transparent
    { service: '49535343-fe7d-4ae5-8fa9-9fafd205e455', write: ['49535343-8841-43f4-a8d4-ecbe34729bb3'] },
    // 6) Other POS services
    { service: 0x18F0, write: [0x2AF0, 0x2AF1, '00002af0-0000-1000-8000-00805f9b34fb', '00002af1-0000-1000-8000-00805f9b34fb'] },
    { service: '000018f0-0000-1000-8000-00805f9b34fb', write: ['00002af0-0000-1000-8000-00805f9b34fb', '00002af1-0000-1000-8000-00805f9b34fb'] },
    { service: 0xAE30, write: [0xAE01, '0000ae01-0000-1000-8000-00805f9b34fb'] },
    { service: 0xAF30, write: [0xAF01, '0000af01-0000-1000-8000-00805f9b34fb'] },
    { service: 0xFEE7, write: [0xFEC7, 0xFEC8, 0xFED4, 0xFED5, 0xFED6, 0xFED7, 0xFED8] }
];

var OPTIONAL_SERVICES = [
    '6e400001-b5a3-f393-e0a9-e50e24dcca9e',
    '0000ff00-0000-1000-8000-00805f9b34fb',
    '0000ffe0-0000-1000-8000-00805f9b34fb',
    '0000fff0-0000-1000-8000-00805f9b34fb',
    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
    '000018f0-0000-1000-8000-00805f9b34fb',
    '0000ae30-0000-1000-8000-00805f9b34fb',
    '0000af30-0000-1000-8000-00805f9b34fb',
    '0000fee7-0000-1000-8000-00805f9b34fb',
    '0000ffe5-0000-1000-8000-00805f9b34fb',
    'e7810a71-73ae-499d-8c15-faa9aef0c3f',
    '0000180a-0000-1000-8000-00805f9b34fb',
    '0000eee0-0000-1000-8000-00805f9b34fb',
    '0000eee2-0000-1000-8000-00805f9b34fb',
    '0000ff10-0000-1000-8000-00805f9b34fb',
    '0000ff80-0000-1000-8000-00805f9b34fb'
];

var rppDevice = null, rppChar = null, rppOk = false, rppBusy = false;
var rppAllWritableChars = [], rppCurrentCharIdx = -1;
var ESC = 0x1B, GS = 0x1D;

function sleep(ms) {
    return new Promise(function (r) { setTimeout(r, ms); });
}

// Rejects if a BLE promise never settles (the freeze root cause)
function rppTimeout(promise, ms) {
    return new Promise(function (resolve, reject) {
        var done = false;
        var timer = setTimeout(function () {
            if (!done) { done = true; reject(new Error('WRITE_TIMEOUT')); }
        }, ms || 5000);
        promise.then(function (v) {
            if (!done) { done = true; clearTimeout(timer); resolve(v); }
        }, function (e) {
            if (!done) { done = true; clearTimeout(timer); reject(e); }
        });
    });
}

function rppStatus(msg, type) {
    if (!type) type = 'info';
    var textEl = document.getElementById('rppStatusText');
    var badgeEl = document.getElementById('rppStatus');
    var wrapEl = document.getElementById('rppStatusWrap');
    var alertEl = document.getElementById('rppStatusAlert');

    if (badgeEl) badgeEl.textContent = msg;
    if (textEl) textEl.textContent = msg;
    if (wrapEl) wrapEl.style.display = 'block';
    if (alertEl) {
        alertEl.className = 'alert alert-' + type + ' py-2 small mb-2 d-flex justify-content-between align-items-center';
    }
}

function rppProgress(pct) { /* counter display disabled */ }

function rppSetBusy(busy) {
    rppBusy = !!busy;
    ['btnThermal', 'btnTsplTest', 'btnRppShare'].forEach(function (id) {
        var b = document.getElementById(id);
        if (b) b.disabled = !!busy;
    });
}

function isIos() {
    var ua = navigator.userAgent || '';
    return /iPad|iPhone|iPod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

function isBluefy() {
    return /Bluefy/i.test(navigator.userAgent || '');
}

function rppError(err) {
    if (!err) return 'حدث خطأ غير معروف';
    var n = (err && err.name) || '', m = (err && err.message) || String(err);

    if (err === 'NO_BLUETOOTH' || (/Bluetooth/i.test(m) && !navigator.bluetooth)) {
        if (isIos()) {
            return 'المتصفح لا يدعم Web Bluetooth على iOS — يرجى فتح هذه الصفحة في متصفح Bluefy المجاني من App Store للطباعة المباشرة.';
        }
        return 'المتصفح لا يدعم Web Bluetooth — استخدم متصفح Chrome أو Edge مع رابط HTTPS آمن.';
    }

    if (n === 'NotFoundError' || /cancell?ed|cansell?ed|discovery was c/i.test(m)) {
        return 'تم إلغاء اختيار الطابعة.';
    }

    if (n === 'SecurityError' || /HTTPS|localhost/i.test(m)) {
        return 'الصفحة تحتاج اتصال HTTPS آمن للوصول إلى البلوتوث.';
    }

    if (n === 'NetworkError' || n === 'NotConnectedError') {
        return 'تعذر الاتصال بالطابعة RPP320 — تأكد من تشغيلها وقربها وأنها غير مقترنة بجهاز آخر.';
    }

    if (err && err.message === 'DISCONNECTED') {
        return 'انقطع اتصال البلوتوث اثناء البحث - اعد الاتصال بالطابعة.';
    }

    if (err && err.message === 'SCAN_FAIL') {
        return 'تعذر مسح خدمات الطابعة - اعد الاتصال وحاول مجددا.';
    }

    if (err && (err.message === 'WRITE_TIMEOUT')) {
        return 'انتهت مهلة الكتابة - القناة لا تستجيب، اضغط القناة التالية او اعد الاتصال.';
    }

    if (err && (err.message === 'WRITE_FAIL')) {
        return 'تعذرت الكتابة للطابعة - تحقق من القناة وقرب الطابعة.';
    }

    if (/wihout|without|with.?response|reponse|response|charact|gatt|fail|error/i.test(m)) {
        return 'تعذرت الكتابة عبر قناة البلوتوث - اعد المحاولة، وان تكرر اعد الاتصال بالطابعة.';
    }

    if (err && err.message === 'EMPTY_CANVAS') {
        return 'لوحة الرسم فارغة - لا توجد بيانات للطباعة.';
    }

    if (err === 'CHANNEL_FAIL' || /CHANNEL_FAIL/i.test(m)) {
        return 'تم الاتصال بـ RPP320 ولكن لم يتم العثور على قناة كتابة متوافقة (تأكد من اختيار طابعة فواتير متوافقة).';
    }

    return m;
}

// TSPL-safe probe: a blank line is ignored by TSPL and proves the pipe accepts writes.
// (Never send ESC/POS DLE EOT to a TSPL-mode printer.)
async function testWriteProbe(ch) {
    if (!ch || !ch.properties) return false;
    var p = ch.properties;
    if (!p.write && !p.writeWithoutResponse) return false;
    var probe = new Uint8Array([0x0D, 0x0A]);
    var ms = 4000;
    try {
        if (p.writeWithoutResponse && typeof ch.writeValueWithoutResponse === 'function') {
            await rppTimeout(ch.writeValueWithoutResponse(probe), ms);
            return true;
        }
    } catch (e1) { /* fall through */ }
    try {
        if (p.write && typeof ch.writeValueWithResponse === 'function') {
            await rppTimeout(ch.writeValueWithResponse(probe), ms);
            return true;
        }
    } catch (e2) { /* fall through */ }
    try {
        if (typeof ch.writeValue === 'function') {
            await rppTimeout(ch.writeValue(probe), ms);
            return true;
        }
    } catch (e3) { return false; }
    return false;
}

// Root fix: confirm the channel by READING a printer reply.
// Sends DLE EOT 1 and waits for a status notification on the same service.
// A wrong pipe accepts writes silently but never answers.


// Same-pipe reply check for TSPL mode: documented '!? ' status query -> notify reply.
// UART-style data pipes answer; dead/config pipes never do.
async function rppVerifyUart(ch) {
    try {
        var pr = ch.properties || {};
        if (!(pr.notify || pr.indicate)) return false;
        if (!(pr.write || pr.writeWithoutResponse)) return false;
        var got = false;
        var handler = function () { got = true; };
        try { await rppTimeout(ch.startNotifications(), 2500); } catch (e0) { return false; }
        ch.addEventListener('characteristicvaluechanged', handler);
        try {
            var probe = new Uint8Array([0x21, 0x3F, 0x0D, 0x0A]);
            if (pr.writeWithoutResponse && typeof ch.writeValueWithoutResponse === 'function') {
                await rppTimeout(ch.writeValueWithoutResponse(probe), 2500);
            } else if (pr.write && typeof ch.writeValueWithResponse === 'function') {
                await rppTimeout(ch.writeValueWithResponse(probe), 2500);
            } else { throw new Error('WRITE_FAIL'); }
        } catch (e1) { /* keep waiting briefly in case a reply arrives */ }
        var waited = 0;
        while (!got && waited < 700) { await sleep(100); waited += 100; }
        try { ch.removeEventListener('characteristicvaluechanged', handler); } catch (e2) {}
        try { await ch.stopNotifications(); } catch (e3) {}
        return got;
    } catch (e) { return false; }
}

async function discoverWritableCharacteristic(server) {
    rppAllWritableChars = [];
    rppCurrentCharIdx = -1;
    var i, j, k, s, c, d;
    var candList = CANDIDATES.slice(0);
    for (i = 0; i < candList.length; i++) {
        if (!server.connected) throw new Error('DISCONNECTED');
        var cand = candList[i];
        try {
            var service = await server.getPrimaryService(cand.service);
            if (!service) continue;
            if (cand.write && cand.write.length) {
                for (j = 0; j < cand.write.length; j++) {
                    try {
                        var ch = await service.getCharacteristic(cand.write[j]);
                        if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
                            if (await testWriteProbe(ch)) {
                                rppAllWritableChars.push(ch);
                            }
                        }
                    } catch (e) { /* next */ }
                }
            }
        } catch (e) { /* service missing, next candidate */ }
    }
    if (!rppAllWritableChars.length && server.connected) {
        try {
            var allServices = await server.getPrimaryServices();
            for (s = 0; s < allServices.length; s++) {
                if (!server.connected) throw new Error('DISCONNECTED');
                var svc = allServices[s];
                try {
                    var sChars = await svc.getCharacteristics();
                    for (c = 0; c < sChars.length; c++) {
                        var cItem = sChars[c];
                        if (cItem && (cItem.properties.write || cItem.properties.writeWithoutResponse)) {
                            var dup = false;
                            for (d = 0; d < rppAllWritableChars.length; d++) {
                                if (rppAllWritableChars[d].uuid === cItem.uuid) { dup = true; break; }
                            }
                            if (!dup && await testWriteProbe(cItem)) {
                                rppAllWritableChars.push(cItem);
                            }
                        }
                    }
                } catch (e) { /* next */ }
            }
        } catch (e) { if (e && e.message === 'DISCONNECTED') throw e; }
    }
    var remU = null;
    try { remU = localStorage.getItem('rppChUuid') || null; } catch (eR) {}
    var uartP = [], restP = [], v;
    for (v = 0; v < rppAllWritableChars.length; v++) {
        var pp = rppAllWritableChars[v].properties || {};
        if ((pp.notify || pp.indicate) && (pp.write || pp.writeWithoutResponse)) uartP.push(rppAllWritableChars[v]);
        else restP.push(rppAllWritableChars[v]);
    }
    var ordered = uartP.concat(restP);
    if (remU) {
        ordered.sort(function (a, b) {
            var ar = (a.uuid === remU) ? 0 : 1, br = (b.uuid === remU) ? 0 : 1;
            return ar - br;
        });
    }
    var checked = 0;
    for (v = 0; v < ordered.length && checked < 4; v++) {
        var qp = ordered[v].properties || {};
        if (!((qp.notify || qp.indicate) && (qp.write || qp.writeWithoutResponse))) continue;
        checked++;
        try {
            if (await rppVerifyUart(ordered[v])) {
                try { localStorage.setItem('rppChUuid', ordered[v].uuid); } catch (eW) {}
                return ordered[v];
            }
        } catch (ve) { /* next pipe */ }
    }
    if (ordered.length) return ordered[0];
    return null;
}

function onRppDisconnected() {
    rppOk = false;
    rppChar = null;
    rppSetBusy(false);
    rppStatus('انقطع الاتصال بالطابعة — أعد المحاولة أو اضغط اتصال', 'warning');
}

async function reconnectRppGatt() {
    try {
        if (!rppDevice || !rppDevice.gatt) return false;
        var server = await rppTimeout(rppDevice.gatt.connect(), 12000);
        var ch = await discoverWritableCharacteristic(server);
        if (ch) {
            rppChar = ch;
            rppOk = true;
            return true;
        }
    } catch (e) {
        console.warn('[RPP] Reconnection failed:', e);
    }
    return false;
}

// Shows ALL nearby BLE devices so RPP320 appears even with no advertised name.
// Falls back to name filters only if Bluefy rejects the acceptAll shape.
async function rppPickDevice() {
    var reqFiltered = {
        filters: [
            { namePrefix: 'RPP' }, { namePrefix: 'RP' },
            { namePrefix: 'RONGTA' }, { namePrefix: 'PRINT' },
            { namePrefix: 'PRN' }, { namePrefix: 'MTP' },
            { namePrefix: 'MP' }, { namePrefix: 'BT' },
            { namePrefix: 'Blue' }, { namePrefix: 'Thermal' },
            { namePrefix: 'POS' }, { namePrefix: 'XP' },
            { namePrefix: 'GPRT' }, { namePrefix: 'HM' }
        ],
        optionalServices: OPTIONAL_SERVICES
    };
    try {
        return await navigator.bluetooth.requestDevice({ acceptAllDevices: true, optionalServices: OPTIONAL_SERVICES });
    } catch (errAll) {
        var mAll = (errAll && errAll.message) || String(errAll);
        if (errAll && errAll.name === 'NotFoundError') throw errAll;
        if (/cancell?ed|cansell?ed/i.test(mAll)) throw errAll;
        if (!/pars|payload|option|service|filter|argument|invalid/i.test(mAll)) throw errAll;
        return await navigator.bluetooth.requestDevice(reqFiltered);
    }
}

// Synchronously initiated from user click handler
async function rppConnectDevice() {
    if (!navigator.bluetooth) {
        rppStatus(rppError('NO_BLUETOOTH'), 'warning');
        return false;
    }

    rppSetBusy(true);
    var bluefy = isBluefy();
    rppStatus(bluefy ? 'متصفح Bluefy — اختر الطابعة RPP320 من القائمة...' : 'اختر الطابعة RPP320 من القائمة...', 'info');

    var chosenDevice;
    try {
        chosenDevice = await rppPickDevice();
    } catch (err) {
        rppSetBusy(false);
        if (err.name === 'NotFoundError' || /cancell?ed|cansell?ed/i.test(err.message || '')) {
            rppStatus('تم إلغاء اختيار الطابعة.', 'secondary');
        } else {
            rppStatus(rppError(err), 'danger');
        }
        return false;
    }

    try {
        rppDevice = chosenDevice;
        rppDevice.addEventListener('gattserverdisconnected', onRppDisconnected);
        rppStatus('تم اختيار الجهاز، جارٍ الاتصال بقناة GATT...', 'info');

        var server = await rppDevice.gatt.connect();
        var nm = ((rppDevice && rppDevice.name) || '').toLowerCase();
        if (/iphone|ipad|galaxy|samsung|huawei|xiaomi|redmi|oppo|vivo|pixel|oneplus|macbook|watch|airpods|buds|phone/.test(nm)) {
            try { rppDevice.gatt.disconnect(); } catch (eD) {}
            rppOk = false; rppChar = null; rppSetBusy(false);
            rppStatus('اخترت ' + (rppDevice.name || 'هذا الجهاز') + ' وهو هاتف وليس طابعة - افصل ثم اختر RPP320 من القائمة', 'warning');
            return false;
        }
        rppStatus('متصل GATT — جارٍ اكتشاف قناة الكتابة للطابعة...', 'info');

        var ch = await discoverWritableCharacteristic(server);
        if (!ch) {
            throw new Error('CHANNEL_FAIL');
        }

        rppChar = ch;
        rppOk = true;
        rppSetBusy(false);
        var chUuidShort = String(ch.uuid || '').slice(-8);
        rppStatus('متصل: ' + (rppDevice.name || 'RPP320') + ' [قناة ' + chUuidShort + ']', 'success');
        return true;
    } catch (err) {
        rppOk = false;
        rppChar = null;
        rppSetBusy(false);
        rppStatus(rppError(err), 'danger');
        return false;
    }
}

async function writeBytesWithChunks(bytes, onProgress, onStatus) {
    var offset = 0;
    var chunkSize = RPP.chunkSize;
    var delayMs = RPP.chunkDelayMs;
    var reconnectAttempts = 0;

    if (onProgress) onProgress(0);

    while (offset < bytes.length) {
        if (!rppDevice || !rppDevice.gatt || !rppDevice.gatt.connected || !rppChar) {
            if (reconnectAttempts < RPP.maxReconnectAttempts && rppDevice) {
                reconnectAttempts++;
                if (onStatus) onStatus('انقطع الاتصال بالطابعة، جارٍ إعادة الاتصال واستكمال الطباعة (محاولة ' + reconnectAttempts + '/' + RPP.maxReconnectAttempts + ')...', 'warning');
                await sleep(1000);
                var ok = await reconnectRppGatt();
                if (ok) {
                    if (onStatus) onStatus('تمت استعادة الاتصال بنجاح، استكمال الإرسال...', 'info');
                    continue;
                }
            }
            throw new Error('انقطع الاتصال بالطابعة أثناء الطباعة');
        }

        var chunk = bytes.slice(offset, offset + chunkSize);
        var wms = RPP.writeTimeoutMs;
        try {
            var p = rppChar.properties || {};
            var sentChunk = false;
            var lastErr = null;
            // Acknowledged write first (proven on RPP320), unacknowledged fallback
            if (p.write && typeof rppChar.writeValueWithResponse === 'function') {
                try { await rppTimeout(rppChar.writeValueWithResponse(chunk), wms); sentChunk = true; }
                catch (eWR) { lastErr = eWR; }
            }
            if (!sentChunk && p.writeWithoutResponse && typeof rppChar.writeValueWithoutResponse === 'function') {
                try { await rppTimeout(rppChar.writeValueWithoutResponse(chunk), wms); sentChunk = true; }
                catch (eWU) { lastErr = eWU; }
            }
            if (!sentChunk && typeof rppChar.writeValue === 'function') {
                await rppTimeout(rppChar.writeValue(chunk), wms); sentChunk = true;
            }
            if (!sentChunk) { throw lastErr || new Error('WRITE_FAIL'); }

            offset += chunk.length;
            reconnectAttempts = 0;
            var pct = Math.min(100, Math.floor((offset / bytes.length) * 100));
            if (onProgress) onProgress(pct);

            if (delayMs > 0 && offset < bytes.length) {
                await sleep(delayMs);
            }
        } catch (err) {
            console.warn('[RPP] Write chunk error at offset', offset, err);
            if (!rppDevice || !rppDevice.gatt || !rppDevice.gatt.connected) {
                if (reconnectAttempts < RPP.maxReconnectAttempts && rppDevice) {
                    reconnectAttempts++;
                    if (onStatus) onStatus('انقطع الاتصال، جارٍ إعادة الاتصال التلقائي...', 'warning');
                    await sleep(1000);
                    var reOk = await reconnectRppGatt();
                    if (reOk) continue;
                }
                throw new Error('انقطع الاتصال بالطابعة أثناء الطباعة');
            }

            // Downsizing
            if (chunkSize > RPP.minChunkSize) {
                chunkSize = Math.max(RPP.minChunkSize, Math.floor(chunkSize / 2));
                if (onStatus) onStatus('تعديل حجم الحزمة للملاءمة...', 'info');
                await sleep(35);
                continue;
            } else {
                throw err;
            }
        }
    }

    if (onProgress) onProgress(100);
}
var ESC = 0x1B, GS = 0x1D;
function rppRaster(mono, w) {
    var row = Math.ceil(w / 8);
    var h = Math.floor(mono.length / row);
    var BAND = 24; // 24 rows per band = ~1.7KB per GS v 0 block (safe for printer buffer)
    var parts = [];
    for (var y0 = 0; y0 < h; y0 += BAND) {
        var bh = Math.min(BAND, h - y0);
        var blk = new Uint8Array(8 + row * bh);
        blk.set([GS, 0x76, 0x30, 0x00, row & 0xFF, (row >> 8) & 0xFF, bh & 0xFF, (bh >> 8) & 0xFF], 0);
        blk.set(mono.subarray(y0 * row, (y0 + bh) * row), 8);
        parts.push(blk);
    }
    return rppConcat.apply(null, parts);
}

function rppQR(text) {
    var raw = new TextEncoder().encode(text);
    var L = raw.length + 3;
    var pL = L & 0xFF;
    var pH = (L >> 8) & 0xFF;
    return new Uint8Array([
        GS, 0x28, 0x6B, 0x04, 0x00, 0x31, 0x41, 0x32, 0x00,
        GS, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x43, 0x06,
        GS, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x45, 0x31,
        GS, 0x28, 0x6B, pL, pH, 0x31, 0x50, 0x30
    ].concat(Array.from(raw)).concat([
        GS, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x51, 0x30
    ]));
}

function rppConcat() {
    var total = 0, k;
    for (k = 0; k < arguments.length; k++) {
        if (arguments[k]) total += arguments[k].length;
    }
    var out = new Uint8Array(total), off = 0;
    for (k = 0; k < arguments.length; k++) {
        if (arguments[k]) { out.set(arguments[k], off); off += arguments[k].length; }
    }
    return out;
}

function rppDraw(inv) {
    var W = RPP.dotsWidth;
    var cv = document.getElementById('rppCanvas') || document.createElement('canvas');
    cv.width = W;

    // Estimate generous canvas height
    var estimatedH = 350 + (inv.items.length * 75) + 550;
    cv.height = estimatedH;

    var ctx = cv.getContext('2d');
    ctx.direction = 'rtl'; // ensure Arabic renders correctly
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, W, estimatedH);

    ctx.fillStyle = '#000000';
    ctx.fillRect(10, 6, W - 20, 4); // Top solid black bar

    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';

    var y = 20;

    // Header
    ctx.font = 'bold 40px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText(inv.store || 'شركة النخبة للتنظيف', W / 2, y);
    y += 48;

    ctx.font = '22px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('نظام إدارة الغسيل والتنظيف الجاف', W / 2, y);
    y += 34;

    ctx.font = 'bold 28px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('فاتورة ضريبية مبسطة / Invoice', W / 2, y);
    y += 40;

    ctx.font = 'bold 26px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('# ' + (inv.order || ''), W / 2, y);
    y += 36;

    ctx.font = '22px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('تاريخ الإنشاء: ' + (inv.date || ''), W / 2, y);
    y += 32;

    if (inv.deliver) {
        ctx.fillText('موعد التسليم: ' + inv.deliver, W / 2, y);
        y += 32;
    }

    // Divider
    y += 10;
    ctx.fillRect(20, y, W - 40, 2);
    y += 16;

    // Customer Info Section
    ctx.font = 'bold 25px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('بيانات العميل', W / 2, y);
    y += 34;

    ctx.textAlign = 'right';
    ctx.font = '23px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('الاسم: ' + (inv.customer || '—'), W - 25, y);
    y += 32;

    ctx.fillText('الهاتف: ' + (inv.phone || '—'), W - 25, y);
    y += 32;

    if (inv.address) {
        ctx.fillText('العنوان: ' + inv.address, W - 25, y);
        y += 32;
    }

    // Status
    if (inv.status) {
        ctx.fillText('حالة الطلب: ' + inv.status, W - 25, y);
        y += 32;
    }
    if (inv.pay_status_text || inv.pay_status) {
        ctx.fillText('حالة الدفع: ' + (inv.pay_status_text || inv.pay_status), W - 25, y);
        y += 32;
    }
    if (inv.pay_method) {
        ctx.fillText('طريقة الدفع: ' + inv.pay_method, W - 25, y);
        y += 32;
    }

    // Divider
    y += 10;
    ctx.fillRect(20, y, W - 40, 2);
    y += 16;

    // Items Header
    ctx.textAlign = 'center';
    ctx.font = 'bold 26px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('تفاصيل الخدمات والقطع', W / 2, y);
    y += 36;

    // Table Header
    ctx.font = 'bold 21px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText('الصنف / الخدمة', W - 25, y);
    ctx.textAlign = 'left';
    ctx.fillText('المجموع', 25, y);
    y += 28;
    ctx.fillRect(25, y, W - 50, 1);
    y += 12;

    // Items List
    inv.items.forEach(function (it) {
        ctx.textAlign = 'right';
        ctx.font = 'bold 24px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText(it.name, W - 25, y);
        y += 30;

        ctx.font = '21px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText(it.qty + ' × ' + Number(it.price).toFixed(2) + ' ₪', W - 25, y);

        ctx.textAlign = 'left';
        ctx.font = 'bold 22px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText(Number(it.total).toFixed(2) + ' ₪', 25, y);
        y += 32;
    });

    // Divider
    y += 10;
    ctx.fillRect(20, y, W - 40, 2);
    y += 20;

    // Totals Section
    ctx.textAlign = 'center';
    ctx.font = 'bold 34px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('المجموع الإجمالي: ' + Number(inv.total).toFixed(2) + ' ₪', W / 2, y);
    y += 44;

    if (parseFloat(inv.discount) > 0) {
        ctx.font = 'bold 25px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText('الخصم: - ' + Number(inv.discount).toFixed(2) + ' ₪', W / 2, y);
        y += 36;
    }

    if (parseFloat(inv.paid) > 0) {
        ctx.font = 'bold 25px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText('المدفوع: ' + Number(inv.paid).toFixed(2) + ' ₪', W / 2, y);
        y += 36;
    }

    if (parseFloat(inv.remaining) > 0) {
        ctx.font = 'bold 28px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText('المتبقي (دين): ' + Number(inv.remaining).toFixed(2) + ' ₪', W / 2, y);
        y += 40;
    }

    // Notes & Creator
    y += 10;
    if (inv.notes) {
        ctx.font = '21px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText('ملاحظات: ' + inv.notes, W / 2, y);
        y += 32;
    }

    if (inv.created_by) {
        ctx.font = '20px Tajawal, Cairo, Tahoma, Arial, sans-serif';
        ctx.fillText('أنشأ الطلب: ' + inv.created_by, W / 2, y);
        y += 32;
    }

    // Footer Greeting
    y += 12;
    ctx.font = 'bold 23px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('شكراً لاختياركم شركة النخبة للتنظيف ❤️', W / 2, y);
    y += 32;

    ctx.font = '19px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('للاستفسار يرجى الاتصال بنا', W / 2, y);
    y += 24;

    return { cv: cv, height: y };
}

function rppMono(cv, height) {
    var ctx = cv.getContext('2d');
    var d = ctx.getImageData(0, 0, cv.width, height).data;
    var row = Math.ceil(cv.width / 8);
    var mono = new Uint8Array(row * height);
    for (var yy = 0; yy < height; yy++) {
        for (var xx = 0; xx < cv.width; xx++) {
            var i = (yy * cv.width + xx) * 4;
            var alpha = d[i + 3];
            var lum = 0.299 * d[i] + 0.587 * d[i + 1] + 0.114 * d[i + 2];
            if (alpha > 128 && lum < 128) {
                mono[yy * row + (xx >> 3)] |= 0x80 >> (xx & 7);
            }
        }
    }
    return mono;
}

function rppRememberUuid(u) { try { if (u) localStorage.setItem('rppChUuid', u); } catch (eW) {} }


// ===== TSPL job builders (RPP320 runs in TSPL mode - never send ESC/POS) =====
// Ref: TSC TSPL/TSPL2 + Rongta Label Printer Programming Manual (TSPL).
// 203dpi => 1mm = 8 dots. Commands are ASCII terminated with CRLF.
// BITMAP data: 1 = black dot, MSB first (matches rppMono output).
function tsplLine(s) {
    return new TextEncoder().encode(s + '\r\n');
}
function tsplConcatBytes(parts) {
    var total = 0, k;
    for (k = 0; k < parts.length; k++) total += parts[k].length;
    var out = new Uint8Array(total), off = 0;
    for (k = 0; k < parts.length; k++) { out.set(parts[k], off); off += parts[k].length; }
    return out;
}
function tsplBitmapCmds(mono, w, bandRows) {
    var row = Math.ceil(w / 8);
    var h = Math.floor(mono.length / row);
    var BAND = bandRows || 200;
    var cmds = [];
    for (var y0 = 0; y0 < h; y0 += BAND) {
        var bh = Math.min(BAND, h - y0);
        var head = new TextEncoder().encode('BITMAP 0,' + y0 + ',' + row + ',' + bh + ',0,');
        var blk = new Uint8Array(head.length + row * bh + 2);
        blk.set(head, 0);
        blk.set(mono.subarray(y0 * row, (y0 + bh) * row), head.length);
        blk[head.length + row * bh] = 0x0D;
        blk[head.length + row * bh + 1] = 0x0A;
        cmds.push(blk);
    }
    return { cmds: cmds, heightDots: h };
}
function tsplInvoiceJob(mono, w, qrUrl) {
    var row = Math.ceil(w / 8);
    var h = Math.floor(mono.length / row);
    var qrY = h + 10;
    var qrX = Math.max(0, Math.floor((w - 160) / 2));
    var totalDots = qrY + 170;
    var hMm = Math.max(30, Math.ceil(totalDots / 8));
    var parts = [];
    parts.push(tsplLine('SIZE 80 mm,' + hMm + ' mm'));
    parts.push(tsplLine('GAP 0,0'));
    parts.push(tsplLine('CLS'));
    var bands = tsplBitmapCmds(mono, w, 200);
    for (var b = 0; b < bands.cmds.length; b++) parts.push(bands.cmds[b]);
    parts.push(tsplLine('QRCODE ' + qrX + ',' + qrY + ',H,4,A,0,"' + qrUrl + '"'));
    parts.push(tsplLine('PRINT 1'));
    parts.push(tsplLine('FEED 80'));
    return { job: tsplConcatBytes(parts), heightDots: h, heightMm: hMm };
}
function tsplBarTest() {
    // Minimal isolated TSPL test: one black bar, no fonts, no images.
    var parts = [];
    parts.push(tsplLine('SIZE 80 mm,30 mm'));
    parts.push(tsplLine('GAP 0,0'));
    parts.push(tsplLine('CLS'));
    parts.push(tsplLine('BAR 100,80,376,80'));
    parts.push(tsplLine('PRINT 1'));
    parts.push(tsplLine('FEED 80'));
    return tsplConcatBytes(parts);
}
async function rppTsplTest() {
    if (rppBusy) return;
    if (!navigator.bluetooth) { rppStatus(rppError('NO_BLUETOOTH'), 'warning'); return; }
    if (!rppOk || !rppDevice || !rppDevice.gatt || !rppDevice.gatt.connected || !rppChar) {
        var ok = await rppConnectDevice();
        if (!ok) return;
    }
    rppSetBusy(true);
    rppStatus('يرسل اختبار TSPL (شريط اسود)...', 'info');
    try {
        var job = tsplBarTest();
        await writeBytesWithChunks(job, null, function (msg, type) { rppStatus(msg, type); });
        rppStatus('اكتمل الارسال - هل ظهر شريط اسود على الورق؟', 'success');
    } catch (err) {
        rppStatus('خطا اثناء الاختبار: ' + rppError(err), 'danger');
    } finally {
        rppSetBusy(false);
    }
}

async function rppPrint() {
    if (rppBusy) return;
    try { if (rppChar && rppChar.uuid) rppRememberUuid(rppChar.uuid); } catch (eM) {}
    rppSetBusy(true);
    rppStatus('جارٍ تجهيز الفاتورة...', 'info');

    try {
        var inv = window.RPP_INVOICE;
        if (!inv || !inv.items || !inv.items.length) {
            throw new Error('لا توجد بنود في الفاتورة للطباعة');
        }

        // Wait for Arabic fonts to load before drawing to canvas
        if (document.fonts && document.fonts.ready) {
            await document.fonts.ready;
        }

        var drawn = rppDraw(inv);
        var mono = rppMono(drawn.cv, drawn.height);

        var inkBytes = 0;
        for (var mi = 0; mi < mono.length; mi++) {
            if (mono[mi] !== 0) inkBytes++;
        }

        var qrUrl = window.location.origin + '/orders/' + inv.order;
        if (!inkBytes) throw new Error('EMPTY_CANVAS');
        var tspl = tsplInvoiceJob(mono, drawn.cv.width, qrUrl);
        var job = tspl.job;

        rppStatus('جارٍ إرسال الفاتورة بلغة TSPL إلى الطابعة (' + inkBytes + ' بايت حبر)...', 'info');
        await writeBytesWithChunks(
            job,
            null,
            function (msg, type) { rppStatus(msg, type); }
        );

        rppStatus('✅ اكتمل إرسال الفاتورة (تأكيد وصول البيانات فقط - تحقق من الورقة) (' + inkBytes + ' بايت حبر)', 'success');
    } catch (err) {
        rppStatus('خطأ أثناء الطباعة: ' + rppError(err), 'danger');
    } finally {
        rppSetBusy(false);
    }
}

// 1-Click: requestDevice called directly from user click if not connected
async function rppPrintOneClick() {
    if (rppBusy) return;
    if (!navigator.bluetooth) {
        rppStatus(rppError('NO_BLUETOOTH'), 'warning');
        return;
    }

    if (!rppOk || !rppDevice || !rppDevice.gatt || !rppDevice.gatt.connected || !rppChar) {
        var ok = await rppConnectDevice();
        if (!ok) return; // Cancelled or error, status already shown, no fallback print!
    }

    await rppPrint();
}

// Global exports
window.rppIsOk = function () { return rppOk; };
window.rppDoPrint = function () { rppPrint(); };
window.rppPrintOneClick = rppPrintOneClick;
window.rppConnectCb = function (cb) {
    rppConnectDevice().then(function (ok) {
        if (cb) cb(ok);
    });
};

var tsplTestBtn = document.getElementById('btnTsplTest');
if (tsplTestBtn) tsplTestBtn.addEventListener('click', rppTsplTest);
var shareBtn = document.getElementById('btnRppShare');
if (shareBtn) {
    shareBtn.addEventListener('click', function () {
        try {
            var inv = window.RPP_INVOICE;
            if (!inv || !inv.items || !inv.items.length) {
                rppStatus('لا توجد بنود في الفاتورة للمشاركة', 'warning');
                return;
            }
            rppStatus('جارٍ تجهيز صورة الفاتورة...', 'info');
            var drawn = rppDraw(inv);
            var cv = document.createElement('canvas');
            cv.width = drawn.cv.width;
            cv.height = drawn.height;
            cv.getContext('2d').drawImage(drawn.cv, 0, 0);
            var dataUrl = cv.toDataURL('image/png');
            var fileName = 'invoice-' + inv.order + '.png';

            var a = document.createElement('a');
            a.href = dataUrl;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { a.remove(); }, 2000);

            if (navigator.canShare) {
                try {
                    var bin = atob(dataUrl.split(',')[1]);
                    var arr = new Uint8Array(bin.length);
                    for (var i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
                    var file = new File([arr], fileName, { type: 'image/png' });
                    if (navigator.canShare({ files: [file] })) {
                        navigator.share({
                            files: [file],
                            title: 'فاتورة طلب ' + inv.order,
                            text: 'فاتورة شركة النخبة للتنظيف — طلب #' + inv.order
                        }).then(function () {
                            rppStatus('تمت مشاركة الفاتورة بنجاح', 'success');
                        }, function (e) {
                            if (e && e.name === 'AbortError') {
                                rppStatus('تم إلغاء المشاركة', 'secondary');
                            } else {
                                rppStatus('تم تنزيل الفاتورة — يمكنك مشاركتها عبر تطبيق Toprint أو واتساب', 'info');
                            }
                        });
                        return;
                    }
                } catch (err) { /* fall through */ }
            }
            rppStatus('تم تنزيل الفاتورة — يمكنك فتحها في تطبيق Toprint والطباعة من الصور', 'info');
        } catch (err) {
            rppStatus('تعذر تجهيز صورة الفاتورة: ' + (err.message || err), 'danger');
        }
    });
}

})();
</script>


