import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Setup condition for production vs local environment
const isLocal = import.meta.env.VITE_REVERB_HOST === 'localhost' || !import.meta.env.VITE_REVERB_HOST;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: isLocal ? '127.0.0.1' : import.meta.env.VITE_REVERB_HOST,
    wsPort: isLocal ? 8080 : undefined,
    wssPort: isLocal ? 8080 : undefined,
    forceTLS: !isLocal,
    enabledTransports: ['ws', 'wss'],
});