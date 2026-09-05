@extends('layouts.admin')

@section('title', 'Assessments - SignGyaan Admin')
@section('page-title', 'Assessments')
@section('description', 'Manage learner assessment settings, publishing and quiz configuration.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Assessments</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">Turn eligible Quiz or Exercise practice items into structured assessments with pass marks, attempt limits, timing and feedback settings.</p>
                </div>
                <a href="{{ route('admin.assessments.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">+ New Assessment</a>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
                    <p class="text-sm font-semibold text-red-800">The assessment action could not be completed.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Total assessments', 'value' => $totalAssessments],
                    ['label' => 'Published', 'value' => $publishedAssessments],
                    ['label' => 'Draft', 'value' => $draftAssessments],
                    ['label' => 'Learner attempts', 'value' => $totalAttempts],
                ] as $stat)
                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-sm font-semibold text-sign-muted">{{ $stat['label'] }}</p>
                        <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.assessments.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_12rem_12rem_12rem_auto] xl:items-end">
                    <div>
                        <label for="assessment-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search</label>
                        <input id="assessment-search" type="search" name="q" value="{{ request('q') }}" placeholder="Assessment, lesson or slug" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>
                    <div>
                        <label for="assessment-subject" class="mb-2 block text-sm font-semibold text-sign-primary">Subject</label>
                        <select id="assessment-subject" name="subject" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="assessment-type" class="mb-2 block text-sm font-semibold text-sign-primary">Type</label>
                        <select id="assessment-type" name="type" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All types</option>
                            <option value="quiz" @selected(request('type') === 'quiz')>Quiz</option>
                            <option value="exercise" @selected(request('type') === 'exercise')>Exercise</option>
                        </select>
                    </div>
                    <div>
                        <label for="assessment-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                        <select id="assessment-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Filter</button>
                        @if (request()->hasAny(['q', 'subject', 'course', 'lesson', 'type', 'status']))
                            <a href="{{ route('admin.assessments.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                @if ($assessments->count())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th class="px-5 py-4">Assessment</th>
                                    <th class="px-5 py-4">Lesson</th>
                                    <th class="px-5 py-4">Settings</th>
                                    <th class="px-5 py-4">Questions</th>
                                    <th class="px-5 py-4">Attempts</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sign-border">
                                @foreach ($assessments as $assessment)
                                    @php
                                        $practice = $assessment->practiceResource;
                                        $lesson = $practice?->lesson;
                                        $course = $lesson?->unit?->course;
                                        $subject = $course?->subject;
                                    @endphp
                                    <tr class="align-top">
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-sign-primary">{{ $practice?->title ?? 'Missing practice item' }}</p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="rounded-full bg-sign-soft px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ ucfirst($practice?->resource_type ?? 'assessment') }}</span>
                                                <span class="rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">Pass {{ $assessment->passing_percentage }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-medium text-sign-text">{{ $lesson?->title ?? 'Missing lesson' }}</p>
                                            <p class="mt-1 text-xs text-sign-muted">{{ $course?->title ?? 'Missing course' }}</p>
                                            <p class="mt-1 text-xs font-semibold text-sign-cyan-dark">{{ $subject?->name ?? 'Missing subject' }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-xs leading-6 text-sign-muted">
                                            <p>{{ $assessment->max_attempts ? $assessment->max_attempts.' max attempts' : 'Unlimited attempts' }}</p>
                                            <p>{{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes.' min limit' : 'No time limit' }}</p>
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-sign-primary">{{ $assessment->questions_count }}</td>
                                        <td class="px-5 py-4 font-semibold text-sign-primary">{{ $assessment->attempts_count }}</td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $assessment->is_published,
                                                'bg-gray-100 text-sign-muted' => ! $assessment->is_published,
                                            ])>{{ $assessment->is_published ? 'Published' : 'Draft' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.assessments.edit', $assessment) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Manage</a>
                                                <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" onsubmit="return confirm('Delete this assessment and its questions? Learner attempts prevent deletion.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-sign-border lg:hidden">
                        @foreach ($assessments as $assessment)
                            @php
                                $practice = $assessment->practiceResource;
                                $lesson = $practice?->lesson;
                            @endphp
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ ucfirst($practice?->resource_type ?? 'Assessment') }}</p>
                                        <h3 class="mt-1 font-heading text-xl font-semibold text-sign-primary">{{ $practice?->title ?? 'Missing practice item' }}</h3>
                                        <p class="mt-1 text-xs text-sign-muted">{{ $lesson?->title ?? 'Missing lesson' }}</p>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-sign-light text-sign-primary' => $assessment->is_published,
                                        'bg-gray-100 text-sign-muted' => ! $assessment->is_published,
                                    ])>{{ $assessment->is_published ? 'Published' : 'Draft' }}</span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Pass mark</span><strong class="mt-1 block text-sign-primary">{{ $assessment->passing_percentage }}%</strong></div>
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Questions</span><strong class="mt-1 block text-sign-primary">{{ $assessment->questions_count }}</strong></div>
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Attempts</span><strong class="mt-1 block text-sign-primary">{{ $assessment->attempts_count }}</strong></div>
                                    <div class="rounded-xl bg-sign-soft p-3"><span class="block text-sign-muted">Time</span><strong class="mt-1 block text-sign-primary">{{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes.' min' : 'None' }}</strong></div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2 border-t border-sign-border pt-4">
                                    <a href="{{ route('admin.assessments.edit', $assessment) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Manage</a>
                                    <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" onsubmit="return confirm('Delete this assessment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Delete</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center sm:p-12">
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No assessments found</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Create an eligible Quiz or Exercise practice item first, then configure its assessment settings.</p>
                        <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                            <a href="{{ route('admin.assessments.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">New Assessment</a>
                            <a href="{{ route('admin.practice.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-5 py-3 text-sm font-semibold text-sign-primary">Practice & Resources</a>
                        </div>
                    </div>
                @endif
            </div>

            @if ($assessments->hasPages())
                <div class="mt-6">{{ $assessments->links() }}</div>
            @endif
        </div>
    </section>
@endsection
