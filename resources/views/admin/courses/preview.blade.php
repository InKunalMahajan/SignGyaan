@extends('layouts.app')

@section('title', 'Preview: ' . ($currentLessonModel?->title ?: $course['title']) . ' - SignGyaan')
@section('description', 'Admin draft preview for SignGyaan course content.')

@section('content')
    <section class="border-b border-sign-border bg-sign-dark py-3 text-white" data-admin-preview-banner>
        <x-container>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan">Admin Preview</p>
                    <p class="mt-1 text-sm text-white/80">Draft units, lessons, media and rich content are visible here. Learners cannot access this preview URL.</p>
                </div>
                <a href="{{ route('admin.courses.builder', $courseModel) }}" class="inline-flex min-h-10 w-fit items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-sign-primary">Exit Preview</a>
            </div>
        </x-container>
    </section>

    <div
        data-draft-preview-links
        data-public-course-url="{{ route('courses.show', ['subject' => $subjectSlug, 'course' => $courseSlug]) }}"
        data-public-subject-url="{{ route('subjects.show', $subjectSlug) }}"
        data-preview-url="{{ route('admin.courses.preview', $courseModel) }}"
        data-builder-url="{{ route('admin.courses.builder', $courseModel) }}"
    >
        @if ($currentLessonModel)
            <div class="border-b border-sign-border bg-sign-soft py-3">
                <x-container>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-primary ring-1 ring-sign-border">{{ $currentUnitModel->is_published ? 'Unit published' : 'Unit draft' }}</span>
                        <span class="rounded-full bg-white px-3 py-1.5 text-sign-primary ring-1 ring-sign-border">{{ $currentLessonModel->is_published ? 'Lesson published' : 'Lesson draft' }}</span>
                    </div>
                </x-container>
            </div>

            @include('partials.course.lesson')
            @include('admin.courses.preview-rich-content')
        @else
            <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-14">
                <x-container>
                    <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Preview breadcrumb">
                        <a href="{{ route('admin.courses.builder', $courseModel) }}" class="font-semibold text-sign-primary">Course Builder</a>
                        <span aria-hidden="true">/</span>
                        <span>{{ $course['title'] }}</span>
                        <span aria-hidden="true">/</span>
                        <span class="font-semibold text-sign-primary">Preview</span>
                    </nav>

                    <div class="mt-6 grid gap-7 xl:grid-cols-[minmax(0,1fr)_22rem] xl:items-start xl:gap-12">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary ring-1 ring-sign-border">{{ $course['level'] }}</span>
                                <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $courseModel->is_published ? 'bg-sign-light text-sign-primary' : 'bg-gray-200 text-sign-muted' }}">{{ $courseModel->is_published ? 'Published course' : 'Draft course' }}</span>
                                @if ($courseModel->subject)
                                    <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $courseModel->subject->is_published ? 'bg-sign-light text-sign-primary' : 'bg-gray-200 text-sign-muted' }}">{{ $courseModel->subject->is_published ? 'Subject published' : 'Subject draft' }}</span>
                                @endif
                            </div>

                            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $subject['name'] }} Course</p>
                            <h1 class="mt-2 font-heading text-3xl font-semibold leading-tight text-sign-primary sm:text-5xl lg:text-6xl">{{ $course['title'] }}</h1>
                            <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-lg sm:leading-8">{{ $course['description'] }}</p>

                            <div class="mt-6 flex flex-wrap gap-3">
                                @if ($firstLessonKey)
                                    <a href="{{ route('admin.courses.preview', ['course' => $courseModel, 'lesson' => $firstLessonKey]) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white">Preview First Lesson</a>
                                @endif
                                <a href="{{ route('admin.courses.builder', $courseModel) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-6 py-3 text-sm font-semibold text-sign-primary">Back to Builder</a>
                            </div>
                        </div>

                        <aside class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-label="Preview summary">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sm font-bold text-sign-primary">{{ $subject['code'] }}</div>
                            <h2 class="mt-4 font-heading text-xl font-semibold text-sign-primary">Preview at a glance</h2>
                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-sign-soft p-4 text-center"><p class="text-xl font-semibold text-sign-primary">{{ $course['units'] }}</p><p class="mt-1 text-xs text-sign-muted">All units</p></div>
                                <div class="rounded-2xl bg-sign-soft p-4 text-center"><p class="text-xl font-semibold text-sign-primary">{{ $course['lessons'] }}</p><p class="mt-1 text-xs text-sign-muted">All lessons</p></div>
                            </div>
                            <p class="mt-4 text-xs leading-5 text-sign-muted">These totals include draft items because this is an admin-only preview.</p>
                        </aside>
                    </div>
                </x-container>
            </section>

            <section class="bg-white py-10 sm:py-14">
                <x-container>
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner preview</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-4xl">Review the complete course flow before publishing</h2>
                        <p class="mt-4 text-sm leading-7 text-sign-muted sm:text-base">The curriculum below includes both published and draft units and lessons in their current authoring order.</p>
                    </div>
                </x-container>
            </section>

            @include('partials.course.curriculum')
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('[data-draft-preview-links]');
            if (!root) return;

            const publicCourse = root.dataset.publicCourseUrl;
            const publicSubject = root.dataset.publicSubjectUrl;
            const previewUrl = root.dataset.previewUrl;
            const builderUrl = root.dataset.builderUrl;

            root.querySelectorAll('a[href]').forEach((link) => {
                try {
                    const url = new URL(link.href, window.location.origin);

                    if (publicCourse && url.href.startsWith(publicCourse)) {
                        const lesson = url.searchParams.get('lesson');
                        link.href = lesson ? `${previewUrl}?lesson=${encodeURIComponent(lesson)}` : previewUrl;
                    } else if (publicSubject && url.href === publicSubject) {
                        link.href = builderUrl;
                    }
                } catch (_) {
                    // Leave non-standard links unchanged.
                }
            });
        });
    </script>
@endsection
