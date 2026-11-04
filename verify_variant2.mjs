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
    let rows = Array.from(document.querySelectorAll('#myTable tbody tr')).map(tr=>tr.innerText.trim().slice(0,120));
    return {count: rows.length, rows: rows.slice(0,3)};
  });

  // Open add modal
  await page.click('button[data-target="#addNew"]');
  await page.waitForTimeout(1200);
  // Fill specifically addNew modal
  await page.fill('#addNew input[name="name"]', 'TestVariant_' + Date.now().toString().slice(-5));
  await page.fill('#addNew input[name="name_bn"]', 'اختبار');
  await page.fill('#addNew input[name="position"]', '99');
  // Capture response
  let postPromise = page.waitForResponse(resp => resp.url().includes('/variants') && resp.request().method() === 'POST', {timeout: 5000}).catch(()=>null);
  await page.click('#addNew button[type="submit"]');
  let resp = await postPromise;
  let respInfo = null;
  if(resp){
    let status = resp.status();
    let text = await resp.text().catch(()=> '');
    respInfo = {status, url: resp.url(), snippet: text.slice(0,2500), headers: resp.headers()['content-type']};
  }
  await page.waitForTimeout(2500);
  let after = await page.evaluate(()=>{
    let rows = Array.from(document.querySelectorAll('#myTable tbody tr')).map(tr=>tr.innerText.trim().slice(0,120));
    let bodyText = document.body.innerText.slice(0,3000);
    return {count: rows.length, rows: rows.slice(0,5), bodyText, hasSuccess: bodyText.includes('تمت إضافة') || bodyText.includes('Demo Mode')};
  });

  // Also try direct fetch as alternative
  let fetchTest = await page.evaluate(async()=>{
    let token = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
    let fd = new FormData();
    fd.append('_token', token);
    fd.append('name', 'FetchVariant_' + Date.now().toString().slice(-4));
    fd.append('name_bn', 'اختبار fetch');
    fd.append('position', '100');
    try{
      let r = await fetch('/variants', {method:'POST', body: fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
      let txt = await r.text();
      return {status: r.status, snippet: txt.slice(0,2000), headers: r.headers.get('content-type')};
    }catch(e){ return {error: e.message}; }
  });

  return {before, respInfo, after, fetchTest};
}
