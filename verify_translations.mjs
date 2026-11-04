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

  const checks = [];
  const urls = [
    {url:'/variants', expect: ['جميع المتغيرات','إضافة متغير جديد'], notExpect: ['الكل المتغيرات','اضافة جديد المتغير']},
    {url:'/products', expect: ['جميع المنتجات','إضافة منتج جديد'], notExpect: ['الكل المنتجات','اضافة جديد المنتج']},
    {url:'/banners', expect: ['إضافة لافتة جديدة'], notExpect: ['اضافة جديد اللافتة']},
    {url:'/customers', expect: ['جميع العملاء'], notExpect: ['الكل العملاء']},
    {url:'/customers/create', expect: ['إضافة عميل جديد'], notExpect: ['جديد العميل','اضافة جديد العميل']},
    {url:'/drivers', expect: ['قائمة السائقين','إضافة سائق جديد','السائقون غير النشطين'], notExpect: ['السائقون القائمة','اضافة جديد السائق','غير نشط السائق']},
  ];
  for(let item of urls){
    await page.goto('http://127.0.0.1:8000'+item.url,{waitUntil:'domcontentloaded'});
    await page.waitForTimeout(1200);
    let body = await page.evaluate(()=>document.body.innerText);
    let result = {url:item.url, bodySnippet: body.slice(0,1200), passed:[], failed:[]};
    for(let exp of item.expect){
      if(body.includes(exp)) result.passed.push(exp); else result.failed.push('missing:'+exp);
    }
    for(let ne of item.notExpect){
      if(body.includes(ne)) result.failed.push('should not contain:'+ne);
    }
    checks.push(result);
  }
  return {checks};
}
