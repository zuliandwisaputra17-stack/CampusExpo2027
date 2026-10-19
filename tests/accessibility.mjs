import {chromium, expect} from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';
import assert from 'node:assert/strict';
const browser=await chromium.launch({executablePath:process.env.CHROMIUM_PATH||'/usr/bin/chromium',args:['--no-sandbox']});
try{
 const context=await browser.newContext({reducedMotion:'reduce'});const page=await context.newPage();
 for(const width of [1440,390]){
  await page.setViewportSize({width,height:1000});await page.goto((process.env.TEST_BASE_URL||'http://127.0.0.1:8000')+'/',{waitUntil:'networkidle'});
  await expect(page.locator('#member-carousel-root .expo-polaroids')).toBeVisible();
  await expect(page.locator('#campus-reel-root .expo-reel__band')).toBeVisible();
  await page.evaluate(()=>document.fonts.ready);
  const result=await new AxeBuilder({page}).withTags(['wcag2a','wcag2aa','wcag21aa']).analyze();
  for(const violation of result.violations)console.log(violation.id,violation.nodes.map(n=>({target:n.target,summary:n.failureSummary})));
  assert.equal(result.violations.length,0,`Accessibility violations at ${width}px`);console.log(`PASS axe WCAG A/AA scan at ${width}px`);
 }
}finally{await browser.close();}
