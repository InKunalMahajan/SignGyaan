@extends('layouts.app')

@section('title', 'Subjects - SignGyaan')

@section('description', 'Browse SignGyaan subjects and start learning through visual, structured and ISL-supported lessons.')

@section('content')

    {{-- Page Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">
                    Subjects
                </p>

                <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl">
                    Choose what you want to learn
                </h1>

                <p class="mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                    Explore subjects designed around clear explanations, visual learning, Indian Sign Language support and step-by-step practice.
                </p>

                <div class="mt-7 flex flex-wrap gap-3 text-sm text-sign-muted">
                    <span class="rounded-full border border-sign-border bg-white px-4 py-2">Visual lessons</span>
                    <span class="rounded-full border border-sign-border bg-white px-4 py-2">ISL-supported learning</span>
                    <span class="rounded-full border border-sign-border bg-white px-4 py-2">Practice activities</span>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Subject Grid --}}
    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading
                    title="Browse subjects"
                    description="Start with a subject, then move through its courses, units and lessons at your own pace."
                />

                <a
                    href="{{ route('learn') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-sign-primary transition hover:text-sign-cyan-dark"
                >
                    View learning paths
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                {{-- English --}}
                <article class="group flex h-full flex-col rounded-3xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">English</h2>
                    <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Build vocabulary, grammar, reading and everyday communication skills.</p>
                    <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore English
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </article>

                {{-- Mathematics --}}
                <article class="group flex h-full flex-col rounded-3xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h6m-3-3v6m6.75-3h5.25m-5.25 7.5h5.25m-5.25 4.5h5.25M4.5 16.5l6 6m0-6-6 6" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">Mathematics</h2>
                    <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Understand numbers, money, measurements and practical calculations step by step.</p>
                    <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore Mathematics
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </article>

                {{-- Science --}}
                <article class="group flex h-full flex-col rounded-3xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3v5.25L4.5 17.25A2.25 2.25 0 0 0 6.45 20.625h11.1a2.25 2.25 0 0 0 1.95-3.375l-5.25-9V3m-4.5 0h4.5m-6 10.5h7.5" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">Science</h2>
                    <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Explore scientific ideas with visual examples, clear explanations and simple activities.</p>
                    <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore Science
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </article>

                {{-- Digital Skills --}}
                <article class="group flex h-full flex-col rounded-3xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13.5h18M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v7.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 14.25v-7.5A2.25 2.25 0 0 1 5.25 4.5Z" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">Digital Skills</h2>
                    <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Learn computers, internet tools, software and practical digital tasks.</p>
                    <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore Digital Skills
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </article>

                {{-- General Knowledge --}}
                <article class="group flex h-full flex-col rounded-3xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.25-2.25 3.375-5.25 3.375-9S14.25 5.25 12 3M3.75 9h16.5m-16.5 6h16.5" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">General Knowledge</h2>
                    <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Build useful knowledge about India, the world, society, places and everyday topics.</p>
                    <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore General Knowledge
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </article>

                {{-- Life Skills --}}
                <article class="group flex h-full flex-col rounded-3xl border border-sign-border bg-white p-6 transition duration-200 hover:-translate-y-1 hover:border-sign-cyan hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sign-light text-sign-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6c0-3.75-3-6.75-6-9.75-3 3-6 6-6 9.75a6 6 0 0 0 6 6Zm0 0v2.25" />
                        </svg>
                    </div>
                    <h2 class="mt-5 font-heading text-2xl font-semibold text-sign-primary">Life Skills</h2>
                    <p class="mt-3 flex-1 text-sm leading-6 text-sign-muted">Develop communication, confidence, organisation and practical everyday skills.</p>
                    <a href="{{ route('learn') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sign-primary">
                        Explore Life Skills
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </article>

            </div>

        </x-container>
    </section>

    {{-- Learning Structure --}}
    <section class="border-t border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning structure</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Learn in a clear sequence</h2>
                    <p class="mt-4 text-base leading-7 text-sign-muted">SignGyaan organises learning so you always know what comes next.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">01</span>
                        <p class="mt-2 font-semibold text-sign-primary">Subject</p>
                    </div>
                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">02</span>
                        <p class="mt-2 font-semibold text-sign-primary">Course</p>
                    </div>
                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">03</span>
                        <p class="mt-2 font-semibold text-sign-primary">Unit</p>
                    </div>
                    <div class="rounded-2xl border border-sign-border bg-white p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">04</span>
                        <p class="mt-2 font-semibold text-sign-primary">Lesson</p>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

@endsection
