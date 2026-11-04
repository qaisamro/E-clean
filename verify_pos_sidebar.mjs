export default async function run(page, ui){
  async function login(email, pass){
    await page.goto('http://127.0.0.1:8000/login',{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(800);
    let snap = await ui.snapshot();
    let e1 = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
    let e2 = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
    let e5 = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
    await ui.fill('@'+e1,email);
    await ui.fill('@'+e2,pass);
    await ui.click('@'+e5);
    await page.waitForTimeout(2500);
  }
  // Test driver
  await login('test@gmail.com','test123');
  let driverCheck = await page.evaluate(()=>{
    let hasPosManage = !!document.querySelector('#posManage');
    let hasPosDirect = !!Array.from(document.querySelectorAll('a.nav-link')).find(a=>a.href.includes('/pos') && !a.getAttribute('href').startsWith('#') && a.innerText.includes('نقطة البيع') || a.innerText.includes('POS'));
    let posManageLink = document.querySelector('a[href="#posManage"]');
    let sidebar = document.querySelector('#sidenav-main')?.innerText.slice(0,1500) || '';
    // Find POS link for driver
    let posLinks = Array.from(document.querySelectorAll('#sidenav-main a')).filter(a=>a.href.includes('/pos')).map(a=>({text:a.innerText.trim(), href:a.getAttribute('href'), isCollapse: a.getAttribute('href')=== '#posManage'}));
    return {hasPosManage, posLinks, hasPosDirect, hasPosManageLink: !!posManageLink, sidebar: sidebar.slice(0,1200)};
  });
  // Logout via POST fetch
  await page.evaluate(async()=>{
    let token = document.querySelector('meta[name="csrf-token"]')?.content;
    await fetch('/logout',{method:'POST', headers:{'X-CSRF-TOKEN': token, 'Content-Type':'application/x-www-form-urlencoded'}, body:'_token='+encodeURIComponent(token), credentials:'same-origin'});
  });
  await page.waitForTimeout(1000);
  // Test admin
  await login('qais@gmail.com','admin12345');
  let adminCheck = await page.evaluate(()=>{
    let hasPosManage = !!document.querySelector('#posManage');
    let posManageLink = document.querySelector('a[href="#posManage"]');
    let sidebar = document.querySelector('#sidenav-main')?.innerText.slice(0,1500) || '';
    let posLinks = Array.from(document.querySelectorAll('#sidenav-main a')).filter(a=>a.href.includes('/pos')).map(a=>({text:a.innerText.trim(), href:a.getAttribute('href'), isCollapse: a.getAttribute('href')=== '#posManage'}));
    return {hasPosManage, posLinks, hasPosManageLink: !!posManageLink, sidebar: sidebar.slice(0,1200)};
  });
  return {driverCheck, adminCheck};
}
