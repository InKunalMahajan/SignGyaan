@extends('layouts.app')

@section('title', 'My Learning - SignGyaan')
@section('description', 'View saved SignGyaan course progress, assessment attempts and completed learning.')

@section('content')
    @php
        $user = auth()->user();
        $firstName = trim(explode(' ', $user->name)[0] ?? $user->name);
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-12">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="transition hover:text-sign-primary">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">My Learning</span>
            </nav>

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-end">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your learning journey</p>
                    <h1 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">My Learning</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">Welcome, {{ $firstName }}. Your saved courses, completed lessons, assessment attempts and results are kept together here.</p>
                </div>
                <a href="{{ route('explore') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto lg:w-full">Explore New Learning</a>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Courses in progress</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $activeCourses->count() }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Courses you can continue now.</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Courses completed</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $completedCourses->count() }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Courses fully completed.</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Assessment attempts</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $assessmentSummary['total_attempts'] }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">{{ $assessmentSummary['submitted'] }} submitted · {{ $assessmentSummary['in_progress'] }} open.</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Best assessment score</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $assessmentSummary['best_score'] === null ? '—' : number_format((float) $assessmentSummary['best_score'], 2).'%' }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">{{ $assessmentSummary['passed'] }} passed attempt{{ $assessmentSummary['passed'] === 1 ? '' : 's' }}.</p>
                </div>
            </div>

            <div class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start">
                <div class="min-w-0 space-y-12">
                    <section aria-labelledby="my-learning-continue-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Continue learning</p>
                                <h2 id="my-learning-continue-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Pick up where you left off</h2>
                            </div>
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Back to Dashboard</a>
                        </div>

                        @if ($activeCourses->isNotEmpty())
                            <div class="mt-5 space-y-4">
                                @foreach ($activeCourses as $progress)
                                    <article class="overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                                        <div class="grid lg:grid-cols-[minmax(0,1fr)_16rem]">
                                            <div class="p-5 sm:p-6">
                                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                                    <span class="rounded-full bg-sign-soft px-3 py-1 text-sign-primary">{{ $progress->subject_name }}</span>
                                                    <span class="text-sign-cyan-dark">{{ $progress->progressPercent() }}% complete</span>
                                                </div>
                                                <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $progress->course_title }}</h3>
                                                <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $progress->completedLessonsCount() }} of {{ $progress->total_lessons }} lessons completed.</p>
                                                @if ($progress->current_lesson_title)
                                                    <p class="mt-2 text-sm font-semibold text-sign-primary">Next: {{ $progress->current_lesson_title }}</p>
                                                @endif
                                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="{{ $progress->course_title }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress->progressPercent() }}"><div class="h-full rounded-full bg-sign-primary" style="width: {{ $progress->progressPercent() }}%"></div></div>
                                            </div>
                                            <div class="flex items-center bg-sign-soft p-5 sm:p-6">
                                                <a href="{{ route('courses.show', ['subject' => $progress->subject_slug, 'course' => $progress->course_slug, 'lesson' => $progress->current_lesson_key]) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Continue Course</a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 sm:rounded-3xl sm:p-8">
                                <h3 class="font-heading text-xl font-semibold text-sign-primary sm:text-2xl">No course in progress</h3>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-sign-muted">Start a lesson, then use Save My Place or Complete & Continue.</p>
                            </div>
                        @endif
                    </section>

                    @include('partials.learning.assessment-progress')

                    @if ($completedCourses->isNotEmpty())
                        <section aria-labelledby="completed-learning-heading">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Completed learning</p>
                            <h2 id="completed-learning-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Finished courses</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                @foreach ($completedCourses as $progress)
                                    <article class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl sm:p-6">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">✓ Course completed</span>
                                        <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">{{ $progress->course_title }}</h3>
                                        <p class="mt-2 text-sm text-sign-muted">{{ $progress->total_lessons }} lessons · {{ $progress->subject_name }}</p>
                                        <a href="{{ route('courses.show', ['subject' => $progress->subject_slug, 'course' => $progress->course_slug]) }}" class="mt-5 inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Review course →</a>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($progressRecords->isEmpty() && $starterCourses->isNotEmpty())
                        <section aria-labelledby="starter-learning-heading">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Start learning</p>
                            <h2 id="starter-learning-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Choose your first course</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                @foreach ($starterCourses as $course)
                                    <article class="flex flex-col rounded-2xl border border-sign-border p-5 sm:rounded-3xl">
                                        <div class="flex flex-wrap gap-2 text-xs font-semibold"><span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">{{ $course['level'] }}</span><span class="text-sign-cyan-dark">{{ $course['subject'] }}</span></div>
                                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">{{ $course['title'] }}</h3>
                                        <p class="mt-2 flex-1 text-sm leading-6 text-sign-muted">{{ $course['description'] }}</p>
                                        <p class="mt-3 text-xs text-sign-muted">{{ $course['units'] }} units · {{ $course['lessons'] }} lessons</p>
                                        <a href="{{ $course['url'] }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Start Lesson</a>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="My Learning navigation">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning summary</p>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span class="text-sign-muted">Lessons completed</span><span class="font-semibold text-sign-primary">{{ $completedLessons }}</span></div>
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span class="text-sign-muted">Tracked courses</span><span class="font-semibold text-sign-primary">{{ $progressRecords->count() }}</span></div>
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span class="text-sign-muted">Assessments passed</span><span class="font-semibold text-sign-primary">{{ $assessmentSummary['passed'] }}</span></div>
                            @if ($assessmentSummary['average_score'] !== null)
                                <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span class="text-sign-muted">Average score</span><span class="font-semibold text-sign-primary">{{ number_format((float) $assessmentSummary['average_score'], 2) }}%</span></div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Find more learning</p>
                        <nav class="mt-4 space-y-2">
                            <a href="{{ route('subjects') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>Browse Subjects</span><span aria-hidden="true">→</span></a>
                            <a href="{{ route('explore') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary hover:bg-sign-light"><span>Explore Learning</span><span aria-hidden="true">→</span></a>
                        </nav>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
