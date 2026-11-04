export default async function run(page, ui) {
  const out = {};
  await page.goto('http://127.0.0.1:8000/login');
  await page.waitForTimeout(1200);
  let snap = await ui.snapshot();
  const email = snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)?.[1];
  const pass = snap.match(/@(e\d+) textbox "كلمة المرور"/)?.[1];
  const btn = snap.match(/@(e\d+) button "تسجيل الدخول"/)?.[1];
  await ui.fill(email, 'qais@gmail.com');
  await ui.fill(pass, 'admin12345');
  await ui.click(btn);
  await page.waitForTimeout(2200);

  await page.goto('http://127.0.0.1:8000/pos');
  await page.waitForTimeout(3000);

  const before = await page.evaluate(() => {
    const vis = (el) => el ? ({inDom:true, visible: el.offsetParent !== null, rect: {x: el.getBoundingClientRect().x, y: el.getBoundingClientRect().y, w: el.getBoundingClientRect().width, h: el.getBoundingClientRect().height}, display: getComputedStyle(el).display}) : ({inDom:false});
    return {
      deliveryDate: vis(document.querySelector('#deliveryDateInput')),
      visa: vis(document.querySelector('[data-payment-method="visa"]')),
      cash: vis(document.querySelector('[data-payment-method="cash"]')),
      grandTotal: vis(document.querySelector('#grandTotal')),
      basket: vis(document.querySelector('#basketProducts')),
      winW: window.innerWidth, winH: window.innerHeight,
      scrollY: window.scrollY
    };
  });

  // select a customer
  const custSnap = await ui.snapshot();
  const custSel = custSnap.match(/@(e\d+) combobox[^\n]*/)?.[1];
  out.custSelFound = custSel;
  if (custSel) {
    await page.locator(`select#customerId, select[name="customer_id"]`).first().evaluate(el => {
      el.selectedIndex = 1;
      el.dispatchEvent(new Event('change', { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    });
    await page.waitForTimeout(1500);
  }

  const after = await page.evaluate(() => {
    const vis = (el) => el ? ({inDom:true, visible: el.offsetParent !== null}) : ({inDom:false});
    return {
      deliveryDate: vis(document.querySelector('#deliveryDateInput')),
      visa: vis(document.querySelector('[data-payment-method="visa"]')),
      cash: vis(document.querySelector('[data-payment-method="cash"]')),
      addressSelectHidden: document.querySelector('#addAddressButton')?.classList.contains('d-none'),
      bodyText: document.body.innerText.slice(0,300)
    };
  });

  return { before, after };
}