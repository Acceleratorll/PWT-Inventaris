import './bootstrap';

import Alpine from 'alpinejs';
import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "your-pusher-key",
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

window.Alpine = Alpine;

Alpine.start();
