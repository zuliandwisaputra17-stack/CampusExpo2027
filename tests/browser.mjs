import {chromium, expect} from '@playwright/test';
import assert from 'node:assert/strict';
import {execFileSync} from 'node:child_process';
import {randomBytes} from 'node:crypto';
import {mkdirSync} from 'node:fs';
const base=process.env.TEST_BASE_URL||'http://127.0.0.1:8000';
const php=process.env.PHP_BINARY||'php';
const email=`browser-${Date.now()}@example.test`,password=randomBytes(24).toString('base64url');
const memberSlug=`browser-social-${Date.now()}`;
const bootstrap=`require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();`;
const phpRun=code=>execFileSync(php,['-r',bootstrap+code],{env:{...process.env,BROWSER_TEST_EMAIL:email,BROWSER_TEST_PASSWORD:password,BROWSER_TEST_MEMBER_SLUG:memberSlug},stdio:['ignore','pipe','pipe']});
phpRun(`$u=new App\\Models\\User;$u->name='Pengujian Browser';$u->email=getenv('BROWSER_TEST_EMAIL');$u->password=getenv('BROWSER_TEST_PASSWORD');$u->is_admin=true;$u->save();`);
mkdirSync('/workspace/artifacts',{recursive:true});
const browser=await chromium.launch({executablePath:process.env.CHROMIUM_PATH||'/usr/bin/chromium',args:['--no-sandbox']});
const context=await browser.newContext({viewport:{width:1440,height:1000},reducedMotion:'reduce'}),page=await context.newPage();
const errors=[];page.on('pageerror',e=>errors.push(e.message));
let checks=0;const ok=(condition,msg)=>{assert(condition,msg);checks++;console.log('PASS',msg);};
try{
 await page.goto(base,{waitUntil:'networkidle'});
 ok(await page.locator('h1').innerText().then(t=>t.includes('Masa depanmu')),'Homepage renders active event');
 const total=await page.locator('[data-member]').count();ok(total>=8,'Member directory is populated');
 await expect(page.locator('#campus-reel-root .expo-reel__band')).toBeVisible();ok(await page.locator('.expo-reel__tile').count()>1,'Curved campus reel mounts');
 await expect(page.locator('#member-carousel-root .expo-polaroids')).toBeVisible();
 await expect(page.locator('.expo-polaroids')).toHaveAttribute('data-reduced-motion','true');
 ok(await page.locator('.expo-reel__tile').evaluateAll(tiles=>tiles.every(tile=>tile.style.animationPlayState==='paused')),'Reduced motion pauses campus animation');
 ok(await page.locator('.expo-polaroids').getAttribute('data-paused')==='true','Reduced motion pauses member animation');
 await expect(page.locator('#member-directory')).toBeHidden();ok(true,'Mounted carousel replaces fallback member grid');
 await page.locator('[data-campus-name]').first().click();
 ok(await page.locator('#campus-filter').inputValue()==='universitas-indonesia','Campus card selects member filter');
 ok((await page.locator('[data-member]:not([hidden])').count())===2,'Campus selection shows only matching members');
 await expect(page.locator('.expo-polaroids__card')).toHaveCount(2);
 await expect(page.locator('.expo-polaroids__profile h3')).toHaveText('Aulia Rahma');
 await page.getByRole('button',{name:'Anggota berikutnya',exact:true}).click();
 await expect(page.locator('.expo-polaroids__profile h3')).toHaveText('Dinda Maharani');
 await expect(page.getByRole('button',{name:'Anggota berikutnya',exact:true})).toBeDisabled();
 ok(true,'Two-member carousel arrows select profiles and stop at boundary');
 await page.locator('.expo-polaroids__stage').focus();await page.keyboard.press('ArrowLeft');
 await expect(page.locator('.expo-polaroids__profile h3')).toHaveText('Aulia Rahma');ok(true,'Member carousel supports keyboard navigation');
 ok(new URL(page.url()).searchParams.get('campus')==='universitas-indonesia','Campus filter has a shareable URL');
 await page.locator('#member-search').fill('Tidak Ada Nama Ini');ok(await page.locator('#member-empty').isVisible(),'Search displays meaningful empty state');
 await expect(page.locator('#member-carousel-root')).toBeHidden();ok(true,'Zero matches hide interactive carousel');
 await page.locator('#empty-reset').click();ok(await page.locator('[data-member]:not([hidden])').count()===total,'Reset restores directory');
 await page.locator('#major-filter').selectOption({label:'Psikologi'});ok(await page.locator('[data-member]:not([hidden])').count()===1,'Major filter works');
 await expect(page.locator('.expo-polaroids__card')).toHaveCount(1);
 await expect(page.locator('.expo-polaroids__profile h3')).toHaveText('Nadia Putri');
 await expect(page.getByRole('button',{name:'Anggota sebelumnya',exact:true})).toBeDisabled();
 await expect(page.getByRole('button',{name:'Anggota berikutnya',exact:true})).toBeDisabled();ok(true,'Single-member result renders with disabled navigation');
 await page.locator('#reset-filters').click();await page.locator('#program-filter').selectOption({label:'S1 Agribisnis'});ok(await page.locator('[data-member]:not([hidden])').count()===1,'Study program filter works');await page.locator('#reset-filters').click();
 await expect(page.locator('.expo-polaroids__card')).toHaveCount(total);
 await page.locator('.expo-polaroids__stage').scrollIntoViewIfNeeded();
 const profiles=JSON.parse(await page.locator('#member-carousel-data').textContent());
 await page.getByRole('button',{name:'Anggota sebelumnya',exact:true}).click();
 await expect(page.locator('.expo-polaroids__profile h3')).toHaveText(profiles.at(-1).title);
 await page.getByRole('button',{name:'Anggota berikutnya',exact:true}).click();
 await expect(page.locator('.expo-polaroids__profile h3')).toHaveText(profiles[0].title);ok(true,'Long member line wraps in both directions');
 const stageBox=await page.locator('.expo-polaroids__stage').boundingBox();
 await page.mouse.move(stageBox.x+stageBox.width*.65,stageBox.y+130);await page.mouse.down();
 await page.mouse.move(stageBox.x+stageBox.width*.65-310,stageBox.y+130,{steps:12});await page.mouse.up();
 await expect(page.locator('.expo-polaroids__profile h3')).not.toHaveText('Aulia Rahma');ok(true,'Dragging selects another member');
 const link=page.locator('[data-member] a[target="_blank"]').first();ok((await link.getAttribute('rel')).includes('noopener'),'Official campus links have safe new-tab attributes');
 await page.goto(base+'/?campus=institut-teknologi-bandung#anggota');ok(await page.locator('[data-member]:not([hidden])').count()===2,'Shared campus URL restores filter');
 await page.locator('#reset-filters').click();await page.locator('#campus-next').click();ok(await page.locator('#campus-carousel').evaluate(e=>e.scrollLeft)>0,'Carousel next control scrolls');
 await page.locator('#campus-carousel').focus();const previous=await page.locator('#campus-carousel').evaluate(e=>e.scrollLeft);await page.keyboard.press('ArrowRight');ok(await page.locator('#campus-carousel').evaluate(e=>e.scrollLeft)>previous,'Carousel supports keyboard navigation');
 // Check user-controlled motion with animation preferences enabled.
 await page.emulateMedia({reducedMotion:'no-preference'});
 await page.locator('.campus-reel').scrollIntoViewIfNeeded();
 await expect.poll(()=>page.locator('.expo-reel__tile').first().evaluate(tile=>tile.style.animationPlayState)).toBe('running');
 await page.getByRole('button',{name:'Jeda animasi kampus',exact:true}).click();
 await expect.poll(()=>page.locator('.expo-reel__tile').first().evaluate(tile=>tile.style.animationPlayState)).toBe('paused');
 await page.getByRole('button',{name:'Putar animasi kampus',exact:true}).click();
 await expect.poll(()=>page.locator('.expo-reel__tile').first().evaluate(tile=>tile.style.animationPlayState)).toBe('running');ok(true,'Campus reel can pause and resume');
 await page.getByRole('button',{name:'Jeda animasi anggota',exact:true}).click();
 await expect(page.locator('.expo-polaroids')).toHaveAttribute('data-paused','true');
 await page.getByRole('button',{name:'Putar animasi anggota',exact:true}).click();
 await expect(page.locator('.expo-polaroids')).toHaveAttribute('data-paused','false');ok(true,'Member carousel can pause and resume');
 await page.emulateMedia({reducedMotion:'reduce'});
 const faq=page.locator('#faq details').first();await faq.locator('summary').click();ok(await faq.getAttribute('open')!==null,'FAQ accordion opens');
 await page.locator('[data-lightbox]').first().click();ok(await page.locator('#lightbox').isVisible(),'Gallery lightbox opens');await page.keyboard.press('Escape');ok(!await page.locator('#lightbox').isVisible(),'Escape closes lightbox');
 // Load lazy images before the full-page evidence capture.
 await page.goto(base);await page.locator('footer').scrollIntoViewIfNeeded();await page.evaluate(()=>{document.querySelectorAll('img[loading="lazy"]').forEach(img=>img.loading='eager');});await page.evaluate(()=>Promise.all([...document.images].map(img=>img.decode().catch(()=>{}))));await page.evaluate(()=>scrollTo(0,0));
 ok(await page.evaluate(()=>[...document.images].filter(i=>i.getAttribute("src")).every(i=>i.complete&&i.naturalWidth>0)),'All public image assets load');await page.screenshot({path:'/workspace/artifacts/home-desktop.png',fullPage:true});
 for(const width of [360,390,430]){await page.setViewportSize({width,height:844});await page.goto(base);await page.evaluate(()=>document.fonts.ready);ok(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'No horizontal overflow at '+width+'px');await page.locator('.mobile-toggle').click();ok(await page.locator('#mobile-nav').isVisible(),'Mobile navigation opens at '+width+'px');await page.locator('#mobile-nav a[href="#jadwal"]').click();ok(!await page.locator('#mobile-nav').isVisible(),'Mobile link closes menu at '+width+'px');}
 await page.setViewportSize({width:390,height:844});await page.evaluate(()=>{document.querySelectorAll('img[loading="lazy"]').forEach(img=>img.loading='eager');scrollTo(0,0);});await page.evaluate(()=>Promise.all([...document.images].map(img=>img.decode().catch(()=>{}))));await page.screenshot({path:'/workspace/artifacts/home-mobile.png',fullPage:true});
 await page.setViewportSize({width:1440,height:1000});await page.goto(base+'/admin');ok(page.url().endsWith('/admin/login'),'Guest cannot open admin');await page.screenshot({path:'/workspace/artifacts/login-desktop.png'});
 await page.getByLabel('Email panitia').fill(email);await page.getByLabel('Kata sandi',{exact:true}).fill(password);await page.getByRole('button',{name:'Masuk ke Dashboard'}).click();await page.waitForURL(base+'/admin');ok(await page.locator('.admin-welcome').isVisible(),'Admin login works with session and CSRF');await page.screenshot({path:'/workspace/artifacts/admin-desktop.png',fullPage:true});
 for(const resource of ['event-editions','campuses','members','schedules','galleries','faqs','partners','settings']){const response=await page.goto(base+'/admin/'+resource);ok(response.status()===200,'Admin '+resource+' loads');}
 await page.goto(base+'/admin/schedules/reorder');const firstOrder=await page.locator('#reorder-list li').first().locator('strong').innerText();await page.locator('#reorder-list li').first().locator('[data-move="down"]').click();ok(await page.locator('#reorder-list li').nth(1).locator('strong').innerText()===firstOrder,'Schedule ordering supports accessible move buttons');await page.locator('#reorder-list li').nth(1).locator('[data-move="up"]').click();await page.getByRole('button',{name:'Simpan Urutan'}).click();await page.waitForLoadState();ok(await page.locator('.alert.success').isVisible(),'Schedule ordering persists through CMS');
 await page.goto(base+'/admin/campuses/create');for(const [field,value] of Object.entries({name:'Kampus Browser',slug:'kampus-browser',abbreviation:'KB',city:'Jakarta',province:'DKI Jakarta',official_url:'https://example.org',display_order:'99'}))await page.locator(`[name="${field}"]`).fill(value);
 await page.locator('button[type="submit"]').click();await page.waitForURL(base+'/admin/campuses');ok(await page.locator('table').innerText().then(t=>t.includes('Kampus Browser')),'Campus creation works through admin form');
 const publicPage=await context.newPage();await publicPage.goto(base);ok(await publicPage.locator('[data-campus-name="Kampus Browser"]').count()===1,'Saved campus appears on public site');await publicPage.close();
 const row=page.locator('tr').filter({hasText:'Kampus Browser'});await row.getByRole('link',{name:'Edit'}).click();await page.locator('[name="name"]').fill('Kampus Browser Diperbarui');await page.locator('button[type="submit"]').click();await page.waitForURL(base+'/admin/campuses');ok(await page.locator('table').innerText().then(t=>t.includes('Kampus Browser Diperbarui')),'Campus edit persists');
 page.once('dialog',dialog=>dialog.accept());await page.locator('tr').filter({hasText:'Kampus Browser Diperbarui'}).getByRole('button',{name:'Hapus'}).click();await page.waitForLoadState();ok(await page.locator('table').innerText().then(t=>!t.includes('Kampus Browser Diperbarui')),'Campus deletion works through admin form');
 // Social URLs entered in the CMS must reach the visible public member profile.
 await page.goto(base+'/admin/members/create');
 for(const [field,value] of Object.entries({name:'Anggota Browser Sosial',slug:memberSlug,study_program:'S1 Desain',display_order:'99',instagram_url:'https://www.instagram.com/anggota.browser/',linkedin_url:'https://www.linkedin.com/in/anggota-browser/'}))await page.locator(`[name="${field}"]`).fill(value);
 await page.locator('[name="event_edition_id"]').selectOption({label:'Campus Expo 2027'});
 await page.locator('[name="campus_id"]').selectOption({label:'Universitas Indonesia'});
 await page.locator('button[type="submit"]').click();await page.waitForURL(base+'/admin/members');
 ok(await page.locator('table').innerText().then(t=>t.includes('Anggota Browser Sosial')),'Admin can save member Instagram and LinkedIn links');
 const socialPage=await context.newPage();await socialPage.goto(base);await socialPage.locator('#member-search').fill('Anggota Browser Sosial');
 await expect(socialPage.locator('.expo-polaroids__profile h3')).toHaveText('Anggota Browser Sosial');
 for(const [platform,url] of [['Instagram','https://www.instagram.com/anggota.browser/'],['LinkedIn','https://www.linkedin.com/in/anggota-browser/']]){
  const social=socialPage.locator('.expo-polaroids__socials a').filter({hasText:platform});
  await expect(social).toBeVisible();await expect(social).toHaveAttribute('href',url);
  ok((await social.getAttribute('rel')).includes('noopener'),'Public '+platform+' link is visible and opens safely');
 }
 await socialPage.close();
 page.once('dialog',dialog=>dialog.accept());await page.locator('tr').filter({hasText:'Anggota Browser Sosial'}).getByRole('button',{name:'Hapus'}).click();await page.waitForLoadState();
 const rejected=await context.request.post(base+'/admin/campuses',{data:{name:'No CSRF'}});ok(rejected.status()===419,'CSRF rejects mutation without token');
 await page.goto(base+'/admin');await page.getByRole('button',{name:'Keluar dari akun'}).click();await page.waitForURL(base+'/admin/login');ok(page.url().endsWith('/admin/login'),'Logout clears authenticated session');
 // Fixed clocks exercise the countdown and schedule boundary states in WIB.
 for(const [iso,state] of [['2027-01-18T00:59:59Z','upcoming'],['2027-01-18T01:00:00Z','live'],['2027-01-18T08:00:00Z','live'],['2027-01-18T08:00:01Z','finished']]){
  const timed=await context.newPage();await timed.clock.setFixedTime(new Date(iso));await timed.goto(base);
  if(state==='upcoming')ok(await timed.locator('[data-count="seconds"]').innerText()==='01','Countdown uses Asia/Jakarta offset before event');
  else ok((await timed.locator('#countdown-title').innerText()).includes(state==='live'?'sedang berlangsung':'telah selesai'),'Countdown state '+state+' at '+iso);
  await timed.close();
 }
 const fallbackContext=await browser.newContext({javaScriptEnabled:false});const fallbackPage=await fallbackContext.newPage();await fallbackPage.goto(base);
 ok(await fallbackPage.locator('[data-member]:visible').count()===total,'Member directory remains available without JavaScript');await fallbackContext.close();
 ok(errors.length===0,'No JavaScript runtime errors');console.log(`${checks} browser checks passed.`);
}finally{await browser.close();phpRun(`App\\Models\\User::where('email',getenv('BROWSER_TEST_EMAIL'))->delete();App\\Models\\Member::withTrashed()->where('slug',getenv('BROWSER_TEST_MEMBER_SLUG'))->forceDelete();$c=App\\Models\\Campus::withTrashed()->where('slug','kampus-browser')->first();if($c)$c->forceDelete();`);}
