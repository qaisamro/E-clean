export default async function run(page, ui){
  await page.waitForTimeout(700);
  let snap = await ui.snapshot();
  let e1 = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let e2 = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let e5 = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  await ui.fill('@'+e1,'qais@gmail.com');
  await ui.fill('@'+e2,'admin12345');
  await ui.click('@'+e5);
  await page.waitForTimeout(2500);
  await page.goto('http://127.0.0.1:8000/customers',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  let adminCustomers = await page.evaluate(()=>({count: document.querySelectorAll('table tbody tr').length, body: document.body.innerText.slice(0,1200)}));
  await page.goto('http://127.0.0.1:8000/orders',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1200);
  let adminOrders = await page.evaluate(()=>({count: document.querySelectorAll('table tbody tr').length, body: document.body.innerText.slice(document.body.innerText.indexOf('إظهار'), document.body.innerText.indexOf('إظهار')+80)}));
  let sidebar = await page.evaluate(()=>document.querySelector('#sidenav-main')?.innerText.slice(0,1200));
  await page.evaluate(()=>{ let l=document.querySelector('a[href="#posManage"]'); if(l) l.click(); });
  await page.waitForTimeout(800);
  let posMenu = await page.evaluate(()=>document.querySelector('#posManage')?.innerText.slice(0,300));
  return {adminCustomers, adminOrders, posMenu, sidebar: sidebar?.slice(0,1500)};
}


