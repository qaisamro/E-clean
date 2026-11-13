/* ==========================================================================
   E-Clean — Piece stickers canvas renderer + Bluetooth send.
   Shared between the two Laravel apps; depends on eclean-bt-core.js.
   Item shape (built server-side):
     { customer: 'name', order: 'E-0001', item: 'اسم الصنف' }
   or an expanded list from EcleanBT.stickers.expandItems([...quantity items]).
   ========================================================================== */
(function (root, factory) {
    'use strict';
    const api = factory(typeof root.EcleanBT !== 'undefined'
        ? root.EcleanBT
        : (typeof require === 'function' ? require('./eclean-bt-core.js') : undefined));
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
    root.EcleanBTStickers = api;
})(typeof window !== 'undefined' ? window : globalThis, function (EcleanBT) {
    'use strict';

    const DEFAULTS = {
        widthPx: 384,
        heightPx: 220,
        fontFamily: '"Cairo", Tahoma, Arial, sans-serif',
    };

    /* draw one sticker on the given canvas (fills it) */
    function drawSticker(canvas, s, opts) {
        const o = opts || {};
        const width = o.widthPx || DEFAULTS.widthPx;
        const height = o.heightPx || DEFAULTS.heightPx;
        const fontFamily = o.fontFamily || DEFAULTS.fontFamily;
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);

        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.setLineDash([8, 6]);
        ctx.strokeRect(6, 6, width - 12, height - 12);
        ctx.setLineDash([]);

        ctx.fillStyle = '#000000';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        ctx.font = 'bold 34px ' + fontFamily;
        ctx.fillText(String(s.customer || 'عميل نقدي'), width / 2, Math.round(height * 0.24));

        ctx.font = 'bold 28px ' + fontFamily;
        ctx.fillText('# ' + String(s.order || ''), width / 2, Math.round(height * 0.48));

        const itemText = String(s.item || '');
        const maxWidth = width - 36;
        ctx.font = itemText.length > 28 ? '18px ' + fontFamily : 'bold 21px ' + fontFamily;
        ctx.fillText(itemText, width / 2, Math.round(height * 0.76), maxWidth);

        return canvas;
    }

    /* expand server-prepared items by quantity, preserving order */
    function expandItems(items) {
        if (EcleanBT && EcleanBT.stickers && typeof EcleanBT.stickers.expandItems === 'function') {
            return EcleanBT.stickers.expandItems(items);
        }
        const out = [];
        const list = Array.isArray(items) ? items : [];
        for (let i = 0; i < list.length; i++) {
            const qty = Math.max(1, Math.floor(Number(list[i].quantity) || 1));
            for (let c = 0; c < qty; c++) out.push(list[i]);
        }
        return out;
    }

    /* print every sticker (list order), one canvas each, one job each */
    async function printStickers(items, opts) {
        const o = opts || {};
        const conn = o.conn;
        if (!conn) throw new Error('NO_CONNECTION');
        const profile = o.profile || ((conn.loadPref ? (conn.loadPref('label') || {}).profile : null) || 'generic-escpos');
        const role = 'label';
        if (conn.state && conn.state.role) conn.state.role = role;

        const list = expandItems(items);
        if (!list.length) {
            if (typeof o.onStatus === 'function') o.onStatus('لا توجد ملصقات للطباعة', 'warning');
            return { printed: 0, total: 0 };
        }
        const width = o.widthPx || DEFAULTS.widthPx;
        const height = o.heightPx || DEFAULTS.heightPx;
        const fontFamily = o.fontFamily || DEFAULTS.fontFamily;

        const canvas = typeof document !== 'undefined' ? document.createElement('canvas') : o.canvas;
        if (!canvas) throw new Error('NO_CANVAS');

        let success = 0;
        for (let i = 0; i < list.length; i++) {
            if (o.isAborted && o.isAborted()) {
                throw new Error('JOB_ABORTED');
            }
            if (typeof o.onStatus === 'function') {
                o.onStatus('جارٍ إرسال الملصق (' + (i + 1) + ' من ' + list.length + ')...', 'info');
            }
            drawSticker(canvas, list[i], { widthPx: width, heightPx: height, fontFamily: fontFamily });
            const mono = EcleanBT.raster.canvasToMono(canvas);
            const job = EcleanBT.raster.buildJob(mono, width, height, {
                profile: profile,
                copies: 1,
            });
            const idx = i;
            await EcleanBT.jobs.writeChunked(conn, job, {
                profile: profile,
                isAborted: o.isAborted,
                onStatus: o.onStatus,
                onProgress: function (pct) {
                    if (typeof o.onProgress === 'function') {
                        o.onProgress(Math.floor(((idx + pct / 100) / list.length) * 100));
                    }
                },
            });
            success++;
        }
        if (typeof o.onProgress === 'function') o.onProgress(100);
        return { printed: success, total: list.length };
    }

    return {
        drawSticker: drawSticker,
        printStickers: printStickers,
        expandItems: expandItems,
        DEFAULTS: DEFAULTS,
    };
});
