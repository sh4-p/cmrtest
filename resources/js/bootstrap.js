import axios from 'axios';

// Set up Axios defaults
window.axios = axios;

// Common headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// CSRF Token for Laravel
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Base URL configuration
window.axios.defaults.baseURL = '/api';

// Sanctum: Ensure credentials are included for cookie-based authentication
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;
