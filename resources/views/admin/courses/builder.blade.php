@extends('layouts.admin')

@section('title', $course->title . ' Builder - SignGyaan Admin')
@section('page-title', 'Course Builder')
@section('description', 'Build and review the complete SignGyaan course structure from one admin workspace.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.courses.index') }}" class="transition hover:text-sign-primary">Courses</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $course->title }}</span>
                <span aria-hidden="true">/</span>
                <span>Builder</span>
            </nav>

            <div class="mt-5 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">{{ $course->subject?->name ?? 'Course' }}</p>
                        <span @class([
                            'rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-sign-light text-sign-primary' => $course->is_published,
                            'bg-gray-100 text-sign-muted' => ! $course->is_published,
                        ])>{{ $course->is_published ? 'Published' : 'Draft' }}</span>
                    </div>
                    <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl lg:text-5xl">{{ $course->title }}</h1>
                    <p class="mt-3 text-sm leading-7 text-sign-muted sm:text-base">{{ $course->short_description ?: ($course->description ?: 'Build this course from units, lessons, practice, vocabulary and assessments in one workspace.') }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Edit Course</a>
                    <a href="{{ route('admin.units.create', ['course' => $course->id]) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sign-dark">+ Add Unit</a>
                    @if ($course->is_published && $course->subject?->is_published)
                        <a href="{{ route('courses.show', ['subject' => $course->subject->slug, 'course' => $course->slug]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">Open Learner View ↗</a>
                    @endif
                </div>
            </div>

            <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
                @foreach ([
                    ['label' => 'Units', 'value' => $totalUnits, 'detail' => $publishedUnits . ' published'],
                    ['label' => 'Lessons', 'value' => $totalLessons, 'detail' => $publishedLessons . ' published'],
                    ['label' => 'Practice', 'value' => $practiceCount, 'detail' => 'activities'],
                    ['label' => 'Resources', 'value' => $resourceCount, 'detail' => 'support items'],
                    ['label' => 'Vocabulary', 'value' => $vocabularyCount, 'detail' => 'course terms'],
                    ['label' => 'Assessments', 'value' => $assessmentCount, 'detail' => $publishedAssessmentCount . ' published'],
                ] as $metric)
                    <div class="rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-muted">{{ $metric['label'] }}</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $metric['value'] }}</p>
                        <p class="mt-1 text-xs text-sign-muted">{{ $metric['detail'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-7 grid gap-4 lg:grid-cols-4">
                <a href="{{ route('admin.units.index', ['course' => $course->id]) }}" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Structure</p>
                    <p class="mt-1 font-semibold text-sign-primary">Manage Units</p>
                </a>
                <a href="{{ route('admin.lessons.index', ['course' => $course->id]) }}" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Content</p>
                    <p class="mt-1 font-semibold text-sign-primary">Manage Lessons</p>
                </a>
                <a href="{{ route('admin.practice.index', ['course' => $course->id]) }}" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Activities</p>
                    <p class="mt-1 font-semibold text-sign-primary">Practice & Resources</p>
                </a>
                <a href="{{ route('admin.vocabulary.index', ['course' => $course->id]) }}" class="rounded-2xl border border-sign-border bg-white p-4 transition hover:border-sign-cyan hover:bg-sign-soft">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">ISL support</p>
                    <p class="mt-1 font-semibold text-sign-primary">Vocabulary Library</p>
                </a>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Course structure</p>
                    <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Units and lessons</h2>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">This is the central authoring map. Drag-and-drop ordering will be added in Step 11B.</p>
                </div>
                <a href="{{ route('admin.assessments.index', ['course' => $course->id]) }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">View Course Assessments</a>
            </div>

            @if ($course->units->isEmpty())
                <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-white p-8 text-center sm:rounded-3xl sm:p-12">
                    <h3 class="font-heading text-2xl font-semibold text-sign-primary">Start with the first unit</h3>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">A course needs units before lessons can be added. Create the first unit to begin building the learning sequence.</p>
                    <a href="{{ route('admin.units.create', ['course' => $course->id]) }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">+ Create First Unit</a>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($course->units as $unitIndex => $unit)
                        <section class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl" aria-labelledby="builder-unit-{{ $unit->id }}">
                            <div class="border-b border-sign-border bg-sign-soft p-5 sm:p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sign-primary ring-1 ring-sign-border">Unit {{ $unitIndex + 1 }}</span>
                                            <span @class([
                                                'rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $unit->is_published,
                                                'bg-gray-100 text-sign-muted' => ! $unit->is_published,
                                            ])>{{ $unit->is_published ? 'Published' : 'Draft' }}</span>
                                        </div>
                                        <h3 id="builder-unit-{{ $unit->id }}" class="mt-3 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $unit->title }}</h3>
                                        @if ($unit->short_description)
                                            <p class="mt-2 max-w-3xl text-sm leading-6 text-sign-muted">{{ $unit->short_description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        <a href="{{ route('admin.units.edit', $unit) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border bg-white px-3 py-2 text-xs font-semibold text-sign-primary">Edit Unit</a>
                                        <a href="{{ route('admin.lessons.create', ['unit' => $unit->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-sign-primary px-3 py-2 text-xs font-semibold text-white">+ Add Lesson</a>
                                    </div>
                                </div>
                            </div>

                            @if ($unit->lessons->isEmpty())
                                <div class="p-5 sm:p-6">
                                    <p class="text-sm text-sign-muted">No lessons in this unit yet.</p>
                                    <a href="{{ route('admin.lessons.create', ['unit' => $unit->id]) }}" class="mt-3 inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-primary px-3 py-2 text-xs font-semibold text-sign-primary">Create lesson</a>
                                </div>
                            @else
                                <div class="divide-y divide-sign-border">
                                    @foreach ($unit->lessons as $lessonIndex => $lesson)
                                        @php
                                            $lessonPractice = $lesson->practiceResources->where('kind', 'practice');
                                            $lessonResources = $lesson->practiceResources->where('kind', 'resource');
                                            $lessonAssessments = $lesson->practiceResources->map->assessment->filter();
                                            $hasIslVideo = filled($lesson->isl_video_url) || ($lesson->mediaAsset && $lesson->mediaAsset->media_type === 'video');
                                        @endphp
                                        <article class="p-5 sm:p-6">
                                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-xs font-semibold text-sign-cyan-dark">Lesson {{ $lessonIndex + 1 }}</span>
                                                        <span @class([
                                                            'rounded-full px-2 py-0.5 text-[11px] font-semibold',
                                                            'bg-sign-light text-sign-primary' => $lesson->is_published,
                                                            'bg-gray-100 text-sign-muted' => ! $lesson->is_published,
                                                        ])>{{ $lesson->is_published ? 'Published' : 'Draft' }}</span>
                                                        @if ($hasIslVideo)<span class="rounded-full bg-sign-soft px-2 py-0.5 text-[11px] font-semibold text-sign-cyan-dark">ISL video</span>@endif
                                                        @if ($lesson->isl_transcript)<span class="rounded-full bg-sign-soft px-2 py-0.5 text-[11px] font-semibold text-sign-primary">Transcript</span>@endif
                                                    </div>
                                                    <h4 class="mt-2 font-heading text-lg font-semibold text-sign-primary sm:text-xl">{{ $lesson->title }}</h4>
                                                    <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $lesson->short_description ?: 'No short description yet.' }}</p>

                                                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-sign-muted">
                                                        <span class="rounded-lg bg-sign-soft px-2.5 py-1.5">{{ $lessonPractice->count() }} practice</span>
                                                        <span class="rounded-lg bg-sign-soft px-2.5 py-1.5">{{ $lessonResources->count() }} resources</span>
                                                        <span class="rounded-lg bg-sign-soft px-2.5 py-1.5">{{ $lesson->vocabularyTerms->count() }} vocabulary</span>
                                                        <span class="rounded-lg bg-sign-soft px-2.5 py-1.5">{{ $lessonAssessments->count() }} assessments</span>
                                                    </div>
                                                </div>

                                                <div class="flex shrink-0 flex-wrap gap-2 xl:max-w-sm xl:justify-end">
                                                    <a href="{{ route('admin.lessons.edit', $lesson) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Edit Lesson</a>
                                                    <a href="{{ route('admin.practice.create', ['lesson' => $lesson->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">+ Practice / Resource</a>
                                                    <a href="{{ route('admin.vocabulary.index', ['course' => $course->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Vocabulary</a>
                                                    @if ($lessonAssessments->isNotEmpty())
                                                        <a href="{{ route('admin.assessments.index', ['lesson' => $lesson->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Assessments</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
