@extends('layouts.app')

@section('title', 'Dashboard - SignGyaan')
@section('description', 'Your SignGyaan learning dashboard with quick access to courses, lessons and learning recommendations.')

@section('content')
    @php
        $user = auth()->user();
        $firstName = trim(explode(' ', $user->name)[0] ?? $user->name);
        $initial = strtoupper(substr(trim($user->name), 0, 1));

        $recommendedCourses = [
            [
                'title' => 'English Foundations',
                'subject' => 'English',
                'level' => 'Beginner',
                'description' => 'Build everyday vocabulary, grammar and simple sentence understanding.',
                'units' => 4,
                'lessons' => 18,
                'url' => route('courses.show', ['subject' => 'english', 'course' => 'english-foundations']),
            ],
            [
                'title' => 'Computer Basics',
                'subject' => 'Digital Skills',
                'level' => 'Beginner',
                'description' => 'Learn hardware, software, files, folders and everyday computer use.',
                'units' => 5,
                'lessons' => 24,
                'url' => route('courses.show', ['subject' => 'digital-skills', 'course' => 'computer-basics']),
            ],
            [
                'title' => 'Everyday Mathematics',
                'subject' => 'Mathematics',
                'level' => 'Beginner',
                'description' => 'Use numbers, money, time and calculations in practical situations.',
                'units' => 4,
                'lessons' => 20,
                'url' => route('courses.show', ['subject' => 'mathematics', 'course' => 'everyday-mathematics']),
            ],
        ];
    @endphp

    {{-- Dashboard Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-12">
        <x-container>
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
                <div>
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sign-primary font-heading text-xl font-semibold text-white sm:h-16 sm:w-16 sm:text-2xl" aria-hidden="true">
                            {{ $initial }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your dashboard</p>
                            <h1 class="mt-1 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">
                                Welcome, {{ $firstName }}.
                            </h1>
                        </div>
                    </div>

                    <p class="mt-5 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">
                        Start a course, continue exploring subjects and build your learning journey one lesson at a time.
                    </p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-sign-primary">Ready to learn?</p>
                            <p class="mt-1 text-sm leading-6 text-sign-muted">Choose a subject or discover a course that matches your next goal.</p>
                        </div>
                    </div>
                    <a href="{{ route('explore') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                        Explore Learning
                    </a>
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14 lg:py-16">
        <x-container>
            {{-- Learning Overview --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-semibold text-sign-muted">Courses in progress</span>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">0</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Your started courses will appear here.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-semibold text-sign-muted">Lessons completed</span>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">0</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Completed lessons will be counted here.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-semibold text-sign-muted">Learning progress</span>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sign-soft text-sign-primary" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 font-heading text-3xl font-semibold text-sign-primary">0%</p>
                    <p class="mt-1 text-xs leading-5 text-sign-muted">Progress tracking will begin when you start learning.</p>
                </div>
            </div>

            <div class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
                <div class="min-w-0 space-y-10">
                    {{-- Empty Continue Learning --}}
                    <section aria-labelledby="continue-learning-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your learning</p>
                                <h2 id="continue-learning-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Continue learning</h2>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 sm:rounded-3xl sm:p-8">
                            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                                <div class="max-w-xl">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sign-primary shadow-sm" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary sm:text-2xl">Start your first learning path</h3>
                                    <p class="mt-2 text-sm leading-6 text-sign-muted">Once you start learning, your active courses and next lessons will appear here for quick access.</p>
                                </div>
                                <a href="{{ route('subjects') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                                    Browse Subjects
                                </a>
                            </div>
                        </div>
                    </section>

                    {{-- Recommendations --}}
                    <section aria-labelledby="recommended-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Recommended</p>
                                <h2 id="recommended-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Good places to start</h2>
                            </div>
                            <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">View all learning →</a>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($recommendedCourses as $course)
                                <a href="{{ $course['url'] }}" class="group flex min-w-0 flex-col rounded-2xl border border-sign-border bg-white p-5 transition hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-sm sm:rounded-3xl">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                        <span class="rounded-full bg-sign-soft px-2.5 py-1 text-sign-primary">{{ $course['level'] }}</span>
                                        <span class="text-sign-cyan-dark">{{ $course['subject'] }}</span>
                                    </div>
                                    <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary group-hover:text-sign-cyan-dark">{{ $course['title'] }}</h3>
                                    <p class="mt-2 flex-1 text-sm leading-6 text-sign-muted">{{ $course['description'] }}</p>
                                    <div class="mt-5 flex items-center justify-between gap-3 border-t border-sign-border pt-4 text-xs">
                                        <span class="text-sign-muted">{{ $course['units'] }} units · {{ $course['lessons'] }} lessons</span>
                                        <span class="font-semibold text-sign-primary">Open →</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- Quick Actions --}}
                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Dashboard quick actions">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Quick actions</p>
                        <nav class="mt-4 space-y-2" aria-label="Learning shortcuts">
                            <a href="{{ route('subjects') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                                <span>Browse Subjects</span><span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ route('explore') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                                <span>Explore Learning</span><span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ route('search') }}" class="flex min-h-11 items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">
                                <span>Search</span><span aria-hidden="true">→</span>
                            </a>
                        </nav>
                    </div>

                    <div class="rounded-2xl bg-sign-dark p-5 text-white sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan">Learning tip</p>
                        <h2 class="mt-2 font-heading text-xl font-semibold">One lesson at a time.</h2>
                        <p class="mt-3 text-sm leading-6 text-white/70">Watch the visual explanation, read the key points and practise before moving forward.</p>
                    </div>

                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Account</p>
                        <p class="mt-3 font-semibold text-sign-primary">{{ $user->name }}</p>
                        <p class="mt-1 break-all text-sm text-sign-muted">{{ $user->email }}</p>
                        <p class="mt-4 text-xs leading-5 text-sign-muted">Profile and account settings will be available in a later step.</p>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
