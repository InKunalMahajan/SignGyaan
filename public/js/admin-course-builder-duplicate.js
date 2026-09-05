document.addEventListener('DOMContentLoaded', () => {
    const builder = document.querySelector('[data-course-builder]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!(builder instanceof HTMLElement) || !csrf) {
        return;
    }

    const match = window.location.pathname.match(/^\/admin\/courses\/(\d+)\/builder\/?$/);
    if (!match) {
        return;
    }

    const courseId = match[1];

    const makeDuplicateForm = (action, label, confirmMessage, compact = false) => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;
        form.className = 'inline-flex';
        form.dataset.duplicateControl = 'true';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrf;
        form.append(token);

        const button = document.createElement('button');
        button.type = 'submit';
        button.className = compact
            ? 'inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-soft'
            : 'inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-soft';
        button.textContent = label;
        button.setAttribute('aria-label', label);
        form.append(button);

        form.addEventListener('submit', (event) => {
            if (!window.confirm(confirmMessage)) {
                event.preventDefault();
                return;
            }

            button.disabled = true;
            button.textContent = 'Copying…';
        });

        return form;
    };

    const courseActions = builder.querySelector('div.mt-5.flex.flex-col.gap-5.xl\\:flex-row > div.flex.flex-wrap.gap-2');
    if (courseActions && !courseActions.querySelector('[data-duplicate-control]')) {
        courseActions.prepend(makeDuplicateForm(
            `/admin/courses/${courseId}/builder/duplicate`,
            'Copy Course',
            'Create a full draft copy of this course, including units, lessons, rich content, activities, assessments and course vocabulary?'
        ));
    }

    const unitList = builder.querySelector('[data-sortable-list][data-sort-type="units"]');
    if (unitList) {
        Array.from(unitList.children).forEach((unitItem) => {
            if (!(unitItem instanceof HTMLElement) || !unitItem.matches('[data-sort-item]')) {
                return;
            }

            const unitId = unitItem.dataset.id;
            const editLink = unitItem.querySelector(`a[href="/admin/units/${unitId}/edit"]`)
                || unitItem.querySelector('a[href*="/admin/units/"][href$="/edit"]');
            const actions = editLink?.parentElement;

            if (!unitId || !actions || actions.querySelector('[data-duplicate-control]')) {
                return;
            }

            actions.append(makeDuplicateForm(
                `/admin/courses/${courseId}/builder/units/${unitId}/duplicate`,
                'Copy Unit',
                'Copy this unit and all of its lessons as drafts?',
                true
            ));

            const lessonList = unitItem.querySelector('[data-sortable-list][data-sort-type="lessons"]');
            if (!lessonList) {
                return;
            }

            Array.from(lessonList.children).forEach((lessonItem) => {
                if (!(lessonItem instanceof HTMLElement) || !lessonItem.matches('[data-sort-item]')) {
                    return;
                }

                const lessonId = lessonItem.dataset.id;
                const lessonEditLink = lessonItem.querySelector(`a[href="/admin/lessons/${lessonId}/edit"]`)
                    || lessonItem.querySelector('a[href*="/admin/lessons/"][href$="/edit"]');
                const lessonActions = lessonEditLink?.parentElement;

                if (!lessonId || !lessonActions || lessonActions.querySelector('[data-duplicate-control]')) {
                    return;
                }

                lessonActions.append(makeDuplicateForm(
                    `/admin/courses/${courseId}/builder/lessons/${lessonId}/duplicate`,
                    'Copy',
                    'Copy this lesson, including rich content, practice/resources, assessments and vocabulary links?',
                    true
                ));
            });
        });
    }
});
