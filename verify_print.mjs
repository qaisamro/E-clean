export default async function run(page, ui) {
  await page.waitForTimeout(500);
  try {
    await page.locator('input[type="email"], input[name="email"]').first().fill('qais@gmail.com', {timeout: 5000});
  } catch(e) { return {error:'login fill fail', msg:e.message}; }
  await page.locator('input[type="password"], input[name="password"]').first().fill('admin12345');
  await page.getByRole('button', {name:'تسجيل الدخول'}).click();
  await page.waitForTimeout(2000);

  const tests = await page.evaluate(async () => {
    const results = [];
    const urls = [
      '/orders/1/print-invoice',
      '/orders/1/print-labels?quantity=5',
      '/revenues/pdf?type=month',
      '/reports/pdf?from=2026-08-01&to=2026-09-19'
    ];
    for (let url of urls) {
      try {
        let res = await fetch(url, {method:'GET', credentials:'same-origin'});
        let text = await res.text();
        let isPdf = res.headers.get('content-type')?.includes('pdf');
        let isHtmlError = text.includes('HTTP ERROR 500') || text.includes('Server Error') || text.includes('Fatal error') || text.includes('Allowed memory');
        results.push({url, status: res.status, contentType: res.headers.get('content-type'), isPdf, isHtmlError, snippet: text.slice(0,600), ok: res.ok && !isHtmlError});
      } catch(e) {
        results.push({url, error: e.message});
      }
    }
    return results;
  });
  return {tests};
}
