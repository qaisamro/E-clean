export default async function run(page, ui){
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
    return {count: rows.length, rows: rows.slice(0,5), body: document.body.innerText.slice(0,2000)};
  });
  let canAccessOther = await page.evaluate(async()=>{
    let r = await fetch('/customers/5',{method:'GET'});
    return {status: r.status, is403: r.status===403};
  });
  let posMenu = await page.evaluate(async()=>{
    // expand posManage
    let link=document.querySelector('a[href="#posManage"]');
    if(link) link.click();
    await new Promise(r=>setTimeout(r,800));
    let posManage = document.querySelector('#posManage');
    return posManage ? posManage.innerText : 'no';
  });
  return {driverCustomers, canAccessOther, posMenu};
}
