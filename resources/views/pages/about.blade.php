@extends('layouts.app')

@section('title', 'About - SignGyaan')

@section('description', 'Learn about SignGyaan, a visual and structured learning platform designed with Indian Sign Language support and Deaf learners in mind.')

@section('content')

    {{-- Hero --}}
    <section class="border-b border-sign-border bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-16">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">About SignGyaan</p>

                    <h1 class="mt-3 font-heading text-4xl font-semibold tracking-tight text-sign-primary sm:text-5xl lg:text-6xl">
                        Learning designed to be clear, visual and accessible.
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-sign-muted sm:text-lg sm:leading-8">
                        SignGyaan is a structured learning platform built around visual explanations, simple notes, practice and Indian Sign Language support so learners can understand one concept at a time.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('learn') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                            Start Learning
                        </a>
                        <a href="{{ route('subjects') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-white">
                            Browse Subjects
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6 shadow-sm sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">The SignGyaan idea</p>
                    <h2 class="mt-3 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Understand first. Practise next.</h2>
                    <p class="mt-4 text-sm leading-7 text-sign-muted">
                        Learning should not depend on long text or unclear explanations. SignGyaan organises every learning path into smaller steps that are easier to watch, read, understand and revisit.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-sign-soft p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Visual</p>
                            <p class="mt-2 text-sm font-semibold text-sign-primary">Clear explanations and examples</p>
                        </div>
                        <div class="rounded-2xl bg-sign-soft p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Structured</p>
                            <p class="mt-2 text-sm font-semibold text-sign-primary">Subject → Course → Unit → Lesson</p>
                        </div>
                        <div class="rounded-2xl bg-sign-soft p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Accessible</p>
                            <p class="mt-2 text-sm font-semibold text-sign-primary">ISL-supported learning</p>
                        </div>
                        <div class="rounded-2xl bg-sign-soft p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">Practical</p>
                            <p class="mt-2 text-sm font-semibold text-sign-primary">Short notes and practice</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Mission --}}
    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Our purpose</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Make learning easier to follow.</h2>
                </div>

                <div class="max-w-3xl space-y-5 text-base leading-8 text-sign-muted">
                    <p>
                        SignGyaan is being built for learners who benefit from visual communication, clear structure and flexible revision. The platform places Indian Sign Language support alongside written learning instead of treating accessibility as an extra feature.
                    </p>
                    <p>
                        The goal is a learning experience where a learner can choose a subject, follow a course in sequence, understand each lesson visually, practise the idea and continue without losing track of what comes next.
                    </p>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Learning approach --}}
    <section class="border-y border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning approach</p>
                <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Four simple steps in every learning journey</h2>
                <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-sign-muted">
                    SignGyaan keeps the learning flow predictable so learners can focus on the concept instead of figuring out how to use the platform.
                </p>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">01 · Watch</span>
                    <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">See the concept</h3>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Use visual explanation and ISL-supported video where available.</p>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">02 · Understand</span>
                    <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Read the key idea</h3>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Review simple notes, key points and examples without unnecessary complexity.</p>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">03 · Practise</span>
                    <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Try it yourself</h3>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Use short questions and activities to check understanding.</p>
                </div>

                <div class="rounded-3xl border border-sign-border bg-white p-6">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">04 · Continue</span>
                    <h3 class="mt-3 font-heading text-xl font-semibold text-sign-primary">Move step by step</h3>
                    <p class="mt-3 text-sm leading-6 text-sign-muted">Follow clear previous and next lesson navigation through the course.</p>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Accessibility --}}
    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">
                <div class="rounded-3xl bg-sign-dark p-6 text-white sm:p-8 lg:p-10">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan">Accessibility by design</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold sm:text-4xl">ISL belongs inside the learning experience.</h2>
                    <p class="mt-5 text-base leading-8 text-white/75">
                        SignGyaan is designed with Deaf learners and visual communication in mind. Lessons can combine Indian Sign Language video, written notes, visual examples and practice in one place.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-sign-border bg-sign-soft p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm" aria-hidden="true">CC</div>
                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">Clear content</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Shorter sections, predictable layout and readable learning notes.</p>
                    </div>

                    <div class="rounded-3xl border border-sign-border bg-sign-soft p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm" aria-hidden="true">ISL</div>
                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">Visual language</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Space for Indian Sign Language video as part of lesson delivery.</p>
                    </div>

                    <div class="rounded-3xl border border-sign-border bg-sign-soft p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm" aria-hidden="true">⌨</div>
                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">Keyboard friendly</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Navigation and search are designed to work without relying only on a mouse.</p>
                    </div>

                    <div class="rounded-3xl border border-sign-border bg-sign-soft p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sign-primary shadow-sm" aria-hidden="true">Aa</div>
                        <h3 class="mt-4 font-heading text-xl font-semibold text-sign-primary">Readable interface</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Strong contrast, clear headings and responsive layouts across devices.</p>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

    {{-- Who it is for --}}
    <section class="border-y border-sign-border bg-sign-soft py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Who SignGyaan is for</p>
                    <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">A flexible place to learn at your own pace.</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-sign-muted">
                        The platform can support learners beginning from foundational topics as well as learners who want to revisit a concept using a more visual approach.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-sign-border bg-white p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learners</p>
                        <p class="mt-3 font-heading text-xl font-semibold text-sign-primary">Learn independently</p>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Follow structured courses and revisit lessons whenever needed.</p>
                    </div>
                    <div class="rounded-3xl border border-sign-border bg-white p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Teachers</p>
                        <p class="mt-3 font-heading text-xl font-semibold text-sign-primary">Support learning</p>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Use clear visual lesson structures to reinforce classroom concepts.</p>
                    </div>
                    <div class="rounded-3xl border border-sign-border bg-white p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Families</p>
                        <p class="mt-3 font-heading text-xl font-semibold text-sign-primary">Follow progress</p>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Understand what a learner is studying and where they are in a course.</p>
                    </div>
                </div>
            </div>
        </x-container>
    </section>

    {{-- CTA --}}
    <section class="bg-white py-14 sm:py-16 lg:py-20">
        <x-container>
            <div class="rounded-3xl bg-sign-primary px-6 py-10 text-center text-white sm:px-10 sm:py-12 lg:px-14">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-light">Explore SignGyaan</p>
                <h2 class="mx-auto mt-3 max-w-3xl font-heading text-3xl font-semibold sm:text-4xl">
                    Choose a subject and begin with one clear lesson.
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-white/80 sm:text-base">
                    Learn visually, practise at your own pace and continue through a structured learning path.
                </p>

                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('subjects') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                        Browse Subjects
                    </a>
                    <a href="{{ route('explore') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Explore Learning
                    </a>
                </div>
            </div>
        </x-container>
    </section>

@endsection
