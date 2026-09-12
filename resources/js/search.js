function isProtectedWorkspace() {
    return /^\/(dashboard\/(learner|teacher|parents|admin)|learner(?:\/|$)|teacher(?:\/|$)|parents(?:\/|$)|admin(?:\/|$)|search(?:\/|$))/.test(window.location.pathname);
}

function installHeaderSearch() {
    if (!isProtectedWorkspace() || document.querySelector('[data-global-search-form]')) return;

    const header = document.querySelector('main#main-content > header .mx-auto')
        || document.querySelector('.sg-app-header-fallback-inner');

    if (!header) return;

    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '/search';
    form.setAttribute('role', 'search');
    form.dataset.globalSearchForm = 'true';
    form.className = 'sg-global-search';
    form.innerHTML = `
        <label class="sg-sr-only" for="sg-global-search-input">Search SignGyaan</label>
        <span class="sg-global-search__icon" aria-hidden="true">⌕</span>
        <input id="sg-global-search-input" name="q" type="search" maxlength="100" autocomplete="off" placeholder="Search" aria-label="Search SignGyaan">
        <kbd class="sg-global-search__kbd" aria-hidden="true">Ctrl K</kbd>
    `;

    const account = header.querySelector('.sg-account-menu, [data-account-menu-enhanced]') || header.lastElementChild;
    if (account) {
        header.insertBefore(form, account);
    } else {
        header.appendChild(form);
    }
}

function installKeyboardShortcut() {
    document.addEventListener('keydown', (event) => {
        if (!(event.ctrlKey || event.metaKey) || event.key.toLowerCase() !== 'k') return;

        const target = event.target;
        if (target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement || target?.isContentEditable) return;

        event.preventDefault();
        const input = document.querySelector('#sg-global-search-input');
        if (input) {
            input.focus();
            input.select();
        } else if (isProtectedWorkspace()) {
            window.location.assign('/search');
        }
    });
}

function initSearch() {
    installHeaderSearch();
    installKeyboardShortcut();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearch, { once: true });
} else {
    initSearch();
}
