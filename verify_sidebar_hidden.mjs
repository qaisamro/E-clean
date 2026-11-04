export default async function run(page, ui){
  await page.waitForTimeout(600);
  let snap = await ui.snapshot();
  let emailRef = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let passRef = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let loginRef = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  if(!emailRef) return {error:'login not found', snap: snap.slice(0,1500)};
  await ui.fill('@'+emailRef, 'qais@gmail.com');
  await ui.fill('@'+passRef, 'admin12345');
  await ui.click('@'+loginRef);
  await page.waitForTimeout(2500);
  const sidebarText = await page.evaluate(()=>{
    let nav = document.querySelector('#sidenav-main');
    return nav ? nav.innerText : document.body.innerText.slice(0,4000);
  });
  const checks = {
    hasContacts: sidebarText.includes('جهات الاتصال') || sidebarText.includes('Contacts'),
    hasWebSetting: sidebarText.includes('إعدادات الموقع') || sidebarText.includes('Website Setting') || sidebarText.includes('Web_Setting'),
    hasFaq: sidebarText.includes('الاسئلة الشائعة') || sidebarText.includes('Faq') || sidebarText.includes('الأسئلة'),
    hasSettings: sidebarText.includes('الإعدادات') || sidebarText.includes('Settings'),
    hasLanguage: sidebarText.includes('اللغة') || sidebarText.includes('language'),
    hasDashboard: sidebarText.includes('لوحة التحكم') || sidebarText.includes('Dashboard'),
    hasOrders: sidebarText.includes('الطلبات') || sidebarText.includes('Orders'),
    fullSidebar: sidebarText.slice(0,3000)
  };
  return checks;
}
