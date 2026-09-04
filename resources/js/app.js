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
        searchTrigger.setAttribute('title', 'Search — press / or Ctrl+K');
    }
};

const enhanceForms = () => {
    document.querySelectorAll('input.border-red-300, select.border-red-300, textarea.border-red-300').forEach((field) => {
        field.setAttribute('aria-invalid', 'true');
    });

    const errorSummary = document.querySelector('main [data-error-summary], main [role="alert"]');

    if (errorSummary instanceof HTMLElement) {
        if (!errorSummary.hasAttribute('tabindex')) {
            errorSummary.setAttribute('tabindex', '-1');
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        requestAnimationFrame(() => {
            errorSummary.focus({ preventScroll: true });
            errorSummary.scrollIntoView({
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
                block: 'center',
            });
        });
    }
};

const returnFocusFromHeaderSearch = (input) => {
    const isDesktopSearch = input.id === 'desktop-search-input';
    const isMobileSearch = input.id === 'mobile-search-input';

    if (!isDesktopSearch && !isMobileSearch) {
        return false;
    }

    input.blur();

    requestAnimationFrame(() => {
        const trigger = isDesktopSearch
            ? document.querySelector('[aria-controls="desktop-global-search"]')
            : document.querySelector('[aria-controls="mobile-navigation"]');

        if (trigger instanceof HTMLElement && isVisible(trigger)) {
            trigger.focus();
        }
    });

    return true;
};

const returnFocusFromHeaderPanel = (target) => {
    if (!(target instanceof HTMLElement)) {
        return false;
    }

    if (target.closest('#desktop-account-menu')) {
        requestAnimationFrame(() => {
            const trigger = document.querySelector('[aria-controls="desktop-account-menu"]');
            if (trigger instanceof HTMLElement && isVisible(trigger)) {
                trigger.focus();
            }
        });
        return true;
    }

    if (target.closest('#mobile-navigation')) {
        requestAnimationFrame(() => {
            const trigger = document.querySelector('[aria-controls="mobile-navigation"]');
            if (trigger instanceof HTMLElement && isVisible(trigger)) {
                trigger.focus();
            }
        });
        return true;
    }

    return false;
};

document.addEventListener('DOMContentLoaded', () => {
    enhanceSearchInputs();
    enhanceForms();
});

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

    if (event.key === 'Escape') {
        if (target instanceof HTMLInputElement && target.type === 'search') {
            if (returnFocusFromHeaderSearch(target)) {
                return;
            }

            if (target.value === '') {
                target.blur();
                return;
            }
        }

        returnFocusFromHeaderPanel(target);
    }
});
