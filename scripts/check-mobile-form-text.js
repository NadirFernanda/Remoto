import { readFileSync } from 'node:fs';
import { chromium, devices } from 'playwright';

const manifest = JSON.parse(readFileSync(new URL('../public/build/manifest.json', import.meta.url), 'utf8'));
const css = readFileSync(new URL(`../public/build/${manifest['resources/css/app.css'].file}`, import.meta.url), 'utf8');

function templateStyle(path) {
    const template = readFileSync(new URL(`../resources/views/${path}`, import.meta.url), 'utf8');
    return template.match(/<style>([\s\S]*?)<\/style>/)?.[1] ?? '';
}

const checks = [
    { name: 'login', template: 'auth/login.blade.php', markup: '<div class="login-left"><input class="lf-input" id="field"></div>', expected: 'dark' },
    { name: 'cadastro', template: 'livewire/auth/register-wizard.blade.php', markup: '<div class="reg-wrap"><div class="reg-left"><input class="lf-input" id="field"></div></div>', expected: 'dark' },
    { name: 'documento no cadastro', template: 'livewire/auth/register-wizard.blade.php', markup: '<div class="reg-wrap"><div class="reg-left"><input class="lf-input" id="reg-document-number"></div></div>', selector: '#reg-document-number', expected: 'dark' },
    { name: 'seleção no cadastro', template: 'livewire/auth/register-wizard.blade.php', markup: '<div class="reg-wrap"><div class="reg-left"><select class="lf-input" id="field"><option value="Texto visível">Texto visível</option></select></div></div>', expected: 'dark' },
    { name: 'formulário público escuro', markup: '<div class="pub-page"><input class="pub-input" id="field"></div>', expected: 'light' },
    { name: 'seleção pública escura', markup: '<div class="pub-page"><select class="pub-select" id="field"><option value="Texto visível">Texto visível</option></select></div>', expected: 'light' },
    { name: 'filtro público escuro', template: 'livewire/freelancer-search.blade.php', markup: '<div class="fsp-filter-card"><input class="fsp-input" id="field"></div>', expected: 'light' },
    { name: 'formulário do painel', markup: '<main class="dash-main"><div class="workspace-layout"><input class="bg-white text-gray-900" id="field"></div></main>', expected: 'light' },
    { name: 'selecção do KYC', markup: '<main class="dash-main workspace-layout"><select class="block w-full max-w-xs rounded-lg border border-gray-200 py-2 px-3" id="field"><option value="Texto visível">Texto visível</option></select></main>', expected: 'light' },
    { name: 'formulário administrativo', markup: '<main class="dash-main"><div class="admin-layout"><input class="bg-white text-gray-900" id="field"></div></main>', expected: 'light' },
    { name: 'modal administrativo claro', markup: '<main class="dash-main"><div class="admin-layout"><div class="admin-light-modal"><input class="bg-white text-gray-900" id="field"></div></div></main>', expected: 'dark' },
    { name: 'seleção administrativa clara', markup: '<main class="dash-main"><div class="admin-layout"><div class="admin-light-modal"><select id="field"><option value="Texto visível">Texto visível</option></select></div></div></main>', expected: 'dark' },
    { name: 'campo com utilitário bg-white', markup: '<input class="bg-white text-gray-900" id="field">', expected: 'light' },
    { name: 'textarea com utilitário bg-white', markup: '<textarea class="bg-white text-gray-900" id="field"></textarea>', expected: 'light' },
];

const browser = await chromium.launch();
const page = await browser.newPage({ ...devices['iPhone 13'] });

try {
    for (const check of checks) {
        const style = check.template ? templateStyle(check.template) : '';
        await page.setContent(`<html><head><style>${css}</style><style>${style}</style></head><body class="site-theme">${check.markup}</body></html>`);
        const field = page.locator(check.selector ?? '#field');
        await field.focus();
        if (await field.evaluate((element) => element.tagName === 'SELECT')) {
            await field.selectOption('Texto visível');
        } else {
            await field.fill('Texto visível');
        }

        const result = await field.evaluate((element) => {
            const readColor = (value) => (value.match(/[\d.]+/g) ?? []).map(Number);
            const textFill = getComputedStyle(element).webkitTextFillColor;
            const layers = [];
            for (let node = element; node; node = node.parentElement) {
                layers.push(readColor(getComputedStyle(node).backgroundColor));
            }
            let background = [255, 255, 255];
            for (const [red, green, blue, alpha = 1] of layers.reverse()) {
                if (red === undefined) continue;
                background = [red, green, blue].map((channel, index) =>
                    channel * alpha + background[index] * (1 - alpha));
            }
            const luminance = (channels) => {
                const linear = channels.map((channel) => {
                    const value = channel / 255;
                    return value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4;
                });
                return linear[0] * .2126 + linear[1] * .7152 + linear[2] * .0722;
            };
            const values = [luminance(readColor(textFill)), luminance(background)].sort((a, b) => b - a);
            return {
                value: element.value,
                focused: document.activeElement === element,
                textFill,
                background: background.map(Math.round),
                contrast: (values[0] + .05) / (values[1] + .05),
            };
        });

        const channels = (result.textFill.match(/\d+(?:\.\d+)?/g) ?? []).slice(0, 3).map(Number);
        const brightness = channels[0] * .2126 + channels[1] * .7152 + channels[2] * .0722;
        if (!result.focused || result.value !== 'Texto visível' || channels.length !== 3 || result.contrast < 4.5 ||
            check.expected === 'dark' && brightness > 130 || check.expected === 'light' && brightness < 160) {
            throw new Error(`${check.name}: ${JSON.stringify(result)}`);
        }

        console.log(`${check.name}: contraste ${result.contrast.toFixed(1)}:1`);
    }
} finally {
    await browser.close();
}
