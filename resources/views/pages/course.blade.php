@extends('layouts.app')

@section('title', $course['title'] . ' - SignGyaan')
@section('description', $course['description'])

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-16">
        <x-container>
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects') }}" class="transition hover:text-sign-primary">Subjects</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects.show', $subjectSlug) }}" class="transition hover:text-sign-primary">{{ $subject['name'] }}</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $course['title'] }}</span>
            </nav>

            <div class="mt-6 grid gap-7 sm:mt-8 xl:grid-cols-[minmax(0,1fr)_22rem] xl:items-start xl:gap-12">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-sign-primary shadow-sm ring-1 ring-sign-border sm:px-4 sm:py-2">{{ $course['level'] }}</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">{{ $subject['name'] }} Course</span>
                    </div>

                    <h1 class="mt-4 font-heading text-3xl font-semibold leading-tight tracking-tight text-sign-primary sm:mt-5 sm:text-5xl lg:text-6xl">{{ $course['title'] }}</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-sign-muted sm:mt-5 sm:text-lg sm:leading-8">{{ $course['description'] }}</p>

                    <div class="mt-6 flex flex-col gap-3 sm:mt-8 sm:flex-row">
                        @if ($firstLessonKey)
                            <a href="{{ route('courses.show', ['subject' => $subjectSlug, 'course' => $courseSlug, 'lesson' => $firstLessonKey]) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto">
                                Start Course
                            </a>
                        @else
                            <a href="#course-curriculum" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto">
                                View Curriculum
                            </a>
                        @endif

                        <a href="{{ route('subjects.show', $subjectSlug) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white sm:w-auto">
                            Back to {{ $subject['name'] }}
                        </a>
                    </div>
                </div>

                <aside class="w-full rounded-2xl border border-sign-border bg-white p-5 shadow-sm sm:rounded-3xl sm:p-6" aria-label="Course summary">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sm font-bold text-sign-primary sm:h-14 sm:w-14 sm:text-base">{{ $subject['code'] }}</div>
                    <h2 class="mt-4 font-heading text-xl font-semibold text-sign-primary sm:mt-5 sm:text-2xl">Course at a glance</h2>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:mt-6">
                        <div class="rounded-2xl bg-sign-soft p-4 text-center">
                            <p class="text-lg font-semibold text-sign-primary sm:text-xl">{{ $course['units'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">Published units</p>
                        </div>
                        <div class="rounded-2xl bg-sign-soft p-4 text-center">
                            <p class="text-lg font-semibold text-sign-primary sm:text-xl">{{ $course['lessons'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">Published lessons</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3 text-sm text-sign-muted">
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span>Level</span>
                            <span class="font-semibold text-sign-primary">{{ $course['level'] }}</span>
                        </div>
                        @if ($courseModel->estimated_duration_minutes)
                            <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                                <span>Estimated time</span>
                                <span class="font-semibold text-sign-primary">{{ $courseModel->estimated_duration_minutes }} min</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span>Learning style</span>
                            <span class="font-semibold text-sign-primary">Visual</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>ISL support</span>
                            <span class="font-semibold text-sign-primary">Where available</span>
                        </div>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>

    <section id="course-overview" class="scroll-mt-24 bg-white py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:gap-12 xl:gap-14">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">Course overview</p>
                    <h2 class="mt-3 font-heading text-2xl font-semibold text-sign-primary sm:text-4xl">What you will learn</h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-sign-muted sm:text-base">This course is organised into published units and lessons from the SignGyaan learning catalogue, so the learner view stays in sync with Admin content.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                    @foreach ($course['outcomes'] as $outcome)
                        <div class="flex gap-3 rounded-2xl border border-sign-border bg-white p-4 shadow-sm sm:gap-4 sm:p-5">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary sm:h-10 sm:w-10" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </div>
                            <p class="pt-1 text-sm font-semibold leading-6 text-sign-primary sm:pt-2">{{ $outcome }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-container>
    </section>

    <section class="border-y border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark sm:text-sm">How you will learn</p>
                <h2 class="mt-3 font-heading text-2xl font-semibold text-sign-primary sm:text-4xl">Watch, understand and practise</h2>
                <p class="mt-4 text-sm leading-7 text-sign-muted sm:text-base">Each published lesson can combine Indian Sign Language support, visual notes, examples and practice activities.</p>
            </div>

            <div class="mt-8 grid gap-4 sm:mt-10 md:grid-cols-3 md:gap-5">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sign-light text-sign-primary sm:h-12 sm:w-12" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" /></svg>
                    </div>
                    <h3 class="mt-4 font-heading text-lg font-semibold text-sign-primary sm:mt-5 sm:text-xl">Watch</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Use clear visual explanations and ISL-supported video when the lesson includes one.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sign-light text-sign-primary sm:h-12 sm:w-12" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    </div>
                    <h3 class="mt-4 font-heading text-lg font-semibold text-sign-primary sm:mt-5 sm:text-xl">Understand</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Review lesson notes, objectives, key points and examples added by the content team.</p>
                </div>

                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sign-light text-sign-primary sm:h-12 sm:w-12" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="mt-4 font-heading text-lg font-semibold text-sign-primary sm:mt-5 sm:text-xl">Practise</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Practice and supporting resources will appear with lessons as the database learning flow is connected.</p>
                </div>
            </div>
        </x-container>
    </section>

    @include('partials.course.curriculum')

    <section class="border-t border-sign-border bg-sign-soft py-10 sm:py-14 lg:py-20">
        <x-container>
            <div class="rounded-2xl bg-sign-dark px-5 py-8 text-white sm:rounded-3xl sm:px-10 sm:py-12 lg:flex lg:items-center lg:justify-between lg:gap-10">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan sm:text-sm">Ready to learn?</p>
                    <h2 class="mt-3 font-heading text-2xl font-semibold leading-tight sm:text-4xl">Begin {{ $course['title'] }} step by step.</h2>
                    <p class="mt-4 text-sm leading-7 text-white/75 sm:text-base">Move through the published curriculum in order and revisit any lesson whenever you need.</p>
                </div>

                @if ($firstLessonKey)
                    <a href="{{ route('courses.show', ['subject' => $subjectSlug, 'course' => $courseSlug, 'lesson' => $firstLessonKey]) }}" class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft sm:w-auto lg:mt-0">Start First Lesson</a>
                @else
                    <a href="#course-curriculum" class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft sm:w-auto lg:mt-0">View Curriculum</a>
                @endif
            </div>
        </x-container>
    </section>
@endsection
