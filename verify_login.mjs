export default async function run(page, ui){
  const text = await page.evaluate(()=>document.body.innerText);
  const html = await page.evaluate(()=>document.body.innerHTML.slice(0,4000));
  return {
    hasTerms: text.includes('الشروط') || text.includes('Terms') || text.includes('سياسة الخصوصية') || text.includes('Privacy'),
    hasTermsLink: html.includes('web.terms') || html.includes('web.privacy') || html.includes('terms_conditions'),
    bodySnippet: text.slice(0,1500),
    htmlSnippet: html.slice(0,2000)
  };
}
