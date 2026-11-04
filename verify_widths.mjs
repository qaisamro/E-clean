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
  await page.evaluate(()=>{ let l=document.querySelector('a[href="#product_manage"]'); if(l) l.click(); });
  await page.waitForTimeout(800);
  let r = await page.evaluate(()=>{
    let main = document.querySelector('.navbar-nav > .nav-item > .nav-link');
    let sub = document.querySelector('.nav-link.sub-menu');
    let collapse = document.querySelector('#product_manage');
    let innerNav = document.querySelector('#product_manage .nav');
    let sidebar = document.querySelector('#sidenav-main');
    function info(el){
      if(!el) return null;
      let cs = getComputedStyle(el);
      let rect = el.getBoundingClientRect();
      return {tag: el.tagName, cls: el.className.slice(0,60), width: Math.round(rect.width), height: Math.round(rect.height), padding: cs.padding, margin: cs.margin, display: cs.display};
    }
    return {
      sidebar: info(sidebar),
      main: info(main),
      collapse: info(collapse),
      innerNav: info(innerNav),
      sub: info(sub),
      subParent: info(sub?.parentElement),
    };
  });
  return r;
}
