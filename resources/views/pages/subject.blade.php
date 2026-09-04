@extends('layouts.app')

@section('title', $subject['name'] . ' - SignGyaan')

@section('description', $subject['description'])

@section('content')

    {{-- Breadcrumb + Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-10 sm:py-12 lg:py-16">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-sign-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-sign-primary">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('subjects') }}" class="transition hover:text-sign-primary">Subjects</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $subject['name'] }}</span>
            </nav>

            <div class="mt-8 grid items-center gap-8 lg:grid-cols-[1fr_auto] lg:gap-12">
                <div class="max-w-3xl">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-base font-bold text-sign-primary shadow-sm ring-1 ring-sign-border">
                        {{ $subject['code'] }}
                    </div>

                    <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                        {{ $subject['eyebrow'] }}
                    </p>

                    <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">
                        {{ $subject['name'] }}
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        {{ $subject['description'] }}
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3 text-sm text-sign-muted">
                        <span class="rounded-full border border-sign-border bg-white px-4 py-2">Visual explanations</span>
                        <span class="rounded-full border border-sign-border bg-white px-4 py-2">ISL-supported lessons</span>
                        <span class="rounded-full border border-sign-border bg-white px-4 py-2">Practice activities</span>
                    </div>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6 shadow-sm lg:w-80">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Start here</p>
                    <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">{{ $subject['featured_course'] }}</h2>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Begin with the first structured course and move lesson by lesson.</p>
                    <div class="mt-5 grid grid-cols-2 gap-3 text-center text-sm">
                        <div class="rounded-xl bg-sign-soft p-3">
                            <p class="font-semibold text-sign-primary">{{ $subject['featured_units'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">Units</p>
                        </div>
                        <div class="rounded-xl bg-sign-soft p-3">
                            <p class="font-semibold text-sign-primary">{{ $subject['featured_lessons'] }}</p>
                            <p class="mt-1 text-xs text-sign-muted">Lessons</p>
                        </div>
                    </div>
                    <a href="{{ route('courses.show', ['subject' => $slug, 'course' => \Illuminate\Support\Str::slug($subject['featured_course'])]) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                        Start featured course
                    </a>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Courses --}}
    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading
                    title="Courses in {{ $subject['name'] }}"
                    description="Choose a course and progress through its units and lessons in a clear sequence."
                />

                <a href="{{ route('learn') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark">
                    View all learning
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach ($subject['courses'] as $course)
                    <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                        <div class="bg-sign-soft p-6">
                            <div class="flex items-start justify-between gap-4">
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">
                                    {{ $course['level'] }}
                                </span>
                                <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">
                                    {{ $subject['name'] }}
                                </span>
                            </div>

                            <h2 class="mt-6 font-heading text-2xl font-semibold text-sign-primary">
                                {{ $course['title'] }}
                            </h2>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <p class="text-sm leading-6 text-sign-muted">
                                {{ $course['description'] }}
                            </p>

                            <div class="mt-6 flex flex-wrap gap-4 text-sm text-sign-muted">
                                <span>{{ $course['units'] }} Units</span>
                                <span aria-hidden="true">•</span>
                                <span>{{ $course['lessons'] }} Lessons</span>
                            </div>

                            <a href="{{ route('courses.show', ['subject' => $slug, 'course' => \Illuminate\Support\Str::slug($course['title'])]) }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                                View course
                                <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </x-container>
    </section>

    {{-- What you will learn --}}
    <section class="border-y border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning outcomes</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">What you will build</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-sign-muted">Each course is broken into smaller lessons so you can understand, practise and revise without feeling overloaded.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($subject['outcomes'] as $outcome)
                        <div class="flex gap-4 rounded-2xl border border-sign-border bg-white p-5">
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

    {{-- CTA --}}
    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="rounded-3xl bg-sign-dark px-6 py-10 text-white sm:px-10 sm:py-12 lg:flex lg:items-center lg:justify-between lg:gap-10">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">Ready to begin?</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold sm:text-4xl">Start learning {{ $subject['name'] }} step by step.</h2>
                    <p class="mt-4 text-sm leading-7 text-white/75 sm:text-base">Follow a course, complete each lesson, and practise until the concept feels clear.</p>
                </div>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row lg:mt-0">
                    <a href="{{ route('courses.show', ['subject' => $slug, 'course' => \Illuminate\Support\Str::slug($subject['featured_course'])]) }}" class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Start Learning</a>
                    <a href="{{ route('subjects') }}" class="inline-flex items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">All Subjects</a>
                </div>
            </div>
        </x-container>
    </section>

@endsection
