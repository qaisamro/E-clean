export default async function run(page, ui){
  await page.waitForTimeout(800);
  let snap = await ui.snapshot();
  let emailRef = (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let passRef = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let loginRef = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  if(!emailRef) return {error:'login not found', snap: snap.slice(0,1500)};
  await ui.fill('@'+emailRef, 'qais@gmail.com');
  await ui.fill('@'+passRef, 'admin12345');
  await ui.click('@'+loginRef);
  await page.waitForTimeout(2500);

  await page.goto('http://127.0.0.1:8000/variants',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let before = await page.evaluate(()=>{
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.trim().slice(0,120));
    let html = document.body.innerHTML.slice(0,2000);
    return {rows, count: rows.length, hasAddButton: !!document.querySelector('button[data-target="#addNew"]'), htmlSnippet: html.slice(0,1500)};
  });

  // Try to open add modal and submit
  try{
    await page.click('button[data-target="#addNew"]');
  }catch(e){
    // fallback via ui
    let snap2 = await ui.snapshot();
    let addRef = (snap2.match(/@(e\d+) button "إضافة جديد المتغير"/)||[])[1] || (snap2.match(/@(e\d+) button "Add_New.*Variant"/)||[])[1];
    if(addRef) await ui.click('@'+addRef);
  }
  await page.waitForTimeout(1000);
  // Fill form
  await page.fill('form[action*="/variants"] input[name="name"]', 'TestVariant_' + Date.now().toString().slice(-5));
  await page.fill('form[action*="/variants"] input[name="name_bn"]', 'اختبار');
  await page.fill('form[action*="/variants"] input[name="position"]', '99');
  // Intercept request
  let postResult = null;
  page.on('response', async (resp)=>{
    if(resp.url().includes('/variants') && resp.request().method()==='POST'){
      postResult = {url: resp.url(), status: resp.status(), text: (await resp.text()).slice(0,2000)};
    }
  });
  // Submit
  await page.click('form[action*="/variants"] button[type="submit"]');
  await page.waitForTimeout(2500);
  // Check after
  let after = await page.evaluate(()=>{
    let rows = Array.from(document.querySelectorAll('table tbody tr')).map(tr=>tr.innerText.trim().slice(0,120));
    let html = document.body.innerHTML.slice(0,3000);
    let hasToast = document.body.innerText.includes('Demo Mode') || document.body.innerText.includes('تمت إضافة');
    return {rows, count: rows.length, hasToast, htmlSnippet: html.slice(0,2000), bodyText: document.body.innerText.slice(0,2000)};
  });
  // Also check network via evaluate fetch? Use page.waitForResponse
  return {before, after, postResult};
}
