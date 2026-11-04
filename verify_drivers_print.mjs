export default async function run(page, ui){
  await page.waitForTimeout(500);
  try{ await page.locator('input[type="email"]').first().fill('qais@gmail.com',{timeout:5000}); }catch(e){return {error:'fill email',msg:e.message};}
  await page.locator('input[type="password"]').first().fill('admin12345');
  await page.getByRole('button',{name:'تسجيل الدخول'}).click();
  await page.waitForTimeout(2000);

  // Check drivers page
  await page.goto('http://127.0.0.1:8000/drivers',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const driversCheck = await page.evaluate(()=>{
    let body = document.body.innerText;
    let html = document.body.innerHTML;
    let hasEdit = html.includes('driver.edit') || html.includes('/drivers/') && html.includes('fa-edit');
    let hasDelete = html.includes('fa-trash') || html.includes('driver.destroy');
    let statusForms = document.querySelectorAll('form[action*="drivers"][action*="status"]').length;
    let statusAnchorsGet = document.querySelectorAll('a[href*="drivers"][href*="status"]').length;
    let actionButtons = document.querySelectorAll('td .fa-eye, td .fa-edit, td .fa-trash').length;
    let firstEditLink = document.querySelector('a[href*="/drivers/"][href*="/edit"]')?.href || null;
    let firstDeleteForm = document.querySelector('form[action*="/drivers/"] button[type="submit"]')?.outerHTML?.slice(0,300) || null;
    return {
      bodySnippet: body.slice(0,2000),
      hasEdit,
      hasDelete,
      statusForms,
      statusAnchorsGet,
      actionButtons,
      firstEditLink,
      firstDeleteForm,
      htmlSnippet: html.slice(0,3000)
    };
  });

  // Try to fetch edit page for first driver
  let editCheck = null;
  if(driversCheck.firstEditLink){
    try{
      await page.goto(driversCheck.firstEditLink,{waitUntil:'domcontentloaded'});
      await page.waitForTimeout(1000);
      editCheck = await page.evaluate(()=>({url:location.href, body:document.body.innerText.slice(0,2000), hasForm: !!document.querySelector('form[action*="/drivers/"]'), inputs: Array.from(document.querySelectorAll('input[name]')).map(e=>e.name).slice(0,10)}));
    }catch(e){ editCheck = {error:e.message}; }
  }

  // Check print links same-page
  await page.goto('http://127.0.0.1:8000/orders',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const printCheckOrders = await page.evaluate(()=>{
    let links = Array.from(document.querySelectorAll('a[href*="print-invoice"], a[href*="print-labels"]'));
    return links.map(a=>({href:a.href.slice(-40), target:a.getAttribute('target'), hasClass: a.classList.contains('print-same-page'), outer: a.outerHTML.slice(0,350)}));
  });
  await page.goto('http://127.0.0.1:8000/revenues',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const printCheckRevenues = await page.evaluate(()=>{
    let links = Array.from(document.querySelectorAll('a[href*="pdf"]'));
    return links.map(a=>({href:a.href.slice(-40), target:a.getAttribute('target'), hasClass: a.classList.contains('print-same-page')}));
  });
  // Check POS modal print
  await page.goto('http://127.0.0.1:8000/pos',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const posPrint = await page.evaluate(()=>{
    let el = document.getElementById('printOrderInvoice');
    return el ? {hasClass: el.classList.contains('print-same-page'), target: el.getAttribute('target'), href: el.getAttribute('href')} : null;
  });
  // Check hidden iframe exists
  const iframeCheck = await page.evaluate(()=>!!document.getElementById('printFrame'));

  return {driversCheck, editCheck, printCheckOrders: printCheckOrders.slice(0,3), printCheckRevenues, posPrint, iframeExists: iframeCheck};
}
