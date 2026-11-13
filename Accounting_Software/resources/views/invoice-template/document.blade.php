<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>معاينة فاتورة نقطة البيع</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}?v=20261112">
    <link rel="stylesheet" href="{{ asset('web/css/pos-invoice.css') }}">
</head>
<body class="invoice-document">
    @if (!$payload['preview'])
        <nav class="invoice-toolbar" aria-label="إجراءات الطباعة">
            <button type="button" id="print-invoice">طباعة / حفظ PDF</button>
            <button type="button" id="print-bt" class="ecleanbt-toolbar-btn" style="border:0;border-radius:8px;padding:10px 18px;color:#fff;background:#0f766e;cursor:pointer;font:inherit;display:inline-flex;align-items:center;gap:6px">طباعة عبر بلوتوث</button>
            <span>اختر مقاس الورق المطابق، ومقياس 100%، وأوقف رؤوس وتذييلات المتصفح.</span>
        </nav>
        <div id="bt-mount" style="padding:0 16px 8px"></div>
    @endif
    <main id="invoice-root" aria-label="الفاتورة"></main>
    <noscript>يرجى تفعيل JavaScript لعرض قالب الفاتورة والطباعة.</noscript>
    <script>window.posInvoicePayload = {{ Illuminate\Support\Js::from($payload) }};</script>
    <script src="{{ asset('web/js/pos-invoice.js') }}"></script>

    <script src="{{ asset('web/js/eclean-bt-core.js') }}?v=bt3"></script>
    <script src="{{ asset('web/js/eclean-bt-invoice.js') }}?v=bt3"></script>
    <script src="{{ asset('web/js/eclean-bt-ui.js') }}?v=bt3"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            var p = window.posInvoicePayload || {};
            var btPayload = Object.assign({}, p.data || {}, { copy: p.copy || 'normal' });
            var conn = EcleanBT.connection.create();
            var profileFromPref = function () { return (conn.loadPref('invoice') || {}).profile || 'rpp320-tspl'; };
            var printing = false;

            EcleanBTUI.mount(document.getElementById('bt-mount'), {
                role: 'invoice',
                connection: conn,
                onTestPrint: async function (c) {
                    if (!btPayload.order) return;
                    await EcleanBTInvoice.printInvoice(btPayload, {
                        conn: c,
                        profile: profileFromPref(),
                        copy: p.copy || 'normal',
                    });
                },
            });

            var printBt = document.getElementById('print-bt');
            if (printBt) {
                printBt.addEventListener('click', async function () {
                    if (printing) return;
                    printing = true;
                    var previous = printBt.innerHTML;
                    printBt.disabled = true;
                    printBt.innerHTML = '<i class="bx bx-loader-circle bx-spin" aria-hidden="true"></i> جارٍ الطباعة';
                    try {
                        await EcleanBT.connection.ensureInteractive(conn, 'invoice');
                        await EcleanBTInvoice.printInvoice(btPayload, {
                            conn: conn,
                            profile: profileFromPref(),
                            copy: p.copy || 'normal',
                        });
                    } catch (err) {
                        console.error('[BT] invoice print failed', err);
                    } finally {
                        printing = false;
                        printBt.disabled = false;
                        printBt.innerHTML = previous;
                    }
                });
            }
        });
    </script>
</body>
</html>
