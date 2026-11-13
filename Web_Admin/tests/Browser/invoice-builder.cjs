/* Run the feature suite with INVOICE_BROWSER_ARTIFACTS first. Uses rendered Blade
   fixtures from the isolated SQLite tests, real assets and an intercepted API.
   API persistence and authorization are separately covered by the feature suite. */
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const runtime = process.env.CODEX_NODE_MODULES || 'C:/Users/Lenovo/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules';
const { chromium } = require(path.join(runtime, 'playwright'));
const root = path.resolve(__dirname, '../..');
const artifacts = path.join(root, 'storage/framework/testing/invoice-browser');
const origin = 'http://127.0.0.1:8000';
let checks = 0;
function check(value, message) { assert.ok(value, message); checks++; }

(async () => {
    const browser = await chromium.launch({ executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
    try {
        const context = await browser.newContext({ viewport: { width: 1600, height: 1100 }, locale: 'ar' });
        let saveBody = null, defaults = null;
        const errors = [];
        const page = await context.newPage();
        page.on('pageerror', error => { if (/pos-invoice|invoice-builder/.test(error.stack || '')) errors.push(error.message); });
        await context.route('**/*', async route => {
            const request = route.request(), url = new URL(request.url());
            if (url.origin !== origin) return route.fulfill({ status: 200, body: '' });
            if (url.pathname === '/vendor/invoice-template' && request.method() === 'POST') {
                saveBody = request.postData();
                return route.fulfill({ json: { message: 'تم حفظ قالب الفاتورة.' } });
            }
            if (url.pathname === '/vendor/invoice-template' && request.method() === 'DELETE') {
                return route.fulfill({ json: { message: 'تمت استعادة القالب الافتراضي.', settings: defaults } });
            }
            if (url.pathname === '/vendor/invoice-template') return route.fulfill({ contentType: 'text/html', body: fs.readFileSync(path.join(artifacts, 'editor.html'), 'utf8') });
            if (url.pathname === '/vendor/invoice-template/preview') return route.fulfill({ contentType: 'text/html', body: fs.readFileSync(path.join(artifacts, 'document.html'), 'utf8') });
            const file = path.resolve(root, 'public', '.' + decodeURIComponent(url.pathname));
            if (file.startsWith(path.join(root, 'public') + path.sep) && fs.existsSync(file) && fs.statSync(file).isFile()) {
                const types = { '.js': 'text/javascript', '.css': 'text/css', '.png': 'image/png', '.jpg': 'image/jpeg', '.svg': 'image/svg+xml' };
                return route.fulfill({ contentType: types[path.extname(file)] || 'application/octet-stream', body: fs.readFileSync(file) });
            }
            return route.fulfill({ status: 200, body: '{}' });
        });
        await page.goto(origin + '/vendor/invoice-template');
        const frame = page.frameLocator('#invoice-preview');
        await frame.locator('.pos-invoice').waitFor();
        defaults = await page.evaluate(() => window.invoiceBuilder.defaults);
        check((await frame.locator('[data-field="total"]').textContent()).includes('23.00'), 'Stored total displayed');
        const originalDocument = await frame.locator('body').evaluate(body => { body.dataset.testIdentity = 'unchanged-document'; return body.dataset.testIdentity; });
        await page.locator('[data-text="title"]').fill('فاتورة المحل الجديدة');
        await frame.locator('h1').filter({ hasText: 'فاتورة المحل الجديدة' }).waitFor();
        check(await frame.locator('body').getAttribute('data-test-identity') === originalDocument, 'Live preview did not reload');
        await page.locator('[data-setting="accent"]').fill('#123456');
        await page.waitForFunction(() => document.getElementById('invoice-preview').contentDocument.querySelector('.pos-invoice').style.getPropertyValue('--accent') === '#123456');
        checks++;
        await page.locator('[data-text="title"]').fill('<img src=x onerror="window.injected=true">');
        await frame.locator('h1').filter({ hasText: '<img src=x' }).waitFor();
        check(await frame.locator('h1 img').count() === 0, 'Custom text cannot become HTML');
        check(await page.frames()[1].evaluate(() => !window.injected), 'Script did not execute');
        await page.locator('[data-text="title"]').fill('فاتورة مبيعات');
        // Thermal stress fixture: long names and zero prices, without changing real endpoint data.
        await page.frames()[1].evaluate(() => {
            window.posInvoicePayload.data.order.items[0].name = 'اسم منتج طويل جدًا بالعربية لاختبار الطباعة الحرارية دون قص النص أو تداخل المبالغ '.repeat(3);
        });
        for (const paper of ['A4', '80mm', '58mm']) {
            await page.locator('[data-setting="paper"]').selectOption(paper);
            await frame.locator(`.pos-invoice[data-paper="${paper}"]`).waitFor();
            const metrics = await frame.locator('.pos-invoice').evaluate(node => ({ width: node.getBoundingClientRect().width, scroll: node.scrollWidth, client: node.clientWidth }));
            const expected = { A4: 793.7, '80mm': 302.36, '58mm': 219.21 }[paper];
            check(Math.abs(metrics.width - expected) < 2, `Correct ${paper} width`);
            check(metrics.scroll <= metrics.client + 1, `No clipped content in ${paper}`);
            const totals = await frame.locator('[data-field="total"]').textContent();
            for (const copy of ['normal', 'customer', 'store']) {
                await page.locator('#preview-copy').selectOption(copy);
                if (copy === 'normal') await frame.locator('.copy-heading').waitFor({ state: 'detached' });
                else await frame.locator('.copy-heading').filter({ hasText: copy === 'customer' ? 'نسخة الزبون' : 'نسخة المحل' }).waitFor();
                check(await frame.locator('[data-field="total"]').textContent() === totals, `${paper}/${copy} total unchanged`);
                check(await frame.locator('.copy-heading').count() === (copy === 'normal' ? 0 : 1), 'Only selected copy heading');
            }
            await page.locator('#preview-copy').selectOption('normal');
            await page.screenshot({ path: path.join(artifacts, `editor-${paper}.png`) });
            // Print PDFs use the real document and the CSS dimensions; thermal roll
            // height is selected by the driver in physical printing.
            const printPage = await context.newPage();
            await printPage.goto(origin + '/vendor/invoice-template/preview');
            await printPage.waitForFunction(() => !!window.PosInvoice);
            await printPage.evaluate(paper => { const p = window.posInvoicePayload; p.settings.paper = paper; window.PosInvoice.render(p); }, paper);
            await printPage.pdf({ path: path.join(artifacts, `invoice-${paper}.pdf`), printBackground: true,
                ...(paper === 'A4' ? { format: 'A4', preferCSSPageSize: true } : { width: paper, height: '300mm' }), margin: { top: 0, right: 0, bottom: 0, left: 0 } });
            await printPage.close();
        }
        await page.locator('[data-setting="font_size"]').fill('24');
        await page.locator('[data-setting="heading_size"]').fill('36');
        await page.locator('[data-setting="padding"]').fill('15');
        await page.waitForFunction(() => document.getElementById('invoice-preview').contentDocument.querySelector('.pos-invoice').style.getPropertyValue('--padding') === '15mm');
        check(await frame.locator('.pos-invoice').evaluate(node => node.scrollWidth <= node.clientWidth + 1), '58mm extreme font and padding remain inside paper');
        await page.locator('[data-setting="font_size"]').fill('12');
        await page.locator('[data-setting="heading_size"]').fill('20');
        await page.locator('[data-setting="padding"]').fill('5');
        await page.locator('[data-setting="language"]').selectOption('en');
        await page.waitForFunction(() => document.getElementById('invoice-preview').contentDocument.documentElement.dir === 'ltr');
        check(/Grand total23\.00/.test(await frame.locator('[data-field="total"]').textContent()), 'English LTR labels use same amounts');
        await page.locator('[data-setting="language"]').selectOption('ar');
        // Hide / show field and reorder with accessible controls.
        await page.locator('summary').filter({ hasText: 'إظهار الحقول' }).click();
        await page.locator('[data-visible="customer_phone"]').uncheck();
        await frame.locator('[data-field="customer_phone"]').waitFor({ state: 'detached' }); checks++;
        await page.locator('summary').filter({ hasText: 'ترتيب الأقسام' }).click();
        await page.locator('#invoice-sections button[aria-label="تأخير هوية المتجر والشعار"]').click();
        await page.waitForFunction(() => document.getElementById('invoice-preview').contentDocument.querySelector('[data-section]').dataset.section === 'header'); checks++;
        // Logo upload updates before saving (real PNG, no HTTP upload required).
        await page.locator('summary').filter({ hasText: 'شعار المحل' }).click();
        await page.locator('#invoice-logo').setInputFiles({ name: 'logo.png', mimeType: 'image/png', buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl6pWQAAAAASUVORK5CYII=', 'base64') });
        await frame.locator('.brand img').waitFor(); checks++;
        check(await frame.locator('.brand img').getAttribute('src').then(src => src.startsWith('data:image/png;base64,')), 'Logo preview uses uploaded image');
        check(await page.frames()[1].evaluate(() => window.PosInvoice.barcodeBits('41').startsWith((35770).toString(2))), 'Barcode start symbol');
        check(await page.frames()[1].evaluate(() => window.PosInvoice.barcodeBits('<script>') === null), 'Barcode rejects invalid input');
        await page.locator('#save-template').click();
        await page.locator('#builder-status').filter({ hasText: 'تم حفظ قالب الفاتورة.' }).waitFor();
        check(saveBody?.includes('name="settings"') && saveBody?.includes('name="logo"'), 'Save sends settings and logo');
        await page.frames()[1].evaluate(() => { window.print = () => { window.testPrinted = true; }; });
        await page.locator('#preview-print').click();
        await page.waitForFunction(() => document.getElementById('invoice-preview').contentWindow.testPrinted === true); checks++;
        page.once('dialog', dialog => dialog.accept());
        await page.locator('#reset-template').click();
        await page.locator('#builder-status').filter({ hasText: 'تمت استعادة القالب الافتراضي.' }).waitFor();
        check(await page.locator('[data-setting="paper"]').inputValue() === '80mm', 'Reset updates form');
        await frame.locator('.brand img').waitFor({ state: 'detached' }); checks++;
        await page.setViewportSize({ width: 390, height: 844 });
        await page.screenshot({ path: path.join(artifacts, 'editor-mobile.png') });
        check(await page.locator('.ib-layout').evaluate(node => getComputedStyle(node).gridTemplateColumns.split(' ').length === 1), 'Mobile stacks editor and preview');
        check(errors.length === 0, 'No invoice JavaScript errors: ' + errors.join('; '));
        console.log(`PASS: ${checks} browser assertions; screenshots and print PDFs: ${artifacts}`);
        await context.close();
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
