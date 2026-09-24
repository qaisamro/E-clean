export default async function run(page) {
  await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'domcontentloaded' });
  await page.fill('input[name=email]', 'qais@gmail.com');
  await page.fill('input[name=password]', 'admin12345');
  await page.click('button[type=submit], form button, .btn-primary');
  await page.waitForTimeout(1500);

  const out = [];
  for (const path of ['/orders', '/']) {
    const resources = [];
    const t0 = Date.now();
    page.on('request', r => {
      if (!r.url().startsWith('http://127.0.0.1')) resources.push({ url: r.url().slice(0, 120), start: Date.now() - t0 });
    });
    const pageStart = Date.now();
    await page.goto('http://127.0.0.1:8000' + path, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(3000);
    const r = await page.evaluate(() => performance.getEntriesByType('resource').map(e => ({ n: e.name.slice(0, 130), d: Math.round(e.duration), t: Math.round(e.startTime) })).sort((a,b)=>b.d-a.d).slice(0, 18));
    out.push({ path, pageMs: Date.now() - pageStart, slowest: r });
  }
  return out;
}