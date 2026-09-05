@extends('layouts.app')

@section('title', 'My Courses - SignGyaan')
@section('description', 'View your SignGyaan courses, saved lesson progress and completed learning.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-12">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="transition hover:text-sign-primary">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">My Courses</span>
            </nav>

            <div class="mt-6 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your courses</p>
                    <h1 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">My Courses</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">Continue active courses, review completed courses and return to the exact lesson where you stopped.</p>
                </div>
                <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Explore Courses</a>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Course summary">
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Tracked courses</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $courseSummary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">In progress</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $courseSummary['in_progress'] }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Completed</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $courseSummary['completed'] }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-semibold text-sign-muted">Overall progress</p>
                    <p class="mt-3 font-heading text-3xl font-semibold text-sign-primary">{{ $courseSummary['overall_progress'] }}%</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="Overall course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $courseSummary['overall_progress'] }}">
                        <div class="h-full rounded-full bg-sign-primary" style="width: {{ $courseSummary['overall_progress'] }}%"></div>
                    </div>
                </div>
            </div>

            @if ($courses->isNotEmpty())
                <section class="mt-10" aria-labelledby="my-courses-heading">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Course library</p>
                            <h2 id="my-courses-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Your saved courses</h2>
                        </div>
                        <p class="text-sm text-sign-muted">Most recently opened courses appear first.</p>
                    </div>

                    <div class="mt-5 grid gap-5 lg:grid-cols-2">
                        @foreach ($courses as $course)
                            <article class="flex min-w-0 flex-col rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full bg-sign-soft px-3 py-1 text-xs font-semibold text-sign-primary">{{ $course['subject'] }}</span>
                                        <span class="rounded-full bg-sign-light px-3 py-1 text-xs font-semibold text-sign-cyan-dark">{{ $course['course_level'] }}</span>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $course['status'] === 'completed' ? 'bg-sign-light text-sign-primary' : 'bg-gray-100 text-sign-muted' }}">{{ $course['status'] === 'completed' ? 'Completed' : 'In progress' }}</span>
                                </div>

                                <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">{{ $course['course_title'] }}</h3>
                                <p class="mt-2 text-sm text-sign-muted">{{ $course['completed_lessons'] }} of {{ $course['total_lessons'] }} lessons completed</p>

                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-sign-soft" role="progressbar" aria-label="{{ $course['course_title'] }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $course['progress_percent'] }}">
                                    <div class="h-full rounded-full bg-sign-primary" style="width: {{ $course['progress_percent'] }}%"></div>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-sign-cyan-dark">{{ $course['progress_percent'] }}% complete</p>

                                @if ($course['status'] === 'in-progress')
                                    <div class="mt-5 rounded-2xl bg-sign-soft p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-sign-muted">Continue from</p>
                                        <p class="mt-1 text-sm font-semibold text-sign-primary">{{ $course['current_lesson_title'] ?: 'Current lesson' }}</p>
                                        @if ($course['current_unit_title'])
                                            <p class="mt-1 text-xs text-sign-muted">{{ $course['current_unit_title'] }}</p>
                                        @endif
                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-sign-muted">
                                            @if ($course['current_lesson_duration'])<span>{{ $course['current_lesson_duration'] }} min</span>@endif
                                            @if ($course['video_watched_percent'] !== null)<span>{{ $course['video_watched_percent'] }}% video watched</span>@endif
                                            @if ($course['last_accessed_at'])<span>Opened {{ $course['last_accessed_at']->diffForHumans() }}</span>@endif
                                        </div>
                                    </div>
                                @elseif ($course['completed_at'])
                                    <p class="mt-5 text-sm font-semibold text-sign-cyan-dark">✓ Completed {{ $course['completed_at']->diffForHumans() }}</p>
                                @endif

                                <div class="mt-auto flex flex-col gap-2 pt-5 sm:flex-row">
                                    @if ($course['status'] === 'in-progress')
                                        <a href="{{ $course['resume_url'] }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl bg-sign-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Continue Course</a>
                                    @endif
                                    <a href="{{ $course['course_url'] }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:border-sign-cyan hover:bg-sign-soft">{{ $course['status'] === 'completed' ? 'Review Course' : 'View Course' }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="mt-10 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center sm:rounded-3xl sm:p-10" aria-labelledby="empty-courses-heading">
                    <h2 id="empty-courses-heading" class="font-heading text-2xl font-semibold text-sign-primary">No saved courses yet</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">Start a published lesson and save your place. Your course will then appear here automatically.</p>
                    <a href="{{ route('subjects') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Browse Subjects</a>
                </section>

                @if ($starterCourses->isNotEmpty())
                    <section class="mt-10" aria-labelledby="starter-courses-heading">
                        <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Start learning</p>
                        <h2 id="starter-courses-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Courses to begin with</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                            @foreach ($starterCourses as $starter)
                                <a href="{{ $starter['url'] }}" class="rounded-2xl border border-sign-border p-5 transition hover:border-sign-cyan hover:shadow-sm sm:rounded-3xl">
                                    <div class="flex flex-wrap gap-2 text-xs font-semibold"><span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">{{ $starter['level'] }}</span><span class="text-sign-cyan-dark">{{ $starter['subject'] }}</span></div>
                                    <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">{{ $starter['title'] }}</h3>
                                    <p class="mt-3 text-xs text-sign-muted">{{ $starter['units'] }} units · {{ $starter['lessons'] }} lessons</p>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif
        </x-container>
    </section>
@endsection
