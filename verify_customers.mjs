export default async function run(page, ui){
  // Test as driver test@gmail.com
  await page.waitForTimeout(700);
  let snap = await ui.snapshot();
  let e1 = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let e2 = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let e5 = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  await ui.fill('@'+e1,'test@gmail.com');
  await ui.fill('@'+e2,'test123');
  await ui.click('@'+e5);
  await page.waitForTimeout(2500);
  await page.goto('http://127.0.0.1:8000/customers',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let driverCustomers = await page.evaluate(()=>{
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.slice(0,150));
    return {count: rows.length, rows: rows.slice(0,5), body: document.body.innerText.slice(0,2500)};
  });
  // Try to access a customer not assigned to driver (e.g., customer 5 with id maybe 5)
  // Find a customer id not in driver's assigned list: we know driver has customers 1 and 8, try customer 5
  let notAssignedAccess = await page.evaluate(async()=>{
    try{
      let r = await fetch('/customers/5',{method:'GET'});
      return {status: r.status, text: (await r.text()).slice(0,600)};
    }catch(e){ return {error: e.message}; }
  });

  // Now test as admin qais@gmail.com
  await page.goto('http://127.0.0.1:8000/logout',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1000);
  // Use form POST for logout? The logout is POST, but we can just goto login and login again
  await page.goto('http://127.0.0.1:8000/login',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1000);
  snap = await ui.snapshot();
  e1 = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  e2 = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  e5 = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  await ui.fill('@'+e1,'qais@gmail.com');
  await ui.fill('@'+e2,'admin12345');
  await ui.click('@'+e5);
  await page.waitForTimeout(2500);
  await page.goto('http://127.0.0.1:8000/customers',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let adminCustomers = await page.evaluate(()=>{
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.slice(0,150));
    return {count: rows.length, body: document.body.innerText.slice(0,1500)};
  });
  await page.goto('http://127.0.0.1:8000/orders',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let adminOrders = await page.evaluate(()=>{
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.slice(0,120));
    return {count: rows.length, totalText: document.body.innerText.slice(document.body.innerText.indexOf('إظهار'), document.body.innerText.indexOf('إظهار')+80)};
  });

  // Check sidebar POS for driver vs admin
  await page.goto('http://127.0.0.1:8000/',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1000);
  let adminSidebar = await page.evaluate(()=> document.querySelector('#sidenav-main')?.innerText.slice(0,1500));

  // Check driver sidebar again by logging back as driver
  await page.goto('http://127.0.0.1:8000/logout',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(800);
  await page.goto('http://127.0.0.1:8000/login',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(800);
  snap = await ui.snapshot();
  e1 = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  e2 = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  e5 = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  await ui.fill('@'+e1,'test@gmail.com');
  await ui.fill('@'+e2,'test123');
  await ui.click('@'+e5);
  await page.waitForTimeout(2500);
  await page.evaluate(()=>{ let link=document.querySelector('a[href="#posManage"]'); if(link) link.click(); });
  await page.waitForTimeout(1000);
  let driverPosMenu = await page.evaluate(()=>{
    let posManage = document.querySelector('#posManage');
    return posManage ? posManage.innerText.slice(0,500) : 'no posManage';
  });

  return {driverCustomers, notAssignedAccess, adminCustomers, adminOrders, adminSidebar: adminSidebar?.slice(0,1000), driverPosMenu};
}
