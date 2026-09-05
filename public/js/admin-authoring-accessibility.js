(() => {
    const shell = document.querySelector('[data-admin-shell]');
    const main = document.querySelector('[data-admin-main]');

    if (!(shell instanceof HTMLElement) || !(main instanceof HTMLElement)) {
        return;
    }

    const focusClasses = [
        'focus-visible:outline-none',
        'focus-visible:ring-4',
        'focus-visible:ring-sign-light',
        'focus-visible:ring-offset-2',
    ];

    main.querySelectorAll('a[href], button, summary, input, select, textarea').forEach((element) => {
        if (element instanceof HTMLElement) {
            element.classList.add(...focusClasses);
        }
    });

    const builder = main.querySelector('[data-course-builder]');

    if (builder instanceof HTMLElement) {
        const status = builder.querySelector('[data-builder-save-status]');
        const instructionsId = 'course-builder-ordering-instructions';
        let instructions = document.getElementById(instructionsId);

        if (!instructions) {
            instructions = document.createElement('p');
            instructions.id = instructionsId;
            instructions.className = 'sr-only';
            instructions.textContent = 'Reorder items with the Move Up and Move Down buttons. Pointer drag and drop is also available on supported devices.';
            builder.prepend(instructions);
        }

        if (status instanceof HTMLElement) {
            status.id = status.id || 'course-builder-order-status';
            status.setAttribute('role', 'status');
            status.setAttribute('aria-live', 'polite');
            status.setAttribute('aria-atomic', 'true');
        }

        const finePointer = window.matchMedia('(pointer: fine)').matches;

        builder.querySelectorAll('[data-sortable-list]').forEach((list) => {
            if (!(list instanceof HTMLElement)) return;
            list.setAttribute('role', 'list');

            Array.from(list.children).forEach((item) => {
                if (!(item instanceof HTMLElement) || !item.matches('[data-sort-item]')) return;
                item.setAttribute('role', 'listitem');
                item.draggable = finePointer;

                const handle = item.querySelector('[data-drag-handle]');
                if (handle instanceof HTMLElement) {
                    handle.setAttribute('aria-describedby', instructionsId);
                    handle.setAttribute('title', finePointer ? 'Drag to reorder, or use Move Up and Move Down' : 'Use Move Up and Move Down to reorder');
                    if (!finePointer) {
                        handle.setAttribute('aria-disabled', 'true');
                        handle.classList.remove('cursor-grab');
                        handle.classList.add('cursor-default');
                    }
                }
            });
        });

        builder.querySelectorAll('[data-move="up"], [data-move="down"]').forEach((button) => {
            if (!(button instanceof HTMLButtonElement)) return;
            const item = button.closest('[data-sort-item]');
            const title = item?.querySelector('h3, h4, h5, [data-order-label]')?.textContent?.trim();
            const direction = button.dataset.move === 'up' ? 'up' : 'down';
            button.setAttribute('aria-label', `Move ${title || 'item'} ${direction}`);
        });
    }

    main.querySelectorAll('table').forEach((table, index) => {
        if (!(table instanceof HTMLTableElement)) return;
        const parent = table.parentElement;
        if (!(parent instanceof HTMLElement)) return;

        parent.classList.add('overflow-x-auto');
        if (!parent.hasAttribute('tabindex')) parent.tabIndex = 0;
        if (!parent.hasAttribute('role')) parent.setAttribute('role', 'region');
        if (!parent.hasAttribute('aria-label')) parent.setAttribute('aria-label', `Scrollable admin table ${index + 1}`);
    });

    main.querySelectorAll('a[target="_blank"]').forEach((link) => {
        if (!(link instanceof HTMLAnchorElement)) return;
        if (!link.rel.includes('noopener')) link.rel = `${link.rel} noopener`.trim();
        if (!link.rel.includes('noreferrer')) link.rel = `${link.rel} noreferrer`.trim();
        if (!link.getAttribute('aria-label')) {
            link.setAttribute('aria-label', `${link.textContent.trim()} (opens in a new tab)`);
        }
    });

    const errorSummary = main.querySelector('[data-error-summary]');
    if (errorSummary instanceof HTMLElement) {
        errorSummary.tabIndex = -1;
        requestAnimationFrame(() => errorSummary.focus({ preventScroll: false }));
    }
})();
