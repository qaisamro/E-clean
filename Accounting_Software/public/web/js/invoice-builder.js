(function () {
    'use strict';
    const config = window.invoiceBuilder;
    if (!config) return;
    const clone = value => JSON.parse(JSON.stringify(value));
    let settings = clone(config.settings), logo = config.logo, file = null, dirty = false, busy = false;
    const form = document.getElementById('invoice-builder-form'), frame = document.getElementById('invoice-preview');
    const status = document.getElementById('builder-status'), sectionList = document.getElementById('invoice-sections');
    const copy = document.getElementById('preview-copy');
    const sectionNames = { brand: 'هوية المتجر والشعار', header: 'العنوان والنص العلوي', details: 'تفاصيل الطلب والعميل', items: 'المنتجات والخدمات', totals: 'المبالغ والدفع', footer: 'التذييل والملاحظات والباركود' };
    function message(text, error = false) { status.textContent = text; status.dataset.error = String(error); }
    function preview() {
        frame.contentWindow?.postMessage({ type: 'invoice-preview', settings, logo, copy: copy.value }, location.origin);
    }
    function changed() { dirty = true; preview(); message('تعديلات غير محفوظة — احفظ القالب لتطبيقه على الفواتير القادمة.'); }
    function sections() {
        sectionList.replaceChildren();
        settings.sections.forEach((key, index) => {
            const row = document.createElement('li'), name = document.createElement('span'), buttons = document.createElement('div');
            name.textContent = sectionNames[key]; row.append(name, buttons);
            for (const [delta, label] of [[-1, '↑'], [1, '↓']]) {
                const button = document.createElement('button'); button.type = 'button'; button.textContent = label;
                button.disabled = index + delta < 0 || index + delta >= settings.sections.length;
                button.setAttribute('aria-label', `${delta === -1 ? 'تقديم' : 'تأخير'} ${sectionNames[key]}`);
                button.addEventListener('click', () => {
                    [settings.sections[index], settings.sections[index + delta]] = [settings.sections[index + delta], settings.sections[index]];
                    sections(); changed();
                }); buttons.append(button);
            }
            sectionList.append(row);
        });
    }
    function populate() {
        form.querySelectorAll('[data-setting]').forEach(input => { input.value = settings[input.dataset.setting]; });
        form.querySelectorAll('[data-text]').forEach(input => { input.value = settings.text[input.dataset.text] || ''; });
        form.querySelectorAll('[data-visible]').forEach(input => { input.checked = settings.visible[input.dataset.visible]; });
        sections();
    }
    form.addEventListener('input', event => {
        const input = event.target;
        if (input.dataset.setting) {
            if (!input.checkValidity()) return;
            settings[input.dataset.setting] = input.type === 'number' ? Number(input.value) : input.value;
            if (input.dataset.setting === 'language') {
                settings.direction = input.value === 'ar' ? 'rtl' : 'ltr';
                const defaults = { ar: ['فاتورة مبيعات', 'شكرًا لزيارتكم'], en: ['Sales invoice', 'Thank you for your visit'] };
                if (Object.values(defaults).some(pair => pair[0] === settings.text.title)) settings.text.title = defaults[input.value][0];
                if (Object.values(defaults).some(pair => pair[1] === settings.text.thanks)) settings.text.thanks = defaults[input.value][1];
                populate();
            }
        } else if (input.dataset.text) settings.text[input.dataset.text] = input.value;
        else if (input.dataset.visible) settings.visible[input.dataset.visible] = input.checked;
        else return;
        changed();
    });
    document.getElementById('invoice-logo').addEventListener('change', async event => {
        const selected = event.target.files[0];
        if (!selected) return;
        if (!['image/png', 'image/jpeg', 'image/webp'].includes(selected.type) || selected.size > 1024 * 1024) {
            event.target.value = ''; message('الشعار يجب أن يكون PNG أو JPEG أو WebP بحجم لا يتجاوز 1MB.', true); return;
        }
        const reader = new FileReader();
        reader.onload = () => {
            const image = new Image();
            image.onload = () => {
                if (image.width > 2400 || image.height > 2400) { message('أبعاد الشعار يجب ألا تتجاوز 2400×2400.', true); event.target.value = ''; return; }
                file = selected; logo = reader.result; document.getElementById('remove-logo').checked = false; changed();
            };
            image.onerror = () => { event.target.value = ''; message('تعذر قراءة صورة الشعار.', true); };
            image.src = reader.result;
        };
        reader.readAsDataURL(selected);
    });
    document.getElementById('remove-logo').addEventListener('change', event => {
        if (event.target.checked) { file = null; logo = null; document.getElementById('invoice-logo').value = ''; }
        else logo = config.logo;
        changed();
    });
    window.addEventListener('message', event => {
        if (event.origin === location.origin && event.source === frame.contentWindow && event.data?.type === 'invoice-ready') preview();
    });
    frame.addEventListener('load', preview);
    document.getElementById('preview-order').addEventListener('change', event => {
        const url = new URL(config.previewUrl); if (event.target.value) url.searchParams.set('order', event.target.value);
        frame.src = url.href;
    });
    copy.addEventListener('change', preview);
    document.getElementById('preview-print').addEventListener('click', () => {
        preview(); frame.contentWindow?.postMessage({ type: 'invoice-print' }, location.origin);
    });
    async function send(url, method, body) {
        if (busy) return null;
        busy = true;
        // Freeze editable controls during persistence so the saved preview cannot diverge.
        const controls = Array.from(document.querySelectorAll('.invoice-builder input, .invoice-builder textarea, .invoice-builder select, .invoice-builder button'));
        const states = controls.map(control => control.disabled); controls.forEach(control => { control.disabled = true; });
        try {
            const response = await fetch(url, { method, body, credentials: 'same-origin', headers: { Accept: 'application/json',
                'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value } });
            const result = await response.json();
            if (!response.ok) throw new Error(Object.values(result.errors || {}).flat().join('\n') || result.message || 'تعذر حفظ القالب.');
            message(result.message); return result;
        } catch (error) { message(error.message || 'تعذر الاتصال بالخادم.', true); return null; }
        finally { busy = false; controls.forEach((control, index) => { control.disabled = states[index]; }); }
    }
    form.addEventListener('submit', async event => {
        event.preventDefault(); if (!form.reportValidity()) return;
        const body = new FormData(); body.set('settings', JSON.stringify(settings));
        body.set('remove_logo', document.getElementById('remove-logo').checked ? '1' : '0');
        if (file) body.set('logo', file);
        const result = await send(config.saveUrl, 'POST', body);
        if (result) { dirty = false; config.logo = logo; file = null; document.getElementById('invoice-logo').value = ''; document.getElementById('remove-logo').checked = false; }
    });
    document.getElementById('reset-template').addEventListener('click', async () => {
        if (!confirm('استعادة القالب الافتراضي وحذف الشعار المخصص المحفوظ؟')) return;
        const result = await send(config.resetUrl, 'DELETE');
        if (result) {
            settings = clone(result.settings); logo = config.logo = null; file = null; dirty = false;
            document.getElementById('invoice-logo').value = ''; document.getElementById('remove-logo').checked = false;
            populate(); preview();
        }
    });
    window.addEventListener('beforeunload', event => { if (dirty) { event.preventDefault(); event.returnValue = ''; } });
    populate();
})();
