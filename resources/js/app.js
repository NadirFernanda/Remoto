import './bootstrap';

/**
 * Shared interaction layer for authenticated and public screens.
 * Uses delegation so new Blade/Livewire content receives the same UX without
 * adding page-specific scripts.
 */
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

const markPageReady = () => {
    document.documentElement.classList.add('ui-ready');
};

const addRipple = (event) => {
    if (reducedMotion.matches) return;

    const target = event.target.closest('button, a');
    if (!target || target.matches('[disabled], [aria-disabled="true"], [data-no-ripple]')) return;
    if (target.closest('[data-no-ripple]')) return;

    const rect = target.getBoundingClientRect();
    const ripple = document.createElement('span');
    const size = Math.max(rect.width, rect.height);

    ripple.className = 'ui-ripple';
    ripple.style.width = `${size}px`;
    ripple.style.height = `${size}px`;
    ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${event.clientY - rect.top - size / 2}px`;

    target.classList.add('ui-ripple-host');
    target.appendChild(ripple);
    window.setTimeout(() => ripple.remove(), 520);
};

const markLoading = (event) => {
    const control = event.target.closest('button[type="submit"], input[type="submit"]');
    if (!control || control.disabled || control.closest('[data-no-loading-state]')) return;

    control.classList.add('is-interacting');
    control.setAttribute('aria-busy', 'true');
    window.setTimeout(() => {
        control.classList.remove('is-interacting');
        control.removeAttribute('aria-busy');
    }, 1800);
};

const markTouchInteraction = (event) => {
    const target = event.target.closest('button, a');
    if (!target || target.matches('[disabled], [aria-disabled="true"]')) return;

    target.classList.add('ui-touch-active');
    window.setTimeout(() => target.classList.remove('ui-touch-active'), 180);
};

const showNavigationProgress = (event) => {
    const link = event.target.closest('a[href]');
    if (!link || link.target === '_blank' || link.hasAttribute('download')) return;
    if (link.origin !== window.location.origin || link.getAttribute('href').startsWith('#')) return;
    if (link.closest('[wire\\:ignore], [data-no-page-progress]')) return;
    document.documentElement.classList.add('is-navigating');
    window.setTimeout(() => document.documentElement.classList.remove('is-navigating'), 4000);
};

document.addEventListener('DOMContentLoaded', markPageReady, { once: true });
document.addEventListener('pointerdown', addRipple, { passive: true });
document.addEventListener('pointerdown', markTouchInteraction, { passive: true });
document.addEventListener('submit', markLoading, true);
document.addEventListener('click', showNavigationProgress, true);
window.addEventListener('pageshow', () => {
    document.documentElement.classList.remove('is-navigating');
    markPageReady();
});
