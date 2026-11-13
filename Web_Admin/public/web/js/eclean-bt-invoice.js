/* ==========================================================================
   E-Clean — Invoice canvas renderer + Bluetooth send for thermal receipts.
   Shared between the two Laravel apps; depends on eclean-bt-core.js.
   Payload shape (see PosInvoiceData::make):
     { vendor: {name, phone, address, email, logo},
       currency: '₪',
       order: { id, code, invoice_number, date, time, customer_name,
                customer_phone, cashier, subtotal, discount, delivery, tax,
                total, payment_method, payment_status, order_notes,
                items: [{name, service, quantity, unit_price, discount, total}],
                extras: [string] } | null,
       copy: 'normal' | 'customer' | 'store' }
   ========================================================================== */
(function (root, factory) {
    'use strict';
    const api = factory(typeof root.EcleanBT !== 'undefined'
        ? root.EcleanBT
        : (typeof require === 'function' ? require('./eclean-bt-core.js') : undefined));
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
    root.EcleanBTInvoice = api;
})(typeof window !== 'undefined' ? window : globalThis, function (EcleanBT) {
    'use strict';

    /* ---------- tiny text utils (testable with a stub ctx) ---------- */
    function createMeasureCtx(fontPx, fontFamily) {
        return { font: 'bold ' + fontPx + 'px ' + fontFamily, measureText: function (s) { return { width: String(s).length * fontPx * 0.62 }; } };
    }
    function wrapText(measureCtx, text, maxWidth, fontFamily, fontPx, fontWeight) {
        const ctx = measureCtx;
        ctx.font = (fontWeight || '') + ' ' + fontPx + 'px ' + fontFamily;
        const words = String(text).split(/\s+/).filter(Boolean);
        const lines = [];
        let line = '';
        for (let i = 0; i < words.length; i++) {
            const candidate = line ? line + ' ' + words[i] : words[i];
            if (ctx.measureText(candidate).width <= maxWidth || !line) {
                line = candidate;
            } else {
                lines.push(line);
                line = words[i];
            }
        }
        if (line) lines.push(line);
        return lines;
    }

    function copyLabel(copy, lang) {
        const l = lang || 'ar';
        const labels = {
            ar: { customer: 'نسخة الزبون', store: 'نسخة المحل', normal: '' },
            en: { customer: 'Customer copy', store: 'Store copy', normal: '' },
        }[l] || {};
        return labels[copy] || '';
    }

    function formatMoney(value, currency) {
        if (value === null || value === undefined || value === '' || !isFinite(Number(value))) {
            return '—';
        }
        return Number(value).toFixed(2) + (currency ? ' ' + currency : '');
    }

    function barcodeBits(id) {
        const symbols = [20957, 29783, 23639, 30485, 20951, 29813, 23669, 20855, 29789, 23645];
        if (!/^\d+$/.test(String(id))) return null;
        const stop = (35770).toString(2);
        return stop + Array.from(String(id), function (digit) {
            return symbols[Number(digit)].toString(2) + '0';
        }).join('') + stop;
    }
    function drawBarcodeBits(ctx, bits, x, y, barWidth, barHeight, color) {
        ctx.fillStyle = color || '#000000';
        let cx = x;
        for (let i = 0; i < bits.length; i++) {
            if (bits[i] === '1') {
                ctx.fillRect(cx, y, barWidth, barHeight);
            }
            cx += barWidth;
        }
        return cx - x;
    }

    /* ---------- layout & draw ---------- */
    function drawInvoice(canvas, payload, opts) {
        const o = opts || {};
        const width = o.widthPx || 576;
        const fontFamily = o.fontFamily || '"Cairo", Tahoma, Arial, sans-serif';
        const rtl = o.rtl !== false;
        const data = payload || {};
        const vendor = data.vendor || {};
        const order = data.order || null;
        const copy = o.copy || data.copy || 'normal';
        const showCopyRibbon = copy === 'customer' || copy === 'store';
        const measureCtx = createMeasureCtx(20, fontFamily);

        /* helper for two measure contexts (bold vs regular) */
        const bCtx = createMeasureCtx(20, fontFamily);
        const rCtx = createMeasureCtx(20, fontFamily);

        let y = 18;
        const padX = 18;
        const maxText = width - padX * 2;
        const items = (order && Array.isArray(order.items)) ? order.items : [];
        const lineH = 24;

        // estimate height first
        const vendorName = vendor.name ? String(vendor.name) : '';
        const infoLines = [];
        if (order) {
            infoLines.push(String(order.invoice_number || order.code || ''));
            infoLines.push(String((order.date || '') + '  ' + (order.time || '')));
        }

        const itemLayout = items.map(function (it) {
            const nameLines = wrapText(measureCtx, String(it.name || '—'), 210, fontFamily, 20, 'bold');
            const serviceLines = it.service ? wrapText(rCtx, String(it.service), 210, fontFamily, 15, '') : [];
            const rows = Math.max(nameLines.length, serviceLines.length, 1);
            return { it: it, nameLines: nameLines, serviceLines: serviceLines, rows: rows };
        });

        let estimated = 20; // top pad
        estimated += 46;    // vendor name
        estimated += vendor.phone || vendor.address ? 30 : 0;
        estimated += 10;    // gap
        estimated += 2;     // divider
        estimated += infoLines.length * 24 + 10;
        if (order && order.customer_name) estimated += 24;
        if (order && order.customer_phone) estimated += 24;
        if (order && order.cashier) estimated += 24;
        estimated += 10;    // gap
        estimated += 2;     // divider
        estimated += 30;    // table header
        estimated += itemLayout.reduce(function (acc, row) { return acc + Math.max(1, row.rows) * lineH + 6; }, 0);
        estimated += 12;    // divider
        estimated += 24 * ((order && order.discount ? 1 : 0) + ((order && order.delivery !== null && order.delivery !== undefined && order.delivery !== '') ? 1 : 0) + ((order && order.tax !== null && order.tax !== undefined && order.tax !== '') ? 1 : 0) + 1); // subtotal(+discount)(+delivery)(+tax)
        estimated += 56;    // total big
        estimated += 26;    // payment method
        estimated += 26;    // payment status
        if (order && order.extras && order.extras.length) estimated += 28;
        const noteLines = wrapText(rCtx, String((order && order.order_notes) || ''), maxText, fontFamily, 16, '');
        estimated += noteLines.length * 22 + (noteLines.length ? 10 : 0);
        estimated += showCopyRibbon ? 54 : 0;
        estimated += 120;   // barcode
        estimated += 26;    // caption
        estimated += 18;    // bottom pad

        canvas.width = width;
        canvas.height = Math.max(200, Math.ceil(estimated));
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.textBaseline = 'middle';
        ctx.fillStyle = '#111111';

        function divider(yy) {
            ctx.strokeStyle = '#000000';
            ctx.lineWidth = 2;
            ctx.setLineDash([10, 8]);
            ctx.beginPath();
            ctx.moveTo(padX, yy);
            ctx.lineTo(width - padX, yy);
            ctx.stroke();
            ctx.setLineDash([]);
        }

        /* vendor */
        ctx.textAlign = 'center';
        ctx.direction = rtl ? 'rtl' : 'ltr';
        ctx.font = 'bold 32px ' + fontFamily;
        ctx.fillText(vendorName || 'المتجر', width / 2, y + 22);
        y += 46;
        if (vendor.address || vendor.phone) {
            ctx.font = '19px ' + fontFamily;
            ctx.fillStyle = '#333333';
            const sub = [vendor.address, vendor.phone].filter(Boolean).join('  ·  ');
            ctx.fillText(sub, width / 2, y + 11);
            y += 30;
        }
        ctx.fillStyle = '#111111';
        y += 10;
        divider(y);
        y += 6;

        /* invoice meta */
        ctx.textAlign = 'right';
        if (order) {
            ctx.font = 'bold 21px ' + fontFamily;
            ctx.fillText('فاتورة رقم: ' + (order.invoice_number || order.code || ''), width - padX, y + 13);
            y += 24;
            ctx.font = '19px ' + fontFamily;
            ctx.fillStyle = '#333333';
            ctx.fillText(String((order.date || '') + (order.time ? '  ·  ' + order.time : '')), width - padX, y + 11);
            ctx.fillStyle = '#111111';
            y += 24;
            ctx.textAlign = 'right';
            if (order.customer_name) {
                ctx.font = 'bold 21px ' + fontFamily;
                ctx.fillText('العميل: ' + order.customer_name, width - padX, y + 12);
                y += 24;
            }
            if (order.customer_phone) {
                ctx.font = '18px ' + fontFamily;
                ctx.fillStyle = '#333333';
                ctx.fillText('هاتف: ' + order.customer_phone, width - padX, y + 11);
                ctx.fillStyle = '#111111';
                y += 24;
            }
            if (order.cashier) {
                ctx.font = '18px ' + fontFamily;
                ctx.fillStyle = '#333333';
                ctx.fillText('الكاشير: ' + order.cashier, width - padX, y + 11);
                ctx.fillStyle = '#111111';
                y += 24;
            }
        }
        y += 8;
        divider(y);
        y += 6;

        /* items table header */
        const colNameX = width - padX;      // right aligned
        const colQtyX = 348;
        const colPriceX = 262;
        const colTotalX = 150;
        ctx.font = 'bold 19px ' + fontFamily;
        ctx.textAlign = 'right';
        ctx.fillText('الصنف', colNameX, y + 14);
        ctx.textAlign = 'left';
        ctx.fillText('كمية', colQtyX, y + 14);
        ctx.fillText('السعر', colPriceX, y + 14);
        ctx.fillText('الإجمالي', colTotalX, y + 14);
        y += 30;
        divider(y);
        y += 4;

        /* items */
        itemLayout.forEach(function (row) {
            const rowTop = y;
            const rowH = Math.max(1, row.rows) * lineH + 6;
            ctx.textAlign = 'right';
            ctx.font = 'bold 20px ' + fontFamily;
            let yy = y + 12;
            row.nameLines.forEach(function (ln) {
                ctx.fillText(ln, colNameX, yy);
                yy += lineH;
            });
            ctx.font = '16px ' + fontFamily;
            ctx.fillStyle = '#444444';
            row.serviceLines.forEach(function (ln) {
                ctx.fillText(ln, colNameX, yy);
                yy += lineH - 2;
            });
            ctx.fillStyle = '#111111';
            ctx.font = '19px ' + fontFamily;
            ctx.direction = 'ltr';
            ctx.textAlign = 'left';
            const mid = rowTop + rowH / 2;
            ctx.fillText(String(row.it.quantity), colQtyX, mid);
            ctx.fillText(formatMoney(row.it.unit_price, ''), colPriceX, mid);
            ctx.fillText(formatMoney(row.it.total, ''), colTotalX, mid);
            ctx.direction = rtl ? 'rtl' : 'ltr';
            y += rowH;
        });
        y += 6;
        divider(y);
        y += 10;

        /* totals */
        const currency = data.currency || '';
        function totalRow(label, value, strong) {
            ctx.textAlign = 'right';
            ctx.font = (strong ? 'bold 22px ' : '19px ') + fontFamily;
            ctx.fillStyle = strong ? '#000000' : '#222222';
            ctx.fillText(label, width - padX, y + 12);
            ctx.textAlign = 'left';
            ctx.direction = 'ltr';
            ctx.fillText(formatMoney(value, currency), colPriceX, y + 12);
            ctx.direction = rtl ? 'rtl' : 'ltr';
            y += 26;
        }
        if (order) {
            totalRow('المجموع الفرعي', order.subtotal, false);
            if (order.tax !== null && order.tax !== undefined && order.tax !== '') {
                totalRow('الضريبة', order.tax, false);
            }
            if (order.discount) {
                totalRow('الخصم', order.discount, false);
            }
            if (order.delivery !== null && order.delivery !== undefined && order.delivery !== '') {
                totalRow('رسوم التوصيل', order.delivery, false);
            }
            y += 4;
            divider(y);
            y += 8;
            ctx.textAlign = 'right';
            ctx.font = 'bold 26px ' + fontFamily;
            ctx.fillText('الإجمالي النهائي', width - padX, y + 18);
            ctx.textAlign = 'left';
            ctx.direction = 'ltr';
            ctx.fillText(formatMoney(order.total, currency), colPriceX, y + 18);
            ctx.direction = rtl ? 'rtl' : 'ltr';
            y += 56;

            /* payment */
            ctx.font = '19px ' + fontFamily;
            ctx.textAlign = 'right';
            if (order.payment_method) {
                ctx.fillStyle = '#333333';
                ctx.fillText('طريقة الدفع: ' + order.payment_method, width - padX, y + 11);
                y += 24;
            }
            if (order.payment_status) {
                ctx.fillText('حالة الدفع: ' + order.payment_status, width - padX, y + 11);
                y += 24;
            }
            ctx.fillStyle = '#111111';

            /* extras */
            if (order.extras && order.extras.length) {
                ctx.font = '17px ' + fontFamily;
                ctx.fillStyle = '#333333';
                ctx.fillText('إضافات: ' + order.extras.join('، '), width - padX, y + 11);
                ctx.fillStyle = '#111111';
                y += 28;
            }
        }

        /* notes (already contains the "المدفوع/المتبقي" line when applicable) */
        noteLines.forEach(function (ln) {
            ctx.font = '16px ' + fontFamily;
            ctx.fillStyle = '#333333';
            ctx.textAlign = 'right';
            ctx.fillText(ln, width - padX, y + 11);
            y += 22;
        });
        ctx.fillStyle = '#111111';
        if (noteLines.length) y += 8;

        /* copy ribbon */
        if (showCopyRibbon) {
            const label = copyLabel(copy, 'ar');
            ctx.fillStyle = '#111111';
            ctx.fillRect(0, y, width, 46);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 22px ' + fontFamily;
            ctx.textAlign = 'center';
            ctx.fillText(label, width / 2, y + 23);
            ctx.fillStyle = '#111111';
            y += 56;
        }

        /* barcode */
        if (order && order.id) {
            const bits = barcodeBits(order.id);
            if (bits) {
                const bw = 3;
                const barHeight = 64;
                const totalW = bits.length * bw;
                const x = Math.floor((width - totalW) / 2);
                drawBarcodeBits(ctx, bits, x, y + 4, bw, barHeight, '#000000');
                y += barHeight + 8;
                ctx.font = '17px ' + fontFamily;
                ctx.textAlign = 'center';
                ctx.direction = 'ltr';
                ctx.fillText('# ' + bits.length + ' · ' + String(order.code || order.id), width / 2, y + 9);
                ctx.direction = rtl ? 'rtl' : 'ltr';
                y += 20;
            }
        }

        return canvas;
    }

    /* ---------- send flow ---------- */
    async function printInvoice(payload, opts) {
        const o = opts || {};
        const conn = o.conn;
        if (!conn) throw new Error('NO_CONNECTION');
        const profile = o.profile || (conn.loadPref ? (conn.loadPref('invoice') || {}).profile : 'rpp320-tspl') || 'rpp320-tspl';
        const role = 'invoice';
        if (conn.state && conn.state.role) conn.state.role = role;

        const canvas = o.canvas || (typeof document !== 'undefined' ? document.createElement('canvas') : null);
        if (!canvas) throw new Error('NO_CANVAS');
        drawInvoice(canvas, payload, {
            copy: o.copy,
            fontFamily: o.fontFamily,
            rtl: o.rtl,
        });
        if (document.fonts && document.fonts.ready) {
            try { await document.fonts.ready; } catch (e) { /* non-fatal */ }
            // re-draw with real measurements now that Cairo is loaded
            drawInvoice(canvas, payload, { copy: o.copy, fontFamily: o.fontFamily, rtl: o.rtl });
        }
        const mono = EcleanBT.raster.canvasToMono(canvas);
        const job = EcleanBT.raster.buildJob(mono, canvas.width, canvas.height, {
            profile: profile,
            copies: o.copies || 1,
        });
        await EcleanBT.jobs.writeChunked(conn, job, {
            profile: profile,
            onProgress: o.onProgress,
            onStatus: o.onStatus,
            isAborted: o.isAborted,
        });
        return { bytes: job.length, width: canvas.width, height: canvas.height };
    }

    return {
        drawInvoice: drawInvoice,
        printInvoice: printInvoice,
        wrapText: wrapText,
        formatMoney: formatMoney,
        copyLabel: copyLabel,
        barcodeBits: barcodeBits,
        createMeasureCtx: createMeasureCtx,
    };
});
