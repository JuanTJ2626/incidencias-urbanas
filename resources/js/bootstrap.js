import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// By default, Axios configures the 'X-XSRF-TOKEN' from the 'XSRF-TOKEN' cookie.
// No need to manually assign 'X-CSRF-TOKEN' from a potentially stale meta tag in Inertia SPAs.
