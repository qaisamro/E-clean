export default async function run(page, ui) {
  const out = {};
  await page.goto('https://elitecleanps.com/login');
  await page.waitForTimeout(2500);
  const snap = await ui.snapshot();
  const email = snap.match(/@(e\d+) textbox "Email"/)?.[1];
  const pass = snap.match(/@(e\d+) textbox "Password"/)?.[1];
  const btn = snap.match(/@(e\d+) button "Login"/)?.[1];
  if (!email || !pass || !btn) return { error: 'no login fields', snap };
  await ui.fill(email, 'qais@gmail.com');
  await ui.fill(pass, 'admin12345');
  await ui.click(btn);
  await page.waitForTimeout(4000);
  out.afterLoginUrl = page.url();

  await page.goto('https://elitecleanps.com/pos');
  await page.waitForTimeout(4000);
  out.posUrl = page.url();
  out.pos = await page.evaluate(() => {
    const dt = document.querySelector('#deliveryDateInput');
    const visa = document.querySelector('[data-payment-method="visa"]');
    const cash = document.querySelector('[data-payment-method="cash"]');
    const basket = document.querySelector('#basketProducts');
    return {
      deliveryDateInDom: !!dt,
      deliveryDateVisible: dt ? (dt.offsetParent !== null) : false,
      visaInDom: !!visa,
      visaVisible: visa ? (visa.offsetParent !== null) : false,
      cashVisible: cash ? (cash.offsetParent !== null) : false,
      basketPresent: !!basket,
      bodyText: document.body.innerText.slice(0, 400)
    };
  });
  return out;
}