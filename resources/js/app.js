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
    const pageSearch = document.getElementById('search-page-input')
        || document.getElementById('catalog-search');

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
        '#search-page-input, #catalog-search, #desktop-search-input, #mobile-search-input, #explore-search'
    );

    searchInputs.forEach((input) => {
        input.setAttribute('enterkeyhint', 'search');
        input.setAttribute('aria-keyshortcuts', '/ Control+K Meta+K');
    });

    const searchTrigger = document.querySelector('[aria-controls="desktop-global-search"]');

    if (searchTrigger instanceof HTMLElement) {
        searchTrigger.setAttribute('aria-keyshortcuts', '/ Control+K Meta+K');
        searchTrigger.setAttribute('title', 'Search — press / or Ctrl+K');
        searchTrigger.setAttribute('aria-haspopup', 'true');
    }
};

const connectFieldErrors = () => {
    document.querySelectorAll('form p.text-red-700, form [data-field-error]').forEach((message, index) => {
        if (!(message instanceof HTMLElement)) {
            return;
        }

        const container = message.parentElement;
        const field = container?.querySelector('input, select, textarea');

        if (!(field instanceof HTMLElement)) {
            return;
        }

        const errorId = message.id || `field-error-${field.id || index}`;
        message.id = errorId;
        field.setAttribute('aria-invalid', 'true');

        const describedBy = (field.getAttribute('aria-describedby') || '')
            .split(/\s+/)
            .filter(Boolean);

        if (!describedBy.includes(errorId)) {
            describedBy.push(errorId);
            field.setAttribute('aria-describedby', describedBy.join(' '));
        }
    });
};

const enhanceForms = () => {
    document.querySelectorAll('input.border-red-300, select.border-red-300, textarea.border-red-300').forEach((field) => {
        field.setAttribute('aria-invalid', 'true');
    });

    connectFieldErrors();

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

const openHashTargetCourseUnit = () => {
    if (!window.location.hash) {
        return;
    }

    let targetId;

    try {
        targetId = decodeURIComponent(window.location.hash.slice(1));
    } catch {
        targetId = window.location.hash.slice(1);
    }

    if (!targetId.startsWith('course-unit-heading-')) {
        return;
    }

    const target = document.getElementById(targetId);
    const unitSection = target?.closest('section[aria-labelledby]');
    const toggle = unitSection?.querySelector('button[aria-controls]');

    if (!(toggle instanceof HTMLButtonElement)) {
        return;
    }

    if (toggle.getAttribute('aria-expanded') === 'false') {
        toggle.click();
    }
};

const enhancePublicShell = () => {
    const shell = document.querySelector('[data-public-shell]');

    if (!(shell instanceof HTMLElement)) {
        return;
    }

    const mobileTrigger = document.querySelector('[aria-controls="mobile-navigation"]');
    const accountTrigger = document.querySelector('[aria-controls="desktop-account-menu"]');

    if (mobileTrigger instanceof HTMLElement) {
        mobileTrigger.setAttribute('aria-haspopup', 'true');
    }

    if (accountTrigger instanceof HTMLElement) {
        accountTrigger.setAttribute('aria-haspopup', 'true');
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(openHashTargetCourseUnit);
    });

    window.addEventListener('hashchange', () => {
        requestAnimationFrame(openHashTargetCourseUnit);
    });

    const desktopQuery = window.matchMedia('(min-width: 1024px)');
    const closeStaleMobileNavigation = (event) => {
        if (
            event.matches
            && mobileTrigger instanceof HTMLElement
            && mobileTrigger.getAttribute('aria-expanded') === 'true'
        ) {
            mobileTrigger.click();
        }
    };

    if (typeof desktopQuery.addEventListener === 'function') {
        desktopQuery.addEventListener('change', closeStaleMobileNavigation);
    } else if (typeof desktopQuery.addListener === 'function') {
        desktopQuery.addListener(closeStaleMobileNavigation);
    }
};

const getAdminDrawer = () => document.getElementById('admin-mobile-navigation');

const getAdminDrawerFocusable = () => {
    const drawer = getAdminDrawer();

    if (!(drawer instanceof HTMLElement) || !isVisible(drawer)) {
        return [];
    }

    return Array.from(
        drawer.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )
    ).filter((element) => element instanceof HTMLElement && isVisible(element));
};

const trapAdminDrawerFocus = (event) => {
    if (event.key !== 'Tab') {
        return false;
    }

    const focusable = getAdminDrawerFocusable();

    if (focusable.length === 0) {
        return false;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    const active = document.activeElement;

    if (event.shiftKey && active === first) {
        event.preventDefault();
        last.focus();
        return true;
    }

    if (!event.shiftKey && active === last) {
        event.preventDefault();
        first.focus();
        return true;
    }

    if (!(active instanceof HTMLElement) || !getAdminDrawer()?.contains(active)) {
        event.preventDefault();
        first.focus();
        return true;
    }

    return false;
};

const enhanceAdminShell = () => {
    const shell = document.querySelector('[data-admin-shell]');

    if (!(shell instanceof HTMLElement)) {
        return;
    }

    const trigger = document.querySelector('[aria-controls="admin-mobile-navigation"]');
    const closeButton = document.querySelector('#admin-mobile-navigation [aria-label="Close admin navigation"]');

    if (trigger instanceof HTMLElement) {
        trigger.setAttribute('aria-haspopup', 'true');
    }

    if (closeButton instanceof HTMLElement) {
        closeButton.addEventListener('click', () => {
            requestAnimationFrame(() => {
                if (trigger instanceof HTMLElement && isVisible(trigger)) {
                    trigger.focus();
                }
            });
        });
    }

    const desktopQuery = window.matchMedia('(min-width: 1024px)');
    const closeAdminNavigation = (event) => {
        if (event.matches && closeButton instanceof HTMLElement) {
            closeButton.click();
        }
    };

    if (typeof desktopQuery.addEventListener === 'function') {
        desktopQuery.addEventListener('change', closeAdminNavigation);
    } else if (typeof desktopQuery.addListener === 'function') {
        desktopQuery.addListener(closeAdminNavigation);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    enhanceSearchInputs();
    enhanceForms();
    enhancePublicShell();
    enhanceAdminShell();
});

document.addEventListener('keydown', (event) => {
    const target = event.target;
    const key = event.key.toLowerCase();
    const isSlashShortcut = event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey;
    const isCommandSearch = key === 'k' && (event.ctrlKey || event.metaKey) && !event.altKey;

    if (trapAdminDrawerFocus(event)) {
        return;
    }

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
