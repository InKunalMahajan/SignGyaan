@extends('layouts.app')

@section('title', 'My Learning - SignGyaan')
@section('description', 'View your SignGyaan learning journey, continue courses and find your next lesson.')

@section('content')
    @php
        $user = auth()->user();
        $firstName = trim(explode(' ', $user->name)[0] ?? $user->name);

        /*
         * Real learner progress is added in Step 6G.
         * Until then, the page intentionally uses an empty learner state.
         */
        $activeCourses = collect();
        $completedCourses = collect();
        $savedCourses = collect();

        $starterCourses = [
            [
                'title' => 'English Foundations',
                'subject' => 'English',
                'level' => 'Beginner',
                'units' => 4,
                'lessons' => 18,
                'description' => 'Build vocabulary, grammar and simple sentence understanding for everyday use.',
                'courseUrl' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations']),
                'lessonUrl' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations', 'lesson' => 'unit-1-lesson-1']),
            ],
            [
                'title' => 'Computer Basics',
                'subject' => 'Digital Skills',
                'level' => 'Beginner',
                'units' => 5,
                'lessons' => 24,
                'description' => 'Understand hardware, software, files, folders and everyday computer use.',
                'courseUrl' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics']),
                'lessonUrl' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics', 'lesson' => 'unit-1-lesson-1']),
            ],
            [
                'title' => 'Everyday Mathematics',
                'subject' => 'Mathematics',
                'level' => 'Beginner',
                'units' => 4,
                'lessons' => 20,
                'description' => 'Use numbers, money, time and calculations in practical everyday situations.',
                'courseUrl' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics']),
                'lessonUrl' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics', 'lesson' => 'unit-1-lesson-1']),
            ],
        ];
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
                    <h1 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">
                        My Learning
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">
                        Welcome, {{ $firstName }}. Continue where you stopped, review completed learning and keep your courses organised in one place.
                    </p>
                </div>

                <a href="{{ route('explore') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto lg:w-full">
                    Explore New Learning
                </a>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            {{-- Learning Summary --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-sign-muted">In progress</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $activeCourses->count() }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Courses currently being learned.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-sign-muted">Completed</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $completedCourses->count() }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Finished courses will appear here.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-sign-muted">Saved</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185v15.02a.75.75 0 0 1-1.15.635L12 17.185l-6.35 3.977a.75.75 0 0 1-1.15-.635V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">{{ $savedCourses->count() }}</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Learning saved for later.</p>
                </div>
            </div>

            <div class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start">
                <div class="min-w-0 space-y-10">
                    {{-- Continue Learning --}}
                    <section aria-labelledby="my-learning-continue-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Continue learning</p>
                                <h2 id="my-learning-continue-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Pick up where you left off</h2>
                            </div>
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">
                                Back to Dashboard
                            </a>
                        </div>

                        @if ($activeCourses->isNotEmpty())
                            <div class="mt-5 space-y-4">
                                @foreach ($activeCourses as $course)
                                    <article class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                                        <p class="font-semibold text-sign-primary">{{ $course['title'] }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-5 overflow-hidden rounded-2xl border border-sign-border bg-sign-soft sm:rounded-3xl">
                                <div class="grid md:grid-cols-[minmax(0,1fr)_16rem]">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">No course in progress yet</h3>
                                        <p class="mt-2 max-w-xl text-sm leading-6 text-sign-muted">
                                            Start your first lesson and SignGyaan will use this area for quick access to your next lesson. Persistent progress tracking is added in the next step.
                                        </p>
                                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                            <a href="{{ route('subjects') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                                                Browse Subjects
                                            </a>
                                            <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                                                Explore Learning
                                            </a>
                                        </div>
                                    </div>

                                    <div class="flex min-h-44 items-center justify-center bg-sign-primary p-6 text-white md:min-h-full">
                                        <div class="text-center">
                                            <p class="font-heading text-4xl font-semibold">0%</p>
                                            <p class="mt-2 text-sm text-white/75">Course progress</p>
                                            <div class="mx-auto mt-4 h-2 w-32 overflow-hidden rounded-full bg-white/20">
                                                <div class="h-full w-0 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </section>

                    {{-- Starter Courses --}}
                    <section aria-labelledby="starter-learning-heading">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Start learning</p>
                            <h2 id="starter-learning-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Choose your first course</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">These beginner learning paths are good starting points for your SignGyaan journey.</p>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($starterCourses as $course)
                                <article class="flex min-w-0 flex-col rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                        <span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">{{ $course['level'] }}</span>
                                        <span class="text-sign-cyan-dark">{{ $course['subject'] }}</span>
                                    </div>

                                    <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">{{ $course['title'] }}</h3>
                                    <p class="mt-2 flex-1 text-sm leading-6 text-sign-muted">{{ $course['description'] }}</p>

                                    <div class="mt-5 flex items-center justify-between gap-3 border-t border-sign-border pt-4 text-xs text-sign-muted">
                                        <span>{{ $course['units'] }} units</span>
                                        <span>{{ $course['lessons'] }} lessons</span>
                                    </div>

                                    <div class="mt-4 grid gap-2 sm:grid-cols-2 md:grid-cols-1 2xl:grid-cols-2">
                                        <a href="{{ $course['courseUrl'] }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">View Course</a>
                                        <a href="{{ $course['lessonUrl'] }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sign-dark">Start Lesson</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="My Learning navigation">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning library</p>
                        <nav class="mt-4 space-y-2" aria-label="Learning library sections">
                            <a href="#my-learning-continue-heading" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                                <span>In Progress</span><span>{{ $activeCourses->count() }}</span>
                            </a>
                            <div class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm text-sign-muted">
                                <span>Completed</span><span>{{ $completedCourses->count() }}</span>
                            </div>
                            <div class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm text-sign-muted">
                                <span>Saved</span><span>{{ $savedCourses->count() }}</span>
                            </div>
                        </nav>
                    </div>

                    <div class="rounded-2xl bg-sign-dark p-5 text-white sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan">How it works</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold">Watch. Understand. Practise.</h2>
                        <p class="mt-3 text-sm leading-6 text-white/70">Your learning area will connect lessons, progress and the next step in your course.</p>
                        <a href="{{ route('learn') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                            Learning Guide
                        </a>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
