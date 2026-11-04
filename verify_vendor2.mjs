export default async function run(page, ui) {
  await page.goto('https://elitecleanps.com/login');
  await page.waitForTimeout(1000);
  await page.locator('input[type=text], input[type=email], input[name=email]').first().fill('0595373462');
  await page.locator('input[type=password]').fill('admin12345');
  await page.locator('button[type=submit]').click();
  try { await page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 8000 }); } catch(e){}
  await page.waitForTimeout(2000);
  await page.goto('https://elitecleanps.com/vendor/settings', { waitUntil: 'domcontentloaded', timeout: 8000 });
  await page.waitForTimeout(2000);
  return { url: page.url(), title: await page.title(), snippet: await page.evaluate(() => document.body ? document.body.innerText.slice(0, 80) : '') };
}