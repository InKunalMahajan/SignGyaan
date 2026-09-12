function sidebarIconSvg(label) {
    const key = label.toLowerCase();

    let path = '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 4v16"/>';

    if (key.includes('dashboard')) path = '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>';
    else if (key.includes('class')) path = '<path d="M4 19.5V5.5A2.5 2.5 0 0 1 6.5 3H20v16.5H6.5A2.5 2.5 0 0 0 4 22Z"/><path d="M8 7h8M8 11h8"/>';
    else if (key.includes('course') || key.includes('curriculum') || key.includes('academic')) path = '<path d="M4 19.5V5.5A2.5 2.5 0 0 1 6.5 3H20v16.5H6.5A2.5 2.5 0 0 0 4 22Z"/><path d="M8 7h8M8 11h6"/>';
    else if (key.includes('profile') || key.includes('user')) path = '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>';
    else if (key.includes('parent') || key.includes('learner')) path = '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="2.5"/><path d="M3 21a6 6 0 0 1 12 0M14 21a5 5 0 0 1 7-4.6"/>';
    else if (key.includes('progress') || key.includes('assessment')) path = '<path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>';
    else if (key.includes('teaching')) path = '<path d="M4 5h16v12H4z"/><path d="M8 21h8M12 17v4"/>';
    else if (key.includes('explore')) path = '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5 5-2Z"/>';
    else if (key.includes('setting')) path = '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06-2.83 2.83-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.04 1.56V21h-4v-.08A1.7 1.7 0 0 0 8.96 19.36a1.7 1.7 0 0 0-1.87.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15 1.7 1.7 0 0 0 3.08 14H3v-4h.08A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 8.96 4.64 1.7 1.7 0 0 0 10 3.08V3h4v.08a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.87-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.17.62.73 1 1.36 1H21v4h-.24c-.63 0-1.19.38-1.36 1Z"/>';

    return `<svg class="sg-sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${path}</svg>`;
}

function enhanceIconSidebars() {
    document.querySelectorAll('aside').forEach((aside) => {
        const navLinks = [...aside.querySelectorAll('nav a')];
        if (!navLinks.length || aside.dataset.iconSidebarReady === 'true') return;

        aside.dataset.iconSidebarReady = 'true';
        aside.classList.add('sg-icon-sidebar');

        const shell = aside.parentElement;
        if (shell) shell.classList.add('sg-icon-sidebar-shell');

        const brand = aside.querySelector('a[aria-label*="SignGyaan"], .sg-app-sidebar-brand, a[href*="/dashboard/"]');
        if (brand) brand.classList.add('sg-icon-sidebar-brand');

        navLinks.forEach((link) => {
            const label = link.textContent.trim().replace(/\s+/g, ' ');
            if (!label) return;

            link.classList.add('sg-icon-sidebar-link');
            link.setAttribute('title', label);
            if (!link.getAttribute('aria-label')) link.setAttribute('aria-label', label);

            const text = document.createElement('span');
            text.className = 'sg-icon-sidebar-text';
            text.textContent = label;

            link.textContent = '';
            link.insertAdjacentHTML('afterbegin', sidebarIconSvg(label));
            link.appendChild(text);
        });

        const footer = [...aside.children].find((child) => child !== aside.firstElementChild && child.querySelector?.('form[action$="/logout"]'));
        if (footer) footer.classList.add('sg-icon-sidebar-account-card');
    });
}

document.addEventListener('DOMContentLoaded', enhanceIconSidebars);
