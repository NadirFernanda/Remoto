const SITE_URL = 'https://24horas.ao';

const viewLogin = document.getElementById('view-login');
const viewMain = document.getElementById('view-main');
const loginForm = document.getElementById('login-form');
const loginError = document.getElementById('login-error');
const loginSubmit = document.getElementById('login-submit');
const settingsBtn = document.getElementById('settings-btn');
const logoutBtn = document.getElementById('logout-btn');

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

function renderMain({ user, badges }) {
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
    a.href = `${SITE_URL}${link.path}`;
    a.target = '_blank';
    a.rel = 'noopener';
    a.textContent = link.label;
    quickLinks.appendChild(a);
  }
}

function renderLogin() {
  viewLogin.hidden = false;
  viewMain.hidden = true;
}

async function fetchBadges(token) {
  try {
    const response = await fetch(`${SITE_URL}/api/v1/badges`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    if (!response.ok) return null;
    return await response.json();
  } catch {
    return null;
  }
}

async function init() {
  const { token, user } = await chrome.storage.local.get(['token', 'user']);
  if (!token || !user) {
    renderLogin();
    return;
  }

  const badges = await fetchBadges(token);
  if (badges === null) {
    // token pode ter expirado — pede novo login.
    await chrome.storage.local.remove(['token', 'user']);
    renderLogin();
    return;
  }

  renderMain({ user, badges });
  chrome.runtime.sendMessage({ type: 'refresh-badge' });
}

loginForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  loginError.hidden = true;
  loginSubmit.disabled = true;
  loginSubmit.textContent = 'A entrar…';

  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  try {
    const response = await fetch(`${SITE_URL}/api/v1/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ email, password }),
    });

    if (!response.ok) {
      const body = await response.json().catch(() => ({}));
      throw new Error(body?.errors?.email?.[0] || body?.message || 'Credenciais inválidas.');
    }

    const data = await response.json();
    await chrome.storage.local.set({ token: data.token, user: data.user });

    const badges = await fetchBadges(data.token);
    renderMain({ user: data.user, badges });
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
  const { token } = await chrome.storage.local.get(['token']);
  if (token) {
    fetch(`${SITE_URL}/api/v1/auth/logout`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    }).catch(() => {});
  }
  await chrome.storage.local.remove(['token', 'user']);
  chrome.runtime.sendMessage({ type: 'clear-badge' });
  renderLogin();
});

settingsBtn.addEventListener('click', () => logoutBtn.click());

init();
