import { chromium, devices } from 'playwright';
import fs from 'fs';

(async () => {
  try {
    const screenshotsDir = './screenshots';
    if (!fs.existsSync(screenshotsDir)) fs.mkdirSync(screenshotsDir);

    const browser = await chromium.launch();

    // Desktop
    const page = await browser.newPage({ viewport: { width: 1200, height: 800 } });
    await page.goto('http://127.0.0.1:8000/projetos', { waitUntil: 'networkidle' });
    await page.screenshot({ path: `${screenshotsDir}/projetos-desktop.png`, fullPage: true });
    await page.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle' });
    await page.screenshot({ path: `${screenshotsDir}/home-desktop.png`, fullPage: true });

    // Mobile (iPhone 13 emulação)
    const iPhone = devices['iPhone 13'];
    const mobileCtx = await browser.newContext({ ...iPhone });
    const mobilePage = await mobileCtx.newPage();
    await mobilePage.goto('http://127.0.0.1:8000/projetos', { waitUntil: 'networkidle' });
    await mobilePage.screenshot({ path: `${screenshotsDir}/projetos-mobile.png`, fullPage: true });
    await mobilePage.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle' });
    await mobilePage.screenshot({ path: `${screenshotsDir}/home-mobile.png`, fullPage: true });

    await browser.close();
    console.log('Screenshots saved to', screenshotsDir);
  } catch (err) {
    console.error('Error taking screenshots:', err);
    process.exit(1);
  }
})();
