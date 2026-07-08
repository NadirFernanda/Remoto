import { chromium } from 'playwright';
import fs from 'fs';

(async () => {
  try {
    const outDir = './public/img/pwa';
    if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

    const browser = await chromium.launch();

    // Desktop — form_factor "wide", exigido pela "richer install UI" do Chrome.
    const desktopPage = await browser.newPage({ viewport: { width: 1280, height: 800 } });
    await desktopPage.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle' });
    await desktopPage.screenshot({ path: `${outDir}/screenshot-wide.png` });
    await desktopPage.close();

    // Mobile — sem form_factor "wide" (implicitamente "narrow").
    const mobileCtx = await browser.newContext({ viewport: { width: 390, height: 844 } });
    const mobilePage = await mobileCtx.newPage();
    await mobilePage.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle' });
    await mobilePage.screenshot({ path: `${outDir}/screenshot-mobile.png` });
    await mobileCtx.close();

    await browser.close();
    console.log('PWA screenshots saved to', outDir);
  } catch (err) {
    console.error('Error taking PWA screenshots:', err);
    process.exit(1);
  }
})();
