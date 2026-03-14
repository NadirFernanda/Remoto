import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo + Pusher — real-time WebSockets
 *
 * Requires:  npm install laravel-echo pusher-js
 * Set in .env:  BROADCAST_CONNECTION=pusher  and PUSHER_* vars
 * Expose via vite.config.js:  VITE_PUSHER_APP_KEY, VITE_PUSHER_APP_CLUSTER
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster:  'pusher',
    key:           import.meta.env.VITE_PUSHER_APP_KEY,
    cluster:       import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
    wsHost:        import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu'}.pusher.com`,
    wsPort:        import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort:       import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS:      (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

