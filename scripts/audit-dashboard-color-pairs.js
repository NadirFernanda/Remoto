import { readFileSync, readdirSync } from 'node:fs';
import { join, relative } from 'node:path';
import { chromium } from 'playwright';

function filesUnder(directory) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const path = join(directory, entry.name);
        return entry.isDirectory() ? filesUnder(path) : path.endsWith('.blade.php') ? [path] : [];
    });
}

const manifest = JSON.parse(readFileSync('public/build/manifest.json', 'utf8'));
const css = readFileSync(`public/build/${manifest['resources/css/app.css'].file}`, 'utf8');
const sources = [
    ...filesUnder('resources/views/livewire'),
    ...filesUnder('resources/views/admin'),
    'resources/views/project-detail-authenticated.blade.php',
];
const examples = new Map();

for (const file of sources) {
    if (/auth[\\/]|freelancer-search|freelancer[\\/]listing|pdf|excel|export/i.test(file)) continue;
    const source = readFileSync(file, 'utf8');
    for (const match of source.matchAll(/\bclass\s*=\s*"([^"]*)"/g)) {
        const value = match[1];
        if (value.includes('{{') || value.includes('@') || value.includes('wire:')) continue;
        const tokens = value.split(/\s+/);
        const backgrounds = tokens.filter((token) => /^bg-(?:white|black|gray|slate|neutral|red|yellow|amber|green|emerald|blue|sky)-?(?:\d{2,3})?$/.test(token) || /^bg-\[#[0-9a-fA-F]{6}\]$/.test(token));
        const foregrounds = tokens.filter((token) => /^text-(?:white|black|gray|slate|neutral|red|yellow|amber|green|emerald|blue|sky)-?(?:\d{2,3})?$/.test(token) || /^text-\[#[0-9a-fA-F]{6}\]$/.test(token));
        if (!foregrounds.length || !backgrounds.length && !tokens.includes('text-[#0055ff]')) continue;
        const classes = [...backgrounds, ...foregrounds, ...tokens.filter((token) => token === 'platform-badge')].join(' ');
        if (!examples.has(classes)) examples.set(classes, relative('.', file));
    }
}

const fixtures = [...examples].map(([classes, file], index) => ({
    classes,
    file,
    index,
    markup: file.includes('client-search') && classes.includes('bg-[#e0f7fa]')
        ? (fixtureIndex) => `<div class="rounded-2xl bg-white"><span data-audit="${fixtureIndex}" class="rounded-full ${classes}">Cliente</span></div>`
        : undefined,
}));
fixtures.push({
    classes: 'texto azul do perfil público',
    file: 'resources/views/freelancer/_profile-body.blade.php',
    index: fixtures.length,
    markup: (index) => `<div class="freelancer-profile-content"><div class="freelancer-profile-card bg-white"><span data-audit="${index}" class="text-[#0055ff]">Perfil</span></div></div>`,
});
const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 390, height: 844 } });

try {
    await page.setContent(`<html><head><style>${css}</style></head><body class="site-theme" style="background:#080d1a"><main class="dash-main workspace-layout" style="background:#0d1424">${fixtures.map(({ classes, index, markup }) => markup ? markup(index) : `<span data-audit="${index}" class="${classes}">Texto de exemplo</span>`).join('')}</main></body></html>`);
    const results = await page.locator('[data-audit]').evaluateAll((elements) => {
        const numbers = (value) => (value.match(/[\d.]+/g) ?? []).map(Number);
        const luminance = (rgb) => rgb.slice(0, 3).map((channel) => {
            const value = channel / 255;
            return value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4;
        }).reduce((sum, channel, index) => sum + channel * [.2126, .7152, .0722][index], 0);

        return elements.map((element) => {
            const layers = [];
            for (let node = element; node; node = node.parentElement) layers.push(numbers(getComputedStyle(node).backgroundColor));
            let background = [255, 255, 255];
            for (const [red, green, blue, alpha = 1] of layers.reverse()) {
                if (red === undefined) continue;
                background = [red, green, blue].map((channel, index) => channel * alpha + background[index] * (1 - alpha));
            }
            const [lighter, darker] = [luminance(numbers(getComputedStyle(element).color)), luminance(background)].sort((a, b) => b - a);
            return { index: Number(element.dataset.audit), contrast: (lighter + .05) / (darker + .05) };
        });
    });

    const failures = results.filter((result) => result.contrast < 4.5).map((result) => ({ ...fixtures[result.index], contrast: result.contrast }));
    console.log(`Combinações avaliadas: ${fixtures.length}; contraste abaixo de 4,5:1: ${failures.length}`);
    for (const failure of failures.slice(0, 60)) console.log(`${failure.contrast.toFixed(1)}:1 ${failure.classes} — ${failure.file}`);
    if (failures.length) process.exitCode = 1;
} finally {
    await browser.close();
}
