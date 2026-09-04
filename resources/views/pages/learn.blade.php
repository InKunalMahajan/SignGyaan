@extends('layouts.app')

@section('title', 'Learn - SignGyaan')

@section('description', 'Explore SignGyaan learning paths, subjects and structured visual lessons with Indian Sign Language support.')

@section('content')

    {{-- Learn Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-14 sm:py-18 lg:py-20">
        <x-container>
            <div class="grid items-center gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:gap-16">

                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                        Learn on SignGyaan
                    </p>

                    <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">
                        Choose a path and learn step by step.
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        Start with a subject, follow a structured course, move through units and lessons, and practise each concept with clear visual support.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-button href="{{ route('subjects') }}">
                            Browse Subjects
                        </x-button>

                        <a
                            href="#learning-paths"
                            class="inline-flex items-center justify-center rounded-lg border border-sign-primary px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2"
                        >
                            View Learning Paths
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">
                        Learning structure
                    </p>

                    <div class="mt-6 space-y-3">
                        <div class="flex items-center gap-4 rounded-2xl bg-sign-soft p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-primary text-sm font-bold text-white">1</span>
                            <div>
                                <p class="font-semibold text-sign-primary">Subject</p>
                                <p class="mt-1 text-sm text-sign-muted">Choose the area you want to study.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 rounded-2xl bg-sign-soft p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-primary text-sm font-bold text-white">2</span>
                            <div>
                                <p class="font-semibold text-sign-primary">Course</p>
                                <p class="mt-1 text-sm text-sign-muted">Follow a complete learning path.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 rounded-2xl bg-sign-soft p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-primary text-sm font-bold text-white">3</span>
                            <div>
                                <p class="font-semibold text-sign-primary">Units & Lessons</p>
                                <p class="mt-1 text-sm text-sign-muted">Learn one concept at a time.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </x-container>
    </section>

    {{-- Learning Paths --}}
    <section id="learning-paths" class="bg-white py-16 sm:py-20 lg:py-24">
        <x-container>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading
                    title="Start with a learning path"
                    description="These beginner-friendly paths show how SignGyaan courses can guide you from basic ideas to practical understanding."
                />

                <a
                    href="{{ route('subjects') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark"
                >
                    Browse all subjects
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">

                {{-- English Foundations --}}
                <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="bg-sign-soft p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm">
                                <span class="font-heading text-lg font-semibold">Aa</span>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">Beginner</span>
                        </div>

                        <p class="mt-8 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">English</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">English Foundations</h2>
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-sm leading-6 text-sign-muted">
                            Learn everyday vocabulary, basic grammar, reading and simple communication in a clear sequence.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3 text-sm text-sign-muted">
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Vocabulary</span>
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Grammar</span>
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Reading</span>
                        </div>

                        <a href="{{ route('subjects') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                            View learning path
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>

                {{-- Computer Basics --}}
                <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="bg-sign-soft p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13.5h18M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v7.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 14.25v-7.5A2.25 2.25 0 0 1 5.25 4.5Z" />
                                </svg>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">Beginner</span>
                        </div>

                        <p class="mt-8 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Digital Skills</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Computer Basics</h2>
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-sm leading-6 text-sign-muted">
                            Understand computer parts, files, software, internet basics and useful everyday digital tasks.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3 text-sm text-sign-muted">
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Hardware</span>
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Files</span>
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Internet</span>
                        </div>

                        <a href="{{ route('subjects') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                            View learning path
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>

                {{-- Everyday Mathematics --}}
                <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="bg-sign-soft p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm">
                                <span class="text-xl font-bold">±</span>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">Beginner</span>
                        </div>

                        <p class="mt-8 text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Mathematics</p>
                        <h2 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Everyday Mathematics</h2>
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-sm leading-6 text-sign-muted">
                            Build confidence with numbers, money, percentages, measurements and practical calculations.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3 text-sm text-sign-muted">
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Numbers</span>
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Money</span>
                            <span class="rounded-full bg-sign-soft px-3 py-1.5">Measurement</span>
                        </div>

                        <a href="{{ route('subjects') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                            View learning path
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>

            </div>

        </x-container>
    </section>

    {{-- Visual Learning Support --}}
    <section class="border-y border-sign-border bg-sign-soft py-16 sm:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-3">

                <div class="lg:col-span-1">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Built for clarity</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">
                        More than just reading.
                    </h2>
                    <p class="mt-4 text-base leading-7 text-sign-muted">
                        SignGyaan combines different learning formats so each lesson can be easier to follow and revisit.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 lg:col-span-2">
                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-semibold text-sign-primary">Watch</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Use ISL-supported video explanations where available.</p>
                    </div>

                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-semibold text-sign-primary">Read</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Review short notes, examples and important key points.</p>
                    </div>

                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-semibold text-sign-primary">Practise</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Reinforce concepts with short activities and questions.</p>
                    </div>
                </div>

            </div>
        </x-container>
    </section>

    {{-- Learn CTA --}}
    <section class="bg-white py-16 sm:py-20">
        <x-container>
            <div class="rounded-3xl bg-sign-dark px-6 py-10 text-center text-white sm:px-10 sm:py-12">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">Ready to begin?</p>
                <h2 class="mx-auto mt-3 max-w-3xl font-heading text-3xl font-semibold sm:text-4xl">
                    Find a subject that matches what you want to learn.
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-white/75 sm:text-base">
                    Start with the basics and move forward one clear lesson at a time.
                </p>

                <div class="mt-7">
                    <a
                        href="{{ route('subjects') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-2 focus:ring-sign-cyan focus:ring-offset-2 focus:ring-offset-sign-dark"
                    >
                        Explore Subjects
                    </a>
                </div>
            </div>
        </x-container>
    </section>

@endsection
