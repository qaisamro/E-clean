export default async function run(page, ui) {
  await page.waitForTimeout(800);
  // Use page locators directly
  const email = page.getByPlaceholder('البريد الإلكتروني').first();
  // fallback: first textbox
  try {
    await page.locator('input[type="email"], input[name="email"]').first().fill('qais@gmail.com', { timeout: 5000 });
  } catch(e) {
    return { error: 'fill email failed', msg: e.message, html: await page.content().then(c=>c.slice(0,2000)) };
  }
  await page.locator('input[type="password"], input[name="password"]').first().fill('admin12345');
  await page.getByRole('button', { name: 'تسجيل الدخول' }).click();
  await page.waitForTimeout(2000);
  const afterLoginUrl = page.url();
  const afterBody = await page.evaluate(() => document.body.innerText.slice(0, 4000));
  const afterHtml = await page.content().then(c=>c.slice(0,3000));

  // Navigate to POS
  await page.goto('http://127.0.0.1:8000/pos', { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(1500);

  const posChecks = await page.evaluate(() => {
    const delivery = document.querySelector('#deliveryDateInput');
    const cashBox = document.querySelector('[data-payment-method="cash"]');
    const visaBox = document.querySelector('[data-payment-method="visa"]');
    const cashRadio = document.querySelector('#paymentMethodCash');
    const visaRadio = document.querySelector('#paymentMethodVisa');
    const grandTotal = document.querySelector('#grandTotal');
    const basket = document.querySelector('#basketProducts');
    const confirmBtn = document.querySelector('a[onclick="submitform()"]');
    const getRect = (el) => {
      if (!el) return null;
      const r = el.getBoundingClientRect();
      // also check computed visibility
      const style = window.getComputedStyle(el);
      return { x: Math.round(r.x), y: Math.round(r.y), w: Math.round(r.width), h: Math.round(r.height), display: style.display, visibility: style.visibility, offsetParent: el.offsetParent !== null };
    };
    return {
      url: location.href,
      title: document.title,
      bodyText: document.body.innerText.slice(0, 3000),
      deliveryDateInput: delivery ? { exists: true, value: delivery.value, type: delivery.type, rect: getRect(delivery), outerHTML: delivery.outerHTML.slice(0, 600) } : { exists: false },
      cashBox: cashBox ? { exists: true, rect: getRect(cashBox), html: cashBox.outerHTML.slice(0, 700) } : { exists: false },
      visaBox: visaBox ? { exists: true, rect: getRect(visaBox), html: visaBox.outerHTML.slice(0, 700) } : { exists: false },
      cashRadio: cashRadio ? { exists: true, checked: cashRadio.checked, value: cashRadio.value } : { exists: false },
      visaRadio: visaRadio ? { exists: true, checked: visaRadio.checked, value: visaRadio.value } : { exists: false },
      grandTotal: grandTotal ? grandTotal.textContent.trim() : null,
      basketEmpty: basket ? basket.innerHTML.trim().length === 0 : null,
      confirmBtn: confirmBtn ? { exists: true, rect: getRect(confirmBtn), text: confirmBtn.innerText } : { exists: false },
      allIds: Array.from(document.querySelectorAll('[id]')).map(e => e.id).slice(0, 50),
    };
  });
  const snap = await ui.snapshot();
  return { afterLoginUrl, afterBody: afterBody.slice(0,3000), afterHtml: afterHtml.slice(0,2000), posChecks, snap: snap.slice(0,6000) };
}
