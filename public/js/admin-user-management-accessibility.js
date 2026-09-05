(() => {
    const path = window.location.pathname;

    if (!/^\/admin\/(users|teachers|learners)(\/|$)/.test(path)) {
        return;
    }

    const errorSummary = document.querySelector('[role="alert"]');
    if (errorSummary) {
        errorSummary.setAttribute('tabindex', '-1');
        window.requestAnimationFrame(() => errorSummary.focus({ preventScroll: false }));
    }

    const statusMessage = document.querySelector('[role="status"]');
    if (statusMessage && !statusMessage.hasAttribute('aria-live')) {
        statusMessage.setAttribute('aria-live', 'polite');
    }

    const bulkForm = document.querySelector('form[action*="/users/bulk/action"]');
    if (!bulkForm) {
        return;
    }

    const checkboxes = Array.from(bulkForm.querySelectorAll('input[name="user_ids[]"]'));
    const submitButton = bulkForm.querySelector('button[type="submit"]');

    if (!checkboxes.length || !submitButton) {
        return;
    }

    const liveRegion = document.createElement('p');
    liveRegion.className = 'sr-only';
    liveRegion.setAttribute('role', 'status');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    bulkForm.appendChild(liveRegion);

    const updateSelection = () => {
        const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
        liveRegion.textContent = `${selected} user account${selected === 1 ? '' : 's'} selected.`;
        submitButton.setAttribute('aria-describedby', 'bulk-user-selection-status');
    };

    liveRegion.id = 'bulk-user-selection-status';
    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));
    updateSelection();
})();
