import './bootstrap';
import Alpine from 'alpinejs';
import axios from 'axios';

// Configure Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Make Alpine globally available
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
