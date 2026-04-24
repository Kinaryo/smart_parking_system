import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo & Pusher Configuration
 * Konfigurasi ini disesuaikan untuk menggunakan layanan Pusher (Cloud)
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher', // Ubah dari 'reverb' ke 'pusher'
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    forceTLS: true, // Untuk Pusher Cloud, ini harus TRUE (menggunakan HTTPS/WSS)
    enabledTransports: ['ws', 'wss'],
});