export default async function run(page, ui){
  await page.waitForTimeout(800);
  let snap = await ui.snapshot();
  let emailRef = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let passRef = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let loginRef = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  if(!emailRef) return {error:'login not found'};
  await ui.fill('@'+emailRef, 'hammam@gmail.com');
  await ui.fill('@'+passRef, 'driver123');
  await ui.click('@'+loginRef);
  await page.waitForTimeout(2500);

  // Check dashboard income
  let dash = await page.evaluate(()=>{
    return {
      url: location.href,
      body: document.body.innerText.slice(0,3000),
      incomeText: document.body.innerText.includes('الدخل') ? document.body.innerText.slice(document.body.innerText.indexOf('الدخل')-20, document.body.innerText.indexOf('الدخل')+200) : null,
      hasIncome: document.body.innerText.includes('30 ₪') || document.body.innerText.includes('₪'),
    };
  });

  // Check sidebar for driver
  let sidebar = await page.evaluate(()=>{
    let nav = document.querySelector('#sidenav-main');
    return nav ? nav.innerText.slice(0,2500) : 'no nav';
  });

  // Go to orders
  await page.goto('http://127.0.0.1:8000/orders',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let orders = await page.evaluate(()=>{
    return {
      body: document.body.innerText.slice(0,2500),
      hasOrders: document.body.innerText.includes('الطلبات') || document.body.innerText.includes('Orders'),
      firstOrderLink: document.querySelector('a[href*="/orders/"]')?.href || null,
      count: document.querySelectorAll('table tbody tr').length
    };
  });

  // Try to open first order details
  let orderDetails = null;
  if(orders.firstOrderLink){
    await page.goto(orders.firstOrderLink,{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(1500);
    orderDetails = await page.evaluate(()=>{
      return {
        body: document.body.innerText.slice(0,3500),
        hasCustomer: document.body.innerText.includes('تفاصيل العميل') || document.body.innerText.includes('Customer Details'),
        hasAddress: document.body.innerText.includes('العنوان') || document.body.innerText.includes('Address'),
        hasPrice: document.body.innerText.includes('₪')
      };
    });
  }

  // Check POS
  await page.goto('http://127.0.0.1:8000/pos',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let pos = await page.evaluate(()=>{
    return {
      body: document.body.innerText.slice(0,2500),
      hasPos: document.body.innerText.includes('نقطة البيع') || document.body.innerText.includes('POS'),
      hasProducts: !!document.querySelector('#products') || document.body.innerText.includes('Ironing'),
      hasBasket: document.body.innerText.includes('طلب السلة')
    };
  });

  // Check revenues page (should be hidden or show —)
  await page.goto('http://127.0.0.1:8000/revenues',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let revenues = await page.evaluate(()=>{
    return {
      body: document.body.innerText.slice(0,3000),
      hasTotalSales: document.body.innerText.includes('مجموع المبيعات') || document.body.innerText.includes('Total_Sales'),
      totalSalesValue: (()=>{ let m=document.body.innerText.match(/مجموع المبيعات[\s\S]{0,30}₪|Total_Sales[\s\S]{0,30}—/); return m?m[0].slice(0,80):null; })(),
      hasReportsMenu: document.body.innerText.includes('التقارير')
    };
  });

  return {dash, sidebar: sidebar.slice(0,2000), orders, orderDetails, pos, revenues};
}
