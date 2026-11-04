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
  // Expand product_manage submenu if collapsed
  await page.evaluate(()=>{
    let link = document.querySelector('a[href="#product_manage"]');
    if(link && link.getAttribute('aria-expanded') !== 'true'){
      link.click();
    }
  });
  await page.waitForTimeout(800);
  // Also expand settings and faq to have more submenus visible, but main is product_manage
  const checks = await page.evaluate(()=>{
    const submenuLinks = Array.from(document.querySelectorAll('.nav-link.sub-menu'));
    const results = submenuLinks.slice(0,6).map(el=>{
      const cs = window.getComputedStyle(el);
      const icon = el.querySelector('i');
      const iconCS = icon ? window.getComputedStyle(icon) : null;
      return {
        text: el.innerText.trim().slice(0,20),
        display: cs.display,
        flexDirection: cs.flexDirection,
        justifyContent: cs.justifyContent,
        alignItems: cs.alignItems,
        textAlign: cs.textAlign,
        paddingRight: cs.paddingRight,
        paddingLeft: cs.paddingLeft,
        height: cs.height,
        minHeight: cs.minHeight,
        gap: cs.gap,
        iconWidth: iconCS ? iconCS.width : null,
        iconMinWidth: iconCS ? iconCS.minWidth : null,
        hasRowReverse: cs.flexDirection === 'row-reverse',
        isFlexEnd: cs.justifyContent === 'flex-end',
      };
    });
    // Also check container indent
    const collapseNav = document.querySelector('#product_manage .nav');
    const containerCS = collapseNav ? window.getComputedStyle(collapseNav) : null;
    return {
      count: submenuLinks.length,
      results,
      containerBorderRight: containerCS ? containerCS.borderRightWidth : null,
      containerMarginRight: containerCS ? containerCS.marginRight : null,
      rtl: document.documentElement.getAttribute('dir'),
      rtlCssLoaded: !!Array.from(document.styleSheets).find(s=>s.href && s.href.includes('rtl.css')),
      rtlHref: Array.from(document.querySelectorAll('link[href*="rtl.css"]')).map(l=>l.href)
    };
  });
  return checks;
}
