import './bootstrap';

const isEditableTarget = (target) => {
    if (!(target instanceof HTMLElement)) {
        return false;
    }

    return (
        target.isContentEditable ||
        ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName)
    );
};

const isVisible = (element) => {
    if (!(element instanceof HTMLElement)) {
        return false;
    }

    return Boolean(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
};

const focusSearchInput = (input) => {
    if (!(input instanceof HTMLInputElement)) {
        return false;
    }

    input.focus();

    if (input.value) {
        input.select();
    }

    return true;
};

const openGlobalSearch = () => {
    const pageSearch = document.getElementById('search-page-input');

    if (isVisible(pageSearch)) {
        return focusSearchInput(pageSearch);
    }

    if (window.matchMedia('(min-width: 1024px)').matches) {
        const desktopSearch = document.getElementById('desktop-search-input');

        if (isVisible(desktopSearch)) {
            return focusSearchInput(desktopSearch);
        }

        const desktopTrigger = document.querySelector('[aria-controls="desktop-global-search"]');

        if (desktopTrigger instanceof HTMLElement) {
            desktopTrigger.click();

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    focusSearchInput(document.getElementById('desktop-search-input'));
                });
            });

            return true;
        }
    }

    const mobileSearch = document.getElementById('mobile-search-input');

    if (isVisible(mobileSearch)) {
        return focusSearchInput(mobileSearch);
    }

    const mobileTrigger = document.querySelector('[aria-controls="mobile-navigation"]');

    if (mobileTrigger instanceof HTMLElement) {
        mobileTrigger.click();

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                focusSearchInput(document.getElementById('mobile-search-input'));
            });
        });

        return true;
    }

    return false;
};

const enhanceSearchInputs = () => {
    const searchInputs = document.querySelectorAll(
        '#search-page-input, #desktop-search-input, #mobile-search-input, #explore-search'
    );

    searchInputs.forEach((input) => {
        input.setAttribute('enterkeyhint', 'search');
        input.setAttribute('aria-keyshortcuts', '/ Control+K Meta+K');
    });

    const searchTrigger = document.querySelector('[aria-controls="desktop-global-search"]');

    if (searchTrigger instanceof HTMLElement) {
        searchTrigger.setAttribute('aria-keyshortcuts', '/ Control+K Meta+K');
    }
};

document.addEventListener('DOMContentLoaded', enhanceSearchInputs);

document.addEventListener('keydown', (event) => {
    const target = event.target;
    const key = event.key.toLowerCase();
    const isSlashShortcut = event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey;
    const isCommandSearch = key === 'k' && (event.ctrlKey || event.metaKey) && !event.altKey;

    if ((isSlashShortcut || isCommandSearch) && !isEditableTarget(target)) {
        event.preventDefault();
        openGlobalSearch();
        return;
    }

    if (event.key === 'Escape' && target instanceof HTMLInputElement && target.type === 'search') {
        if (target.value === '') {
            target.blur();
        }
    }
});
