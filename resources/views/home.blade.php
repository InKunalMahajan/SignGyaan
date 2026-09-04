@extends('layouts.app')

@section('title', 'SignGyaan - Learn Visually')

@section('description', 'Accessible visual learning through Indian Sign Language.')

@section('content')

    <section class="overflow-hidden bg-white py-16 sm:py-20 lg:py-28">
        <x-container>

            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Hero Content --}}
                <div class="max-w-2xl">

                    <div
                        class="inline-flex items-center rounded-full bg-sign-soft px-4 py-2 text-sm font-semibold text-sign-primary"
                    >
                        Visual learning through Indian Sign Language
                    </div>

                    <h1
                        class="mt-6 font-heading text-4xl font-semibold leading-tight tracking-tight text-sign-primary sm:text-5xl lg:text-6xl"
                    >
                        Learn visually.
                        <span class="text-sign-cyan-dark">Learn clearly.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        SignGyaan makes learning simple, visual and accessible with structured lessons designed for Deaf learners.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-button href="{{ route('learn') }}">
                            Start Learning
                        </x-button>

                        <x-button href="{{ route('subjects') }}" class="border border-sign-primary bg-white text-sign-primary hover:bg-sign-soft">
                            Explore Subjects
                        </x-button>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-sign-muted">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            ISL-first learning
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Simple explanations
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sign-cyan"></span>
                            Practice-focused
                        </div>
                    </div>

                </div>

                {{-- Hero Visual --}}
                <div class="relative">

                    <div class="absolute -left-6 -top-6 h-28 w-28 rounded-full bg-sign-light blur-2xl"></div>
                    <div class="absolute -bottom-6 -right-6 h-32 w-32 rounded-full bg-sign-light blur-2xl"></div>

                    <div class="relative overflow-hidden rounded-3xl border border-sign-border bg-sign-soft p-5 shadow-sm sm:p-7">

                        <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">

                            <div class="flex aspect-video items-center justify-center rounded-xl bg-sign-primary">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="ml-1 h-7 w-7 text-sign-primary"
                                        aria-hidden="true"
                                    >
                                        <path d="M8.25 5.433c0-1.178 1.296-1.896 2.295-1.272l9.067 5.666c.94.588.94 1.958 0 2.546l-9.067 5.666c-.999.624-2.295-.094-2.295-1.272V5.433Z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-5">
                                <div class="h-3 w-24 rounded-full bg-sign-light"></div>
                                <div class="mt-3 h-4 w-3/4 rounded-full bg-sign-primary/15"></div>
                                <div class="mt-2 h-4 w-1/2 rounded-full bg-sign-primary/10"></div>
                            </div>

                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-white p-3 text-center shadow-sm">
                                <div class="mx-auto h-8 w-8 rounded-full bg-sign-light"></div>
                                <p class="mt-2 text-xs font-semibold text-sign-primary">Watch</p>
                            </div>

                            <div class="rounded-xl bg-white p-3 text-center shadow-sm">
                                <div class="mx-auto h-8 w-8 rounded-full bg-sign-light"></div>
                                <p class="mt-2 text-xs font-semibold text-sign-primary">Learn</p>
                            </div>

                            <div class="rounded-xl bg-white p-3 text-center shadow-sm">
                                <div class="mx-auto h-8 w-8 rounded-full bg-sign-light"></div>
                                <p class="mt-2 text-xs font-semibold text-sign-primary">Practice</p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </x-container>
    </section>

    {{-- Subject Categories --}}
    <section class="border-y border-sign-border bg-sign-soft py-16 sm:py-20">
        <x-container>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading
                    title="What do you want to learn?"
                    description="Choose a subject and start learning through clear, visual and structured lessons."
                />

                <a
                    href="{{ route('subjects') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark"
                >
                    View all subjects
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                {{-- English --}}
                <a
                    href="{{ route('subjects') }}"
                    class="group rounded-2xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">English</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Build vocabulary, grammar, reading and communication skills.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore subject
                        <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>

                {{-- Mathematics --}}
                <a
                    href="{{ route('subjects') }}"
                    class="group rounded-2xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h6v6H3v-6Zm12 0h6v6h-6v-6ZM3 16.5h6m6 0h6m-3-3v6" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Mathematics</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Understand numbers, calculations and problem solving step by step.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore subject
                        <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>

                {{-- Science --}}
                <a
                    href="{{ route('subjects') }}"
                    class="group rounded-2xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3v5.25L4.5 17.25A2.25 2.25 0 0 0 6.45 20.625h11.1a2.25 2.25 0 0 0 1.95-3.375l-5.25-9V3m-4.5 0h4.5m-6 10.5h7.5" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Science</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Explore scientific ideas through visual examples and simple explanations.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore subject
                        <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>

                {{-- Digital Skills --}}
                <a
                    href="{{ route('subjects') }}"
                    class="group rounded-2xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13.5h18M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v7.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 14.25v-7.5A2.25 2.25 0 0 1 5.25 4.5Z" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Digital Skills</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Learn computers, internet tools and practical digital skills.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore subject
                        <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>

                {{-- General Knowledge --}}
                <a
                    href="{{ route('subjects') }}"
                    class="group rounded-2xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.25-2.25 3.375-5.25 3.375-9S14.25 5.25 12 3m0 18c-2.25-2.25-3.375-5.25-3.375-9S9.75 5.25 12 3M3.75 9h16.5m-16.5 6h16.5" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">General Knowledge</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Discover useful knowledge about society, places, events and the world.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore subject
                        <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>

                {{-- Life Skills --}}
                <a
                    href="{{ route('subjects') }}"
                    class="group rounded-2xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:border-sign-cyan hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6c0-3.75-3-6.75-6-9.75-3 3-6 6-6 9.75a6 6 0 0 0 6 6Zm0 0v2.25" />
                        </svg>
                    </div>
                    <h3 class="mt-5 font-heading text-xl font-semibold text-sign-primary">Life Skills</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Develop everyday communication, confidence and practical life skills.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore subject
                        <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>

            </div>

        </x-container>
    </section>

    {{-- Featured Learning Paths --}}
    <section class="bg-white py-16 sm:py-20 lg:py-24">
        <x-container>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading
                    title="Featured learning paths"
                    description="Follow a structured path from the basics to practical understanding, one lesson at a time."
                />

                <a
                    href="{{ route('learn') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark"
                >
                    Explore all learning
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">

                {{-- English Foundations --}}
                <article class="group overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="bg-sign-soft p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">Beginner</span>
                        </div>

                        <div class="mt-8 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">
                            English
                        </div>

                        <h3 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">English Foundations</h3>
                    </div>

                    <div class="p-6">
                        <p class="text-sm leading-6 text-sign-muted">Build everyday vocabulary, basic grammar, reading and simple communication step by step.</p>

                        <div class="mt-6 flex flex-wrap gap-4 text-sm text-sign-muted">
                            <span>4 Units</span>
                            <span>•</span>
                            <span>18 Lessons</span>
                        </div>

                        <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                            Start learning
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>

                {{-- Computer Basics --}}
                <article class="group overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="bg-sign-soft p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13.5h18M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v7.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 14.25v-7.5A2.25 2.25 0 0 1 5.25 4.5Z" />
                                </svg>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">Beginner</span>
                        </div>

                        <div class="mt-8 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">
                            Digital Skills
                        </div>

                        <h3 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Computer Basics</h3>
                    </div>

                    <div class="p-6">
                        <p class="text-sm leading-6 text-sign-muted">Understand computer parts, files, software, internet basics and everyday digital tasks.</p>

                        <div class="mt-6 flex flex-wrap gap-4 text-sm text-sign-muted">
                            <span>5 Units</span>
                            <span>•</span>
                            <span>24 Lessons</span>
                        </div>

                        <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                            Start learning
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>

                {{-- Everyday Mathematics --}}
                <article class="group overflow-hidden rounded-3xl border border-sign-border bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="bg-sign-soft p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h6m-3-3v6m6.75-3h5.25m-5.25 7.5h5.25m-5.25 4.5h5.25M4.5 16.5l6 6m0-6-6 6" />
                                </svg>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-primary shadow-sm">Beginner</span>
                        </div>

                        <div class="mt-8 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">
                            Mathematics
                        </div>

                        <h3 class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Everyday Mathematics</h3>
                    </div>

                    <div class="p-6">
                        <p class="text-sm leading-6 text-sign-muted">Practice numbers, money, percentages, measurements and useful everyday calculations.</p>

                        <div class="mt-6 flex flex-wrap gap-4 text-sm text-sign-muted">
                            <span>4 Units</span>
                            <span>•</span>
                            <span>20 Lessons</span>
                        </div>

                        <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                            Start learning
                            <span class="transition group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>

            </div>

        </x-container>
    </section>

@endsection
