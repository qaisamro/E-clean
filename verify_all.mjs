export default async function run(page, ui) {
  // Login
  await page.waitForTimeout(600);
  try {
    await page.locator('input[type="email"], input[name="email"]').first().fill('qais@gmail.com', {timeout: 5000});
  } catch(e) { return {error: 'fill email failed', msg: e.message, html: (await page.content()).slice(0,1500)}; }
  await page.locator('input[type="password"], input[name="password"]').first().fill('admin12345');
  await page.getByRole('button', {name: 'تسجيل الدخول'}).click();
  await page.waitForTimeout(2000);

  let results = {};

  // 1. Check POS page delivery label and currency
  await page.goto('http://127.0.0.1:8000/pos', {waitUntil: 'domcontentloaded'});
  await page.waitForTimeout(1500);
  results.pos = await page.evaluate(() => {
    let bodyText = document.body.innerText;
    return {
      hasTasleem: bodyText.includes('تاريخ التسليم'),
      hasTawseelOld: bodyText.includes('تاريخ التوصيل'),
      bodySnippet: bodyText.slice(0, 2500),
      deliveryLabel: document.querySelector('label[for="deliveryDateInput"]')?.innerText || document.querySelector('label.form-label')?.innerText || null,
      allLabels: Array.from(document.querySelectorAll('label.form-label')).map(e=>e.innerText).slice(0,10),
      currencyPlaceholder: document.querySelector('#discountInput')?.placeholder || document.querySelector('#deliveryChargeInput')?.placeholder || null,
      visaBox: document.querySelector('[data-payment-method="visa"]')?.outerHTML?.slice(0,400) || null,
      visaVisible: (()=>{let el=document.querySelector('[data-payment-method="visa"]'); if(!el) return false; let r=el.getBoundingClientRect(); return r.width>0 && r.height>0 && el.offsetParent!==null;})(),
      cashBoxVisible: (()=>{let el=document.querySelector('[data-payment-method="cash"]'); if(!el) return false; let r=el.getBoundingClientRect(); return r.width>0&&r.height>0;})(),
      modalExists: !!document.querySelector('#successModal'),
      modalHTML: document.querySelector('#successModal')?.outerHTML?.slice(0,3000) || null,
    };
  });

  // 2. Check orders page filter
  await page.goto('http://127.0.0.1:8000/orders?sort=oldest', {waitUntil: 'domcontentloaded'});
  await page.waitForTimeout(1200);
  results.ordersOldest = await page.evaluate(() => {
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.slice(0,120));
    let select = document.querySelector('select[name="sort"]');
    return { url: location.href, sortSelect: select?.outerHTML?.slice(0,500), selectedValue: select?.value, rows: rows.slice(0,5), bodyText: document.body.innerText.slice(0,1500) };
  });
  await page.goto('http://127.0.0.1:8000/orders?sort=newest', {waitUntil: 'domcontentloaded'});
  await page.waitForTimeout(1200);
  results.ordersNewest = await page.evaluate(() => {
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.slice(0,120));
    let select = document.querySelector('select[name="sort"]');
    return { url: location.href, sortSelect: select?.outerHTML?.slice(0,500), selectedValue: select?.value, rows: rows.slice(0,5) };
  });

  // 3. Check web-setting page currency
  await page.goto('http://127.0.0.1:8000/web-setting', {waitUntil: 'domcontentloaded'});
  await page.waitForTimeout(1200);
  results.webSetting = await page.evaluate(() => {
    return {
      bodyText: document.body.innerText.slice(0,3000),
      hasShekelBadge: document.body.innerText.includes('الشيكل'),
      currencyInput: document.querySelector('input[name="currency"]')?.outerHTML?.slice(0,400),
      currencyInputValue: document.querySelector('input[name="currency"]')?.value,
      currencyNameValue: document.querySelector('input[name="currency_name"]')?.value,
      hasILS: document.body.innerText.includes('ILS'),
    };
  });
  // Try alternative route for web setting
  if (!results.webSetting.bodyText.includes('الشيكل')) {
    await page.goto('http://127.0.0.1:8000/webSetting', {waitUntil: 'domcontentloaded'});
    await page.waitForTimeout(1200);
    results.webSettingAlt = await page.evaluate(()=>({bodyText: document.body.innerText.slice(0,2000), html: document.body.innerHTML.slice(0,2000)}));
  }

  // 4. Check reports/revenues print button existence
  await page.goto('http://127.0.0.1:8000/revenues', {waitUntil: 'domcontentloaded'});
  await page.waitForTimeout(1200);
  results.revenues = await page.evaluate(()=>({bodyText: document.body.innerText.slice(0,2000), hasPrint: document.body.innerText.includes('طباعة'), printLink: document.querySelector('a[href*="print"], a[href*="pdf"]')?.outerHTML?.slice(0,400)}));

  // 5. Check success modal structure in detail
  await page.goto('http://127.0.0.1:8000/pos', {waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1000);
  // Trigger modal via JS to inspect redesign
  await page.evaluate(()=>{
    // mock order data to show modal
    window.lastOrderId = 1;
    let modal = document.getElementById('successModal');
    if(modal){
      document.getElementById('successOrderCode').textContent = 'TEST123';
      document.getElementById('successTotalAmount').textContent = '₪ 100.00';
      document.getElementById('successPaymentMethod').innerHTML = '<i class="bi bi-cash me-1"></i>كاش';
    }
  });
  await page.evaluate(()=>{ if(window.$) $('#successModal').modal('show'); });
  await page.waitForTimeout(800);
  results.modalRedesign = await page.evaluate(()=>{
    let m = document.getElementById('successModal');
    if(!m) return {exists: false};
    let r = m.getBoundingClientRect();
    return {
      exists: true,
      visible: m.classList.contains('show') || r.width>0,
      innerHTML: m.innerHTML.slice(0,4000),
      hasGradient: m.innerHTML.includes('linear-gradient'),
      hasPaymentStatusBadge: !!document.getElementById('successPaymentStatusBadge'),
      hasPrintInvoice: !!document.getElementById('printOrderInvoice'),
      hasMarkPaid: !!document.getElementById('successMarkPaid'),
      modalText: m.innerText.slice(0,1500),
    };
  });

  return results;
}
