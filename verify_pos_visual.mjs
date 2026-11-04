export default async function run(page, ui) {
  await page.goto('http://127.0.0.1:8000/login');
  await page.waitForTimeout(1000);
  let snap = await ui.snapshot();
  const email = snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)?.[1];
  const pass = snap.match(/@(e\d+) textbox "كلمة المرور"/)?.[1];
  const btn = snap.match(/@(e\d+) button "تسجيل الدخول"/)?.[1];
  await ui.fill(email, 'qais@gmail.com');
  await ui.fill(pass, 'admin12345');
  await ui.click(btn);
  await page.waitForTimeout(2000);

  await page.goto('http://127.0.0.1:8000/pos');
  await page.waitForTimeout(3000);
  const info = await page.evaluate(() => {
    const dt = document.querySelector('#deliveryDateInput');
    const visaBox = document.querySelector('[data-payment-method="visa"]');
    const cashBox = document.querySelector('[data-payment-method="cash"]');
    return {
      deliveryDateVisible: dt ? (dt.offsetParent !== null) : null,
      visaBoxVisible: visaBox ? (visaBox.offsetParent !== null) : null,
      cashBoxVisible: cashBox ? (cashBox.offsetParent !== null) : null,
      basketHtmlPresent: !!document.querySelector('#basketProducts'),
      bodyScrollHeight: document.body.scrollHeight,
      bodyClientHeight: document.body.clientHeight,
      hasWebView: !!document.querySelector('#webView'),
      hasClientPaymentBox: !!document.querySelector('.client_payment_box')
    };
  });
  await page.screenshot({ path: 'D:/Temp/opencode/pos_full.png', fullPage: true });
  return info;
}