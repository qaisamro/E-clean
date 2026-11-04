export default async function run(page, ui) {
  await page.waitForTimeout(1000);
  await page.locator("input[type=text], input[type=email], input[name=email]").first().fill("0592925567@admin.iyad");
  await page.locator("input[type=password]").fill("admin12345");
  await page.locator("button[type=submit]").click();
  try { await page.waitForNavigation({ waitUntil: "domcontentloaded", timeout: 10000 }); } catch(e){}
  await page.waitForTimeout(2000);
  const r = await page.goto("https://elitecleanps.com/orders/16/print-invoice", { waitUntil: "domcontentloaded", timeout: 15000 });
  await page.waitForTimeout(2000);
  return {
    after: page.url(),
    title: await page.title(),
    ct: r ? r.headers()["content-type"] : null,
    bodyText: (await page.evaluate(() => document.body ? document.body.innerText.slice(0, 80) : ""))
  };
}