export default async function run(page, ui){
  await page.waitForTimeout(800);
  let snap = await ui.snapshot();
  // try to find email and password via snapshot refs
  // snapshot has @e1 textbox "البريد الإلكتروني", @e2 textbox "كلمة المرور", @e5 button "تسجيل الدخول"
  let emailRef = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let passRef = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let loginRef = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  if(!emailRef || !passRef || !loginRef){
    return {error:'snapshot refs not found', snap: snap.slice(0,2000)};
  }
  await ui.fill('@'+emailRef, 'qais@gmail.com');
  await ui.fill('@'+passRef, 'admin12345');
  await ui.click('@'+loginRef);
  await page.waitForTimeout(2500);

  // Check drivers page
  await page.goto('http://127.0.0.1:8000/drivers',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const driversCheck = await page.evaluate(()=>{
    let html = document.body.innerHTML;
    let hasEdit = !!document.querySelector('a[href*="/drivers/"][href*="/edit"]');
    let hasDelete = !!document.querySelector('form[action*="/drivers/"] button');
    let statusForms = document.querySelectorAll('form[action*="drivers"][action*="status"]').length;
    let statusAnchorsGet = document.querySelectorAll('a[href*="drivers"][href*="status"]').length;
    return {hasEdit, hasDelete, statusForms, statusAnchorsGet, firstEdit: document.querySelector('a[href*="/edit"]')?.href?.slice(-30) || null, body: document.body.innerText.slice(0,1800)};
  });

  let editCheck = null;
  let firstEditUrl = await page.evaluate(()=>document.querySelector('a[href*="/drivers/"][href*="/edit"]')?.href);
  if(firstEditUrl){
    await page.goto(firstEditUrl,{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(1000);
    editCheck = await page.evaluate(()=>({url:location.href, hasForm: !!document.querySelector('form[action*="/drivers/"]'), body: document.body.innerText.slice(0,1500)}));
  }

  // print checks
  await page.goto('http://127.0.0.1:8000/orders',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const printCheckOrders = await page.evaluate(()=>{
    let links = Array.from(document.querySelectorAll('a[href*="print-invoice"], a[href*="print-labels"]'));
    return links.slice(0,3).map(a=>({href:a.href.slice(-40), target:a.getAttribute('target'), hasClass: a.classList.contains('print-same-page')}))
  });
  await page.goto('http://127.0.0.1:8000/revenues',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const printCheckRevenues = await page.evaluate(()=>{
    let links = Array.from(document.querySelectorAll('a[href*="pdf"]'));
    return links.map(a=>({href:a.href.slice(-40), target:a.getAttribute('target'), hasClass: a.classList.contains('print-same-page')}))
  });
  await page.goto('http://127.0.0.1:8000/pos',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  const posPrint = await page.evaluate(()=>{
    let el = document.getElementById('printOrderInvoice');
    return el ? {hasClass: el.classList.contains('print-same-page'), target: el.getAttribute('target')} : null;
  });
  const iframeExists = await page.evaluate(()=>!!document.getElementById('printFrame'));

  // Test print via fetch
  const fetchTests = await page.evaluate(async()=>{
    let urls=['/orders/1/print-invoice','/orders/1/print-labels?quantity=2'];
    let res=[];
    for(let u of urls){
      try{
        let r=await fetch(u);
        res.push({url:u, status:r.status, ct:r.headers.get('content-type'), ok:r.ok});
      }catch(e){res.push({url:u, error:e.message})}
    }
    return res;
  });

  return {driversCheck, editCheck, printCheckOrders, printCheckRevenues, posPrint, iframeExists, fetchTests};
}
