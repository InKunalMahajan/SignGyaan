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

const formatVideoTime = (seconds) => {
    if (!Number.isFinite(seconds) || seconds < 0) {
        return '0:00';
    }

    const totalSeconds = Math.floor(seconds);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const remainder = totalSeconds % 60;

    if (hours > 0) {
        return `${hours}:${String(minutes).padStart(2, '0')}:${String(remainder).padStart(2, '0')}`;
    }

    return `${minutes}:${String(remainder).padStart(2, '0')}`;
};

const enhanceAccessibleIslVideos = () => {
    document.querySelectorAll('#lesson-video video').forEach((video, index) => {
        if (!(video instanceof HTMLVideoElement) || video.dataset.accessiblePlayerReady === 'true') {
            return;
        }

        video.dataset.accessiblePlayerReady = 'true';
        video.controls = true;
        video.setAttribute('playsinline', '');
        video.setAttribute('tabindex', '0');
        video.setAttribute('aria-keyshortcuts', 'Space K J L ArrowLeft ArrowRight M F');

        const controlsId = `isl-video-controls-${index + 1}`;
        const helpId = `isl-video-help-${index + 1}`;
        const statusId = `isl-video-status-${index + 1}`;
        const wrapper = document.createElement('div');
        wrapper.id = controlsId;
        wrapper.dataset.accessibleVideoControls = 'true';
        wrapper.className = 'border-t border-sign-border bg-white p-3 sm:p-4';
        wrapper.setAttribute('aria-label', 'ISL video controls');

        const toolbar = document.createElement('div');
        toolbar.className = 'flex flex-wrap items-center gap-2';
        toolbar.setAttribute('role', 'group');
        toolbar.setAttribute('aria-label', 'Video playback controls');

        const makeButton = (label, shortLabel = label) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-sign-soft px-3 py-2 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-light';
            button.textContent = shortLabel;
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

        const speedLabel = document.createElement('label');
        speedLabel.className = 'inline-flex min-h-11 items-center gap-2 rounded-xl border border-sign-border bg-white px-3 text-sm font-semibold text-sign-primary';
        speedLabel.textContent = 'Speed';

        const speedSelect = document.createElement('select');
        speedSelect.className = 'min-h-9 rounded-lg border border-sign-border bg-white px-2 text-base text-sign-text focus:border-sign-cyan';
        speedSelect.setAttribute('aria-label', 'Playback speed');
        [0.5, 0.75, 1, 1.25, 1.5, 2].forEach((rate) => {
            const option = document.createElement('option');
            option.value = String(rate);
            option.textContent = `${rate}×`;
            option.selected = rate === 1;
            speedSelect.append(option);
        });
        speedLabel.append(speedSelect);

        const timeStatus = document.createElement('span');
        timeStatus.id = statusId;
        timeStatus.className = 'ml-auto text-xs font-semibold text-sign-muted';
        timeStatus.setAttribute('role', 'status');
        timeStatus.setAttribute('aria-live', 'off');
        timeStatus.textContent = '0:00 / 0:00';

        toolbar.append(
            playButton,
            backButton,
            forwardButton,
            muteButton,
            captionsButton,
            speedLabel,
            fullscreenButton,
            timeStatus,
        );

        const help = document.createElement('details');
        help.id = helpId;
        help.className = 'mt-3 rounded-xl bg-sign-soft px-4 py-3 text-xs leading-5 text-sign-muted';
        help.innerHTML = '<summary class="cursor-pointer font-semibold text-sign-primary">Keyboard shortcuts</summary><p class="mt-2">Space or K: play/pause · J or Left Arrow: back 10 seconds · L or Right Arrow: forward 10 seconds · M: mute · F: full screen.</p>';

        wrapper.append(toolbar, help);
        video.insertAdjacentElement('afterend', wrapper);
        video.setAttribute('aria-describedby', helpId);

        const updatePlayState = () => {
            const playing = !video.paused && !video.ended;
            playButton.textContent = playing ? 'Pause' : 'Play';
            playButton.setAttribute('aria-label', playing ? 'Pause video' : 'Play video');
        };

        const updateMuteState = () => {
            muteButton.textContent = video.muted ? 'Unmute' : 'Mute';
            muteButton.setAttribute('aria-label', video.muted ? 'Unmute video' : 'Mute video');
        };

        const updateTime = () => {
            timeStatus.textContent = `${formatVideoTime(video.currentTime)} / ${formatVideoTime(video.duration)}`;
        };

        const getCaptionTrack = () => Array.from(video.textTracks || []).find((track) => ['captions', 'subtitles'].includes(track.kind));

        const updateCaptionState = () => {
            const track = getCaptionTrack();
            if (!track) {
                captionsButton.hidden = true;
                return;
            }

            captionsButton.hidden = false;
            const showing = track.mode === 'showing';
            captionsButton.textContent = showing ? 'CC On' : 'CC';
            captionsButton.setAttribute('aria-pressed', showing ? 'true' : 'false');
            captionsButton.setAttribute('aria-label', showing ? 'Turn captions off' : 'Turn captions on');
        };

        const togglePlay = () => {
            if (video.paused || video.ended) {
                void video.play();
            } else {
                video.pause();
            }
        };

        const seekBy = (seconds) => {
            const duration = Number.isFinite(video.duration) ? video.duration : Number.MAX_SAFE_INTEGER;
            video.currentTime = Math.max(0, Math.min(video.currentTime + seconds, duration));
        };

        const toggleFullscreen = async () => {
            try {
                if (document.fullscreenElement) {
                    await document.exitFullscreen();
                } else if (typeof video.requestFullscreen === 'function') {
                    await video.requestFullscreen();
                }
            } catch {
                // Keep native controls available when Fullscreen API is unavailable.
            }
        };

        playButton.addEventListener('click', togglePlay);
        backButton.addEventListener('click', () => seekBy(-10));
        forwardButton.addEventListener('click', () => seekBy(10));
        muteButton.addEventListener('click', () => {
            video.muted = !video.muted;
            updateMuteState();
        });
        captionsButton.addEventListener('click', () => {
            const track = getCaptionTrack();
            if (!track) {
                return;
            }

            track.mode = track.mode === 'showing' ? 'hidden' : 'showing';
            updateCaptionState();
        });
        fullscreenButton.addEventListener('click', toggleFullscreen);
        speedSelect.addEventListener('change', () => {
            video.playbackRate = Number(speedSelect.value) || 1;
        });

        video.addEventListener('play', updatePlayState);
        video.addEventListener('pause', updatePlayState);
        video.addEventListener('ended', updatePlayState);
        video.addEventListener('volumechange', updateMuteState);
        video.addEventListener('loadedmetadata', () => {
            updateTime();
            updateCaptionState();
        });
        video.addEventListener('durationchange', updateTime);
        video.addEventListener('timeupdate', updateTime);
        video.addEventListener('keydown', (event) => {
            if (event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }

            const key = event.key.toLowerCase();
            const actions = {
                ' ': togglePlay,
                k: togglePlay,
                j: () => seekBy(-10),
                l: () => seekBy(10),
                arrowleft: () => seekBy(-10),
                arrowright: () => seekBy(10),
                m: () => {
                    video.muted = !video.muted;
                    updateMuteState();
                },
                f: toggleFullscreen,
            };

            if (actions[key]) {
                event.preventDefault();
                actions[key]();
            }
        });

        document.addEventListener('fullscreenchange', () => {
            const active = document.fullscreenElement === video;
            fullscreenButton.textContent = active ? 'Exit full screen' : 'Full screen';
            fullscreenButton.setAttribute('aria-label', active ? 'Exit full screen' : 'Enter full screen');
        });

        updatePlayState();
        updateMuteState();
        updateTime();
        updateCaptionState();
    });
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
    enhanceAccessibleIslVideos();
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
