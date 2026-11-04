export default async function run(page, ui){
  await page.waitForTimeout(600);
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
    function getInfo(el){
      let cs = getComputedStyle(el);
      let rect = el.getBoundingClientRect();
      return {
        text: el.innerText.trim().slice(0,15),
        width: Math.round(rect.width),
        height: Math.round(rect.height),
        padding: cs.padding,
        paddingRight: cs.paddingRight,
        paddingLeft: cs.paddingLeft,
        display: cs.display,
        flexDirection: cs.flexDirection,
        gap: cs.gap
      };
    }
    return {
      main: main? getInfo(main) : null,
      sub: sub? getInfo(sub) : null,
      mainRect: main? main.getBoundingClientRect().width : null,
      subRect: sub? sub.getBoundingClientRect().width : null
    };
  });
  return r;
}
