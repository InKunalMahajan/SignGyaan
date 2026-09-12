import axios from 'axios';
import './accessibility';
import './search';
import '../css/accessibility.css';
import '../css/search.css';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
