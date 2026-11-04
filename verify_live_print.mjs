export default async function run(page, ui) {
  const out = {};
  await page.waitForTimeout(1500);
  const snap = await ui.snapshot();
  const m = snap.match(/@(e\d+) textbox ["'](رقم الهاتف|Email|البريد|البريد الإلكتروني)["']/);
  const p = snap.match(/@(e\d+) textbox ["'](كلمة المرور|Password)["']/);
  const b = snap.match(/@(e\d+) button ["'](تسجيل الدخول|Login)["']/);
  if (!m || !p || !b) return { error: 'no login fields', snapshot: snap.slice(0, 800) };
  await ui.fill(m[1], '0592925567@admin.iyad');
  await ui.fill(p[1], 'admin12345');
  await ui.click(b[1]);
  try { await page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 10000 }); } catch(e){}
  await page.waitForTimeout(2500);
  out.afterLoginUrl = page.url();
  const res = await page.goto('https://elitecleanps.com/orders/16/print-invoice', { waitUntil: 'domcontentloaded' });
  out.printStatus = res ? res.status() : null;
  out.printContentType = res ? res.headers()['content-type'] : null;
  out.printUrl = page.url();
  await page.waitForTimeout(2500);
  out.bodyChars = await page.evaluate(() => document.body ? document.body.innerText.length : 0);
  out.bodySnippet = await page.evaluate(() => document.body ? document.body.innerText.slice(0, 120) : '');
  return out;
}