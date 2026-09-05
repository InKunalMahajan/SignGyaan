(() => {
    const dashboard = document.querySelector('[data-learner-dashboard]');

    if (!dashboard) {
        return;
    }

    const focusClasses = [
        'focus-visible:outline-none',
        'focus-visible:ring-4',
        'focus-visible:ring-sign-light',
        'focus-visible:ring-offset-2',
    ];

    dashboard.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])').forEach((element) => {
        focusClasses.forEach((className) => element.classList.add(className));
    });

    dashboard.querySelectorAll('a, button').forEach((element) => {
        element.classList.add('min-h-11');
    });

    dashboard.querySelectorAll('[role="progressbar"]').forEach((progressbar) => {
        const current = Number(progressbar.getAttribute('aria-valuenow') || 0);
        const value = Math.max(0, Math.min(100, Number.isFinite(current) ? current : 0));

        progressbar.setAttribute('aria-valuemin', '0');
        progressbar.setAttribute('aria-valuemax', '100');
        progressbar.setAttribute('aria-valuenow', String(value));
        progressbar.setAttribute('aria-valuetext', `${value}% complete`);
    });

    const quickNav = dashboard.querySelector('[data-dashboard-quick-nav]');
    if (quickNav) {
        quickNav.setAttribute('tabindex', '0');
        quickNav.setAttribute('role', 'navigation');
        quickNav.setAttribute('aria-label', 'Dashboard section shortcuts');
        quickNav.style.overscrollBehaviorX = 'contain';

        quickNav.addEventListener('keydown', (event) => {
            const links = Array.from(quickNav.querySelectorAll('a[href^="#"]'));
            if (!links.length || !['Home', 'End'].includes(event.key)) {
                return;
            }

            event.preventDefault();
            const target = event.key === 'Home' ? links[0] : links[links.length - 1];
            target.focus();
            target.scrollIntoView({ block: 'nearest', inline: 'center' });
        });
    }

    const focusSectionFromHash = () => {
        const id = window.location.hash.slice(1);
        if (!id) {
            return;
        }

        const section = document.getElementById(id);
        if (!section || !dashboard.contains(section)) {
            return;
        }

        const heading = section.querySelector('h2, h3') || section;
        if (!heading.hasAttribute('tabindex')) {
            heading.setAttribute('tabindex', '-1');
        }

        window.requestAnimationFrame(() => heading.focus({ preventScroll: true }));
    };

    dashboard.querySelectorAll('[data-dashboard-quick-nav] a[href^="#"]').forEach((link) => {
        link.addEventListener('click', () => {
            window.setTimeout(focusSectionFromHash, 0);
        });
    });

    window.addEventListener('hashchange', focusSectionFromHash);
    focusSectionFromHash();
})();
