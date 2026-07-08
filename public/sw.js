// Service worker mínimo — necessário para o critério de instalabilidade PWA do Chrome.
// Não faz cache agressivo (o site já tem cache HTTP próprio); apenas garante que
// o navegador reconhece a página como instalável.

self.addEventListener('install', () => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', () => {
  // Passa tudo directamente à rede — sem cache offline por agora.
});
