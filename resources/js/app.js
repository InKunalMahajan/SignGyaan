import './bootstrap';
import '../css/file-input.css';
import '../css/shape-position-fix.css';

document.addEventListener('DOMContentLoaded', () => {
    if (!window.location.pathname.includes('/dashboard/learner')) {
        return;
    }

    const overview = document.querySelector('section[aria-labelledby="overview-heading"]');
    const sourceLabel = overview?.querySelector(':scope > div > span');

    if (sourceLabel?.textContent?.trim() === 'Demo values') {
        sourceLabel.textContent = 'Live data';
    }
});
