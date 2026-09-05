document.addEventListener('DOMContentLoaded', () => {
    const dashboard = document.querySelector('[data-admin-dashboard]');

    if (!dashboard) {
        return;
    }

    let liveRegion = document.querySelector('[data-dashboard-live-region]');

    if (!liveRegion) {
        liveRegion = document.createElement('div');
        liveRegion.setAttribute('data-dashboard-live-region', '');
        liveRegion.setAttribute('role', 'status');
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        dashboard.prepend(liveRegion);
    }

    document.querySelectorAll('[role="progressbar"]').forEach((progressBar) => {
        const value = progressBar.getAttribute('aria-valuenow');

        if (value !== null && !progressBar.hasAttribute('aria-valuetext')) {
            progressBar.setAttribute('aria-valuetext', `${value}% complete`);
        }
    });

    const filterForm = document.querySelector('form[action*="/admin/learners"]');

    filterForm?.addEventListener('submit', () => {
        liveRegion.textContent = 'Applying learner filters.';
    });

    const resetLink = Array.from(document.querySelectorAll('a')).find((link) =>
        link.textContent?.trim() === 'Reset dashboard view'
    );

    resetLink?.addEventListener('click', () => {
        liveRegion.textContent = 'Resetting dashboard view.';
    });
});
