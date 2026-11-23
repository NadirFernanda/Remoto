import { readFileSync } from 'node:fs';
import { chromium, devices } from 'playwright';

const manifest = JSON.parse(readFileSync(new URL('../public/build/manifest.json', import.meta.url), 'utf8'));
const css = readFileSync(new URL(`../public/build/${manifest['resources/css/app.css'].file}`, import.meta.url), 'utf8');
const cases = [
    ['KYC pendente', 'bg-yellow-100 text-yellow-700'],
    ['KYC verificado', 'bg-green-100 text-green-700'],
    ['KYC rejeitado', 'bg-red-100 text-red-600'],
    ['KYC em análise', 'bg-yellow-50 text-yellow-800'],
    ['KYC erro', 'bg-red-50 text-red-700'],
    ['KYC sucesso', 'bg-green-50 text-green-700'],
    ['KYC ficheiro', 'bg-sky-50 text-sky-700'],
    ['KYC informação', 'bg-blue-50 text-blue-700'],
    ['Aviso âmbar', 'bg-amber-50 text-amber-700'],
    ['Aviso esmeralda', 'bg-emerald-50 text-emerald-700'],
    ['Aviso azul', 'bg-blue-100 text-blue-800'],
    ['Aviso céu', 'bg-sky-100 text-sky-800'],
];
const publicCases = [
    ['Suporte sucesso', 'bg-green-50 text-green-800'],
    ['Suporte erro', 'bg-red-50 text-red-700'],
    ['Histórico positivo', 'bg-emerald-50 text-emerald-700'],
    ['Histórico negativo', 'bg-red-50 text-red-700'],
    ['Perfil indisponível', 'bg-amber-50 text-amber-700'],
];

const browser = await chromium.launch();
const page = await browser.newPage({ ...devices['iPhone 13'] });

try {
    await page.setContent(`<html><head><style>${css}</style></head><body class="site-theme" style="background:#080d1a"><main class="dash-main workspace-layout" style="background:#0d1424">${cases.map(([name, classes], index) => `<div id="case-${index}" class="${classes} border rounded-xl p-4 mb-3">${name}</div>`).join('')}<div class="admin-light-modal bg-white p-4"><div id="light-modal" class="bg-blue-50 text-blue-700 border rounded-xl p-4">Aviso no modal claro</div></div></main><section class="status-dark-surface" style="background:#080d1a">${publicCases.map(([name, classes], index) => `<div id="public-${index}" class="${classes} border rounded-xl p-4 mb-3">${name}</div>`).join('')}</section></body></html>`);

    const results = await page.locator('[id^="case-"], #light-modal, [id^="public-"]').evaluateAll((elements) => {
        const rgba = (value) => (value.match(/[\d.]+/g) ?? []).map(Number);
        const luminance = (channels) => channels.slice(0, 3).map((channel) => {
            const value = channel / 255;
            return value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4;
        }).reduce((total, channel, index) => total + channel * [.2126, .7152, .0722][index], 0);

        return elements.map((element) => {
            const layers = [];
            for (let node = element; node; node = node.parentElement) layers.push(rgba(getComputedStyle(node).backgroundColor));
            let background = [255, 255, 255];
            for (const [red, green, blue, alpha = 1] of layers.reverse()) {
                if (red === undefined) continue;
                background = [red, green, blue].map((channel, index) => channel * alpha + background[index] * (1 - alpha));
            }
            const foregroundLuminance = luminance(rgba(getComputedStyle(element).color));
            const backgroundLuminance = luminance(background);
            const [lighter, darker] = [foregroundLuminance, backgroundLuminance].sort((a, b) => b - a);
            return {
                name: element.textContent,
                contrast: (lighter + .05) / (darker + .05),
                backgroundLuminance,
                modal: element.id === 'light-modal',
            };
        });
    });

    for (const result of results) {
        if (result.contrast < 4.5 || result.modal && result.backgroundLuminance < .7 || !result.modal && result.backgroundLuminance > .25) {
            throw new Error(`${result.name}: ${JSON.stringify(result)}`);
        }
        console.log(`${result.name}: contraste ${result.contrast.toFixed(1)}:1`);
    }
} finally {
    await browser.close();
}
