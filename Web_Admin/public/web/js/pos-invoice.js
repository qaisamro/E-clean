/* One renderer for saved invoices and unsaved live previews. No user HTML is parsed. */
(function () {
    'use strict';
    const labels = {
        ar: { customer: 'نسخة الزبون', store: 'نسخة المحل', invoice_number: 'رقم الفاتورة', order_number: 'رقم الطلب',
            date: 'التاريخ', time: 'الوقت', delivery_date: 'تاريخ التسليم', delivery_time: 'ساعة التسليم', customer_name: 'العميل', customer_phone: 'هاتف العميل', cashier: 'الكاشير',
            item_name: 'المنتج / الخدمة', quantity: 'الكمية', unit_price: 'سعر الوحدة', item_discount: 'خصم الصنف', line_total: 'الإجمالي',
            subtotal: 'المجموع الفرعي', discount: 'الخصم', tax: 'الضريبة', delivery: 'رسوم التوصيل', total: 'الإجمالي النهائي',
            payment_method: 'طريقة الدفع', payment_status: 'حالة الدفع', order_notes: 'ملاحظات الطلب',
            tax_number: 'الرقم الضريبي', empty: 'لا يوجد طلب للمعاينة. سيظهر هنا الطلب الحقيقي عند توفره.', unavailable: 'غير متاح',
            extras: 'إضافات الطلب (السعر التاريخي غير محفوظ)', barcode: 'رقم الطلب' },
        en: { customer: 'Customer copy', store: 'Store copy', invoice_number: 'Invoice no.', order_number: 'Order no.',
            date: 'Date', time: 'Time', delivery_date: 'Delivery date', delivery_time: 'Delivery time', customer_name: 'Customer', customer_phone: 'Customer phone', cashier: 'Cashier',
            item_name: 'Product / service', quantity: 'Qty', unit_price: 'Unit price', item_discount: 'Item discount', line_total: 'Total',
            subtotal: 'Subtotal', discount: 'Discount', tax: 'Tax', delivery: 'Delivery', total: 'Grand total',
            payment_method: 'Payment method', payment_status: 'Payment status', order_notes: 'Order notes', tax_number: 'Tax ID',
            empty: 'No order available. A real order will appear here when available.', unavailable: 'Unavailable',
            extras: 'Order extras (historical price unavailable)', barcode: 'Order ID' },
    };
    // Numeric Code 39 symbol data; barcode carries the real internal order ID, not a public tracking credential.
    function barcodeBits(id) {
        if (!/^\d+$/.test(String(id))) return null;
        const symbols = [20957, 29783, 23639, 30485, 20951, 29813, 23669, 20855, 29789, 23645];
        const stop = (35770).toString(2);
        return stop + Array.from(String(id), digit => symbols[Number(digit)].toString(2) + '0').join('') + stop;
    }
    function el(tag, value, className) {
        const node = document.createElement(tag);
        if (value !== undefined && value !== null) node.textContent = String(value);
        if (className) node.className = className;
        return node;
    }
    function money(value, currency, t) {
        return value === null || value === undefined || value === '' || !Number.isFinite(Number(value))
            ? t.unavailable : Number(value).toFixed(2) + (currency ? ' ' + currency : '');
    }
    function barcode(id, label) {
        const bits = barcodeBits(id);
        if (!bits) return null;
        const wrap = el('div', null, 'barcode');
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', `0 0 ${bits.length + 24} 45`);
        svg.setAttribute('preserveAspectRatio', 'none');
        svg.setAttribute('role', 'img'); svg.setAttribute('aria-label', `${label}: ${id}`);
        Array.from(bits).forEach((bit, index) => {
            if (bit !== '1') return;
            const rect = document.createElementNS(svg.namespaceURI, 'rect');
            rect.setAttribute('x', String(index + 12)); rect.setAttribute('y', '0');
            rect.setAttribute('width', '1'); rect.setAttribute('height', '45'); rect.setAttribute('fill', '#000');
            svg.append(rect);
        });
        wrap.append(svg, el('small', `${label}: ${id}`));
        return wrap;
    }
    function render(payload) {
        const { settings: s, data, logo, copy = 'normal' } = payload;
        const t = labels[s.language] || labels.ar;
        const o = data.order, v = data.vendor, show = s.visible;
        document.documentElement.dir = s.direction;
        document.documentElement.lang = s.language;
        const paper = s.paper === 'A4' ? '210mm' : s.paper === '58mm' ? '58mm' : '80mm';
        let pageStyle = document.getElementById('invoice-page-style');
        if (!pageStyle) { pageStyle = el('style'); pageStyle.id = 'invoice-page-style'; document.head.append(pageStyle); }
        pageStyle.textContent = `@page { size: ${s.paper === 'A4' ? 'A4' : 'auto'}; margin: 0; }`;
        const sheet = el('article', null, 'pos-invoice'); sheet.dataset.paper = s.paper;
        const fonts = { system: 'Tahoma, Arial, sans-serif', serif: 'Georgia, "Times New Roman", serif', mono: 'monospace' };
        const vars = { 'paper-width': paper, padding: `${s.padding}mm`, ink: s.text_color, paper: s.background,
            font: fonts[s.font], 'font-size': `${s.font_size}px`, 'heading-size': `${s.heading_size}px`,
            spacing: `${s.spacing}px`, 'border-width': `${s.border_width}px`, line: s.border_color,
            accent: s.accent, align: s.align, 'logo-align': s.logo_align, 'logo-size': `${s.logo_size}px` };
        Object.entries(vars).forEach(([key, value]) => sheet.style.setProperty('--' + key, value));
        if (copy !== 'normal') {
            const heading = el('div', t[copy], 'copy-heading');
            heading.append(el('small', copy === 'customer' ? o?.customer_name : v.name)); sheet.append(heading);
        }
        const text = (key, fallback) => s.text[key] || fallback || '';
        const blocks = {};
        for (const key of ['brand', 'header', 'details', 'items', 'totals', 'footer']) {
            blocks[key] = el('section', null, key); blocks[key].dataset.section = key;
        }
        if (show.logo && (logo || v.logo)) {
            const img = el('img'); img.alt = v.name || ''; img.src = logo || v.logo; blocks.brand.append(img);
        }
        if (show.store_name) blocks.brand.append(el('h2', text('store_name', v.name)));
        for (const key of ['address', 'phone', 'email', 'tax_number', 'contact']) {
            const value = text(key, v[key]);
            if (show[key] && value) blocks.brand.append(el('p', key === 'tax_number' ? `${t.tax_number}: ${value}` : value));
        }
        blocks.header.append(el('h1', text('title')), el('p', text('header')));
        const detail = (parent, key, value, numeric = false) => {
            if (!show[key] || value === null || value === undefined || value === '') return;
            const row = el('div', null, 'detail' + (key === 'total' ? ' grand-total' : ''));
            row.dataset.field = key;
            row.append(el('span', t[key]), el('span', numeric ? money(value, data.currency, t) : value, numeric ? 'money' : ''));
            parent.append(row);
        };
        if (!o) {
            blocks.details.append(el('p', t.empty, 'empty-order'));
        } else {
            for (const [key, value] of Object.entries({ invoice_number: o.invoice_number, order_number: o.code,
                date: o.date, time: o.time, delivery_date: o.delivery_date, delivery_time: o.delivery_time, customer_name: o.customer_name, customer_phone: o.customer_phone, cashier: o.cashier })) {
                detail(blocks.details, key, value);
            }
            const columns = ['item_name', 'quantity', 'unit_price', 'item_discount', 'line_total'].filter(key =>
                show[key] && (key !== 'item_discount' || o.items.some(item => item.discount != null)));
            const table = el('table', null, 'wide-items'), head = el('thead'), headRow = el('tr'), body = el('tbody');
            columns.forEach(key => headRow.append(el('th', t[key]))); head.append(headRow);
            const thermal = el('div', null, 'thermal-items');
            for (const item of o.items) {
                const values = { item_name: item.name, quantity: item.quantity, unit_price: money(item.unit_price, data.currency, t),
                    item_discount: money(item.discount, data.currency, t), line_total: money(item.total, data.currency, t) };
                const row = el('tr'), narrow = el('div', null, 'thermal-item');
                for (const key of columns) {
                    if (key === 'item_discount' && item.discount == null) continue;
                    const cell = el('td', values[key]);
                    if (key === 'item_name' && item.service) cell.append(el('small', item.service));
                    row.append(cell);
                    if (key === 'item_name') {
                        narrow.append(el('strong', item.name));
                        if (item.service) narrow.append(el('small', item.service));
                    } else { const field = el('div', null, 'detail'); field.append(el('span', t[key]), el('span', values[key])); narrow.append(field); }
                }
                // Keep table cells aligned when no historical line discount exists.
                if (columns.includes('item_discount') && item.discount == null) {
                    const index = columns.indexOf('item_discount'); row.insertBefore(el('td', '—'), row.children[index] || null);
                }
                body.append(row); thermal.append(narrow);
            }
            if (columns.length) { table.append(head, body); blocks.items.append(table, thermal); }
            if (show.item_name && o.extras?.length) {
                blocks.items.append(el('p', t.extras)); o.extras.forEach(name => blocks.items.append(el('p', name)));
            }
            for (const key of ['subtotal', 'discount', 'tax', 'delivery', 'total']) detail(blocks.totals, key, o[key], true);
            detail(blocks.totals, 'payment_method', o.payment_method);
            detail(blocks.totals, 'payment_status', s.language === 'en' ? (o.payment_status_raw || o.payment_status) : o.payment_status);
            detail(blocks.footer, 'order_notes', o.order_notes);
        }
        for (const key of ['thanks', 'notes', 'footer']) if (text(key)) blocks.footer.append(el('p', text(key)));
        if (o && show.barcode) { const code = barcode(o.id, t.barcode); if (code) blocks.footer.append(code); }
        for (const section of s.sections) if (blocks[section]) sheet.append(blocks[section]);
        document.getElementById('invoice-root').replaceChildren(sheet);
        return sheet;
    }
    async function printReady() {
        await document.fonts.ready;
        await Promise.all(Array.from(document.images, img => img.decode?.().catch(() => {}) || Promise.resolve()));
        window.print();
    }
    // Exposed for deterministic DOM and barcode tests; no network or global customer state.
    window.PosInvoice = { render, barcodeBits, printReady };
    if (window.posInvoicePayload) {
        const initial = window.posInvoicePayload;
        render(initial);
        if (initial.preview) {
            window.addEventListener('message', event => {
                if (event.origin !== location.origin || event.source !== parent) return;
                if (event.data?.type === 'invoice-preview') render({ ...initial, settings: event.data.settings, logo: event.data.logo, copy: event.data.copy });
                if (event.data?.type === 'invoice-print') printReady();
            });
            parent.postMessage({ type: 'invoice-ready' }, location.origin);
        }
        document.getElementById('print-invoice')?.addEventListener('click', printReady);
    }
})();
