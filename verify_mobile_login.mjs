export default async function run(page, ui){
  await page.waitForTimeout(800);
  let snap = await ui.snapshot();
  // Check login form placeholder is now phone
  let hasPhonePlaceholder = snap.includes('رقم الهاتف') || snap.includes('رقم الجوال');
  let hasEmailPlaceholder = snap.includes('البريد الإلكتروني') && !hasPhonePlaceholder;
  // Try login with mobile
  let e1 = (snap.match(/@(e\d+) textbox ".*"/)||[])[1];
  // More precise: find email textbox
  let emailRef = (snap.match(/@(e\d+) textbox "رقم الهاتف"/)||[])[1] || (snap.match(/@(e\d+) textbox "البريد الإلكتروني"/)||[])[1];
  let passRef = (snap.match(/@(e\d+) textbox "كلمة المرور"/)||[])[1];
  let loginRef = (snap.match(/@(e\d+) button "تسجيل الدخول"/)||[])[1];
  if(!emailRef) return {error:'email ref not found', snap: snap.slice(0,1500), hasPhonePlaceholder, hasEmailPlaceholder};
  await ui.fill('@'+emailRef, '0592925567');
  await ui.fill('@'+passRef, '0592925567');
  await ui.click('@'+loginRef);
  await page.waitForTimeout(3000);
  let after = await page.evaluate(()=>({url: location.href, body: document.body.innerText.slice(0,2000), title: document.title}));
  // Try profile change-password GET
  await page.goto('http://127.0.0.1:8000/profile/change-password',{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(1500);
  let profilePage = await page.evaluate(()=>({url: location.href, status: document.body.innerText.slice(0,1500), hasForm: !!document.querySelector('form[action*="change-password"]')}));
  // Check login form HTML
  let loginHtml = await page.evaluate(()=> document.documentElement.outerHTML.slice(0,3000));
  return {hasPhonePlaceholder, hasEmailPlaceholder, after, profilePage, loginHtmlSnippet: loginHtml.slice(0,1500)};
}
