<x-app-layout :title="config('app.name') . ' | تفاصيل طلب ' . $order->order_code">

    @php
    // Invoice payload embedded once for Bluetooth printing (see eclean-bt-invoice.js)
    $btPayload = \App\Support\PosInvoiceData::make($order);
    $btPayload['copy'] = 'normal';
@endphp

{{-- =========== Breadcrumb & Actions (مخفية عند الطباعة) =========== --}}
    <div id="hide-on-print" class="d-flex justify-content-between align-items-center flex-column flex-md-row mb-4 no-print">
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
            <button class="btn btn-primary" data-bt-invoice="normal" data-copy="normal">
                <i class='bx bx-bluetooth me-1'></i> طباعة
            </button>
            <button class="btn btn-outline-success" data-bt-invoice="customer" data-copy="customer">
                <i class='bx bx-bluetooth me-1'></i> نسخة الزبون
            </button>
            <button class="btn btn-outline-success" data-bt-invoice="store" data-copy="store">
                <i class='bx bx-bluetooth me-1'></i> نسخة المحل
            </button>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('orders.invoice', $order) }}" target="_blank" rel="noopener" title="طباعة ورقية / حفظ PDF عبر المتصفح">
                <i class='bx bx-file-blank me-1'></i> PDF
            </a>
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

    {{-- بلوتوث: طابعة الفواتير --}}
    <div id="bt-invoice-mount" class="no-print mb-3"></div>

    {{-- =========== Invoice (POS Template) =========== --}}
    <div class="card shadow-sm invoice-frame-card" id="invoice-card">
        <iframe id="invoice-frame"
            src="{{ route('orders.invoice', ['order' => $order, 'preview' => 1]) }}"
            title="الفاتورة"
            style="width:100%; height:1100px; border:0; display:block; background:transparent;"></iframe>
    </div>

    {{-- Payment History (لا يظهر على الفاتورة) --}}
    @if($order->payments && $order->payments->count() > 0)
        <div class="mt-4 no-print">
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

    {{-- Staff Info (شاشة فقط) --}}
    @if($order->user_id)
        <div class="border-top pt-3 pb-2 text-muted small no-print">
            <i class='bx bx-user me-1'></i>
            أنشأ الطلب: <strong>{{ $order->user->name }}</strong>
            في {{ $order->created_at->format('Y/m/d H:i') }}
        </div>
    @endif


    @push('js')
        <script id="bt-pos-payload" type="application/json">@json($btPayload)</script>
        <script src="{{ asset('web/js/eclean-bt-core.js') }}?v=bt3"></script>
        <script src="{{ asset('web/js/eclean-bt-invoice.js') }}?v=bt3"></script>
        <script src="{{ asset('web/js/eclean-bt-ui.js') }}?v=bt3"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                const payload = JSON.parse(document.getElementById('bt-pos-payload').textContent || '{}');
                const conn = EcleanBT.connection.create();
                const profileFromPref = function () { return (conn.loadPref('invoice') || {}).profile || 'rpp320-tspl'; };
                let printing = false;

                EcleanBTUI.mount(document.getElementById('bt-invoice-mount'), {
                    role: 'invoice',
                    connection: conn,
                    onTestPrint: async function (c) {
                        if (!payload.order) return;
                        await EcleanBTInvoice.printInvoice(payload, {
                            conn: c,
                            profile: profileFromPref(),
                            copy: 'normal',
                        });
                    },
                });

                async function printWithCopy(copy) {
                    if (printing) return;
                    printing = true;
                    const btn = document.querySelector('[data-bt-invoice="' + copy + '"]');
                    const old = btn ? btn.innerHTML : null;
                    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-circle bx-spin me-1"></i> جارٍ الطباعة'; }
                    try {
                        await EcleanBT.connection.ensureInteractive(conn, 'invoice');
                        await EcleanBTInvoice.printInvoice(payload, {
                            conn: conn,
                            profile: profileFromPref(),
                            copy: copy,
                        });
                    } catch (err) {
                        console.error('[BT] invoice print failed', err);
                    } finally {
                        printing = false;
                        if (btn) { btn.disabled = false; btn.innerHTML = old; }
                    }
                }

                document.querySelectorAll('[data-bt-invoice]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        printWithCopy(btn.dataset.copy || 'normal');
                    });
                });
            });
        </script>
    @endpush

</x-app-layout>
