import axios from 'axios';
import './accessibility';
import '../css/accessibility.css';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
