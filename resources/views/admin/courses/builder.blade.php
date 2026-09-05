@extends('layouts.admin')

@section('title', $course->title . ' Builder - SignGyaan Admin')
@section('page-title', 'Course Builder')
@section('description', 'Build, review, reorder and quickly create lessons from one SignGyaan course workspace.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-7xl" data-course-builder data-reorder-endpoint="{{ route('admin.courses.builder.reorder', $course) }}">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
            <a href="{{ route('admin.courses.index') }}" class="transition hover:text-sign-primary">Courses</a><span aria-hidden="true">/</span>
            <span class="font-semibold text-sign-primary">{{ $course->title }}</span><span aria-hidden="true">/</span><span>Builder</span>
        </nav>

        <div class="mt-5 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $course->subject?->name ?? 'Course' }}</p>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $course->is_published ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">{{ $course->is_published ? 'Published' : 'Draft' }}</span>
                </div>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl lg:text-5xl">{{ $course->title }}</h1>
                <p class="mt-3 text-sm leading-7 text-sign-muted sm:text-base">{{ $course->short_description ?: ($course->description ?: 'Build this course from units, lessons, practice, vocabulary and assessments in one workspace.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary">Edit Course</a>
                <a href="{{ route('admin.units.create', ['course' => $course->id]) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white">+ Add Unit</a>
            </div>
        </div>

        <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
            @foreach ([['Units',$totalUnits,$publishedUnits.' published'],['Lessons',$totalLessons,$publishedLessons.' published'],['Practice',$practiceCount,'activities'],['Resources',$resourceCount,'support items'],['Vocabulary',$vocabularyCount,'course terms'],['Assessments',$assessmentCount,$publishedAssessmentCount.' published']] as $metric)
                <div class="rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5"><p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">{{ $metric[0] }}</p><p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $metric[1] }}</p><p class="mt-1 text-xs text-sign-muted">{{ $metric[2] }}</p></div>
            @endforeach
        </div>

        <div class="mt-8 rounded-2xl border border-sign-border bg-sign-soft p-4 sm:rounded-3xl sm:p-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-sm font-semibold text-sign-primary">Course Builder tools</p><p class="mt-1 text-xs leading-5 text-sign-muted">Drag items by the ↕ handle to reorder. Use each unit's Quick Lesson Creator to add the next lesson without leaving this page.</p></div>
                <p data-builder-save-status class="text-xs font-semibold text-sign-muted" role="status" aria-live="polite">Ready</p>
            </div>
        </div>

        <div class="mt-8 flex items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Course structure</p><h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Units and lessons</h2></div><a href="{{ route('admin.assessments.index', ['course' => $course->id]) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary">Assessments</a></div>

        @if ($course->units->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-white p-8 text-center sm:rounded-3xl"><h3 class="font-heading text-2xl font-semibold text-sign-primary">Start with the first unit</h3><p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">Create a unit first, then the Quick Lesson Creator will appear inside it.</p><a href="{{ route('admin.units.create', ['course' => $course->id]) }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">+ Create First Unit</a></div>
        @else
            <div class="mt-6 space-y-5" data-sortable-list data-sort-type="units">
                @foreach ($course->units as $unitIndex => $unit)
                    <section draggable="true" data-sort-item data-id="{{ $unit->id }}" class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl" aria-labelledby="builder-unit-{{ $unit->id }}">
                        <div class="border-b border-sign-border bg-sign-soft p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 gap-3">
                                    <button type="button" data-drag-handle class="mt-0.5 inline-flex h-10 w-10 shrink-0 cursor-grab items-center justify-center rounded-lg border border-sign-border bg-white text-sign-primary" aria-label="Drag unit {{ $unit->title }}">↕</button>
                                    <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><span data-order-label class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sign-primary ring-1 ring-sign-border">Unit {{ $unitIndex + 1 }}</span><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $unit->is_published ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">{{ $unit->is_published ? 'Published' : 'Draft' }}</span></div><h3 id="builder-unit-{{ $unit->id }}" class="mt-3 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $unit->title }}</h3></div>
                                </div>
                                <div class="flex shrink-0 flex-wrap gap-2"><button type="button" data-move="up" class="min-h-10 rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary">↑ Up</button><button type="button" data-move="down" class="min-h-10 rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary">↓ Down</button><a href="{{ route('admin.units.edit', $unit) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary">Edit Unit</a><a href="{{ route('admin.lessons.create', ['unit' => $unit->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary">Full Lesson Editor</a></div>
                            </div>
                        </div>

                        <div class="border-b border-sign-border bg-white p-5 sm:p-6">
                            <div class="rounded-2xl border border-sign-cyan/40 bg-sign-light/40 p-4 sm:p-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div><p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Quick Lesson Creator</p><h4 class="mt-1 font-heading text-lg font-semibold text-sign-primary">Add the next lesson to {{ $unit->title }}</h4><p class="mt-1 text-xs leading-5 text-sign-muted">Create the lesson shell now. Add ISL video, transcript, vocabulary and full content later in the lesson editor.</p></div>
                                </div>

                                @if ($errors->any() && (string) old('unit_id') === (string) $unit->id)
                                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
                                        <p class="font-semibold">Please check the lesson details.</p>
                                        <ul class="mt-1 list-disc pl-5 text-xs">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.courses.builder.quick-lesson', $course) }}" class="mt-4 grid gap-3 lg:grid-cols-12 lg:items-end">
                                    @csrf
                                    <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                                    <div class="lg:col-span-4"><label for="quick-title-{{ $unit->id }}" class="mb-1.5 block text-xs font-semibold text-sign-primary">Lesson title <span aria-hidden="true">*</span></label><input id="quick-title-{{ $unit->id }}" name="title" type="text" maxlength="180" required value="{{ (string) old('unit_id') === (string) $unit->id ? old('title') : '' }}" placeholder="e.g. Keyboard Basics" class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2 text-sm text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"></div>
                                    <div class="lg:col-span-4"><label for="quick-summary-{{ $unit->id }}" class="mb-1.5 block text-xs font-semibold text-sign-primary">Short summary</label><input id="quick-summary-{{ $unit->id }}" name="short_description" type="text" maxlength="255" value="{{ (string) old('unit_id') === (string) $unit->id ? old('short_description') : '' }}" placeholder="What will students learn?" class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2 text-sm text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"></div>
                                    <div class="lg:col-span-2"><label for="quick-duration-{{ $unit->id }}" class="mb-1.5 block text-xs font-semibold text-sign-primary">Minutes</label><input id="quick-duration-{{ $unit->id }}" name="estimated_duration_minutes" type="number" min="1" max="100000" value="{{ (string) old('unit_id') === (string) $unit->id ? old('estimated_duration_minutes') : '' }}" placeholder="10" class="min-h-11 w-full rounded-xl border border-sign-border bg-white px-3 py-2 text-sm text-sign-text outline-none focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"></div>
                                    <div class="lg:col-span-2"><label class="flex min-h-11 cursor-pointer items-center gap-2 rounded-xl border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary"><input type="checkbox" name="is_published" value="1" @checked((string) old('unit_id') === (string) $unit->id && old('is_published')) class="h-4 w-4 rounded border-sign-border text-sign-primary focus:ring-sign-cyan"><span>Publish now</span></label></div>
                                    <div class="lg:col-span-12 flex flex-wrap items-center gap-3"><button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sign-dark">+ Create Lesson</button><span class="text-xs text-sign-muted">It will be placed at the end of this unit automatically.</span></div>
                                </form>
                            </div>
                        </div>

                        <div class="divide-y divide-sign-border" data-sortable-list data-sort-type="lessons" data-parent-id="{{ $unit->id }}">
                            @forelse ($unit->lessons as $lessonIndex => $lesson)
                                @php $lessonPractice=$lesson->practiceResources->where('kind','practice'); $lessonResources=$lesson->practiceResources->where('kind','resource'); $lessonAssessments=$lesson->practiceResources->map->assessment->filter(); @endphp
                                <article id="builder-lesson-{{ $lesson->id }}" draggable="true" data-sort-item data-id="{{ $lesson->id }}" class="scroll-mt-28 p-5 sm:p-6">
                                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                        <div class="flex min-w-0 flex-1 gap-3"><button type="button" data-drag-handle class="inline-flex h-10 w-10 shrink-0 cursor-grab items-center justify-center rounded-lg border border-sign-border bg-sign-soft text-sign-primary" aria-label="Drag lesson {{ $lesson->title }}">↕</button><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><span data-order-label class="text-xs font-semibold text-sign-cyan-dark">Lesson {{ $lessonIndex + 1 }}</span><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $lesson->is_published ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">{{ $lesson->is_published ? 'Published' : 'Draft' }}</span>@if($lesson->estimated_duration_minutes)<span class="rounded-full bg-sign-soft px-2 py-0.5 text-[11px] font-semibold text-sign-muted">{{ $lesson->estimated_duration_minutes }} min</span>@endif</div><h4 class="mt-2 font-heading text-lg font-semibold text-sign-primary sm:text-xl">{{ $lesson->title }}</h4>@if($lesson->short_description)<p class="mt-1 text-sm leading-6 text-sign-muted">{{ $lesson->short_description }}</p>@endif<div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold text-sign-muted"><span>{{ $lessonPractice->count() }} practice</span><span>· {{ $lessonResources->count() }} resources</span><span>· {{ $lesson->vocabularyTerms->count() }} vocabulary</span><span>· {{ $lessonAssessments->count() }} assessments</span></div></div></div>
                                        <div class="flex shrink-0 flex-wrap gap-2"><button type="button" data-move="up" class="min-h-10 rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">↑ Up</button><button type="button" data-move="down" class="min-h-10 rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">↓ Down</button><a href="{{ route('admin.lessons.edit', $lesson) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a><a href="{{ route('admin.practice.create', ['lesson' => $lesson->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">+ Activity</a></div>
                                    </div>

                                    @if ($lesson->practiceResources->isNotEmpty())
                                        <div class="mt-4 space-y-2 rounded-xl bg-sign-soft p-3" data-sortable-list data-sort-type="practice" data-parent-id="{{ $lesson->id }}">
                                            @foreach ($lesson->practiceResources as $activityIndex => $activity)
                                                <div draggable="true" data-sort-item data-id="{{ $activity->id }}" class="flex flex-col gap-2 rounded-lg border border-sign-border bg-white p-3 sm:flex-row sm:items-center sm:justify-between"><div class="flex min-w-0 items-center gap-3"><button type="button" data-drag-handle class="inline-flex h-9 w-9 shrink-0 cursor-grab items-center justify-center rounded-lg border border-sign-border text-sign-primary" aria-label="Drag {{ $activity->title }}">↕</button><div class="min-w-0"><p class="text-xs font-semibold text-sign-cyan-dark"><span data-order-label>{{ ucfirst($activity->kind) }} {{ $activityIndex + 1 }}</span> · {{ $activity->resource_type }}</p><p class="truncate text-sm font-semibold text-sign-primary">{{ $activity->title }}</p></div></div><div class="flex gap-2"><button type="button" data-move="up" class="rounded-lg border border-sign-border px-2.5 py-2 text-xs font-semibold text-sign-primary">↑</button><button type="button" data-move="down" class="rounded-lg border border-sign-border px-2.5 py-2 text-xs font-semibold text-sign-primary">↓</button><a href="{{ route('admin.practice.edit', $activity) }}" class="rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a></div></div>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="p-5 text-sm text-sign-muted">No lessons yet. Use the Quick Lesson Creator above to add the first one.</div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        @endif

        @if ($course->vocabularyTerms->isNotEmpty())
            <section class="mt-8 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                <div class="flex items-center justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">ISL vocabulary</p><h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Course vocabulary order</h2></div><a href="{{ route('admin.vocabulary.index', ['course' => $course->id]) }}" class="text-sm font-semibold text-sign-primary">Manage vocabulary</a></div>
                <div class="mt-5 space-y-2" data-sortable-list data-sort-type="vocabulary">
                    @foreach ($course->vocabularyTerms as $termIndex => $term)
                        <div draggable="true" data-sort-item data-id="{{ $term->id }}" class="flex items-center justify-between gap-3 rounded-xl border border-sign-border bg-sign-soft p-3"><div class="flex min-w-0 items-center gap-3"><button type="button" data-drag-handle class="inline-flex h-9 w-9 shrink-0 cursor-grab items-center justify-center rounded-lg border border-sign-border bg-white text-sign-primary" aria-label="Drag vocabulary {{ $term->term }}">↕</button><div><p data-order-label class="text-xs font-semibold text-sign-cyan-dark">Term {{ $termIndex + 1 }}</p><p class="font-semibold text-sign-primary">{{ $term->term }}</p></div></div><div class="flex gap-2"><button type="button" data-move="up" class="rounded-lg border border-sign-border bg-white px-2.5 py-2 text-xs font-semibold text-sign-primary">↑</button><button type="button" data-move="down" class="rounded-lg border border-sign-border bg-white px-2.5 py-2 text-xs font-semibold text-sign-primary">↓</button><a href="{{ route('admin.vocabulary.edit', $term) }}" class="rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary">Edit</a></div></div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const builder = document.querySelector('[data-course-builder]');
    if (!builder) return;
    const endpoint = builder.dataset.reorderEndpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const status = builder.querySelector('[data-builder-save-status]');
    let dragged = null;

    const updateLabels = (list) => {
        const type = list.dataset.sortType;
        [...list.children].filter(el => el.matches('[data-sort-item]')).forEach((item, index) => {
            const label = item.querySelector(':scope > * [data-order-label], :scope > [data-order-label]');
            if (!label) return;
            if (type === 'units') label.textContent = `Unit ${index + 1}`;
            else if (type === 'lessons') label.textContent = `Lesson ${index + 1}`;
            else if (type === 'vocabulary') label.textContent = `Term ${index + 1}`;
            else if (type === 'practice') {
                const kind = label.textContent.trim().split(' ')[0];
                label.textContent = `${kind} ${index + 1}`;
            }
        });
    };

    const save = async (list) => {
        const ids = [...list.children].filter(el => el.matches('[data-sort-item]')).map(el => Number(el.dataset.id));
        if (!ids.length) return;
        status.textContent = 'Saving order…';
        try {
            const response = await fetch(endpoint, {method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf}, body:JSON.stringify({type:list.dataset.sortType,parent_id:list.dataset.parentId ? Number(list.dataset.parentId) : null,ids})});
            if (!response.ok) throw new Error('Save failed');
            status.textContent = 'Order saved';
        } catch (error) {
            status.textContent = 'Could not save order. Refresh and try again.';
        }
    };

    builder.querySelectorAll('[data-sortable-list]').forEach((list) => {
        list.addEventListener('dragstart', (event) => {
            const item = event.target.closest('[data-sort-item]');
            if (!item || item.parentElement !== list) return;
            dragged = item;
            item.classList.add('opacity-50');
            event.dataTransfer.effectAllowed = 'move';
        });
        list.addEventListener('dragover', (event) => {
            if (!dragged || dragged.parentElement !== list) return;
            event.preventDefault();
            const target = event.target.closest('[data-sort-item]');
            if (!target || target === dragged || target.parentElement !== list) return;
            const rect = target.getBoundingClientRect();
            list.insertBefore(dragged, event.clientY < rect.top + rect.height / 2 ? target : target.nextSibling);
        });
        list.addEventListener('dragend', () => {
            if (!dragged || dragged.parentElement !== list) return;
            dragged.classList.remove('opacity-50');
            dragged = null;
            updateLabels(list);
            void save(list);
        });
        list.addEventListener('click', (event) => {
            const button = event.target.closest('[data-move]');
            if (!button) return;
            const item = button.closest('[data-sort-item]');
            if (!item || item.parentElement !== list) return;
            if (button.dataset.move === 'up' && item.previousElementSibling) list.insertBefore(item, item.previousElementSibling);
            if (button.dataset.move === 'down' && item.nextElementSibling) list.insertBefore(item.nextElementSibling, item);
            updateLabels(list);
            void save(list);
            button.focus();
        });
    });
});
</script>
@endpush
