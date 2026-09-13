function isProtectedWorkspace() {
    return /^\/(dashboard\/(learner|teacher|parents|admin)|learner(?:\/|$)|teacher(?:\/|$)|parents(?:\/|$)|admin(?:\/|$)|search(?:\/|$)|reports(?:\/|$))/.test(window.location.pathname);
}

function installReportsNavigation() {
    if (!isProtectedWorkspace() || document.querySelector('a[href="/reports"]')) return;

    const nav = document.querySelector('aside nav div');
    if (!nav) return;

    const link = document.createElement('a');
    link.href = '/reports';
    link.textContent = 'Reports';
    link.className = 'flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100';

    if (window.location.pathname.startsWith('/reports')) {
        link.className = 'flex min-w-fit items-center rounded-xl bg-slate-950 px-3 py-3 text-sm font-bold text-white';
        link.setAttribute('aria-current', 'page');
    }

    nav.appendChild(link);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', installReportsNavigation, { once: true });
} else {
    installReportsNavigation();
}
