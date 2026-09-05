(() => {
    const endpoint = '/admin/media-picker';
    let activeSelect = null;
    let lastTrigger = null;
    let assets = [];

    const escapeHtml = (value = '') => String(value).replace(/[&<>'"]/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));

    const dialog = document.createElement('dialog');
    dialog.id = 'admin-media-picker';
    dialog.className = 'w-[min(64rem,calc(100%-2rem))] max-w-5xl rounded-3xl border border-sign-border bg-white p-0 shadow-2xl backdrop:bg-sign-dark/60';
    dialog.setAttribute('aria-labelledby', 'media-picker-title');
    dialog.innerHTML = `
        <div class="border-b border-sign-border p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Media Library</p><h2 id="media-picker-title" class="mt-1 font-heading text-2xl font-semibold text-sign-primary">Choose media</h2><p class="mt-1 text-sm text-sign-muted">Search and preview an existing SignGyaan media item.</p></div>
                <button type="button" data-media-close class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-sign-border text-xl text-sign-primary" aria-label="Close media picker">×</button>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-[1fr_11rem_9rem]">
                <label><span class="sr-only">Search media</span><input data-media-search type="search" placeholder="Search media…" class="min-h-11 w-full rounded-xl border border-sign-border px-3 text-sm"></label>
                <label><span class="sr-only">Media type</span><select data-media-type class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 text-sm"><option value="">All types</option><option value="image">Images</option><option value="video">Videos</option><option value="audio">Audio</option><option value="document">Documents</option></select></label>
                <label class="flex min-h-11 items-center gap-2 rounded-xl border border-sign-border px-3 text-sm font-semibold text-sign-primary"><input data-media-isl type="checkbox" class="h-4 w-4 rounded border-sign-border"><span>ISL only</span></label>
            </div>
        </div>
        <div class="max-h-[60vh] overflow-y-auto p-5 sm:p-6"><p data-media-status class="text-sm text-sign-muted" role="status" aria-live="polite">Loading media…</p><div data-media-grid class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3"></div></div>`;
    document.body.append(dialog);

    const search = dialog.querySelector('[data-media-search]');
    const type = dialog.querySelector('[data-media-type]');
    const isl = dialog.querySelector('[data-media-isl]');
    const grid = dialog.querySelector('[data-media-grid]');
    const status = dialog.querySelector('[data-media-status]');
    let timer = null;

    const render = () => {
        status.textContent = assets.length ? `${assets.length} media item${assets.length === 1 ? '' : 's'} found.` : 'No matching media found.';
        grid.innerHTML = assets.map((asset) => {
            const preview = asset.media_type === 'image' && asset.url
                ? `<img src="${escapeHtml(asset.url)}" alt="" class="aspect-video w-full rounded-xl bg-sign-soft object-cover">`
                : asset.media_type === 'video' && asset.url
                    ? `<video src="${escapeHtml(asset.url)}" preload="metadata" muted class="aspect-video w-full rounded-xl bg-sign-dark object-cover"></video>`
                    : `<div class="flex aspect-video items-center justify-center rounded-xl bg-sign-soft text-sm font-bold uppercase text-sign-primary">${escapeHtml(asset.media_type)}</div>`;
            return `<article class="rounded-2xl border border-sign-border p-3">${preview}<div class="mt-3 flex flex-wrap gap-1.5"><span class="rounded-full bg-sign-soft px-2 py-1 text-[11px] font-semibold text-sign-primary">${escapeHtml(asset.media_type)}</span>${asset.is_isl ? '<span class="rounded-full bg-sign-light px-2 py-1 text-[11px] font-semibold text-sign-primary">ISL</span>' : ''}${asset.is_published ? '' : '<span class="rounded-full bg-gray-100 px-2 py-1 text-[11px] font-semibold text-sign-muted">Draft</span>'}</div><h3 class="mt-2 line-clamp-2 text-sm font-semibold text-sign-primary">${escapeHtml(asset.title)}</h3><p class="mt-1 text-xs text-sign-muted">${escapeHtml([asset.duration, asset.file_size].filter(Boolean).join(' · '))}</p><button type="button" data-media-choose="${asset.id}" class="mt-3 inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-sign-primary px-3 py-2 text-xs font-semibold text-white">Use this media</button></article>`;
        }).join('');
    };

    const load = async () => {
        status.textContent = 'Loading media…';
        const params = new URLSearchParams();
        if (search.value.trim()) params.set('q', search.value.trim());
        if (type.value) params.set('type', type.value);
        if (isl.checked) params.set('isl', '1');
        try {
            const response = await fetch(`${endpoint}?${params}`, {headers:{Accept:'application/json'}, credentials:'same-origin'});
            if (!response.ok) throw new Error();
            const payload = await response.json();
            assets = payload.data || [];
            render();
        } catch {
            assets = [];
            grid.innerHTML = '';
            status.textContent = 'Could not load the Media Library. Try again.';
        }
    };

    const openPicker = (select, trigger) => {
        activeSelect = select;
        lastTrigger = trigger;
        const blockType = select.closest('form')?.querySelector('[name="type"]')?.value;
        type.value = blockType === 'image' ? 'image' : blockType === 'isl_video' ? 'video' : '';
        isl.checked = blockType === 'isl_video';
        search.value = '';
        dialog.showModal();
        void load();
        requestAnimationFrame(() => search.focus());
    };

    document.querySelectorAll('select[name="media_asset_id"]').forEach((select, index) => {
        if (select.dataset.mediaPickerReady === 'true') return;
        select.dataset.mediaPickerReady = 'true';
        select.classList.add('sr-only');
        select.setAttribute('aria-hidden', 'true');
        select.tabIndex = -1;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'inline-flex min-h-11 w-full items-center justify-between gap-3 rounded-xl border border-sign-border bg-white px-3 py-2 text-left text-sm font-semibold text-sign-primary';
        button.dataset.mediaPickerTrigger = String(index);
        const refreshLabel = () => {
            const selected = select.options[select.selectedIndex];
            button.innerHTML = `<span class="min-w-0 truncate">${escapeHtml(selected?.value ? selected.textContent.trim() : 'Choose from Media Library')}</span><span aria-hidden="true">Browse →</span>`;
        };
        refreshLabel();
        select.insertAdjacentElement('afterend', button);
        button.addEventListener('click', () => openPicker(select, button));
        select.addEventListener('change', refreshLabel);
    });

    dialog.addEventListener('click', (event) => {
        const choose = event.target.closest('[data-media-choose]');
        if (choose && activeSelect) {
            const id = String(choose.dataset.mediaChoose);
            let option = Array.from(activeSelect.options).find((item) => item.value === id);
            const asset = assets.find((item) => String(item.id) === id);
            if (!option && asset) {
                option = new Option(`${asset.media_type}${asset.is_isl ? ' · ISL' : ''} · ${asset.title}`, id);
                activeSelect.add(option);
            }
            activeSelect.value = id;
            activeSelect.dispatchEvent(new Event('change', {bubbles:true}));
            dialog.close();
        }
        if (event.target.closest('[data-media-close]')) dialog.close();
    });

    const scheduleLoad = () => { clearTimeout(timer); timer = setTimeout(load, 250); };
    search.addEventListener('input', scheduleLoad);
    type.addEventListener('change', load);
    isl.addEventListener('change', load);
    dialog.addEventListener('close', () => { lastTrigger?.focus(); activeSelect = null; });
})();
