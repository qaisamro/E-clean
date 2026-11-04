export default async function run(page, ui){
  await page.waitForTimeout(800);
  let snap = await ui.snapshot();
  let emailRef = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let passRef = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let loginRef = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  if(!emailRef) return {error:'login not found', snap: snap.slice(0,1500)};
  await ui.fill('@'+emailRef, 'test@gmail.com');
  await ui.fill('@'+passRef, 'test123');
  await ui.click('@'+loginRef);
  await page.waitForTimeout(3000);
  let afterLogin = await page.evaluate(()=>({url: location.href, body: document.body.innerText.slice(0,3000), title: document.title}));
  // Check sidebar
  let sidebar = await page.evaluate(()=>{
    let nav = document.querySelector('#sidenav-main');
    return nav ? nav.innerText.slice(0,2500) : document.body.innerText.slice(0,2500);
  });
  // Go to orders
  await page.goto('http://127.0.0.1:8000/orders',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let orders = await page.evaluate(()=>{
    return {
      body: document.body.innerText.slice(0,2500),
      hasOrders: document.body.innerText.includes('الطلبات'),
      firstOrderLink: document.querySelector('a[href*="/orders/"]')?.href || null,
      count: document.querySelectorAll('table tbody tr').length
    };
  });
  let orderDetails = null;
  if(orders.firstOrderLink){
    await page.goto(orders.firstOrderLink,{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(1500);
    orderDetails = await page.evaluate(()=>{
      return {
        body: document.body.innerText.slice(0,3500),
        hasCustomer: document.body.innerText.includes('تفاصيل العميل'),
        hasAddress: document.body.innerText.includes('العنوان'),
        hasPrice: document.body.innerText.includes('₪')
      };
    });
  }
  // POS
  await page.goto('http://127.0.0.1:8000/pos',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let pos = await page.evaluate(()=>{
    return {
      body: document.body.innerText.slice(0,2500),
      hasPos: document.body.innerText.includes('نقطة البيع'),
      hasProducts: document.body.innerText.includes('Ironing') || !!document.querySelector('#products'),
    };
  });
  // Revenues
  await page.goto('http://127.0.0.1:8000/revenues',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let revenues = await page.evaluate(()=>{
    let body = document.body.innerText;
    return {
      body: body.slice(0,3000),
      hasTotalSalesCard: body.includes('مجموع المبيعات') || body.includes('Total_Sales'),
      totalSalesValue: (()=>{ let el=document.querySelector('.card-stats .display-3'); return el?el.innerText.slice(0,50):null; })(),
      hasReports: body.includes('التقارير'),
    };
  });
  // Dashboard income
  await page.goto('http://127.0.0.1:8000/',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let dash = await page.evaluate(()=>{
    let incomeEl = Array.from(document.querySelectorAll('.card-stats')).find(c=>c.innerText.includes('الدخل'));
    return {
      body: document.body.innerText.slice(0,3000),
      incomeCardText: incomeEl ? incomeEl.innerText.slice(0,150) : null,
    };
  });
  return {afterLogin, sidebar: sidebar.slice(0,2000), orders, orderDetails, pos, revenues, dash};
}
