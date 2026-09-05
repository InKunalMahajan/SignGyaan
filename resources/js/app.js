import './bootstrap';

const isEditableTarget = (target) => {
    if (!(target instanceof HTMLElement)) return false;
    return target.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName);
};

const isVisible = (element) => element instanceof HTMLElement && Boolean(element.offsetWidth || element.offsetHeight || element.getClientRects().length);

const focusSearchInput = (input) => {
    if (!(input instanceof HTMLInputElement)) return false;
    input.focus();
    if (input.value) input.select();
    return true;
};

const openGlobalSearch = () => {
    const pageSearch = document.getElementById('search-page-input') || document.getElementById('catalog-search');
    if (isVisible(pageSearch)) return focusSearchInput(pageSearch);
    const desktopSearch = document.getElementById('desktop-search-input');
    if (isVisible(desktopSearch)) return focusSearchInput(desktopSearch);
    const mobileSearch = document.getElementById('mobile-search-input');
    if (isVisible(mobileSearch)) return focusSearchInput(mobileSearch);
    return false;
};

const enhanceSearchInputs = () => {
    document.querySelectorAll('#search-page-input, #catalog-search, #desktop-search-input, #mobile-search-input, #explore-search').forEach((input) => {
        input.setAttribute('enterkeyhint', 'search');
        input.setAttribute('aria-keyshortcuts', '/ Control+K Meta+K');
    });
};

const connectFieldErrors = () => {
    document.querySelectorAll('form p.text-red-700, form [data-field-error]').forEach((message, index) => {
        if (!(message instanceof HTMLElement)) return;
        const field = message.parentElement?.querySelector('input, select, textarea');
        if (!(field instanceof HTMLElement)) return;
        const errorId = message.id || `field-error-${field.id || index}`;
        message.id = errorId;
        field.setAttribute('aria-invalid', 'true');
        const ids = (field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
        if (!ids.includes(errorId)) field.setAttribute('aria-describedby', [...ids, errorId].join(' '));
    });
};

const enhanceForms = () => {
    document.querySelectorAll('input.border-red-300, select.border-red-300, textarea.border-red-300').forEach((field) => field.setAttribute('aria-invalid', 'true'));
    connectFieldErrors();
    const errorSummary = document.querySelector('main [data-error-summary], main [role="alert"]');
    if (errorSummary instanceof HTMLElement) {
        if (!errorSummary.hasAttribute('tabindex')) errorSummary.setAttribute('tabindex', '-1');
        requestAnimationFrame(() => errorSummary.focus({ preventScroll: true }));
    }
};

const formatVideoTime = (seconds) => {
    if (!Number.isFinite(seconds) || seconds < 0) return '0:00';
    const total = Math.floor(seconds);
    const hours = Math.floor(total / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    const remainder = total % 60;
    return hours > 0 ? `${hours}:${String(minutes).padStart(2, '0')}:${String(remainder).padStart(2, '0')}` : `${minutes}:${String(remainder).padStart(2, '0')}`;
};

const enhanceAccessibleIslVideos = () => {
    document.querySelectorAll('#lesson-video video').forEach((video, index) => {
        if (!(video instanceof HTMLVideoElement) || video.dataset.accessiblePlayerReady === 'true') return;
        video.dataset.accessiblePlayerReady = 'true';
        video.controls = true;
        video.setAttribute('playsinline', '');
        video.setAttribute('tabindex', '0');
        video.setAttribute('aria-keyshortcuts', 'Space K J L ArrowLeft ArrowRight M F');

        const helpId = `isl-video-help-${index + 1}`;
        const wrapper = document.createElement('div');
        wrapper.dataset.accessibleVideoControls = 'true';
        wrapper.className = 'border-t border-sign-border bg-white p-3 sm:p-4';
        const toolbar = document.createElement('div');
        toolbar.className = 'flex flex-wrap items-center gap-2';
        toolbar.setAttribute('role', 'group');
        toolbar.setAttribute('aria-label', 'Video playback controls');
        const makeButton = (label, text = label) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-sign-soft px-3 py-2 text-sm font-semibold text-sign-primary';
            button.textContent = text;
            button.setAttribute('aria-label', label);
            return button;
        };
        const playButton = makeButton('Play video', 'Play');
        const backButton = makeButton('Rewind 10 seconds', '−10s');
        const forwardButton = makeButton('Forward 10 seconds', '+10s');
        const muteButton = makeButton('Mute video', 'Mute');
        const captionsButton = makeButton('Turn captions on', 'CC');
        captionsButton.hidden = true;
        const fullscreenButton = makeButton('Enter full screen', 'Full screen');
        const speedSelect = document.createElement('select');
        speedSelect.className = 'min-h-11 rounded-xl border border-sign-border bg-white px-3 text-base text-sign-text';
        speedSelect.setAttribute('aria-label', 'Playback speed');
        [0.5, 0.75, 1, 1.25, 1.5, 2].forEach((rate) => speedSelect.add(new Option(`${rate}×`, String(rate), rate === 1, rate === 1)));
        const timeStatus = document.createElement('span');
        timeStatus.className = 'ml-auto text-xs font-semibold text-sign-muted';
        timeStatus.setAttribute('role', 'status');
        toolbar.append(playButton, backButton, forwardButton, muteButton, captionsButton, speedSelect, fullscreenButton, timeStatus);
        const help = document.createElement('details');
        help.id = helpId;
        help.className = 'mt-3 rounded-xl bg-sign-soft px-4 py-3 text-xs leading-5 text-sign-muted';
        help.innerHTML = '<summary class="cursor-pointer font-semibold text-sign-primary">Keyboard shortcuts</summary><p class="mt-2">Space or K: play/pause · J or Left Arrow: back 10 seconds · L or Right Arrow: forward 10 seconds · M: mute · F: full screen.</p>';
        wrapper.append(toolbar, help);
        video.insertAdjacentElement('afterend', wrapper);
        video.setAttribute('aria-describedby', helpId);

        const updatePlay = () => { playButton.textContent = !video.paused && !video.ended ? 'Pause' : 'Play'; };
        const updateTime = () => { timeStatus.textContent = `${formatVideoTime(video.currentTime)} / ${formatVideoTime(video.duration)}`; };
        const captionTrack = () => Array.from(video.textTracks || []).find((track) => ['captions', 'subtitles'].includes(track.kind));
        const updateCaptions = () => {
            const track = captionTrack();
            captionsButton.hidden = !track;
            if (track) captionsButton.setAttribute('aria-pressed', track.mode === 'showing' ? 'true' : 'false');
        };
        const togglePlay = () => video.paused || video.ended ? void video.play() : video.pause();
        const seekBy = (seconds) => { video.currentTime = Math.max(0, Math.min(video.currentTime + seconds, Number.isFinite(video.duration) ? video.duration : Number.MAX_SAFE_INTEGER)); };
        const toggleFullscreen = async () => {
            try { document.fullscreenElement ? await document.exitFullscreen() : await video.requestFullscreen?.(); } catch { /* native controls remain */ }
        };
        playButton.addEventListener('click', togglePlay);
        backButton.addEventListener('click', () => seekBy(-10));
        forwardButton.addEventListener('click', () => seekBy(10));
        muteButton.addEventListener('click', () => { video.muted = !video.muted; });
        captionsButton.addEventListener('click', () => { const track = captionTrack(); if (track) { track.mode = track.mode === 'showing' ? 'hidden' : 'showing'; updateCaptions(); } });
        fullscreenButton.addEventListener('click', toggleFullscreen);
        speedSelect.addEventListener('change', () => { video.playbackRate = Number(speedSelect.value) || 1; });
        video.addEventListener('play', updatePlay);
        video.addEventListener('pause', updatePlay);
        video.addEventListener('ended', updatePlay);
        video.addEventListener('timeupdate', updateTime);
        video.addEventListener('loadedmetadata', () => { updateTime(); updateCaptions(); });
        video.addEventListener('keydown', (event) => {
            if (event.ctrlKey || event.metaKey || event.altKey) return;
            const actions = { ' ': togglePlay, k: togglePlay, j: () => seekBy(-10), l: () => seekBy(10), arrowleft: () => seekBy(-10), arrowright: () => seekBy(10), m: () => { video.muted = !video.muted; }, f: toggleFullscreen };
            const action = actions[event.key.toLowerCase()];
            if (action) { event.preventDefault(); action(); }
        });
        updatePlay(); updateTime(); updateCaptions();
    });
};

const enhanceLessonVideoProgress = () => {
    const config = document.querySelector('[data-video-progress-config]');
    const video = document.querySelector('#lesson-video video');
    if (!(config instanceof HTMLElement) || !(video instanceof HTMLVideoElement)) return;
    const endpoint = config.dataset.endpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!endpoint || !csrf) return;
    const savedPosition = Number(config.dataset.position || 0);
    const status = config.querySelector('[data-video-save-status]');
    const label = document.querySelector('[data-video-progress-label]');
    let lastSavedPosition = savedPosition;
    let lastSaveAt = 0;
    let saving = false;

    video.addEventListener('loadedmetadata', () => {
        if (savedPosition > 0 && Number.isFinite(video.duration) && savedPosition < video.duration - 2 && video.currentTime < 1) {
            video.currentTime = savedPosition;
            if (status) status.textContent = `Video resumed at ${formatVideoTime(savedPosition)}.`;
        }
    });

    const savePosition = async (force = false) => {
        if (saving || !Number.isFinite(video.duration) || video.duration <= 0) return;
        const now = Date.now();
        if (!force && (now - lastSaveAt < 15000 || Math.abs(video.currentTime - lastSavedPosition) < 5)) return;
        saving = true;
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
                body: JSON.stringify({ subject_slug: config.dataset.subject, course_slug: config.dataset.course, lesson_id: Number(config.dataset.lessonId), position_seconds: video.currentTime, duration_seconds: video.duration }),
            });
            if (!response.ok) return;
            const data = await response.json();
            lastSavedPosition = Number(data.position_seconds || video.currentTime);
            lastSaveAt = Date.now();
            if (status) status.textContent = `Video position saved at ${formatVideoTime(lastSavedPosition)}. Lesson completion remains manual.`;
            if (label instanceof HTMLElement) label.textContent = `Video watched: ${data.watched_percent}%`;
        } finally { saving = false; }
    };

    video.addEventListener('timeupdate', () => void savePosition(false));
    video.addEventListener('pause', () => void savePosition(true));
    video.addEventListener('ended', () => void savePosition(true));
};

const enhancePublicShell = () => {};
const enhanceAdminShell = () => {};
const trapAdminDrawerFocus = () => false;

document.addEventListener('DOMContentLoaded', () => {
    enhanceSearchInputs();
    enhanceForms();
    enhancePublicShell();
    enhanceAdminShell();
    enhanceAccessibleIslVideos();
    enhanceLessonVideoProgress();
});

document.addEventListener('keydown', (event) => {
    if (trapAdminDrawerFocus(event)) return;
    const key = event.key.toLowerCase();
    const searchShortcut = (event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey) || (key === 'k' && (event.ctrlKey || event.metaKey) && !event.altKey);
    if (searchShortcut && !isEditableTarget(event.target)) { event.preventDefault(); openGlobalSearch(); }
});
