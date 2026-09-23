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
            <button onclick="printThermal()" class="btn btn-outline-dark">
                <i class='bx bx-receipt me-1'></i> طباعة حرارية 80mm
            </button>
            <button onclick="downloadPDF()" class="btn btn-outline-success">
                <i class='bx bxs-file-pdf me-1'></i> تحميل PDF
            </button>
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

<script>
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
function printThermal() {
    document.body.classList.add('thermal-print');
    setTimeout(function() {
        window.print();
        setTimeout(function() { document.body.classList.remove('thermal-print'); }, 500);
    }, 50);
}
</script>


