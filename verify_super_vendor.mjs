export default async function run(page, ui){
  async function login(email, pass){
    await page.goto('http://127.0.0.1:8000/login',{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(800);
    let snap = await ui.snapshot();
    let e1 = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1] || (snap.match(/@(e\d+) textbox "رقم الهاتف"/)||[])[1];
    let e2 = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
    let e5 = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
    if(!e1) return false;
    await ui.fill('@'+e1,email);
    await ui.fill('@'+e2,pass);
    await ui.click('@'+e5);
    await page.waitForTimeout(2500);
    return true;
  }
  // Super admin
  await login('0592925567','0592925567');
  let superSidebar = await page.evaluate(()=>document.querySelector('#sidenav-main')?.innerText || '');
  let superHasPos = superSidebar.includes('نقطة البيع') || superSidebar.includes('إدارة نقطة البيع');
  let superHasOrders = superSidebar.includes('الطلبات');
  let superHasProduct = superSidebar.includes('إدارة المنتجات');
  
  // Need a vendor user - create one via direct DB? For now check vendor via test@gmail.com is driver, not vendor_admin. Create a vendor_admin user for test.
  // We will just check the logic: super should NOT have those, vendor should have
  return {superSidebar: superSidebar.slice(0,1200), superHasPos, superHasOrders, superHasProduct};
}
