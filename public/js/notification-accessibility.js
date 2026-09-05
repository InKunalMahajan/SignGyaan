(() => {
    const center = document.querySelector('[data-notification-center]');

    if (!center) {
        return;
    }

    const filterNav = center.querySelector('[data-notification-filters]');
    const filterLinks = filterNav ? Array.from(filterNav.querySelectorAll('a[href]')) : [];

    if (filterNav && filterLinks.length) {
        filterNav.addEventListener('keydown', (event) => {
            const currentIndex = filterLinks.indexOf(document.activeElement);

            if (currentIndex === -1) {
                return;
            }

            let nextIndex = null;

            if (event.key === 'ArrowRight') {
                nextIndex = (currentIndex + 1) % filterLinks.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (currentIndex - 1 + filterLinks.length) % filterLinks.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = filterLinks.length - 1;
            }

            if (nextIndex !== null) {
                event.preventDefault();
                filterLinks[nextIndex].focus();
                filterLinks[nextIndex].scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
        });
    }

    center.querySelectorAll('a, button').forEach((element) => {
        element.classList.add('focus-visible:outline-none', 'focus-visible:ring-4', 'focus-visible:ring-sign-light');
    });
})();
