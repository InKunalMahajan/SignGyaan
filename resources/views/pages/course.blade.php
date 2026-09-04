@extends('layouts.app')

@section('title', $course['title'] . ' - SignGyaan')

@section('description', $course['description'])

@section('content')

    {{-- Breadcrumb + Course Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-10 sm:py-12 lg:py-16">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects') }}" class="transition hover:text-sign-primary">Subjects</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects.show', $subjectSlug) }}" class="transition hover:text-sign-primary">{{ $subject['name'] }}</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $course['title'] }}</span>
            </nav>

            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem] lg:items-start lg:gap-12">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-sign-primary shadow-sm ring-1 ring-sign-border">
                            {{ $course['level'] }}
                        </span>
                        <span class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                            {{ $subject['name'] }} Course
                        </span>
                    </div>

                    <h1 class="mt-5 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">
                        {{ $course['title'] }}
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        {{ $course['description'] }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a
                            href="#course-curriculum"
                            class="inline-flex items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark"
                        >
                            Start Course
                        </a>

                        <a
                            href="{{ route('subjects.show', $subjectSlug) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-sign-primary px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white"
                        >
                            Back to {{ $subject['name'] }}
                        </a>
                    </div>
                </div>

                {{-- Course Summary Card --}}
                <aside class="rounded-3xl border border-sign-border bg-white p-6 shadow-sm" aria-label="Course summary">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sign-light text-base font-bold text-sign-primary">
                        {{ $subject['code'] }}
                    </div>

                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">Course at a glance</h2>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-sign-soft p-4 text-center">
                            <p class="text-xl font-semibold text-sign-primary">{{ $course['units'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">Units</p>
                        </div>
                        <div class="rounded-2xl bg-sign-soft p-4 text-center">
                            <p class="text-xl font-semibold text-sign-primary">{{ $course['lessons'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">Lessons</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3 text-sm text-sign-muted">
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span>Level</span>
                            <span class="font-semibold text-sign-primary">{{ $course['level'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-sign-border pb-3">
                            <span>Learning style</span>
                            <span class="font-semibold text-sign-primary">Visual</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>ISL support</span>
                            <span class="font-semibold text-sign-primary">Included</span>
                        </div>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>

    {{-- Course Overview --}}
    <section id="course-overview" class="scroll-mt-24 bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-14">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Course overview</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">What you will learn</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-sign-muted">
                        This course is organised into short units and lessons so you can understand one idea at a time and practise before moving forward.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($course['outcomes'] as $outcome)
                        <div class="flex gap-4 rounded-2xl border border-sign-border bg-white p-5 shadow-sm">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="pt-2 text-sm font-semibold leading-6 text-sign-primary">{{ $outcome }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-container>
    </section>

    {{-- Learning Method --}}
    <section class="border-y border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">How you will learn</p>
                <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Watch, understand and practise</h2>
                <p class="mt-4 text-base leading-7 text-sign-muted">Each lesson can combine Indian Sign Language support, visual notes and short practice activities.</p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Watch</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Use clear visual explanations and ISL-supported video where available.</p>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Understand</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Review simple notes, key points and examples without long blocks of text.</p>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Practise</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Complete short questions and activities to reinforce each lesson.</p>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Unit & Lesson List --}}
    @include('partials.course.curriculum')

    {{-- CTA --}}
    <section class="border-t border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="rounded-3xl bg-sign-dark px-6 py-10 text-white sm:px-10 sm:py-12 lg:flex lg:items-center lg:justify-between lg:gap-10">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">Ready to learn?</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold sm:text-4xl">Begin {{ $course['title'] }} step by step.</h2>
                    <p class="mt-4 text-sm leading-7 text-white/75 sm:text-base">Move through each unit, revisit lessons when needed and practise until the concept is clear.</p>
                </div>

                <a href="#course-curriculum" class="mt-7 inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft lg:mt-0">
                    View Lessons
                </a>
            </div>
        </x-container>
    </section>

@endsection
