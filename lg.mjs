export default async function run(page, ui) {
  await page.waitForTimeout(1000);
  const snap = await ui.snapshot();
  const email = snap.match(/@(e1) textbox/);
  const pass = snap.match(/@(e2) textbox/);
  const btn = snap.match(/@(e3) button/);
  await ui.fill('e1', '0592925567@admin.iyad');
  await ui.fill('e2', 'admin12345');
  await ui.click('e3');
  try { await page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 8000 }); } catch(e){}
  await page.waitForTimeout(2000);
  const res = await page.goto('https://elitecleanps.com/orders/5/print-invoice', { waitUntil: 'domcontentloaded' });
  return { url: page.url(), status: res ? res.status() : null, ct: res ? res.headers()['content-type'] : null };
}