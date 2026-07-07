const viewLogin = document.getElementById('view-login');
const viewMain = document.getElementById('view-main');
const loginForm = document.getElementById('login-form');
const loginError = document.getElementById('login-error');
const loginSubmit = document.getElementById('login-submit');
const settingsBtn = document.getElementById('settings-btn');
const logoutBtn = document.getElementById('logout-btn');

function normalizeSiteUrl(raw) {
  let url = raw.trim().replace(/\/+$/, '');
  if (!/^https?:\/\//i.test(url)) url = `https://${url}`;
  return url;
}

function requestOrigin(siteUrl) {
  return new Promise((resolve) => {
    const origin = `${new URL(siteUrl).origin}/*`;
    chrome.permissions.request({ origins: [origin] }, (granted) => resolve(granted));
  });
}

function quickLinksFor(role) {
  const dashboardPath = { admin: '/admin/dashboard', freelancer: '/freelancer/dashboard' }[role] || '/cliente/dashboard';
  const links = [
    { label: '📊 Painel', path: dashboardPath },
    { label: '💬 Mensagens', path: '/mensagens' },
  ];

  if (role === 'freelancer') {
    links.push({ label: '🔔 Notificações', path: '/freelancer/notificacoes' });
    links.push({ label: '💰 Carteira', path: '/freelancer/carteira' });
  } else if (role === 'admin') {
    links.push({ label: '💰 Financeiro', path: '/admin/financeiro' });
  } else {
    links.push({ label: '🔔 Notificações', path: '/notificacoes' });
    links.push({ label: '💰 Financeiro', path: '/cliente/financeiro' });
  }

  return links;
}

function renderMain({ siteUrl, user, badges }) {
  viewLogin.hidden = true;
  viewMain.hidden = false;

  document.getElementById('user-initial').textContent = (user.name || '?').trim().charAt(0).toUpperCase();
  document.getElementById('user-name').textContent = user.name || '';
  document.getElementById('user-role').textContent = user.role || '';
  document.getElementById('chat-count').textContent = badges?.chat_unread ?? 0;
  document.getElementById('notif-count').textContent = badges?.notifications_unread ?? 0;

  const quickLinks = document.getElementById('quick-links');
  quickLinks.innerHTML = '';
  for (const link of quickLinksFor(user.role)) {
    const a = document.createElement('a');
    a.className = 'quick-link';
    a.href = `${siteUrl}${link.path}`;
    a.target = '_blank';
    a.rel = 'noopener';
    a.textContent = link.label;
    quickLinks.appendChild(a);
  }
}

function renderLogin(prefillUrl) {
  viewLogin.hidden = false;
  viewMain.hidden = true;
  if (prefillUrl) document.getElementById('site-url').value = prefillUrl;
}

async function fetchBadges(siteUrl, token) {
  try {
    const response = await fetch(`${siteUrl}/api/v1/badges`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    if (!response.ok) return null;
    return await response.json();
  } catch {
    return null;
  }
}

async function init() {
  const { siteUrl, token, user } = await chrome.storage.local.get(['siteUrl', 'token', 'user']);
  if (!siteUrl || !token || !user) {
    renderLogin(siteUrl);
    return;
  }

  const badges = await fetchBadges(siteUrl, token);
  if (badges === null) {
    // token pode ter expirado — pede novo login, mantendo o endereço do site.
    await chrome.storage.local.remove(['token', 'user']);
    renderLogin(siteUrl);
    return;
  }

  renderMain({ siteUrl, user, badges });
  chrome.runtime.sendMessage({ type: 'refresh-badge' });
}

loginForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  loginError.hidden = true;
  loginSubmit.disabled = true;
  loginSubmit.textContent = 'A entrar…';

  const siteUrl = normalizeSiteUrl(document.getElementById('site-url').value);
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  try {
    const granted = await requestOrigin(siteUrl);
    if (!granted) {
      throw new Error('É necessário autorizar o acesso ao site para continuar.');
    }

    const response = await fetch(`${siteUrl}/api/v1/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ email, password }),
    });

    if (!response.ok) {
      const body = await response.json().catch(() => ({}));
      throw new Error(body?.errors?.email?.[0] || body?.message || 'Credenciais inválidas.');
    }

    const data = await response.json();
    await chrome.storage.local.set({ siteUrl, token: data.token, user: data.user });

    const badges = await fetchBadges(siteUrl, data.token);
    renderMain({ siteUrl, user: data.user, badges });
    chrome.runtime.sendMessage({ type: 'refresh-badge' });
  } catch (error) {
    loginError.textContent = error.message || 'Não foi possível iniciar sessão.';
    loginError.hidden = false;
  } finally {
    loginSubmit.disabled = false;
    loginSubmit.textContent = 'Entrar';
  }
});

logoutBtn.addEventListener('click', async () => {
  const { siteUrl, token } = await chrome.storage.local.get(['siteUrl', 'token']);
  if (siteUrl && token) {
    fetch(`${siteUrl}/api/v1/auth/logout`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    }).catch(() => {});
  }
  await chrome.storage.local.remove(['token', 'user']);
  chrome.runtime.sendMessage({ type: 'clear-badge' });
  renderLogin(siteUrl);
});

settingsBtn.addEventListener('click', () => logoutBtn.click());

init();
