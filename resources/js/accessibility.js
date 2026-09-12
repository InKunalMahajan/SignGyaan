const SETTINGS_KEY = 'signgyaan.accessibility.v1';

const defaults = {
    largeText: false,
    highContrast: false,
    reduceMotion: false,
};

function readSettings() {
    try {
        return { ...defaults, ...JSON.parse(localStorage.getItem(SETTINGS_KEY) || '{}') };
    } catch {
        return { ...defaults };
    }
}

function saveSettings(settings) {
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
}

function applySettings(settings) {
    const root = document.documentElement;
    root.classList.toggle('sg-a11y-large-text', settings.largeText);
    root.classList.toggle('sg-a11y-high-contrast', settings.highContrast);
    root.classList.toggle('sg-a11y-reduce-motion', settings.reduceMotion);
}

function ensureDocumentLanguage() {
    if (!document.documentElement.getAttribute('lang')) {
        document.documentElement.setAttribute('lang', 'en');
    }
}

function ensureMainLandmark() {
    const main = document.querySelector('main') || document.querySelector('[role="main"]');
    if (!main) return null;

    if (!main.id) main.id = 'main-content';
    main.setAttribute('role', 'main');
    if (!main.hasAttribute('tabindex')) main.setAttribute('tabindex', '-1');
    return main;
}

function installSkipLink(main) {
    if (!main || document.querySelector('.sg-skip-link')) return;

    const link = document.createElement('a');
    link.className = 'sg-skip-link';
    link.href = `#${main.id}`;
    link.textContent = 'Skip to main content';
    document.body.prepend(link);
}

function enhanceNavigation() {
    const pathname = window.location.pathname.replace(/\/$/, '') || '/';

    document.querySelectorAll('nav').forEach((nav, index) => {
        if (!nav.getAttribute('aria-label') && !nav.getAttribute('aria-labelledby')) {
            nav.setAttribute('aria-label', index === 0 ? 'Primary navigation' : `Navigation ${index + 1}`);
        }
    });

    document.querySelectorAll('nav a[href]').forEach((link) => {
        try {
            const url = new URL(link.href, window.location.origin);
            const target = url.pathname.replace(/\/$/, '') || '/';
            if (target === pathname) link.setAttribute('aria-current', 'page');
        } catch {
            // Ignore non-standard links.
        }
    });
}

function enhanceStatusMessages() {
    document.querySelectorAll('[role="alert"]').forEach((element) => {
        element.setAttribute('aria-live', 'assertive');
        element.setAttribute('aria-atomic', 'true');
    });

    document.querySelectorAll('[role="status"]').forEach((element) => {
        element.setAttribute('aria-live', 'polite');
        element.setAttribute('aria-atomic', 'true');
    });

    const firstAlert = document.querySelector('[role="alert"]');
    if (firstAlert) {
        firstAlert.setAttribute('tabindex', '-1');
    }
}

function enhanceForms() {
    document.querySelectorAll('input, select, textarea').forEach((field) => {
        if (field.type === 'hidden') return;

        const id = field.id;
        const hasLabel = id && document.querySelector(`label[for="${CSS.escape(id)}"]`);
        const wrappedByLabel = field.closest('label');
        const accessibleName = field.getAttribute('aria-label') || field.getAttribute('aria-labelledby');

        if (!hasLabel && !wrappedByLabel && !accessibleName && field.placeholder) {
            field.setAttribute('aria-label', field.placeholder);
        }

        if (field.hasAttribute('required')) {
            field.setAttribute('aria-required', 'true');
        }

        const container = field.closest('.space-y-1, .space-y-2, .space-y-3, .mb-4, .mb-5, .mb-6, div');
        const error = container?.querySelector('[data-error], .text-red-500, .text-red-600, .text-rose-500, .text-rose-600');

        if (error && error.textContent.trim()) {
            field.setAttribute('aria-invalid', 'true');
            if (!error.id) {
                error.id = `error-${field.name || field.id || Math.random().toString(36).slice(2)}`;
            }
            const describedBy = new Set((field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
            describedBy.add(error.id);
            field.setAttribute('aria-describedby', [...describedBy].join(' '));
        }
    });
}

function enhanceTables() {
    document.querySelectorAll('table').forEach((table, index) => {
        if (!table.querySelector('caption')) {
            const caption = document.createElement('caption');
            caption.className = 'sg-sr-only';
            caption.textContent = table.getAttribute('aria-label') || `Data table ${index + 1}`;
            table.prepend(caption);
        }

        table.querySelectorAll('thead th').forEach((heading) => {
            if (!heading.hasAttribute('scope')) heading.setAttribute('scope', 'col');
        });
    });
}

function enhanceMedia() {
    document.querySelectorAll('video').forEach((video) => {
        video.controls = true;
        video.setAttribute('playsinline', '');
        video.setAttribute('preload', video.getAttribute('preload') || 'metadata');

        const captionSource = video.dataset.captionSrc;
        if (captionSource && !video.querySelector('track[kind="captions"]')) {
            const track = document.createElement('track');
            track.kind = 'captions';
            track.label = video.dataset.captionLabel || 'English captions';
            track.srclang = video.dataset.captionLang || 'en';
            track.src = captionSource;
            track.default = true;
            video.appendChild(track);
        }

        const transcriptId = video.dataset.transcriptId;
        if (transcriptId && document.getElementById(transcriptId)) {
            video.setAttribute('aria-describedby', transcriptId);
        }
    });
}

function installAccessibilityPanel(settings) {
    if (document.querySelector('[data-sg-a11y-panel]')) return;

    const details = document.createElement('details');
    details.className = 'sg-a11y-panel';
    details.dataset.sgA11yPanel = 'true';

    details.innerHTML = `
        <summary aria-label="Open accessibility settings">Accessibility</summary>
        <div class="sg-a11y-panel__menu" role="group" aria-label="Accessibility preferences">
            <p class="sg-a11y-panel__title">Accessibility settings</p>
            <label><input type="checkbox" data-a11y-setting="largeText"> Larger text</label>
            <label><input type="checkbox" data-a11y-setting="highContrast"> Higher contrast</label>
            <label><input type="checkbox" data-a11y-setting="reduceMotion"> Reduce motion</label>
            <button type="button" data-a11y-reset>Reset settings</button>
            <p class="sg-a11y-panel__note">These display preferences are saved only in this browser.</p>
        </div>
    `;

    document.body.appendChild(details);

    const sync = () => {
        details.querySelector('[data-a11y-setting="largeText"]').checked = settings.largeText;
        details.querySelector('[data-a11y-setting="highContrast"]').checked = settings.highContrast;
        details.querySelector('[data-a11y-setting="reduceMotion"]').checked = settings.reduceMotion;
    };

    sync();

    details.querySelectorAll('[data-a11y-setting]').forEach((control) => {
        control.addEventListener('change', () => {
            settings[control.dataset.a11ySetting] = control.checked;
            applySettings(settings);
            saveSettings(settings);
        });
    });

    details.querySelector('[data-a11y-reset]').addEventListener('click', () => {
        Object.assign(settings, defaults);
        applySettings(settings);
        saveSettings(settings);
        sync();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && details.open) {
            details.open = false;
            details.querySelector('summary').focus();
        }
    });
}

function announcePageReady() {
    if (document.querySelector('#sg-a11y-live-region')) return;
    const live = document.createElement('div');
    live.id = 'sg-a11y-live-region';
    live.className = 'sg-sr-only';
    live.setAttribute('role', 'status');
    live.setAttribute('aria-live', 'polite');
    live.setAttribute('aria-atomic', 'true');
    document.body.appendChild(live);
}

function initAccessibility() {
    const settings = readSettings();
    applySettings(settings);
    ensureDocumentLanguage();
    const main = ensureMainLandmark();
    installSkipLink(main);
    enhanceNavigation();
    enhanceStatusMessages();
    enhanceForms();
    enhanceTables();
    enhanceMedia();
    installAccessibilityPanel(settings);
    announcePageReady();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAccessibility, { once: true });
} else {
    initAccessibility();
}
