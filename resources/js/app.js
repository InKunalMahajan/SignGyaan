import './bootstrap';
import '../css/file-input.css';
import '../css/shape-position-fix.css';
import '../css/header-user-menu.css';
import '../css/app-sidebar-fallback.css';

function initialsFromName(name) {
    return name
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('') || 'SG';
}

function iconSvg(type) {
    const paths = {
        profile: '<path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0"/>',
        settings: '<path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06-2.83 2.83-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.04 1.56V21h-4v-.08A1.7 1.7 0 0 0 8.96 19.36a1.7 1.7 0 0 0-1.87.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15 1.7 1.7 0 0 0 3.08 14H3v-4h.08A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 8.96 4.64 1.7 1.7 0 0 0 10 3.08V3h4v.08a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.87-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.17.62.73 1 1.36 1H21v4h-.24c-.63 0-1.19.38-1.36 1Z"/>',
        logout: '<path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 19V5a2 2 0 0 0-2-2h-6"/>',
        chevron: '<path d="m6 9 6 6 6-6"/>',
    };

    return `<svg class="sg-account-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths[type] ?? ''}</svg>`;
}

function roleFromPath() {
    const path = window.location.pathname;

    if (path.startsWith('/learner')) return 'learner';
    if (path.startsWith('/teacher')) return 'teacher';
    if (path.startsWith('/parents')) return 'parents';
    if (path.startsWith('/admin')) return 'admin';

    return null;
}

function navigationForRole(role) {
    const navigation = {
        learner: [
            ['Dashboard', '/dashboard/learner', (path) => path === '/dashboard/learner'],
            ['My Classes', '/learner/classes', (path) => path.startsWith('/learner/classes')],
            ['My Profile', '/learner/profile', (path) => path.startsWith('/learner/profile')],
            ['Parent Access', '/learner/parent-links', (path) => path.startsWith('/learner/parent-links')],
            ['Explore Public', '/dashboard/guest', () => false],
        ],
        teacher: [
            ['Dashboard', '/dashboard/teacher', (path) => path === '/dashboard/teacher'],
            ['My Classes', '/teacher/classes', (path) => path.startsWith('/teacher/classes')],
            ['Course Content', '/teacher/courses', (path) => path.startsWith('/teacher/courses')],
            ['My Profile', '/teacher/profile', (path) => path.startsWith('/teacher/profile')],
            ['Explore Public', '/dashboard/guest', () => false],
        ],
        parents: [
            ['Dashboard', '/dashboard/parents', (path) => path === '/dashboard/parents'],
            ['Parent Profile', '/parents/profile', (path) => path.startsWith('/parents/profile')],
            ['Learner Progress', '/parents/profile', (path) => path.startsWith('/parents/learners')],
            ['Explore Public', '/dashboard/guest', () => false],
        ],
        admin: [
            ['Dashboard', '/dashboard/admin', (path) => path === '/dashboard/admin'],
            ['Users & Roles', '/admin/users', (path) => path.startsWith('/admin/users')],
            ['Academic Structure', '/admin/curriculum', (path) => path === '/admin/curriculum'],
            ['Course Content', '/admin/curriculum/content', (path) => path.startsWith('/admin/curriculum/content')],
            ['Teaching Management', '/admin/teaching', (path) => path.startsWith('/admin/teaching')],
            ['Progress & Assessment', '/admin/progress', (path) => path.startsWith('/admin/progress')],
            ['Explore Public', '/dashboard/guest', () => false],
        ],
    };

    return navigation[role] ?? [];
}

function hasExistingAppSidebar() {
    return Boolean(
        document.querySelector('aside nav[aria-label]') ||
        document.querySelector('[data-app-sidebar]') ||
        document.querySelector('.sg-app-sidebar-fallback')
    );
}

function installFallbackAppSidebar() {
    const role = roleFromPath();
    if (!role || hasExistingAppSidebar()) {
        return;
    }

    const path = window.location.pathname;
    const roleLabel = role === 'parents' ? 'Parent' : role.charAt(0).toUpperCase() + role.slice(1);
    const items = navigationForRole(role);

    const shell = document.createElement('div');
    shell.className = 'sg-app-shell-fallback-root';

    const sidebar = document.createElement('aside');
    sidebar.className = 'sg-app-sidebar-fallback';
    sidebar.setAttribute('data-app-sidebar', 'fallback');
    sidebar.setAttribute('aria-label', `${roleLabel} navigation`);

    const links = items.map(([label, href, isActive]) => {
        const current = isActive(path) ? ' aria-current="page"' : '';
        return `<a href="${href}"${current}>${label}</a>`;
    }).join('');

    sidebar.innerHTML = `
        <a class="sg-app-sidebar-brand" href="/dashboard/${role}">
            <span class="sg-app-sidebar-mark" aria-hidden="true">SG</span>
            <span class="sg-app-sidebar-brand-copy">
                <strong>SignGyaan</strong>
                <span>Accessible learning</span>
            </span>
        </a>
        <p class="sg-app-sidebar-label">Navigation</p>
        <nav class="sg-app-sidebar-nav" aria-label="${roleLabel} app navigation">${links}</nav>
        <div class="sg-app-sidebar-footer">
            <span class="sg-app-sidebar-role">${roleLabel} account</span>
        </div>
    `;

    const content = document.createElement('div');
    content.className = 'sg-app-shell-fallback-content';

    while (document.body.firstChild) {
        content.appendChild(document.body.firstChild);
    }

    shell.append(sidebar, content);
    document.body.appendChild(shell);
    document.body.classList.add('sg-app-shell-fallback');
}

function enhanceHeaderAccountMenu() {
    const header = document.querySelector('main#main-content > header');
    if (!header) {
        return;
    }

    const identity = header.querySelector('span.hidden.text-right.sm\\:block');
    if (!identity || identity.dataset.accountMenuEnhanced === 'true') {
        return;
    }

    const name = identity.children[0]?.textContent?.trim() || 'Account';
    const roleText = identity.children[1]?.textContent?.trim() || '';
    const role = roleText.replace(/\s+account$/i, '').trim().toLowerCase();

    const profilePath = {
        learner: '/learner/profile',
        teacher: '/teacher/profile',
        parents: '/parents/profile',
        admin: '/dashboard/admin',
    }[role] || '/dashboard';

    const settingsPath = {
        learner: '/learner/profile#settings',
        teacher: '/teacher/profile#settings',
        parents: '/parents/profile#settings',
        admin: '/admin/users',
    }[role] || '/dashboard';

    const logoutSource = document.querySelector('form[action$="/logout"]');

    const menu = document.createElement('details');
    menu.className = 'sg-account-menu';
    menu.dataset.accountMenuEnhanced = 'true';

    const summary = document.createElement('summary');
    summary.setAttribute('aria-label', `Open account menu for ${name}`);
    summary.innerHTML = `
        <span class="sg-account-avatar" aria-hidden="true">${initialsFromName(name)}</span>
        <span class="sg-account-text">
            <span class="sg-account-name">${name}</span>
            <span class="sg-account-role">${roleText}</span>
        </span>
        <svg class="sg-account-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
    `;

    const popover = document.createElement('div');
    popover.className = 'sg-account-popover';
    popover.innerHTML = `
        <div class="sg-account-popover-header">
            <strong>${name}</strong>
            <span>${roleText}</span>
        </div>
        <a class="sg-account-link" href="${profilePath}">${iconSvg('profile')}<span>Profile</span></a>
        <a class="sg-account-link" href="${settingsPath}">${iconSvg('settings')}<span>Settings</span></a>
        <div class="sg-account-menu-divider"></div>
    `;

    if (logoutSource) {
        const logoutForm = logoutSource.cloneNode(true);
        logoutForm.className = '';
        const logoutButton = logoutForm.querySelector('button');
        if (logoutButton) {
            logoutButton.className = 'sg-account-logout';
            logoutButton.innerHTML = `${iconSvg('logout')}<span>Sign out</span>`;
        }
        popover.appendChild(logoutForm);
    }

    menu.append(summary, popover);
    identity.replaceWith(menu);

    document.addEventListener('click', (event) => {
        if (menu.open && !menu.contains(event.target)) {
            menu.removeAttribute('open');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.open) {
            menu.removeAttribute('open');
            summary.focus();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    installFallbackAppSidebar();
    enhanceHeaderAccountMenu();

    if (!window.location.pathname.includes('/dashboard/learner')) {
        return;
    }

    const overview = document.querySelector('section[aria-labelledby="overview-heading"]');
    const sourceLabel = overview?.querySelector(':scope > div > span');

    if (sourceLabel?.textContent?.trim() === 'Demo values') {
        sourceLabel.textContent = 'Live data';
    }
});
