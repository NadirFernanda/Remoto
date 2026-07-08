const SITE_URL = 'https://24horas.ao';
const ALARM_NAME = 'badge-refresh';
const ALARM_PERIOD_MINUTES = 2;

async function getToken() {
  const { token } = await chrome.storage.local.get(['token']);
  return token;
}

async function clearBadge() {
  await chrome.action.setBadgeText({ text: '' });
}

async function refreshBadge() {
  const token = await getToken();
  if (!token) {
    await clearBadge();
    return;
  }

  try {
    const response = await fetch(`${SITE_URL}/api/v1/badges`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    });

    if (!response.ok) {
      if (response.status === 401) {
        await chrome.storage.local.remove(['token', 'user']);
        await clearBadge();
      }
      return;
    }

    const data = await response.json();
    const total = data.total || 0;

    await chrome.action.setBadgeBackgroundColor({ color: '#ff2d55' });
    await chrome.action.setBadgeText({ text: total > 0 ? String(Math.min(total, 99)) : '' });
  } catch (error) {
    // Site indisponível ou offline — mantém o badge actual, tenta de novo no próximo alarme.
  }
}

chrome.runtime.onInstalled.addListener(() => {
  chrome.alarms.create(ALARM_NAME, { periodInMinutes: ALARM_PERIOD_MINUTES });
  refreshBadge();
});

chrome.runtime.onStartup.addListener(() => {
  refreshBadge();
});

chrome.alarms.onAlarm.addListener((alarm) => {
  if (alarm.name === ALARM_NAME) refreshBadge();
});

chrome.runtime.onMessage.addListener((message) => {
  if (message?.type === 'refresh-badge') refreshBadge();
  if (message?.type === 'clear-badge') clearBadge();
});
