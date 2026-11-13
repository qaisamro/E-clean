<x-app-layout :title="config('app.name') . ' | ملصقات طلب ' . $order->order_code">

    @php
        // الكمية × ملصق لكل قطعة — كل سطر في الطلب يتكرر بحسب كميته
        $btStickers = $order->orderDetails->flatMap(function ($detail) use ($order) {
            $qty = max(1, (int) ($detail->quantity ?? 1));
            $item = trim(($detail->item?->name ?? '') . ($detail->service?->name ? ' — ' . $detail->service->name : ''));
            return collect(range(1, $qty))->map(fn () => [
                'customer' => $order->customer?->name ?? '',
                'order' => (string) $order->order_code,
                'item' => $item,
            ]);
        })->values();
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 mb-4 no-print">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">
                <a href="{{ route('orders.show', $order) }}">طلب {{ $order->order_code }}</a> /
            </span>
            ملصقات القطع
        </h4>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <button id="btnPrintAllBT" class="btn btn-primary">
                <i class='bx bx-bluetooth me-1'></i> طباعة الكل ({{ $btStickers->count() }})
            </button>
            <button id="btnSaveAll" class="btn btn-outline-secondary">
                <i class='bx bx-download me-1'></i> حفظ كل الصور
            </button>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class='bx bx-printer me-1'></i> طباعة ورقية
            </button>
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i> رجوع للطلب
            </a>
        </div>
    </div>

    {{-- بلوتوث: الاتصال والطباعة والتشخيص --}}
    <div id="bt-stickers-mount" class="no-print mb-3"></div>
    @if ($btStickers->isEmpty())
        <div class="alert alert-warning no-print">لا توجد قطع في هذا الطلب.</div>
    @endif

    <div class="stickers-sheet">
        @foreach ($btStickers as $index => $sticker)
            <div class="sticker-wrap">
                <div class="sticker">
                    <div class="sticker-customer">{{ $sticker['customer'] ?: '—' }}</div>
                    <div class="sticker-order"># {{ $sticker['order'] }}</div>
                    <div class="sticker-item">{{ $sticker['item'] }}</div>
                    @if ($btStickers->count() > 1)
                        <div class="sticker-piece">قطعة {{ $index + 1 }} من {{ $btStickers->count() }}</div>
                    @endif
                </div>
                <div class="d-flex gap-1 justify-content-center no-print mt-1">
                    <button class="btn btn-sm btn-outline-primary btn-print-single" data-index="{{ $index }}">
                        <i class='bx bx-bluetooth me-1'></i> طباعة
                    </button>
                    <button class="btn btn-sm btn-outline-info btn-share" data-index="{{ $index }}">
                        <i class='bx bx-share-alt me-1'></i> حفظ / مشاركة
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .stickers-sheet {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .sticker-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .sticker {
            width: 72mm;
            min-height: 34mm;
            border: 1.5px dashed #333;
            border-radius: 4px;
            padding: 3mm 4mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1mm;
            background: #fff;
            page-break-inside: avoid;
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif !important;
        }
        .sticker-customer {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
        }
        .sticker-order {
            font-size: 16px;
            font-weight: 700;
        }
        .sticker-item {
            font-size: 13px;
            color: #333;
        }
        .sticker-piece {
            font-size: 10px;
            color: #888;
            margin-top: .5mm;
        }
        @media print {
            @page {
                size: auto;
                margin: 4mm;
            }
            body * {
                visibility: hidden !important;
            }
            .stickers-sheet, .stickers-sheet * {
                visibility: visible !important;
            }
            .stickers-sheet {
                position: absolute;
                inset: 0;
                display: block;
            }
            .sticker {
                margin-bottom: 4mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <script id="bt-stickers-data" type="application/json">@json($btStickers)</script>

    @push('js')
        <script src="{{ asset('web/js/eclean-bt-core.js') }}?v=bt3"></script>
        <script src="{{ asset('web/js/eclean-bt-stickers.js') }}?v=bt3"></script>
        <script src="{{ asset('web/js/eclean-bt-ui.js') }}?v=bt3"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                const dataEl = document.getElementById('bt-stickers-data');
                const items = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

                const conn = EcleanBT.connection.create();
                const profileFromPref = function () { return (conn.loadPref('label') || {}).profile || 'generic-escpos'; };
                let printing = false;
                const progress = function (pct) {
                    const btn = document.getElementById('btnPrintAllBT');
                    if (btn) btn.dataset.progress = pct;
                };

                async function printSubset(subset, startIndex) {
                    if (printing) return;
                    printing = true;
                    const btn = document.getElementById('btnPrintAllBT');
                    const oldLabel = btn ? btn.innerHTML : null;
                    try {
                        await EcleanBT.connection.ensureInteractive(conn, 'label');
                        await EcleanBTStickers.printStickers(subset, {
                            conn: conn,
                            profile: profileFromPref(),
                            widthPx: 384,
                            heightPx: 220,
                            onProgress: progress,
                            onStatus: function (msg, type) { console.log('[BT]', type || 'info', msg); },
                        });
                    } catch (err) {
                        console.error('[BT] print failed', err);
                    } finally {
                        printing = false;
                        if (btn && oldLabel) btn.innerHTML = oldLabel;
                    }
                }

                const mount = document.getElementById('bt-stickers-mount');
                EcleanBTUI.mount(mount, {
                    role: 'label',
                    connection: conn,
                    onTestPrint: async function (c) {
                        if (!items.length) return;
                        await EcleanBTStickers.printStickers([items[0]], {
                            conn: c,
                            profile: profileFromPref(),
                            widthPx: 384,
                            heightPx: 220,
                        });
                    },
                    onDiagnostic: async function (c, log) {
                        const canvas = document.createElement('canvas');
                        canvas.width = 384; canvas.height = 220;
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, 384, 220);
                        ctx.fillStyle = '#000';
                        ctx.font = 'bold 26px Cairo, Tahoma, Arial';
                        ctx.textAlign = 'center';
                        ctx.fillText('تشخيص الطابعة', 192, 60);
                        ctx.font = '18px Cairo, Tahoma, Arial';
                        ctx.fillText('RPP/Label · 203dpi · 384px', 192, 110);
                        ctx.fillText(new Date().toLocaleString('ar'), 192, 145);
                        const monoData = EcleanBT.raster.canvasToMono(canvas);
                        const job = EcleanBT.raster.buildJob(monoData, 384, 220, { profile: profileFromPref() });
                        await EcleanBT.connection.ensureInteractive(c, 'label');
                        await EcleanBT.jobs.writeChunked(c, job, { profile: profileFromPref() });
                        if (log) log('تم إرسال صفحة التشخيص');
                    },
                });

                const allBtn = document.getElementById('btnPrintAllBT');
                if (allBtn) allBtn.addEventListener('click', function () { printSubset(items, 0); });

                document.querySelectorAll('.btn-print-single').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(btn.dataset.index || '0', 10);
                        printSubset([items[idx]], idx);
                    });
                });

                /* حفظ الملصق كصورة */
                document.querySelectorAll('.btn-share').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(btn.dataset.index || '0', 10);
                        const canvas = document.createElement('canvas');
                        canvas.width = 384; canvas.height = 220;
                        EcleanBTStickers.drawSticker(canvas, items[idx], { widthPx: 384, heightPx: 220 });
                        canvas.toBlob(function (blob) {
                            if (!blob) return;
                            const a = document.createElement('a');
                            a.href = URL.createObjectURL(blob);
                            a.download = 'sticker-' + items[idx].order + '-' + (idx + 1) + '.png';
                            a.click();
                            URL.revokeObjectURL(a.href);
                        });
                    });
                });

                /* حفظ كل الصور */
                const saveAll = document.getElementById('btnSaveAll');
                if (saveAll) saveAll.addEventListener('click', function () {
                    items.forEach(function (item, idx) {
                        const canvas = document.createElement('canvas');
                        canvas.width = 384; canvas.height = 220;
                        EcleanBTStickers.drawSticker(canvas, item, { widthPx: 384, heightPx: 220 });
                        canvas.toBlob(function (blob) {
                            if (!blob) return;
                            const a = document.createElement('a');
                            a.href = URL.createObjectURL(blob);
                            a.download = 'sticker-' + item.order + '-' + (idx + 1) + '.png';
                            a.click();
                            URL.revokeObjectURL(a.href);
                        });
                    });
                });
            });
        </script>
    @endpush

</x-app-layout>
